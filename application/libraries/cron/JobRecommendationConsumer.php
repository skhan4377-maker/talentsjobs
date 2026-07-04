<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * JobRecommendationConsumer
 *
 * Processes the tb_job_recommendation_queue (filled by JobRecommendationDispatcher).
 * Sends job alert emails using the same rich HTML template.
 * Handles retries, candidate tracking and cron execution logging.
 *
 * Uses cron job's `emails_per_run` as batch size.
 * Does NOT write any per-email log table – tracking is handled externally.
 */
class JobRecommendationConsumer
{
    protected $CI;
    private $lockFile;
    private $batchSize = 50;
    private $maxAttempts = 3;

    // Per‑job email service preference (default Mailercloud)
    private $useMailercloud = true;

    public function __construct()
    {
        $this->CI =& get_instance();
        $this->CI->load->database();
        $this->CI->load->helper('email');
    }

    /**
     * Main entry point – called by CronController
     * @param array $config  ['cron_job_id', 'cron_name', 'batch_size']
     * @return array
     */
    public function run($config = [])
    {
        $this->lockFile = sys_get_temp_dir() . '/job_recommendation_consumer.lock';

        if ($this->_isLocked($this->lockFile, 1800)) {
            return [
                'status'         => 'skipped',
                'emails_sent'    => 0,
                'failed'         => 0,
                'display_result' => '⏭ Consumer already running'
            ];
        }

        file_put_contents($this->lockFile, time());
        $startTime = microtime(true);

        try {
            $cron = null;
            $cronId = null;
            $cronName = 'JobRecommendationConsumer';

            // 1. Get cron job record
            if (!empty($config['cron_job_id']) && !empty($config['cron_name'])) {
                $cronId = (int) $config['cron_job_id'];
                $cron = (object) ['id' => $cronId, 'name' => $config['cron_name']];
                $cronName = $config['cron_name'];
            } else {
                $cron = $this->CI->db
                    ->where('context', 'job_recommendation')
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

            // 2. Set email service from cron job
            if (!empty($cron->email_service)) {
                $this->useMailercloud = ($cron->email_service === 'mailercloud');
            }

            // 3. DB running state check
            if ($this->_isCronRunning($cron)) {
                @unlink($this->lockFile);
                return [
                    'status'         => 'skipped',
                    'emails_sent'    => 0,
                    'failed'         => 0,
                    'display_result' => '⚠️ Consumer already running since ' . $cron->running_since
                ];
            }

            $this->_setCronRunning($cronId, true);

            // 4. Schedule check
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
                    'display_result' => '⏭ ' . $sched['message']
                ];
            }

            // 5. Batch size from cron or config override
            if (!empty($cron->emails_per_run) && (int)$cron->emails_per_run > 0) {
                $this->batchSize = (int)$cron->emails_per_run;
            }
            if (!empty($config['batch_size']) && (int)$config['batch_size'] > 0) {
                $this->batchSize = (int)$config['batch_size'];
            }

            // 6. Fetch pending queue items
            $queueItems = $this->CI->db
                ->select('q.*, c.name, c.email, c.unsubscribe_token, c.designations, c.candidate_id AS cid')
                ->from('tb_job_recommendation_queue q')
                ->join('tb_candidate c', 'c.candidate_id = q.candidate_id')
                ->where('q.status', 'pending')
                ->where('q.scheduled_at <=', date('Y-m-d H:i:s'))
                ->order_by('q.created_at', 'ASC')
                ->limit($this->batchSize)
                ->get()
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
                    'display_result' => '📭 No pending queue items'
                ];
            }

            // 7. Insert execution log (tb_jr_cron_execution_logs)
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

            $processed = 0;
            $failed = 0;
            $errorCounts = [];

            foreach ($queueItems as $item) {
                // Mark as processing
                $this->CI->db->where('id', $item->id)
                             ->where('status', 'pending')
                             ->update('tb_job_recommendation_queue', [
                                 'status'                 => 'processing',
                                 'attempts'               => $item->attempts + 1,
                                 'processing_started_at'  => date('Y-m-d H:i:s')
                             ]);

                if ($this->CI->db->affected_rows() == 0) continue;

                // Build candidate object
                $candidateObj = (object)[
                    'candidate_id'      => $item->candidate_id,
                    'email'             => $item->email,
                    'name'              => $item->name,
                    'designations'      => $item->designations,
                    'unsubscribe_token' => $item->unsubscribe_token
                ];

                // Fetch jobs from stored JSON
                $jobIds = json_decode($item->job_ids, true);
                if (!is_array($jobIds) || empty($jobIds)) {
                    $errorMsg = 'No valid job IDs in queue';
                    $this->_handleFailure($item, $candidateObj, $errorMsg, $execLogId, $cronId);
                    $failed++;
                    $errorCounts[$errorMsg] = ($errorCounts[$errorMsg] ?? 0) + 1;
                    continue;
                }

                $jobs = $this->_getActiveJobsByIds($jobIds);
                if (empty($jobs)) {
                    $errorMsg = 'No active jobs found for IDs: ' . implode(',', $jobIds);
                    $this->_handleFailure($item, $candidateObj, $errorMsg, $execLogId, $cronId);
                    $failed++;
                    $errorCounts[$errorMsg] = ($errorCounts[$errorMsg] ?? 0) + 1;
                    continue;
                }

                // Build email
                $subject = $item->subject;
                $message = $this->_buildJobAlertEmail($candidateObj, $jobs);

                // Send email
                $sent = $this->_sendEmail($candidateObj->email, $candidateObj->name, $subject, $message);

                if ($sent) {
                    // ✅ Success
                    $this->CI->db->where('id', $item->id)->update('tb_job_recommendation_queue', [
                        'status'       => 'sent',
                        'processed_at' => date('Y-m-d H:i:s')
                    ]);
                    $this->_updateCandidateOnSuccess($candidateObj->candidate_id);
                    $processed++;
                } else {
                    // ❌ Failure with retry logic
                    $errorMsg = $item->last_error ?? 'Email send failed';
                    $newAttempts = $item->attempts + 1;
                    $maxAttempts = $item->max_attempts ?? $this->maxAttempts;

                    if ($newAttempts >= $maxAttempts) {
                        $this->_handleFailure($item, $candidateObj, $errorMsg, $execLogId, $cronId, true);
                        $failed++;
                    } else {
                        // Retry with exponential backoff
                        $delayMinutes = min(5 * pow(2, $newAttempts), 120);
                        $newScheduledAt = date('Y-m-d H:i:s', strtotime("+{$delayMinutes} minutes"));
                        $this->CI->db->where('id', $item->id)->update('tb_job_recommendation_queue', [
                            'status'       => 'pending',
                            'scheduled_at' => $newScheduledAt,
                            'last_error'   => $errorMsg
                        ]);
                    }
                    $errorCounts[$errorMsg] = ($errorCounts[$errorMsg] ?? 0) + 1;
                }

                usleep(100000); // 0.1 sec delay
            }

            $execTime = round(microtime(true) - $startTime, 4);

            // Build detailed log message
            $logMessage = "Emails sent: {$processed}, Failed: {$failed}";
            if (!empty($errorCounts)) {
                $errorParts = [];
                foreach ($errorCounts as $error => $count) {
                    $errorParts[] = "{$error} ({$count})";
                }
                $logMessage .= " | Errors: " . implode('; ', $errorParts);
            }

            $status = $processed > 0 ? ($failed > 0 ? 'partial' : 'success') : ($failed > 0 ? 'failed' : 'no_action');

            // Update execution log
            $this->CI->db->where('id', $execLogId)->update('tb_jr_cron_execution_logs', [
                'status'          => $status,
                'message'         => $logMessage,
                'processed_count' => $processed,
                'failed_count'    => $failed,
                'execution_time'  => $execTime,
                'raw_response'    => json_encode(['sent' => $processed, 'failed' => $failed, 'errors' => $errorCounts])
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
            log_message('error', 'JobRecommendationConsumer Exception: ' . $e->getMessage());
            return [
                'status'         => 'error',
                'emails_sent'    => 0,
                'failed'         => 0,
                'display_result' => '❌ Exception: ' . $e->getMessage()
            ];
        } finally {
            if (isset($cronId)) {
                $this->_setCronRunning($cronId, false);
            }
            $this->_unlock($this->lockFile);
        }
    }

    // --------------------------------------------------------------------
    // EMAIL BUILDING & SENDING
    // --------------------------------------------------------------------

    private function _sendEmail($to, $toName, $subject, $message)
	{
		// ======== DUMMY MODE (TESTING ONLY) ========
		//log_message('debug', "Dummy email to {$to}: {$subject}");
		//return true;
		// ===========================================
		
		// --- असली कोड बाद में वापस लगाने के लिए रखें ---
		
		if ($this->useMailercloud) {
			$result = send_mailercloud_email($to, $toName, $subject, $message);
			return ($result['status'] === 'success');
		} else {
			$result = SendEmailTo($to, $subject, $message);
			return ($result['status'] === 'success');
		}
		
	}

    private function _getActiveJobsByIds($jobIds)
    {
        if (empty($jobIds)) return [];
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
            ->where_in('j.job_id', $jobIds)
            ->group_by('j.job_id')
            ->get()
            ->result();
    }

    private function _buildJobAlertEmail($candidate, $matchedJobs)
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

    // --------------------------------------------------------------------
    // HELPERS: SUCCESS / FAILURE HANDLING
    // --------------------------------------------------------------------

    private function _updateCandidateOnSuccess($candidateId)
    {
        $this->CI->db->where('candidate_id', $candidateId)
            ->update('tb_candidate', [
                'last_email_sent_at' => date('Y-m-d H:i:s'),
                'email_failed_count' => 0,
                'email_bounced_count' => 0,
                'updated_at' => date('Y-m-d H:i:s')
            ]);
    }

    private function _updateCandidateOnFailure($candidateId)
    {
        $this->CI->db->set('email_failed_count', 'email_failed_count + 1', FALSE);
        $this->CI->db->set('updated_at', date('Y-m-d H:i:s'));
        $this->CI->db->where('candidate_id', $candidateId);
        $this->CI->db->update('tb_candidate');
    }

    /**
     * Handle final failure (max attempts exhausted or invalid data)
     */
    private function _handleFailure($item, $candidateObj, $errorMsg, $execLogId, $cronId, $isSendFailure = false)
    {
        // Update queue as failed
        $this->CI->db->where('id', $item->id)->update('tb_job_recommendation_queue', [
            'status'       => 'failed',
            'last_error'   => $errorMsg,
            'processed_at' => date('Y-m-d H:i:s')
        ]);
        // Update candidate failure counter
        $this->_updateCandidateOnFailure($candidateObj->candidate_id);
    }

    // --------------------------------------------------------------------
    // CRON & SCHEDULE HELPERS
    // --------------------------------------------------------------------

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

    private function _logExecutionOnException($cron, $execTime, $errorMessage)
    {
        $this->CI->db->insert('tb_jr_cron_execution_logs', [
            'cron_job_id'     => $cron->id,
            'cron_name'       => $cron->name ?? 'JobRecommendationConsumer',
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