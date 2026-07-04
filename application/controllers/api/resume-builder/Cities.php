<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . 'libraries/REST_Controller.php';

class Cities extends REST_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    /**
     * GET: /api/cities
     * Public API (search + pagination)
     */
    public function index_get() {

        $search = $this->get('search');
        $page   = (int) $this->get('page') ?: 1;
        $limit  = (int) $this->get('limit') ?: 50;
        $offset = ($page - 1) * $limit;

        $this->db->select('
                c.city_id,
                c.city_name,
                c.state_id,
                c.country_id,
                co.country_name
            ')
            ->from('cities c')
            ->join('countries co', 'c.country_id = co.country_id', 'left');

        if (!empty($search)) {
            $this->db->like('c.city_name', $search);
        }

        // 🔢 Total count (pagination ke liye)
        $count_db = clone $this->db;
        $total    = $count_db->count_all_results();

        // 📄 Data fetch
        $this->db->limit($limit, $offset);
        $query = $this->db->get();

        $cities = [];
        foreach ($query->result() as $row) {
            $cities[] = [
                'city_id'      => $row->city_id,
                'city_name'    => $row->city_name,
                'state_id'     => $row->state_id,
                'country_id'   => $row->country_id,
                'country_name' => $row->country_name,
                'display_text' => $row->city_name . ', ' . $row->country_name
            ];
        }

        return $this->response([
            'status' => true,
            'data'   => $cities,
            'total'  => $total,
            'page'   => $page,
            'limit'  => $limit
        ], REST_Controller::HTTP_OK);
    }
}
