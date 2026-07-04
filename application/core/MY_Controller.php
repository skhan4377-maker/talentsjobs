<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Controller extends CI_Controller {

    protected $user_permissions = [];

    public function __construct() {
        parent::__construct();

        if ($this->is_webhook_request()) return;

        $this->load->model('login_mdl');

        // 🔐 AUTH CHECK
        $this->check_auth();

        // 👑 SUPER ADMIN BYPASS (no permission checks)
        if ($this->session->userdata('role') === 'super_admin') {
            return;
        }

        // 🔒 PERMISSION CHECK
        $this->_check_permission();
    }

    /* ================= AUTH ================= */

    protected function check_auth() {
        $excluded = [
            'blogs','cartcontroller','careerservices','common','companiescontroller',
            'dashboard_api','forgot','forgotpassword','home','invoicecontroller',
            'jobalert_cron','jobrecommendation','jobs','leads','login','logout',
            'mastercron','registrationcontroller','resumeservicecontroller',
            'servicesfeatures','sitemap','support','track','unsubscribe','userauth',
            'userplan','verifications','mailclouder_webhook','razorpaywebhook',
            'payment','refund','resume_draft','contact','resume_templates','cities','redirect','push',
        ];

        if (in_array(strtolower($this->router->class), $excluded, true)) return;

        if (!$this->session->userdata('logged_in') || !$this->verify_token()) {
            redirect('auth/login');
            exit;
        }
    }

    private function verify_token() {
		$user_id = $this->session->userdata('user_id');
		$role    = $this->session->userdata('role');

		if (!$user_id || !$role) {
			return false;
		}

		// ✅ Only session check (since login_token removed)
		return true;
	}

    protected function is_webhook_request() {
        return in_array(strtolower($this->router->class), [
            'razorpaywebhook','mailclouder_webhook'
        ], true);
    }

    /* ================= PERMISSION CHECK ================= */

    private function _check_permission() {

        $this->config->load('permissions');
        $map = $this->config->item('permission_map');

        $directory = trim($this->router->directory, '/');
        $class     = strtolower($this->router->class);
        $method    = strtolower($this->router->method);

        $key = $directory ? $directory . '/' . $class : $class;

        $required_perm = null;

        if (isset($map[$key])) {
            $rule = $map[$key];

            if (is_string($rule)) {
                $required_perm = $rule;
            } elseif (is_array($rule)) {
                $required_perm = $rule[$method] ?? $rule['*'] ?? null;
            }
        }

        // ❌ No mapping found
        if ($required_perm === null) {
            log_message('error', "Permission mapping missing for: {$key}/{$method}");
            $this->_access_denied();
            return;
        }

        // ❌ Permission not granted
        if (!can($required_perm)) {
            log_message('info', "Permission '{$required_perm}' denied for {$key}/{$method}");
            $this->_access_denied();
        }
    }

    /* ================= ACCESS DENIED ================= */

    private function _access_denied() {
        if ($this->input->is_ajax_request()) {
            $this->output
                ->set_status_header(403)
                ->set_content_type('application/json')
                ->set_output(json_encode(['error' => 'Access denied']));
            exit;
        }
        show_error('You do not have permission to access this page.', 403, 'Access Denied');
    }
}