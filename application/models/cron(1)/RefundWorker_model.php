<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * RefundWorker
 *
 * Cron-executed worker
 * - Creates refund request in Razorpay
 * - Does NOT finalize refund (handled by webhook)
 * - Safe for retries
 * - Global + row-level lock protected
 */
class RefundWorker_model extends CI_Model {

    public function __construct() {
        parent::__construct();

        $this->load->database();
        $this->load->model('admin/Refund_model');
        $this->load->model('admin/Payment_model');
        $this->load->config('razorpay');
    }

    /**
     * Entry point called by CronManager
     */
    public function process($config = []) {

        // -----------------------------
        // GLOBAL LOCK (CRON LEVEL)
        // -----------------------------
        $lockFile = sys_get_temp_dir() . '/refund_worker.lock';

        if ($this->_is_locked($lockFile)) {
            return [
                'status' => 'skipped',
                'emails_sent' => 0,
                'display_result' => 'Skipped - RefundWorker already running'
            ];
        }

        file_put_contents($lockFile, time());

        try {

            $limit            = (int)($config['emails_per_run'] ?? 5);
            $executionLogId   = $config['execution_log_id'] ?? null;

            // -----------------------------
            // FETCH APPROVED REFUNDS
            // -----------------------------
            $refunds = $this->db
                ->where('status', 'approved')
                ->order_by('id', 'ASC')
                ->limit($limit)
                ->get('tb_refund_requests')
                ->result_array();

            if (empty($refunds)) {
                return [
                    'status' => 'no_action',
                    'emails_sent' => 0,
                    'display_result' => 'No approved refunds found'
                ];
            }

            $processed = 0;
            $failed    = 0;

            foreach ($refunds as $refund) {

                // -----------------------------
                // ROW-LEVEL IDEMPOTENT LOCK
                // -----------------------------
                if (!$this->Refund_model->lock_refund_for_processing($refund['id'])) {
                    continue;
                }

                $result = $this->_create_razorpay_refund($refund);

                if ($result['success']) {

                    $this->Refund_model->update_refund_status(
                        $refund['id'],
                        'processing',
                        [
                            'razorpay_refund_id' => $result['refund_id'],
                            'gateway_status'     => 'pending'
                        ]
                    );

                    // Optional observability
                    if ($executionLogId) {
                        log_message(
                            'info',
                            "RefundWorker: Refund {$refund['id']} initiated (execution_log_id={$executionLogId})"
                        );
                    }

                    $processed++;

                } else {

                    $this->Refund_model->finalize_refund(
                        $refund['id'],
                        false,
                        null,
                        $result['message']
                    );

                    if ($executionLogId) {
                        log_message(
                            'error',
                            "RefundWorker: Refund {$refund['id']} failed - {$result['message']} (execution_log_id={$executionLogId})"
                        );
                    }

                    $failed++;
                }
            }

            // -----------------------------
            // FINAL STATUS (CRON SAFE)
            // -----------------------------
            return [
                'status' => $processed > 0
                    ? ($failed > 0 ? 'partial' : 'success')
                    : ($failed > 0 ? 'error' : 'no_action'),
                'emails_sent' => 0,
                'display_result' =>
                    "Refunds processed: {$processed}\n" .
                    "Refunds failed: {$failed}"
            ];

        } finally {
            $this->_unlock($lockFile);
        }
    }

    /**
     * Create refund in Razorpay
     */
    private function _create_razorpay_refund($refund) {

        $payment = $this->Payment_model
            ->get_payment_by_order_id($refund['order_id']);

        if (!$payment || empty($payment['payment_id'])) {
            return [
                'success' => false,
                'message' => 'Payment not found for order'
            ];
        }

        try {

            $api = new \Razorpay\Api\Api(
                $this->config->item('razorpay_key_id'),
                $this->config->item('razorpay_key_secret')
            );

            $razorpayRefund = $api->payment
                ->fetch($payment['payment_id'])
                ->refund([
                    'amount' => (int) round($refund['amount'] * 100)
                ]);

            return [
                'success'   => true,
                'refund_id' => $razorpayRefund->id
            ];

        } catch (Exception $e) {

            // Graceful handling: refund already exists
            if (stripos($e->getMessage(), 'already') !== false) {
                return [
                    'success'   => true,
                    'refund_id' => null
                ];
            }

            log_message(
                'error',
                'RefundWorker Razorpay error: ' . $e->getMessage()
            );

            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * GLOBAL LOCK HELPERS
     */
    private function _is_locked($lockFile) {
        $lockTtl = 1800; // 30 minutes (crash-safe)
        return file_exists($lockFile) && (time() - filemtime($lockFile)) < $lockTtl;
    }

    private function _unlock($lockFile) {
        if (file_exists($lockFile)) {
            unlink($lockFile);
        }
    }
}
