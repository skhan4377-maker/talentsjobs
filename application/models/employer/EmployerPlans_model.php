<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class EmployerPlans_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }
	
	// EmployerPlan use
	 public function getCurrentActivePlan($employerId) {

        return $this->db
            ->select('
                pp.id as plan_purchase_id,
                pp.employer_id,
                pp.plan_id,
                pp.plan_status,
                pp.start_date,
                pp.end_date,
                pp.purchase_date,
                pp.job_posts_used,
                pp.cv_views_used,
                pp.searches_used,
                pp.bulk_downloads_used,

                sp.plan_name,
                sp.plan_category as plan_type,
                sp.price as discounted_price,
                sp.job_post_limit as post_balance,
                sp.cv_view_limit as cv_views_per_requirement,
                sp.search_limit as search_results_limit,
                sp.bulk_download_limit,
                sp.single_user_access,
                sp.email_multiple_candidates,
                sp.download_cvs_in_bulk,
                sp.plan_validity_days
            ')
            ->from('tb_plan_purchases pp')
            ->join('tb_subscription_plans sp', 'sp.id = pp.plan_id', 'left')
            ->where('pp.employer_id', $employerId)
            ->where('pp.plan_status', 'active')
            ->where('pp.is_deleted', 0)
            ->where('pp.end_date >=', date('Y-m-d H:i:s'))
            ->order_by('pp.id', 'DESC')
            ->limit(1)
            ->get()
            ->row_array();
    
    }
	
    /**
     * Get active plan details candidate database use
     */
    public function getActivePlanDetails($employerId) {

        return $this->db
            ->select('
                pp.id as plan_purchase_id,
                pp.employer_id,
                pp.plan_id,
                pp.plan_status,
                pp.start_date,
                pp.end_date,
                pp.job_posts_used,
                pp.cv_views_used,
                pp.searches_used,
                pp.bulk_downloads_used,

                sp.plan_name,
                sp.plan_category,
                sp.price,
                sp.job_post_limit,
                sp.cv_view_limit,
                sp.search_limit,
                sp.bulk_download_limit,
                sp.single_user_access,
                sp.email_multiple_candidates,
                sp.download_cvs_in_bulk,
                sp.plan_validity_days
            ')
            ->from('tb_plan_purchases pp')
            ->join('tb_subscription_plans sp','sp.id = pp.plan_id','left')
            ->where('pp.employer_id',$employerId)
            ->where('pp.plan_status','active')
            ->where('pp.is_deleted',0)
            ->where('pp.end_date >=',date('Y-m-d H:i:s'))
            ->order_by('pp.id','DESC')
            ->limit(1)
            ->get()
            ->row_array();
    }


    /**
     * Check if employer has active plan
     */
    public function hasActivePlan($employerId) {

        return $this->db
            ->where('employer_id',$employerId)
            ->where('plan_status','active')
            ->where('is_deleted',0)
            ->where('end_date >=',date('Y-m-d H:i:s'))
            ->count_all_results('tb_plan_purchases') > 0;
    }


    /**
     * Increment usage counters
     * job | cv | search | download
     */
    public function incrementUsage($planPurchaseId,$type) {

        $map = [
            'job'      => 'job_posts_used',
            'cv'       => 'cv_views_used',
            'search'   => 'searches_used',
            'download' => 'bulk_downloads_used'
        ];

        if(!isset($map[$type])) return false;

        return $this->db
            ->set($map[$type], $map[$type].' + 1', FALSE)
            ->where('id',$planPurchaseId)
            ->update('tb_plan_purchases');
    }


    /**
     * Check if action allowed
     */
    public function canPerformAction($employerId,$actionType) {

        $plan = $this->getActivePlanDetails($employerId);

        if(!$plan) return "No active plan found.";

        switch($actionType) {

            case 'job_post':

                if($plan['job_post_limit'] > 0 &&
                   $plan['job_posts_used'] >= $plan['job_post_limit']) {

                    return "Job posting limit exceeded.";
                }

            break;


            case 'cv_view':

                if($plan['cv_view_limit'] > 0 &&
                   $plan['cv_views_used'] >= $plan['cv_view_limit']) {

                    return "CV view limit exceeded.";
                }

            break;


            case 'search':

                if($plan['search_limit'] > 0 &&
                   $plan['searches_used'] >= $plan['search_limit']) {

                    return "Search limit exceeded.";
                }

            break;


            case 'bulk_download':

                if(!$plan['download_cvs_in_bulk']) {

                    return "Bulk download not allowed in your plan.";
                }

                if($plan['bulk_download_limit'] > 0 &&
                   $plan['bulk_downloads_used'] >= $plan['bulk_download_limit']) {

                    return "Bulk download limit exceeded.";
                }

            break;

            default:
                return "Invalid action.";

        }

        return true;
    }


    /**
     * Remaining credits for UI
     */
    public function getRemainingCredits($employerId) {

        $plan = $this->getActivePlanDetails($employerId);

        if(!$plan) return null;

        return [

            'jobs_used' => $plan['job_posts_used'],
            'jobs_limit' => $plan['job_post_limit'],

            'cv_used' => $plan['cv_views_used'],
            'cv_limit' => $plan['cv_view_limit'],

            'search_used' => $plan['searches_used'],
            'search_limit' => $plan['search_limit'],

            'download_used' => $plan['bulk_downloads_used'],
            'download_limit' => $plan['bulk_download_limit']

        ];
    }


    /**
     * Remaining job posts
     */
    public function getRemainingJobPosts($employerId) {

        $plan = $this->getActivePlanDetails($employerId);

        if(!$plan) return 0;

        return max(0, $plan['job_post_limit'] - $plan['job_posts_used']);
    }


    /**
     * Expire old plans (cron job)
     */
    public function expireOldPlans() {

        return $this->db
            ->set('plan_status','expired')
            ->where('plan_status','active')
            ->where('end_date <',date('Y-m-d H:i:s'))
            ->update('tb_plan_purchases');
    }

	public function getFreePlan()
	{
		return $this->db
			->where('plan_category','Free')
			->where('status','active')
			->limit(1)
			->get('tb_subscription_plans')
			->row_array();
	}
}