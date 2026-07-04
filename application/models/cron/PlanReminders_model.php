<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class PlanReminders_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->helper('email');
    }

    /**
     * Main cron method - Returns array with detailed report
     */
    public function plan_reminders($config = []) {
        $lockFile = sys_get_temp_dir() . '/plan_reminders.lock';

        if ($this->_is_locked($lockFile)) {
            return [
                'status' => 'skipped',
                'message' => "⏭ Skipped - Already running",
                'emails_sent' => 0,
                'display_result' => "⏭ Skipped - Already running",
                'error_message' => ''
            ];
        }

        file_put_contents($lockFile, time());

        try {
            $cronId = isset($config['cron_id']) ? $config['cron_id'] : null;
            $executionLogId = isset($config['execution_log_id']) ? $config['execution_log_id'] : null;
            $emailsPerRun = isset($config['emails_per_run']) ? (int)$config['emails_per_run'] : 5;
            
            $result = $this->_process_reminders($cronId, $executionLogId, $emailsPerRun);
            
            return [
                'status' => $result['overall_status'],
                'emails_sent' => $result['stats']['total_sent'],
                'display_result' => $result['report'],
                'error_message' => $result['error_message']
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

    /**
     * Process all plan reminders
     */
    private function _process_reminders($cronId, $executionLogId, $limit) {
        $stats = [
            'today' => 0,
            'tomorrow' => 0,
            '3_days' => 0,
            '7_days' => 0,
            'total_sent' => 0,
            'total_failed' => 0,
            'failed_emails' => []  // New: Store failed email details
        ];
        
        // Get counts for each day
        $stats['today'] = $this->_get_expiring_count_for_day(0);
        $stats['tomorrow'] = $this->_get_expiring_count_for_day(1);
        $stats['3_days'] = $this->_get_expiring_count_for_day(3);
        $stats['7_days'] = $this->_get_expiring_count_for_day(7);
        
        // Send reminders in reverse order (7, 3, 1, 0 days)
        $days_to_check = [7, 3, 1, 0];
        
        foreach ($days_to_check as $days) {
            if ($stats['total_sent'] >= $limit) break;
            
            $remaining = $limit - $stats['total_sent'];
            $result = $this->_send_reminders_for_days($days, $cronId, $executionLogId, $remaining);
            
            $stats['total_sent'] += $result['sent'];
            $stats['total_failed'] += $result['failed'];
            $stats['failed_emails'] = array_merge($stats['failed_emails'], $result['failed_emails'] ?? []);
        }
        
        return [
            'stats' => $stats,
            'report' => $this->_generate_report($stats),
            'overall_status' => $this->_determine_overall_status($stats),
            'error_message' => $this->_compile_error_message($stats['failed_emails'])
        ];
    }

    /**
     * Generate simple report - UNCHANGED FROM ORIGINAL
     */
    private function _generate_report($stats) {
        $total_reminders = $stats['today'] + $stats['tomorrow'] + $stats['3_days'] + $stats['7_days'];
        
        // Determine status
        if ($stats['total_sent'] == 0 && $stats['total_failed'] == 0 && $total_reminders == 0) {
            $status = 'no_action';
            $message = 'No reminders to send';
        } elseif ($stats['total_failed'] > 0 && $stats['total_sent'] > 0) {
            $status = 'partial';
            $message = 'Some reminders sent, some failed';
        } elseif ($stats['total_failed'] > 0 && $stats['total_sent'] == 0) {
            $status = 'error';
            $message = 'All emails failed';
        } else {
            $status = 'success';
            $message = 'Reminders sent successfully';
        }
        
        return "📋 Plan Reminder Report\n" .
               "===============================\n" .
               "Expiring Today: {$stats['today']}\n" .
               "Expiring Tomorrow: {$stats['tomorrow']}\n" .
               "Expiring in 3 Days: {$stats['3_days']}\n" .
               "Expiring in 7 Days: {$stats['7_days']}\n" .
               "Total Reminders: {$total_reminders}\n" .
               "Emails Sent: {$stats['total_sent']}\n" .
               "Emails Failed: {$stats['total_failed']}\n" .
               "Status: {$status}\n" .
               "Message: {$message}";
    }

    /**
     * Determine overall status based on stats
     */
    private function _determine_overall_status($stats) {
        $total_reminders = $stats['today'] + $stats['tomorrow'] + $stats['3_days'] + $stats['7_days'];
        
        if ($stats['total_sent'] == 0 && $stats['total_failed'] == 0 && $total_reminders == 0) {
            return 'no_action';
        } elseif ($stats['total_failed'] > 0 && $stats['total_sent'] > 0) {
            return 'partial';
        } elseif ($stats['total_failed'] > 0 && $stats['total_sent'] == 0) {
            return 'error';
        } else {
            return 'success';
        }
    }

    /**
     * Compile error message from failed emails
     */
    private function _compile_error_message($failedEmails) {
        if (empty($failedEmails)) {
            return '';
        }
        
        $errors = [];
        foreach ($failedEmails as $failed) {
            $errors[] = $failed['email'] . ': ' . $failed['error'];
        }
        
        return implode('; ', $errors);
    }

    /**
     * Get count of plans expiring in X days - UNCHANGED
     */
    private function _get_expiring_count_for_day($days_before) {
        $current_time = date('Y-m-d H:i:s');
        
        $this->db->select('COUNT(*) as count')
                 ->from('tb_user_plans up')
                 ->join('tb_candidate u', 'u.candidate_id = up.user_id')
                 ->where('up.status', 'active');
        
        if ($days_before == 0) {
            $today = date('Y-m-d');
            $this->db->where('DATE(up.end_date)', $today)
                     ->where('up.end_date >', $current_time);
        } else {
            $target_date = date('Y-m-d', strtotime("+{$days_before} days"));
            $this->db->where('DATE(up.end_date)', $target_date)
                     ->where('up.end_date >', $current_time);
        }
        
        return $this->db->get()->row()->count;
    }

    /**
     * Send reminders for specific days - MODIFIED TO COLLECT FAILED EMAILS
     */
	 private function _send_reminders_for_days($days_before, $cronId, $executionLogId, $limit) {
		$sent = 0;
		$failed = 0;
		$failed_emails = [];

		$plans = $this->db
			->from('tb_user_plans up')
			->join('tb_candidate u', 'u.candidate_id = up.user_id')
			->where('up.status', 'active')
			->where('DATE(up.end_date)', date('Y-m-d', strtotime("+{$days_before} days")))
			->limit($limit)
			->get()
			->result();

		foreach ($plans as $plan) {
			$exists = $this->db
				->where([
					'plan_id' => $plan->id,
					'reminder_type' => "{$days_before}_days"
				])
				->where('DATE(reminder_date)', date('Y-m-d'))
				->get('tb_plan_reminders')
				->row();

			if ($exists) continue;

			$res = $this->_send_reminder_email($plan, $days_before, $cronId, $executionLogId);

			if ($res['success']) {
				$this->_log_reminder($plan->id, $plan->user_id, $days_before);
				$sent++;
			} else {
				$failed++;
				$failed_emails[] = [
					'email' => $plan->email,
					'error' => $res['error']
				];
			}
		}

		return compact('sent', 'failed', 'failed_emails');
	}


    /**
     * Send reminder email - MODIFIED TO USE UNIFIED FUNCTION
     */
    private function _send_reminder_email($plan, $days_before, $cronId, $executionLogId) {
        // Subject based on days
        if ($days_before == 0) {
            $subject = "Reminder: Your Plan Expires Today!";
        } elseif ($days_before == 1) {
            $subject = "Reminder: Your Plan Expires Tomorrow";
        } else {
            $subject = "Reminder: Your Plan Expires in {$days_before} days";
        }
        
        // Get recommended plans
        $recommended_plans = $this->_get_recommended_plans($plan->feature_id);
        
        // Build email
        $message = $this->_build_reminder_email($plan, $days_before, $recommended_plans);
        
        // ✅ CHANGED: Use unified email function
        $result = $this->_send_email_unified($plan->email, $plan->name, $subject, $message);
        
        if ($result['status'] === 'success') {
            $this->_log_email($plan->user_id, $plan->email, $subject, $cronId, $executionLogId);
            return ['success' => true];
        } else {
            $error_msg = $this->_extract_error_message($result, $plan->email);
            $this->_log_email_failure($plan->user_id, $plan->email, $subject, $cronId, $executionLogId, $error_msg);
            return ['success' => false, 'error' => $error_msg];
        }
    }

    /**
     * Unified email sending function that works with any email service
     * Returns consistent response format
     */
    private function _send_email_unified($toEmail, $toName, $subject, $message) {
        // Decide which email service to use (you can add logic here)
        $useMailerCloud = true; // Set based on config or condition
        
        if ($useMailerCloud) {
            $result = send_mailercloud_email($toEmail, $toName, $subject, $message);
        } else {
            // For SMTP, we need to adapt parameters
            $result = SendEmailTo($toEmail, $subject, $message);
            
            // Convert SMTP response to same format if needed
            if (!isset($result['data'])) {
                $result['data'] = [
                    'sent_count' => $result['sent'] ?? 0,
                    'failed' => $result['failed'] ?? [],
                    'errors' => $result['errors'] ?? []
                ];
            }
        }
        
        return $result;
    }

    /**
     * Extract error message from any email response format
     */
    private function _extract_error_message($emailResponse, $recipientEmail = null) {
        $errorMsg = 'Unknown error occurred while sending email';
        
        // Check if it's a debug response
        if (isset($emailResponse['status']) && $emailResponse['status'] === 'debug') {
            return 'Debug mode: ' . ($emailResponse['message'] ?? 'No debug message');
        }
        
        // Check for mailercloud format errors
        if (isset($emailResponse['data']['errors']) && !empty($emailResponse['data']['errors'])) {
            $errors = $emailResponse['data']['errors'];
            if (is_array($errors)) {
                // Check if there's error for specific recipient
                if ($recipientEmail && isset($errors[$recipientEmail])) {
                    return $errors[$recipientEmail];
                }
                // Otherwise get first error
                $errorMsg = implode(', ', $errors);
            } else {
                $errorMsg = (string) $errors;
            }
        }
        // Check for SMTP format errors
        elseif (isset($emailResponse['errors']) && !empty($emailResponse['errors'])) {
            $errors = $emailResponse['errors'];
            if (is_array($errors)) {
                if ($recipientEmail && isset($errors[$recipientEmail])) {
                    return $errors[$recipientEmail];
                }
                $errorMsg = implode(', ', $errors);
            } else {
                $errorMsg = (string) $errors;
            }
        }
        // Fallback to message
        elseif (isset($emailResponse['message'])) {
            $errorMsg = $emailResponse['message'];
        }
        
        // Clean up error message
        $errorMsg = strip_tags($errorMsg);
        $errorMsg = substr($errorMsg, 0, 500); // Limit length for database
        
        return $errorMsg;
    }

    /**
     * Get recommended plans - UNCHANGED
     */
    private function _get_recommended_plans($feature_id) {
        // Get current feature's service
        $current_feature = $this->db->select('service_id')
                                   ->from('tb_features')
                                   ->where('feature_id', $feature_id)
                                   ->get()->row();
        
        if (!$current_feature) return [];
        
        // Get other features from same service
        return $this->db->select('f.feature_id, f.feature_name, f.feature_short_description, fd.duration, fd.plan_total')
                       ->from('tb_features f')
                       ->join('tb_feature_durations fd', 'fd.feature_id = f.feature_id')
                       ->where('f.is_active', 'yes')
                       ->where('f.feature_id !=', $feature_id)
                       ->where('f.service_id', $current_feature->service_id)
                       ->order_by('fd.plan_total', 'asc')
                       ->limit(3)
                       ->get()->result();
    }

    /**
     * Log reminder sent - UNCHANGED
     */
    private function _log_reminder($plan_id, $user_id, $days_before) {
        $this->db->insert('tb_plan_reminders', [
            'plan_id' => $plan_id,
            'user_id' => $user_id,
            'reminder_type' => $days_before . '_days',
            'reminder_date' => date('Y-m-d H:i:s')
        ]);
    }

    /**
     * Log successful email - UNCHANGED
     */
    private function _log_email($candidate_id, $email, $subject, $cronId, $executionLogId) {
        $this->db->insert('tb_jr_cron_email_logs', [   // ✅ changed from 'tb_cron_email_logs'
            'cron_job_id' => $cronId,
            'cron_execution_log_id' => $executionLogId,
            'candidate_id' => $candidate_id,
            'email' => $email,
            'subject' => $subject,
            'sent_at' => date('Y-m-d H:i:s'),
            'status' => 'sent'
        ]);
    }

    /**
     * Log failed email - UNCHANGED
     */
    private function _log_email_failure($candidate_id, $email, $subject, $cronId, $executionLogId, $error_message) {
        $this->db->insert('tb_jr_cron_email_logs', [   // ✅ changed from 'tb_cron_email_logs'
            'cron_job_id' => $cronId,
            'cron_execution_log_id' => $executionLogId,
            'candidate_id' => $candidate_id,
            'email' => $email,
            'subject' => $subject,
            'sent_at' => date('Y-m-d H:i:s'),
            'status' => 'failed',
            'error_message' => $error_message
        ]);
    }

    /**
     * Lock/Unlock methods - UNCHANGED
     */
    private function _is_locked($lockFile) {
		$lockTtl = 1800; // 30 minutes
		return file_exists($lockFile) && (time() - filemtime($lockFile)) < $lockTtl;
	}


    private function _unlock($lockFile) {
        if (file_exists($lockFile)) unlink($lockFile);
    }

    /**
     * Build reminder email template
     */
    private function _build_reminder_email($plan, $days_before, $recommended_plans = []) {
        // Set urgency colors
        if ($days_before == 0) {
            $urgency_color = '#e74c3c';
            $urgency_text = 'EXPIRES TODAY!';
            $status_color = '#ffebee';
        } elseif ($days_before == 1) {
            $urgency_color = '#f39c12';
            $urgency_text = 'EXPIRES TOMORROW';
            $status_color = '#fff3e0';
        } else {
            $urgency_color = '#3498db';
            $urgency_text = "EXPIRES IN {$days_before} DAYS";
            $status_color = '#e3f2fd';
        }
        
        // Format dates
        $expiry_date = date('l, d M Y', strtotime($plan->end_date));
        $expiry_time = date('h:i A', strtotime($plan->end_date));
        
        // Plan details
        $plan_name = !empty($plan->feature_name) ? $plan->feature_name : 'Premium Plan';
        $plan_type = !empty($plan->plan_level) ? $plan->plan_level : 'Standard';
        $duration = !empty($plan->duration) ? $plan->duration : 'Monthly';
        $service = !empty($plan->service_name) ? $plan->service_name : 'Service';
        
        // Build recommended plans HTML
        $recommended_html = '';
        if (!empty($recommended_plans)) {
            $recommended_html .= '<div style="margin: 20px 0; padding: 15px; background: #f8f9fa; border-radius: 6px;">
                <h3 style="color: #2c3e50; margin: 0 0 15px 0; font-size: 18px; font-weight: 600;">Recommended Plans For You</h3>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 15px;">';
            
            foreach ($recommended_plans as $rec) {
                $rec_price = !empty($rec->plan_total) ? '₹' . $rec->plan_total : 'Custom Price';
                $rec_duration = !empty($rec->duration) ? $rec->duration : 'Flexible';
                
                $recommended_html .= '<div style="background: white; padding: 15px; border-radius: 5px; box-shadow: 0 1px 4px rgba(0,0,0,0.05);">
                    <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 10px;">
                        <h4 style="margin: 0; color: #2c3e50; font-size: 16px; font-weight: 600;">' . htmlspecialchars($rec->feature_name) . '</h4>
                        <span style="background: #2ecc71; color: white; padding: 3px 10px; border-radius: 15px; font-size: 11px; font-weight: bold;">' . $rec_duration . '</span>
                    </div>
                    <p style="margin: 0 0 12px 0; color: #666; font-size: 13px; line-height: 1.4;">' . htmlspecialchars($rec->feature_short_description) . '</p>
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span style="font-size: 18px; font-weight: bold; color: #e74c3c;">' . $rec_price . '</span>
                        <a href="https://resume.talentsjobs.in/career-services" style="background: #3498db; color: white; padding: 6px 12px; text-decoration: none; border-radius: 3px; font-size: 13px; font-weight: 500;">View Details</a>
                    </div>
                </div>';
            }
            
            $recommended_html .= '</div></div>';
        }
        
        return '<!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
        </head>
        <body style="font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; line-height: 1.5; color: #333; margin: 0; padding: 0;">
            <table width="100%" cellpadding="0" cellspacing="0" style="background: #f5f7fa;">
                <tr>
                    <td>
                        <table width="100%" cellpadding="0" cellspacing="0" style="background: white; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
                            <!-- Header -->
                            <tr>
                                <td style="background: ' . $urgency_color . '; padding: 18px 15px; text-align: center;">
                                    <h1 style="color: white; margin: 0; font-size: 20px; font-weight: 700;">⏰ PLAN EXPIRY REMINDER</h1>
                                    <div style="background: rgba(255,255,255,0.2); display: inline-block; padding: 6px 18px; border-radius: 20px; margin-top: 12px;">
                                        <span style="color: white; font-size: 15px; font-weight: 600;">' . $urgency_text . '</span>
                                    </div>
                                </td>
                            </tr>
                            
                            <!-- Greeting -->
                            <tr>
                                <td style="padding: 18px 15px 10px 15px;">
                                    <h2 style="color: #2c3e50; margin: 0 0 8px 0; font-size: 17px; font-weight: 600;">Hello ' . htmlspecialchars($plan->name) . ',</h2>
                                    <p style="color: #7f8c8d; margin: 0; font-size: 14px;">This is a friendly reminder about your current subscription plan.</p>
                                </td>
                            </tr>
                            
                            <!-- Current Plan Status -->
                            <tr>
                                <td style="padding: 0 15px 15px 15px;">
                                    <div style="background: ' . $status_color . '; padding: 15px; border-radius: 6px; border-left: 3px solid ' . $urgency_color . ';">
                                        <h3 style="color: #2c3e50; margin: 0 0 12px 0; font-size: 15px; font-weight: 600;">📋 Your Current Plan Status</h3>
                                        <table width="100%" cellpadding="0" cellspacing="0" style="font-size: 14px;">
                                            <tr>
                                                <td style="padding: 8px 0; border-bottom: 1px solid #eee;">
                                                    <strong style="color: #666; display: inline-block; width: 120px; font-weight: 500;">Plan Name:</strong>
                                                    <span style="color: #2c3e50; font-weight: 600;">' . htmlspecialchars($plan_name) . '</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 8px 0; border-bottom: 1px solid #eee;">
                                                    <strong style="color: #666; display: inline-block; width: 120px; font-weight: 500;">Plan Type:</strong>
                                                    <span style="color: #2c3e50;">' . $plan_type . ' (' . $duration . ')</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 8px 0; border-bottom: 1px solid #eee;">
                                                    <strong style="color: #666; display: inline-block; width: 120px; font-weight: 500;">Service:</strong>
                                                    <span style="color: #2c3e50;">' . $service . '</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 8px 0; border-bottom: 1px solid #eee;">
                                                    <strong style="color: #666; display: inline-block; width: 120px; font-weight: 500;">Expiry Date:</strong>
                                                    <span style="color: #e74c3c; font-weight: 600;">' . $expiry_date . ' at ' . $expiry_time . '</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 8px 0;">
                                                    <strong style="color: #666; display: inline-block; width: 120px; font-weight: 500;">Days Left:</strong>
                                                    <span style="color: ' . $urgency_color . '; font-weight: 700; font-size: 16px;">' . ($days_before == 0 ? '0 (Today)' : $days_before) . '</span>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                </td>
                            </tr>
                            
                            <!-- Recommended Plans Section -->
                            ' . $recommended_html . '
                            
                            <!-- Call to Action -->
                            <tr>
                                <td style="padding: 15px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); text-align: center;">
                                    <p style="color: rgba(255,255,255,0.9); margin: 0 0 15px 0; font-size: 14px;">
                                        Renew now to avoid interruption in service.
                                    </p>
                                    <a href="https://resume.talentsjobs.in/career-services" style="background: white; color: #764ba2; padding: 10px 24px; text-decoration: none; border-radius: 25px; font-size: 15px; font-weight: 700; display: inline-block; box-shadow: 0 3px 12px rgba(0,0,0,0.15);">
                                        🔄 Renew Plan Now
                                    </a>
                                </td>
                            </tr>
                            
                            <!-- Footer -->
                            <tr>
                                <td style="padding: 15px; background: #2c3e50; text-align: center;">
                                    <p style="color: #bdc3c7; margin: 0 0 8px 0; font-size: 12px;">
                                        © ' . date('Y') . ' ' . SITE_NAME . '. All rights reserved.
                                    </p>
                                    <p style="color: #95a5a6; margin: 0; font-size: 11px;">
                                        This is an automated reminder email.
                                    </p>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </body>
        </html>';
    }
}