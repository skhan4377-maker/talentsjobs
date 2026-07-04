<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Payment_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    /**
     * Save payment record and generate invoice number
     */
    public function saveOrder($data) {
        // Generate invoice number using sequence table
        $this->db->insert('tb_ft_invoice_sequence', ['created_at' => date('Y-m-d H:i:s')]);
        $seqId = $this->db->insert_id();
        if (!$seqId) throw new Exception('Failed to generate invoice sequence');

        $invoiceNo = 'INV-' . str_pad($seqId, 6, '0', STR_PAD_LEFT);

        $insertData = [
            'user_id'     => $data['user_id'] ?? null,
            'order_id'    => $data['order_id'],
            'payment_id'  => $data['payment_id'] ?? null,
            'invoice_no'  => $invoiceNo,
            'amount'      => $data['amount'],
            'currency'    => $data['currency'],
            'status'      => $data['status'],
            'method'      => $data['method'] ?? 'Razorpay',
            'created_at'  => date('Y-m-d H:i:s'),
        ];
        $this->db->insert('tb_ft_payments', $insertData);
        $paymentId = $this->db->insert_id();
        if (!$paymentId) throw new Exception('Failed to save payment');

        return [
            'invoice_no' => $invoiceNo,
            'payment_id' => $paymentId
        ];
    }

    /**
     * Activate a user purchase for a specific feature and plan
     * Handles chaining: if user already has an active plan, new plan starts after it ends.
     */
    public function addUserPurchase($user_id, $feature_id, $plan_id, $payment_id, $razorpay_payment_id, $duration_str) {
		$existing = $this->getUserPurchasesByFeature($user_id, $feature_id);
		$duration = strtolower(str_replace(' ', '', $duration_str));
		$current_time = date('Y-m-d H:i:s');

		// Determine start date (chaining)
		if (!empty($existing['active'])) {
			$latest_active = $existing['active'][0];
			$start_date = $latest_active['end_date'];
		} elseif (!empty($existing['upcoming'])) {
			$latest_upcoming = $existing['upcoming'][0];
			$start_date = $latest_upcoming['end_date'];
		} else {
			$start_date = $current_time;
		}

		// Calculate end date
		$start = new DateTime($start_date);
		$end = clone $start;
		switch ($duration) {
			case '1month': $end->modify('+1 month'); break;
			case '2months': $end->modify('+2 months'); break;
			case '3months': $end->modify('+3 months'); break;
			case '6months': $end->modify('+6 months'); break;
			case 'annual': $end->modify('+12 months'); break;
			default: $end->modify('+1 month');
		}

		$status = ($start_date > $current_time) ? 'upcoming' : 'active';

		// Insert into tb_ft_user_purchases
		$purchase_data = [
			'user_id'              => $user_id,
			'feature_id'           => $feature_id,
			'plan_id'              => $plan_id,
			'payment_id'           => $payment_id,
			'razorpay_payment_id'  => $razorpay_payment_id,
			'start_date'           => $start->format('Y-m-d H:i:s'),
			'end_date'             => $end->format('Y-m-d H:i:s'),
			'status'               => $status,
			'created_at'           => $current_time
		];
		$this->db->insert('tb_ft_user_purchases', $purchase_data);
		$purchase_id = $this->db->insert_id();

		// ✅ Update or insert into tb_ft_user_subscriptions
		$existing_sub = $this->db->get_where('tb_ft_user_subscriptions', [
			'candidate_id' => $user_id,
			'feature_id'   => $feature_id
		])->row();

		if ($existing_sub) {
			$this->db->where('subscription_id', $existing_sub->subscription_id);
			$this->db->update('tb_ft_user_subscriptions', [
				'is_active'      => 1,
				'start_date'     => $start->format('Y-m-d H:i:s'),
				'end_date'       => $end->format('Y-m-d H:i:s'),
				'payment_status' => 'paid'
			]);
		} else {
			$this->db->insert('tb_ft_user_subscriptions', [
				'candidate_id'   => $user_id,
				'feature_id'     => $feature_id,
				'is_active'      => 1,
				'start_date'     => $start->format('Y-m-d H:i:s'),
				'end_date'       => $end->format('Y-m-d H:i:s'),
				'payment_status' => 'paid'
			]);
		}

		return $purchase_id;
	}
    /**
     * Get user's purchases for a feature, grouped by status
     */
    public function getUserPurchasesByFeature($user_id, $feature_id) {
        $current_time = date('Y-m-d H:i:s');
        $this->db->select('*');
        $this->db->from('tb_ft_user_purchases');
        $this->db->where('user_id', $user_id);
        $this->db->where('feature_id', $feature_id);
        $this->db->order_by('start_date', 'ASC');
        $purchases = $this->db->get()->result_array();

        $categorized = ['active' => [], 'upcoming' => [], 'expired' => []];
        foreach ($purchases as $p) {
            if ($p['end_date'] < $current_time) {
                $categorized['expired'][] = $p;
            } elseif ($p['start_date'] > $current_time) {
                $categorized['upcoming'][] = $p;
            } else {
                $categorized['active'][] = $p;
            }
        }
        // Sort by end_date descending (latest first)
        foreach (['active', 'upcoming'] as $key) {
            usort($categorized[$key], function($a, $b) {
                return strtotime($b['end_date']) - strtotime($a['end_date']);
            });
        }
        return $categorized;
    }

    /**
     * Get plan duration string from tb_ft_plans
     */
    public function getPlanDuration($feature_id, $plan_id) {
        $this->db->select('duration');
        $this->db->from('tb_ft_plans');
        $this->db->where('feature_id', $feature_id);
        $this->db->where('duration_id', $plan_id);
        return $this->db->get()->row();
    }

    /**
     * Get full plan details for email (including feature name and description)
     */
    public function getPlanDetails($feature_id, $plan_id) {
        $this->db->select('p.*, f.feature_name, f.feature_full_description');
        $this->db->from('tb_ft_plans p');
        $this->db->join('tb_ft_features f', 'f.feature_id = p.feature_id', 'left');
        $this->db->where('p.feature_id', $feature_id);
        $this->db->where('p.duration_id', $plan_id);
        $result = $this->db->get()->row();
        if ($result) {
            $plan_name = $result->feature_name;
            if ($result->plan_level && $result->plan_level != 'All Level') {
                $plan_name .= ' - ' . $result->plan_level;
            }
            $result->plan_name = $plan_name;
        }
        return $result;
    }

    /**
     * Check if a payment already exists (by razorpay_payment_id)
     */
    public function paymentExists($razorpay_payment_id) {
        return $this->db->where('payment_id', $razorpay_payment_id)
                        ->count_all_results('tb_ft_payments') > 0;
    }

    /**
     * Get payment history for a user
     */
    public function getUserPaymentHistory($user_id) {
        $this->db->select('*');
        $this->db->from('tb_ft_payments');
        $this->db->where('user_id', $user_id);
        $this->db->order_by('created_at', 'DESC');
        return $this->db->get()->result_array();
    }

    /**
     * Get invoice details for a specific invoice number and user
     */
    public function getInvoiceDetails($invoice_no, $user_id) {
        $this->db->select('p.*, up.*');
        $this->db->from('tb_ft_payments p');
        $this->db->join('tb_ft_user_purchases up', 'up.payment_id = p.id', 'left');
        $this->db->where('p.invoice_no', $invoice_no);
        $this->db->where('p.user_id', $user_id);
        return $this->db->get()->row_array();
    }
}
?>