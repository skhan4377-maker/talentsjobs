<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . '/libraries/REST_Controller.php';

class ForgotPassword extends REST_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Forgot_model');
        $this->load->helper('email');
        $this->load->library('form_validation');
    }

    /**
     * POST: /api/forgotpassword/send-otp
     * Send OTP to email for password reset
     */
    public function send_otp_post() {
        // Clean expired OTPs first
        $this->Forgot_model->clean_expired_otps();
        $role = 'candidate';
        
        $postData = $this->post();

        // ✅ Simple validation
        $this->form_validation->set_data($postData);
        $this->form_validation->set_rules('email', 'Email', 'required|valid_email|max_length[100]');

        if ($this->form_validation->run() === FALSE) {
            return $this->response([
                'status' => false,
                'message' => 'Validation failed',
                'errors' => $this->form_validation->error_array()
            ], REST_Controller::HTTP_BAD_REQUEST);
        }

        $email = $this->security->xss_clean(trim($postData['email']));

        // Check if user exists
        $user = $this->Forgot_model->get_user_by_email_and_role($email, $role);
        if (!$user) {
            return $this->response([
                'status' => false,
                'message' => 'This email is not registered with us.'
            ], REST_Controller::HTTP_BAD_REQUEST);
        }

        // Generate OTP
        $otp_data = $this->Forgot_model->generate_otp($email);
        
        if (!$otp_data) {
            return $this->response([
                'status' => false,
                'message' => 'Failed to generate OTP. Please try again.'
            ], REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
        }

        // Send OTP via email using helper
        $email_sent = send_otp_email($email, $otp_data['otp'], $otp_data['user_name']);

        if (!$email_sent) {
            // Log the error but don't reveal to user
            log_message('error', "Failed to send OTP email to: $email");
        }

        // In development, you might want to return OTP for testing
        $is_development = ENVIRONMENT === 'development';
        
        return $this->response([
            'status' => true,
            'message' => 'OTP has been sent to your email.',
            'debug_otp' => $is_development ? $otp_data['otp'] : null,
            'expires_in' => '10 minutes'
        ], REST_Controller::HTTP_OK);
    }

    /**
     * POST: /api/forgotpassword/verify-otp
     * Verify OTP and return reset token
     */
    public function verify_otp_post() {
        $postData = $this->post();

        // ✅ Simple validation
        $this->form_validation->set_data($postData);
        $this->form_validation->set_rules('email', 'Email', 'required|valid_email|max_length[100]');
        $this->form_validation->set_rules('otp', 'OTP', 'required|exact_length[6]|numeric');

        if ($this->form_validation->run() === FALSE) {
            return $this->response([
                'status' => false,
                'message' => 'Validation failed',
                'errors' => $this->form_validation->error_array()
            ], REST_Controller::HTTP_BAD_REQUEST);
        }

        $email = $this->security->xss_clean(trim($postData['email']));
        $otp = $this->security->xss_clean(trim($postData['otp']));

        // Verify OTP
        $token = $this->Forgot_model->verify_otp($email, $otp);

        if (!$token) {
            return $this->response([
                'status' => false,
                'message' => 'Invalid or expired OTP.'
            ], REST_Controller::HTTP_UNAUTHORIZED);
        }

        return $this->response([
            'status' => true,
            'message' => 'OTP verified successfully.',
            'reset_token' => $token
        ], REST_Controller::HTTP_OK);
    }

    /**
     * POST: /api/forgotpassword/reset-password
     * Reset password using reset token
     */
    public function reset_password_post() {
        $postData = $this->post();
        $role = 'candidate';

        // ✅ Simple and secure validation
        $this->form_validation->set_data($postData);
        $this->form_validation->set_rules('email', 'Email', 'required|valid_email|max_length[100]');
        $this->form_validation->set_rules('reset_token', 'Reset Token', 'required');
        $this->form_validation->set_rules('new_password', 'New Password', 'required|min_length[8]|max_length[32]');
        $this->form_validation->set_rules('confirm_password', 'Confirm Password', 'required|matches[new_password]');

        if ($this->form_validation->run() === FALSE) {
            return $this->response([
                'status' => false,
                'message' => 'Validation failed',
                'errors' => $this->form_validation->error_array()
            ], REST_Controller::HTTP_BAD_REQUEST);
        }

        // ✅ Sanitize inputs
        $email = $this->security->xss_clean(trim($postData['email']));
        $reset_token = $this->security->xss_clean(trim($postData['reset_token']));
        $new_password = $this->security->xss_clean($postData['new_password']);
        $confirm_password = $this->security->xss_clean($postData['confirm_password']);

        // Validate reset token
        $token_record = $this->Forgot_model->validate_reset_token($reset_token);
        
        if (!$token_record || $token_record->email !== $email) {
            return $this->response([
                'status' => false,
                'message' => 'Invalid or expired reset token.'
            ], REST_Controller::HTTP_UNAUTHORIZED);
        }

        // Update password
        $password_updated = $this->Forgot_model->updateUserPassword($email, $new_password);

        if (!$password_updated) {
            return $this->response([
                'status' => false,
                'message' => 'Failed to reset password. Please try again.'
            ], REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
        }

        // Send success notification email
        $user = $this->Forgot_model->get_user_by_email_and_role($email, $role);
        if ($user) {
            send_password_reset_success_email($email, $user->name);
        }

        // Clean up used OTP
        $this->db->where('token', $reset_token)->delete('tb_password_reset_otp');

        return $this->response([
            'status' => true,
            'message' => 'Password reset successfully. You can now login with your new password.'
        ], REST_Controller::HTTP_OK);
    }

    /**
     * POST: /api/forgotpassword/resend-otp
     * Resend OTP
     */
    public function resend_otp_post() {
        $postData = $this->post();

        // ✅ Simple validation
        $this->form_validation->set_data($postData);
        $this->form_validation->set_rules('email', 'Email', 'required|valid_email|max_length[100]');

        if ($this->form_validation->run() === FALSE) {
            return $this->response([
                'status' => false,
                'message' => 'Validation failed',
                'errors' => $this->form_validation->error_array()
            ], REST_Controller::HTTP_BAD_REQUEST);
        }

        $email = $this->security->xss_clean(trim($postData['email']));

        // Check if user exists
        $user = $this->Forgot_model->get_user_by_email($email);
        if (!$user) {
            // For security, don't reveal if email exists or not
            return $this->response([
                'status' => true,
                'message' => 'If the email exists, an OTP has been sent.'
            ], REST_Controller::HTTP_OK);
        }

        // Generate new OTP
        $otp_data = $this->Forgot_model->generate_otp($email);
        
        if (!$otp_data) {
            return $this->response([
                'status' => false,
                'message' => 'Failed to generate OTP. Please try again.'
            ], REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
        }

        // Send OTP via email
        $email_sent = send_otp_email($email, $otp_data['otp'], $otp_data['name']);

        if (!$email_sent) {
            log_message('error', "Failed to resend OTP email to: $email");
        }

        $is_development = ENVIRONMENT === 'development';
        
        return $this->response([
            'status' => true,
            'message' => 'OTP has been resent to your email.',
            'debug_otp' => $is_development ? $otp_data['otp'] : null,
            'expires_in' => '10 minutes'
        ], REST_Controller::HTTP_OK);
    }

    /**
     * POST: /api/forgotpassword/validate-token
     * Validate reset token (optional - for frontend token validation)
     */
    public function validate_token_post() {
        $postData = $this->post();

        // ✅ Simple validation
        $this->form_validation->set_data($postData);
        $this->form_validation->set_rules('reset_token', 'Reset Token', 'required');

        if ($this->form_validation->run() === FALSE) {
            return $this->response([
                'status' => false,
                'message' => 'Validation failed',
                'errors' => $this->form_validation->error_array()
            ], REST_Controller::HTTP_BAD_REQUEST);
        }

        $reset_token = $this->security->xss_clean(trim($postData['reset_token']));

        // Validate reset token
        $token_record = $this->Forgot_model->validate_reset_token($reset_token);
        
        if (!$token_record) {
            return $this->response([
                'status' => false,
                'message' => 'Invalid or expired reset token.'
            ], REST_Controller::HTTP_UNAUTHORIZED);
        }

        return $this->response([
            'status' => true,
            'message' => 'Reset token is valid.',
            'email' => $token_record->email
        ], REST_Controller::HTTP_OK);
    }
}