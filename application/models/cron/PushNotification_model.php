<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use Google\Auth\Credentials\ServiceAccountCredentials;

class PushNotification_model extends CI_Model {

    private $projectId = 'govtjobs-ai-prod';
    private $dailyLimit = 6;

    public function __construct(){
        parent::__construct();
        $this->load->database();
    }

    // ========================= MAIN CRON ENTRY POINT =========================
    public function send_push_notifications($config = []) {
        $lockFile = sys_get_temp_dir() . '/push.lock';
        if ($this->_is_locked($lockFile)) {
            return $this->_response('skipped', 0, "⏭ Already running");
        }
        file_put_contents($lockFile, time());

        try {
            $cronId = isset($config['cron_id']) ? $config['cron_id'] : null;
            $executionLogId = isset($config['execution_log_id']) ? $config['execution_log_id'] : null;

            $cronJob = $this->db
                ->select('queue_per_run')
                ->from('tb_jr_cron_jobs')
                ->where('id', $cronId)
                ->get()
                ->row();
            $enqueueLimit = ($cronJob && $cronJob->queue_per_run > 0) ? (int)$cronJob->queue_per_run : 100;
            $sendLimit = $enqueueLimit;

            // Determine current slot for enqueue (used for job ordering and title)
            $slot = $this->_getCurrentSlot();

            // Step 1: Enqueue pushes (store only job_id and slot_hour)
            $enqueueResult = $this->_enqueue_pushes($enqueueLimit, $slot, $cronId, $executionLogId);

            // Step 2: Process queue (dynamically generate body, link, title)
            $processResult = $this->process_push_queue($sendLimit, $cronId, $executionLogId);

            $totalSent = $processResult['sent'];
            $totalFailed = $processResult['failed'];
            $totalQueued = $enqueueResult['queued'];

            $report = $this->_generate_report($totalQueued, $totalSent, $totalFailed);

            return [
                'status' => ($totalFailed > 0 && $totalSent == 0) ? 'error' : ($totalSent > 0 ? 'success' : 'no_action'),
                'emails_sent' => $totalSent,
                'display_result' => $report,
                'error_message' => ''
            ];
        } catch (Exception $e) {
            return [
                'status' => 'error',
                'emails_sent' => 0,
                'display_result' => "❌ Error: " . $e->getMessage(),
                'error_message' => $e->getMessage()
            ];
        } finally {
            $this->_unlock($lockFile);
        }
    }

    // ========================= ENQUEUE PUSHES (Minimal) =========================
    private function _enqueue_pushes($batchLimit, $slot, $cronId = null, $executionLogId = null)
    {
        $stats = ['processed' => 0, 'queued' => 0, 'failed' => 0];

        $accessToken = $this->_getAccessToken();
        if (!$accessToken) {
            echo "❌ No access token, enqueue skipped.\n";
            return $stats;
        }

        $users = $this->db
            ->select('*')
            ->from('tb_push_tokens')
            ->where('is_active', 1)
            ->limit($batchLimit)
            ->get()
            ->result();

        if (empty($users)) {
            echo "⚠️ No active devices found.\n";
            return $stats;
        }

        foreach ($users as $user) {
            $stats['processed']++;
            $this->_ensureUserState($user->device_id);

            if (!$this->_canSendToday($user->device_id)) {
                echo "⏭ Device {$user->device_id} daily limit reached.\n";
                continue;
            }

            // Get next unseen job using slot-based ordering
            $job = $this->_getJob($user->device_id, $slot);
            if (!$job) {
                echo "⚠️ No new job for device {$user->device_id}. Skipping.\n";
                continue;
            }

            // Store minimal data in queue (no title, body, link)
            $queueData = [
                'device_id'     => $user->device_id,
                'token'         => $user->token,
                'job_id'        => $job->job_id,
                'slot_hour'     => $slot,            // store slot to preserve title/ordering at send time
                'status'        => 'pending',
                'attempts'      => 0,
                'max_attempts'  => 3,
                'scheduled_at'  => date('Y-m-d H:i:s'),
                'created_at'    => date('Y-m-d H:i:s')
            ];
            $this->db->insert('tb_push_queue', $queueData);
            $stats['queued']++;
            echo "✅ Enqueued push for device {$user->device_id} (job ID {$job->job_id}, slot {$slot})\n";
            usleep(50000);
        }
        return $stats;
    }

    // ========================= QUEUE WORKER (Dynamically generate content) =========================
    public function process_push_queue($limit = 20, $cronId = null, $executionLogId = null) {
        $queueItems = $this->db
            ->select('*')
            ->from('tb_push_queue')
            ->where('status', 'pending')
            ->where('scheduled_at <=', date('Y-m-d H:i:s'))
            ->order_by('created_at', 'ASC')
            ->limit($limit)
            ->get()
            ->result();

        $stats = ['sent' => 0, 'failed' => 0];

        foreach ($queueItems as $item) {
            $this->db->where('id', $item->id)->update('tb_push_queue', [
                'status' => 'processing',
                'processed_at' => date('Y-m-d H:i:s')
            ]);

            $accessToken = $this->_getAccessToken();
            if (!$accessToken) {
                $this->_handle_queue_failure($item, 'Failed to get FCM access token', $cronId, $executionLogId);
                $stats['failed']++;
                continue;
            }

            // Fetch job details dynamically
            $job = $this->db->select('j.job_id, j.job_title, j.slug, j.min_salary, j.max_salary, e.company_name, c.city_name')
                ->from('tb_post_job j')
                ->join('tb_employer e', 'e.employer_id = j.employer_id', 'left')
                ->join('tb_cities c', 'c.city_id = e.city_id', 'left')
                ->where('j.job_id', $item->job_id)
                ->get()
                ->row();

            if (!$job) {
                $errorMsg = "Job ID {$item->job_id} not found or inactive";
                $this->_handle_queue_failure($item, $errorMsg, $cronId, $executionLogId);
                $stats['failed']++;
                continue;
            }

            // Dynamically generate title based on stored slot (or fallback to current slot)
            $slot = $item->slot_hour ?? $this->_getCurrentSlot();
            $title = $this->_getTitleBySlot($slot);

            $company = $job->company_name ?? 'Company';
            $location = $job->city_name ?? 'India';
            $salary = "Not Disclosed";
            if ($job->min_salary > 0 || $job->max_salary > 0) {
                $salary = "₹".$job->min_salary." - ₹".$job->max_salary;
            }
            $body = "{$job->job_title} | {$company} | {$salary} | {$location}";
            $link = base_url($job->slug);

            $data = [
                'title' => $title,
                'body'  => $body,
                'link'  => $link,
                'job_id'=> $job->job_id
            ];

            $sent = $this->_sendPush($item->token, $data, $accessToken);

            if ($sent) {
                $this->_update_on_success($item->device_id, $item->job_id);
                $this->db->where('id', $item->id)->update('tb_push_queue', [
                    'status' => 'sent',
                    'processed_at' => date('Y-m-d H:i:s')
                ]);
                $this->_log_push_event($cronId, $executionLogId, $item->device_id, $title, $item->job_id, 'sent', null);
                $stats['sent']++;
                echo "✅ Push sent to device: {$item->device_id}\n";
            } else {
                $newAttempts = $item->attempts + 1;
                if ($newAttempts >= $item->max_attempts) {
                    $this->_handle_queue_failure($item, 'Max attempts reached', $cronId, $executionLogId);
                    $stats['failed']++;
                } else {
                    $retryDelay = pow(2, $newAttempts) * 60;
                    $this->db->where('id', $item->id)->update('tb_push_queue', [
                        'status' => 'pending',
                        'attempts' => $newAttempts,
                        'last_error' => 'Retry after failure',
                        'scheduled_at' => date('Y-m-d H:i:s', strtotime("+{$retryDelay} seconds"))
                    ]);
                    echo "⚠️ Push retry {$newAttempts}/{$item->max_attempts} for device {$item->device_id}\n";
                    $stats['failed']++;
                }
            }
            usleep(50000);
        }
        return $stats;
    }

    private function _handle_queue_failure($item, $errorMsg, $cronId, $executionLogId)
    {
        $title = "Push Failed"; // fallback title for log
        $this->_log_push_event($cronId, $executionLogId, $item->device_id, $title, $item->job_id, 'failed', $errorMsg);
        $this->db->where('id', $item->id)->update('tb_push_queue', [
            'status' => 'failed',
            'last_error' => $errorMsg,
            'processed_at' => date('Y-m-d H:i:s')
        ]);
        echo "❌ Push failed permanently for device {$item->device_id}: $errorMsg\n";
    }

    // ========================= HELPER FUNCTIONS =========================

    private function _getJob($device_id, $slot)
    {
        $state = $this->db->where('device_id', $device_id)->get('tb_user_push_state')->row();
        $lastJobId = $state->last_job_id ?? 0;

        $this->db->select('j.job_id, j.job_title, j.slug, j.min_salary, j.max_salary,
                           e.company_name,
                           c.city_name');
        $this->db->from('tb_post_job j');
        $this->db->join('tb_employer e', 'e.employer_id = j.employer_id', 'left');
        $this->db->join('tb_cities c', 'c.city_id = e.city_id', 'left');
        $this->db->where('j.is_deleted', 0);
        $this->db->where('j.status', 'active');
        $this->db->where('j.job_id >', $lastJobId);

        if ($slot == 9 || $slot == 18) {
            $this->db->order_by('j.job_id', 'DESC');
        } elseif ($slot == 12 || $slot == 21) {
            $this->db->order_by('j.max_salary', 'DESC');
        } elseif ($slot == 15) {
            $this->db->order_by('RAND()');
        } else {
            $this->db->order_by('j.job_id', 'DESC');
        }
        return $this->db->limit(1)->get()->row();
    }

    private function _getTitleBySlot($slot)
    {
        if ($slot == 9 || $slot == 18) {
            return "🔥 Jobs Matching You";
        } elseif ($slot == 12 || $slot == 21) {
            return "🚀 Trending Jobs";
        } else {
            return "✨ New Job Opportunity";
        }
    }

    private function _sendPush($token, $data, $accessToken) {
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => "https://fcm.googleapis.com/v1/projects/".$this->projectId."/messages:send",
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => [
                "Authorization: Bearer ".$accessToken,
                "Content-Type: application/json"
            ],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POSTFIELDS => json_encode([
                "message" => [
                    "token" => $token,
                    "data" => $data
                ]
            ])
        ]);
        $res = curl_exec($ch);
        curl_close($ch);
        $response = json_decode($res, true);

        if (isset($response['error']['details'][0]['errorCode']) && $response['error']['details'][0]['errorCode'] == 'UNREGISTERED') {
            $this->db->where('token', $token)->delete('tb_push_tokens');
            log_message('error', "❌ Deleted invalid token: ".$token);
            return false;
        }
        return isset($response['name']) && empty($response['error']);
    }

    private function _ensureUserState($device_id)
    {
        if (!$this->db->where('device_id', $device_id)->get('tb_user_push_state')->row()) {
            $this->db->insert('tb_user_push_state', [
                'device_id' => $device_id,
                'last_job_id' => 0,
                'daily_count' => 0,
                'last_sent_date' => NULL
            ]);
        }
    }

    private function _incrementDaily($device_id) {
        $this->db->set('daily_count', 'daily_count+1', false)
                 ->where('device_id', $device_id)
                 ->update('tb_user_push_state');
    }

    private function _updateLastJob($device_id, $jobId)
    {
        $this->db->where('device_id', $device_id)
                 ->update('tb_user_push_state', ['last_job_id' => $jobId]);
    }

    private function _update_on_success($device_id, $job_id)
    {
        $this->_incrementDaily($device_id);
        $this->_updateLastJob($device_id, $job_id);
        $this->db->where('device_id', $device_id)
                 ->update('tb_user_push_state', ['last_sent_date' => date('Y-m-d')]);
    }

    private function _canSendToday($device_id)
    {
        $row = $this->db->where('device_id', $device_id)->get('tb_user_push_state')->row();
        $today = date('Y-m-d');
        if (!$row) return true;
        if ($row->last_sent_date != $today) {
            $this->db->where('device_id', $device_id)->update('tb_user_push_state', [
                'daily_count' => 0,
                'last_sent_date' => $today
            ]);
            return true;
        }
        return ($row->daily_count < $this->dailyLimit);
    }

    private function _getCurrentSlot() {
        $h = (int)date('H');
        if ($h >= 9 && $h < 12) return 9;
        if ($h >= 12 && $h < 15) return 12;
        if ($h >= 15 && $h < 18) return 15;
        if ($h >= 18 && $h < 21) return 18;
        if ($h >= 21 && $h < 24) return 21;
        return null;
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

    private function _log_push_event($cronId, $executionLogId, $device_id, $subject, $jobCount, $status, $errorMsg = null) {
        $data = [
            'cron_job_id' => $cronId,
            'cron_execution_log_id' => $executionLogId,
            'candidate_id' => 0,
            'email' => 'push_' . $device_id,
            'subject' => $subject,
            'job_count' => $jobCount,
            'sent_at' => date('Y-m-d H:i:s'),
            'status' => $status,
            'error_message' => $errorMsg
        ];
        $this->db->insert('tb_jr_cron_email_logs', $data);
    }

    private function _generate_report($queued, $sent, $failed)
    {
        return "📱 Push Notification Report (Queue System)\n" .
               "============================================\n" .
               "Newly Queued: {$queued}\n" .
               "Pushes Sent: {$sent}\n" .
               "Pushes Failed: {$failed}\n" .
               "Total Processed: " . ($sent + $failed) . "\n" .
               "Status: " . (($failed > 0 && $sent == 0) ? 'error' : ($sent > 0 ? 'success' : 'no_action')) . "\n";
    }

    private function _response($status, $count, $msg)
    {
        return [
            'status' => $status,
            'emails_sent' => $count,
            'display_result' => $msg,
            'error_message' => ''
        ];
    }

    private function _is_locked($lockFile)
    {
        $lockTtl = 1800;
        return file_exists($lockFile) && (time() - filemtime($lockFile)) < $lockTtl;
    }

    private function _unlock($lockFile)
    {
        if (file_exists($lockFile)) unlink($lockFile);
    }
}
?>