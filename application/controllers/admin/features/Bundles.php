<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Bundles extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('admin/Bundles_model');
        $this->load->model('admin/Features_model'); // to get all features
    }

    public function index() {
        $data['bundles'] = $this->Bundles_model->get_all_bundles();
        $data['features'] = $this->Features_model->get_all_features();
        $data['title'] = 'Manage Bundles';
        $data['content'] = $this->load->view('admin/bundles/manage_bundles', $data, true);
        $this->load->view('templates/master', $data);
    }

    // AJAX: Get single bundle for editing
    public function get_bundle($id) {
        $bundle = $this->Bundles_model->get_bundle_by_id($id);
        if ($bundle) {
            $bundle['features'] = $this->Bundles_model->get_bundle_features($id);
            echo json_encode([
                'success' => true,
                'data' => $bundle,
                'csrf_token' => $this->_csrf_response()
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Bundle not found',
                'csrf_token' => $this->_csrf_response()
            ]);
        }
    }

    // AJAX: Add new bundle
    public function add_bundle() {
        $this->form_validation->set_rules('bundle_name', 'Bundle Name', 'required|trim');
        $this->form_validation->set_rules('bundle_description', 'Description', 'trim');
        $this->form_validation->set_rules('is_active', 'Status', 'required|in_list[0,1]');
        $this->form_validation->set_rules('features[]', 'Features', 'required'); // at least one feature

        if ($this->form_validation->run() == FALSE) {
            echo json_encode([
                'success' => false,
                'message' => validation_errors(),
                'csrf_token' => $this->_csrf_response()
            ]);
            return;
        }

        $slug = url_title($this->input->post('bundle_name'), '-', true);
        if ($this->Bundles_model->slug_exists($slug)) {
            echo json_encode([
                'success' => false,
                'message' => 'Bundle slug already exists. Please change the name.',
                'csrf_token' => $this->_csrf_response()
            ]);
            return;
        }

        $data = [
            'bundle_name'        => $this->input->post('bundle_name'),
            'bundle_slug'        => $slug,
            'bundle_description' => $this->input->post('bundle_description'),
            'is_active'          => $this->input->post('is_active'),
            'created_at'         => date('Y-m-d H:i:s')
        ];

        $bundle_id = $this->Bundles_model->insert_bundle($data);
        if ($bundle_id) {
            $feature_ids = $this->input->post('features');
            $this->Bundles_model->assign_features($bundle_id, $feature_ids);
            echo json_encode([
                'success' => true,
                'message' => 'Bundle added successfully',
                'csrf_token' => $this->_csrf_response()
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Database error',
                'csrf_token' => $this->_csrf_response()
            ]);
        }
    }

    // AJAX: Update bundle
    public function update_bundle() {
        $id = $this->input->post('bundle_id');
        if (!$id) {
            echo json_encode([
                'success' => false,
                'message' => 'Bundle ID required',
                'csrf_token' => $this->_csrf_response()
            ]);
            return;
        }

        $this->form_validation->set_rules('bundle_name', 'Bundle Name', 'required|trim');
        $this->form_validation->set_rules('bundle_description', 'Description', 'trim');
        $this->form_validation->set_rules('is_active', 'Status', 'required|in_list[0,1]');
        $this->form_validation->set_rules('features[]', 'Features', 'required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode([
                'success' => false,
                'message' => validation_errors(),
                'csrf_token' => $this->_csrf_response()
            ]);
            return;
        }

        $slug = url_title($this->input->post('bundle_name'), '-', true);
        if ($this->Bundles_model->slug_exists($slug, $id)) {
            echo json_encode([
                'success' => false,
                'message' => 'Bundle slug already exists. Please change the name.',
                'csrf_token' => $this->_csrf_response()
            ]);
            return;
        }

        $data = [
            'bundle_name'        => $this->input->post('bundle_name'),
            'bundle_slug'        => $slug,
            'bundle_description' => $this->input->post('bundle_description'),
            'is_active'          => $this->input->post('is_active')
        ];

        $updated = $this->Bundles_model->update_bundle($id, $data);
        if ($updated !== false) {
            $feature_ids = $this->input->post('features');
            $this->Bundles_model->assign_features($id, $feature_ids);
            echo json_encode([
                'success' => true,
                'message' => 'Bundle updated successfully',
                'csrf_token' => $this->_csrf_response()
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Update failed',
                'csrf_token' => $this->_csrf_response()
            ]);
        }
    }

    // AJAX: Delete bundle
    public function delete_bundle() {
        $id = $this->input->post('id');
        if (!$id) {
            echo json_encode([
                'success' => false,
                'message' => 'Bundle ID required',
                'csrf_token' => $this->_csrf_response()
            ]);
            return;
        }

        $deleted = $this->Bundles_model->delete_bundle($id);
        if ($deleted) {
            echo json_encode([
                'success' => true,
                'message' => 'Bundle deleted successfully',
                'csrf_token' => $this->_csrf_response()
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Delete failed',
                'csrf_token' => $this->_csrf_response()
            ]);
        }
    }

    // Helper for CSRF response
    private function _csrf_response() {
        return [
            'name' => $this->security->get_csrf_token_name(),
            'hash' => $this->security->get_csrf_hash()
        ];
    }
}
?>