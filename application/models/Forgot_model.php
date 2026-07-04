<?php
class Forgot_model extends CI_Model {

    public function get_user_by_email_and_role($email, $role) {
		$table = ($role === 'candidate') ? 'tb_candidate' : 'tb_employer';
		$this->db->where('email', $email);
		return $this->db->get($table)->row();
	}

	public function save_token_by_role($user_id, $role, $token, $expires) {
		$table = ($role === 'candidate') ? 'tb_candidate' : 'tb_employer';
		$id_col = ($role === 'candidate') ? 'candidate_id' : 'employer_id';

		$this->db->where($id_col, $user_id)->update($table, [
			'reset_token'       => $token,
			'token_expiration'  => $expires
		]);
	}

	public function validate_token($token, $role) {
		$table = ($role === 'candidate') ? 'tb_candidate' : 'tb_employer';
		$this->db->where('reset_token', $token);
		$this->db->where('token_expiration >=', date('Y-m-d H:i:s'));
		$query = $this->db->get($table);
		
		//log_message('error', 'Token Query: ' . $this->db->last_query());
		
		return $query->row();
	}


	public function update_password($user_id, $role, $hashed_password) {
		$table = ($role === 'candidate') ? 'tb_candidate' : 'tb_employer';
		$id_col = ($role === 'candidate') ? 'candidate_id' : 'employer_id';

		$this->db->where($id_col, $user_id)->update($table, [
			'password' => $hashed_password
		]);
	}

	public function clear_token($user_id, $role) {
		$table = ($role === 'candidate') ? 'tb_candidate' : 'tb_employer';
		$id_col = ($role === 'candidate') ? 'candidate_id' : 'employer_id';

		$this->db->where($id_col, $user_id)->update($table, [
			'reset_token'      => NULL,
			'token_expiration' => NULL
		]);
	}
	
	/**
	* Api forgot password section start
	*/
	
	/**
     * Generate and store OTP for password reset
     */
    public function generate_otp($email) {
        // Check if user exists
        $user = $this->db->get_where('tb_candidate', ['email' => $email, 'is_deleted' => 0])->row();
        if (!$user) {
            return false;
        }

        // Generate 6-digit OTP
        $otp = sprintf("%06d", mt_rand(1, 999999));
        
        // Generate unique token
        $token = bin2hex(random_bytes(32));
        
        // Set expiration (10 minutes from now)
        $expires_at = date('Y-m-d H:i:s', strtotime('+10 minutes'));

        // Clean up old OTPs for this email
        $this->db->where('email', $email)->delete('tb_password_reset_otp');

        // Insert new OTP
        $otp_data = [
            'email' => $email,
            'otp' => $otp,
            'token' => $token,
            'expires_at' => $expires_at,
            'created_at' => date('Y-m-d H:i:s')
        ];

        $this->db->insert('tb_password_reset_otp', $otp_data);
        
        return [
            'otp' => $otp,
            'token' => $token,
            'expires_at' => $expires_at,
            'user_name' => $user->name
        ];
    }

    /**
     * Verify OTP
     */
    public function verify_otp($email, $otp) {
        $current_time = date('Y-m-d H:i:s');
        
        $this->db->where('email', $email)
                 ->where('otp', $otp)
                 ->where('expires_at >', $current_time)
                 ->where('is_used', 0);
        
        $otp_record = $this->db->get('tb_password_reset_otp')->row();
        
        if ($otp_record) {
            // Mark OTP as used
            $this->db->where('id', $otp_record->id)
                     ->update('tb_password_reset_otp', ['is_used' => 1]);
            
            return $otp_record->token;
        }
        
        return false;
    }

    /**
     * Validate reset token
     */
    public function validate_reset_token($token) {
        $current_time = date('Y-m-d H:i:s');
        
        $this->db->where('token', $token)
                 ->where('expires_at >', $current_time)
                 ->where('is_used', 1);
        
        return $this->db->get('tb_password_reset_otp')->row();
    }

    /**
     * Update user password
     */
    public function updateUserPassword($email, $new_password) {
        $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);
        
        $this->db->where('email', $email)
                 ->update('tb_candidate', [
                     'password' => $hashed_password,
                     'updated_at' => date('Y-m-d H:i:s')
                 ]);
        
        return $this->db->affected_rows() > 0;
    }

    /**
     * Clean expired OTPs
     */
    public function clean_expired_otps() {
        $current_time = date('Y-m-d H:i:s');
        $this->db->where('expires_at <', $current_time)
                 ->delete('tb_password_reset_otp');
    }


    /**
     * Check if OTP exists and is valid
     */
    public function check_otp_exists($email, $otp) {
        $current_time = date('Y-m-d H:i:s');
        
        $this->db->where('email', $email)
                 ->where('otp', $otp)
                 ->where('expires_at >', $current_time)
                 ->where('is_used', 0);
        
        return $this->db->get('tb_password_reset_otp')->row();
    }
	
	/**
	* Api forgot password section end
	*/
	
}

?>