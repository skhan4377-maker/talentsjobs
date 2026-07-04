<?php
class Companies_mdl extends CI_Model{
    
    public function __construct(){
        parent::__construct();
    }

    /**
     * Get active companies count with optional filters.
     * Only companies with at least one active job are counted.
     */
    public function get_active_companies_count($search_term = array()) {
        $this->db->select('COUNT(DISTINCT e.employer_id) as count');
        $this->db->from('employer e');
        $this->db->join('tb_post_job j', 'e.employer_id = j.employer_id AND j.status = "active"', 'inner');
        $this->db->join('industry i', 'e.industry_id = i.industry_id', 'left');
        $this->db->join('cities c', 'e.city_id = c.city_id', 'left');
        $this->db->where('e.status', 'active');

        if (!empty($search_term)) {
            if (!empty($search_term['company'])) {
                $this->db->like('e.company_name', $search_term['company']);
            }
            if (!empty($search_term['industry'])) {
                $this->db->where('i.industry_id', $search_term['industry']);
            }
            if (!empty($search_term['location'])) {
                // location is city ID
                $this->db->where('c.city_id', $search_term['location']);
            }
        }

        $query = $this->db->get();
        return $query->row()->count;
    }

    /**
     * Fetch paginated active companies with profile score.
     * Only companies with at least one active job are returned.
     */
    public function fetch_data($per_page, $start, $search_term = array()) {
        $this->db->select('
            e.employer_id,
            e.company_name,
            e.logo as company_logo,
            e.membership_type,
            e.recuiter_type,
            i.industry_name,
            e.company_size,
            e.company_founded,
            e.company_type,
            c.city_name,
            COUNT(j.job_id) as total_jobs,
            (
                (CASE WHEN e.logo IS NOT NULL AND e.logo != "" THEN 1 ELSE 0 END) +
                (CASE WHEN e.company_size IS NOT NULL AND e.company_size != "" THEN 1 ELSE 0 END) +
                (CASE WHEN e.company_founded IS NOT NULL AND e.company_founded != "" THEN 1 ELSE 0 END) +
                (CASE WHEN e.company_type IS NOT NULL AND e.company_type != "" THEN 1 ELSE 0 END) +
                (CASE WHEN e.industry_id IS NOT NULL THEN 1 ELSE 0 END) +
                (CASE WHEN e.city_id IS NOT NULL THEN 1 ELSE 0 END)
            ) AS profile_score
        ');
        
        $this->db->from('employer e');
        $this->db->join('tb_post_job j', 'e.employer_id = j.employer_id AND j.status = "active"', 'inner');
        $this->db->join('industry i', 'e.industry_id = i.industry_id', 'left');
        $this->db->join('cities c', 'e.city_id = c.city_id', 'left');
        $this->db->where('e.status', 'active');

        if (!empty($search_term)) {
            if (!empty($search_term['company'])) {
                $this->db->like('e.company_name', $search_term['company']);
            }
            if (!empty($search_term['industry'])) {
                $this->db->where('i.industry_id', $search_term['industry']);
            }
            if (!empty($search_term['location'])) {
                // location is city ID
                $this->db->where('c.city_id', $search_term['location']);
            }
        }

        $this->db->group_by('e.employer_id');
        $this->db->order_by('profile_score', 'DESC');
        $this->db->order_by('e.created_at', 'DESC');
        $this->db->limit($per_page, $start);

        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * Get industries that have at least one active company with active jobs.
     */
    public function get_industries_with_companies() {
        $this->db->select('i.industry_id, i.industry_name, COUNT(DISTINCT e.employer_id) as company_count');
        $this->db->from('industry i');
        $this->db->join('employer e', 'e.industry_id = i.industry_id AND e.status = "active"', 'inner');
        $this->db->join('tb_post_job j', 'e.employer_id = j.employer_id AND j.status = "active"', 'inner');
        $this->db->group_by('i.industry_id');
        $this->db->having('company_count >', 0);
        $this->db->order_by('i.industry_name', 'ASC');
        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * Search cities that have at least one active company with active jobs.
     */
    public function search_cities(string $searchTerm = '', int $limit = 10): array {
        $this->db->select('c.city_id, c.city_name, COUNT(DISTINCT e.employer_id) as employer_count');
        $this->db->from('cities c');
        $this->db->join('employer e', 'e.city_id = c.city_id AND e.status = "active"', 'inner');
        $this->db->join('tb_post_job j', 'e.employer_id = j.employer_id AND j.status = "active"', 'inner');

        if (strlen(trim($searchTerm)) >= 1) {
            $this->db->group_start();
            $this->db->like('c.city_name', $searchTerm, 'after');
            $this->db->or_like('c.city_name', $searchTerm, 'both');
            $this->db->group_end();
        }

        $this->db->group_by('c.city_id, c.city_name');
        $this->db->having('employer_count >', 0);
        $this->db->order_by('employer_count', 'DESC');
        $this->db->order_by('c.city_name', 'ASC');
        $this->db->limit($limit);

        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * Get single company details (ensuring active).
     */
    public function getCompanyDetails($company_id) {
        $this->db->select('
            e.employer_id,
            e.company_name,
            e.logo,
            e.recuiter_type,
            e.company_size,
            e.company_founded,
            e.membership_type,
            e.about_company,
            e.company_website,
            e.company_address,
            e.mobile,
            e.email,
            i.industry_name,
            c.city_name
        ');
        $this->db->from('employer e');
        $this->db->join('industry i', 'e.industry_id = i.industry_id', 'left');
        $this->db->join('cities c', 'e.city_id = c.city_id', 'left');
        $this->db->where('e.employer_id', $company_id);
        $this->db->where('e.status', 'active');
        $query = $this->db->get();
        return $query->row_array();
    }
}
?>