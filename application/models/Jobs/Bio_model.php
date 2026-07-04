<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Bio_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    public function get_all() {
        $this->db->order_by('created_at', 'DESC');
        return $this->db->get('bio')->result();
    }

    public function insert($data) {
        $this->db->insert('bio', $data);
        return $this->db->insert_id();
    }

    public function get($id) {
        return $this->db->get_where('bio', ['id' => $id])->row();
    }

    public function get_by_slug($slug) {
        return $this->db->get_where('bio', ['slug' => $slug])->row();
    }

    public function update($id, $data) {
        $this->db->where('id', $id);
        return $this->db->update('bio', $data);
    }

    public function delete($id) {
        $this->db->where('id', $id);
        return $this->db->delete('bio');
    }

    public function slug_exists($slug, $exclude_id = 0) {
        $this->db->where('slug', $slug);
        if ($exclude_id) {
            $this->db->where('id !=', $exclude_id);
        }
        return $this->db->get('bio')->num_rows() > 0;
    }

    public function get_paginated($limit, $offset) {
        $this->db->order_by('created_at', 'DESC');
        $this->db->limit($limit, $offset);
        return $this->db->get('bio')->result();
    }

    public function count_all() {
        return $this->db->count_all('bio');
    }
}