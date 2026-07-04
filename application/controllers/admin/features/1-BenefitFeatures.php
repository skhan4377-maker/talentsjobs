<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class BenefitFeatures extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('admin/services/Feature_model');
    }

    /**
     * Get header labels by feature_id
     */
    public function get_benefit_values($feature_id) {
        $data = $this->Feature_model->get_benefit_values($feature_id);
        if ($data) {
            echo json_encode(['success' => true, 'data' => $data]);
        } else {
            echo json_encode(['success' => false, 'message' => 'No record found.']);
        }
    }

    /**
     * Save or update benefit header labels
     */
    public function save_benefit() {
		$this->load->library('form_validation');

		$this->form_validation->set_rules('feature_id', 'Feature ID', 'required|trim');
		$this->form_validation->set_rules('title_label', 'Title Label', 'required|trim');
		$this->form_validation->set_rules('col_1_label', 'Column 1 Label', 'required|trim');
		$this->form_validation->set_rules('col_2_label', 'Column 2 Label', 'required|trim');

		if ($this->form_validation->run() === FALSE) {
			echo json_encode([
				'success' => false,
				'message' => validation_errors('<div>', '</div>')
			]);
			return;
		}

		$this->load->model('admin/services/Feature_model');

		$feature_id = $this->input->post('feature_id');

		// Save header
		$headerData = [
			'feature_id'   => $feature_id,
			'title_label'  => $this->input->post('title_label'),
			'col_1_label'  => $this->input->post('col_1_label'),
			'col_2_label'  => $this->input->post('col_2_label'),
		];

		$headerSaved = $this->Feature_model->upsert_benefit($headerData);

		// Proceed only if header is saved
		if (!$headerSaved) {
			echo json_encode(['success' => false, 'message' => 'Failed to save benefit headers.']);
			return;
		}

		// Clear old benefit rows
		$this->db->where('feature_id', $feature_id)->delete('tb_benefit_comparisons');

		// Get benefit row arrays
		$benefit_titles = $this->input->post('benefit_title');
		$col_1_values   = $this->input->post('col_1');
		$col_2_values   = $this->input->post('col_2');

		if (is_array($benefit_titles)) {
			foreach ($benefit_titles as $index => $title) {
				if (trim($title)) {
					$comparisonRow = [
						'feature_id'    => $feature_id,
						'benefit_title' => trim($title),
						'col_1'         => trim($col_1_values[$index]),
						'col_2'         => trim($col_2_values[$index])
					];
					$this->Feature_model->insert_comparison($comparisonRow);
				}
			}
		}

		echo json_encode(['success' => true, 'message' => 'Benefit headers and comparisons saved successfully.']);
	}


}
