<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MasterCron extends CI_Controller {

    public function __construct() {
        parent::__construct();

        //if (!$this->input->is_cli_request()) {
           // show_error('Direct access not allowed', 403);
        //}
    }

    public function index() {
        // Existing tasks
        $this->load->library('cron/RefundWorker');
        $this->refundworker->run();

        $this->load->library('cron/PushConsumer');
        $this->pushconsumer->run();

        // Campaign email sender (testing mode)
        $this->load->library('cron/CampaignSender');
        $this->campaignsender->run(); 
		
		$this->load->library('cron/PlanReminderConsumer');
        $this->planreminderconsumer->run();
		
		$this->load->library('cron/PlanAutomationConsumer');
        $this->planautomationconsumer->run();
		
		$this->load->library('cron/JobRecommendationConsumer');
        $this->jobrecommendationconsumer->run();
		

        echo PHP_EOL . "MasterCron Completed" . PHP_EOL;
    }
}