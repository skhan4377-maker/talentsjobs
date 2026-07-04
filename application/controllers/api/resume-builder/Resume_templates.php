<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

class Resume_templates extends REST_Controller {

	public function __construct() {
		parent::__construct();
		$this->load->model('api/ResumeTemplates_model');
		$this->load->model('api/TemplateActivity_model');
		
	}

	/* =====================================================
	   GET: /api/resume-templates
	===================================================== */
	public function index_get() {
		$page  = max(1, (int) $this->get('page'));
		$limit = max(1, (int) $this->get('limit', true) ?: 20);
		$offset = ($page - 1) * $limit;

		$data  = $this->ResumeTemplates_model->api_get_all($limit, $offset);
		$total = $this->ResumeTemplates_model->get_total_count();

		return $this->response([
			'status' => true,
			'page'   => $page,
			'limit'  => $limit,
			'total'  => $total,
			'data'   => $data
		], REST_Controller::HTTP_OK);
	}

	/* =====================================================
	   GET: /api/resume-templates/{id}
	===================================================== */
	public function view_get($id = null) {
		if (!$id) {
			return $this->response([
				'status'  => false,
				'message' => 'Template ID is required'
			], REST_Controller::HTTP_BAD_REQUEST);
		}

		$template = $this->ResumeTemplates_model->api_get_by_id($id);
		if (!$template) {
			return $this->response([
				'status'  => false,
				'message' => 'Template not found'
			], REST_Controller::HTTP_NOT_FOUND);
		}

		return $this->response([
			'status'  => true,
			'data'    => $template
		], REST_Controller::HTTP_OK);
	}

	/* =====================================================
	   POST: /api/resume-templates/track-usage
	===================================================== */
	public function track_usage_post() {
		$template_id = $this->post('template_id');
		$user_id     = $this->post('user_id');

		if (!$template_id) {
			return $this->response([
				'status' => false,
				'message' => 'template_id is required'
			], REST_Controller::HTTP_BAD_REQUEST);
		}

		$this->TemplateActivity_model->log_template_usage([
			'user_id'     => $user_id,
			'template_id' => $template_id
		]);

		return $this->response([
			'status'  => true,
			'message' => 'Usage logged'
		], REST_Controller::HTTP_OK);
	}
	
	
}