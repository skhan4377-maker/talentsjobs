<?php class Setting extends MY_Controller {
    
    public function __construct() {
        parent::__construct();
        // Load the session library
        $this->load->library('session');

        // Check if the user is logged in
        if(!$this->session->userdata('logged_in')) {
            // User is not logged in, redirect to the login page or display an error message
            redirect('login'); // You should replace 'login' with the actual login page URL
        }
        $this->load->model('admin/Setting_model');
    }
    
    
    public function index() {
        // Fetch settings from the model
        $settings = $this->Setting_model->getSettings();
        // Pass the settings to the view
        $data['settings'] = $settings;
        $this->load->view('admin/setting', $data);
    }

    public function saveSettings() {
        // Validate the form data
        $this->form_validation->set_rules('variable_name', 'Variable Name', 'required');

        if ($this->form_validation->run() === FALSE) {
            // Validation failed
            $response = array('message' => validation_errors(), 'success' => false);
        } else {
            // Form data is valid
            $variableName = str_replace(' ', '_', $this->input->post('variable_name')); // Replace spaces with underscores
            $value = $this->input->post('value');
            $sendingLimit = $this->input->post('sending_limit');
            $cronJobStatus = $this->input->post('cron_job_status');

            // Save data to the database
            $saved = $this->Setting_model->saveSettings($variableName, $value, $sendingLimit, $cronJobStatus);

            if ($saved) {
                $response = array('message' => 'Settings saved successfully', 'success' => true);
            } else {
                $response = array('message' => 'Failed to save settings', 'success' => false);
            }
        }

        // Send a JSON response back to the client
        $this->output->set_content_type('application/json')->set_output(json_encode($response));
    }
    
    public function updateSetting() {
    // Check if it's an AJAX request
        if ($this->input->is_ajax_request()) {
        // Get the data sent from the frontend
        $id = $this->input->post('id');
        $variableName = str_replace(' ', '_', $this->input->post('variable_name'));
        $value = $this->input->post('value');
        $sendingLimit = $this->input->post('sending_limit');
        $cronJobStatus = $this->input->post('cron_job_status');

        // Validate the data (you can add more validation)
        if (!empty($id) && !empty($variableName)) {
            // Update the setting in the database
            $data = array(
                'option_name' => $variableName,
                'option_value' => $value,
                'send_limit' => $sendingLimit,
                'status' => $cronJobStatus
            );

            // Use the 'id' to identify the row to be updated
            $this->db->where('id', $id);
            $result = $this->db->update('options', $data);

            if ($result) {
                // Setting updated successfully
                echo json_encode(array('status' => 'success', 'message' => 'Setting updated successfully.'));
            } else {
                // Error while updating
                echo json_encode(array('status' => 'error', 'message' => 'Error updating setting.'));
            }
        } else {
            // Invalid data
            echo json_encode(array('status' => 'error', 'message' => 'Invalid data.'));
        }
        }
    }



    
    public function myprofile(){
        $adminId = $this->session->userdata('id');
        $adminDetails = $this->Setting_model->getAdminById($adminId); // Update the method name
        $data['adminDetails'] = $adminDetails; // Pass admin details to the view
        $this->load->view('admin/myprofile', $data);
    }
   
    public function updateAdminData() {
        // Get the form data from the POST request
        $adminId = $this->session->userdata('id');
        $companyName = $this->input->post('company_name');
        $contactPerson = $this->input->post('contact_person');
        $companyAddress = $this->input->post('company_address');
        $cityName = $this->input->post('city_name');
        $workEmail = $this->input->post('work_email');
        $companyContact = $this->input->post('company_contact');
        $companyFounded = $this->input->post('company_founded');
        $companyWebsite = $this->input->post('company_website');
    
        // Prepare an array with the updated data
        $updateData = array(
            'employee_company_name' => $companyName,
            'employee_name' => $contactPerson,
            'company_address' => $companyAddress,
            'company_location' => $cityName,
            'work_email' => $workEmail,
            'company_contact' => $companyContact,
            'company_founded' => $companyFounded,
            'company_website' => $companyWebsite,
            // Add other fields as needed
        );
    
        // Update the admin data using the model
        $result = $this->Setting_model->updateAdminData($updateData, $adminId);
        // Check if the update was successful
        if ($result) {
            $response = array('success' => true, 'message' => 'Admin data updated successfully');
        } else {
            $response = array('success' => false, 'message' => 'Failed to update admin data');
        }
        
    
        echo json_encode($response);
    }
    
    
   


    
}?>