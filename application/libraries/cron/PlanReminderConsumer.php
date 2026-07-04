<?php
defined('BASEPATH') OR exit('No direct script access allowed');
date_default_timezone_set('Asia/Kolkata');

if (!class_exists('PlanReminderConsumer')) {

    class PlanReminderConsumer
    {
        protected $CI;
        private $lockFile;
        private $batchSize = 50;
        private $maxAttempts = 3;

        // ✅ Per‑job email service preference (default Mailercloud)
        private $useMailercloud = true;

        public function __construct()
        {
            $this->CI =& get_instance();
            $this->CI->load->database();
            $this->CI->load->helper('email');
        }

        public function run($config = [])
        {
            $this->lockFile = sys_get_temp_dir() . '/plan_reminder_consumer.lock';

            if ($this->_isLocked($this->lockFile, 1800)) {
                return [
                    'status'         => 'skipped',
                    'emails_sent'    => 0,
                    'failed'         => 0,
                    'display_result' => 'Consumer already running'
                ];
            }

            file_put_contents($this->lockFile, time());
            $startTime = microtime(true);

            try {
                $cron = null;
                $cronId = null;
                $cronName = 'PlanReminderConsumer';

                if (!empty($config['cron_job_id']) && !empty($config['cron_name'])) {
                    $cronId = (int) $config['cron_job_id'];
                    $cron = (object) ['id' => $cronId, 'name' => $config['cron_name']];
                    $cronName = $config['cron_name'];
                } else {
                    $cron = $this->CI->db
                        ->where('context', 'plan_reminders')
                        ->where('is_active', 1)
                        ->get('tb_jr_cron_jobs')
                        ->row();
                    if ($cron) {
                        $cronId = (int) $cron->id;
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
                    $execTime = round(microtime(true) - $startTime, 4);
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

                $queueItems = $this->CI->db
                    ->where('status', 'pending')
                    ->where('scheduled_at <=', date('Y-m-d H:i:s'))
                    ->limit($this->batchSize)
                    ->get('tb_plan_reminder_queue')
                    ->result();

                if (empty($queueItems)) {
                    $execTime = round(microtime(true) - $startTime, 4);
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

                // Insert execution log
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

                $userIds = array_unique(array_column($queueItems, 'user_id'));
                $planIds = array_unique(array_column($queueItems, 'plan_id'));

                $users = $this->CI->db
                    ->select('candidate_id, email, name')
                    ->where_in('candidate_id', $userIds)
                    ->get('tb_candidate')
                    ->result();
                $usersMap = [];
                foreach ($users as $u) {
                    $usersMap[$u->candidate_id] = $u;
                }

                $purchases = $this->CI->db
                    ->select('up.id, up.user_id, up.feature_id, up.end_date, up.start_date, f.feature_name, f.feature_id, s.service_name')
                    ->from('tb_ft_user_purchases up')
                    ->join('tb_ft_features f', 'f.feature_id = up.feature_id', 'left')
                    ->join('tb_ft_services s', 's.service_id = f.service_id', 'left')
                    ->where_in('up.id', $planIds)
                    ->get()
                    ->result();
                $purchaseMap = [];
                foreach ($purchases as $p) {
                    $purchaseMap[$p->id] = $p;
                }

                $processed = 0;
                $failed = 0;
                $errorCounts = [];

                foreach ($queueItems as $item) {
                    $this->CI->db->where('id', $item->id)
                                 ->where('status', 'pending')
                                 ->update('tb_plan_reminder_queue', [
                                     'status'                 => 'processing',
                                     'attempts'               => $item->attempts + 1,
                                     'processing_started_at'  => date('Y-m-d H:i:s')
                                 ]);

                    if ($this->CI->db->affected_rows() == 0) continue;

                    $user = $usersMap[$item->user_id] ?? null;
                    $purchase = $purchaseMap[$item->plan_id] ?? null;

                    if (!$user || !$purchase) {
                        $this->_markFailed($item, 'User or purchase not found');
                        $failed++;
                        $errorCounts['User or purchase not found'] = ($errorCounts['User or purchase not found'] ?? 0) + 1;
                        continue;
                    }

                    $success = $this->_sendReminderEmail($item, $user, $purchase);

                    if ($success) {
                        $this->CI->db->where('id', $item->id)->update('tb_plan_reminder_queue', [
                            'status'       => 'sent',
                            'processed_at' => date('Y-m-d H:i:s')
                        ]);

                        $this->CI->db->insert('tb_plan_reminder_tracking', [
                            'candidate_id' => $item->user_id,
                            'created_at'   => date('Y-m-d H:i:s')
                        ]);

                        $this->CI->db->insert('tb_plan_reminders', [
                            'plan_id'       => $item->plan_id,
                            'user_id'       => $item->user_id,
                            'reminder_type' => $item->reminder_type,
                            'reminder_date' => date('Y-m-d H:i:s')
                        ]);

                        $processed++;
                    } else {
                        $maxAttempts = $item->max_attempts ?? $this->maxAttempts;
                        $errorMsg = $item->last_error ?? 'Send failed';
                        $errorCounts[$errorMsg] = ($errorCounts[$errorMsg] ?? 0) + 1;

                        if ($item->attempts + 1 >= $maxAttempts) {
                            $this->_markFailed($item, $errorMsg);
                            $failed++;
                        } else {
                            $delayMinutes = min(5 * pow(2, $item->attempts), 120);
                            $newScheduledAt = date('Y-m-d H:i:s', strtotime("+{$delayMinutes} minutes"));
                            $this->CI->db->where('id', $item->id)->update('tb_plan_reminder_queue', [
                                'status'       => 'pending',
                                'scheduled_at' => $newScheduledAt,
                                'last_error'   => $errorMsg
                            ]);
                        }
                    }

                    usleep(100000);
                }

                $execTime = round(microtime(true) - $startTime, 4);

                // Build detailed log message with error breakdown
                $logMessage = "Emails sent: {$processed}, Failed: {$failed}";
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
                $execTime = round(microtime(true) - $startTime, 4);
                if (isset($cronId)) {
                    $this->_updateLastRun($cronId, $execTime);
                }
                if (isset($cron)) {
                    $this->_logExecutionOnException($cron, $execTime, $e->getMessage());
                }
                log_message('error', 'PlanReminderConsumer Exception: ' . $e->getMessage());
                return [
                    'status'         => 'error',
                    'emails_sent'    => 0,
                    'failed'         => 0,
                    'display_result' => 'Exception: ' . $e->getMessage()
                ];
            } finally {
                if (isset($cronId)) {
                    $this->_setCronRunning($cronId, false);
                }
                $this->_unlock($this->lockFile);
            }
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
                case 'daily':
                    return ['allowed' => true, 'message' => 'Daily schedule allowed'];
                case 'weekly':
                    if (empty($cron->week_days)) {
                        return ['allowed' => false, 'message' => 'No week days configured'];
                    }
                    $days = array_map('trim', explode(',', strtolower($cron->week_days)));
                    if (!in_array($todayDay, $days)) {
                        return ['allowed' => false, 'message' => 'Not a scheduled weekday'];
                    }
                    return ['allowed' => true, 'message' => 'Weekly schedule allowed'];
                case 'monthly':
                    if (empty($cron->month_day)) {
                        return ['allowed' => false, 'message' => 'No month day configured'];
                    }
                    if ((int)date('j') != $cron->month_day) {
                        return ['allowed' => false, 'message' => 'Not monthly scheduled date'];
                    }
                    return ['allowed' => true, 'message' => 'Monthly schedule allowed'];
                case 'custom':
                    if (empty($cron->custom_schedule)) {
                        return ['allowed' => false, 'message' => 'No custom schedule'];
                    }
                    $customDateTime = date('Y-m-d H:i:00', strtotime($cron->custom_schedule));
                    return date('Y-m-d H:i:00') >= $customDateTime
                        ? ['allowed' => true, 'message' => 'Custom schedule time reached']
                        : ['allowed' => false, 'message' => 'Custom schedule time not yet reached'];
                default:
                    return ['allowed' => false, 'message' => 'Invalid schedule type'];
            }
        }

        // ✅ Subject mein plan name, recommendations added, emojis removed
        private function _sendReminderEmail($queueItem, $user, $purchase)
        {
            $daysBefore = (int) str_replace('_days', '', $queueItem->reminder_type);
            $planDisplayName = !empty($purchase->feature_name) ? $purchase->feature_name : 'Premium Plan';

            if ($daysBefore == 0) {
                $subject = "Reminder: Your {$planDisplayName} Plan Expires Today!";
            } elseif ($daysBefore == 1) {
                $subject = "Reminder: Your {$planDisplayName} Plan Expires Tomorrow";
            } else {
                $subject = "Reminder: Your {$planDisplayName} Plan Expires in {$daysBefore} days";
            }

            // Recommendations laao (current plan ko chhodkar)
            $recommendations = $this->_getRecommendedPlans($purchase->user_id, $purchase->feature_id);

            $message = $this->_buildEmailHtml($purchase, $daysBefore, $user->name ?? 'User', $recommendations);

            // ✅ Use per-job service instead of USE_MAILERCLOUD
            if ($this->useMailercloud) {
                $result = send_mailercloud_email($user->email, $user->name ?? '', $subject, $message);
                if ($result['status'] === 'success') {
                    return true;
                }
                $errorMsg = $this->_extractError($result);
            } else {
                $smtpResult = SendEmailTo($user->email, $subject, $message);
                if ($smtpResult['status'] === 'success') {
                    return true;
                }
                $errorMsg = !empty($smtpResult['errors'])
                    ? substr(json_encode($smtpResult['errors']), 0, 500)
                    : ($smtpResult['message'] ?? 'SMTP failed');
            }

            $queueItem->last_error = $errorMsg;
            return false;
        }

        // ✅ Email HTML with compact recommended plans
        private function _buildEmailHtml($purchase, $daysBefore, $userName, $recommendations = [])
        {
            $expiry_date = date('l, d M Y', strtotime($purchase->end_date));
            $plan_name = !empty($purchase->feature_name) ? $purchase->feature_name : 'Premium Plan';
            $service = !empty($purchase->service_name) ? $purchase->service_name : 'Service';

            $urgency_color = ($daysBefore == 0) ? '#e74c3c' : (($daysBefore == 1) ? '#f39c12' : '#3498db');
            $urgency_text  = ($daysBefore == 0) ? 'EXPIRES TODAY!' : (($daysBefore == 1) ? 'EXPIRES TOMORROW' : "EXPIRES IN {$daysBefore} DAYS");

            $open_pixel = base_url('track/plan_reminder?candidate_id=' . $purchase->user_id);
            $renew_url  = base_url('track/plan_reminder?candidate_id=' . $purchase->user_id . '&plan_id=' . $purchase->id . '&redirect=' . urlencode('https://resume.talentsjobs.in/career-services'));

            $html = '<!DOCTYPE html>
            <html>
            <head><meta charset="UTF-8"></head>
            <body style="font-family:Arial, sans-serif;">
                <img src="' . $open_pixel . '" width="1" height="1" style="display:none;" />
                <div style="background:' . $urgency_color . '; color:white; padding:20px; text-align:center;">
                    <h2>PLAN EXPIRY REMINDER</h2>
                    <p>' . $urgency_text . '</p>
                </div>
                <div style="padding:20px;">
                    <p>Hello ' . htmlspecialchars($userName) . ',</p>
                    <p>Your plan <strong>' . htmlspecialchars($plan_name) . '</strong> (' . $service . ') expires on <strong>' . $expiry_date . '</strong>.</p>
                    <p><a href="' . $renew_url . '" style="background:#764ba2; color:white; padding:10px 20px; text-decoration:none; border-radius:5px;">Renew Now</a></p>
                </div>';

            // --- Compact recommended plans (no emoji) ---
            if (!empty($recommendations)) {
                $html .= '
                <div style="padding:0 20px 20px 20px;">
                    <hr style="border:0;border-top:1px solid #eee;margin:10px 0;" />
                    <p style="font-size:14px;color:#555;margin:0 0 10px 0;">You might also like</p>
                    <table style="width:100%;border-collapse:collapse;font-size:12px;color:#333;">';

                foreach ($recommendations as $rec) {
                    $recName = htmlspecialchars($rec['name']);
                    $recDuration = $rec['duration'] ?? '';
                    $recPrice = isset($rec['price']) ? '₹' . number_format($rec['price'], 0) : '';
                    $recMonthly = isset($rec['monthly']) ? '₹' . number_format($rec['monthly'], 0) . '/mo' : '';
                    $recLink = base_url('career-services#' . ($rec['slug'] ?? ''));

                    $html .= '
                    <tr>
                        <td style="padding:5px 8px;border-bottom:1px solid #f0f0f0;">
                            <strong>' . $recName . '</strong><br>
                            <span style="color:#888;">' . $recDuration . ' &nbsp; ' . $recMonthly . '</span>
                        </td>
                        <td style="padding:5px 8px;border-bottom:1px solid #f0f0f0;text-align:right;white-space:nowrap;">
                            <span style="font-weight:bold;color:#e74c3c;">' . $recPrice . '</span>
                        </td>
                        <td style="padding:5px 8px;border-bottom:1px solid #f0f0f0;text-align:right;">
                            <a href="' . $recLink . '" style="background:#764ba2;color:white;padding:4px 10px;text-decoration:none;border-radius:3px;font-size:11px;">View</a>
                        </td>
                    </tr>';
                }

                $html .= '
                    </table>
                </div>';
            }

            $html .= '
            </body>
            </html>';

            return $html;
        }

        // ✅ 3 active features jo user ne nahi liye (cheapest plan details)
        private function _getRecommendedPlans($userId, $excludeFeatureId)
        {
            $purchased = $this->CI->db
                ->select('feature_id')
                ->where('user_id', $userId)
                ->get('tb_ft_user_purchases')
                ->result();
            $purchasedIds = array_column($purchased, 'feature_id');
            $purchasedIds[] = $excludeFeatureId;

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

        private function _extractError($emailResponse)
        {
            $msg = 'Unknown error';
            if (isset($emailResponse['data']['errors']) && !empty($emailResponse['data']['errors'])) {
                $errors = $emailResponse['data']['errors'];
                $msg = is_array($errors) ? implode(', ', $errors) : (string)$errors;
            } elseif (isset($emailResponse['message'])) {
                $msg = $emailResponse['message'];
            }
            return substr(strip_tags($msg), 0, 500);
        }

        private function _markFailed($item, $errorMsg)
        {
            $this->CI->db->where('id', $item->id)->update('tb_plan_reminder_queue', [
                'status'       => 'failed',
                'last_error'   => $errorMsg,
                'processed_at' => date('Y-m-d H:i:s')
            ]);
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

        private function _isLocked($file, $ttl = 1800)
        {
            return file_exists($file) && (time() - filemtime($file)) < $ttl;
        }

        private function _unlock($file)
        {
            if (file_exists($file)) @unlink($file);
        }

        private function _updateLastRun($cronId, $execTime)
        {
            $this->CI->db->where('id', $cronId)->update('tb_jr_cron_jobs', [
                'last_run'            => date('Y-m-d H:i:s'),
                'last_execution_time' => $execTime
            ]);
        }

        private function _logExecutionOnException($cron, $execTime, $errorMessage)
        {
            $this->CI->db->insert('tb_jr_cron_execution_logs', [
                'cron_job_id'     => $cron->id,
                'cron_name'       => $cron->name ?? 'PlanReminderConsumer',
                'status'          => 'error',
                'message'         => 'Exception: ' . $errorMessage,
                'processed_count' => 0,
                'failed_count'    => 0,
                'execution_time'  => $execTime,
                'raw_response'    => json_encode(['error' => $errorMessage]),
                'created_at'      => date('Y-m-d H:i:s')
            ]);
        }
    }

}