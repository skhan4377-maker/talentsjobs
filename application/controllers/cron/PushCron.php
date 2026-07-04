<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class PushCron extends CI_Controller {

    public function __construct() {
        parent::__construct();
        if (!$this->input->is_cli_request()) {
            show_error('Direct access not allowed', 403);
        }
    }

    public function index() {
        // Existing push enqueuer
        $this->load->library('cron/PushEnqueuer');
        $result = $this->pushenqueuer->run();
		
		$this->load->library('cron/PlanReminderDispatcher');
        $result = $this->planreminderdispatcher->run();	
		
		$this->load->library('cron/PlanAutomationDispatcher');
        $result = $this->planautomationdispatcher->run();	
		
		$this->load->library('cron/JobRecommendationDispatcher');
        $result = $this->jobrecommendationdispatcher->run();	
		
        echo PHP_EOL . "PushEnqueuer Result:" . PHP_EOL;
        print_r($result);

    }
}