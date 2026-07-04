<?php
class Features_model extends CI_Model {

    public function get_all_services() {
        return $this->db->where('is_active', 1)->get('tb_ft_services')->result_array();
    }

    public function get_all_features() {
        $this->db->select('f.*, s.service_name');
        $this->db->from('tb_ft_features f');
        $this->db->join('tb_ft_services s', 'f.service_id = s.service_id', 'left');
        $this->db->order_by('f.create_dt', 'DESC');
        return $this->db->get()->result_array();
    }

    public function insert_feature($data) {
        $this->db->insert('tb_ft_features', $data);
        return $this->db->insert_id();
    }

    public function update_feature($feature_id, $data) {
        $this->db->where('feature_id', $feature_id);
        return $this->db->update('tb_ft_features', $data);
    }

    public function get_tags($feature_id) {
        return $this->db->where('feature_id', $feature_id)->get('tb_ft_tags')->result_array();
    }

    public function get_qas($feature_id) {
        return $this->db->where('feature_id', $feature_id)->get('tb_ft_qas')->result_array();
    }

    public function get_benefit_header($feature_id) {
        return $this->db->where('feature_id', $feature_id)->get('tb_ft_benefit_headers')->row_array();
    }

    public function get_benefit_comparisons($feature_id) {
        return $this->db->where('feature_id', $feature_id)->get('tb_ft_benefit_comparisons')->result_array();
    }
}