<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * PlanAutomationDispatcher
 *
 * Scans tb_ft_user_purchases for plans to expire/activate
 * and inserts them into tb_plan_automation_queue with status 'pending'.
 * Does NOT send emails – purely a queue filler.
 */
class PlanAutomationDispatcher
{
    protected $CI;
    private $lockFile;
    private $batchSize = 200;

    public function __construct()
    {
        $this->CI =& get_instance();
        $this->CI->load->database();
    }

    /**
     * Run the dispatcher.
     * @param array $config  (optional) ['cron_job_id'=>..., 'cron_name'=>..., 'batch_size'=>...]
     * @return array
     */
    public function run($config = [])
    {
        $this->lockFile = sys_get_temp_dir() . '/plan_automation_dispatcher.lock';

        if ($this->_isLocked($this->lockFile, 1800)) {
            return [
                'status'      => 'skipped',
                'inserted'    => 0,
                'display_result' => '⏭ Dispatcher already running'
            ];
        }

        file_put_contents($this->lockFile, time());
        $start = microtime(true);

        try {
            // ---- 1. Cron job record ----
            $cron    = null;
            $cronId  = null;
            $cronName = 'PlanAutomationDispatcher';

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
                    'status'      => 'no_action',
                    'inserted'    => 0,
                    'display_result' => 'No active cron job found'
                ];
            }

            // ---- 2. DB running state check ----
            if ($this->_isCronRunning($cron)) {
                @unlink($this->lockFile);
                return [
                    'status'      => 'skipped',
                    'inserted'    => 0,
                    'display_result' => '⚠️ Dispatcher already running since ' . $cron->running_since
                ];
            }

            $this->_setCronRunning($cronId, true);

            // ---- 3. Schedule check ----
            $sched = $this->_isScheduleAllowed($cron);
            if (!$sched['allowed']) {
                $execTime = round(microtime(true) - $start, 4);
                $this->_updateLastRun($cronId, $execTime);
                $this->_setCronRunning($cronId, false);
                @unlink($this->lockFile);
                return [
                    'status'      => 'skipped',
                    'inserted'    => 0,
                    'display_result' => '⏭ ' . $sched['message']
                ];
            }

            // ---- 4. Batch size ----
            if (!empty($config['batch_size']) && (int)$config['batch_size'] > 0) {
                $this->batchSize = (int)$config['batch_size'];
            }

            $totalInserted = 0;

            // ---- 5. Find plans to expire ----
            $current_time = date('Y-m-d H:i:s');
            $expiredPlans = $this->CI->db
                ->select('id AS plan_id, user_id')
                ->from('tb_ft_user_purchases')
                ->where('status', 'active')
                ->where('end_date <', $current_time)
                ->limit($this->batchSize)
                ->get()
                ->result();

            foreach ($expiredPlans as $plan) {
                // Check duplicate
                $exists = $this->CI->db
                    ->where('plan_id', $plan->plan_id)
                    ->where('user_id', $plan->user_id)
                    ->where('action_type', 'expire')
                    ->where('status', 'pending')
                    ->get('tb_plan_automation_queue')
                    ->row();
                if ($exists) continue;

                $this->CI->db->insert('tb_plan_automation_queue', [
                    'plan_id'      => $plan->plan_id,
                    'user_id'      => $plan->user_id,
                    'action_type'  => 'expire',
                    'status'       => 'pending',
                    'scheduled_at' => date('Y-m-d H:i:s')
                ]);
                $totalInserted++;
            }

            // ---- 6. Find plans to activate ----
            // Users with an expired active plan AND an upcoming plan
            $users = $this->CI->db->query("
                SELECT DISTINCT u.candidate_id AS user_id
                FROM tb_candidate u
                WHERE EXISTS (
                    SELECT 1 FROM tb_ft_user_purchases up1
                    WHERE up1.user_id = u.candidate_id
                      AND up1.status = 'active'
                      AND up1.end_date < ?
                )
                AND EXISTS (
                    SELECT 1 FROM tb_ft_user_purchases up2
                    WHERE up2.user_id = u.candidate_id
                      AND up2.status = 'upcoming'
                )
                LIMIT ?
            ", [$current_time, $this->batchSize - $totalInserted])->result();

            foreach ($users as $user) {
                // Get the oldest upcoming plan
                $upcoming = $this->CI->db
                    ->select('id AS plan_id')
                    ->from('tb_ft_user_purchases')
                    ->where('user_id', $user->user_id)
                    ->where('status', 'upcoming')
                    ->order_by('created_at', 'ASC')
                    ->limit(1)
                    ->get()->row();
                if (!$upcoming) continue;

                // Avoid duplicate
                $exists = $this->CI->db
                    ->where('plan_id', $upcoming->plan_id)
                    ->where('user_id', $user->user_id)
                    ->where('action_type', 'activate')
                    ->where('status', 'pending')
                    ->get('tb_plan_automation_queue')
                    ->row();
                if ($exists) continue;

                $this->CI->db->insert('tb_plan_automation_queue', [
                    'plan_id'      => $upcoming->plan_id,
                    'user_id'      => $user->user_id,
                    'action_type'  => 'activate',
                    'status'       => 'pending',
                    'scheduled_at' => date('Y-m-d H:i:s')
                ]);
                $totalInserted++;
            }

            // ---- 7. Wrap up ----
            $execTime = round(microtime(true) - $start, 4);
            $msg = "Inserted {$totalInserted} pending automation tasks into queue";
            $this->_updateLastRun($cronId, $execTime);

            return [
                'status'         => 'success',
                'inserted'       => $totalInserted,
                'display_result' => $msg
            ];

        } catch (Throwable $e) {
            $execTime = round(microtime(true) - $start, 4);
            if (isset($cronId)) $this->_updateLastRun($cronId, $execTime);
            log_message('error', 'PlanAutomationDispatcher Exception: ' . $e->getMessage());
            return [
                'status'         => 'error',
                'inserted'       => 0,
                'display_result' => '❌ Exception: ' . $e->getMessage()
            ];
        } finally {
            if (isset($cronId)) $this->_setCronRunning($cronId, false);
            $this->_unlock($this->lockFile);
        }
    }

    // ----------------------------------------------------------------
    //  Schedule check
    // ----------------------------------------------------------------
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
                return ['allowed' => true, 'message' => ''];
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
            default:
                return ['allowed' => false, 'message' => 'Invalid schedule type'];
        }
    }

    // ----------------------------------------------------------------
    //  Cron running state helpers
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
    //  Update last run
    // ----------------------------------------------------------------
    private function _updateLastRun($cronId, $execTime)
    {
        $this->CI->db->where('id', $cronId)->update('tb_jr_cron_jobs', [
            'last_run'            => date('Y-m-d H:i:s'),
            'last_execution_time' => $execTime
        ]);
    }

    // ----------------------------------------------------------------
    //  File lock / unlock
    // ----------------------------------------------------------------
    private function _isLocked($file, $ttl = 1800)
    {
        return file_exists($file) && (time() - filemtime($file)) < $ttl;
    }

    private function _unlock($file)
    {
        if (file_exists($file)) @unlink($file);
    }
}