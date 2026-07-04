<?php defined('BASEPATH') OR exit('No direct script access allowed');
date_default_timezone_set('Asia/Kolkata');
// ------------------------------------------------------------------------
class Applied extends MY_Controller {
    protected $user_id;
  
    public function __construct(){
        parent::__construct();      
        $this->load->model('candidate/Profile_mdl');
        $this->load->model('candidate/Applied_mdl');
		$this->load->model('candidate/Candidate_plan_model'); 
        $this->user_id = $this->session->userdata('user_id');
        $this->name    = $this->session->userdata('name');
    } 
    
    public function index() {    
        $data['title'] = 'Applied Jobs';          
        $data['content'] = $this->load->view('candidate/myapply', $data, TRUE);
        $this->load->view('templates/master', $data);
    }
        
    public function toggleFavoriteStatus() {
        $pid = $this->input->post('pid', true);
        if ($pid) {
            $isAlreadyFavorited = $this->Applied_mdl
                  ->check_applied('favourite', [
                      'candidate_id' => $this->user_id,
                      'job_id'       => $pid
                  ]);

            if ($isAlreadyFavorited > 0) {
                $this->db->delete('favourite', [
                    'candidate_id' => $this->user_id,
                    'job_id'       => $pid
                ]);
                $response = ['status' => 'success', 'action' => 'unfavorited'];
            } else {
                $this->db->insert('favourite', [
                    'job_id'       => $pid,
                    'candidate_id' => $this->user_id,
                    'status'       => '1',
                    'create_dt'    => date('Y-m-d H:i:s')
                ]);
                $response = ['status' => 'success', 'action' => 'favorited'];
            }
        } else {
            $response = ['status' => 'error', 'message' => 'Invalid job ID.'];
        }

        $response['csrf_token'] = $this->security->get_csrf_hash();
        echo json_encode($response);
        exit;
    }
        
    public function submitJobApplication() {
        $success = false;
        $return_data = array();

        $this->load->library('form_validation');
        $this->form_validation->set_rules('pid', 'Job Post ID', 'required');

        $profile = $this->Profile_mdl->get_candidate_details($this->user_id);

        if (empty($profile['industry_id'])) {
            $this->form_validation->set_rules('industry_id', 'Industry', 'required');
        }
        if (empty($profile['functional_id'])) {
            $this->form_validation->set_rules('functional_id', 'Functional Area', 'required');
        }
        if (empty($profile['city_id'])) {
            $this->form_validation->set_rules('city_id', 'Current Location', 'required');
        }
        if (empty($profile['work_status'])) {
            $this->form_validation->set_rules('work_status', 'Work Status', 'required');
        }

        if ($this->form_validation->run() == FALSE) {
            $return_data['error_msg'] = validation_errors();
            $return_data['success']   = false;
            $return_data['csrf_token'] = $this->security->get_csrf_hash();
            $return_data['csrf_name']  = $this->security->get_csrf_token_name();
            echo json_encode($return_data);
            exit;
        }

        $pid = $this->input->post('pid', true);
        $post_job_result = $this->Applied_mdl->get_post_details($pid);
        $post_job = $post_job_result['data'];

        $enable_link    = $post_job['enable_apply_link'];
        $apply_web_link = $post_job['apply_web_link'];

        $appliedDetail = [
            'name'        => $profile['name'],
            'job_title'   => $post_job['job_title'],
            'company_name'=> $post_job['company_name']
        ];

        // Update profile if requested
        if ($this->input->post('complete_profle', true) == 'update') {
            $project_data = [
                'industry_id'    => $this->input->post('industry_id'),
                'functional_id'  => $this->input->post('functional_id'),
                'city_id'        => $this->input->post('city_id'),
                'work_status'    => strtolower($this->input->post('work_status'))
            ];
            $this->Profile_mdl->update_candidate_details($this->user_id, $project_data);
        }

        $appliedJob = [
            'job_id'       => $pid,
            'candidate_id' => $this->user_id,
            'created_at'   => date('Y-m-d H:i:s')
        ];

        // External link apply
        if ($enable_link == 'yes') {
            $this->insertAppliedJob($appliedJob);
            $return_data['success']     = true;
            $return_data['success_msg'] = 'Redirecting your application...';
            $return_data['redirect']    = $apply_web_link;
            $return_data['csrf_token']  = $this->security->get_csrf_hash();
            $return_data['csrf_name']   = $this->security->get_csrf_token_name();
            echo json_encode($return_data);

            if (function_exists('fastcgi_finish_request')) fastcgi_finish_request();
            $this->mail_to_employer($post_job['employer_email'], $pid, $appliedDetail);
            exit;
        }

        // Already applied?
        if ($this->Applied_mdl->check_applied('applied', [
            'candidate_id' => $this->user_id,
            'job_id'       => $pid
        ]) > 0) {
            $return_data['success']     = true;
            $return_data['success_msg'] = 'Info: You have already applied for this job.';
            $return_data['similar_job'] = $post_job['job_title'];
            $return_data['csrf_token']  = $this->security->get_csrf_hash();
            $return_data['csrf_name']   = $this->security->get_csrf_token_name();
            echo json_encode($return_data);
            exit;
        }

        // New application
        $insert_id = $this->insertAppliedJob($appliedJob);

        if (!$insert_id) {
            $return_data['success']    = false;
            $return_data['error_msg']  = 'Something went wrong.';
            $return_data['csrf_token'] = $this->security->get_csrf_hash();
            $return_data['csrf_name']  = $this->security->get_csrf_token_name();
            echo json_encode($return_data);
            exit;
        }

        // Success
        $return_data['success']     = true;
        $return_data['success_msg'] = 'You have applied successfully.';
        $return_data['similar_job'] = $post_job['job_title'];
        $return_data['is_active']   = ($profile['status'] == 2) ? $this->popUpalertMessageHtml() : '';
        $return_data['csrf_token']  = $this->security->get_csrf_hash();
        $return_data['csrf_name']   = $this->security->get_csrf_token_name();

        echo json_encode($return_data);

        if (function_exists('fastcgi_finish_request')) fastcgi_finish_request();

        // Notifications & emails
        $full_name = (!empty($profile['name']) || !empty($profile['last_name']))
            ? trim($profile['name'].' '.$profile['last_name'])
            : 'A candidate';

        $notification_data = [
            'user_id'    => $post_job['employer_id'],
            'type'       => 'employer',
            'message'    => $full_name . ' applied for your job "' . $post_job['job_title'] . '"',
            'link'       => 'employer/applications/view/' . $insert_id,
            'created_at' => date('Y-m-d H:i:s')
        ];
        $this->load->model('Notification_model');
        $this->Notification_model->create($notification_data);

        $this->mail_to_employer($post_job['employer_email'], $pid, $appliedDetail);
        $this->mail_to_applied(
            $profile['email'],
            $post_job['company_name'],
            $post_job['job_title'],
            $full_name,
            $post_job['job_id']
        );

        exit;
    }

    private function insertAppliedJob($data) {
        $this->db->trans_start();
        $this->db->insert('applied', $data);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            $this->insertApplicationLog($insert_id, $data['job_id'], $data['candidate_id']);
        }
        $this->db->trans_complete();
        return $insert_id;
    }
        
    private function insertApplicationLog($applied_id, $job_id, $candidate_id, $performed_by = 'candidate', $comment = null) {
        $logData = [
            'application_id' => $applied_id,
            'job_id'         => $job_id,
            'candidate_id'   => $candidate_id,          
            'performed_by'   => $performed_by,               
            'created_at'     => date('Y-m-d H:i:s')
        ];
        $this->db->insert('tb_application_logs', $logData);
    }

    private function popUpalertMessageHtml() {
        $activationLink = base_url('settings/settings');
        $html = '<div class="modal-header">';
        $html .= '<h5 class="modal-title" id="applicationModalLabel">Hi, ' . $this->name . '</h5>';
        $html .= '<span>Please fill this form to apply for this opportunity</span>';
        $html .= '<button type="button" class="close" data-dismiss="modal" aria-label="Close">';
        $html .= '<span aria-hidden="true">&times;</span>';
        $html .= '</button>';
        $html .= '</div>';
        $html .= '<div class="modal-body">';
        $html .= '<div class="alert alert-warning" style="color: #8a6d3b;">';
        $html .= '<strong>Note:</strong> Your job application was successful, but your profile is currently inactive. ';
        $html .= 'As a result, employers will not be able to see your applied job profile. ';
        $html .= 'Please <a href="' . $activationLink . '" style="color: blue;font-wight:700;">click here</a> to activate your profile.';
        $html .= '</div>';
        $html .= '</div>';
        return $html;
    }
            
    public function myApplyJobs() {
        $id = $this->user_id;   
        $search_term = $this->input->get(NULL, TRUE);
        
        $this->load->library("pagination");
        
        $config = array(
            'base_url'             => base_url('job/myapply'),
            'total_rows'           => $this->Applied_mdl->get_myapply_count($id, $search_term),
            'per_page'             => 12,
            'use_page_numbers'     => TRUE,
            'enable_query_strings' => TRUE,
            'page_query_string'    => TRUE,
            'reuse_query_string'   => TRUE,
            'query_string_segment' => 'page',
            'full_tag_open'        => '<nav class="my-8"><ul class="pagination flex justify-center space-x-2">',
            'full_tag_close'       => '</ul></nav>',
            'first_tag_open'       => '<li>',
            'first_tag_close'      => '</li>',
            'last_tag_open'        => '<li>',
            'last_tag_close'       => '</li>',
            'num_tag_open'         => '<li>',
            'num_tag_close'        => '</li>',
            'cur_tag_open'         => '<li class="active"><a class="bg-blue-600 text-white px-4 py-2 rounded-lg">',
            'cur_tag_close'        => '</a></li>',
            'prev_tag_open'        => '<li>',
            'prev_tag_close'       => '</li>',
            'next_tag_open'        => '<li>',
            'next_tag_close'       => '</li>',
            'prev_link'            => '← Previous',
            'next_link'            => 'Next →',
            'attributes'           => array('class' => 'px-4 py-2 bg-white border rounded-lg hover:bg-gray-50')
        );
        
        $this->pagination->initialize($config);

        $page  = $this->input->get('page');
        $page  = ($page && $page >= 1) ? $page : 1;
        $start = ($page - 1) * $config['per_page'];

        $pagination_links = $this->pagination->create_links();
        $results = $this->Applied_mdl->myapply($config['per_page'], $start, $id, $search_term);
        
        $applied_ids = array_column($results, 'applied_id');
        $log_map = $this->Applied_mdl->get_latest_application_logs($applied_ids);
		
		$hasActivePlan = $this->Candidate_plan_model->has_active_plan($this->user_id);
		
        $data['job_html'] = $this->generate_job_html($results, $log_map, $hasActivePlan);
        $data['pagination_link'] = $pagination_links;

        echo json_encode($data);
        exit;
    }
        
	public function generate_job_html($results, $log_map = [], $hasActivePlan = false) {
    if (empty($results)) {
        return '<div class="text-gray-500 text-center p-10 text-base">
                  <i class="fas fa-briefcase text-4xl mb-3 text-gray-300 block"></i>
                  No applied jobs found.
                </div>';
    }

    $is_verified = $this->Profile_mdl->get_candidate_details($this->user_id);
    $html = '<div class="space-y-4">';

    foreach ($results as $result) {
        $cities = !empty($result['cities']) ? $result['cities'] : 'Multiple Locations';
        $hideContactStages = ['Rejected', 'Withdraw', 'Canceled', 'Completed'];
        $showContact = !in_array($result['ApplicationStage'], $hideContactStages, true);
        $stage = htmlspecialchars($result['ApplicationStage']);

        // Salary formatting
        $raw = $result['salary_range'] ?? '';
        $parts = preg_split('/\s*-\s*/', $raw);
        if (count($parts) === 2 && is_numeric($parts[0]) && is_numeric($parts[1])) {
            $salaryDisplay = '₹' . number_format((float)$parts[0], 0) . ' - ₹' . number_format((float)$parts[1], 0);
        } elseif (is_numeric($raw)) {
            $salaryDisplay = '₹' . number_format((float)$raw, 0);
        } else {
            $salaryDisplay = htmlspecialchars($raw) ?: 'Not disclosed';
        }

        // Company logo
        $logoPath = FCPATH . 'uploads/employer/profile/' . ($result['logo'] ?? '');
        $hasLogo  = !empty($result['logo']) && file_exists($logoPath);
        $companyName = htmlspecialchars($result['company_name'] ?? 'Company');
        $jobSlug = $result['slug'];

        // Next stages (यदि कोई हों)
        $nextStages = get_application_statuses($result['ApplicationStage']);
        $nextStagesLabels = !empty($nextStages) ? implode(' → ', array_keys($nextStages)) : '—';

        // Status colors
        $statusStyles = [
            'applied' => 'bg-blue-100 text-blue-800',
            'review'  => 'bg-purple-100 text-purple-800',
            'shortlisted' => 'bg-green-100 text-green-800',
            'interview' => 'bg-yellow-100 text-yellow-800',
            'selected' => 'bg-emerald-100 text-emerald-800',
            'rejected' => 'bg-red-100 text-red-800',
            'withdraw' => 'bg-gray-100 text-gray-800',
            'canceled' => 'bg-gray-100 text-gray-800',
            'completed' => 'bg-teal-100 text-teal-800'
        ];
        $badgeClass = $statusStyles[strtolower($stage)] ?? 'bg-gray-100 text-gray-800';

        // ========== CARD START ==========
        $html .= '<div class="bg-white rounded-xl border border-gray-200 shadow-sm hover:shadow-md transition-all duration-200 overflow-hidden">';

        // ----- Card Body (Flex layout) -----
        $html .= '<div class="p-4 sm:p-5">';

        // Upper section: Logo + Main Info + Status
        $html .= '<div class="flex flex-col sm:flex-row sm:items-start gap-4">';

        // Logo
        $html .= '<div class="flex-shrink-0">';
        if ($hasLogo) {
            $html .= '<img src="' . base_url($result['logo']) . '" alt="' . $companyName . '" class="w-12 h-12 rounded-lg object-cover border border-gray-100 shadow-sm" />';
        } else {
            $initials = strtoupper(substr(preg_replace("/[^A-Za-z]/", "", $companyName), 0, 2)) ?: "CO";
            $html .= '<div class="w-12 h-12 rounded-lg bg-gradient-to-r from-blue-500 to-indigo-600 flex items-center justify-center text-white font-bold text-base shadow-sm">'
                     . $initials . '</div>';
        }
        $html .= '</div>';

        // Main Info
        $html .= '<div class="flex-1 min-w-0">';
        // Job Title (clickable)
        $html .= '<a href="' . base_url($jobSlug) . '" class="text-base sm:text-lg font-semibold text-gray-900 hover:text-blue-600 transition-colors mb-1 block truncate">'
                . htmlspecialchars(ucfirst($result['job_title'])) . '</a>';
        // Company name (muted)
        $html .= '<div class="flex items-center flex-wrap gap-x-2 gap-y-1 text-sm text-gray-600 mb-2">';
        $html .= '<span class="truncate max-w-[200px]">' . $companyName . '</span>';
        $html .= '<span class="hidden sm:inline text-gray-300">|</span>';
        $html .= '<span class="inline-flex items-center gap-1"><i class="fas fa-map-marker-alt text-gray-400 text-xs"></i>' . htmlspecialchars($cities) . '</span>';
        $html .= '<span class="hidden sm:inline text-gray-300">|</span>';
        $html .= '<span class="inline-flex items-center gap-1"><i class="fas fa-briefcase text-gray-400 text-xs"></i>' . ucfirst($result['job_type'] ?? 'N/A') . '</span>';
        $html .= '</div>';

        // Salary + Application date
        $html .= '<div class="flex flex-wrap items-center gap-x-3 gap-y-1 text-sm">';
        $html .= '<span class="font-medium text-gray-800"><i class="far fa-money-bill-alt text-gray-400 mr-1"></i>' . $salaryDisplay . '</span>';
        $html .= '<span class="text-gray-500 text-xs">Applied on ' . date('d M Y', strtotime($result['created_at'])) . '</span>';
        $html .= '</div>';
        $html .= '</div>'; // end main info

        // Status badge (right side on large screens, inline on mobile)
        $html .= '<div class="flex-shrink-0 self-start">';
        $html .= '<span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold ' . $badgeClass . '">'
                . ucfirst($stage) . '</span>';
        $html .= '</div>';

        $html .= '</div>'; // end upper flex

        // ----- Timeline (collapsible) -----
        if (!empty($log_map[$result['applied_id']])) {
            $timelineLogs = $log_map[$result['applied_id']];
            $html .= '<div class="mt-3 pt-3 border-t border-gray-100 text-xs">';
            $html .= '<button class="timeline-toggle flex items-center gap-2 text-blue-600 font-medium hover:text-blue-800 w-full justify-between">';
            $html .= '<span><i class="fas fa-history mr-1"></i> Application Timeline</span>';
            $html .= '<i class="fas fa-chevron-down toggle-arrow transition-transform duration-200"></i>';
            $html .= '</button>';
            // Timeline list (hidden by default on mobile, show on desktop? we keep hidden and toggle)
            $html .= '<div class="timeline-content hidden mt-2 space-y-1.5">';
            foreach ($timelineLogs as $log) {
                $html .= '<div class="flex items-start gap-2">';
                $html .= '<div class="w-1.5 h-1.5 mt-1.5 rounded-full bg-blue-400 flex-shrink-0"></div>';
                $html .= '<div>';
                $html .= '<span class="text-gray-800 font-medium">' . htmlspecialchars(ucwords($log['stage'])) . '</span>';
                $html .= '<span class="text-gray-400 ml-2">' . date('d M Y, h:i A', strtotime($log['created_at'])) . '</span>';
                if (!empty($log['comment'])) {
                    $html .= '<p class="text-gray-500 mt-0.5">' . htmlspecialchars($log['comment']) . '</p>';
                }
                $html .= '</div>';
                $html .= '</div>';
            }
            $html .= '</div>';
            $html .= '</div>';
        }

        // ----- Contact Details (Premium Gating) -----
        if ($showContact) {
            $html .= '<div class="mt-3 pt-3 border-t border-gray-100">';
            if ($hasActivePlan) {
                if (isset($is_verified['is_verified']) && $is_verified['is_verified'] == 1) {
                    // Premium verified user – show contacts with toggle
                    $html .= '<button class="employer-details-toggle flex items-center justify-between w-full text-sm text-blue-600 font-medium py-1">';
                    $html .= '<span><i class="fas fa-phone-alt mr-1.5"></i><span class="toggle-text">View Contact Details</span></span>';
                    $html .= '<i class="fas fa-chevron-down toggle-arrow transition-transform duration-200"></i>';
                    $html .= '</button>';
                    $html .= '<div class="accordion-content hidden mt-2 grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">';
                    $html .= '<div class="bg-gray-50 p-2 rounded-lg"><span class="text-gray-400">Email</span><p class="font-medium text-gray-800 truncate">' . htmlspecialchars($result['email']) . '</p></div>';
                    $html .= '<div class="bg-gray-50 p-2 rounded-lg"><span class="text-gray-400">Phone</span><p class="font-medium text-gray-800">' . htmlspecialchars($result['mobile']) . '</p></div>';
                    $html .= '</div>';
                } else {
                    $html .= '<div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3 text-sm text-yellow-700">';
                    $html .= '<i class="fas fa-exclamation-circle mr-1"></i> Please <a href="' . base_url('candidate/profile') . '" class="font-semibold underline">verify your email</a> to view contact details.';
                    $html .= '</div>';
                }
            } else {
                // Free user – blurred contact with upgrade prompt
              $html .= '<div class="relative rounded-lg overflow-hidden border border-dashed border-gray-300 bg-gray-50 min-h-[140px] flex items-center justify-center p-4">';

				$html .= '<div class="filter blur-sm opacity-40 pointer-events-none space-y-2 text-xs w-full">';
				$html .= '<div class="flex items-center gap-2"><span class="text-gray-400">Email:</span><span class="text-gray-600">e***@company.com</span></div>';
				$html .= '<div class="flex items-center gap-2"><span class="text-gray-400">Phone:</span><span class="text-gray-600">+91-******123</span></div>';
				$html .= '</div>';

				$html .= '<div class="absolute inset-0 z-10 flex flex-col items-center justify-center bg-white/90 backdrop-blur-sm p-4 text-center">';
				$html .= '<i class="fas fa-lock text-2xl text-gray-400 mb-1"></i>';
				$html .= '<p class="text-sm font-medium text-gray-700">Unlock contact details with a premium plan</p>';

				$html .= '<a href="' . base_url('career-services') . '" 
				class="inline-flex items-center px-4 py-2 mt-2 bg-gradient-to-r from-amber-500 to-orange-600 text-white text-sm font-medium rounded-full hover:shadow-lg transition-all duration-200">';

				$html .= '<i class="fas fa-crown mr-2"></i> View Premium Plans';
				$html .= '</a>';
				$html .= '</div>';

				$html .= '</div>';
            }
            $html .= '</div>'; // end contact
        }

        $html .= '</div>'; // end card body
        $html .= '</div>'; // end card
    }

    $html .= '</div>';
    return $html;
}
    private function contact_detail_box($label, $value) {
        return '<div class="bg-gray-50 p-1.5 rounded">
            <div class="text-[9px] text-gray-400">' . $label . '</div>
            <div class="font-medium text-xs text-gray-800 truncate">' . htmlspecialchars($value) . '</div>
        </div>';
    }
        
    public function view($interview_id) {
        $user_id = $this->user_id;
        $profile = $this->Profile_mdl->get_candidate_details($user_id);
        $status = isset($profile['status']) ? strtolower($profile['status']) : 'inactive';

        $this->load->model(['employer/Interview_model']);
        $interview = $this->Interview_model->get_interview_for_candidate($interview_id, $user_id);
        if (!$interview) show_404();

        $data['title'] = 'Interview Details';
        $this->load->model('Jobs/Jobs_model');
        $data['mightBeLike'] = $this->Jobs_model->mightBeLike(10,[
			'job_id'    => $interview->job_id,
			'job_title' => $interview->job_title
		]);
        
        $data['content'] = $this->load->view(
            'candidate/notify/interview_detail', 
            [
                'interview'    => $interview,
                'profile'      => $profile,
                'mightBeLike'  => $data['mightBeLike']
            ], 
            TRUE
        );
        
        $this->load->view('templates/master', $data);
    }
            
    public function mail_to_employer($email, $pid, $job_detail) {
        if ($email && filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $todayApplicationCount = $this->Applied_mdl->countTodayApplications($pid);
            $subject = sprintf(
                '🌟 New Application Alert for "%s" - %d Application(s) Received Today! 🚀',
                $job_detail['job_title'],
                $todayApplicationCount
            );
            $data['candidate_name'] = $this->name;
            $data['job_title']      = $job_detail['job_title'];
            $data['employee_company_name'] = $job_detail['company_name'];
            $data['review_application_link'] = base_url('employer/jobs/applications/'.$pid);
            $message = $this->load->view('employer/email/mail_to_employer', $data, true);
            SendEmailTo($email, $subject, $message);
            return true;
        }
        return false;
    }
                
    function mail_to_applied($email, $company_name, $job_title, $name, $job_id) {
        if ($email && filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $subject = SITE_NAME . ' Application : ' . ucfirst($job_title);
            $matched_job = $this->Applied_mdl->recommended($job_title, 10, $this->user_id);
            $data['matched_job'] = ($matched_job ? $matched_job : '');
            $data['name']        = $name;
            $data['job_title']   = $job_title;
            $data['company_name']= $company_name;
            $data['job_id']      = $job_id;
            $message = $this->load->view('candidate/email/mail_to_applied', $data, true);
            SendEmailTo($email, $subject, $message);
            return true;
        }
        return false;
    }
}