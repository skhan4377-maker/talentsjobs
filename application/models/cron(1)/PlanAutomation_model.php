<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class PlanAutomation_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->helper('email');
    }

    /**
     * Main cron method - Returns array with detailed report
     */
    public function plan_automation($config = []) {
        $lockFile = sys_get_temp_dir() . '/plan_automation.lock';

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
            
            $result = $this->_process_plans($cronId, $executionLogId, $emailsPerRun);
            
            return [
                'status' => $result['overall_status'],
                'emails_sent' => $result['stats']['activation_emails_sent'] + $result['stats']['expiry_emails_sent'],
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
     * Process all plan automation tasks
     */
    private function _process_plans($cronId, $executionLogId, $limit) {
        $stats = [
            'expired_count' => 0,
            'activated_count' => 0,
            'fixed_count' => 0,
            'activation_emails_sent' => 0,
            'expiry_emails_sent' => 0,
            'activation_emails_failed' => 0,
            'expiry_emails_failed' => 0,
            'failed_emails' => []  // New: Store failed email details
        ];
        
        // Step 1: Expire past plans
        $expired_result = $this->_expire_past_plans($cronId, $executionLogId, $limit);
        $stats['expired_count'] = $expired_result['count'];
        $stats['expiry_emails_sent'] = $expired_result['emails_sent'];
        $stats['expiry_emails_failed'] = $expired_result['emails_failed'];
        $stats['failed_emails'] = array_merge($stats['failed_emails'], $expired_result['failed_emails'] ?? []);
        
        // Step 2: Activate upcoming plans
        $remaining_limit = $limit - ($stats['expiry_emails_sent'] + $stats['expiry_emails_failed']);
        $activated_result = $this->_activate_upcoming_plans($cronId, $executionLogId, $remaining_limit);
        $stats['activated_count'] = $activated_result['count'];
        $stats['activation_emails_sent'] = $activated_result['emails_sent'];
        $stats['activation_emails_failed'] = $activated_result['emails_failed'];
        $stats['failed_emails'] = array_merge($stats['failed_emails'], $activated_result['failed_emails'] ?? []);
        
        // Step 3: Fix inconsistencies
        $fixed_result = $this->_fix_plan_inconsistencies();
        $stats['fixed_count'] = $fixed_result['count'];
        
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
        $total_emails_sent = $stats['activation_emails_sent'] + $stats['expiry_emails_sent'];
        $total_emails_failed = $stats['activation_emails_failed'] + $stats['expiry_emails_failed'];
        
        // Determine status
        if ($stats['expired_count'] == 0 && $stats['activated_count'] == 0 && $stats['fixed_count'] == 0) {
            $status = 'no_action';
            $message = 'No plans to process';
        } elseif ($total_emails_failed > 0 && $total_emails_sent > 0) {
            $status = 'partial';
            $message = 'Some emails sent, some failed';
        } elseif ($total_emails_failed > 0 && $total_emails_sent == 0) {
            $status = 'error';
            $message = 'All emails failed';
        } else {
            $status = 'success';
            $message = 'Plan automation completed';
        }
        
        return "📋 Plan Automation Report\n" .
               "===============================\n" .
               "Plans Expired: {$stats['expired_count']}\n" .
               "Plans Activated: {$stats['activated_count']}\n" .
               "Issues Fixed: {$stats['fixed_count']}\n" .
               "Activation Emails Sent: {$stats['activation_emails_sent']}\n" .
               "Activation Emails Failed: {$stats['activation_emails_failed']}\n" .
               "Expiry Emails Sent: {$stats['expiry_emails_sent']}\n" .
               "Expiry Emails Failed: {$stats['expiry_emails_failed']}\n" .
               "Status: {$status}\n" .
               "Message: {$message}";
    }

    /**
     * Determine overall status based on stats
     */
    private function _determine_overall_status($stats) {
        $total_emails_sent = $stats['activation_emails_sent'] + $stats['expiry_emails_sent'];
        $total_emails_failed = $stats['activation_emails_failed'] + $stats['expiry_emails_failed'];
        $total_actions = $stats['expired_count'] + $stats['activated_count'] + $stats['fixed_count'];
        
        if ($total_actions == 0 && $total_emails_sent == 0 && $total_emails_failed == 0) {
            return 'no_action';
        } elseif ($total_emails_failed > 0 && $total_emails_sent == 0) {
            return 'error';
        } elseif ($total_emails_failed > 0) {
            return 'partial';
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
     * Expire past plans - MODIFIED TO COLLECT FAILED EMAILS
     */
    private function _expire_past_plans($cronId, $executionLogId, $limit) {
        $current_time = date('Y-m-d H:i:s');
        $emails_sent = 0;
        $emails_failed = 0;
        $failed_emails = [];
        
        $this->db->select('up.*, u.email, u.name, f.feature_name')
                 ->from('tb_user_plans up')
                 ->join('tb_candidate u', 'u.candidate_id = up.user_id')
                 ->join('tb_features f', 'f.feature_id = up.feature_id', 'left')
                 ->where('up.end_date <', $current_time)
                 ->where('up.status', 'active')
                 ->limit($limit);
        
        $expired_plans = $this->db->get()->result();
        
        foreach ($expired_plans as $plan) {
            // Update plan status - UNCHANGED
            $this->db->where('id', $plan->id)
                     ->update('tb_user_plans', ['status' => 'expired', 'updated_at' => $current_time]);
            
            // Send email
            $email_result = $this->_send_expiry_email($plan, $cronId, $executionLogId);
            if ($email_result['success']) {
                $emails_sent++;
            } else {
                $emails_failed++;
                $failed_emails[] = [
                    'email' => $plan->email,
                    'error' => $email_result['error'] ?? 'Unknown error'
                ];
            }
        }
        
        return [
            'count' => count($expired_plans), 
            'emails_sent' => $emails_sent, 
            'emails_failed' => $emails_failed,
            'failed_emails' => $failed_emails
        ];
    }

    /**
     * Activate upcoming plans - MODIFIED TO COLLECT FAILED EMAILS
     */
    private function _activate_upcoming_plans($cronId, $executionLogId, $limit) {
        $current_time = date('Y-m-d H:i:s');
        $emails_sent = 0;
        $emails_failed = 0;
        $failed_emails = [];
        
        // Find users with expired active plans and upcoming plans
        $users = $this->db->query("
            SELECT DISTINCT u.candidate_id as user_id, u.email, u.name 
            FROM tb_candidate u
            WHERE EXISTS (
                SELECT 1 FROM tb_user_plans up1 
                WHERE up1.user_id = u.candidate_id 
                AND up1.status = 'active' 
                AND up1.end_date < ?
            )
            AND EXISTS (
                SELECT 1 FROM tb_user_plans up2 
                WHERE up2.user_id = u.candidate_id 
                AND up2.status = 'upcoming'
            )
            LIMIT ?
        ", [$current_time, $limit])->result();
        
        foreach ($users as $user) {
            // Get oldest upcoming plan
            $upcoming_plan = $this->db->select('up.*, f.feature_name, fd.duration')
                                      ->from('tb_user_plans up')
                                      ->join('tb_features f', 'f.feature_id = up.feature_id', 'left')
                                      ->join('tb_feature_durations fd', 'fd.duration_id = up.plan_id', 'left')
                                      ->where('up.user_id', $user->user_id)
                                      ->where('up.status', 'upcoming')
                                      ->order_by('up.created_at', 'ASC')
                                      ->limit(1)
                                      ->get()->row();
            
            if ($upcoming_plan) {
                // Calculate dates
                $duration = $upcoming_plan->duration ?? 30;
                $start_date = $current_time;
                $end_date = date('Y-m-d H:i:s', strtotime("+{$duration} days"));
                
                // Update plan - UNCHANGED
                $this->db->where('id', $upcoming_plan->id)
                         ->update('tb_user_plans', [
                             'status' => 'active',
                             'start_date' => $start_date,
                             'end_date' => $end_date,
                             'updated_at' => $current_time
                         ]);
                
                // Add user details
                $upcoming_plan->email = $user->email;
                $upcoming_plan->name = $user->name;
                
                // Send email
                $email_result = $this->_send_activation_email($upcoming_plan, $start_date, $end_date, $cronId, $executionLogId);
                if ($email_result['success']) {
                    $emails_sent++;
                } else {
                    $emails_failed++;
                    $failed_emails[] = [
                        'email' => $user->email,
                        'error' => $email_result['error'] ?? 'Unknown error'
                    ];
                }
            }
        }
        
        return [
            'count' => count($users), 
            'emails_sent' => $emails_sent, 
            'emails_failed' => $emails_failed,
            'failed_emails' => $failed_emails
        ];
    }

    /**
     * Fix plan inconsistencies - UNCHANGED
     */
    private function _fix_plan_inconsistencies() {
        $current_time = date('Y-m-d H:i:s');
        $count = 0;
        
        // Fix expired but active
        $this->db->where('end_date <', $current_time)
                 ->where('status', 'active')
                 ->update('tb_user_plans', ['status' => 'expired', 'updated_at' => $current_time]);
        $count += $this->db->affected_rows();
        
        // Fix active but not started
        $this->db->where('start_date >', $current_time)
                 ->where('status', 'active')
                 ->update('tb_user_plans', ['status' => 'upcoming', 'updated_at' => $current_time]);
        $count += $this->db->affected_rows();
        
        // Fix upcoming but should be active
        $this->db->where('start_date <=', $current_time)
                 ->where('end_date >=', $current_time)
                 ->where('status', 'upcoming')
                 ->update('tb_user_plans', ['status' => 'active', 'updated_at' => $current_time]);
        $count += $this->db->affected_rows();
        
        return ['count' => $count];
    }

    /**
     * Send plan activation email - MODIFIED TO USE UNIFIED FUNCTION
     */
    private function _send_activation_email($plan, $start_date, $end_date, $cronId, $executionLogId) {
        $subject = '🎉 Your Plan Has Been Activated!';
        $message = $this->_build_activation_email($plan, $start_date, $end_date);
        
        // ✅ CHANGED: Use unified email function
        $status = $this->_send_email_unified($plan->email, $plan->name, $subject, $message);
        
        if ($status['status'] === 'success') {
            $this->_log_email($plan->user_id, $plan->email, $subject, $cronId, $executionLogId);
            return ['success' => true];
        } else {
            $error_msg = $this->_extract_error_message($status, $plan->email);
            $this->_log_email_failure($plan->user_id, $plan->email, $subject, $cronId, $executionLogId, $error_msg);
            return ['success' => false, 'error' => $error_msg];
        }
    }

    /**
     * Send plan expiry email - MODIFIED TO USE UNIFIED FUNCTION
     */
    private function _send_expiry_email($plan, $cronId, $executionLogId) {
        $subject = '⚠️ Your Plan Has Expired';
        $message = $this->_build_expiry_email($plan);
        
        // ✅ CHANGED: Use unified email function
        $status = $this->_send_email_unified($plan->email, $plan->name, $subject, $message);
        
        if ($status['status'] === 'success') {
            $this->_log_email($plan->user_id, $plan->email, $subject, $cronId, $executionLogId);
            return ['success' => true];
        } else {
            $error_msg = $this->_extract_error_message($status, $plan->email);
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
     * Log successful email - UNCHANGED
     */
    private function _log_email($candidate_id, $email, $subject, $cronId, $executionLogId) {
        $this->db->insert('tb_jr_cron_email_logs', [
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
        $this->db->insert('tb_jr_cron_email_logs', [
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
        return (file_exists($lockFile) && (time() - filemtime($lockFile)) < 300);
    }

    private function _unlock($lockFile) {
        if (file_exists($lockFile)) unlink($lockFile);
    }
    
    /**
     * Build activation email template - Compact version with plan details
     */
    private function _build_activation_email($plan, $start_date, $end_date) {
        // Plan details
        $plan_name = !empty($plan->feature_name) ? $plan->feature_name : 'Premium Plan';
        $plan_type = !empty($plan->plan_level) ? $plan->plan_level : 'Standard';
        $duration = !empty($plan->duration) ? $plan->duration : 'Monthly';
        $service = !empty($plan->service_name) ? $plan->service_name : 'Service';
        
        // Format dates
        $start_date_formatted = date('l, d M Y', strtotime($start_date));
        $end_date_formatted = date('l, d M Y', strtotime($end_date));
        $start_time = date('h:i A', strtotime($start_date));
        $end_time = date('h:i A', strtotime($end_date));
        
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
                                <td style="background: #4CAF50; padding: 18px 15px; text-align: center;">
                                    <h1 style="color: white; margin: 0; font-size: 20px; font-weight: 700;">🎉 Plan Activated</h1>
                                </td>
                            </tr>
                            
                            <!-- Greeting -->
                            <tr>
                                <td style="padding: 18px 15px 10px 15px;">
                                    <h2 style="color: #2c3e50; margin: 0 0 8px 0; font-size: 17px; font-weight: 600;">Hello ' . htmlspecialchars($plan->name) . ',</h2>
                                    <p style="color: #7f8c8d; margin: 0; font-size: 14px;">Your subscription plan has been <strong>activated successfully</strong>!</p>
                                </td>
                            </tr>
                            
                            <!-- Plan Activation Details -->
                            <tr>
                                <td style="padding: 0 15px 15px 15px;">
                                    <div style="background: #f8f9fa; padding: 15px; border-radius: 6px; border-left: 3px solid #4CAF50;">
                                        <h3 style="color: #2c3e50; margin: 0 0 12px 0; font-size: 15px; font-weight: 600;">📋 Activated Plan Details</h3>
                                        <table width="100%" cellpadding="0" cellspacing="0" style="font-size: 14px;">
                                            <tr>
                                                <td style="padding: 8px 0; border-bottom: 1px solid #eee;">
                                                    <strong style="color: #666; display: inline-block; width: 130px; font-weight: 500;">Plan Name:</strong>
                                                    <span style="color: #2c3e50; font-weight: 600;">' . htmlspecialchars($plan_name) . '</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 8px 0; border-bottom: 1px solid #eee;">
                                                    <strong style="color: #666; display: inline-block; width: 130px; font-weight: 500;">Plan Type:</strong>
                                                    <span style="color: #2c3e50;">' . $plan_type . ' (' . $duration . ')</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 8px 0; border-bottom: 1px solid #eee;">
                                                    <strong style="color: #666; display: inline-block; width: 130px; font-weight: 500;">Service:</strong>
                                                    <span style="color: #2c3e50;">' . $service . '</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 8px 0; border-bottom: 1px solid #eee;">
                                                    <strong style="color: #666; display: inline-block; width: 130px; font-weight: 500;">Activated On:</strong>
                                                    <span style="color: #2c3e50;">' . $start_date_formatted . ' at ' . $start_time . '</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 8px 0;">
                                                    <strong style="color: #666; display: inline-block; width: 130px; font-weight: 500;">Valid Until:</strong>
                                                    <span style="color: #2c3e50; font-weight: 600;">' . $end_date_formatted . ' at ' . $end_time . '</span>
                                                </td>
                                            </tr>
                                        </table>
                                        <div style="margin-top: 12px; padding: 8px 12px; background: #e8f5e8; border-radius: 4px; display: inline-block;">
                                            <span style="color: #4CAF50; font-weight: 600; font-size: 14px;">✅ Active & Running</span>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            
                            <!-- Important Information -->
                            <tr>
                                <td style="padding: 0 15px 15px 15px;">
                                    <div style="background: #e3f2fd; padding: 12px; border-radius: 6px; border-left: 3px solid #2196F3;">
                                        <h4 style="color: #1565c0; margin: 0 0 8px 0; font-size: 14px; font-weight: 600;">What You Can Do Now:</h4>
                                        <ul style="margin: 0; padding-left: 18px; color: #7f8c8d; font-size: 13px;">
                                            <li style="margin-bottom: 4px;">Access all premium features immediately</li>
                                            <li style="margin-bottom: 4px;">Use the dashboard to manage your plan</li>
                                            <li>Contact support if you need assistance</li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                            
                            <!-- Call to Action -->
                            <tr>
                                <td style="padding: 15px; background: linear-gradient(135deg, #4CAF50 0%, #2E7D32 100%); text-align: center;">
                                    <p style="color: rgba(255,255,255,0.9); margin: 0 0 15px 0; font-size: 14px;">
                                        Start using your activated plan now!
                                    </p>
                                    <a href="https://resume.talentsjobs.in/dashboard" style="background: white; color: #4CAF50; padding: 10px 24px; text-decoration: none; border-radius: 25px; font-size: 15px; font-weight: 700; display: inline-block; box-shadow: 0 3px 12px rgba(0,0,0,0.15);">
                                        Go to Dashboard
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
                                        This is an automated email.
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

    /**
     * Build expiry email template - Compact version with plan details
     */
    private function _build_expiry_email($plan) {
        // Plan details
        $plan_name = !empty($plan->feature_name) ? $plan->feature_name : 'Premium Plan';
        $plan_type = !empty($plan->plan_level) ? $plan->plan_level : 'Standard';
        $duration = !empty($plan->duration) ? $plan->duration : 'Monthly';
        $service = !empty($plan->service_name) ? $plan->service_name : 'Service';
        
        // Format dates
        $expiry_date = date('l, d M Y', strtotime($plan->end_date));
        $expiry_time = date('h:i A', strtotime($plan->end_date));
        $current_date = date('d M Y, h:i A');
        
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
                                <td style="background: #e74c3c; padding: 18px 15px; text-align: center;">
                                    <h1 style="color: white; margin: 0; font-size: 20px; font-weight: 700;">⚠️ Plan Expired</h1>
                                </td>
                            </tr>
                            
                            <!-- Greeting -->
                            <tr>
                                <td style="padding: 18px 15px 10px 15px;">
                                    <h2 style="color: #2c3e50; margin: 0 0 8px 0; font-size: 17px; font-weight: 600;">Hello ' . htmlspecialchars($plan->name) . ',</h2>
                                    <p style="color: #7f8c8d; margin: 0; font-size: 14px;">Your subscription plan has <strong>expired</strong> as of ' . $current_date . '.</p>
                                </td>
                            </tr>
                            
                            <!-- Expired Plan Details -->
                            <tr>
                                <td style="padding: 0 15px 15px 15px;">
                                    <div style="background: #ffebee; padding: 15px; border-radius: 6px; border-left: 3px solid #e74c3c;">
                                        <h3 style="color: #c62828; margin: 0 0 12px 0; font-size: 15px; font-weight: 600;">📋 Expired Plan Details</h3>
                                        <table width="100%" cellpadding="0" cellspacing="0" style="font-size: 14px;">
                                            <tr>
                                                <td style="padding: 8px 0; border-bottom: 1px solid #eee;">
                                                    <strong style="color: #666; display: inline-block; width: 130px; font-weight: 500;">Plan Name:</strong>
                                                    <span style="color: #2c3e50; font-weight: 600;">' . htmlspecialchars($plan_name) . '</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 8px 0; border-bottom: 1px solid #eee;">
                                                    <strong style="color: #666; display: inline-block; width: 130px; font-weight: 500;">Plan Type:</strong>
                                                    <span style="color: #2c3e50;">' . $plan_type . ' (' . $duration . ')</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 8px 0; border-bottom: 1px solid #eee;">
                                                    <strong style="color: #666; display: inline-block; width: 130px; font-weight: 500;">Service:</strong>
                                                    <span style="color: #2c3e50;">' . $service . '</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 8px 0;">
                                                    <strong style="color: #666; display: inline-block; width: 130px; font-weight: 500;">Expired On:</strong>
                                                    <span style="color: #e74c3c; font-weight: 600;">' . $expiry_date . ' at ' . $expiry_time . '</span>
                                                </td>
                                            </tr>
                                        </table>
                                        <div style="margin-top: 12px; padding: 8px 12px; background: #ffcdd2; border-radius: 4px; display: inline-block;">
                                            <span style="color: #c62828; font-weight: 600; font-size: 14px;">❌ Expired</span>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            
                            <!-- Important Information -->
                            <tr>
                                <td style="padding: 0 15px 15px 15px;">
                                    <div style="background: #fff3e0; padding: 12px; border-radius: 6px; border-left: 3px solid #ff9800;">
                                        <h4 style="color: #e65100; margin: 0 0 8px 0; font-size: 14px; font-weight: 600;">What Happens Next:</h4>
                                        <ul style="margin: 0; padding-left: 18px; color: #7f8c8d; font-size: 13px;">
                                            <li style="margin-bottom: 4px;">Premium features are no longer accessible</li>
                                            <li style="margin-bottom: 4px;">Your data is safe and preserved for 30 days</li>
                                            <li>Any upcoming plans will be activated automatically</li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                            
                            <!-- Call to Action -->
                            <tr>
                                <td style="padding: 15px; background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%); text-align: center;">
                                    <p style="color: rgba(255,255,255,0.9); margin: 0 0 15px 0; font-size: 14px;">
                                        Renew your plan to continue enjoying premium features.
                                    </p>
                                    <a href="https://resume.talentsjobs.in/career-services" style="background: white; color: #e74c3c; padding: 10px 24px; text-decoration: none; border-radius: 25px; font-size: 15px; font-weight: 700; display: inline-block; box-shadow: 0 3px 12px rgba(0,0,0,0.15);">
                                        Renew Your Plan
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
                                        This is an automated email.
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