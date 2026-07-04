<?php
class Application_model extends CI_Model {

    private $resume_builder_feature_id;

    public function __construct() {
        parent::__construct();
        $this->resume_builder_feature_id = $this->get_resume_builder_feature_id();
    }

    /**
     * Get the feature ID for the resume builder (by slug).
     * FIXED: use correct table name tb_ft_features
     */
    private function get_resume_builder_feature_id() {
        $this->db->select('feature_id');
        $this->db->from('tb_ft_features'); 
        $this->db->where('slug', 'resume-builder');
        $row = $this->db->get()->row();
        return $row ? (int) $row->feature_id : 0;
    }

    /**
     * Count total applications (with filters).
     */
    public function count_applications($employer_id, $job_id = null, $search = null, $status = null, $start_date = null, $end_date = null) {
        $this->db->from('tb_applied a');
        $this->db->join('tb_post_job j', 'a.job_id = j.job_id');
        $this->db->join('tb_candidate c', 'a.candidate_id = c.candidate_id');
        $this->db->where('j.employer_id', $employer_id);

        if ($job_id) {
            $this->db->where('a.job_id', $job_id);
        }
        if (!empty($status)) {
            $this->db->where('a.ApplicationStage', $status);
        }
        if (!empty($search)) {
            $this->db->group_start();
            $this->db->like('c.name', $search);
            $this->db->or_like('c.email', $search);
            $this->db->or_like('c.resume_headline', $search);
            $this->db->group_end();
        }
        if (!empty($start_date)) {
            $this->db->where('DATE(a.created_at) >=', $start_date);
        }
        if (!empty($end_date)) {
            $this->db->where('DATE(a.created_at) <=', $end_date);
        }

        return $this->db->count_all_results();
    }

    /**
     * Get applications (with filters, pagination, and active plan flag).
     * FIXED: use tb_ft_user_purchases instead of tb_user_plans
     */
    public function get_applications($employer_id, $job_id = null, $limit = null, $offset = 0, $search = null, $status = null, $start_date = null, $end_date = null)
	{
		// अब feature_id की ज़रूरत नहीं, किसी भी active purchase को ऊपर लाएँगे
		$this->db->select("
			a.applied_id, a.ApplicationStage, a.created_at,
			j.job_title, j.job_id,
			c.candidate_id, c.name, c.last_name, c.email, c.mobile,
			c.resume, c.logo, c.resume_headline,
			(
				SELECT EXISTS (
					SELECT 1
					FROM tb_ft_user_purchases up
					WHERE up.user_id = c.candidate_id
					  AND up.status = 'active'
					  AND up.start_date <= NOW()
					  AND up.end_date >= NOW()
				)
			) AS has_active_plan
		");

		$this->db->from('tb_applied a');
		$this->db->join('tb_post_job j', 'a.job_id = j.job_id');
		$this->db->join('tb_candidate c', 'a.candidate_id = c.candidate_id');
		$this->db->where('j.employer_id', $employer_id);

		if ($job_id) {
			$this->db->where('a.job_id', $job_id);
		}
		if (!empty($status)) {
			$this->db->where('a.ApplicationStage', $status);
		}
		if (!empty($search)) {
			$this->db->group_start();
			$this->db->like('c.name', $search);
			$this->db->or_like('c.email', $search);
			$this->db->or_like('c.resume_headline', $search);
			$this->db->group_end();
		}
		if (!empty($start_date)) {
			$this->db->where('DATE(a.created_at) >=', $start_date);
		}
		if (!empty($end_date)) {
			$this->db->where('DATE(a.created_at) <=', $end_date);
		}

		// पहले active plan वाले ऊपर, फिर तारीख के हिसाब से
		$this->db->order_by('has_active_plan', 'DESC');
		$this->db->order_by('a.created_at', 'DESC');

		if ($limit) {
			$this->db->limit($limit, $offset);
		}

		return $this->db->get()->result_array();
	}

   public function get_job_applications($job_id, $limit = null, $offset = null)
	{
		$this->db->select("
			a.applied_id,
			a.job_id,
			a.ApplicationStage,
			a.created_at as applied_date,
			c.candidate_id,
			c.name,
			c.last_name,
			c.email,
			c.mobile,
			c.resume,
			city.city_name,
			(
				SELECT EXISTS (
					SELECT 1
					FROM tb_ft_user_purchases up
					WHERE up.user_id = c.candidate_id
					  AND up.status = 'active'
					  AND up.start_date <= NOW()
					  AND up.end_date >= NOW()
				)
			) AS has_active_plan
		");

		$this->db->from('tb_applied a');
		$this->db->join('tb_candidate c', 'a.candidate_id = c.candidate_id');
		$this->db->join('tb_cities city', 'city.city_id = c.city_id', 'left');
		$this->db->where('a.job_id', $job_id);
		$this->db->order_by('has_active_plan', 'DESC');
		$this->db->order_by('a.created_at', 'DESC');

		if ($limit) {
			$this->db->limit($limit, $offset);
		}

		return $this->db->get()->result_array();
	}
	
	
	/**
		 * दिए गए candidate_ids के लिए सभी active फ़ीचर की जानकारी (slug और label)
		 *
		 * @param array $candidate_ids
		 * @return array   [candidate_id => [ ['feature_id'=>.., 'slug'=>.., 'label'=>..], ... ] ]
		 */
		public function get_active_features_for_candidates(array $candidate_ids)
		{
			if (empty($candidate_ids)) {
				return [];
			}

			$this->db->select('up.user_id, f.feature_id, f.slug, f.feature_name AS label')
				->from('tb_ft_user_purchases up')
				->join('tb_ft_features f', 'up.feature_id = f.feature_id')
				->where('up.status', 'active')
				->where('up.start_date <=', date('Y-m-d H:i:s'))
				->where('up.end_date >=', date('Y-m-d H:i:s'))
				->where_in('up.user_id', $candidate_ids);
			$rows = $this->db->get()->result_array();

			$result = [];
			foreach ($rows as $row) {
				$uid = $row['user_id'];
				unset($row['user_id']);
				$result[$uid][] = $row;
			}

			return $result;
		}

    /**
     * Get a single application with candidate details.
     * FIXED: use tb_ft_user_purchases
     */
    public function get_application_by_id($applied_id, $employer_id) {
        $feature_id = (int) $this->resume_builder_feature_id;

        $subquery = "(
            SELECT EXISTS (
                SELECT 1
                FROM tb_ft_user_purchases up
                WHERE up.user_id = c.candidate_id
                  AND up.feature_id = {$feature_id}
                  AND up.status = 'active'
                  AND up.start_date <= NOW()
                  AND up.end_date >= NOW()
            )
        ) AS has_active_plan";

        $this->db->select("
            a.*, 
            c.*, 
            j.job_title, 
            j.employer_id, 
            ind.industry_name, 
            fn.functional_area, 
            ct.city_name,
            e.company_name AS employer_company_name,
            e.logo AS employer_logo,
            e.email AS employer_email,
            {$subquery}
        ");
        $this->db->from('tb_applied a');
        $this->db->join('tb_candidate c', 'c.candidate_id = a.candidate_id');
        $this->db->join('tb_post_job j', 'j.job_id = a.job_id');
        $this->db->join('tb_employer e', 'e.employer_id = j.employer_id');
        $this->db->join('tb_industry ind', 'ind.industry_id = c.industry_id', 'left');
        $this->db->join('tb_functional_area fn', 'fn.functional_id = c.functional_id', 'left');
        $this->db->join('tb_cities ct', 'ct.city_id = c.city_id', 'left');
        $this->db->where([
            'a.applied_id'    => $applied_id,
            'j.employer_id'   => $employer_id
        ]);
        $application = $this->db->get()->row_array();

        if (!$application || empty($application['candidate_id'])) {
            return null;
        }

        $candidate_id = (int) $application['candidate_id'];

        // Education
        $application['education'] = $this->db
            ->order_by('endYear', 'DESC')
            ->limit(3)
            ->get_where('tb_education_history', ['candidate_id' => $candidate_id])
            ->result_array();

        // Employment
        $application['employment_details'] = $this->db
            ->order_by('start_date', 'DESC')
            ->limit(3)
            ->get_where('tb_employment_history', ['candidate_id' => $candidate_id])
            ->result_array();

        // Skills
        $skills = $this->db->select('skill_name')
            ->from('tb_candidate_skills')
            ->where('candidate_id', $candidate_id)
            ->get()
            ->result_array();
        $application['skills'] = array_column($skills, 'skill_name');

        // Preferred locations
        $locations = $this->db->select('c.city_name')
            ->from('tb_candidate_preferred_locations pl')
            ->join('tb_cities c', 'c.city_id = pl.city_id')
            ->where('pl.candidate_id', $candidate_id)
            ->get()
            ->result_array();
        $application['preferred_locations'] = array_column($locations, 'city_name');

        return $application;
    }

    /**
     * Get application timeline logs.
     */
    public function get_application_timeline($applied_id) {
        return $this->db
            ->select('log_id, stage, performed_by, created_at')
            ->from('tb_application_logs')
            ->where('application_id', (int)$applied_id)
            ->order_by('created_at', 'ASC')
            ->get()
            ->result_array();
    }

    /**
     * Verify job ownership.
     */
    public function get_job($job_id, $employer_id) {
        return $this->db->get_where('tb_post_job', [
            'job_id' => $job_id,
            'employer_id' => $employer_id
        ])->row_array();
    }
}