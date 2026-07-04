<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class AdminUser_model extends CI_Model {

    public function get_user_by_id($candidate_id) {
        $this->db->select('candidate_id, name, email, mobile, status, created_at, logo');
        $this->db->from('candidate');
        $this->db->where('candidate_id', $candidate_id);
        return $this->db->get()->row_array();
    }
}
