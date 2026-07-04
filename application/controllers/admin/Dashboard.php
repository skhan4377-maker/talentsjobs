<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('admin/Dashboard_model');
    }

    public function index() {
        $data['title'] = 'Admin Dashboard';
        $data['content'] = $this->load->view('admin/dashboard', $data, TRUE);
        $this->load->view('templates/master', $data);
    }

    public function ajax_dashboard_stats() {
        if (!$this->input->is_ajax_request()) show_404();
        
        $start_date = $this->input->get('start_date') ?: date('Y-m-d', strtotime('-30 days'));
        $end_date   = $this->input->get('end_date') ?: date('Y-m-d');
        
        $data = [
            'employers'        => $this->Dashboard_model->get_employer_growth_stats($start_date, $end_date),
            'candidates'       => $this->Dashboard_model->get_candidate_growth_stats($start_date, $end_date),
            'active_jobs'      => $this->Dashboard_model->get_job_growth_stats($start_date, $end_date),
            'applications'     => $this->Dashboard_model->get_application_growth_stats($start_date, $end_date),
            'on_hold_jobs'     => $this->Dashboard_model->get_jobs_on_hold(),
            'draft_jobs'       => $this->Dashboard_model->get_draft_jobs(),
            'support_contacts' => $this->Dashboard_model->get_support_contact_stats(),
            'todayStats'       => $this->Dashboard_model->get_selected_range_stats($start_date, $end_date),
            'blog_stats'       => $this->Dashboard_model->get_blog_stats(),
            'additional_stats' => $this->Dashboard_model->get_additional_stats($start_date, $end_date),
        ];
        
        echo json_encode(['status' => 'success', 'data' => $data]);
    }

    public function ajax_chart_data() {
        if (!$this->input->is_ajax_request()) show_404();
        
        $start_date = $this->input->get('start_date') ?: date('Y-m-d', strtotime('-30 days'));
        $end_date   = $this->input->get('end_date') ?: date('Y-m-d');
        
        $data = [
            'registration' => $this->Dashboard_model->get_registration_chart_data($start_date, $end_date),
            'application'  => $this->Dashboard_model->get_application_chart_data($start_date, $end_date)
        ];
        
        echo json_encode(['status' => 'success', 'data' => $data]);
    }

    public function ajax_pending_employers() {
        if (!$this->input->is_ajax_request()) show_404();
        $list = $this->Dashboard_model->get_pending_employers(5);
        echo json_encode(['status' => 'success', 'data' => $list]);
    }

    public function ajax_recent_candidates() {
        if (!$this->input->is_ajax_request()) show_404();
        $list = $this->Dashboard_model->get_recent_users(5);
        echo json_encode(['status' => 'success', 'data' => $list]);
    }

    public function approve_employer() {
        if (!$this->input->is_ajax_request()) show_404();
        $employer_id = $this->input->post('employer_id');
        $result = $this->Dashboard_model->update_employer_status($employer_id, 'active');
        echo json_encode(['status' => $result ? 'success' : 'error']);
    }

    public function reject_employer() {
        if (!$this->input->is_ajax_request()) show_404();
        $employer_id = $this->input->post('employer_id');
        $reason = $this->input->post('reason');
        $result = $this->Dashboard_model->update_employer_status($employer_id, 'rejected', $reason);
        echo json_encode(['status' => $result ? 'success' : 'error']);
    }
}
?>