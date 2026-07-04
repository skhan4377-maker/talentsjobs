<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Logout extends CI_Controller {
    public function __construct(){
        parent::__construct();
    }

	public function index(){
		$user_id = $this->session->userdata('user_id'); // ✅ pehle lo

		if ($user_id) {
			$this->db->where('user_id', $user_id)
					 ->update('tb_refresh_tokens', ['revoked' => 1]);
		}

		$this->session->sess_destroy(); // ✅ baad me destroy

		redirect('auth/login');
	}


	
}
