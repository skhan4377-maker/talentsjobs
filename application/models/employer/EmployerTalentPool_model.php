<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class EmployerTalentPool_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    /**
     * Count total matching candidates (without limit, for pagination)
     */
    public function count_candidates($filters = []) {
        $this->db->from('tb_candidate c');

        // Only active candidates (no longer filtering by is_verified)
        $this->db->where('c.status', 'active');

        $this->apply_filters($filters, false);
        return $this->db->count_all_results();
    }

    public function get_candidates_paginated($filters = [], $limit = 15, $offset = 0) {
		$this->db->select("
			c.candidate_id,
			c.name,
			c.last_name,
			c.designations,
			c.logo AS profile_image,
			c.city_id,
			c.work_status,
			c.total_experience_years,
			c.total_experience_months,
			c.resume,
			c.email,
			c.mobile,
			c.about,
			c.is_verified,
			c.last_login,
			c.status,
			city.city_name,
			IF(fp.user_id IS NOT NULL, 1, 0) AS has_active_plan 
		", FALSE);

		$this->db->from('tb_candidate c');

		$this->db->join(
			'tb_cities city',
			'city.city_id = c.city_id',
			'left'
		);

		// Paid users join
		$this->db->join(
			'(
				SELECT DISTINCT user_id
				FROM tb_ft_user_purchases
				WHERE status = "active"
				AND start_date <= NOW()
				AND end_date >= NOW()
			) fp',
			'fp.user_id = c.candidate_id',
			'left',
			FALSE
		);

		// Active candidates only
		$this->db->where('c.status', 'active');

		// Apply filters
		$this->apply_filters($filters, true);

		// Skills filter
		if (!empty($filters['skills'])) {

			$skills = array_map('trim', explode(',', $filters['skills']));

			$this->db->join(
				'tb_candidate_skills cs',
				'cs.candidate_id = c.candidate_id',
				'inner'
			);

			$this->db->group_start();

			foreach ($skills as $skill) {
				$this->db->or_like('cs.skill_name', $skill);
			}

			$this->db->group_end();
		}

		// Ordering
		// 1. Paid users first
		// 2. Latest login
		// 3. Latest candidate id

		$this->db->order_by('has_active_plan', 'DESC');
		$this->db->order_by('c.last_login', 'DESC');
		$this->db->order_by('c.candidate_id', 'DESC');

		// Pagination
		if ($limit) {
			$this->db->limit($limit, $offset);
		}

		$query = $this->db->get();

		$candidates = $query->result_array();

		// Attach skills
		if (!empty($candidates)) {

			$candidate_ids = array_column($candidates, 'candidate_id');

			$skills_map = $this->get_skills_for_candidates($candidate_ids);

			foreach ($candidates as &$c) {
				$c['skills'] = $skills_map[$c['candidate_id']] ?? [];
			}
		}

		return $candidates;
	}
	
    /**
     * Apply common filters to query builder
     */
    private function apply_filters($filters, $use_like = true) {
        // Keywords
        if (!empty($filters['keywords'])) {
            $kw = $this->db->escape_like_str($filters['keywords']);
            $this->db->group_start();
            $this->db->like('c.name', $kw);
            $this->db->or_like('c.last_name', $kw);
            $this->db->or_like('c.email', $kw);
            $this->db->or_like('c.designations', $kw);
            $this->db->group_end();
        }

        // Experience
        if (!empty($filters['experience'])) {
            switch ($filters['experience']) {
                case '0-2':
                    $this->db->where('c.total_experience_years <=', 2);
                    break;
                case '3-5':
                    $this->db->where('c.total_experience_years >=', 3);
                    $this->db->where('c.total_experience_years <=', 5);
                    break;
                case '6+':
                    $this->db->where('c.total_experience_years >=', 6);
                    break;
            }
        }

        // Location
        if (!empty($filters['location'])) {
            $this->db->like('city.city_name', $filters['location']);
        }

        // Job type
        if (!empty($filters['job_type'])) {
            $this->db->where('LOWER(c.work_status)', strtolower($filters['job_type']));
        }
    }

    /**
     * Fetch skills for multiple candidates
     */
    private function get_skills_for_candidates($candidate_ids) {
        if (empty($candidate_ids)) return [];

        $this->db->select('candidate_id, skill_name')
            ->from('tb_candidate_skills')
            ->where_in('candidate_id', $candidate_ids);

        $rows = $this->db->get()->result_array();
        $map = [];
        foreach ($rows as $row) {
            $map[$row['candidate_id']][] = $row['skill_name'];
        }
        return $map;
    }

    /**
     * Get full candidate details for drawer – no premium subquery
     */
    public function get_candidate_details($candidate_id) {
        $this->db->select("
            c.*, city.city_name,
            ind.industry_name,
            fn.functional_area
        ");
        $this->db->from('tb_candidate c');
        $this->db->join('tb_cities city', 'city.city_id = c.city_id', 'left');
        $this->db->join('tb_industry ind', 'ind.industry_id = c.industry_id', 'left');
        $this->db->join('tb_functional_area fn', 'fn.functional_id = c.functional_id', 'left');
        $this->db->where('c.candidate_id', $candidate_id);
        $candidate = $this->db->get()->row_array();

        if (!$candidate) return null;

        // Education
        $candidate['education_history'] = $this->db
            ->order_by('endYear', 'DESC')
            ->get_where('tb_education_history', ['candidate_id' => $candidate_id])
            ->result_array();

        // Employment
        $candidate['employment_history'] = $this->db
            ->order_by('start_date', 'DESC')
            ->get_where('tb_employment_history', ['candidate_id' => $candidate_id])
            ->result_array();

        // Skills
        $skills = $this->db
            ->select('skill_name')
            ->get_where('tb_candidate_skills', ['candidate_id' => $candidate_id])
            ->result_array();
        $candidate['skills'] = array_column($skills, 'skill_name');

        // Preferred locations
        $locations = $this->db
            ->select('c.city_name')
            ->from('tb_candidate_preferred_locations pl')
            ->join('tb_cities c', 'c.city_id = pl.city_id')
            ->where('pl.candidate_id', $candidate_id)
            ->get()
            ->result_array();
        $candidate['preferred_locations'] = array_column($locations, 'city_name');

        return $candidate;
    }
}