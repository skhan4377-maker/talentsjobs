<?php defined('BASEPATH') or exit('No direct script access allowed');

class Profile extends MY_Controller {

    public function __construct(){
        parent::__construct();     
        $this->load->model('candidate/Profile_mdl');    
        $this->user_id = $this->session->userdata('user_id');
        $this->name = $this->session->userdata('name');
    }

    public function index() {    
        $data['title'] = 'Profile';        
        $data['candidate_info'] = $this->Profile_mdl->get_candidate_details($this->user_id);  
        if (isset($data['candidate_info']['json_data']) && is_string($data['candidate_info']['json_data'])) {
            $data['candidate_info']['json_data'] = json_decode($data['candidate_info']['json_data'], true);
        }        
        $data['content'] = $this->load->view('candidate/profile', $data, TRUE);
        $this->load->view('templates/master', $data);        
    }
    
    // AJAX: Fetch candidate details for pre-filling form fields
    public function get_candidate_details() {
        $candidate = $this->Profile_mdl->get_candidate_details($this->user_id);
        echo json_encode([
            'success' => true, 
            'data' => $candidate,
            'csrf_token' => $this->security->get_csrf_hash()
        ]);
        exit;
    }

	public function save_detail() {
		$form_type = $this->input->post('form_type');
		if (!$form_type) {
			echo json_encode([
				'success' => false, 
				'message' => 'Form type not provided',
				'csrf_token' => $this->security->get_csrf_hash()
			]);
			return;
		}

		$id = $this->user_id;

		/* ================= VALIDATION ================= */
		switch ($form_type) {
			case 'basic':
				$this->form_validation->set_rules('first_name', 'First Name', 'required|trim');
				$this->form_validation->set_rules('last_name', 'Last Name', 'required|trim');
				break;

			case 'resume':
				$this->form_validation->set_rules('resume_headline', 'Resume Headline', 'required|trim');
				break;

			case 'about':
				$this->form_validation->set_rules('about', 'About', 'required|trim');
				break;

			case 'personal':
				$this->form_validation->set_rules('mobile', 'Mobile', 'trim');
				break;

			case 'job_preferences':
				$this->form_validation->set_rules('postal', 'Postal Code', 'required|trim');
				$this->form_validation->set_rules('notice_period', 'Notice Period', 'required|trim');
				$this->form_validation->set_rules('current_ctc', 'Current CTC', 'required|numeric');
				break;

			case 'links':
				// No validation rules required
				break;

			default:
				echo json_encode([
					'success' => false,
					'message' => 'Invalid form type',
					'csrf_token' => $this->security->get_csrf_hash()
				]);
				return;
		}

		// Run validation only if rules were set (skip for 'links')
		if ($form_type !== 'links') {
			if ($this->form_validation->run() == FALSE) {
				echo json_encode([
					'success' => false,
					'errors'  => validation_errors(),
					'csrf_token' => $this->security->get_csrf_hash()
				]);
				return;
			}
		}

		/* ================= DATA MAPPING ================= */
		$data = [];

		switch ($form_type) {
			case 'basic':
				$data = [
					'name' => $this->input->post('first_name'),
					'last_name' => $this->input->post('last_name'),
					'dob' => $this->input->post('dob'),
					'gender' => $this->input->post('gender'),
					'work_status' => $this->input->post('work_status'),
					'designations' => $this->input->post('designation'),
					'total_experience_years' => $this->input->post('experience_years') ?: 0,
					'total_experience_months' => $this->input->post('experience_months') ?: 0
				];
				break;

			case 'resume':
				if (!empty($_FILES['resume']['name'])) {
					$resume_data = $this->handleResumeUpload();
					if ($resume_data['status']) {
						$data['resume'] = $resume_data['file_path'];
					}
				}
				$data['resume_headline'] = $this->input->post('resume_headline');
				break;

			case 'about':
				$data = [
					'about' => $this->input->post('about'),
					'objective' => $this->input->post('objective')
				];
				break;

			case 'personal':
				$data = [
					'mobile' => $this->input->post('mobile'),
					'address' => $this->input->post('address'),
					'placeOfBirth' => $this->input->post('placeOfBirth'),
					'city_id' => $this->input->post('city_id'),
					//'country_id' => $this->input->post('country_id'),
					'functional_id' => $this->input->post('functional_id'),
					'industry_id' => $this->input->post('industry_id')
				];
				break;

			case 'job_preferences':
				$data = [
					'postal' => $this->input->post('postal'),
					'notice_period' => $this->input->post('notice_period'),
					'current_ctc' => $this->input->post('current_ctc')
				];
				break;

			case 'links':
				$data = [
					'linkedinProfile' => $this->input->post('linkedinProfile'),
					'portfolioUrl' => $this->input->post('portfolioUrl')
				];
				break;
		}

		/* ================= UPDATE ================= */
		$updated = $this->Profile_mdl->update_candidate_details($id, $data);

		if ($updated) {
			echo json_encode([
				'success' => true,
				'message' => 'Profile updated successfully',
				'csrf_token' => $this->security->get_csrf_hash()
			]);
		} else {
			echo json_encode([
				'success' => false,
				'message' => 'Update failed',
				'csrf_token' => $this->security->get_csrf_hash()
			]);
		}
		exit;
	}

    public function save_skills() {
        $candidate_id = $this->user_id;

        $skills_string = $this->input->post('skills');
        if (empty($skills_string)) {
            echo json_encode([
                'success' => false, 
                'message' => 'Skills are required',
                'csrf_token' => $this->security->get_csrf_hash()
            ]);
            return;
        }

        $skills_array = array_filter(array_map('trim', explode(',', $skills_string)));
        $skills_array = array_unique($skills_array);
    
        $this->Profile_mdl->delete_skills_by_candidate($candidate_id);

        foreach ($skills_array as $skill_name) {
            $this->Profile_mdl->insert_skill([
                'candidate_id' => $candidate_id,
                'skill_name'   => $skill_name
            ]);
        }

        echo json_encode([
            'success' => true, 
            'message' => 'Skills updated successfully.',
            'csrf_token' => $this->security->get_csrf_hash()
        ]);
    }

    public function save_work() {   
        $this->form_validation->set_rules('job_title', 'Job Title', 'required');
        $this->form_validation->set_rules('employer_name', 'Employer Name', 'required');
        $this->form_validation->set_rules('start_date', 'Start Date', 'required');
        $this->form_validation->set_rules('job_type', 'Job Type', 'trim');
        $this->form_validation->set_rules('work_location', 'Work Location', 'trim');
        $this->form_validation->set_rules('responsibilities', 'Responsibilities', 'trim');
        $this->form_validation->set_rules('achievements', 'Achievements', 'trim');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode([
                'success' => false,
                'errors'  => validation_errors(),
                'csrf_token' => $this->security->get_csrf_hash()
            ]);
            return;
        }
        
        $data = array(
            'candidate_id'     => $this->user_id,
            'job_title'        => $this->input->post('job_title'),
            'employer_name'    => $this->input->post('employer_name'),
            'start_date'       => $this->input->post('start_date'),
            'end_date'         => $this->input->post('end_date'),
            'is_current'       => $this->input->post('is_current') ? 1 : 0,
            'job_type'         => $this->input->post('job_type'),
            'work_location'    => $this->input->post('work_location'),
            'responsibilities' => $this->input->post('responsibilities'),
            'achievements'     => $this->input->post('achievements')
        );

        $id = $this->input->post('id');
        if ($id) {
            $updated = $this->Profile_mdl->update_work($id, $data);
            if ($updated) {
                echo json_encode([
                    'success' => true, 
                    'message' => 'Work experience updated successfully.',
                    'csrf_token' => $this->security->get_csrf_hash()
                ]);
            } else {
                echo json_encode([
                    'success' => false, 
                    'message' => 'Failed to update work experience.',
                    'csrf_token' => $this->security->get_csrf_hash()
                ]);
            }
        } else {
            $insert_id = $this->Profile_mdl->employment_history_store($data);
            if ($insert_id) {
                echo json_encode([
                    'success' => true, 
                    'insert_id' => $insert_id, 
                    'message' => 'Work experience added successfully.',
                    'csrf_token' => $this->security->get_csrf_hash()
                ]);
            } else {
                echo json_encode([
                    'success' => false, 
                    'errors' => 'Database insert failed.',
                    'csrf_token' => $this->security->get_csrf_hash()
                ]);
            }
        }
    }
    
    public function get_work() {
        $workExperiences = $this->Profile_mdl->get_work_experiences($this->user_id);

        if ($workExperiences) {
            echo json_encode([
                'success' => true,
                'data'    => $workExperiences,
                'csrf_token' => $this->security->get_csrf_hash()
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'No work experience found.',
                'csrf_token' => $this->security->get_csrf_hash()
            ]);
        }
        $this->db->close();
    }

    public function get_work_experience_by_id() {
        $id = $this->input->get('id');
        if (!$id) {
            echo json_encode([
                'success' => false,
                'message' => 'Invalid or missing ID.',
                'csrf_token' => $this->security->get_csrf_hash()
            ]);
            $this->db->close();
            return;
        }
        
        $record = $this->Profile_mdl->get_work_experience_by_id($id);
        if ($record) {
            echo json_encode([
                'success' => true,
                'data' => $record,
                'csrf_token' => $this->security->get_csrf_hash()
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Record not found.',
                'csrf_token' => $this->security->get_csrf_hash()
            ]);
        }
        $this->db->close();
    }

    public function delete_work_experience() {
        $id = $this->input->post('id');
        if (!$id) {
            echo json_encode([
                'success' => false,
                'message' => 'Invalid or missing ID.',
                'csrf_token' => $this->security->get_csrf_hash()
            ]);
            $this->db->close();
            return;
        }
        
        $deleted = $this->Profile_mdl->delete_work_experience($id);
        if ($deleted) {
            echo json_encode([
                'success' => true,
                'message' => 'Work experience deleted successfully.',
                'csrf_token' => $this->security->get_csrf_hash()
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Failed to delete work experience.',
                'csrf_token' => $this->security->get_csrf_hash()
            ]);
        }
        $this->db->close();
    }

    public function save_education() {
        $this->form_validation->set_rules('degreeName', 'Degree Name', 'required');
        $this->form_validation->set_rules('institutionName', 'Institution Name', 'required');
        $this->form_validation->set_rules('startYear', 'Start Year', 'required|numeric');
        $this->form_validation->set_rules('endYear', 'End Year', 'required|numeric');
        $this->form_validation->set_rules('fieldOfStudy', 'Field of Study', 'required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode([
                'success' => false,
                'errors'  => validation_errors(),
                'csrf_token' => $this->security->get_csrf_hash()
            ]);
            return;
        }

        $data = array(
            'candidate_id'    => $this->user_id,
            'degreeName'      => $this->input->post('degreeName'),
            'institutionName' => $this->input->post('institutionName'),
            'startYear'       => $this->input->post('startYear'),
            'endYear'         => $this->input->post('endYear'),
            'fieldOfStudy'    => $this->input->post('fieldOfStudy'),
            'honors'          => $this->input->post('honors')
        );

        $id = $this->input->post('id');
        if ($id) {
            $updated = $this->Profile_mdl->update_education($id, $data);
            if ($updated) {
                echo json_encode([
                    'success' => true, 
                    'message' => 'Education updated successfully.',
                    'csrf_token' => $this->security->get_csrf_hash()
                ]);
            } else {
                echo json_encode([
                    'success' => false, 
                    'message' => 'Failed to update education.',
                    'csrf_token' => $this->security->get_csrf_hash()
                ]);
            }
        } else {
            $insert_id = $this->Profile_mdl->store_education($data);
            if ($insert_id) {
                echo json_encode([
                    'success' => true, 
                    'message' => 'Education added successfully.', 
                    'insert_id' => $insert_id,
                    'csrf_token' => $this->security->get_csrf_hash()
                ]);
            } else {
                echo json_encode([
                    'success' => false, 
                    'message' => 'Failed to add education.',
                    'csrf_token' => $this->security->get_csrf_hash()
                ]);
            }
        }
    }

    public function get_education() {
        $educationRecords = $this->Profile_mdl->get_educations($this->user_id);
        if ($educationRecords) {
            echo json_encode([
                'success' => true,
                'data'    => $educationRecords,
                'csrf_token' => $this->security->get_csrf_hash()
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'No education records found.',
                'csrf_token' => $this->security->get_csrf_hash()
            ]);
        }
        $this->db->close();
    }

    public function get_education_by_id() {
        $id = $this->input->get('id');
        if (!$id) {
            echo json_encode([
                'success' => false,
                'message' => 'Invalid or missing ID.',
                'csrf_token' => $this->security->get_csrf_hash()
            ]);
            return;
        }
        $record = $this->Profile_mdl->get_education_by_id($id);
        if ($record) {
            echo json_encode([
                'success' => true,
                'data'    => $record,
                'csrf_token' => $this->security->get_csrf_hash()
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Record not found.',
                'csrf_token' => $this->security->get_csrf_hash()
            ]);
        }
        $this->db->close();
    }

    public function delete_education() {
        $id = $this->input->post('id');
        if (!$id) {
            echo json_encode([
                'success' => false,
                'message' => 'Invalid or missing ID.',
                'csrf_token' => $this->security->get_csrf_hash()
            ]);
            return;
        }
        $deleted = $this->Profile_mdl->delete_education($id);
        if ($deleted) {
            echo json_encode([
                'success' => true,
                'message' => 'Education record deleted successfully.',
                'csrf_token' => $this->security->get_csrf_hash()
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Failed to delete education record.',
                'csrf_token' => $this->security->get_csrf_hash()
            ]);
        }
        $this->db->close();
    }

    public function upload_image() {
        $response = array('success' => 0, 'error_msg' => '');
        
        if (isset($_FILES['image']['name']) && $_FILES['image']['name'] != "") {
            $id = $this->user_id;
            
            $config['upload_path']   = './uploads/candidate/profile/';
            $config['allowed_types'] = 'jpg|jpeg|png';
            $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
			$config['file_name'] = time() . '_' . uniqid() . '.' . $ext;


            $this->load->library('upload', $config);

            if (!$this->upload->do_upload('image')) {
                $response['error_msg'] = $this->upload->display_errors();
            } else {
                $upload_data = $this->upload->data();
                $image_path  = $upload_data['full_path'];

                $this->compress_image($image_path);

                $file_paths = $this->Profile_mdl->get_candidate_files($id);

                if (!empty($file_paths['profile_image'])) {
                    $file_path = FCPATH . $file_paths['profile_image'];
                    if (file_exists($file_path)) {
                        unlink($file_path);
                    }
                }

                $relative_path = 'uploads/candidate/profile/' . $upload_data['file_name'];

                $update_data = array(
                    'logo'       => $relative_path,
                    'updated_at' => date('Y-m-d H:i:s')
                );

                $updated = $this->Profile_mdl->update_candidate_details($id, $update_data);

                if ($updated) {
					// 🔥 yahi main fix hai
					$this->session->set_userdata([
						'logo' => base_url($relative_path)
					]);

                    $response['success'] = 1;
                    $response['image_url'] = $relative_path;
                    $response['csrf_token'] = $this->security->get_csrf_hash();
                } else {
                    $response['error_msg'] = 'Database update failed.';
                }
            }
        } else {
            $response['error_msg'] = 'No file selected for upload.';
        }
        
        echo json_encode($response);
    }

    private function compress_image($image_path) {
        $config['image_library']  = 'gd2';
        $config['source_image']   = $image_path;
        $config['maintain_ratio'] = TRUE;
        $config['width']          = 800;
        $config['height']         = 600;
        $config['quality']        = 75;

        $this->load->library('image_lib', $config);
        $this->image_lib->resize();
    }

    public function deleteResume() {
        $response = array('success' => 0);
    
        if (!empty($_POST['resume'])) {
            $resume_file = $_POST['resume'];
            $file_path = FCPATH . $resume_file;

            $id = $this->user_id;
            $project_data = array(
                'updated_at' => date('Y-m-d H:i:s'),
                'resume' => NULL
            );
            $this->Profile_mdl->update_candidate_details($id, $project_data);

            if (file_exists($file_path)) {
                if (unlink($file_path)) {
                    $response['success'] = 1;
                    $response['csrf_token'] = $this->security->get_csrf_hash();
                }
            }
        }
    
        echo json_encode($response);
    }
    
    public function handleResumeUpload() {
        $file_data = array('status' => false);
        $id = $this->user_id;

        $this->load->model('Profile_mdl');
        $file_name = $this->Profile_mdl->get_candidate_files($id);

        if (!empty($file_name['resume']) && file_exists($file_name['resume'])) {
            unlink($file_name['resume']);
        }

        if (!empty($_FILES['resume']['name'])) {
            $config['upload_path']   = './uploads/candidate/resume';
            $config['allowed_types'] = 'pdf|doc|docx|rtf';
            $config['file_name']     = time() . '-' . preg_replace("/[\s_]/", "-", $_FILES['resume']['name']);
            $config['max_size']      = 10240;

            $this->load->library('upload', $config);

            if ($this->upload->do_upload('resume')) {
                $uploadData = $this->upload->data();
                $file_data['status'] = true;
                $file_data['file_data'] = $uploadData;
                $file_data['file_path'] = 'uploads/candidate/resume/' . $uploadData['file_name'];
            } else {
                $file_data['message'] = $this->upload->display_errors();
            }
        } else {
            $file_data['status'] = true;
        }

        return $file_data;
    }
}