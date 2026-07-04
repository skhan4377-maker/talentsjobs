<?php
defined('BASEPATH') OR exit('No direct script access allowed');
 
class CronManager_model extends CI_Model {
    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    public function run_due_crons() {
        $currentDateTime = date('Y-m-d H:i:s');
        $currentTime     = date('H:i:s');
        $today           = date('Y-m-d');
        $todayDay        = strtolower(date('l'));

        echo "🚀 Cron Scheduler Started: $currentDateTime\n";

        // Fetch active cron jobs
        $crons = $this->db->where('is_active', 1)
                          ->get('tb_jr_cron_jobs') 
                          ->result();

        $totalCrons = count($crons);
        echo "📊 Total Active Cron Jobs: $totalCrons\n";

        if (empty($crons)) {
            echo "⚠️ No Active Cron Jobs Found!\n";
            return;
        }

        foreach ($crons as $cron) {
            
            // 1. Prevent concurrent run
            if ($cron->is_running) {
                echo "⏭ Skipping {$cron->name} - Already running\n";
                continue;
            }

            // 2. Time window check
            if ($cron->schedule_type !== 'custom') {
                if ($currentTime < $cron->start_time || $currentTime > $cron->end_time) {
                    echo "⏭ Skipping {$cron->name} - Outside time window\n";
                    continue;
                }
            }

            // 3. Check already executed today
            $executed_today = $this->db->where('cron_job_id', $cron->id)
                                       ->where('execution_date', $today)
                                       ->get('tb_jr_cron_execution_logs')   // ✅ FIXED table name
                                       ->row();

            // 4. Schedule validation
            $scheduleResult = $this->_is_schedule_allowed($cron, $todayDay, $today, $executed_today);
            
            if (!$scheduleResult['allowed']) {
                echo "⏭ Skipping {$cron->name} - {$scheduleResult['message']}\n";
                continue;
            }

            // 5. Execute cron
            echo "▶️ Executing: {$cron->name}\n";
            $result = $this->_execute_cron($cron);
            
            // Display result
            $status_display = in_array($result['status'], ['success', 'partial', 'error', 'skipped', 'no_action']) ? 
                              $result['status'] : 'unknown';
            echo "✅ Completed: {$cron->name} - Status: " . $status_display . "\n";
            
            // Print the full report
            if (!empty($result['display_result'])) {
                echo "\n📋 Report:\n";
                echo str_repeat("=", 50) . "\n";
                echo $result['display_result'] . "\n";
                echo str_repeat("=", 50) . "\n";
            }
            
            // Show error message if exists
            if (!empty($result['error_message'])) {
                echo "❌ Error: " . $result['error_message'] . "\n";
            }
        }

        echo "🏁 Cron Scheduler Finished: " . date('Y-m-d H:i:s') . "\n";
    }

    private function _is_schedule_allowed($cron, $todayDay, $todayDate) {
        $scheduleType = $cron->schedule_type;

        if ($scheduleType == 'daily') {
            return ['allowed' => true, 'message' => 'Daily schedule'];
        }

        if ($scheduleType == 'weekly') {
            if (empty($cron->week_days)) {
                return ['allowed' => false, 'message' => 'No week days configured'];
            }

            $days = explode(',', strtolower($cron->week_days));
            $days = array_map('trim', $days);

            if (!in_array($todayDay, $days)) {
                return ['allowed' => false, 'message' => 'Not a scheduled weekday'];
            }

            return ['allowed' => true, 'message' => 'Weekly schedule'];
        }

        if ($scheduleType == 'monthly') {
            if (empty($cron->month_day)) {
                return ['allowed' => false, 'message' => 'No month day configured'];
            }

            $todayDayOfMonth = (int) date('j');

            if ($todayDayOfMonth != $cron->month_day) {
                return ['allowed' => false, 'message' => 'Not monthly scheduled date'];
            }

            return ['allowed' => true, 'message' => 'Monthly schedule'];
        }

        if ($scheduleType == 'custom') {
            if (empty($cron->custom_schedule)) {
                return ['allowed' => false, 'message' => 'No custom schedule'];
            }

            $customDateTime = date('Y-m-d H:i:00', strtotime($cron->custom_schedule));
            $now = date('Y-m-d H:i:00');

            if ($now >= $customDateTime) {
                return ['allowed' => true, 'message' => 'Custom schedule time'];
            }

            return ['allowed' => false, 'message' => 'Custom schedule time not hit yet'];
        }

        return ['allowed' => false, 'message' => 'Invalid schedule'];
    }

    private function _execute_cron($cron) {
        $start_time = microtime(true);
        
        // Mark as running
        $this->db->where('id', $cron->id)->update('tb_jr_cron_jobs', [  
            'is_running' => 1,
            'running_since' => date('Y-m-d H:i:s')
        ]);

        try {
            $modelName  = $cron->cron_model;
            $methodName = $cron->context;

            $this->load->model('cron/' . $modelName);

            if (!method_exists($this->$modelName, $methodName)) {
                throw new Exception("Method $methodName does not exist in $modelName");
            }

            // ✅ ALWAYS create execution log BEFORE calling cron method
            $execution_log_id = $this->_create_execution_log($cron->id, 'running');

            $config = [
                'cron_id' => $cron->id,
                'execution_log_id' => $execution_log_id,
                'emails_per_run' => $cron->emails_per_run
            ];

            // Execute the cron method and get result
            $result = $this->$modelName->$methodName($config);
            
            $execution_time = microtime(true) - $start_time;
            
            // Analyze the result
            $analysis = $this->_analyze_cron_result($result, $execution_time);
            
            // Determine final status
            $final_status = $analysis['status'];
            
            // ✅ ALWAYS UPDATE LAST_RUN IN CRON TABLE (Your main requirement)
            $this->_update_cron_last_run($cron->id, $execution_time);
            
            // ✅ Decide if we should keep execution log or delete it
            if ($analysis['should_keep_log']) {
                // Update execution log with details
                $update_data = [
                    'status' => $final_status,
                    'emails_sent' => $analysis['emails_sent'],
                    'execution_time' => $execution_time,
                    'error_message' => $analysis['error_message'],
                    'finished_at' => date('Y-m-d H:i:s')
                ];
                
                $this->_update_execution_log($execution_log_id, $update_data);
                
                echo "📝 Execution log UPDATED for {$cron->name} (Work done: {$analysis['emails_sent']} emails)\n";
            } else {
                // ❌ DELETE the execution log since no work was done
                $this->db->where('id', $execution_log_id)->delete('tb_jr_cron_execution_logs');   // ✅ FIXED table name
                echo "🗑️ Execution log DELETED for {$cron->name} - No work done (Status: {$analysis['status']}, Emails: {$analysis['emails_sent']})\n";
            }
            
            return [
                'status' => $final_status,
                'display_result' => $analysis['display_result'],
                'error_message' => $analysis['error_message']
            ];
            
        } catch (Exception $e) {
            $execution_time = microtime(true) - $start_time;
            $error_message = $e->getMessage();
            
            // ✅ ALWAYS UPDATE LAST_RUN IN CRON TABLE (even on error)
            $this->_update_cron_last_run($cron->id, $execution_time);
            
            // Error occurred, update execution log
            if (isset($execution_log_id)) {
                $this->_update_execution_log($execution_log_id, [
                    'status' => 'error',
                    'error_message' => $error_message,
                    'execution_time' => $execution_time,
                    'finished_at' => date('Y-m-d H:i:s')
                ]);
            }
            
            return [
                'status' => 'error',
                'display_result' => "❌ Error: " . $error_message,
                'error_message' => $error_message
            ];
            
        } finally {
            // Reset cron status
            $this->db->where('id', $cron->id)->update('tb_jr_cron_jobs', [   
                'is_running' => 0,
                'running_since' => NULL
            ]);

            // Deactivate custom schedule cron
            if ($cron->schedule_type == 'custom') {
                $this->db->where('id', $cron->id)->update('tb_jr_cron_jobs', [ 
                    'is_active' => 0
                ]);
            }
        }
    }
    
    /**
     * Analyze cron result to determine if we should keep the execution log
     */
    private function _analyze_cron_result($result, $execution_time) {
        $analysis = [
            'should_keep_log' => false,
            'status' => 'skipped',
            'emails_sent' => 0,
            'display_result' => '',
            'error_message' => ''
        ];
        
        if (is_array($result)) {
            // New format: array with details
            $analysis['status'] = isset($result['status']) ? $result['status'] : 'partial';
            $analysis['emails_sent'] = isset($result['emails_sent']) ? (int)$result['emails_sent'] : 0;
            $analysis['display_result'] = isset($result['display_result']) ? $result['display_result'] : '';
            $analysis['error_message'] = isset($result['error_message']) ? $result['error_message'] : '';
            
            // ✅ ONLY keep log if work was done or error occurred
            $analysis['should_keep_log'] = (
                $analysis['emails_sent'] > 0 || 
                $analysis['status'] == 'error'
            );
            
        } else if (is_string($result)) {
            // Old format: string report
            $analysis['display_result'] = $result;
            
            // Extract status from report
            $analysis['status'] = 'skipped';
            if (strpos($result, 'Status:') !== false) {
                $lines = explode("\n", $result);
                foreach ($lines as $line) {
                    if (strpos($line, 'Status:') !== false) {
                        $parts = explode(':', $line);
                        if (isset($parts[1])) {
                            $analysis['status'] = strtolower(trim($parts[1]));
                            break;
                        }
                    }
                }
            }
            
            // Check if emails were sent
            if (preg_match('/Emails Sent: (\d+)/', $result, $matches)) {
                $analysis['emails_sent'] = (int)$matches[1];
            }
            
            // ✅ ONLY keep log if work was done or error occurred
            $analysis['should_keep_log'] = (
                $analysis['emails_sent'] > 0 || 
                $analysis['status'] == 'error'
            );
            
        } else {
            // Integer format (very old)
            $analysis['emails_sent'] = (int)$result;
            $analysis['status'] = $analysis['emails_sent'] > 0 ? 'success' : 'skipped';
            $analysis['display_result'] = "Sent: {$analysis['emails_sent']} emails | Time: " . round($execution_time, 2) . "s";
            $analysis['should_keep_log'] = ($analysis['emails_sent'] > 0);
        }
        
        return $analysis;
    }
    
    // ================= HELPER FUNCTIONS =================

    private function _create_execution_log($cronId, $status = 'running') {
        $data = [
            'cron_job_id'   => $cronId,
            'execution_date' => date('Y-m-d'),
            'started_at'     => date('Y-m-d H:i:s'),
            'status'         => $status
        ];

        $this->db->insert('tb_jr_cron_execution_logs', $data);   // ✅ FIXED table name
        return $this->db->insert_id();
    }

    private function _update_execution_log($logId, $data) {
        $this->db->where('id', $logId)->update('tb_jr_cron_execution_logs', $data);   // ✅ FIXED table name
    }

    /**
     * ✅ NEW FUNCTION: Only updates last_run and last_execution_time in cron table
     * This runs EVERY TIME regardless of email count or status
     */
    private function _update_cron_last_run($cronId, $execution_time) {
        $updateData = [
            'last_run' => date('Y-m-d H:i:s'),
            'last_execution_time' => round($execution_time, 4)
        ];
        
        $this->db->where('id', $cronId)->update('tb_jr_cron_jobs', $updateData);   // ✅ FIXED table name
    }
}