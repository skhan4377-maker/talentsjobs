<?php defined('BASEPATH') or exit('No direct script access allowed');

class Common extends CI_Controller{

    public function __construct(){
        parent::__construct();
        $this->load->model('home_mdl');
        $this->load->config('recaptcha');
    }

	private function verify_recaptcha_v3($token, $expected_action){
		if (empty($token)) {
			return false;
		}

		$secret_key = $this->config->item('recaptcha_secret_key');
		$threshold  = $this->config->item('recaptcha_threshold');

		$ch = curl_init('https://www.google.com/recaptcha/api/siteverify');

		curl_setopt_array($ch, [
			CURLOPT_POST           => true,
			CURLOPT_POSTFIELDS     => http_build_query([
				'secret'   => $secret_key,
				'response' => $token,
				'remoteip' => $this->input->ip_address()
			]),
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_TIMEOUT        => 10,
			CURLOPT_SSL_VERIFYPEER => true
		]);

		$response = curl_exec($ch);

		if (curl_errno($ch)) {
			log_message('error', 'reCAPTCHA v3 cURL error: ' . curl_error($ch));
			curl_close($ch);
			return false;
		}

		curl_close($ch);

		$result = json_decode($response);

		/*
		 Expected from Google:
		 success = true
		 score   = 0.0 – 1.0
		 action  = candidate_register / register / login
		*/
		if (
			isset($result->success, $result->score, $result->action) &&
			$result->success === true &&
			$result->score >= $threshold &&
			$result->action === $expected_action
		) {
			return true;
		}

		log_message('error', 'reCAPTCHA v3 failed: ' . json_encode($result));
		return false;
	}
	
	public function common_register() {
        // -------------------------------
        // reCAPTCHA v3 verification
        // -------------------------------
        $recaptcha_token = $this->input->post('recaptcha_token', true);
    
        if (!$this->verify_recaptcha_v3($recaptcha_token, 'candidate_register')) {
            return $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status'    => 'error',
                    'error_msg' => 'Suspicious activity detected. Please try again.',
                    'csrf_name' => $this->security->get_csrf_token_name(),
                    'csrf_hash' => $this->security->get_csrf_hash()
                ]));
        }
    
        // -------------------------------
        // Inputs
        // -------------------------------
        $name        = $this->input->post('name', true);
        $designation = $this->input->post('designation', true);
        $email       = $this->input->post('email', true);
        $mobile      = $this->input->post('mobile', true);          // local number (no prefix)
        $country_id  = $this->input->post('country_id', true);     // from dropdown
        $country_code= $this->input->post('country_code', true);   // hidden iso2 field
        $password    = $this->input->post('password');
    
        // -------------------------------
        // Form Validation
        // -------------------------------
        $this->form_validation->set_rules('name', 'Name', 'required|min_length[3]|max_length[30]');
        $this->form_validation->set_rules('designation', 'Designation', 'required|min_length[3]|max_length[30]');
        $this->form_validation->set_rules('email', 'Email', 'required|valid_email');
        
        // New rules for country
        $this->form_validation->set_rules('country_id', 'Country', 'required|integer');
        $this->form_validation->set_rules('country_code', 'Country Code', 'required|regex_match[/^\+\d{1,4}$/]');
        
        // Mobile now expects only digits (6-15), no country prefix
        $this->form_validation->set_rules(
            'mobile',
            'Mobile',
            'required|regex_match[/^\d{6,15}$/]'
        );
        $this->form_validation->set_rules('password', 'Password', 'required|min_length[8]|max_length[16]');
        $this->form_validation->set_rules('recaptcha_token', 'reCAPTCHA', 'required');
    
        if ($this->form_validation->run() === FALSE) {
            return $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status'    => 'error',
                    'error_msg' => validation_errors('<span>', '</span><br>'),
                    'csrf_name' => $this->security->get_csrf_token_name(),
                    'csrf_hash' => $this->security->get_csrf_hash()
                ]));
        }
    
        // -------------------------------
        // Duplicate Check
        // -------------------------------
        $existing = $this->home_mdl->check_exist_value($mobile, $email, 'candidate');
        if ($existing) {
            $msg = ($existing['mobile'] === $mobile)
                ? 'Mobile number already exists'
                : 'Email already exists';
    
            return $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status'    => 'error',
                    'error_msg' => $msg,
                    'csrf_name' => $this->security->get_csrf_token_name(),
                    'csrf_hash' => $this->security->get_csrf_hash()
                ]));
        }
    
        // -------------------------------
        // Insert Candidate
        // -------------------------------
        $data = [
            'name'         => trim($name),
            'designations' => trim($designation),
            'email'        => trim($email),
            'mobile'       => $mobile,                      // local number only
            'country_id'   => $country_id,                  // from tb_countries
            'country_code' => $country_code,   // e.g., +91
            'password'     => password_hash($password, PASSWORD_BCRYPT),
            'created_at'   => date('Y-m-d H:i:s')
        ];
    
        $this->load->model('candidate/Profile_mdl');
        $insert_id = $this->Profile_mdl->save_candidate_record($data);
    
        if (!$insert_id) {
            return $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status'    => 'error',
                    'error_msg' => 'Registration failed. Try again.',
                    'csrf_name' => $this->security->get_csrf_token_name(),
                    'csrf_hash' => $this->security->get_csrf_hash()
                ]));
        }
    
        // -------------------------------
        // Fetch role and permissions
        // -------------------------------
        $this->load->model('login_mdl');
        $role_id = $this->login_mdl->get_role_id('candidate');
        $permissions = $this->login_mdl->get_user_permissions($role_id) ?? [];
    
        // -------------------------------
        // Session + Tokens
        // -------------------------------
        $login_token = bin2hex(random_bytes(32));
    
        $this->session->set_userdata([
            'logged_in'   => true,
            'user_id'     => $insert_id,
            'role'        => 'candidate',
            'role_id'     => $role_id,
            'permissions' => $permissions,
            'name'        => $name,
            'email'       => $email,
            'login_token' => $login_token,
            'last_login'  => date('Y-m-d H:i:s')
        ]);
    
        $this->load->model('login_mdl');
        $this->login_mdl->update_login_token($insert_id, 'candidate', $login_token);
    
        // -------------------------------
        // Email Verification
        // -------------------------------
        $verification_token = bin2hex(random_bytes(16));
        $this->load->model('Verification_mdl');
        $this->Verification_mdl->storeVerificationToken(
            $email,
            $verification_token,
            'candidate'
        );
    
        $this->mail_to_register($email, $name, 'candidate', $verification_token, $insert_id);
    
        // -------------------------------
        // FINAL RESPONSE
        // -------------------------------
        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'status'    => 'success',
                'message'   => 'Registration submitted successfully',
                'redirect'  => base_url('browse-jobs/' . make_slug($designation)),
                'csrf_name' => $this->security->get_csrf_token_name(),
                'csrf_hash' => $this->security->get_csrf_hash()
            ]));
    }
	
    public function process_registration() {
        // -------------------------------
        // reCAPTCHA v3 verification (unchanged)
        // -------------------------------
        $recaptcha_token = $this->input->post('recaptcha_token', true);
        if (!$this->verify_recaptcha_v3($recaptcha_token, 'register')) {
            return $this->outputResponse('error', 'Suspicious activity detected. Please try again.');
        }
    
        // -------------------------------
        // Inputs – add country fields
        // -------------------------------
        $mobile       = $this->input->post('mobile', true);        // local number
        $email        = $this->input->post('email', true);
        $name         = $this->input->post('name', true);
        $company_name = $this->input->post('company_name', true);
        $country_id   = $this->input->post('country_id', true);    // dropdown value
        $country_code = $this->input->post('country_code', true);  // dial code (+91)
    
        // -------------------------------
        // Duplicate Check (unchanged, mobile ab local number hai)
        // -------------------------------
        $existingData = $this->home_mdl->check_exist_value($mobile, $email, 'employer');
        if (!empty($existingData)) {
            if ($existingData['mobile'] == $mobile) {
                return $this->outputResponse('error', 'Mobile number already exists');
            }
            if ($existingData['email'] == $email) {
                return $this->outputResponse('error', 'Email already exists');
            }
        }
    
        // -------------------------------
        // Form Validation (updated version)
        // -------------------------------
        $validationResponse = $this->validateRegistrationForm();
        if ($validationResponse['status'] === 'error') {
            return $this->outputResponse('error', '', $validationResponse['errors']);
        }
    
        // -------------------------------
        // Prepare Data – base fields
        // -------------------------------
        $formData = $this->prepareFormData();
        $formData['password'] = password_hash($this->input->post('password'), PASSWORD_BCRYPT);
    
        // Add country data
        $formData['country_id']   = $country_id;
        $formData['country_code'] = $country_code;   // e.g., +91
    
        // -------------------------------
        // Insert Employer (unchanged)
        // -------------------------------
        $this->load->model('employer/Profile_mdl');
        $returnId = $this->Profile_mdl->insert_employer_data($formData);
    
        if (!$returnId) {
            return $this->outputResponse('error', 'Registration failed. Try again.');
        }
    
        // -------------------------------
        // Fetch role and permissions (unchanged)
        // -------------------------------
        $this->load->model('login_mdl');
        $role_id = $this->login_mdl->get_role_id('employer');
        $permissions = $this->login_mdl->get_user_permissions($role_id) ?? [];
    
        // -------------------------------
        // Tokens & Session (unchanged)
        // -------------------------------
        $role               = 'employer';
        $verification_token = bin2hex(random_bytes(16));
        $login_token        = bin2hex(random_bytes(32));
    
        $this->load->model('Verification_mdl');
        $this->Verification_mdl->storeVerificationToken($email, $verification_token, $role);
    
        $session_data = [
            'logged_in'   => true,
            'user_id'     => $returnId,
            'role'        => $role,
            'role_id'     => $role_id,
            'permissions' => $permissions,
            'name'        => $name,
            'email'       => $email,
            'login_token' => $login_token,
            'last_login'  => date('Y-m-d H:i:s')
        ];
        $this->session->set_userdata($session_data);
    
        $this->load->model('login_mdl');
        $this->login_mdl->update_login_token($returnId, $role, $login_token);
    
        // -------------------------------
        // Emails (unchanged)
        // -------------------------------
        $this->mail_to_register($email, $name, $role, $verification_token, $returnId);
        $this->send_admin_notification($name, $email, $mobile, $company_name, $formData);
    
        // -------------------------------
        // FINAL RESPONSE (unchanged)
        // -------------------------------
        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'status'    => 'success',
                'message'   => 'Form submitted successfully',
                'redirect'  => base_url('employer/jobs/create'),
                'csrf_name' => $this->security->get_csrf_token_name(),
                'csrf_hash' => $this->security->get_csrf_hash()
            ]));
    }
	
	private function validateRegistrationForm() {
        $this->form_validation->set_rules('name', 'Your Name', 'required|min_length[3]|max_length[60]');
        $this->form_validation->set_rules('email', 'Your Email', 'required|valid_email');
        $this->form_validation->set_rules('password', 'Password', 'required|min_length[8]|max_length[16]');
        
        // Mobile: ab sirf digits, 6‑15 characters (country code alag hai)
        $this->form_validation->set_rules('mobile', 'Mobile Number', 'required|min_length[6]|max_length[15]|numeric');
        
        // Country fields
        $this->form_validation->set_rules('country_id', 'Country', 'required|integer');
        $this->form_validation->set_rules('country_code', 'Country Code', 'required|regex_match[/^\+\d{1,4}$/]');
        
        $this->form_validation->set_rules('recuiter_type', 'Recruiter Type', 'required');
        $this->form_validation->set_rules('company_type', 'Company Type', 'required');
        $this->form_validation->set_rules('company_name', 'Company Name', 'required|min_length[2]');
        $this->form_validation->set_rules('agreeToTerms', 'Agree to Terms', 'required');
        $this->form_validation->set_rules('recaptcha_token', 'reCAPTCHA', 'required');
    
        if ($this->form_validation->run() === FALSE) {
            $errors = [
                'name'          => form_error('name'),
                'email'         => form_error('email'),
                'password'      => form_error('password'),
                'mobile'        => form_error('mobile'),
                'country_id'    => form_error('country_id'),
                'country_code'  => form_error('country_code'),
                'recuiter_type' => form_error('recuiter_type'),
                'company_type'  => form_error('company_type'),
                'company_name'  => form_error('company_name'),
                'agreeToTerms'  => form_error('agreeToTerms')
            ];
            return ['status' => 'error', 'errors' => $errors];
        }
        return ['status' => 'success'];
    }

	private function prepareFormData() {
        $formData = [
            'name'          => $this->input->post('name'),
            'email'         => $this->input->post('email'),
            'mobile'        => $this->input->post('mobile'),   // local number only
            'recuiter_type' => $this->input->post('recuiter_type'),
            'company_type'  => $this->input->post('company_type'),
            'company_name'  => $this->input->post('company_name'),
            'agree_to_terms'=> $this->input->post('agreeToTerms'),
            'created_at'    => date('Y-m-d H:i:s')
        ];
    
        $campaignData = $this->session->userdata('campaign_data');
        if ($campaignData) {
            $formData['campaign_id'] = $campaignData['campaign_id'];
        }
        return $formData;
    }
	
	private function outputResponse($status, $message = '', $errors = []) {
		$response = [
			'status'    => $status,
			'error_msg' => $message,
			'errors'    => $errors,
			'csrf_name' => $this->security->get_csrf_token_name(),
			'csrf_hash' => $this->security->get_csrf_hash()
		];

		$this->output->set_content_type('application/json')
					 ->set_output(json_encode($response));
	}
	
    public function getSkills(){
        $jsonFilePath = 'json/Skill.json';
        $term = $this->input->get('term');
    
        if (file_exists($jsonFilePath)) {
            $jsonData = file_get_contents($jsonFilePath);
            $Skills = json_decode($jsonData);
    
            if ($Skills !== null) {
                // Filter Skills based on the search term if provided
                if (!empty($term)) {
                    $Skills = array_filter($Skills, function ($Skill) use ($term) {
                        return stripos($Skill->name, $term) !== false;
                    });
                }
    
                // Set the response content type to JSON
                $this->output->set_content_type('application/json')->set_output(json_encode(array_values($Skills)));
            } else {
                // JSON decoding error
                $this->output->set_status_header(500)->set_output('Error decoding JSON file');
            }
        } else {
            // JSON file not found
            $this->output->set_status_header(404)->set_output('JSON file not found');
        }
    }
	
	public function get_search_data() {
		$allowed_files = [		
			'industry' => [
				'path' => 'json/Industry.json',
				'search_key' => 'industry',
				'id_key' => 'industry_id'
			],
			'functional_area' => [
				'path' => 'json/FunctionalType.json',
				'search_key' => 'role',
				'id_key' => 'role_id'
			],
			'job_profile' => [
				'path' => 'json/JobProfile.json',
				'search_key' => 'profile',
				'id_key' => 'profile'
			]			
		];

		$type = $this->input->get('type');
		$search_term = $this->input->get('term');

		if (!array_key_exists($type, $allowed_files)) {
			return $this->output->set_status_header(400)
				->set_content_type('application/json')
				->set_output(json_encode(['error' => 'Invalid search type']));
		}

		$config = $allowed_files[$type];
		$result = get_json_data($config['path'], $search_term, $config['search_key']);

		// Reform data with id and value
		$formatted_data = [];
		foreach($result['data'] as $item) {
			$formatted_data[] = [
				'id' => $item[$config['id_key']],
				'value' => $item[$config['search_key']]
			];
		}

		$this->output->set_status_header($result['status'])
			->set_content_type('application/json')
			->set_output(json_encode(
				isset($result['error']) 
					? ['error' => $result['error']] 
					: $formatted_data // Formatted data भेजें
			));
	}

	public function get_cities() {
		$term = $this->input->get('term');
		
		$this->load->model('Common_model');
		$results = $this->Common_model->searchCities($term);
		
		$data = array_map(function($city) {
			return [
				'id' => $city['city_id'],
				'text' => $city['city_name']			
			];
		}, $results);

		$this->output
			->set_content_type('application/json')
			->set_output(json_encode($data));
	}	
	
	// Common controller में  searc city me use ho raha hai 9.5.25
	public function get_job_cities() {
		$term = $this->input->get('term');
		
		$this->load->model('Common_model');
		$results = $this->Common_model->searchJobCities($term); // अपडेटेड मॉडल फंक्शन
		
		$data = array_map(function($city) {
			return [
				'id' => $city['city_id'],
				'text' => $city['city_name']			
			];
		}, $results);

		$this->output
			->set_content_type('application/json')
			->set_output(json_encode($data));
	}

	public function get_countries() {
		$term = $this->input->get('term');

		$this->load->model('Common_model');
		$results = $this->Common_model->searchCountries($term);

		$data = array_map(function($country) {
			return [
				'id' => $country['country_id'],
				'text' => $country['country_name']
			];
		}, $results);

		$this->output
			->set_content_type('application/json')
			->set_output(json_encode($data));
	}
		
	public function mail_to_register ($email, $name, $type, $verification_token, $user_id) {
		if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
			log_message('error', 'Invalid email: ' . $email);
			return false;
		}

		try {
			// Define subject
			$subject      = $this->getWelcomeSubject($type);
			$data['name'] = $name;
			$data['type'] = $type;

			// Simple verification link (no tracking)
			$data['verification_link'] = base_url("auth/verify?token={$verification_token}&type={$type}");

			// Render email body
			$message = $this->load->view('website/emails/welcome_letter', $data, TRUE);

			// Send email
			return SendEmailTo($email, $subject, $message);
			/*send_mailercloud_email(
				$email,				
				$name,
				$subject,
				$message			
			);*/
			
		} catch (Exception $e) {
			log_message('error', 'Error in mail_to_register: ' . $e->getMessage());
			return false;
		}
	}
		
	private function send_admin_notification ($employer_name, $employer_email, $employer_mobile, $company_name, $formData) {
		try {
			// Admin email - you can define this in config or constants
			$admin_email = defined('ADMIN_EMAIL') ? ADMIN_EMAIL : 'talentnetworkhub@gmail.com';
			
			$subject = 'New Employer Registration - ' . SITE_NAME;
			
			// Prepare email data
			$data = [
				'employer_name' => $employer_name,
				'employer_email' => $employer_email,
				'employer_mobile' => $employer_mobile,
				'company_name' => $company_name,
				'recruiter_type' => $formData['recuiter_type'] ?? 'Not specified',
				'company_type' => $formData['company_type'] ?? 'Not specified',
				'registration_date' => date('Y-m-d H:i:s'),
				'site_name' => SITE_NAME,
				'admin_dashboard_url' => base_url('admin/login') // Adjust this URL as per your admin panel
			];
			
			// Render email template
			$message = $this->load->view('website/emails/admin_employer_notification', $data, TRUE);
			
			// Send email to admin using BCC
			return SendEmailTo($admin_email, $subject, $message);
			
		} catch (Exception $e) {
			log_message('error', 'Error sending admin notification: ' . $e->getMessage());
			return false;
		}
	}

	private function getWelcomeSubject ($type) {
		switch ($type) {
			case 'employer':
				return 'Welcome to ' . SITE_NAME . '! Start Hiring Smarter Today';
			case 'candidate':
				return 'Welcome to ' . SITE_NAME . '! Let’s Build Your Career';
			// Add more cases if you have additional registration types
			default:
				return 'Welcome to ' . SITE_NAME . '! Let’s Get Started';
		}
	}

	
}

					