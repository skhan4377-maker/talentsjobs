<?php defined('BASEPATH') OR exit('No direct script access allowed');

class SalarySearch extends CI_Controller {
    public function __construct() {
        parent::__construct();
        //$this->load->model('salary_mdl');
    }

    public function index() {
		//$data['industries'] = $this->salary_mdl->get_industries_with_job_count();

		$job = $this->input->get('job', TRUE);
		if ($job && preg_match('/^[a-zA-Z0-9 ]+$/', $job)) {
			//$data['averageSalaryData'] = $this->salary_mdl->getFunctionalAreasBySalaryStatistics($job);
		}
		
		if (isset($data['industries'])) {
			foreach ($data['industries'] as $key => $industry) {
				//$data['industries'][$key]['functional_areas'] = $this->salary_mdl->get_functional_areas_by_industry($industry['industry_id']);
			}
		}
		
		// Echo the maintenance message before loading the view
		echo "<div class='maintenance-message' style='text-align:center; padding:10px; background-color:#f8d7da; color:#721c24;'>
				Under Maintenance
			  </div>";
		
		//$this->load->view('public/search_salary', isset($data) ? $data : []);
	}

}

	
