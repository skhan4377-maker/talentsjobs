<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class RazorpayWebhook extends CI_Controller {

    public function __construct() {
        parent::__construct();

        $this->load->model('admin/Refund_model');
        $this->load->model('admin/Payment_model');
        $this->load->model('admin/PurchasePlan_model');
        $this->load->config('razorpay');
    }

    /**
     * ===================== REFUND WEBHOOK =====================
     * Razorpay is FINAL source of truth
     */
    public function refund()
    {
        // 1️⃣ Read raw body + signature
        $payload   = file_get_contents('php://input');
        $signature = $_SERVER['HTTP_X_RAZORPAY_SIGNATURE'] ?? '';

        if (empty($payload) || empty($signature)) {
            return $this->respond_ok();
        }

        // 2️⃣ Verify signature
        if (!$this->verify_signature($payload, $signature)) {
            log_message('error', 'Razorpay webhook: invalid signature');
            return $this->respond_ok();
        }

        // 3️⃣ Decode JSON
        $data = json_decode($payload, true);
        if (empty($data['event'])) {
            return $this->respond_ok();
        }

        // 4️⃣ Only refund events
        if (!in_array($data['event'], ['refund.processed', 'refund.failed'], true)) {
            return $this->respond_ok();
        }

        // 5️⃣ Correct payload mapping (IMPORTANT)
        $refundEntity  = $data['payload']['refund']['entity']  ?? [];
        $paymentEntity = $data['payload']['payment']['entity'] ?? [];

        $razorpay_refund_id = $refundEntity['id'] ?? null;
        $razorpay_payment_id= $refundEntity['payment_id'] ?? null;
        $order_id           = $paymentEntity['order_id'] ?? null;
        $refund_amount      = ($refundEntity['amount'] ?? 0) / 100; // paise → rupees

        if (!$razorpay_refund_id) {
            log_message('error', 'Webhook refund id missing');
            return $this->respond_ok();
        }

        // 6️⃣ Find refund request (SAFE FALLBACKS)
        $refund = $this->Refund_model->find_refund(
            $razorpay_refund_id,
            $razorpay_payment_id,
            $order_id
        );

        if (!$refund) {
            log_message('error', 'Refund request not found: '.$razorpay_refund_id);
            return $this->respond_ok();
        }

        // 7️⃣ Idempotency (final state check)
        if ($refund['gateway_status'] === 'success' || $refund['gateway_status'] === 'failed') {
            return $this->respond_ok();
        }

        $old_status = $refund['status'];

        // 8️⃣ DB transaction
        $this->db->trans_begin();

        /* ===================== SUCCESS ===================== */
        if ($data['event'] === 'refund.processed') {

            $this->Refund_model->finalize_refund(
                $refund['id'],
                true,
                $razorpay_refund_id
            );

            $this->Payment_model->update_payment_refund_status(
                $order_id,
                'processed',
                $refund_amount
            );

            $this->PurchasePlan_model->update_plan_by_order_id(
                $order_id,
                ['status' => 'refunded']
            );

            $this->Refund_model->log_action(
                $refund['id'],
                $old_status,
                'processed',
                'Refund confirmed via Razorpay webhook'
            );
        }

        /* ===================== FAILED ===================== */
        if ($data['event'] === 'refund.failed') {

            $reason = $refundEntity['error_reason'] ?? 'Gateway failure';

            $this->Refund_model->finalize_refund(
                $refund['id'],
                false,
                $razorpay_refund_id,
                $reason
            );

            $this->Refund_model->log_action(
                $refund['id'],
                $old_status,
                'failed',
                'Refund failed via Razorpay webhook'
            );
        }

        // 9️⃣ Commit / rollback
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            log_message('error', 'Refund webhook DB transaction failed');
            return $this->respond_ok();
        }

        $this->db->trans_commit();
        return $this->respond_ok();
    }

    /* ===================== SIGNATURE ===================== */
    private function verify_signature($payload, $signature)
    {
        $secret = $this->config->item('razorpay_webhook_secret');
        if (!$secret) return false;

        $expected = hash_hmac('sha256', $payload, $secret);
        return hash_equals($expected, $signature);
    }

    private function respond_ok()
    {
        http_response_code(200);
        echo 'OK';
        exit;
    }
}
