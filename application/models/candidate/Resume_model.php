<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Resume_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    // Get resumes + download count
    public function get_resumes_by_candidate($user_id) {
        $this->db->select('
            d.draft_id,
            d.user_id,
            d.template_id,
            d.form_data,
            d.is_finalized,
            d.updated_at,
            t.name as template_name,
            t.preview_image,
            COUNT(rd.id) as download_count
        ');
        $this->db->from('tb_ft_resume_drafts d');
        $this->db->join('tb_ft_resume_templates t', 't.template_id = d.template_id', 'left');
        $this->db->join('tb_ft_resume_downloads rd', 'rd.resume_id = d.draft_id', 'left');

        $this->db->where('d.user_id', $user_id);
        $this->db->where('d.is_deleted', 0);

        $this->db->group_by('d.draft_id');
        $this->db->order_by('d.updated_at', 'DESC');

        return $this->db->get()->result_array();
    }

    // total downloads
    public function get_total_downloads($user_id) {
        $this->db->select('COUNT(rd.id) as total');
        $this->db->from('tb_ft_resume_downloads rd');
        $this->db->join('tb_ft_resume_drafts d', 'd.draft_id = rd.resume_id');
        $this->db->where('d.user_id', $user_id);

        $row = $this->db->get()->row();
        return $row ? $row->total : 0;
    }

    // delete
    /*public function delete_draft($id, $user_id) {
        return $this->db->where([
            'draft_id' => $id,
            'user_id' => $user_id
        ])->update('tb_ft_resume_drafts', ['is_deleted' => 1]);
    }*/

    /**
     * Check if user has an active premium Resume Builder plan
     * Looks up feature_id dynamically by slug (resume-builder-profile-boost)
     */
    public function has_active_plan($user_id) {
        // Get feature_id of the paid resume builder plan using its slug
        $feature = $this->db
            ->select('feature_id')
            ->where('slug', 'resume-builder-profile-boost')
            ->get('tb_ft_features')
            ->row();

        if (!$feature) {
            return false;   // feature not found
        }

        // Check for any active purchase of that feature
        return $this->db
            ->where('user_id', $user_id)
            ->where('feature_id', $feature->feature_id)
            ->where('status', 'active')
            ->where('end_date >', date('Y-m-d H:i:s'))
            ->count_all_results('tb_ft_user_purchases') > 0;
    }
}