<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class AdminPurchasePlans extends MY_Controller {

     public function __construct() {
        parent::__construct();

        // ✅ CORRECT loader usage
        $this->load->model('admin/PurchasePlan_model');
        $this->load->model('admin/Refund_model');
        $this->load->model('admin/Payment_model');

        $this->load->helper('url');
    }

    /* =========================================================
     * LIST
     * ========================================================= */
    public function index() {

        $data['title'] = 'Manage Purchase Plans';

        $filter = [
            'status'     => $this->input->get('status'),
            'feature_id' => $this->input->get('feature_id'),
            'date_from'  => $this->input->get('date_from'),
            'date_to'    => $this->input->get('date_to'),
            'search'     => $this->input->get('search')
        ];

        $data['plans'] = $this->PurchasePlan_model
            ->get_all_purchase_plans($filter);

        $this->load->model('admin/Features_model');
        $data['features'] = $this->Features_model->get_all_features();

        $data['stats'] = $this->PurchasePlan_model
            ->get_purchase_statistics();

        $data['content'] = $this->load->view(
            'admin/purchase_plans/index',
            $data,
            true
        );

        $this->load->view('templates/master', $data);
    }

    /* =========================================================
     * VIEW
     * ========================================================= */
    public function view($id) {

        $data['title'] = 'Purchase Plan Details';

        /* ---------- PLAN ---------- */
        $data['plan'] = $this->PurchasePlan_model
            ->get_purchase_plan_by_id($id);

        if (!$data['plan']) {
            $this->session->set_flashdata('error', 'Purchase plan not found');
            redirect('admin/purchase-plans');
        }

        /* ---------- USER ---------- */
        $this->db->select('candidate_id, name, email, mobile');
        $this->db->from('tb_candidate');
        $this->db->where('candidate_id', $data['plan']['user_id']);
        $data['user'] = $this->db->get()->row_array();

        /* ---------- PAYMENT ---------- */
        $data['payment'] = null;

        if (!empty($data['plan']['payment_id'])) {
            $data['payment'] =
                $this->Payment_model
                    ->get_payment_by_id($data['plan']['payment_id']);
        }

        /* ---------- REFUND REQUEST (SAFE) ---------- */
        $data['refund_request'] = null;

        if (!empty($data['payment']['order_id'])) {
            $data['refund_request'] =
                $this->Refund_model
                    ->get_refund_request_by_order_id(
                        $data['payment']['order_id']
                    );
        }

        $data['content'] = $this->load->view(
            'admin/purchase_plans/view',
            $data,
            true
        );

        $this->load->view('templates/master', $data);
    }

    /* =========================================================
     * UPDATE PLAN STATUS (AJAX SAFE)
     * ========================================================= */
    public function update_status() {
		if (!$this->input->is_ajax_request()) {
			show_404();
		}

		// 🔧 FIX HERE
		$id     = (int) $this->input->post('plan_id');
		$status = $this->input->post('status');
		$notes  = $this->input->post('notes');

		$plan = $this->PurchasePlan_model
			->get_purchase_plan_by_id($id);

		if (!$plan) {
			echo json_encode([
				'success' => false,
				'message' => 'Plan not found'
			]);
			return;
		}

		if ($plan['status'] === 'refunded') {
			echo json_encode([
				'success' => false,
				'message' => 'Refunded plans cannot be modified'
			]);
			return;
		}

		$allowed = ['active', 'expired', 'cancelled'];
		if (!in_array($status, $allowed)) {
			echo json_encode([
				'success' => false,
				'message' => 'Invalid status'
			]);
			return;
		}

		$updated = $this->PurchasePlan_model
			->update_plan_status($id, $status, $notes);

		echo json_encode([
			'success' => (bool)$updated,
			'message' => $updated
				? 'Status updated successfully'
				: 'Update failed'
		]);
	}


    /* =========================================================
     * EXPORT CSV
     * ========================================================= */
    public function export() {

        $filter = [
            'status'    => $this->input->get('status'),
            'date_from'=> $this->input->get('date_from'),
            'date_to'  => $this->input->get('date_to')
        ];

        $plans = $this->PurchasePlan_model
            ->get_all_purchase_plans($filter);

        header('Content-Type: text/csv');
        header(
            'Content-Disposition: attachment; filename="purchase-plans-'
            . date('Y-m-d') . '.csv"'
        );

        $out = fopen('php://output', 'w');

        fputcsv($out, [
            'ID','User','Email','Feature','Plan Level','Duration',
            'Amount','Payment ID','Status','Purchase Date'
        ]);

        foreach ($plans as $plan) {
            fputcsv($out, [
                $plan['id'],
                $plan['first_name'],
                $plan['email'],
                $plan['feature_name'],
                $plan['plan_level'],
                $plan['duration'],
                $plan['plan_total'],
                $plan['payment_id'] ?? 'N/A',
                ucfirst($plan['status']),
                $plan['created_at']
            ]);
        }

        fclose($out);
        exit;
    }

    /* =========================================================
     * ANALYTICS (REFUND SAFE)
     * ========================================================= */
    public function analytics() {

        $data['title'] = 'Purchase Analytics';

        $data['revenue_stats'] = [
            'total_revenue'    => $this->get_total_revenue(),
            'total_purchases' => $this->db->count_all('tb_user_plans'),
            'months'           => [],
            'revenue'          => []
        ];

        for ($i = 5; $i >= 0; $i--) {

            $month = date('Y-m', strtotime("-{$i} months"));

            $this->db->select('SUM(p.amount) AS amount', false);
            $this->db->from('tb_user_plans up');
            $this->db->join('tb_payments p', 'p.id = up.payment_id');
            $this->db->where('p.status', 'paid');
            $this->db->where_in('p.refund_status', ['none', NULL]);
            $this->db->like('p.created_at', $month);

            $row = $this->db->get()->row_array();

            $data['revenue_stats']['months'][] =
                date('M Y', strtotime($month));
            $data['revenue_stats']['revenue'][] =
                $row['amount'] ?? 0;
        }

        $data['content'] = $this->load->view(
            'admin/purchase_plans/analytics',
            $data,
            true
        );

        $this->load->view('templates/master', $data);
    }

    /* =========================================================
     * TOTAL REVENUE (REFUND SAFE)
     * ========================================================= */
    private function get_total_revenue() {

        $this->db->select('SUM(amount) AS amount', false);
        $this->db->from('tb_payments');
        $this->db->where('status', 'paid');
        $this->db->where_in('refund_status', ['none', NULL]);

        $row = $this->db->get()->row_array();
        return $row['amount'] ?? 0;
    }
}
