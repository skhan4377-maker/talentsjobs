<?php class Blogs_model extends CI_Model{
		
	public function __construct(){
		parent::__construct();
	}
	
	//3
    // Fetch category options for dropdown
    public function get_category() {
        return $this->db->get('blogs_category')->result_array();
    }
    
	//1
    public function count_all_blogs() {
        return $this->db->count_all('blogs');
    }

	//2
    // Fetch blogs with category name (for listing)
    public function fetch_blogs($limit, $start) {
        $this->db->select('blogs.*, bc.category_name as blogs_category_name');
        $this->db->from('blogs');
        $this->db->join('blogs_category as bc', 'bc.id = blogs.blogs_category');
        $this->db->order_by('created_at', 'DESC');
        $this->db->limit($limit, $start);
        return $this->db->get()->result_array();
    }
    
	//4
    // Get single blog by ID (for editing)
    public function get_blog_by_id($id) {
        return $this->db->get_where('blogs', ['id' => $id])->row_array();
    }
    
	//6
	 // Insert new blog
    public function insert_blog($data) {
        $this->db->insert('blogs', $data);
        return $this->db->insert_id();
    }
    
	//5
   // Update existing blog
    public function update_blog($id, $data) {
        $this->db->where('id', $id);
        return $this->db->update('blogs', $data);
    }
	//7
    // Delete blog
    public function delete_blog($id) {
        $this->db->where('id', $id);
        return $this->db->delete('blogs');
    }


}