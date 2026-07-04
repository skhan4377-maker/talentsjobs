<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Verifications extends CI_Controller {
    public function __construct(){
		parent::__construct();
	} 	
		
	public function ajaxInitiateEmailVerification() {
		if ($this->session->userdata('role') == 'employer') {
		    $name = $this->session->userdata('name');
		    $email = $this->session->userdata('email');
			$response = $this->initiateEmailVerification('employer', $name, $email);
		} elseif ($this->session->userdata('role') == 'candidate') {
		    $name = $this->session->userdata('name');
		    $email = $this->session->userdata('email');
			$response = $this->initiateEmailVerification('candidate', $name, $email);
		} else {
			$response = ['status' => 'error', 'message' => 'User not logged in'];
		}

		echo json_encode($response);
	}

	private function initiateEmailVerification($userType, $name, $email) {
		$this->load->model('Verification_mdl');
		$verification_token = uniqid();

		$this->Verification_mdl->storeVerificationToken($email, $verification_token, $userType);
		$this->mail_to_verificaton($email, $name, $userType, $verification_token);

		return ['status' => 'success', 'message' => 'Email verification code has been sent. Please check your inbox and spam folder.'];
	}
    
	public function submit_details() {
		 if ($this->input->is_ajax_request()) {
			// Retrieve POST data
			$firstName = trim($this->input->post('firstName'));
			$lastName  = trim($this->input->post('lastName'));
			$email     = trim($this->input->post('email'));
			$specialOffers = $this->input->post('specialOffers');
			$templateId = trim($this->input->post('templateId')); // Retrieve template_id

			// Basic validation
			if (empty($firstName) || empty($lastName) || empty($email)) {
				echo json_encode(['status' => 'error', 'message' => 'All fields are required.']);
				return;
			}
			
			if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
				echo json_encode(['status' => 'error', 'message' => 'Invalid email address.']);
				return;
			}

			// Load the model (adjust the model name as needed)
			$this->load->model('home_mdl');

			// Check if the email already exists in the database
			if ($this->home_mdl->check_exist_value(FALSE, $email, 'candidate')) {
				echo json_encode([
					'status' => 'error', 
					'message' => 'Email already exists. Please <a href="'.base_url('auth/login').'" class="font-bold text-blue-600">login</a>.'
				]);
				return;
			}

			// Prepare data to insert
			$data = [
				'name'          => $firstName,
				'last_name'     => $lastName,
				'email'         => $email,
				'special_offers'=> $specialOffers ? 1 : 0,
				// Add other fields if necessary
			];

			$this->load->model('candidate/Candidate_mdl');            
			
			// Insert data using the model
			$insert_id = $this->Candidate_mdl->save_candidate_record($data);

			if ($insert_id > 0) {    
				$this->common_handleRegistrationSuccess($insert_id, $email, $firstName .' '. $lastName);
				
				// Construct the redirect URL
				$redirectUrl = site_url('resume/') . $templateId . '/edit';
				
				// Return success with redirect URL
				echo json_encode(['status' => 'success', 'message' => 'Details saved successfully.', 'redirectUrl' => $redirectUrl]);
				
			} else {
				echo json_encode(['status' => 'error', 'message' => 'Failed to save details. Please try again.']);
			}
		} else {
			show_404();
		}
	}

	public function verifyEmail() {
		// Load necessary libraries
		$this->load->helper('url');
		$this->load->library('session');
		
		// Retrieve token and type from the query string
		$token = $this->input->get('token');
		$type  = $this->input->get('type');

		// Check if required parameters are provided
		if (!$token || !$type) {
			echo "<script>alert('Invalid verification request: missing token or type.');</script>";
			show_error("Invalid verification request: missing token or type.", 400);
			return;
		}

		// Determine the corresponding table name and primary key column based on user type
		$table_name = ($type == 'employer') ? 'tb_employer' : 'tb_candidate';
		$id_field = ($type == 'employer') ? 'employer_id' : 'candidate_id';

		// Check if the user with the provided token and type exists
		$user = $this->db->get_where($table_name, array('verification_token' => $token))->row_array();
		
		if ($user) {
			// Check if the token is still valid (not expired)
			$tokenExpiration = strtotime($user['token_expiration']);
			$currentTimestamp = time();

			if ($currentTimestamp <= $tokenExpiration) {
				// Update verification status and clear the token using the dynamic primary key
				$this->db->where($id_field, $user[$id_field]);
				$this->db->update($table_name, array(
					'is_verified' => 1,
					'verification_token' => null,
					'updated_at' => date('Y-m-d H:i:s')
				));
				
				 echo "<script>alert('Email verified successfully. You can now log in.');</script>";

				// Redirect based on user type
				if ($type == 'employer') {
					redirect('employer/profile');
				} else {
					redirect('candidate/profile');
				}
			} else {
				// Token has expired
				echo "<script>alert('Verification token has expired. Please request a new one.');</script>";
				
				// Redirect based on user type
				if ($type == 'employer') {
					redirect('employer/profile');
				} else {
					redirect('candidate/profile');
				}
			}
		} else {
			 // Invalid verification token or user type
			echo "<script>alert('Invalid verification token or user type.');</script>";
			redirect('auth/login');
		}
	}


	
public function mail_to_verificaton($email, $name, $type, $verification_token) {
    if ($email && filter_var($email, FILTER_VALIDATE_EMAIL)) {
        // Prepare email content
        $subject = "✅ Verify Your Email | " . SITE_NAME;        
        $verification_link = base_url('auth/verify') . '?' . http_build_query([
            'token' => $verification_token, 
            'type' => $type
        ]);

        // Modern HTML email template with inline CSS
        $message = <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        @media only screen and (max-width: 600px) {
            .container { width: 95%!important; }
            .header { padding: 25px 15px!important; }
            .cta-button { width: 100%!important; }
        }
    </style>
</head>
<body style="margin: 0; padding: 20px 0; background: #f7fafc;">
    <table class="container" width="100%" border="0" cellspacing="0" cellpadding="0" style="max-width: 600px; margin: 0 auto; font-family: 'Segoe UI', system-ui, sans-serif;">
        <!-- Header -->
        <tr>
            <td class="header" style="background: linear-gradient(135deg, #6366f1 0%, #a855f7 100%); padding: 40px 30px; border-radius: 12px 12px 0 0; text-align: center;">
                <img src="{$_SERVER['REQUEST_SCHEME']}://{$_SERVER['HTTP_HOST']}/assets/frontend/logo.png" alt="Logo" style="height: 40px; margin-bottom: 15px;">
                <h1 style="color: #ffffff; margin: 0; font-size: 24px;">Email Verification</h1>
            </td>
        </tr>

        <!-- Content -->
        <tr>
            <td style="background: #ffffff; padding: 40px 30px; border-radius: 0 0 12px 12px;">
                <h2 style="color: #1f2937; margin: 0 0 25px; font-size: 20px;">Hi {$name},</h2>
                <p style="color: #4b5563; line-height: 1.6; margin: 0 0 25px;">
                    Please verify your email address to complete your {$type} account setup. 
                    This ensures we can communicate important updates and keep your account secure.
                </p>
                
                <!-- CTA Button -->
                <table width="100%" cellspacing="0" cellpadding="0">
                    <tr>
                        <td align="center" style="padding: 15px 0 30px;">
                            <a href="{$verification_link}" class="cta-button" 
                               style="display: inline-block; background: #6366f1; color: #ffffff!important; 
                                      text-decoration: none; padding: 14px 35px; border-radius: 8px; 
                                      font-weight: 600; transition: all 0.3s ease;">
                                Verify Your Email
                            </a>
                        </td>
                    </tr>
                </table>

                <!-- Secondary Text -->
                <p style="color: #6b7280; font-size: 14px; line-height: 1.6; margin: 0;">
                    If you didn't create this account, you can safely ignore this email.<br>
                    Link expires in 24 hours. Can't click the button? 
                    <a href="{$verification_link}" 
                       style="color: #6366f1; text-decoration: underline;">Copy this link</a>
                </p>
            </td>
        </tr>

        <!-- Footer -->
        <tr>
            <td style="padding: 30px 0 0; text-align: center;">
                <p style="color: #9ca3af; font-size: 12px; line-height: 1.6; margin: 0;">
                    © {date('Y')} {SITE_NAME}. All rights reserved.<br>
                    <a href="{base_url('privacy-policy')}" style="color: #9ca3af; text-decoration: none;">Privacy Policy</a> | 
                    <a href="{base_url('terms')}" style="color: #9ca3af; text-decoration: none;">Terms of Service</a>
                </p>
            </td>
        </tr>
    </table>
    
    <!-- Tracking Pixel -->
    <img src="{base_url('track/email-open/'.$verification_token)}" 
         alt="" width="1" height="1" style="display: none;">
</body>
</html>
HTML;

       
        $status = SendEmailTo(
            $email,
            $subject,
            $message
			);

		//$status = send_mailercloud_email($email, $name, $subject, $message);
        return $status;
    }
    return false;
}


	
}