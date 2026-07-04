<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard_mdl extends CI_Model {

    private $resume_builder_feature_id;

    public function __construct() {
        parent::__construct();
        $this->load->database();
        $this->resume_builder_feature_id = $this->get_resume_builder_feature_id();
    }

    private function get_resume_builder_feature_id() {
        // Fixed: use correct table name tb_ft_features
        $this->db->select('feature_id')->from('tb_ft_features')->where('slug', 'resume-builder');
        $row = $this->db->get()->row();
        return $row ? (int) $row->feature_id : 0;
    }

    public function get_dashboard_stats($employer_id) {
        $stats = [];

        // Active jobs
        $this->db->where('employer_id', $employer_id);
        $this->db->where('status', 'active');
        $stats['active_jobs'] = $this->db->count_all_results('tb_post_job');

        // Total applications
        $this->db->select('COUNT(a.applied_id) as total_apps');
        $this->db->from('tb_applied a');
        $this->db->join('tb_post_job j', 'a.job_id = j.job_id');
        $this->db->where('j.employer_id', $employer_id);
        $stats['total_applications'] = (int) $this->db->get()->row()->total_apps;

        // New applications (last 24h)
        $this->db->select('COUNT(a.applied_id) as new_apps');
        $this->db->from('tb_applied a');
        $this->db->join('tb_post_job j', 'a.job_id = j.job_id');
        $this->db->where('j.employer_id', $employer_id);
        $this->db->where('a.created_at >=', date('Y-m-d H:i:s', strtotime('-1 day')));
        $stats['new_applications'] = (int) $this->db->get()->row()->new_apps;

        // Interviews scheduled
        $this->db->select('COUNT(i.interview_id) as interviews');
        $this->db->from('tb_interviews i');
        $this->db->join('tb_applied a', 'i.applied_id = a.applied_id');
        $this->db->join('tb_post_job j', 'a.job_id = j.job_id');
        $this->db->where('j.employer_id', $employer_id);
        $this->db->where('i.status', 'Scheduled');
        $stats['interviews'] = (int) $this->db->get()->row()->interviews;

        // Percentage change (yesterday vs day before)
        $today_start = date('Y-m-d 00:00:00');
        $yesterday_start = date('Y-m-d 00:00:00', strtotime('-1 day'));
        $two_days_ago_start = date('Y-m-d 00:00:00', strtotime('-2 days'));

        $this->db->select('COUNT(a.applied_id) as count');
        $this->db->from('tb_applied a');
        $this->db->join('tb_post_job j', 'a.job_id = j.job_id');
        $this->db->where('j.employer_id', $employer_id);
        $this->db->where('a.created_at >=', $yesterday_start);
        $this->db->where('a.created_at <', $today_start);
        $yesterday_count = (int) $this->db->get()->row()->count;

        $this->db->select('COUNT(a.applied_id) as count');
        $this->db->from('tb_applied a');
        $this->db->join('tb_post_job j', 'a.job_id = j.job_id');
        $this->db->where('j.employer_id', $employer_id);
        $this->db->where('a.created_at >=', $two_days_ago_start);
        $this->db->where('a.created_at <', $yesterday_start);
        $day_before_count = (int) $this->db->get()->row()->count;

        $stats['percentage_change'] = ($day_before_count > 0) ? round((($yesterday_count - $day_before_count) / $day_before_count) * 100) : null;

        return $stats;
    }

    public function get_extra_stats($employer_id) {
        $extra = [];

        // 1. Active plan details
        $extra['plan'] = $this->get_active_plan($employer_id);

        // 2. Unread notifications
        $this->db->where('user_id', $employer_id);
        $this->db->where('type', 'employer');
        $this->db->where('is_read', 0);
        $extra['unread_notifications'] = $this->db->count_all_results('tb_notifications');

        // 3. Shortlisted candidates
        $this->db->select('COUNT(a.applied_id) as count');
        $this->db->from('tb_applied a');
        $this->db->join('tb_post_job j', 'a.job_id = j.job_id');
        $this->db->where('j.employer_id', $employer_id);
        $this->db->where('a.ApplicationStage', 'Shortlist');
        $extra['shortlisted'] = (int) $this->db->get()->row()->count;

        // 4. Hired candidates
        $this->db->select('COUNT(a.applied_id) as count');
        $this->db->from('tb_applied a');
        $this->db->join('tb_post_job j', 'a.job_id = j.job_id');
        $this->db->where('j.employer_id', $employer_id);
        $this->db->where('a.ApplicationStage', 'Hired');
        $extra['hired'] = (int) $this->db->get()->row()->count;

        // 5. Total applications (all stages)
        $this->db->select('COUNT(a.applied_id) as count');
        $this->db->from('tb_applied a');
        $this->db->join('tb_post_job j', 'a.job_id = j.job_id');
        $this->db->where('j.employer_id', $employer_id);
        $total_apps = (int) $this->db->get()->row()->count;

        // 6. Interviews scheduled (any status)
        $this->db->select('COUNT(i.interview_id) as count');
        $this->db->from('tb_interviews i');
        $this->db->join('tb_applied a', 'i.applied_id = a.applied_id');
        $this->db->join('tb_post_job j', 'a.job_id = j.job_id');
        $this->db->where('j.employer_id', $employer_id);
        $interviews = (int) $this->db->get()->row()->count;

        $extra['interview_conversion_rate'] = ($total_apps > 0) ? round(($interviews / $total_apps) * 100) : 0;

        // 7. Applications per active job
        $active_jobs = $this->db->where('employer_id', $employer_id)->where('status', 'active')->count_all_results('tb_post_job');
        $extra['applications_per_job'] = ($active_jobs > 0) ? round($total_apps / $active_jobs, 1) : 0;

        return $extra;
    }

    private function get_active_plan($employer_id) {
        $this->db->select('p.*, s.plan_name, s.job_post_limit, s.cv_view_limit, s.search_limit, s.bulk_download_limit');
        $this->db->from('tb_plan_purchases p');
        $this->db->join('tb_subscription_plans s', 'p.plan_id = s.id', 'left');
        $this->db->where('p.employer_id', $employer_id);
        $this->db->where('p.plan_status', 'active');
        $this->db->where('p.end_date >=', date('Y-m-d H:i:s'));
        $this->db->order_by('p.end_date', 'DESC');
        $plan = $this->db->get()->row_array();

        if ($plan) {
            $remaining_jobs = max(0, $plan['job_post_limit'] - $plan['job_posts_used']);
            $remaining_cv_views = max(0, $plan['cv_view_limit'] - $plan['cv_views_used']);
            $remaining_searches = max(0, $plan['search_limit'] - $plan['searches_used']);
            $remaining_bulk = max(0, $plan['bulk_download_limit'] - $plan['bulk_downloads_used']);
            $days_left = ceil((strtotime($plan['end_date']) - time()) / 86400);

            return [
                'has_plan' => true,
                'plan_name' => $plan['plan_name'],
                'expiry_date' => $plan['end_date'],
                'days_left' => $days_left,
                'remaining_jobs' => $remaining_jobs,
                'remaining_cv_views' => $remaining_cv_views,
                'remaining_searches' => $remaining_searches,
                'remaining_bulk' => $remaining_bulk,
            ];
        } else {
            return ['has_plan' => false];
        }
    }

    public function get_recent_applications($employer_id, $limit = 10) {
		// सबक्वेरी – किसी भी एक्टिव प्लान को 1, नहीं तो 0
		$has_active_plan_subquery = "(
			SELECT EXISTS (
				SELECT 1
				FROM tb_ft_user_purchases up
				WHERE up.user_id = c.candidate_id
				  AND up.status = 'active'
				  AND up.start_date <= NOW()
				  AND up.end_date >= NOW()
			)
		) as has_active_plan";

		$this->db->select("
			a.applied_id,
			c.name as candidate_name,
			j.job_title,
			j.job_id,
			a.ApplicationStage,
			a.created_at as applied_date,
			c.resume,
			c.candidate_id,
			$has_active_plan_subquery
		", false);

		$this->db->from('tb_applied a');
		$this->db->join('tb_post_job j', 'a.job_id = j.job_id');
		$this->db->join('tb_candidate c', 'a.candidate_id = c.candidate_id');
		$this->db->where('j.employer_id', $employer_id);

		// ✅ पहले प्रीमियम वाले, फिर तारीख के हिसाब से नए
		$this->db->order_by('has_active_plan', 'DESC');
		$this->db->order_by('a.created_at', 'DESC');

		$this->db->limit($limit);
		return $this->db->get()->result_array();
	}

    public function get_profile_completeness($employer_id) {
        $this->db->where('employer_id', $employer_id);
        $employer = $this->db->get('tb_employer')->row_array();

        $required_fields = [
            'company_name' => ['label' => 'Company Name', 'weight' => 1.5],
            'logo' => ['label' => 'Company Logo', 'weight' => 1.5],
            'about_company' => ['label' => 'About Company', 'weight' => 1.5],
            'company_address' => ['label' => 'Company Address', 'weight' => 1],
            'industry_id' => ['label' => 'Industry', 'weight' => 1],
            'company_website' => ['label' => 'Website', 'weight' => 1],
            'company_size' => ['label' => 'Company Size', 'weight' => 1],
            'company_type' => ['label' => 'Company Type', 'weight' => 1],
            'recruiter_type' => ['label' => 'Recruiter Type', 'weight' => 1],
            'employee_designation' => ['label' => 'Your Designation', 'weight' => 1],
            'expertise_specialization' => ['label' => 'Expertise', 'weight' => 1],
            'city_id' => ['label' => 'Location', 'weight' => 1]
        ];

        $total_weight = 0;
        $completed_weight = 0;
        $missing = [];

        foreach($required_fields as $field => $data) {
            $total_weight += $data['weight'];
            if(!empty($employer[$field])) {
                $completed_weight += $data['weight'];
            } else {
                $missing[] = $data['label'];
            }
        }

        $percentage = round(($completed_weight / $total_weight) * 100);
        return [
            'percentage' => min($percentage, 100),
            'missing_fields' => $missing
        ];
    }

    public function get_active_jobs($employer_id, $limit = 3) {
        $this->db->select('j.job_id, j.job_title, j.created_at, j.status, COUNT(a.applied_id) as applications, c.city_name');
        $this->db->from('tb_post_job j');
        $this->db->join('tb_applied a', 'j.job_id = a.job_id', 'left');
        $this->db->join('tb_job_cities jc', 'j.job_id = jc.job_id', 'left');
        $this->db->join('tb_cities c', 'jc.city_id = c.city_id', 'left');
        $this->db->where('j.employer_id', $employer_id);
        $this->db->where('j.status', 'active');
        $this->db->group_by('j.job_id');
        $this->db->order_by('j.created_at', 'DESC');
        $this->db->limit($limit);
        return $this->db->get()->result_array();
    }

    public function get_hiring_analytics($employer_id) {
        // Applications over last 30 days
        $this->db->select('DATE(a.created_at) as date, COUNT(a.applied_id) as count');
        $this->db->from('tb_applied a');
        $this->db->join('tb_post_job j', 'j.job_id = a.job_id');
        $this->db->where('j.employer_id', $employer_id);
        $this->db->where('a.created_at >=', date('Y-m-d', strtotime('-30 days')));
        $this->db->group_by('DATE(a.created_at)');
        $applications = $this->db->get()->result_array();

        // Application status distribution
        $this->db->select('a.ApplicationStage, COUNT(a.applied_id) as count');
        $this->db->from('tb_applied a');
        $this->db->join('tb_post_job j', 'j.job_id = a.job_id');
        $this->db->where('j.employer_id', $employer_id);
        $this->db->group_by('a.ApplicationStage');
        $status_distribution = $this->db->get()->result_array();

        return [
            'applications' => $applications,
            'status_distribution' => $status_distribution
        ];
    }
}