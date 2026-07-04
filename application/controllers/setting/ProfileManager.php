<?php defined('BASEPATH') or exit('No direct script access allowed');

class ProfileManager extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('setting/ProfileManager_mdl');
        $this->user_id = $this->session->userdata('user_id');
        $this->role    = $this->session->userdata('role');
    }

    /* ================= SETTINGS PAGE (ALL ROLES) ================= */

    public function index() {

        $data['title'] = 'Profile Settings';
        $profile = [];

        // 🎯 Employer Profile
        if ($this->role === 'employer') {
            $this->load->model('employer/Profile_mdl', 'Profile_mdl');
            $profile = $this->Profile_mdl->get_employer_details($this->user_id);
        }

        // 🎯 Candidate Profile
        elseif ($this->role === 'candidate') {
            $this->load->model('candidate/Profile_mdl', 'Profile_mdl');
            $profile = $this->Profile_mdl->get_candidate_details($this->user_id);
        }

        // 🎯 Other Roles (admin / super_admin / moderator / future roles)
        else {
            $profile = [
                'role' => $this->role,
                'status' => 'active',
                'membership_type' => null
            ];
        }

        $data['profile']         = $profile;
        $data['userType']        = $profile['role'] ?? $this->role;
        $data['profileStatus']   = $profile['status'] ?? 'active';
        $data['membership_type'] = $profile['membership_type'] ?? '';
        $data['status']          = $profile['status'] ?? 'active';

        $data['content'] = $this->load->view('setting/profile_manage', $data, TRUE);
        $this->load->view('templates/master', $data);
    }

    /* ================= STATUS TOGGLE (EMPLOYER & CANDIDATE ONLY) ================= */

    public function toggleProfileStatus() {

        // ❌ Only employer/candidate allowed
        if (!in_array($this->role, ['employer','candidate'])) {
            echo json_encode([
                'success' => false,
                'message' => 'Profile status change not allowed for this role.',
                'csrf_name' => $this->security->get_csrf_token_name(),
                'csrf_hash' => $this->security->get_csrf_hash()
            ]);
            return;
        }

        $newStatus = $this->input->post('status');

        // Load correct model
        if ($this->role === 'employer') {
            $this->load->model('employer/Profile_mdl');
            $details = $this->Profile_mdl->get_employer_details($this->user_id);
        } else {
            $this->load->model('candidate/Profile_mdl');
            $details = $this->Profile_mdl->get_candidate_details($this->user_id);
        }

        if (empty($details)) {
            echo json_encode([
                'success' => false,
                'message' => ucfirst($this->role).' details not found.',
                'csrf_name' => $this->security->get_csrf_token_name(),
                'csrf_hash' => $this->security->get_csrf_hash()
            ]);
            return;
        }

        // ❌ Block restricted statuses
        if (in_array($details['status'], ['under_review','rejected'])) {
            $messages = [
                'under_review' => 'Account Under Review. You cannot change status.',
                'rejected'     => 'Account Rejected. Activation not allowed.'
            ];
            echo json_encode([
                'success' => false,
                'message' => $messages[$details['status']],
                'csrf_name' => $this->security->get_csrf_token_name(),
                'csrf_hash' => $this->security->get_csrf_hash()
            ]);
            return;
        }

        // Update status
        $result = ($this->role === 'employer')
            ? $this->ProfileManager_mdl->updateEmployerProfileStatus($this->user_id, $newStatus)
            : $this->ProfileManager_mdl->updateCandidateProfileStatus($this->user_id, $newStatus);

        if ($result) {
            $response = [
                'success' => true,
                'status' => $newStatus,
                'logout' => false,
                'csrf_name' => $this->security->get_csrf_token_name(),
                'csrf_hash' => $this->security->get_csrf_hash()
            ];

            if ($newStatus == 2) {
                $this->session->sess_destroy();
                $response['logout'] = true;
            }

            echo json_encode($response);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Failed to update profile status.',
                'csrf_name' => $this->security->get_csrf_token_name(),
                'csrf_hash' => $this->security->get_csrf_hash()
            ]);
        }
    }

    /* ================= DELETE PROFILE (EMPLOYER & CANDIDATE ONLY) ================= */

    public function deleteProfile() {

        if (!in_array($this->role, ['employer','candidate'])) {
            echo json_encode([
                'success' => false,
                'message' => 'Profile deletion not allowed for this role.',
                'csrf_name' => $this->security->get_csrf_token_name(),
                'csrf_hash' => $this->security->get_csrf_hash()
            ]);
            return;
        }

        $canDelete = ($this->role === 'employer')
            ? $this->ProfileManager_mdl->canDeleteEmployer($this->user_id)
            : $this->ProfileManager_mdl->canDeleteCandidate($this->user_id);

        if (!$canDelete) {
            echo json_encode([
                'success' => false,
                'message' => 'Cannot delete profile with active payments.',
                'csrf_name' => $this->security->get_csrf_token_name(),
                'csrf_hash' => $this->security->get_csrf_hash()
            ]);
            return;
        }

        $result = ($this->role === 'employer')
            ? $this->ProfileManager_mdl->deleteEmployerProfile($this->user_id)
            : $this->ProfileManager_mdl->deleteCandidateProfile($this->user_id);

        if ($result) {
            $this->session->sess_destroy();
            echo json_encode([
                'success' => true,
                'message' => 'Profile deleted successfully.',
                'csrf_name' => $this->security->get_csrf_token_name(),
                'csrf_hash' => $this->security->get_csrf_hash()
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Failed to delete profile.',
                'csrf_name' => $this->security->get_csrf_token_name(),
                'csrf_hash' => $this->security->get_csrf_hash()
            ]);
        }
    }
}