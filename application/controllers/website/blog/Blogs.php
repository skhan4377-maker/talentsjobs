<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Blogs extends CI_Controller {

	public function __construct(){
		parent::__construct();
		$this->load->model('blog/Blog_mdl');
	}
	
	public function index() {
		$page = max((int)$this->input->get('page'), 1);
		$per_page = 12;

		$search = $this->input->get('search');
		$total_blogs = $this->Blog_mdl->get_total_blogs($search);

		$max_page = ceil($total_blogs / $per_page);
		$page = min($page, $max_page);
		$offset = ($page - 1) * $per_page;

		$data['blogs'] = $this->Blog_mdl->get_blogs([
			'limit' => $per_page,
			'offset' => $offset,
			'search' => $search
		]);

		$this->load->library('pagination');

		$config['base_url'] = base_url('blogs');
		$config['total_rows'] = $total_blogs;
		$config['per_page'] = $per_page;
		$config['page_query_string'] = TRUE;
		$config['query_string_segment'] = 'page';
		$config['reuse_query_string'] = TRUE;
		$config['first_link'] = 'First';
		$config['last_link']  = 'Last';
		$config['num_links'] = 2;

		$config['full_tag_open']  = '<ul class="flex flex-wrap justify-center mt-8 gap-2">';
		$config['full_tag_close'] = '</ul>';

		$config['num_tag_open']   = '<li>';
		$config['num_tag_close']  = '</li>';

		$config['cur_tag_open']   = '<li><a class="px-4 py-2 bg-blue-600 text-white rounded-lg shadow" href="#">';
		$config['cur_tag_close']  = '</a></li>';

		$config['first_tag_open'] = '<li>';
		$config['first_tag_close'] = '</li>';

		$config['last_tag_open'] = '<li>';
		$config['last_tag_close'] = '</li>';

		$config['prev_tag_open'] = '<li>';
		$config['prev_tag_close'] = '</li>';

		$config['next_tag_open'] = '<li>';
		$config['next_tag_close'] = '</li>';

		$config['attributes'] = [
			'class' => 'px-4 py-2 bg-white text-gray-700 rounded-lg shadow hover:shadow-md transition-shadow'
		];


		$this->pagination->initialize($config);
		$data['pagination_links'] = $this->pagination->create_links();

		$data['recent_blogs'] = $this->Blog_mdl->get_blogs(['limit' => 5]);
		$data['popular_blogs'] = $this->Blog_mdl->popular_blogs_category(1, 6);

		$data['title'] = 'Expert Insights & Top Trending Blogs | ' . SITE_NAME;
		$data['description'] = 'Discover expert insights, how‑to guides, and top trending blog posts on [your niche topics]. Stay informed, get inspired, and elevate your knowledge with YourSiteName.';

		$this->load->view('particles/header', $data);
		$this->load->view('particles/nav');
		$this->load->view('website/blog/blogs', $data);
		$this->load->view('particles/footer');
	}

	
    public function blog_detail() {
		$this->load->model('blog/Blog_mdl');   
		$segment = $this->uri->segment(2);
		
		if ($segment) {
			$segment = $this->db->escape_str($segment);

			$post = $this->Blog_mdl->blog(false, false, 1, $segment);
			if (!$post) {
				redirect('blog');
				exit;
			}

			$data['read_blog']     = $post;
			$data['recent_blogs']  = $this->Blog_mdl->get_blogs(['limit' => 5]);
			$data['popular_blogs'] = $this->Blog_mdl->popular_blogs_category(1, 6);
		} else {
			redirect('blog');
			exit;
		}

		// ───── Dynamic SEO Metadata ─────
		$data['title'] = $post['blogs_title'] . ' | ' . SITE_NAME;
		$plain = strip_tags($post['blogs_content']);
		$data['description'] = (mb_strlen($plain) > 160)
			? mb_substr($plain, 0, 157) . '…'
			: $plain;

		// Related jobs
		//$this->load->model('Jobs/Jobs_model');
		//$data['mightBeLike'] = $this->Jobs_model->mightBeLike(10, false, false);

		// Views
		$this->load->view('particles/header', $data);
		$this->load->view('particles/nav');
		$this->load->view('website/blog/blog-detail', $data);  
		$this->load->view('particles/footer');  
	}

	/*public function fix_missing_banners() {
		  $candidates = $this->db->select('candidate_id, resume')
							   ->from('tb_candidate')
							   ->where('resume IS NOT NULL')
							   ->where('resume !=', '')
							   ->get()
							   ->result_array();

		$updated = 0;
		foreach ($candidates as $candidate) {
			$file = FCPATH . 'uploads/candidate/resume/' . trim($candidate['resume']);
			if (!file_exists($file)) {
				$this->db->where('candidate_id', $candidate['candidate_id']);
				$this->db->update('tb_candidate', ['resume' => NULL]);
				$updated++;
			}
		}

		echo "$updated candidate resumes set to NULL due to missing files.";
		}*/


	

}
