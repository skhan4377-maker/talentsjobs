<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class CareerPlans extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('admin/services/CareerService_model');      
    }

    // खरीदे गए प्लान दिखाने की मुख्य विधि
    public function index() {
        $user_id = $this->session->userdata('user_id');
        
        // डेटा फ़ेच करें
        $data['plans'] = $this->CareerService_model->get_purchased_plans($user_id);
        
        // व्यू लोड करें
        $data['title'] = 'My Career Service Plans';		
        $data['content'] = $this->load->view('candidate/career_plans', $data, TRUE);
       		
		$this->load->view('templates/master', $data);       
    }

    public function invoice($purchase_id) {
		$user_id = $this->session->userdata('user_id');
		
		if(!is_numeric($purchase_id)) {
			show_404();
		}

		$invoice_data = $this->CareerService_model->get_invoice_details($purchase_id, $user_id);
		
		if(empty($invoice_data)) {
			$this->session->set_flashdata('error', 'Invoice not found');
			redirect('career-plans');
		}

		// Calculate validity days
		$end_date = new DateTime($invoice_data['end_date']);
		$today = new DateTime();
		$validity_days = $end_date->diff($today)->format("%a");

		$data['invoice'] = $invoice_data;
		$data['validity_days'] = $validity_days;
		$data['title'] = 'Invoice #'.$invoice_data['invoice_number'];		
		  		
		$data['content'] = $this->load->view('candidate/invoice', $data, TRUE);
		$this->load->view('templates/master', $data); 
	}

}
?>