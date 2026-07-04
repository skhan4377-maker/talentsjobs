<?php
defined('BASEPATH') OR exit('No direct script access allowed');
date_default_timezone_set('Asia/Kolkata');

use Google\Auth\Credentials\ServiceAccountCredentials;

class PushConsumer
{
    protected $CI;
    private $projectId = 'govtjobs-ai-prod';
    private $batchSize = 200;

    public function __construct() {
        $this->CI =& get_instance();
        $this->CI->load->database();
    }

    public function run() {
        $lockFile = sys_get_temp_dir() . '/push_consumer.lock';
        if ($this->_isLocked($lockFile, 300)) {
            echo "⏭ Consumer already running\n";
            return;
        }
        file_put_contents($lockFile, time());

        $startTime = microtime(true);
        $cron = $this->_getActiveCron();
        $cronId = $cron ? $cron->id : null;

        // ✅ FIX: Stop immediately if no active cron job
        if (!$cron) {
            echo "ℹ️ No active push cron found. Exiting.\n";
            @unlink($lockFile);
            return;
        }

        // ✅ ADDED: Schedule check (start_time, end_time, weekly/monthly/custom)
        $sched = $this->_isScheduleAllowed($cron);
        if (!$sched['allowed']) {
            $this->_updateLastRun($cronId, microtime(true) - $startTime);
            echo "⏭ " . $sched['message'] . "\n";
            @unlink($lockFile);
            return;
        }

        try {
            // Check & set database running state
            if ($cron->is_running && !empty($cron->running_since)) {
                $runningSinceTimestamp = strtotime($cron->running_since);
                if (time() - $runningSinceTimestamp < 300) {
                    echo "⚠️ Push consumer already running since {$cron->running_since}\n";
                    return;
                }
            }
            $this->CI->db->where('id', $cronId)->update('tb_jr_cron_jobs', [
                'is_running'    => 1,
                'running_since' => date('Y-m-d H:i:s')
            ]);

            $accessToken = $this->_getAccessToken();
            if (!$accessToken) {
                echo "❌ Failed to get FCM access token\n";
                if ($cronId) $this->_updateLastRun($cronId, microtime(true) - $startTime);
                return;
            }

            $tasks = $this->CI->db
                ->where('status', 'pending')
                ->where('scheduled_at <=', date('Y-m-d H:i:s'))
                ->limit($this->batchSize)
                ->get('tb_push_notification_queue')
                ->result();

            if (empty($tasks)) {
                echo "ℹ️ No pending tasks\n";
                if ($cronId) $this->_updateLastRun($cronId, microtime(true) - $startTime);
                return;
            }

            // Fetch all tokens in one query
            $tokenIds = array_column($tasks, 'user_id');
            $tokensMap = [];
            if (!empty($tokenIds)) {
                $tokenRows = $this->CI->db
                    ->select('id, token')
                    ->where_in('id', $tokenIds)
                    ->where('is_active', 1)
                    ->get('tb_push_tokens')
                    ->result();
                foreach ($tokenRows as $row) {
                    $tokensMap[$row->id] = $row->token;
                }
            }

            $processed = 0;
            $failed    = 0;

            foreach ($tasks as $task) {
                // Optimistic lock
                $this->CI->db->where('id', $task->id)
                             ->where('status', 'pending')
                             ->update('tb_push_notification_queue', [
                                 'status'               => 'processing',
                                 'attempts'             => $task->attempts + 1,
                                 'processing_started_at'=> date('Y-m-d H:i:s')
                             ]);

                if ($this->CI->db->affected_rows() == 0) {
                    continue;
                }

                $tokenString = $tokensMap[$task->user_id] ?? null;
                if (empty($tokenString)) {
                    $this->_markFailed($task, 'Token not found or inactive');
                    $failed++;
                    echo "❌ Token not found for ID {$task->user_id}\n";
                    continue;
                }

                $data = json_decode($task->data_payload, true);
                $success = $this->_sendToToken($tokenString, $data, $accessToken, $task);

                if ($success) {
                    $this->CI->db->where('id', $task->id)->update('tb_push_notification_queue', [
                        'status'       => 'sent',
                        'processed_at' => date('Y-m-d H:i:s')
                    ]);

                    $this->CI->db->insert('tb_push_notification_log', [
                        'user_id'      => $task->user_id,
                        'job_id'       => $task->job_id,
                        'data_payload' => $task->data_payload,
                        'sent_at'      => date('Y-m-d H:i:s')
                    ]);
                    $processed++;
                    echo "✅ Sent to token ID {$task->user_id}\n";
                } else {
                    $maxAttempts = $task->max_attempts ?? 3;
                    if ($task->attempts + 1 >= $maxAttempts) {
                        $this->_markFailed($task, $task->last_error ?? 'Max attempts reached');
                        $failed++;
                        echo "❌ Permanently failed for token ID {$task->user_id}\n";
                    } else {
                        $delayMinutes = min(5 * pow(2, $task->attempts), 120);
                        $newScheduledAt = date('Y-m-d H:i:s', strtotime("+{$delayMinutes} minutes"));
                        $this->CI->db->where('id', $task->id)->update('tb_push_notification_queue', [
                            'status'       => 'pending',
                            'scheduled_at' => $newScheduledAt,
                            'last_error'   => $task->last_error ?? 'Send failed'
                        ]);
                        echo "⏳ Rescheduled token ID {$task->user_id} (attempt ".($task->attempts+1).")\n";
                    }
                }
                usleep(50000);
            }

            $execTime = round(microtime(true) - $startTime, 4);
            $status = $processed > 0 ? ($failed > 0 ? 'partial' : 'success') : ($failed > 0 ? 'failed' : 'no_action');

            if (($processed > 0 || $failed > 0) && $cron) {
                $this->_logExecution($cron, $status, $processed, $failed, $execTime);
            }

            if ($cronId) $this->_updateLastRun($cronId, $execTime);

            echo "✅ Processed: {$processed}, Failed: {$failed}\n";

        } catch (Throwable $e) {
            $execTime = round(microtime(true) - $startTime, 4);
            if ($cron) {
                $this->_logExecution($cron, 'failed', 0, 0, $execTime, $e->getMessage());
            }
            if ($cronId) $this->_updateLastRun($cronId, $execTime);
            log_message('error', 'PushConsumer Exception: ' . $e->getMessage());
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

    // ======================= SCHEDULE CHECK =========================

    private function _isScheduleAllowed($cron) {
        $nowTime  = date('H:i:s');
        $todayDay = strtolower(date('l'));

        // For non-custom schedule, check time window
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

    // ======================= PRIVATE HELPERS =========================

    private function _sendToToken($token, $data, $accessToken, $task = null) {
        return $this->_sendFcm($token, $data, $accessToken, $task);
    }

    private function _sendFcm($token, $data, $accessToken, $task = null) {
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => "https://fcm.googleapis.com/v1/projects/{$this->projectId}/messages:send",
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => [
                "Authorization: Bearer " . $accessToken,
                "Content-Type: application/json"
            ],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POSTFIELDS => json_encode([
                "message" => [
                    "token" => $token,
                    "data"  => $data,
                    "webpush" => [
                        "notification" => [
                            "title" => $data['title'] ?? '',
                            "body"  => $data['body'] ?? '',
                            "icon"  => $data['icon'] ?? '',
                            "badge" => base_url('assets/frontend/favicon.ico')
                        ],
                        "fcm_options" => [
                            "link" => $data['link'] ?? ''
                        ]
                    ]
                ]
            ])
        ]);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($curlError) {
            if ($task) {
                $task->last_error = "Curl error: " . $curlError;
            }
            return false;
        }

        $res = json_decode($response, true);
        if (isset($res['error'])) {
            $errorCode = $res['error']['details'][0]['errorCode'] ?? $res['error']['status'] ?? 'UNKNOWN';
            $errorMsg  = $res['error']['message'] ?? 'FCM error';

            if ($errorCode === 'UNREGISTERED' || $errorCode === 'INVALID_ARGUMENT') {
                $this->CI->db->where('token', $token)->delete('tb_push_tokens');
                log_message('info', "Deleted invalid token: $token");
                if ($task) {
                    $task->last_error = "Invalid token ($errorCode): $errorMsg";
                }
            } else {
                if ($task) {
                    $task->last_error = "FCM error ($errorCode): $errorMsg";
                }
            }
            return false;
        }

        if (isset($res['name']) && empty($res['error'])) {
            return true;
        }

        if ($task) {
            $task->last_error = "Unknown FCM response: " . substr($response, 0, 200);
        }
        return false;
    }

    private function _markFailed($task, $errorMsg) {
        $this->CI->db->where('id', $task->id)->update('tb_push_notification_queue', [
            'status'       => 'failed',
            'last_error'   => $errorMsg,
            'processed_at' => date('Y-m-d H:i:s')
        ]);
    }

    private function _getAccessToken() {
        $keyPath = FCPATH . 'keys/firebase.json';
        if (!file_exists($keyPath)) return false;

        $credentials = new ServiceAccountCredentials(
            ['https://www.googleapis.com/auth/firebase.messaging'],
            json_decode(file_get_contents($keyPath), true)
        );
        $token = $credentials->fetchAuthToken();
        return $token['access_token'] ?? false;
    }

    private function _isLocked($file, $ttl = 300) {
        return file_exists($file) && (time() - filemtime($file)) < $ttl;
    }

    private function _getActiveCron() {
        return $this->CI->db
            ->where('context', 'send_push_notifications')
            ->where('is_active', 1)
            ->get('tb_jr_cron_jobs')
            ->row();
    }

    private function _logExecution($cron, $status, $processed, $failed, $execTime, $message = null) {
        if (!$cron) return;

        $this->CI->db->insert('tb_jr_cron_execution_logs', [
            'cron_job_id'     => $cron->id,
            'cron_name'       => $cron->name ?? 'PushConsumer',
            'status'          => $status,
            'message'         => $message ?? ("Push notifications processed: {$processed}, failed: {$failed}"),
            'processed_count' => $processed,
            'failed_count'    => $failed,
            'execution_time'  => $execTime,
            'raw_response'    => json_encode(['processed' => $processed, 'failed' => $failed]),
            'created_at'      => date('Y-m-d H:i:s')
        ]);
    }

    private function _updateLastRun($cronId, $execTime) {
        $this->CI->db->where('id', $cronId)->update('tb_jr_cron_jobs', [
            'last_run'            => date('Y-m-d H:i:s'),
            'last_execution_time' => $execTime
        ]);
    }
}