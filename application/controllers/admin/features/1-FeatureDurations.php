<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class FeatureDurations extends MY_Controller {

	public function __construct() {
		parent::__construct();		
		$this->load->model('admin/services/Feature_model');
	}

	// List all feature durations
	public function index() {
		$data['durations'] = $this->Feature_model->get_all_durations();
		$data['title']     = 'Feature Duration List';
		$data['mode']      = 'list';
		$data['content']   = $this->load->view('admin/services/add-edit-feature-duration', $data, true);
		$this->load->view('templates/master', $data);
	}

	// Show add duration form
	public function add() {
		$data['features'] = $this->Feature_model->get_all_features();
		$data['title']    = 'Add Feature Duration';
		$data['mode']     = 'add';
		$data['content']  = $this->load->view('admin/services/add-edit-feature-duration', $data, true);
		$this->load->view('templates/master', $data);
	}
	
	public function save() {
		$this->load->library('form_validation');

		// Sanitize & fetch static field
		$feature_id = $this->security->xss_clean($this->input->post('feature_id'));

		// Validate static field
		if (empty($feature_id) || !is_numeric($feature_id)) {
			$response = [
				'success' => false, 
				'message' => 'Invalid or missing Feature ID',
				'csrf_token' => $this->get_csrf_token()
			];
			echo json_encode($response);
			exit;
		}

		// Sanitize arrays
		$plan_level       = $this->security->xss_clean($this->input->post('plan_level'));
		$plan_durations   = $this->security->xss_clean($this->input->post('duration'));
		$plan_mrp         = $this->security->xss_clean($this->input->post('plan_mrp'));
		$plan_discount    = $this->security->xss_clean($this->input->post('plan_discount'));
		$plan_total       = $this->security->xss_clean($this->input->post('plan_total'));
		$plan_taxes       = $this->security->xss_clean($this->input->post('plan_taxes'));
		$experience_range = $this->security->xss_clean($this->input->post('experience_range'));
		$monthly_cost     = $this->security->xss_clean($this->input->post('monthly_cost'));

		$batchData = [];
		for ($i = 0; $i < count($plan_durations); $i++) {
			$batchData[] = [
				'feature_id'       => (int) $feature_id,
				'plan_level'       => $plan_level[$i] ?? null,
				'experience_range' => $experience_range[$i] ?? null,
				'duration'         => $plan_durations[$i],
				'plan_mrp'         => (float) str_replace(',', '', $plan_mrp[$i] ?? 0),
				'plan_discount'    => (float) str_replace(',', '', $plan_discount[$i] ?? 0),
				'plan_total'       => (float) str_replace(',', '', $plan_total[$i] ?? 0),
				'plan_taxes'       => (float) str_replace(',', '', $plan_taxes[$i] ?? 0),
				'monthly_cost'     => (float) str_replace(',', '', $monthly_cost[$i] ?? 0),
				'created_at'       => date('Y-m-d H:i:s')
			];
		}

		// Perform DB insert in transaction
		if (!empty($batchData)) {
			$this->db->trans_start();
			$this->db->insert_batch('tb_feature_durations', $batchData);
			$this->db->trans_complete();

			if ($this->db->trans_status() === TRUE) {
				$response = [
					'success' => true, 
					'message' => 'Durations saved successfully',
					'csrf_token' => $this->get_csrf_token()
				];
			} else {
				//log_message('error', 'DB insert_batch failed in FeatureDurations/save');
				$response = [
					'success' => false, 
					'message' => 'Database transaction failed',
					'csrf_token' => $this->get_csrf_token()
				];
			}
		} else {
			$response = [
				'success' => false, 
				'message' => 'No data to insert',
				'csrf_token' => $this->get_csrf_token()
			];
		}
		
		echo json_encode($response);
		exit;
	}

	
	public function update() {
		$duration_id = $this->input->post('duration_id');
		if (empty($duration_id)) {
			$response = [
				'success' => false, 
				'message' => 'Missing Duration ID',
				'csrf_token' => $this->get_csrf_token()
			];
			echo json_encode($response);
			exit;
		}

		$data = [
			'feature_id'       => (int) $this->input->post('feature_id'),
			'plan_level'       => $this->input->post('plan_level'),
			'experience_range' => $this->input->post('experience_range'),
			'duration'         => $this->input->post('duration'),
			'plan_mrp'         => (float) str_replace(',', '', $this->input->post('plan_mrp')),
			'plan_discount'    => (float) str_replace(',', '', $this->input->post('plan_discount')),
			'plan_total'       => (float) str_replace(',', '', $this->input->post('plan_total')),
			'plan_taxes'       => (float) str_replace(',', '', $this->input->post('plan_taxes')),
			'monthly_cost'     => (float) str_replace(',', '', $this->input->post('monthly_cost')),
			'updated_at'       => date('Y-m-d H:i:s')
		];

		$this->db->where('duration_id', $duration_id);
		$updated = $this->db->update('tb_feature_durations', $data);

		if ($updated) {
			$response = [
				'success' => true, 
				'message' => 'Duration updated successfully',
				'csrf_token' => $this->get_csrf_token()
			];
		} else {
			$response = [
				'success' => false, 
				'message' => 'Failed to update duration',
				'csrf_token' => $this->get_csrf_token()
			];
		}

		echo json_encode($response);
		exit;
	}

	
	public function edit($id) { 
		$durations = $this->Feature_model->get_durations_by_feature($id);
		$features = $this->Feature_model->get_all_features();
		
		$feature_id = null;
		if (!empty($durations)) {
			// पहले duration से feature_id लें
			$feature_id = $durations[0]['feature_id'] ?? null;
		}

		if (!empty($durations)) {
			$response = [
				'success' => true,
				'data' => $durations,
				'feature_id' => $feature_id, // ✅ अब यह सही feature_id भेजेगा
				'features' => $features,
				'csrf_token' => $this->get_csrf_token()
			];
		} else {
			$response = [
				'success' => false,
				'message' => 'Duration not found.',
				'csrf_token' => $this->get_csrf_token()
			];
		}

		echo json_encode($response);
		exit;
	}
	
	// ✅ Add delete method with CSRF token
	public function delete($id) {
		$this->db->where('duration_id', $id);
		$deleted = $this->db->delete('tb_feature_durations');

		if ($deleted) {
			$response = [
				'success' => true, 
				'message' => 'Duration plan deleted successfully',
				'csrf_token' => $this->get_csrf_token()
			];
		} else {
			$response = [
				'success' => false, 
				'message' => 'Failed to delete duration plan',
				'csrf_token' => $this->get_csrf_token()
			];
		}

		echo json_encode($response);
		exit;
	}

	// ✅ Helper method to get CSRF token in consistent format
	private function get_csrf_token() {
		return [
			'name' => $this->security->get_csrf_token_name(),
			'hash' => $this->security->get_csrf_hash()
		];
	}
}
?>