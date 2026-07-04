<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH . 'libraries/REST_Controller.php';

class Redirect extends REST_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    public function track_post() {

        $input = json_decode(file_get_contents("php://input"), true);

        $data = [
            'source'        => $input['source'] ?? 'direct',
            'medium'        => $input['medium'] ?? 'none',
            'campaign'      => $input['campaign'] ?? null,
            'content'       => $input['content'] ?? null,
            'landing_page'  => $input['landing_page'] ?? null,
            'referrer_url'  => $input['referrer_url'] ?? null,
            'ip_address'    => $this->input->ip_address(),
            'user_agent'    => $this->input->user_agent()
        ];

        $this->db->insert('redirect_tracking', $data);

        return $this->response([
            'status' => true,
            'message' => 'Redirect tracked successfully'
        ], REST_Controller::HTTP_OK);
    }
}
