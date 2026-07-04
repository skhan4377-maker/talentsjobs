<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class PurchasePlan_model extends CI_Model {

    /* ===================== LIST ===================== */
    public function get_all_purchase_plans($filter = []) {

        $this->db->select('
            up.*,
            c.name AS first_name,
            c.email,
            f.feature_name,
            f.feature_tag,
            fp.duration,
            fp.plan_level,
            fp.plan_total,
            fp.plan_mrp,
            p.payment_id,
            p.status AS payment_status,
            p.created_at AS payment_date,
            p.order_id AS razorpay_order_id
        ');

        $this->db->from('tb_ft_user_purchases up'); // ← fixed table name
        $this->db->join('tb_candidate c', 'c.candidate_id = up.user_id', 'left');
        $this->db->join('tb_ft_features f', 'f.feature_id = up.feature_id', 'left');
        $this->db->join('tb_ft_plans fp', 'fp.duration_id = up.plan_id', 'left'); // ← fixed table
        $this->db->join('tb_ft_payments p', 'p.id = up.payment_id', 'left'); // payments table is tb_ft_payments

        /* ---------- Filters ---------- */
        if (!empty($filter['status'])) {
            $this->db->where('up.status', $filter['status']);
        }

        if (!empty($filter['feature_id'])) {
            $this->db->where('up.feature_id', $filter['feature_id']);
        }

        if (!empty($filter['date_from'])) {
            $this->db->where('up.created_at >=', $filter['date_from'].' 00:00:00');
        }

        if (!empty($filter['date_to'])) {
            $this->db->where('up.created_at <=', $filter['date_to'].' 23:59:59');
        }

        if (!empty($filter['search'])) {
            $this->db->group_start();
            $this->db->like('c.name', $filter['search']);
            $this->db->or_like('c.email', $filter['search']);
            $this->db->or_like('p.payment_id', $filter['search']);
            $this->db->or_like('p.order_id', $filter['search']);
            $this->db->or_like('f.feature_name', $filter['search']);
            $this->db->group_end();
        }

        // Also allow filtering by refund status (joins payment's refund_status)
        if (!empty($filter['refund_status'])) {
            $this->db->where('p.refund_status', $filter['refund_status']);
        }

        $this->db->order_by('up.created_at', 'DESC');

        return $this->db->get()->result_array();
    }
    
    /* ===================== FETCH PLAN BY ORDER ID ===================== */
    public function get_plan_by_order_id($order_id) {

        if (empty($order_id)) {
            return null;
        }

        $this->db->select('
            up.*,
            f.feature_name,
            fp.plan_level,
            fp.duration,
            p.payment_id,
            p.order_id,
            p.amount,
            p.status AS payment_status
        ');

        $this->db->from('tb_ft_user_purchases up');
        $this->db->join('tb_ft_payments p', 'p.id = up.payment_id');
        $this->db->join('tb_ft_features f', 'f.feature_id = up.feature_id', 'left');
        $this->db->join('tb_ft_plans fp', 'fp.duration_id = up.plan_id', 'left');
        $this->db->where('p.order_id', $order_id);

        return $this->db->get()->row_array();
    }
    
    /* ===================== SINGLE ===================== */
    public function get_purchase_plan_by_id($id)
    {
        return $this->db
            ->select('
                up.*,
                up.payment_id,
                f.feature_name,
                fp.plan_level,
                fp.duration,
                fp.experience_range
            ')
            ->from('tb_ft_user_purchases up')
            ->join('tb_ft_features f', 'f.feature_id = up.feature_id', 'left')
            ->join('tb_ft_plans fp', 'fp.duration_id = up.plan_id', 'left')
            ->where('up.id', (int)$id)
            ->get()
            ->row_array();
    }

    /* ===================== PAYMENT ===================== */
    public function get_payment_details($payment_id) {
        if (empty($payment_id)) {
            return null;
        }

        return $this->db
            ->where('id', $payment_id)
            ->get('tb_ft_payments')
            ->row_array();
    }

    /* ===================== UPDATE ===================== */
    public function update_plan_status($id, $status, $notes = '') {

        $data = [
            'status'     => $status,
            'updated_at' => date('Y-m-d H:i:s')
        ];

        $this->db->where('id', $id);
        return $this->db->update('tb_ft_user_purchases', $data);
    }

    public function update_plan_by_order_id($order_id, $data) {

        $this->db->select('up.id');
        $this->db->from('tb_ft_user_purchases up');
        $this->db->join('tb_ft_payments p', 'p.id = up.payment_id');
        $this->db->where('p.order_id', $order_id);

        $plan = $this->db->get()->row_array();

        if (!$plan) {
            return false;
        }

        $this->db->where('id', $plan['id']);
        return $this->db->update('tb_ft_user_purchases', $data);
    }

    /* ===================== STATS ===================== */
    public function get_purchase_statistics() {

        $stats = [];

        $stats['total_purchases'] =
            $this->db->count_all('tb_ft_user_purchases');

        $stats['active_plans'] =
            $this->db->where('status', 'active')
                     ->where('end_date >=', date('Y-m-d'))
                     ->count_all_results('tb_ft_user_purchases');

        $stats['expired_plans'] =
            $this->db->where('status', 'active')
                     ->where('end_date <', date('Y-m-d'))
                     ->count_all_results('tb_ft_user_purchases');

        // Total revenue (paid only)
        $this->db->select('SUM(p.amount) AS amount', false);
        $this->db->from('tb_ft_user_purchases up');
        $this->db->join('tb_ft_payments p', 'p.id = up.payment_id');
        $this->db->where('p.status', 'paid');
        $row = $this->db->get()->row_array();

        $stats['total_revenue'] = $row['amount'] ?? 0;

        // Monthly revenue
        $month = date('Y-m');

        $this->db->select('SUM(p.amount) AS amount', false);
        $this->db->from('tb_ft_user_purchases up');
        $this->db->join('tb_ft_payments p', 'p.id = up.payment_id');
        $this->db->where('p.status', 'paid');
        $this->db->like('p.created_at', $month);
        $row = $this->db->get()->row_array();

        $stats['monthly_revenue'] = $row['amount'] ?? 0;

        return $stats;
    }

 
    public function get_feature_popularity() {

        $this->db->select('
            f.feature_name,
            COUNT(up.id) AS purchase_count,
            SUM(p.amount) AS total_revenue
        ', false);

        $this->db->from('tb_ft_user_purchases up');
        $this->db->join('tb_ft_features f', 'f.feature_id = up.feature_id');
        $this->db->join(
            'tb_ft_payments p',
            'p.id = up.payment_id AND p.status = "paid"',
            'left'
        );

        $this->db->group_by('up.feature_id');
        $this->db->order_by('purchase_count', 'DESC');

        return $this->db->get()->result_array();
    }

    public function get_monthly_trends() {

        $this->db->select('
            DATE_FORMAT(p.created_at, "%Y-%m") AS month,
            COUNT(up.id) AS total_plans,
            SUM(p.amount) AS total_revenue
        ', false);

        $this->db->from('tb_ft_user_purchases up');
        $this->db->join('tb_ft_payments p', 'p.id = up.payment_id');
        $this->db->where('p.status', 'paid');
        $this->db->group_by('month');
        $this->db->order_by('month', 'DESC');
        $this->db->limit(12);

        return $this->db->get()->result_array();
    }

    public function get_user_type_stats() {

        $this->db->select('
            c.role,
            COUNT(up.id) AS total_plans,
            SUM(p.amount) AS total_revenue
        ', false);

        $this->db->from('tb_ft_user_purchases up');
        $this->db->join('tb_candidate c', 'c.candidate_id = up.user_id');
        $this->db->join(
            'tb_ft_payments p',
            'p.id = up.payment_id AND p.status = "paid"',
            'left'
        );

        $this->db->group_by('c.role');

        return $this->db->get()->result_array();
    }

    /* ===================== FEATURES ===================== */
    public function get_all_features() {
        return $this->db
            ->where('is_active', 'active')
            ->get('tb_ft_features')
            ->result_array();
    }
}