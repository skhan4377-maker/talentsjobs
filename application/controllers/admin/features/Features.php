<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Features extends MY_Controller {

    public function __construct() {
        parent::__construct();
		  $this->load->library('upload');   // ✅ Add this line
        $this->load->model('admin/Features_model');
    }

    public function index() {
        $data['services'] = $this->Features_model->get_all_services();
        $data['features'] = $this->Features_model->get_all_features();
        $data['title'] = 'Manage features';
        $data['content'] = $this->load->view('admin/features/features_list', $data, true);
        $this->load->view('templates/master', $data);
    }

    // Add feature (AJAX)
    public function add_feature() {
        $response = ['success' => false, 'message' => ''];
    
        $this->form_validation->set_rules('service_id', 'Service', 'required|integer');
        $this->form_validation->set_rules('feature_name', 'Feature Name', 'required|trim');
        $this->form_validation->set_rules('feature_tag', 'Feature Tag', 'required|trim');
        $this->form_validation->set_rules('feature_short_description', 'Short Description', 'required');
        $this->form_validation->set_rules('feature_full_description', 'Full Description', 'required');
        $this->form_validation->set_rules('is_active', 'Status', 'required');
    
        if ($this->form_validation->run() == FALSE) {
            $response['message'] = validation_errors();
        } else {
            $data = $this->_prepare_feature_data();
    
            if (isset($data['error'])) {
                $response['message'] = $data['error'];
                echo json_encode($this->_csrfResponse($response));
                return;
            }
    
            $feature_id = $this->Features_model->insert_feature($data);
    
            if ($feature_id) {
                $this->_save_related_data($feature_id);
                $response = ['success' => true, 'message' => 'Feature added successfully.'];
            } else {
                $response['message'] = 'Database error.';
            }
        }
    
        echo json_encode($this->_csrfResponse($response));
    }

    // Update feature (AJAX)
    public function update_feature() {
        $response = ['success' => false, 'message' => ''];
    
        $feature_id = $this->input->post('feature_id');
    
        $this->form_validation->set_rules('feature_id', 'Feature ID', 'required|integer');
        $this->form_validation->set_rules('service_id', 'Service', 'required|integer');
        $this->form_validation->set_rules('feature_name', 'Feature Name', 'required|trim');
        $this->form_validation->set_rules('feature_tag', 'Feature Tag', 'required|trim');
        $this->form_validation->set_rules('feature_short_description', 'Short Description', 'required');
        $this->form_validation->set_rules('feature_full_description', 'Full Description', 'required');
    
        if ($this->form_validation->run() == FALSE) {
            $response['message'] = validation_errors();
        } else {
            $data = $this->_prepare_feature_data($feature_id);
    
            if (isset($data['error'])) {
                $response['message'] = $data['error'];
                echo json_encode($this->_csrfResponse($response));
                return;
            }
    
            $updated = $this->Features_model->update_feature($feature_id, $data);
    
            if ($updated) {
                $this->_save_related_data($feature_id, true);
                $response = ['success' => true, 'message' => 'Feature updated successfully.'];
            } else {
                $response['message'] = 'No changes or DB error.';
            }
        }
    
        echo json_encode($this->_csrfResponse($response));
    }

    // Toggle status
    public function toggle_status() {
        $feature_id = $this->input->post('id');
        $current_status = $this->input->post('current_status');
    
        $new_status = ($current_status == 'active') ? 'inactive' : 'active';
    
        $result = $this->db
            ->where('feature_id', $feature_id)
            ->update('tb_ft_features', ['is_active' => $new_status]);
    
        $response = [
            'success' => $result
        ];
    
        echo json_encode($this->_csrfResponse($response));
    }

    // NEW: Fetch related data for editing (AJAX)
    public function get_feature_related($feature_id) {
		$feature_id = (int) $feature_id;

		$response = [
			'tags' => [],
			'qas' => [],
			'benefitHeader' => null,
			'benefitRows' => []
		];

		$tags = $this->Features_model->get_tags($feature_id);
		$response['tags'] = array_map(function($tag) {
			return $tag['tag_title'];
		}, $tags);

		$response['qas'] = $this->Features_model->get_qas($feature_id);
		$response['benefitHeader'] = $this->Features_model->get_benefit_header($feature_id);
		$response['benefitRows'] = $this->Features_model->get_benefit_comparisons($feature_id);

		// ✅ CSRF ADD
		echo json_encode($this->_csrfResponse($response));
	}

    // Helper: Prepare feature data (including file uploads)
    private function _prepare_feature_data($feature_id = null) {
        $data = [
            'service_id' => $this->input->post('service_id'),
            'feature_name' => $this->input->post('feature_name'),
            'feature_tag' => $this->input->post('feature_tag'),
            'slug' => url_title($this->input->post('feature_name'), '-', true),
			'redirect_url' => $this->input->post('redirect_url'),
            'feature_short_description' => $this->input->post('feature_short_description'),
            'feature_full_description' => $this->input->post('feature_full_description'),
            'feature_custom_label' => $this->input->post('feature_custom_label'),
            'feature_coupon_discount' => $this->input->post('feature_coupon_discount'),
            'is_active' => $this->input->post('is_active')
        ];

        // Upload feature logo
        if (!empty($_FILES['feature_logo']['name'])) {
            $upload = $this->_do_upload('feature_logo', 'feature_logos');
            if ($upload['status']) $data['feature_logo'] = $upload['file_path'];
            else return ['error' => $upload['error']];
        }
        // Benefit logo (optional)
        if (!empty($_FILES['benefit_logo']['name'])) {
            $upload = $this->_do_upload('benefit_logo', 'benefit_logos');
            if ($upload['status']) $data['benefit_logo'] = $upload['file_path'];
            else return ['error' => $upload['error']];
        }
        // Video/GIF
        if (!empty($_FILES['feature_video_gif']['name'])) {
            $upload = $this->_do_upload('feature_video_gif', 'feature_videos');
            if ($upload['status']) $data['feature_video_gif'] = $upload['file_path'];
            else return ['error' => $upload['error']];
        }
        return $data;
    }

    private function _do_upload($field, $subfolder) {
        $config['upload_path'] = './uploads/' . $subfolder . '/';
        $config['allowed_types'] = ($subfolder == 'feature_videos') ? 'gif|jpg|jpeg|png|mp4|webm|ogg' : 'jpg|jpeg|png|gif|svg|webp';
        $config['max_size'] = 2048;
        $config['encrypt_name'] = TRUE;

        if (!is_dir($config['upload_path'])) mkdir($config['upload_path'], 0777, true);
        $this->upload->initialize($config);

        if (!$this->upload->do_upload($field)) {
            return ['status' => false, 'error' => $this->upload->display_errors()];
        } else {
            $data = $this->upload->data();
            return ['status' => true, 'file_path' => 'uploads/' . $subfolder . '/' . $data['file_name']];
        }
    }

    // Save Tags, QAs, Benefits (Headers & Comparisons)
    private function _save_related_data($feature_id, $clear_existing = false) {
        if ($clear_existing) {
            $this->db->where('feature_id', $feature_id)->delete('tb_ft_tags');
            $this->db->where('feature_id', $feature_id)->delete('tb_ft_qas');
            $this->db->where('feature_id', $feature_id)->delete('tb_ft_benefit_headers');
            $this->db->where('feature_id', $feature_id)->delete('tb_ft_benefit_comparisons');
        }

        // Tags
        $tags = $this->input->post('tags');
        if (!empty($tags) && is_array($tags)) {
            foreach ($tags as $tag_title) {
                $tag_title = trim($tag_title);
                if ($tag_title !== '') {
                    $this->db->insert('tb_ft_tags', [
                        'feature_id' => $feature_id,
                        'tag_title' => $tag_title,
                        'is_active' => 'yes'
                    ]);
                }
            }
        }

        // QAs
        $questions = $this->input->post('questions');
        $answers = $this->input->post('answers');
        if (!empty($questions) && is_array($questions)) {
            $count = count($questions);
            for ($i = 0; $i < $count; $i++) {
                $q = trim($questions[$i]);
                $a = trim($answers[$i]);
                if ($q !== '' && $a !== '') {
                    $this->db->insert('tb_ft_qas', [
                        'feature_id' => $feature_id,
                        'question' => $q,
                        'answer' => $a
                    ]);
                }
            }
        }

        // Benefit Headers
        $header_title = $this->input->post('benefit_header_title');
        $col1_label = $this->input->post('benefit_col1_label');
        $col2_label = $this->input->post('benefit_col2_label');
        if (!empty($header_title) || !empty($col1_label) || !empty($col2_label)) {
            $this->db->insert('tb_ft_benefit_headers', [
                'feature_id' => $feature_id,
                'title_label' => $header_title ?: '',
                'col_1_label' => $col1_label ?: '',
                'col_2_label' => $col2_label ?: ''
            ]);
        }

        // Benefit Comparisons (rows)
        $benefit_titles = $this->input->post('benefit_titles');
        $col1_values = $this->input->post('col1_values');
        $col2_values = $this->input->post('col2_values');
        if (!empty($benefit_titles) && is_array($benefit_titles)) {
            $count = count($benefit_titles);
            for ($i = 0; $i < $count; $i++) {
                $title = trim($benefit_titles[$i]);
                if ($title !== '') {
                    $this->db->insert('tb_ft_benefit_comparisons', [
                        'feature_id' => $feature_id,
                        'benefit_title' => $title,
                        'col_1' => trim($col1_values[$i] ?? ''),
                        'col_2' => trim($col2_values[$i] ?? '')
                    ]);
                }
            }
        }
    }
    
    private function _csrfResponse($response = []) {
        $response['csrf_token'] = [
            'hash' => $this->security->get_csrf_hash(),
            'name' => $this->security->get_csrf_token_name()
        ];
        return $response;
    }
}