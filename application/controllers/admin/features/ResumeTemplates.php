<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ResumeTemplates extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('admin/ResumeTemplates_model');
        $this->load->model('admin/Features_model');
       
    }

    // Show all resume templates (Admin List)
    public function index() {
        $data['title']     = 'All Resume Templates';
        $data['templates'] = $this->ResumeTemplates_model->get_all();
        $data['mode']      = 'list';
        $data['content']   = $this->load->view('admin/templates/list_templates', $data, true);
        $this->load->view('templates/master', $data);
    }

    // Show add template form
    public function add() {
        $data['title']      = 'Add Resume Template';
        $data['industries'] = $this->ResumeTemplates_model->get_industries();
        $data['features']   = $this->Features_model->get_all_features();
        $data['mode']       = 'add';

        $data['content'] = $this->load->view('admin/templates/add_template', $data, true);
        $this->load->view('templates/master', $data);
    }

    // Save new template (using model)
	public function save() {
		// Prepare data from POST
		$post_data = $this->input->post();
		
		// Handle file upload first
		$preview_image = '';
		if (!empty($_FILES['preview_image']['name'])) {
			$config['upload_path']   = './uploads/templates/';
			$config['allowed_types'] = 'jpg|jpeg|png|webp';
			$config['max_size']      = 5120; // 5MB
			$this->upload->initialize($config);
			
			if (!$this->upload->do_upload('preview_image')) {
				echo json_encode([
					'success' => false,
					'message' => $this->upload->display_errors(),
					'csrf_token' => $this->security->get_csrf_hash(),      // ← string
					'csrf_name'  => $this->security->get_csrf_token_name() // ← string
				]);
				return;
			} else {
				$upload_data = $this->upload->data();
				$preview_image = 'uploads/templates/' . $upload_data['file_name'];
			}
		}

		// Build data array for model
		$data = [
			'feature_id'       => $post_data['feature_id'] ?? null,
			'name'             => $post_data['name'],
			'layout_type'      => $post_data['layout_type'],
			'industry_id'      => $post_data['industry_id'],
			'html_layout'      => $post_data['html_layout'],
			'layout_config'    => $post_data['layout_config'] ?? null,
			'schema_json'      => $post_data['schema_json'] ?? null,
			'zones_supported'  => $post_data['zones_supported'] ?? null,
			'description'      => $post_data['description'] ?? null,
			'experience_level' => $post_data['experience_level'],
			'template_type'    => $post_data['template_type'],
			'preview_image'    => $preview_image,
			'is_premium'       => isset($post_data['is_premium']) ? 1 : 0,
			'is_active'        => isset($post_data['is_active']) ? 1 : 0
		];

		// Validate required fields
		if (empty($data['name']) || empty($data['layout_type']) || empty($data['experience_level']) || empty($data['template_type'])) {
			echo json_encode([
				'success' => false,
				'message' => 'All required fields must be filled',
				'csrf_token' => $this->security->get_csrf_hash(),
				'csrf_name'  => $this->security->get_csrf_token_name()
			]);
			return;
		}

		// Insert via model
		$insert_id = $this->ResumeTemplates_model->insert($data);
		
		if ($insert_id) {
			echo json_encode([
				'success' => true,
				'message' => 'Template saved successfully.',
				'csrf_token' => $this->security->get_csrf_hash(),
				'csrf_name'  => $this->security->get_csrf_token_name()
			]);
		} else {
			echo json_encode([
				'success' => false,
				'message' => 'Failed to save template. Please try again.',
				'csrf_token' => $this->security->get_csrf_hash(),
				'csrf_name'  => $this->security->get_csrf_token_name()
			]);
		}
	}

    // Edit form
    public function edit($template_id) {
        $template = $this->ResumeTemplates_model->get_by_id($template_id);
        if (!$template) {
            show_404();
        }

        $data['title']      = 'Edit Resume Template';
        $data['template']   = $template;
        $data['industries'] = $this->ResumeTemplates_model->get_industries();
        $data['features']   = $this->Features_model->get_all_features(); // FIXED: added features dropdown
        $data['mode']       = 'edit';
        $data['content']    = $this->load->view('admin/templates/edit_template', $data, true);
        $this->load->view('templates/master', $data);
    }

	public function update() {

    $template_id = $this->input->post('template_id');

    if (!$template_id) {
        echo json_encode([
            'success' => false,
            'message' => 'Template ID is required.',
            'csrf_token' => $this->security->get_csrf_hash(),
            'csrf_name'  => $this->security->get_csrf_token_name()
        ]);
        return;
    }

    $post_data = $this->input->post();

    // =========================
    // FILE UPLOAD
    // =========================
    $preview_image = null;

    if (!empty($_FILES['preview_image']['name'])) {

        $config['upload_path']   = './uploads/templates/';
        $config['allowed_types'] = 'jpg|jpeg|png|webp';
        $config['max_size']      = 5120;

        $this->load->library('upload', $config);

        if (!$this->upload->do_upload('preview_image')) {
            echo json_encode([
                'success' => false,
                'message' => strip_tags($this->upload->display_errors()),
                'csrf_token' => $this->security->get_csrf_hash(),
                'csrf_name'  => $this->security->get_csrf_token_name()
            ]);
            return;
        }

        $upload_data = $this->upload->data();
        $preview_image = 'uploads/templates/' . $upload_data['file_name'];

        // DELETE OLD IMAGE
        $old = $this->ResumeTemplates_model->get_by_id($template_id);
        if (!empty($old['preview_image']) && file_exists(FCPATH . $old['preview_image'])) {
            @unlink(FCPATH . $old['preview_image']);
        }
    }

    // =========================
    // BUILD DATA
    // =========================
    $data = [
        'name'             => $post_data['name'],
        'layout_type'      => $post_data['layout_type'],
        'industry_id'      => $post_data['industry_id'],
        'html_layout'      => $post_data['html_layout'],
        'layout_config'    => $post_data['layout_config'] ?? null,
        'schema_json'      => $post_data['schema_json'] ?? null,
        'zones_supported'  => $post_data['zones_supported'] ?? null,
        'description'      => $post_data['description'] ?? null,
        'experience_level' => $post_data['experience_level'],
        'template_type'    => $post_data['template_type'],
        'is_premium'       => isset($post_data['is_premium']) ? 1 : 0,
        'is_active'        => isset($post_data['is_active']) ? 1 : 0
    ];

    if ($preview_image) {
        $data['preview_image'] = $preview_image;
    }

    // =========================
    // VALIDATION
    // =========================
    if (empty($data['name']) || empty($data['layout_type']) || empty($data['experience_level']) || empty($data['template_type'])) {
        echo json_encode([
            'success' => false,
            'message' => 'All required fields must be filled.',
            'csrf_token' => $this->security->get_csrf_hash(),
            'csrf_name'  => $this->security->get_csrf_token_name()
        ]);
        return;
    }

    // =========================
    // UPDATE
    // =========================
    $updated = $this->ResumeTemplates_model->update($template_id, $data);

    echo json_encode([
        'success' => $updated ? true : false,
        'message' => $updated ? 'Template updated successfully.' : 'No changes made.',
        'csrf_token' => $this->security->get_csrf_hash(),
        'csrf_name'  => $this->security->get_csrf_token_name()
    ]);
}

	// Delete (redirect)
    public function delete($template_id) {
        if (!$template_id || !is_numeric($template_id)) {
            show_404();
        }

        $template = $this->ResumeTemplates_model->get_by_id($template_id);
        if (!$template) {
            show_404();
        }

        // Delete image file
        if (!empty($template['preview_image']) && file_exists(FCPATH . $template['preview_image'])) {
            unlink(FCPATH . $template['preview_image']);
        }

        // Delete record
        $deleted = $this->ResumeTemplates_model->delete($template_id);

        if ($deleted) {
            $this->session->set_flashdata('success', 'Template deleted successfully!');
        } else {
            $this->session->set_flashdata('error', 'Failed to delete the template.');
        }

        redirect('admin/features/ResumeTemplates');
    }

    // AJAX delete
    /*public function ajax_delete() {
        $template_id = $this->input->post('template_id');
        
        if (!$template_id || !is_numeric($template_id)) {
            echo json_encode([
                'success' => false,
                'message' => 'Invalid template ID',
                'csrf_token' => [
                    'name' => $this->security->get_csrf_token_name(),
                    'hash' => $this->security->get_csrf_hash()
                ]
            ]);
            return;
        }

        $template = $this->ResumeTemplates_model->get_by_id($template_id);
        if (!$template) {
            echo json_encode([
                'success' => false,
                'message' => 'Template not found',
                'csrf_token' => [
                    'name' => $this->security->get_csrf_token_name(),
                    'hash' => $this->security->get_csrf_hash()
                ]
            ]);
            return;
        }

        // Delete image
        if (!empty($template['preview_image']) && file_exists(FCPATH . $template['preview_image'])) {
            unlink(FCPATH . $template['preview_image']);
        }

        // Delete record
        $deleted = $this->ResumeTemplates_model->delete($template_id);

        if ($deleted) {
            echo json_encode([
                'success' => true,
                'message' => 'Template deleted successfully!',
                'csrf_token' => [
                    'name' => $this->security->get_csrf_token_name(),
                    'hash' => $this->security->get_csrf_hash()
                ]
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Failed to delete the template.',
                'csrf_token' => [
                    'name' => $this->security->get_csrf_token_name(),
                    'hash' => $this->security->get_csrf_hash()
                ]
            ]);
        }
    }*/
}