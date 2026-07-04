<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Support extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('admin/Support_model');
        $this->load->library('pagination');
        $this->load->helper(['form', 'url']);
    }

    public function index() {
        $search = $this->input->get('search', true);
        $page = $this->input->get('page') ? (int)$this->input->get('page') : 1;
        $per_page = 10;
        $offset = ($page - 1) * $per_page;

        $total_rows = $this->Support_model->count_filtered($search);

        // Pagination config
        $config['base_url'] = base_url('admin/support');
        $config['total_rows'] = $total_rows;
        $config['per_page'] = $per_page;
        $config['page_query_string'] = TRUE;
        $config['query_string_segment'] = 'page';
        $config['use_page_numbers'] = TRUE;
        $config['reuse_query_string'] = TRUE;

        $config['full_tag_open'] = '<div class="flex justify-center mt-4"><ul class="flex space-x-1">';
        $config['full_tag_close'] = '</ul></div>';
        $config['first_link'] = 'First';
        $config['last_link'] = 'Last';
        $config['prev_link'] = '‹ Prev';
        $config['next_link'] = 'Next ›';
        $config['num_tag_open'] = '<li>';
        $config['num_tag_close'] = '</li>';
        $config['cur_tag_open'] = '<li><span class="bg-blue-600 text-white px-3 py-1 rounded">';
        $config['cur_tag_close'] = '</span></li>';
        $config['next_tag_open'] = '<li>';
        $config['next_tag_close'] = '</li>';
        $config['prev_tag_open'] = '<li>';
        $config['prev_tag_close'] = '</li>';
        $config['first_tag_open'] = '<li>';
        $config['first_tag_close'] = '</li>';
        $config['last_tag_open'] = '<li>';
        $config['last_tag_close'] = '</li>';
        $config['attributes'] = ['class' => 'px-3 py-1 border rounded hover:bg-gray-100'];

        $this->pagination->initialize($config);

        $data['enquiries'] = $this->Support_model->get_paginated($per_page, $offset, $search);
        $data['links'] = $this->pagination->create_links();
        $data['search'] = $search;
        $data['total_rows'] = $total_rows;
        $data['current_offset'] = $offset;
        $data['per_page'] = $per_page;
        $data['title'] = 'Support Enquiries';

        $data['content'] = $this->load->view('admin/support/index', $data, true);
        $this->load->view('templates/master', $data);
    }

    public function get_enquiry($id) {
        $enquiry = $this->Support_model->get_by_id($id);
        if ($enquiry) {
            echo json_encode(['status' => 'success', 'data' => $enquiry]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Enquiry not found']);
        }
    }

    public function reply() {
        $post = $this->input->post();
        if (empty($post['id']) || empty($post['email']) || empty($post['subject']) || empty($post['reply_message'])) {
            $this->output->set_header('X-CSRF-TOKEN: ' . $this->security->get_csrf_hash());
            echo json_encode(['status' => 'error', 'message' => 'Missing required fields.']);
            return;
        }

        $id = (int)$post['id'];
        $to = $post['email'];
        $subject = $post['subject'];
        $message = $post['reply_message'];

        $sent = SendEmailTo($to, $subject, $message);

        if ($sent) {
            $this->db->where('id', $id)->update('tb_support_contacts', [
                'reply_message' => $message,
                'replied_at' => date('Y-m-d H:i:s'),
            ]);
            $this->output->set_header('X-CSRF-TOKEN: ' . $this->security->get_csrf_hash());
            echo json_encode(['status' => 'success', 'message' => 'Reply sent successfully.']);
        } else {
            $this->output->set_header('X-CSRF-TOKEN: ' . $this->security->get_csrf_hash());
            echo json_encode(['status' => 'error', 'message' => 'Failed to send email.']);
        }
    }
}
?>