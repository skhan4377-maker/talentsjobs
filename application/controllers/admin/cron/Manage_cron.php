<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Manage_cron extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->helper(['url', 'form']);
        $this->load->library(['session', 'form_validation', 'pagination']);
        $this->load->database();
    }

    // ------------------------------------------------------------------
    // Helpers
    // ------------------------------------------------------------------

    private function csrf_json(): array {
        return [
            'name' => $this->security->get_csrf_token_name(),
            'hash' => $this->security->get_csrf_hash()
        ];
    }

    private function parse_date_range(?string $dateRange): array {
        $from = $to = null;
        $display = '';
        if (!empty($dateRange)) {
            $dates = preg_split('/\s+(?:to|until|-)\s+/i', $dateRange);
            if (count($dates) === 2) {
                $from = trim($dates[0]);
                $to   = trim($dates[1]);
            } elseif (count($dates) === 1) {
                $from = $to = trim($dates[0]);
            }
            if ($from && $to) {
                $display = date('M d, Y', strtotime($from)) . ' to ' . date('M d, Y', strtotime($to));
            }
        }
        return [$from, $to, $display];
    }

    private function apply_date_range($db, string $column, ?string $from, ?string $to): void {
        if ($from) $db->where("DATE($column) >=", $from);
        if ($to)   $db->where("DATE($column) <=", $to);
    }

    // ------------------------------------------------------------------
    // Dashboard
    // ------------------------------------------------------------------

    public function index() {
		$data['title'] = 'Automation Jobs Manager';
		$dateRange = $this->input->get('date_range');
		[$from, $to, $range_display] = $this->parse_date_range($dateRange);
		$data['date_range'] = $dateRange;
		$data['range_display'] = $range_display;

		// All cron jobs
		$data['cron_jobs'] = $this->db
			->order_by('is_active DESC, created_at DESC')
			->get('tb_jr_cron_jobs')
			->result();

		// Global stats from jobs table
		$jobsStats = $this->db->select('
				COUNT(*) as total_jobs,
				SUM(CASE WHEN is_active = 1 THEN 1 ELSE 0 END) as active_jobs
			')->get('tb_jr_cron_jobs')->row();

		// Total processed items (sum of processed_count) with date filter
		$processedQuery = $this->db->select('SUM(processed_count) as total_processed')
			->from('tb_jr_cron_execution_logs');
		$this->apply_date_range($processedQuery, 'created_at', $from, $to);
		$total_processed = $processedQuery->get()->row()->total_processed ?? 0;

		// Today's processed items (or successful in range)
		if (empty($dateRange)) {
			$todayProcessed = $this->db->select('SUM(processed_count) as cnt')
				->where('DATE(created_at)', date('Y-m-d'))
				->where('status', 'success')
				->get('tb_jr_cron_execution_logs')
				->row()->cnt ?? 0;
		} else {
			$successQuery = $this->db->select('SUM(processed_count) as cnt')
				->from('tb_jr_cron_execution_logs')
				->where('status', 'success');
			$this->apply_date_range($successQuery, 'created_at', $from, $to);
			$todayProcessed = $successQuery->get()->row()->cnt ?? 0;
		}

		// Execution stats
		$execQuery = $this->db->select('COUNT(*) as total_runs, SUM(processed_count) as total_processed')
			->from('tb_jr_cron_execution_logs');
		$this->apply_date_range($execQuery, 'created_at', $from, $to);
		$execution = $execQuery->get()->row();

		$data['stats'] = [
			'total_jobs'        => $jobsStats->total_jobs ?? 0,
			'active_jobs'       => $jobsStats->active_jobs ?? 0,
			'running_jobs'      => $this->db->where('is_running', 1)->count_all_results('tb_jr_cron_jobs'),
			'total_processed'   => $total_processed,
			'today_processed'   => $todayProcessed,
			'total_executions'  => $execution->total_runs ?? 0,
		];

		// Per‑job stats
		foreach ($data['cron_jobs'] as &$job) {
			// Recent executions (last 5)
			$recentQuery = $this->db->select('*')
				->from('tb_jr_cron_execution_logs')
				->where('cron_job_id', $job->id);
			$this->apply_date_range($recentQuery, 'created_at', $from, $to);
			$job->recent_executions = $recentQuery->order_by('created_at', 'DESC')->limit(5)->get()->result();

			// Overall and in‑range processed totals
			$totals = $this->db->select("
					SUM(processed_count) AS total_overall,
					SUM(CASE WHEN DATE(created_at) BETWEEN " . $this->db->escape($from) . " AND " . $this->db->escape($to) . " THEN processed_count ELSE 0 END) AS total_in_range
				", FALSE)
				->where('cron_job_id', $job->id)
				->get('tb_jr_cron_execution_logs')->row();
			$job->total_processed_overall = $totals->total_overall ?? 0;
			$job->total_processed_in_range = $totals->total_in_range ?? 0;

			// ──────────────────────────────────────────
			// 🔄 CHANGED: fetch created_at from log
			// ──────────────────────────────────────────
			$latestLog = $this->db->select('status, message, processed_count, failed_count, created_at')
				->where('cron_job_id', $job->id)
				->order_by('created_at', 'DESC')
				->limit(1)
				->get('tb_jr_cron_execution_logs')->row();

			if ($latestLog) {
				$processed = (int)$latestLog->processed_count;
				$failed    = (int)$latestLog->failed_count;
				$msg       = trim($latestLog->message);
				$job->last_message_full = $msg ?: 'No details';

				// ──────────────────────────────────────────
				// 🔄 CHANGED: format log’s own timestamp
				// ──────────────────────────────────────────
				$logTimeFormatted = date('M j, g:i A', strtotime($latestLog->created_at));

				switch ($latestLog->status) {
					case 'success':
						$statusMsg = "✅ Success: {$processed} processed";
						break;
					case 'partial':
						$statusMsg = "⚠️ Partial: {$processed} processed, {$failed} failed";
						break;
					case 'warning':
						$statusMsg = "⚠️ Warning: " . (strlen($msg) > 80 ? substr($msg,0,80).'…' : $msg);
						break;
					case 'failed':
						$statusMsg = "❌ Failed: " . (strlen($msg) > 80 ? substr($msg,0,80).'…' : $msg);
						break;
					default:
						$statusMsg = "ℹ️ " . ucfirst($latestLog->status);
				}

				// ──────────────────────────────────────────
				// 🔄 CHANGED: prepend the log timestamp
				// ──────────────────────────────────────────
				$job->last_message = $logTimeFormatted . ' – ' . $statusMsg;
			} else {
				$job->last_message      = "⏳ Never executed";
				$job->last_message_full = '';
			}

			// Today's stats (only if no date range)
			if (empty($dateRange)) {
				$job->today_stats = $this->db->select('SUM(processed_count) as processed_today')
					->where('cron_job_id', $job->id)
					->where('DATE(created_at)', date('Y-m-d'))
					->get('tb_jr_cron_execution_logs')->row();
			}
		}

		$data['content'] = $this->load->view('admin/cron_jobs/list', $data, TRUE);
		$this->load->view('templates/master', $data);
	}
    // ------------------------------------------------------------------
    // Create & Edit
    // ------------------------------------------------------------------

    public function create() {
        if ($this->input->method() === 'post') {
            $this->_handle_job_save();
            return;
        }
        $data['title'] = 'Create Automation Job';
        $data['context_options'] = $this->_get_context_options();
        $data['content'] = $this->load->view('admin/cron_jobs/create', $data, TRUE);
        $this->load->view('templates/master', $data);
    }

    public function edit($id) {
        $job = $this->db->get_where('tb_jr_cron_jobs', ['id' => $id])->row();
        if (!$job) show_404();

        if ($this->input->method() === 'post') {
            $this->_handle_job_save($id);
            return;
        }

        $data['title'] = 'Edit Automation Job';
        $data['job'] = $job;
        $data['context_options'] = $this->_get_context_options();
        $data['job_stats'] = $this->_get_job_detailed_stats($id);
        $data['content'] = $this->load->view('admin/cron_jobs/create', $data, TRUE);
        $this->load->view('templates/master', $data);
    }

    // ------------------------------------------------------------------
    // Toggle, Delete, Logs
    // ------------------------------------------------------------------

    public function toggle_status($id) {
        $job = $this->db->get_where('tb_jr_cron_jobs', ['id' => $id])->row();
        if (!$job) show_404();
        $this->db->where('id', $id)->update('tb_jr_cron_jobs', [
            'is_active' => $job->is_active ? 0 : 1,
            'updated_at' => date('Y-m-d H:i:s')
        ]);
        $this->session->set_flashdata('success', 'Job status updated.');
        redirect('admin/cron/Manage_cron');
    }

    public function delete($id) {
        $job = $this->db->get_where('tb_jr_cron_jobs', ['id' => $id])->row();
        if (!$job) show_404();
        $this->db->where('id', $id)->delete('tb_jr_cron_jobs');
        $this->session->set_flashdata('success', 'Job deleted successfully.');
        redirect('admin/cron/Manage_cron');
    }


    // ------------------------------------------------------------------
    // Private helpers
    // ------------------------------------------------------------------

    private function _handle_job_save(?int $id = null) {
        $this->form_validation->set_rules('name', 'Job Name', 'required|max_length[255]');
        $this->form_validation->set_rules('context', 'Context', 'required');
        $this->form_validation->set_rules('cron_model', 'Cron Model', 'required');
        $this->form_validation->set_rules('emails_per_run', 'Items Per Run', 'required|integer|greater_than[0]');
        $this->form_validation->set_rules('schedule_type', 'Schedule Type', 'required');
        $this->form_validation->set_rules('timezone', 'Timezone', 'required');

        if (!$this->form_validation->run()) {
            echo json_encode([
                'status' => 'error',
                'message' => validation_errors('<div class="error">', '</div>'),
                'csrf_token' => $this->csrf_json()
            ]);
            return;
        }

        $schedule = $this->_process_schedule_data();
        if (isset($schedule['error'])) {
            echo json_encode([
                'status' => 'error',
                'message' => $schedule['error'],
                'csrf_token' => $this->csrf_json()
            ]);
            return;
        }

        $data = [
            'name'           => $this->input->post('name'),
            'description'    => $this->input->post('description'),
            'context'        => $this->input->post('context'),
            'cron_model'     => $this->input->post('cron_model'),
			'email_service'  => $this->input->post('email_service', true),
            'emails_per_run' => $this->input->post('emails_per_run'),
            'timezone'       => $this->input->post('timezone'),
            'is_active'      => $this->input->post('is_active') ? 1 : 0,
        ];
        $data = array_merge($data, $schedule);

        if ($id) {
            $data['updated_at'] = date('Y-m-d H:i:s');
            $this->db->where('id', $id)->update('tb_jr_cron_jobs', $data);
            $message = 'Job updated successfully.';
        } else {
            $data['created_at'] = date('Y-m-d H:i:s');
            $this->db->insert('tb_jr_cron_jobs', $data);
            $message = 'Job created successfully!';
        }

        echo json_encode([
            'status' => 'success',
            'message' => $message,
            'redirect' => $id ? null : base_url('admin/cron/Manage_cron'),
            'csrf_token' => $this->csrf_json()
        ]);
    }

    private function _process_schedule_data(): array {
        $schedule_type   = $this->input->post('schedule_type');
        $custom_schedule = $this->input->post('custom_schedule');
        $start_time      = $this->input->post('start_time');
        $end_time        = $this->input->post('end_time');
        $week_days       = $this->input->post('week_days');
        $month_day       = $this->input->post('month_day');

        $schedule = [
            'schedule_type'   => $schedule_type,
            'custom_schedule' => null,
            'start_time'      => null,
            'end_time'        => null,
            'week_days'       => null,
            'month_day'       => null
        ];

        if ($schedule_type === 'custom') {
            if (empty($custom_schedule)) {
                return ['error' => 'Custom schedule date & time required'];
            }
            if (strtotime($custom_schedule) <= time()) {
                return ['error' => 'Custom schedule must be in the future'];
            }
            $schedule['custom_schedule'] = $custom_schedule;
        } else {
            if (empty($start_time) || empty($end_time)) {
                return ['error' => 'Start Time and End Time required'];
            }
            if (strtotime($start_time) >= strtotime($end_time)) {
                return ['error' => 'End Time must be greater than Start Time'];
            }
            $schedule['start_time'] = $start_time;
            $schedule['end_time']   = $end_time;

            if ($schedule_type === 'weekly') {
                if (empty($week_days)) {
                    return ['error' => 'At least one day must be selected for weekly schedule'];
                }
                $schedule['week_days'] = is_array($week_days) ? implode(',', $week_days) : $week_days;
            } elseif ($schedule_type === 'monthly') {
                if (empty($month_day) || $month_day < 1 || $month_day > 28) {
                    return ['error' => 'Month day must be between 1 and 28'];
                }
                $schedule['month_day'] = $month_day;
            }
        }
        return $schedule;
    }

    private function _get_context_options(): array {
        return [
            'job_recommendation'   => 'Job Recommendations',
            'newsletter'           => 'Newsletter',
            'profile_reminder'     => 'Profile Reminder',
            'registration_success' => 'Registration Success',
            'registration_welcome' => 'Registration Welcome',
            'forgot_password'      => 'Forgot Password',
            'interview_invitation' => 'Interview Invitation',
            'application_update'   => 'Application Update',
            'email_verification'   => 'Email Verification',
            'profile_rejected'     => 'Profile Rejected',
            'profile_approved'     => 'Profile Approved'
        ];
    }

    private function _get_job_detailed_stats($job_id) {
        $stats = new stdClass();

        $overall = $this->db->select('
                COUNT(*) as total_executions,
                SUM(processed_count) as total_processed,
                AVG(execution_time) as avg_execution_time,
                MIN(created_at) as first_execution,
                MAX(created_at) as last_execution
            ')
            ->where('cron_job_id', $job_id)
            ->get('tb_jr_cron_execution_logs')->row();
        $stats->overall = $overall;

        $success_rate = $this->db->select("
                COUNT(*) as total,
                SUM(CASE WHEN status='success' THEN 1 ELSE 0 END) as success,
                SUM(CASE WHEN status='failed' THEN 1 ELSE 0 END) as errors
            ")
            ->where('cron_job_id', $job_id)
            ->get('tb_jr_cron_execution_logs')->row();
        $stats->success_rate = $success_rate->total > 0 ? round(($success_rate->success/$success_rate->total)*100,2) : 0;
        $stats->error_rate = $success_rate->total > 0 ? round(($success_rate->errors/$success_rate->total)*100,2) : 0;

        // Monthly trend (last 6 months)
        $stats->monthly_trend = $this->db->select('
                DATE_FORMAT(created_at, "%Y-%m") as month,
                COUNT(*) as executions,
                SUM(processed_count) as items_processed,
                AVG(execution_time) as avg_time
            ')
            ->where('cron_job_id', $job_id)
            ->where('created_at >=', date('Y-m-01', strtotime('-6 months')))
            ->group_by('DATE_FORMAT(created_at, "%Y-%m")')
            ->order_by('month', 'DESC')
            ->get('tb_jr_cron_execution_logs')
            ->result();

        return $stats;
    }
}