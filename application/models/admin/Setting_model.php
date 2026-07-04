<?php class Setting_model extends CI_Model {
    
    
     public function getAdminById($adminId) {
        $this->db->select('E.*, E.id as employer_id, I.industry_name');
        $this->db->from('employer as E');
        $this->db->join('industry as I', 'I.id = E.industry_type', 'left'); // Assuming the column name for industry ID in the employer table is 'industry_id'
        $this->db->where('E.user_type', 'admin');
        $this->db->where('E.id', $adminId);
        $query = $this->db->get();
        if ($query && $query->num_rows() > 0) {
            return $query->row_array();
        } else {
            return null; // Employer not found
        }
    }
    
    public function updateAdminData($data, $adminId) {
        // Update the admin data in your database
        $this->db->where('id', $adminId); // Replace 'id' with your actual admin identifier column
        $this->db->update('employer', $data); // Replace 'admin' with your actual table name
        return $this->db->affected_rows() > 0;
    }

    public function saveSettings($variableName, $value, $sendingLimit, $cronJobStatus) {
        $data = array(
            'option_name' => $variableName,
            'option_value' => $value,
            'send_limit' => $sendingLimit,
            'status' => $cronJobStatus
        );

        $this->db->insert('options', $data);
        return $this->db->affected_rows() > 0; // Return true if the insert was successful
    }
    
    public function getSettings() {
        $query = $this->db->get('options');
        return $query->result_array();
    }


    public function updateSetting($data) {
        $this->db->where('id', $data['id']);
        return $this->db->update('options', $data);
    }

    

}
?>