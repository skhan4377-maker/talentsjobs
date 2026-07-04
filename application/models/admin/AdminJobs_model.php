<?php defined('BASEPATH') OR exit('No direct script access allowed');

class AdminJobs_model extends CI_Model {

    public function get_jobs_paginated($limit, $offset, $filters = []) {
        $this->db->select('j.job_id, j.job_title, j.status, j.created_at, j.min_experience, j.max_experience, 
                          j.min_salary, j.max_salary, j.salary_type, j.is_paid, j.is_deleted,
                          e.company_name, e.status as employer_status, e.is_deleted as employer_deleted,
                          (SELECT COUNT(*) FROM tb_applied a WHERE a.job_id = j.job_id) as applied_count');
        $this->db->from('tb_post_job j');
        $this->db->join('tb_employer e', 'e.employer_id = j.employer_id', 'left');
        
        $this->apply_job_filters($filters);
        $this->db->order_by('j.created_at', 'DESC');
        $this->db->limit($limit, $offset);
        
        $query = $this->db->get();
        $result = $query->result_array();
        return $this->format_job_results($result);
    }

    public function count_filtered_jobs($filters = []) {
        $this->db->from('tb_post_job j');
        $this->apply_job_filters($filters);
        return $this->db->count_all_results();
    }

    private function apply_job_filters($filters) {
        if (!empty($filters['job_title'])) {
            $this->db->like('j.job_title', $filters['job_title']);
        }
        if (!empty($filters['status'])) {
            $this->db->where('j.status', $filters['status']);
        }
        if ($filters['is_paid'] !== '' && $filters['is_paid'] !== null) {
            $this->db->where('j.is_paid', $filters['is_paid']);
        }
        if (!empty($filters['created_from'])) {
            $this->db->where('j.created_at >=', $filters['created_from'] . ' 00:00:00');
        }
        if (!empty($filters['created_to'])) {
            $this->db->where('j.created_at <=', $filters['created_to'] . ' 23:59:59');
        }
    }

    private function format_job_results($jobs) {
        $formatted = [];
        foreach ($jobs as $row) {
            $row['company_name'] = $this->format_employer_info($row);
            $row['experience'] = $row['min_experience'] . ' - ' . $row['max_experience'] . ' yrs';
            $row['salary'] = $this->format_salary($row);
            $row['status'] = $this->format_job_status($row);
            $row['created_at'] = $this->timeAgo($row['created_at']);
            $row['actions'] = $this->format_action_buttons($row);
            $formatted[] = $row;
        }
        return $formatted;
    }

    private function format_employer_info($row) {
        $badge = '';
        if ((int)$row['employer_deleted'] === 1) {
            $badge = '<span class="ml-2 text-red-700 text-xs font-medium bg-red-100 px-2 py-0.5 rounded-full">Deleted</span>';
        } else {
            $map = [
                'active' => ['text-green-600', 'bg-green-100', 'Active'],
                'inactive' => ['text-gray-600', 'bg-gray-100', 'Inactive'],
                'under_review' => ['text-yellow-700', 'bg-yellow-100', 'Under Review'],
                'rejected' => ['text-red-700', 'bg-red-100', 'Rejected']
            ];
            $cfg = $map[$row['employer_status']] ?? ['text-gray-700', 'bg-gray-100', ucfirst($row['employer_status'])];
            $badge = '<span class="ml-2 ' . $cfg[0] . ' text-xs font-medium ' . $cfg[1] . ' px-2 py-0.5 rounded-full">' . $cfg[2] . '</span>';
        }
        $company = htmlspecialchars($row['company_name']) . ' ' . $badge;
        if ((int)$row['employer_deleted'] === 1) $company .= ' <small class="text-red-600">(Employer Deleted)</small>';
        return $company;
    }

    private function format_salary($row) {
        $salary = '₹' . $row['min_salary'] . ' - ₹' . $row['max_salary'];
        if (!empty($row['salary_type'])) $salary .= ' ' . ucfirst($row['salary_type']);
        return $salary;
    }

    private function format_job_status($row) {
        if ((int)$row['is_deleted'] === 1) {
            return '<span class="px-2 py-1 text-xs rounded-full bg-red-200 text-red-800 font-medium">Deleted</span>';
        }
        $map = [
            'active' => ['bg-green-100', 'text-green-700', 'Active'],
            'draft' => ['bg-gray-100', 'text-gray-600', 'Draft'],
            'on_hold' => ['bg-yellow-100', 'text-yellow-800', 'On Hold'],
            'expired' => ['bg-red-100', 'text-red-700', 'Expired'],
            'under_review' => ['bg-blue-100', 'text-blue-700', 'Under Review'],
            'rejected' => ['bg-red-200', 'text-red-800', 'Rejected'],
            'suspended' => ['bg-orange-100', 'text-orange-700', 'Suspended']
        ];
        $cfg = $map[$row['status']] ?? ['bg-gray-100', 'text-gray-600', ucfirst($row['status'])];
        return '<span class="px-2 py-1 text-xs rounded-full ' . $cfg[0] . ' ' . $cfg[1] . ' font-medium">' . $cfg[2] . '</span>';
    }

    private function format_action_buttons($row) {
        // Always show buttons (permission check removed)
        $actions = '<div class="flex gap-2 justify-center">';
        $actions .= '<button class="view-job-btn text-gray-600 hover:text-gray-800" data-id="' . $row['job_id'] . '" title="View Job"><div class="p-2 rounded-lg bg-gray-100 hover:bg-gray-200"><i class="fas fa-eye"></i></div></button>';
        $actions .= '<button class="open-status-modal text-indigo-600 hover:text-indigo-800" data-id="' . $row['job_id'] . '" title="Change Status"><div class="p-2 rounded-lg bg-indigo-100 hover:bg-indigo-200"><i class="fas fa-exchange-alt"></i></div></button>';
        $actions .= '<button class="delete-job-btn text-red-600 hover:text-red-800" data-id="' . $row['job_id'] . '" title="Delete Job"><div class="p-2 rounded-lg bg-red-100 hover:bg-red-200"><i class="fas fa-trash"></i></div></button>';
        $actions .= '</div>';
        return $actions;
    }

    private function timeAgo($datetime) {
        if (function_exists('timeAgo')) return timeAgo($datetime);
        return date('d M Y', strtotime($datetime));
    }
}
?>