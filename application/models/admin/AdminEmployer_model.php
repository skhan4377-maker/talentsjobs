<?php defined('BASEPATH') OR exit('No direct script access allowed');
class AdminEmployer_model extends CI_Model {

	 public function get_datatables($start, $length, $search, $order_column, $order_dir, $filters = []) {
		$this->apply_employer_filters($filters, true);

		$this->db->select('e.*, e.is_deleted, COALESCE(j.job_count, 0) as job_count');
		$this->db->from('tb_employer e');
		$this->db->join(
			'(SELECT employer_id, COUNT(*) as job_count FROM tb_post_job GROUP BY employer_id) j',
			'e.employer_id = j.employer_id',
			'left'
		);
		$this->db->where('e.role', 'employer');

		// Search Logic
		if (!empty($search)) {
			$this->db->group_start();
			$this->db->like('e.company_name', $search);
			$this->db->or_like('e.email', $search);
			$this->db->or_like('e.name', $search);
			$this->db->group_end();
		}

		$allowed_columns = ['company_name', 'name', 'email', 'status', 'created_at'];
		if (!in_array($order_column, $allowed_columns)) {
			$order_column = 'created_at';
		}

		// ✅ Custom order: Non-deleted first, then by latest registration
		$this->db->order_by('e.is_deleted', 'asc');      // Non-deleted first
		$this->db->order_by("e.$order_column", $order_dir); // User-defined secondary order
		$this->db->limit($length, $start);

		$query = $this->db->get()->result_array();

		foreach ($query as &$row) {
			$row['checkbox'] = '<input type="checkbox" class="employer-checkbox" value="' . $row['employer_id'] . '" data-email="' . htmlspecialchars($row['email']) . '">';

			$row['company_name'] = '<a href="#" class="text-blue-600 hover:underline view-employer" data-id="' . $row['employer_id'] . '">' . htmlspecialchars($row['company_name']) . '</a>';

			$verifiedBadge = ($row['is_verified'] == '1')
				? '<span class="ml-2 text-green-600 text-xs font-semibold">(Verified)</span>'
				: '<span class="ml-2 text-red-600 text-xs font-semibold">(Unverified)</span>';

			$row['email'] = htmlspecialchars($row['email']) . $verifiedBadge;

			// Status Badges
			if ($row['is_deleted'] == 1) {
				$row['status'] = '<span class="px-2 py-1 text-xs rounded-full bg-red-200 text-red-800 font-medium">Deleted</span>';
			} else {
				switch ($row['status']) {
					case 'active':
						$row['status'] = '<span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-700 font-medium">Active</span>';
						break;
					case 'inactive':
						$row['status'] = '<span class="px-2 py-1 text-xs rounded-full bg-gray-200 text-gray-700 font-medium">Inactive</span>';
						break;
					case 'under_review':
						$row['status'] = '<span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800 font-medium">Under Review</span>';
						break;
					case 'rejected':
						$row['status'] = '<span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-700 font-medium">Rejected</span>';
						break;
					default:
						$row['status'] = '<span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-600 font-medium">' . ucfirst($row['status']) . '</span>';
						break;
				}
			}

			$row['created_at'] = date('Y-m-d', strtotime($row['created_at']));

			$actions = '<div class="flex justify-center gap-2">';

			if (can('manage_job_posts', 'view')) {
				$actions .= '
					<a href="' . base_url('admin/employers/AdminEmployer/employer_jobs/' . $row['employer_id']) . '" title="View Jobs">
						<button class="p-2 bg-purple-100 hover:bg-purple-200 text-purple-600 rounded text-xs">
							<i class="fas fa-briefcase"></i>
						</button>
					</a>';
			}

			$actions .= '</div>';

			$row['actions'] = $actions;

		}

		return $query;
	}

	public function count_all() {	
		return $this->db->count_all_results('tb_employer');
	}

	public function count_filtered($search, $filters = []) {
		$this->apply_employer_filters($filters, true);
		$this->db->from('tb_employer e');	

		if (!empty($search)) {
			$this->db->group_start();
			$this->db->like('e.company_name', $search);
			$this->db->or_like('e.email', $search);
			$this->db->or_like('e.name', $search);
			$this->db->group_end();
		}
		return $this->db->count_all_results();
	}

	// Shared filter logic with alias support
	private function apply_employer_filters($filters, $useAlias = false) {
		$prefix = $useAlias ? 'e.' : ''; // Add alias prefix if needed

		if (!empty($filters['company_name'])) {
			$this->db->like($prefix . 'company_name', $filters['company_name']);
		}
		if (!empty($filters['email'])) {
			$this->db->like($prefix . 'email', $filters['email']);
		}
		if (!empty($filters['mobile'])) {
			$this->db->like($prefix . 'mobile', $filters['mobile']);
		}
		if (!empty($filters['status'])) {
			$this->db->where($prefix . 'status', $filters['status']);
		}
		if (!empty($filters['industry_id'])) {
			$this->db->where($prefix . 'industry_id', $filters['industry_id']);
		}
		if (!empty($filters['membership_type'])) {
			$this->db->where($prefix . 'membership_type', $filters['membership_type']);
		}
		if (!empty($filters['from_date'])) {
			$this->db->where($prefix . 'created_at >=', $filters['from_date']);
		}
		if (!empty($filters['to_date'])) {
			$this->db->where($prefix . 'created_at <=', $filters['to_date']);
		}
	}
	
	public function get_industries() {
		return $this->db
			->select('i.industry_id, i.industry_name')
			->from('tb_industry i')
			->join('tb_employer e', 'e.industry_id = i.industry_id', 'left')
			->group_by('i.industry_id')
			->order_by('i.industry_name', 'ASC')
			->get()
			->result_array();
	}

    public function get_employer($employer_id) {
		$this->db->select('e.*, c.city_name, i.industry_name,
			(SELECT COUNT(*) FROM tb_post_job j WHERE j.employer_id = e.employer_id AND j.is_deleted = 0) AS total_jobs,
			(SELECT MAX(j.created_at) FROM tb_post_job j WHERE j.employer_id = e.employer_id AND j.is_deleted = 0) AS last_job_posted_at
		')
		->from('tb_employer e')
		->join('tb_cities c', 'c.city_id = e.city_id', 'left')
		->join('tb_industry i', 'i.industry_id = e.industry_id', 'left')
		->where('e.employer_id', $employer_id);


		return $this->db->get()->row_array();
	}
	
	/*public function get_logs_by_employer($employer_id) {
		return $this->db
			->select('email_context, sent_at AS created_at, email_opened_at, profile_clicked_at, status')
			->where('user_id', $employer_id)
			->where('role', 'employer')
			->order_by('sent_at', 'DESC')
			->get('tb_cron_email_logs')
			->result_array();
	}*/


    // Update employer data
	 public function update_employer($where, $data) {
		return $this->db->where($where)->set($data)->update('tb_employer');
	}
	
	// Reject hone par sabhi jobs under_review ho
	public function set_employer_jobs_status($employer_id, $status) {
		$this->db->where('employer_id', $employer_id)
				 ->where('is_deleted', 0)
				 ->update('tb_post_job', ['status' => $status]);
	}

	// Active hone par sirf ek recent job active ho
	public function set_one_job_active($employer_id) {
		// ✅ Activate all jobs that are:
		// - under_review
		// - not deleted
		// - belong to this employer
		$this->db->where('employer_id', $employer_id)				
				 ->where('status', 'under_review')
				 ->update('tb_post_job', ['status' => 'active']);
	}


    public function get_employer_jobs_dt($employer_id, $start, $length, $search, $order_column, $order_dir, $filters = []) {
		$this->db->select('j.*, COUNT(a.applied_id) as applied_count');
		$this->db->from('tb_post_job j');
		$this->db->join('tb_applied a', 'a.job_id = j.job_id', 'left');
		$this->db->where('j.employer_id', $employer_id);

		$this->apply_job_filters($filters);

		if (!empty($search)) {
			$this->db->group_start();
			$this->db->like('j.job_title', $search);
			$this->db->or_like('j.location', $search);
			$this->db->group_end();
		}

		$this->db->group_by('j.job_id');

		$allowed_columns = ['job_title', 'status', 'applied_count', 'created_at', 'min_salary', 'min_experience'];
		if (!in_array($order_column, $allowed_columns)) {
			$order_column = 'j.created_at';
		}
		$this->db->order_by($order_column, $order_dir);
		$this->db->limit($length, $start);

		$result = $this->db->get()->result_array();

		foreach ($result as &$r) {
			$r['experience'] = $r['min_experience'] . ' - ' . $r['max_experience'] . ' yrs';
			$r['salary'] = '₹' . $r['min_salary'] . ' - ₹' . $r['max_salary'];
			if (!empty($r['salary_type'])) {
				$r['salary'] .= ' ' . ucfirst($r['salary_type']);
			}

			// Soft delete status check
			if (isset($r['is_deleted']) && $r['is_deleted'] == 1) {
				$r['status'] = 'Deleted';
				$r['actions'] = '<span class="text-red-600 font-semibold">Deleted</span>';
				continue; // skip action buttons
			} else {
				$r['status'] = ucfirst($r['status']);
			}

			$r['created_at'] = date('Y-m-d', strtotime($r['created_at']));

			// Action buttons
			$actions = '<div class="flex gap-2 justify-center">';

			if (can('manage_job_posts', 'view')) {
				$actions .= '
					<button class="view-job-btn text-gray-600 hover:text-gray-800" data-id="' . $r['job_id'] . '">
						<div class="p-2 rounded-lg bg-gray-100 hover:bg-gray-200"><i class="fas fa-eye"></i></div>
					</button>';
			}

			if (can('manage_job_posts', 'edit')) {
				$actions .= '
					<button class="open-status-modal text-indigo-600 hover:text-indigo-800" data-id="' . $r['job_id'] . '">
						<div class="p-2 rounded-lg bg-indigo-100 hover:bg-indigo-200"><i class="fas fa-exchange-alt"></i></div>
					</button>';
			}

			if (can('manage_job_posts', 'delete')) {
				$actions .= '
					<button class="delete-job-btn text-red-600 hover:text-red-800" data-id="' . $r['job_id'] . '">
						<div class="p-2 rounded-lg bg-red-100 hover:bg-red-200"><i class="fas fa-trash"></i></div>
					</button>';
			}

			$actions .= '</div>';
			$r['actions'] = $actions;
		}


		return $result;
	}

	
	public function count_employer_jobs($employer_id){
		$this->db->from('tb_post_job');
		$this->db->where('employer_id', $employer_id);
		//$this->db->where('is_deleted', 0);
		return $this->db->count_all_results();
	}

	public function count_employer_jobs_filtered($employer_id, $search, $filters = []) {
		$this->db->select('j.job_id'); // prevent duplicate columns in subquery
		$this->db->from('tb_post_job j');
		$this->db->join('tb_applied a', 'a.job_id = j.job_id', 'left');
		$this->db->where('j.employer_id', $employer_id);
		//$this->db->where('j.is_deleted', 0);
		$this->apply_job_filters($filters);

		if (!empty($search)) {
			$this->db->group_start();
			$this->db->like('j.job_title', $search);
			$this->db->or_like('j.location', $search);
			$this->db->group_end();
		}

		$this->db->group_by('j.job_id');
		return $this->db->count_all_results();
	}

	private function apply_job_filters($filters) {
		if (!empty($filters['job_title'])) {
			$this->db->like('j.job_title', $filters['job_title']); // Specify alias j
		}
		if (!empty($filters['status'])) {
			$this->db->where('j.status', $filters['status']); // Specify alias j
		}
		if (!empty($filters['industry_id'])) {
			$this->db->where('j.industry_id', $filters['industry_id']); // Specify alias j
		}
		//if (!empty($filters['functional_id'])) {
			//$this->db->where('j.functional_id', $filters['functional_id']); // Specify alias j
		//}
		if (!empty($filters['job_type'])) {
			$this->db->where('j.job_type', $filters['job_type']); // Specify alias j
		}
		if ($filters['is_paid'] !== '' && $filters['is_paid'] !== null) {
			$this->db->where('j.is_paid', $filters['is_paid']); // Specify alias j
		}
		if (!empty($filters['created_from'])) {
			$this->db->where('j.created_at >=', $filters['created_from'] . ' 00:00:00'); // Specify alias j
		}
		if (!empty($filters['created_to'])) {
			$this->db->where('j.created_at <=', $filters['created_to'] . ' 23:59:59'); // Specify alias j
		}

		// Additional filter for sorting - sorting by 'job_count' could be part of this if needed
	}

	public function get_job_statuses($employer_id) {
		return $this->db->distinct()
						->select('status')
						->from('tb_post_job')
						->where('employer_id', $employer_id)
						->get()
						->result_array();
	}

	// Only industries used in at least one job
	public function get_all_industries($employer_id) {
		return $this->db
			->select('i.industry_id as id, i.industry_name as name')
			->from('tb_industry i')
			->join('tb_post_job j', 'j.industry_id = i.industry_id', 'inner')
			->where('j.employer_id', $employer_id)
			->group_by('i.industry_id')
			->order_by('i.industry_name', 'ASC')
			->get()
			->result_array();
	}

	// Only functions used in at least one job
	/*public function get_all_functions($employer_id) {
		return $this->db
			->select('f.functional_id as id, f.functional_area as name')
			->from('functional_area f')
			->join('tb_post_job j', 'j.functional_id = f.functional_id', 'inner')
			->where('j.employer_id', $employer_id)
			->group_by('f.functional_id')
			->order_by('f.functional_area', 'ASC')
			->get()
			->result_array();
	}*/
		
	public function update_job_status($job_id, $status, $reason = null) {
		$data = ['status' => $status];
		if ($status === 'rejected' && $reason) {
			$data['rejection_reason'] = $reason;
		} else {
			$data['rejection_reason'] = null;
		}

		$this->db->where('job_id', $job_id)->update('tb_post_job', $data);
	}
	
	public function get_job($job_id) {
		// 1. Main Job + Employer
		$job = $this->db->select('
				j.*,
				e.company_name,
				e.email AS employer_email,
				e.status AS employer_status,
				i.industry_name,				
				c.city_name AS job_city
			')
			->from('tb_post_job j')
			->join('tb_employer e', 'e.employer_id = j.employer_id', 'left')
			->join('tb_industry i', 'i.industry_id = j.industry_id', 'left')		
			->join('tb_job_cities jc', 'jc.job_id = j.job_id', 'left')
			->join('tb_cities c', 'c.city_id = jc.city_id', 'left')
			->where('j.job_id', $job_id)
			->get()
			->row_array();

		if (!$job) return null;

		// 2. Load All Cities (in case of multiple)
		$cities = $this->db
			->select('c.city_name')
			->from('tb_job_cities jc')
			->join('tb_cities c', 'c.city_id = jc.city_id', 'inner')
			->where('jc.job_id', $job_id)
			->get()
			->result_array();

		$job['cities'] = array_column($cities, 'city_name');

		// 3. Load Skills
		$skills = $this->db
			->select('skill_name')
			->from('tb_job_skills')
			->where('job_id', $job_id)
			->get()
			->result_array();

		$job['skills'] = array_column($skills, 'skill_name');

		return $job;
	}
	
	public function soft_delete_job($job_id) {
		return $this->db->where('job_id', $job_id)
						->update('tb_post_job', [
							'is_deleted' => 1,
							'status' => 'rejected', // or 'suspended'
							'updated_at' => date('Y-m-d H:i:s')
						]);
	}

	
	public function bulk_soft_delete($ids = []) {
		if (empty($ids)) return false;

		$this->db->where_in('employer_id', $ids)
				 ->update('tb_employer', [
					'is_deleted' => 1,
					'status' => 'rejected',
					'updated_at' => date('Y-m-d H:i:s')
				 ]);

		// Also soft-delete their jobs
		$this->db->where_in('employer_id', $ids)
				 ->update('tb_post_job', [
					'is_deleted' => 1,
					'status' => 'rejected',
					'updated_at' => date('Y-m-d H:i:s')
				 ]);

		return true;
	}








}
