<?php
class CompaniesController extends CI_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->model('companies_mdl');
        $this->load->database();
        $this->load->driver('cache', ['adapter' => 'file']);	
    }

    /**
     * Main company listing page (Tailwind version)
     */
    public function companies_hiring() {
        $data['title'] = 'Top Companies Hiring Now - Talents Jobs';
        $data['description'] = 'Discover companies actively hiring across India and abroad. Explore job openings from trusted employers at Talents Jobs and apply easily to grow your career.';

        // Get industries for filter dropdown
        $data['industries'] = $this->companies_mdl->get_industries_with_companies();

        $this->load->view('particles/header', $data);
        $this->load->view('particles/nav');		
        $this->load->view('website/companies/companies-hiring', $data);
        $this->load->view('particles/footer');
    }

    /**
     * AJAX endpoint to fetch paginated companies
     */
    public function fetch_data() {	
        $search_term = $this->input->get(NULL, TRUE);	
        
        $this->load->library("pagination");
        $config = array(
            'base_url' => base_url('website/companies/CompaniesController/fetch_data'),
            'total_rows' => $this->companies_mdl->get_active_companies_count($search_term),
            'per_page' => 18,
            'uri_segment' => 3,
            'use_page_numbers' => TRUE,
            'enable_query_strings' => TRUE,
            'page_query_string' => TRUE,
            'reuse_query_string' => TRUE,
            'query_string_segment' => 'page',
            // Tailwind pagination styling
            'full_tag_open' => '<nav aria-label="Page navigation"><ul class="flex items-center justify-center gap-2 py-4">',
            'full_tag_close' => '</ul></nav>',
            'num_tag_open' => '<li>',
            'num_tag_close' => '</li>',
            'cur_tag_open' => '<li><a class="px-4 py-2 text-white bg-purple-600 rounded-full shadow-md hover:bg-purple-700 transition-colors">',
            'cur_tag_close' => '</a></li>',
            'prev_tag_open' => '<li class="hover:bg-purple-50 rounded-full">',
            'prev_tag_close' => '</li>',
            'prev_link' => '<i class="fas fa-chevron-left px-2 py-1 text-purple-600"></i>',
            'next_tag_open' => '<li class="hover:bg-purple-50 rounded-full">',
            'next_tag_close' => '</li>',
            'next_link' => '<i class="fas fa-chevron-right px-2 py-1 text-purple-600"></i>',
            'attributes' => array(
                'class' => 'px-4 py-2 text-gray-700 hover:bg-purple-100 rounded-full transition-colors flex items-center justify-center w-10 h-10'
            ),
            'first_link' => false,
            'last_link' => false,
            'num_links' => 3,
            'display_pages' => TRUE
        );
        $this->pagination->initialize($config);
        
        $page = empty((int)$this->input->get('page')) ? 1 : (int)$this->input->get('page');
        $start = ($page - 1) * $config["per_page"];
        $pagination_links = $this->pagination->create_links();

        $results = $this->companies_mdl->fetch_data($config["per_page"], $start, $search_term);			 
        $data['pagination_link'] = $pagination_links;
        $data['company_data'] = $this->render_company_cards($results);
        
        echo json_encode($data);
        $this->db->close();
        exit;
    }

    /**
     * Helper to generate HTML for company cards
     */
    private function render_company_cards($result) {
        $output = '';
        if ($result) {
            $industryColors = [
                'it'         => 'bg-blue-100 text-blue-800',
                'healthcare' => 'bg-green-100 text-green-800',
                'education'  => 'bg-yellow-100 text-yellow-800',
                'finance'    => 'bg-purple-100 text-purple-800',
                'retail'     => 'bg-pink-100 text-pink-800',
            ];

            foreach ($result as $company) {
                // Logo or initials
                if (!empty($company['company_logo'])) {
                    $logoHtml = '<img src="' . base_url($company['company_logo']) . '" 
                        class="w-20 h-20 object-contain rounded-xl border border-gray-100 shadow-sm flex-shrink-0"
                        alt="' . $company['company_name'] . '">';
                } else {
                    $initials = strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $company['company_name']), 0, 2)) ?: 'CO';
                    $logoHtml = '<div class="w-20 h-20 rounded-xl bg-purple-100 border border-gray-200 
                        flex items-center justify-center text-purple-700 font-bold text-xl shadow-sm flex-shrink-0">'
                        . $initials . '</div>';
                }

                $isPremium = (!empty($company['membership_type']) && strtolower($company['membership_type']) == 'paid');
                $membershipTag = $isPremium
                    ? '<span class="ml-2 text-green-600" title="Verified"><i class="fas fa-check-circle text-base"></i></span>'
                    : '';

                $cardClass = $isPremium 
                    ? 'bg-gradient-to-r from-yellow-50 to-yellow-100 border-yellow-300 relative'
                    : 'bg-white border-gray-200';

                $premiumBadge = $isPremium 
                    ? '<span class="absolute top-3 right-3 bg-yellow-500 text-white text-xs font-semibold px-3 py-1 rounded-full shadow">
                            <i class="fas fa-crown mr-1"></i> Premium
                       </span>' 
                    : '';

                $randomReviews = rand(100, 1000);
                $companyUrl = base_url('companies-detail?cmpid=') . url_title(strtolower($company['company_name'])) . '-' . $company['employer_id'];

                $industryKey = strtolower($company['industry_name'] ?? '');
                $industryClass = $industryColors[$industryKey] ?? 'bg-gray-100 text-gray-800';

                $output .= '
                    <div class="' . $cardClass . ' rounded-xl border shadow-sm hover:shadow-md transition-all duration-300 group p-4 sm:p-5">
                        ' . $premiumBadge . '
                        <div class="flex items-start gap-4">
                            ' . $logoHtml . '
                            <div class="flex-1 min-w-0 flex flex-col justify-between">
                                <div class="min-w-0">
                                    <div class="flex items-center gap-2">
                                        <a href="' . $companyUrl . '" 
                                           class="text-sm sm:text-base font-semibold text-gray-800 hover:text-purple-700 transition truncate max-w-[160px] sm:max-w-[220px]">
                                            ' . $company['company_name'] . '
                                        </a>
                                        ' . $membershipTag . '
                                    </div>
                                    <div class="flex items-center gap-1 text-xs text-gray-500 mt-0.5">
                                        <i class="fas fa-star text-yellow-400 text-[11px]"></i>
                                        <span>' . $randomReviews . ' Reviews</span>
                                    </div>
                                    <div class="flex flex-wrap items-center gap-1.5 mt-1 text-xs text-gray-600">
                                        ' . (!empty($company['recuiter_type']) ? '
                                        <span class="bg-purple-100 text-purple-800 px-2 py-0.5 rounded-full font-medium">
                                            ' . ucfirst($company['recuiter_type']) . '
                                        </span>' : '') . '
                                        ' . (!empty($company['industry_name']) ? '
                                        <span class="px-2 py-0.5 rounded-full font-medium ' . $industryClass . '">
                                            <i class="fas fa-industry"></i> ' . $company['industry_name'] . '
                                        </span>' : '') . '
                                        ' . (!empty($company['company_size']) ? '
                                        <span class="flex items-center gap-1">
                                            <i class="fas fa-users text-purple-500"></i> ' . $company['company_size'] . ' emp.
                                        </span>' : '') . '
                                        ' . (!empty($company['company_founded']) ? '
                                        <span class="flex items-center gap-1">
                                            <i class="fas fa-calendar-alt text-purple-500"></i> Founded ' . date('Y', strtotime($company['company_founded'])) . '
                                        </span>' : '') . '
                                    </div>
                                </div>
                                <div class="mt-3 flex justify-end">
                                    <a href="' . $companyUrl . '" 
                                       class="inline-flex items-center gap-1 px-2.5 py-1.5 sm:px-3 sm:py-1.5 rounded 
                                       bg-gradient-to-r from-amber-500 to-yellow-600 hover:from-amber-600 hover:to-yellow-700 
                                       text-white font-semibold text-[11px] sm:text-xs shadow transition">
                                       View Company <i class="fa fa-arrow-right text-[11px]"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>';
            }
        } else {
            $output = '
            <div class="col-span-full text-center py-12">
                <div class="text-2xl font-bold text-red-500 mb-2">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    No companies found!
                </div>
                <p class="text-gray-600">Try adjusting your search filters</p>
            </div>';
        }
        return $output;
    }

    /**
     * Company detail page (unchanged but ensure active company)
     */
    public function companies_detail() {
        $url_segment = $this->input->get('cmpid', TRUE);
        if (preg_match('/(\d+)$/', $url_segment, $matches)) {
            $company_id = $matches[1];
        } else {
            show_error("Invalid Company ID format", 400);
            return;
        }

        $company_id = (int)$company_id;
        $company_data = $this->companies_mdl->getCompanyDetails($company_id);
        if (!$company_data) {
            show_404();
            return;
        }

        $data['title'] = htmlspecialchars($company_data['company_name']) . ' | Talents Jobs';
        $description_parts = [];
        $description_parts[] = "Explore career opportunities at " . htmlspecialchars($company_data['company_name']);
        if (!empty($company_data['industry_name'])) {
            $description_parts[] = htmlspecialchars($company_data['industry_name']) . " industry leader";
        }
        if (!empty($company_data['city_name'])) {
            $description_parts[] = "based in " . htmlspecialchars($company_data['city_name']);
        }
        if (!empty($company_data['about_company'])) {
            $clean = strip_tags($company_data['about_company']);
            if (mb_strlen($clean) > 120) {
                $clean = mb_substr($clean, 0, 117) . '…';
            }
            $description_parts[] = $clean;
        }
        $data['description'] = implode('. ', $description_parts) . ' | Find jobs, reviews, and company information.';

        $this->load->view('particles/header', $data);
        $this->load->view('particles/nav');

        $this->load->model('Jobs/Jobs_model');
        $data['jobs'] = $this->Jobs_model->getJobsByCompany($company_id, 12);
        $data['company_data'] = $company_data;
        $data['departments'] = $this->Jobs_model->getJobsGroupedByDepartment($company_id);
		
		$data['isLoggedIn'] = $this->session->userdata('logged_in') ? true : false;
		
        $this->load->view('website/companies/companies-detail', $data);
        $this->load->view('particles/footer');
    }

    /**
     * Get industries for filter dropdown (AJAX)
     */
    public function get_industries() {
        $industries = $this->companies_mdl->get_industries_with_companies();
        header('Content-Type: application/json');
        echo json_encode($industries);
        $this->db->close();
        exit;
    }

    /**
     * Search cities for filter dropdown (AJAX)
     */
    public function search_cities() {
        try {
            $term = trim($this->input->get('term', true));
            $cacheKey = 'city_search_' . md5($term);
            if (!$results = $this->cache->get($cacheKey)) {
                $results = $this->companies_mdl->search_cities($term, 10);
                $this->cache->save($cacheKey, $results, 300);
            }
            $data = array_map(function($c){
                return [
                    'id'   => $c['city_id'],
                    'text' => sprintf('%s (%d companies)', $c['city_name'], $c['employer_count'])
                ];
            }, $results);
            $this->db->close();
            return $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode($data));
        } catch (\Exception $e) {
            log_message('error', 'search_cities error: ' . $e->getMessage());
            $this->db->close();
            return $this->output
                ->set_status_header(500)
                ->set_content_type('application/json')
                ->set_output(json_encode(['error' => 'Server error']));
        }
    }
}
?>
