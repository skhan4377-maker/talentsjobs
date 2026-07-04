<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Bio extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Jobs/Bio_model');
        $this->load->library('form_validation');
        $this->load->helper('url');
        $this->load->helper('html');
    }

    // --------------------------------------------------------------------
    //  Main listing page with server-side pagination
    // --------------------------------------------------------------------
    public function manage() {
        // Pagination configuration
        $per_page = $this->input->get('per_page') ? (int)$this->input->get('per_page') : 10;
        $page = $this->input->get('page') ? (int)$this->input->get('page') : 1;
        $offset = ($page - 1) * $per_page;

        // Get total records
        $total = $this->Bio_model->count_all();

        // Get paginated jobs
        $jobs = $this->Bio_model->get_paginated($per_page, $offset);

        // Generate pagination links using CodeIgniter's Pagination library
        $this->load->library('pagination');
        $config['base_url'] = base_url('bio/manage');
        $config['total_rows'] = $total;
        $config['per_page'] = $per_page;
        $config['use_page_numbers'] = TRUE;
        $config['page_query_string'] = TRUE;
        $config['query_string_segment'] = 'page';
        $config['reuse_query_string'] = TRUE;
        $config['full_tag_open'] = '<ul class="pagination">';
        $config['full_tag_close'] = '</ul>';
        $config['attributes'] = ['class' => 'page-link'];
        $config['first_tag_open'] = '<li class="page-item">';
        $config['first_tag_close'] = '</li>';
        $config['last_tag_open'] = '<li class="page-item">';
        $config['last_tag_close'] = '</li>';
        $config['next_tag_open'] = '<li class="page-item">';
        $config['next_tag_close'] = '</li>';
        $config['prev_tag_open'] = '<li class="page-item">';
        $config['prev_tag_close'] = '</li>';
        $config['cur_tag_open'] = '<li class="page-item active"><span class="page-link">';
        $config['cur_tag_close'] = '</span></li>';
        $config['num_tag_open'] = '<li class="page-item">';
        $config['num_tag_close'] = '</li>';

        $this->pagination->initialize($config);
        $pagination_links = $this->pagination->create_links();

        $data['title'] = 'Job Posts';
        $data['jobs'] = $jobs;
        $data['total'] = $total;
        $data['per_page'] = $per_page;
        $data['current_page'] = $page;
        $data['pagination_links'] = $pagination_links;
        $data['content'] = $this->load->view('admin/bio/bio_list', $data, true);
        $this->load->view('templates/master', $data);
    }

    // --------------------------------------------------------------------
    //  AJAX: Create a new job (unchanged, but CSRF remains)
    // --------------------------------------------------------------------
    public function store_ajax() {
        $this->form_validation->set_rules('title', 'Title', 'required');
        $this->form_validation->set_rules('content', 'Content', 'required');

        if ($this->form_validation->run() == FALSE) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status'  => 'error',
                    'message' => strip_tags(validation_errors())
                ]));
        } else {
            $title = $this->input->post('title', TRUE);
            $base_slug = url_title($title, 'dash', TRUE);
            $slug = $this->_unique_slug($base_slug);

            $data = [
                'title'        => $title,
                'slug'         => $slug,
                'content'      => $this->input->post('content', FALSE),
                'external_url' => $this->input->post('external_url', TRUE)
            ];

            $insert_id = $this->Bio_model->insert($data);
            if ($insert_id) {
                $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode([
                        'status'     => 'success',
                        'message'    => 'Job created successfully',
                        'csrf_token' => $this->security->get_csrf_hash()
                    ]));
            } else {
                $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode([
                        'status'  => 'error',
                        'message' => 'Failed to create job'
                    ]));
            }
        }
    }

    // --------------------------------------------------------------------
    //  AJAX: Update a job
    // --------------------------------------------------------------------
    public function update_ajax() {
        $id = $this->input->post('id');
        $this->form_validation->set_rules('title', 'Title', 'required');
        $this->form_validation->set_rules('content', 'Content', 'required');

        if ($this->form_validation->run() == FALSE) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status'  => 'error',
                    'message' => strip_tags(validation_errors())
                ]));
        } else {
            $title = $this->input->post('title', TRUE);
            $base_slug = url_title($title, 'dash', TRUE);
            $slug = $this->_unique_slug($base_slug, $id);

            $data = [
                'title'        => $title,
                'slug'         => $slug,
                'content'      => $this->input->post('content', FALSE),
                'external_url' => $this->input->post('external_url', TRUE)
            ];

            $updated = $this->Bio_model->update($id, $data);
            if ($updated) {
                $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode([
                        'status'     => 'success',
                        'message'    => 'Job updated successfully',
                        'csrf_token' => $this->security->get_csrf_hash()
                    ]));
            } else {
                $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode([
                        'status'  => 'error',
                        'message' => 'Failed to update job'
                    ]));
            }
        }
    }

    // --------------------------------------------------------------------
    //  AJAX: Delete a job
    // --------------------------------------------------------------------
    public function delete_ajax() {
        $id = $this->input->post('id');
        $deleted = $this->Bio_model->delete($id);
        if ($deleted) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status'     => 'success',
                    'message'    => 'Job deleted',
                    'csrf_token' => $this->security->get_csrf_hash()
                ]));
        } else {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status'  => 'error',
                    'message' => 'Delete failed'
                ]));
        }
    }

    // --------------------------------------------------------------------
    //  Private method: Ensure slug uniqueness
    // --------------------------------------------------------------------
    private function _unique_slug($base_slug, $exclude_id = 0) {
        $slug = $base_slug;
        $counter = 1;
        while ($this->Bio_model->slug_exists($slug, $exclude_id)) {
            $slug = $base_slug . '-' . $counter++;
        }
        return $slug;
    }
}