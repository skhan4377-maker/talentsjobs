<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Verification_mdl extends CI_Model {

    public function find_by_verification_token($token) {
        return $this->db->get_where('employer', array('verification_token' => $token))->row_array();
    }

    public function update_verification_status($userId) {
        $this->db->where('id', $userId);
        $this->db->update('employer', array('email_verification' => 1, 'verification_token'=>'', 'updated_at'=>date('Y-m-d h:i:s')));
    }

}