<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class EmployerPlans extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->user_id = $this->session->userdata('user_id');
        $this->load->helper('security');
        $this->load->model('employer/EmployerPlans_model');
    }

    public function index() {
        $data['title'] = 'Plans';
        $this->load->model('employer/Profile_mdl');

        $profile = $this->Profile_mdl->get_employer_details($this->user_id);
        $data['status'] = $profile['status'] ?? 'active';

        $data['content'] = $this->load->view('employer/purchased_plans', $data, TRUE);
        $this->load->view('templates/master', $data);
    }

    /**
     * AJAX endpoint: returns HTML for the active plan + CSRF token.
     */
    public function fetchPlanDetails($tabType, $planId = null) {
        // CSRF validation is handled automatically by CodeIgniter if enabled.
        $activePlan = $this->EmployerPlans_model->getCurrentActivePlan($this->user_id);

        if (!$activePlan) {
            $activePlan = [];
        } else {
            $now = time();
            $end = strtotime($activePlan['end_date']);
            $activePlan['expired'] = ($end < $now) ? 'true' : 'false';

            // Remaining job posts
            $activePlan['post_credit'] = max(0, ($activePlan['post_balance'] ?? 0) - ($activePlan['job_posts_used'] ?? 0));

            // Remaining for other quotas
            $activePlan['cv_remaining'] = max(0, ($activePlan['cv_views_per_requirement'] ?? 0) - ($activePlan['cv_views_used'] ?? 0));
            $activePlan['search_remaining'] = max(0, ($activePlan['search_results_limit'] ?? 0) - ($activePlan['searches_used'] ?? 0));
            $activePlan['bulk_remaining'] = max(0, ($activePlan['bulk_download_limit'] ?? 0) - ($activePlan['bulk_downloads_used'] ?? 0));

            // Remaining time if not expired
            if ($activePlan['expired'] === 'false') {
                $diff = $end - $now;
                $activePlan['remaining_days'] = floor($diff / 86400);
                $activePlan['remaining_hours'] = floor(($diff % 86400) / 3600);
                $activePlan['remaining_minutes'] = floor(($diff % 3600) / 60);
                $activePlan['remaining_seconds'] = $diff % 60;
            }
        }

        $data['activePlan'] = $this->currentActivePlanHtml($activePlan);
        $data['csrf_token'] = $this->security->get_csrf_hash();
        $data['csrf_name']  = $this->security->get_csrf_token_name();

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($data));
    }

    /**
     * Build the HTML for the active plan (or a "no plan" message)
     */
    private function currentActivePlanHtml($activePlan) {
        if (empty($activePlan)) {
                return '
                <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-8 text-center">
            
                    <div class="mx-auto flex items-center justify-center w-20 h-20 bg-blue-100 rounded-full mb-5">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8c-1.657 0-3 1.343-3 3v1H8a2 2 0 00-2 2v2a2 2 0 002 2h8a2 2 0 002-2v-2a2 2 0 00-2-2h-1v-1c0-1.657-1.343-3-3-3zm0-4a7 7 0 00-7 7v1a4 4 0 00-3 3.87V18a4 4 0 004 4h12a4 4 0 004-4v-2.13A4 4 0 0019 12v-1a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
            
                    <h4 class="text-2xl font-bold text-gray-800 mb-3">
                        Activate Your Free Plan
                    </h4>
            
                    <p class="text-gray-600 text-base leading-relaxed mb-6 max-w-lg mx-auto">
                        Your FREE employer plan is waiting for you. Activate it now and start posting jobs, receiving applications, and hiring top talent without any cost.
                    </p>
            
                    <div class="flex flex-wrap justify-center gap-3 mb-6">
                        <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-sm font-medium">
                            ✓ Free Job Posting
                        </span>
            
                        <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-sm font-medium">
                            ✓ Candidate Applications
                        </span>
            
                        <span class="px-3 py-1 bg-purple-100 text-purple-700 rounded-full text-sm font-medium">
                            ✓ Instant Activation
                        </span>
                    </div>
            
                    <a href="' . base_url('employer/jobs/create') . '"
                       class="inline-flex items-center px-8 py-3 text-sm font-semibold rounded-lg text-white bg-blue-600 hover:bg-blue-700 shadow-md hover:shadow-lg transition-all duration-200">
            
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                        </svg>
            
                        Activate Free Plan Now
                    </a>
            
                </div>';
            }

        // Status badge
        if ($activePlan['expired'] === 'true') {
            $plan_status = '<span class="flex items-center text-red-500 font-semibold">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                </svg>Expired</span>';
        } else {
            $plan_status = '<span class="flex items-center text-green-500 font-semibold">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>Active</span>';
        }

        // Helper to format a quota line
        $quotaLine = function($label, $used, $total, $remaining) {
            return '
            <div class="flex flex-col space-y-1 p-3 bg-gray-50 rounded-lg">
                <span class="text-sm text-gray-500">' . $label . '</span>
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Used:</span>
                    <span class="font-medium">' . $used . '</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Total:</span>
                    <span class="font-medium">' . $total . '</span>
                </div>
                <div class="flex justify-between items-center border-t border-gray-200 pt-1 mt-1">
                    <span class="text-gray-600">Remaining:</span>
                    <span class="font-semibold text-blue-600">' . $remaining . '</span>
                </div>
            </div>';
        };

        // Safely extract values with fallbacks
        $plan_name           = $activePlan['plan_name'] ?? 'N/A';
        $discounted_price    = $activePlan['discounted_price'] ?? 0;
        $plan_type           = $activePlan['plan_type'] ?? 'N/A';
        $purchase_date       = isset($activePlan['purchase_date']) ? date('d M Y', strtotime($activePlan['purchase_date'])) : 'N/A';
        $remaining_time      = $this->formatRemainingTime($activePlan);

        // Quota data
        $job_used    = $activePlan['job_posts_used'] ?? 0;
        $job_total   = $activePlan['post_balance'] ?? 0;
        $job_rem     = $activePlan['post_credit'] ?? 0;

        $cv_used     = $activePlan['cv_views_used'] ?? 0;
        $cv_total    = $activePlan['cv_views_per_requirement'] ?? 0;
        $cv_rem      = $activePlan['cv_remaining'] ?? 0;

        $search_used = $activePlan['searches_used'] ?? 0;
        $search_total= $activePlan['search_results_limit'] ?? 0;
        $search_rem  = $activePlan['search_remaining'] ?? 0;

        $bulk_used   = $activePlan['bulk_downloads_used'] ?? 0;
        $bulk_total  = $activePlan['bulk_download_limit'] ?? 0;
        $bulk_rem    = $activePlan['bulk_remaining'] ?? 0;

        $single_user_access = isset($activePlan['single_user_access']) && $activePlan['single_user_access'] == 1 ? 'Yes' : 'No';

        // Build the HTML
        $html = '<div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="p-6 bg-gradient-to-r from-blue-50 to-indigo-50 border-b border-gray-200">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center">
                    <div>
                        <h3 class="text-2xl font-bold text-gray-800">' . $plan_name . '</h3>
                        <p class="text-gray-600">' . $plan_type . ' Plan · ₹' . $discounted_price . '</p>
                    </div>
                    <div class="mt-2 md:mt-0">' . $plan_status . '</div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 p-6">
                ' . $quotaLine('Job Posts', $job_used, $job_total, $job_rem) . '
                ' . $quotaLine('CV Views', $cv_used, $cv_total, $cv_rem) . '
                ' . $quotaLine('Searches', $search_used, $search_total, $search_rem) . '
                ' . $quotaLine('Bulk Downloads', $bulk_used, $bulk_total, $bulk_rem) . '
            </div>

            <div class="border-t border-gray-200 bg-gray-50 p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Purchase Date:</span>
                        <span class="font-medium">' . $purchase_date . '</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Single User Mode:</span>
                        <span class="font-medium">' . $single_user_access . '</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">' . ($activePlan['expired'] === 'true' ? 'Expired:' : 'Time Remaining:') . '</span>
                        <span class="font-medium">' . $remaining_time . '</span>
                    </div>
                </div>
            </div>
        </div>';

        return $html;
    }

    /**
     * Format remaining time or expired time nicely.
     */
    private function formatRemainingTime($activePlan) {
        if ($activePlan['expired'] === 'true') {
            $endDate = strtotime($activePlan['end_date']);
            return timeAgo($endDate);  // assumes timeAgo() is globally available
        }

        if (isset($activePlan['remaining_days'])) {
            $days = (int) $activePlan['remaining_days'];
            $hours = $activePlan['remaining_hours'] ?? 0;
            $mins = $activePlan['remaining_minutes'] ?? 0;
            $formatted = $days . ' days';
            if ($days <= 7) {
                $formatted .= ' ' . $hours . ' hrs ' . $mins . ' min';
            }
            return $formatted;
        }

        return 'N/A';
    }
}