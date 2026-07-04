<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once FCPATH . 'vendor/autoload.php';

class ResumeServiceController extends CI_Controller {

    public function __construct() {
        parent::__construct();      			
        $this->load->model('admin/services/ResumeTemplates_model'); // Load the template model      
    }
 
    public function index() {
        $data['title']= 'Talents Jobs';
		$data['description']='Talents Jobs';
		// Load header and navigation views
		$this->load->view('particles/header',$data);
		$this->load->view('particles/nav');		
        $this->load->view('website/services/build_cv'); 
       $this->load->view('particles/footer');
    }
   
    public function choose_template() {		
        // Set page title and description
        $data['title'] = 'Talents Jobs';
        $data['description'] = 'Talents Jobs';
        
        // Load header and navigation views
        $this->load->view('particles/header', $data);
        $this->load->view('particles/nav');
        
        // Get the template categories and templates
        $data['template_category'] = $this->ResumeTemplates_model->get_categories();
        $data['templates'] = $this->ResumeTemplates_model->get_all(); // Pre-load templates
    
        // Load the resume gallery view with data
        $this->load->view('website/services/resume_gallery', $data);
        $this->load->view('particles/footer');
    }


    public function get_templates() {
        if ($this->input->is_ajax_request()) {
            $templates = $this->ResumeTemplates_model->get_all();
            $htmlOutput = '';
    
            foreach ($templates as $template) {
                // Determine the redirect URL based on login status
                if ($this->session->userdata('role')=='candidate') {
                    $redirectUrl = site_url('resume/') . htmlspecialchars($template['template_id']) . '/edit';
                } else {
                    $redirectUrl = base_url('app/create-resume?step=introduction');
                }
    
                $htmlOutput .= '
                    <div class="chose-template-card" data-category="' . htmlspecialchars($template['category']) . '">
                        <div class="card-image-container">
                            <img src="' . base_url(htmlspecialchars($template['preview_image'])) . '" 
                                 alt="' . htmlspecialchars($template['name']) . '">
                            <div class="card-overlay">
                                <button class="chose-template-use-btn" onclick="storeTemplateCookie(\'' . htmlspecialchars($template['template_id']) . '\', \'' . $redirectUrl . '\')">
                                    Use Template
                                </button>
                            </div>
                            <div class="card-category">
                                ' . ucfirst($template['category']) . '
                            </div>
                        </div>
                    </div>';
            }
    
            echo json_encode(['html' => $htmlOutput]);
        } else {
            show_404();
        }
    }
	
	public function create_resume(){
	     // Set page title and description
        $data['title'] = 'Talents Jobs';
        $data['description'] = 'Talents Jobs';
        
        // Load header and navigation views
        $this->load->view('particles/header', $data);
        $this->load->view('particles/nav');
        
	  if ($this->session->userdata('role')=='candidate') {
			redirect('app/build-cv');
			exit();
		}
		$this->load->view('website/services/create-resume');
		  $this->load->view('particles/footer');
	}
    
	public function load_templates() {
		if ($this->input->is_ajax_request()) {
			$templates = $this->ResumeTemplates_model->get_all();
			$htmlOutput = '<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">';

			foreach ($templates as $template) {
				$createResumeUrl = base_url('app/create-resume?step=introduction');
				$imageUrl = base_url(htmlspecialchars($template['preview_image']));
				$templateName = htmlspecialchars($template['name']);
				$templateId = htmlspecialchars($template['template_id']);

				$htmlOutput .= '
				  <div class="bg-white rounded-lg overflow-hidden shadow hover:shadow-lg transform hover:-translate-y-1 transition">
					<div class="relative group bg-gray-100">
					  <img src="' . $imageUrl . '" 
						   alt="' . $templateName . '" 
						   class="w-full h-auto max-h-[350px] object-contain" />
					  <div class="absolute inset-0 bg-blue-600 bg-opacity-0 group-hover:bg-opacity-70 flex items-center justify-center transition duration-300">
						<button class="use-template-btn px-4 py-2 bg-white text-blue-600 rounded shadow" 
						  onclick="storeTemplateCookie(\'' . $templateId . '\', \'' . $createResumeUrl . '\')">
						  Use this template
						</button>
					  </div>
					</div>
					<div class="p-4 text-center">
					  <p class="text-gray-700 font-semibold">' . $templateName . '</p>
					</div>
				  </div>
				';

			}

			$htmlOutput .= '</div>';
			echo json_encode(['html' => $htmlOutput]);
		} else {
			show_404();
		}
	}


	
	
}