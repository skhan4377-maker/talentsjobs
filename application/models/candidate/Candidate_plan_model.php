<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Candidate_plan_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    /**
     * Get all plans purchased by a candidate with full details
     * @param int $candidate_id
     * @return array
     */
     public function get_candidate_plans($candidate_id) {
		$this->db->select('
			up.id,
			up.user_id,
			up.plan_id,
			up.feature_id,
			up.payment_id,
			up.start_date,
			up.end_date,
			up.status as plan_status,
			up.created_at as purchase_date,
			f.feature_name,
			f.slug as feature_slug,
			fp.duration,
			fp.plan_mrp,
			fp.plan_discount,
			fp.plan_total as plan_price,
			p.invoice_no,
			p.order_id,    
			p.amount as paid_amount,
			p.status as payment_status,
			p.created_at as payment_date,
			p.refund_status,
			rr.id as refund_req_id,
			rr.status as refund_req_status,
			rr.requested_at as refund_req_at,
			rr.processed_at as refund_req_processed_at,
			rr.admin_notes,
			rr.gateway_status
		');
		$this->db->from('tb_ft_user_purchases up');
		$this->db->join('tb_ft_features f', 'up.feature_id = f.feature_id', 'left');
		$this->db->join('tb_ft_plans fp', 'up.plan_id = fp.duration_id', 'left');
		$this->db->join('tb_ft_payments p', 'up.payment_id = p.id', 'left');
		
		$this->db->join('tb_ft_refund_requests rr', 'p.order_id = rr.order_id AND rr.user_id = up.user_id', 'left');
		$this->db->where('up.user_id', $candidate_id);
		$this->db->order_by('up.created_at', 'DESC');
		$query = $this->db->get();
		
		$plans = $query->result_array();

		foreach ($plans as &$plan) {
			$plan['tags'] = $this->get_feature_tags($plan['feature_id']);
		}
		return $plans;
		return $query->result_array();
	}
    
    /**
     * Check if a candidate has at least one active plan
     * @param int $candidate_id
     * @return bool
     */
    public function has_active_plan($candidate_id) {
        $now = date('Y-m-d H:i:s');
        $this->db->where('user_id', $candidate_id);
        $this->db->where('status', 'active');
        $this->db->where('start_date <=', $now);
        $this->db->where('end_date >=', $now);
        return $this->db->count_all_results('tb_ft_user_purchases') > 0;
    }
    
    public function get_invoice_by_invoice_no($invoice_no, $candidate_id) {
        $this->db->select('
			p.id as payment_id,
			p.invoice_no,
			p.amount as paid_amount,
			p.method as payment_method,
			p.payment_id as transaction_id,  
			p.status as payment_status,
			p.created_at as payment_date,
			up.id as user_plan_id,
			up.start_date,
			up.end_date,
			up.status as plan_status,
			f.feature_name,
			f.slug,
			fp.duration,
			fp.plan_mrp,
			fp.plan_discount,
			fp.plan_taxes,   
			fp.plan_total,
			u.name as first_name,
			u.last_name,
			u.email,
			u.mobile
		');
        $this->db->from('tb_ft_payments p');
        $this->db->join('tb_ft_user_purchases up', 'p.id = up.payment_id', 'inner');
        $this->db->join('tb_ft_features f', 'up.feature_id = f.feature_id', 'left');
        $this->db->join('tb_ft_plans fp', 'up.plan_id = fp.duration_id', 'left');
        $this->db->join('tb_candidate u', 'up.user_id = u.candidate_id', 'left');
        $this->db->where('p.invoice_no', $invoice_no);
        $this->db->where('up.user_id', $candidate_id);
        $query = $this->db->get();
        return $query->row_array();
    }
	
	/**
	 * Get all tags (benefits) for a specific feature
	 */
	public function get_feature_tags($feature_id) {
		return $this->db
			->select('tag_title')
			->where('feature_id', $feature_id)
			->where('is_active', 'yes')
			->order_by('tag_order', 'ASC')
			->get('tb_ft_tags')
			->result_array();
	}
	
	/**
	 * Get the current active plan of a candidate (if any)
	 * @param int $candidate_id
	 * @return array|null
	 * use at candidate dashboard **/
	public function get_active_plan($candidate_id)
	{
		$now = date('Y-m-d H:i:s');
		$this->db->select('
			up.id, up.start_date, up.end_date, up.status,
			f.feature_name, f.slug,
			fp.duration, fp.plan_total as plan_price
		');
		$this->db->from('tb_ft_user_purchases up');
		$this->db->join('tb_ft_features f', 'up.feature_id = f.feature_id', 'left');
		$this->db->join('tb_ft_plans fp', 'up.plan_id = fp.duration_id', 'left');
		$this->db->where('up.user_id', $candidate_id);
		$this->db->where('up.status', 'active');
		$this->db->where('up.start_date <=', $now);
		$this->db->where('up.end_date >=', $now);
		$this->db->order_by('up.end_date', 'DESC'); // longest validity first, or just any
		$this->db->limit(1);
		return $this->db->get()->row_array();
	}
	
}