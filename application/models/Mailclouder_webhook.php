<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mailclouder_webhook extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    public function index() {

        // 🔴 Read raw payload
        $payload = file_get_contents('php://input');

        // ✅ Insert the complete raw response into dummy table
        $this->db->insert('tb_webhook_logs', [
            'raw_payload' => $payload,
            'created_at'  => date('Y-m-d H:i:s')
        ]);

        // Optional: log for debugging
        log_message('info', 'Webhook received and stored. Length: ' . strlen($payload));

        // 🔴 Always return 200 OK to acknowledge receipt
        return $this->sendResponse(['status' => 'logged'], 200);
    }

    private function sendResponse($data, $statusCode = 200) {
        return $this->output
            ->set_content_type('application/json')
            ->set_status_header($statusCode)
            ->set_output(json_encode($data));
    }
}