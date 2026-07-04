<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . 'libraries/REST_Controller.php';

class MY_REST_Controller extends REST_Controller {

    protected $user_id;
    protected $user;

    public function __construct() {
        parent::__construct();

        $this->load->library('jwt_lib');
        $this->load->config('jwt');

        $this->_check_jwt();
    }

    protected function _check_jwt() {

        $header = $this->input->get_request_header('Authorization', TRUE);

        if (!$header || !preg_match('/Bearer\s(\S+)/', $header, $matches)) {
            $this->_unauthorized('Authorization token missing');
        }

        try {
            $payload = (array) $this->jwt_lib->decode_token($matches[1]);

            if (empty($payload['user_id'])) {
                $this->_unauthorized('Invalid token payload');
            }

            $this->user_id = (int) $payload['user_id'];
            $this->user    = $payload;

        } catch (Exception $e) {
            $this->_unauthorized('Invalid or expired token');
        }
    }

    protected function _unauthorized($msg) {
        $this->response([
            'status' => false,
            'message' => $msg
        ], REST_Controller::HTTP_UNAUTHORIZED);
        exit;
    }
}
