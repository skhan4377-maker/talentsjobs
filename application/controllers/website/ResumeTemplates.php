<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ResumeTemplates extends CI_Controller {

    public function index()
    {
        // Load the model (path matches your existing structure)
        $this->load->model('api/ResumeTemplates_model');

        // Fetch all active templates (set a reasonable limit)
        $templates = $this->ResumeTemplates_model->api_get_all(50, 0);

        // Enrich with download count and type for the view
        foreach ($templates as &$tmpl) {
            $tmpl['downloads'] = $this->ResumeTemplates_model->get_download_count($tmpl['template_id']);
            $tmpl['type']      = $tmpl['is_premium'] ? 'premium' : 'free';
        }
        unset($tmpl);

        // Resume token for candidate‑specific links
        $data['resumeToken'] = ($this->session->userdata('role') === 'candidate')
            ? $this->session->userdata('login_token')
            : null;

        $data['resumeTemplates'] = $templates;

        // Page meta
        $data['title']       = 'Resume Templates – Professional & ATS‑Friendly';
        $data['description'] = 'Browse 50+ free and premium resume templates. One‑click PDF download, recruiter‑approved.';

        $this->load->view('particles/header', $data);
		$this->load->view('particles/nav');
        $this->load->view('website/resume_templates/index', $data);
        $this->load->view('particles/footer');
    }
}