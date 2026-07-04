<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class AdminJobs extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('admin/AdminJobs_model');
        $this->load->library('pagination');
        $this->load->helper(['form', 'url']);
    }

    public function jobs() {
        // Get filter values from GET
        $filters = [
            'job_title'   => $this->input->get('job_title', true),
            'status'      => $this->input->get('status', true),
            'is_paid'     => $this->input->get('is_paid', true),
            'created_from'=> $this->input->get('created_from', true),
            'created_to'  => $this->input->get('created_to', true)
        ];
    
        // Total records after applying filters
        $total_rows = $this->AdminJobs_model->count_filtered_jobs($filters);
    
        // Pagination configuration
        $config['base_url']          = base_url('admin/jobs/AdminJobs/jobs');
        $config['total_rows']        = $total_rows;
        $config['per_page']          = 10;
        $config['uri_segment']       = 5; // because offset is the 5th segment
        $config['reuse_query_string'] = true;
    
        // Styling (Tailwind friendly)
        $config['full_tag_open']   = '<div class="flex justify-center mt-4"><ul class="flex space-x-1">';
        $config['full_tag_close']  = '</ul></div>';
        $config['first_link']      = 'First';
        $config['last_link']       = 'Last';
        $config['prev_link']       = '‹ Prev';
        $config['next_link']       = 'Next ›';
        $config['num_tag_open']    = '<li>';
        $config['num_tag_close']   = '</li>';
        $config['cur_tag_open']    = '<li><span class="bg-blue-600 text-white px-3 py-1 rounded">';
        $config['cur_tag_close']   = '</span></li>';
        $config['next_tag_open']   = '<li>';
        $config['next_tag_close']  = '</li>';
        $config['prev_tag_open']   = '<li>';
        $config['prev_tag_close']  = '</li>';
        $config['first_tag_open']  = '<li>';
        $config['first_tag_close'] = '</li>';
        $config['last_tag_open']   = '<li>';
        $config['last_tag_close']  = '</li>';
        $config['attributes']      = ['class' => 'px-3 py-1 border rounded hover:bg-gray-100'];
    
        $this->pagination->initialize($config);
    
        // Get the offset from URI segment 5
          $offset = ($this->uri->segment(5)) ? (int)$this->uri->segment(5) : 0;
        $data['jobs'] = $this->AdminJobs_model->get_jobs_paginated($config['per_page'], $offset, $filters);
        $data['links'] = $this->pagination->create_links();
        $data['filters'] = $filters;
        $data['statuses'] = ['draft', 'active', 'expired', 'on_hold', 'rejected', 'under_review', 'suspended'];
        $data['title'] = 'Job Posts List';
        // Add these two lines:
        $data['total_rows'] = $total_rows;
        $data['current_offset'] = $offset;
        $data['per_page'] = $config['per_page'];
        
        $data['content'] = $this->load->view('admin/jobs/list', $data, true);
        $this->load->view('templates/master', $data);
    }

    // AJAX: Update job status
    public function ajax_update_status() {
        $this->load->model('admin/AdminEmployer_model');
        $this->load->model('Notification_model');

        $job_id = $this->input->post('job_id');
        $status = $this->input->post('status');
        $reason = $this->input->post('reason', true);

        $admin_statuses = ['under-review', 'rejected', 'suspended', 'active'];
        if (!in_array($status, $admin_statuses)) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid status', 'csrf_token' => $this->security->get_csrf_hash()]);
            return;
        }
        if (empty($job_id)) {
            echo json_encode(['status' => 'error', 'message' => 'Job ID missing', 'csrf_token' => $this->security->get_csrf_hash()]);
            return;
        }

        $this->AdminEmployer_model->update_job_status($job_id, $status, $reason);
        $job = $this->AdminEmployer_model->get_job($job_id);
        if (!$job) {
            echo json_encode(['status' => 'error', 'message' => 'Job not found', 'csrf_token' => $this->security->get_csrf_hash()]);
            return;
        }

        $job_link = base_url('employer/jobs/view/' . $job_id);
        $notifMsg = '';
        if ($status === 'under-review') $notifMsg = "Your job posting titled '{$job['job_title']}' is now under review.";
        elseif ($status === 'rejected') $notifMsg = "Your job posting titled '{$job['job_title']}' has been rejected. Reason: {$reason}.";
        elseif ($status === 'suspended') $notifMsg = "Your job posting titled '{$job['job_title']}' has been suspended.";
        elseif ($status === 'active') $notifMsg = "Your job posting titled '{$job['job_title']}' is now active.";

        if ($notifMsg) {
            $this->Notification_model->create([
                'user_id'    => $job['employer_id'],
                'type'       => 'employer',
                'message'    => $notifMsg,
                'link'       => 'employer/jobs/view/' . $job_id,
                'created_at' => date('Y-m-d H:i:s'),
            ]);
        }

        $emailSubject = "Talents Jobs - Job Status Update";
        $emailMessage = "<html><body><p>Dear Employer,</p><p>Your job posting titled '{$job['job_title']}' has been updated. Status: <strong>{$status}</strong>.</p>";
        if (!empty($reason)) $emailMessage .= "<p>Reason: <strong>{$reason}</strong></p>";
        $emailMessage .= "<p><a href='{$job_link}'>Click here to view the job details</a></p><p><a href='https://www.talentsjobs.in' style='padding:10px 20px; background-color:#004aad; color:#fff; text-decoration:none;'>Visit Talents Jobs</a></p></body></html>";
        SendEmailTo($job['employer_email'], $emailSubject, $emailMessage);

        echo json_encode(['status' => 'success', 'message' => 'Job status updated', 'csrf_token' => $this->security->get_csrf_hash()]);
    }

    // AJAX: View job details
    public function ajax_view_job($job_id) {
        $this->load->model('admin/AdminEmployer_model');
        $job = $this->AdminEmployer_model->get_job($job_id);
        if (!$job) {
            echo '<div class="text-red-500 text-center py-8">Job not found.</div>';
            return;
        }

        $format = function ($value, $default = 'N/A') {
            return empty($value) ? '<span class="text-gray-400 italic">' . $default . '</span>' : '<span class="break-words">' . html_escape($value) . '</span>';
        };

        $job_description = !empty($job['job_description']) ? html_escape(strip_tags($job['job_description'])) : '';
        $salary = 'N/A';
        if ((float)$job['min_salary'] > 0 || (float)$job['max_salary'] > 0) {
            $salary = '₹' . number_format($job['min_salary']) . ' - ₹' . number_format($job['max_salary']);
            if (!empty($job['salary_type'])) $salary .= ' ' . ucfirst($job['salary_type']);
        }

        $getStatusBadge = function ($status) {
            $badges = [
                'active' => 'bg-green-100 text-green-800',
                'draft' => 'bg-gray-100 text-gray-600',
                'on_hold' => 'bg-yellow-100 text-yellow-800',
                'expired' => 'bg-red-100 text-red-700',
                'under_review' => 'bg-blue-100 text-blue-700',
                'rejected' => 'bg-red-200 text-red-800',
                'suspended' => 'bg-orange-100 text-orange-700'
            ];
            return $badges[$status] ?? 'bg-gray-100 text-gray-600';
        };

        echo '
        <div class="space-y-4 pr-2 text-sm overflow-x-hidden">
            <div class="border-b pb-3">
                <div class="flex justify-between gap-2 min-w-0">
                    <div class="min-w-0">
                        <h3 class="font-bold text-gray-900 break-words">' . $format($job['job_title']) . '</h3>
                        <div class="flex flex-wrap items-center gap-2 mt-1">
                            <span class="px-2 py-0.5 text-xs rounded ' . $getStatusBadge($job['status']) . '">' . ucfirst($job['status']) . '</span>';
        if ($job['is_paid']) echo '<span class="px-2 py-0.5 text-xs bg-purple-100 text-purple-700 rounded"><i class="fas fa-crown mr-1 text-xs"></i>Paid</span>';
        echo '          </div>
                    </div>
                    <div class="text-xs text-gray-500 whitespace-nowrap">Posted: ' . date('d M Y', strtotime($job['created_at'])) . '</div>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 min-w-0">
                <div class="space-y-2 min-w-0">
                    <h4 class="font-medium text-gray-700 text-sm border-b pb-1">Job Information</h4>
                    <div class="grid grid-cols-2 gap-2 text-xs min-w-0">
                        <div><p class="text-gray-500">Experience</p><p class="font-medium break-words">' . $job['min_experience'] . ' - ' . $job['max_experience'] . ' yrs</p></div>
                        <div><p class="text-gray-500">Salary</p><p class="font-medium break-words">' . $salary . '</p></div>
                        <div><p class="text-gray-500">Job Type</p><p class="font-medium break-words">' . $format($job['job_type']) . '</p></div>
                        <div><p class="text-gray-500">Education</p><p class="font-medium break-words">' . $format($job['education']) . '</p></div>
                        <div><p class="text-gray-500">Deadline</p><p class="font-medium">' . (!empty($job['deadline_date']) ? date('d M Y', strtotime($job['deadline_date'])) : 'N/A') . '</p></div>
                        <div><p class="text-gray-500">Job ID</p><p class="font-medium">#' . $job['job_id'] . '</p></div>
                    </div>
                </div>
                <div class="space-y-2 min-w-0">
                    <h4 class="font-medium text-gray-700 text-sm border-b pb-1">Employer Information</h4>
                    <div class="space-y-1.5 text-xs min-w-0">
                        <div class="flex justify-between gap-2"><span class="text-gray-500">Company:</span><span class="font-medium break-words">' . $format($job['company_name']) . '</span></div>
                        <div class="flex justify-between gap-2"><span class="text-gray-500">Email:</span><span class="font-medium break-all">' . $format($job['employer_email']) . '</span></div>
                        <div class="flex justify-between gap-2"><span class="text-gray-500">Status:</span><span class="font-medium">' . ucfirst($job['employer_status']) . '</span></div>
                        <div class="flex justify-between gap-2"><span class="text-gray-500">Posted By:</span><span class="font-medium break-words">' . $format($job['posted_by']) . '</span></div>
                        <div class="flex justify-between gap-2"><span class="text-gray-500">Updated:</span><span class="font-medium">' . date('d M Y', strtotime($job['updated_at'])) . '</span></div>
                    </div>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-3 text-xs min-w-0">
                <div><p class="text-gray-500 mb-1">Industry</p><p class="break-words">' . $format($job['industry_name']) . '</p></div>
                <div><p class="text-gray-500 mb-1">Tags</p><p class="break-words">' . $format($job['job_tag']) . '</p></div>
                <div><p class="text-gray-500 mb-1">Apply Link</p>';
        if ($job['enable_apply_link'] === 'yes' && !empty($job['apply_web_link'])) {
            echo '<a href="' . html_escape($job['apply_web_link']) . '" target="_blank" class="text-blue-600 text-xs break-all block max-w-full">' . html_escape($job['apply_web_link']) . '</a>';
        } else {
            echo '<span class="text-gray-400">Not provided</span>';
        }
        echo '      </div>
            </div>
            <div>
                <h4 class="font-medium text-gray-700 text-sm mb-1">Required Skills</h4>';
        if (!empty($job['skills'])) {
            echo '<div class="flex flex-wrap gap-1 min-w-0">';
            foreach ($job['skills'] as $skill) echo '<span class="px-2 py-0.5 bg-gray-100 text-xs rounded break-words">' . html_escape($skill) . '</span>';
            echo '</div>';
        } else {
            echo '<p class="text-gray-400 italic text-xs">No skills specified</p>';
        }
        echo '  </div>
            <div>
                <h4 class="font-medium text-gray-700 text-sm mb-1">Job Description</h4>
                <div class="border rounded p-3 bg-gray-50 max-h-40 overflow-y-auto overflow-x-hidden break-words">' . (!empty($job_description) ? nl2br($job_description) : '<p class="text-gray-400 italic">No description provided</p>') . '</div>
            </div>';
        if (!empty($job['cities'])) {
            echo '<div><h4 class="font-medium text-gray-700 text-sm mb-1">Job Locations</h4><div class="flex flex-wrap gap-1 min-w-0">';
            foreach ($job['cities'] as $city) echo '<span class="px-2 py-0.5 bg-blue-50 text-blue-700 text-xs rounded"><i class="fas fa-map-marker-alt mr-1 text-xs"></i>' . html_escape($city) . '</span>';
            echo '</div></div>';
        }
        if ($job['status'] === 'rejected' && !empty($job['rejection_reason'])) {
            echo '<div class="border border-red-200 rounded p-3 bg-red-50"><h4 class="font-medium text-red-700 text-sm mb-1">Rejection Reason</h4><p class="text-red-600 text-xs break-words">' . nl2br(html_escape(strip_tags($job['rejection_reason']))) . '</p></div>';
        }
        echo '</div>';
    }

    // AJAX: Soft delete job
    public function soft_delete_job() {
        $csrf_token = $this->security->get_csrf_hash();
        $job_id = $this->input->post('job_id');
        if (empty($job_id)) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid job ID', 'csrf_token' => $csrf_token]);
            return;
        }

        $this->load->model('admin/AdminEmployer_model');
        $this->load->model('Notification_model');
        $job = $this->AdminEmployer_model->get_job($job_id);
        if (!$job) {
            echo json_encode(['status' => 'error', 'message' => 'Job not found', 'csrf_token' => $csrf_token]);
            return;
        }

        $deleted = $this->AdminEmployer_model->soft_delete_job($job_id);
        if ($deleted) {
            $this->Notification_model->create([
                'user_id'    => $job['employer_id'],
                'type'       => 'employer',
                'message'    => "Your job posting titled <strong>{$job['job_title']}</strong> has been removed by the " . SITE_NAME . " administrator.",
                'link'       => 'employer/jobs/view/' . $job_id,
                'created_at' => date('Y-m-d H:i:s'),
            ]);
            echo json_encode(['status' => 'success', 'message' => 'Job removed successfully.', 'csrf_token' => $csrf_token]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Delete failed', 'csrf_token' => $csrf_token]);
        }
    }
}
?>