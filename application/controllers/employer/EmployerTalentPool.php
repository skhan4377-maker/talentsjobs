<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class EmployerTalentPool extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->user_id = $this->session->userdata('user_id');
        $this->load->model('employer/EmployerTalentPool_model');
        $this->load->model('employer/EmployerPlans_model'); // ✅ NEW
        $this->load->database();
        $this->load->driver('cache');
    }

    public function index() {
        $data['title'] = 'Talent Pool - Candidate Search';

        $this->load->model('employer/Profile_mdl');
        $profile = $this->Profile_mdl->get_employer_details($this->user_id);
        $data['status'] = isset($profile['status']) ? strtolower($profile['status']) : 'inactive';

        $data['filter_keywords']   = $this->input->get('keywords');
        $data['filter_experience'] = $this->input->get('experience');
        $data['filter_location']   = $this->input->get('location');
        $data['filter_skills']     = $this->input->get('skills');
        $data['filter_job_type']   = $this->input->get('job_type');

        $data['candidates'] = [];
        $data['total_matches'] = 0;

        $data['content'] = $this->load->view('employer/search-candidate', $data, TRUE);
        $this->load->view('templates/master', $data);
    }

    /**
     * AJAX endpoint: paginated candidate search
     */
    public function ajax_get_candidates() {

		$page = (int) $this->input->get('page');
		if ($page < 1) $page = 1;

		// ✅ PLAN CHECK — SEARCH LIMIT
		$check = $this->EmployerPlans_model->canPerformAction($this->user_id, 'search');

		if ($check !== true) {
			echo json_encode(['error' => $check]);
			return;
		}

		// ✅ Increment search usage ONLY when first page (new search)
		if ($page == 1) {
			$plan = $this->EmployerPlans_model->getActivePlanDetails($this->user_id);

			if ($plan) {
				$this->EmployerPlans_model->incrementUsage($plan['plan_purchase_id'], 'search');
			}
		}

		$limit = 15;
		$offset = ($page - 1) * $limit;

		// ✅ Filters
		$filters = [
			'keywords'   => $this->input->get('keywords'),
			'experience' => $this->input->get('experience'),
			'location'   => $this->input->get('location'),
			'skills'     => $this->input->get('skills'),
			'job_type'   => $this->input->get('job_type')
		];

		$filters = array_filter($filters, fn($v) => !is_null($v) && $v !== '');

		// ✅ Cache key
		$cache_key = 'candidates_' . md5(serialize($filters) . $page);

		$cached = $this->cache->get($cache_key);

		if ($cached) {
			echo json_encode($cached);
			return;
		}

		// ✅ Get candidates
		$candidates = $this->EmployerTalentPool_model->get_candidates_paginated($filters, $limit, $offset);
		$total      = $this->EmployerTalentPool_model->count_candidates($filters);

		// ✅ Remaining plan credits
		$remaining = $this->EmployerPlans_model->getRemainingCredits($this->user_id);

		$response = [
			'candidates'        => $candidates,
			'total'             => $total,
			'page'              => $page,
			'total_pages'       => ceil($total / $limit),
			'remaining_credits' => $remaining
		];

		// ✅ Save cache
		$this->cache->save($cache_key, $response, 300);

		echo json_encode($response);
	}

    /**
     * AJAX endpoint: candidate profile details
     */
    public function get_candidate_details() {

        // ✅ PLAN CHECK — CV VIEW LIMIT
        $check = $this->EmployerPlans_model->canPerformAction($this->user_id, 'cv_view');
        if ($check !== true) {
            echo json_encode([
                'error' => $check,
                'csrf_token' => $this->security->get_csrf_hash(),
                'csrf_name'  => $this->security->get_csrf_token_name()
            ]);
            return;
        }

        $candidate_id = $this->input->post('candidate_id');

        if (!$candidate_id) {
            echo json_encode([
                'error' => 'No candidate ID',
                'csrf_token' => $this->security->get_csrf_hash(),
                'csrf_name'  => $this->security->get_csrf_token_name()
            ]);
            return;
        }

        // ✅ Increment CV view usage
        $plan = $this->EmployerPlans_model->getActivePlanDetails($this->user_id);
        $this->EmployerPlans_model->incrementUsage($plan['plan_purchase_id'], 'cv');

        $details = $this->EmployerTalentPool_model->get_candidate_details($candidate_id);

        if ($details) {
            $details['csrf_token'] = $this->security->get_csrf_hash();
            $details['csrf_name']  = $this->security->get_csrf_token_name();
            echo json_encode($details);
        } else {
            echo json_encode([
                'error' => 'Candidate not found',
                'csrf_token' => $this->security->get_csrf_hash(),
                'csrf_name'  => $this->security->get_csrf_token_name()
            ]);
        }
    }
	
	public function export_excel() {

		// ✅ PLAN CHECK — BULK DOWNLOAD LIMIT
		$check = $this->EmployerPlans_model->canPerformAction($this->user_id, 'bulk_download');

		if ($check !== true) {
			$this->planLimitUI($check);
		}

		// ✅ Get Plan
		$plan = $this->EmployerPlans_model->getActivePlanDetails($this->user_id);

		if (!$plan) {
			$this->planLimitUI('No active plan found.');
		}

		// ✅ Increment bulk download usage
		$this->EmployerPlans_model->incrementUsage($plan['plan_purchase_id'], 'download');

		// ✅ Filters
		$filters = [
			'keywords'   => $this->input->get('keywords'),
			'experience' => $this->input->get('experience'),
			'location'   => $this->input->get('location'),
			'skills'     => $this->input->get('skills'),
			'job_type'   => $this->input->get('job_type')
		];

		$filters = array_filter($filters);

		// ✅ Get candidates
		$candidates = $this->EmployerTalentPool_model->get_candidates_paginated($filters, 1000, 0);

		// ✅ Excel headers
		header("Content-Type: application/vnd.ms-excel");
		header("Content-Disposition: attachment; filename=candidates_" . date('YmdHis') . ".xls");

		function clean($str) {
			return str_replace(["\t","\n","\r"], ' ', $str);
		}

		echo "Name\tEmail\tMobile\tExperience\tLocation\tSkills\n";

		foreach ($candidates as $c) {

			$name   = clean(trim($c['name'].' '.($c['last_name'] ?? '')));
			$email  = clean($c['email']);
			$mobile = clean($c['mobile']);
			$exp    = clean($c['total_experience_years']."y ".$c['total_experience_months']."m");
			$city   = clean($c['city_name']);
			$skills = clean(implode(', ', $c['skills']));

			echo "{$name}\t{$email}\t{$mobile}\t{$exp}\t{$city}\t{$skills}\n";
		}

		exit;
	}
	
	private function planLimitUI($message) {
    	echo '
    	<!DOCTYPE html>
    	<html>
    	<head>
    		<title>Activate Recruiter Plan</title>
    		<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">
    		<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css">
    	</head>
    
    	<body class="bg-gray-50 flex items-center justify-center min-h-screen p-4">
    
    		<div class="bg-white shadow-lg rounded-xl p-6 text-center max-w-lg w-full border border-gray-100">
    
    			<div class="w-16 h-16 mx-auto mb-4 rounded-full bg-blue-100 flex items-center justify-center">
    				<i class="fas fa-users text-blue-600 text-2xl"></i>
    			</div>
    
    			<h2 class="text-2xl font-bold text-gray-800 mb-2">
    				Activate Your Free Recruiter Plan
    			</h2>
    
    			<p class="text-gray-600 mb-5">
    				'.$message.'
    			</p>
    
    			<div class="flex flex-wrap justify-center gap-2 mb-6 text-xs">
    
    				<span class="bg-green-100 text-green-700 px-3 py-1 rounded-full">
    					<i class="fas fa-briefcase mr-1"></i>
    					Job Posting
    				</span>
    
    				<span class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full">
    					<i class="fas fa-search mr-1"></i>
    					Candidate Search
    				</span>
    
    				<span class="bg-purple-100 text-purple-700 px-3 py-1 rounded-full">
    					<i class="fas fa-file-alt mr-1"></i>
    					Resume Access
    				</span>
    
    			</div>
    
    			<div class="flex flex-wrap justify-center gap-3">
    
    				<a href="javascript:history.back()"
    				class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm">
    					<i class="fas fa-arrow-left mr-1"></i>
    					Back
    				</a>
    
    				<a href="'.base_url('employer/jobs/create').'"
    				class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg text-sm font-medium">
    					<i class="fas fa-rocket mr-1"></i>
    					Activate Free Plan
    				</a>
    
    			</div>
    
    		</div>
    
    	</body>
    	</html>';
    
    	exit;
    }
}