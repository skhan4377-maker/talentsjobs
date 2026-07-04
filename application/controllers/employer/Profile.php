<?php defined('BASEPATH') or exit('No direct script access allowed');
date_default_timezone_set('Asia/Kolkata');
class Profile extends MY_Controller {

	public function __construct(){
		parent::__construct();			
		$this->employer_id = $this->session->userdata('user_id');		
		$this->load->model('employer/Profile_mdl');
	}

	// Controller में (Profile controller का index method)
	public function index() {
		$data['title'] = 'Profile';
		$employerDetails = $this->Profile_mdl->get_employer_details($this->employer_id);
		$status = isset($employerDetails['status']) ? strtolower($employerDetails['status']) : 'inactive';
		$data['status'] = $status;	
		// ✅ Otherwise show profile
		$data['content'] = $this->load->view('employer/profile', $data, TRUE);
		$this->load->view('templates/master', $data);
		$this->db->close();
	}
	
	public function get_employer_details() {
		$employer = $this->Profile_mdl->get_employer_details($this->employer_id);
		echo json_encode([
            'success' => true, 
            'data' => $employer,
            'csrf_token' => $this->security->get_csrf_hash()
        ]);
		exit;
	}
	
	public function save_detail() {
        // 1) Which section?
        $section = $this->input->post('form_type');
        if (!$section) {
            echo json_encode([
                'success' => false, 
                'message' => 'Form type not provided',
                'csrf_token' => $this->security->get_csrf_hash()
            ]);
            return;
        }
		
		// --- DUPLICATE CHECK FOR MOBILE ONLY IN contactBasic SECTION ---
		if ($section === 'contactBasic') {
			$mobile = $this->input->post('mobile');

			// load the helper model
			$this->load->model('home_mdl');

			// only check mobile; pass false for email
			$existing = $this->home_mdl->check_exist_value($mobile, false, 'employer');

			// if found and it belongs to another employer, block the update
			if ($existing && $existing['employer_id'] != $this->employer_id) {
				echo json_encode([
					'success' => false,
					'message' => 'That mobile number is already registered.',
                    'csrf_token' => $this->security->get_csrf_hash()
				]);
				return;
			}
		}
		
        // 2) Validation rules
        switch ($section) {
            case 'companyBasic':
                $this->form_validation->set_rules('company_name',    'Company Name',     'required|trim');
                $this->form_validation->set_rules('company_founded', 'Founded Date',     'trim');
                $this->form_validation->set_rules('company_size',    'Company Size',     'trim');
                $this->form_validation->set_rules('industry_id',     'Industry',         'trim');
				$this->form_validation->set_rules('expertise_specialization', 'Expertise & Specialization', 'trim');
           
                break;

            case 'companyExtra':
                $this->form_validation->set_rules('company_type',    'Company Type',     'trim');
                $this->form_validation->set_rules('recuiter_type',   'Recruiter Type',   'trim');
                $this->form_validation->set_rules('company_website', 'Website',          'trim|valid_url');
                $this->form_validation->set_rules('city_id',         'City',             'trim');
                $this->form_validation->set_rules('company_address','Address',          'trim');
                break;

            case 'contactBasic':
                $this->form_validation->set_rules('name',        'First Name', 'required|trim');
                $this->form_validation->set_rules('last_name',   'Last Name',  'trim');
                $this->form_validation->set_rules('mobile',      'Mobile',     'required|trim');
                break;

            case 'contactExtra':
                $this->form_validation->set_rules('employee_designation','Designation',       'trim');
                $this->form_validation->set_rules('gender',              'Gender',            'trim');
                $this->form_validation->set_rules('alternate_contact',   'Alternate Contact', 'trim');
                break;

            case 'aboutCompany':
                $this->form_validation->set_rules('about_company', 'About Company', 'required|trim');
                break;

            case 'accountSettings':
                //$this->form_validation->set_rules('status',       'Account Status', 'trim');
                $this->form_validation->set_rules('email_status', 'Email Status',   'trim');
                $this->form_validation->set_rules('call_status',  'Call Status',    'trim');
                break;

            default:
                echo json_encode([
                    'success' => false, 
                    'message' => 'Invalid form type',
                    'csrf_token' => $this->security->get_csrf_hash()
                ]);
                return;
        }

        // 3) Run validation
        if ($this->form_validation->run() === FALSE) {
            // strip out <p> tags so we get plain text
            $msg = strip_tags(validation_errors());
            echo json_encode([
                'success' => false, 
                'message' => $msg,
                'csrf_token' => $this->security->get_csrf_hash()
            ]);
            return;
        }

        // 4) Build $data array
        $data = [];
        switch ($section) {
            case 'companyBasic':
                $data = [
                    'company_name'    => $this->input->post('company_name'),
                    'company_founded' => $this->input->post('company_founded'),
                    'company_size'    => $this->input->post('company_size'),
                    'industry_id'     => $this->input->post('industry_id'),
					'expertise_specialization'   => $this->input->post('expertise_specialization'),
                ];
                break;

            case 'companyExtra':
                $data = [
                    'company_type'    => $this->input->post('company_type'),
                    'recuiter_type'   => $this->input->post('recuiter_type'),
                    'company_website' => $this->input->post('company_website'),
                    'city_id'         => $this->input->post('city_id'),
                    'company_address'=> $this->input->post('company_address'),
                ];
                break;

            case 'contactBasic':
                $data = [
                    'name'  => $this->input->post('name'),
                    'last_name'   => $this->input->post('last_name'),
                    'mobile' => $this->input->post('mobile'),
                ];
                break;

            case 'contactExtra':
                $data = [
                    'employee_designation'       => $this->input->post('employee_designation'),
                    'gender'    => $this->input->post('gender'),
                    'alternate_contact' => $this->input->post('alternate_contact'),
                ];
                break;

            case 'aboutCompany':
                $data = ['about_company' => $this->input->post('about_company')];
                break;

            case 'accountSettings':
                $data = [
                    //'status'       => $this->input->post('status'),
                    'email_status' => $this->input->post('email_status'),
                    'call_status'  => $this->input->post('call_status'),
                ];
                break;
        }

        // 5) Persist
        $ok = $this->Profile_mdl->update_employer_details(
            $this->employer_id,
			$data,
            $section           
        );

        echo json_encode([
            'success' => $ok,
            'message' => $ok
                ? 'Section saved successfully.'
                : 'Failed to save section.',
            'csrf_token' => $this->security->get_csrf_hash()
        ]);
        exit;
    }
	
	public function upload_image() {		
		$response = ['success' => 0, 'error_msg' => ''];
		$id = $this->employer_id;            // use the session‐loaded employer_id
		$section = 'companyExtra';           // or whatever section you prefer

		if (!empty($_FILES['logo']['name'])) {
			// 1) Configure upload
			$config['upload_path']   = './uploads/employer/profile/';
			$config['allowed_types'] = 'jpg|jpeg|png|webp';
			$ext = pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION);
			$config['file_name'] = time() . '_' . uniqid() . '.' . $ext;

			$this->load->library('upload', $config);

			if (!$this->upload->do_upload('logo')) {
				$response['error_msg'] = $this->upload->display_errors('', '');
			} else {
				$upload_data = $this->upload->data();
				$full_path   = $upload_data['full_path'];

				// 2) Optional: compress / resize
				$this->compress_image($full_path);

				// 3) Fetch old logo path so we can delete it
				$old = $this->Profile_mdl->get_employer_details($id);
				if (!empty($old['logo'])) {
					$old_file = FCPATH . $old['logo'];
					if (file_exists($old_file)) {
						@unlink($old_file);
					}
				}

				// 4) Build the new relative path for DB
				$relative_path = 'uploads/employer/profile/'.$upload_data['file_name'];

				// 5) Persist the change
				$update_data = [
					'logo'       => $relative_path,
					'updated_at' => date('Y-m-d H:i:s'),
				];
				$ok = $this->Profile_mdl->update_employer_details(
					$id,
					$update_data,
					$section
					
				);

				if ($ok) {
					// 🔥 SESSION SYNC (real fix)
					$this->session->set_userdata([
						'logo' => base_url($relative_path)
					]);
					$response['success']  = 1;
					$response['image_url'] = base_url($relative_path);
					$response['csrf_token'] = $this->security->get_csrf_hash();
				} else {
					$response['error_msg'] = 'Database update failed.';
				}
			}
		} else {
			$response['error_msg'] = 'No file selected.';
		}

		echo json_encode($response);
	}

	/**
	 * Compress the uploaded image
	 * 
	 * @param string $image_path Full path of the uploaded image file
	 */
	private function compress_image($image_path) {
		$config['image_library']  = 'gd2';
		$config['source_image']   = $image_path;
		$config['maintain_ratio'] = TRUE;
		$config['width']          = 800; // Maximum width constraint
		$config['height']         = 600; // Maximum height constraint
		$config['quality']        = 75;  // Image quality

		$this->load->library('image_lib', $config);
		$this->image_lib->resize();
	}
	
}