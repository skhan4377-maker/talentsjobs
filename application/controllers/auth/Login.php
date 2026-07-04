<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends CI_Controller {
	
    public function __construct(){
        parent::__construct();
        
        // Login controller constructor
        $this->output
            ->set_header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0")
            ->set_header("Cache-Control: private, no-store, no-cache, must-revalidate")
            ->set_header("Pragma: no-cache")
            ->set_header("Expires: 0");
            
        $this->load->model('login_mdl');
        $this->load->config('recaptcha');
    }	

	// candidate & employer
    public function index() {
        if ($this->session->userdata('logged_in')) {
            redirect($this->get_redirect_url($this->session->userdata('role')));
        }
        
		$data['title'] = 'Login | Talent Jobs';
		$data['description'] = 'Login to your Talent Jobs account as a candidate or employer to manage your profile, jobs, and applications.';
        $data['recaptcha_site_key'] = $this->config->item('recaptcha_site_key');

		$this->load->view('particles/header', $data);
		$this->load->view('particles/nav');
		$this->load->view('auth/user_login_template');
		$this->load->view('particles/footer');
	}
	
	private function verify_recaptcha_v3($token)
	{
		if (empty($token)) {
			return false;
		}

		$secret_key = $this->config->item('recaptcha_secret_key');
		$threshold  = $this->config->item('recaptcha_threshold');

		$response = file_get_contents(
			'https://www.google.com/recaptcha/api/siteverify?' . http_build_query([
				'secret'   => $secret_key,
				'response' => $token,
				'remoteip' => $this->input->ip_address()
			])
		);

		if (!$response) {
			log_message('error', 'reCAPTCHA v3: No response from Google');
			return false;
		}

		$result = json_decode($response);

		/*
		 * Expected:
		 * success = true
		 * score   = 0.0 – 1.0
		 * action  = "login"
		 */
		if (
			isset($result->success, $result->score, $result->action) &&
			$result->success === true &&
			$result->score >= $threshold &&
			$result->action === 'login'
		) {
			return true;
		}

		log_message('error', 'reCAPTCHA v3 Failed: ' . json_encode($result));
		return false;
	}
	
	// candidate & employer
	public function credential()
	{
		$return_data = [
			'csrf_token' => $this->security->get_csrf_hash(),
			'success'    => 0
		];

		// ---- Validation ----
		$this->form_validation->set_rules('login_id', 'Username', 'required|trim|max_length[50]|xss_clean');
		$this->form_validation->set_rules('login_password', 'Password', 'required|trim|min_length[8]|xss_clean');
		$this->form_validation->set_rules('role', 'Role', 'required|in_list[candidate,employer]|xss_clean');

		if (!$this->form_validation->run()) {
			$return_data['error_message'] = validation_errors('<div class="error">', '</div>');
			echo json_encode($return_data);
			exit;
		}

		// ---- reCAPTCHA ----
		$recaptcha_token = $this->input->post('recaptcha_token');
		if (!$this->verify_recaptcha_v3($recaptcha_token)) {
			$return_data['error_message'] = 'Suspicious activity detected. Please try again.';
			echo json_encode($return_data);
			exit;
		}

		// ---- Fetch input ----
		$post_data = $this->input->post(null, true);
		$role      = strtolower($post_data['role']);
		$username  = $post_data['login_id'];
		$password  = $post_data['login_password'];

		// ---- Get user ----
		$user = $this->login_mdl->get_user($username, $role);

		if (!$user) {
			usleep(300000); // delay to slow brute-force
			$return_data['error_message'] = 'Invalid credentials';
			echo json_encode($return_data);
			exit;
		}

		if ($user->role !== $role) {
			$return_data['error_message'] = 'Invalid role selection';
			echo json_encode($return_data);
			exit;
		}

		if (!empty($user->is_deleted)) {
			$return_data['error_message'] = 'Your account has been deleted by admin.';
			echo json_encode($return_data);
			exit;
		}

		if (!password_verify($password, $user->password)) {
			usleep(250000);
			$return_data['error_message'] = 'Invalid password';
			echo json_encode($return_data);
			exit;
		}

		// ---- Determine user ID based on role ----
		$user_id = ($role === 'employer') ? $user->employer_id : $user->candidate_id;

		// ---- 🔥 Check if candidate has an active premium plan ----
		$has_active_plan = false;
		if ($role === 'candidate') {
			// This method must exist in login_mdl (or any loaded model)
			$has_active_plan = $this->login_mdl->has_active_candidate_plan($user_id);
		}

		// ---- Generate new login token & update DB ----
		$login_token = bin2hex(random_bytes(32));
		$last_login  = date('Y-m-d H:i:s');
		$this->login_mdl->update_login_token($user_id, $role, $login_token);

		// ---- Regenerate session ID ----
		$this->session->sess_regenerate(true);

		// ---- Get role ID & permissions ----
		$role_id     = $this->login_mdl->get_role_id($role);
		$permissions = $this->login_mdl->get_user_permissions($role_id) ?? [];

		// ---- Build session data (now includes premium flag) ----
		$session_data = [
			'logged_in'      => true,
			'user_id'        => $user_id,
			'role'           => $role,
			'role_id'        => $role_id,
			'permissions'    => $permissions,
			'name'           => $user->name,
			'email'          => $user->email,
			'login_token'    => $login_token,
			'logo'           => !empty($user->logo) ? base_url($user->logo) : null,
			'last_login'     => $last_login,
			'has_active_plan'=> $has_active_plan 
		];

		$this->session->set_userdata($session_data);

		// ---- Success response ----
		$return_data['success']  = 1;
		$return_data['redirect'] = $this->get_redirect_url($role);

		echo json_encode($return_data);
		exit;
	}
	
	// candidate & employer
   private function get_redirect_url($role) {
        switch ($role) {
            case 'candidate': return base_url('candidate/dashboard');
            case 'employer':  return base_url('employer/dashboard');
            default:          return base_url('admin/dashboard');
        }
    }	
	
	public function login() {
	    
		$response = [];
		 // ✅ Already logged-in user ko login page se hatao
        if ($this->session->userdata('logged_in') && $this->session->userdata('role')) {
            redirect($this->get_redirect_url($this->session->userdata('role')));
        }

		if ($this->input->is_ajax_request()) {

			$csrf_token_name = $this->security->get_csrf_token_name();
			$csrf_hash       = $this->security->get_csrf_hash();

			$username = $this->input->post('username', true);
			$password = $this->input->post('password', true);

			$query = $this->db->select('employer_id, email, password, role, name, logo')
							  ->from('tb_employer')
							  ->where('email', $username)
							  ->get();

			if ($query->num_rows() > 0) {
				$user = $query->row();

				if (password_verify($password, $user->password)) {

					// ❌ Block employer & candidate
					if (in_array($user->role, ['employer', 'candidate'])) {
						return $this->output
							->set_content_type('application/json')
							->set_output(json_encode([
								'success' => false,
								'message' => 'Access denied.',
								'csrf_token_name' => $csrf_token_name,
								'csrf_hash' => $csrf_hash
							]));
					}

					// ✅ Security
					$this->session->sess_regenerate(true);

					// ✅ Token
					$login_token = bin2hex(random_bytes(32));
					$last_login  = date('Y-m-d H:i:s');
					$logo_url    = !empty($user->logo) ? base_url($user->logo) : null;

					$this->db->where('employer_id', $user->employer_id)
							 ->update('tb_employer', [
								 'login_token' => $login_token,
								 'last_login'  => $last_login
							 ]);

					// ✅ Use MODEL functions
					$role_id = $this->login_mdl->get_role_id($user->role);

					$permissions = $this->login_mdl->get_user_permissions($role_id) ?? [];

					// ✅ Session
					$this->session->set_userdata([
						'user_id'     => $user->employer_id,
						'name'        => $user->name,
						'email'       => $user->email,
						'role'        => $user->role,
						'role_id'     => $role_id,
						'permissions' => $permissions,
						'logo'        => $logo_url,
						'login_token' => $login_token,
						'last_login'  => $last_login,
						'logged_in'   => true
					]);

					$response = [
						'success'         => true,
						'message'         => 'Login successful.',
						'redirect_url'    => site_url('admin/dashboard'),
						'csrf_token_name' => $csrf_token_name,
						'csrf_hash'       => $csrf_hash
					];

				} else {
					$response = [
						'success' => false,
						'message' => 'Invalid password.',
						'csrf_token_name' => $csrf_token_name,
						'csrf_hash' => $csrf_hash
					];
				}

			} else {
				$response = [
					'success' => false,
					'message' => 'User not found.',
					'csrf_token_name' => $csrf_token_name,
					'csrf_hash' => $csrf_hash
				];
			}

			return $this->output
				->set_content_type('application/json')
				->set_output(json_encode($response));
		}

		// Page load
		$data['title'] = 'Admin Login | Talent IT Solutions';
		$data['description'] = 'Secure admin access panel';

		$this->load->view('particles/header', $data);
		$this->load->view('particles/nav');
		$this->load->view('auth/admin_login_template');
		$this->load->view('particles/footer');
	}	
	
}
?>
