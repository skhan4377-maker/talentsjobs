<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once FCPATH . 'vendor/autoload.php';
class ResumeBuilder extends CI_Controller {

    public function __construct() {
        parent::__construct();			
        $this->load->model('admin/services/ResumeTemplateModel'); // Load the template model
        $this->load->helper('url');        
    }
	    
    public function edit_resume(){        
        $data['title'] = 'Resume Builder - Create Your Professional Resume | Talents Jobs';
		$data['description'] = 'Easily create and edit a professional resume online with Talents Jobs. Choose templates, customize your details, and download your resume instantly to apply for jobs.';
		// Load header and navigation views
		$this->load->view('particles/header',$data);
		$this->load->view('particles/nav');
        
        $this->load->view('website/services/edit_resume'); 
       	$this->load->view('particles/footer');
    }

    public function extractCandidateDetails() {
		// Set the header to JSON
		header('Content-Type: application/json');

		// Get the user_id from the session
		$user_id = $this->session->userdata('user_id');

		if (!$user_id) {
			echo json_encode([
				'success' => false,
				'message' => 'User not logged in.'
			]);
			return;
		}

		// Load the model and fetch candidate details
		$this->load->model('candidate/Profile_mdl');
		$candidate_details = $this->Profile_mdl->get_candidate_details($user_id);

		// Check if candidate details exist and return accordingly
		if ($candidate_details) {
			echo json_encode([
				'success' => true,
				'data' => $candidate_details
			]);
		} else {
			echo json_encode([
				'success' => false,
				'message' => 'Candidate details not found.'
			]);
		}
	}

	public function preview() {
		$template_id = $this->input->post('template_id');
		$user_id = $this->session->userdata('user_id');

		// Fetch or update template
		$existingTemplate = $this->ResumeTemplateModel->get_user_selected_template($user_id, $template_id);
		if ($existingTemplate) {
			$this->ResumeTemplateModel->update_user_template($user_id, $template_id);
			$template = $existingTemplate;
		} else {
			$this->ResumeTemplateModel->insert_user_template($user_id, $template_id);
			$template = $this->ResumeTemplateModel->get_template_by_id($template_id);
		}

		// Load profile model
		$this->load->model('candidate/Profile_mdl');
		$data['personal'] = $this->Profile_mdl->get_candidate_details($user_id);

		// Get experience type
		$experience_type = $data['personal']['work_status'] ?? 'fresher'; // Default: fresher

		// Process skills
		$skillsStr = $data['personal']['skills'] ?? '';
		$skillsArr = array_filter(array_map('trim', explode(',', $skillsStr)));
		$data['skills'] = array_map(function($s) {
			return ['skill' => $s];
		}, $skillsArr);

		// Show/hide sections based on experience
		if ($experience_type === 'fresher') {
			$data['experiences'] = [];                          // Hide employment
			$data['projects'] = [];                             // Hide projects
			$data['certifications'] = [];                       // Hide certifications
			$data['internships'] = $this->Profile_mdl->get_internships($user_id);
			$data['courses'] = $this->Profile_mdl->get_courses($user_id);
			$data['extraCurricularActivities'] = $this->Profile_mdl->get_extraCurricularActivities($user_id);
		} else {
			$data['experiences'] = $this->Profile_mdl->get_work_experiences($user_id);
			$data['projects'] = $this->Profile_mdl->get_projects($user_id);
			$data['certifications'] = $this->Profile_mdl->get_certifications($user_id);
			$data['internships'] = [];                          // Hide internships
			$data['courses'] = [];                              // Hide courses
			$data['extraCurricularActivities'] = [];            // Hide extracurricular
		}

		// Sections shown for both
		$data['educations'] = $this->Profile_mdl->get_educations($user_id);
		$data['awards'] = $this->Profile_mdl->get_awards($user_id);
		$data['hobbies'] = $this->Profile_mdl->get_hobbies($user_id);
		$data['languages'] = $this->Profile_mdl->get_languages($user_id);

		// Template HTML/CSS
		$layout_html = $template['layout_html'] ?? '';
		$css = isset($template['css']) ? "<style id='dynamic-template-css'>{$template['css']}</style>" : '';

		// Parse and render
		$html = $this->parse_template($layout_html, $data);
		echo $css . $html;
	}

	private function parse_template($html, $data) {
		// Personal details replacement
		if (isset($data['personal']) && is_array($data['personal'])) {
			foreach ($data['personal'] as $key => $value) {
				if ($key === 'logo' && !empty($value)) {
					$value = base_url($value);
				}
				$html = str_replace("{{{$key}}}", html_escape($value), $html);
			}
		}

		// Section content processing
		$sections = [
			'skills',
			'experiences',
			'educations',
			'internships',
			'certifications',
			'courses',
			'awards',
			'hobbies',
			'projects',
			'extraCurricularActivities',
			'languages'
		];

		foreach ($sections as $section) {
			$pattern = '/{{#'.$section.'}}(.*?){{\/'.$section.'}}/s';
			if (preg_match($pattern, $html, $m)) {
				if (!empty($data[$section])) {
					$block = '';
					foreach ($data[$section] as $item) {
						$temp = $m[1];
						foreach ($item as $k => $v) {
							$temp = str_replace("{{{$k}}}", html_escape($v), $temp);
						}
						$block .= $temp;
					}
					$html = str_replace($m[0], $block, $html);
				} else {
					$html = str_replace($m[0], '', $html);
				}
			}
		}

		// Dynamic Section Titles with Data Validation
		$sectionTitles = [
			'employment',
			'skills',
			'internship',
			'education',
			'project',
			'certification',
			'language',
			'course',
			'award',
			'extracurricular',
			'hobby'
		];

		$sectionDataMap = [
			'employment' => 'experiences',
			'skills' => 'skills',
			'internship' => 'internships',
			'education' => 'educations',
			'project' => 'projects',
			'certification' => 'certifications',
			'language' => 'languages',
			'course' => 'courses',
			'award' => 'awards',
			'extracurricular' => 'extraCurricularActivities',
			'hobby' => 'hobbies'
		];

		foreach ($sectionTitles as $section) {
			$dataKey = $sectionDataMap[$section];
			$titleText = '';
			
			if (!empty($data[$dataKey])) {
				$title = $this->ResumeTemplateModel->get_section_title($section);
				
				if (is_string($title)) {
					$titleText = $title;
				} elseif (is_object($title) && property_exists($title, 'section_title')) {
					$titleText = $title->section_title;
				} elseif (is_array($title) && isset($title['section_title'])) {
					$titleText = $title['section_title'];
				} else {
					$titleText = ucfirst($section); // Fallback
				}
			}
			
			$html = str_replace(
				"{{{$section}Title}}", 
				html_escape($titleText), 
				$html
			);
		}
		return $html;
	}
	
	public function update_resume_template() {
		try {
			if (!$this->session->userdata('logged_in')) {
				throw new Exception('User is not logged in');
			}

			$user_id = $this->session->userdata('user_id');
			$template_id = $this->input->post('template_id');
			$form_source = $this->input->post('form_source');

			if (empty($template_id)) {
				throw new Exception('Template ID is required');
			}

			// Verify template ownership
			$template_exists = $this->db->get_where('tb_user_resume_templates', [
				'user_id' => $user_id,
				'template_id' => $template_id
			])->num_rows();

			if (!$template_exists) {
				throw new Exception('Template not found');
			}

			$update_data = [];
			$upload_result = [];

			// Handle file upload
			if (!empty($_FILES['logo']['name'])) {
				$upload_result = $this->upload_photo();
				if ($upload_result['status'] !== 'success') {
					throw new Exception($upload_result['message']);
				}
				$update_data['logo'] = $upload_result['photoUrl'];
			}			
			switch($form_source) {
				case 'main_form':
					$this->form_validation->set_rules('name', 'First Name', 'required|max_length[50]');
					$this->form_validation->set_rules('last_name', 'Last Name', 'max_length[50]');
					$this->form_validation->set_rules('designations', 'Job Title', 'required|max_length[100]');
					$this->form_validation->set_rules('mobile', 'Phone Number', 'required|regex_match[/^[0-9]{10}$/]');
					$this->form_validation->set_rules('city_id', 'City', 'numeric');
					$this->form_validation->set_rules('country', 'Country', 'max_length[50]');
					$this->form_validation->set_rules('address', 'Address', 'max_length[255]');
					$this->form_validation->set_rules('postal', 'Postal Code', 'max_length[20]');
					$this->form_validation->set_rules('portfolioUrl', 'Portfolio URL', 'valid_url');
					//$this->form_validation->set_rules('linkedinProfile', 'LinkedIn Profile', 'valid_url');
					$this->form_validation->set_rules('dob', 'Date of Birth');
					//$this->form_validation->set_rules('nationality', 'Nationality', 'max_length[50]');

					if (!$this->form_validation->run()) {
						throw new Exception(validation_errors());
					}

					$update_data = array_merge($update_data, [
						'name'        => $this->input->post('name', TRUE),
						'last_name'   => $this->input->post('last_name', TRUE),
						'designations'=> $this->input->post('designations', TRUE),
						'mobile'      => $this->input->post('mobile', TRUE),
						'city_id'         => $this->input->post('city_id', TRUE),
						'placeOfBirth'    => $this->input->post('placeOfBirth', TRUE),
						'country_id'      => $this->input->post('country_id', TRUE),
						'address'         => $this->input->post('address', TRUE),
						'postal'          => $this->input->post('postal', TRUE),
						'dob'             => $this->input->post('dob', TRUE),
						//'nationality'     => $this->input->post('nationality', TRUE),
						'portfolioUrl'    => $this->input->post('portfolioUrl', TRUE),
						//'linkedinProfile' => $this->input->post('linkedinProfile', TRUE),
						//'resume_headline' => $this->input->post('resume_headline', TRUE),
						'objective'       => $this->input->post('objective', TRUE)
						
					]);
					break;

				case 'modal_form':
					$this->form_validation->set_rules('city_id', 'City', 'numeric');
					$this->form_validation->set_rules('country', 'Country', 'max_length[50]');
					$this->form_validation->set_rules('address', 'Address', 'max_length[255]');
					$this->form_validation->set_rules('postal', 'Postal Code', 'max_length[20]');
					$this->form_validation->set_rules('portfolioUrl', 'Portfolio URL', 'valid_url');
					//$this->form_validation->set_rules('linkedinProfile', 'LinkedIn Profile', 'valid_url');
					$this->form_validation->set_rules('dob', 'Date of Birth');
					//$this->form_validation->set_rules('nationality', 'Nationality', 'max_length[50]');
					

					if (!$this->form_validation->run()) {
						throw new Exception(validation_errors());
					}

					$update_data = array_merge($update_data, [
						'city_id'         => $this->input->post('city_id', TRUE),
						'placeOfBirth'    => $this->input->post('placeOfBirth', TRUE),
						'country_id'         => $this->input->post('country_id', TRUE),
						'address'         => $this->input->post('address', TRUE),
						'postal'          => $this->input->post('postal', TRUE),
						'dob'             => $this->input->post('dob', TRUE),
						//'nationality'     => $this->input->post('nationality', TRUE),
						'portfolioUrl'    => $this->input->post('portfolioUrl', TRUE),
						//'linkedinProfile' => $this->input->post('linkedinProfile', TRUE),
						//'resume_headline' => $this->input->post('resume_headline', TRUE),
						'objective'       => $this->input->post('objective', TRUE)
					]);
					break;

				default:
					throw new Exception('Invalid form source');
			}

			 // Update resume template data using Profile model
			$this->load->model('candidate/Profile_mdl');			
			$success = $this->Profile_mdl->update_candidate_details(
				$user_id,			
				$update_data
			);

			if (!$success) {
				throw new Exception('Failed to update resume template');
			}

			
			echo json_encode([
				'success' => true,
				'message' => 'Resume updated successfully!' // सफलता संदेश जोड़ें
			]);

		}catch (Exception $e) {
			echo json_encode([
				'success' => false,
				'message' => $e->getMessage()
			]);
		}
	}
	
	public function update_work_status() {
		$this->load->model('candidate/Profile_mdl');

		$experience_type = $this->input->post('experience_type');
		$user_id = $this->session->userdata('user_id');

		if (!$user_id || !$experience_type) {
			echo json_encode(['status' => 'error', 'message' => 'Invalid input']);
			return;
		}

		$update_data = ['work_status' => $experience_type];

		$success = $this->Profile_mdl->update_candidate_details($user_id, $update_data);

		if ($success) {
			echo json_encode(['status' => 'success']);
		} else {
			echo json_encode(['status' => 'error', 'message' => 'Update failed']);
		}
	}

	// Handle fetching skills from candidate table
	public function fetch_skills() {
		try {
			$user_id = $this->session->userdata('user_id');
			$this->db->select('skills');
			$candidate = $this->db->get_where('candidate', ['candidate_id' => $user_id])->row();
			
			$skills = explode(',', $candidate->skills ?? '');
			$skills = array_map('trim', $skills);
			$skills = array_filter($skills);

			echo json_encode([
				'success' => true,
				'skills' => array_map(function($skill) {
					return ['skill_name' => $skill];
				}, $skills)
			]);
		} catch (Exception $e) {
			echo json_encode(['success' => false, 'message' => $e->getMessage()]);
		}
	}

	// Save new skill to candidate's skill column
	public function save_skill() {
		try {
			$user_id = $this->session->userdata('user_id');
			$skill_name = $this->input->post('skill_name');

			// सही कॉलम नाम 'skills' का उपयोग करें
			$this->db->select('skills');
			$candidate = $this->db->get_where('candidate', ['candidate_id' => $user_id])->row();
			
			// 'skill' को 'skills' में बदलें
			$current_skills = explode(',', $candidate->skills ?? ''); // Fix here
			$current_skills = array_map('trim', $current_skills);

			if (in_array($skill_name, $current_skills)) {
				throw new Exception('Skill already exists');
			}

			$current_skills[] = $skill_name;
			$this->db->where('candidate_id', $user_id);
			$this->db->update('candidate', [
				'skills' => implode(', ', array_filter($current_skills))
			]);

			echo json_encode(['success' => true]);
		} catch (Exception $e) {
			echo json_encode(['success' => false, 'message' => $e->getMessage()]);
		}
	}

	// Update existing skill in candidate's skill column
	public function update_skill() {
		try {
			$user_id = $this->session->userdata('user_id');
			$old_name = $this->input->post('old_name');
			$new_name = $this->input->post('new_name');

			$this->db->select('skills');
			$candidate = $this->db->get_where('candidate', ['candidate_id' => $user_id])->row();
			$skills = explode(',', $candidate->skills ?? '');
			$skills = array_map('trim', $skills);

			$index = array_search($old_name, $skills);
			if ($index === false) throw new Exception('Skill not found');
			
			$skills[$index] = $new_name;
			$this->db->where('candidate_id', $user_id);
			$this->db->update('candidate', [
				'skills' => implode(', ', array_filter($skills))
			]);

			echo json_encode(['success' => true]);
		} catch (Exception $e) {
			echo json_encode(['success' => false, 'message' => $e->getMessage()]);
		}
	}
	
	// Delete skill from candidate's skill column
	public function delete_skill() {
		try {
			$user_id = $this->session->userdata('user_id');
			$skill_name = $this->input->post('skill_name');

			$this->db->select('skills');
			$candidate = $this->db->get_where('candidate', ['candidate_id' => $user_id])->row();
			$skills = explode(',', $candidate->skills ?? '');
			$skills = array_map('trim', $skills);

			// To (PHP 7.3 compatible syntax)
			$skills = array_filter($skills, function($s) use ($skill_name) {
				return $s !== $skill_name;
			});
			$this->db->where('candidate_id', $user_id);
			$this->db->update('candidate', [
				'skills' => implode(', ', $skills)
			]);

			echo json_encode(['success' => true]);
		} catch (Exception $e) {
			echo json_encode(['success' => false, 'message' => $e->getMessage()]);
		}
	}

    public function ajax_upload_photo() {
		// Configure upload parameters
		$config['upload_path']   = './uploads/resume_photos/';
		$config['allowed_types'] = 'jpg|jpeg|png';
		$config['max_size']      = 2048; // Maximum file size in kilobytes (2MB)
		$config['encrypt_name']  = TRUE;   // Prevent file name conflicts

		$this->load->library('upload', $config);

		// Attempt file upload with field name "photo"
		if (!$this->upload->do_upload('photo')) {
			echo json_encode([
				'status'  => 'error',
				'message' => $this->upload->display_errors('', '')
			]);
			return;
		}

		// Get the new file data and build the new photo URL (full URL now)
		$uploadData = $this->upload->data();
		$newPhotoUrl = base_url('uploads/resume_photos/' . $uploadData['file_name']);
		
		// Get user details and load the model
		$user_id = $this->session->userdata('user_id');
		$this->load->model('candidate/Profile_mdl');

		// Unlink the old photo if it exists
		$existingCandidate = $this->Profile_mdl->get_candidate_details($user_id);
		if (!empty($existingCandidate['logo'])) {
			// Combine FCPATH with the relative file path to form an absolute path
			$file_path = FCPATH . $existingCandidate['logo'];
			if (file_exists($file_path)) { // Check if file exists before attempting to delete
				unlink($file_path);
			}
		}

		// Prepare update data with the new photo URL
		$update_data = ['logo' => 'uploads/resume_photos/' . $uploadData['file_name']]; // save relative path in db if needed
		$success = $this->Profile_mdl->update_candidate_details($user_id, $update_data);

		if (!$success) {
			echo json_encode([
				'status'  => 'error',
				'message' => 'Failed to update profile photo.'
			]);
			return;
		}

		echo json_encode([
			'status'   => 'success',
			'photoUrl' => $newPhotoUrl
		]);
	}
    
	public function fetch_work_experiences() {
		$user_id = $this->session->userdata('user_id');      
		// Fetch template data based on the user and template
		$this->load->model('candidate/Profile_mdl');
		$workExperiences = $this->Profile_mdl->get_work_experiences($user_id);

		if ($workExperiences) {      
			echo json_encode([
				'success'     => true,
				'employments' => $workExperiences, // Changed key here
			]);
		} else {
			echo json_encode([
				'success' => false,
				'message' => ''
			]);
		}
	}
	
	public function save_or_update_work_experience() {
		$this->load->library('form_validation');

		$this->form_validation->set_rules('template_id', 'Template ID', 'required|integer');
		$this->form_validation->set_rules('job_title', 'Job Title', 'required|trim');
		$this->form_validation->set_rules('employer_name', 'Employer Name', 'required|trim');
		$this->form_validation->set_rules('start_date', 'Start Date', 'required|trim');
		$this->form_validation->set_rules('end_date', 'End Date', 'trim');
		$this->form_validation->set_rules('job_type', 'Job Type', 'trim');
		$this->form_validation->set_rules('work_location', 'Work Location', 'trim');
		$this->form_validation->set_rules('responsibilities', 'Responsibilities', 'trim');
		$this->form_validation->set_rules('is_current', 'Is Current', 'trim|in_list[0,1]');
		
		if ($this->form_validation->run() == FALSE) {
			echo json_encode([
				'success' => false,
				'message' => validation_errors()
			]);
			return;
		}
		
		$template_id      = $this->input->post('template_id', true);
		$job_title        = $this->input->post('job_title', true);
		$employer_name    = $this->input->post('employer_name', true);
		$start_date       = $this->input->post('start_date', true);
		$end_date         = $this->input->post('end_date', true);
		$job_type         = $this->input->post('job_type', true);
		$work_location    = $this->input->post('work_location', true);
		$responsibilities = $this->input->post('responsibilities', true);
		$is_current       = $this->input->post('is_current', true);
		$employment_history_id = $this->input->post('employment_id', true);

		$user_id = $this->session->userdata('user_id');

		$template_exists = $this->db->get_where('tb_user_resume_templates', [
			'user_id'     => $user_id,
			'template_id' => $template_id
		])->num_rows();

		if (!$template_exists) {
			echo json_encode([
				'success' => false,
				'message' => 'Template not found or access denied.'
			]);
			return;
		}

		$data = [
			'job_title'       => $job_title,
			'employer_name'   => $employer_name,
			'start_date'      => $start_date,
			'end_date'        => $end_date,
			'is_current'      => $is_current,
			'job_type'        => $job_type,
			'work_location'   => $work_location,
			'responsibilities'=> $responsibilities,
			'updated_at'      => date('Y-m-d H:i:s')
		];

		$this->load->model('candidate/Profile_mdl');
		
		if ($employment_history_id) {
			$updated = $this->Profile_mdl->update_work($employment_history_id, $data);
			if ($updated) {
				echo json_encode([
					'success' => true,
					'message' => 'Employment data updated successfully.',
					'data'    => ['id' => $employment_history_id]
				]);
			} else {
				echo json_encode([
					'success' => false,
					'message' => 'Failed to update employment data.'
				]);
			}
		} else {
			$data['candidate_id'] = $user_id;
			$data['created_at']   = date('Y-m-d H:i:s');
			$insert_id = $this->Profile_mdl->employment_history_store($data);
			if ($insert_id) {
				echo json_encode([
					'success' => true,
					'message' => 'Employment data added successfully.',
					'data'    => ['id' => $insert_id]
				]);
			} else {
				echo json_encode([
					'success' => false,
					'message' => 'Failed to save employment data.'
				]);
			}
		}
	}

	public function delete_work_experience() {
		$id = $this->input->post('employment_id');
		if (!$id) {
			echo json_encode([
				'success' => false,
				'message' => 'Invalid or missing ID.'
			]);
			return;
		}
		
		$this->load->model('candidate/Profile_mdl');
		$deleted = $this->Profile_mdl->delete_work_experience($id);
		if ($deleted) {
			echo json_encode([
				'success' => true,
				'message' => 'Work experience deleted successfully.'
			]);
		} else {
			echo json_encode([
				'success' => false,
				'message' => 'Failed to delete work experience.'
			]);
		}
	}
	
	public function fetch_educations() {
		$user_id = $this->session->userdata('user_id');      
		$this->load->model('candidate/Profile_mdl');
		$educations = $this->Profile_mdl->get_educations($user_id);

		if ($educations) {      
			echo json_encode([
				'success'    => true,
				'educations' => $educations,
			]);
		} else {
			echo json_encode([
				'success' => false,
				'message' => ''
			]);
		}
	}

	public function save_or_update_education() {
		$this->load->library('form_validation');

		$this->form_validation->set_rules('template_id', 'Template ID', 'required|integer');
		$this->form_validation->set_rules('degreeName', 'Degree Name', 'required|trim');
		$this->form_validation->set_rules('institutionName', 'Institution Name', 'required|trim');
		$this->form_validation->set_rules('startYear', 'Start Year', 'required');
		$this->form_validation->set_rules('endYear', 'End Year', 'required');
		//$this->form_validation->set_rules('fieldOfStudy', 'Field of Study', 'trim');
		//$this->form_validation->set_rules('honors', 'Honors', 'trim');

		if ($this->form_validation->run() == FALSE) {
			echo json_encode([
				'success' => false,
				'message' => validation_errors()
			]);
			return;
		}

		$template_id      = $this->input->post('template_id', true);
		$degreeName       = $this->input->post('degreeName', true);
		$institutionName  = $this->input->post('institutionName', true);
		$startYear        = $this->input->post('startYear', true);
		$endYear          = $this->input->post('endYear', true);
		//$fieldOfStudy     = $this->input->post('fieldOfStudy', true);
		//$honors           = $this->input->post('honors', true);
		$education_id     = $this->input->post('education_id', true);

		$user_id = $this->session->userdata('user_id');

		$template_exists = $this->db->get_where('tb_user_resume_templates', [
			'user_id'     => $user_id,
			'template_id' => $template_id
		])->num_rows();

		if (!$template_exists) {
			echo json_encode([
				'success' => false,
				'message' => 'Template not found or access denied.'
			]);
			return;
		}

		$data = [
			'degreeName'       => $degreeName,
			'institutionName'  => $institutionName,
			'startYear'        => $startYear,
			'endYear'          => $endYear,		
			'updated_at'       => date('Y-m-d H:i:s')
		];

		$this->load->model('candidate/Profile_mdl');

		if ($education_id) {
			$updated = $this->Profile_mdl->update_education($education_id, $data);
			if ($updated) {
				echo json_encode([
					'success' => true,
					'message' => 'Education data updated successfully.',
					'data'    => ['id' => $education_id]
				]);
			} else {
				echo json_encode([
					'success' => false,
					'message' => 'Failed to update education data.'
				]);
			}
		} else {
			$data['candidate_id'] = $user_id;
			$data['created_at']   = date('Y-m-d H:i:s');
			$insert_id = $this->Profile_mdl->store_education($data);
			if ($insert_id) {
				echo json_encode([
					'success' => true,
					'message' => 'Education data added successfully.',
					'data'    => ['id' => $insert_id]
				]);
			} else {
				echo json_encode([
					'success' => false,
					'message' => 'Failed to save education data.'
				]);
			}
		}
	}

	public function delete_education() {
		$id = $this->input->post('education_id');
		if (!$id) {
			echo json_encode([
				'success' => false,
				'message' => 'Invalid or missing ID.'
			]);
			return;
		}

		$this->load->model('candidate/Profile_mdl');
		$deleted = $this->Profile_mdl->delete_education($id);
		if ($deleted) {
			echo json_encode([
				'success' => true,
				'message' => 'Education record deleted successfully.'
			]);
		} else {
			echo json_encode([
				'success' => false,
				'message' => 'Failed to delete education record.'
			]);
		}
	}
	
	public function fetch_languages() {
		$user_id = $this->session->userdata('user_id');
		$this->load->model('candidate/Profile_mdl');
		$languages = $this->Profile_mdl->get_languages($user_id);

		if ($languages) {
			echo json_encode([
				'success'   => true,
				'languages' => $languages,
			]);
		} else {
			echo json_encode([
				'success' => false,
				'message' => 'No language records found.'
			]);
		}
	}

	public function save_or_update_language() {
		$this->load->library('form_validation');

		// Define rules for the language form
		$this->form_validation->set_rules('template_id', 'Template ID', 'required|integer');
		$this->form_validation->set_rules('languageName', 'Language Name', 'required|trim');
		$this->form_validation->set_rules('proficiencyLevel', 'Proficiency Level', 'required|trim');

		if ($this->form_validation->run() == FALSE) {
			echo json_encode([
				'success' => false,
				'message' => validation_errors()
			]);
			return;
		}

		// Get POST data safely
		$template_id       = $this->input->post('template_id', true);
		$languageName      = $this->input->post('languageName', true);
		$proficiencyLevel  = $this->input->post('proficiencyLevel', true);
		$language_id       = $this->input->post('language_id', true);

		$user_id = $this->session->userdata('user_id');

		// Check if the user has access to the provided template
		$template_exists = $this->db->get_where('tb_user_resume_templates', [
			'user_id'     => $user_id,
			'template_id' => $template_id
		])->num_rows();

		if (!$template_exists) {
			echo json_encode([
				'success' => false,
				'message' => 'Template not found or access denied.'
			]);
			return;
		}

		// Prepare data array for insert/update
		$data = [
			'languageName'     => $languageName,
			'proficiencyLevel' => $proficiencyLevel,
			//'updated_at'       => date('Y-m-d H:i:s')
		];

		$this->load->model('candidate/Profile_mdl');

		if ($language_id) {
			// Update existing language record
			$updated = $this->Profile_mdl->update_language($language_id, $data);
			if ($updated) {
				echo json_encode([
					'success' => true,
					'message' => 'Language data updated successfully.',
					'data'    => ['id' => $language_id]
				]);
			} else {
				echo json_encode([
					'success' => false,
					'message' => 'Failed to update language data.'
				]);
			}
		} else {
			// Insert new language record
			$data['candidate_id'] = $user_id;
			//$data['created_at']   = date('Y-m-d H:i:s');
			$insert_id = $this->Profile_mdl->store_language($data);
			if ($insert_id) {
				echo json_encode([
					'success' => true,
					'message' => 'Language data added successfully.',
					'data'    => ['id' => $insert_id]
				]);
			} else {
				echo json_encode([
					'success' => false,
					'message' => 'Failed to save language data.'
				]);
			}
		}
	}

	public function delete_language() {
		$language_id = $this->input->post('language_id');
		if (!$language_id) {
			echo json_encode([
				'success' => false,
				'message' => 'Invalid or missing ID.'
			]);
			return;
		}

		$this->load->model('candidate/Profile_mdl');
		$deleted = $this->Profile_mdl->delete_language($language_id);
		if ($deleted) {
			echo json_encode([
				'success' => true,
				'message' => 'Language record deleted successfully.'
			]);
		} else {
			echo json_encode([
				'success' => false,
				'message' => 'Failed to delete language record.'
			]);
		}
	}

    public function fetch_internships() {
		$user_id = $this->session->userdata('user_id');
		$this->load->model('candidate/Profile_mdl');
		$internships = $this->Profile_mdl->get_internships($user_id);

		if ($internships) {
			echo json_encode([
				'success'      => true,
				'internships'  => $internships,
			]);
		} else {
			echo json_encode([
				'success' => false,
				'message' => 'No internship records found.'
			]);
		}
	}

	public function save_or_update_internship() {
		$this->load->library('form_validation');

		// Define validation rules for the internship form
		$this->form_validation->set_rules('template_id', 'Template ID', 'required|integer');
		$this->form_validation->set_rules('jobTitle', 'Job Title', 'required|trim');
		$this->form_validation->set_rules('employerName', 'Employer Name', 'required|trim');
		$this->form_validation->set_rules('startDate', 'Start Date', 'required');
		$this->form_validation->set_rules('endDate', 'End Date', 'required');
		$this->form_validation->set_rules('location', 'Location', 'required|trim');
		$this->form_validation->set_rules('jobDescription', 'Job Description', 'trim');

		if ($this->form_validation->run() == FALSE) {
			echo json_encode([
				'success' => false,
				'message' => validation_errors()
			]);
			return;
		}

		// Retrieve POST data safely
		$template_id   = $this->input->post('template_id', true);
		$jobTitle      = $this->input->post('jobTitle', true);
		$employerName  = $this->input->post('employerName', true);
		$startDate     = $this->input->post('startDate', true);
		$endDate       = $this->input->post('endDate', true);
		$location      = $this->input->post('location', true);
		$jobDescription= $this->input->post('jobDescription', true);
		$internship_id = $this->input->post('internship_id', true);

		$user_id = $this->session->userdata('user_id');

		// Check if the user has access to the provided template
		$template_exists = $this->db->get_where('tb_user_resume_templates', [
			'user_id'     => $user_id,
			'template_id' => $template_id
		])->num_rows();

		if (!$template_exists) {
			echo json_encode([
				'success' => false,
				'message' => 'Template not found or access denied.'
			]);
			return;
		}

		// Prepare data for insert/update
		$data = [
			'jobTitle'      => $jobTitle,
			'employerName'  => $employerName,
			'startDate'     => $startDate,
			'endDate'       => $endDate,
			'location'      => $location,
			'jobDescription'=> $jobDescription,
			'updated_at'    => date('Y-m-d H:i:s')
		];

		$this->load->model('candidate/Profile_mdl');

		if ($internship_id) {
			// Update existing internship record
			$updated = $this->Profile_mdl->update_internship($internship_id, $data);
			if ($updated) {
				echo json_encode([
					'success' => true,
					'message' => 'Internship data updated successfully.',
					'data'    => ['id' => $internship_id]
				]);
			} else {
				echo json_encode([
					'success' => false,
					'message' => 'Failed to update internship data.'
				]);
			}
		} else {
			// Insert new internship record
			$data['candidate_id'] = $user_id;
			$data['created_at']   = date('Y-m-d H:i:s');
			$insert_id = $this->Profile_mdl->store_internship($data);
			if ($insert_id) {
				echo json_encode([
					'success' => true,
					'message' => 'Internship data added successfully.',
					'data'    => ['id' => $insert_id]
				]);
			} else {
				echo json_encode([
					'success' => false,
					'message' => 'Failed to save internship data.'
				]);
			}
		}
	}

	public function delete_internship() {
		$internship_id = $this->input->post('internship_id');
		if (!$internship_id) {
			echo json_encode([
				'success' => false,
				'message' => 'Invalid or missing ID.'
			]);
			return;
		}

		$this->load->model('candidate/Profile_mdl');
		$deleted = $this->Profile_mdl->delete_internship($internship_id);
		if ($deleted) {
			echo json_encode([
				'success' => true,
				'message' => 'Internship record deleted successfully.'
			]);
		} else {
			echo json_encode([
				'success' => false,
				'message' => 'Failed to delete internship record.'
			]);
		}
	}

	public function fetch_certifications() {
		$user_id = $this->session->userdata('user_id');
		$this->load->model('candidate/Profile_mdl');
		$certifications = $this->Profile_mdl->get_certifications($user_id);

		if ($certifications) {
			echo json_encode([
				'success'         => true,
				'certifications'  => $certifications,
			]);
		} else {
			echo json_encode([
				'success' => false,
				'message' => 'No certification records found.'
			]);
		}
	}

	public function save_or_update_certification() {
		$this->load->library('form_validation');

		// Set validation rules
		$this->form_validation->set_rules('template_id', 'Template ID', 'required|integer');
		$this->form_validation->set_rules('certificationName', 'Certification Name', 'required|trim');
		$this->form_validation->set_rules('issuingAuthority', 'Issuing Authority', 'required|trim');
		$this->form_validation->set_rules('dateIssued', 'Date Issued', 'required');
		$this->form_validation->set_rules('expiryDate', 'Expiry Date', 'required');

		if ($this->form_validation->run() == FALSE) {
			echo json_encode([
				'success' => false,
				'message' => validation_errors()
			]);
			return;
		}

		// Retrieve POST data safely
		$template_id        = $this->input->post('template_id', true);
		$certificationName  = $this->input->post('certificationName', true);
		$issuingAuthority   = $this->input->post('issuingAuthority', true);
		$dateIssued         = $this->input->post('dateIssued', true);
		$expiryDate         = $this->input->post('expiryDate', true);
		$certification_id   = $this->input->post('certification_id', true);

		$user_id = $this->session->userdata('user_id');

		// Check template access
		$template_exists = $this->db->get_where('tb_user_resume_templates', [
			'user_id'     => $user_id,
			'template_id' => $template_id
		])->num_rows();

		if (!$template_exists) {
			echo json_encode([
				'success' => false,
				'message' => 'Template not found or access denied.'
			]);
			return;
		}

		// Prepare data array for insert/update
		$data = [
			'certificationName' => $certificationName,
			'issuingAuthority'  => $issuingAuthority,
			'dateIssued'        => $dateIssued,
			'expiryDate'        => $expiryDate,
			'updated_at'        => date('Y-m-d H:i:s')
		];

		$this->load->model('candidate/Profile_mdl');

		if ($certification_id) {
			// Update certification record
			$updated = $this->Profile_mdl->update_certification($certification_id, $data);
			if ($updated) {
				echo json_encode([
					'success' => true,
					'message' => 'Certification data updated successfully.',
					'data'    => ['id' => $certification_id]
				]);
			} else {
				echo json_encode([
					'success' => false,
					'message' => 'Failed to update certification data.'
				]);
			}
		} else {
			// Insert new certification record
			$data['candidate_id'] = $user_id;
			$data['created_at']   = date('Y-m-d H:i:s');
			$insert_id = $this->Profile_mdl->store_certification($data);
			if ($insert_id) {
				echo json_encode([
					'success' => true,
					'message' => 'Certification data added successfully.',
					'data'    => ['id' => $insert_id]
				]);
			} else {
				echo json_encode([
					'success' => false,
					'message' => 'Failed to save certification data.'
				]);
			}
		}
	}

	public function delete_certification() {
		$certification_id = $this->input->post('certification_id');
		if (!$certification_id) {
			echo json_encode([
				'success' => false,
				'message' => 'Invalid or missing certification ID.'
			]);
			return;
		}

		$this->load->model('candidate/Profile_mdl');
		$deleted = $this->Profile_mdl->delete_certification($certification_id);
		if ($deleted) {
			echo json_encode([
				'success' => true,
				'message' => 'Certification record deleted successfully.'
			]);
		} else {
			echo json_encode([
				'success' => false,
				'message' => 'Failed to delete certification record.'
			]);
		}
	}
	
    public function fetch_projects() {
		$user_id = $this->session->userdata('user_id');
		$this->load->model('candidate/Profile_mdl');
		$projects = $this->Profile_mdl->get_projects($user_id);

		if ($projects) {
			echo json_encode([
				'success'  => true,
				'projects' => $projects,
			]);
		} else {
			echo json_encode([
				'success' => false,
				'message' => 'No project records found.'
			]);
		}
	}

	public function save_or_update_project() {
		$this->load->library('form_validation');

		// Set validation rules
		$this->form_validation->set_rules('template_id', 'Template ID', 'required|integer');
		$this->form_validation->set_rules('projectName', 'Project Name', 'required|trim');
		$this->form_validation->set_rules('role', 'Role', 'required|trim');
		$this->form_validation->set_rules('description', 'Description', 'trim');
		$this->form_validation->set_rules('technologiesUsed', 'Technologies Used', 'trim');
		$this->form_validation->set_rules('outcome', 'Outcome', 'trim');
		$this->form_validation->set_rules('projectUrl', 'Project URL', 'trim');

		if ($this->form_validation->run() == FALSE) {
			echo json_encode([
				'success' => false,
				'message' => validation_errors()
			]);
			return;
		}

		// Retrieve POST data safely
		$template_id      = $this->input->post('template_id', true);
		$projectName      = $this->input->post('projectName', true);
		$description      = $this->input->post('description', true);
		$role             = $this->input->post('role', true);
		$technologiesUsed = $this->input->post('technologiesUsed', true);
		$outcome          = $this->input->post('outcome', true);
		$projectUrl       = $this->input->post('projectUrl', true);
		$project_id       = $this->input->post('project_id', true);

		$user_id = $this->session->userdata('user_id');

		// Check if the user has access to the provided template
		$template_exists = $this->db->get_where('tb_user_resume_templates', [
			'user_id'     => $user_id,
			'template_id' => $template_id
		])->num_rows();

		if (!$template_exists) {
			echo json_encode([
				'success' => false,
				'message' => 'Template not found or access denied.'
			]);
			return;
		}

		// Prepare data for insert/update
		$data = [
			'projectName'      => $projectName,
			'description'      => $description,
			'role'             => $role,
			'technologiesUsed' => $technologiesUsed,
			'outcome'          => $outcome,
			'projectUrl'       => $projectUrl,
			'updated_at'       => date('Y-m-d H:i:s')
		];

		$this->load->model('candidate/Profile_mdl');

		if ($project_id) {
			// Update existing project record
			$updated = $this->Profile_mdl->update_project($project_id, $data);
			if ($updated) {
				echo json_encode([
					'success' => true,
					'message' => 'Project data updated successfully.',
					'data'    => ['id' => $project_id]
				]);
			} else {
				echo json_encode([
					'success' => false,
					'message' => 'Failed to update project data.'
				]);
			}
		} else {
			// Insert new project record
			$data['candidate_id'] = $user_id;
			$data['created_at']   = date('Y-m-d H:i:s');
			$insert_id = $this->Profile_mdl->store_project($data);
			if ($insert_id) {
				echo json_encode([
					'success' => true,
					'message' => 'Project data added successfully.',
					'data'    => ['id' => $insert_id]
				]);
			} else {
				echo json_encode([
					'success' => false,
					'message' => 'Failed to save project data.'
				]);
			}
		}
	}

	public function delete_project() {
		$project_id = $this->input->post('project_id');
		if (!$project_id) {
			echo json_encode([
				'success' => false,
				'message' => 'Invalid or missing project ID.'
			]);
			return;
		}

		$this->load->model('candidate/Profile_mdl');
		$deleted = $this->Profile_mdl->delete_project($project_id);
		if ($deleted) {
			echo json_encode([
				'success' => true,
				'message' => 'Project record deleted successfully.'
			]);
		} else {
			echo json_encode([
				'success' => false,
				'message' => 'Failed to delete project record.'
			]);
		}
	}
	
	public function fetch_awards() {
		$user_id = $this->session->userdata('user_id');
		$this->load->model('candidate/Profile_mdl');
		$awards = $this->Profile_mdl->get_awards($user_id);

		if ($awards) {
			echo json_encode([
				'success' => true,
				'awards'  => $awards,
			]);
		} else {
			echo json_encode([
				'success' => false,
				'message' => 'No award records found.'
			]);
		}
	}

	public function save_or_update_award() {
		$this->load->library('form_validation');

		// Set validation rules
		$this->form_validation->set_rules('template_id', 'Template ID', 'required|integer');
		$this->form_validation->set_rules('awardName', 'Award Name', 'required|trim');
		$this->form_validation->set_rules('awardingOrganization', 'Awarding Organization', 'required|trim');
		$this->form_validation->set_rules('dateAwarded', 'Date Awarded', 'required');

		if ($this->form_validation->run() == FALSE) {
			echo json_encode([
				'success' => false,
				'message' => validation_errors()
			]);
			return;
		}

		// Retrieve POST data safely
		$template_id          = $this->input->post('template_id', true);
		$awardName            = $this->input->post('awardName', true);
		$awardingOrganization = $this->input->post('awardingOrganization', true);
		$dateAwarded          = $this->input->post('dateAwarded', true);
		$award_id             = $this->input->post('award_id', true);

		$user_id = $this->session->userdata('user_id');

		// Check if the user has access to the provided template
		$template_exists = $this->db->get_where('tb_user_resume_templates', [
			'user_id'     => $user_id,
			'template_id' => $template_id
		])->num_rows();

		if (!$template_exists) {
			echo json_encode([
				'success' => false,
				'message' => 'Template not found or access denied.'
			]);
			return;
		}

		// Prepare data for insert/update
		$data = [
			'awardName'            => $awardName,
			'awardingOrganization' => $awardingOrganization,
			'dateAwarded'          => $dateAwarded,
			'updated_at'           => date('Y-m-d H:i:s')
		];

		$this->load->model('candidate/Profile_mdl');

		if ($award_id) {
			// Update existing award record
			$updated = $this->Profile_mdl->update_award($award_id, $data);
			if ($updated) {
				echo json_encode([
					'success' => true,
					'message' => 'Award data updated successfully.',
					'data'    => ['id' => $award_id]
				]);
			} else {
				echo json_encode([
					'success' => false,
					'message' => 'Failed to update award data.'
				]);
			}
		} else {
			// Insert new award record
			$data['candidate_id'] = $user_id;
			$data['created_at']   = date('Y-m-d H:i:s');
			$insert_id = $this->Profile_mdl->store_award($data);
			if ($insert_id) {
				echo json_encode([
					'success' => true,
					'message' => 'Award data added successfully.',
					'data'    => ['id' => $insert_id]
				]);
			} else {
				echo json_encode([
					'success' => false,
					'message' => 'Failed to save award data.'
				]);
			}
		}
	}

	public function delete_award() {
		$award_id = $this->input->post('award_id');
		if (!$award_id) {
			echo json_encode([
				'success' => false,
				'message' => 'Invalid or missing award ID.'
			]);
			return;
		}

		$this->load->model('candidate/Profile_mdl');
		$deleted = $this->Profile_mdl->delete_award($award_id);
		if ($deleted) {
			echo json_encode([
				'success' => true,
				'message' => 'Award record deleted successfully.'
			]);
		} else {
			echo json_encode([
				'success' => false,
				'message' => 'Failed to delete award record.'
			]);
		}
	}
	
	public function fetch_hobbies() {
		$user_id = $this->session->userdata('user_id');
		$this->load->model('candidate/Profile_mdl');
		$hobbies = $this->Profile_mdl->get_hobbies($user_id);

		if ($hobbies) {
			echo json_encode([
				'success' => true,
				'hobbies' => $hobbies,
			]);
		} else {
			echo json_encode([
				'success' => false,
				'message' => 'No hobby records found.'
			]);
		}
	}

	public function save_or_update_hobby() {
		$this->load->library('form_validation');

		// Set validation rules
		$this->form_validation->set_rules('template_id', 'Template ID', 'required|integer');
		$this->form_validation->set_rules('hobbyName', 'Hobby Name', 'required|trim');
		$this->form_validation->set_rules('description', 'Description', 'trim');

		if ($this->form_validation->run() == FALSE) {
			echo json_encode([
				'success' => false,
				'message' => validation_errors()
			]);
			return;
		}

		// Retrieve POST data safely
		$template_id = $this->input->post('template_id', true);
		$hobbyName   = $this->input->post('hobbyName', true);
		$description = $this->input->post('description', true);
		$hobby_id    = $this->input->post('hobby_id', true);

		$user_id = $this->session->userdata('user_id');

		// Check if the user has access to the provided template
		$template_exists = $this->db->get_where('tb_user_resume_templates', [
			'user_id'     => $user_id,
			'template_id' => $template_id
		])->num_rows();

		if (!$template_exists) {
			echo json_encode([
				'success' => false,
				'message' => 'Template not found or access denied.'
			]);
			return;
		}

		// Prepare data for insert/update
		$data = [
			'hobbyName'   => $hobbyName,
			'description' => $description,
			'updated_at'  => date('Y-m-d H:i:s')
		];

		$this->load->model('candidate/Profile_mdl');

		if ($hobby_id) {
			// Update existing hobby record
			$updated = $this->Profile_mdl->update_hobby($hobby_id, $data);
			if ($updated) {
				echo json_encode([
					'success' => true,
					'message' => 'Hobby data updated successfully.',
					'data'    => ['id' => $hobby_id]
				]);
			} else {
				echo json_encode([
					'success' => false,
					'message' => 'Failed to update hobby data.'
				]);
			}
		} else {
			// Insert new hobby record
			$data['candidate_id'] = $user_id;
			$data['created_at']   = date('Y-m-d H:i:s');
			$insert_id = $this->Profile_mdl->store_hobby($data);
			if ($insert_id) {
				echo json_encode([
					'success' => true,
					'message' => 'Hobby data added successfully.',
					'data'    => ['id' => $insert_id]
				]);
			} else {
				echo json_encode([
					'success' => false,
					'message' => 'Failed to save hobby data.'
				]);
			}
		}
	}

	public function delete_hobby() {
		$hobby_id = $this->input->post('hobby_id');
		if (!$hobby_id) {
			echo json_encode([
				'success' => false,
				'message' => 'Invalid or missing hobby ID.'
			]);
			return;
		}

		$this->load->model('candidate/Profile_mdl');
		$deleted = $this->Profile_mdl->delete_hobby($hobby_id);
		if ($deleted) {
			echo json_encode([
				'success' => true,
				'message' => 'Hobby record deleted successfully.'
			]);
		} else {
			echo json_encode([
				'success' => false,
				'message' => 'Failed to delete hobby record.'
			]);
		}
	}
	
	public function fetch_courses() {
		$user_id = $this->session->userdata('user_id');
		$this->load->model('candidate/Profile_mdl');
		$courses = $this->Profile_mdl->get_courses($user_id);

		if ($courses) {
			echo json_encode([
				'success' => true,
				'courses' => $courses,
			]);
		} else {
			echo json_encode([
				'success' => false,
				'message' => 'No course records found.'
			]);
		}
	}

	public function save_or_update_course() {
		$this->load->library('form_validation');

		// Validation rules for course fields
		$this->form_validation->set_rules('template_id', 'Template ID', 'required|integer');
		$this->form_validation->set_rules('courseName', 'Course Name', 'required|trim');
		$this->form_validation->set_rules('instituteName', 'Institute Name', 'required|trim');
		$this->form_validation->set_rules('dateCompleted', 'Date Completed', 'required');

		if ($this->form_validation->run() == FALSE) {
			echo json_encode([
				'success' => false,
				'message' => validation_errors()
			]);
			return;
		}

		// Retrieve POST data safely
		$template_id     = $this->input->post('template_id', true);
		$courseName      = $this->input->post('courseName', true);
		$instituteName   = $this->input->post('instituteName', true);
		$dateCompleted   = $this->input->post('dateCompleted', true);
		$course_id       = $this->input->post('course_id', true);

		$user_id = $this->session->userdata('user_id');

		// Check template access
		$template_exists = $this->db->get_where('tb_user_resume_templates', [
			'user_id'     => $user_id,
			'template_id' => $template_id
		])->num_rows();

		if (!$template_exists) {
			echo json_encode([
				'success' => false,
				'message' => 'Template not found or access denied.'
			]);
			return;
		}

		$data = [
			'courseName'    => $courseName,
			'instituteName' => $instituteName,
			'dateCompleted' => $dateCompleted,
			'updated_at'    => date('Y-m-d H:i:s')
		];

		$this->load->model('candidate/Profile_mdl');

		if ($course_id) {
			// Update existing course record
			$updated = $this->Profile_mdl->update_course($course_id, $data);
			if ($updated) {
				echo json_encode([
					'success' => true,
					'message' => 'Course data updated successfully.',
					'data'    => ['id' => $course_id]
				]);
			} else {
				echo json_encode([
					'success' => false,
					'message' => 'Failed to update course data.'
				]);
			}
		} else {
			// Insert new course record
			$data['candidate_id'] = $user_id;
			$data['created_at']   = date('Y-m-d H:i:s');
			$insert_id = $this->Profile_mdl->store_course($data);
			if ($insert_id) {
				echo json_encode([
					'success' => true,
					'message' => 'Course data added successfully.',
					'data'    => ['id' => $insert_id]
				]);
			} else {
				echo json_encode([
					'success' => false,
					'message' => 'Failed to save course data.'
				]);
			}
		}
	}

	public function delete_course() {
		$course_id = $this->input->post('course_id');
		if (!$course_id) {
			echo json_encode([
				'success' => false,
				'message' => 'Invalid or missing course ID.'
			]);
			return;
		}

		$this->load->model('candidate/Profile_mdl');
		$deleted = $this->Profile_mdl->delete_course($course_id);
		if ($deleted) {
			echo json_encode([
				'success' => true,
				'message' => 'Course record deleted successfully.'
			]);
		} else {
			echo json_encode([
				'success' => false,
				'message' => 'Failed to delete course record.'
			]);
		}
	}
	
	public function fetch_extraCurricularActivities() {
		$user_id = $this->session->userdata('user_id');
		$this->load->model('candidate/Profile_mdl');
		$activities = $this->Profile_mdl->get_extraCurricularActivities($user_id);

		if ($activities) {
			echo json_encode([
				'success'    => true,
				'activities' => $activities,
			]);
		} else {
			echo json_encode([
				'success' => false,
				'message' => 'No extra curricular activities found.'
			]);
		}
	}

	public function save_or_update_extraCurricularActivity() {
		$this->load->library('form_validation');

		// Set validation rules
		$this->form_validation->set_rules('template_id', 'Template ID', 'required|integer');
		$this->form_validation->set_rules('activityName', 'Activity Name', 'required|trim');
		$this->form_validation->set_rules('position', 'Position', 'required|trim');
		$this->form_validation->set_rules('description', 'Description', 'trim');

		if ($this->form_validation->run() == FALSE) {
			echo json_encode([
				'success' => false,
				'message' => validation_errors()
			]);
			return;
		}

		// Retrieve POST data safely
		$template_id  = $this->input->post('template_id', true);
		$activityName = $this->input->post('activityName', true);
		$position     = $this->input->post('position', true);
		$description  = $this->input->post('description', true);
		$activity_id  = $this->input->post('activity_id', true);

		$user_id = $this->session->userdata('user_id');

		// Check template access
		$template_exists = $this->db->get_where('tb_user_resume_templates', [
			'user_id'     => $user_id,
			'template_id' => $template_id
		])->num_rows();

		if (!$template_exists) {
			echo json_encode([
				'success' => false,
				'message' => 'Template not found or access denied.'
			]);
			return;
		}

		// Prepare data for insert/update
		$data = [
			'activityName' => $activityName,
			'position'     => $position,
			'description'  => $description,
			'updated_at'   => date('Y-m-d H:i:s')
		];

		$this->load->model('candidate/Profile_mdl');

		if ($activity_id) {
			// Update existing record
			$updated = $this->Profile_mdl->update_extraCurricularActivity($activity_id, $data);
			if ($updated) {
				echo json_encode([
					'success' => true,
					'message' => 'Extra curricular activity updated successfully.',
					'data'    => ['id' => $activity_id]
				]);
			} else {
				echo json_encode([
					'success' => false,
					'message' => 'Failed to update extra curricular activity.'
				]);
			}
		} else {
			// Insert new record
			$data['candidate_id'] = $user_id;
			$data['created_at']   = date('Y-m-d H:i:s');
			$insert_id = $this->Profile_mdl->store_extraCurricularActivity($data);
			if ($insert_id) {
				echo json_encode([
					'success' => true,
					'message' => 'Extra curricular activity added successfully.',
					'data'    => ['id' => $insert_id]
				]);
			} else {
				echo json_encode([
					'success' => false,
					'message' => 'Failed to save extra curricular activity.'
				]);
			}
		}
	}

	public function delete_extraCurricularActivity() {
		$activity_id = $this->input->post('activity_id');
		if (!$activity_id) {
			echo json_encode([
				'success' => false,
				'message' => 'Invalid or missing activity ID.'
			]);
			return;
		}

		$this->load->model('candidate/Profile_mdl');
		$deleted = $this->Profile_mdl->delete_extraCurricularActivity($activity_id);
		if ($deleted) {
			echo json_encode([
				'success' => true,
				'message' => 'Extra curricular activity deleted successfully.'
			]);
		} else {
			echo json_encode([
				'success' => false,
				'message' => 'Failed to delete extra curricular activity.'
			]);
		}
	}
	
	public function update_section_title() {
		$this->db->trans_start(); // Transaction start
			
		$section = strtolower($this->input->post('section')); // Lowercase में कन्वर्ट करें
		$title = $this->input->post('title');

		$success = $this->ResumeTemplateModel->update_section_title(			
			$section,
			$title
		);

		$this->db->trans_complete(); // Transaction complete
		$this->output
			->set_content_type('application/json')
			->set_output(json_encode([
				'success' => $success,
				'message' => $success ? 'Title updated successfully' : 'Failed to update title'			
			]));
	}
	
	// Controller में सुधार
	public function get_section_title() {
		try {
			$section = $this->input->post('section');
			$title = $this->ResumeTemplateModel->get_section_title($section);
			
			$this->output
				->set_content_type('application/json')
				->set_output(json_encode([
					'success' => true,
					'title' => $title ? $title['section_title'] : ucfirst($section)
				]));
				
		} catch(Exception $e) {
			$this->output
				->set_status_header(400)
				->set_output(json_encode([
					'success' => false,
					'error' => $e->getMessage()
				]));
		}
	}



}