<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends CI_Controller {

	public function __construct(){
		parent::__construct();
		$this->load->model('home_mdl');	
	} 
	
	public function index() {

		$data['title'] = 'Talents Jobs - Free Job Posting & Job Search Across India';

		$data['description'] = 'Post jobs for free and find top talent across India. Talents Jobs helps job seekers discover the latest jobs in IT, Marketing, Finance, and more, while enabling employers to hire faster and smarter.';

		// Load header and navigation views
		$this->load->view('particles/header', $data);
		$this->load->view('particles/nav');

		// Sanitize search input
		$search_term = $this->input->get('search', TRUE);

		// ===== Blog Section =====
		$this->load->model('blog/Blog_mdl');

		$blogLimit  = 10;
		$blogOffset = 0;

		$params = [
			'limit'  => $blogLimit,
			'offset' => $blogOffset,
		];

		$data['blogs'] = $this->Blog_mdl->get_blogs($params);

		// Load main content and footer views
		$this->load->view('website/index', $data);
		$this->load->view('particles/footer');
	}
    
	
    

	

		
		
    
	
}