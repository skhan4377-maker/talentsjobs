<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class AdminEmployer extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('admin/AdminEmployer_model');
        $this->load->library('pagination');
        $this->load->helper(['form', 'url']);
    }
   
    public function employers() {
		//$this->load->model('AdminEmployer_model');

		// Define and sanitize filter inputs
		$filters = [
			'company_name'     => $this->input->get('company_name', true),
			'email'            => $this->input->get('email', true),
			'mobile'           => $this->input->get('mobile', true),
			'status'           => $this->input->get('status', true),
			'industry_id'      => $this->input->get('industry_id', true),
			'membership_type'  => $this->input->get('membership_type', true),
			'from_date'        => $this->input->get('from_date', true),
			'to_date'          => $this->input->get('to_date', true),
		];
		$data = [
			'title'      => 'Employers List',
			'filters'    => $filters,
			'industries' => $this->AdminEmployer_model->get_industries()
		];

		$data['content'] = $this->load->view('admin/employers/list_full', $data, true);
		$this->load->view('templates/master', $data);
	}

	public function datatables() {
		$start  = intval($this->input->get('start'));
		$length = intval($this->input->get('length'));
		$search = $this->input->get('search')['value'] ?? '';

		$order_column_index = $this->input->get('order')[0]['column'] ?? 0;
		$order_column = $this->input->get('columns')[$order_column_index]['data'] ?? 'id';
		$order_dir = $this->input->get('order')[0]['dir'] ?? 'asc';

		// Custom filters from GET
		$filters = [
			'company_name'     => $this->input->get('company_name', true),
			'email'            => $this->input->get('email', true),
			'mobile'           => $this->input->get('mobile', true),
			'status'           => $this->input->get('status', true),
			'industry_id'      => $this->input->get('industry_id', true),
			'membership_type'  => $this->input->get('membership_type', true),
			'from_date'        => $this->input->get('from_date', true),
			'to_date'          => $this->input->get('to_date', true),
		];

		$data = $this->AdminEmployer_model->get_datatables($start, $length, $search, $order_column, $order_dir, $filters);
		$total = $this->AdminEmployer_model->count_all();
		$filtered = $this->AdminEmployer_model->count_filtered($search, $filters);

		echo json_encode([
			"draw" => intval($this->input->get('draw')),
			"recordsTotal" => $total,
			"recordsFiltered" => $filtered,
			"data" => $data
		]);
	}


	// Return employer details and email logs in JSON
	public function get_employer_json($id) {		
		$data = $this->AdminEmployer_model->get_employer($id);
		

		if ($data) {
			echo json_encode([
				'status' => 'success',
				'data' => $data,
				'email_logs' => ''
			]);
		} else {
			echo json_encode([
				'status' => 'error',
				'message' => 'Employer not found'
			]);
		}
	}

    public function update_employer() {
		$data = $this->input->post();

		 if (empty($data['employer_id'])) {
			echo json_encode([
				'status' => 'error',
				'message' => 'Invalid ID',
				'csrf_token' => $this->security->get_csrf_hash()
			]);
			return;
		}

		$this->load->model('Notification_model');	
		// Fetch current employer info
		$employer = $this->AdminEmployer_model->get_employer($data['employer_id']);
		if (!$employer) {
			echo json_encode([
				'status' => 'error',
				'message' => 'Employer not found',
				'csrf_token' => $this->security->get_csrf_hash()
			]);
			return;
		}

		$update = [
			'status' => $data['status'],
			'updated_at' => date('Y-m-d H:i:s'),
		];

		$notifMsg = null;
		$emailSubject = "";
		$emailMessage = "";
		
		if ($data['status'] === 'rejected' && !empty($data['rejection_reason'])) {
			$update['rejection_reason'] = $data['rejection_reason'];
			$update['admin_suggestion'] = NULL;
			
			$notifMsg = "Your employer profile has been rejected by Talents Jobs. Reason: " . $data['rejection_reason'];
			
			// Email content for rejection
			$emailSubject = "Talents Jobs - Employer Profile Rejected";
			$emailMessage = "
			<html>
			<body style='font-family: Arial, sans-serif; margin: 0; padding: 0; background-color: #f4f4f4;'>
			  <div style='width: 100%; max-width: 600px; margin: 20px auto; background-color: #ffffff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);'>
				<div style='text-align: center; padding: 15px 0; background-color: #d32f2f; color: #ffffff; border-radius: 8px 8px 0 0;'>
				  <h1 style='font-size: 24px; margin: 0;'>Talents Jobs</h1>
				</div>
				<div style='padding: 25px; font-size: 16px; color: #333333; line-height: 1.6;'>
				  <p>Dear Employer,</p>
				  <p>Thank you for registering with <strong>Talents Jobs</strong>.</p>
				  <p>After reviewing your profile, we regret to inform you that your employer account has been <strong>rejected</strong>.</p>
				  <p><strong>Reason:</strong> {$data['rejection_reason']}</p>
				  <p>If you believe this decision was made in error or if you would like to rectify the issues mentioned above, feel free to reach out to our support team.</p>
				  <div style='text-align: center; margin: 30px 0;'>
					<a href='" . base_url('contact-us') . "' style='padding: 12px 25px; background-color: #d32f2f; color: #ffffff; text-decoration: none; border-radius: 5px; font-weight: bold;'>Contact Support</a>
				  </div>
				</div>
				<div style='padding: 10px; background-color: #f1f1f1; text-align: center; font-size: 12px; color: #666666; border-radius: 0 0 8px 8px;'>
				  <p>&copy; 2025 Talents Jobs | All rights reserved</p>
				</div>
			  </div>
			</body>
			</html>";

		} elseif ($data['status'] === 'under_review' && !empty($data['admin_suggestion'])) {
			$update['admin_suggestion'] = $data['admin_suggestion'];
			$update['rejection_reason'] = NULL;
			
			$notifMsg = "Your employer profile is under review. Suggestion: " . $data['admin_suggestion'];

			$emailSubject = "Talents Jobs - Employer Profile Under Review";
			$emailMessage = "
			<html>
			<body style='font-family: Arial, sans-serif; margin: 0; padding: 0; background-color: #f4f4f4;'>
			  <div style='max-width: 600px; margin: 20px auto; background-color: #ffffff; border-radius: 8px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); overflow: hidden;'>
				<div style='background-color: #f9a825; color: #ffffff; text-align: center; padding: 20px;'>
				  <h1 style='font-size: 24px; margin: 0;'>Talents Jobs</h1>
				</div>
				<div style='padding: 25px 20px; font-size: 16px; color: #333333; line-height: 1.6;'>
				  <p>Dear Employer,</p>
				  <p>Thank you for submitting your profile to <strong>Talents Jobs</strong>.</p>
				  <p>Your employer profile is currently <strong>under review</strong> by our verification team.</p>
				  <p><strong>Suggestion:</strong> {$data['admin_suggestion']}</p>
				  <p>Please log in and make the necessary updates to help us complete the verification process quickly.</p>
				  <div style='text-align: center; margin: 30px 0;'>
					<a href='" . base_url('employer/profile') . "' style='padding: 12px 25px; background-color: #f9a825; color: #ffffff; text-decoration: none; border-radius: 5px; font-weight: bold;'>Update Your Profile</a>
				  </div>
				  <p>If you have any questions, feel free to contact our support team for help.</p>
				</div>
				<div style='background-color: #f1f1f1; padding: 15px; text-align: center; font-size: 12px; color: #777777;'>
				  &copy; 2025 Talents Jobs | All rights reserved
				</div>
			  </div>
			</body>
			</html>";

		} else {
			// Active or inactive
			$update['rejection_reason'] = NULL;
			$update['admin_suggestion'] = NULL;

			if ($data['status'] === 'active') {
				$notifMsg = "Your employer profile has been approved and is now active.";
				
				$emailSubject = "Talents Jobs - Your Employer Profile is Now Active!";
				$emailMessage = "
				<html>
				<body style='font-family: Arial, sans-serif; margin: 0; padding: 0; background-color: #f7f9fc;'>
				  <div style='max-width: 600px; margin: 20px auto; background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);'>
					<div style='background-color: #004aad; color: #ffffff; padding: 20px; text-align: center;'>
					  <h1 style='margin: 0; font-size: 24px;'>Welcome to Talents Jobs</h1>
					</div>
					<div style='padding: 30px 20px; color: #333333; font-size: 16px; line-height: 1.6;'>
					  <p>Dear Employer,</p>
					  <p>We're pleased to inform you that your employer profile has been <strong>approved</strong> and is now <strong>active</strong> on <strong>Talents Jobs</strong>.</p>
					  <p>As part of our commitment to support growing businesses, your account now has access to <strong>unlimited lifetime free job postings</strong>. You can start hiring the best talent without any charges.</p>
					  <p><strong>With your activated account, you can now:</strong></p>
					  <ul style='margin: 15px 0 20px 20px;'>
						<li>Post unlimited job openings for free</li>
						<li>Receive and review candidate applications</li>
						<li>Schedule interviews with shortlisted candidates</li>
						<li>Track application progress with real-time status</li>
						<li>Use your employer dashboard for complete job management</li>
					  </ul>
					  <p>Get started now and connect with top talent effortlessly!</p>
					  <div style='text-align: center; margin: 30px 0;'>
						<a href='" . base_url('employer/jobs/create') . "' 
						style='padding: 12px 25px; background-color: #004aad; color: #ffffff; text-decoration: none; border-radius: 5px; font-weight: bold;'>Post Your First Job Now</a>
					  </div>
					</div>
					<div style='background-color: #f1f1f1; padding: 15px; text-align: center; font-size: 12px; color: #777777;'>
					  &copy; 2025 Talents Jobs. All rights reserved.
					</div>
				  </div>
				</body>
				</html>";
			}
		}

		// Update employer data
		$this->AdminEmployer_model->update_employer(
			['employer_id' => $data['employer_id']],
			$update
		);

		// ✅ Job status logic based on employer status
		if ($data['status'] === 'rejected') {
			$this->AdminEmployer_model->set_employer_jobs_status($data['employer_id'], 'rejected');
		} elseif ($data['status'] === 'active') {
			$this->AdminEmployer_model->set_one_job_active($data['employer_id']);
		}

		// Send in-app notification
		if ($notifMsg) {
			$this->Notification_model->create([
				'user_id'    => $data['employer_id'],
				'type'       => 'employer',
				'message'    => $notifMsg,
				'link'       => 'employer/dashboard',
				'created_at' => date('Y-m-d H:i:s'),
			]);
		}

		// Send email (without logging & tracking)
		if ($emailMessage) {	
			SendEmailTo($employer['email'], $emailSubject, $emailMessage);
			//send_mailercloud_email($employer['email'], 'Employer', $emailSubject, $emailMessage);
		}

		 // After finishing all operations:
		echo json_encode([
			'status' => 'success',
			'csrf_token' => $this->security->get_csrf_hash()
		]);
	}
	
	public function employer_jobs($employer_id) {
		$this->load->model('AdminEmployer_model');
		$employer = $this->AdminEmployer_model->get_employer($employer_id);
		$data = [
			'title' => 'Employer Jobs List',
			'employer_id' => $employer_id,
			'employer' => $employer,
			'industries' => $this->AdminEmployer_model->get_all_industries($employer_id),
			//'functions' => $this->AdminEmployer_model->get_all_functions($employer_id),
			'statuses' => $this->AdminEmployer_model->get_job_statuses($employer_id),

			// 👇 Add these empty default values for now since DataTables handles them via AJAX
			'filters' => [
				'job_title' => '',
				'status' => '',
				'industry_id' => '',
				'functional_id' => '',
				'job_type' => '',
				'is_paid' => '',
				'created_from' => '',
				'created_to' => '',
			],
			'showing_start' => 0,
			'showing_end' => 0,
			'total_jobs' => 0,
		];
		$data['content'] = $this->load->view('admin/employers/employer_jobs_full', $data, true);
		$this->load->view('templates/master', $data);
	}

	public function employer_jobs_ajax($employer_id) {
		$start  = intval($this->input->get('start'));
		$length = intval($this->input->get('length'));
		$search = $this->input->get('search')['value'] ?? '';

		$order_column_index = $this->input->get('order')[0]['column'] ?? 0;
		$order_column = $this->input->get('columns')[$order_column_index]['data'] ?? 'created_at';
		$order_dir = $this->input->get('order')[0]['dir'] ?? 'desc';

		$filters = [
			'job_title'   => $this->input->get('job_title', true),
			'status'      => $this->input->get('status', true),
			'industry_id' => $this->input->get('industry_id', true),
			'job_type'    => $this->input->get('job_type', true),
			'is_paid'     => $this->input->get('is_paid', true),
			'created_from'=> $this->input->get('created_from', true),
			'created_to'  => $this->input->get('created_to', true),
		];

		$data      = $this->AdminEmployer_model->get_employer_jobs_dt($employer_id, $start, $length, $search, $order_column, $order_dir, $filters);
		$total     = $this->AdminEmployer_model->count_employer_jobs($employer_id);
		$filtered  = $this->AdminEmployer_model->count_employer_jobs_filtered($employer_id, $search, $filters);

		echo json_encode([
			"draw" => intval($this->input->get('draw')),
			"recordsTotal" => $total,
			"recordsFiltered" => $filtered,
			"data" => $data
		]);
	}
   
	public function ajax_view_job($job_id) {
		//$this->load->model('AdminEmployer_model');
		$job = $this->AdminEmployer_model->get_job($job_id);

		if (!$job) {
			echo '<div class="text-red-500 text-center">Job not found.</div>';
			return;
		}

		// Utility function for fallback
		$format = function ($value, $default = '<span class="text-gray-400 italic">Not provided</span>') {
			return !empty($value) ? html_escape($value) : $default;
		};

		// Fallback for salary
		$salary = (float)$job['min_salary'] > 0 || (float)$job['max_salary'] > 0
			? '₹' . number_format($job['min_salary']) . ' - ₹' . number_format($job['max_salary']) . ' (' . ucfirst($job['salary_type']) . ')'
			: '<span class="text-gray-400 italic">Not provided</span>';

		// Skills / Locations
		$skills = !empty($job['skills']) ? implode(', ', array_map('html_escape', $job['skills'])) : '<span class="text-gray-400 italic">Not provided</span>';
		$cities = !empty($job['cities']) ? implode(', ', array_map('html_escape', $job['cities'])) : '<span class="text-gray-400 italic">Not provided</span>';

		// Apply Link
		$applyLink = ($job['enable_apply_link'] === 'yes' && !empty($job['apply_web_link']))
			? '<a href="' . html_escape($job['apply_web_link']) . '" target="_blank" class="text-blue-600 underline">Click here</a>'
			: '<span class="text-gray-400 italic">Not provided</span>';

		// Rejection reason (optional)
		$rejection = ($job['status'] === 'rejected' && !empty($job['rejection_reason']))
			? '<p class="text-red-600"><strong>Rejection Reason:</strong><br>' . nl2br(html_escape($job['rejection_reason'])) . '</p>'
			: '';

		// Build the HTML
		echo '
		<div class="grid md:grid-cols-2 gap-6 text-sm text-gray-800">

		  <!-- Left: Job Info -->
		  <div class="space-y-3">
			<h3 class="text-base font-semibold text-blue-600 border-b pb-1">Job Details</h3>

			<p><span class="font-medium text-gray-700">Title:</span> ' . $format($job['job_title']) . '</p>
			<p><span class="font-medium text-gray-700">Industry:</span> ' . $format($job['industry_name']) . '</p>
			
			<p><span class="font-medium text-gray-700">Experience:</span> ' . 
				((int)$job['min_experience'] > 0 || (int)$job['max_experience'] > 0 
					? $job['min_experience'] . ' - ' . $job['max_experience'] . ' yrs'
					: '<span class="text-gray-400 italic">Not provided</span>') . 
			'</p>
			<p><span class="font-medium text-gray-700">Salary:</span> ' . $salary . '</p>
			<p><span class="font-medium text-gray-700">Job Type:</span> ' . $format($job['job_type']) . '</p>
			<p><span class="font-medium text-gray-700">Education:</span> ' . $format($job['education']) . '</p>
			<p><span class="font-medium text-gray-700">Tags:</span> ' . $format($job['job_tag']) . '</p>
			<p><span class="font-medium text-gray-700">Skills:</span> ' . $skills . '</p>
			<p><span class="font-medium text-gray-700">Locations:</span> ' . $cities . '</p>
			<p><span class="font-medium text-gray-700">Apply Link:</span> ' . $applyLink . '</p>
			<p><span class="font-medium text-gray-700">Status:</span> ' . ucfirst($job['status']) . '</p>
			' . $rejection . '
			<p><span class="font-medium text-gray-700">Posted On:</span> ' . date('d M Y, h:i A', strtotime($job['created_at'])) . '</p>
			<p><span class="font-medium text-gray-700">Deadline:</span> ' . 
				(!empty($job['deadline_date']) ? date('d M Y', strtotime($job['deadline_date'])) : '<span class="text-gray-400 italic">Not provided</span>') . 
			'</p>
		  </div>

		  <!-- Right: Employer Info -->
		  <div class="space-y-3">
			<h3 class="text-base font-semibold text-blue-600 border-b pb-1">Employer Info</h3>

			<p><span class="font-medium text-gray-700">Company:</span> ' . $format($job['company_name']) . '</p>
			<p><span class="font-medium text-gray-700">Email:</span> ' . $format($job['employer_email']) . '</p>
			<p><span class="font-medium text-gray-700">Employer Status:</span> ' . ucfirst($job['employer_status']) . '</p>
			<p><span class="font-medium text-gray-700">Posted By:</span> ' . $format($job['posted_by']) . '</p>
			<p><span class="font-medium text-gray-700">Last Updated:</span> ' . date('d M Y, h:i A', strtotime($job['updated_at'])) . '</p>

			<div class="mt-4">
			  <h4 class="text-sm font-semibold text-gray-800 mb-1">Description:</h4>
			  <div class="p-3 bg-gray-50 border rounded text-gray-700 leading-relaxed text-sm max-h-64 overflow-y-auto">
				' . (!empty($job['job_description']) ? $job['job_description'] : '<span class="text-gray-400 italic">Not provided</span>') . '
			  </div>
			</div>
		  </div>

		</div>';
	}
	
	public function send_bulk_email() {
		$data = $this->input->post();

		$ids     = $data['ids'] ?? [];
		$subject = trim($data['subject'] ?? '');
		$message = trim($data['message'] ?? '');
		$context = trim($data['context'] ?? 'bulk_custom');

		if (empty($ids) || !$subject || !$message || !$context) {
			echo json_encode(['status' => 'error', 'message' => 'Missing recipients, subject, message, or context.']);
			return;
		}

		$this->load->model('Notification_model');
		
		$role         = 'employer';
		$successCount = 0;

		foreach ($ids as $employer_id) {
			$employer = $this->AdminEmployer_model->get_employer($employer_id);
			if (!$employer || empty($employer['email'])) {
				continue;
			}

			$email = $employer['email'];

			// CTA button (optional based on context)
			$ctaButton = '';
			if ($context === 'job_post') {
				$ctaText = 'Post a Job Now';
			} elseif ($context === 'profile_reminder') {
				$ctaText = 'Complete Your Profile';
			} else {
				$ctaText = '';
			}

			if (!empty($ctaText)) {
				$ctaURL = base_url('recruit/client-registration-form');

				$ctaButton = '
				<table role="presentation" border="0" cellpadding="0" cellspacing="0" align="center" style="margin: 30px auto;">
				  <tr>
					<td align="center" bgcolor="#004aad" style="border-radius: 5px;">
					  <a href="' . $ctaURL . '" target="_blank" rel="noopener noreferrer" style="
						font-size: 14px;
						font-family: Arial, sans-serif;
						color: #ffffff;
						text-decoration: none;
						padding: 12px 24px;
						display: inline-block;
						font-weight: bold;
						border-radius: 5px;
					  ">' . htmlspecialchars($ctaText) . '</a>
					</td>
				  </tr>
				</table>';
			}

			// Full email body
			$emailBody = '
			<!DOCTYPE html>
			<html>
			<head>
			  <meta charset="UTF-8">
			  <title>' . htmlspecialchars($subject) . '</title>
			</head>
			<body style="margin:0; padding:0; background-color:#f9fafb;">
			  <table cellpadding="0" cellspacing="0" border="0" width="100%" style="background-color:#f9fafb; font-family:Segoe UI, Roboto, Helvetica, Arial, sans-serif;">
				<tr>
				  <td align="center">
					<table cellpadding="0" cellspacing="0" border="0" width="100%" style="max-width:600px; margin: 20px auto; background-color:#ffffff; border-radius:10px; overflow:hidden; box-shadow:0 2px 12px rgba(0,0,0,0.05);">
					  <!-- Header -->
					  <tr>
						<td align="center" style="background-color:#004aad; padding:20px;">
						  <h1 style="margin:0; font-size:24px; color:#ffffff;">Talents Jobs</h1>
						</td>
					  </tr>

					  <!-- Body Content -->
					  <tr>
						<td style="padding:30px 20px; font-size:15px; color:#333333; line-height:1.6;">
						  <p style="margin-top:0">Dear Sir/Madam,</p>
						  ' . $message . '
						  ' . $ctaButton . '
						  <p style="margin-top: 30px;">Best Regards,<br><strong>Talents Jobs Team</strong></p>
						</td>
					  </tr>

					  <!-- Footer -->
					  <tr>
						<td align="center" style="background-color:#f1f1f1; padding:15px; font-size:12px; color:#888888;">
						  &copy; ' . date('Y') . ' Talents Jobs. All rights reserved.
						</td>
					  </tr>
					</table>
				  </td>
				</tr>
			  </table>
			</body>
			</html>';

			// Send email
			$status = SendEmailTo($email, $subject, $emailBody);
			//$status = send_mailercloud_email($email, 'Employer', $subject, $emailBody);

			if ($status) {
				$successCount++;

				// In-app notification
				$this->Notification_model->create([
					'user_id'    => $employer_id,
					'type'       => 'employer',
					'message'    => strip_tags($subject),
					'link'       => 'employer/dashboard',
					'created_at' => date('Y-m-d H:i:s'),
				]);
			}
		}

		echo json_encode([
			'status'  => 'success',
			'message' => "Emails successfully sent: {$successCount}"
		]);
	}


	public function bulk_soft_delete() {
		$ids = $this->input->post('ids');

		if (empty($ids) || !is_array($ids)) {
			echo json_encode([
				'status' => 'error',
				'message' => 'No employers selected.',
				'csrf_token' => $this->security->get_csrf_hash()
			]);
			return;
		}

		$success = $this->AdminEmployer_model->bulk_soft_delete($ids);

		echo json_encode([
			'status' => $success ? 'success' : 'error',
			'message' => $success ? 'Selected employers deleted.' : 'Deletion failed.',
			'csrf_token' => $this->security->get_csrf_hash()
		]);
	}

	
	
	
}