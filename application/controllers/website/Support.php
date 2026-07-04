<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Support extends CI_Controller {

    public function __construct() {
        parent::__construct();
        // Load necessary models if any
        $this->load->config('recaptcha');
    }

    private function verify_recaptcha($recaptcha_response) {
		$secret_key = $this->config->item('recaptcha_secret_key');
		$verify_url = $this->config->item('recaptcha_verify_url');

		$curl = curl_init();

		curl_setopt($curl, CURLOPT_URL, $verify_url);
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query([
			'secret'   => $secret_key,
			'response' => $recaptcha_response,
			'remoteip' => $this->input->ip_address()
		]));
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true); // strict SSL
		curl_setopt($curl, CURLOPT_TIMEOUT, 10);

		$response = curl_exec($curl);

		if (curl_errno($curl)) {
			log_message('error', 'reCAPTCHA cURL Error: ' . curl_error($curl));
			curl_close($curl);
			return false;
		}

		curl_close($curl);

		$result = json_decode($response);
		return isset($result->success) && $result->success;
	}

    // Help Center page
    public function help_center() {
        $data['title'] = 'Help Center';
        $data['description'] = 'Visit our Help Center for common questions and troubleshooting.';        
        $this->load->view('particles/header', $data);
        $this->load->view('particles/nav');
        $this->load->view('website/support/help_center');
        $this->load->view('particles/footer');
    }

    // Contact Us page
    public function contact_us() {
        $data['title'] = 'Contact Us';
        $data['description'] = 'Get in touch with us for inquiries or support. We are here to help you.';
        $data['recaptcha_site_key'] = $this->config->item('recaptcha_site_key');
        
        $this->load->view('particles/header', $data);
        $this->load->view('particles/nav');
        $this->load->view('website/support/contact_us');
        $this->load->view('particles/footer');
    }

    // Privacy Policy page
    public function privacy_policy() {
        $data['title'] = 'Privacy Policy';
        $data['description'] = 'Read our privacy policy to understand how we handle your personal information.';
        
        $this->load->view('particles/header', $data);
        $this->load->view('particles/nav');
        $this->load->view('website/support/privacy_policy');
        $this->load->view('particles/footer');
    }
	
	public function about_us() {
		$data['title'] = 'About Us';
		$data['description'] = 'Learn more about Talents Jobs and our mission.';
		$this->load->view('particles/header', $data);
		$this->load->view('particles/nav');
		$this->load->view('website/support/about_us');
		$this->load->view('particles/footer');
	}

    // Terms of Service page
    public function terms_of_service() {
        $data['title'] = 'Terms of Service';
        $data['description'] = 'Review the terms of service for using our website and services.';
        
        $this->load->view('particles/header', $data);
        $this->load->view('particles/nav');
        $this->load->view('website/support/terms_of_service');
        $this->load->view('particles/footer');
    }
	
	/**
	 * Secure contact form submission (CodeIgniter 3)
	 */
	public function submit_contact_form() {
		// Remove the die statement that was blocking execution
		// die;

		// Load libs, helpers, models
		$this->load->library(['upload', 'form_validation', 'session']);
		$this->load->helper(['security', 'file', 'url']);
		$this->load->model('Notification_model');

		// ------------------ RATE LIMIT (basic) ------------------
		// Simple per-session throttle: max 5 submissions per 5 minutes
		$ip = $this->input->ip_address();
		$now = time();
		$limit_window = 300; // seconds
		$limit_max = 5;
		$submits = $this->session->userdata('contact_submits') ?: [];
		// purge old
		$submits = array_values(array_filter($submits, function ($t) use ($now, $limit_window) {
			return ($now - $t) <= $limit_window;
		}));
		if (count($submits) >= $limit_max) {
			echo json_encode(['status' => 'error', 'message' => 'Too many submissions. Please try again later.']);
			return;
		}

		// ------------------ reCAPTCHA VERIFICATION ------------------
		$recaptcha_response = $this->input->post('recaptcha_response', true);
		if (!$this->verify_recaptcha($recaptcha_response)) {
			echo json_encode(['status' => 'error', 'message' => 'reCAPTCHA verification failed. Please try again.']);
			return;
		}

		// ------------------ VALIDATION RULES ------------------
		$this->form_validation->set_rules('name', 'Full Name', 'required|trim|min_length[2]|max_length[100]|regex_match[/^[a-zA-Z\s\.\-]+$/]');
		$this->form_validation->set_rules('email', 'Email', 'required|trim|valid_email|max_length[100]');
		$this->form_validation->set_rules('role', 'Role', 'required|in_list[candidate,employer]');
		$this->form_validation->set_rules('subject', 'Subject', 'required|trim|max_length[150]|xss_clean');
		// allow basic punctuation in message but block SQL-control chars; we'll also do further detection
		$this->form_validation->set_rules('message', 'Message', 'required|trim|max_length[2000]|xss_clean');
		$this->form_validation->set_rules('recaptcha_response', 'reCAPTCHA', 'required');

		if (!$this->form_validation->run()) {
			echo json_encode(['status' => 'error', 'message' => validation_errors('<div>', '</div>')]);
			return;
		}

		// ------------------ CLEAN INPUT ------------------
		// Note: second param true in input->post ensures basic XSS cleaning, but we call xss_clean explicitly too
		$name_raw    = $this->input->post('name', true);
		$email_raw   = $this->input->post('email', true);
		$role_raw    = $this->input->post('role', true);
		$subject_raw = $this->input->post('subject', true);
		$message_raw = $this->input->post('message', true);

		// normalize & hard-clean
		$name    = $this->security->xss_clean(strip_tags($name_raw));
		$email   = $this->security->xss_clean($email_raw);
		$role    = strtolower($this->security->xss_clean($role_raw));
		$subject = $this->security->xss_clean(strip_tags($subject_raw));
		$message = $this->security->xss_clean($message_raw);

		// ------------------ SUSPICIOUS PATTERN CHECK ------------------
		// block common SQLi / time-based injection patterns and some control characters
		$suspicious_patterns = [
			'/\b(sleep|pg_sleep|dbms_pipe|waitfor|benchmark|sleep\(|pg_sleep\(|waitfor delay)\b/i',
			'/\b(union|select|insert|update|delete|drop|alter|truncate|load_file|outfile)\b/i',
			'/(--|;|\/\*|\*\/|\'\s+or\s+|"\s+or\s+)/i' // catch some obvious injection punctuation combos
		];

		foreach ($suspicious_patterns as $pat) {
			if (preg_match($pat, $name) || preg_match($pat, $subject) || preg_match($pat, $message)) {			
				echo json_encode([
					'status'  => 'error',
					'message' => 'Your message contains disallowed content.',
					'csrf_token' => $this->security->get_csrf_hash()
				]);
				return;
			}
		}

		// ------------------ FILE UPLOAD (if provided) ------------------
		$file_path = '';
		if (!empty($_FILES['attachment']['name'])) {
			// upload config
			$upload_dir = FCPATH . 'uploads/support/';
			if (!is_dir($upload_dir)) {
				mkdir($upload_dir, 0755, true);
			}

			$config = [
				'upload_path'   => $upload_dir,
				'allowed_types' => 'jpg|jpeg|png|pdf',
				'max_size'      => 2048, // KB -> 2 MB
				'encrypt_name'  => true,
				'max_filename'  => 200
			];

			$this->upload->initialize($config);

			if (! $this->upload->do_upload('attachment')) {
				// sanitize error message and return
				$err = strip_tags($this->upload->display_errors());
				echo json_encode(['status' => 'error', 'message' => $err]);
				return;
			}

			$fileData = $this->upload->data();
			$fullpath = $fileData['full_path'];

			// Additional safety checks: MIME type + fileinfo
			$finfo_mime = get_mime_by_extension($fileData['file_ext']);
			// Using PHP finfo for more reliable mime detection
			$finfo = finfo_open(FILEINFO_MIME_TYPE);
			$detected_mime = finfo_file($finfo, $fullpath);
			finfo_close($finfo);

			$allowed_mimes = ['image/jpeg', 'image/png', 'application/pdf'];
			if (! in_array($detected_mime, $allowed_mimes, true)) {
				// delete the uploaded file and reject
				@unlink($fullpath);
				log_message('error', "Upload rejected due to MIME mismatch ({$detected_mime}) from IP {$ip}");
				echo json_encode(['status' => 'error', 'message' => 'Uploaded file type is not allowed.']);
				return;
			}

			// Optionally: run antivirus/clamd scan here (recommended on production)
			// e.g. exec("clamscan " . escapeshellarg($fullpath), $out, $rc);

			// relative path to save in DB
			$file_path = 'uploads/support/' . $fileData['file_name'];
		}

		// ------------------ SAVE TO DB (Query Builder with bound params) ------------------
		$data = [
			'name'         => $name,
			'email'        => $email,
			'role'         => $role,
			'subject'      => $subject,
			'message'      => $message,
			'attachment'   => $file_path,
			'ip_address'   => $ip,
			'submitted_at' => date('Y-m-d H:i:s')
		];

		// Insert using CI Query Builder - binds values and prevents SQL injection
		$inserted = $this->db->insert('support_contacts', $data);
		if (! $inserted) {
			log_message('error', 'Failed to insert support_contacts: ' . $this->db->last_query());
			echo json_encode(['status' => 'error', 'message' => 'Could not save your message. Please try later.']);
			return;
		}

		// Update rate limit only on successful submission
		$submits[] = $now;
		$this->session->set_userdata('contact_submits', $submits);

		// ------------------ ADMIN NOTIFICATION ------------------
		$admin_message = "New contact request from {$name} (" . ucfirst($role) . ") - {$subject}";
		$this->Notification_model->create([
			'user_id'    => 1,
			'type'       => 'admin',
			'message'    => $admin_message,
			'link'       => 'admin/support/messages',
			'created_at' => date('Y-m-d H:i:s')
		]);

		// ------------------ EMAIL CONFIRMATION ------------------
		$emailBody = "
			<p>Hi " . htmlspecialchars($name) . ",</p>
			<p>Thank you for contacting <strong>Talents Jobs</strong>. We've received your message with the subject: <strong>" . htmlspecialchars($subject) . "</strong>.</p>
			<p>Our support team will review your message and respond as soon as possible.</p>
			<p><strong>Your Message:</strong><br>" . nl2br(htmlspecialchars($message)) . "</p>
			<p>Best Regards,<br>Talents Jobs Support Team</p>
		";

		// Use your existing SendEmailTo helper (ensure SendEmailTo is secure)
		SendEmailTo($email, "We've received your support query", $emailBody);

		// ------------------ SUCCESS ------------------
		echo json_encode([
			'status'  => 'success',
			'message' => 'Your message has been submitted and emailed to support.',
			'csrf_token' => $this->security->get_csrf_hash()
		]);
		return;

	}
}
?>