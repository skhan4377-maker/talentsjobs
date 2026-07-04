<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class AdminApplications_model extends CI_Model {

    public function get_applications_paginated($limit, $offset, $filters = []) {
        $this->db->select('a.*, c.name AS candidate_name, c.email, j.job_title, e.company_name, e.status as employer_status, e.is_deleted');
        $this->db->from('tb_applied a');
        $this->db->join('tb_candidate c', 'c.candidate_id = a.candidate_id', 'left');
        $this->db->join('tb_post_job j', 'j.job_id = a.job_id', 'left');
        $this->db->join('tb_employer e', 'e.employer_id = j.employer_id', 'left');

        $this->apply_application_filters($filters);
        $this->db->order_by('a.created_at', 'DESC');
        $this->db->limit($limit, $offset);

        $result = $this->db->get()->result_array();
        return $this->format_application_results($result);
    }

    public function count_filtered_applications($filters = []) {
        $this->db->from('tb_applied a');
        $this->db->join('tb_candidate c', 'c.candidate_id = a.candidate_id', 'left');
        $this->db->join('tb_post_job j', 'j.job_id = a.job_id', 'left');
        $this->db->join('tb_employer e', 'e.employer_id = j.employer_id', 'left');
        $this->apply_application_filters($filters);
        return $this->db->count_all_results();
    }

    private function apply_application_filters($filters) {
        if (!empty($filters['candidate_name'])) {
            $this->db->like('c.name', $filters['candidate_name']);
        }
        if (!empty($filters['job_title'])) {
            $this->db->like('j.job_title', $filters['job_title']);
        }
        if (!empty($filters['status'])) {
            $this->db->where('a.ApplicationStage', $filters['status']);
        }
        if (!empty($filters['applied_from'])) {
            $this->db->where('a.created_at >=', $filters['applied_from'] . ' 00:00:00');
        }
        if (!empty($filters['applied_to'])) {
            $this->db->where('a.created_at <=', $filters['applied_to'] . ' 23:59:59');
        }
    }

    private function format_application_results($applications) {
        foreach ($applications as &$row) {
            // Format status badge
            $row['ApplicationStage'] = $this->format_status_badge($row['ApplicationStage']);
            // Time ago
            $row['created_at'] = timeago($row['created_at']);
            // Employer status badge
            $row['company_name'] = $this->format_employer_info($row);
            // Action buttons
            $row['actions'] = $this->format_action_buttons($row);
        }
        return $applications;
    }

    private function format_status_badge($stage) {
        $stage = strtolower($stage);
        $map = [
            'applied'             => ['bg-blue-100', 'text-blue-700', 'Applied'],
            'viewed'              => ['bg-indigo-100', 'text-indigo-700', 'Viewed'],
            'under review'        => ['bg-yellow-100', 'text-yellow-800', 'Under Review'],
            'under_review'        => ['bg-yellow-100', 'text-yellow-800', 'Under Review'],
            'shortlist'           => ['bg-green-100', 'text-green-700', 'Shortlisted'],
            'shortlisted'         => ['bg-green-100', 'text-green-700', 'Shortlisted'],
            'interview scheduled' => ['bg-purple-100', 'text-purple-700', 'Interview'],
            'interview'           => ['bg-purple-100', 'text-purple-700', 'Interview'],
            'offer extended'      => ['bg-blue-200', 'text-blue-800', 'Offer'],
            'offer'               => ['bg-blue-200', 'text-blue-800', 'Offer'],
            'hired'               => ['bg-green-200', 'text-green-800', 'Hired'],
            'withdraw'            => ['bg-gray-100', 'text-gray-600', 'Withdrawn'],
            'withdrawn'           => ['bg-gray-100', 'text-gray-600', 'Withdrawn'],
            'rejected'            => ['bg-red-100', 'text-red-700', 'Rejected'],
            'completed'           => ['bg-teal-100', 'text-teal-700', 'Completed'],
            'canceled'            => ['bg-red-200', 'text-red-800', 'Canceled'],
            'cancelled'           => ['bg-red-200', 'text-red-800', 'Canceled']
        ];
        $cfg = $map[$stage] ?? ['bg-gray-100', 'text-gray-600', ucfirst($stage)];
        return '<span class="px-2 py-1 text-xs rounded-full ' . $cfg[0] . ' ' . $cfg[1] . ' font-medium">' . $cfg[2] . '</span>';
    }

    private function format_employer_info($row) {
        $badge = '';
        if ($row['is_deleted'] == 1) {
            $badge = '<span class="ml-2 text-red-800 text-xs font-medium bg-red-200 px-2 py-0.5 rounded-full">Deleted</span>';
        } else {
            switch ($row['employer_status']) {
                case 'active':
                    $badge = '<span class="ml-2 text-green-600 text-xs font-medium bg-green-100 px-2 py-0.5 rounded-full">Active</span>';
                    break;
                case 'inactive':
                    $badge = '<span class="ml-2 text-gray-600 text-xs font-medium bg-gray-100 px-2 py-0.5 rounded-full">Inactive</span>';
                    break;
                case 'under_review':
                    $badge = '<span class="ml-2 text-yellow-700 text-xs font-medium bg-yellow-100 px-2 py-0.5 rounded-full">Under Review</span>';
                    break;
                case 'rejected':
                    $badge = '<span class="ml-2 text-red-700 text-xs font-medium bg-red-100 px-2 py-0.5 rounded-full">Rejected</span>';
                    break;
                default:
                    $badge = '<span class="ml-2 text-gray-700 text-xs font-medium bg-gray-100 px-2 py-0.5 rounded-full">' . ucfirst($row['employer_status']) . '</span>';
            }
        }
        return htmlspecialchars($row['company_name']) . ' ' . $badge;
    }

    private function format_action_buttons($row) {
        $actions = '<div class="flex gap-2 justify-center">';
        $actions .= '<button class="view-application-btn text-gray-600 hover:text-gray-800" data-id="' . $row['applied_id'] . '" title="View Application">
                        <div class="p-2 rounded-lg bg-gray-100 hover:bg-gray-200">
                            <i class="fas fa-eye"></i>
                        </div>
                     </button>';
        $actions .= '</div>';
        return $actions;
    }

    // Keep original methods for AJAX modal
    public function get_application_details($id) {
        return $this->db->select('a.*,c.*')
            ->from('tb_applied a')
            ->join('tb_candidate c', 'c.candidate_id = a.candidate_id')
            ->join('tb_post_job j', 'j.job_id = a.job_id')
            ->where('a.applied_id', $id)
            ->get()->row();
    }
}
?>