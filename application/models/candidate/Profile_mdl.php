<?php class Profile_mdl extends CI_Model{
	
	public function __construct(){
		parent::__construct();
	}
	
	function save_candidate_record($data) {
        $this->db->trans_start();
        $this->db->insert('candidate',$data);
        $inserted_id = $this->db->insert_id();
        $this->db->trans_complete();
        return $inserted_id;
    }
	
	function update_candidate_details($id, $data) {
        $this->db->trans_start();
        $this->db->update('candidate', $data, array('candidate_id' => $id));
        $this->db->trans_complete();
        return $id;
    }
	
	
	 // Function to get employer image path
    public function get_candidate_files($id) {
		$this->db->select('logo, resume');
		$this->db->where('candidate_id', $id);
		$query = $this->db->get('candidate');
		$result = $query->row_array();
		
		$file_paths = array();

		if (isset($result['logo'])) {
			$file_paths['profile_image'] = $result['logo'];
		} else {
			$file_paths['profile_image'] = false;
		}

		if (isset($result['resume'])) {
			$file_paths['resume'] =  $result['resume'];
		} else {
			$file_paths['resume'] = false;
		}

		return $file_paths;
	}
	
    function get_candidate_details($id) {
		$this->db->select('
			c.*,
			i.industry_name,
			fa.functional_area,
			fa.functional_id,
			ct.city_name,
			cnt.country_name as country,
			cnt.currency,
			cnt.currency_symbol,
			cnt.timezones
		');
		$this->db->from('candidate as c');
		$this->db->join('industry as i', 'i.industry_id = c.industry_id', 'left');
		$this->db->join('functional_area as fa', 'fa.functional_id = c.functional_id', 'left');
		$this->db->join('cities as ct', 'ct.city_id = c.city_id', 'left');
		$this->db->join('countries as cnt', 'cnt.country_id = c.country_id', 'left');
		$this->db->where('c.candidate_id', $id);

		$query = $this->db->get();
		$candidate = $query->row_array();

		if ($candidate) {
			// ✅ Fetch skills
			$skill_query = $this->db
				->select('skill_name')
				->from('candidate_skills')
				->where('candidate_id', $id)
				->get();
			$skills = $skill_query->result_array();
			$candidate['skills'] = implode(',', array_column($skills, 'skill_name'));

			// ✅ Fetch preferred locations
			$location_query = $this->db
				->select('cpl.city_id, ct.city_name')
				->from('candidate_preferred_locations as cpl')
				->join('cities as ct', 'ct.city_id = cpl.city_id', 'left')
				->where('cpl.candidate_id', $id)
				->get();
			$locations = $location_query->result_array();

			// Add both formats: array of city_ids and a display-friendly string of names
			$candidate['preferred_location_ids'] = array_column($locations, 'city_id');
			$candidate['preferred_location_names'] = implode(', ', array_column($locations, 'city_name'));
		}

		return $candidate;
	} 
		
	
	function applied_view_job($id,$status) {
        $this->db->select('AP.post_id, COUNT(AP.status) as viewed_by_recruiter');
        $this->db->from('applied as AP');
        $this->db->where_in('AP.post_id', $id);
        $this->db->where('AP.status', $status);
        $this->db->group_by('AP.post_id');
       	$query=$this->db->get();
       	$return_arr = array();
        if ($query->num_rows() > 0) {
            $results = $query->result_array();
            foreach ($results as $result) {
                $return_arr[$result['post_id']] = $result;
            }
        }
        return $return_arr;
    }
	
		
	/*public function shortlisted($reff_id){
		$this->db->where('AP.candidate_id',$reff_id);
		$this->db->where('AP.status',2);
		$this->db->from('applied as AP');
		$query=$this->db->get();
		return $query->num_rows();
	
	}*/
	
	/*function apply_job($job_id){
		$this->db->select('count(post_id) as applyCount,A.candidate_id,P.salary_from,P.salary_to, C.currency_symbol, CY.city_name');
		$this->db->group_by("A.post_id");
		$this->db->from('applied as A');
		$this->db->join('post_job as P','P.id = A.post_id');
		$this->db->join('countries as C','C.tcid = P.country_name');
		$this->db->join('city as CY','CY.id = P.current_location');
		$this->db->where('A.post_id',$job_id);
		$query = $this->db->get();
		$applyCount = $query->row_array();
		
		
		$this->db->select('C.name, C.designations,C.candidate_mobile,C.work_status,C.qualification,C.email, CY.city_name, A.post_id');
		$this->db->from('applied as A');
		$this->db->join('candidate as C','C.id = A.candidate_id');
		$this->db->join('city as CY','CY.id = C.current_location');
		$this->db->limit(4);
		$this->db->where('A.post_id',$job_id);
		$this->db->order_by('A.id','desc');
		$query1 = $this->db->get();
		$candidates = $query1->result_array();
		return array('apply'=>$applyCount, 'candidates'=>$candidates);
		 
	}*/
	
	function save_notification($data,$company_name){
	    $post_data['job_id'] = $data['post_id'];
	    $post_data['reff_id'] = $data['candidate_id'];
	    $post_data['title'] = 'your application sent to the recruiter';
	    $post_data['message'] = $company_name;
	    $this->db->trans_start();
	    $this->db->insert('notification ',$post_data);
	   	$this->db->trans_complete();
	}
	
	function fetch_notification($id, $limit){
	    $this->db->select('NOTY.title, NOTY.message, NOTY.created_at');
		$this->db->from('notification as NOTY');
		$this->db->where('NOTY.reff_id',$id);
		$this->db->order_by('NOTY.id','desc');
		$this->db->limit($limit);
		$query = $this->db->get();
		return $query->result_array();  
	}
	
	public function employment_history_store($data) {
        $this->db->insert('employment_history', $data);
        return $this->db->insert_id();
    }
	
	/**
     * Get work experience records for a given candidate.
     *
     * @param int $user_id Candidate's user ID.
     * @return array|bool Returns an array of work experience records if found, otherwise false.
     */
    public function get_work_experiences($user_id) {
        $this->db->where('candidate_id', $user_id);
        $query = $this->db->get('employment_history'); 

        if ($query->num_rows() > 0) {
            return $query->result_array();
        } else {
            return false;
        }
    }
	
	// Function to fetch work experience by its ID
    public function get_work_experience_by_id($id) {
        $this->db->where('id', $id);
        $query = $this->db->get('employment_history'); // Adjust table name if needed
        if ($query->num_rows() > 0) {
            return $query->row_array();
        }
        return false;
    }
	
	 // Update work experience record
    public function update_work($id, $data) {
        if (empty($id) || empty($data)) {
            return false;
        }
        $this->db->where('id', $id);
        $updated = $this->db->update('employment_history', $data);
        if (!$updated) {
            log_message('error', 'Error updating work experience record with id ' . $id);
        }
        return $updated;
    }	
	
	 // Delete work experience record by ID
    public function delete_work_experience($id) {
        $this->db->where('id', $id);
        return $this->db->delete('employment_history'); // Returns TRUE on success, FALSE otherwise
    }
	
	    // Store education record
    public function store_education($data) {
        $this->db->insert('education_history', $data);
        return $this->db->insert_id();
    }

    /**
     * Get education records for a given candidate.
     *
     * @param int $user_id Candidate's user ID.
     * @return array|bool Returns an array of education records if found, otherwise false.
     */
	 public function get_educations($user_id) {
		$this->db->where('candidate_id', $user_id);
		$query = $this->db->get('education_history');
		
		if (!$query) {
			log_message('error', 'Database error: ' . $this->db->error()['message']);
			return false;
		}

		if ($query->num_rows() > 0) {
			return $query->result_array();
		} else {
			return false;
		}
	}


    // Function to fetch education record by its ID
    public function get_education_by_id($id) {
        $this->db->where('id', $id);
        $query = $this->db->get('education_history');
        if ($query->num_rows() > 0) {
            return $query->row_array();
        }
        return false;
    }
	
	public function update_education($id, $data) {
        // Ensure that $id and $data are provided
        if (empty($id) || empty($data)) {
            return false;
        }
        
        // Set the where clause
        $this->db->where('id', $id);
        
        // Update the education table with the new data
        $updated = $this->db->update('education_history', $data);
        
        // Optionally, you can check for errors and log them
        if (!$updated) {
            // Log error message
            log_message('error', 'Error updating education record with id ' . $id);
        }
        
        return $updated;
    }
	
	
    // Delete education record by ID
    public function delete_education($id) {
        $this->db->where('id', $id);
        return $this->db->delete('education_history');
    }
	
	 // Fetch all languages for a given candidate
    public function get_languages($candidate_id) {
        $this->db->where('candidate_id', $candidate_id);
        $query = $this->db->get('language');
        if (!$query) {
			log_message('error', 'Database error: ' . $this->db->error()['message']);
			return false;
		}

		if ($query->num_rows() > 0) {
			return $query->result_array();
		} else {
			return false;
		}
    }

    // Insert a new language record
    public function store_language($data) {
        $this->db->insert('language', $data);
        return $this->db->insert_id();
    }

    // Update an existing language record
    public function update_language($language_id, $data) {
        $this->db->where('id', $language_id);
        return $this->db->update('language', $data);
    }

    // Delete a language record
    public function delete_language($language_id) {
        $this->db->where('id', $language_id);
        return $this->db->delete('language');
    }
	
	 // Fetch all internship records for a given candidate
    public function get_internships($candidate_id) {
        $this->db->where('candidate_id', $candidate_id);
        $query = $this->db->get('internships');
        return $query->result_array();
    }

    // Insert a new internship record
    public function store_internship($data) {
        $this->db->insert('internships', $data);
        return $this->db->insert_id();
    }

    // Update an existing internship record
    public function update_internship($internship_id, $data) {
        $this->db->where('id', $internship_id);
        return $this->db->update('internships', $data);
    }

    // Delete an internship record
    public function delete_internship($internship_id) {
        $this->db->where('id', $internship_id);
        return $this->db->delete('internships');
    }
	
	 // Fetch all certifications for a candidate
    public function get_certifications($candidate_id) {
        $this->db->where('candidate_id', $candidate_id);
        $query = $this->db->get('certifications');
        return $query->result_array();
    }

    // Insert a new certification record
    public function store_certification($data) {
        $this->db->insert('certifications', $data);
        return $this->db->insert_id();
    }

    // Update an existing certification record
    public function update_certification($certification_id, $data) {
        $this->db->where('id', $certification_id);
        return $this->db->update('certifications', $data);
    }

    // Delete a certification record
    public function delete_certification($certification_id) {
        $this->db->where('id', $certification_id);
        return $this->db->delete('certifications');
    }
	
	 // Fetch all projects for a given candidate
    public function get_projects($candidate_id) {
        $this->db->where('candidate_id', $candidate_id);
        $query = $this->db->get('projects');
        return $query->result_array();
    }

    // Insert a new project record
    public function store_project($data) {
        $this->db->insert('projects', $data);
        return $this->db->insert_id();
    }

    // Update an existing project record
    public function update_project($project_id, $data) {
        $this->db->where('id', $project_id);
        return $this->db->update('projects', $data);
    }

    // Delete a project record
    public function delete_project($project_id) {
        $this->db->where('id', $project_id);
        return $this->db->delete('projects');
    }
	
	 // Fetch all awards for a given candidate
    public function get_awards($candidate_id) {
        $this->db->where('candidate_id', $candidate_id);
        $query = $this->db->get('awards');
        return $query->result_array();
    }

    // Insert a new award record
    public function store_award($data) {
        $this->db->insert('awards', $data);
        return $this->db->insert_id();
    }

    // Update an existing award record
    public function update_award($award_id, $data) {
        $this->db->where('id', $award_id);
        return $this->db->update('awards', $data);
    }

    // Delete an award record
    public function delete_award($award_id) {
        $this->db->where('id', $award_id);
        return $this->db->delete('awards');
    }
	
	 // Fetch all hobbies for a given candidate
    public function get_hobbies($candidate_id) {
        $this->db->where('candidate_id', $candidate_id);
        $query = $this->db->get('hobbies');
        return $query->result_array();
    }

    // Insert a new hobby record
    public function store_hobby($data) {
        $this->db->insert('hobbies', $data);
        return $this->db->insert_id();
    }

    // Update an existing hobby record
    public function update_hobby($hobby_id, $data) {
        $this->db->where('id', $hobby_id);
        return $this->db->update('hobbies', $data);
    }

    // Delete a hobby record
    public function delete_hobby($hobby_id) {
        $this->db->where('id', $hobby_id);
        return $this->db->delete('hobbies');
    }
	
	// Fetch all courses for a candidate
    public function get_courses($candidate_id) {
        $this->db->where('candidate_id', $candidate_id);
        $query = $this->db->get('courses');
        return $query->result_array();
    }

    // Insert a new course record
    public function store_course($data) {
        $this->db->insert('courses', $data);
        return $this->db->insert_id();
    }

    // Update an existing course record
    public function update_course($course_id, $data) {
        $this->db->where('id', $course_id);
        return $this->db->update('courses', $data);
    }

    // Delete a course record
    public function delete_course($course_id) {
        $this->db->where('id', $course_id);
        return $this->db->delete('courses');
    }
	
	  // Fetch all extra curricular activities for a candidate
    public function get_extraCurricularActivities($candidate_id) {
        $this->db->where('candidate_id', $candidate_id);
        $query = $this->db->get('extraCurricularActivities');
        return $query->result_array();
    }

    // Insert a new extra curricular activity
    public function store_extraCurricularActivity($data) {
        $this->db->insert('extraCurricularActivities', $data);
        return $this->db->insert_id();
    }

    // Update an existing extra curricular activity
    public function update_extraCurricularActivity($activity_id, $data) {
        $this->db->where('id', $activity_id);
        return $this->db->update('extraCurricularActivities', $data);
    }

    // Delete an extra curricular activity
    public function delete_extraCurricularActivity($activity_id) {
        $this->db->where('id', $activity_id);
        return $this->db->delete('extraCurricularActivities');
    }
	
	 public function delete_skills_by_candidate($candidate_id) {
        $this->db->where('candidate_id', $candidate_id);
        return $this->db->delete('candidate_skills');
    }

    public function insert_skill($data) {
        return $this->db->insert('candidate_skills', $data);
    }

	
}