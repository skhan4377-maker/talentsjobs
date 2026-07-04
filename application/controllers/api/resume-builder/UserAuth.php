<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . 'libraries/REST_Controller.php';

class UserAuth extends REST_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('login_mdl');
        $this->load->library('jwt_lib');
        $this->load->config('jwt');
        $this->load->library('form_validation');
    }

    // POST: api/userauth/login
    /*public function login_post() {
        $email    = $this->post('email', TRUE);
        $password = $this->post('password', TRUE);
        $role     = 'candidate';

        // ✅ Simple and secure validation
        $this->form_validation->set_data($this->post());
        $this->form_validation->set_rules('email', 'Email', 'required|valid_email|max_length[100]');
        $this->form_validation->set_rules('password', 'Password', 'required|min_length[8]|max_length[32]');

        if ($this->form_validation->run() === FALSE) {
            return $this->response([
                'status'  => false,
                'message' => 'Validation failed',
                'errors'  => $this->form_validation->error_array()
            ], REST_Controller::HTTP_BAD_REQUEST);
        }

        // ✅ Sanitize inputs
        $email = $this->security->xss_clean($email);
        $password = $this->security->xss_clean($password);

        // Fetch user
        $user = $this->login_mdl->get_user($email, $role);
        if (!$user) {
            return $this->response([
                'status'  => false,
                'message' => 'Invalid email or password'
            ], REST_Controller::HTTP_UNAUTHORIZED);
        }

        // Verify password
        if (!password_verify($password, $user->password)) {
            return $this->response([
                'status'  => false,
                'message' => 'Invalid password'
            ], REST_Controller::HTTP_UNAUTHORIZED);
        }

        // Block deleted account
        if (isset($user->is_deleted) && $user->is_deleted == 1) {
            return $this->response([
                'status'  => false,
                'message' => 'Your account is deleted'
            ], REST_Controller::HTTP_FORBIDDEN);
        }

       
        $payload = [
            'user_id' => $user->candidate_id,
            'email'   => $user->email,
            'role'    => $role
        ];

        $access_token = $this->jwt_lib->generate_token($payload);

       
        $refresh_token = $this->jwt_lib->generate_refresh_token();
        $refresh_exp   = time() + $this->config->item('refresh_exp');

        // Save into DB
        $this->login_mdl->save_refresh_token(
            $user->candidate_id,
            $refresh_token,
            $refresh_exp
        );

        // Update last login
        $this->login_mdl->update_last_login($user->candidate_id, $role);

        return $this->response([
            'status'  => true,
            'message' => 'Login successful',
            'access_token'  => $access_token,
            'refresh_token' => $refresh_token,
            'token_type'    => 'Bearer',
            'expires_in'    => $this->config->item('jwt_exp'),
            'user' => [
                'id'    => $user->candidate_id,
                'name'  => $user->name,
                'email' => $user->email,
                'role'  => $role,
                'logo'  => null
            ]
        ], REST_Controller::HTTP_OK);
    }*/
	
   
    // POST: api/userauth/refresh
	/*public function refresh_post() {
		$refresh_token = $this->post('refresh_token', TRUE);
		if (!$refresh_token) {
			return $this->response(['status' => false, 'message' => 'Refresh token required'], 400);
		}
		$refresh_token = $this->security->xss_clean($refresh_token);

		$tokenRow = $this->login_mdl->verify_refresh_token($refresh_token);
		if (!$tokenRow) {
			return $this->response(['status' => false, 'message' => 'Invalid or expired refresh token'], 401);
		}

		// नया एक्सेस टोकन बनाएँ
		$payload = ['user_id' => $tokenRow->user_id];
		$new_access_token = $this->jwt_lib->generate_token($payload);

		// (वैकल्पिक) रीफ़्रेश टोकन रोटेशन के लिए पुराना रिवोक कर नया बनाएँ
		$this->login_mdl->revoke_refresh_token($refresh_token);
		$new_refresh_token = bin2hex(random_bytes(32));
		$this->login_mdl->save_refresh_token($tokenRow->user_id, $new_refresh_token, time() + (86400 * 30));

		return $this->response([
			'status' => true,
			'message' => 'Token refreshed',
			'access_token' => $new_access_token,
			'refresh_token' => $new_refresh_token,  // केवल अगर रोटेशन कर रहे हैं
			'token_type' => 'Bearer',
			'expires_in' => $this->config->item('jwt_exp'),
		], 200);
	}*/
    
	public function login_token_get() {
		$loginToken = $this->input->get('token', TRUE);

		if (!$loginToken) {
			return $this->response([
				'status'  => false,
				'message' => 'Token missing'
			], 400);
		}

		// Sanitize
		$loginToken = $this->security->xss_clean(trim($loginToken));

		// =====================================================
		// FETCH CANDIDATE BY login_token (tb_candidate)
		// =====================================================
		$user = $this->db
			->select('candidate_id, name, email')
			->where('login_token', $loginToken)
			->where('is_deleted', 0)
			->limit(1)
			->get('tb_candidate')
			->row();

		if (!$user) {
			return $this->response([
				'status'  => false,
				'message' => 'Invalid login token'
			], 401);
		}

		// ✅ login_token को null नहीं करेंगे – बार-बार उपयोग के लिए छोड़ देंगे

		// =====================================================
		// CHECK FOR EXISTING VALID REFRESH TOKEN
		// =====================================================
		$existing = $this->db
			->where('user_id', $user->candidate_id)
			->where('revoked', 0)
			->where('expires_at >', date('Y-m-d H:i:s'))
			->limit(1)
			->get('tb_refresh_tokens')
			->row();

		$refresh_token = null;

		if ($existing) {
			// पहले से valid refresh token मौजूद है – उसे reuse करेंगे
			$refresh_token = $existing->refresh_token;
		} else {
			// कोई valid token नहीं है – नया token जनरेट करें और पुराने सब revoke करें
			$this->db->where('user_id', $user->candidate_id)
					 ->update('tb_refresh_tokens', ['revoked' => 1]);

			$refresh_token = bin2hex(random_bytes(32));

			$this->db->insert('tb_refresh_tokens', [
				'user_id'               => $user->candidate_id,
				'refresh_token'         => $refresh_token,
				'expires_at'            => date('Y-m-d H:i:s', time() + (86400 * 30)),
				'revoked'               => 0,
				'processing'            => 0,
				'processing_started_at' => null,
				'created_at'            => date('Y-m-d H:i:s')
			]);
		}

		// =====================================================
		// GENERATE NEW ACCESS TOKEN (हर बार नया)
		// =====================================================
		try {
			$payload = [
				'user_id' => $user->candidate_id,
				'email'   => $user->email,
				'role'    => 'candidate'
			];

			$access_token = $this->jwt_lib->generate_token($payload);

			return $this->response([
				'status'        => true,
				'message'       => 'Login successful',
				'access_token'  => $access_token,
				'refresh_token' => $refresh_token,
				'token_type'    => 'Bearer',
				'expires_in'    => $this->config->item('jwt_exp'),
				'user' => [
					'id'    => $user->candidate_id,
					'name'  => $user->name,
					'email' => $user->email,
					'role'  => 'candidate'
				]
			], 200);

		} catch (Exception $e) {
			log_message('error', 'Login Token Error: ' . $e->getMessage());
			return $this->response([
				'status'  => false,
				'message' => 'Authentication failed'
			], 500);
		}
	}

    // POST: api/userauth/logout
    /*public function logout_post() {
        $refresh_token = $this->post('refresh_token', TRUE);

        if (!$refresh_token) {
            return $this->response([
                'status' => false,
                'message' => 'Refresh token required'
            ], 400);
        }

        // ✅ Secure validation
        $refresh_token = $this->security->xss_clean($refresh_token);

        // Mark as revoked
        $this->login_mdl->revoke_refresh_token($refresh_token);

        return $this->response([
            'status' => true,
            'message' => 'Logout successful'
        ], 200);
    }*/
        
    /*public function register_post() {
        // 📥 Get JSON or POST data
        $postData = $this->post();

        // ✅ Simple and secure validation
        $this->form_validation->set_data($postData);
        $this->form_validation->set_rules('first_name', 'First Name', 'required|trim|min_length[2]|max_length[50]|regex_match[/^[a-zA-Z\s\-\.\']+$/]');
        $this->form_validation->set_rules('last_name', 'Last Name', 'required|trim|min_length[2]|max_length[50]|regex_match[/^[a-zA-Z\s\-\.\']+$/]');
        $this->form_validation->set_rules('email', 'Email', 'required|trim|valid_email|max_length[100]|is_unique[tb_candidate.email]');
        $this->form_validation->set_rules('password', 'Password', 'required|trim|min_length[8]|max_length[32]');
        
        // Optional template validation
        if (!empty($postData['template'])) {
            $this->form_validation->set_rules('template', 'Template', 'trim|numeric');
        }

        if ($this->form_validation->run() === FALSE) {
            return $this->response([
                'status' => false,
                'message' => 'Validation failed',
                'errors' => $this->form_validation->error_array()
            ], REST_Controller::HTTP_BAD_REQUEST);
        }

        // ✅ Sanitize all inputs
        $first_name = $this->security->xss_clean(trim($postData['first_name']));
        $last_name = $this->security->xss_clean(trim($postData['last_name']));
        $email = $this->security->xss_clean(trim($postData['email']));
        $password = $this->security->xss_clean($postData['password']);
        $template = !empty($postData['template']) ? $this->security->xss_clean($postData['template']) : null;

        // 🛠 Prepare registration data
        $data = [
            'name'           => $first_name,
            'last_name'      => $last_name,
            'email'          => $email,
            'password'       => password_hash($password, PASSWORD_BCRYPT),
            'is_resume_user' => 1,
            'created_at'     => date('Y-m-d H:i:s')
        ];

        // 💾 Save candidate record
        $this->load->model('candidate/Profile_mdl');
        $insert_id = $this->Profile_mdl->save_candidate_record($data);

        if ($insert_id <= 0) {
            return $this->response([
                'status'  => false,
                'message' => 'Registration failed. Please try again.'
            ], REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
        }

        // 🎨 Resume Template Mapping (optional if template is sent)
        if (!empty($template)) {
            $templateData = [
                'template_id' => (int)$template,
                'user_id'     => $insert_id,
                'updated_at'  => date('Y-m-d H:i:s')
            ];

            $exists = $this->db->get_where('tb_resume_drafts', ['user_id' => $insert_id])->row();
            if ($exists) {
                $this->db->where('user_id', $insert_id)->update('tb_resume_drafts', $templateData);
            } else {
                $templateData['created_at'] = date('Y-m-d H:i:s');
                $this->db->insert('tb_resume_drafts', $templateData);
            }
        }

        // 🔑 Generate JWT Tokens (Same as login)
        $role = 'candidate';
        
        // Fetch the newly created user
        $user = $this->login_mdl->get_user_by_id($insert_id, $role);
        
        if (!$user) {
            return $this->response([
                'status'  => false,
                'message' => 'Registration successful but user data not found.'
            ], REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
        }

        
        $payload = [
            'user_id' => $user->candidate_id,
            'email'   => $user->email,
            'role'    => $role
        ];

        $access_token = $this->jwt_lib->generate_token($payload);

        $refresh_token = $this->jwt_lib->generate_refresh_token();
        $refresh_exp   = time() + $this->config->item('refresh_exp');

        // Save into DB
        $this->login_mdl->save_refresh_token(
            $user->candidate_id,
            $refresh_token,
            $refresh_exp
        );
        
        $user_name = $data['name'] . ' ' . $data['last_name'];
         
        // Update last login
        $this->login_mdl->update_last_login($user->candidate_id, $role);
        $this->send_welcome_email($data['email'], $user_name);
        
        // 📤 Final Response with JWT Tokens
        return $this->response([
            'status'  => true,
            'message' => 'Registration successful.',
            'access_token'  => $access_token,
            'refresh_token' => $refresh_token,
            'token_type'    => 'Bearer',
            'expires_in'    => $this->config->item('jwt_exp'),
            'user'    => [
                'id'            => $user->candidate_id,
                'name'          => $user->name,
                'first_name'    => $data['name'],
                'last_name'     => $data['last_name'],
                'email'         => $data['email'],
                'role'          => $role,
                'is_resume_user'=> 1,
                'template'      => $template
            ]
        ], REST_Controller::HTTP_OK);
    }
    
		
    function send_welcome_email ($email, $user_name, $login_url = 'https://resume.talentsjobs.in/login') {
		$support_email = defined('CONTACT_EMAIL') ? CONTACT_EMAIL : 'info@talentsjobs.in';
		$support_phone = defined('CONTACT_PHONE') ? CONTACT_PHONE : '';

		$subject = 'Welcome to Talents Jobs - Start Building Your Resume';

		$html = "
		<!DOCTYPE html>
		<html>
		<head>
			<meta charset='UTF-8'>
			<title>Welcome</title>
		</head>
		<body style='margin:0;padding:0;background:#ffffff;font-family:Arial,sans-serif;color:#333;'>
			<table width='100%' border='0' cellspacing='0' cellpadding='0' style='max-width:600px;margin:auto;padding:20px;'>
				<tr>
					<td style='text-align:center;padding:20px 0;'>
						<h2 style='margin:0;font-size:24px;color:#333;'>Welcome to Talents Jobs!</h2>
					</td>
				</tr>

				<tr>
					<td style='background:#f9f9f9;padding:20px;border-radius:5px;'>
						<p style='font-size:15px;margin:0 0 12px;'>Hello <strong>{$user_name}</strong>,</p>

						<p style='font-size:15px;margin:0 0 12px;'>
							Welcome to Talents Jobs! We're excited to have you on board.
						</p>

						<div style='background:#f8f9fa;border-left:4px solid #667eea;padding:15px;margin:20px 0;'>
							<p style='margin:0;color:#555;font-size:14px;'>
								<strong>Your account has been successfully created.</strong><br>
								You can now start building your professional resume.
							</p>
						</div>

						<p style='font-size:15px;margin:0 0 10px;'><strong>With Talents Jobs, you can:</strong></p>

						<ul style='font-size:14px;margin:10px 0 20px;padding-left:18px;'>
							<li>Create professional resumes in minutes</li>
							<li>Choose from multiple templates</li>
							<li>Download in PDF format</li>
							<li>Track your job applications</li>
						</ul>

						<div style='text-align:center;margin:30px 0;'>
							<a href='{$login_url}' 
							   style='background:#667eea;background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);
									  color:white;padding:14px 30px;text-decoration:none;border-radius:5px;
									  font-weight:bold;font-size:15px;display:inline-block;'>
							   Login to Your Account
							</a>
						</div>

						<p style='font-size:14px;margin:0 0 10px;'>
							If you have any questions, feel free to contact our support team.
						</p>

						<!-- Support Block -->
						<div style='margin-top:15px;padding:15px;background:#ffffff;border:1px solid #e0e0e0;border-radius:6px;'>
							<p style='margin:0 0 6px;font-size:14px;color:#333;'>
								<strong>Support</strong>
							</p>
							<p style='margin:0;font-size:13px;color:#555;line-height:1.6;'>
								Email:
								<a href='mailto:{$support_email}' style='color:#667eea;text-decoration:none;'>
									{$support_email}
								</a><br>
								Phone: {$support_phone}
							</p>
						</div>

					</td>
				</tr>

				<tr>
					<td style='text-align:left;color:#777;font-size:12px;padding-top:20px;'>
						<p style='margin:0;'>
							Best regards,<br>
							<strong>Talents Jobs Team</strong>
						</p>
					</td>
				</tr>
			</table>
		</body>
		</html>
		";

		return send_mailercloud_email($email, $user_name, $subject, $html);
	}*/

}