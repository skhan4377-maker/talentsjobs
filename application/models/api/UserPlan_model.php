<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class UserPlan_model extends CI_Model {

    // Plan related tables
    protected $user_purchases_table = 'tb_ft_user_purchases';
    protected $payments_table = 'tb_ft_payments';
    protected $plans_table = 'tb_ft_plans';
    protected $features_table = 'tb_ft_features';
    protected $refund_requests_table = 'tb_ft_refund_requests';
    protected $benefit_comparisons_table = 'tb_ft_benefit_comparisons';
    
    // Module access tables
    protected $user_subscriptions_table = 'tb_ft_user_subscriptions';
    protected $modules_table = 'tb_ft_modules';
    protected $module_limits_table = 'tb_ft_module_limits';
    protected $feature_usage_table = 'tb_ft_feature_usage';
    protected $resume_downloads_table = 'tb_ft_resume_downloads';

    public function __construct() {
        parent::__construct();
    }

    // ========== PLAN METHODS ==========

    public function get_user_active_plan($user_id) {
        $REFUND_WINDOW_SECONDS = 86400;

        $sql = "
            SELECT 
                up.id AS user_purchase_id,
                up.user_id,
                up.payment_id,
                up.razorpay_payment_id,
                up.feature_id,
                up.start_date,
                up.end_date,
                up.status,
                up.created_at AS purchase_date,
                p.plan_level,
                p.duration,
                p.plan_total AS total_cost,
                p.monthly_cost,
                f.feature_name,
                pay.order_id,
                pay.refund_status,
                rr.status AS refund_request_status,
                rr.reason AS refund_reason,
                rr.requested_at AS refund_requested_at,
                rr.processed_at AS refund_processed_at,
                rr.gateway_status,
                rr.failure_reason,
                TIMESTAMPDIFF(SECOND, up.created_at, UTC_TIMESTAMP()) AS seconds_since_purchase
            FROM {$this->user_purchases_table} up
            LEFT JOIN {$this->plans_table} p ON p.duration_id = up.plan_id
            LEFT JOIN {$this->features_table} f ON f.feature_id = up.feature_id
            LEFT JOIN {$this->payments_table} pay ON pay.id = up.payment_id
            LEFT JOIN {$this->refund_requests_table} rr 
                ON rr.order_id = pay.order_id AND rr.user_id = up.user_id
            WHERE up.user_id = ?
              AND up.status = 'active'
              AND up.end_date >= UTC_TIMESTAMP()
            ORDER BY up.end_date DESC
            LIMIT 1
        ";

        $query = $this->db->query($sql, [$user_id]);
        if ($query->num_rows() === 0) return null;

        $row = $query->row_array();
        $displayFormat = 'Y-M-d h:i A';
        foreach (['start_date', 'end_date', 'purchase_date'] as $dateField) {
            if (!empty($row[$dateField])) {
                $row[$dateField] = date($displayFormat, strtotime($row[$dateField]));
            }
        }

        $row['refund_status_detailed'] = $this->determineRefundStatus($row);
        $secondsPassed = (int)$row['seconds_since_purchase'];

        if ($row['refund_status'] === 'processed' || $row['refund_request_status'] === 'processed') {
            $row['refund'] = ['eligible' => false, 'label' => 'Refund already processed'];
        } elseif ($row['refund_request_status'] === 'pending' || $row['refund_request_status'] === 'approved') {
            $row['refund'] = ['eligible' => false, 'label' => 'Refund request in progress'];
        } elseif ($secondsPassed >= $REFUND_WINDOW_SECONDS) {
            $row['refund'] = ['eligible' => false, 'label' => 'Refund window expired'];
        } else {
            $remaining = $REFUND_WINDOW_SECONDS - $secondsPassed;
            $row['refund'] = [
                'eligible' => true,
                'remaining_seconds' => $remaining,
                'label' => floor($remaining / 3600) . 'h ' . floor(($remaining % 3600) / 60) . 'm left'
            ];
        }
        unset($row['seconds_since_purchase']);
        return $row;
    }

    private function determineRefundStatus($planData) {
        if ($planData['refund_status'] === 'processed') return 'processed';
        if ($planData['refund_request_status']) {
            switch($planData['refund_request_status']) {
                case 'pending': return 'pending_approval';
                case 'approved': return 'approved_pending_razorpay';
                case 'processing': return 'processing_razorpay';
                case 'processed': return 'processed';
                case 'failed': return 'failed';
                case 'rejected': return 'rejected';
            }
        }
        if ($planData['gateway_status'] === 'success') return 'processed';
        return 'eligible';
    }

    public function get_user_upcoming_plans($user_id) {
        $this->db->select("
            up.id AS user_purchase_id,
            up.user_id,
            up.payment_id,
            up.razorpay_payment_id,
            pay.order_id,
            f.feature_id,
            f.feature_name,
            p.plan_level,
            p.duration,
            up.start_date,
            up.end_date,
            up.status,
            p.plan_total AS total_cost,
            p.monthly_cost,
            up.created_at AS purchase_date
        ");
        $this->db->from($this->user_purchases_table . " up");
        $this->db->join($this->payments_table . " pay", "pay.id = up.payment_id", "left");
        $this->db->join($this->plans_table . " p", "up.plan_id = p.duration_id", "left");
        $this->db->join($this->features_table . " f", "up.feature_id = f.feature_id", "left");
        $this->db->where("up.user_id", $user_id);
        $this->db->where("up.status", "upcoming");
        $this->db->where("up.start_date >", date('Y-m-d H:i:s'));
        $this->db->order_by("up.start_date", "ASC");

        $plans = $this->db->get()->result_array();
        foreach ($plans as &$plan) {
            $plan['start_date'] = date("d F Y", strtotime($plan['start_date']));
            $plan['end_date'] = date("d F Y", strtotime($plan['end_date']));
            $plan['purchase_date'] = date("d F Y", strtotime($plan['purchase_date']));
            $plan['total_cost'] = (float)$plan['total_cost'];
            $plan['monthly_cost'] = (float)$plan['monthly_cost'];
        }
        return $plans;
    }

    public function get_user_past_plans($user_id) {
        $this->db->select("
            up.id AS user_purchase_id,
            up.user_id,
            up.payment_id,
            up.razorpay_payment_id,
            pay.order_id,
            f.feature_id,
            f.feature_name,
            p.plan_level,
            p.duration,
            up.start_date,
            up.end_date,
            up.status,
            p.plan_total AS total_cost,
            p.monthly_cost,
            up.created_at AS purchase_date
        ");
        $this->db->from($this->user_purchases_table . " up");
        $this->db->join($this->payments_table . " pay", "pay.id = up.payment_id", "left");
        $this->db->join($this->plans_table . " p", "up.plan_id = p.duration_id", "left");
        $this->db->join($this->features_table . " f", "up.feature_id = f.feature_id", "left");
        $this->db->where("up.user_id", $user_id);
        $this->db->where_in("up.status", ['expired', 'cancelled', 'refunded']);
        $this->db->order_by("up.updated_at", "DESC");

        $plans = $this->db->get()->result_array();
        foreach ($plans as &$plan) {
            $plan['start_date'] = date("d F Y", strtotime($plan['start_date']));
            $plan['end_date'] = date("d F Y", strtotime($plan['end_date']));
            $plan['purchase_date'] = date("d F Y", strtotime($plan['purchase_date']));
            $plan['total_cost'] = (float)$plan['total_cost'];
            $plan['monthly_cost'] = (float)$plan['monthly_cost'];
        }
        return $plans;
    }

    public function has_active_plan($user_id) {
        return !empty($this->get_user_active_plan($user_id));
    }

    public function get_feature_highlights($feature_id) {
        $this->db->select("benefit_title");
        $this->db->from($this->benefit_comparisons_table);
        $this->db->where("feature_id", $feature_id);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return array_column($query->result_array(), 'benefit_title');
        }
        return [];
    }

    // ========== MODULE ACCESS METHODS (formerly FeatureAccess_model) ==========

    public function getModuleIdByKey($module_key) {
        $row = $this->db->select('module_id')
            ->from($this->modules_table)
            ->where('module_key', $module_key)
            ->get()
            ->row();
        return $row ? (int)$row->module_id : null;
    }

    public function getUserActiveFeature($user_id) {
		// 🔥 ALWAYS pick latest active purchase
		$row = $this->db->select('feature_id')
			->from($this->user_purchases_table)
			->where('user_id', $user_id)
			->where('status', 'active')
			->order_by('end_date', 'DESC')
			->limit(1)
			->get()
			->row();

		if ($row && !empty($row->feature_id)) {
			return (int)$row->feature_id; // ✅ PAID PLAN
		}

		// fallback (optional)
		return 1; // free
	}

    public function canAccessModuleByKey($user_id, $module_key) {
        $module_id = $this->getModuleIdByKey($module_key);
        if (!$module_id) return false;
        return $this->canAccessModule($user_id, $module_id);
    }

    public function canAccessModule($user_id, $module_id) {
		$feature_id = $this->getUserActiveFeature($user_id);

		$limit = $this->db->select('is_limited, usage_limit')
			->from($this->module_limits_table)
			->where('feature_id', $feature_id)
			->where('module_id', $module_id)
			->get()
			->row();

		if (!$limit) return false;

		// ✅ UNLIMITED PLAN
		if ((int)$limit->is_limited === 0) {
			return true;
		}

		// 🔥 SAFETY FIX (IMPORTANT)
		if ((int)$limit->usage_limit === 0) {
			return true; // treat as unlimited
		}

		$used = $this->db->where('candidate_id', $user_id)
			->where('feature_id', $feature_id)
			->where('module_id', $module_id)
			->where('used_at >=', date('Y-m-d H:i:s', strtotime('-30 days')))
			->count_all_results($this->feature_usage_table);

		return $used < $limit->usage_limit;
	}

    public function getRemainingDownloads($user_id) {
        return $this->getRemainingUsageByKey($user_id, 'resume_download');
    }

    public function getRemainingUsageByKey($user_id, $module_key) {
        $module_id = $this->getModuleIdByKey($module_key);
        if (!$module_id) return 0;
        return $this->getRemainingUsage($user_id, $module_id);
    }

    public function getRemainingUsage($user_id, $module_id) {
        $feature_id = $this->getUserActiveFeature($user_id);
        $limit = $this->db->select('is_limited, usage_limit')
            ->from($this->module_limits_table)
            ->where('feature_id', $feature_id)
            ->where('module_id', $module_id)
            ->get()
            ->row();
        if (!$limit) return 0;
        if ($limit->is_limited == 0) return -1;
        $used = $this->db->where('candidate_id', $user_id)
            ->where('feature_id', $feature_id)
            ->where('module_id', $module_id)
            ->where('used_at >=', date('Y-m-d H:i:s', strtotime('-30 days')))
            ->count_all_results($this->feature_usage_table);
        return max(0, $limit->usage_limit - $used);
    }

    public function recordUsageByKey($user_id, $module_key) {
        $module_id = $this->getModuleIdByKey($module_key);
        if (!$module_id) return false;
        return $this->recordUsage($user_id, $module_id);
    }

    public function recordUsage($user_id, $module_id) {
        $feature_id = $this->getUserActiveFeature($user_id);
        return $this->db->insert($this->feature_usage_table, [
            'candidate_id' => $user_id,
            'feature_id'   => $feature_id,
            'module_id'    => $module_id,
            'used_at'      => date('Y-m-d H:i:s')
        ]);
    }

    public function log_resume_download($user_id, $resume_id, $template_id) {
        $data = [
            'user_id'      => $user_id,
            'resume_id'    => $resume_id,
            'template_id'  => $template_id,
            'ip_address'   => $this->input->ip_address(),
            'user_agent'   => $this->input->user_agent(),
            'downloaded_at'=> date('Y-m-d H:i:s')
        ];
        return $this->db->insert($this->resume_downloads_table, $data);
    }
}