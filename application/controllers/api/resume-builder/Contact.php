<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH . '/libraries/REST_Controller.php';

class Contact extends REST_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

	
	// application/controllers/api/Contact.php mein yeh method add karen
	public function info_get() {
		try {
			// Fetch contact information from tb_options
			$this->db->select('option_name, option_value');
			$this->db->from('tb_options');
			$this->db->like('option_name', 'contact_', 'after');
			$query = $this->db->get();
			
			$contact_data = array();
			foreach ($query->result() as $row) {
				$contact_data[$row->option_name] = $row->option_value;
			}
			
			// Get site name
			$this->db->select('option_value');
			$this->db->from('tb_options');
			$this->db->where('option_name', 'site_name');
			$site_query = $this->db->get();
			$site_name = $site_query->row() ? $site_query->row()->option_value : 'Talents Jobs';
			
			// Prepare response
			$response = array(
				'status' => true,
				'data' => array(
					'email' => isset($contact_data['contact_email']) ? $contact_data['contact_email'] : 'support@talentsjobs.in',
					'phone' => isset($contact_data['contact_phone_display']) ? $contact_data['contact_phone_display'] : '+91 1800-123-456',
					'phone_raw' => isset($contact_data['contact_phone']) ? $contact_data['contact_phone'] : '+911800123456',
					'whatsapp' => isset($contact_data['contact_whatsapp']) ? $contact_data['contact_whatsapp'] : 'https://wa.me/911800123456',
					'address' => isset($contact_data['contact_address']) ? $contact_data['contact_address'] : 'Talents Jobs, New Delhi',
					'escalation_email' => isset($contact_data['contact_escalation']) ? $contact_data['contact_escalation'] : 'escalate@talentsjobs.in',
					'site_name' => $site_name,
					'support_hours' => array(
						'mon_fri' => isset($contact_data['contact_hours_mon_fri']) ? $contact_data['contact_hours_mon_fri'] : '9:00 AM - 6:00 PM',
						'saturday' => isset($contact_data['contact_hours_sat']) ? $contact_data['contact_hours_sat'] : '10:00 AM - 2:00 PM',
						'sunday' => isset($contact_data['contact_hours_sun']) ? $contact_data['contact_hours_sun'] : 'Emergency Only'
					)
				)
			);
			
			$this->response($response, REST_Controller::HTTP_OK);
			
		} catch (Exception $e) {
			$this->response(array(
				'status' => false,
				'message' => 'Failed to fetch contact information'
			), REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
		}
	}
	
    /**
     * Handle contact form submission
     * POST: /api/contact/submit
     */
    public function submit_post() {
        try {
            // Get input data - handle both JSON and FormData
            $input = [];
            
            if (!empty($_FILES)) {
                // FormData with files
                $input = $this->post();
            } else {
                // JSON data
                $input = json_decode(file_get_contents('php://input'), true);
                if (!$input) {
                    $input = $this->post();
                }
            }
            
            // Validate required fields
            $required_fields = ['name', 'email', 'subject', 'description', 'category'];
            $missing_fields = [];
            
            foreach ($required_fields as $field) {
                if (empty($input[$field])) {
                    $missing_fields[] = $field;
                }
            }
            
            if (!empty($missing_fields)) {
                $this->response([
                    'status' => false,
                    'message' => 'Required fields missing',
                    'missing_fields' => $missing_fields
                ], REST_Controller::HTTP_BAD_REQUEST);
                return;
            }
            
            // Validate email
            if (!filter_var($input['email'], FILTER_VALIDATE_EMAIL)) {
                $this->response([
                    'status' => false,
                    'message' => 'Invalid email format'
                ], REST_Controller::HTTP_BAD_REQUEST);
                return;
            }
            
            // Generate ticket ID
            $ticket_id = 'TKT-' . date('Ymd') . '-' . strtoupper(substr(md5(uniqid()), 0, 6));
            
            // Handle file uploads if any
            $uploaded_files = [];
            if (!empty($_FILES)) {
                $uploaded_files = $this->handle_file_uploads($ticket_id);
            } else if (isset($input['attachments']) && is_array($input['attachments'])) {
                // If sending file names in JSON (without actual files)
                $uploaded_files = $input['attachments'];
            }
            
            // Prepare complaint data
            $complaint_data = [
                'ticket_id' => $ticket_id,
                'name' => $this->db->escape_str($input['name']),
                'email' => $this->db->escape_str($input['email']),
                'phone' => isset($input['phone']) ? $this->db->escape_str($input['phone']) : '',
                'order_id' => isset($input['orderId']) ? $this->db->escape_str($input['orderId']) : '',
                'subject' => $this->db->escape_str($input['subject']),
                'description' => $this->db->escape_str($input['description']),
                'category' => $this->db->escape_str($input['category']),
                'urgent' => isset($input['urgent']) && $input['urgent'] ? 1 : 0,
                'status' => 'open',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];
            
            // Save to database
            $complaint_id = $this->save_complaint($complaint_data, $uploaded_files);
            
            // Send emails
            $this->send_emails($complaint_data, $uploaded_files);
            
            // Prepare response
            $response = [
                'status' => true,
                'message' => 'Complaint submitted successfully',
                'ticket_id' => $ticket_id,
                'data' => [
                    'ticket_details' => [
                        'id' => $ticket_id,
                        'category' => $complaint_data['category'],
                        'subject' => $complaint_data['subject'],
                        'status' => 'open',
                        'created_at' => $complaint_data['created_at']
                    ],
                    'estimated_response_time' => $complaint_data['urgent'] ? '4 hours' : '24 hours',
                    'files_uploaded' => count($uploaded_files)
                ]
            ];
            
            $this->response($response, REST_Controller::HTTP_OK);
            
        } catch (Exception $e) {
            $this->response([
                'status' => false,
                'message' => 'Internal server error'
            ], REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Save complaint to database
     */
    private function save_complaint($data, $attachments = []) {
        // Insert into tb_contact_complaints
        $sql = "INSERT INTO tb_contact_complaints 
                (ticket_id, name, email, phone, order_id, subject, description, category, urgent, status, created_at, updated_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $this->db->query($sql, [
            $data['ticket_id'],
            $data['name'],
            $data['email'],
            $data['phone'],
            $data['order_id'],
            $data['subject'],
            $data['description'],
            $data['category'],
            $data['urgent'],
            $data['status'],
            $data['created_at'],
            $data['updated_at']
        ]);
        
        $complaint_id = $this->db->insert_id();
        
        // Save attachments if any
        if (!empty($attachments) && $complaint_id) {
            foreach ($attachments as $file_name) {
                $att_sql = "INSERT INTO tb_complaint_attachments 
                           (complaint_id, file_name, uploaded_at)
                           VALUES (?, ?, ?)";
                $this->db->query($att_sql, [
                    $complaint_id,
                    $this->db->escape_str($file_name),
                    date('Y-m-d H:i:s')
                ]);
            }
        }
        
        return $complaint_id;
    }

    /**
     * Handle file uploads (simple version)
     */
    private function handle_file_uploads($ticket_id) {
        $uploaded_files = [];
        
        // Check if upload directory exists
        $upload_path = FCPATH . 'uploads/contact_attachments/';
        if (!is_dir($upload_path)) {
            mkdir($upload_path, 0777, true);
        }
        
        // Handle files
        foreach ($_FILES as $key => $file) {
            // Check if file array is multiple
            if (is_array($file['name'])) {
                for ($i = 0; $i < count($file['name']); $i++) {
                    if ($file['error'][$i] === UPLOAD_ERR_OK) {
                        $file_name = $this->upload_single_file($file, $i, $ticket_id, $upload_path);
                        if ($file_name) {
                            $uploaded_files[] = $file_name;
                        }
                    }
                }
            } else {
                // Single file
                if ($file['error'] === UPLOAD_ERR_OK) {
                    $file_name = $this->upload_single_file($file, null, $ticket_id, $upload_path);
                    if ($file_name) {
                        $uploaded_files[] = $file_name;
                    }
                }
            }
        }
        
        return $uploaded_files;
    }
    
    /**
     * Upload single file
     */
    private function upload_single_file($file, $index = null, $ticket_id, $upload_path) {
        // Get file details
        if ($index !== null) {
            $original_name = $file['name'][$index];
            $tmp_name = $file['tmp_name'][$index];
            $file_size = $file['size'][$index];
            $file_type = $file['type'][$index];
        } else {
            $original_name = $file['name'];
            $tmp_name = $file['tmp_name'];
            $file_size = $file['size'];
            $file_type = $file['type'];
        }
        
        // Validate file type
        $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'application/pdf'];
        if (!in_array($file_type, $allowed_types)) {
            return false;
        }
        
        // Validate file size (5MB max)
        if ($file_size > 5 * 1024 * 1024) {
            return false;
        }
        
        // Generate safe file name
        $file_extension = pathinfo($original_name, PATHINFO_EXTENSION);
        $safe_file_name = $ticket_id . '_' . time() . '_' . uniqid() . '.' . $file_extension;
        $destination = $upload_path . $safe_file_name;
        
        // Move file
        if (move_uploaded_file($tmp_name, $destination)) {
            return $safe_file_name;
        }
        
        return false;
    }

    /**
     * Send emails to candidate and admin
     */
    private function send_emails($complaint_data, $attachments = []) {
        // 1. Send email to candidate (user)
        $candidate_subject = 'Complaint Registered - Ticket #' . $complaint_data['ticket_id'];
        $candidate_message = '
        <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">
            <h2 style="color: #4f46e5;">Complaint Registered Successfully</h2>
            <p>Hello ' . htmlspecialchars($complaint_data['name']) . ',</p>
            <p>Your complaint has been registered with us. Here are the details:</p>
            
            <div style="background: #f9fafb; padding: 15px; border-radius: 8px; margin: 15px 0;">
                <p><strong>Ticket ID:</strong> ' . $complaint_data['ticket_id'] . '</p>
                <p><strong>Category:</strong> ' . $complaint_data['category'] . '</p>
                <p><strong>Subject:</strong> ' . htmlspecialchars($complaint_data['subject']) . '</p>
                <p><strong>Status:</strong> Open</p>
                <p><strong>Submitted:</strong> ' . $complaint_data['created_at'] . '</p>
            </div>
            
            <p><strong>Response Time:</strong> ' . ($complaint_data['urgent'] ? 'Within 4 hours (Urgent)' : 'Within 24 hours') . '</p>
            
            <p>Our support team will review your complaint and get back to you soon.</p>
            
            <p>Thank you,<br>ResumeBuilder Support Team</p>
        </div>';
        
        // Send email to candidate
        SendEmailTo($complaint_data['email'], $candidate_subject, $candidate_message);
        
        // 2. Send notification email to admin/support
        $admin_subject = 'New Complaint Received - ' . $complaint_data['ticket_id'];
        $admin_message = '
        <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">
            <h2 style="color: #dc2626;">New Complaint Received</h2>
            <p>A new complaint has been submitted through the contact form.</p>
            
            <div style="background: #fef2f2; padding: 15px; border-radius: 8px; margin: 15px 0;">
                <p><strong>Ticket ID:</strong> ' . $complaint_data['ticket_id'] . '</p>
                <p><strong>Name:</strong> ' . htmlspecialchars($complaint_data['name']) . '</p>
                <p><strong>Email:</strong> ' . $complaint_data['email'] . '</p>
                <p><strong>Phone:</strong> ' . ($complaint_data['phone'] ?: 'Not provided') . '</p>
                <p><strong>Category:</strong> ' . $complaint_data['category'] . '</p>
                <p><strong>Subject:</strong> ' . htmlspecialchars($complaint_data['subject']) . '</p>
                <p><strong>Priority:</strong> ' . ($complaint_data['urgent'] ? 'URGENT' : 'Normal') . '</p>
                <p><strong>Time:</strong> ' . $complaint_data['created_at'] . '</p>
            </div>
            
            <p><strong>Description:</strong><br>' . nl2br(htmlspecialchars($complaint_data['description'])) . '</p>
            
            <p>Attachments: ' . (count($attachments) > 0 ? count($attachments) . ' file(s)' : 'None') . '</p>
            
            <p>Please review and respond within ' . ($complaint_data['urgent'] ? '4 hours' : '24 hours') . '.</p>
        </div>';
        
        // Send email to admin
        SendEmailTo('skhan4377@gmail.com', $admin_subject, $admin_message);
    }
}