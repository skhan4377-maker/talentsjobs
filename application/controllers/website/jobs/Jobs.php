<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Jobs extends CI_Controller {

	public function __construct(){
		parent::__construct();
		$this->load->model('Jobs/Jobs_model');	
		 $this->load->model('Jobs/Bio_model', 'bio_model');
	} 
	
	public function index() {	
		$data['title'] = 'Browse Freshers & Experienced Jobs 2026 | IT, Software, Finance – Talents Jobs';

		$data['description'] = 'Browse the latest freshers and experienced job openings in 2026. Apply for IT jobs, software jobs, marketing jobs, finance jobs, remote jobs and more.';

		$data['meta_keywords'] = 'latest jobs, IT jobs, software jobs, jobs in India, fresher jobs, experienced jobs, remote jobs';

		
		 // Set canonical to the current clean URL
		$data['canonical'] = current_url();
		
	
		$this->load->view('particles/header', $data);
		$this->load->view('particles/nav');

		$search_term = $this->input->get('search', TRUE); // XSS cleaned

		// Load job-related filter data
		$data['activeIndustries'] = $this->Jobs_model->getActiveIndustriesWithCount();
		$data['activeJobTypes'] = $this->Jobs_model->getActiveJobTypesWithCount();
		$data['activeEducations'] = $this->Jobs_model->getActiveEducationsWithCount();

		$this->load->view('website/browse-jobs', $data);
		$this->load->view('particles/footer');

	}
 
	public function fetch_data($page = 0) {
		$filters = $this->input->get(NULL, TRUE);
		$recommended_mode = isset($filters['recommended']) && $filters['recommended'] == 1;

		// Recommended mode setup
		if ($recommended_mode && $this->session->userdata('logged_in') && $this->session->userdata('role') === 'candidate') {
			$candidateId = $this->session->userdata('user_id');
			$this->load->model('candidate/Profile_mdl');
			$profile = $this->Profile_mdl->get_candidate_details($candidateId);

			if ($profile) {
				if (!empty($profile['designations'])) {
					$filters['key_word'] = $profile['designations'];
				}
				if (!empty($profile['total_experience_years'])) {
					$exp = (int)$profile['total_experience_years'];
					$filters['experience'] = ($exp >= 5) ? '5+' : '0-' . $exp;
				}
			}
		}

		// Clean filters
		if (!empty($filters['key_word'])) {
			$filters['key_word'] = trim($filters['key_word']);
		}

		$multi_value_keys = ['industry', 'job_type', 'education', 'locations', 'salary'];
		foreach ($multi_value_keys as $key) {
			if (isset($filters[$key])) {
				if (!is_array($filters[$key])) {
					$filters[$key] = explode(',', $filters[$key]);
				}
				if ($key === 'salary') {
					$validRanges = [];
					foreach ($filters[$key] as $range) {
						if (preg_match('/^(\d+)-(\d+)$/', $range, $matches)) {
							$minLPA = (float)$matches[1];
							$maxLPA = (float)$matches[2];
							$validRanges[] = ['min' => $minLPA * 100000, 'max' => $maxLPA * 100000];
						}
					}
					$filters[$key] = $validRanges;
				}
			}
		}

		// Load pagination
		$this->load->library("pagination");
		$config = [
			"base_url" => base_url("website/jobs/Jobs"),
			"per_page" => 10,
			"page_query_string" => TRUE,
			"query_string_segment" => "page",
			"use_page_numbers" => TRUE,
			"first_link" => FALSE,
			"last_link" => FALSE,
			"num_links" => 3,

			// Wrapper
			'full_tag_open' => '<div class="p-4 border-t border-gray-100"><nav class="flex items-center justify-center gap-2">',
			'full_tag_close' => '</nav></div>',

			// Prev Button
			'prev_link' => '<i class="fa fa-angle-left"></i>',
			'prev_tag_open' => '<button class="w-8 h-8 sm:w-10 sm:h-10 flex items-center justify-center rounded-full bg-black text-white hover:bg-gray-800 transition text-xs sm:text-sm">',
			'prev_tag_close' => '</button>',

			// Next Button
			'next_link' => '<i class="fa fa-angle-right"></i>',
			'next_tag_open' => '<button class="w-8 h-8 sm:w-10 sm:h-10 flex items-center justify-center rounded-full bg-black text-white hover:bg-gray-800 transition text-xs sm:text-sm">',
			'next_tag_close' => '</button>',

			// Normal Page Numbers
			'num_tag_open' => '<button class="w-8 h-8 sm:w-10 sm:h-10 flex items-center justify-center rounded-full bg-black text-white hover:bg-gray-700 transition text-xs sm:text-sm">',
			'num_tag_close' => '</button>',

			// Active Page (disabled)
			'cur_tag_open' => '<button disabled class="w-8 h-8 sm:w-10 sm:h-10 flex items-center justify-center rounded-full bg-blue-600 text-white shadow-lg text-xs sm:text-sm cursor-not-allowed opacity-70">',
			'cur_tag_close' => '</button>',
		];


		$this->load->model('Jobs_model');
		$original_filters = $filters;
		$config["total_rows"] = $this->Jobs_model->count_all($filters);

		// Fallback for recommended
		if ($recommended_mode && $config["total_rows"] == 0) {
			unset($filters['recommended'], $filters['key_word'], $filters['experience']);
			$config["total_rows"] = $this->Jobs_model->count_all($filters);
		}

		// ✅ Custom fallback if no results found
		if ($config["total_rows"] == 0) {
			// Try with only keyword
			if (!empty($original_filters['key_word'])) {
				$filters = $original_filters;
				unset($filters['locations']);
				$config["total_rows"] = $this->Jobs_model->count_all($filters);
			}

			// If still 0, try with only location
			if ($config["total_rows"] == 0 && !empty($original_filters['locations'])) {
				$filters = $original_filters;
				unset($filters['key_word']);
				$config["total_rows"] = $this->Jobs_model->count_all($filters);
			}

			// Final fallback: use original if nothing worked
			if ($config["total_rows"] == 0) {
				$filters = $original_filters;
			}
		}

		// Initialize pagination
		$this->pagination->initialize($config);
		$page = max(1, (int)$this->input->get('page'));
		$start = ($page - 1) * $config["per_page"];

		// Fetch results
		$results = $this->Jobs_model->fetch_data($config["per_page"], $start, $filters);

		// Output data
		$data = [
			'pagination_link' => $this->pagination->create_links(),
			'result' => $this->client_arr_table($results),
			'total_rows' => number_format($config["total_rows"])
		];

		// Optional: generate dynamic meta data
		$meta = $this->generate_meta_data($filters, $results, $config["total_rows"]);
		if ($meta) {
			$data['meta'] = $meta;
		}

		echo json_encode($data);
		exit;
	}
	
	private function generate_meta_data($filters, $results, $total_rows = 0) {
		$is_search_active = false;
		$search_keys = ['key_word', 'industry', 'job_type', 'education', 'locations', 'salary', 'experience'];
		foreach ($search_keys as $key) {
			if (!empty($filters[$key])) {
				$is_search_active = true;
				break;
			}
		}

		if ($is_search_active && !empty($results) && $total_rows > 0) {
			$job       = $results[0];
			$jobTitle  = !empty($filters['key_word']) ? ucwords($filters['key_word']) : $job['job_title'];
			$city      = !empty($job['job_locations']) ? $job['job_locations'] : 'India';
			$monthYear = date('F Y');
			$siteName  = defined('SITE_NAME') ? SITE_NAME : 'our platform';

			$metaTitle = $jobTitle . ' Jobs - ' . number_format($total_rows) . ' ' . $jobTitle . ' Job Vacancies In ' . $monthYear . ' - ' . $siteName;
			$metaDesc  = number_format($total_rows) . ' ' . $jobTitle . ' Jobs Available On ' . $siteName . '. Explore ' . $jobTitle . ' Job Vacancies In ' . $city . ' Now!';

			return [
				'title' => $metaTitle,
				'description' => $metaDesc
			];
		}

		return null;
	}
	
    private function client_arr_table($results) {
        if (empty($results)) {
            return '<div class="text-gray-500 text-center p-6 text-sm">No jobs found.</div>';
        }
    
        // Filter applied jobs if candidate is logged in
        if ($this->session->userdata('logged_in')) {
            $candidateId = $this->session->userdata('user_id');
            $results = $this->filter_applied_jobs($results, $candidateId);
        }
    
        // Get favorited jobs
        $favoritedJobs = [];
        if ($this->session->userdata('logged_in')) {
            $candidateId = $this->session->userdata('user_id');
            $this->load->model('candidate/Applied_mdl');
            $favoritedJobs = $this->Applied_mdl->getFavoritedJobs($candidateId);
        }
    
        $output = '<div class="space-y-3">';
    
        foreach ($results as $job) {
            $jobPostId = $job['job_id'] ?? '';
            $isFavorited = in_array($jobPostId, $favoritedJobs);
            $favoriteIconClass = $isFavorited ? 'favorited' : '';
            $favoriteDataAttr = $isFavorited ? '1' : '0';
    
            // Company logo
            if (!empty($job['logo'])) {
                $thumbnail = '<img src="' . base_url($job['logo']) . '" 
    				class="w-full h-full object-contain p-1 rounded-md bg-white" 
    				alt="company logo" />';
            } else {
                $companyName = $job['company_name'] ?? 'Company';
                $initials = strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $companyName), 0, 2));
                if (empty($initials)) $initials = "CO";
                $thumbnail = '<div class="w-full h-full flex items-center justify-center bg-gray-200 text-gray-600 font-bold rounded-md text-xs uppercase">'
                            . $initials . '</div>';
            }
    
            // Time ago
            $postDate = !empty($job['createdAt']) ? strtotime($job['createdAt']) : time();
            $timeAgo = $this->timeAgo($postDate);
    
            // Job title
            $rawJobTitle = $job['job_title'] ?? 'Untitled';
            $jobTitle = substr(preg_replace('/[^a-zA-Z\s]/', '', ucfirst($rawJobTitle)), 0, 40);
            $jobTitle = strlen($jobTitle) > 40 ? $jobTitle.'...' : $jobTitle;
    
            // Skills pills with Show More
            $skillsArray = !empty($job['skills']) ? explode(',', $job['skills']) : [];
            $skillDisplay = '<div class="inline-flex flex-wrap items-center gap-1">';
            if (!empty($skillsArray)) {
                $skillsArray = array_map(function($s) {
                    return ucfirst(trim($s));
                }, $skillsArray);
                $visibleSkills = array_slice($skillsArray, 0, 2);
                $remainingSkills = array_slice($skillsArray, 2);
                foreach ($visibleSkills as $s) {
                    $skillDisplay .= '<span class="skill-pill px-1.5 py-0.5 rounded-full text-xs font-normal bg-gray-100 text-gray-800">
                                        <i class="fa fa-lightbulb mr-1 text-gray-500 text-[10px]"></i>' . htmlspecialchars($s) . '</span>';
                }
                if (!empty($remainingSkills)) {
                    $skillDisplay .= '<span class="extra-skills hidden inline-flex flex-wrap gap-1">';
                    foreach ($remainingSkills as $s) {
                        $skillDisplay .= '<span class="skill-pill px-1.5 py-0.5 rounded-full text-xs font-normal bg-gray-100 text-gray-800">
                                            <i class="fa fa-lightbulb mr-1 text-gray-500 text-[10px]"></i>' . htmlspecialchars($s) . '</span>';
                    }
                    $skillDisplay .= '</span>';
                    $skillDisplay .= '<button type="button" class="show-more-skills text-xs px-1.5 py-0.5 rounded bg-gray-100 hover:bg-gray-200 text-blue-600 font-medium">
                                        +' . count($remainingSkills) . '</button>';
                }
            }
            $skillDisplay .= '</div>';
    
            // Cities pills with Show More
            $citiesArray = !empty($job['job_locations']) ? explode(', ', $job['job_locations']) : [];
            $cityDisplay = '<div class="inline-flex flex-wrap items-center gap-1">';
            if (!empty($citiesArray)) {
                $visibleCities = array_slice($citiesArray, 0, 2);
                $remainingCities = array_slice($citiesArray, 2);
                foreach ($visibleCities as $c) {
                    $cityDisplay .= '<span class="city-pill px-1.5 py-0.5 rounded-full text-xs font-normal bg-gray-100 text-gray-800">
                                        <i class="fa fa-map-marker mr-1 text-gray-500 text-[10px]"></i>' . htmlspecialchars($c) . '</span>';
                }
                if (!empty($remainingCities)) {
                    $cityDisplay .= '<span class="extra-cities hidden inline-flex flex-wrap gap-1">';
                    foreach ($remainingCities as $c) {
                        $cityDisplay .= '<span class="city-pill px-1.5 py-0.5 rounded-full text-xs font-normal bg-gray-100 text-gray-800">
                                            <i class="fa fa-map-marker mr-1 text-gray-500 text-[10px]"></i>' . htmlspecialchars($c) . '</span>';
                    }
                    $cityDisplay .= '</span>';
                    $cityDisplay .= '<button type="button" class="show-more-cities text-xs px-1.5 py-0.5 rounded bg-gray-100 hover:bg-gray-200 text-blue-600 font-medium">
                                        +' . count($remainingCities) . '</button>';
                }
            }
            $cityDisplay .= '</div>';
    
            // Salary display
            $salaryFrom = isset($job['min_salary']) ? number_format($job['min_salary'], 0) : '0';
            $salaryTo = isset($job['max_salary']) ? number_format($job['max_salary'], 0) : '0';
            $salaryType = $job['salary_type'] ?? '';
            $salaryText = "₹ $salaryFrom - $salaryTo " . ucfirst($salaryType);
    
            // Experience
            $minExp = $job['min_experience'] ?? '0';
            $maxExp = $job['max_experience'] ?? '0';
            $expText = "$minExp - $maxExp yrs";
    
            // Slug for job link
            $slug = $job['slug'] ?? '';
    
            // Start card
            $output .= '<div class="bg-white rounded-lg border border-gray-100 p-2 sm:p-3 hover:shadow-sm transition-shadow duration-200 relative">';
    
            // Premium badge
            if (!empty($job['is_paid']) && $job['is_paid'] == 1) {
                $output .= '<div class="absolute top-0 left-0">
                                <span class="px-2 py-0.5 text-xs font-semibold rounded-tr-lg rounded-bl-lg 
                                bg-gradient-to-r from-yellow-500 to-amber-600 text-white shadow-sm">
                                <i class="fa fa-crown mr-1 text-xs"></i> Premium
                                </span>
                            </div>';
            }
    
            // Horizontal layout
            $output .= '<div class="flex flex-row items-start gap-2 sm:gap-3">';
                // Logo
                $output .= '<div class="flex-shrink-0 w-10 h-10 rounded-md overflow-hidden border border-gray-100 shadow-sm">'
                          . $thumbnail . '</div>';
    
                // Main info
                $output .= '<div class="flex-1 min-w-0">';
                    // Job title and company – with proper query param
                    $output .= '<div class="flex flex-wrap items-baseline gap-x-1 gap-y-0.5 mb-1">';
                    $output .= '<a href="' . site_url($slug) . '?key_word=' . urlencode($rawJobTitle) . '" class="text-sm font-semibold text-gray-900 hover:text-blue-600 truncate max-w-full">'
                             . $jobTitle . '</a>';
                    $output .= '<span class="text-xs text-gray-500 whitespace-nowrap">at</span>';
                    $output .= '<span class="text-xs font-medium text-gray-700 truncate max-w-full">' . htmlspecialchars($job['company_name'] ?? 'Company') . '</span>';
                    $output .= '</div>';
    
                    // Details row
                    $output .= '<div class="flex flex-wrap items-center gap-x-1.5 gap-y-0.5 text-xs text-gray-500">';
                        $output .= $cityDisplay;
                        $output .= $skillDisplay;
                        $output .= '<span class="inline-flex items-center whitespace-nowrap">
                                        <i class="fa fa-money text-gray-500 text-xs mr-1"></i>' . $salaryText . '
                                    </span>';
                        $output .= '<span class="inline-flex items-center whitespace-nowrap">
                                        <i class="fa fa-briefcase text-gray-500 text-xs mr-1"></i>' . $expText . '
                                    </span>';
                        $output .= '<span class="text-gray-400 text-xs whitespace-nowrap">
                                        <i class="fa fa-clock-o text-gray-500 text-xs mr-1"></i>' . $timeAgo . '
                                    </span>';
                    $output .= '</div>';
                $output .= '</div>';
    
                // Actions
                $output .= '<div class="flex items-center gap-1.5 sm:gap-2 flex-shrink-0">';
                    // Apply button – fixed link
                    $output .= '<a href="' . site_url($slug) . '?key_word=' . urlencode($rawJobTitle) . '"
                                   class="inline-flex items-center px-2.5 sm:px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-medium rounded-md transition-colors whitespace-nowrap">
                                   Apply
                                   <i class="fa fa-arrow-right text-xs ml-1"></i>
                                </a>';
                    // Favorite button
                    $output .= '<button class="favorite-icon w-8 h-8 flex items-center justify-center rounded-full hover:bg-gray-100 transition-colors ' . $favoriteIconClass . '"
                                data-pid="' . $jobPostId . '"
                                data-favorite="' . $favoriteDataAttr . '">
                                    <i class="fa fa-bookmark text-base ' . ($isFavorited ? 'text-blue-600' : 'text-gray-500 hover:text-blue-500') . '"></i>
                                </button>';
                $output .= '</div>';
    
            $output .= '</div>'; // end flex row
            $output .= '</div>'; // end card
        }
    
        $output .= '</div>'; // end space-y-3 container
        return $output;
    }

	private function formatSkills($skillsRaw) {
		if (empty($skillsRaw)) return '';

		$skillsArray = array_map('trim', explode(',', $skillsRaw));

		$badges = array_map(function ($skill) {
			$skill = ucfirst(strtolower($skill)); // ✅ lowercase करके ucfirst लागू करें
			return '<span class="text-blue-800 text-xs rounded-full border border-blue-300 px-2 py-0.5">' .
						htmlspecialchars($skill) .
					'</span>';
		}, $skillsArray);

		return implode(' ', $badges);
	}

    private function filter_applied_jobs($results, $candidateId) {
        $this->load->model('candidate/Applied_mdl');
        return array_filter($results, function($job) use ($candidateId) {
            return !$this->Applied_mdl->hasAppliedForJob($job['job_id'], $candidateId);
        });
    }
    
	private function timeAgo($timestamp) {
		return timeAgo($timestamp);
	}
	
   public function job_details($slug = null) {
        /* ===============================
           1. HANDLE ?job-id= PARAMETER
        =============================== */
        $jobIdParam = $this->input->get('job-id', TRUE);
        if ($jobIdParam) {
            $this->load->model('Jobs/Jobs_model');
            $this->load->model('candidate/Applied_mdl');
    
            $postDetails = $this->Applied_mdl->get_post_details($jobIdParam);
            if (!empty($postDetails['data'])) {
                $job = $postDetails['data'];
                redirect(site_url($job['slug']), 'location', 301);
                return;
            } else {
                show_404();
                return;
            }
        }
    
        /* ===============================
           2. VALIDATION
        =============================== */
        if (!$slug) {
            redirect('browse-jobs');
            return;
        }
    
        if (!preg_match('/-(\d+)$/', $slug, $matches)) {
            show_404();
            return;
        }
    
        $jobId = (int) $matches[1];
    
        /* ===============================
           3. LOAD MODELS
        =============================== */
        $this->load->model('candidate/Applied_mdl');
        $this->load->model('Jobs/Jobs_model');
    
        /* ===============================
           4. FETCH JOB
        =============================== */
        $postDetails = $this->Applied_mdl->get_post_details($jobId);
    
        if (empty($postDetails['data']) || $postDetails['data']['job_status'] !== 'active') {
    
            $data['mightBeLike'] = $this->Jobs_model->mightBeLike(4, []);
            $data['title'] = "Job Not Available | " . SITE_NAME;
            $data['description'] = "The job you are trying to view is not available or expired.";
            $data['meta_keywords'] = "jobs, job not available, expired jobs";
            $data['canonical'] = site_url('browse-jobs');
    
            $data['error_banner'] = [
                'heading'  => 'Job Not Available',
                'message'  => 'The job may have been removed or expired.',
                'cta_text' => 'Browse Jobs',
                'cta_url'  => site_url('browse-jobs')
            ];
    
            $this->load->view('particles/header', $data);
            $this->load->view('particles/nav');
            $this->load->view('website/job-unavailable', $data);
            $this->load->view('particles/footer');
            return;
        }
    
        $job = $postDetails['data'];
    
        // ✅ GUEST JOB VIEW TRACKING – केवल बिना लॉगिन वाले यूज़र के लिए
        if (!$this->session->userdata('logged_in') && !empty($job['job_id'])) {
                $cookieName = 'job_view_' . $job['job_id'];  // हर जॉब के लिए अलग कुकी
                if (empty($_COOKIE[$cookieName])) {
                    $this->Jobs_model->increment_job_view($job['job_id']);
                    setcookie($cookieName, '1', time() + 3600, '/');
                }
            }
    
        /* ===============================
           5. CANONICAL CHECK
        =============================== */
        if ($slug !== $job['slug']) {
            redirect(site_url($job['slug']), 'location', 301);
            return;
        }
    
        /* ===============================
           6. SIMILAR JOBS
        =============================== */
        $mightBeLike = $this->Jobs_model->mightBeLike(4, [
            'job_id'    => $job['job_id'],
            'job_title' => $job['job_title']
        ]);
    
        /* ===============================
           7. SEO META DATA
        =============================== */
        $jobTitle   = strip_tags($job['job_title']);
        $company    = strip_tags($job['company_name']);
        $locations  = !empty($job['job_locations']) ? $job['job_locations'] : 'India';
        $experience = $job['min_experience'] . ' - ' . $job['max_experience'] . ' years';
    
        $data['title'] = "{$jobTitle} Jobs – {$locations} @ {$company} ({$experience}) | " . SITE_NAME;
    
        $desc = "{$jobTitle} job in {$locations} at {$company}. Requires {$experience} experience with {$job['education']}. Apply now!";
        $data['description'] = mb_strlen($desc) > 160 ? mb_substr($desc, 0, 157) . '...' : $desc;
    
        $data['meta_keywords'] = implode(', ', [
            strtolower($jobTitle),
            strtolower($jobTitle) . ' jobs',
            strtolower($jobTitle) . ' vacancy',
            strtolower($company) . ' jobs',
            strtolower($locations) . ' jobs',
            'jobs in India',
            'latest jobs',
            'fresher jobs'
        ]);
    
        $data['canonical'] = site_url($job['slug']);
    
        /* ===============================
           8. PASS DATA
        =============================== */
        $data['post_details'] = $job;
        $data['mightBeLike']  = $mightBeLike;
    
        /* ===============================
           9. CANDIDATE INFO
        =============================== */
        if ($this->session->userdata('logged_in') && $this->session->userdata('role') === 'candidate') {
            $this->load->model('candidate/Candidate_plan_model');
            $data['hasActivePlan'] = $this->Candidate_plan_model->has_active_plan(
                $this->session->userdata('user_id')
            );
        } else {
            $data['hasActivePlan'] = false;
        }
    
        /* ===============================
           10. LOAD VIEW
        =============================== */
        $this->load->view('particles/header', $data);
        $this->load->view('particles/nav');
        $this->load->view('website/job-detail', $data);
        $this->load->view('particles/footer');
    }
	
	
	/**
     * Bio Listing Page
     */
    public function bio() {
        $per_page = 10;
        $page = 1;

        $data['bios'] = $this->bio_model->get_paginated($per_page, 0);
        $data['total_rows'] = $this->bio_model->count_all();
        $data['per_page'] = $per_page;
        $data['page'] = 2;
        $data['title'] = 'Latest Jobs in India 2026 | Govt & Private Job Vacancies';

		$data['description'] = 'Find latest government and private job vacancies in India. Apply online for fresher and experienced jobs in Uttar Pradesh and across India.';

		$data['meta_keywords'] = 'latest jobs, govt jobs, private jobs, job vacancies, jobs in India, jobs in UP, Prayagraj jobs, fresher jobs';

        $this->load->view('particles/header', $data);
        $this->load->view('particles/nav');
        $this->load->view('website/bio/bio_list', $data);
        $this->load->view('particles/footer');
    }

    /**
     * Infinite Scroll API
     */
    public function bio_fetch_more() {

        $page = (int)$this->input->get('page');
        $per_page = (int)$this->input->get('per_page') ?: 10;

        if ($page < 1) $page = 1;

        $offset = ($page - 1) * $per_page;

        $bios = $this->bio_model->get_paginated($per_page, $offset);
        $total = $this->bio_model->count_all();

        $has_more = ($offset + $per_page) < $total;

        $html = '';
        foreach ($bios as $bio) {
            $html .= $this->load->view('website/bio/bio_item', ['bio' => $bio], TRUE);
        }

        echo json_encode([
            'html' => $html,
            'has_more' => $has_more,
            'page' => $page + 1
        ]);
    }

    /**
     * Bio Detail Page
     */
    public function bio_detail($slug = null) {

        if (!$slug) {
            redirect('bio');
        }

        $bio = $this->bio_model->get_by_slug($slug);

        if (!$bio) {
            show_404();
        }

        $data['bio'] = $bio;
        $data['title'] = ($bio->name ?? $bio->title) . ' | Bio Directory';
        $data['description'] = substr(strip_tags($bio->content), 0, 160);

        $this->load->view('particles/header', $data);
        $this->load->view('particles/nav');
        $this->load->view('website/bio/bio_detail', $data);
        $this->load->view('particles/footer');
    }
}