<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * IMPORTANT:
 * REST base controller manually load
 */
require APPPATH . 'libraries/MY_REST_Controller.php';

class Dashboard_api extends MY_REST_Controller
{
    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    /**
     * GET: /api/dashboard
     * Logged-in user ka dashboard stats
     */
    public function stats_get() {

        // 🔐 JWT se aaya hua user_id
        $user_id = $this->user_id;

        // Total resumes created (drafts) - using correct table name
        $this->db->where('user_id', $user_id);
        $resumes_count = $this->db->count_all_results('tb_ft_resume_drafts');

        // Total downloads - using correct table name
        $this->db->where('user_id', $user_id);
        $downloads_count = $this->db->count_all_results('tb_ft_resume_downloads');

        // Templates used (distinct) - using correct table name
        $this->db->distinct()
                 ->select('template_id')
                 ->where('user_id', $user_id);
        $templates_used = $this->db->get('tb_ft_template_usage')->num_rows();

        // Resume completion rate (finalized drafts) - using correct table name
        $this->db->where('user_id', $user_id)
                 ->where('is_finalized', 1);
        $finalized_count = $this->db->count_all_results('tb_ft_resume_drafts');

        $completion_rate = 0;
        if ($resumes_count > 0) {
            $completion_rate = round(($finalized_count / $resumes_count) * 100);
        }

        $data = [
            'resumesCreated'  => $resumes_count,
            'downloads'       => $downloads_count,
            'templatesUsed'   => $templates_used,
            'completionRate'  => $completion_rate . '%'
        ];

        return $this->response([
            'status'  => true,
            'message' => 'Dashboard stats fetched successfully',
            'data'    => $data
        ], REST_Controller::HTTP_OK);
    }
}
?>