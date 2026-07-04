<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Verification_mdl extends CI_Model {
    
    public function storeVerificationToken($email, $verification_token, $userType) {
        // Check the user type and determine the corresponding table name, email column, and id column
        if ($userType == 'employer') {
            $table_name = 'tb_employer';
            $email_column = 'email';
            $id_column = 'employer_id'; // Use employer_id for employers
        } else {
            $table_name = 'tb_candidate';
            $email_column = 'email';
            $id_column = 'candidate_id'; // Use candidate_id for candidates
        }
    
        // Check if the user with the provided email exists
        $existing_user = $this->db->get_where($table_name, array($email_column => $email))->row_array();
    
        // If the user exists, update the verification token and timestamp
        if ($existing_user) {
            $this->db->where($id_column, $existing_user[$id_column]);
            $this->db->update($table_name, array(
                'verification_token' => $verification_token,
                'token_expiration' => date('Y-m-d H:i:s', strtotime('+60 minutes')) // Set token expiration to 1 hour from now
            ));
        }
        // If the user doesn't exist, do nothing (no insert operation)
    }

	
	
  
}