<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . 'libraries/MY_REST_Controller.php';

class UserPlan extends MY_REST_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('api/UserPlan_model', 'UserPlanModel');
    }

    /**
     * GET /api/userplan/my_plan
     * Returns user's plan data and remaining downloads.
     */
    public function my_plan_get() {
        $user_id = $this->user_id;
        if (!$user_id) {
            return $this->response([
                'success' => false,
                'message' => 'User authentication required'
            ], REST_Controller::HTTP_UNAUTHORIZED);
        }

        // Fetch plan data
        $active_plan = $this->UserPlanModel->get_user_active_plan($user_id);
        $upcoming_plans = $this->UserPlanModel->get_user_upcoming_plans($user_id);
        $past_plans = $this->UserPlanModel->get_user_past_plans($user_id);

        // Add features to active plan
        if ($active_plan) {
            $active_plan['features'] = $this->UserPlanModel->get_feature_highlights($active_plan['feature_id']);
        }
        foreach ($upcoming_plans as &$plan) {
            $plan['features'] = $this->UserPlanModel->get_feature_highlights($plan['feature_id']);
        }

        // Get remaining downloads
        $remaining_downloads = $this->UserPlanModel->getRemainingDownloads($user_id);

        if (!$active_plan && empty($upcoming_plans) && empty($past_plans)) {
            return $this->response([
                'success' => false,
                'message' => 'No subscription plans found',
                'remaining_downloads' => $remaining_downloads
            ], REST_Controller::HTTP_OK);
        }

        return $this->response([
            'success' => true,
            'data' => [
                'active_plan'   => $active_plan,
                'upcoming_plans'=> $upcoming_plans,
                'past_plans'    => $past_plans
            ],
            'remaining_downloads' => $remaining_downloads
        ], REST_Controller::HTTP_OK);
    }

    public function track_download_post() {
		$template_id = $this->post('template_id');
		$resume_id   = $this->post('resume_id');
		$user_id     = $this->post('user_id');

		if (!$template_id) {
			return $this->response([
				'status' => false,
				'message' => 'template_id is required'
			], REST_Controller::HTTP_BAD_REQUEST);
		}

		if (!$user_id) {
			$user_id = $this->user_id;
		}

		if (!$user_id) {
			return $this->response([
				'status' => false,
				'message' => 'User ID is required'
			], REST_Controller::HTTP_BAD_REQUEST);
		}

		// ─────────────────────────────────────────────
		// 🚫 NEW: Check if template is premium and user has only free tier
		// ─────────────────────────────────────────────
		$template = $this->db
			->select('is_premium, template_type')
			->from('tb_ft_resume_templates')
			->where('template_id', $template_id)
			->get()
			->row();

		$is_premium = false;
		if ($template) {
			$is_premium = ($template->is_premium == 1 || $template->template_type === 'paid');
		}

		if ($is_premium) {
			$active_feature = $this->UserPlanModel->getUserActiveFeature($user_id);
			// feature_id 1 = Free Resume Builder
			if ($active_feature == 1) {
				return $this->response([
					'status' => false,
					'message' => 'Premium templates require a paid subscription. Please upgrade your plan.',
					'remaining_downloads' => $this->UserPlanModel->getRemainingDownloads($user_id)
				], REST_Controller::HTTP_FORBIDDEN);
			}
		}

		// ─────────────────────────────────────────────
		// EXISTING download limit check (free tier)
		// ─────────────────────────────────────────────
		if (!$this->UserPlanModel->canAccessModuleByKey($user_id, 'resume_download')) {
			$remaining = $this->UserPlanModel->getRemainingUsageByKey($user_id, 'resume_download');
			$msg = $remaining === 0 
				? 'You have reached your free download limit. Please upgrade to continue.'
				: 'Download limit exceeded.';
			return $this->response([
				'status' => false,
				'message' => $msg,
				'remaining_downloads' => $remaining
			], REST_Controller::HTTP_FORBIDDEN);
		}

		// Record usage and log download
		$this->UserPlanModel->recordUsageByKey($user_id, 'resume_download');
		$this->UserPlanModel->log_resume_download($user_id, $resume_id, $template_id);

		$remaining = $this->UserPlanModel->getRemainingUsageByKey($user_id, 'resume_download');
		return $this->response([
			'status'  => true,
			'message' => 'Download logged and usage recorded',
			'remaining_downloads' => $remaining
		], REST_Controller::HTTP_OK);
	}
}