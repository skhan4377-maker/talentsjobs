<?php
class EmailCampaign_model extends CI_Controller {

    public function __construct() {
        parent::__construct();
        // ✅ Agar sirf CLI se run karna ho to enable karo
         if (!$this->input->is_cli_request()) {
             exit("❌ CLI only\n");
         }
    }

  
	public function send_bulk_emails() {
		// ✅ Step 1: Get active campaigns
		$campaigns = $this->db->query("
			SELECT id, name, send_limit, subject, content
			FROM tb_email_campaigns
			WHERE status = 'active'
		")->result();

		if (empty($campaigns)) {
			echo "⚠️ No active campaigns\n";
			return;
		}

		foreach ($campaigns as $c) {
			$pickSize = (int) $c->send_limit;

			// ✅ Step 2: Pick pending emails (use lead email & name from tb_campaign_leads)
			$emails = $this->db->query("
				SELECT q.id as queue_id, 
					   l.id as lead_id, l.name, l.email, l.user_type,
					   c.id as campaign_id, c.name as campaign_name, 
					   c.subject, c.content
				FROM tb_email_queue q
				INNER JOIN tb_campaign_leads l ON q.lead_id = l.id
				INNER JOIN tb_email_campaigns c ON q.campaign_id = c.id
				WHERE q.status = 'pending'
				  AND q.campaign_id = ?
				ORDER BY q.id ASC
				LIMIT {$pickSize} FOR UPDATE;
			", [$c->id])->result();
						
			// ✅ If no emails in queue, skip sending
			if (empty($emails)) {
				echo "⚠️ Campaign {$c->id} ({$c->name}) has no leads in queue\n";
				continue;
			}

			// ✅ Step 3: Filter out failed/bounced emails separately
			$emailAddresses = array_column($emails, 'email');
			$badEmails = [];

			if (!empty($emailAddresses)) {
				$badEmails = $this->db->select('DISTINCT(email)')
									  ->where_in('email', $emailAddresses)
									  ->where_in('status', ['failed', 'bounced'])
									  ->get('tb_email_queue')
									  ->result_array();
			}

			$badList = array_column($badEmails, 'email');

			$validEmails = [];
			foreach ($emails as $mail) {
				if (!in_array($mail->email, $badList)) {
					$validEmails[] = $mail;
				}
			}

			// ✅ Replace original list with filtered safe emails
			$emails = $validEmails;

			if (empty($emails)) {
				echo "⚠️ Campaign {$c->id} ({$c->name}) skipped - all emails are failed/bounced\n";
				continue;
			}

			$this->db->trans_start();

			// ✅ Lock picked rows
			$queueIds = array_column($emails, 'queue_id');
			$this->db->where_in('id', $queueIds)
					 ->update('tb_email_queue', ['status' => 'processing']);

			$this->db->trans_complete();

			echo "🔄 Campaign {$c->id}: Picked " . count($emails) . " emails\n";

			$successIds = [];
			$failed = [];

			// ✅ Step 4: Send Emails
			foreach ($emails as $mail) {
				$message = $this->load->view("admin/campaigns/layout_template", [
					'lead'     => $mail, // lead-specific info: name, email
					'campaign' => $c     // campaign info: subject, content
				], true);

				$status = send_mailercloud_email(					
					$mail->email,    // ✅ always from leads table
					$mail->name,     // ✅ correct lead name
					$c->subject,     // ✅ always from campaign
					$message
				);

				if ($status['status'] === 'success') {
					$successIds[] = $mail->queue_id;
				} else {
					$failed[] = [
						'id'            => $mail->queue_id,
						'error_message' => isset($status['message']) ? $status['message'] : 'Unknown error'
					];
				}
			}

			// ✅ Bulk update results
			if (!empty($successIds)) {
				$this->db->where_in('id', $successIds)
						 ->update('tb_email_queue', [
							 'status'  => 'sent',
							 'sent_at' => date('Y-m-d H:i:s')
						 ]);
			}		
			
			if (!empty($failed)) {
				foreach ($failed as $f) {
					$this->db->where('id', $f['id'])
							 ->update('tb_email_queue', [
								 'status'        => 'failed',
								 'error_message' => $f['error_message'],
								 'sent_at'       => date('Y-m-d H:i:s')
							 ]);
				}
			}

			echo "✅ Campaign {$c->id}: Sent " . count($successIds) . " | ❌ Failed " . count($failed) . "\n";

			// ✅ Update last_run for this campaign
			$this->db->where('id', $c->id)
					 ->update('tb_email_campaigns', [
						 'last_run' => date('Y-m-d H:i:s')
					 ]);

			// ✅ Step 5: Mark campaign completed if no more pending
			$pendingCount = $this->db->where('campaign_id', $c->id)
									 ->where('status', 'pending')
									 ->count_all_results('tb_email_queue');

			if ($pendingCount == 0) {
				$this->db->where('id', $c->id)
						 ->update('tb_email_campaigns', ['status' => 'completed']);
				echo "🏁 Campaign {$c->id} ({$c->name}) completed\n";
			}
		}
	}
		
	






}
