<?php class Profile_mdl extends CI_Model{
	
	public function __construct(){
		parent::__construct();
	}
    
	function get_employer_details($id) {
		$this->db->select([
			'E.employer_id',
			'E.name',
			'E.last_name',
			'E.gender',
			'E.employee_designation',
			'E.company_name',
			'E.status',
			'E.mobile',
			'E.alternate_contact',
			'E.email_status',
			'E.call_status',
			'E.recuiter_type',
			'E.company_type',
			'E.city_id',
			'E.about_company',
			'E.logo',
			'E.email',
			'C.city_name',
			'E.company_address',
			'E.industry_id',
			'I.industry_name',
			'E.expertise_specialization',
			'E.membership_type',
			'E.company_website',		
			'E.is_verified',
			'E.company_size',
			'E.company_founded',
			'E.role',
			'E.created_at',
			'E.last_login',
			'E.login_token',
			'E.updated_at'
		]);
		$this->db->from('employer E');
		$this->db->join('cities C', 'C.city_id = E.city_id', 'left');
		$this->db->join('industry I', 'I.industry_id = E.industry_id', 'left');
		$this->db->where('E.employer_id', $id);
		
		$query = $this->db->get();
		return $query->row_array();
	}
	
    function insert_employer_data($data) {
        $this->db->trans_start();
        $this->db->insert('employer', $data);
        $inserted_id = $this->db->insert_id();
        $this->db->trans_complete();
        return $inserted_id;
    } 
    	 
	public function update_employer_details($employer_id, $udata, $section) {
		$this->db->trans_start();

		if (!empty($udata)) {
			$this->db->update('employer', $udata, ['employer_id' => $employer_id]);
		}

		$this->db->trans_complete();
		return $employer_id;
	}


	
    
	
}