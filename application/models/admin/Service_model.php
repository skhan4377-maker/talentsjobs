<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Service_model extends CI_Model {

    protected $table = 'tb_ft_services';

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    public function get_all_services() {
        $this->db->order_by('is_active', 'DESC');
        $this->db->order_by('service_name', 'ASC');
        return $this->db->get($this->table)->result_array();
    }

    public function get_active_services() {
        $this->db->where('is_active', 1);
        $this->db->order_by('service_name', 'ASC');
        return $this->db->get($this->table)->result_array();
    }

    public function insert_service($data) {
        return $this->db->insert($this->table, $data);
    }

    public function get_service_by_id($id) {
        return $this->db->get_where($this->table, array('service_id' => $id))->row_array();
    }

    public function update_service($id, $data) {
        $this->db->where('service_id', $id);
        return $this->db->update($this->table, $data);
    }

    public function toggle_status($id) {
        $service = $this->get_service_by_id($id);
        if ($service) {
            $new_status = $service['is_active'] == 1 ? 0 : 1;
            return $this->db->update($this->table, 
                array('is_active' => $new_status, 'update_dt' => date('Y-m-d H:i:s')), 
                array('service_id' => $id)
            );
        }
        return false;
    }
}