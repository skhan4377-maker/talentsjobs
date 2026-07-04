<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

class RegistrationController extends CI_Controller {

	public function __construct(){
		parent::__construct();
		$this->load->config('recaptcha');
	} 
	
	public function index() {	
        $data['title'] = 'Candidate Registration - Join Talents Jobs';
        $data['description'] = 'Create your free Talents Jobs account to search and apply for jobs easily. Register now and take the next step in your career.';
        $data['recaptcha_site_key'] = $this->config->item('recaptcha_site_key');
        
        // ✅ Country list load karo
        $this->load->model('Common_model');
        $data['countries'] = $this->Common_model->get_countries_for_dropdown();
        
        $this->load->view('particles/header', $data);
        $this->load->view('particles/nav');
        $this->load->view('website/registration/candidate_registration');
        $this->load->view('particles/footer');
    }
	
	public function employerRegistration(){	
		$data['title'] = 'Employer Registration - Hire with Talents Jobs';
		$data['description'] = 'Register your company on Talents Jobs and start hiring qualified candidates. Post jobs, manage applications, and find top talent today.';
		// ✅ CONFIG se site key bhejo
		$data['recaptcha_site_key'] = $this->config->item('recaptcha_site_key');
		
		 // ✅ Country list load karo
        $this->load->model('Common_model');
        $data['countries'] = $this->Common_model->get_countries_for_dropdown();
        
		$this->load->view('particles/header', $data);
		$this->load->view('particles/nav');	
		$this->load->view('website/registration/employer_registration');
		$this->load->view('particles/footer');
	}
}
