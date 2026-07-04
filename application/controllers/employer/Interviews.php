<?php defined('BASEPATH') OR exit('No direct script access allowed');
date_default_timezone_set('Asia/Kolkata');

class Interviews extends MY_Controller {

    public function __construct() {
        parent::__construct();		
        $this->load->model(['employer/Interview_model', 'employer/Application_model', 'employer/PostJobModel']);
        $this->employer_id = $this->session->userdata('user_id');    
    }
	
	public function index() {
		$this->load->model('employer/Profile_mdl', 'Profile_mdl');    
		$profile = $this->Profile_mdl->get_employer_details($this->employer_id);    
		$status = isset($profile['status']) ? strtolower($profile['status']) : 'inactive';

		// ✅ Load interviews if status is valid
		$data['title'] = 'Upcoming Interviews';
		$data['interviews'] = $this->Interview_model->get_upcoming($this->employer_id);
		$data['pending_interviews'] = $this->Interview_model->count_pending_interviews($this->employer_id);
		$data['status'] = $status;

		$data['content'] = $this->load->view('employer/interviews/list', $data, TRUE);
		$this->load->view('templates/master', $data);
		$this->db->close();
	}
  	
    public function schedule($applied_id) {
		$this->load->library('form_validation');

		$application = $this->Application_model->get_application_by_id($applied_id, $this->employer_id);
		if (!$application) show_404();

		// ✅ Check employer status
		$this->load->model('employer/Profile_mdl', 'Profile_mdl');
		$profile = $this->Profile_mdl->get_employer_details($this->employer_id);
		$status = isset($profile['status']) ? strtolower($profile['status']) : 'inactive';
		
		if ($status !== 'active') {
		$error_msg = 'Your employer account is inactive. You cannot schedule interviews.';

		if ($this->input->is_ajax_request()) {
			$this->output
				->set_content_type('application/json')
				->set_output(json_encode([
					'success' => false,
					'error'   => $error_msg,
					'csrf'    => $this->security->get_csrf_hash()
				]));
			return;
			}
		}
		

		$interview = null;
		$is_edit = false;

		if ($this->input->get('edit')) {
			$interview = $this->Interview_model->get_interview($this->input->get('edit'), $this->employer_id);
			if ($interview && $interview['applied_id'] != $applied_id) {
				show_error('Invalid interview for this application', 403);
			}
			$is_edit = (bool) $interview;
		}

		$this->form_validation->set_rules('interview_date', 'Date', 'required|callback_validate_future_date');
		$this->form_validation->set_rules('interview_time', 'Time', 'required');
		$this->form_validation->set_rules('interview_type', 'Type', 'required|in_list[Video Call,In-person,Phone]');
		$this->form_validation->set_rules('interview_link', 'Interview Link', 'valid_url|max_length[255]');


		$this->form_validation->set_rules('notes', 'Notes', 'max_length[500]');

		if ($this->form_validation->run()) {
			$data = $this->input->post();
			unset($data['participants']);
		
			try {
				if ($is_edit) {
					$success = $this->Interview_model->update_interview($interview['interview_id'], $data);
					$message = 'Interview updated successfully!';
				} else {
					$success = $this->Interview_model->schedule($applied_id, $data);
					$message = 'Interview scheduled successfully!';
				}

				if ($success) {
					$interview = $this->Interview_model->get_interview(
						$is_edit ? $interview['interview_id'] : $success, 
						$this->employer_id
					);

					$application = $this->Application_model->get_application_by_id($applied_id, $this->employer_id);

					$notifMsg = sprintf(
						"%s on %s at %s",
						$is_edit ? "Your interview has been updated" : "Your interview has been scheduled",
						date('j M Y', strtotime($interview['interview_date'])),
						date('h:i A', strtotime($interview['interview_time']))
					);

					$this->load->model('Notification_model');
					$this->Notification_model->create([
						'user_id' => $application['candidate_id'],
						'type'    => 'candidate',
						'message' => $notifMsg,
						'link'    => 'candidate/interviews/view/'.$interview['interview_id'],
						'created_at'=> date('Y-m-d H:i:s'),
					]);

					// Send Email
					$candidate_email = $application['email'] ?? '';
					$candidate_name  = $application['name'] . ' ' . $application['last_name'];
					$company_name    = $application['employer_company_name'] ?? SITE_NAME;
					$job_title       = $application['job_title'] ?? '';
					$interview_date  = date('j M Y', strtotime($interview['interview_date']));
					$interview_time  = date('h:i A', strtotime($interview['interview_time']));
					$interview_type  = $interview['interview_type'] ?? '';
					$interview_link  = $interview['interview_link'] ?? '';
					$interview_notes = $interview['notes'] ?? '';

					if (!empty($candidate_email)) {
						$subject = 'Interview ' . ($is_edit ? 'Updated' : 'Scheduled') . ' - ' . $company_name;

						$email_message = $this->load->view('employer/email/interview_invite', [
							'candidate_name'   => $candidate_name,
							'company_name'     => $company_name,
							'job_title'        => $job_title,
							'interview_date'   => $interview_date,
							'interview_time'   => $interview_time,
							'interview_type'   => $interview_type,
							'interview_notes'  => $interview_notes,
							'interview_link'   => $interview_link ?? '', // ✅ FIXED
							'is_edit'          => $is_edit
						], true);
						
						SendEmailTo($candidate_email, $subject, $email_message);
						//send_mailercloud_email($candidate_email, $candidate_name, $subject, $email_message);
					}

					if ($this->input->is_ajax_request()) {
						$this->output
							->set_content_type('application/json')
							->set_output(json_encode([
								'success' => true,
								'message' => $message,
								'interview' => $interview,
								'csrf' => $this->security->get_csrf_hash()
							]));
						return;
					}

					$this->session->set_flashdata('success', $message);
					redirect('employer/interviews/view/'.($is_edit ? $interview['interview_id'] : $success));
				}
			} catch(Exception $e) {
				$error = $e->getMessage();
			}

			if (!isset($error)) {
				$error = 'Failed to ' . ($is_edit ? 'update' : 'schedule') . ' interview. Please try again.';
			}
		} else {
			$error = validation_errors('<li>', '</li>');
		}

		if ($this->input->is_ajax_request()) {
			$this->output
				->set_content_type('application/json')
				->set_output(json_encode([
					'success' => false,
					'error' => $error,
					'errors' => $this->form_validation->error_array(),
					'csrf' => $this->security->get_csrf_hash()
				]));
			return;
		}

		$data['title'] = ($is_edit ? 'Edit' : 'Schedule') . ' Interview - ' . htmlspecialchars($application['name']);
		$data['application'] = $application;
		$data['interview'] = $interview;
		$data['status'] = $status;

		$data['content'] = $this->load->view('employer/interviews/schedule', $data, TRUE);
		$this->load->view('templates/master', $data);
	}

    public function validate_future_date($date) {
		$time = $this->input->post('interview_time');

		if (!$date || !$time) {
			$this->form_validation->set_message('validate_future_date', 'Interview date and time are required.');
			return false;
		}

		$interview_timestamp = strtotime($date . ' ' . $time);
		$now = time();
		$min_date = strtotime('+1 day', strtotime(date('Y-m-d 00:00:00'))); // Tomorrow 00:00
		$max_date = strtotime('+60 days', strtotime(date('Y-m-d 00:00:00'))); // 60 days ahead

		if ($interview_timestamp < $min_date) {
			$this->form_validation->set_message('validate_future_date', 'Interview must be scheduled at least 1 day in advance.');
			return false;
		}

		if ($interview_timestamp > $max_date) {
			$this->form_validation->set_message('validate_future_date', 'Interview cannot be scheduled more than 60 days in advance.');
			return false;
		}

		return true;
	}

    public function update($interview_id) {
		$this->load->library('form_validation');
		$interview = $this->Interview_model->get_interview($interview_id, $this->employer_id);
		if (!$interview) show_404();

		$this->form_validation->set_rules('interview_date', 'Date', 'required|callback_validate_future_date');
		$this->form_validation->set_rules('interview_time', 'Time', 'required');
		$this->form_validation->set_rules('interview_type', 'Type', 'required|in_list[Video Call,In-person,Phone]');
		$this->form_validation->set_rules('ApplicationStage', 'Interview Stage', 'required|in_list[Interview Scheduled,Scheduled,Rescheduled,Completed,Hired,Rejected]');
		$this->form_validation->set_rules('interview_link', 'Interview Link', 'valid_url|max_length[255]');

		$this->form_validation->set_rules('notes', 'Notes', 'max_length[500]');

		if ($this->form_validation->run()) {
			$data = $this->input->post();
			$update_data = [
				'interview_date'    => $data['interview_date'],
				'interview_time'    => $data['interview_time'],
				'interview_type'    => $data['interview_type'],
				'status'            => $data['ApplicationStage'], // ✅ stage
				'notes'             => $data['notes'] ?? null,
				'interview_link'    => $data['interview_link'],   // ✅ new line
				'created_at'        => date('Y-m-d H:i:s')
			];

			if ($this->Interview_model->update_interview($interview_id, $update_data)) {
				// Optional: update stage in tb_applied if required
				$this->db->where('applied_id', $interview['applied_id'])
						 ->update('tb_applied', ['ApplicationStage' => $data['ApplicationStage']]);
				
				$application = $this->Application_model->get_application_by_id($interview['applied_id'], $this->employer_id);
				if ($application) {
					$notifMsg = sprintf(
						"Your interview has been updated on %s at %s",
						date('j M Y', strtotime($data['interview_date'])),
						date('h:i A', strtotime($data['interview_time']))
					);
					$this->load->model('Notification_model');
					$this->Notification_model->create([
						'user_id' => $application['candidate_id'],
						'type'    => 'candidate',
						'message' => $notifMsg,
						'link'    => 'candidate/interviews/view/' . $interview_id
					]);
				}

				if ($this->input->is_ajax_request()) {
					$this->output->set_content_type('application/json')->set_output(json_encode([
						'success'   => true,
						'message'   => 'Interview updated successfully!',
						'interview' => $this->Interview_model->get_interview($interview_id),
						'csrf'      => $this->security->get_csrf_hash()
					]));
					return;
				}
			} else {
				$error = 'Failed to update interview. Please try again.';
			}
		} else {
			$error = validation_errors('<li>', '</li>');
		}
	}
	
	

    

}
