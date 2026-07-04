<?php
defined('BASEPATH') OR exit('No direct script access allowed');
date_default_timezone_set('Asia/Kolkata');

class PlanAutomationConsumer
{
    protected $CI;
    private $lockFile;
    private $batchSize = 50;
    private $maxAttempts = 3;
    private $_lastEmailError = null;

    // ✅ Per‑job email service preference (default Mailercloud)
    private $useMailercloud = true;

    public function __construct()
    {
        $this->CI =& get_instance();
        $this->CI->load->database();
        $this->CI->load->helper('email');
        $this->CI->load->helper('url');
    }

    public function run($config = []) {
        $this->lockFile = sys_get_temp_dir() . '/plan_automation_consumer.lock';

        if ($this->_isLocked($this->lockFile, 1800)) {
            return [
                'status'         => 'skipped',
                'emails_sent'    => 0,
                'failed'         => 0,
                'display_result' => 'Consumer already running'
            ];
        }

        file_put_contents($this->lockFile, time());
        $start = microtime(true);

        try {
            $cron    = null;
            $cronId  = null;
            $cronName = 'PlanAutomationConsumer';

            if (!empty($config['cron_job_id']) && !empty($config['cron_name'])) {
                $cronId   = (int)$config['cron_job_id'];
                $cron     = (object) ['id' => $cronId, 'name' => $config['cron_name']];
                $cronName = $config['cron_name'];
            } else {
                $cron = $this->CI->db
                    ->where('context', 'plan_automation')
                    ->where('is_active', 1)
                    ->get('tb_jr_cron_jobs')
                    ->row();
                if ($cron) {
                    $cronId   = (int)$cron->id;
                    $cronName = $cron->name ?? $cronName;
                }
            }

            if (!$cronId) {
                @unlink($this->lockFile);
                return [
                    'status'         => 'no_action',
                    'emails_sent'    => 0,
                    'failed'         => 0,
                    'display_result' => 'No active cron job found'
                ];
            }

            // ✅ Set email service from cron job (default remains Mailercloud)
            if (!empty($cron->email_service)) {
                $this->useMailercloud = ($cron->email_service === 'mailercloud');
            }

            if ($this->_isCronRunning($cron)) {
                @unlink($this->lockFile);
                return [
                    'status'         => 'skipped',
                    'emails_sent'    => 0,
                    'failed'         => 0,
                    'display_result' => 'Consumer already running since ' . $cron->running_since
                ];
            }

            $this->_setCronRunning($cronId, true);

            $sched = $this->_isScheduleAllowed($cron);
            if (!$sched['allowed']) {
                $execTime = round(microtime(true) - $start, 4);
                $this->_updateLastRun($cronId, $execTime);
                $this->_setCronRunning($cronId, false);
                @unlink($this->lockFile);
                return [
                    'status'         => 'skipped',
                    'emails_sent'    => 0,
                    'failed'         => 0,
                    'display_result' => $sched['message']
                ];
            }

            // ✅ Cron job ke `emails_per_run` se batch size le lo
            if (!empty($cron->emails_per_run) && (int)$cron->emails_per_run > 0) {
                $this->batchSize = (int)$cron->emails_per_run;
            }

            // ✅ Agar config me override diya ho to use highest priority do
            if (!empty($config['batch_size']) && (int)$config['batch_size'] > 0) {
                $this->batchSize = (int)$config['batch_size'];
            }

            $items = $this->CI->db
                ->where('status', 'pending')
                ->where('scheduled_at <=', date('Y-m-d H:i:s'))
                ->limit($this->batchSize)
                ->get('tb_plan_automation_queue')
                ->result();

            if (empty($items)) {
                $execTime = round(microtime(true) - $start, 4);
                $this->_updateLastRun($cronId, $execTime);
                $this->_setCronRunning($cronId, false);
                @unlink($this->lockFile);
                return [
                    'status'         => 'no_action',
                    'emails_sent'    => 0,
                    'failed'         => 0,
                    'display_result' => 'No pending items'
                ];
            }

            $this->CI->db->insert('tb_jr_cron_execution_logs', [
                'cron_job_id'     => $cronId,
                'cron_name'       => $cronName,
                'status'          => 'processing',
                'message'         => 'Consumer started',
                'processed_count' => 0,
                'failed_count'    => 0,
                'execution_time'  => 0,
                'created_at'      => date('Y-m-d H:i:s')
            ]);
            $execLogId = $this->CI->db->insert_id();

            $planIds = array_unique(array_column($items, 'plan_id'));
            $userIds = array_unique(array_column($items, 'user_id'));

            // Fetch feature_name for email
            $plans = $this->CI->db
                ->select('up.*, f.feature_name')
                ->from('tb_ft_user_purchases up')
                ->join('tb_ft_features f', 'f.feature_id = up.feature_id', 'left')
                ->where_in('up.id', $planIds)
                ->get()
                ->result();
            $planMap = [];
            foreach ($plans as $p) { $planMap[$p->id] = $p; }

            $users = $this->CI->db
                ->select('candidate_id, email, name')
                ->where_in('candidate_id', $userIds)
                ->get('tb_candidate')
                ->result();
            $userMap = [];
            foreach ($users as $u) { $userMap[$u->candidate_id] = $u; }

            $processed   = 0;
            $failed      = 0;
            $errorCounts = [];

            foreach ($items as $item) {
                $this->CI->db->where('id', $item->id)
                             ->where('status', 'pending')
                             ->update('tb_plan_automation_queue', [
                                 'status'                => 'processing',
                                 'attempts'              => $item->attempts + 1,
                                 'processing_started_at' => date('Y-m-d H:i:s')
                             ]);
                if ($this->CI->db->affected_rows() == 0) continue;

                $plan = $planMap[$item->plan_id] ?? null;
                $user = $userMap[$item->user_id] ?? null;

                if (!$plan || !$user) {
                    $this->_markFailed($item, 'Plan or user not found');
                    $failed++;
                    $errorCounts['Plan or user not found'] = ($errorCounts['Plan or user not found'] ?? 0) + 1;
                    continue;
                }

                $plan->email = $user->email;
                $plan->name  = $user->name;

                $success = $this->_processAction($item, $plan);

                if ($success) {
                    $processed++;
                } else {
                    $maxAttempts = $item->max_attempts ?? $this->maxAttempts;
                    $errorMsg = $item->last_error ?? 'Action failed';
                    $errorCounts[$errorMsg] = ($errorCounts[$errorMsg] ?? 0) + 1;

                    if ($item->attempts + 1 >= $maxAttempts) {
                        $this->_markFailed($item, $errorMsg);
                        $failed++;
                    } else {
                        $delay = min(5 * pow(2, $item->attempts), 120);
                        $newScheduledAt = date('Y-m-d H:i:s', strtotime("+{$delay} minutes"));
                        $this->CI->db->where('id', $item->id)->update('tb_plan_automation_queue', [
                            'status'       => 'pending',
                            'scheduled_at' => $newScheduledAt,
                            'last_error'   => $errorMsg
                        ]);
                    }
                }
                usleep(100000);
            }

            $execTime = round(microtime(true) - $start, 4);

            // Build detailed message with error breakdown
            $logMessage = "Actions processed: {$processed}, Failed: {$failed}";
            if (!empty($errorCounts)) {
                $errorParts = [];
                foreach ($errorCounts as $error => $count) {
                    $errorParts[] = "{$error} ({$count} emails)";
                }
                $logMessage .= " | Errors: " . implode('; ', $errorParts);
                if ($failed == 0) {
                    $logMessage .= " [All affected emails have been rescheduled]";
                }
            }

            // Determine status
            if ($processed == 0 && $failed == 0 && !empty($errorCounts)) {
                $status = 'warning';
            } else {
                $status = $processed > 0 ? ($failed > 0 ? 'partial' : 'success') : ($failed > 0 ? 'failed' : 'no_action');
            }

            $this->CI->db->where('id', $execLogId)->update('tb_jr_cron_execution_logs', [
                'status'          => $status,
                'message'         => $logMessage,
                'processed_count' => $processed,
                'failed_count'    => $failed,
                'execution_time'  => $execTime,
                'raw_response'    => json_encode(['sent' => $processed, 'failed' => $failed, 'errorCounts' => $errorCounts])
            ]);

            $this->_updateLastRun($cronId, $execTime);

            return [
                'status'         => $status,
                'emails_sent'    => $processed,
                'failed'         => $failed,
                'display_result' => $logMessage
            ];

        } catch (Throwable $e) {
            $execTime = round(microtime(true) - $start, 4);
            if (isset($cronId)) $this->_updateLastRun($cronId, $execTime);
            if (isset($cron)) {
                $this->CI->db->insert('tb_jr_cron_execution_logs', [
                    'cron_job_id'     => $cron->id,
                    'cron_name'       => $cron->name ?? $cronName,
                    'status'          => 'error',
                    'message'         => 'Exception: ' . $e->getMessage(),
                    'processed_count' => 0,
                    'failed_count'    => 0,
                    'execution_time'  => $execTime,
                    'raw_response'    => json_encode(['error' => $e->getMessage()]),
                    'created_at'      => date('Y-m-d H:i:s')
                ]);
            }
            log_message('error', 'PlanAutomationConsumer Exception: ' . $e->getMessage());
            return [
                'status'         => 'error',
                'emails_sent'    => 0,
                'failed'         => 0,
                'display_result' => 'Exception: ' . $e->getMessage()
            ];
        } finally {
            if (isset($cronId)) $this->_setCronRunning($cronId, false);
            $this->_unlock($this->lockFile);
        }
    }

    // ----------------------------------------------------------------
    private function _processAction($item, $plan) {
        $now = date('Y-m-d H:i:s');

        if ($item->action_type === 'expire') {
            $this->CI->db->where('id', $plan->id)
                ->update('tb_ft_user_purchases', [
                    'status'     => 'expired',
                    'updated_at' => $now
                ]);
            $emailSent = $this->_sendEmail($plan, 'expiry', $item);
        } else {
            $duration = $this->_getPlanDuration($plan);
            $start_date = $now;
            $end_date   = date('Y-m-d H:i:s', strtotime("+{$duration} days"));

            $this->CI->db->where('id', $plan->id)
                ->update('tb_ft_user_purchases', [
                    'status'     => 'active',
                    'start_date' => $start_date,
                    'end_date'   => $end_date,
                    'updated_at' => $now
                ]);
            $emailSent = $this->_sendEmail($plan, 'activation', $item, [
                'start_date' => $start_date,
                'end_date'   => $end_date
            ]);
        }

        if ($emailSent) {
            $this->CI->db->where('id', $item->id)->update('tb_plan_automation_queue', [
                'status'       => 'sent',
                'processed_at' => $now
            ]);
            return true;
        } else {
            $item->last_error = $this->_lastEmailError ?? 'Email send failed';
            return false;
        }
    }

    private function _getPlanDuration($plan) {
        $durationRow = $this->CI->db
            ->select('p.duration')
            ->from('tb_ft_plans p')
            ->where('p.duration_id', $plan->plan_id)
            ->get()->row();
        if ($durationRow) {
            $map = ['1 Month'=>30, '2 Months'=>60, '3 Months'=>90, '6 Months'=>180, 'Annual'=>365];
            return $map[$durationRow->duration] ?? 30;
        }
        return 30;
    }

    private function _generateTrackingToken() {
        return bin2hex(random_bytes(32));
    }

    // ✅ _sendEmail – uses per-job email service
    private function _sendEmail($plan, $type, $item, $extra = []) {
        $this->_lastEmailError = null;
        $planDisplayName = !empty($plan->feature_name) ? $plan->feature_name : 'Premium Plan';

        $subject = ($type === 'activation')
            ? "Your {$planDisplayName} Plan Has Been Activated!"
            : "Your {$planDisplayName} Plan Has Expired";

        $recommendations = [];
        if ($type === 'expiry') {
            $recommendations = $this->_getRecommendedPlans($plan->user_id, $plan->feature_id);
        }

        $message = $this->_buildEmailHtml($plan, $type, $extra, $recommendations);

        $token = $this->_generateTrackingToken();
        $modifiedMessage = $this->_addPlanTracking($message, $token);

        if ($this->useMailercloud) {
            $result = send_mailercloud_email(
                $plan->email,
                $plan->name ?? '',
                $subject,
                $modifiedMessage
            );

            if ($result['status'] === 'success') {
                $this->CI->db->insert('tb_plan_automation_tracking', [
                    'tracking_token' => $token,
                    'candidate_id'   => $plan->user_id,
                    'plan_id'        => $plan->id,
                    'action_type'    => ($type === 'activation') ? 'activate' : 'expire'
                ]);
                return true;
            } else {
                $this->_lastEmailError = $this->_extractError($result);
                return false;
            }
        } else {
            $smtpResult = SendEmailTo($plan->email, $subject, $modifiedMessage);

            if ($smtpResult['status'] === 'success') {
                $this->CI->db->insert('tb_plan_automation_tracking', [
                    'tracking_token' => $token,
                    'candidate_id'   => $plan->user_id,
                    'plan_id'        => $plan->id,
                    'action_type'    => ($type === 'activation') ? 'activate' : 'expire'
                ]);
                return true;
            } else {
                $this->_lastEmailError = !empty($smtpResult['errors'])
                    ? substr(json_encode($smtpResult['errors']), 0, 500)
                    : ($smtpResult['message'] ?? 'SMTP failed');
                return false;
            }
        }
    }

    // ✅ _buildEmailHtml – emojis removed, recommendations added
    private function _buildEmailHtml($plan, $type, $extra = [], $recommendations = []) {
        $name     = htmlspecialchars($plan->name ?? 'User');
        $planName = htmlspecialchars($plan->feature_name ?? 'Premium Plan');

        if ($type === 'activation') {
            $start = date('d M Y', strtotime($extra['start_date']));
            $end   = date('d M Y', strtotime($extra['end_date']));
            $dashboardUrl = base_url('dashboard');

            return <<<HTML
            <!DOCTYPE html>
            <html>
            <body style="font-family:Arial,sans-serif;">
                <div style="background:#4CAF50;color:white;padding:20px;text-align:center;">
                    <h2>Plan Activated</h2>
                </div>
                <div style="padding:20px;">
                    <p>Hello <strong>{$name}</strong>,</p>
                    <p>Your <strong>{$planName}</strong> plan is now active.</p>
                    <p><strong>Activated:</strong> {$start} &nbsp;|&nbsp; <strong>Expires:</strong> {$end}</p>
                    <a href="{$dashboardUrl}" style="display:inline-block;background:#4CAF50;color:white;padding:10px 20px;text-decoration:none;border-radius:4px;">Go to Dashboard</a>
                </div>
            </body>
            </html>
            HTML;
        } else {
            $expiry   = date('d M Y', strtotime($plan->end_date));
            $renewUrl = base_url('career-services');

            $html = <<<HTML
            <!DOCTYPE html>
            <html>
            <body style="font-family:Arial,sans-serif;">
                <div style="background:#e74c3c;color:white;padding:20px;text-align:center;">
                    <h2>Plan Expired</h2>
                </div>
                <div style="padding:20px;">
                    <p>Hello <strong>{$name}</strong>,</p>
                    <p>Your <strong>{$planName}</strong> plan expired on <strong>{$expiry}</strong>.</p>
                    <a href="{$renewUrl}" style="display:inline-block;background:#e74c3c;color:white;padding:10px 20px;text-decoration:none;border-radius:4px;">Renew Now</a>
                </div>
            HTML;

            // --- Recommended Plans (compact, no emojis) ---
            if (!empty($recommendations)) {
                $html .= <<<HTML
                <div style="padding:0 20px 20px 20px;">
                    <hr style="border:0;border-top:1px solid #eee;margin:10px 0;" />
                    <p style="font-size:14px;color:#555;margin:0 0 10px 0;">You might also like</p>
                    <table style="width:100%;border-collapse:collapse;font-size:12px;color:#333;">
                HTML;

                foreach ($recommendations as $rec) {
                    $recName = htmlspecialchars($rec['name']);
                    $recDuration = $rec['duration'] ?? '';
                    $recPrice = isset($rec['price']) ? '₹' . number_format($rec['price'], 0) : '';
                    $recMonthly = isset($rec['monthly']) ? '₹' . number_format($rec['monthly'], 0) . '/mo' : '';
                    $recLink = base_url('career-services#' . ($rec['slug'] ?? ''));

                    $html .= <<<HTML
                    <tr>
                        <td style="padding:5px 8px;border-bottom:1px solid #f0f0f0;">
                            <strong>{$recName}</strong><br>
                            <span style="color:#888;">{$recDuration} &nbsp; {$recMonthly}</span>
                        </td>
                        <td style="padding:5px 8px;border-bottom:1px solid #f0f0f0;text-align:right;white-space:nowrap;">
                            <span style="font-weight:bold;color:#e74c3c;">{$recPrice}</span>
                        </td>
                        <td style="padding:5px 8px;border-bottom:1px solid #f0f0f0;text-align:right;">
                            <a href="{$recLink}" style="background:#764ba2;color:white;padding:4px 10px;text-decoration:none;border-radius:3px;font-size:11px;">View</a>
                        </td>
                    </tr>
                    HTML;
                }

                $html .= <<<HTML
                    </table>
                </div>
                HTML;
            }

            $html .= <<<HTML
            </body>
            </html>
            HTML;

            return $html;
        }
    }

    // ✅ New method: fetch 3 recommended plans (features) user hasn't purchased
    private function _getRecommendedPlans($userId, $excludeFeatureId) {
        // Previously purchased features (active/expired)
        $purchased = $this->CI->db
            ->select('feature_id')
            ->where('user_id', $userId)
            ->get('tb_ft_user_purchases')
            ->result();
        $purchasedIds = array_column($purchased, 'feature_id');
        $purchasedIds[] = $excludeFeatureId; // current one also removed

        // Active features that the user hasn't purchased
        $features = $this->CI->db
            ->select('f.feature_id, f.feature_name, f.slug')
            ->from('tb_ft_features f')
            ->where('f.is_active', 'active')
            ->where_not_in('f.feature_id', $purchasedIds)
            ->limit(3)
            ->get()
            ->result();

        $recommendations = [];
        foreach ($features as $feat) {
            // Get cheapest plan by price
            $plan = $this->CI->db
                ->select('duration, plan_total, monthly_cost')
                ->where('feature_id', $feat->feature_id)
                ->order_by('plan_total', 'ASC')
                ->limit(1)
                ->get('tb_ft_plans')
                ->row();

            $recommendations[] = [
                'name'     => $feat->feature_name,
                'slug'     => $feat->slug,
                'duration' => $plan->duration ?? '',
                'price'    => $plan->plan_total ?? 0,
                'monthly'  => $plan->monthly_cost ?? 0,
            ];
        }
        return $recommendations;
    }

    private function _addPlanTracking($body, $token) {
        if (empty($body)) return $body;

        $openUrl = site_url("track/plan_automation_open/{$token}");
        $pixel = '<img src="' . $openUrl . '" width="1" height="1" style="display:none;" />';

        if (stripos($body, '</body>') !== false) {
            $body = str_ireplace('</body>', $pixel . '</body>', $body);
        } else {
            $body .= $pixel;
        }

        $clickBase = site_url("track/plan_automation_click/{$token}?url=");
        $body = preg_replace_callback(
            '/href=["\'](https?:\/\/[^"\']+)["\']/i',
            function ($matches) use ($clickBase) {
                return 'href="' . $clickBase . urlencode($matches[1]) . '"';
            },
            $body
        );

        return $body;
    }

    private function _extractError($response)
    {
        $msg = 'Unknown error';
        if (isset($response['data']['errors']) && !empty($response['data']['errors'])) {
            $errors = $response['data']['errors'];
            $msg = is_array($errors) ? implode(', ', $errors) : (string)$errors;
        } elseif (isset($response['message'])) {
            $msg = $response['message'];
        }
        return substr(strip_tags($msg), 0, 500);
    }

    private function _markFailed($item, $errorMsg)
    {
        $this->CI->db->where('id', $item->id)->update('tb_plan_automation_queue', [
            'status'       => 'failed',
            'last_error'   => $errorMsg,
            'processed_at' => date('Y-m-d H:i:s')
        ]);
    }

    private function _isScheduleAllowed($cron)
    {
        $nowTime  = date('H:i:s');
        $todayDay = strtolower(date('l'));

        if ($cron->schedule_type !== 'custom') {
            if ($nowTime < $cron->start_time || $nowTime > $cron->end_time) {
                return [
                    'allowed' => false,
                    'message' => "Outside allowed time window ({$cron->start_time} - {$cron->end_time})"
                ];
            }
        }

        switch ($cron->schedule_type) {
            case 'daily': return ['allowed' => true, 'message' => ''];
            case 'weekly':
                if (empty($cron->week_days)) return ['allowed' => false, 'message' => 'No week days configured'];
                $days = array_map('trim', explode(',', strtolower($cron->week_days)));
                if (!in_array($todayDay, $days)) return ['allowed' => false, 'message' => 'Not a scheduled weekday'];
                return ['allowed' => true, 'message' => ''];
            case 'monthly':
                if (empty($cron->month_day)) return ['allowed' => false, 'message' => 'No month day configured'];
                if ((int)date('j') != $cron->month_day) return ['allowed' => false, 'message' => 'Not monthly scheduled date'];
                return ['allowed' => true, 'message' => ''];
            case 'custom':
                if (empty($cron->custom_schedule)) return ['allowed' => false, 'message' => 'No custom schedule'];
                $customDateTime = date('Y-m-d H:i:00', strtotime($cron->custom_schedule));
                return date('Y-m-d H:i:00') >= $customDateTime
                    ? ['allowed' => true, 'message' => '']
                    : ['allowed' => false, 'message' => 'Custom schedule time not yet reached'];
            default: return ['allowed' => false, 'message' => 'Invalid schedule type'];
        }
    }

    private function _isCronRunning($cron)
    {
        if (!empty($cron->is_running) && !empty($cron->running_since)) {
            return (time() - strtotime($cron->running_since)) < 300;
        }
        return false;
    }

    private function _setCronRunning($cronId, $running)
    {
        $data = $running
            ? ['is_running' => 1, 'running_since' => date('Y-m-d H:i:s')]
            : ['is_running' => 0, 'running_since' => null];
        $this->CI->db->where('id', $cronId)->update('tb_jr_cron_jobs', $data);
    }

    private function _updateLastRun($cronId, $execTime)
    {
        $this->CI->db->where('id', $cronId)->update('tb_jr_cron_jobs', [
            'last_run'            => date('Y-m-d H:i:s'),
            'last_execution_time' => $execTime
        ]);
    }

    private function _isLocked($file, $ttl = 1800)
    {
        return file_exists($file) && (time() - filemtime($file)) < $ttl;
    }

    private function _unlock($file)
    {
        if (file_exists($file)) @unlink($file);
    }
}