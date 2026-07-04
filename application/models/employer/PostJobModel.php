<?php
class PostJobModel extends CI_Model {

    /*
    |---------------------------------------
    | Job Count
    |---------------------------------------
    */
    public function job_count($employer_id, $job_id = false)
    {
        $this->db->from('tb_post_job p');
        $this->db->where('p.employer_id', $employer_id);

        if (!empty($job_id)) {
            $this->db->where('p.job_id', (int)$job_id);
        }

        // Status filter
        $status = $this->input->get('status');
        if (!empty($status) && $status !== 'all') {
            $this->db->where('p.status', $status);
        }

        // Job title search
        if ($q = $this->input->get('q')) {
            $this->db->like('p.job_title', $q);
        }

        return $this->db->count_all_results();
    }


    /*
    |---------------------------------------
    | Job List
    |---------------------------------------
    */
    // 🔧 PostJobModel.php – job_list() with View Count

    public function job_list($employer_id, $limit = false, $offset = false, $job_id = false)
    {
    
        $this->db->select("
            p.job_id,
            p.job_title,
            COALESCE(p.min_salary,0) as min_salary,
            COALESCE(p.max_salary,0) as max_salary,
            p.created_at,
            p.deadline_date,
            p.job_type,
            COALESCE(p.min_experience,0) as min_experience,
            COALESCE(p.max_experience,0) as max_experience,
            COALESCE(p.education,'Not specified') as education,
            p.status,
            COUNT(a.applied_id) as applications_count,
            COALESCE(v.total_views, 0) as view_count      
        ");
    
        $this->db->from('tb_post_job p');
    
        $this->db->join('tb_applied a', 'a.job_id = p.job_id', 'left');
        $this->db->join('tb_job_views v', 'v.job_id = p.job_id', 'left'); 
    
        $this->db->where('p.employer_id', $employer_id);
    
        if (!empty($job_id)) {
            $this->db->where('p.job_id', (int)$job_id);
        }
    
        // Status filter
        $status = $this->input->get('status');
        if (!empty($status) && $status !== 'all') {
            $this->db->where('p.status', $status);
        }
    
        // Job title search
        if ($q = $this->input->get('q')) {
            $this->db->like('p.job_title', $q);
        }
    
        $this->db->group_by('p.job_id');
    
        $this->db->order_by('p.created_at', 'DESC');
    
        if ($limit !== false) {
            $this->db->limit($limit, $offset);
        }
    
        return $this->db->get()->result_array();
    }


    /*
    |---------------------------------------
    | Insert Job
    |---------------------------------------
    */
    public function insert_job_data($data)
    {
        $this->db->insert('tb_post_job', $data);
        return $this->db->insert_id();
    }


    /*
    |---------------------------------------
    | Insert Job Skills
    |---------------------------------------
    */
    public function insertJobSkillsBatch($data)
    {
        return $this->db->insert_batch('tb_job_skills', $data);
    }


    /*
    |---------------------------------------
    | Get Job Skills
    |---------------------------------------
    */
    public function get_job_skills($job_id)
    {
        return $this->db
            ->select('skill_name')
            ->get_where('tb_job_skills', ['job_id'=>$job_id])
            ->result_array();
    }


    /*
    |---------------------------------------
    | Delete Skill
    |---------------------------------------
    */
    public function deleteSingleSkill($job_id,$skill_name)
    {
        $this->db->where('job_id',$job_id);
        $this->db->where('LOWER(skill_name)',strtolower($skill_name));
        $this->db->delete('tb_job_skills');
    }


    /*
    |---------------------------------------
    | Insert Cities
    |---------------------------------------
    */
    public function insert_job_cities_batch(array $batch)
    {
        if(empty($batch)) return;

        return $this->db->insert_batch('tb_job_cities',$batch);
    }


    /*
    |---------------------------------------
    | Get Job Cities
    |---------------------------------------
    */
    public function get_job_cities($job_id)
    {

        $this->db->select('jc.city_id,c.city_name');

        $this->db->from('tb_job_cities jc');

        $this->db->join('cities c','jc.city_id=c.city_id','inner');

        $this->db->where('jc.job_id',$job_id);

        return $this->db->get()->result_array();
    }


    /*
    |---------------------------------------
    | Remove City
    |---------------------------------------
    */
    public function remove_job_city($job_id,$city_id)
    {

        $this->db->where('job_id',$job_id);
        $this->db->where('city_id',$city_id);

        $this->db->delete('tb_job_cities');

        return $this->db->affected_rows()>0;
    }


    /*
    |---------------------------------------
    | Edit Job Details
    |---------------------------------------
    */
    public function get_edit_post_details($job_id, $employer_id)
    {
        $this->db->select('
            tb.job_id,
            tb.job_title,
            tb.min_experience,
            tb.max_experience,
            tb.job_type,
            tb.positions_open,
            tb.education,
            tb.salary_type,
            tb.min_salary,
            tb.max_salary,
            tb.job_description,
            tb.deadline_date,
            tb.industry_id,
            tb.functional_id,
            tb.apply_web_link,
            tb.enable_apply_link,
            ind.industry_name 
        ');
    
        $this->db->from('tb_post_job tb');
        $this->db->join('tb_industry ind', 'ind.industry_id = tb.industry_id', 'left'); // ← join
    
        $this->db->where('tb.job_id', $job_id);
        $this->db->where('tb.employer_id', $employer_id);
    
        $result = $this->db->get()->row();
    
        if ($result) {
            $skills = $this->db
                ->select('skill_name')
                ->from('tb_job_skills')
                ->where('job_id', $job_id)
                ->get()
                ->result_array();
    
            $result->skills = array_column($skills, 'skill_name');
        }
    
        return $result;
    }


    /*
    |---------------------------------------
    | Update Job
    |---------------------------------------
    */
    public function update_post_job($job_id,$user_id,$data)
    {

        $this->db->where('job_id',$job_id);

        $this->db->where('employer_id',$user_id);

        return $this->db->update('tb_post_job',$data);
    }



    /*
    |---------------------------------------
    | Update Application Status
    |---------------------------------------
    */
    public function update_application_status($applied_id,$status)
    {

        $this->db->where('applied_id',$applied_id);

        $this->db->update('tb_applied',[
            'ApplicationStage'=>$status
        ]);

        return $this->db->affected_rows()>0;
    }

}