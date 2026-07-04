<?php
class Jobs_model extends CI_Model {

    public function __construct(){
        parent::__construct();
    }
    
	public function fetch_data($limit, $start, $filters = []) {
		$this->_build_base_query($filters);
		$this->db->limit($limit, $start);
		$jobs = $this->db->get()->result_array();

		if (empty($jobs)) return [];

		$job_ids = array_column($jobs, 'job_id');
		$cities = $this->_fetch_job_cities($job_ids);
		$skills = $this->_fetch_job_skills($job_ids);

		foreach ($jobs as &$job) {
			$job['job_locations'] = $cities[$job['job_id']] ?? '';
			$job['skills'] = $skills[$job['job_id']] ?? '';
		}
		return $jobs;
	}

    public function count_all($filters = []) {
		$this->_build_count_query($filters);
		return $this->db->get()->row()->total;
	}	
	
	private function _build_base_query($filters) {
		$this->db->select("P.job_id, P.job_title, P.slug, P.min_salary, P.max_salary, P.min_experience, P.max_experience,
						  P.job_type, E.logo, E.company_name, P.created_at as createdAt");
		$this->db->from('post_job P');
		$this->db->join('employer E', 'E.employer_id = P.employer_id');
		$this->db->where('P.status', 'active');
		$this->db->where('E.status', 'active');
		$this->_apply_filters($filters, false); // false = not count query
		
		// Only add default ordering if not already ordered by relevance
		if (empty($filters['key_word'])) {
			$this->db->order_by('P.created_at', 'DESC');
		}
	}

	private function _build_count_query($filters) {
		$this->db->select("COUNT(DISTINCT P.job_id) as total");
		$this->db->from('post_job P');
		$this->db->join('employer E', 'E.employer_id = P.employer_id');
		$this->db->where('P.status', 'active');
		$this->db->where('E.status', 'active');
		$this->_apply_filters($filters, true); // true = count query
	}

	private function _apply_filters($filters, $is_count_query = false) {    
		if (!empty($filters['key_word'])) {
			$words = preg_split('/[\s,]+/', $filters['key_word']);
			$words = array_filter(array_map('trim', $words));

			if ($words) {
				$this->db->join('job_skills JS', 'JS.job_id = P.job_id', 'left');
				$this->db->group_start();
				
				// FIRST: Exact phrase match in job_title (highest priority)
				$this->db->or_like('P.job_title', $filters['key_word'], 'both');
				
				// SECOND: Individual words in job_title
				foreach ($words as $word) {
					$this->db->or_like('P.job_title', $word);
				}
				
				// THIRD: Skill matches
				foreach ($words as $word) {
					$this->db->or_like('JS.skill_name', $word);
				}
				
				// FOURTH: Description matches (lowest priority)
				foreach ($words as $word) {
					$this->db->or_like('P.job_description', $word);
				}
				
				$this->db->group_end();
				
				// Add custom ORDER BY for relevance if not a count query
				if (!$is_count_query) {
					// Calculate relevance score
					$relevance_sql = "(
						CASE 
							WHEN P.job_title LIKE '%" . $this->db->escape_like_str($filters['key_word']) . "%' THEN 100
							WHEN P.job_title REGEXP '" . $this->db->escape_str(implode('.*', $words)) . "' THEN 80
							ELSE (
								(CASE WHEN P.job_title LIKE '%" . $this->db->escape_like_str($words[0]) . "%' THEN 20 ELSE 0 END) +
								(CASE WHEN JS.skill_name LIKE '%" . $this->db->escape_like_str($filters['key_word']) . "%' THEN 30 ELSE 0 END) +
								(CASE WHEN P.job_description LIKE '%" . $this->db->escape_like_str($filters['key_word']) . "%' THEN 10 ELSE 0 END)
							)
						END
					) as relevance";
					
					$this->db->select($relevance_sql, false);
					$this->db->order_by('relevance', 'DESC');
					$this->db->order_by('P.created_at', 'DESC');
				}
				
				// Add group by only for non-count queries to avoid duplicates
				if (!$is_count_query) {
					$this->db->group_by('P.job_id');
				}
			}
		}

		// Rest of the filter logic remains the same...
		if (!empty($filters['industry'])) {
			$this->db->where_in('P.industry_id', $filters['industry']);
		}
		if (!empty($filters['education'])) {
			$this->db->where_in('P.education', $filters['education']);
		}
		if (!empty($filters['job_type'])) {
			$this->db->where_in('P.job_type', $filters['job_type']);
		}
		if (!empty($filters['salary'])) {
			$this->db->group_start();
			foreach ($filters['salary'] as $range) {
				$this->db->or_where("(P.min_salary <= {$range['max']} AND P.max_salary >= {$range['min']})");
			}
			$this->db->group_end();
		}
		
		if (!empty($filters['locations'])) {
			$locations = array_map('trim', $filters['locations']);
			$this->db->join('job_cities JC', 'JC.job_id = P.job_id', 'inner');
			$this->db->join('cities C', 'C.city_id = JC.city_id', 'inner');
			$this->db->where_in('C.city_name', $locations);
			// Add group by only for non-count queries
			if (!$is_count_query && empty($filters['key_word'])) {
				$this->db->group_by('P.job_id');
			}
		}

		if (!empty($filters['experience'])) {
			if ($filters['experience'] === '5+') {
				$this->db->where('P.min_experience >=', 5);
			} else {
				list($min, $max) = explode('-', $filters['experience']);
				$this->db->where('P.min_experience <=', $max);
				$this->db->where('P.max_experience >=', $min);
			}
		}
	}

	private function _fetch_job_cities($job_ids) {
		$rows = $this->db->select('JC.job_id, C.city_name')
			->from('job_cities JC')
			->join('cities C', 'C.city_id = JC.city_id')
			->where_in('JC.job_id', $job_ids)
			->get()->result_array();

		$map = [];
		foreach ($rows as $row) {
			$map[$row['job_id']][] = $row['city_name'];
		}
		foreach ($map as &$list) {
			$list = implode(', ', array_unique($list));
		}
		return $map;
	}
		
	private function _fetch_job_skills($job_ids) {
		$rows = $this->db->select('job_id, skill_name')
			->from('job_skills')
			->where_in('job_id', $job_ids)
			->get()->result_array();

		$map = [];
		foreach ($rows as $row) {
			$map[$row['job_id']][] = $row['skill_name'];
		}
		foreach ($map as &$list) {
			$list = implode(', ', array_unique($list));
		}
		return $map;
	}


	public function getActiveIndustriesWithCount() {
		$this->db->select('I.industry_id, I.industry_name, COUNT(P.job_id) as job_count')
			->from('post_job P')
			->join('industry I', 'I.industry_id = P.industry_id')
			->join('employer E', 'E.employer_id = P.employer_id')
			->where('P.status', 'active')
			->where('E.status', 'active')
			//->where('P.deadline_date >=', date('Y-m-d')) // ✅ exclude expired jobs
			->group_by('I.industry_id')
			->order_by('I.industry_name', 'ASC');
		
		return $this->db->get()->result_array();
	}

	
	public function getActiveJobTypesWithCount() {
		$this->db->select('job_type, COUNT(job_id) as job_count')
			->from('post_job P')
			->join('employer E', 'E.employer_id = P.employer_id')
			->where('P.status', 'active')
			->where('E.status', 'active')
			//->where('P.deadline_date >=', date('Y-m-d')) // ✅ exclude expired jobs
			->group_by('job_type')
			->order_by('job_type', 'ASC');
		
		return $this->db->get()->result_array();
	}
	
	public function getActiveEducationsWithCount() {
		$this->db->select('P.education, COUNT(P.job_id) as job_count')
			->from('post_job P')
			->join('employer E', 'E.employer_id = P.employer_id')
			->where('P.status', 'active')
			->where('E.status', 'active')
			//->where('P.deadline_date >=', date('Y-m-d')) // ✅ exclude expired jobs
			->where('P.education !=', '')
			->group_by('P.education')
			->order_by('P.education', 'ASC');
		
		return $this->db->get()->result_array();
	}

	// use in job details page
	public function mightBeLike($limit = 4, $job = [], $offset = 0){
        // 🚫 Safety check
        if (empty($job['job_title']) || empty($job['job_id'])) {
            return [];
        }
    
        // 🔤 Normalize title
        $title = strtolower(trim($job['job_title']));
    
        // 🧠 Extract keywords (remove special chars)
        $keywords = preg_split('/\s+/', preg_replace('/[^a-zA-Z0-9 ]/', '', $title));
    
        // 🎯 Filter valid keywords (min length 3)
        $validKeywords = array_filter($keywords, function ($word) {
            return strlen($word) >= 3;
        });
    
        $this->db->select('
            p.job_id,
            p.job_title,
            p.slug,
            p.job_type,
            p.min_salary,
            p.max_salary,
			 p.created_at,     
            e.company_name,
            e.logo,
            GROUP_CONCAT(DISTINCT c.city_name ORDER BY c.city_name SEPARATOR ", ") AS job_locations
        ');
    
        // ✅ Use correct table names (as per your DB)
        $this->db->from('tb_post_job p');
        $this->db->join('tb_employer e', 'e.employer_id = p.employer_id', 'inner');
        $this->db->join('tb_job_cities jc', 'jc.job_id = p.job_id', 'left');
        $this->db->join('tb_cities c', 'c.city_id = jc.city_id', 'left');
    
        // ✅ Base conditions
        $this->db->where('p.status', 'active');
        $this->db->where('e.status', 'active');
        $this->db->where('p.job_id !=', (int)$job['job_id']);
    
        // 🔍 Apply keyword matching ONLY if valid keywords exist
        if (!empty($validKeywords)) {
            $this->db->group_start();
            foreach ($validKeywords as $word) {
                $this->db->or_like('p.job_title', $word);
            }
            $this->db->group_end();
        }
    
        // 📊 Group + Order
        $this->db->group_by('p.job_id');
        $this->db->order_by('p.created_at', 'DESC');
    
        // 🔢 Limit
        $this->db->limit($limit, $offset);
    
        // 🚀 Execute
        $query = $this->db->get();
    
        return $query ? $query->result_array() : [];
    }

	
	//use in candidate dashboard 
	public function getRecommendedJobs($userDesignations = null, $limit = 9, $offset = 0) {
		$this->db->select('
			p.job_id,
			p.job_title,
			p.slug,
			p.job_type,
			p.min_salary,
			p.max_salary,
			p.min_experience,
			p.max_experience,
			p.created_at,
			e.logo,
			e.company_name,
			GROUP_CONCAT(DISTINCT c.city_name ORDER BY c.city_name SEPARATOR ", ") AS job_locations,
			GROUP_CONCAT(DISTINCT js.skill_name ORDER BY js.skill_name SEPARATOR ", ") AS skills
		');

		$this->db->from('post_job as p');
		$this->db->join('employer as e', 'e.employer_id = p.employer_id', 'inner');
		$this->db->join('job_cities as jc', 'jc.job_id = p.job_id', 'left');
		$this->db->join('cities as c', 'c.city_id = jc.city_id', 'left');
		$this->db->join('job_skills as js', 'js.job_id = p.job_id', 'left');

		$this->db->where('p.status', 'active');
		$this->db->where('e.status', 'active');

		// Personalization by designations
		if (!empty($userDesignations)) {

			$designations = is_string($userDesignations)
				? array_map('trim', explode(',', $userDesignations))
				: (array)$userDesignations;

			if (!empty($designations)) {
				$this->db->group_start();
				foreach ($designations as $designation) {
					$clean = trim($designation);
					if (!empty($clean)) {
						$this->db->or_like('p.job_title', $clean);
						$this->db->or_like('js.skill_name', $clean);
					}
				}
				$this->db->group_end();
			}
		}

		$this->db->group_by('p.job_id');
		$this->db->order_by('p.created_at', 'DESC');
		$this->db->limit($limit, $offset);

		$query = $this->db->get();
		return $query ? $query->result_array() : [];
	}

    
    public function get_top_companies($limit = 10) {
		$this->db->select('E.company_name, E.logo, J.positions_open');
		$this->db->from('employer as E');
		$this->db->join('post_job as J', 'E.employer_id = J.employer_id', 'inner');

		$this->db->where('J.positions_open IS NOT NULL', null, false);
		$this->db->where("J.positions_open != ''", null, false);
		//$this->db->where('J.deadline_date >=', date('Y-m-d')); // ✅ exclude expired jobs

		$this->db->group_by('E.company_name');
		$this->db->order_by('J.created_at', 'DESC');
		$this->db->limit($limit);

		$query = $this->db->get();
		return $query ? $query->result_array() : [];
	}

		
	public function getJobsByCompany($company_id, $limit, $offset = 0) {
		$this->db->select([
			'j.job_id',
			'j.job_title',
			'j.slug',
			'j.min_experience',
			'j.max_experience',
			'j.min_salary',
			'j.max_salary',
			'j.salary_type',
			'j.created_at',
			'j.job_type',
			'e.company_name',
			'e.company_type',
			'e.logo AS employer_logo',
			'GROUP_CONCAT(DISTINCT c.city_name ORDER BY c.city_name SEPARATOR ", ") as city_names'
		]);
		$this->db->from('post_job j');
		$this->db->join('tb_employer e', 'j.employer_id = e.employer_id', 'left');
		$this->db->join('tb_job_cities jc', 'jc.job_id = j.job_id', 'left');
		$this->db->join('tb_cities c', 'c.city_id = jc.city_id', 'left');
		$this->db->where('j.employer_id', $company_id);
		$this->db->where('j.status', 'active');
		//$this->db->where('j.deadline_date >=', date('Y-m-d')); // ✅ exclude expired jobs
		$this->db->group_by('j.job_id');
		$this->db->order_by('j.created_at', 'DESC');
		$this->db->limit($limit, $offset);

		$query = $this->db->get();
		return $query->num_rows() > 0 ? $query->result_array() : [];
	}


	public function getJobsGroupedByDepartment($company_id) {
		$this->db->select('j.industry_id, i.industry_name as department, COUNT(*) as total_openings');
		$this->db->from('post_job j');
		$this->db->join('tb_industry i', 'j.industry_id = i.industry_id', 'left');
		$this->db->where('j.status', 'active');
		$this->db->where('j.employer_id', $company_id);
		//$this->db->where('j.deadline_date >=', date('Y-m-d')); // ✅ exclude expired jobs
		$this->db->group_by('j.industry_id');
		$this->db->order_by('total_openings', 'DESC');

		$query = $this->db->get();
		return $query ? $query->result_array() : [];
	}
	
	public function get_active_jobs($limit, $offset) {
		$sql = "
			SELECT 
				j.job_id, 
				j.employer_id,
				j.job_title,
				j.slug,
				j.min_experience,
				j.max_experience,
				j.updated_at,
				e.company_name,
				GROUP_CONCAT(c.city_name ORDER BY c.city_name ASC) AS job_locations
			FROM tb_post_job j
			LEFT JOIN tb_employer e ON e.employer_id = j.employer_id
			LEFT JOIN tb_job_cities jc ON jc.job_id = j.job_id
			LEFT JOIN tb_cities c ON c.city_id = jc.city_id
			WHERE j.status = 'active'
			  AND j.is_deleted = 0
			GROUP BY j.job_id
			ORDER BY j.updated_at DESC, j.job_id DESC
			LIMIT ? OFFSET ?
		";

		return $this->db->query($sql, [$limit, $offset])->result_array();
	}



    // Count total active jobs
    public function count_active_jobs() {
        return $this->db->where('status','active')->from('post_job')->count_all_results();
    }
	//site map use end
    
    
    /**
     * जॉब का व्यू काउंट बढ़ाएँ (गेस्ट के लिए)
     *
     * @param int $job_id
     * @return void
     */
    public function increment_job_view($job_id)
    {
        $this->db->query("
            INSERT INTO tb_job_views (job_id, total_views, last_viewed)
            VALUES (?, 1, NOW())
            ON DUPLICATE KEY UPDATE
                total_views = total_views + 1,
                last_viewed = NOW()
        ", [$job_id]);
    }
    
    
}
