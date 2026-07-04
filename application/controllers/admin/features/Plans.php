<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Plans extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('admin/Plans_model');
        $this->load->model('admin/Features_model'); // to get features list
    }

    public function index() {
        $data['plans'] = $this->Plans_model->get_all_plans();
        $data['features'] = $this->Features_model->get_all_features();
        $data['title'] = 'Manage Feature Plans';
        $data['content'] = $this->load->view('admin/plans/manage_plans', $data, true);
        $this->load->view('templates/master', $data);
    }

    // AJAX: Get single plan for editing
    public function get_plan($id) {
        $plan = $this->Plans_model->get_plan_by_id($id);
        if ($plan) {
            echo json_encode([
                'success' => true,
                'data' => $plan,
                'csrf_token' => $this->_csrf_response()
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Plan not found',
                'csrf_token' => $this->_csrf_response()
            ]);
        }
    }

    // AJAX: Add new plan
    public function add_plan() {
        $this->form_validation->set_rules('feature_id', 'Feature', 'required|integer');
        $this->form_validation->set_rules('plan_level', 'Plan Level', 'required|in_list[All Level,Entry To Mid Level,Senior To Executive Level]');
        $this->form_validation->set_rules('duration', 'Duration', 'required|in_list[1 Month,2 Months,3 Months,6 Months,Annual]');
        $this->form_validation->set_rules('plan_mrp', 'MRP', 'required|numeric|greater_than[0]');
        $this->form_validation->set_rules('plan_discount', 'Discount', 'required|numeric|greater_than_equal_to[0]|less_than_equal_to[100]');
        $this->form_validation->set_rules('plan_taxes', 'Taxes', 'required|numeric|greater_than_equal_to[0]');
        $this->form_validation->set_rules('plan_total', 'Total', 'required|numeric|greater_than[0]');
        $this->form_validation->set_rules('monthly_cost', 'Monthly Cost', 'required|numeric|greater_than[0]');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode([
                'success' => false,
                'message' => validation_errors(),
                'csrf_token' => $this->_csrf_response()
            ]);
            return;
        }

        $feature_id = $this->input->post('feature_id');
        $duration = $this->input->post('duration');

        // Check duplicate plan for same feature and duration
        if ($this->Plans_model->plan_exists($feature_id, $duration)) {
            echo json_encode([
                'success' => false,
                'message' => 'A plan with this duration already exists for the selected feature.',
                'csrf_token' => $this->_csrf_response()
            ]);
            return;
        }

        $data = [
            'feature_id'        => $feature_id,
            'plan_level'        => $this->input->post('plan_level'),
            'experience_range'  => $this->input->post('experience_range') ?: null,
            'duration'          => $duration,
            'plan_mrp'          => $this->input->post('plan_mrp'),
            'plan_discount'     => $this->input->post('plan_discount'),
            'plan_taxes'        => $this->input->post('plan_taxes'),
            'plan_total'        => $this->input->post('plan_total'),
            'monthly_cost'      => $this->input->post('monthly_cost'),
            'created_at'        => date('Y-m-d H:i:s')
        ];

        $insert_id = $this->Plans_model->insert_plan($data);
        if ($insert_id) {
            echo json_encode([
                'success' => true,
                'message' => 'Plan added successfully',
                'csrf_token' => $this->_csrf_response()
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Database error',
                'csrf_token' => $this->_csrf_response()
            ]);
        }
    }

    // AJAX: Update plan
    public function update_plan() {
        $id = $this->input->post('plan_id');
        if (!$id) {
            echo json_encode([
                'success' => false,
                'message' => 'Plan ID required',
                'csrf_token' => $this->_csrf_response()
            ]);
            return;
        }

        $this->form_validation->set_rules('feature_id', 'Feature', 'required|integer');
        $this->form_validation->set_rules('plan_level', 'Plan Level', 'required|in_list[All Level,Entry To Mid Level,Senior To Executive Level]');
        $this->form_validation->set_rules('duration', 'Duration', 'required|in_list[1 Month,2 Months,3 Months,6 Months,Annual]');
        $this->form_validation->set_rules('plan_mrp', 'MRP', 'required|numeric|greater_than[0]');
        $this->form_validation->set_rules('plan_discount', 'Discount', 'required|numeric|greater_than_equal_to[0]|less_than_equal_to[100]');
        $this->form_validation->set_rules('plan_taxes', 'Taxes', 'required|numeric|greater_than_equal_to[0]');
        $this->form_validation->set_rules('plan_total', 'Total', 'required|numeric|greater_than[0]');
        $this->form_validation->set_rules('monthly_cost', 'Monthly Cost', 'required|numeric|greater_than[0]');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode([
                'success' => false,
                'message' => validation_errors(),
                'csrf_token' => $this->_csrf_response()
            ]);
            return;
        }

        $feature_id = $this->input->post('feature_id');
        $duration = $this->input->post('duration');

        // Check duplicate excluding current
        if ($this->Plans_model->plan_exists($feature_id, $duration, $id)) {
            echo json_encode([
                'success' => false,
                'message' => 'A plan with this duration already exists for the selected feature.',
                'csrf_token' => $this->_csrf_response()
            ]);
            return;
        }

        $data = [
            'feature_id'        => $feature_id,
            'plan_level'        => $this->input->post('plan_level'),
            'experience_range'  => $this->input->post('experience_range') ?: null,
            'duration'          => $duration,
            'plan_mrp'          => $this->input->post('plan_mrp'),
            'plan_discount'     => $this->input->post('plan_discount'),
            'plan_taxes'        => $this->input->post('plan_taxes'),
            'plan_total'        => $this->input->post('plan_total'),
            'monthly_cost'      => $this->input->post('monthly_cost'),
            'updated_at'        => date('Y-m-d H:i:s')
        ];

        $updated = $this->Plans_model->update_plan($id, $data);
        if ($updated) {
            echo json_encode([
                'success' => true,
                'message' => 'Plan updated successfully',
                'csrf_token' => $this->_csrf_response()
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'No changes or update failed',
                'csrf_token' => $this->_csrf_response()
            ]);
        }
    }

    // AJAX: Delete plan
    public function delete_plan() {
        $id = $this->input->post('id');
        if (!$id) {
            echo json_encode([
                'success' => false,
                'message' => 'Plan ID required',
                'csrf_token' => $this->_csrf_response()
            ]);
            return;
        }

        $deleted = $this->Plans_model->delete_plan($id);
        if ($deleted) {
            echo json_encode([
                'success' => true,
                'message' => 'Plan deleted successfully',
                'csrf_token' => $this->_csrf_response()
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Delete failed',
                'csrf_token' => $this->_csrf_response()
            ]);
        }
    }

    // Helper for CSRF response
    private function _csrf_response() {
        return [
            'name' => $this->security->get_csrf_token_name(),
            'hash' => $this->security->get_csrf_hash()
        ];
    }
}
?>