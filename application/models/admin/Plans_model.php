<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Plans_model extends CI_Model {

    protected $table = 'tb_ft_plans';

    public function __construct() {
        parent::__construct();
    }

    // Get all plans with feature names (for listing)
    public function get_all_plans() {
        $this->db->select('p.*, f.feature_name, f.feature_tag, f.service_id');
        $this->db->from($this->table . ' p');
        $this->db->join('tb_ft_features f', 'p.feature_id = f.feature_id', 'left');
        $this->db->order_by('p.created_at', 'DESC');
        return $this->db->get()->result_array();
    }

    // Get plans by feature_id
    public function get_plans_by_feature($feature_id) {
        $this->db->where('feature_id', $feature_id);
        $this->db->order_by('FIELD(duration, "1 Month", "2 Months", "3 Months", "6 Months", "Annual")');
        return $this->db->get($this->table)->result_array();
    }

    // Get single plan by ID
    public function get_plan_by_id($id) {
        return $this->db->get_where($this->table, ['duration_id' => $id])->row_array();
    }

    // Insert plan
    public function insert_plan($data) {
        $this->db->insert($this->table, $data);
        return $this->db->insert_id();
    }

    // Update plan
    public function update_plan($id, $data) {
        $this->db->where('duration_id', $id);
        return $this->db->update($this->table, $data);
    }

    // Delete plan
    public function delete_plan($id) {
        $this->db->where('duration_id', $id);
        return $this->db->delete($this->table);
    }

    // Check if a plan already exists for same feature and duration
    public function plan_exists($feature_id, $duration, $exclude_id = null) {
        $this->db->where('feature_id', $feature_id);
        $this->db->where('duration', $duration);
        if ($exclude_id) {
            $this->db->where('duration_id !=', $exclude_id);
        }
        return $this->db->get($this->table)->num_rows() > 0;
    }
}
?>