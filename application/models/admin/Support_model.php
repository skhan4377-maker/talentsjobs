<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Support_model extends CI_Model {

    private $table = 'tb_support_contacts';

    public function get_paginated($limit, $offset, $search = '') {
        $this->db->from($this->table);
        if (!empty($search)) {
            $this->db->group_start();
            $this->db->like('name', $search);
            $this->db->or_like('email', $search);
            $this->db->or_like('subject', $search);
            $this->db->or_like('message', $search);
            $this->db->group_end();
        }
        $this->db->order_by('submitted_at', 'DESC');
        $this->db->limit($limit, $offset);
        $result = $this->db->get()->result();
        return $this->format_enquiries($result);
    }

    public function count_filtered($search = '') {
        $this->db->from($this->table);
        if (!empty($search)) {
            $this->db->group_start();
            $this->db->like('name', $search);
            $this->db->or_like('email', $search);
            $this->db->or_like('subject', $search);
            $this->db->or_like('message', $search);
            $this->db->group_end();
        }
        return $this->db->count_all_results();
    }

    public function get_by_id($id) {
        return $this->db->get_where($this->table, ['id' => $id])->row();
    }

    private function format_enquiries($enquiries) {
        foreach ($enquiries as $row) {
            $row->has_reply = (!empty($row->reply_message) && !empty($row->replied_at));
        }
        return $enquiries;
    }
}
?>