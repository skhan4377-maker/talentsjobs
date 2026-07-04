<?php
defined('BASEPATH') OR exit('No direct script access allowed');
date_default_timezone_set('Asia/Kolkata');

class CampaignSender
{
    protected $CI;
    private $batchSize = 50;
    private $sleepMicro = 50000;

    public function __construct()
    {
        $this->CI =& get_instance();
        $this->CI->load->database();
        $this->CI->load->library('email');
        $this->CI->load->helper('url');
    }

    /**
     * @param array $config  Optional overrides like ['cron_job_id' => X, 'cron_name' => 'Y', 'batch_size' => 100]
     */
    public function run($config = [])
    {
        $lockFile = sys_get_temp_dir() . '/campaign_sender.lock';
        if ($this->_isLocked($lockFile, 600)) {
            echo "⏭ Campaign Sender already running\n";
            return;
        }
        file_put_contents($lockFile, time());

        $startTime = microtime(true);
        $cron = null;
        $cronId = null;
        $errorCounts = [];

        try {
            // ---------- Fetch active cron ----------
            if (!empty($config['cron_job_id']) && !empty($config['cron_name'])) {
                $cronId = (int)$config['cron_job_id'];
                $cron = (object) ['id' => $cronId, 'name' => $config['cron_name']];
            } else {
                $cron = $this->_getActiveCron();
                if ($cron) $cronId = (int)$cron->id;
            }

            if (!$cronId) {
                echo "ℹ️ No active campaign cron found\n";
                return;
            }

            // ✅ Use per‑job emails_per_run (fallback to hardcoded 50)
            if (!empty($cron->emails_per_run) && (int)$cron->emails_per_run > 0) {
                $this->batchSize = (int)$cron->emails_per_run;
            }
            // ✅ Manual override from $config (highest priority)
            if (!empty($config['batch_size']) && (int)$config['batch_size'] > 0) {
                $this->batchSize = (int)$config['batch_size'];
            }

            // running check
            if ($cron->is_running && !empty($cron->running_since)) {
                $runningSinceTimestamp = strtotime($cron->running_since);
                if (time() - $runningSinceTimestamp < 600) {
                    echo "⚠️ Cron already running since {$cron->running_since}\n";
                    return;
                }
            }

            $this->CI->db->where('id', $cronId)->update('tb_jr_cron_jobs', [
                'is_running'    => 1,
                'running_since' => date('Y-m-d H:i:s')
            ]);

            $sched = $this->_isScheduleAllowed($cron);
            if (!$sched['allowed']) {
                $this->_updateLastRun($cronId, microtime(true) - $startTime);
                echo "⏭ " . $sched['message'] . "\n";
                return;
            }

            // ---------- Fetch pending tasks ----------
            $tasks = $this->CI->db
                ->select('tb_campaign_queue.*')
                ->from('tb_campaign_queue')
                ->join('tb_campaigns', 'tb_campaign_queue.campaign_id = tb_campaigns.id')
                ->where('tb_campaign_queue.status', 'pending')
                ->where('tb_campaign_queue.scheduled_at <=', date('Y-m-d H:i:s'))
                ->where('tb_campaigns.status', 'active')
                ->where('tb_campaigns.start_date <=', date('Y-m-d H:i:s'))
                ->where('tb_campaigns.end_date >=', date('Y-m-d H:i:s'))
                ->limit($this->batchSize)
                ->get()
                ->result();

            if (empty($tasks)) {
                echo "ℹ️ No pending emails\n";
                if ($cronId) $this->_updateLastRun($cronId, microtime(true) - $startTime);
                return;
            }

            $processed = 0;
            $failed    = 0;

            // ---------- Process each task ----------
            foreach ($tasks as $task) {
                $this->CI->db->where('id', $task->id)
                             ->where('status', 'pending')
                             ->update('tb_campaign_queue', [
                                 'status'               => 'processing',
                                 'attempts'             => $task->attempts + 1,
                                 'processing_started_at'=> date('Y-m-d H:i:s')
                             ]);

                if ($this->CI->db->affected_rows() == 0) continue;

                $sendResult = $this->_sendEmail($task);
                $success = $sendResult['success'];
                $errorMsg = $sendResult['error'];

                if ($success) {
                    $processed++;
                    echo "✅ Email sent to {$task->email}\n";
                } else {
                    $this->CI->db->where('id', $task->id)->update('tb_campaign_queue', [
                        'last_error' => $errorMsg
                    ]);

                    $errorCounts[$errorMsg] = ($errorCounts[$errorMsg] ?? 0) + 1;

                    if ($task->attempts + 1 >= $task->max_attempts) {
                        $this->CI->db->where('id', $task->id)->update('tb_campaign_queue', [
                            'status'       => 'failed',
                            'processed_at' => date('Y-m-d H:i:s')
                        ]);
                        $failed++;
                        echo "❌ Permanently failed for {$task->email} - Error: {$errorMsg}\n";
                    } else {
                        $delay = min(5 * pow(2, $task->attempts), 120);
                        $newTime = date('Y-m-d H:i:s', strtotime("+{$delay} minutes"));
                        $this->CI->db->where('id', $task->id)->update('tb_campaign_queue', [
                            'status'       => 'pending',
                            'scheduled_at' => $newTime
                        ]);
                        echo "⏳ Rescheduled {$task->email} (attempt " . ($task->attempts + 1) . ") – Error: {$errorMsg}\n";
                    }
                }

                usleep($this->sleepMicro);
            }

            $execTime = round(microtime(true) - $startTime, 4);

            // ---------- Execution log ----------
            if (($processed > 0 || $failed > 0 || !empty($errorCounts)) && $cron) {
                if ($processed == 0 && $failed == 0) {
                    $status = 'warning';
                } else {
                    $status = $processed > 0 ? ($failed > 0 ? 'partial' : 'success') : 'failed';
                }

                $logMessage = "Processed: {$processed}, Failed: {$failed}";
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

                $this->_logExecution($cron, $status, $processed, $failed, $execTime, $logMessage);
            }

            if ($cronId) $this->_updateLastRun($cronId, $execTime);
            echo "✅ Processed: {$processed}, Failed: {$failed}\n";

        } catch (Throwable $e) {
            $execTime = round(microtime(true) - $startTime, 4);
            log_message('error', 'CampaignSender Exception: ' . $e->getMessage());
            if ($cron) {
                $this->_logExecution($cron, 'failed', 0, 0, $execTime, 'Exception: ' . $e->getMessage());
            }
            if ($cronId) $this->_updateLastRun($cronId, $execTime);
            echo "❌ Exception: " . $e->getMessage() . "\n";
        } finally {
            if ($cronId) {
                $this->CI->db->where('id', $cronId)->update('tb_jr_cron_jobs', [
                    'is_running'    => 0,
                    'running_since' => null
                ]);
            }
            @unlink($lockFile);
        }
    }

    // ----------------------------------------------------------------
    // SCHEDULE CHECK (unchanged)
    // ----------------------------------------------------------------
    private function _isScheduleAllowed($cron)
    {
        $nowTime  = date('H:i:s');
        $todayDay = strtolower(date('l'));

        if ($cron->schedule_type !== 'custom') {
            if ($nowTime < $cron->start_time || $nowTime > $cron->end_time) {
                return ['allowed' => false, 'message' => "Outside allowed time window ({$cron->start_time} - {$cron->end_time})"];
            }
        }

        switch ($cron->schedule_type) {
            case 'daily': return ['allowed' => true, 'message' => 'Daily schedule allowed'];
            case 'weekly':
                if (empty($cron->week_days)) return ['allowed' => false, 'message' => 'No week days configured'];
                $days = array_map('trim', explode(',', strtolower($cron->week_days)));
                if (!in_array($todayDay, $days)) return ['allowed' => false, 'message' => 'Not a scheduled weekday'];
                return ['allowed' => true, 'message' => 'Weekly schedule allowed'];
            case 'monthly':
                if (empty($cron->month_day)) return ['allowed' => false, 'message' => 'No month day configured'];
                if ((int)date('j') != $cron->month_day) return ['allowed' => false, 'message' => 'Not monthly scheduled date'];
                return ['allowed' => true, 'message' => 'Monthly schedule allowed'];
            case 'custom':
                if (empty($cron->custom_schedule)) return ['allowed' => false, 'message' => 'No custom schedule'];
                $customDateTime = date('Y-m-d H:i:00', strtotime($cron->custom_schedule));
                return date('Y-m-d H:i:00') >= $customDateTime
                    ? ['allowed' => true, 'message' => 'Custom schedule time reached']
                    : ['allowed' => false, 'message' => 'Custom schedule time not yet reached'];
            default: return ['allowed' => false, 'message' => 'Invalid schedule type'];
        }
    }

    // ----------------------------------------------------------------
    // TRACKING HELPERS (unchanged)
    // ----------------------------------------------------------------
    private function _generateTrackingToken($queue_id)
    {
        $secret = $this->CI->config->item('campaign_tracking_secret');
        if (empty($secret)) $secret = 'default-fallback-secret-change-me';
        return hash_hmac('sha256', $queue_id, $secret);
    }

    private function _generateUnsubscribeToken($campaign_id, $email)
    {
        $payload = $campaign_id . '|' . $email;
        return strtr(base64_encode($payload), '+/', '-_');
    }

    private function _addCampaignTracking($body, $token)
    {
        if (empty($body)) return $body;
        $trackingUrl = site_url("track/campaign_open/{$token}");
        $pixel = '<img src="' . $trackingUrl . '" width="1" height="1" style="display:none;" />';
        if (stripos($body, '</body>') !== false) {
            $body = str_ireplace('</body>', $pixel . '</body>', $body);
        } else {
            $body .= $pixel;
        }
        $clickBase = site_url("track/campaign_click/{$token}?url=");
        $body = preg_replace_callback(
            '/href=["\'](https?:\/\/[^"\']+)["\']/i',
            function ($matches) use ($clickBase) {
                return 'href="' . $clickBase . urlencode($matches[1]) . '"';
            },
            $body
        );
        return $body;
    }

    // ----------------------------------------------------------------
    // SEND EMAIL – uses title/description + campaign's email_service
    // ----------------------------------------------------------------
    private function _sendEmail($task)
    {
        $toEmail = $task->email ?? '';
        $toName  = $task->name ?? '';

        if (empty($toEmail)) {
            return ['success' => false, 'error' => 'Missing email'];
        }

        $campaign = $this->CI->db
            ->select('title, description, email_service')
            ->where('id', $task->campaign_id)
            ->get('tb_campaigns')
            ->row();

        if (!$campaign) {
            return ['success' => false, 'error' => 'Campaign not found'];
        }

        $subject = $campaign->title;
        $message = $campaign->description;

        if (empty($subject) && empty($message)) {
            return ['success' => false, 'error' => 'Campaign email template is empty'];
        }

        $name        = $task->name ?? '';
        $designation = $task->designation ?? '';

        if (stripos($subject, '{{name}}') === false && stripos($subject, '{{designation}}') === false) {
            $parts = [];
            if (!empty($name)) $parts[] = trim($name);
            $parts[] = trim($subject);
            if (!empty($designation)) $parts[] = trim($designation);
            $subject = implode(' | ', $parts);
        }

        if (stripos($message, '{{name}}') === false) {
            $greeting = '<p>Hello ' . (!empty($name) ? $name : 'Candidate') . ',</p>';
            $message = $greeting . $message;
        }

        $placeholders = [
            '{{name}}'        => htmlspecialchars($name, ENT_QUOTES, 'UTF-8'),
            '{{designation}}' => htmlspecialchars($designation, ENT_QUOTES, 'UTF-8'),
            '{{email}}'       => htmlspecialchars($toEmail, ENT_QUOTES, 'UTF-8'),
        ];
        $subject = str_replace(array_keys($placeholders), array_values($placeholders), $subject);
        $message = str_replace(array_keys($placeholders), array_values($placeholders), $message);

        $unsubscribe_token = $this->_generateUnsubscribeToken($task->campaign_id, $task->email);
        $unsubscribe_url = site_url("unsubscribe/email_unsubscribe/{$unsubscribe_token}");
        $unsubscribe_footer = '
            <hr style="border:0;border-top:1px solid #e5e7eb;margin:20px 0 10px 0;">
            <p style="font-size:11px;color:#6b7280;text-align:center;margin:0;">
                If you no longer wish to receive these emails,
                <a href="' . $unsubscribe_url . '" style="color:#3b82f6;">unsubscribe here</a>.
            </p>';

        if (stripos($message, '</body>') !== false) {
            $message = str_ireplace('</body>', $unsubscribe_footer . '</body>', $message);
        } else {
            $message .= $unsubscribe_footer;
        }

        if (empty($task->tracking_token)) {
            $token = $this->_generateTrackingToken($task->id);
            $this->CI->db->where('id', $task->id)->update('tb_campaign_queue', ['tracking_token' => $token]);
            $task->tracking_token = $token;
        }

        $modifiedMessage = $this->_addCampaignTracking($message, $task->tracking_token);

        $useMailercloud = ($campaign->email_service === 'mailercloud');

        if ($useMailercloud) {
            $result = send_mailercloud_email($toEmail, $toName, $subject, $modifiedMessage);
            if (is_array($result) && isset($result['status']) && $result['status'] === 'success') {
                $this->CI->db->where('id', $task->id)->update('tb_campaign_queue', [
                    'status'       => 'sent',
                    'subject'      => $subject,
                    'body'         => $modifiedMessage,
                    'last_error'   => null,
                    'processed_at' => date('Y-m-d H:i:s')
                ]);
                return ['success' => true, 'error' => ''];
            }
            $errorMsg = is_array($result) && isset($result['message']) ? $result['message'] : 'Unknown error';
        } else {
            $smtpResult = SendEmailTo($toEmail, $subject, $modifiedMessage);
            if (is_array($smtpResult) && isset($smtpResult['status']) && $smtpResult['status'] === 'success') {
                $this->CI->db->where('id', $task->id)->update('tb_campaign_queue', [
                    'status'       => 'sent',
                    'subject'      => $subject,
                    'body'         => $modifiedMessage,
                    'last_error'   => null,
                    'processed_at' => date('Y-m-d H:i:s')
                ]);
                return ['success' => true, 'error' => ''];
            }
            $errorMsg = !empty($smtpResult['errors'])
                ? substr(json_encode($smtpResult['errors']), 0, 500)
                : ($smtpResult['message'] ?? 'SMTP failed');
        }

        return ['success' => false, 'error' => $errorMsg ?? 'Unknown error'];
    }

    // ----------------------------------------------------------------
    // LOGGING & HELPERS (unchanged)
    // ----------------------------------------------------------------
    private function _logExecution($cron, $status, $processed, $failed, $execTime, $message = null)
    {
        if (!$cron) return;
        $this->CI->db->insert('tb_jr_cron_execution_logs', [
            'cron_job_id'     => $cron->id,
            'cron_name'       => $cron->name ?? 'CampaignSender',
            'status'          => $status,
            'message'         => $message ?? ("Campaign emails processed: {$processed}, failed: {$failed}"),
            'processed_count' => $processed,
            'failed_count'    => $failed,
            'execution_time'  => $execTime,
            'raw_response'    => json_encode(['processed' => $processed, 'failed' => $failed]),
            'created_at'      => date('Y-m-d H:i:s')
        ]);
    }

    private function _getActiveCron()
    {
        return $this->CI->db
            ->where('context', 'newsletter')
            ->where('is_active', 1)
            ->get('tb_jr_cron_jobs')
            ->row();
    }

    private function _updateLastRun($cronId, $execTime)
    {
        if (!$cronId) return;
        $this->CI->db->where('id', $cronId)->update('tb_jr_cron_jobs', [
            'last_run'            => date('Y-m-d H:i:s'),
            'last_execution_time' => $execTime
        ]);
    }

    private function _isLocked($file, $ttl = 600)
    {
        return file_exists($file) && (time() - filemtime($file)) < $ttl;
    }
}