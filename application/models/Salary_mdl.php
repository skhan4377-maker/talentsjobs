<?php class Salary_mdl extends CI_Model {
    public function __construct() {
        parent::__construct();
    }

    public function get_industries_with_job_count() {
        $this->db->select('i.id AS industry_id, i.industry_name, COUNT(pj.id) as job_count, MAX(CAST(pj.salary_from AS UNSIGNED)) as max_salary');
        $this->db->from('industry AS i');
        $this->db->join('post_job AS pj', 'i.id = pj.industry', 'inner');
        $this->db->where('pj.status', '1');
        $this->db->where("pj.salary_from REGEXP '^[0-9]+$'");
        $this->db->where("pj.salary_to REGEXP '^[0-9]+$'");
        $this->db->group_by('i.id');
        $this->db->limit(50);
        //return $this->db->get()->result_array();
    }

    public function get_functional_areas_by_industry($industry_id) {
        if (!ctype_digit($industry_id)) {
            return []; 
        }
        
        $this->db->select('pj.job_title as functional_area, MAX(CAST(pj.salary_from AS UNSIGNED)) as max_salary');
        $this->db->from('post_job AS pj');
        $this->db->where('pj.industry', $industry_id);
        $this->db->where('pj.status', '1');
        $this->db->where('pj.salary_type', 'per-month');
        $this->db->where("pj.salary_from REGEXP '^[0-9]+$'");
        $this->db->where("pj.salary_to REGEXP '^[0-9]+$'");
        $this->db->group_by('pj.job_title');
        $this->db->having('max_salary >=', 10000);
        $this->db->order_by('pj.created_at', 'DESC');
        $this->db->limit(15);
        //return $this->db->get()->result_array();
    }

    public function getFunctionalAreasBySalaryStatistics($job_title) {
        $functional_area_count = $this->countResultsByJobTitle($job_title);
        $job_title = $this->db->escape_like_str($job_title);

        $this->db->select('pj.job_title, MAX(CAST(pj.salary_from AS UNSIGNED)) as min_salary, MAX(CAST(pj.salary_to AS UNSIGNED)) as max_salary');
        $this->db->from('post_job AS pj');
        $this->db->where('pj.status', '1');
        $this->db->where('pj.salary_type', 'per-month');
        $this->db->like('pj.job_title', $job_title);
        $this->db->group_by('pj.job_title');
        $this->db->order_by('pj.created_at', 'DESC');
        $result = $this->db->get()->row_array();
        
        if ($result) {
            $salaries = [$result['min_salary'], $result['max_salary']];
            $result['median_salary'] = $this->median($salaries);
            $result['functional_area_count'] = $functional_area_count;
        }
        //return $result;
    }

    public function countResultsByJobTitle($job_title) {
        $job_title = $this->db->escape_like_str($job_title);
        $this->db->select('COUNT(DISTINCT pj.functional_area) as functional_area_count');
        $this->db->from('post_job AS pj');
        $this->db->where('pj.status', '1');
        $this->db->where('pj.salary_type', 'per-month');
        $this->db->like('pj.job_title', $job_title);
        //return $this->db->get()->row()->functional_area_count;
    }

    function median($arr) {
        sort($arr);
        $count = count($arr);
        return $count % 2 == 0 ? ($arr[$count / 2 - 1] + $arr[$count / 2]) / 2 : $arr[floor($count / 2)];
    }
}