<?php defined('BASEPATH') OR exit('No direct script access allowed');
date_default_timezone_set('Asia/Kolkata');

class PostJobController extends MY_Controller {
	
    public function __construct() {
        parent::__construct();	
        $this->user_id = $this->session->userdata('user_id');      
        $this->load->model('employer/PostJobModel');	
        $this->load->model('employer/EmployerPlans_model');        
    }
	
	public function index($job_id = null)
	{
		$data['title'] = 'Manage Your Job Postings';

		// Load profile model
		$this->load->model('employer/Profile_mdl', 'Profile_mdl');

		// Employer profile
		$profile = $this->Profile_mdl->get_employer_details($this->user_id);
		$data['status'] = $profile['status'] ?? 'active';

		/*
		|------------------------------------------
		| Job Counts
		|------------------------------------------
		*/

		$data['filtered_count'] = $this->PostJobModel->job_count($this->user_id, $job_id);
		$data['total_count']    = $this->PostJobModel->job_count($this->user_id);

		/*
		|------------------------------------------
		| Pagination Config
		|------------------------------------------
		*/

		$config = [
			'base_url'             => base_url('employer/jobs'),
			'per_page'             => 10,
			'total_rows'           => $data['filtered_count'],
			'use_page_numbers'     => TRUE,
			'enable_query_strings' => TRUE,
			'page_query_string'    => TRUE,
			'reuse_query_string'   => TRUE,
			'query_string_segment' => 'page',

			'num_links'            => 2,

			'full_tag_open'  => '<nav class="my-8"><ul class="pagination flex flex-wrap justify-center gap-2 text-sm sm:text-base">',
			'full_tag_close' => '</ul></nav>',

			'first_link' => FALSE,
			'last_link'  => FALSE,

			'num_tag_open'  => '<li>',
			'num_tag_close' => '</li>',

			'cur_tag_open'  => '<li><span class="px-4 py-2 bg-blue-600 text-white rounded-lg">',
			'cur_tag_close' => '</span></li>',

			'prev_tag_open'  => '<li class="mr-2">',
			'prev_tag_close' => '</li>',

			'next_tag_open'  => '<li>',
			'next_tag_close' => '</li>',

			'prev_link' => '← Prev',
			'next_link' => 'Next →',

			'attributes' => [
				'class' => 'px-2 py-1 sm:px-4 sm:py-2 bg-white dark:bg-gray-700 border dark:border-gray-600 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors text-sm sm:text-base'
			]
		];

		$this->pagination->initialize($config);

		/*
		|------------------------------------------
		| Current Page
		|------------------------------------------
		*/

		$page   = max((int)$this->input->get('page'), 1);
		$offset = ($page - 1) * $config['per_page'];

		/*
		|------------------------------------------
		| Job List
		|------------------------------------------
		*/

		$jobs = $this->PostJobModel->job_list(
			$this->user_id,
			$config['per_page'],
			$offset,
			$job_id
		);

		/*
		|------------------------------------------
		| Attach Cities
		|------------------------------------------
		*/

		foreach ($jobs as &$job) {
			$job['cities'] = $this->PostJobModel->get_job_cities($job['job_id']);
		}
		unset($job);

		/*
		|------------------------------------------
		| Pass Data to View
		|------------------------------------------
		*/

		$data['post_job_list'] = $jobs;
		$data['links']         = $this->pagination->create_links();

		$data['content'] = $this->load->view(
			'employer/jobs/all-jobs',
			$data,
			TRUE
		);

		$this->load->view('templates/master', $data);
	}
	
	public function addNewJob($post_id = null) {

		$data['title'] = $post_id ? 'Edit Job Posting' : 'New Job Posting';

		if (!$this->user_id) {
			show_error('User not found. Please log in again.', 403);
		}

		$this->load->model([
			'employer/Profile_mdl',
			'employer/EmployerPlans_model',
			'PostJobModel'
		]);

		// Employer profile
		$employer = $this->Profile_mdl->get_employer_details($this->user_id);

		if (empty($employer)) {
			show_error('Employer details not found.', 404);
		}

		$data['employerDetails'] = $employer;

		// Employer status
		$status = isset($employer['status']) ? strtolower($employer['status']) : 'inactive';
		$data['status'] = $status;

		// Active Plan
		$activePlan = $this->EmployerPlans_model->getActivePlanDetails($this->user_id);

		$data['planDetails'] = $activePlan;
		$data['hasActivePlan'] = !empty($activePlan);

		// ⭐ Fetch Free Plan (for modal popup)
		$freePlan = $this->EmployerPlans_model->getFreePlan();

		$data['freePlan'] = $freePlan;

		// Edit mode
		$post_detail = new stdClass();

		if ($post_id) {

			$post_detail = $this->PostJobModel
			->get_edit_post_details($post_id, $this->user_id);

			if (empty($post_detail)) {
				show_error('Job post not found.', 404);
			}

			$post_detail->cities = $this->PostJobModel
			->get_job_cities($post_id);
		}

		$data['post_detail'] = $post_detail;

		// Load view
		$data['content'] = $this->load
		->view('employer/jobs/add_new_job', $data, TRUE);

		$this->load->view('templates/master', $data);
	}

	
	// In your controller
	public function getRecentJobsForEmployer() {
		$jobs = $this->PostJobModel->getRecentJobs($this->user_id);
		$jobHtml = $this->embedJobHtml($jobs);
		$this->output->set_content_type('application/json')->set_output(json_encode($jobHtml));
	}

	private function embedJobHtml($jobs) {
		$html = '';  // Initialize an empty string to store HTML

		foreach ($jobs as $job) {
			$html .= '<div class="card-body">';
			 // Ensure job title does not exceed 50 characters
			$truncatedTitle = mb_strimwidth($job->job_title, 0, 100, '..');

			$html .= '<h5 class="card-title">' . htmlspecialchars($truncatedTitle) . '</h5>';

			// Remove HTML tags from job description
			$description = strip_tags($job->job_description);

			// Truncate description to 50 characters and append "..."
			$truncatedDescription = mb_strimwidth($description, 0, 100, '..');

			// Convert line breaks to <br> tags
			$truncatedDescription = nl2br($truncatedDescription);

			$html .= '<p class="card-text">' . htmlspecialchars($truncatedDescription) . '</p>';

			// Use a ternary operator to handle the display of the count in the button
			$applicantsCount = $job->applicants_count > 0 ? '(' . $job->applicants_count . ')' : '';

			$html .= '<a href="'.base_url('/job/application-response/'.$job->post_id).'" class="btn btn-info">View Applications ' . $applicantsCount . '</a>';

			$html .= '</div>';
		}
		return $html;  // Return the generated HTML
	}
	
	public function activateFreePlan()
	{
		try {

			// Fetch Free Plan
			$freePlan = $this->db
				->where('plan_category', 'Free')
				->where('status', 'active')
				->get('tb_subscription_plans')
				->row_array();

			if (!$freePlan) {
				show_error('Free plan not found.');
			}

			// Check if free plan already activated
			$exists = $this->db
				->where('employer_id', $this->user_id)
				->where('plan_id', $freePlan['id'])
				->where('plan_status', 'active')
				->get('tb_plan_purchases')
				->row();

			if ($exists) {

				$this->session->set_flashdata(
					'plan_activation_success',
					'Your free plan is already active.'
				);

				redirect('employer/jobs/create');
				return;
			}

			$startDate = date('Y-m-d H:i:s');
			$endDate   = date('Y-m-d H:i:s', strtotime("+{$freePlan['plan_validity_days']} days"));

			$data = [

				'payment_id' => NULL,
				'employer_id' => $this->user_id,
				'plan_id' => $freePlan['id'],

				'start_date' => $startDate,
				'end_date' => $endDate,

				'plan_status' => 'active',
				'purchase_date' => $startDate,

				'job_posts_used' => 0,
				'cv_views_used' => 0,
				'searches_used' => 0,
				'bulk_downloads_used' => 0,

				'is_deleted' => 0
			];

			// Transaction
			$this->db->trans_start();

			$this->db->insert('tb_plan_purchases', $data);

			$this->db->trans_complete();

			if ($this->db->trans_status() === FALSE) {

				$this->session->set_flashdata(
					'plan_activation_error',
					'Failed to activate free plan.'
				);

			} else {

				$this->session->set_flashdata(
					'plan_activation_success',
					'Your free plan has been activated successfully.'
				);
			}

			redirect('employer/jobs/create');

		} catch (Exception $e) {

			log_message('error', 'Error in activateFreePlan: ' . $e->getMessage());

			show_error('Something went wrong while activating the free plan.');
		}
	}

	public function insert_post_job() {

		$return_data = ['success' => 0];

		$this->load->model('employer/Profile_mdl');
		$employerDetails = $this->Profile_mdl->get_employer_details($this->user_id);

		// 1️⃣ Employer active check
		if (empty($employerDetails) || strtolower($employerDetails['status']) !== 'active') {

			$return_data['error_msg'] = 'Your employer account is inactive. Please activate your account to post jobs.';
			$return_data['csrf_token'] = $this->security->get_csrf_hash();

			echo json_encode($return_data);
			exit;
		}

		// 2️⃣ PLAN VALIDATION
		if (!$this->isPlanValid()) {
			return;
		}

		// 3️⃣ PLAN LIMIT CHECK (NEW SECURITY LAYER)
		$check = $this->EmployerPlans_model->canPerformAction($this->user_id,'job_post');

		if ($check !== true) {

			$this->handleError($check);

			return;
		}

		// 4️⃣ Form validation (NO balance cut yet)
		$validation_result = $this->validateFormData();

		if ($validation_result !== true) {

			$return_data['error_msg'] = validation_errors('<span>', '</span><br/>');
			$return_data['validation_step'] = $validation_result;
			$return_data['csrf_token'] = $this->security->get_csrf_hash();

			echo json_encode($return_data);
			exit;
		}

		// 5️⃣ Profile validation
		$skipProfileCheck = $this->input->post('skip_profile_check') === '1';

		if (!$skipProfileCheck) {

			$validation_employer = $this->validateEmployerDetails($employerDetails);

			if ($validation_employer !== true) {

				$return_data['error_generate'] = $this->generateApplicationModalHtml();
				$return_data['error_msg'] = validation_errors();
				$return_data['csrf_token'] = $this->security->get_csrf_hash();

				echo json_encode($return_data);
				exit;
			}
		}

		// 6️⃣ Start DB transaction
		$this->db->trans_start();

		// 7️⃣ Optional profile update
		if ($this->input->post('complete_profile') === 'update') {

			$project_data = [
				'industry_id' => $this->input->post('industryType'),
				'city_id' => $this->input->post('hiddenLocationId'),
				'company_size' => $this->input->post('company_size'),
				'updated_at' => date('Y-m-d H:i:s'),
			];

			$upload_result = $this->handleFileUpload();

			if ($upload_result['success'] === 1 && isset($upload_result['file_name'])) {

				$project_data['logo'] = 'uploads/employer/profile/' . $upload_result['file_name'];

			} elseif ($upload_result['success'] === 0) {

				$this->db->trans_rollback();

				$return_data['error_generate'] = $this->generateApplicationModalHtml();
				$return_data['error_msg'] = $upload_result['error_msg'];
				$return_data['csrf_token'] = $this->security->get_csrf_hash();

				echo json_encode($return_data);
				exit;
			}

			$this->Profile_mdl->update_employer_details($this->user_id,$project_data,[]);
		}

		// 8️⃣ Insert Job
		if ($this->insertJobData()) {

			// 9️⃣ Deduct job credit
			if (!$this->updatePostBalance()) {

				$this->db->trans_rollback();

				$return_data['error_msg'] = 'Post balance update failed.';
				$return_data['csrf_token'] = $this->security->get_csrf_hash();

				echo json_encode($return_data);
				exit;
			}

			$this->db->trans_complete();

			$return_data['success'] = 1;
			$return_data['success_msg'] = 'Job posted successfully!';
			$return_data['csrf_token'] = $this->security->get_csrf_hash();

		} else {

			$this->db->trans_rollback();

			$return_data['error_msg'] = 'Failed to save job data.';
			$return_data['csrf_token'] = $this->security->get_csrf_hash();
		}

		echo json_encode($return_data);
		exit;
	}


	private function generateApplicationModalHtml() {
		$html = '';

		// Load industry JSON
		$industryFilePath = 'json/Industry.json';
		$industryData = json_decode(file_get_contents($industryFilePath), true);

		// Load employer details
		$this->load->model('employer/Profile_mdl');
		$profile = $this->Profile_mdl->get_employer_details($this->user_id);

		$html .= '
			<h2 class="text-xl font-semibold text-gray-800 mb-1">Hi, ' . $profile['name'] . '</h2>
			<p class="text-sm text-gray-600 mb-4">Please complete your profile to publish a job opportunity.</p>

			<form id="completeProfileForm" enctype="multipart/form-data">
			  <div id="profile-modal-message" class="hidden mb-4 p-3 rounded text-sm"></div>
				<input type="hidden" name="complete_profile" value="update">
				<input type="hidden" 
				   name="'.$this->security->get_csrf_token_name().'" 
				   value="'.$this->security->get_csrf_hash().'">


				<!-- Industry Type -->
				<div class="mb-4">
					<label class="block text-sm font-medium text-gray-700 mb-1">Industry Type <span class="text-red-500">*</span></label>
					<select name="industryType" id="industry" class="block w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-sm">
						<option value="">Select Industry</option>';
						foreach ($industryData as $industry) {
							$isSelected = isset($profile['industry_id']) && $profile['industry_id'] == $industry['industry_id'] ? 'selected' : '';
							$html .= '<option value="' . $industry['industry_id'] . '" ' . $isSelected . '>' . $industry['industry'] . '</option>';
						}
		$html .= '
					</select>
				</div>

				<!-- Company Size -->
				<div class="mb-4">
					<label class="block text-sm font-medium text-gray-700 mb-1">Company Size <span class="text-red-500">*</span></label>
					<select name="company_size" class="block w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-sm">
						<option value="">Select</option>';
						$sizes = ['1-10', '11-50', '51-100', '101-500', '501+'];
						foreach ($sizes as $size) {
							$selected = (isset($profile['company_size']) && $profile['company_size'] == $size) ? 'selected' : '';
							$html .= '<option value="' . $size . '" ' . $selected . '>' . $size . '</option>';
						}
		$html .= '
					</select>
				</div>

			<!-- Company Location -->
				<div class="mb-4 company-location-container">
				  <label class="block text-sm font-medium text-gray-700 mb-1">
					Company Location <span class="text-red-500">*</span>
				  </label>
				  <div class="relative">
					<input type="text" name="company_location" id="company_location"
						   value="' . (isset($profile['city_name']) ? $profile['city_name'] : '') . '"
						   placeholder="Start typing to search cities..."
						   autocomplete="off"
						   class="block w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-sm" />
					
					<input type="hidden" name="hiddenLocationId" id="hiddenLocationId"
						   value="' . (isset($profile['city_id']) ? $profile['city_id'] : '') . '" />
					
					<ul id="company_location_list" class="absolute z-50 bg-white dark:bg-gray-800 shadow-lg rounded-b-lg mt-1 max-h-60 overflow-y-auto hidden"></ul>
				  </div>
				</div>


				<!-- Company Logo -->
				<div class="mb-6">
					<label class="block text-sm font-medium text-gray-700 mb-1">Upload Company Logo <span class="text-red-500">*</span></label>
					<input type="file" name="company_logo" id="company_logo"
						   class="block w-full text-sm text-gray-700 border border-gray-300 rounded-md focus:outline-none focus:ring focus:ring-blue-500" />
				</div>

				<div class="flex justify-between items-center border-t pt-4">
					<button type="button" id="skipProfileBtn"
							class="px-4 py-2 text-sm font-medium text-gray-500 hover:text-gray-700 hover:underline">
						Skip & Continue
					</button>

					<div class="flex gap-3">
						<a href="' . base_url('employer/profile') . '" class="px-4 py-2 text-sm font-medium text-gray-600 bg-gray-100 rounded-md hover:bg-gray-200">
							Complete Profile
						</a>
						<button type="submit" id="submitApplication"
								class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md flex items-center gap-2">
							<span class="spinner hidden"><i class="fas fa-spinner fa-spin"></i></span>
							Submit
						</button>
					</div>
				</div>
			</form>
		';

		return $html;
	}
    
	private function isPlanValid()
	{
		$plan = $this->EmployerPlans_model
			->getActivePlanDetails($this->user_id);

		if (!$plan) {

			$this->handleError(
				'No active plan found. Please activate a plan first.',
				base_url('pricing/plan')
			);
		}

		// plan expiry check
		if (!empty($plan['end_date']) && strtotime($plan['end_date']) < time()) {

			$this->handleError(
				'Your plan has expired. Please renew your plan.',
				base_url('pricing/plan')
			);
		}

		return true;
	}
	
	private function updatePostBalance()
	{
		$plan = $this->EmployerPlans_model
			->getActivePlanDetails($this->user_id);

		if (!$plan) {
			return false;
		}

		$usedPosts = $plan['job_posts_used'] + 1;

		$this->db->where('id', $plan['plan_purchase_id']);

		return $this->db->update(
			'tb_plan_purchases',
			[
				'job_posts_used' => $usedPosts
			]
		);
	}
	
	private function validateFormData() {
		// -------- Step 0 --------
		$this->form_validation->set_rules('job_title', 'Job Title', 'required|trim|min_length[2]|max_length[100]|xss_clean');
		$this->form_validation->set_rules('industry_id', 'Industry', 'required|trim|numeric|xss_clean');
		$this->form_validation->set_rules('job_type', 'Job Type', 'required|trim|xss_clean');
		$this->form_validation->set_rules('city_ids', 'Cities', 'required|trim|xss_clean');

		if (!$this->form_validation->run()) {
			return 0;
		}

		// -------- Step 1 --------
		$this->form_validation->set_rules('salary_type', 'Salary Type', 'required|trim|xss_clean');
		$this->form_validation->set_rules(
			'min_salary', 'Min Salary',
			'required|numeric|trim|min_length[1]|max_length[10]|xss_clean|callback_check_salary_range'
		);
		$this->form_validation->set_rules('max_salary', 'Max Salary', 'required|numeric|trim|min_length[1]|max_length[10]|xss_clean');
		$this->form_validation->set_rules('education', 'Education', 'required|trim|xss_clean');
		$this->form_validation->set_rules(
			'min_experience', 'Min Experience',
			'required|numeric|trim|xss_clean|callback_check_experience_range'
		);
		$this->form_validation->set_rules('max_experience', 'Max Experience', 'required|numeric|trim|xss_clean');
		$this->form_validation->set_rules('skills', 'Skills', 'required|trim|xss_clean');

		// 🔥 TINYMCE FIX
		$desc_html  = $this->input->post('job_description', false);
		$desc_plain = trim(strip_tags($desc_html));
		$_POST['job_description_plain'] = $desc_plain;

		$this->form_validation->set_rules(
			'job_description_plain',
			'Job Description',
			'required|min_length[100]'
		);

		if (!$this->form_validation->run()) {
			return 1;
		}

		// -------- Step 2 --------
		$this->form_validation->set_rules(
			'application_method',
			'Application Method',
			'trim|in_list[weblink]|xss_clean'
		);

		if (
			$this->input->post('application_method') === 'weblink' &&
			$this->input->post('enable_apply_link') === 'on'
		) {
			$this->form_validation->set_rules(
				'apply_web_link',
				'Application Link',
				'required|valid_url|xss_clean'
			);
		}

		$this->form_validation->set_rules('deadline_date', 'Application Deadline', 'required|trim|callback_check_deadline');
		$this->form_validation->set_rules('positions_open', 'Positions Open', 'required|numeric|trim|xss_clean');

		if (!$this->form_validation->run()) {
			return 2;
		}

		return true;
	}


	// Callback: ensure min_salary <= max_salary
	public function check_salary_range($min) {
		$max = $this->input->post('max_salary');
		if ($min <= $max) {
			return true;
		}
		$this->form_validation->set_message(
			'check_salary_range',
			'The {field} must be less than or equal to Max Salary.'
		);
		return false;
	}

	// Callback: ensure min_experience <= max_experience
	public function check_experience_range($min) {
		$max = $this->input->post('max_experience');
		if ($min <= $max) {
			return true;
		}
		$this->form_validation->set_message(
			'check_experience_range',
			'The {field} must be less than or equal to Max Experience.'
		);
		return false;
	}

	// Callback: ensure deadline_date is today or future
	public function check_deadline($date) {
		if (strtotime($date) >= strtotime(date('Y-m-d'))) {
			return true;
		}
		$this->form_validation->set_message(
			'check_deadline',
			'The {field} must be today or a future date.'
		);
		return false;
	}

	public function update_post_job() {		
        $return_data = ['success' => 0];
        $this->load->model('employer/Profile_mdl');
        $employerDetails = $this->Profile_mdl->get_employer_details($this->user_id);
        
        $job_id = $this->input->post('job_id');
        // Verify job ownership
        $existingJob = $this->PostJobModel->get_edit_post_details($job_id, $this->user_id);
        if(!$existingJob) {
            $return_data['error_msg'] = 'Job not found or unauthorized';
            $return_data['csrf_token'] = $this->security->get_csrf_hash();
            echo json_encode($return_data);
            exit;
        }

        // Start transaction
        $this->db->trans_start();

        // Form validation
        $validation_result = $this->validateFormData();
        if($validation_result === true) {
            // Validate employer details
            $validation_employer = $this->validateEmployerDetails($employerDetails);
            if($validation_employer === true) {
                
                if($this->input->post('complete_profile', true) === 'update') {
                    $project_data = [
                        'industry_id' => $this->input->post('industryType'),
                        'city_id' => $this->input->post('hiddenLocationId'),
                        'company_size' => $this->input->post('company_size'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ];

                    // Handle logo upload
                    $upload_result = $this->handleFileUpload();
                    
                    if($upload_result['success'] === 1) {
                        if(isset($upload_result['file_name'])) {
                            // Delete old logo if exists
                            if(!empty($existingJob->logo)) {
                                    @unlink(FCPATH . $existingJob->logo);
                            }
                            $project_data['logo'] = 'uploads/employer/profile/' . $upload_result['file_name'];
                        }
                    } else {
                        $return_data['error_generate'] = $this->generateApplicationModalHtml();
                        $return_data['error_msg'] = $upload_result['error_msg'];
                        $return_data['csrf_token'] = $this->security->get_csrf_hash();
                        echo json_encode($return_data);
                        exit;
                    }

                    // Update employer details
                    $this->Profile_mdl->update_employer_details($this->user_id, $project_data,[]);
                }

                // Update job data
                if($this->updateJobData($job_id, $existingJob)) {
                    $this->db->trans_complete();
                    $return_data['success'] = 1;
                    $return_data['success_msg'] = 'Job updated successfully';
                    $return_data['csrf_token'] = $this->security->get_csrf_hash();
                } else {
                    $this->db->trans_rollback();
                    $return_data['error_msg'] = 'Failed to update job data';
                    $return_data['csrf_token'] = $this->security->get_csrf_hash();
                }
            } else {
                $return_data['error_generate'] = $this->generateApplicationModalHtml();
                $return_data['error_msg'] = validation_errors();
                $return_data['csrf_token'] = $this->security->get_csrf_hash();
            }
        } else {
            $return_data['error_msg'] = validation_errors('<span>', '</span><br/>');
            $return_data['validation_step'] = $validation_result;
            $return_data['csrf_token'] = $this->security->get_csrf_hash();
        }

        echo json_encode($return_data);
        exit;
    }

    private function insertJobData() {
        
        $post = $this->input->post(NULL, TRUE);
    
        // Active plan
        $plan = $this->EmployerPlans_model->getActivePlanDetails($this->user_id);
        $isPaid = $plan['plan_category'] === 'Paid' ? 1 : 0;
    
        // Employer details
        $this->load->model('employer/Profile_mdl');
        $employer = $this->Profile_mdl->get_employer_details($this->user_id);
        $company_name = $employer['company_name'] ?? '';
    
        // Application method
        $is_weblink = isset($post['application_method']) && $post['application_method'] === 'weblink';
        $enable_link = isset($post['enable_apply_link']) ? 'yes' : 'no';
    
        // TEMP slug (without ID)
        $temp_slug = $this->generateJobSlug(
            $post['job_title'],
            $company_name,
            $post['min_experience'],
            $post['max_experience']
        );
    
        $data = [
            'employer_id'        => $this->user_id,
            'job_title'          => preg_replace('/[^\w\s]/', '', $post['job_title']),
            'slug'               => $temp_slug,
            'min_experience'     => intval($post['min_experience']),
            'max_experience'     => intval($post['max_experience']),
            'industry_id'        => $post['industry_id'],
            'job_type'           => $post['job_type'],
            'positions_open'     => $post['positions_open'],
            'education'          => $post['education'],
            'salary_type'        => $post['salary_type'],
            'min_salary'         => $post['min_salary'],
            'max_salary'         => $post['max_salary'],
            'job_description'    => $post['job_description'],
            'job_tag'            => '',
            'is_paid'            => $isPaid,
            'purchase_id'        => $plan['plan_purchase_id'],
            'status'             => 'active',
    
            'deadline_date'      => date('Y-m-d 23:59:59', strtotime($post['deadline_date'])),
    
            'apply_web_link'     => $is_weblink ? ($post['apply_web_link'] ?? '') : '',
            'enable_apply_link'  => $is_weblink ? $enable_link : 'no',
    
            'posted_by'          => 'employer',
            'created_at'         => date('Y-m-d H:i:s'),
            'updated_at'         => date('Y-m-d H:i:s'),
        ];
    
        // Insert job
        $job_id = $this->PostJobModel->insert_job_data($data);
    
        // FINAL slug update
        if ($job_id) {
            $final_slug = $this->generateJobSlug(
                $post['job_title'],
                $company_name,
                $post['min_experience'],
                $post['max_experience'],
                $job_id
            );
    
            $this->db->where('job_id', $job_id)->update('post_job', [
                'slug' => $final_slug
            ]);
        }
    
        // ================= Cities =================
        $cityIdsString = $post['city_ids'] ?? '';
        $rawCities = array_filter(explode(',', $cityIdsString), fn($v) => $v !== '' && is_numeric($v));
    
        if (!empty($rawCities)) {
            $batch = [];
            foreach ($rawCities as $city_id) {
                $batch[] = [
                    'job_id'  => $job_id,
                    'city_id' => (int)$city_id
                ];
            }
            $this->PostJobModel->insert_job_cities_batch($batch);
        }
    
        // ================= Skills =================
        if (!empty($post['skills']) && $job_id) {
    
            $skills = array_map('trim', explode(',', $post['skills']));
            $batch = [];
    
            foreach ($skills as $skill) {
                if ($skill === '') continue;
    
                $batch[] = [
                    'job_id' => $job_id,
                    'skill_name' => $skill
                ];
            }
    
            if (!empty($batch)) {
                $this->PostJobModel->insertJobSkillsBatch($batch);
            }
        }
    
        return $job_id ? true : false;
    }
	
	private function updateJobData($job_id, $existingJob) {
        $post = $this->input->post(NULL, TRUE);
    
        // Employer details
        $this->load->model('employer/Profile_mdl');
        $employer = $this->Profile_mdl->get_employer_details($this->user_id);
        $company_name = $employer['company_name'] ?? '';
    
        // Application method
        $is_weblink = isset($post['application_method']) && $post['application_method'] === 'weblink';
        $enable_link = isset($post['enable_apply_link']) ? 'yes' : 'no';
    
        // FINAL slug (job_id already available)
        $final_slug = $this->generateJobSlug(
            $post['job_title'],
            $company_name,
            $post['min_experience'],
            $post['max_experience'],
            $job_id
        );
    
        $data = [
            'job_title'          => preg_replace('/[^\w\s]/', '', $post['job_title']),
            'slug'               => $final_slug,
            'min_experience'     => intval($post['min_experience']),
            'max_experience'     => intval($post['max_experience']),
            'industry_id'        => $post['industry_id'],
            'job_type'           => $post['job_type'],
            'positions_open'     => $post['positions_open'],
            'education'          => $post['education'],
            'salary_type'        => $post['salary_type'],
            'min_salary'         => $post['min_salary'],
            'max_salary'         => $post['max_salary'],
            'job_description'    => $post['job_description'],
    
            'deadline_date'      => date('Y-m-d 23:59:59', strtotime($post['deadline_date'])),
    
            'apply_web_link'     => $is_weblink ? ($post['apply_web_link'] ?? '') : '',
            'enable_apply_link'  => $is_weblink ? $enable_link : 'no',
    
            'updated_at'         => date('Y-m-d H:i:s'),
        ];
    
        // Update job
        $this->PostJobModel->update_post_job($job_id, $this->user_id, $data);
    
        // ================= Skills =================
        if (!empty($post['skills'])) {
    
            $existingSkills = $this->PostJobModel->get_job_skills($job_id);
            $existingSkillNames = array_map('strtolower', array_column($existingSkills, 'skill_name'));
    
            $postedSkills = array_map('strtolower', array_map('trim', explode(',', $post['skills'])));
    
            $toDelete = array_diff($existingSkillNames, $postedSkills);
            $toInsert = array_diff($postedSkills, $existingSkillNames);
    
            foreach ($toDelete as $skill) {
                $this->PostJobModel->deleteSingleSkill($job_id, $skill);
            }
    
            if (!empty($toInsert)) {
                $batch = [];
                foreach ($toInsert as $skill) {
                    $batch[] = [
                        'job_id' => $job_id,
                        'skill_name' => $skill
                    ];
                }
                $this->PostJobModel->insertJobSkillsBatch($batch);
            }
        }
    
        // ================= Cities =================
        $cityIdsString = $post['city_ids'] ?? '';
        $rawCities = array_filter(explode(',', $cityIdsString), fn($v) => $v !== '' && is_numeric($v));
    
        $existingCities = $this->PostJobModel->get_job_cities($job_id);
        $existingCityIds = array_column($existingCities, 'city_id');
    
        $newCities = array_diff($rawCities, $existingCityIds);
    
        if (!empty($newCities)) {
            $batch = [];
            foreach ($newCities as $city_id) {
                $batch[] = [
                    'job_id'  => $job_id,
                    'city_id' => (int)$city_id
                ];
            }
            $this->PostJobModel->insert_job_cities_batch($batch);
        }
    
        return true;
    }
	
    private function generateJobSlug($job_title, $company_name, $min_exp, $max_exp, $job_id = null)
    {
        // Clean
        $job_title   = preg_replace('/[^\w\s]/', '', $job_title);
        $company     = preg_replace('/[^\w\s]/', '', $company_name);
    
        // Company with dash
        $company = str_replace(' ', '-', strtolower($company));
    
        // ✅ CORRECT experience format
        $experience = $min_exp . ' to ' . $max_exp . ' years';
    
        // Combine
        $slug_string = $job_title . ' ' . $company . ' ' . $experience;
    
        // Slug convert
        $slug = url_title($slug_string, '-', true);
    
        // Clean dashes
        $slug = preg_replace('/-+/', '-', $slug);
        $slug = trim($slug, '-');
    
        // Append job_id at END only
        if ($job_id) {
            $slug .= '-' . $job_id;
        }
    
        return $slug;
    }
	
    private function validateEmployerDetails($employerDetails) {
        if (empty($employerDetails['industry_id'])) {
            $this->form_validation->set_rules('industryType', 'Industry Type', 'required');
        }
    
        if (empty($employerDetails['city_id'])) {
            $this->form_validation->set_rules('hiddenLocationId', 'Company Location', 'required');
        }
    
        if (empty($employerDetails['company_size'])) {
            $this->form_validation->set_rules('company_size', 'Company Size', 'required');
        }
    
        // If company_logo is empty in the database, show the modal but do not make it required
        if (empty($employerDetails['logo'])) {
            // Trigger modal for optional upload but don't set the logo as required
            $this->form_validation->set_rules('company_logo', 'Company Logo', 'callback_optional_logo');
        }
    
        if ($this->form_validation->run() == FALSE) {
            return false;
        }
    
        return true;
    }
    	
	private function handleError($message, $redirectUrl = null) {
        $return_data['error_msg'] = $message;
        $return_data['success'] = 0;
        $return_data['csrf_token'] = $this->security->get_csrf_hash();

        if ($redirectUrl !== null) {
            $return_data['redirect_url'] = $redirectUrl;
        }

        echo json_encode($return_data);
        exit;
    }

    public function remove_job_city() {
		$job_id  = $this->input->post('job_id', TRUE);
		$city_id = $this->input->post('city_id', TRUE);

		if (!is_numeric($job_id) || !is_numeric($city_id)) {
			echo json_encode([
				'success' => 0,
				'error_msg' => 'Invalid parameters',
				'csrf_token' => $this->security->get_csrf_hash()
			]);
			return;
		}

		// 🔐 Ownership check (MANDATORY)
		$job = $this->PostJobModel->get_edit_post_details($job_id, $this->user_id);
		if (!$job) {
			echo json_encode([
				'success' => 0,
				'error_msg' => 'Unauthorized action',
				'csrf_token' => $this->security->get_csrf_hash()
			]);
			return;
		}

		$deleted = $this->PostJobModel->remove_job_city($job_id, $city_id);
		if ($deleted) {
			echo json_encode([
				'success' => 1,
				'success_msg' => 'City removed',
				'csrf_token' => $this->security->get_csrf_hash()
			]);
		} else {
			echo json_encode([
				'success' => 0,
				'error_msg' => 'City not found or could not be removed',
				'csrf_token' => $this->security->get_csrf_hash()
			]);
		}
	}

				
	 public function jobStatus() {
        $this->load->library('form_validation');
        $this->form_validation
            ->set_rules('id', 'Job ID', 'required|integer')
            ->set_rules(
                'status',
                'Status',
                'required|in_list[draft,active,on-hold,under-review,rejected,suspended]'
            );

        if ($this->form_validation->run() === FALSE) {
            echo json_encode([
                'success'   => 0,
                'error_msg' => validation_errors(),
                'csrf_token' => $this->security->get_csrf_hash()
            ]);
            return;
        }

        $id  = $this->input->post('id', TRUE);
        $new = strtolower($this->input->post('status', TRUE));

        // Fetch job with status and deadline
        $row = $this->db
            ->select('status, deadline_date')
            ->where('job_id', $id)
            ->get('post_job')
            ->row();

        if (! $row) {
            echo json_encode([
                'success'   => 0,
                'error_msg' => 'Job not found.',
                'csrf_token' => $this->security->get_csrf_hash()
            ]);
            return;
        }

        $current  = strtolower($row->status);
        $deadline = $row->deadline_date;

        // Define blocked transitions
        $locked_statuses   = ['under-review', 'rejected', 'suspended'];
        $restricted_target = ['draft', 'active', 'on-hold'];

        // Custom messages per status
        $custom_messages = [			
            'under-review'  => 'This job is under review. Please wait for admin approval.',
            'rejected'      => 'This job has been rejected. Please contact admin for further steps.',
            'suspended'     => 'This job is suspended and cannot be updated. Please contact admin.',
        ];

        // First restriction: Locked status logic
        if (in_array($current, $locked_statuses, TRUE) && in_array($new, $restricted_target, TRUE)) {
            $error_msg = isset($custom_messages[$current])
                ? $custom_messages[$current]
                : 'You cannot change this status. Please contact admin.';

            echo json_encode([
                'success'   => 0,
                'error_msg' => $error_msg,
                'csrf_token' => $this->security->get_csrf_hash()
            ]);
            return;
        }

        // Second restriction: Deadline check
        if (strtotime($deadline) < time() && in_array($new, ['draft', 'active', 'on-hold'], TRUE)) {
            echo json_encode([
                'success'   => 0,
                'error_msg' => 'This job has passed its deadline and cannot be moved to an active status.',
                'csrf_token' => $this->security->get_csrf_hash()
            ]);
            return;
        }

        // Proceed with update
        $this->db->trans_start();
        $updated = $this->db
            ->where('job_id', $id)
            ->update('post_job', [
                'status'     => $new,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        $this->db->trans_complete();

        if ($updated) {
            echo json_encode([
                'success'     => 1,
                'success_msg' => 'Status updated successfully.',
                'csrf_token' => $this->security->get_csrf_hash()
            ]);
        } else {
            echo json_encode([
                'success'   => 0,
                'error_msg' => 'Could not update status.',
                'csrf_token' => $this->security->get_csrf_hash()
            ]);
        }
    }
		
	// Custom callback function to handle optional logo upload
    public function optional_logo() {
		if (empty($_FILES['company_logo']['name'])) {
			return true;
		}

		$allowed = ['jpg','jpeg','png','gif'];
		$ext = strtolower(pathinfo($_FILES['company_logo']['name'], PATHINFO_EXTENSION));

		if (!in_array($ext, $allowed)) {
			$this->form_validation->set_message(
				'optional_logo',
				'Only JPG, PNG, GIF images are allowed.'
			);
			return false;
		}

		return true;
	}
	
	// Function to compress image if larger than 100KB
	private function compress_image($image_path) {		
		$config['image_library'] = 'gd2';
		$config['source_image'] = $image_path;
		$config['maintain_ratio'] = TRUE;
		$config['width'] = 800;
		$config['height'] = 600;
		$config['quality'] = 75;

		$this->load->library('image_lib', $config);
		$this->image_lib->resize();
	}

	
	private function handleFileUpload() {
		$return_data = ['success' => 0];

		if (!empty($_FILES['company_logo']['name'])) {

			if (!is_dir('./uploads/employer/profile/')) {
				mkdir('./uploads/employer/profile/', 0777, true);
			}

			$config['upload_path']   = './uploads/employer/profile/';
			$config['allowed_types'] = 'jpg|jpeg|png|gif';
			$config['encrypt_name']  = TRUE;
			$config['max_size']      = 2048;
			$config['detect_mime']   = TRUE;

			$this->load->library('upload', $config);

			if ($this->upload->do_upload('company_logo')) {

			$upload_data = $this->upload->data();

			$return_data['success']   = 1;
			$return_data['file_name'] = $upload_data['file_name'];

			$this->compress_image($upload_data['full_path']);

			// 🔥 session update (IMPORTANT)
			$this->session->set_userdata([
				'logo' => base_url('uploads/employer/profile/' . $upload_data['file_name'])
			]);

		} else {
				$return_data['error_msg'] = strip_tags($this->upload->display_errors());
			}

		} else {
			$return_data['success'] = 1;
		}

		return $return_data;
	}
	
	// calling from url update all slug post table
	public function migrate_all_slugs()
	{
		// Optional: restrict access (remove after use)
		$secret = $this->input->get('key');
		if ($secret !== 'migrate_slugs_2025') {
			show_error('Unauthorized', 403);
		}

		set_time_limit(0);
		$this->load->model('employer/Profile_mdl');

		$batchSize = 1000;
		$offset    = 0;
		$updated   = 0;

		$total = $this->db->count_all('tb_post_job');

		while ($offset < $total) {
			// Fetch a batch of jobs
			$jobs = $this->db->select('job_id, employer_id, job_title, min_experience, max_experience')
							 ->from('tb_post_job')
							 ->limit($batchSize, $offset)
							 ->get()
							 ->result();

			if (empty($jobs)) break;

			// Preload employer company names from tb_employer
			$employerIds = array_unique(array_column($jobs, 'employer_id'));
			$employers = $this->db->select('employer_id, company_name')
								  ->from('tb_employer')
								  ->where_in('employer_id', $employerIds)
								  ->get()
								  ->result_array();

			$employerMap = [];
			foreach ($employers as $emp) {
				$employerMap[$emp['employer_id']] = $emp['company_name'];
			}

			// Update each job slug
			foreach ($jobs as $job) {
				$companyName = $employerMap[$job->employer_id] ?? '';

				$newSlug = $this->generateJobSlug(
					$job->job_title,
					$companyName,
					$job->min_experience,
					$job->max_experience,
					$job->job_id
				);

				$this->db->where('job_id', $job->job_id)
						 ->update('tb_post_job', ['slug' => $newSlug]);

				$updated++;
			}

			$offset += $batchSize;
			echo "Processed $offset of $total jobs...<br>";
			flush(); // output progress
		}

		echo "<br>✅ Done. Updated $updated job slugs.";
	}
	  
    

    
}
?>