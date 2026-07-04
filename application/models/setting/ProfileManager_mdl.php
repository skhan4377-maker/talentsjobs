<?php 
class ProfileManager_mdl extends CI_Model {
    
    public function updateEmployerProfileStatus($userId, $status) {
        $data = ['status' => $status];
        $this->db->where('employer_id', $userId);
        return $this->db->update('tb_employer', $data);
    }

    public function updateCandidateProfileStatus($userId, $status) {
        $data = ['status' => $status];
        $this->db->where('candidate_id', $userId);
        return $this->db->update('tb_candidate', $data);
    }

    public function deleteEmployerProfile($userId) {
        $this->db->trans_start();

        // 1. Check for successful payments in tb_payments
        $this->db->select('p.id');
        $this->db->from('tb_payments p');
        $this->db->join('tb_plan_purchases pp', 'pp.payment_id = p.id', 'left');
        $this->db->where('pp.employer_id', $userId);
        $this->db->where('p.status', 'success');
        $paymentHistory = $this->db->get()->row();

        if ($paymentHistory) {
            $this->db->trans_rollback();
            return false;
        }

        // 2. Get job IDs for this employer
        $this->db->where('employer_id', $userId);
        $jobs = $this->db->select('job_id')->get('tb_post_job')->result_array();

        // 3. Soft delete jobs
        $this->db->where('employer_id', $userId);
        $this->db->update('tb_post_job', ['is_deleted' => 1]);

        // 4. Soft delete related job applications and logs
        if (!empty($jobs)) {
            $jobIds = array_column($jobs, 'job_id');
            
            // Soft delete applications for these jobs
            $this->db->where_in('job_id', $jobIds);
            $this->db->update('tb_applied', ['is_deleted' => 1]);
            
            // Soft delete application logs for these jobs
            $this->db->where_in('job_id', $jobIds);
            $this->db->update('tb_application_logs', ['is_deleted' => 1]);
            
            // Soft delete job skills
            $this->db->where_in('job_id', $jobIds);
            $this->db->update('tb_job_skills', ['is_deleted' => 1]);
            
            // Soft delete job cities
            $this->db->where_in('job_id', $jobIds);
            $this->db->update('tb_job_cities', ['is_deleted' => 1]);
        }

        // 5. Soft delete notifications for employer
        $this->db->where('user_id', $userId);
        $this->db->update('tb_notifications', ['is_deleted' => 1]);

        // 6. Soft delete plan purchases
        $this->db->where('employer_id', $userId);
        $this->db->update('tb_plan_purchases', ['is_deleted' => 1]);

        // 7. Soft delete employer profile
        $this->db->where('employer_id', $userId);
        $this->db->update('tb_employer', [
            'is_deleted' => 1,
            'status' => 'inactive',
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        $this->db->trans_complete();
        return $this->db->trans_status();
    }

    public function deleteCandidateProfile($userId) {
        $this->db->trans_start();

        // 1. Check if the user has any successful payments in tb_payments
        $this->db->select('id');
        $this->db->from('tb_payments');
        $this->db->where('user_id', $userId);
        $this->db->where('status', 'success');
        $payment = $this->db->get()->row();

        // If payment exists, rollback and abort
        if ($payment) {
            $this->db->trans_rollback();
            return false;
        }

        // 2. Check for active user plans
        $this->db->select('id');
        $this->db->from('tb_user_plans');
        $this->db->where('user_id', $userId);
        $this->db->where('status', 'active');
        $activePlan = $this->db->get()->row();

        // If active plan exists, rollback and abort
        if ($activePlan) {
            $this->db->trans_rollback();
            return false;
        }

        // 3. Soft delete all related candidate data
        
        // Applications
        $this->db->where('candidate_id', $userId);
        $this->db->update('tb_applied', ['is_deleted' => 1]);
        
        // Application logs
        $this->db->where('candidate_id', $userId);
        $this->db->update('tb_application_logs', ['is_deleted' => 1]);
        
        // Employment history
        $this->db->where('candidate_id', $userId);
        $this->db->update('tb_employment_history', ['is_deleted' => 1]);

        // Favorites
        $this->db->where('candidate_id', $userId);
        $this->db->update('tb_favourite', ['is_deleted' => 1]);

        // Notifications
        $this->db->where('user_id', $userId);
        $this->db->update('tb_notifications', ['is_deleted' => 1]);

        // Candidate skills
        $this->db->where('candidate_id', $userId);
        $this->db->update('tb_candidate_skills', ['is_deleted' => 1]);

        // Preferred locations
        $this->db->where('candidate_id', $userId);
        $this->db->update('tb_candidate_preferred_locations', ['is_deleted' => 1]);

        // Education history
        $this->db->where('candidate_id', $userId);
        $this->db->update('tb_education_history', ['is_deleted' => 1]);

        // Projects
        $this->db->where('candidate_id', $userId);
        $this->db->update('tb_projects', ['is_deleted' => 1]);

        // Certifications
        $this->db->where('candidate_id', $userId);
        $this->db->update('tb_certifications', ['is_deleted' => 1]);

        // Awards
        $this->db->where('candidate_id', $userId);
        $this->db->update('tb_awards', ['is_deleted' => 1]);

        // Courses
        $this->db->where('candidate_id', $userId);
        $this->db->update('tb_courses', ['is_deleted' => 1]);

        // Hobbies
        $this->db->where('candidate_id', $userId);
        $this->db->update('tb_hobbies', ['is_deleted' => 1]);

        // Languages
        $this->db->where('candidate_id', $userId);
        $this->db->update('tb_language', ['is_deleted' => 1]);

        // Internships
        $this->db->where('candidate_id', $userId);
        $this->db->update('tb_internships', ['is_deleted' => 1]);

        // Resume drafts
        $this->db->where('user_id', $userId);
        $this->db->update('tb_resume_drafts', ['is_deleted' => 1]);

        // User plans (set to cancelled)
        $this->db->where('user_id', $userId);
        $this->db->update('tb_user_plans', ['status' => 'cancelled']);

        // 4. Soft delete main candidate record
        $this->db->where('candidate_id', $userId);
        $this->db->update('tb_candidate', [
            'is_deleted' => 1,
            'status' => 'inactive',
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        $this->db->trans_complete();
        return $this->db->trans_status();
    }

    /**
     * Check if employer can be deleted (no active payments)
     */
    public function canDeleteEmployer($userId) {
        $this->db->select('p.id');
        $this->db->from('tb_payments p');
        $this->db->join('tb_plan_purchases pp', 'pp.payment_id = p.id', 'left');
        $this->db->where('pp.employer_id', $userId);
        $this->db->where('p.status', 'success');
        return $this->db->get()->row() === null;
    }

    /**
     * Check if candidate can be deleted (no active payments or plans)
     */
    public function canDeleteCandidate($userId) {
        // Check for successful payments
        $this->db->select('id');
        $this->db->from('tb_payments');
        $this->db->where('user_id', $userId);
        $this->db->where('status', 'success');
        $payment = $this->db->get()->row();

        if ($payment) {
            return false;
        }

        // Check for active plans
        $this->db->select('id');
        $this->db->from('tb_user_plans');
        $this->db->where('user_id', $userId);
        $this->db->where('status', 'active');
        $activePlan = $this->db->get()->row();

        return $activePlan === null;
    }
}
?>