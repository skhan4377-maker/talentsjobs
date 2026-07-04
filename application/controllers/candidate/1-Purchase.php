<?php 
class Purchase extends MY_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->model('admin/services/purchase_model');
    }
    
    public function purchased_plans() {
		$data['title']='Purchased Resume';
		$data['status']='';
        $user_id = $this->session->userdata('user_id');
        $data['purchases'] = $this->purchase_model->get_purchases($user_id);
        
        $data['content'] =  $this->load->view('candidate/purchased_plans', $data, TRUE);
        $this->load->view('templates/master', $data);
    }
}

?>