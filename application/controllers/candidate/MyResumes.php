<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MyResumes extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('candidate/Resume_model');
        $this->load->helper('url');
    }

    public function index() {
		$user_id = $this->session->userdata('user_id');
		if (!$user_id) {
			redirect('auth/login');
		}

		$resumes = $this->Resume_model->get_resumes_by_candidate($user_id);

		foreach ($resumes as &$r) {
			$json = json_decode($r['form_data'], true);
			if (!is_array($json)) {
				$json = [];
			}

			// --- 1. Basic info (just used for preview, not completion) ---
			$personal = $json['personal'] ?? $json;
			$firstName = $personal['first_name'] ?? $personal['firstName'] ?? '';
			$lastName  = $personal['last_name'] ?? $personal['lastName'] ?? '';
			$fullName  = $personal['full_name'] ?? $personal['fullName'] ?? $personal['name'] ?? '';
			$r['name'] = trim($firstName . ' ' . $lastName) ?: ($fullName ?: 'No Name');
			$r['email'] = $personal['email'] ?? $personal['email_address'] ?? '';
			$r['phone'] = $personal['phone'] ?? $personal['mobile'] ?? $personal['contact'] ?? '';

			// --- 2. Preview image ---
			$previewImages = $json['preview_images'] ?? [];
			$r['draft_preview'] = !empty($previewImages) ? $previewImages[0] : null;
			$r['preview_image_url'] = $r['draft_preview'] ?? ($r['preview_image'] ?? '');

			// --- 3. DYNAMIC COMPLETION (checks all sections) ---
			$sections = [];

			// Personal details (at least one core field filled)
			$sections[] = !empty($r['name']) || !empty($r['email']) || !empty($r['phone'])
						  || !empty($personal['address'] ?? $personal['location'] ?? '');

			// Professional summary / objective
			$sections[] = !empty($personal['summary'] ?? $personal['professional_summary'] ?? $json['summary'] ?? '');

			// Work experience (must have at least one entry with some data)
			$exp = $json['experience'] ?? $json['employment'] ?? [];
			$hasExp = false;
			if (is_array($exp) && count($exp) > 0) {
				foreach ($exp as $item) {
					if (!empty($item['title'] ?? $item['company'] ?? $item['description'] ?? '')) {
						$hasExp = true;
						break;
					}
				}
			}
			$sections[] = $hasExp;

			// Education
			$edu = $json['education'] ?? [];
			$hasEdu = false;
			if (is_array($edu) && count($edu) > 0) {
				foreach ($edu as $item) {
					if (!empty($item['school'] ?? $item['degree'] ?? $item['field'] ?? '')) {
						$hasEdu = true;
						break;
					}
				}
			}
			$sections[] = $hasEdu;

			// Skills
			$skills = $json['skills'] ?? $personal['skills'] ?? [];
			$sections[] = is_array($skills) && count($skills) > 0;

			// Languages
			$languages = $json['languages'] ?? [];
			$sections[] = is_array($languages) && count($languages) > 0;

			// Certifications / Courses
			$certs = $json['certifications'] ?? $json['courses'] ?? [];
			$sections[] = is_array($certs) && count($certs) > 0;

			// Projects
			$projects = $json['projects'] ?? [];
			$hasProj = false;
			if (is_array($projects) && count($projects) > 0) {
				foreach ($projects as $item) {
					if (!empty($item['name'] ?? $item['description'] ?? '')) {
						$hasProj = true;
						break;
					}
				}
			}
			$sections[] = $hasProj;

			// Achievements / Awards
			$ach = $json['achievements'] ?? $json['awards'] ?? [];
			$sections[] = is_array($ach) && count($ach) > 0;

			// Hobbies / Interests (optional, but included for completeness)
			$hobbies = $json['hobbies'] ?? $json['interests'] ?? [];
			$sections[] = is_array($hobbies) && count($hobbies) > 0;

			// Volunteer / Extracurricular
			$vol = $json['volunteer'] ?? $json['extracurricular'] ?? [];
			$sections[] = is_array($vol) && count($vol) > 0;

			// References
			$refs = $json['references'] ?? [];
			$sections[] = is_array($refs) && count($refs) > 0;

			// Calculate percentage
			$filled = count(array_filter($sections));
			$totalSections = count($sections);
			$r['completion'] = $totalSections > 0 ? round(($filled / $totalSections) * 100) : 0;

			// --- 4. Auto-finalize ---
			if ($r['completion'] == 100 && $r['is_finalized'] == 0) {
				$this->db->where('draft_id', $r['draft_id'])
						 ->update('tb_ft_resume_drafts', ['is_finalized' => 1]);
				$r['is_finalized'] = 1;
			}

			// --- 5. Status labels ---
			if ($r['completion'] == 100) {
				$r['status'] = 'Completed';
				$r['status_color'] = 'green';
			} elseif ($r['completion'] >= 50) {
				$r['status'] = 'In Progress';
				$r['status_color'] = 'blue';
			} else {
				$r['status'] = 'Draft';
				$r['status_color'] = 'yellow';
			}
		}

		// Stats
		$data['resumes']         = $resumes;
		$data['total']           = count($resumes);
		$data['completed']       = count(array_filter($resumes, fn($r) => $r['completion'] == 100));
		$data['draft']           = count(array_filter($resumes, fn($r) => $r['completion'] < 100));
		$data['total_downloads'] = $this->Resume_model->get_total_downloads($user_id);
		$data['has_active_plan'] = $this->Resume_model->has_active_plan($user_id);
		$data['title']           = 'My Resumes';

		$data['content'] = $this->load->view('candidate/my_resumes', $data, TRUE);
		$this->load->view('templates/master', $data);
	}

    /*public function delete($id) {
        $user_id = $this->session->userdata('user_id');
        if ($user_id && $id) {
            $this->Resume_model->delete_draft($id, $user_id);
        }
        redirect('my-resumes');
    }*/
}