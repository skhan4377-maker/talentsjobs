<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Candidate_plans extends MY_Controller {

    public function __construct() {
		parent::__construct();
		$this->load->database();
		$this->load->model('candidate/Candidate_plan_model');
	}

    public function index() {
        $candidate_id = $this->session->userdata('user_id');
        $resume_token = $this->session->userdata('resume_token'); // Get token from session

        $plans = $this->Candidate_plan_model->get_candidate_plans($candidate_id);

        $now = new DateTime();
        $near_expiry_days = 7;

        foreach ($plans as &$plan) {
            $start = new DateTime($plan['start_date']);
            $end = new DateTime($plan['end_date']);

            // Status determination (same as before)
            if ($plan['plan_status'] == 'cancelled') {
                $plan['status_display'] = 'Cancelled';
                $plan['status_badge'] = 'bg-gray-200 text-gray-800';
            } elseif ($plan['plan_status'] == 'expired' || $end < $now) {
                $plan['status_display'] = 'Expired';
                $plan['status_badge'] = 'bg-red-100 text-red-800';
            } elseif ($start > $now) {
                $plan['status_display'] = 'Upcoming';
                $plan['status_badge'] = 'bg-blue-100 text-blue-800';
            } elseif ($end >= $now) {
                $interval = $now->diff($end);
                $days_left = $interval->days;
               if ($days_left <= $near_expiry_days) {
					$plan['status_display'] = 'Expiring Soon';
					$plan['status_badge'] = 'bg-amber-100 text-amber-800';
					$plan['days_left'] = $days_left;
					$plan['hours_left'] = floor(($end->getTimestamp() - time()) / 3600);
					
					// ⬇️ YE LINE ADD KARO
					$plan['expiry_timestamp'] = $end->getTimestamp() * 1000;  // milliseconds for JS
				} else {
					$plan['status_display'] = 'Active';
					$plan['status_badge'] = 'bg-green-100 text-green-800';

					$plan['days_left'] = $days_left;
				}
            } else {
                $plan['status_display'] = ucfirst($plan['plan_status']);
                $plan['status_badge'] = 'bg-gray-100 text-gray-800';
            }

            // Format dates
            $plan['start_date_formatted'] = date('d M Y', strtotime($plan['start_date']));
            $plan['end_date_formatted'] = date('d M Y', strtotime($plan['end_date']));
            $plan['purchase_date_formatted'] = date('d M Y', strtotime($plan['purchase_date']));
            $plan['payment_date_formatted'] = $plan['payment_date'] ? date('d M Y', strtotime($plan['payment_date'])) : 'N/A';

            // Build invoice link
            $plan['invoice_link'] = $plan['invoice_no'] ? site_url('candidate/invoice/' . $plan['invoice_no']) : '#';
        }

        $data['plans'] = $plans;
        $data['resume_token'] = $resume_token; // Pass token to view
        $data['title'] = 'My Plans';      
        $data['content'] = $this->load->view('candidate/candidate_plans_view', $data, TRUE);
		$this->load->view('templates/master', $data);
    }
	
	/**
	 * Display invoice for a given invoice number
	 * @param string $invoice_no
	 */
	public function invoice($invoice_no) {
		// Ensure user is logged in
		$candidate_id = $this->session->userdata('user_id');		

		// Fetch invoice details
		$invoice = $this->Candidate_plan_model->get_invoice_by_invoice_no($invoice_no, $candidate_id);
		if (!$invoice) {
			show_404(); // Invoice not found or not owned by this candidate
		}

		// Prepare data for view
		$data['invoice'] = $invoice;
		$data['title'] = 'Invoice #' . htmlspecialchars($invoice_no);
		$data['company'] = $this->get_company_details(); // Helper to get company info

		// Load view
		$data['content'] = $this->load->view('candidate/invoice_view', $data, TRUE);
		$this->load->view('templates/master', $data);
	}
	
	 /**
     * एक्टिव प्लान के लिए रिफंड रिक्वेस्ट (24 घंटे के अंदर)
     * POST: candidate/request_refund
     */
	 public function request_refund() {
        $user_id = $this->session->userdata('user_id');
        if (!$user_id) {
            echo json_encode(['status' => false, 'message' => 'Please login first']);
            return;
        }

        $order_id = trim($this->input->post('order_id'));
        $reason   = trim($this->input->post('reason')) ?: 'Refund requested by user';

        if (!$order_id) {
            echo json_encode(['status' => false, 'message' => 'Order ID is required']);
            return;
        }

        // डिबग लॉग (जरूरत न हो तो हटाएँ)
        log_message('debug', 'Refund request - user_id: ' . $user_id . ' | order_id: ' . $order_id);

        $this->db->trans_begin();

        $purchase = $this->db->query("
            SELECT
                up.id        AS user_purchase_id,
                up.status    AS purchase_status,
                p.id         AS payment_id,
                p.payment_id AS razorpay_payment_id,
                p.amount,
                p.status     AS payment_status,
                p.created_at AS payment_time
            FROM tb_ft_user_purchases up
            INNER JOIN tb_ft_payments p ON p.id = up.payment_id
            WHERE up.user_id = ?
              AND p.user_id = ?
              AND p.order_id = ?
              AND up.status = 'active'
              AND p.status = 'paid'
            LIMIT 1
        ", [$user_id, $user_id, $order_id])->row();

        // डिबग: क्वेरी का रिजल्ट देखें
        log_message('debug', 'Purchase found: ' . ($purchase ? 'Yes' : 'No'));
        if ($purchase) {
            log_message('debug', 'Purchase data: ' . json_encode($purchase));
        }

        if (!$purchase) {
            $this->db->trans_rollback();
            echo json_encode(['status' => false, 'message' => 'Refund allowed only for ACTIVE paid plans']);
            return;
        }

        $exists = $this->db->get_where('tb_ft_refund_requests', [
            'user_id'  => $user_id,
            'order_id' => $order_id
        ])->row();

        if ($exists) {
            $this->db->trans_rollback();
            echo json_encode(['status' => false, 'message' => 'Refund request already submitted']);
            return;
        }

        if ((time() - strtotime($purchase->payment_time)) > (24 * 60 * 60)) {
            $this->db->trans_rollback();
            echo json_encode(['status' => false, 'message' => 'Refund window expired (24 hours)']);
            return;
        }

        $this->db->insert('tb_ft_refund_requests', [
            'user_id'             => $user_id,
            'order_id'            => $order_id,
            'razorpay_payment_id' => $purchase->razorpay_payment_id,
            'amount'              => $purchase->amount,
            'reason'              => $reason,
            'status'              => 'pending',
            'requested_at'        => date('Y-m-d H:i:s'),
            'created_ip'          => $this->input->ip_address()
        ]);

        $this->db->where('id', $purchase->payment_id)
                 ->update('tb_ft_payments', ['refund_status' => 'requested']);

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            echo json_encode(['status' => false, 'message' => 'Failed to submit refund request']);
            return;
        }

        $this->db->trans_commit();

        echo json_encode([
            'status'  => true,
            'message' => 'Refund request submitted. Awaiting admin approval.',
            'data'    => ['refund_amount' => $purchase->amount]
        ]);
    }

    /**
     * अपकमिंग प्लान कैंसल करके रिफंड रिक्वेस्ट करें
     * POST: candidate/cancel_upcoming
     */
    public function cancel_upcoming() {
		$user_id = $this->session->userdata('user_id');
		if (!$user_id) {
			echo json_encode(['status' => false, 'message' => 'Please login first']);
			return;
		}

		$order_id = trim($this->input->post('order_id'));
		if (!$order_id) {
			echo json_encode(['status' => false, 'message' => 'Order ID is required']);
			return;
		}

		$this->db->trans_begin();

		$purchase = $this->db->query("
			SELECT
				up.id AS user_purchase_id,
				p.id  AS payment_id,
				p.payment_id AS razorpay_payment_id,
				p.amount,
				p.status AS payment_status
			FROM tb_ft_user_purchases up
			INNER JOIN tb_ft_payments p ON p.id = up.payment_id
			WHERE up.user_id = ?
			  AND p.user_id = ?
			  AND p.order_id = ?
			  AND up.status = 'upcoming'
			  AND p.status = 'paid'
			LIMIT 1
		", [$user_id, $user_id, $order_id])->row();

		if (!$purchase) {
			$this->db->trans_rollback();
			echo json_encode(['status' => false, 'message' => 'No UPCOMING paid plan found']);
			return;
		}

		$this->db->where('id', $purchase->user_purchase_id)
				 ->update('tb_ft_user_purchases', ['status' => 'cancelled', 'updated_at' => date('Y-m-d H:i:s')]);

		$this->db->insert('tb_ft_refund_requests', [
			'user_id'             => $user_id,
			'order_id'            => $order_id,
			'razorpay_payment_id' => $purchase->razorpay_payment_id,   // ← यह भी जोड़ा
			'amount'              => $purchase->amount,
			'reason'              => 'Upcoming plan cancelled by user',
			'status'              => 'pending',
			'requested_at'        => date('Y-m-d H:i:s'),
			'created_ip'          => $this->input->ip_address()
		]);

		$this->db->where('id', $purchase->payment_id)
				 ->update('tb_ft_payments', ['refund_status' => 'requested']);

		if ($this->db->trans_status() === FALSE) {
			$this->db->trans_rollback();
			echo json_encode(['status' => false, 'message' => 'Failed to cancel plan']);
			return;
		}

		$this->db->trans_commit();

		echo json_encode([
			'status'  => true,
			'message' => 'Upcoming plan cancelled. Refund request sent to admin.'
		]);
	}
	
	private function get_company_details() {
		return [
			'name'    => 'Talents Jobs Pvt Ltd',
			'address' => 'Prayagraj, Uttar Pradesh, India',
			'email'   => 'support@talentsjobs.in',
			'phone'   => '+91 9876543210',
			'gstin'   => '22AAAAA0000A1Z5'
		];
	}
}