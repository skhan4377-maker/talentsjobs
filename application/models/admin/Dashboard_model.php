<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    // ---------- Helper for previous period ----------
    private function get_previous_period($start_date, $end_date) {
        $days = (strtotime($end_date) - strtotime($start_date)) / 86400;
        $prev_start = date('Y-m-d', strtotime($start_date . " -$days days"));
        $prev_end   = date('Y-m-d', strtotime($start_date . ' -1 day'));
        return ['start' => $prev_start, 'end' => $prev_end];
    }

    // ---------- Employer stats ----------
    public function get_employer_growth_stats($start_date, $end_date) {
        $prev = $this->get_previous_period($start_date, $end_date);
        
        $current = $this->db->where('is_deleted', 0)
            ->where('created_at >=', $start_date . ' 00:00:00')
            ->where('created_at <=', $end_date . ' 23:59:59')
            ->count_all_results('tb_employer');
            
        $previous = $this->db->where('is_deleted', 0)
            ->where('created_at >=', $prev['start'] . ' 00:00:00')
            ->where('created_at <=', $prev['end'] . ' 23:59:59')
            ->count_all_results('tb_employer');
            
        $total = $this->db->where('is_deleted', 0)->count_all_results('tb_employer');
        
        return ['current' => $current, 'previous' => $previous, 'total' => $total];
    }

    // ---------- Candidate stats ----------
    public function get_candidate_growth_stats($start_date, $end_date) {
        $prev = $this->get_previous_period($start_date, $end_date);
        
        $current = $this->db->where('is_deleted', 0)
            ->where('created_at >=', $start_date . ' 00:00:00')
            ->where('created_at <=', $end_date . ' 23:59:59')
            ->count_all_results('tb_candidate');
            
        $previous = $this->db->where('is_deleted', 0)
            ->where('created_at >=', $prev['start'] . ' 00:00:00')
            ->where('created_at <=', $prev['end'] . ' 23:59:59')
            ->count_all_results('tb_candidate');
            
        $total = $this->db->where('is_deleted', 0)->count_all_results('tb_candidate');
        
        return ['current' => $current, 'previous' => $previous, 'total' => $total];
    }

    // ---------- Active jobs stats ----------
    public function get_job_growth_stats($start_date, $end_date) {
        $prev = $this->get_previous_period($start_date, $end_date);
        
        $current = $this->db->where('status', 'active')
            ->where('is_deleted', 0)
            ->where('created_at >=', $start_date . ' 00:00:00')
            ->where('created_at <=', $end_date . ' 23:59:59')
            ->count_all_results('tb_post_job');
            
        $previous = $this->db->where('status', 'active')
            ->where('is_deleted', 0)
            ->where('created_at >=', $prev['start'] . ' 00:00:00')
            ->where('created_at <=', $prev['end'] . ' 23:59:59')
            ->count_all_results('tb_post_job');
            
        $total = $this->db->where('status', 'active')
            ->where('is_deleted', 0)
            ->count_all_results('tb_post_job');
            
        return ['current' => $current, 'previous' => $previous, 'total' => $total];
    }

    // ---------- Applications stats ----------
    public function get_application_growth_stats($start_date, $end_date) {
        $prev = $this->get_previous_period($start_date, $end_date);
        
        $current = $this->db->where('created_at >=', $start_date . ' 00:00:00')
            ->where('created_at <=', $end_date . ' 23:59:59')
            ->count_all_results('tb_applied');
            
        $previous = $this->db->where('created_at >=', $prev['start'] . ' 00:00:00')
            ->where('created_at <=', $prev['end'] . ' 23:59:59')
            ->count_all_results('tb_applied');
            
        $total = $this->db->count_all('tb_applied');
        
        return ['current' => $current, 'previous' => $previous, 'total' => $total];
    }

    // ---------- On Hold jobs ----------
    public function get_jobs_on_hold() {
        return $this->db->where('status', 'on_hold')
            ->where('is_deleted', 0)
            ->count_all_results('tb_post_job');
    }

    // ---------- Draft jobs ----------
    public function get_draft_jobs() {
        return $this->db->where('status', 'draft')
            ->where('is_deleted', 0)
            ->count_all_results('tb_post_job');
    }

    // ---------- Support contacts ----------
    public function get_support_contact_stats() {
        $total = $this->db->count_all('tb_support_contacts');
        $today = $this->db->where('DATE(submitted_at)', date('Y-m-d'))->count_all_results('tb_support_contacts');
        return ['total' => $total, 'today' => $today];
    }

    // ---------- Selected range stats (posted jobs & applications) ----------
    public function get_selected_range_stats($start_date, $end_date) {
        $posted_jobs = $this->db->where('created_at >=', $start_date . ' 00:00:00')
            ->where('created_at <=', $end_date . ' 23:59:59')
            ->where('is_deleted', 0)
            ->count_all_results('tb_post_job');
            
        $applications = $this->db->where('created_at >=', $start_date . ' 00:00:00')
            ->where('created_at <=', $end_date . ' 23:59:59')
            ->count_all_results('tb_applied');
            
        return ['posted_jobs' => $posted_jobs, 'applications' => $applications];
    }

    // ---------- Blog stats ----------
    public function get_blog_stats() {
        $total = $this->db->count_all('tb_blogs');
        $published = $this->db->where('blogs_status', 1)->count_all_results('tb_blogs');
        $draft = $this->db->where('blogs_status', 0)->count_all_results('tb_blogs');
        return ['total' => $total, 'published' => $published, 'draft' => $draft];
    }

    // ---------- Additional stats (revenue) ----------
    public function get_additional_stats($start_date, $end_date) {
        $this->db->select_sum('amount');
        $this->db->where('created_at >=', $start_date . ' 00:00:00');
        $this->db->where('created_at <=', $end_date . ' 23:59:59');
        $this->db->where('status', 'captured');
        $revenue = $this->db->get('tb_payments')->row()->amount ?? 0;
        return ['payment_revenue' => (float)$revenue];
    }

    // ---------- Chart: Registration trends ----------
    public function get_registration_chart_data($start_date, $end_date) {
        $period = new DatePeriod(
            new DateTime($start_date),
            new DateInterval('P1D'),
            (new DateTime($end_date))->modify('+1 day')
        );
        $labels = [];
        $candidates_data = [];
        $employers_data = [];
        foreach ($period as $date) {
            $cur = $date->format('Y-m-d');
            $labels[] = $date->format('M j');
            $candidates_data[] = $this->db->where('DATE(created_at)', $cur)->where('is_deleted', 0)->count_all_results('tb_candidate');
            $employers_data[] = $this->db->where('DATE(created_at)', $cur)->where('is_deleted', 0)->count_all_results('tb_employer');
        }
        return ['labels' => $labels, 'candidates' => $candidates_data, 'employers' => $employers_data];
    }

    // ---------- Chart: Application stage distribution ----------
    public function get_application_chart_data($start_date, $end_date) {
        $stages = ['Applied', 'Under Review', 'Hired', 'Rejected'];
        $data = [];
        foreach ($stages as $stage) {
            $count = $this->db->where('ApplicationStage', $stage)
                ->where('created_at >=', $start_date . ' 00:00:00')
                ->where('created_at <=', $end_date . ' 23:59:59')
                ->count_all_results('tb_applied');
            $data[] = $count;
        }
        return ['labels' => $stages, 'data' => $data];
    }

    // ---------- Pending employers (under_review) ----------
    public function get_pending_employers($limit = 5) {
        return $this->db->select('employer_id, company_name, name, created_at')
            ->where('status', 'under_review')
            ->where('is_deleted', 0)
            ->order_by('created_at', 'DESC')
            ->limit($limit)
            ->get('tb_employer')
            ->result();
    }

    // ---------- Recent candidates ----------
    public function get_recent_users($limit = 5) {
        return $this->db->select('candidate_id, name, last_name, email, created_at')
            ->order_by('created_at', 'DESC')
            ->limit($limit)
            ->get('tb_candidate')
            ->result();
    }

    // ---------- Update employer status ----------
    public function update_employer_status($employer_id, $status, $rejection_reason = null) {
        $data = ['status' => $status];
        if ($rejection_reason) {
            $data['rejection_reason'] = $rejection_reason;
        }
        $this->db->where('employer_id', $employer_id);
        return $this->db->update('tb_employer', $data);
    }
}
?>