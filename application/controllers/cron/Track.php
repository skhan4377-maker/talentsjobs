<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Track extends CI_Controller {

      // -----------------------------------------------------------------
    // Job Alert Email Open Tracking (1x1 pixel)
    // -----------------------------------------------------------------
    public function open()
    {
        $candidate_id = $this->input->get('candidate_id');

        if (!$candidate_id) {
            $this->_outputGif();
            return;
        }

        // हाल ही में इस candidate को भेजे गए 'sent' queue आइटम को ढूँढें
        $queue = $this->db
            ->select('id, candidate_id')
            ->from('tb_job_recommendation_queue')
            ->where('candidate_id', $candidate_id)
            ->where('status', 'sent')
            ->order_by('processed_at', 'DESC')
            ->limit(1)
            ->get()
            ->row();

        if ($queue) {
            $this->db->insert('tb_job_recommendation_tracking', [
                'queue_id'     => $queue->id,
                'candidate_id' => $candidate_id,
                'event_type'   => 'open',
                'ip_address'   => $this->input->ip_address(),
                'user_agent'   => $this->input->user_agent(),
                'created_at'   => date('Y-m-d H:i:s')
            ]);
        }

        $this->_outputGif();
    }

    // -----------------------------------------------------------------
    // Job Alert Email Click Tracking (redirect)
    // -----------------------------------------------------------------
    public function click()
    {
        $candidate_id = $this->input->get('candidate_id');
        $job_id       = $this->input->get('job_id');
        $redirect     = $this->input->get('redirect');

        if (!$candidate_id || !$job_id) {
            redirect($redirect ?: base_url());
            return;
        }

        // सबसे हालिया 'sent' queue आइटम निकालें
        $queue = $this->db
            ->select('id, candidate_id')
            ->from('tb_job_recommendation_queue')
            ->where('candidate_id', $candidate_id)
            ->where('status', 'sent')
            ->order_by('processed_at', 'DESC')
            ->limit(1)
            ->get()
            ->row();

        if ($queue) {
            $this->db->insert('tb_job_recommendation_tracking', [
                'queue_id'     => $queue->id,
                'candidate_id' => $candidate_id,
                'event_type'   => 'click',
                'job_id'       => $job_id,
                'redirect_url' => $redirect ? urldecode($redirect) : null,
                'ip_address'   => $this->input->ip_address(),
                'user_agent'   => $this->input->user_agent(),
                'created_at'   => date('Y-m-d H:i:s')
            ]);
        }

        redirect($redirect ? urldecode($redirect) : base_url());
    }
	
     // -----------------------------------------------------------------
    // Campaign Email Open Tracking (1x1 pixel)
    // -----------------------------------------------------------------
    public function campaign_open($token = null) {
        if (!$token) {
            $this->_outputGif();
            return;
        }

        // Find queue record by tracking_token
        $queue = $this->db->select('id')
                          ->where('tracking_token', $token)
                          ->get('tb_campaign_queue')
                          ->row();

        if ($queue) {
            $this->db->insert('tb_campaign_tracking', [
                'queue_id'    => $queue->id,
                'event_type'  => 'open',
                'ip_address'  => $this->input->ip_address(),
                'user_agent'  => $this->input->user_agent(),
                'created_at'  => date('Y-m-d H:i:s')
            ]);
        }

        $this->_outputGif();
    }

    // -----------------------------------------------------------------
    // Campaign Email Click Tracking (redirect)
    // -----------------------------------------------------------------
    public function campaign_click($token = null)
    {
        $url = $this->input->get('url', true);
        if (!$token || !$url) {
            show_error('Invalid tracking link', 400);
        }

        $queue = $this->db->select('id')
                          ->where('tracking_token', $token)
                          ->get('tb_campaign_queue')
                          ->row();

        if ($queue) {
            $this->db->insert('tb_campaign_tracking', [
                'queue_id'    => $queue->id,
                'event_type'  => 'click',
                'link_url'    => urldecode($url),
                'ip_address'  => $this->input->ip_address(),
                'user_agent'  => $this->input->user_agent(),
                'created_at'  => date('Y-m-d H:i:s')
            ]);
        }

        redirect(urldecode($url));
    }

  
    
    /**
     * Handle push notification click tracking
     * URL: /track/push_click/{token}?redirect=...
     */
    public function push_click($token = null) {
		$redirect = $this->input->get('redirect', true);
		if (!$token || !$redirect) {
			show_error('Invalid tracking link', 400);
		}

		// Find queue record by tracking_token
		$queue = $this->db
			->select('id')
			->where('tracking_token', $token)
			->get('tb_push_notification_queue')
			->row();

		if ($queue) {
			// Log the click event
			$this->db->insert('tb_push_tracking', [
				'queue_id'   => $queue->id,
				'event_type' => 'click',
				'link_url'   => urldecode($redirect),
				'ip_address' => $this->input->ip_address(),
				'user_agent' => $this->input->user_agent(),
				'created_at' => date('Y-m-d H:i:s')
			]);

			log_message('info', "Push click tracked for queue ID {$queue->id}");
		} else {
			log_message('warning', "Push click with unknown token: $token");
		}

		// Redirect to the original destination
		redirect(urldecode($redirect));
	}
	
	public function plan_reminder() {
		$candidate_id = $this->input->get('candidate_id');
		$plan_id      = $this->input->get('plan_id');
		$redirect     = $this->input->get('redirect');

		if (!$candidate_id) {
			if ($redirect) redirect(urldecode($redirect));
			else $this->_outputGif();
			return;
		}

		// Find the most recent tracking record for this candidate
		$tracking = $this->db
			->where('candidate_id', $candidate_id)
			->order_by('created_at', 'DESC')
			->limit(1)
			->get('tb_plan_reminder_tracking')
			->row();

		if (!$tracking) {
			if ($redirect) redirect(urldecode($redirect));
			else $this->_outputGif();
			return;
		}

		if ($redirect) {
			// Click
			$this->db->where('id', $tracking->id)->update('tb_plan_reminder_tracking', [
				'clicked'    => 1,
				'clicked_at' => date('Y-m-d H:i:s'),
				'link_url'   => urldecode($redirect)
			]);
			redirect(urldecode($redirect));
		} else {
			// Open
			$this->db->where('id', $tracking->id)->update('tb_plan_reminder_tracking', [
				'opened'    => 1,
				'opened_at' => date('Y-m-d H:i:s')
			]);
			$this->_outputGif();
		}
	}
	
	
	/**
	 * Track plan automation email open (pixel)
	 * URL: /track/plan_automation_open/{token}
	 */
	// In Track.php controller

	public function plan_automation_open($token = null) {
		if (!$token) { $this->_outputGif(); return; }
		$track = $this->db->where('tracking_token', $token)->get('tb_plan_automation_tracking')->row();
		if ($track) {
			$this->db->where('id', $track->id)->update('tb_plan_automation_tracking', [
				'opened'    => 1,
				'opened_at' => date('Y-m-d H:i:s')
			]);
		}
		$this->_outputGif();
	}

	public function plan_automation_click($token = null) {
		$url = $this->input->get('url', true);
		if (!$token || !$url) show_error('Invalid tracking link', 400);
		$track = $this->db->where('tracking_token', $token)->get('tb_plan_automation_tracking')->row();
		if ($track) {
			$this->db->where('id', $track->id)->update('tb_plan_automation_tracking', [
				'clicked'    => 1,
				'clicked_at' => date('Y-m-d H:i:s'),
				'link_url'   => urldecode($url)
			]);
		}
		redirect(urldecode($url));
	}

	  // -----------------------------------------------------------------
    // Helper: Output 1x1 transparent GIF
    // -----------------------------------------------------------------
    private function _outputGif()
    {
        header('Content-Type: image/gif');
        echo base64_decode('R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7');
        exit;
    }
    
    // Helper to decode token
    private function decode_token($token) {
        $token = strtr($token, '-_', '+/');
        $decoded = base64_decode($token);
        if (!$decoded) return null;

        $parts = explode('|', $decoded);
        if (count($parts) < 2) return null;

        return [
            'campaign_id' => $parts[0],
            'email'       => $parts[1],
        ];
    }
    
}