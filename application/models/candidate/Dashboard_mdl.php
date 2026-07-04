<?php defined('BASEPATH') or exit('No direct script access allowed');

class Dashboard_mdl extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Count application stages for the candidate
     */
    public function dashboard_count_activity($candidate_id)
    {
        $stages = ['Shortlist', 'Hired', 'Under Review'];
        $counts = [];

        foreach ($stages as $stage) {
            $counts[strtolower($stage)] = $this->db
                ->where('candidate_id', $candidate_id)
                ->where('ApplicationStage', $stage)
                ->count_all_results('tb_applied');
        }

        return [
            'shortlist' => $counts['shortlist'],
            'hired'     => $counts['hired'],
            'review'    => $counts['under review']
        ];
    }

    /**
     * Calculate profile completeness including related tables
     */
    public function get_profile_completeness($candidate_id)
    {
        $candidate = $this->db->where('candidate_id', $candidate_id)->get('tb_candidate')->row_array();

        $checks = [
            'name'            => !empty($candidate['name']),
            'email'           => !empty($candidate['email']),
            'mobile'          => !empty($candidate['mobile']),
            'headline'        => !empty($candidate['resume_headline']),
            'experience'      => ($candidate['total_experience_years'] > 0),
            'education'       => $this->db->where('candidate_id', $candidate_id)->count_all_results('tb_education_history') > 0,
            'skills'          => $this->db->where('candidate_id', $candidate_id)->count_all_results('tb_candidate_skills') > 0,
            'location'        => !empty($candidate['city_id']),
            'resume_file'     => !empty($candidate['resume']) && file_exists(FCPATH . 'uploads/resume/' . $candidate['resume']),
            'employment'      => $this->db->where('candidate_id', $candidate_id)->count_all_results('tb_employment_history') > 0,
            'certifications'  => $this->db->where('candidate_id', $candidate_id)->count_all_results('tb_certifications') > 0,
        ];

        $weights = [
            'name'           => 1.5,
            'email'          => 1.5,
            'mobile'         => 1,
            'headline'       => 1,
            'experience'     => 1,
            'education'      => 1,
            'skills'         => 1,
            'location'       => 1,
            'resume_file'    => 1,
            'employment'     => 0.5,
            'certifications' => 0.5,
        ];

        $total_weight = array_sum($weights);
        $completed_weight = 0;
        $missing = [];

        foreach ($checks as $field => $completed) {
            if ($completed) {
                $completed_weight += $weights[$field];
            } else {
                $missing[] = ucfirst(str_replace('_', ' ', $field));
            }
        }

        $percentage = round(($completed_weight / $total_weight) * 100);
        return [
            'percentage'     => min($percentage, 100),
            'missing_fields' => $missing
        ];
    }

    /**
     * Additional candidate stats (resume downloads, template usage, etc.)
     */
    public function get_additional_stats($candidate_id)
    {
        // Total jobs applied (distinct)
        $applied_total = $this->db
            ->where('candidate_id', $candidate_id)
            ->count_all_results('tb_applied');

        // Total saved jobs
        $saved_total = $this->db
            ->where('candidate_id', $candidate_id)
            ->count_all_results('tb_favourite');

        // Resume downloads (now from tb_ft_resume_downloads)
        $resume_downloads = $this->db
            ->where('user_id', $candidate_id)
            ->count_all_results('tb_ft_resume_downloads');

        // Template usage (now from tb_ft_template_usage)
        $template_uses = $this->db
            ->where('user_id', $candidate_id)
            ->count_all_results('tb_ft_template_usage');

        // Most used template (if any)
        $most_used = $this->db
            ->select('template_id, COUNT(*) as uses')
            ->where('user_id', $candidate_id)
            ->group_by('template_id')
            ->order_by('uses', 'DESC')
            ->limit(1)
            ->get('tb_ft_template_usage')
            ->row();

        $most_used_template = null;
        if ($most_used) {
            $template = $this->db
                ->select('name')
                ->where('template_id', $most_used->template_id)
                ->get('tb_ft_resume_templates')
                ->row();
            $most_used_template = $template ? $template->name : null;
        }

        // Application activity (last 6 months) – for chart
        $activity = $this->db
            ->select("DATE_FORMAT(created_at, '%Y-%m') as month, COUNT(*) as count")
            ->where('candidate_id', $candidate_id)
            ->where('created_at >=', date('Y-m-d', strtotime('-6 months')))
            ->group_by('month')
            ->order_by('month', 'ASC')
            ->get('tb_applied')
            ->result_array();

        return [
            'applied_total'       => $applied_total,
            'saved_total'         => $saved_total,
            'resume_downloads'    => $resume_downloads,
            'template_uses'       => $template_uses,
            'most_used_template'  => $most_used_template,
            'application_activity' => $activity
        ];
    }
}