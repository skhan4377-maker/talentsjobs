<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Package extends CI_Controller {

	public function __construct(){
		parent::__construct();
		$this->load->model('employer/Package_mdl');
	} 
	
	public function index(){
		
		if(!empty($employer_session=$this->session->userdata('employer_session'))){
			
			$action=array('status'=>'1');
			$this->db->where('status',$action['status']);
			$data['package_master'] = $this->Package_mdl->get_results('package_master');
			
			$this->load->view('employer/choose_package',$data);
		}else{
			redirect('');
		}
	}
	
}
