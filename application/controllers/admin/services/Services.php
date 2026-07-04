<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Services extends MY_Controller {

    public function __construct() {
        parent::__construct();       
        $this->load->model('admin/Service_model');
    }

    public function index() {
        $data['services'] = $this->Service_model->get_all_services();
        $data['title'] = 'Manage Services';
        $data['content'] = $this->load->view('admin/services/manage-services', $data, true);
        $this->load->view('templates/master', $data);
    }

    // Helper to return CSRF token in expected format
    private function _csrf_response() {
        return [
            'name' => $this->security->get_csrf_token_name(),
            'hash' => $this->security->get_csrf_hash()
        ];
    }

    // AJAX: Fetch single service for editing
    public function get_service($id) {
        $service = $this->Service_model->get_service_by_id($id);
        if ($service) {
            echo json_encode([
                'success' => true,
                'data' => $service,
                'csrf_token' => $this->_csrf_response()
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Service not found',
                'csrf_token' => $this->_csrf_response()
            ]);
        }
    }

    // AJAX: Add new service
    public function add_service() {
        $this->form_validation->set_rules('service_name', 'Service Name', 'trim|required');
        $this->form_validation->set_rules('service_description', 'Description', 'trim|required');
        $this->form_validation->set_rules('is_active', 'Status', 'trim|required|in_list[0,1]');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode([
                'success' => false,
                'message' => validation_errors(),
                'csrf_token' => $this->_csrf_response()
            ]);
            return;
        }

        // File upload
        $upload = $this->_upload_icon('service_icon');
        if (!$upload['status']) {
            echo json_encode([
                'success' => false,
                'message' => $upload['message'],
                'csrf_token' => $this->_csrf_response()
            ]);
            return;
        }

        $data = [
            'service_name'        => $this->input->post('service_name'),
            'slug'                => url_title($this->input->post('service_name'), '-', true),
            'service_description' => $this->input->post('service_description'),
            'service_icon'        => $upload['path'],
            'is_active'           => $this->input->post('is_active'),
            'create_dt'           => date('Y-m-d H:i:s')
        ];

        $insert_id = $this->Service_model->insert_service($data);
        if ($insert_id) {
            echo json_encode([
                'success' => true,
                'message' => 'Service added successfully',
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

    // AJAX: Update service
    public function update_service() {
        $id = $this->input->post('service_id');
        if (!$id) {
            echo json_encode([
                'success' => false,
                'message' => 'Service ID required',
                'csrf_token' => $this->_csrf_response()
            ]);
            return;
        }

        $this->form_validation->set_rules('service_name', 'Service Name', 'trim|required');
        $this->form_validation->set_rules('service_description', 'Description', 'trim|required');
        $this->form_validation->set_rules('is_active', 'Status', 'trim|required|in_list[0,1]');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode([
                'success' => false,
                'message' => validation_errors(),
                'csrf_token' => $this->_csrf_response()
            ]);
            return;
        }

        $data = [
            'service_name'        => $this->input->post('service_name'),
            'slug'                => url_title($this->input->post('service_name'), '-', true),
            'service_description' => $this->input->post('service_description'),
            'is_active'           => $this->input->post('is_active'),
            'update_dt'           => date('Y-m-d H:i:s')
        ];

        // Handle icon upload if new file provided
        if (!empty($_FILES['service_icon']['name'])) {
            $upload = $this->_upload_icon('service_icon');
            if (!$upload['status']) {
                echo json_encode([
                    'success' => false,
                    'message' => $upload['message'],
                    'csrf_token' => $this->_csrf_response()
                ]);
                return;
            }
            $data['service_icon'] = $upload['path'];

            // Delete old icon
            $old = $this->Service_model->get_service_by_id($id);
            if ($old && !empty($old['service_icon']) && file_exists($old['service_icon'])) {
                @unlink($old['service_icon']);
            }
        }

        $updated = $this->Service_model->update_service($id, $data);
        if ($updated) {
            echo json_encode([
                'success' => true,
                'message' => 'Service updated successfully',
                'csrf_token' => $this->_csrf_response()
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'No changes or update failed',
                'csrf_token' => $this->_csrf_response()
            ]);
        }
    }

    // AJAX: Toggle status
    public function toggle_service_status() {
        $id = $this->input->post('id');
        if (!$id) {
            echo json_encode([
                'success' => false,
                'message' => 'ID required',
                'csrf_token' => $this->_csrf_response()
            ]);
            return;
        }

        $result = $this->Service_model->toggle_status($id);
        if ($result) {
            $service = $this->Service_model->get_service_by_id($id);
            echo json_encode([
                'success' => true,
                'new_status' => $service['is_active'],
                'csrf_token' => $this->_csrf_response()
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Toggle failed',
                'csrf_token' => $this->_csrf_response()
            ]);
        }
    }

    // Private helper for icon upload
    private function _upload_icon($field) {
        $config['upload_path']   = './uploads/services/';
        $config['allowed_types'] = 'gif|jpg|png|svg';
        $config['max_size']      = 2048; // 2MB
        $config['encrypt_name']  = true;

        if (!is_dir($config['upload_path'])) {
            mkdir($config['upload_path'], 0777, true);
        }

        $this->load->library('upload', $config);

        if (!$this->upload->do_upload($field)) {
            return ['status' => false, 'message' => $this->upload->display_errors()];
        } else {
            $data = $this->upload->data();
            return ['status' => true, 'path' => 'uploads/services/' . $data['file_name']];
        }
    }
}