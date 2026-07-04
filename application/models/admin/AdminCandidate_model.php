<?php
class AdminCandidate_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    public function get_all_industries() {
        return $this->db->select('industry_id, industry_name')
                        ->from('tb_industry')
                        ->order_by('industry_name', 'ASC')
                        ->get()->result_array();
    }

    public function count_filtered($filters, $created_from = '', $created_to = '') {
        $this->db->from('tb_candidate c');
        $this->db->join('tb_cities ci', 'ci.city_id = c.city_id', 'left');
        $this->_apply_filters($filters, $created_from, $created_to);
        return $this->db->count_all_results();
    }

    public function get_candidates_paginated($limit, $offset, $filters, $created_from = '', $created_to = '') {
        $this->db->select('c.*, ci.city_name');
        $this->db->from('tb_candidate c');
        $this->db->join('tb_cities ci', 'ci.city_id = c.city_id', 'left');
        $this->_apply_filters($filters, $created_from, $created_to);
        $this->db->order_by('c.created_at', 'DESC');
        $this->db->limit($limit, $offset);
        $query = $this->db->get();
        $result = $query->result_array();

        // Format each row for display
        foreach ($result as &$row) {
            $row['experience'] = $row['total_experience_years'] . 'y ' . $row['total_experience_months'] . 'm';
            $row['location']   = $row['city_name'] ?? '-';
            $row['created']    = timeAgo($row['created_at']);
            $row['status']     = ucfirst($row['status']);

            // Tailwind verified badge
            $verifiedBadge = $row['is_verified']
                ? '<span class="ml-1 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">Verified</span>'
                : '<span class="ml-1 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800">Not Verified</span>';
            $row['email'] = $row['email'] . ' ' . $verifiedBadge;

            // Tailwind view button
            $actions = '';
            $actions .= '<a href="#" class="inline-block px-3 py-1 text-xs font-medium text-blue-600 border border-blue-600 rounded hover:bg-blue-600 hover:text-white transition view-candidate" data-id="' . $row['candidate_id'] . '">View</a>';
            $row['actions'] = $actions;
        }
        return $result;
    }

    private function _apply_filters($filters, $created_from, $created_to) {
        if (!empty($filters['name'])) {
            $this->db->like('c.name', $filters['name']);
        }
        if (!empty($filters['email'])) {
            $this->db->like('c.email', $filters['email']);
        }
        if (!empty($filters['mobile'])) {
            $this->db->like('c.mobile', $filters['mobile']);
        }
        if (!empty($filters['status'])) {
            $this->db->where('c.status', $filters['status']);
        }
        if ($filters['is_verified'] !== '') {
            $this->db->where('c.is_verified', (int)$filters['is_verified']);
        }
        if (!empty($filters['industry_id'])) {
            $this->db->where('c.industry_id', $filters['industry_id']);
        }
        if (!empty($created_from)) {
            $this->db->where('c.created_at >=', $created_from . ' 00:00:00');
        }
        if (!empty($created_to)) {
            $this->db->where('c.created_at <=', $created_to . ' 23:59:59');
        }
    }

    public function get_candidate($id) {
        $this->db->select('c.*, ci.city_name, ind.industry_name, co.country_name');
        $this->db->from('tb_candidate c');
        $this->db->join('tb_cities ci', 'ci.city_id = c.city_id', 'left');
        $this->db->join('tb_industry ind', 'ind.industry_id = c.industry_id', 'left');
        $this->db->join('countries co', 'co.country_id = c.country_id', 'left');
        $this->db->where('c.candidate_id', $id);
        $candidate = $this->db->get()->row_array();
        if (!$candidate) return null;

        $skills = $this->db->select('skill_name')
            ->from('tb_candidate_skills')
            ->where('candidate_id', $id)
            ->get()->result_array();
        $candidate['skills'] = array_column($skills, 'skill_name');

        $locations = $this->db->select('ci.city_name')
            ->from('tb_candidate_preferred_locations pl')
            ->join('tb_cities ci', 'ci.city_id = pl.city_id', 'left')
            ->where('pl.candidate_id', $id)
            ->get()->result_array();
        $candidate['preferred_locations'] = array_column($locations, 'city_name');

        return $candidate;
    }
}