<?php
defined('BASEPATH') OR exit('No direct script access allowed');
date_default_timezone_set('Asia/Kolkata');

class PushEnqueuer
{
    protected $CI;
    private $maxDailyNotifications = 4;     // 4 per day as per requirement
    private $cooldownHours         = 4;     // 4‑hour gap between messages

    // Template index persistence file (inside writable cache folder)
    private $templateIndexFile = APPPATH . 'cache/push_template_index.txt';

    // Generic templates – used sequentially across runs;
    private $templates = [
		0 => [
			'title' => 'New Jobs Just Posted!',
			'body'  => 'Check out the latest opportunities matching your profile.',
			'link'  => null,       
		],
		1 => [
			'title' => 'A Job Match for You',
			'body'  => 'A perfect opportunity might be waiting. Tap to explore.',
			'link'  => null,
		],
		2 => [
			'title' => 'Don’t Miss Out!',
			'body'  => 'Fresh jobs have been added. Apply before they’re gone.',
			'link'  => 'resume-templates',        
		],
		3 => [
			'title' => 'Your Dream Job Awaits',
			'body'  => 'Discover top jobs handpicked just for you.',
			'link'  => null,
		],
		4 => [
			'title' => 'Don’t Wait, Apply Today!',
			'body'  => 'Multiple companies are hiring for roles near you.',
			'link'  => 'companies/hiring',
		],
		5 => [
			'title' => 'Fresh Openings Available',
			'body'  => 'New vacancies are live. Start applying now.',
			'link'  => null,
		],
		6 => [
			'title' => 'Hiring Near You',
			'body'  => 'Explore jobs in your city and apply instantly.',
			'link'  => null,
		],
		7 => [
			'title' => 'Top Companies Hiring',
			'body'  => 'Apply to opportunities from leading employers today.',
			'link'  => 'companies/hiring',
		],
		8 => [
			'title' => 'New Career Opportunity',
			'body'  => 'Your next career move could be one click away.',
			'link'  => 'career-services',        
		],
		9 => [
			'title' => 'Apply Before It’s Too Late',
			'body'  => 'Popular jobs are filling fast. Submit your application now.',
			'link'  => 'resume-templates',      
		]
	];

    public function __construct() {
        $this->CI =& get_instance();
        $this->CI->load->database();
    }

    public function run($config = [], $pushData = [])
    {
        $lockFile = sys_get_temp_dir() . '/push_enqueuer.lock';
        if ($this->_isLocked($lockFile)) {
            return $this->_output('skipped', 0, '⏭ Enqueuer already running');
        }
        file_put_contents($lockFile, time());
        $startTime = microtime(true);

        try {
            $cron = $this->_getActiveCron($config);
            if (!$cron) {
                return $this->_output('no_action', 0, 'No active push cron found');
            }

            $sched = $this->_isScheduleAllowed($cron);
            if (!$sched['allowed']) {
                $this->_updateLastRun($cron->id, microtime(true) - $startTime);
                return $this->_output('skipped', 0, '⏭ ' . $sched['message']);
            }

            $batchLimit = $this->_calculateDynamicBatch($cron, $config);
            $eligibleTokens = $this->_getEligibleTokens($batchLimit);

            if (empty($eligibleTokens)) {
                return $this->_output('no_action', 0, '⚠️ No eligible tokens');
            }

            // 1. Get the template for THIS RUN (not per token)
            $templateIndex = $this->_getNextTemplateIndex();
            $template = $this->templates[$templateIndex];

            // 2. Determine the landing page:
            //    If the template provides a non‑empty link, use it; otherwise fall back to 'browse-jobs'
            $originalLink = !empty($template['link'])
                ? base_url($template['link'])
                : base_url('browse-jobs');

            $queued = 0;
            foreach ($eligibleTokens as $tokenRow) {
                // Generate unique tracking token (32 hex chars)
                $trackingToken = bin2hex(random_bytes(16));

                // Build tracking URL that first logs the click, then redirects
                $trackingUrl = base_url('track/push_click/' . $trackingToken
                    . '?redirect=' . urlencode($originalLink));

                $payload = [
                    'title'  => $template['title'],
                    'body'   => $template['body'],
                    'link'   => $trackingUrl,          // tracking link
                    'icon'   => base_url('assets/favicon.png'),
                    'job_id' => null,
                ];

                $insert = [
                    'user_id'        => $tokenRow->token_id,
                    'data_payload'   => json_encode($payload),
                    'tracking_token' => $trackingToken,
                    'job_id'         => null,
                    'status'         => 'pending',
                    'created_at'     => date('Y-m-d H:i:s'),
                    'scheduled_at'   => date('Y-m-d H:i:s')
                ];

                $this->CI->db->insert('tb_push_notification_queue', $insert);
                $queued++;
            }

            // 3. Move to next template index for the following run
            $this->_incrementTemplateIndex();

            $execTime = round(microtime(true) - $startTime, 4);
            $this->_updateLastRun($cron->id, $execTime);

            return $this->_output('success', $queued, "📥 Queued {$queued} tokens (template #{$templateIndex})");

        } catch (Throwable $e) {
            log_message('error', 'PushEnqueuer Exception: ' . $e->getMessage());
            return $this->_output('error', 0, 'Exception: ' . $e->getMessage());
        } finally {
            $this->_unlock($lockFile);
        }
    }

    // ================= PRIVATE HELPERS =================

    private function _getNextTemplateIndex()
    {
        $index = 0;
        if (file_exists($this->templateIndexFile)) {
            $index = (int) file_get_contents($this->templateIndexFile);
        }
        $total = count($this->templates);
        return $index % $total;   // wrap around
    }

    private function _incrementTemplateIndex()
    {
        $index = $this->_getNextTemplateIndex();   // get current index (before increment)
        $next = ($index + 1) % count($this->templates);
        file_put_contents($this->templateIndexFile, $next);
    }

    private function _getActiveCron($config) {
        if (!empty($config['cron_job_id'])) {
            return $this->CI->db
                ->where('id', (int)$config['cron_job_id'])
                ->where('is_active', 1)
                ->get('tb_jr_cron_jobs')
                ->row();
        }
        return $this->CI->db
            ->where('context', 'send_push_notifications')
            ->where('is_active', 1)
            ->get('tb_jr_cron_jobs')
            ->row();
    }

    private function _isScheduleAllowed($cron) {
        $nowTime  = date('H:i:s');
        $todayDay = strtolower(date('l'));

        if ($cron->schedule_type !== 'custom') {
            if ($nowTime < $cron->start_time || $nowTime > $cron->end_time) {
                return ['allowed' => false, 'message' => "Outside allowed time window ({$cron->start_time} - {$cron->end_time})"];
            }
        }

        switch ($cron->schedule_type) {
            case 'daily':
                return ['allowed' => true, 'message' => 'Daily schedule allowed'];
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
            default:
                return ['allowed' => false, 'message' => 'Invalid schedule type'];
        }
    }

    private function _calculateDynamicBatch($cron, $config) {
        $dbLimit = !empty($config['emails_per_run']) ? (int)$config['emails_per_run'] : (int)$cron->emails_per_run;
        $totalTokens = $this->CI->db->where('is_active', 1)->count_all_results('tb_push_tokens');
        if ($totalTokens == 0) return $dbLimit;

        $now = time();
        $today = date('Y-m-d');
        $windowStart = strtotime($today . ' ' . $cron->start_time);
        $windowEnd   = strtotime($today . ' ' . $cron->end_time);
        if ($now < $windowStart || $now > $windowEnd) return $dbLimit;

        $remainingMin = max(1, ceil(($windowEnd - $now) / 60));
        $requiredTotal = $this->maxDailyNotifications * $totalTokens;
        $perMinute = ceil($requiredTotal / $remainingMin);
        $perMinute = max(1, min($perMinute, 5000));
        return max($dbLimit, $perMinute);
    }

    private function _getEligibleTokens($limit)
    {
        $todayStart = date('Y-m-d 00:00:00');
        $cooldown   = date('Y-m-d H:i:s', strtotime("-{$this->cooldownHours} hours"));

        $this->CI->db
            ->select('t.id AS token_id,
                      (SELECT COUNT(l.id)
                       FROM tb_push_notification_log l
                       WHERE l.user_id = t.id
                         AND l.sent_at >= "'.$todayStart.'") AS daily_count', false)
            ->from('tb_push_tokens t')
            ->where('t.is_active', 1);

        $this->CI->db
            ->where("(
                SELECT COUNT(l.id)
                FROM tb_push_notification_log l
                WHERE l.user_id = t.id
                  AND l.sent_at >= '{$todayStart}'
            ) <", $this->maxDailyNotifications, false);

        $this->CI->db
            ->where("NOT EXISTS (
                SELECT 1 FROM tb_push_notification_log l2
                WHERE l2.user_id = t.id
                  AND l2.sent_at > '{$cooldown}'
            )", null, false);

        $this->CI->db
            ->where("NOT EXISTS (
                SELECT 1 FROM tb_push_notification_queue q
                WHERE q.user_id = t.id
                  AND q.status IN ('pending', 'processing')
                  AND q.scheduled_at > '{$cooldown}'
            )", null, false);

        $this->CI->db->order_by(
            "(SELECT MAX(l3.sent_at) FROM tb_push_notification_log l3 WHERE l3.user_id = t.id)",
            'ASC',
            false
        );

        return $this->CI->db->limit($limit)->get()->result();
    }

    private function _updateLastRun($cronId, $execTime) {
        $this->CI->db->where('id', $cronId)->update('tb_jr_cron_jobs', [
            'last_run' => date('Y-m-d H:i:s'),
            'last_execution_time' => $execTime
        ]);
    }

    private function _isLocked($file) {
        return file_exists($file) && (time() - filemtime($file)) < 1800;
    }

    private function _unlock($file) {
        if (file_exists($file)) @unlink($file);
    }

    private function _output($status, $count, $msg) {
        return ['status' => $status, 'queued' => $count, 'display_result' => $msg];
    }
}