<?php
class Interview_model extends CI_Model {

    public function schedule($applied_id, $data) {
        try {
            $interview_datetime = date('Y-m-d H:i:s', strtotime($data['interview_date'] . ' ' . $data['interview_time']));
            if ($interview_datetime < date('Y-m-d H:i:s')) {
                throw new Exception("Interview datetime cannot be in the past");
            }

            $interview_data = [
                'interview_date'  => $data['interview_date'],
                'interview_time'  => $data['interview_time'],
                'interview_type'  => $data['interview_type'],
                'interview_link'  => $data['interview_link'] ?? null,
                'notes'           => $data['notes'] ?? null,
                'status'          => 'Scheduled',
                'updated_at'      => date('Y-m-d H:i:s')
            ];

            // Check if interview already exists
            $existing = $this->db->get_where('tb_interviews', ['applied_id' => $applied_id])->row();

            if ($existing) {
                $this->db->where('interview_id', $existing->interview_id)
                         ->update('tb_interviews', $interview_data);
                $interview_id = $existing->interview_id;
            } else {
                $interview_data['applied_id'] = $applied_id;
                $interview_data['created_at'] = date('Y-m-d H:i:s');
                $this->db->insert('tb_interviews', $interview_data);
                $interview_id = $this->db->insert_id();
            }

            // Update application status
            $this->load->model('employer/PostJobModel');
            $this->PostJobModel->update_application_status($applied_id, 'Scheduled');

            return $interview_id;
        } catch (Exception $e) {
            log_message('error', 'Error scheduling interview: ' . $e->getMessage());
            return false;
        }
    }

    public function get_upcoming($employer_id)
	{
		// AUTO MARK MISSED INTERVIEWS
		$this->db->query("
			UPDATE tb_interviews
			SET status = 'Missed'
			WHERE CONCAT(interview_date, ' ', interview_time) < NOW()
			AND status = 'Scheduled'
		");

		// SYNC APPLICATION STAGE
		$this->db->query("
			UPDATE tb_applied a
			JOIN tb_interviews i ON i.applied_id = a.applied_id
			SET a.ApplicationStage = i.status
			WHERE a.ApplicationStage != i.status
		");

		// INTERVIEW LIST
		$this->db->select("
			i.*,
			a.ApplicationStage,
			c.name as candidate_name,
			c.logo,
			j.job_title,
			(
				SELECT EXISTS (
					SELECT 1
					FROM tb_ft_user_purchases up
					WHERE up.user_id = c.candidate_id
					AND up.status = 'active'
					AND up.start_date <= NOW()
					AND up.end_date >= NOW()
				)
			) AS has_active_plan
		");

		$this->db->from('tb_interviews i');

		$this->db->join('tb_applied a', 'a.applied_id = i.applied_id');

		$this->db->join('tb_candidate c', 'c.candidate_id = a.candidate_id');

		$this->db->join('tb_post_job j', 'j.job_id = a.job_id');

		$this->db->where('j.employer_id', $employer_id);

		// PREMIUM FIRST
		$this->db->order_by('has_active_plan', 'DESC');

		// UPCOMING FIRST
		$this->db->order_by('i.interview_date ASC, i.interview_time ASC');

		return $this->db->get()->result_array();
	}

    public function count_pending_interviews($employer_id) {
        $this->db->from('tb_interviews i');
        $this->db->join('tb_applied a', 'i.applied_id = a.applied_id');
        $this->db->join('tb_post_job j', 'a.job_id = j.job_id');
        $this->db->where('j.employer_id', $employer_id);
        $this->db->where('i.status', 'Scheduled');
        return $this->db->count_all_results();
    }

    public function update_interview($interview_id, $data) {
        unset($data['participants']);
        $data['updated_at'] = date('Y-m-d H:i:s');
        $this->db->where('interview_id', $interview_id);
        return $this->db->update('tb_interviews', $data);
    }

    public function get_interview($interview_id, $employer_id = null) {
        $this->db->select('i.*, a.applied_id, j.job_title, j.employer_id, c.name as candidate_name, c.email as candidate_email');
        $this->db->from('tb_interviews i');
        $this->db->join('tb_applied a', 'i.applied_id = a.applied_id');
        $this->db->join('tb_post_job j', 'a.job_id = j.job_id');
        $this->db->join('tb_candidate c', 'a.candidate_id = c.candidate_id');
        $this->db->where('i.interview_id', $interview_id);

        if ($employer_id) {
            $this->db->where('j.employer_id', $employer_id);
        }

        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            return $query->row_array();
        }

        return false;
    }

    public function get_interview_for_candidate($interview_id, $candidate_id) {
        return $this->db
            ->select('
                i.*,
                j.job_id,
                j.job_title,
                e.company_name,
                e.logo AS employer_logo,
                a.ApplicationStage,
                e.email AS employer_email,
                e.mobile AS employer_mobile
            ')
            ->from('tb_interviews i')
            ->join('tb_applied a', 'a.applied_id = i.applied_id')
            ->join('tb_post_job j', 'j.job_id = a.job_id')
            ->join('tb_employer e', 'e.employer_id = j.employer_id')
            ->where('i.interview_id', $interview_id)
            ->where('a.candidate_id', $candidate_id)
            ->get()
            ->row();
    }

    public function get_by_applied_id($applied_id, $employer_id) {
        return $this->db
            ->select('i.*')
            ->from('tb_interviews i')
            ->join('tb_applied a', 'a.applied_id = i.applied_id')
            ->join('tb_post_job j', 'a.job_id = j.job_id')
            ->where('i.applied_id', $applied_id)
            ->where('j.employer_id', $employer_id)
            ->get()
            ->row_array();
    }

}