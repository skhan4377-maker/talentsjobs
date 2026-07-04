<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Refund_model extends CI_Model {
    
    /**
     * SAFE refund finder (handles retries, partials, duplicates)
     */
     
    public function find_refund($refund_id = null, $payment_id = null, $order_id = null)
    {
        if ($refund_id) {
            $row = $this->db
                ->where('razorpay_refund_id', $refund_id)
                ->get('tb_ft_refund_requests')
                ->row_array();
            if ($row) return $row;
        }

        if ($payment_id) {
            $row = $this->db
                ->where('razorpay_payment_id', $payment_id)
                ->order_by('id', 'DESC')
                ->get('tb_ft_refund_requests')
                ->row_array();
            if ($row) return $row;
        }

        if ($order_id) {
            return $this->db
                ->where('order_id', $order_id)
                ->order_by('id', 'DESC')
                ->get('tb_ft_refund_requests')
                ->row_array();
        }

        return null;
    }

    /**
     * Lock refund row for worker (idempotent)
     */
    public function lock_refund_for_processing($refund_id) {

        $token = bin2hex(random_bytes(32));

        $this->db->where('id', $refund_id);
        $this->db->where('status', 'approved');
        $this->db->where('processing_token IS NULL', null, false);

        $this->db->update('tb_ft_refund_requests', [
            'status'           => 'processing',
            'processing_token' => $token
        ]);

        return ($this->db->affected_rows() === 1) ? $token : false;
    }

    /**
     * FINAL state update (only webhook / worker failure)
     */
    public function finalize_refund(
        $refund_id,
        $success,
        $razorpay_refund_id = null,
        $failure_reason = null
    ) {

        $data = [
            'status'         => $success ? 'processed' : 'failed',
            'gateway_status' => $success ? 'success' : 'failed',
            'failure_reason' => $failure_reason,
            'processed_at'   => date('Y-m-d H:i:s')
        ];

        // Razorpay refund id sirf pehli baar set ho
        if ($razorpay_refund_id) {
            $data['razorpay_refund_id'] = $razorpay_refund_id;
        }

        $this->db->where('id', $refund_id);
        return $this->db->update('tb_ft_refund_requests', $data);
    }

    public function get_by_razorpay_refund_id($refund_id) {
        return $this->db
            ->where('razorpay_refund_id', $refund_id)
            ->get('tb_ft_refund_requests')
            ->row_array();
    }

    /**
     * Audit trail
     */
    public function log_action($refund_id, $old, $new, $note = '') {

        $this->db->insert('tb_ft_refund_logs', [
            'refund_id' => $refund_id,
            'admin_id'  => $this->session->user_id ?? 0,
            'old_status'=> $old,
            'new_status'=> $new,
            'note'      => $note,
            'ip_address'=> $this->input->ip_address()
        ]);
    }

    public function get_all_refund_requests($filter = []) {

        $this->db->select('
            rr.*,
            c.name,
            c.email,
            p.payment_id,
            p.amount AS payment_amount,
            up.feature_id
        ');

        $this->db->from('tb_ft_refund_requests rr');
        $this->db->join('tb_candidate c', 'c.candidate_id = rr.user_id', 'left');

        $this->db->join(
            'tb_ft_payments p',
            'p.order_id COLLATE utf8mb4_unicode_ci = rr.order_id COLLATE utf8mb4_unicode_ci',
            'left',
            false
        );

        $this->db->join('tb_ft_user_purchases up', 'up.payment_id = p.id', 'left');

        if (!empty($filter['status'])) {
            $this->db->where('rr.status', $filter['status']);
        }

        if (!empty($filter['date_from'])) {
            $this->db->where('rr.requested_at >=', $filter['date_from'].' 00:00:00');
        }

        if (!empty($filter['date_to'])) {
            $this->db->where('rr.requested_at <=', $filter['date_to'].' 23:59:59');
        }

        if (!empty($filter['search'])) {
            $this->db->group_start()
                ->like('c.name', $filter['search'])
                ->or_like('c.email', $filter['search'])
                ->or_like('rr.order_id', $filter['search'])
                ->or_like('p.payment_id', $filter['search'])
            ->group_end();
        }

        $this->db->order_by('rr.requested_at', 'DESC');
        return $this->db->get()->result_array();
    }

    public function get_refund_request_by_id($id) {
        return $this->db->where('id', $id)
            ->get('tb_ft_refund_requests')
            ->row_array();
    }

    public function get_refund_request_by_order_id($order_id) {
        return $this->db->where('order_id', $order_id)
            ->get('tb_ft_refund_requests')
            ->row_array();
    }

    /**
     * Non-final state update (admin / worker only)
     */
    public function update_refund_status($id, $status, $extra = []) {

        if (!$id || !$status) return false;

        $data = ['status' => $status];

        if (in_array($status, ['rejected'])) {
            $data['processed_at'] = date('Y-m-d H:i:s');
        }

        foreach ($extra as $k => $v) {
            if ($v !== null) $data[$k] = $v;
        }

        $this->db->where('id', (int)$id);
        return $this->db->update('tb_ft_refund_requests', $data);
    }

    public function get_refund_statistics() {

        $stats['total_requests'] =
            $this->db->count_all('tb_ft_refund_requests');

        $stats['pending'] =
            $this->db->where('status', 'pending')
                ->count_all_results('tb_ft_refund_requests');

        $stats['approved'] =
            $this->db->where('status', 'approved')
                ->count_all_results('tb_ft_refund_requests');

        $stats['rejected'] =
            $this->db->where('status', 'rejected')
                ->count_all_results('tb_ft_refund_requests');

        $this->db->select_sum('amount');
        $this->db->where('status', 'processed');
        $row = $this->db->get('tb_ft_refund_requests')->row_array();

        $stats['total_refunded'] = $row['amount'] ?? 0;

        return $stats;
    }
}