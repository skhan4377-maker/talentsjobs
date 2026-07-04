<?php defined('BASEPATH') or exit('No direct script access allowed');
date_default_timezone_set('Asia/Kolkata');

class Dashboard extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->user_id = $this->session->userdata('user_id');
        $this->load->model('employer/Dashboard_mdl');
        $this->load->model('employer/Profile_mdl');
    }

    public function index() {
        $data['title'] = 'Dashboard';

        // Employer details
        $data['employer_details'] = $this->Profile_mdl->get_employer_details($this->user_id);

        // Core stats (active jobs, total applications, new apps, interviews)
        $data['dashboard_stats'] = $this->Dashboard_mdl->get_dashboard_stats($this->user_id);

        // Additional stats (plan, notifications, shortlisted, hired, etc.)
        $data['extra_stats'] = $this->Dashboard_mdl->get_extra_stats($this->user_id);

        // Recent applications
        $data['recent_applications'] = $this->Dashboard_mdl->get_recent_applications($this->user_id);

        // Profile completeness
        $data['profile_completeness'] = $this->Dashboard_mdl->get_profile_completeness($this->user_id);

        // Active jobs
        $data['active_jobs'] = $this->Dashboard_mdl->get_active_jobs($this->user_id);

        // Hiring analytics (for chart)
        $data['hiring_analytics'] = $this->Dashboard_mdl->get_hiring_analytics($this->user_id);

        // Employer status
        $profile = $this->Profile_mdl->get_employer_details($this->user_id);
        $data['status'] = $profile['status'] ?? 'active';

        // Render view
        $data['content'] = $this->load->view('employer/dashboard', $data, TRUE);
        $this->load->view('templates/master', $data);
    }
}