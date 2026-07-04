<?php defined('BASEPATH') or exit('No direct script access allowed');

class Dashboard extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('candidate/Dashboard_mdl');
        $this->load->model('candidate/Profile_mdl');
        $this->load->model('Jobs/Jobs_model');
        $this->load->model('blog/Blog_mdl');
        $this->user_id = $this->session->userdata('user_id');
        $this->name    = $this->session->userdata('name');
    }

    public function index() {
        $data['title'] = 'Dashboard';
        $candidate = $this->Profile_mdl->get_candidate_details($this->user_id);

        // Dashboard stats
        $data['dashboard_counts'] = $this->Dashboard_mdl->dashboard_count_activity($this->user_id);
        $data['profile_completeness'] = $this->Dashboard_mdl->get_profile_completeness($this->user_id);
        $data['is_active'] = ($candidate['status'] === 'active');

        // Recommended jobs (personalized)
        $data['mightBeLike'] = $this->Jobs_model->getRecommendedJobs($candidate['designations']);

        // Top hiring companies
        $data['companies'] = $this->Jobs_model->get_top_companies(10);

        // Active blogs
        $data['blogs'] = $this->Blog_mdl->get_blogs(['limit' => 10, 'status' => 1]);

        // Additional stats
        $data['additional_stats'] = $this->Dashboard_mdl->get_additional_stats($this->user_id);
		
		// Fetch active premium plan for banner
		$this->load->model('candidate/Candidate_plan_model');
		$active_plan = $this->Candidate_plan_model->get_active_plan($this->user_id);

		// Inside Dashboard::index() after fetching $active_plan
		if ($active_plan) {
			$end = new DateTime($active_plan['end_date']);
			$now = new DateTime();
			$active_plan['days_remaining'] = $now->diff($end)->days;
			$active_plan['end_date_formatted'] = date('d F Y', strtotime($active_plan['end_date']));
			
			// For live countdown timer if expiring within 7 days
			if ($active_plan['days_remaining'] <= 7) {
				$active_plan['expiry_timestamp'] = $end->getTimestamp() * 1000; // JS milliseconds
			}
		}
		$data['active_plan'] = $active_plan;
		
        // Render view
        $data['content'] = $this->load->view('candidate/dashboard', $data, TRUE);
        $this->load->view('templates/master', $data);
    }

    // AJAX endpoint for live counts (optional, already used in view)
    public function ajaxGetDashboardCounts()
    {
        $data = $this->Dashboard_mdl->dashboard_count_activity($this->user_id);
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }
}