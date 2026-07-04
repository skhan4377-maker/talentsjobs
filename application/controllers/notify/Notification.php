<?php
class Notification extends MY_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->model('notification_model');
    }
		
    public function get_notifications() {
        $user_id = $this->session->userdata('user_id');
        $type = $this->session->userdata('role');
        
        $notifications = $this->notification_model->get_user_notifications($user_id, $type);
        $unread_count = $this->notification_model->count_unread($user_id, $type);
        
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'notifications' => $notifications,
                'unread_count' => $unread_count,
                'csrf_name' => $this->security->get_csrf_token_name(),
                'csrf_hash' => $this->security->get_csrf_hash()
            ]));
    }
	
    public function mark_read_ajax() {
        // Verify it's an AJAX request
        if (!$this->input->is_ajax_request()) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'error', 'message' => 'Invalid request']));
            return;
        }

        $notification_id = $this->input->post('notification_id');
        $user_id = $this->session->userdata('user_id');
        $type = $this->session->userdata('role');
        
        $notification = $this->notification_model->get_notification($notification_id);

        if ($notification && $notification->user_id == $user_id && $notification->type == $type) {
            $this->notification_model->mark_as_read($notification_id);
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => 'success',
                    'csrf_name' => $this->security->get_csrf_token_name(),
                    'csrf_hash' => $this->security->get_csrf_hash()
                ]));
        } else {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => 'error',
                    'csrf_name' => $this->security->get_csrf_token_name(),
                    'csrf_hash' => $this->security->get_csrf_hash()
                ]));
        }
    }
}
?>