<?php class Applied_mdl extends CI_Model{
	
	public function __construct(){
		parent::__construct();
	}
	
	function check_applied($table, $id) {
        $this->db->select("1"); // Select 1 for efficiency, we only need to know if there's a matching row
        $this->db->from("$table");
        $this->db->where("$table.candidate_id", $id['candidate_id']);
        $this->db->where("$table.job_id", $id['job_id']);
        $query = $this->db->get();
        return $query->num_rows() > 0;
    }
    
    
	public function hasAppliedForJob($jobId, $candidateId) {
        $this->db->select('job_id');
        $this->db->from('applied');
        $this->db->where('job_id', $jobId);
        $this->db->where('candidate_id', $candidateId);
        $query = $this->db->get();
        return $query->num_rows() > 0;
    }
    
    public function countTodayApplications($pid) {
        $this->db->select('COUNT(*) as today_count');
        $this->db->from('applied');
        $this->db->where('job_id', $pid);
        $this->db->where('DATE(created_at)', date('Y-m-d')); // Assuming created_at is the application timestamp
        $query = $this->db->get();
        $result = $query->row_array();
        return isset($result['today_count']) ? $result['today_count'] : 0;
    }    
    
	// In your Applied_mdl model
	public function getFavoritedJobs($userId) {
		$this->db->select('job_id');
		$this->db->where('candidate_id', $userId);
		$this->db->where('status', '1'); // Assuming '1' represents favorited status, adjust as needed
		$query = $this->db->get('favourite');

		$favoritedJobs = array();

		foreach ($query->result_array() as $row) {
			$favoritedJobs[] = $row['job_id'];
		}
		return $favoritedJobs;
	}
	
	public function get_post_details($id, $status = true) {
		// ✅ SINGLE QUERY APPROACH - Combine all data in one query
		$this->db->select('
			p.job_id,
			p.job_title,
			p.slug,
			p.job_type,
			p.min_salary,
			p.max_salary,
			p.salary_type,
			p.min_experience,
			p.max_experience,
			p.job_description,
			p.education,
			p.positions_open,
			p.apply_web_link,
			p.enable_apply_link,
			p.created_at as post_date,
			p.deadline_date,
			p.call_status,
			p.email_status,
			p.status as job_status,
			p.employer_id,
			p.functional_id,
			p.industry_id,
			e.company_name,
			e.name,
			e.employee_designation,
			e.is_verified,
			e.email as employer_email,
			e.mobile as employer_mobile,
			e.logo,
			e.about_company,
			e.company_size,
			e.company_founded,
			EI.industry_name as employer_industry_name,
			I.industry_name as post_industry_name,
			FN.functional_area,
			GROUP_CONCAT(DISTINCT s.skill_name ORDER BY s.skill_name SEPARATOR ", ") as skills,
			GROUP_CONCAT(DISTINCT c.city_name ORDER BY c.city_name SEPARATOR ", ") as job_locations,
			CONCAT(p.min_salary, " - ", p.max_salary) as salary_range,
			CONCAT(p.min_experience, " - ", p.max_experience) as experience_range
		');
		
		$this->db->from('post_job as p');
		$this->db->join('tb_employer as e', 'e.employer_id = p.employer_id', 'left');
		$this->db->join('industry as I', 'I.industry_id = p.industry_id', 'left');
		$this->db->join('functional_area as FN', 'FN.functional_id = p.functional_id', 'left');
		$this->db->join('industry as EI', 'EI.industry_id = e.industry_id', 'left');
		$this->db->join('job_skills as s', 's.job_id = p.job_id', 'left');
		$this->db->join('tb_job_cities as jc', 'jc.job_id = p.job_id', 'left');
		$this->db->join('cities as c', 'c.city_id = jc.city_id', 'left');

		if ($status) {
			$this->db->where('p.status', 'active');
		}
		$this->db->where('p.job_id', $id);
		$this->db->group_by('p.job_id');

		$query = $this->db->get();
		$job = $query->row_array();

		if (!$job) return ['count' => 0, 'data' => []];

		return [
			'count' => 1,
			'data' => $job
		];
	}

	function recommended($profile, $limit, $candidateId = null) {
		$this->db->select('
			p.job_id, 
			p.slug,
			p.job_title, 
			p.job_type, 
			p.min_salary, 
			p.max_salary,
			p.job_description, 
			e.logo, 
			e.company_name,
			GROUP_CONCAT(DISTINCT c.city_name ORDER BY c.city_name SEPARATOR ", ") as job_locations,
			GROUP_CONCAT(DISTINCT s.skill_name SEPARATOR ", ") as skill_names
		');
		$this->db->from('post_job as p');
		$this->db->join('employer as e', 'e.employer_id = p.employer_id', 'left');
		$this->db->join('tb_job_cities as jc', 'jc.job_id = p.job_id', 'left');
		$this->db->join('cities as c', 'c.city_id = jc.city_id', 'left');
		$this->db->join('tb_job_skills as s', 's.job_id = p.job_id', 'left');
		$this->db->join('industry as in', 'in.industry_id = p.industry_id', 'left');
		$this->db->join('functional_area as FN', 'FN.functional_id = p.functional_id', 'left');

		$this->db->limit($limit);
		$this->db->where('p.status', 'active');
		$this->db->where('e.status', 'active');
		//$this->db->where('p.deadline_date >=', date('Y-m-d')); // ✅ exclude expired jobs

		if (!empty($profile)) {
			$keywords = explode(' ', $profile);
			$conditions = [];

			foreach ($keywords as $keyword) {
				$keyword = trim($keyword);
				if (!empty($keyword)) {
					$conditions[] = "(UPPER(p.job_title) LIKE UPPER('%$keyword%') OR UPPER(s.skill_name) LIKE UPPER('%$keyword%'))";
				}
			}

			if (!empty($conditions)) {
				$this->db->where('(' . implode(' OR ', $conditions) . ')');
			}
		}

		$this->db->order_by('p.created_at', 'DESC');
		$this->db->group_by('p.job_id');

		$query = $this->db->get();
		$results = $query->result_array();

		if ($candidateId) {
			$results = array_filter($results, function ($job) use ($candidateId) {
				return !$this->hasAppliedForJob($job['job_id'], $candidateId);
			});
		}

		return array_values($results);
	}
	
	public function get_myapply_count($id, $search_term){
		$this->db->select('P.job_id');
		$this->db->from('applied as AP');
		$this->db->join('post_job as P', 'P.job_id = AP.job_id');
		$this->db->join('tb_job_cities as jc', 'jc.job_id = AP.job_id', 'left');
		$this->db->join('cities as c', 'c.city_id = jc.city_id', 'left');
		$this->db->where('AP.candidate_id', $id);
		$this->db->where('P.deadline_date >=', date('Y-m-d'));

		if (isset($search_term['job-id'])) {
			$this->db->where('AP.job_id', $search_term['job-id']);
		}

		$this->db->group_by('P.job_id');  // ✅ same as data query
		return $this->db->get()->num_rows();
	}

		
	public function myapply($limit = FALSE, $offset = FALSE, $id = FALSE, $search_term = FALSE) {
		$this->db->select(
			'P.job_id,
			 P.job_title,
			 P.slug,
			 P.min_experience,
			 P.max_experience,
			 P.job_type,
			 CONCAT(IFNULL(CAST(P.min_salary AS CHAR), ""), "-", IFNULL(CAST(P.max_salary AS CHAR), "")) as salary_range,
			 ER.company_name,
			 ER.name,
			 ER.email,
			 ER.mobile,
			 ER.alternate_contact,
			 ER.updated_at as employer_last_active_dt,
			 GROUP_CONCAT(DISTINCT c.city_name SEPARATOR ", ") as cities,
			 AP.applied_id,
			 AP.ApplicationStage,
			 AP.created_at,
			 AP.updated_at'
		);

		$this->db->from('applied as AP');
		$this->db->join('post_job as P', 'P.job_id = AP.job_id');
		$this->db->join('tb_job_cities as jc', 'jc.job_id = AP.job_id', 'left');
		$this->db->join('cities as c', 'c.city_id = jc.city_id', 'left');
		$this->db->join('employer as ER', 'ER.employer_id = P.employer_id');

		$this->db->where('AP.candidate_id', $id);
		//$this->db->where('P.deadline_date >=', date('Y-m-d')); // ✅ exclude expired jobs

		if (isset($search_term['job-id'])) {
			$this->db->where('AP.job_id', $search_term['job-id']);
		}

		$this->db->group_by('P.job_id');

		// ✅ Order by application date (latest first)
		$this->db->order_by('AP.created_at', 'DESC');

		if ($limit) {
			$this->db->limit($limit, $offset);
		}

		$query = $this->db->get();
		return $query->result_array();
	}
	
	// Get all stage logs grouped by application_id
	public function get_latest_application_logs($applied_ids = []) {
		if (empty($applied_ids)) return [];

		$this->db->select('application_id, stage, created_at');
		$this->db->from('tb_application_logs');
		$this->db->where_in('application_id', $applied_ids);
		$this->db->order_by('created_at', 'DESC');

		$query = $this->db->get()->result_array();

		$logs = [];
		foreach ($query as $log) {
			$logs[$log['application_id']][] = [
				'stage' => $log['stage'],
				'created_at' => $log['created_at']
			];
		}

		return $logs; // [applied_id => [log1, log2, ...]]
	}





	


    



	
}