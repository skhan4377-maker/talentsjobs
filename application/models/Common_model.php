<?php 
class Common_model extends CI_Model {	
    
    public function searchCities($term, $limit = 5) {
        return $this->db
            ->select('city_id, city_name, state_id')
            ->like('city_name', $term, 'both')
            ->order_by('city_name', 'ASC')
            ->limit($limit)
            ->get('tb_cities')
            ->result_array();
    }
    
    // index page search city me use ho raha hai 9.5.25
    public function searchJobCities($term, $limit = 5) {
        return $this->db
            ->select('DISTINCT(tb_cities.city_id), tb_cities.city_name', false)
            ->from('tb_cities')
            ->join('tb_job_cities', 'tb_job_cities.city_id = tb_cities.city_id')
            ->like('tb_cities.city_name', $term, 'after')
            ->order_by('tb_cities.city_name', 'ASC')
            ->limit($limit)
            ->get()
            ->result_array();
    }

    public function searchCountries($term, $limit = 5) {
        return $this->db
            ->select('country_id, country_name')
            ->like('country_name', $term, 'both')
            ->order_by('country_name', 'ASC')
            ->limit($limit)
            ->get('tb_countries')
            ->result_array();
    }
    
    /**
     * Registration form ke country dropdown ke liye
     * Saare countries return karta hai (phonecode + iso2 ke saath)
     */
    public function get_countries_for_dropdown() {
        $this->db->select('country_id, country_name, phonecode, iso2');
        $this->db->where('iso2 IS NOT NULL', NULL, FALSE);
        $this->db->order_by('country_name', 'ASC');
        $result = $this->db->get('tb_countries')->result_array();
        
        // phonecode ko + ke saath format karo
        foreach ($result as &$row) {
            $row['dial_code'] = '+' . $row['phonecode'];
        }
        return $result;
    }
    
}
?>