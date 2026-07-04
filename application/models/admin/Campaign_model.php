<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Campaign_model extends CI_Model {

    protected $table = 'tb_campaigns';

    public function __construct() {
        parent::__construct();
    }

    // ------------------- READ (only non‑deleted) -------------------
    public function get_all() {
        $this->db->where('deleted_at IS NULL');
        $this->db->order_by('id', 'DESC');
        return $this->db->get($this->table)->result();
    }

    public function get($id) {
        $this->db->where('deleted_at IS NULL');
        return $this->db->get_where($this->table, ['id' => $id])->row();
    }

    // ------------------- WRITE (unchanged) -------------------
    public function insert($data) {
        $this->db->insert($this->table, $data);
        return $this->db->insert_id();
    }

    public function update($id, $data) {
        // Only update non‑deleted campaigns
        $this->db->where('id', $id);
        $this->db->where('deleted_at IS NULL');
        return $this->db->update($this->table, $data);
    }

    // ------------------- SOFT DELETE -------------------
    public function delete($id) {
        $this->db->where('id', $id);
        $this->db->where('deleted_at IS NULL');
        return $this->db->update($this->table, ['deleted_at' => date('Y-m-d H:i:s')]);
    }

    // ------------------- RESTORE (optional) -------------------
    public function restore($id) {
        $this->db->where('id', $id);
        return $this->db->update($this->table, ['deleted_at' => NULL]);
    }

    // ------------------- HARD DELETE (optional, for purging) -------------------
    public function force_delete($id) {
        $this->db->where('id', $id);
        return $this->db->delete($this->table);
    }

    // ------------------- FILTERED LIST (soft‑delete aware) -------------------
    public function get_filtered($filters = [], $page = 1, $per_page = 10) {
        $this->db->start_cache();

        // Always exclude soft‑deleted campaigns
        $this->db->where('c.deleted_at IS NULL');

        if (!empty($filters['search'])) {
            $this->db->like('c.title', $filters['search']);
        }
        if (!empty($filters['status'])) {
            $this->db->where('c.status', $filters['status']);
        }
        if (!empty($filters['start_from'])) {
            $this->db->where('c.start_date >=', $filters['start_from'] . ' 00:00:00');
        }
        if (!empty($filters['start_to'])) {
            $this->db->where('c.start_date <=', $filters['start_to'] . ' 23:59:59');
        }

        $this->db->from($this->table . ' c');
        $this->db->stop_cache();

        $total_rows = $this->db->count_all_results();

        $offset = ($page - 1) * $per_page;
        $this->db->select("
            c.*,
            COALESCE(q.total_leads, 0) as total_leads,
            COALESCE(q.pending, 0) as pending,
            COALESCE(q.sent, 0) as sent,
            COALESCE(q.failed, 0) as failed
        ");
        $this->db->join("(
            SELECT campaign_id,
                COUNT(*) as total_leads,
                SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
                SUM(CASE WHEN status = 'sent' THEN 1 ELSE 0 END) as sent,
                SUM(CASE WHEN status = 'failed' THEN 1 ELSE 0 END) as failed
            FROM tb_campaign_queue
            GROUP BY campaign_id
        ) q", 'q.campaign_id = c.id', 'left', FALSE);
        $this->db->order_by('c.id', 'DESC');
        $this->db->limit($per_page, $offset);
        $campaigns = $this->db->get()->result();

        $this->db->flush_cache();

        return [
            'campaigns'  => $campaigns,
            'total_rows' => $total_rows
        ];
    }
}