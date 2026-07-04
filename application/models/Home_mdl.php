<?php class Home_mdl extends CI_Model{
		
	public function __construct(){
		parent::__construct();
	}
	

    public function check_exist_value($mobile = '', $email = '', $type = '') {
        if ($type === 'candidate') {
            // Select candidate_id as id along with other candidate details
            $this->db->select('candidate_id, otp, name, email, mobile');
            $this->db->group_start();
            if (!empty($mobile)) {
                $this->db->where('mobile', $mobile);
            }
            if (!empty($email)) {
                if (!empty($mobile)) {
                    $this->db->or_where('email', $email);
                } else {
                    $this->db->where('email', $email);
                }
            }
            $this->db->group_end();
            $this->db->from('candidate');
        }
    
        if ($type === 'employer') {
            // Select employer_id as id along with other employer details
            $this->db->select('employer_id, otp, company_name, mobile, email');
            $this->db->group_start();
            if (!empty($mobile)) {
                $this->db->where('mobile', $mobile);
            }
            if (!empty($email)) {
                if (!empty($mobile)) {
                    $this->db->or_where('email', $email);
                } else {
                    $this->db->where('email', $email);
                }
            }
            $this->db->group_end();
            $this->db->from('employer');
        }
    
        $query = $this->db->get();
        return $query->row_array();
    }

	
	function getCampaign($id = '', $status = '', $limit = 1) {
        $this->db->select('C.id, C.campaign_name, C.campaign_subject, C.campaign_content, C.campaign_status');
        $this->db->from('campaign as C');
        if ($limit) {
            $this->db->limit($limit);
        }
        if ($status) {
            $this->db->where('C.campaign_status', '1');
        } else {
            $this->db->where('C.id', $id);
        }
        $query = $this->db->get();
        return $query->row_array();
    }

	function campaigns(){
	  	$this->db->select('C.id, C.campaign_name,C.campaign_subject,C.campaign_status, C.created_dt');
		$this->db->from('campaign as C');
		//$this->db->where('C.campaign_status','1');
		$query = $this->db->get();
		return $query->result_array();  
	}
	
    public function FetchCampaign($status = '') {
        $current_time = date('Y-m-d H:i:s'); // Current time
    
        $this->db->select('id, campaign_name, campaign_subject, campaign_content, campaign_status, start_datetime, end_datetime');
        $this->db->from('campaign');
    
        // Filter campaigns that are currently active based on start and end time
        $this->db->where('start_datetime <=', $current_time); // Campaign has started
        $this->db->where('end_datetime >=', $current_time);   // Campaign hasn't ended yet
    
        if ($status !== '') {
            $this->db->where('campaign_status', $status); // Optional: Filter by status
        }
    
        return $this->db->get()->result_array();
    }

    


}