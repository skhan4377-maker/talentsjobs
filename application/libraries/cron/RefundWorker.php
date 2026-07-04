<?php
defined('BASEPATH') OR exit('No direct script access allowed');
date_default_timezone_set('Asia/Kolkata');
/**
 * RefundWorker
 *
 * Cron-executed worker
 * - Creates refund request in Razorpay
 * - Safe for retries
 * - Global + row-level lock protected
 * - Logs only when refund actions occur
 * - Updates last_run / last_execution_time on every run
 * - Respects schedule (daily, weekly, monthly, custom) from tb_jr_cron_jobs
 */
class RefundWorker
{
    protected $CI;

    public function __construct()
    {
        $this->CI =& get_instance();
        $this->CI->load->database();
        $this->CI->load->model('admin/Refund_model');
        $this->CI->load->model('admin/Payment_model');
        $this->CI->load->config('razorpay');
    }

    /**
     * Entry point called by CronManager
     *
     * @param array $config  Optional (e.g. ['cron_job_id' => X, 'cron_name' => 'Y', 'emails_per_run' => 10])
     * @return array
     */
    public function run($config = [])
    {
        $lockFile = sys_get_temp_dir() . '/refund_worker.lock';
        if ($this->_isLocked($lockFile)) {
            return [
                'status'         => 'skipped',
                'processed'      => 0,
                'failed'         => 0,
                'display_result' => 'Skipped – RefundWorker already running'
            ];
        }

        file_put_contents($lockFile, time());
        $startTime = microtime(true);

        try {
            // ------------------ FETCH CRON JOB ------------------
            $cron = null;
            $cronId = null;

            if (!empty($config['cron_job_id']) && !empty($config['cron_name'])) {
                $cronId = (int) $config['cron_job_id'];
                $cron = (object) ['id' => $cronId, 'name' => $config['cron_name']];
            }

            $limit = (int) ($config['emails_per_run'] ?? 0);
            if ($limit <= 0) {
                $dbCron = $this->CI->db
                    ->where('context', 'process')
                    ->where('is_active', 1)
                    ->get('tb_jr_cron_jobs')
                    ->row();

                if (!$dbCron) {
                    return [
                        'status'         => 'no_action',
                        'processed'      => 0,
                        'failed'         => 0,
                        'display_result' => 'No active cron job found'
                    ];
                }

                $limit = (int) $dbCron->emails_per_run;
                if (!$cron) {
                    $cron = $dbCron;
                }
                $cronId = (int) $dbCron->id;
            } elseif (!$cron) {
                $dbCron = $this->CI->db
                    ->where('context', 'process')
                    ->where('is_active', 1)
                    ->get('tb_jr_cron_jobs')
                    ->row();
                if ($dbCron) {
                    $cron = $dbCron;
                    $cronId = (int) $dbCron->id;
                }
            }

            if (!$cronId) {
                return [
                    'status'         => 'no_action',
                    'processed'      => 0,
                    'failed'         => 0,
                    'display_result' => 'Cron job record missing'
                ];
            }

            // ============ SCHEDULE CHECK (INTEGRATED) ============
            $scheduleCheck = $this->_isScheduleAllowed($cron);
            if (!$scheduleCheck['allowed']) {
                $execTime = round(microtime(true) - $startTime, 4);
                $this->_updateLastRun($cronId, $execTime);
                return [
                    'status'         => 'skipped',
                    'processed'      => 0,
                    'failed'         => 0,
                    'display_result' => '⏭ ' . $scheduleCheck['message']
                ];
            }

            // ------------------ FETCH APPROVED REFUNDS ------------------
            $refunds = $this->CI->db
                ->where('status', 'approved')
                ->order_by('id', 'ASC')
                ->limit($limit)
                ->get('tb_ft_refund_requests')
                ->result_array();

            if (empty($refunds)) {
                $execTime = round(microtime(true) - $startTime, 4);
                $this->_updateLastRun($cronId, $execTime);
                return [
                    'status'         => 'no_action',
                    'processed'      => 0,
                    'failed'         => 0,
                    'display_result' => 'No approved refunds found'
                ];
            }

            $processed = 0;
            $failed    = 0;

            foreach ($refunds as $refund) {
                if (!$this->CI->Refund_model->lock_refund_for_processing($refund['id'])) {
                    continue;
                }

                $result = $this->_createRazorpayRefund($refund);

                if ($result['success']) {
                    $this->CI->Refund_model->update_refund_status(
                        $refund['id'],
                        'processing',
                        [
                            'razorpay_refund_id' => $result['refund_id'],
                            'gateway_status'     => 'pending'
                        ]
                    );
                    $processed++;
                } else {
                    $this->CI->Refund_model->finalize_refund(
                        $refund['id'],
                        false,
                        null,
                        $result['message']
                    );
                    $failed++;
                }
            }

            // Build return status
            $status = $processed > 0
                ? ($failed > 0 ? 'partial' : 'success')
                : ($failed > 0 ? 'failed' : 'no_action');

            $execTime = round(microtime(true) - $startTime, 4);

            // Log if something happened
            if ($processed > 0 || $failed > 0) {
                $this->_logExecution($cron, $status, $processed, $failed, $execTime);
            }

            // Always update last_run and last_execution_time
            $this->_updateLastRun($cronId, $execTime);

            return [
                'status'         => $status,
                'processed'      => $processed,
                'failed'         => $failed,
                'display_result' => "Refunds processed: {$processed}\nRefunds failed: {$failed}"
            ];

        } catch (Throwable $e) {
            $execTime = round(microtime(true) - $startTime, 4);
            if (isset($cronId)) {
                $this->_updateLastRun($cronId, $execTime);
            }
            if (isset($cron)) {
                $this->_logExecution($cron, 'failed', 0, 0, $execTime, $e->getMessage());
            }
            log_message('error', 'RefundWorker Exception: ' . $e->getMessage());
            return [
                'status'         => 'error',
                'processed'      => 0,
                'failed'         => 0,
                'display_result' => 'Exception: ' . $e->getMessage()
            ];
        } finally {
            $this->_unlock($lockFile);
        }
    }

    // ---------------------------------------------------------
    // SCHEDULE VALIDATION (same logic as CronManager)
    // ---------------------------------------------------------
    private function _isScheduleAllowed($cron)
    {
        $nowTime  = date('H:i:s');
        $todayDay = strtolower(date('l'));

        // Time window check (skip for custom schedule)
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
                return ['allowed' => true, 'message' => 'Daily schedule allowed'];

            case 'weekly':
                if (empty($cron->week_days)) {
                    return ['allowed' => false, 'message' => 'No week days configured'];
                }
                $days = array_map('trim', explode(',', strtolower($cron->week_days)));
                if (!in_array($todayDay, $days)) {
                    return ['allowed' => false, 'message' => 'Not a scheduled weekday'];
                }
                return ['allowed' => true, 'message' => 'Weekly schedule allowed'];

            case 'monthly':
                if (empty($cron->month_day)) {
                    return ['allowed' => false, 'message' => 'No month day configured'];
                }
                $todayDayOfMonth = (int) date('j');
                if ($todayDayOfMonth != $cron->month_day) {
                    return ['allowed' => false, 'message' => 'Not monthly scheduled date'];
                }
                return ['allowed' => true, 'message' => 'Monthly schedule allowed'];

            case 'custom':
                if (empty($cron->custom_schedule)) {
                    return ['allowed' => false, 'message' => 'No custom schedule set'];
                }
                // custom_schedule can be a datetime string like '2026-06-01 15:30:00'
                $customDateTime = date('Y-m-d H:i:00', strtotime($cron->custom_schedule));
                $nowRounded    = date('Y-m-d H:i:00');
                if ($nowRounded >= $customDateTime) {
                    return ['allowed' => true, 'message' => 'Custom schedule time reached'];
                }
                return ['allowed' => false, 'message' => 'Custom schedule time not yet reached'];

            default:
                return ['allowed' => false, 'message' => 'Invalid schedule type'];
        }
    }

    // ---------------------------------------------------------
    // RAZORPAY REFUND CREATION
    // ---------------------------------------------------------
    private function _createRazorpayRefund($refund)
    {
        $payment = $this->CI->Payment_model->get_payment_by_order_id($refund['order_id']);

        if (!$payment || empty($payment['payment_id'])) {
            return ['success' => false, 'message' => 'Payment not found for order'];
        }

        try {
            $api = new \Razorpay\Api\Api(
                $this->CI->config->item('razorpay_key_id'),
                $this->CI->config->item('razorpay_key_secret')
            );

            $razorpayRefund = $api->payment
                ->fetch($payment['payment_id'])
                ->refund(['amount' => (int) round($refund['amount'] * 100)]);

            return ['success' => true, 'refund_id' => $razorpayRefund->id];

        } catch (Exception $e) {
            // If already refunded, treat as success
            if (stripos($e->getMessage(), 'already') !== false) {
                return ['success' => true, 'refund_id' => null];
            }

            log_message('error', 'RefundWorker Razorpay error: ' . $e->getMessage());
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    // ---------------------------------------------------------
    // LOGGING & HOUSEKEEPING
    // ---------------------------------------------------------
    private function _logExecution($cron, $status, $processed, $failed, $execTime, $message = null)
    {
        if (!$cron) return;

        $this->CI->db->insert('tb_jr_cron_execution_logs', [
            'cron_job_id'     => $cron->id,
            'cron_name'       => $cron->name,
            'status'          => $status,
            'message'         => $message ?? ($status === 'failed' ? 'Refunds failed' : 'Refund processing completed'),
            'processed_count' => $processed,
            'failed_count'    => $failed,
            'execution_time'  => $execTime,
            'raw_response'    => json_encode([
                'processed' => $processed,
                'failed'    => $failed
            ]),
            'created_at'      => date('Y-m-d H:i:s')
        ]);
    }

    private function _updateLastRun($cronId, $execTime)
    {
        $this->CI->db
            ->where('id', $cronId)
            ->update('tb_jr_cron_jobs', [
                'last_run'            => date('Y-m-d H:i:s'),
                'last_execution_time' => $execTime
            ]);
    }

    private function _isLocked($lockFile)
    {
        $ttl = 1800; // 30 minutes
        return file_exists($lockFile) && (time() - filemtime($lockFile)) < $ttl;
    }

    private function _unlock($lockFile)
    {
        if (file_exists($lockFile)) {
            @unlink($lockFile);
        }
    }
}