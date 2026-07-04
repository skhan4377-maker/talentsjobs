<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ResumeDownload extends CI_Controller {

    public function __construct() { 
        parent::__construct();
		// Load necessary libraries, models
        $this->load->model('admin/services/CareerService_model');
    }
    

	public function handle_export() {
		header('Content-Type: application/json; charset=utf-8');

		$template_id = $this->security->xss_clean($this->input->post('template_id', true));
		$export_type = $this->security->xss_clean($this->input->post('export_type', true));
		$user_id     = $this->session->userdata('user_id');

		if (!$user_id || !$template_id) {
			return $this->respond_error('Invalid request.');
		}

		$this->load->model('admin/services/ResumeTemplateModel');
		$this->load->model('CareerService_model');

		$template = $this->ResumeTemplateModel->get_template_metadata($user_id, $template_id);
		if (!$template) {
			return $this->respond_error('Template not found.');
		}

		$download_url = ''; // Placeholder for actual download URL

		$track_download = function () use ($user_id, $template_id) {
			return $this->track_download($user_id, $template_id);
		};

		// ----------------- FREE TEMPLATE -----------------
		if ($template['template_type'] === 'free') {
			$download_url = $this->generate_resume_pdf($user_id, $template_id, $template);
			$track_download();
			return $this->respond_success($export_type, $download_url);
		}

		// ----------------- PAID TEMPLATE -----------------
		if (empty($template['duration_id']) || empty($template['feature_id'])) {
			return $this->respond_error('Invalid plan data.');
		}

		$duration_id = $template['duration_id'];
		$feature_id  = $template['feature_id'];

		if (!is_numeric($duration_id) || !is_numeric($feature_id)) {
			return $this->respond_error('Invalid input data.');
		}

		if ($this->has_active_plan($user_id, $duration_id)) {
			$download_url = $this->generate_resume_pdf($user_id, $template_id, $template);
			$track_download();
			return $this->respond_success($export_type, $download_url);
		}

		// ----------------- ADD TO CART -----------------
		$plan = $this->CareerService_model->get_plan_details_by_duration_id($duration_id, $feature_id);
		if (!$plan) {
			return $this->respond_error('Invalid plan selected.');
		}

		$this->update_cart_if_needed($user_id, $feature_id, $duration_id);

		echo json_encode([
			'status' => 'redirect',
			'url'    => base_url("career-services/resume-builder")
		]);
	}

	private function has_active_plan($user_id, $duration_id) {
		$this->db->select('pyy.*, sp.*')
				 ->from('tb_career_service_payments pyy')
				 ->join('tb_career_service_purchased_plans sp', 'sp.payment_id = pyy.payment_id')
				 ->where('pyy.user_id', $user_id)
				 ->where('sp.duration_id', $duration_id)
				 ->where('sp.purchase_date <=', date('Y-m-d H:i:s'))
				 ->where('sp.end_date >=', date('Y-m-d H:i:s'));

		$result = $this->db->get();
		return ($result && $result->num_rows() > 0);
	}

	private function track_download($user_id, $template_id) {
		$this->db->where(['user_id' => $user_id, 'template_id' => $template_id]);
		$exists = $this->db->get('resume_downloads')->row();

		if ($exists) return false;

		$data = [
			'user_id'       => $user_id,
			'template_id'   => $template_id,
			'downloaded_at' => date('Y-m-d H:i:s')
		];
		return $this->db->insert('resume_downloads', $data);
	}

	private function update_cart_if_needed($user_id, $feature_id, $duration_id) {
		$this->db->where(compact('user_id', 'duration_id', 'feature_id'));
		$exists = $this->db->get('career_service_cart')->row();

		if ($exists) {
			$this->db->where('id', $exists->id)
					 ->update('career_service_cart', ['updated_at' => date('Y-m-d H:i:s')]);
		} else {
			$this->db->insert('career_service_cart', [
				'user_id'     => $user_id,
				'feature_id'  => $feature_id,
				'duration_id' => $duration_id,
				'created_at'  => date('Y-m-d H:i:s')
			]);
		}
	}
	
	public function generate_resume_pdf($user_id, $template_id, $template) {
		// Load profile model
		$this->load->model('candidate/Profile_mdl');

		// Personal details
		$data['personal'] = $this->Profile_mdl->get_candidate_details($user_id);

		// Technical Skills (fixed anonymous function)
		$skillsStr  = $data['personal']['skills'];  // "oracle2, Javascript, html, css, python"
		$skillsArr  = array_filter(array_map('trim', explode(',', $skillsStr)));
		$data['skills'] = array_map(
			function($s) {
				return ['skill' => $s];
			},
			$skillsArr
		);

		// Work experiences
		$data['experiences'] = $this->Profile_mdl->get_work_experiences($user_id);

		// Educations
		$data['educations'] = $this->Profile_mdl->get_educations($user_id);

		// Internships
		$data['internships'] = $this->Profile_mdl->get_internships($user_id);

		// Certifications
		$data['certifications'] = $this->Profile_mdl->get_certifications($user_id);

		// Courses
		$data['courses'] = $this->Profile_mdl->get_courses($user_id);

		// Awards
		$data['awards'] = $this->Profile_mdl->get_awards($user_id);

		// Hobbies
		$data['hobbies']  = $this->Profile_mdl->get_hobbies($user_id);

		// Projects
		$data['projects']  = $this->Profile_mdl->get_projects($user_id);

		// Extra‑Curricular Activities
		$data['extraCurricularActivities'] = $this->Profile_mdl->get_extraCurricularActivities($user_id);

		// Languages
		$data['languages'] = $this->Profile_mdl->get_languages($user_id);

		// Template HTML & CSS
		$layout_html = isset($template['layout_html']) ? $template['layout_html'] : '';

		// Parse the HTML with the data
		$parsed_html = $this->parse_template_html($layout_html, $data);

		// Load PDF generation library
		$this->load->library('pdf');

		// Ensure DOMPDF options are configured
		$options = new Dompdf\Options();
		$options->set('isRemoteEnabled', true); // Allow external images like profile photo
		$options->set('defaultFont', 'DejaVu Sans'); // Support for Hindi, symbols, etc.
		$options->set('isHtml5ParserEnabled', true);
		$options->set('isPhpEnabled', true);


		$this->pdf->setOptions($options);

		// Load HTML content
		$this->pdf->loadHtml($parsed_html);

		// Setup paper
		$this->pdf->setPaper('A4', 'portrait');

		// Render PDF
		$this->pdf->render();

		// Prepare file path
		$pdf_filename = "resume_{$user_id}_{$template_id}.pdf";
		$export_dir = FCPATH . 'exports/';
		$file_path = $export_dir . $pdf_filename;

		// Ensure export directory exists
		if (!is_dir($export_dir)) {
			mkdir($export_dir, 0755, true);
		}

		// Save the PDF to the server
		file_put_contents($file_path, $this->pdf->output());

		// Return the downloadable URL
		return base_url("exports/{$pdf_filename}");

	}
	
	private function parse_template_html($layout_html, $data) {
		// Personal details replacement
		if (isset($data['personal']) && is_array($data['personal'])) {
			foreach ($data['personal'] as $key => $value) {
				$placeholder = "{{{$key}}}";
				if (strpos($layout_html, $placeholder) !== false) {
					$layout_html = str_replace($placeholder, html_escape($value), $layout_html);
				}
			}
		}

		// Process section content dynamically
		$sections = [
			'skills',
			'experiences',
			'educations',
			'internships',
			'certifications',
			'courses',
			'awards',
			'hobbies',
			'projects',
			'extraCurricularActivities',
			'languages'
		];

		foreach ($sections as $section) {
			$pattern = '/{{#'.$section.'}}(.*?){{\/'.$section.'}}/s';
			if (preg_match($pattern, $layout_html, $m)) {
				$block = '';
				if (!empty($data[$section])) {
					foreach ($data[$section] as $item) {
						$temp = $m[1];
						foreach ($item as $k => $v) {
							$temp = str_replace("{{{$k}}}", html_escape($v), $temp);
						}
						$block .= $temp;
					}
					$layout_html = str_replace($m[0], $block, $layout_html);
				} else {
					$layout_html = str_replace($m[0], '', $layout_html);
				}
			}
		}

		// Return the parsed HTML
		return $layout_html;
	}

	private function prepare_skills($skillsStr) {
		// Split the string by commas and remove any extra spaces
		$skillsArr = array_filter(array_map('trim', explode(',', $skillsStr)));

		// Return an array where each skill is wrapped in a 'skill' key, which will be used in the template
		return array_map(function($skill) {
			return ['skill' => $skill];
		}, $skillsArr);
	}

	private function respond_success($export_type, $download_url) {
		echo json_encode([
			'status'       => 'ok',
			'export_type'  => $export_type,
			'download_url' => $download_url
		]);
	}

	private function respond_error($message) {
		echo json_encode([
			'status'  => 'error',
			'message' => $message
		]);
	}

	
}