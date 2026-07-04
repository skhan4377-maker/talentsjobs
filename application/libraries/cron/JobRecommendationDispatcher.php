<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * JobRecommendationDispatcher
 *
 * केवल कैंडिडेट स्कैन करेगा, जॉब मैच करेगा, और tb_job_recommendation_queue में pending डालेगा।
 * EMAIL SEND नहीं करेगा – सिर्फ queue filler है।
 *
 * अब cron job की state (is_running, last_run) को DB में ट्रैक करता है
 * और schedule के अनुसार ही run होता है।
 * लेकिन tb_jr_cron_execution_logs में कोई entry नहीं करता।
 */
class JobRecommendationDispatcher
{
    protected $CI;
    private $lockFile;
    private $batchSize = 5;
    private $queueTable = 'tb_job_recommendation_queue';

    public function __construct()
    {
        $this->CI =& get_instance();
        $this->CI->load->database();
    }

    /**
     * Main entry point
     * @param array $config  ['cron_job_id' => int, 'cron_name' => string, 'batch_size' => int]
     * @return array
     */
    public function run($config = [])
    {
        $this->lockFile = sys_get_temp_dir() . '/job_recommendation_dispatcher.lock';

        // File lock से extra safety
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
            $cron    = null;
            $cronId  = null;
            $cronName = 'JobRecommendationDispatcher';

            // 1. cron job record प्राप्त करें (config या active context से)
            if (!empty($config['cron_job_id']) && !empty($config['cron_name'])) {
                $cronId   = (int)$config['cron_job_id'];
                $cron     = (object) ['id' => $cronId, 'name' => $config['cron_name']];
                $cronName = $config['cron_name'];
            } else {
                $cron = $this->CI->db
                    ->where('context', 'job_recommendation')
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
                    'inserted'       => 0,
                    'display_result' => 'No active cron job found for job_recommendation'
                ];
            }

            // 2. DB running state check
            if ($this->_isCronRunning($cron)) {
                @unlink($this->lockFile);
                return [
                    'status'         => 'skipped',
                    'inserted'       => 0,
                    'display_result' => '⚠️ Dispatcher already running since ' . $cron->running_since
                ];
            }

            // 3. Set running flag
            $this->_setCronRunning($cronId, true);

            // 4. Schedule check
            $sched = $this->_isScheduleAllowed($cron);
            if (!$sched['allowed']) {
                $execTime = round(microtime(true) - $start, 4);
                $this->_updateLastRun($cronId, $execTime);
                $this->_setCronRunning($cronId, false);
                @unlink($this->lockFile);
                return [
                    'status'         => 'skipped',
                    'inserted'       => 0,
                    'display_result' => '⏭ ' . $sched['message']
                ];
            }

            // 5. Batch size priority: config > cron_job.queue_per_run > default
            if (!empty($config['batch_size']) && (int)$config['batch_size'] > 0) {
                $this->batchSize = (int)$config['batch_size'];
            } elseif (!empty($cron->queue_per_run)) {
                $this->batchSize = (int)$cron->queue_per_run;
            }

            // 6. Fetch jobs and process candidates
            $allJobs  = $this->_getAllActiveJobs();
            $inserted = $this->_processCandidates($allJobs, $this->batchSize, $cronId);

            $execTime = round(microtime(true) - $start, 4);
            $msg = "Inserted {$inserted} pending job recommendation emails into queue";

            // 7. Update last run
            $this->_updateLastRun($cronId, $execTime);

            return [
                'status'         => $inserted > 0 ? 'success' : 'no_action',
                'inserted'       => $inserted,
                'display_result' => $msg
            ];

        } catch (Throwable $e) {
            $execTime = round(microtime(true) - $start, 4);
            if (isset($cronId)) {
                $this->_updateLastRun($cronId, $execTime);
            }
            log_message('error', 'JobRecommendationDispatcher Exception: ' . $e->getMessage());
            return [
                'status'         => 'error',
                'inserted'       => 0,
                'display_result' => '❌ Exception: ' . $e->getMessage()
            ];
        } finally {
            // 8. Unset running flag
            if (isset($cronId)) {
                $this->_setCronRunning($cronId, false);
            }
            $this->_unlock($this->lockFile);
        }
    }

    // --------------------------------------------------------------------
    // CANDIDATE ENQUEUING (Pointer-based Round-Robin)
    // --------------------------------------------------------------------

    private function _processCandidates($allJobs, $batchLimit, $cronId)
    {
        $queued = 0;
        $batchSize = min(50, $batchLimit);

        while ($queued < $batchLimit) {
            $currentBatch = min($batchSize, $batchLimit - $queued);
            $candidates = $this->_fetchEligibleCandidates($currentBatch);

            if (empty($candidates)) {
                break;
            }

            foreach ($candidates as $candidate) {
                if ($queued >= $batchLimit) break 2;

                $designation = trim($candidate->designations);
                if (!$designation) {
                    continue;
                }

                $matchedJobs = $this->_findMatchingJobs($allJobs, $designation);
                if (empty($matchedJobs)) {
                    $matchedJobs = array_slice($allJobs, 0, 5);
                }

                if (empty($matchedJobs)) {
                    continue;
                }

                $this->_enqueueCandidate($candidate, $matchedJobs, $cronId);
                $queued++;
                usleep(100000);
            }
        }

        return $queued;
    }

    /**
     * Fetch eligible candidates with all spam/unsub/failure filters.
     * Uses pointer for round-robin, excludes candidates with pending queue today.
     */
    private function _fetchEligibleCandidates($batchSize)
    {
        $this->_ensurePointerRow();

        $pointer = $this->CI->db
            ->select('last_candidate_id')
            ->where('id', 1)
            ->get('tb_job_recommendation_pointer')
            ->row();

        $lastId = $pointer ? (int)$pointer->last_candidate_id : 0;

        // Build query with all necessary filters
        $this->CI->db
            ->select('c.*')
            ->from('tb_candidate c')
            ->where('c.status', 'active')
            ->where('c.is_deleted', 0)
            ->where('c.is_verified', 1)
            // ✅ New filters: unsubscribed, bounce, fail count
            ->where('c.unsubscribed', 0)
            ->where('c.email_bounced_count <', 1)        // 0 bounces allowed
            ->where('c.email_failed_count <', 3)          // max 2 consecutive fails
            ->where("c.designations IS NOT NULL AND TRIM(c.designations) != ''", null, false)
            // Avoid duplicate on same day: either no email sent today OR no pending queue today
            ->group_start()
                ->where("c.last_email_sent_at IS NULL OR DATE(c.last_email_sent_at) != CURDATE()", null, false)
                ->or_where("NOT EXISTS (
                    SELECT 1 FROM {$this->queueTable} q
                    WHERE q.candidate_id = c.candidate_id
                      AND q.status = 'pending'
                      AND DATE(q.created_at) = CURDATE()
                )", null, false)
            ->group_end()
            ->where('c.candidate_id >', $lastId)
            ->order_by('c.candidate_id', 'ASC')
            ->limit($batchSize);

        $candidates = $this->CI->db->get()->result();

        if (!empty($candidates)) {
            $newLastId = end($candidates)->candidate_id;
            $this->CI->db->where('id', 1)->update('tb_job_recommendation_pointer', [
                'last_candidate_id' => $newLastId
            ]);
            return $candidates;
        }

        // No candidates found beyond pointer – check if any candidate exists at all
        $hasAny = $this->CI->db
            ->select('candidate_id')
            ->from('tb_candidate c')
            ->where('c.status', 'active')
            ->where('c.is_deleted', 0)
            ->where('c.is_verified', 1)
            ->where('c.unsubscribed', 0)
            ->where('c.email_bounced_count <', 1)
            ->where('c.email_failed_count <', 3)
            ->where("c.designations IS NOT NULL AND TRIM(c.designations) != ''", null, false)
            ->where('c.candidate_id >', $lastId)
            ->limit(1)
            ->get()
            ->row();

        if (!$hasAny) {
            // All candidates processed – reset pointer and start new cycle
            $this->CI->db->where('id', 1)->update('tb_job_recommendation_pointer', [
                'last_candidate_id' => 0
            ]);
            return $this->_fetchEligibleCandidates($batchSize);
        }

        // Candidates exist but all have pending entries today – return empty
        return [];
    }

    private function _ensurePointerRow()
    {
        $exists = $this->CI->db->where('id', 1)->get('tb_job_recommendation_pointer')->row();
        if (!$exists) {
            $this->CI->db->insert('tb_job_recommendation_pointer', [
                'id'                => 1,
                'last_candidate_id' => 0,
                'last_created_at'   => '2099-01-01 00:00:00'
            ]);
        }
    }

    // --------------------------------------------------------------------
    // SCHEDULE CHECK
    // --------------------------------------------------------------------

    private function _isScheduleAllowed($cron)
    {
        $nowTime  = date('H:i:s');
        $todayDay = strtolower(date('l'));

        if ($cron->schedule_type !== 'custom') {
            if (!empty($cron->start_time) && !empty($cron->end_time)) {
                if ($nowTime < $cron->start_time || $nowTime > $cron->end_time) {
                    return [
                        'allowed' => false,
                        'message' => "Outside allowed time window ({$cron->start_time} - {$cron->end_time})"
                    ];
                }
            }
        }

        switch ($cron->schedule_type) {
            case 'daily':
                return ['allowed' => true, 'message' => ''];

            case 'weekly':
                if (empty($cron->week_days)) {
                    return ['allowed' => false, 'message' => 'No week days configured'];
                }
                $days = array_map('trim', explode(',', strtolower($cron->week_days)));
                if (!in_array($todayDay, $days)) {
                    return ['allowed' => false, 'message' => 'Not a scheduled weekday'];
                }
                return ['allowed' => true, 'message' => ''];

            case 'monthly':
                if (empty($cron->month_day)) {
                    return ['allowed' => false, 'message' => 'No month day configured'];
                }
                if ((int)date('j') != $cron->month_day) {
                    return ['allowed' => false, 'message' => 'Not monthly scheduled date'];
                }
                return ['allowed' => true, 'message' => ''];

            case 'custom':
                if (empty($cron->custom_schedule)) {
                    return ['allowed' => false, 'message' => 'No custom schedule'];
                }
                $customDateTime = date('Y-m-d H:i:00', strtotime($cron->custom_schedule));
                if (date('Y-m-d H:i:00') < $customDateTime) {
                    return ['allowed' => false, 'message' => 'Custom schedule time not yet reached'];
                }
                return ['allowed' => true, 'message' => ''];

            default:
                return ['allowed' => false, 'message' => 'Invalid schedule type'];
        }
    }

    // --------------------------------------------------------------------
    // CRON RUNNING STATE HELPERS (DB level)
    // --------------------------------------------------------------------

    private function _isCronRunning($cron)
    {
        if (!empty($cron->is_running) && !empty($cron->running_since)) {
            return (time() - strtotime($cron->running_since)) < 300; // 5 min timeout
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

    // --------------------------------------------------------------------
    // JOB FETCH & MATCHING
    // --------------------------------------------------------------------

    private function _getAllActiveJobs()
    {
        return $this->CI->db
            ->select('j.job_id, j.job_title, j.slug, j.salary_type, j.job_description,
                      e.company_name, e.logo as company_logo,
                      j.min_experience, j.max_experience, j.min_salary, j.max_salary, j.created_at,
                      GROUP_CONCAT(DISTINCT c.city_name ORDER BY c.city_name SEPARATOR ", ") as city_names,
                      GROUP_CONCAT(DISTINCT js.skill_name ORDER BY js.skill_name SEPARATOR ", ") as skill_names')
            ->from('tb_post_job j')
            ->join('tb_employer e', 'e.employer_id = j.employer_id AND e.is_deleted = 0', 'left')
            ->join('tb_job_cities jc', 'jc.job_id = j.job_id', 'left')
            ->join('tb_cities c', 'c.city_id = jc.city_id', 'left')
            ->join('tb_job_skills js', 'js.job_id = j.job_id', 'left')
            ->where('j.is_deleted', 0)
            ->where('j.status', 'active')
            ->group_by('j.job_id')
            ->order_by('j.created_at', 'DESC')
            ->limit(500)
            ->get()
            ->result();
    }

    private function _findMatchingJobs($allJobs, $designation)
    {
        $matchedJobs = [];
        $designations = array_map('trim', explode(',', $designation));

        // Exact match
        foreach ($designations as $desig) {
            if (empty($desig)) continue;
            $desigLower = strtolower($desig);
            foreach ($allJobs as $job) {
                $jobTitle = strtolower(trim($job->job_title));
                if (strpos($jobTitle, $desigLower) !== false) {
                    $job->relevance_score = 100;
                    $matchedJobs[$job->job_id] = $job;
                }
            }
        }

        if (count($matchedJobs) >= 2) {
            usort($matchedJobs, function($a, $b) {
                return strtotime($b->created_at) - strtotime($a->created_at);
            });
            return array_slice($matchedJobs, 0, 5);
        }

        // All words match
        foreach ($designations as $desig) {
            if (empty($desig)) continue;
            $words = preg_split('/[\s,]+/', $desig);
            $words = array_filter(array_map('trim', $words));
            foreach ($allJobs as $job) {
                if (isset($matchedJobs[$job->job_id])) continue;
                $jobTitle = strtolower(trim($job->job_title));
                $allWordsMatch = true;
                foreach ($words as $word) {
                    if (strpos($jobTitle, strtolower($word)) === false) {
                        $allWordsMatch = false;
                        break;
                    }
                }
                if ($allWordsMatch) {
                    $job->relevance_score = 80;
                    $matchedJobs[$job->job_id] = $job;
                }
            }
        }

        // First word fallback
        if (count($matchedJobs) < 3) {
            foreach ($designations as $desig) {
                if (empty($desig)) continue;
                $firstWord = strtolower(trim(explode(' ', $desig)[0]));
                foreach ($allJobs as $job) {
                    if (isset($matchedJobs[$job->job_id])) continue;
                    if (strpos(strtolower(trim($job->job_title)), $firstWord) !== false) {
                        $job->relevance_score = 20;
                        $matchedJobs[$job->job_id] = $job;
                    }
                }
            }
        }

        usort($matchedJobs, function($a, $b) {
            if ($b->relevance_score != $a->relevance_score) {
                return $b->relevance_score - $a->relevance_score;
            }
            return strtotime($b->created_at) - strtotime($a->created_at);
        });

        return array_slice($matchedJobs, 0, 5);
    }

    private function _enqueueCandidate($candidate, $matchedJobs, $cronId)
    {
        $jobIds = array_column($matchedJobs, 'job_id');
        $firstDesignation = explode(',', $candidate->designations)[0];
        $subject = "Job Alert: " . trim($firstDesignation) . " Positions - " . $candidate->name;

        if (empty($candidate->unsubscribe_token)) {
            $unsubscribeToken = bin2hex(random_bytes(16));
            $this->CI->db->where('candidate_id', $candidate->candidate_id)
                         ->update('tb_candidate', ['unsubscribe_token' => $unsubscribeToken]);
            $candidate->unsubscribe_token = $unsubscribeToken;
        }

        $queueData = [
            'candidate_id'      => $candidate->candidate_id,
            'cron_job_id'       => $cronId,
            'execution_log_id'  => null,
            'subject'           => $subject,
            'job_ids'           => json_encode($jobIds),
            'job_count'         => count($matchedJobs),
            'status'            => 'pending',
            'attempts'          => 0,
            'max_attempts'      => 3,
            'scheduled_at'      => date('Y-m-d H:i:s'),
            'created_at'        => date('Y-m-d H:i:s')
        ];

        $this->CI->db->insert($this->queueTable, $queueData);
    }

    // --------------------------------------------------------------------
    // FILE LOCK HANDLING
    // --------------------------------------------------------------------

    private function _isLocked($file, $ttl = 1800)
    {
        return file_exists($file) && (time() - filemtime($file)) < $ttl;
    }

    private function _unlock($file)
    {
        if (file_exists($file)) @unlink($file);
    }
}