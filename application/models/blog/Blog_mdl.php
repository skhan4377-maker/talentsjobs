<?php class Blog_mdl extends CI_Model{
		
	public function __construct(){
		parent::__construct();
	}
	    
	function blog ($id='', $limit='', $status='', $segment=''){
        if($status){
            $this->db->where('B.blogs_status', (int) $status);   
        }
        if($id){
            $this->db->where('B.id', (int) $id);  
        }
        
        if($segment){
            $this->db->where('B.slug', $this->db->escape_str($this->uri->segment(2)));   
        }
        
        $this->db->select('B.id as blog_id, B.blogs_category, BC.category_name, B.blogs_tags, B.blogs_title, B.blogs_banner, B.blogs_content, B.slug, B.blogs_status, B.created_at');
        $this->db->from('blogs as B');
        $this->db->join('blogs_category as BC', 'BC.id = B.blogs_category');
        
        $query = $this->db->get();
        return $query->row_array();  
    }

	// In Blog_mdl Model
	public function get_blogs($params = []) {
		// अब default में limit null है
		$defaults = [
			'limit'   => null,  
			'offset'  => 0,
			'status'  => 1,
			'search'  => null,
		];
		$options = array_merge($defaults, $params);

		$this->db->select('B.*, BC.category_name')
				 ->from('blogs as B')
				 ->join('blogs_category as BC', 'BC.id = B.blogs_category', 'left')
				 ->where('B.blogs_status', $options['status']);

		if (!empty($options['search'])) {
			$this->db->group_start()
					 ->like('B.blogs_title',   $options['search'])
					 ->or_like('B.blogs_content', $options['search'])
				   ->group_end();
		}

		// केवल तभी लगाएँ जब limit सेट और > 0 हो
		if (isset($options['limit']) && is_numeric($options['limit']) && $options['limit'] > 0) {
			$this->db->limit((int)$options['limit'], (int)$options['offset']);
		}

		$this->db->order_by('B.created_at', 'DESC');

		return $this->db->get()->result_array();
	}

	public function get_total_blogs($search = null) {
		$this->db->from('blogs as B');
		
		// Join with blogs_category table to ensure consistency if filtering by category later
		$this->db->join('blogs_category as BC', 'BC.id = B.blogs_category', 'left');

		if ($search) {
			$this->db->group_start();
			$this->db->like('B.blogs_title', $search);
			$this->db->or_like('B.blogs_content', $search);
			$this->db->group_end();
		}
		
		$this->db->where('B.blogs_status', 1);
		return $this->db->count_all_results();
	}

    function popular_blogs_category($status='',$limit=''){
	    if($limit){
	        $this->db->limit($limit);
	    }
	    if($status){
	       $this->db->where('B.blogs_status',$status);     
	    }
	    $this->db->group_by('B.blogs_category');
	    $this->db->order_by('B.created_at','desc');
	  	$this->db->select('BC.category_name, B.slug, COUNT(B.blogs_category) as polular_category');
		$this->db->from('blogs as B');
		$this->db->join('blogs_category as BC','BC.id = B.blogs_category');
		$query = $this->db->get();
		return $query->result_array();  
	}
   
  


}