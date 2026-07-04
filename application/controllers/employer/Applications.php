<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Applications extends MY_Controller {

    public function __construct() {
        parent::__construct();	
        $this->load->model('employer/Application_model');  
		$this->load->model('employer/PostJobModel');			
        $this->user_id = $this->session->userdata('user_id');
    }

    public function index() {
		$this->load->model('employer/Profile_mdl', 'Profile_mdl');	
		$profile = $this->Profile_mdl->get_employer_details($this->user_id);	
		$status = isset($profile['status']) ? strtolower($profile['status']) : 'inactive';

		$this->load->model('Application_model');

		// 🔍 Filters
		$search     = trim($this->input->get('search'));
		$job_id     = $this->input->get('job_id');
		$status_f   = $this->input->get('status');
		$start_date = $this->input->get('start_date');
		$end_date   = $this->input->get('end_date');

		$per_page = 10;
		$page     = max(1, (int) $this->input->get('page'));
		$offset   = ($page - 1) * $per_page;

		// 📊 Count: total applications (unfiltered)
		$total_all = $this->Application_model->count_applications($this->user_id);

		// 📊 Count: filtered applications (based on current filters)
		$total_filtered = $this->Application_model->count_applications(
			$this->user_id,
			$job_id,
			$search,
			$status_f,
			$start_date,
			$end_date
		);

		// 4) Configure pagination
		$config = [
			'base_url'             => base_url('employer/applications'),
			'first_url'            => base_url('employer/applications') . '?page=1'
									 . ($search ? '&search=' . urlencode($search) : ''),
			'per_page'             => $per_page,
			'total_rows'           => $total_filtered,
			'page_query_string'    => TRUE,
			'use_page_numbers'     => TRUE,
			'query_string_segment' => 'page',
			'reuse_query_string'   => TRUE,
			'attributes'           => ['class' => 'px-4 py-2 bg-white dark:bg-gray-700 border dark:border-gray-600 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors text-base'],
			'full_tag_open'        => '<nav class="my-8"><ul class="flex flex-wrap items-center gap-2 text-sm">',
			'full_tag_close'       => '</ul></nav>',
			'first_link'           => FALSE,
			'last_link'            => FALSE,
			'cur_tag_open'         => '<span class="px-4 py-2 bg-blue-600 text-white rounded-lg">',
			'cur_tag_close'        => '</span>',
			'num_tag_open'         => '<li>',
			'num_tag_close'        => '</li>',
			'prev_tag_open'        => '<li>',
			'prev_tag_close'       => '</li>',
			'next_tag_open'        => '<li>',
			'next_tag_close'       => '</li>',
			'prev_link'            => '<i class="fas fa-chevron-left"></i>',
			'next_link'            => '<i class="fas fa-chevron-right"></i>',
		];
		$this->pagination->initialize($config);

		// Page / offset again after pagination
		$page   = (int) $this->input->get('page');
		$page   = ($page >= 1 ? $page : 1);
		$offset = ($page - 1) * $per_page;

		// 📥 Applications with pagination and filters
		$apps = $this->Application_model->get_applications(
			$this->user_id,
			$job_id,
			$per_page,
			$offset,
			$search,
			$status_f,
			$start_date,
			$end_date
		);

		// ✅ NEW: Extract candidate IDs and fetch active features
		$candidateIds = [];
		foreach ($apps as $app) {
			if (!empty($app['candidate_id'])) {
				$candidateIds[] = $app['candidate_id'];
			}
		}
		$activeFeatures = $this->Application_model->get_active_features_for_candidates(array_unique($candidateIds));

		// Attach active_features to each application
		foreach ($apps as &$app) {
			$cid = $app['candidate_id'] ?? null;
			$app['active_features'] = $activeFeatures[$cid] ?? [];
		}
		unset($app); // break reference

		// 📦 Data for view
		$data = [
			'applications'     => $apps,
			'links'            => $this->pagination->create_links(),
			'title'            => 'All Applications',
			'search'           => $search,
			'status'           => $status,
			'job_id'           => $job_id,
			'status_filter'    => $status_f,
			'start_date'       => $start_date,
			'end_date'         => $end_date,
			'total_all'        => $total_all,
			'total_filtered'   => $total_filtered
		];

		$data['employer_status'] = $status;
		$data['content'] = $this->load->view('employer/applications/all', $data, TRUE);
		$this->load->view('templates/master', $data);
		$this->db->close();
	}
	
	public function view($applied_id) {
		// ✅ Fetch application
		$application = $this->Application_model->get_application_by_id($applied_id, $this->user_id);
		if (!$application) show_404();

		// ✅ Auto-mark as "Viewed" only if currently "Applied"
		if (($application['ApplicationStage'] ?? '') === 'Applied') {
			$this->db->set('ApplicationStage', 'Viewed');
			$this->db->set('updated_at', date('Y-m-d H:i:s')); // remove this line if column doesn't exist
			$this->db->where('applied_id', $applied_id);
			$this->db->update('tb_applied');

			// Insert activity log
			$this->db->insert('tb_application_logs', [
				'application_id' => $applied_id,
				'job_id'         => $application['job_id'] ?? null,
				'candidate_id'   => $application['candidate_id'] ?? null,
				'performed_by'   => 'employer',
				'stage'          => 'Viewed',
				'created_at'     => date('Y-m-d H:i:s')
			]);

			// Update the array so the view shows the new status
			$application['ApplicationStage'] = 'Viewed';
		}

		// ✅ Load employer profile
		$this->load->model('employer/Profile_mdl', 'Profile_mdl');
		$profile = $this->Profile_mdl->get_employer_details($this->user_id);

		$status      = isset($profile['status']) ? strtolower($profile['status']) : 'inactive';
		$is_verified = !empty($profile['is_verified']) ? 1 : 0;

		// ✅ Check profile completion (based on required fields)
		$required_fields = ['company_name', 'company_address', 'industry_id', 'city_id', 'logo'];
		$profile_complete = 1;

		foreach ($required_fields as $field) {
			if (empty($profile[$field])) {
				$profile_complete = 0;
				break;
			}
		}

		// ✅ Mask sensitive fields if employer is not active OR not verified OR profile incomplete
		if ($status !== 'active' || $is_verified != 1 || $profile_complete == 0) {
			$application['email']  = null;
			$application['mobile'] = null;
		}

		// ✅ Load application logs
		$this->load->model('employer/Application_model');
		$logs = $this->Application_model->get_application_timeline($applied_id);

		// ✅ FETCH ACTIVE FEATURES FOR THIS CANDIDATE
		$candidateId = $application['candidate_id'] ?? null;
		$activeFeatures = [];
		if ($candidateId) {
			$activeFeatures = $this->Application_model->get_active_features_for_candidates([$candidateId]);
		}
		$application['active_features'] = $activeFeatures[$candidateId] ?? [];

		// ✅ Pass data to view
		$data = [
			'application'      => $application,
			'logs'             => $logs,
			'is_verified'      => $is_verified,
			'status'           => $status,
			'profile_complete' => $profile_complete,
			'title'            => 'Application Details',
		];

		// ✅ Render view
		$data['content'] = $this->load->view('employer/applications/view', $data, TRUE);
		$this->load->view('templates/master', $data);		
	}
	
	public function get_next_statuses($applied_id) {
		$this->load->model('employer/Application_model');
		$application = $this->Application_model->get_application_by_id($applied_id, $this->user_id);
		if (!$application) show_404();

		$current = $application['ApplicationStage'] ?? 'Applied';
		$nextStatuses = get_application_statuses($current);
		$labeled = [];
		foreach ($nextStatuses as $status) {
			$labeled[$status] = get_status_label($status);
		}

		$this->output->set_content_type('application/json')
			 ->set_output(json_encode([
				 'current_status' => $current,
				 'statuses'       => $labeled
			 ]));
	}
	
	
	public function get_job_applications($job_id) {
		// Verify job ownership
		$job = $this->PostJobModel->get_edit_post_details($job_id, $this->user_id);
		if (!$job) show_404();

		// Employer profile (for status check)
		$this->load->model('employer/Profile_mdl', 'Profile_mdl');
		$profile = $this->Profile_mdl->get_employer_details($this->user_id);
		$status = isset($profile['status']) ? strtolower($profile['status']) : 'inactive';

		// Pagination
		$per_page = 20;
		$page = max(1, (int) $this->input->get('page'));
		$offset = ($page - 1) * $per_page;

		// Count total applications for this job
		$total_rows = $this->Application_model->count_applications($this->user_id, $job_id);

		// Pagination config
		$config = [
			'base_url'            => base_url('employer/applications/applications/' . $job_id),
			'per_page'            => $per_page,
			'total_rows'          => $total_rows,
			'use_page_numbers'    => TRUE,
			'enable_query_strings'=> TRUE,
			'page_query_string'   => TRUE,
			'query_string_segment'=> 'page',
			'num_links'           => 2,
			'full_tag_open'       => '<nav class="my-8"><ul class="pagination flex flex-wrap justify-center gap-2 text-sm sm:text-base">',
			'full_tag_close'      => '</ul></nav>',
			'cur_tag_open'        => '<li><span class="px-4 py-2 bg-blue-600 text-white rounded-lg">',
			'cur_tag_close'       => '</span></li>',
			'num_tag_open'        => '<li>',
			'num_tag_close'       => '</li>',
			'prev_tag_open'       => '<li>',
			'prev_tag_close'      => '</li>',
			'next_tag_open'       => '<li>',
			'next_tag_close'      => '</li>',
			'prev_link'           => false,
			'next_link'           => false,
			'attributes'          => ['class' => 'm-1 px-4 py-2 bg-white dark:bg-gray-700 border dark:border-gray-600 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors text-sm sm:text-base'],
		];
		$this->pagination->initialize($config);

		// Get applications with premium flag & sorting
		$applications = $this->Application_model->get_job_applications($job_id, $per_page, $offset);

		// ✅ NEW: Extract candidate IDs and fetch active features
		$candidateIds = [];
		foreach ($applications as $app) {
			if (!empty($app['candidate_id'])) {
				$candidateIds[] = $app['candidate_id'];
			}
		}
		$activeFeatures = $this->Application_model->get_active_features_for_candidates(array_unique($candidateIds));

		// Attach active_features to each application
		foreach ($applications as &$app) {
			$cid = $app['candidate_id'] ?? null;
			$app['active_features'] = $activeFeatures[$cid] ?? [];
		}
		unset($app);

		$data = [
			'applications'      => $applications,
			'job'               => $job,
			'title'             => 'Applicants for ' . htmlspecialchars($job->job_title),
			'links'             => $this->pagination->create_links(),
			'status'            => $status,
			'totalApplications' => $total_rows
		];

		$data['content'] = $this->load->view('employer/applications/applications', $data, TRUE);
		$this->load->view('templates/master', $data);
		$this->db->close();
	}

	public function update_application_status() {		
		 // ⛔️ Check employer status first
		$this->load->model('employer/Profile_mdl', 'Profile_mdl');
		$profile = $this->Profile_mdl->get_employer_details($this->user_id);
		$employer_status = isset($profile['status']) ? strtolower($profile['status']) : 'inactive';

		if ($employer_status !== 'active') {
			return $this->output
				->set_content_type('application/json')
				->set_output(json_encode([
					'success' => false,
					'message' => 'Your employer account is not active. You cannot update application status.'
				]));
		}
		
		
		$applied_id = $this->input->post('applied_id', true);
		$status     = $this->input->post('status', true);
		$job_id     = $this->input->post('job_id', true);

		// 🚫 If status is Scheduled/Rescheduled, don't update — redirect to interview form instead
		if (in_array($status, ['Scheduled', 'Rescheduled'])) {
			return $this->output
				->set_content_type('application/json')
				->set_output(json_encode([
					'success'  => true,
					'message'  => 'Redirecting to interview scheduling page...',
					'redirect' => base_url('employer/applications/redirect_to_interview/' . $applied_id)
				]));
		}

		// ✅ Proceed with DB update for other statuses
		$success = $this->PostJobModel->update_application_status($applied_id, $status);

		$this->load->model('employer/Application_model');
		$application = $this->Application_model->get_application_by_id($applied_id, $this->user_id);

		$candidate_name  = $application['name'] ?? 'Candidate';
		$candidate_email = $application['email'] ?? '';
		$job_title       = $application['job_title'] ?? '';
		$company_name    = $application['employer_company_name'] ?? 'our company';
		$job_location    = $application['city_name'] ?? '';
		$employer_logo   = $application['employer_logo'] ?? '';
		$candidate_id    = $application['candidate_id'] ?? null;

		// 💬 Notification message
		$statusMessages = [
			'Under Review'        => "Thank you for applying to {$company_name}. Your application is currently under review.",
			'Shortlist'           => "Congratulations! You’ve been shortlisted by {$company_name}. Our team will contact you soon.",
			'Rejected'            => "We appreciate your interest in {$company_name}. Unfortunately, you were not shortlisted.",
			'Interview Scheduled' => "Your interview with {$company_name} has been scheduled. Check your email for details.",
			'Offer Extended'      => "Good news! {$company_name} has extended you a job offer.",
			'Hired'               => "You’ve been successfully hired at {$company_name}. Welcome aboard!",
			'Withdraw'            => "You have withdrawn your application from {$company_name}.",
			'Completed'           => "Your application process with {$company_name} has been marked as completed.",
			'Canceled'            => "This job application has been canceled by {$company_name}.",
			'Viewed'              => "{$company_name} has viewed your application."
		];
		$notification_message = $statusMessages[$status] ?? "Your application status has been updated to \"$status\" by {$company_name}.";

		// 🔔 Notification
		if (!empty($candidate_id)) {
			$this->load->model('Notification_model');
			$this->Notification_model->create([
				'user_id'    => $candidate_id,
				'type'       => 'candidate',
				'message'    => $notification_message,
				'link'       => 'job/myapply?job-id=' . $job_id,
				'created_at' => date('Y-m-d H:i:s')
			]);
		}

		// 📧 Email
		if (!empty($candidate_email)) {
			$subject = 'Application Status Update - ' . SITE_NAME;
			$email_message = $this->load->view('employer/email/application_status_update', [
				'candidate_name' => $application['name'] . ' ' . $application['last_name'],
				'message'        => $notification_message,
				'job_id'         => $job_id,
				'job_title'      => $job_title,
				'company_name'   => $company_name,
				'job_location'   => $job_location,
				'status'         => $status,
				'employer_logo'  => $employer_logo
			], true);
			
			$name = $application['name'];
			SendEmailTo($candidate_email, $subject, $email_message);
			//send_mailercloud_email($candidate_email, $name, $subject, $email_message);
		}

		// 🗂 Log
		$this->db->insert('tb_application_logs', [
			'application_id' => $applied_id,
			'job_id'         => $job_id,
			'candidate_id'   => $candidate_id,
			'performed_by'   => 'employer',
			'stage'          => $status,		
			'created_at'     => date('Y-m-d H:i:s')
		]);

		// ✅ Final response
		// ✅ Final response WITH CSRF HASH
		$this->output
			->set_content_type('application/json')
			->set_output(json_encode([
				'success'   => (bool) $success,
				'message'   => $success ? 'Status updated' : 'Could not update status',
				'redirect'  => null,
				'csrf_hash' => $this->security->get_csrf_hash() // ← REQUIRED
			]));
	}
	
	public function redirect_to_interview($applied_id) {
		$this->load->model('employer/Interview_model');
		$this->load->model('employer/Application_model');

		$application = $this->Application_model->get_application_by_id($applied_id, $this->user_id);
		if (!$application) show_404();

		$interview = $this->Interview_model->get_by_applied_id($applied_id, $this->user_id);

		if ($interview) {
			// Already exists → edit
			redirect('employer/interviews/schedule/' . $applied_id . '?edit=' . $interview['interview_id']);
		} else {
			// Not exists → new schedule
			redirect('employer/interviews/schedule/' . $applied_id);
		}
	}
}