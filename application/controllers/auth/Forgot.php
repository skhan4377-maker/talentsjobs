<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

class Forgot extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Forgot_model');
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

    public function index() {
		$data['title'] ='Forgot Password';
		$data['description'] ='Reset your password to access your account';
		// ✅ CONFIG se site key bhejo
		$data['recaptcha_site_key'] = $this->config->item('recaptcha_site_key');
	
		$this->load->view('particles/header',$data);
		$this->load->view('particles/nav');
        $this->load->view('forgot/request_reset');
		$this->load->view('particles/footer');
    }

    public function send_link() {
		// -------------------------------
		// reCAPTCHA v3 verification
		// -------------------------------
		$recaptcha_token = $this->input->post('recaptcha_token', TRUE);

		if (!$this->verify_recaptcha_v3($recaptcha_token, 'forgot_password')) {
			return $this->output
				->set_content_type('application/json')
				->set_output(json_encode([
					'success'    => false,
					'message'    => 'Suspicious activity detected. Please try again.',
					'csrf_token' => $this->security->get_csrf_hash()
				]));
		}

		// -------------------------------
		// Inputs
		// -------------------------------
		$email = $this->input->post('email', TRUE);
		$role  = $this->input->post('role', TRUE);

		if (!filter_var($email, FILTER_VALIDATE_EMAIL) || !in_array($role, ['candidate', 'employer'])) {
			return $this->output->set_content_type('application/json')->set_output(json_encode([
				'success'    => false,
				'message'    => 'Invalid email or role',
				'csrf_token' => $this->security->get_csrf_hash()
			]));
		}

		// -------------------------------
		// User lookup
		// -------------------------------
		$user = $this->Forgot_model->get_user_by_email_and_role($email, $role);
		if (!$user) {
			return $this->output->set_content_type('application/json')->set_output(json_encode([
				'success'    => false,
				'message'    => 'No account found for this email and role',
				'csrf_token' => $this->security->get_csrf_hash()
			]));
		}

		// Block rejected users
		if (isset($user->status) && strtolower($user->status) === 'rejected') {
			return $this->output->set_content_type('application/json')->set_output(json_encode([
				'success'    => false,
				'message'    => 'Your account has been rejected. Please contact support.',
				'csrf_token' => $this->security->get_csrf_hash()
			]));
		}

		// -------------------------------
		// Token generation
		// -------------------------------
		$user_id = ($role === 'candidate') ? $user->candidate_id : $user->employer_id;
		$name    = $user->name;

		$token   = bin2hex(random_bytes(32));
		$expires = date('Y-m-d H:i:s', strtotime('+1 hour'));

		$this->Forgot_model->save_token_by_role($user_id, $role, $token, $expires);

		$reset_link = base_url("reset-password?token={$token}&role={$role}");

		$this->_send_email($email, $name, $reset_link);

		// -------------------------------
		// Success response
		// -------------------------------
		return $this->output
			->set_content_type('application/json')
			->set_output(json_encode([
				'success'    => true,
				'message'    => 'Password reset link sent to your email.',
				'csrf_token' => $this->security->get_csrf_hash()
			]));
	}



	public function reset() {
		$token = $this->input->get('token', TRUE);
		$role  = $this->input->get('role', TRUE);

		$user = $this->Forgot_model->validate_token($token, $role);
		if (!$user) {
			show_error("Invalid or expired reset link");
		}

		$data['token'] = $token;
		$data['role']  = $role;
		$data['title'] = 'Reset Password';
		$data['description'] ='';
		// ✅ CONFIG se site key bhejo
		$data['recaptcha_site_key'] = $this->config->item('recaptcha_site_key');
		$this->load->view('particles/header', $data);
		$this->load->view('particles/nav');
		$this->load->view('forgot/reset_password', $data);
		$this->load->view('particles/footer');
	}

    public function save_password() {
		$this->load->library('form_validation');

		$token    = $this->input->post('token', TRUE);
		$role     = $this->input->post('role', TRUE);
		$password = $this->input->post('password', TRUE);
		$confirm  = $this->input->post('confirm_password', TRUE);

		// Validation rules
		$this->form_validation->set_rules('password', 'Password', 'required|min_length[8]');
		$this->form_validation->set_rules('confirm_password', 'Confirm Password', 'required|matches[password]');

		if ($this->form_validation->run() === FALSE) {
			$data['token'] = $token;
			$data['role']  = $role;
			$data['title'] = 'Reset Password';
			// ✅ CONFIG se site key bhejo
			$data['recaptcha_site_key'] = $this->config->item('recaptcha_site_key');
	
			$this->load->view('particles/header', $data);
			$this->load->view('particles/nav');
			$this->load->view('forgot/reset_password', $data);
			$this->load->view('particles/footer');
			return;
		}

		// Validate token and fetch user
		$user = $this->Forgot_model->validate_token($token, $role);
		if (!$user) {
			show_error("Invalid or expired reset token");
		}

		// Determine user ID
		$user_id = ($role === 'candidate') ? $user->candidate_id : $user->employer_id;

		// Secure password hash
		$hashed_password = password_hash($password, PASSWORD_BCRYPT);

		// Update password and clear token
		$this->Forgot_model->update_password($user_id, $role, $hashed_password);
		$this->Forgot_model->clear_token($user_id, $role);

		// Redirect after success
		redirect('auth/login?reset=success');
	}

    private function _send_email($to, $name, $link) {
		$subject = "Reset Your Password - " . SITE_NAME;

		$message = '
		<div style="font-family:Arial,sans-serif; background-color:#f4f4f4; padding:30px;">
			<div style="max-width:600px; margin:auto; background-color:#ffffff; border-radius:8px; overflow:hidden; box-shadow:0 4px 10px rgba(0,0,0,0.1);">
				<div style="padding:30px 30px 10px 30px;">
					<h2 style="color:#333333; font-size:22px; margin-bottom:20px;">Hi ' . htmlspecialchars($name) . ',</h2>
					<p style="font-size:15px; color:#555555; line-height:1.6; margin:0 0 20px;">
						You recently requested to reset your password. Click the button below to set a new one.
					</p>
					<div style="text-align:center; margin:30px 0;">
						<a href="' . $link . '" style="background-color:#4F46E5; color:#ffffff; text-decoration:none; padding:12px 24px; font-size:16px; border-radius:6px; display:inline-block;">
							Reset Password
						</a>
					</div>
					<p style="font-size:14px; color:#777777; margin-top:30px;">
						If you didnt request this, you can safely ignore this email. This link will expire in 1 hour.
					</p>
					<p style="font-size:14px; color:#777777; margin-top:10px;">
						Regards,<br><strong>' . SITE_NAME . ' Team</strong>
					</p>
				</div>
			</div>
		</div>';

		SendEmailTo($to, $subject, $message);		
		//send_mailercloud_email($to,	$name, $subject, $message);
	}
}
?>