<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . 'libraries/MY_REST_Controller.php';

class Resume_draft extends MY_REST_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('api/ResumeDraft_model');
        // No separate UserPlan_model – we'll check directly via tb_ft_user_purchases
    }

    public function view_get() {
		$user_id = $this->user_id;
		$draft = $this->ResumeDraft_model->get_by_user($user_id);

		if (!$draft) {
			return $this->response([
				'status'   => true,
				'message'  => 'No draft found',
				'data'     => [],
				'template' => null,
				'plan'     => null
			], REST_Controller::HTTP_OK);
		}

		$formData = json_decode($draft['form_data'], true);

		$preview_image = !empty($draft['preview_image']) 
			? base_url(ltrim($draft['preview_image'], '/')) 
			: null;

		$template = [
			'template_id'      => $draft['template_id'],
			'name'             => $draft['template_name'],
			'html_layout'      => $draft['html_layout'],
			'preview_image'    => $preview_image,
			'template_type'    => $draft['template_type'],
			'is_premium'       => (bool) $draft['is_premium'],
			'layout_type'      => $draft['layout_type'],
			'experience_level' => $draft['experience_level'],
			'industry_id'      => $draft['industry_id'],
			'updated_at'       => date('d M Y, H:i', strtotime($draft['updated_at']))
		];

		// 🔥 FINAL FIX (NO DATE CHECK)
		$has_active_plan = !empty($draft['user_purchase_id']);

		$plan = [
			'has_active_plan' => $has_active_plan,
			'purchase_id'     => $draft['user_purchase_id'] ?? null,
			'feature_id'      => $draft['purchase_feature_id'] ?? null,
			'payment_id'      => $draft['payment_id'] ?? null,
			'payment_status'  => $draft['payment_status'] ?? 'none',
			'start_date'      => $draft['start_date'] ?? null,
			'end_date'        => $draft['end_date'] ?? null
		];

		return $this->response([
			'status'   => true,
			'message'  => 'Draft fetched',
			'data'     => $formData,
			'template' => $template,
			'plan'     => $plan
		], REST_Controller::HTTP_OK);
	}

    /**
     * POST: /api/resume-draft/save
     */
    public function save_post() {
        $user_id = $this->user_id;
        $form_data = $this->post();
        if (empty($form_data)) {
            return $this->response([
                'status'  => false,
                'message' => 'No form data'
            ], REST_Controller::HTTP_BAD_REQUEST);
        }

        $template_id = $form_data['template_id'] ?? null;
        $draft_id = $this->ResumeDraft_model->save_draft($user_id, $form_data, $template_id);
        if (!$draft_id) {
            return $this->response([
                'status'  => false,
                'message' => 'Failed to save draft'
            ], REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
        }

        // Check if user has an active purchase for Resume Builder feature
        $resume_feature_id = $this->config->item('resume_builder_feature_id') ?: 1; // set in config or get from DB
        $has_active_plan = $this->db->where('user_id', $user_id)
            ->where('feature_id', $resume_feature_id)
            ->where('status', 'active')
            ->where('start_date <=', date('Y-m-d H:i:s'))
            ->where('end_date >=', date('Y-m-d H:i:s'))
            ->get('tb_ft_user_purchases')
            ->num_rows() > 0;

        if ($has_active_plan) {
            $result = $this->ResumeDraft_model->save_full_resume($user_id, $form_data);
            if ($result['status']) {
                $this->db->where('draft_id', $draft_id)->update('tb_ft_resume_drafts', ['is_finalized' => 1]);
            }
            $message = 'Draft saved';
            $synced = true;
        } else {
            $message = 'Draft saved';
            $synced = false;
        }

        return $this->response([
            'status'    => true,
            'message'   => $message,
            'draft_id'  => $draft_id,
            'is_synced' => $synced
        ], REST_Controller::HTTP_OK);
    }
}
?>