<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class AdminApplications extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('admin/AdminApplications_model');
        $this->load->library('pagination');
        $this->load->helper(['form', 'url']);
    }

    public function applications() {
        // Get filter values from GET
        $filters = [
            'candidate_name' => $this->input->get('candidate_name', true),
            'job_title'      => $this->input->get('job_title', true),
            'status'         => $this->input->get('status', true),
            'applied_from'   => $this->input->get('applied_from', true),
            'applied_to'     => $this->input->get('applied_to', true)
        ];

        // Total records after applying filters
        $total_rows = $this->AdminApplications_model->count_filtered_applications($filters);

        // Pagination configuration
        $config['base_url']          = base_url('admin/jobs/AdminApplications/applications');
        $config['total_rows']        = $total_rows;
        $config['per_page']          = 10;
        $config['uri_segment']       = 5; // URL: /admin/jobs/AdminApplications/applications/10
        $config['reuse_query_string'] = true; // keep GET parameters (filters)

        // Styling – Tailwind CSS friendly (adjust classes as needed)
        $config['full_tag_open']   = '<div class="flex justify-center mt-4"><ul class="flex space-x-1">';
        $config['full_tag_close']  = '</ul></div>';
        $config['first_link']      = 'First';
        $config['last_link']       = 'Last';
        $config['prev_link']       = '‹ Prev';
        $config['next_link']       = 'Next ›';
        $config['num_tag_open']    = '<li>';
        $config['num_tag_close']   = '</li>';
        $config['cur_tag_open']    = '<li><span class="bg-blue-600 text-white px-3 py-1 rounded">';
        $config['cur_tag_close']   = '</span></li>';
        $config['next_tag_open']   = '<li>';
        $config['next_tag_close']  = '</li>';
        $config['prev_tag_open']   = '<li>';
        $config['prev_tag_close']  = '</li>';
        $config['first_tag_open']  = '<li>';
        $config['first_tag_close'] = '</li>';
        $config['last_tag_open']   = '<li>';
        $config['last_tag_close']  = '</li>';
        $config['attributes']      = ['class' => 'px-3 py-1 border rounded hover:bg-gray-100'];

        $this->pagination->initialize($config);

        $offset = ($this->uri->segment(5)) ? (int)$this->uri->segment(5) : 0;
        $data['applications'] = $this->AdminApplications_model->get_applications_paginated($config['per_page'], $offset, $filters);
        $data['links'] = $this->pagination->create_links();
        $data['filters'] = $filters;
        $data['total_rows'] = $total_rows;
        $data['current_offset'] = $offset;
        $data['per_page'] = $config['per_page'];
        $data['title'] = 'Applications';

        $data['content'] = $this->load->view('admin/jobs/applications_view', $data, true);
        $this->load->view('templates/master', $data);
    }

    // AJAX: View application details (unchanged, but used in modal)
    public function view($applied_id) {
        $this->load->model('employer/Application_model');
        $data = $this->AdminApplications_model->get_application_details($applied_id);
        if (!$data) {
            echo json_encode(['status' => 'error']);
            return;
        }
        $data = (array) $data;
        $logs = $this->Application_model->get_application_timeline($applied_id);
        $data['logs'] = $logs;

        echo json_encode(['status' => 'success', 'data' => $data]);
    }
}
?>