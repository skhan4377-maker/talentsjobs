<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Notification_model extends CI_Model {
    public function __construct() {
        parent::__construct();
    }

    // नई नोटिफिकेशन बनाएं
    public function create($data) {
        $this->db->insert('notifications', $data);
        return $this->db->insert_id();
    }

   public function get_user_notifications($user_id, $type = null, $limit = 5) {
		$notifications = [];

		// Step 1: Get unread notifications
		$this->db->select('id, message, link, is_read, created_at, type');
		$this->db->where('user_id', $user_id);
		$this->db->where('is_read', 0);
		if ($type !== null) {
			$this->db->where('type', $type);
		}
		$this->db->order_by('created_at', 'DESC');
		$unread = $this->db->get('notifications', $limit)->result();

		$notifications = $unread;
		$unread_count = count($unread);

		if ($unread_count < $limit) {
			$remaining = $limit - $unread_count;

			// Step 2: Fill remaining with read notifications
			$this->db->select('id, message, link, is_read, created_at, type');
			$this->db->where('user_id', $user_id);
			$this->db->where('is_read', 1);
			if ($type !== null) {
				$this->db->where('type', $type);
			}
			$this->db->order_by('created_at', 'DESC');
			$read = $this->db->get('notifications', $remaining)->result();

			$notifications = array_merge($notifications, $read);
		}

		return $notifications;
	}


	public function count_unread($user_id, $type = null) {
		$this->db->where('user_id', $user_id);
		$this->db->where('is_read', 0);
		if ($type !== null) {
			$this->db->where('type', $type);
		}
		return $this->db->count_all_results('notifications');
	}

	
	 public function get_notification($notification_id) {
        return $this->db->get_where('notifications', ['id' => $notification_id])->row();
    }
		
    // नोटिफिकेशन को पढ़ा हुआ मार्क करें
    public function mark_as_read($notification_id) {
        $this->db->where('id', $notification_id);
        $this->db->update('notifications', array('is_read' => 1));
    }
	
}

