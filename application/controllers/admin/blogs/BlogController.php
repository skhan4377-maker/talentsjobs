<?php defined('BASEPATH') OR exit('No direct script access allowed');

class BlogController extends MY_Controller {

     public function __construct(){
        parent::__construct();
        $this->load->model('admin/Blogs_model');
        $this->load->helper('url');
        $this->load->library('pagination');
    }

    public function index() {
       
        $config = array();
        $config["base_url"] = base_url('admin/blogs/BlogController');
        $config["total_rows"] = $this->Blogs_model->count_all_blogs();
        $config["per_page"] = 18;
        $config["page_query_string"] = TRUE;
        $config["use_page_numbers"] = TRUE;
        $config["query_string_segment"] = "page";

        // Bootstrap 5 Pagination Styling
        $config['full_tag_open'] = '<ul class="inline-flex items-center space-x-2">';
        $config['full_tag_close'] = '</ul>';
        $config['first_link'] = 'First';
        $config['last_link'] = 'Last';
        $config['prev_link'] = '&laquo;';
        $config['next_link'] = '&raquo;';
        $config['first_tag_open'] = '<li class="page-item">';
        $config['first_tag_close'] = '</li>';
        $config['last_tag_open'] = '<li class="page-item">';
        $config['last_tag_close'] = '</li>';
        $config['next_tag_open'] = '<li class="page-item">';
        $config['next_tag_close'] = '</li>';
        $config['prev_tag_open'] = '<li class="page-item">';
        $config['prev_tag_close'] = '</li>';
        $config['cur_tag_open'] = '<li class="page-item active"><a class="px-4 py-2 text-white bg-blue-600 rounded-lg shadow-md">';
        $config['cur_tag_close'] = '</a></li>';
        $config['num_tag_open'] = '<li class="page-item">';
        $config['num_tag_close'] = '</li>';
        $config['attributes'] = array('class' => 'px-4 py-2 text-gray-700 bg-white border rounded-lg hover:bg-blue-100');


        $this->pagination->initialize($config);

        $page = $this->input->get("page") ? $this->input->get("page") : 1;
        $start = ($page - 1) * $config["per_page"];

        $data["blogs"] = $this->Blogs_model->fetch_blogs($config["per_page"], $start);
        $data["pagination_links"] = $this->pagination->create_links();
        
         // Load the model or call the function to get the category data
        $blogs_category = $this->Blogs_model->get_category();  // Fetch categories from model
        // Pass the category data to the view
        $data['blogs_category'] = $blogs_category;
       
		$data['title'] = 'Manage Blogs';
		$data['content'] = $this->load->view('admin/blogs/list_blogs', $data, true);
		$this->load->view('templates/master', $data);
    }
	
	 // Fetch single blog for editing
    public function get_blog($id) {
        $blog = $this->Blogs_model->get_blog_by_id($id);
        echo json_encode($blog);
    }
	
    // Handle AJAX form submission (create/update)
	public function save_edit_blog() {

		$this->form_validation->set_rules('blogs_title', 'Title', 'required|min_length[3]');
		$this->form_validation->set_rules('blogs_content', 'Content', 'required');
		$this->form_validation->set_rules('blogs_category', 'Category', 'required');

		if (!$this->form_validation->run()) {
			echo json_encode([
				'status' => 'error',
				'message' => validation_errors(),
				'csrf_token' => [
					'name'  => $this->security->get_csrf_token_name(),
					'hash'  => $this->security->get_csrf_hash()
				]
			]);
			return;
		}

		$blog_id = $this->input->post('blog_id');
		$data = [
			'blogs_title' => $this->input->post('blogs_title'),
			'blogs_content' => $this->input->post('blogs_content'),
			'blogs_category' => $this->input->post('blogs_category'),
			'blogs_tags' => $this->input->post('blogs_tags'),
			'blogs_status' => $this->input->post('blogs_status'),
			'slug' => url_title($this->input->post('blogs_title'), 'dash', TRUE)
		];

		// Handle image upload
		if (!empty($_FILES['blogs_banner']['name'])) {
			$config = [
				'upload_path'   => './uploads/blogs/',
				'allowed_types' => 'jpg|png|jpeg|webp|avif',
				'max_size'      => 2048,
				'encrypt_name'  => TRUE
			];

			$this->load->library('upload', $config);

			if ($this->upload->do_upload('blogs_banner')) {
				$upload_data = $this->upload->data();
				$data['blogs_banner'] = $upload_data['file_name'];

				// Remove old image
				if ($blog_id) {
					$old = $this->Blogs_model->get_blog_by_id($blog_id);
					if ($old['blogs_banner']) @unlink("./uploads/blogs/{$old['blogs_banner']}");
				}
			} 
			else {
				echo json_encode([
					'status' => 'error',
					'message' => $this->upload->display_errors(),
					'csrf_token' => [
						'name'  => $this->security->get_csrf_token_name(),
						'hash'  => $this->security->get_csrf_hash()
					]
				]);
				return;
			}
		}

		// Insert or Update
		if ($blog_id) {
			$this->Blogs_model->update_blog($blog_id, $data);
			$message = "Blog updated successfully";
		} else {
			$data['created_at'] = date("Y-m-d H:i:s");
			$this->Blogs_model->insert_blog($data);
			$message = "Blog created successfully";
		}

		echo json_encode([
			'status' => 'success',
			'message' => $message,
			'csrf_token' => [
				'name'  => $this->security->get_csrf_token_name(),
				'hash'  => $this->security->get_csrf_hash()
			]
		]);
	}
   

   // Handle blog deletion
	public function delete($id)
	{
		if (!$this->input->is_ajax_request()) show_404();

		$blog = $this->Blogs_model->get_blog_by_id($id);

		if ($blog && $blog['blogs_banner']) {
			@unlink("./uploads/blogs/{$blog['blogs_banner']}");
		}

		$this->Blogs_model->delete_blog($id);

		echo json_encode([
			'status' => 'success',
			'message' => 'Blog deleted successfully',
			'csrf_token' => [
				'name' => $this->security->get_csrf_token_name(),
				'hash' => $this->security->get_csrf_hash()
			]
		]);
	}

    
}
