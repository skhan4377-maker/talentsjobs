<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * PlanReminderDispatcher
 * 
 * Finds expiring plans and inserts them into tb_plan_reminder_queue with status 'pending'.
 * No execution logs are written – only last_run is updated.
 */
class PlanReminderDispatcher
{
    protected $CI;
    private $lockFile;
    private $batchSize = 200;           // default max queue inserts per run

    public function __construct()
    {
        $this->CI =& get_instance();
        $this->CI->load->database();
    }

    /**
     * Run the dispatcher.
     * @param array $config  (optional) ['cron_job_id' => ..., 'cron_name' => ..., 'batch_size' => ...]
     * @return array
     */
    public function run($config = [])
    {
        $this->lockFile = sys_get_temp_dir() . '/plan_reminder_dispatcher.lock';

        // File lock
        if ($this->_isLocked($this->lockFile, 1800)) {
            return [
                'status'         => 'skipped',
                'inserted'       => 0,
                'display_result' => '⏭ Skipped – Already running'
            ];
        }

        file_put_contents($this->lockFile, time());
        $start = microtime(true);

        try {
            // 1) Determine cron job record
            $cron = null;
            $cronId = null;
            $cronName = 'PlanReminderDispatcher';

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
                    'inserted'       => 0,
                    'display_result' => 'No active cron job found'
                ];
            }

            // 2) DB running-state check
            if ($this->_isCronRunning($cron)) {
                @unlink($this->lockFile);
                return [
                    'status'         => 'skipped',
                    'inserted'       => 0,
                    'display_result' => '⚠️ Dispatcher already running since ' . $cron->running_since
                ];
            }

            // Mark as running
            $this->_setCronRunning($cronId, true);

            // 3) Respect batch size from config if provided
            if (!empty($config['batch_size']) && (int)$config['batch_size'] > 0) {
                $this->batchSize = (int)$config['batch_size'];
            }

            // (Execution log insertion REMOVED)

            // 4) Fetch expiring plans and insert into queue (with batch limit)
            $totalInserted = 0;
            $days = [0, 1, 3, 7];

            foreach ($days as $daysBefore) {
                if ($totalInserted >= $this->batchSize) break;

                $targetDate = date('Y-m-d', strtotime("+{$daysBefore} days"));
                $now = date('Y-m-d H:i:s');

                $plans = $this->CI->db
                    ->select('up.id AS plan_id, up.user_id, up.end_date')
                    ->from('tb_ft_user_purchases up')
                    ->join('tb_candidate u', 'u.candidate_id = up.user_id')
                    ->where('up.status', 'active')
                    ->where('DATE(up.end_date)', $targetDate)
                    ->where('up.end_date >', $now)
                    ->limit($this->batchSize - $totalInserted)
                    ->get()
                    ->result();

                $reminderType = "{$daysBefore}_days";

                foreach ($plans as $plan) {
                    // Check duplicate
                    $exists = $this->CI->db
                        ->where('plan_id', $plan->plan_id)
                        ->where('user_id', $plan->user_id)
                        ->where('reminder_type', $reminderType)
                        ->where('target_date', $targetDate)
                        ->get('tb_plan_reminder_queue')
                        ->row();

                    if ($exists) continue;

                    $this->CI->db->insert('tb_plan_reminder_queue', [
                        'plan_id'       => $plan->plan_id,
                        'user_id'       => $plan->user_id,
                        'reminder_type' => $reminderType,
                        'target_date'   => $targetDate,
                        'status'        => 'pending',
                        'scheduled_at'  => date('Y-m-d H:i:s')
                    ]);
                    $totalInserted++;

                    if ($totalInserted >= $this->batchSize) break 2;
                }
            }

            $execTime = round(microtime(true) - $start, 4);
            $msg = "Inserted {$totalInserted} pending reminders into queue";

            // (Execution log update REMOVED)

            // Update last run on cron job
            $this->_updateLastRun($cronId, $execTime);

            return [
                'status'         => 'success',
                'inserted'       => $totalInserted,
                'display_result' => $msg
            ];

        } catch (Throwable $e) {
            $execTime = round(microtime(true) - $start, 4);
            if (isset($cronId)) {
                $this->_updateLastRun($cronId, $execTime);
            }
            // (Exception log insertion REMOVED)
            log_message('error', 'PlanReminderDispatcher Exception: ' . $e->getMessage());
            return [
                'status'         => 'error',
                'inserted'       => 0,
                'display_result' => '❌ Exception: ' . $e->getMessage()
            ];
        } finally {
            if (isset($cronId)) {
                $this->_setCronRunning($cronId, false);
            }
            $this->_unlock($this->lockFile);
        }
    }

    // ----------------------------------------------------------------
    // CRON RUNNING STATE HELPERS
    // ----------------------------------------------------------------
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

    // ----------------------------------------------------------------
    // FILE LOCK / UNLOCK
    // ----------------------------------------------------------------
    private function _isLocked($lockFile, $ttl = 1800)
    {
        return file_exists($lockFile) && (time() - filemtime($lockFile)) < $ttl;
    }

    private function _unlock($lockFile)
    {
        if (file_exists($lockFile)) {
            @unlink($lockFile);
        }
    }

    // ----------------------------------------------------------------
    // UPDATE LAST RUN
    // ----------------------------------------------------------------
    private function _updateLastRun($cronId, $execTime)
    {
        $this->CI->db->where('id', $cronId)->update('tb_jr_cron_jobs', [
            'last_run'            => date('Y-m-d H:i:s'),
            'last_execution_time' => $execTime
        ]);
    }
}