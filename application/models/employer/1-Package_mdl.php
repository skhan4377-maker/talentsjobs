<?php class Package_mdl extends CI_Model{
	
	public function __construct(){
		parent::__construct();
	}

	
	function get_results($table) {
        return $this->db->from($table)->get()->result_array();
    }
	
	
}