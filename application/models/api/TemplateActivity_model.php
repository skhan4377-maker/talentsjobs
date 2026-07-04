<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class TemplateActivity_model extends CI_Model {

    // Corrected table names
    protected $template_usage_table = 'tb_ft_template_usage';
    protected $resume_downloads_table = 'tb_ft_resume_downloads';

    /**
     * Log template usage (view/preview) with duplicate prevention
     * Prevents multiple logs for same user/template/IP on the same day
     */
    public function log_template_usage($data) {
        $user_id = $data['user_id'] ?? null;
        $template_id = $data['template_id'];

        // Check for existing record (same user/template/IP today)
        $this->db->where('template_id', $template_id);
        if ($user_id) {
            $this->db->where('user_id', $user_id);
        } else {
            $this->db->where('ip_address', $this->input->ip_address());
        }
        $this->db->where('DATE(created_at)', date('Y-m-d'));
        $exists = $this->db->get($this->template_usage_table)->row();

        if (!$exists) {
            $this->db->insert($this->template_usage_table, [
                'user_id'     => $user_id,
                'template_id' => $template_id,
                'ip_address'  => $this->input->ip_address(),
                'user_agent'  => $this->input->user_agent(),
                'created_at'  => date('Y-m-d H:i:s')
            ]);
        }
    }

    /**
     * Log resume download with duplicate prevention
     * Prevents multiple logs for same user/template/IP/resume on the same day
     */
    public function log_resume_download($data) {
        $user_id = $data['user_id'] ?? null;
        $resume_id = $data['resume_id'] ?? null;
        $template_id = $data['template_id'];

        $this->db->where('template_id', $template_id);
        if ($user_id) {
            $this->db->where('user_id', $user_id);
        } else {
            $this->db->where('ip_address', $this->input->ip_address());
        }
        $this->db->where('DATE(downloaded_at)', date('Y-m-d'));
        $exists = $this->db->get($this->resume_downloads_table)->row();

        if (!$exists) {
            $this->db->insert($this->resume_downloads_table, [
                'user_id'       => $user_id,
                'resume_id'     => $resume_id,
                'template_id'   => $template_id,
                'ip_address'    => $this->input->ip_address(),
                'user_agent'    => $this->input->user_agent(),
                'downloaded_at' => date('Y-m-d H:i:s')
            ]);
        }
    }
}
?>