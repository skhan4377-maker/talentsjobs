<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class JobRecommendation_model extends CI_Model
{
    public function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->helper('email');
    }

    // ========================= MAIN CRON ENTRY POINT =========================

    /**
     * Main cron method - Enqueues new candidates and processes pending queue
     * @param array $config Contains cron_id, execution_log_id, emails_per_run
     * @return array
     */
    public function job_recommendation($config = [])
    {
        $lockFile = sys_get_temp_dir() . '/job_recommendation.lock';
    
        if ($this->_is_locked($lockFile)) {
            return [
                'status' => 'skipped',
                'message' => "⏭ Skipped - Already running",
                'emails_sent' => 0,
                'display_result' => "⏭ Skipped - Already running"
            ];
        }
        file_put_contents($lockFile, time());
    
        try {
            $cronId = isset($config['cron_id']) ? $config['cron_id'] : null;
            $executionLogId = isset($config['execution_log_id']) ? $config['execution_log_id'] : null;
    
            // 🔥 GET LIMITS FROM DB
            $cronJob = $this->db
                ->select('emails_per_run, queue_per_run')
                ->from('tb_jr_cron_jobs')
                ->where('id', $cronId)
                ->get()
                ->row();
    
            // ✅ Send limit (email per run)
            $sendLimit = ($cronJob && $cronJob->emails_per_run > 0)
                ? (int)$cronJob->emails_per_run
                : 50;
    
            // ✅ Insert limit (queue per run)
            $insertLimit = ($cronJob && $cronJob->queue_per_run > 0)
                ? (int)$cronJob->queue_per_run
                : 100;
    
            // 🔹 Step 1: Insert into queue (without HTML)
            $enqueueResult = $this->_process_candidates($insertLimit, $cronId, $executionLogId);
    
            // 🔹 Step 2: Send emails (HTML generated at send time)
            $queueStats = $this->process_email_queue($sendLimit, $cronId, $executionLogId);
    
            // 🔹 Stats
            $totalSent = $queueStats['sent'];
            $totalFailed = $queueStats['failed'];
            $totalQueued = $enqueueResult['queued'];
    
            $report = $this->_generate_combined_report($totalQueued, $totalSent, $totalFailed);
    
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

    // ========================= CANDIDATE ENQUEUING =========================

    /**
     * Fetch eligible candidates and insert email jobs into queue
     * @param int $batchLimit
     * @param int|null $cronId
     * @param int|null $executionLogId
     * @return array
     */
    private function _process_candidates($batchLimit, $cronId = null, $executionLogId = null)
    {
        $stats = [
            'candidates_processed' => 0,
            'queued' => 0,
            'failed' => 0
        ];

        $batchSize = min(50, $batchLimit);
        $allJobs = $this->_get_all_active_jobs();

        log_message('info', "📊 Total Active Jobs Found: " . count($allJobs));
        echo "📊 Total Active Jobs Found: " . count($allJobs) . "\n";

        while ($stats['candidates_processed'] < $batchLimit) {
            $currentBatchSize = min($batchSize, $batchLimit - $stats['candidates_processed']);
            $candidates = $this->_fetch_eligible_candidates($currentBatchSize);

            if (empty($candidates)) {
                log_message('info', "⏸ No eligible candidates found.");
                echo "👥 No eligible candidates found.\n";
                break;
            }

            echo "👥 Eligible Candidates Found: " . count($candidates) . "\n";

            foreach ($candidates as $candidate) {
                $stats['candidates_processed']++;

                if ($stats['queued'] + $stats['failed'] >= $batchLimit) {
                    break 2;
                }

                echo "📧 Processing: {$candidate->email} (Failed: {$candidate->email_failed_count}, Bounced: {$candidate->email_bounced_count})\n";

                $designation = trim($candidate->designations);
                if (!$designation) {
                    $this->_log_email($candidate, $cronId, $executionLogId, 'No designation', null, 0, 'failed');
                    $this->_update_candidate_on_failure($candidate->candidate_id, 'No designation');
                    $stats['failed']++;
                    echo "❌ Failed: No designation\n";
                    continue;
                }

                $matchedJobs = $this->_find_matching_jobs($allJobs, $designation);
                if (empty($matchedJobs)) {
                    $matchedJobs = array_slice($allJobs, 0, 5);
                }

                if (empty($matchedJobs)) {
                    $this->_log_email($candidate, $cronId, $executionLogId, 'No jobs available', null, 0, 'failed');
                    $this->_update_candidate_on_failure($candidate->candidate_id, 'No jobs');
                    $stats['failed']++;
                    echo "❌ Failed: No jobs available\n";
                } else {
                    $this->_enqueue_candidate_email($candidate, $matchedJobs, $cronId, $executionLogId);
                    $stats['queued']++;
                    echo "✅ Queued (" . count($matchedJobs) . " jobs)\n";
                }

                usleep(100000); // 0.1 sec delay
            }
        }

        return $stats;
    }

    /**
     * Insert email job into queue (without HTML message)
     * @param object $candidate
     * @param array $matchedJobs
     * @param int|null $cronId
     * @param int|null $executionLogId
     * @return int insert id
     */
    private function _enqueue_candidate_email($candidate, $matchedJobs, $cronId, $executionLogId)
    {
        $jobIds = array_column($matchedJobs, 'job_id');
        $firstDesignation = explode(',', $candidate->designations)[0];
        $subject = "Job Alert: " . trim($firstDesignation) . " Positions - " . $candidate->name;

        // Ensure unsubscribe token exists
        if (empty($candidate->unsubscribe_token)) {
            $unsubscribeToken = bin2hex(random_bytes(16));
            $this->db->where('candidate_id', $candidate->candidate_id)
                     ->update('tb_candidate', ['unsubscribe_token' => $unsubscribeToken]);
            $candidate->unsubscribe_token = $unsubscribeToken;
        }

        $queueData = [
            'candidate_id'      => $candidate->candidate_id,
            'cron_job_id'       => $cronId,
            'execution_log_id'  => $executionLogId,
            'subject'           => $subject,           
            'job_ids'           => json_encode($jobIds),
            'job_count'         => count($matchedJobs),
            'status'            => 'pending',
            'attempts'          => 0,
            'max_attempts'      => 3,
            'scheduled_at'      => date('Y-m-d H:i:s'),
            'created_at'        => date('Y-m-d H:i:s')
        ];

        $this->db->insert('tb_jr_email_queue', $queueData);
        return $this->db->insert_id();
    }

    // ========================= QUEUE WORKER =========================

    /**
     * Process pending emails from queue (called by cron or manually)
     * @param int $limit
     * @param int|null $cronId
     * @param int|null $executionLogId
     * @return array
     */
    public function process_email_queue($limit = 20, $cronId = null, $executionLogId = null)
    {
        $queueItems = $this->db
            ->select('q.*, c.name, c.email, c.unsubscribe_token, c.designations, c.candidate_id')
            ->from('tb_jr_email_queue q')
            ->join('tb_candidate c', 'c.candidate_id = q.candidate_id')
            ->where('q.status', 'pending')
            ->where('q.scheduled_at <=', date('Y-m-d H:i:s'))
            ->order_by('q.created_at', 'ASC')
            ->limit($limit)
            ->get()
            ->result();
    
        $stats = ['sent' => 0, 'failed' => 0];
    
        foreach ($queueItems as $item) {
    
            // 🔹 Mark as processing
            $this->db->where('id', $item->id)->update('tb_jr_email_queue', [
                'status' => 'processing',
                'processed_at' => date('Y-m-d H:i:s')
            ]);
    
            // 🔹 Fetch job details for this candidate
            $jobIds = json_decode($item->job_ids, true);
            if (empty($jobIds) || !is_array($jobIds)) {
                // No jobs – treat as permanent failure
                $errorMsg = "No valid job IDs in queue item";
                $this->_handle_queue_failure($item, $errorMsg, $cronId, $executionLogId);
                $stats['failed']++;
                continue;
            }
    
            $jobs = $this->_get_jobs_by_ids($jobIds);
            if (empty($jobs)) {
                $errorMsg = "No active jobs found for IDs: " . implode(',', $jobIds);
                $this->_handle_queue_failure($item, $errorMsg, $cronId, $executionLogId);
                $stats['failed']++;
                continue;
            }
    
            // 🔹 Build HTML message dynamically
            $candidateObj = (object)[
                'candidate_id' => $item->candidate_id,
                'email' => $item->email,
                'name' => $item->name,
                'designations' => $item->designations,
                'unsubscribe_token' => $item->unsubscribe_token
            ];
            $message = $this->_build_job_alert_email($candidateObj, $jobs);
    
            // 🔹 Send email
            $status = $this->_send_email_unified(
                $item->email,
                $item->name,
                $item->subject,
                $message
            );
    
            // 🔥 STRICT VALIDATION
            $isSuccess = (
                isset($status['status']) &&
                $status['status'] === 'success' &&
                isset($status['data']['sent_count']) &&
                $status['data']['sent_count'] > 0
            );
    
            if ($isSuccess) {
                // ✅ SUCCESS
                $this->_log_email(
                    $candidateObj,
                    $item->cron_job_id,
                    $item->execution_log_id,
                    null,
                    $item->subject,
                    $item->job_count,
                    'sent'
                );
    
                $this->_update_candidate_on_success($item->candidate_id);
    
                $this->db->where('id', $item->id)->update('tb_jr_email_queue', [
                    'status' => 'sent',
                    'processed_at' => date('Y-m-d H:i:s')
                ]);
    
                $stats['sent']++;
                echo "✅ Email sent to {$item->email}\n";
    
            } else {
                // ❌ FAILURE
                $errorMsg = $this->_extract_error_message($status, $item->email);
                $newAttempts = $item->attempts + 1;
    
                if ($newAttempts >= $item->max_attempts) {
                    // 🔴 FINAL FAIL
                    $this->_log_email(
                        $candidateObj,
                        $item->cron_job_id,
                        $item->execution_log_id,
                        $errorMsg,
                        $item->subject,
                        $item->job_count,
                        'failed'
                    );
    
                    $this->_update_candidate_on_failure($item->candidate_id, $errorMsg);
    
                    $this->db->where('id', $item->id)->update('tb_jr_email_queue', [
                        'status' => 'failed',
                        'last_error' => $errorMsg,
                        'processed_at' => date('Y-m-d H:i:s')
                    ]);
    
                    echo "❌ Email failed permanently for {$item->email}: $errorMsg\n";
    
                } else {
                    // 🔁 RETRY
                    $retryDelay = pow(2, $newAttempts) * 60; // 2,4,8 min
    
                    $this->db->where('id', $item->id)->update('tb_jr_email_queue', [
                        'status' => 'pending',
                        'attempts' => $newAttempts,
                        'last_error' => $errorMsg,
                        'scheduled_at' => date('Y-m-d H:i:s', strtotime("+{$retryDelay} seconds"))
                    ]);
    
                    echo "⚠️ Retry {$newAttempts}/{$item->max_attempts} for {$item->email} after {$retryDelay}s\n";
                }
    
                $stats['failed']++;
            }
    
            usleep(100000); // 0.1 sec delay
        }
    
        return $stats;
    }

    /**
     * Helper: handle queue item failure (no jobs or invalid data)
     * @param object $item Queue row
     * @param string $errorMsg
     * @param int|null $cronId
     * @param int|null $executionLogId
     */
    private function _handle_queue_failure($item, $errorMsg, $cronId, $executionLogId)
    {
        $candidateObj = (object)[
            'candidate_id' => $item->candidate_id,
            'email' => $item->email,
            'designations' => $item->designations,
            'unsubscribe_token' => $item->unsubscribe_token ?? ''
        ];

        $this->_log_email(
            $candidateObj,
            $cronId,
            $executionLogId,
            $errorMsg,
            $item->subject,
            $item->job_count,
            'failed'
        );

        $this->_update_candidate_on_failure($item->candidate_id, $errorMsg);

        $this->db->where('id', $item->id)->update('tb_jr_email_queue', [
            'status' => 'failed',
            'last_error' => $errorMsg,
            'processed_at' => date('Y-m-d H:i:s')
        ]);

        echo "❌ Queue item failed (no jobs): {$item->email} - $errorMsg\n";
    }

    /**
     * Helper: fetch full job objects by IDs
     * @param array $jobIds
     * @return array
     */
    private function _get_jobs_by_ids($jobIds)
    {
        if (empty($jobIds)) return [];
        $this->db->select('j.job_id, j.job_title, j.slug, j.salary_type, j.job_description,
                           e.company_name, e.logo as company_logo,
                           j.min_experience, j.max_experience, j.min_salary, j.max_salary, j.created_at,
                           GROUP_CONCAT(DISTINCT c.city_name ORDER BY c.city_name SEPARATOR ", ") as city_names,
                           GROUP_CONCAT(DISTINCT js.skill_name ORDER BY js.skill_name SEPARATOR ", ") as skill_names')
                 ->from('tb_post_job j')
                 ->join('tb_employer e', 'e.employer_id = j.employer_id AND e.is_deleted = 0', 'left')
                 ->join('tb_job_cities jc', 'jc.job_id = j.job_id', 'left')
                 ->join('tb_cities c', 'c.city_id = jc.city_id', 'left')
                 ->join('tb_job_skills js', 'js.job_id = j.job_id', 'left')
                 ->where_in('j.job_id', $jobIds)
                 ->group_by('j.job_id');
        return $this->db->get()->result();
    }

    // ========================= ORIGINAL HELPERS (UNMODIFIED) =========================

    /**
     * Fetch eligible candidates in three tiers:
     * 1. New users who have never received an email (strict filters)
     * 2. Returning users whose next scheduled email time has passed (strict filters)
     * 3. Never‑sent candidates with relaxed filters (fallback)
     */
    private function _fetch_eligible_candidates($batchSize)
    {
        $newLimit = intval($batchSize * 0.4); // 40% new users
        $oldLimit = $batchSize - $newLimit;

        // COMMON CONDITION (🚀 FAST duplicate prevention)
        $this->db->start_cache();
        $this->db->where('c.status', 'active');
        $this->db->where('c.unsubscribed', 0);
        $this->db->where('c.is_deleted', 0);
        $this->db->where('c.is_verified', 1);
        $this->db->where('c.email_failed_count <', 3);
        $this->db->where('c.email_bounced_count <', 1);
        $this->db->where("c.designations IS NOT NULL AND TRIM(c.designations) != ''", null, false);

        // 🚫 SAME DAY DUPLICATE BLOCK (FAST)
        $this->db->where("(c.last_email_sent_at IS NULL OR DATE(c.last_email_sent_at) != CURDATE())", null, false);
        $this->db->stop_cache();

        // STEP 1: NEW USERS
        $newUsers = $this->db
            ->select('c.*')
            ->from('tb_candidate c')
            ->where('c.last_email_sent_at IS NULL', null, false)
            ->order_by('c.created_at', 'DESC')
            ->limit($newLimit)
            ->get()
            ->result();

        // STEP 2: OLD USERS (ROTATION)
        $oldUsers = $this->db
            ->select('c.*')
            ->from('tb_candidate c')
            ->where('c.last_email_sent_at IS NOT NULL', null, false)
            ->order_by('c.last_email_sent_at', 'ASC')
            ->limit($oldLimit)
            ->get()
            ->result();

        // STEP 3: FALLBACK
        $total = count($newUsers) + count($oldUsers);
        if ($total < $batchSize) {
            $remaining = $batchSize - $total;
            $extraQuery = $this->db
                ->select('c.*')
                ->from('tb_candidate c');

            if (!empty($newUsers) || !empty($oldUsers)) {
                $ids = array_merge(
                    array_column($newUsers, 'candidate_id'),
                    array_column($oldUsers, 'candidate_id')
                );
                $extraQuery->where_not_in('c.candidate_id', $ids);
            }

            $extraUsers = $extraQuery
                ->order_by('c.last_email_sent_at', 'ASC')
                ->limit($remaining)
                ->get()
                ->result();

            $this->db->flush_cache();
            return array_merge($newUsers, $oldUsers, $extraUsers);
        }

        $this->db->flush_cache();
        return array_merge($newUsers, $oldUsers);
    }

    /**
     * Get all active jobs with skills (limited to 500 latest)
     * @return array
     */
    private function _get_all_active_jobs()
    {
        return $this->db
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

    /**
     * Find matching jobs based on designation (strict, then partial, then fallback)
     * @param array $allJobs
     * @param string $designation
     * @return array
     */
    private function _find_matching_jobs($allJobs, $designation)
    {
        $matchedJobs = [];
        $designations = array_map('trim', explode(',', $designation));

        // First pass: Exact phrase match in job title
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

        // Second pass: All words match (partial)
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

        // Third pass: First word match
        if (count($matchedJobs) < 3) {
            foreach ($designations as $desig) {
                if (empty($desig)) continue;
                $firstWord = strtolower(trim(explode(' ', $desig)[0]));
                foreach ($allJobs as $job) {
                    if (isset($matchedJobs[$job->job_id])) continue;
                    $jobTitle = strtolower(trim($job->job_title));
                    if (strpos($jobTitle, $firstWord) !== false) {
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

    /**
     * Unified email sending function
     * @param string $toEmail
     * @param string $toName
     * @param string $subject
     * @param string $message
     * @return array
     */
    private function _send_email_unified($toEmail, $toName, $subject, $message)
    {
        $useMailerCloud = true; // Set based on config
        if ($useMailerCloud) {
            return send_mailercloud_email($toEmail, $toName, $subject, $message);
        } else {
            $result = SendEmailTo($toEmail, $subject, $message);
            if (!isset($result['data'])) {
                $result['data'] = [
                    'sent_count' => $result['sent'] ?? 0,
                    'failed' => $result['failed'] ?? [],
                    'errors' => $result['errors'] ?? []
                ];
            }
            return $result;
        }
    }

    /**
     * Update candidate on email success
     * @param int $candidateId
     */
    private function _update_candidate_on_success($candidateId)
    {
        $this->db->where('candidate_id', $candidateId)
            ->update('tb_candidate', [
                'last_email_sent_at' => date('Y-m-d H:i:s'),
                'email_failed_count' => 0,
                'email_bounced_count' => 0,
                'updated_at' => date('Y-m-d H:i:s')
            ]);
    }

    /**
     * Update candidate on email failure
     * @param int $candidateId
     * @param string $errorMessage
     */
    private function _update_candidate_on_failure($candidateId, $errorMessage)
    {
        $this->db->set('email_failed_count', 'email_failed_count + 1', FALSE);
        $this->db->set('updated_at', date('Y-m-d H:i:s'));
        $this->db->where('candidate_id', $candidateId);
        $this->db->update('tb_candidate');
    }

    /**
     * Log email in database
     * @param object $candidate
     * @param int|null $cronId
     * @param int|null $executionLogId
     * @param string|null $errorMsg
     * @param string|null $subject
     * @param int $jobCount
     * @param string $status
     * @return int insert id
     */
    private function _log_email($candidate, $cronId, $executionLogId, $errorMsg, $subject, $jobCount, $status)
    {
        if (!$subject && $status != 'failed') {
            $firstDesignation = explode(',', $candidate->designations)[0];
            $subject = $candidate->name . " Job opportunities for " . trim($firstDesignation);
        }

        if ($errorMsg && strlen($errorMsg) > 500) {
            $errorMsg = substr($errorMsg, 0, 497) . '...';
        }

        $emailLogData = [
            'cron_job_id' => $cronId,
            'cron_execution_log_id' => $executionLogId,
            'candidate_id' => $candidate->candidate_id,
            'email' => $candidate->email,
            'subject' => $subject,
            'job_count' => $jobCount,
            'sent_at' => date('Y-m-d H:i:s'),
            'status' => $status,
            'error_message' => $errorMsg
        ];

        $this->db->insert('tb_jr_cron_email_logs', $emailLogData);
        return $this->db->insert_id();
    }

    /**
     * Extract error message from any email response format
     * @param array $emailResponse
     * @param string|null $recipientEmail
     * @return string
     */
    private function _extract_error_message($emailResponse, $recipientEmail = null)
    {
        $errorMsg = 'Unknown error occurred while sending email';

        if (isset($emailResponse['status']) && $emailResponse['status'] === 'debug') {
            return 'Debug mode: ' . ($emailResponse['message'] ?? 'No debug message');
        }

        if (isset($emailResponse['data']['errors']) && !empty($emailResponse['data']['errors'])) {
            $errors = $emailResponse['data']['errors'];
            if (is_array($errors)) {
                if ($recipientEmail && isset($errors[$recipientEmail])) {
                    return $errors[$recipientEmail];
                }
                $errorMsg = implode(', ', $errors);
            } else {
                $errorMsg = (string) $errors;
            }
        } elseif (isset($emailResponse['errors']) && !empty($emailResponse['errors'])) {
            $errors = $emailResponse['errors'];
            if (is_array($errors)) {
                if ($recipientEmail && isset($errors[$recipientEmail])) {
                    return $errors[$recipientEmail];
                }
                $errorMsg = implode(', ', $errors);
            } else {
                $errorMsg = (string) $errors;
            }
        } elseif (isset($emailResponse['message'])) {
            $errorMsg = $emailResponse['message'];
        }

        $errorMsg = strip_tags($errorMsg);
        $errorMsg = substr($errorMsg, 0, 255);
        return $errorMsg;
    }

    /**
     * Build job alert email HTML (no change – remains as is)
     * @param object $candidate
     * @param array $matchedJobs
     * @return string
     */
    private function _build_job_alert_email($candidate, $matchedJobs)
    {
        $unsubscribeToken = $candidate->unsubscribe_token;
        $unsubscribeUrl   = base_url('unsubscribe/' . $unsubscribeToken);

        $openTrackingPixel = '<img src="'.base_url('track/open?candidate_id='.$candidate->candidate_id).'" width="1" height="1" style="display:none;" alt="" />';

        $message = '<!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Job Recommendations - '.SITE_NAME.'</title>
        </head>
        <body style="margin:0;padding:0;background-color:#f5f7fa;font-family:-apple-system,BlinkMacSystemFont,\'Segoe UI\',Roboto,\'Helvetica Neue\',Arial,sans-serif;font-size:14px;line-height:1.4;color:#333;">
            '.$openTrackingPixel.'
            <table width="100%" border="0" cellpadding="0" cellspacing="0" style="max-width:600px;margin:0 auto;background:#ffffff;">
                <tr>
                    <td style="padding:15px;text-align:center;border-bottom:1px solid #eaeaea;background:#fff;">
                        <img src="'.base_url('assets/frontend/logo.png').'" alt="'.SITE_NAME.'" width="120" height="auto" style="max-width:120px;height:auto;" />
                    </td>
                </tr>
                
                <tr>
                    <td style="padding:15px;">
                        <div style="margin-bottom:15px;">
                            <h1 style="font-size:16px;margin:0 0 6px 0;color:#1a1a1a;line-height:1.3;">Hello '.htmlspecialchars($candidate->name ?? 'Candidate').' 👋</h1>
                            <p style="margin:0;color:#666;font-size:13px;line-height:1.4;">We\'ve found <strong>'.count($matchedJobs).' new job opportunities</strong> matching your profile as <strong>'.htmlspecialchars($candidate->designations).'</strong></p>
                        </div>';

        foreach ($matchedJobs as $job) {
            $jobSlug = $job->slug;
            $jobDetailUrl = base_url($jobSlug);
            $trackingUrl = base_url("track/click?candidate_id={$candidate->candidate_id}&job_id={$job->job_id}&redirect=" . urlencode($jobDetailUrl));

            $minSalary = !empty($job->min_salary) ? (int)$job->min_salary : 0;
            $maxSalary = !empty($job->max_salary) ? (int)$job->max_salary : 0;

            $salary = ($minSalary > 0 || $maxSalary > 0)
                ? '₹' . number_format($minSalary) . ' - ₹' . number_format($maxSalary) . ' ' . ucfirst($job->salary_type)
                : 'Negotiable';

            $city = 'Multiple Locations';
            if (!empty($job->city_names)) {
                $cities = array_map('trim', explode(',', $job->city_names));
                $city = '';
                foreach ($cities as $c) {
                    $city .= '<span style="display:inline-block;background:#eef2ff;color:#4338ca;font-size:10px;padding:3px 6px;border-radius:4px;margin-right:5px;margin-bottom:5px;white-space:nowrap;">'.htmlspecialchars($c).'</span>';
                }
            }

            $experience = '';
            if (!empty($job->min_experience) || !empty($job->max_experience)) {
                if ($job->min_experience == 0 && $job->max_experience == 0) {
                    $experience = 'Fresher';
                } else {
                    $experience = $job->min_experience . ' - ' . $job->max_experience . ' years';
                }
            }

            if (!empty($job->company_logo)) {
                $logoPath = base_url($job->company_logo);
                $companyLogo = '<img src="'.$logoPath.'" alt="'.htmlspecialchars($job->company_name).'" width="36" height="36" style="width:36px;height:36px;object-fit:contain;border-radius:4px;" />';
            } else {
                $initial = strtoupper(substr(trim($job->company_name), 0, 1));
                $companyLogo = '<table width="36" height="36" cellpadding="0" cellspacing="0" border="0" style="width:36px;height:36px;border-radius:4px;background:linear-gradient(135deg,#4f46e5,#7c3aed);"><tr><td align="center" valign="middle" style="font-size:14px;font-weight:600;color:#fff;text-align:center;">'.$initial.'</td></tr></table>';
            }

            $message .= '
                    <table width="100%" border="0" cellpadding="0" cellspacing="0" style="border:1px solid #e0e0e0;border-radius:6px;margin-bottom:10px;background:#fff;">
                        <tr>
                            <td style="padding:12px;">
                                <table width="100%">
                                    <tr>
                                        <td width="36" style="padding-right:10px;">'.$companyLogo.'</td>
                                        <td>
                                            <h3 style="font-size:14px;font-weight:600;margin:0;color:#1e40af;">'.htmlspecialchars($job->job_title).'</h3>
                                            <p style="font-size:12px;color:#4b5563;margin:0 0 8px 0;font-weight:500;">'.htmlspecialchars($job->company_name).'</p>
                                        </td>
                                    </tr>
                                </table>
                                <div style="margin:8px 0 12px 0;">
                                    '.$city;
            if (!empty($experience)) {
                $message .= '<span style="display:inline-block;background:#fef3c7;color:#92400e;font-size:10px;padding:3px 6px;border-radius:4px;margin-right:5px;margin-bottom:5px;white-space:nowrap;">'.$experience.'</span>';
            }
            $message .= '<span style="display:inline-block;background:#ecfdf5;color:#065f46;font-size:10px;padding:3px 6px;border-radius:4px;margin-bottom:5px;white-space:nowrap;">'.$salary.'</span>
                                </div>
                                <table width="100%">
                                    <tr>
                                        <td><a href="'.$trackingUrl.'" style="display:inline-block;background:#10b981;color:white;text-decoration:none;padding:6px 14px;border-radius:4px;font-size:12px;font-weight:600;" target="_blank">Quick Apply</a></td>
                                        <td align="right" width="80"><span style="font-size:10px;color:#6b7280;background:#f3f4f6;padding:4px 8px;border-radius:3px;">'.date('d M', strtotime($job->created_at)).'</span></td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>';
        }

        $message .= '
                        <div style="margin:20px 0;padding:16px;background:linear-gradient(135deg,#e0f2fe,#bae6fd);border-radius:8px;text-align:center;">
                            <p style="margin:0 0 8px 0;font-size:15px;font-weight:600;color:#0369a1;">📌 Get daily job notifications directly</p>
                            <p style="margin:0 0 12px 0;font-size:14px;color:#075985;">Explore new opportunities across different fields 👉</p>
                            <a href="https://www.foundit.in/seeker/registration?spl=IN_paid_display_direct_acq_affiliate_Asterix_AN1719_X5_Mar26&utm_source=ACPL&utm_medium=affiliate&utm_campaign=IN_paid_display_direct_acq_affiliate_Asterix_AN1719_X5_Mar26" style="display:inline-block;background:#0284c7;color:#ffffff;text-decoration:none;padding:10px 24px;border-radius:30px;font-size:14px;font-weight:600;">🔗 Apply Here</a>
                        </div>
                        <div style="margin-top:20px;padding:12px;background:#f8fafc;border-radius:6px;font-size:11px;color:#666;">
                            <p style="margin:0 0 6px 0;"><strong>💡 Pro Tip:</strong> Update your profile regularly to get more relevant job matches.</p>
                            <p style="margin:0;"><strong>🔔 Next Alert:</strong> You will receive your next job alert in 3 days.</p>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td style="padding:15px;text-align:center;background:#f8fafc;border-top:1px solid #eaeaea;font-size:11px;color:#666;">
                        <p style="margin:0 0 8px 0;">You are receiving this email because you registered on '.SITE_NAME.'. <br>To stop receiving these emails, <a href="'.$unsubscribeUrl.'" style="color:#2563eb;">click here to unsubscribe</a>.</p>
                        <p style="font-size:10px;color:#999;margin-top:8px;font-style:italic;"><strong>Important:</strong> '.SITE_NAME.' never asks for money for job offers.</p>
                        <p style="margin:12px 0 0 0;color:#999;font-size:10px;">© '.date('Y').' '.SITE_NAME.'. All rights reserved. | <a href="'.base_url('privacy-policy').'" style="color:#999;">Privacy Policy</a> | <a href="'.base_url('terms-of-service').'" style="color:#999;">Terms of Service</a></p>
                    </td>
                </tr>
            </table>
        </body>
        </html>';

        return $message;
    }

    /**
     * Lock/Unlock methods
     */
    private function _is_locked($lockFile)
    {
        $lockTtl = 1800; // 30 minutes
        return file_exists($lockFile) && (time() - filemtime($lockFile)) < $lockTtl;
    }

    private function _unlock($lockFile)
    {
        if (file_exists($lockFile)) unlink($lockFile);
    }

    /**
     * Generate combined report for cron output
     * @param int $queued
     * @param int $sent
     * @param int $failed
     * @return string
     */
    private function _generate_combined_report($queued, $sent, $failed)
    {
        return "📧 Job Recommendation Report (Queue System)\n" .
               "============================================\n" .
               "Newly Queued: {$queued}\n" .
               "Emails Sent Today: {$sent}\n" .
               "Emails Failed: {$failed}\n" .
               "Total Processed: " . ($sent + $failed) . "\n" .
               "Status: " . (($failed > 0 && $sent == 0) ? 'error' : ($sent > 0 ? 'success' : 'no_action')) . "\n";
    }
}