<?php
defined('BASEPATH') OR exit('No direct script allowed');

class AdminCandidate extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('admin/AdminCandidate_model');
        $this->load->library('pagination');
        $this->load->helper(['form', 'url']);
        $this->load->library('form_validation');
    }

    public function candidates() {
        // Get filter values from GET
        $filters = [
            'name'          => $this->input->get('name', true),
            'email'         => $this->input->get('email', true),
            'mobile'        => $this->input->get('mobile', true),
            'status'        => $this->input->get('status', true),
            'is_verified'   => $this->input->get('is_verified', true),
            'industry_id'   => $this->input->get('industry_id', true),
            'date_range'    => $this->input->get('date_range', true)
        ];

        // Parse date range
        $date_range = $filters['date_range'];
        $created_from = $created_to = '';
        if (!empty($date_range) && strpos($date_range, ' - ') !== false) {
            list($created_from, $created_to) = explode(' - ', $date_range);
            $created_from = trim($created_from);
            $created_to   = trim($created_to);
        }

        // Pagination configuration
        $config['base_url']    = base_url('admin/candidates/AdminCandidate/candidates');
        $config['total_rows']  = $this->AdminCandidate_model->count_filtered($filters, $created_from, $created_to);
        $config['per_page']    = 10;
        $config['uri_segment'] = 5;      // ✅ Fixed: offset is segment 5
        $config['reuse_query_string'] = true;

        $this->pagination->initialize($config);
        $page = ($this->uri->segment(5)) ? (int)$this->uri->segment(5) : 0;   // ✅ Fixed: read from segment 5

        $data['candidates']      = $this->AdminCandidate_model->get_candidates_paginated(
            $config['per_page'], $page, $filters, $created_from, $created_to
        );
        $data['filters']         = $filters;
        $data['industries']      = $this->AdminCandidate_model->get_all_industries();
        $data['title']           = 'Candidates List';
        $data['total_candidates'] = $config['total_rows'];
        $data['total_rows']      = $config['total_rows'];
        $data['per_page']        = $config['per_page'];
        $data['current_page']    = ($page / $config['per_page']) + 1;

        // Custom Tailwind pagination links
        $data['links'] = $this->_build_tailwind_pagination(
            $config['base_url'],
            $config['total_rows'],
            $config['per_page'],
            $page
        );

        $data['content'] = $this->load->view('admin/candidates/list_full', $data, true);
        $this->load->view('templates/master', $data);
    }

    /**
     * Build Tailwind-styled pagination HTML
     */
    private function _build_tailwind_pagination($base_url, $total_rows, $per_page, $current_offset) {
        if ($total_rows == 0) return '';

        $total_pages = ceil($total_rows / $per_page);
        $current_page = floor($current_offset / $per_page) + 1;
        $adjacent = 2;

        // Helper to build URL with offset (skip offset 0 for first page)
        $build_url = function($offset) use ($base_url) {
            $qs = $this->_query_string();
            if ($offset > 0) {
                return $base_url . '/' . $offset . $qs;
            }
            return $base_url . $qs;
        };

        $output = '<div class="flex justify-center mt-6">';
        $output .= '<nav class="inline-flex gap-1">';

        // Previous button
        $prev_offset = ($current_page - 2) * $per_page;
        if ($current_page > 1) {
            $output .= '<a href="' . $build_url($prev_offset) . '" class="px-3 py-2 rounded-md text-sm font-medium bg-white border border-gray-300 text-gray-700 hover:bg-gray-50">Prev</a>';
        } else {
            $output .= '<span class="px-3 py-2 rounded-md text-sm font-medium bg-gray-100 border border-gray-200 text-gray-400 cursor-not-allowed">Prev</span>';
        }

        // Build page numbers with ellipsis
        $pages = [1];
        $start = max(2, $current_page - $adjacent);
        $end = min($total_pages - 1, $current_page + $adjacent);
        if ($start > 2) $pages[] = '...';
        for ($i = $start; $i <= $end; $i++) $pages[] = $i;
        if ($end < $total_pages - 1) $pages[] = '...';
        if ($total_pages > 1) $pages[] = $total_pages;

        foreach ($pages as $page) {
            if ($page === '...') {
                $output .= '<span class="px-3 py-2 text-sm text-gray-500">...</span>';
            } else {
                $offset = ($page - 1) * $per_page;
                if ($page == $current_page) {
                    $output .= '<span class="px-3 py-2 rounded-md text-sm font-medium bg-blue-600 text-white border border-blue-600">' . $page . '</span>';
                } else {
                    $output .= '<a href="' . $build_url($offset) . '" class="px-3 py-2 rounded-md text-sm font-medium bg-white border border-gray-300 text-gray-700 hover:bg-gray-50">' . $page . '</a>';
                }
            }
        }

        // Next button
        $next_offset = $current_page * $per_page;
        if ($current_page < $total_pages) {
            $output .= '<a href="' . $build_url($next_offset) . '" class="px-3 py-2 rounded-md text-sm font-medium bg-white border border-gray-300 text-gray-700 hover:bg-gray-50">Next</a>';
        } else {
            $output .= '<span class="px-3 py-2 rounded-md text-sm font-medium bg-gray-100 border border-gray-200 text-gray-400 cursor-not-allowed">Next</span>';
        }

        $output .= '</nav>';
        $output .= '</div>';

        return $output;
    }

    /**
     * Preserve existing GET parameters in pagination links
     */
    private function _query_string() {
        $get = $_GET;
        if (empty($get)) return '';
        // Remove any 'page' offset from GET if accidentally present (we use URI segment)
        unset($get['page']);
        return '?' . http_build_query($get);
    }

    /**
     * AJAX endpoint to get candidate data as JSON
     */
    public function get_candidate_json($id) {
        $candidate = $this->AdminCandidate_model->get_candidate($id);
        if ($candidate) {
            echo json_encode(['status' => 'success', 'data' => $candidate]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Candidate not found']);
        }
    }
}