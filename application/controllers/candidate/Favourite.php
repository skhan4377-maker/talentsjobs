<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Favourite extends MY_Controller {

    public function __construct(){
        parent::__construct();                           
        $this->user_id = $this->session->userdata('user_id');
        $this->load->model('candidate/Favourite_mdl');        
    } 
    
    public function index(){
        $data['title'] = 'Saved Opportunities';
        $this->load->model('candidate/Profile_mdl');
        $data['candidate_information'] = $this->Profile_mdl->get_candidate_details($this->user_id);        
                
        // Get industries relevant to user's favourites
        $data['industries'] = $this->Favourite_mdl->get_user_favourite_industries($this->user_id);
                
        $data['content'] = $this->load->view('candidate/favourite-jobs', $data, TRUE);
        $this->load->view('templates/master', $data);
    }
    
   
    public function myFavouriteJobs() {
        $search_term = $this->input->get(NULL, TRUE);

        $config = array(
            'base_url'            => base_url('job/favourite'),
            'total_rows'          => $this->Favourite_mdl->get_favourite_count($this->user_id, $search_term),
            'per_page'            => 9,      
            'use_page_numbers'    => TRUE,
            'enable_query_strings'=> TRUE,
            'page_query_string'   => TRUE,
            'reuse_query_string'  => TRUE,
            'query_string_segment'=> 'page',
            'full_tag_open'       => '<nav class="my-8"><ul class="pagination flex justify-center space-x-2">',
            'full_tag_close'      => '</ul></nav>',
            'first_tag_open'      => '<li>',
            'first_tag_close'     => '</li>',
            'last_tag_open'       => '<li>',
            'last_tag_close'      => '</li>',
            'num_tag_open'        => '<li>',
            'num_tag_close'       => '</li>',
            'cur_tag_open'        => '<li class="active"><a class="bg-blue-600 text-white px-4 py-2 rounded-lg">',
            'cur_tag_close'       => '</a></li>',
            'prev_tag_open'       => '<li>',
            'prev_tag_close'      => '</li>',
            'next_tag_open'       => '<li>',
            'next_tag_close'      => '</li>',
            'prev_link'           => '← Previous',
            'next_link'           => 'Next →',
            'attributes'          => array('class' => 'px-4 py-2 bg-white border rounded-lg hover:bg-gray-50')
        );

        $this->pagination->initialize($config);

        $page = empty((int) $this->input->get('page')) ? 1 : (int) $this->input->get('page');
        $start = ($page - 1) * $config["per_page"];
        $pagination_links = $this->pagination->create_links();

        $results = $this->Favourite_mdl->myFavouriteJobs($config['per_page'], $start, $this->user_id, $search_term);
        $data['job_html'] = $this->generate_job_html($results);
        $data['pagination_link'] = $pagination_links;
        
        $data['csrf_token'] = $this->security->get_csrf_hash();
        echo json_encode($data);
        exit;
    }

    private function generate_job_html($results) {
        $html = '';
        $this->load->model('candidate/Applied_mdl');
        $favoritedJobs = $this->Applied_mdl->getFavoritedJobs($this->user_id);
    
        if (empty($results)) {
            return '<div class="flex flex-col items-center justify-center min-h-[400px] text-center py-12">
                        <div class="w-24 h-24 rounded-full bg-blue-50 flex items-center justify-center mb-6">
                            <svg class="w-12 h-12 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-800 mb-2">No Saved Opportunities</h3>
                        <p class="text-gray-500 max-w-md">Jobs you save will appear here. Start exploring and save positions that match your interests.</p>
                    </div>';
        }
    
        $html .= '<div class="space-y-3">'; // Container for strips
    
        foreach ($results as $job) {
            $isFavorited = in_array($job['job_id'], $favoritedJobs);
            $cities = !empty($job['cities']) ? $job['cities'] : 'Multiple Locations';
            $industry = isset($job['industry_name']) ? $job['industry_name'] : 'Not Specified';
            
            // Salary text
            $salaryText = '';
            if (!empty($job['min_salary']) || !empty($job['max_salary'])) {
                $minSalary = !empty($job['min_salary']) ? number_format($job['min_salary'] / 1000, 0) . 'K' : '0';
                $maxSalary = !empty($job['max_salary']) ? number_format($job['max_salary'] / 1000, 0) . 'K' : '0';
                $salaryType = isset($job['salary_type']) ? str_replace('_', ' ', $job['salary_type']) : 'per month';
                $salaryText = "₹$minSalary - $maxSalary";
            } else {
                $salaryText = 'Not disclosed';
            }
            
            // Experience text
            $expText = '';
            if (!empty($job['min_experience']) || !empty($job['max_experience'])) {
                $minExp = $job['min_experience'] ?? 0;
                $maxExp = $job['max_experience'] ?? 0;
                $expText = "$minExp - $maxExp yrs";
            } else {
                $expText = 'Fresher';
            }
            
            // Company logo with fallback
            $logoPath = FCPATH . $job['logo'];
            if (!empty($job['logo']) && file_exists($logoPath)) {
                $companyLogo = '<div class="flex-shrink-0 w-10 h-10 rounded-md overflow-hidden border border-gray-100 shadow-sm">
                                    <img src="' . base_url($job['logo']) . '" 
                                         alt="' . htmlspecialchars($job['company_name']) . '" 
                                         class="w-full h-full object-cover" />
                                </div>';
            } else {
                $initial = !empty($job['company_name']) ? strtoupper(substr($job['company_name'], 0, 1)) : 'C';
                $companyLogo = '<div class="flex-shrink-0 w-10 h-10 rounded-md bg-gradient-to-r from-blue-500 to-indigo-600 flex items-center justify-center text-white font-bold text-sm shadow-sm">
                                    ' . $initial . '
                                </div>';
            }
            
            $jobSlug = $job['slug'];
            $timeAgo = timeAgo(strtotime($job['create_dt']));
            
            // Updated HTML with horizontal layout on all screen sizes
            $html .= '
            <div class="bg-white rounded-lg border border-gray-100 p-3 hover:shadow-sm transition-shadow duration-200">
                <div class="flex flex-row items-start justify-between gap-2">
                    <!-- Logo -->
                    ' . $companyLogo . '
                    
                    <!-- Main info (flexible) -->
                    <div class="flex-1 min-w-0">
                        <div class="flex flex-wrap items-baseline gap-1 mb-1">
                            <h3 class="text-sm sm:text-base font-semibold text-gray-900 truncate">' . htmlspecialchars($job['job_title']) . '</h3>
                            <span class="text-xs sm:text-sm text-gray-500">at</span>
                            <span class="text-xs sm:text-sm font-medium text-gray-700 truncate">' . htmlspecialchars($job['company_name']) . '</span>
                        </div>
                        <div class="flex flex-wrap items-center gap-x-2 gap-y-1 text-xs text-gray-500">
                            <span class="inline-flex items-center truncate max-w-[150px] sm:max-w-none">
                                <svg class="w-3.5 h-3.5 mr-1 flex-shrink-0 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                <span class="truncate">' . htmlspecialchars($cities) . '</span>
                            </span>
                            <span class="inline-flex items-center whitespace-nowrap">
                                <svg class="w-3.5 h-3.5 mr-1 flex-shrink-0 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                </svg>
                                ' . $salaryText . '
                            </span>
                            <span class="inline-flex items-center whitespace-nowrap">
                                <svg class="w-3.5 h-3.5 mr-1 flex-shrink-0 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                </svg>
                                ' . $expText . '
                            </span>
                            <span class="inline-flex items-center">
                                <span class="px-1.5 py-0.5 rounded bg-blue-100 text-blue-800 text-xs font-medium">' . htmlspecialchars($industry) . '</span>
                            </span>
                            <span class="text-gray-400 text-xs whitespace-nowrap">' . $timeAgo . '</span>
                        </div>
                    </div>
                    
                    <!-- Actions -->
                    <div class="flex items-center gap-1 flex-shrink-0">
                        <a href="' . base_url($jobSlug) . '" 
                           class="inline-flex items-center px-2 sm:px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-medium rounded-md transition-colors duration-200 whitespace-nowrap">
                           View
                           <svg class="w-3.5 h-3.5 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                               <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                           </svg>
                        </a>
                        <button class="like-icon flex-shrink-0 p-1.5 rounded-full hover:bg-red-50 transition-colors" data-job-id="' . $job['job_id'] . '">
                            <svg class="w-4 h-4 transition ' . ($isFavorited ? 'text-red-500 fill-current' : 'text-gray-300 hover:text-red-400') . '" 
                                 xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M11.645 20.91l-.007-.003-.022-.012a15.247 15.247 0 01-.383-.218 25.18 25.18 0 01-4.244-3.17C4.688 15.36 2.25 12.174 2.25 8.25 2.25 5.322 4.714 3 7.688 3A5.5 5.5 0 0112 5.052 5.5 5.5 0 0116.313 3c2.973 0 5.437 2.322 5.437 5.25 0 3.925-2.438 7.111-4.739 9.256a25.175 25.175 0 01-4.244 3.17 15.247 15.247 0 01-.383.219l-.022.012-.007.004-.003.001a.752.752 0 01-.704 0l-.003-.001z" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>';
        }
    
        $html .= '</div>';
        return $html;
    }

}