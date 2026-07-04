<?php class EmployerPlans extends MY_Controller {
    
    public function __construct() {
        parent::__construct();
        // Load the session library
        $this->load->library('session');

        // Check if the user is logged in
        if(!$this->session->userdata('logged_in')) {
            // User is not logged in, redirect to the login page or display an error message
            redirect('login'); // You should replace 'login' with the actual login page URL
        }
        $this->load->model('admin/subscriptions/EmployerPlans_model');
    }
    
    public function subscriptionsList() {
        $data['subscription_plans'] = $this->EmployerPlans_model->fetchAllSubscriptionPlans();
        // Load the view and pass the data
        $this->load->view('admin/subscriptions/employer/subscriptions-list', $data);
    }

    public function editSubscriptionPlan($plan_id) {
        // Load the subscription plan data by its ID
        $data['subscription_plan'] = $this->EmployerPlans_model->getSubscriptionPlanById($plan_id);
    
        // Load the view for editing the subscription plan
        $this->load->view('admin/subscriptions/employer/add-edit-subscription-plan', $data);
    }


    public function addSubscriptionPlan() {
       $this->load->view('admin/subscriptions/employer/add-edit-subscription-plan'); 
    }
    
    
    public function createSubscriptionPlan() {
        // Get the ID from the form data
        $id = $this->input->post('id');
        
        // Set validation rules for the form fields
        $this->form_validation->set_rules('plan_name', 'Selected Plan', 'required');
        $this->form_validation->set_rules('price', 'Price', 'required|numeric');
        $this->form_validation->set_rules('features', 'Features', 'required|max_length[2000]');
        $this->form_validation->set_rules('duration', 'Duration', 'required|numeric');
        $this->form_validation->set_rules('plan_type', 'Selected Plan Type', 'required');
        $this->form_validation->set_rules('offer_description', 'Offer Description', 'max_length[1000]');        
        $this->form_validation->set_rules('offer_terms_and_conditions', 'Offer Terms and Conditions', 'required|max_length[1000]');
    
        // Additional fields
        $this->form_validation->set_rules('cv_views_per_requirement', 'CV Views per Requirement', 'required|numeric');
        $this->form_validation->set_rules('search_results_limit', 'Search Results Limit', 'required|numeric');
        $this->form_validation->set_rules('single_user_access', 'Single User Access', 'required|in_list[0,1]');
        $this->form_validation->set_rules('email_multiple_candidates', 'Email Multiple Candidates', 'required|in_list[0,1]');
        $this->form_validation->set_rules('download_cvs_in_bulk', 'Download CVs in Bulk', 'required|in_list[0,1]');
    
        if ($this->form_validation->run() === FALSE) {
            // If validation fails, return a validation error response
            $response['success'] = false;
            $response['message'] = validation_errors();
        } else {
            // Prepare the data for insertion or update
            $data = array(
                'plan_name' => $this->input->post('plan_name'),
                'plan_type' => $this->input->post('plan_type'),
                'price' => $this->input->post('price'),
                'features' => $this->input->post('features'),
                'duration' => $this->input->post('duration'),
                'offer' => $this->input->post('offer'),
                'offer_description' => $this->input->post('offer_description'),
                'offer_code' => $this->input->post('offer_code'),
                'offer_terms_and_conditions' => $this->input->post('offer_terms_and_conditions'),
                // Additional fields
                'cv_views_per_requirement' => $this->input->post('cv_views_per_requirement'),
                'search_results_limit' => $this->input->post('search_results_limit'),
                'single_user_access' => $this->input->post('single_user_access'),
                'email_multiple_candidates' => $this->input->post('email_multiple_candidates'),
                'download_cvs_in_bulk' => $this->input->post('download_cvs_in_bulk'),
				'is_recommended' => $this->input->post('is_recommended'),
            );
    
            if ($id == '0') {
                // Insert the data
                $data['created_at'] = date('Y-m-d H:i:s'); // Current timestamp
                $result = $this->EmployerPlans_model->addSubscriptionPlan($data);
            } else {
                // Update the data
                $result = $this->EmployerPlans_model->updateSubscriptionPlan($id, $data);
            }
    
            if ($result) {
                // If insertion/update is successful, return a success response
                $response['success'] = true;
                $response['message'] = ($id == '0') ? 'Subscription plan added successfully!' : 'Subscription plan updated successfully!';
            } else {
                // If insertion/update fails, return an error response
                $response['success'] = false;
                $response['message'] = 'Failed to ' . ($id == '0' ? 'add' : 'update') . ' the subscription plan. Please try again.';
            }
        }
        
        // Send the response as JSON
        header('Content-Type: application/json');
        echo json_encode($response);
    }









    
}?>