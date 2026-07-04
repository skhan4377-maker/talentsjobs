<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Unsubscribe extends CI_Controller {
    
    public function __construct() {
        parent::__construct();
        $this->load->database();
    }
   
	//job recommended model
	public function job_alerts($token) {
		// Get the candidate based on the unsubscribe token
		$candidate = $this->db->get_where('tb_candidate', ['unsubscribe_token' => $token])->row();

		if ($candidate) {
			// Update the candidate record to mark as unsubscribed
			$this->db->where('candidate_id', $candidate->candidate_id)
					 ->update('tb_candidate', ['unsubscribed' => 1]);

			$message = 'You have been successfully unsubscribed from job alerts.';
			$success = true;
		} else {
			// Invalid token
			$message = 'Invalid unsubscribe link.';
			$success = false;
		}

		// Direct HTML design output
		echo '
		<!DOCTYPE html>
		<html lang="en">
		<head>
			<meta charset="UTF-8">
			<meta name="viewport" content="width=device-width, initial-scale=1.0">
			<title>Unsubscribe - Talent Jobs</title>
			<style>
				body {
					font-family: Arial, sans-serif;
					background: #f8f9fa;
					margin: 0;
					padding: 0;
				}
				.container {
					max-width: 500px;
					margin: 80px auto;
					background: #fff;
					padding: 30px;
					border-radius: 12px;
					text-align: center;
					box-shadow: 0px 4px 10px rgba(0,0,0,0.1);
				}
				.icon {
					font-size: 60px;
					margin-bottom: 20px;
				}
				.success { color: #28a745; }
				.error { color: #dc3545; }
				h1 {
					font-size: 22px;
					margin-bottom: 10px;
				}
				p {
					color: #555;
					font-size: 15px;
				}
				.btn {
					display: inline-block;
					margin-top: 20px;
					padding: 12px 20px;
					background: #007bff;
					color: #fff;
					border-radius: 6px;
					text-decoration: none;
					transition: 0.3s;
				}
				.btn:hover {
					background: #0056b3;
				}
			</style>
		</head>
		<body>
			<div class="container">
				'.($success 
					? '<div class="icon success">✔</div><h1>Unsubscribed</h1><p>'.$message.'</p>' 
					: '<div class="icon error">✖</div><h1>Error</h1><p>'.$message.'</p>').'
				<a href="'.base_url().'" class="btn">Back to Home</a>
			</div>
		</body>
		</html>';
	}
	
	public function email_unsubscribe($token = null) {
        if (empty($token)) {
            show_error("Invalid unsubscribe request", 400);
        }
    
        // Decode token
        $data = $this->decrypt_unsubscribe_token($token);
        if (!$data || empty($data['email']) || empty($data['campaign_id'])) {
            show_error("Invalid or expired unsubscribe link", 400);
        }
    
        $email       = $data['email'];
        $campaign_id = $data['campaign_id'];
    
        // ✅ UPDATE tb_campaign_queue (not tb_email_queue)
        $this->db->where('campaign_id', $campaign_id)
                 ->where('email', $email)
                 ->update('tb_campaign_queue', ['status' => 'unsubscribed']);
    
        // Inline HTML response (unchanged)
        echo '<!doctype html>
        <html lang="en">
        <head>
          <meta charset="utf-8">
          <title>Unsubscribe | Talents Jobs</title>
          <meta name="viewport" content="width=device-width,initial-scale=1">
          <style>
            body { font-family: Arial, sans-serif; background:#f9fafb; text-align:center; padding:40px; color:#111827; }
            .box { background:#fff; padding:30px; border-radius:12px; max-width:480px; margin:0 auto; box-shadow:0 2px 6px rgba(0,0,0,0.1); }
            h1 { font-size:22px; color:#1e40af; margin:0; }
            p { font-size:15px; color:#374151; margin:10px 0; }
            a { display:inline-block; margin-top:15px; color:#2563eb; text-decoration:none; font-weight:600; }
          </style>
        </head>
        <body>
          <div class="box">
            <h1>You’ve been unsubscribed</h1>
            <p>The email <strong>' . htmlspecialchars($email) . '</strong> has been removed from our mailing list.</p>
            <p>You will no longer receive campaign emails from Talents Jobs.</p>
            <a href="' . base_url() . '">Return to Talents Jobs</a>
          </div>
        </body>
        </html>';
    }

	private function decrypt_unsubscribe_token($token) {
		// Convert URL-safe back to standard base64
		$token = strtr($token, '-_', '+/');
		$decoded = base64_decode($token);
		if (!$decoded) return null;

		$parts = explode('|', $decoded);
		if (count($parts) != 2) return null;

		return [
			'campaign_id' => $parts[0],
			'email'       => $parts[1]
		];
	}
	
}