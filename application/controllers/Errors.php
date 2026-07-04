<?php
class Errors extends CI_Controller {
    public function show_404() {
        $this->output->set_status_header('404');
        $this->load->view('404');
    }
}
