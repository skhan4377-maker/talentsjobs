<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ServicesFeatures_model extends CI_Model {

    protected $services_table = 'tb_ft_services';
    protected $features_table = 'tb_ft_features';
    protected $tags_table = 'tb_ft_tags';
    protected $plans_table = 'tb_ft_plans';
    protected $benefit_headers_table = 'tb_ft_benefit_headers';
    protected $benefit_comparisons_table = 'tb_ft_benefit_comparisons';
    protected $qas_table = 'tb_ft_qas';

    // Services
    // Services
	public function get_all_services($limit = null, $offset = 0) {
		$this->db->where('is_active', 1)
				 ->order_by('create_dt', 'DESC');
		
		if ($limit !== null) {
			$this->db->limit($limit, $offset);
		}
		
		return $this->db->get($this->services_table)->result_array();
	}

    public function get_service_by_id($id) {
        return $this->db->where('service_id', $id)
                        ->get($this->services_table)
                        ->row_array();
    }
	
	// In ServicesFeatures_model.php

	public function get_feature_by_slug($slug) {
		return $this->db->where('slug', $slug)
						->where_in('is_active', ['active', 'upcoming'])
						->get($this->features_table)
						->row_array();
	}

    // Features
    public function get_features_by_service($service_id) {
        return $this->db->where('service_id', $service_id)
                        ->where_in('is_active', ['active', 'upcoming'])
                        ->order_by('create_dt', 'DESC')
                        ->get($this->features_table)
                        ->result_array();
    }

    public function get_feature_by_id($id) {
        return $this->db->where('feature_id', $id)
                        ->get($this->features_table)
                        ->row_array();
    }

    // Tags
    public function get_feature_tags($feature_id) {
        return $this->db->where('feature_id', $feature_id)
                        ->where('is_active', 'yes')
                        ->order_by('tag_order', 'ASC')
                        ->get($this->tags_table)
                        ->result_array();
    }

    // Plans
    public function get_feature_plans($feature_id) {
        return $this->db->where('feature_id', $feature_id)
                        ->order_by("FIELD(duration, '1 Month', '2 Months', '3 Months', '6 Months', 'Annual')", '', false)
                        ->get($this->plans_table)
                        ->result_array();
    }

    // Benefit Headers & Comparisons
    public function get_feature_benefit_headers($feature_id) {
        return $this->db->where('feature_id', $feature_id)
                        ->get($this->benefit_headers_table)
                        ->row_array();
    }

    public function get_feature_benefit_comparisons($feature_id) {
        return $this->db->where('feature_id', $feature_id)
                        ->get($this->benefit_comparisons_table)
                        ->result_array();
    }

    // Q&A
    public function get_feature_qas($feature_id) {
        return $this->db->where('feature_id', $feature_id)
                        ->order_by('created_at', 'ASC')
                        ->get($this->qas_table)
                        ->result_array();
    }
}