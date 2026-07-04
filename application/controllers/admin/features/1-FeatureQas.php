<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class FeatureQas extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('admin/services/Feature_model');       
    }

    public function get_feature_qas($feature_id) {
        $qas = $this->Feature_model->get_qas_by_feature($feature_id);
        echo json_encode($qas);
    }

    public function save_feature_qas() {
        $feature_id = $this->input->post('feature_id');
        $qas = $this->input->post('qas');

        if (!$feature_id || !is_array($qas)) {
            echo json_encode(['success' => false, 'message' => 'Invalid input.']);
            return;
        }

        $this->Feature_model->save_qas($feature_id, $qas);

        echo json_encode(['success' => true, 'message' => 'Q&A saved successfully.']);
    }
}
