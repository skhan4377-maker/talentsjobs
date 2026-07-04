<?php
class Favourite_mdl extends CI_Model{
    
    public function __construct(){
        parent::__construct();
    }
    
    public function get_favourite_count($id, $search_term){
        $this->db->from('favourite as F');
        $this->db->join('post_job as P', 'P.job_id = F.job_id');
        $this->db->join('employer as ER', 'ER.employer_id = P.employer_id');
        $this->db->where('F.candidate_id', $id);
        $this->db->where('ER.status', 'active');
        
        // Apply filters
        $this->apply_filters($search_term);
        
        return $this->db->count_all_results();
    }
    
    public function myFavouriteJobs($limit = FALSE, $offset = FALSE, $id = FALSE, $search_term = FALSE) {
        $this->db->select('
            P.job_id,
			P.slug,
            P.job_title,
            P.min_salary,
            P.max_salary,
            P.salary_type,
            P.min_experience,
            P.max_experience,
            P.job_description,
            P.industry_id,
            I.industry_name,
            ER.company_name,
            ER.name,
            ER.email,
            ER.mobile,
            ER.updated_at as employer_last_active_dt,
            ER.logo,
            GROUP_CONCAT(DISTINCT c.city_name SEPARATOR ", ") as cities,
            F.status,
            F.create_dt
        ');

        $this->db->from('favourite as F');
        $this->db->join('post_job as P', 'P.job_id = F.job_id');
        $this->db->join('tb_job_cities as jc', 'jc.job_id = P.job_id', 'left');
        $this->db->join('cities as c', 'c.city_id = jc.city_id', 'left');
        $this->db->join('employer as ER', 'ER.employer_id = P.employer_id');
        $this->db->join('industry as I', 'I.industry_id = P.industry_id', 'left');

        $this->db->where('F.candidate_id', $id);
        $this->db->where('ER.status', 'active');
        
        // Apply filters
        $this->apply_filters($search_term);

        // Apply sorting
        $this->apply_sorting($search_term);

        if ($limit) {
            $this->db->limit($limit, $offset);
        }

        $this->db->group_by('P.job_id');
        $query = $this->db->get();
        return $query->result_array();
    }
    
    private function apply_filters($search_term) {
        if (empty($search_term)) {
            return;
        }
        
        // Industry filter
        if (isset($search_term['industry']) && !empty($search_term['industry'])) {
            $this->db->where('P.industry_id', $search_term['industry']);
        }
        
        // Search term filter
        if (isset($search_term['search']) && !empty($search_term['search'])) {
            $keyword = trim($search_term['search']);
            $this->db->group_start();
            $this->db->like('P.job_title', $keyword);
            $this->db->or_like('ER.company_name', $keyword);
            $this->db->or_like('c.city_name', $keyword);
            $this->db->group_end();
        }
    }
    
    private function apply_sorting($search_term) {
        if (empty($search_term) || !isset($search_term['sort'])) {
            $this->db->order_by("F.favourite_id", "desc");
            return;
        }
        
        switch($search_term['sort']) {
            case 'oldest':
                $this->db->order_by('F.create_dt', 'ASC');
                break;
            case 'salary_high':
                $this->db->order_by('P.max_salary', 'DESC');
                $this->db->order_by('F.favourite_id', 'DESC');
                break;
            case 'salary_low':
                $this->db->order_by('P.min_salary', 'ASC');
                $this->db->order_by('F.favourite_id', 'DESC');
                break;
            case 'newest':
            default:
                $this->db->order_by('F.favourite_id', 'DESC');
                break;
        }
    }
    
    /**
     * Get industries that appear in the candidate's favourite jobs.
     * Used for the filter dropdown.
     */
    public function get_user_favourite_industries($candidate_id) {
        // IMPORTANT: Set second parameter to FALSE to prevent backticks around DISTINCT
        $this->db->select('DISTINCT I.industry_id, I.industry_name', FALSE);
        $this->db->from('favourite F');
        $this->db->join('post_job P', 'P.job_id = F.job_id');
        $this->db->join('industry I', 'I.industry_id = P.industry_id');
        $this->db->where('F.candidate_id', $candidate_id);
        $this->db->order_by('I.industry_name', 'ASC');
        $query = $this->db->get();
        return $query->result_array();
    }
}