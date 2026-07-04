<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once FCPATH . 'vendor/autoload.php';

class ResumeBuilder extends CI_Controller {

    public function __construct() {
        parent::__construct();
        //if (!isCandidateLoggedIn()) {
			//redirect('auth/login');
		//}
		//updateCandidateSession();
		//$userDetails = getUserDetails();
		//$this->user_id = $userDetails['id'];
		//$this->designation = $userDetails['designation'];				
        $this->load->model('admin/services/ResumeTemplateModel'); // Load the template model
        $this->load->helper('url');
        $this->load->library('MYPDF');
    }
 
    
    public function edit_resume(){
       $this->load->view('career-services/sub_feature/edit_resume'); 
    }

    /*public function extractCandidateDetails() {
        $user_id = $this->user_id; // Get user_id from session
    
        if (!$user_id) {
            echo json_encode(['success' => false, 'message' => 'User not logged in.']);
            return;
        }
    
        //$this->load->model('ResumeModel'); // Load your model
        $candidate_details = $this->ResumeTemplateModel->getCandidateDetailsByUserId($user_id);
    
        if ($candidate_details) {
            $placeholders = json_decode($candidate_details['placeholders'], true);
    
            if (is_array($placeholders)) {
                echo json_encode(['success' => true, 'data' => $placeholders]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Invalid JSON format.']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'No candidate details found.']);
        }
    }*/

    public function preview() {
        $template_id = $this->input->post('template_id');
        //$user_id = $this->user_id;
    
        // Fetch or update template (existing code)
        $existingTemplate = $this->ResumeTemplateModel->get_user_selected_template($template_id);
        if ($existingTemplate) {
            $this->ResumeTemplateModel->update_user_template($template_id);
            $template = $existingTemplate;
        } else {
            $this->ResumeTemplateModel->insert_user_template($template_id);
            $template = $this->ResumeTemplateModel->get_template_by_id($template_id);
        }
    
        $placeholders = json_decode($template['placeholders'], true);
        $layout_html = $template['layout_html'];
    
        foreach ($placeholders as $key => $value) {
            if (is_array($value)) {
                // Handle array placeholders (employment, education, etc.)
                $layout_html = $this->replace_array_placeholders($key, $value, $layout_html);
            } else {
                // Replace scalar placeholders
                $layout_html = str_replace("{" . $key . "}", htmlspecialchars($value, ENT_QUOTES, 'UTF-8'), $layout_html);
            }
        }
    
        // Append CSS (existing code)
        $css_file = isset($template['css_file']) ? base_url($template['css_file']) : '';
        $html_with_css = $this->applyDynamicCss($layout_html, $css_file);
        echo $html_with_css;
    }
    
    private function replace_array_placeholders($key, $entries, $layout_html) {
        $placeholder = "{" . $key . "}";
        if (strpos($layout_html, $placeholder) === false) {
            return $layout_html; // Skip if placeholder not found
        }
            
         
        $output = '';
        foreach ($entries as $entry) {
            $entry_html = '';
            switch ($key) {
                case 'employmentHistory':
                    $entry_html = '
                    <li class="resume-placeholder">
                        <strong>' . htmlspecialchars($entry['jobTitle'], ENT_QUOTES) . '</strong> at ' . 
                        htmlspecialchars($entry['employerName'], ENT_QUOTES) . ' (' . 
                        htmlspecialchars($entry['startDate'], ENT_QUOTES) . ' - ' . 
                        htmlspecialchars($entry['endDate'], ENT_QUOTES) . ') in ' . 
                        htmlspecialchars($entry['workLocation'], ENT_QUOTES) . '<br>' . 
                        htmlspecialchars($entry['responsibilities'], ENT_QUOTES) . '<br>' . 
                        htmlspecialchars($entry['achievements'], ENT_QUOTES) . '
                    </li>';
                    break;
    
                case 'education':
                    $entry_html = '
                    <li class="resume-placeholder">
                        ' . htmlspecialchars($entry['degreeName'], ENT_QUOTES) . ' in ' . 
                        htmlspecialchars($entry['fieldOfStudy'], ENT_QUOTES) . ' from ' . 
                        htmlspecialchars($entry['institutionName'], ENT_QUOTES) . ' (' . 
                        htmlspecialchars($entry['startYear'], ENT_QUOTES) . ' - ' . 
                        htmlspecialchars($entry['endYear'], ENT_QUOTES) . ')' . 
                        (!empty($entry['honors']) ? ', ' . htmlspecialchars($entry['honors'], ENT_QUOTES) : '') . '
                    </li>';
                    break;
    
                case 'certifications':
                    $entry_html = '
                    <li class="resume-placeholder">
                        ' . htmlspecialchars($entry['certificationName'], ENT_QUOTES) . ' by ' . 
                        htmlspecialchars($entry['issuingAuthority'], ENT_QUOTES) . ' (' . 
                        htmlspecialchars($entry['dateIssued'], ENT_QUOTES) . 
                        (!empty($entry['expiryDate']) ? ' - ' . htmlspecialchars($entry['expiryDate'], ENT_QUOTES) : '') . ')
                    </li>';
                    break;
    
                case 'projects':
                    $entry_html = '
                    <li class="resume-placeholder">
                        <strong>' . htmlspecialchars($entry['projectName'], ENT_QUOTES) . '</strong><br>' . 
                        htmlspecialchars($entry['description'], ENT_QUOTES) . '<br>' . 
                        'Role: ' . htmlspecialchars($entry['role'], ENT_QUOTES) . '<br>' . 
                        'Technologies Used: ' . htmlspecialchars($entry['technologiesUsed'], ENT_QUOTES) . '<br>' . 
                        'Outcome: ' . htmlspecialchars($entry['outcome'], ENT_QUOTES) . '<br>' . 
                        (!empty($entry['projectUrl']) ? '<a href="' . htmlspecialchars($entry['projectUrl'], ENT_QUOTES) . '">View Project</a>' : '') . '
                    </li>';
                    break;
    
                case 'awards':
                    $entry_html = '
                    <li class="resume-placeholder">
                        ' . htmlspecialchars($entry['awardName'], ENT_QUOTES) . ' by ' . 
                        htmlspecialchars($entry['awardingOrganization'], ENT_QUOTES) . ' (' . 
                        htmlspecialchars($entry['dateAwarded'], ENT_QUOTES) . ')
                    </li>';
                    break;
    
                case 'internships':
                    $entry_html = '
                    <li class="resume-placeholder">
                        <strong>' . htmlspecialchars($entry['jobTitle'], ENT_QUOTES) . '</strong> at ' . 
                        htmlspecialchars($entry['employerName'], ENT_QUOTES) . ' (' . 
                        htmlspecialchars($entry['startDate'], ENT_QUOTES) . ' - ' . 
                        htmlspecialchars($entry['endDate'], ENT_QUOTES) . ')<br>' . 
                        htmlspecialchars($entry['jobDescription'], ENT_QUOTES) . '
                    </li>';
                    break;
    
                case 'languages':
                    $entry_html = '
                    <li class="resume-placeholder">
                        ' . htmlspecialchars($entry['languageName'], ENT_QUOTES) . ' - ' . 
                        htmlspecialchars($entry['proficiencyLevel'], ENT_QUOTES) . '
                    </li>';
                    break;
    
                case 'skills':
                     // Handle skills as a special case
                    if ($key === 'skills') {
                        if (!empty($entries['technicalSkills'])) {
                            $output .= '<strong>' . htmlspecialchars($entries['labelTechnicalSkills'] ?? 'Technical Skills', ENT_QUOTES) . '</strong>';
                            $output .= '<ul>';
                            foreach ($entries['technicalSkills'] as $skill) {
                                $output .= '<li class="resume-placeholder">' . htmlspecialchars($skill['skillName'], ENT_QUOTES) . ' - ' . htmlspecialchars($skill['skillLevel'], ENT_QUOTES) . '</li>';
                            }
                            $output .= '</ul>';
                        }
                        if (!empty($entries['softSkills'])) {
                          
                            $output .= '<strong>' . htmlspecialchars($entries['labelSoftSkills'] ?? 'Soft Skills', ENT_QUOTES) . '</strong>';
                            $output .= '<ul>';
                            foreach ($entries['softSkills'] as $skill) {
                                $output .= '<li class="resume-placeholder">' . htmlspecialchars($skill['skillName'], ENT_QUOTES) . ' - ' . htmlspecialchars($skill['skillLevel'], ENT_QUOTES) . '</li>';
                            }
                            $output .= '</ul>';
                        }
                        return str_replace($placeholder, $output, $layout_html);
                    }
                    break;
    
                case 'hobbies':
                    $entry_html = '
                    <li class="resume-placeholder">
                        ' . htmlspecialchars($entry['hobbyName'], ENT_QUOTES) . ': ' . 
                        htmlspecialchars($entry['description'], ENT_QUOTES) . '
                    </li>';
                    break;
    
                case 'references':
                    $entry_html = '
                    <li class="resume-placeholder">
                        ' . htmlspecialchars($entry['referenceName'], ENT_QUOTES) . ' (' . 
                        htmlspecialchars($entry['relationship'], ENT_QUOTES) . ')<br>' . 
                        'Email: ' . htmlspecialchars($entry['email'], ENT_QUOTES) . '<br>' . 
                        'Phone: ' . htmlspecialchars($entry['phone'], ENT_QUOTES) . '
                    </li>';
                    break;
    
                case 'courses':
                    $entry_html = '
                    <li class="resume-placeholder">
                        ' . htmlspecialchars($entry['courseName'], ENT_QUOTES) . ' by ' . 
                        htmlspecialchars($entry['instituteName'], ENT_QUOTES) . ' (' . 
                        htmlspecialchars($entry['dateCompleted'], ENT_QUOTES) . ')
                    </li>';
                    break;
    
                case 'extraCurricularActivities':
                    $entry_html = '
                    <li class="resume-placeholder">
                        <strong>' . htmlspecialchars($entry['activityName'], ENT_QUOTES) . '</strong> - ' . 
                        htmlspecialchars($entry['position'], ENT_QUOTES) . '<br>' . 
                        htmlspecialchars($entry['description'], ENT_QUOTES) . '
                    </li>';
                    break;
                    
                case 'customSections':
                    $entry_html = '
                    <li class="resume-placeholder">
                        <strong>' . htmlspecialchars($entry['sectionTitle'], ENT_QUOTES) . '</strong>
                        <ul>
                            <li class="resume-placeholder">' . 
                                htmlspecialchars($entry['title'], ENT_QUOTES) . ' - ' . 
                                htmlspecialchars($entry['description'], ENT_QUOTES) . 
                                (!empty($entry['date']) ? ' (' . htmlspecialchars($entry['date'], ENT_QUOTES) . ')' : '') . '
                            </li>
                        </ul>
                    </li>';
                    break;

                default:
                    $entry_html = '<li>';
                    foreach ($entry as $sub_value) {
                        $entry_html .= htmlspecialchars($sub_value, ENT_QUOTES) . ' ';
                    }
                    $entry_html .= '</li>';
                    break;
            }
            $output .= $entry_html;
        }
    
        // Replace the main placeholder with generated HTML
        return str_replace($placeholder, $output, $layout_html);
    }

    private function applyDynamicCss($html_content, $css_file) {
        if ($css_file) {
            // Append a unique query string to bust the cache
            $css_file .= '?v=' . time();
            $css_link = "<link id='dynamic-css' rel='stylesheet' type='text/css' href='{$css_file}'>";
            return $css_link . $html_content;
        }
        return $html_content;
    }
    
    public function update_resume_template() {
        $template_id = $this->input->post('template_id');
        //$user_id = $this->user_id; // Assuming session-based user ID retrieval
    
        if (empty($template_id)) {
            echo json_encode(['success' => false, 'message' => 'Template ID is required']);
            return;
        }
    
        // Fetch existing template data
        $template_data = $this->db->select('placeholders')
                                  ->from('tb_user_resume_templates')
                                  //->where('user_id', $user_id)
                                  ->where('template_id', $template_id)
                                  ->get()
                                  ->row_array();
    
        if (!$template_data) {
            echo json_encode(['success' => false, 'message' => 'Template not found']);
            return;
        }
    
        // Decode the existing JSON data
        $existing_placeholders = json_decode($template_data['placeholders'], true);
        if (!is_array($existing_placeholders)) {
            echo json_encode(['success' => false, 'message' => 'Invalid placeholder data']);
            return;
        }
    
        // Photo upload call
        $uploadResult = $this->upload_photo(); // Call the photo upload function
        $photoUrl = '';
    
        if (!empty($uploadResult['status']) && $uploadResult['status'] === 'success') {
            $photoUrl = $uploadResult['photoUrl']; // Get the uploaded photo URL
            $existing_placeholders['photoUrl'] = $photoUrl; // Update JSON placeholders
        }
    
        // Collect form data and update placeholders
        $form_data = $this->input->post();
    
        foreach ($form_data as $key => $value) {
            if (isset($existing_placeholders[$key]) && !is_array($existing_placeholders[$key])) {
                // Update only single-value keys in the placeholders
                $existing_placeholders[$key] = $value;
            }
        }
    
        // Encode updated placeholders
        $updated_placeholders = json_encode($existing_placeholders);
    
        // Prepare data for database update
        $update_data = [
            'placeholders' => $updated_placeholders,
            'updated_at' => date('Y-m-d H:i:s'),
        ];
    
        // Update the database
        $update_result = $this->db->where('template_id', $template_id)
                                  ->update('tb_user_resume_templates', $update_data);
    
        if ($update_result) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update resume template']);
        }
    }
    
    private function upload_photo() {
        $config['upload_path']   = './uploads/resume_photos/';
        $config['allowed_types'] = 'jpg|jpeg|png';
        $config['max_size']      = 2048;
        $config['encrypt_name']  = TRUE; // To avoid conflicts with file names
    
        $this->load->library('upload', $config);
    
        if (!$this->upload->do_upload('photo')) {
            return [
                'status' => 'error',
                'message' => $this->upload->display_errors('', ''),
            ];
        } else {
            // Fetch user details
            $user_id = $this->user_id; // Assuming session-based user ID retrieval
            $template_id = $this->input->post('template_id');
            
            // Get the current photo URL from the database
            $existingPhoto = $this->db->select('placeholders')
                                      ->from('tb_user_resume_templates')
                                      ->where('user_id', $user_id)
                                      ->where('template_id', $template_id)
                                      ->get()
                                      ->row_array();
    
            if ($existingPhoto) {
                $placeholders = json_decode($existingPhoto['placeholders'], true);
                if (!empty($placeholders['photoUrl'])) {
                    $previousPhotoPath = str_replace(base_url(), './', $placeholders['photoUrl']); // Convert to server path
                    if (file_exists($previousPhotoPath)) {
                        unlink($previousPhotoPath); // Delete the existing photo
                    }
                }
            }
    
            // Upload the new file
            $uploadData = $this->upload->data();
            return [
                'status' => 'success',
                'photoUrl' => base_url('uploads/resume_photos/' . $uploadData['file_name']),
            ];
        }
    }

    /*public function fetch_skills() {
        // Assuming the job title is passed or available in $this->designation
        $job_title = $this->input->post('job_title') ?? $this->designation; 
    
        // Fetch skills based on job title from the database
        $skills = $this->ResumeTemplateModel->get_skills_by_job_title($job_title);
        
        // Decode the skills JSON from the database
        $skillsData = json_decode($skills['skills'], true); // Assuming 'skills' is the column holding the JSON data
            
        // Extract the skills name list from the decoded JSON
        $skillsList = $skillsData['skills_name'] ?? []; // Default to empty array if no skills found
        
        echo json_encode([
            'success' => true,
            'skillsList' => $skillsList
        ]);
    }*/

    public function fetch_selected_skill_html() { 
        $user_id = $this->user_id; // Get user ID from session or authentication
        $template_id = $this->input->post('template_id'); // Template ID from request
    
        // Fetch skills data based on the user and template
        $templateData = $this->ResumeTemplateModel->get_template_data($user_id, $template_id);
    
        if ($templateData) {
            $placeholders = json_decode($templateData['placeholders'], true);
            $skills = $placeholders['skills'] ?? [];
            
            $technicalSkillsHtml = $this->_skill_structure($skills['technicalSkills'] ?? [], 'Technical Skills');
            $softSkillsHtml = $this->_skill_structure($skills['softSkills'] ?? [], 'Soft Skills');
    
            echo json_encode([
                'success' => true,
                'technicalSkillsHtml' => $technicalSkillsHtml,
                'softSkillsHtml' => $softSkillsHtml
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'No skills found for the provided template ID.'
            ]);
        }
    }
    
    private function _skill_structure($skills, $label) {
        // Return an empty string if skills are empty
        if (empty($skills)) {
            return '';
        }
    
        $html = "<h2 class='text-lg font-semibold mb-2'>{$label}</h2>
                 <ul class='space-y-1'>";
    
        foreach ($skills as $skill) {
            // Check if 'id' exists, otherwise assign a fallback value
            $skillName = htmlspecialchars($skill['skillName']);
            $skillId = $skill['id'] ?? md5($skillName); // Fallback to md5 of skill name
            $skillLevel = htmlspecialchars($skill['skillLevel'] ?? $skill['proficiencyLevel']);
            
            
            $html .= "<li class='flex flex-col items-start bg-gray-100 p-2 rounded' data-id='{$skillId}'>
                        <div class='flex items-center justify-between w-full'>
                            <span class='editable-skill'>{$skillName} - {$skillLevel}</span>
                            <button class='delete-skill-btn text-red-500' data-id='{$skillId}'>Delete</button>
                        </div>
                        
                        <div class='edit-skill-form hidden mt-2 w-full'>
                            <input type='text' class='edit-skill-name border p-1 rounded w-1/3' value='{$skillName}' placeholder='Skill Name'>
                            <select class='edit-skill-level border p-1 rounded w-1/3'>
                                <option value='Beginner'" . ($skillLevel == 'Beginner' ? ' selected' : '') . ">Beginner</option>
                                <option value='Intermediate'" . ($skillLevel == 'Intermediate' ? ' selected' : '') . ">Intermediate</option>
                                <option value='Advanced'" . ($skillLevel == 'Advanced' ? ' selected' : '') . ">Advanced</option>
                            </select>
                           <button class='modified-skill-btn bg-blue-500 text-white px-3 py-1 rounded' data-skilltype='{$label}' data-id='{$skillId}'>Save</button>

                        </div>
                    </li>";
        }
    
        $html .= "</ul>";
        return $html;
    }

    public function add_update_skills() {
        $template_id = $this->input->post('template_id');
        $user_id = $this->user_id; // Assuming session-based user ID retrieval
        $skill_data = json_decode($this->input->post('skills'), true);
    
        // Validate input
        if (empty($template_id) || empty($skill_data)) {
            echo json_encode(['success' => false, 'message' => 'Template ID and skill data are required']);
            return;
        }
    
        if (!isset($skill_data['technicalSkills']) || !is_array($skill_data['technicalSkills']) ||
            !isset($skill_data['softSkills']) || !is_array($skill_data['softSkills'])) {
            echo json_encode(['success' => false, 'message' => 'Invalid skill data']);
            return;
        }
    
        // Fetch existing data
        $template_data = $this->db->select('placeholders')
                                  ->from('tb_user_resume_templates')
                                  ->where('user_id', $user_id)
                                  ->where('template_id', $template_id)
                                  ->get()
                                  ->row_array();
    
        $existing_placeholders = !empty($template_data['placeholders'])
            ? json_decode($template_data['placeholders'], true)
            : [];
    
        // Initialize skills if not already set
        if (!isset($existing_placeholders['skills']['technicalSkills'])) {
            $existing_placeholders['skills']['technicalSkills'] = [];
        }
        if (!isset($existing_placeholders['skills']['softSkills'])) {
            $existing_placeholders['skills']['softSkills'] = [];
        }
    
        // Update technical skills
        $existing_technical_skills = &$existing_placeholders['skills']['technicalSkills']; // Reference
        $this->update_skills($skill_data['technicalSkills'], $existing_technical_skills);
    
        // Update soft skills
        $existing_soft_skills = &$existing_placeholders['skills']['softSkills']; // Reference
        $this->update_skills($skill_data['softSkills'], $existing_soft_skills);
    
        // Save updated placeholders
        $updated_placeholders = json_encode($existing_placeholders);
    
        $update_data = [
            'placeholders' => $updated_placeholders,
            'updated_at' => date('Y-m-d H:i:s'),
        ];
    
        // Update or insert into database
        $this->db->where('user_id', $user_id)
                 ->where('template_id', $template_id);
    
        if ($this->db->update('tb_user_resume_templates', $update_data)) {
            echo json_encode(['success' => true, 'message' => 'Skills updated successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update skills']);
        }
    }
    
    private function update_skills($new_skills, &$existing_skills) {
        foreach ($new_skills as $new_skill) {
            $is_updated = false;
    
            if (!empty($new_skill['id'])) {
                // Update existing skill based on ID
                foreach ($existing_skills as &$existing_skill) {
                    if ($existing_skill['id'] === $new_skill['id']) {
                        $existing_skill = $new_skill;
                        $is_updated = true;
                        break;
                    }
                }
            }
    
            if (!$is_updated) {
                // Insert new skill
                $new_skill['id'] = uniqid('skill_'); // Generate unique ID if not present
                $existing_skills[] = $new_skill;
            }
        }
    }

    public function delete_skill() {
        $user_id = $this->user_id; // Get user ID from session or context
        $skill_id = $this->input->post('skill_id'); // Get skill ID from POST data
    
        // Validate input
        if (empty($skill_id)) {
            echo json_encode(['success' => false, 'message' => 'Skill ID is required']);
            return;
        }
    
        // Fetch existing data
        $template_id = $this->input->post('template_id'); // Get template ID if needed
        $template_data = $this->db->select('placeholders')
                                  ->from('tb_user_resume_templates')
                                  ->where('user_id', $user_id)
                                  ->where('template_id', $template_id)
                                  ->get()
                                  ->row_array();
        
        $existing_placeholders = !empty($template_data['placeholders']) 
            ? json_decode($template_data['placeholders'], true) 
            : [];
    
        if (isset($existing_placeholders['skills']['technicalSkills'])) {
            $existing_skills = &$existing_placeholders['skills']['technicalSkills'];
    
            // Find and remove the skill by ID
            foreach ($existing_skills as $key => $skill) {
                if ($skill['id'] === $skill_id) {
                    unset($existing_skills[$key]); // Remove skill from array
                    break;
                }
            }
    
            // Re-index the array to avoid gaps after unset
            $existing_skills = array_values($existing_skills);
    
            // Update the placeholders with the new skills data
            $updated_placeholders = json_encode($existing_placeholders);
    
            $update_data = [
                'placeholders' => $updated_placeholders,
                'updated_at' => date('Y-m-d H:i:s'),
            ];
    
            // Update the database
            $this->db->where('user_id', $user_id)
                     ->where('template_id', $template_id);
    
            if ($this->db->update('tb_user_resume_templates', $update_data)) {
                echo json_encode(['success' => true, 'message' => 'Skill deleted successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to delete skill']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'No skills found to delete']);
        }
    }
    
    public function fetch_employment_data() {
        $user_id = $this->user_id; // Get user ID from session or authentication
        $template_id = $this->input->post('template_id'); // Template ID from request
    
        // Fetch template data based on the user and template
        $templateData = $this->ResumeTemplateModel->get_template_data($user_id, $template_id);
    
        if ($templateData) {
            $placeholders = json_decode($templateData['placeholders'], true);
            $employments = $placeholders['employmentHistory'] ?? [];
    
            // Render employment structure
            $employmentList = $this->_employment_structure($employments, $placeholders['labelEmploymentHistory'] ?? 'Employment History');
    
            echo json_encode([
                'success' => true,
                'employmentList' => $employmentList
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'No data found for the given user and template.'
            ]);
        }
    }
    
    private function _employment_structure($employmentList, $sectionTitle) {
        // Return an empty string if employment data is empty
        if (empty($employmentList)) {
            return '';
        }
    
        $html = "<h2 class='text-lg font-semibold mb-2'>{$sectionTitle}</h2>
                 <ul id='employment-history-list' class='space-y-2'>";
    
        foreach ($employmentList as $employment) {
            $emId = htmlspecialchars($employment['id'] ?? '');
            $jobTitle = htmlspecialchars($employment['jobTitle'] ?? '');
            $employerName = htmlspecialchars($employment['employerName'] ?? '');
            $startDate = htmlspecialchars($employment['startDate'] ?? '');
            $endDate = htmlspecialchars($employment['endDate'] ?? '');
            $jobType = htmlspecialchars($employment['jobType'] ?? '');
            $workLocation = htmlspecialchars($employment['workLocation'] ?? '');
            $responsibilities = htmlspecialchars($employment['responsibilities'] ?? '');
            $achievements = htmlspecialchars($employment['achievements'] ?? '');
            
            // Ensure responsibilities and achievements are arrays before processing
            //$responsibilities = is_array($employment['responsibilities'] ?? []) ? $employment['responsibilities'] : [];
            //$achievements = is_array($employment['achievements'] ?? []) ? $employment['achievements'] : [];
            
            // Implode only if the values are arrays
            //$responsibilitiesText = implode(', ', array_map('htmlspecialchars', $responsibilities));
            //$achievementsText = implode(', ', array_map('htmlspecialchars', $achievements));
    
            $html .= "<li class='bg-gray-100 p-3 rounded' data-id='{$emId}'> <!-- Added data-id to li -->
                    <div class='flex justify-between items-center'>
                        <div class='editable-job'>
                            <strong>{$jobTitle}</strong> at <em>{$employerName}</em> 
                            ({$startDate} to {$endDate}, {$jobType}, {$workLocation})
                        </div>
                        <button class='delete-job-btn text-red-500'>Delete</button>
                    </div>
                    <ul class='mt-2'>
                        <li>{$responsibilities}</li>
                        <li>{$achievements}</li>
                    </ul>
                    <div class='edit-job-form hidden mt-2'>
                        <input type='text' class='edit-job-title border p-1 rounded w-1/3' value='{$jobTitle}' placeholder='Job Title'>
                        <input type='text' class='edit-employer-name border p-1 rounded w-1/3' value='{$employerName}' placeholder='Employer Name'>
                        <input type='date' class='edit-start-date border p-1 rounded' value='{$startDate}'>
                        <input type='date' class='edit-end-date border p-1 rounded' value='{$endDate}'>
                        <select class='edit-job-type border p-1 rounded w-1/3'>
                            <option value='Full-time'" . ($jobType === 'Full-time' ? ' selected' : '') . ">Full-time</option>
                            <option value='Part-time'" . ($jobType === 'Part-time' ? ' selected' : '') . ">Part-time</option>
                            <option value='Contract'" . ($jobType === 'Contract' ? ' selected' : '') . ">Contract</option>
                            <option value='Freelance'" . ($jobType === 'Freelance' ? ' selected' : '') . ">Freelance</option>
                        </select>
                        <input type='text' class='edit-work-location border p-1 rounded w-1/3' value='{$workLocation}' placeholder='Work Location'>
                        <textarea class='edit-responsibilities border p-1 rounded w-full' placeholder='Responsibilities'>{$responsibilities}</textarea>
                        <textarea class='edit-achievements border p-1 rounded w-full' placeholder='Achievements'>{$achievements}</textarea>
                        <button class='update-job-btn bg-blue-500 text-white px-3 py-1 rounded mt-2' data-id='{$emId}'>Save</button>
                    </div>
                </li>";

        }
    
        $html .= "</ul>";
        return $html;
    }

    public function save_employments () { 
        $template_id = $this->input->post('template_id');
        $user_id = $this->user_id; // Assuming session-based user ID retrieval
        $employment_data = json_decode($this->input->post('placeholders'), true);
    
        // Validate input
        if (empty($template_id) || empty($employment_data)) {
            echo json_encode(['success' => false, 'message' => 'Template ID and employment data are required']);
            return;
        }
    
        if (!isset($employment_data['employmentHistory']) || !is_array($employment_data['employmentHistory'])) {
            echo json_encode(['success' => false, 'message' => 'Invalid employment data']);
            return;
        }
    
        // Fetch existing data
        $template_data = $this->db->select('placeholders')
                                  ->from('tb_user_resume_templates')
                                  ->where('user_id', $user_id)
                                  ->where('template_id', $template_id)
                                  ->get()
                                  ->row_array();
    
        $existing_placeholders = !empty($template_data['placeholders'])
            ? json_decode($template_data['placeholders'], true)
            : [];
    
        // Ensure employmentHistory exists
        if (!isset($existing_placeholders['employmentHistory'])) {
            $existing_placeholders['employmentHistory'] = [];
        }
    
        $existing_employment = $existing_placeholders['employmentHistory'];
    
        // Process new employment data
        foreach ($employment_data['employmentHistory'] as $new_entry) {
            $is_updated = false;
    
            if (!empty($new_entry['id'])) {
                // Update existing entry based on ID
                foreach ($existing_employment as &$existing_entry) {
                    if ($existing_entry['id'] === $new_entry['id']) {
                        $existing_entry = $new_entry;
                        $is_updated = true;
                        break;
                    }
                }
            }
    
            if (!$is_updated) {
                // Assign a unique ID for new entries
                $new_entry['id'] = uniqid('emp_');
                $existing_employment[] = $new_entry;
            }
        }
    
        $existing_placeholders['employmentHistory'] = $existing_employment;
        $updated_placeholders = json_encode($existing_placeholders);
    
        $update_data = [
            'placeholders' => $updated_placeholders,
            'updated_at' => date('Y-m-d H:i:s'),
        ];
    
        // Update the placeholders if the template exists or insert a new one if it doesn't
        $this->db->where('user_id', $user_id)
                 ->where('template_id', $template_id);
    
        if ($this->db->update('tb_user_resume_templates', $update_data)) {
            echo json_encode(['success' => true, 'message' => 'Employment data updated successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update employment data']);
        }
    }

    public function delete_employment() {
        $user_id = $this->user_id; // Retrieve user ID from session or other source
        $input = json_decode(file_get_contents('php://input'), true); // Get the raw POST data and decode it
    
        if (empty($input) || !isset($input['id'])) {
            echo json_encode(['success' => false, 'message' => 'Invalid data']);
            return;
        }
    
        // Fetch existing resume template data for the user
        $existing_data = $this->db->select('placeholders')
                                  ->from('tb_user_resume_templates')
                                  ->where('user_id', $user_id)
                                  ->get()
                                  ->row_array();
    
        $placeholders = !empty($existing_data['placeholders']) ? json_decode($existing_data['placeholders'], true) : [];
    
        if (!isset($placeholders['employmentHistory'])) {
            echo json_encode(['success' => false, 'message' => 'No employment history found']);
            return;
        }
    
        // Remove the employment history entry based on ID
        foreach ($placeholders['employmentHistory'] as $key => $employment) {
            if ($employment['id'] === $input['id']) {
                unset($placeholders['employmentHistory'][$key]);
                break;
            }
        }
    
        // Reindex the array and update the placeholders
        $placeholders['employmentHistory'] = array_values($placeholders['employmentHistory']);
        $updated_placeholders = json_encode($placeholders);
    
        // Update the database with the new data
        $update_data = [
            'placeholders' => $updated_placeholders,
            'updated_at' => date('Y-m-d H:i:s'),
        ];
    
        $this->db->where('user_id', $user_id);
        
        if ($this->db->update('tb_user_resume_templates', $update_data)) {
            echo json_encode(['success' => true, 'message' => 'Employment record deleted successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to delete employment record']);
        }
    }
    
    public function fetch_languages_data() { 
        $user_id = $this->user_id; // Assuming session-based user ID retrieval
        $template_id = $this->input->post('template_id');
        
        // Fetch the template data
        $templateData = $this->ResumeTemplateModel->get_template_data($user_id, $template_id);
       
        if ($templateData) {
            // Decode the placeholders JSON
            $placeholders = json_decode($templateData['placeholders'], true);
            if ($placeholders && isset($placeholders['languages'])) {
                $languages = $placeholders['languages']; // Extract the languages data
                
                // Render language structure
                $languageList = $this->_languages_structure($languages, $placeholders['labelLanguages'] ?? 'Languages');
            
                echo json_encode([
                    'success' => true,
                    'languagesList' => $languageList
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'No language data found in the template.'
                ]);
            }
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'No data found for the given user and template.'
            ]);
        }
    }
    
    private function _languages_structure($languageList, $sectionTitle) {
        // Return an empty string if language data is empty
        if (empty($languageList)) {
            return '';
        }
    
        $html = "<h2 class='text-lg font-semibold mb-2'>{$sectionTitle}</h2>
                 <ul id='language-list' class='space-y-2'>";
    
        foreach ($languageList as $language) {
            $langId = htmlspecialchars($language['id'] ?? '');
            $languageName = htmlspecialchars($language['languageName'] ?? '');
            $proficiencyLevel = htmlspecialchars($language['proficiencyLevel'] ?? '');
            
            $html .= "<li class='bg-gray-100 p-3 rounded' data-id='{$langId}'>
                        <div class='flex justify-between items-center'>
                            <div class='editable-language'>
                                <strong>{$languageName}</strong> - <em>{$proficiencyLevel}</em>
                            </div>
                            <button class='delete-language-btn text-red-500'>Delete</button>
                        </div>
                        <div class='edit-language-form hidden mt-2'>
                            <input type='text' class='edit-language-name border p-1 rounded w-1/3' value='{$languageName}' placeholder='Language Name'>
                            <select class='edit-proficiency-level border p-1 rounded w-1/3'>
                                <option value='Beginner'" . ($proficiencyLevel === 'Beginner' ? ' selected' : '') . ">Beginner</option>
                                <option value='Intermediate'" . ($proficiencyLevel === 'Intermediate' ? ' selected' : '') . ">Intermediate</option>
                                <option value='Advanced'" . ($proficiencyLevel === 'Advanced' ? ' selected' : '') . ">Advanced</option>
                                <option value='Fluent'" . ($proficiencyLevel === 'Fluent' ? ' selected' : '') . ">Fluent</option>
                            </select>
                            <button class='update-language-btn bg-blue-500 text-white px-3 py-1 rounded mt-2' data-id='{$langId}'>Save</button>
                        </div>
                    </li>";
        }
    
        $html .= "</ul>";
        return $html;
    }
    
    public function save_languages() { 
        $template_id = $this->input->post('template_id');
        $user_id = $this->user_id; // Assuming session-based user ID retrieval
        $employment_data = json_decode($this->input->post('placeholders'), true);
    
        // Validate input
        if (empty($template_id) || empty($employment_data)) {
            echo json_encode(['success' => false, 'message' => 'Template ID and employment data are required']);
            return;
        }
    
        if (!isset($employment_data['languages']) || !is_array($employment_data['languages'])) {
            echo json_encode(['success' => false, 'message' => 'Invalid employment data']);
            return;
        }
    
        // Fetch existing data
        $template_data = $this->db->select('placeholders')
                                  ->from('tb_user_resume_templates')
                                  ->where('user_id', $user_id)
                                  ->where('template_id', $template_id)
                                  ->get()
                                  ->row_array();
    
        $existing_placeholders = !empty($template_data['placeholders'])
            ? json_decode($template_data['placeholders'], true)
            : [];
    
        // Ensure employmentHistory exists
        if (!isset($existing_placeholders['languages'])) {
            $existing_placeholders['languages'] = [];
        }
    
        $existing_employment = $existing_placeholders['languages'];
    
        // Process new employment data
        foreach ($employment_data['languages'] as $new_entry) {
            $is_updated = false;
    
            if (!empty($new_entry['id'])) {
                // Update existing entry based on ID
                foreach ($existing_employment as &$existing_entry) {
                    if ($existing_entry['id'] === $new_entry['id']) {
                        $existing_entry = $new_entry;
                        $is_updated = true;
                        break;
                    }
                }
            }
    
            if (!$is_updated) {
                // Assign a unique ID for new entries
                $new_entry['id'] = uniqid('emp_');
                $existing_employment[] = $new_entry;
            }
        }
    
        $existing_placeholders['languages'] = $existing_employment;
        $updated_placeholders = json_encode($existing_placeholders);
    
        $update_data = [
            'placeholders' => $updated_placeholders,
            'updated_at' => date('Y-m-d H:i:s'),
        ];
    
        // Update the placeholders if the template exists or insert a new one if it doesn't
        $this->db->where('user_id', $user_id)
                 ->where('template_id', $template_id);
    
        if ($this->db->update('tb_user_resume_templates', $update_data)) {
            echo json_encode(['success' => true, 'message' => 'Employment data updated successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update employment data']);
        }
    }

    public function delete_language() {
        $user_id = $this->user_id; // Retrieve user ID from session or other source
        $input = json_decode(file_get_contents('php://input'), true); // Get the raw POST data and decode it
    
        if (empty($input) || !isset($input['id'])) {
            echo json_encode(['success' => false, 'message' => 'Invalid language data']);
            return;
        }
    
        // Fetch existing resume template data for the user
        $existing_data = $this->db->select('placeholders')
                                  ->from('tb_user_resume_templates')
                                  ->where('user_id', $user_id)
                                  ->get()
                                  ->row_array();
    
        $placeholders = !empty($existing_data['placeholders']) ? json_decode($existing_data['placeholders'], true) : [];
    
        if (!isset($placeholders['languages'])) {
            echo json_encode(['success' => false, 'message' => 'No languages found']);
            return;
        }
    
        // Remove the language entry based on ID
        foreach ($placeholders['languages'] as $key => $language) {
            if ($language['id'] === $input['id']) {
                unset($placeholders['languages'][$key]);
                break;
            }
        }
    
        // Reindex the array and update the placeholders
        $placeholders['languages'] = array_values($placeholders['languages']);
        $updated_placeholders = json_encode($placeholders);
    
        // Update the database with the new data
        $update_data = [
            'placeholders' => $updated_placeholders,
            'updated_at' => date('Y-m-d H:i:s'),
        ];
    
        $this->db->where('user_id', $user_id);
    
        if ($this->db->update('tb_user_resume_templates', $update_data)) {
            echo json_encode(['success' => true, 'message' => 'Language record deleted successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to delete language record']);
        }
    }

    public function fetch_education_data() { 
        $user_id = $this->user_id; // Assuming session-based user ID retrieval
        $template_id = $this->input->post('template_id');
        
        // Fetch the template data
        $templateData = $this->ResumeTemplateModel->get_template_data($user_id, $template_id);
       
        if ($templateData) {
            // Decode the placeholders JSON
            $placeholders = json_decode($templateData['placeholders'], true);
            if ($placeholders && isset($placeholders['education'])) {
                $education = $placeholders['education']; // Extract the education data
                
                // Render education structure
                $educationList = $this->_education_structure($education, $placeholders['labelEducation'] ?? 'Education');
            
                echo json_encode([
                    'success' => true,
                    'educationList' => $educationList
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'No education data found in the template.'
                ]);
            }
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'No data found for the given user and template.'
            ]);
        }
    }
    
    private function _education_structure($educationList, $sectionTitle) {
        // Return an empty string if education data is empty
        if (empty($educationList)) {
            return '';
        }
    
        $html = "<h2 class='text-lg font-semibold mb-2'>{$sectionTitle}</h2>
                 <ul id='education-list' class='space-y-2'>";
    
        foreach ($educationList as $education) {
            $educationId = htmlspecialchars($education['id'] ?? '');
            $degreeName = htmlspecialchars($education['degreeName'] ?? '');
            $institutionName = htmlspecialchars($education['institutionName'] ?? '');
            $startYear = htmlspecialchars($education['startYear'] ?? '');
            $endYear = htmlspecialchars($education['endYear'] ?? '');
            $fieldOfStudy = htmlspecialchars($education['fieldOfStudy'] ?? '');
            $honors = htmlspecialchars($education['honors'] ?? '');
    
            $html .= "<li class='bg-gray-100 p-3 rounded' data-id='{$educationId}'>
                        <div class='flex justify-between items-center'>
                            <div class='editable-education'>
                                <strong>{$degreeName}</strong> at <em>{$institutionName}</em> 
                                ({$startYear} - {$endYear})
                                <br>
                                <span>{$fieldOfStudy}</span><br>
                                <span>{$honors}</span>
                            </div>
                            <button class='delete-education-btn text-red-500'>Delete</button>
                        </div>
                        <div class='edit-education-form hidden mt-2'>
                            <input type='text' class='edit-degree-name border p-1 rounded w-full' value='{$degreeName}' placeholder='Degree Name'>
                            <input type='text' class='edit-institution-name border p-1 rounded w-full' value='{$institutionName}' placeholder='Institution Name'>
                            <input type='text' class='edit-start-year border p-1 rounded w-1/3' value='{$startYear}' placeholder='Start Year'>
                            <input type='text' class='edit-end-year border p-1 rounded w-1/3' value='{$endYear}' placeholder='End Year'>
                            <input type='text' class='edit-field-of-study border p-1 rounded w-full' value='{$fieldOfStudy}' placeholder='Field of Study'>
                            <input type='text' class='edit-honors border p-1 rounded w-full' value='{$honors}' placeholder='Honors'>
                            <button class='update-education-btn bg-blue-500 text-white px-3 py-1 rounded mt-2' data-id='{$educationId}'>Save</button>
                        </div>
                    </li>";
        }
    
        $html .= "</ul>";
        return $html;
    }

    public function save_education() {
        $template_id = $this->input->post('template_id');
        $user_id = $this->user_id; // Assuming session-based user ID retrieval
        $education_data = json_decode($this->input->post('placeholders'), true);
    
        // Validate input
        if (empty($template_id) || empty($education_data)) {
            echo json_encode(['success' => false, 'message' => 'Template ID and education data are required']);
            return;
        }
    
        if (!isset($education_data['education']) || !is_array($education_data['education'])) {
            echo json_encode(['success' => false, 'message' => 'Invalid education data']);
            return;
        }
    
        // Fetch existing data
        $template_data = $this->db->select('placeholders')
                                  ->from('tb_user_resume_templates')
                                  ->where('user_id', $user_id)
                                  ->where('template_id', $template_id)
                                  ->get()
                                  ->row_array();
    
        $existing_placeholders = !empty($template_data['placeholders'])
            ? json_decode($template_data['placeholders'], true)
            : [];
    
        // Ensure education exists
        if (!isset($existing_placeholders['education'])) {
            $existing_placeholders['education'] = [];
        }
    
        $existing_education = $existing_placeholders['education'];
    
        // Process new education data
        foreach ($education_data['education'] as $new_entry) {
            $is_updated = false;
    
            if (!empty($new_entry['id'])) {
                // Update existing entry based on ID
                foreach ($existing_education as &$existing_entry) {
                    if ($existing_entry['id'] === $new_entry['id']) {
                        $existing_entry = $new_entry;
                        $is_updated = true;
                        break;
                    }
                }
            }
    
            if (!$is_updated) {
                // Assign a unique ID for new entries
                $new_entry['id'] = uniqid('edu_');
                $existing_education[] = $new_entry;
            }
        }
    
        $existing_placeholders['education'] = $existing_education;
        $updated_placeholders = json_encode($existing_placeholders);
    
        $update_data = [
            'placeholders' => $updated_placeholders,
            'updated_at' => date('Y-m-d H:i:s'),
        ];
    
        // Update the placeholders if the template exists or insert a new one if it doesn't
        $this->db->where('user_id', $user_id)
                 ->where('template_id', $template_id);
    
        if ($this->db->update('tb_user_resume_templates', $update_data)) {
            echo json_encode(['success' => true, 'message' => 'Education data updated successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update education data']);
        }
    }

    public function delete_education() {
        $user_id = $this->user_id; // Retrieve user ID from session or other source
        $input = json_decode(file_get_contents('php://input'), true); // Get the raw POST data and decode it
    
        if (empty($input) || !isset($input['id'])) {
            echo json_encode(['success' => false, 'message' => 'Invalid language data']);
            return;
        }
    
        // Fetch existing resume template data for the user
        $existing_data = $this->db->select('placeholders')
                                  ->from('tb_user_resume_templates')
                                  ->where('user_id', $user_id)
                                  ->get()
                                  ->row_array();
    
        $placeholders = !empty($existing_data['placeholders']) ? json_decode($existing_data['placeholders'], true) : [];
    
        if (!isset($placeholders['education'])) {
            echo json_encode(['success' => false, 'message' => 'No education found']);
            return;
        }
    
        // Remove the language entry based on ID
        foreach ($placeholders['education'] as $key => $language) {
            if ($language['id'] === $input['id']) {
                unset($placeholders['education'][$key]);
                break;
            }
        }
    
        // Reindex the array and update the placeholders
        $placeholders['education'] = array_values($placeholders['education']);
        $updated_placeholders = json_encode($placeholders);
    
        // Update the database with the new data
        $update_data = [
            'placeholders' => $updated_placeholders,
            'updated_at' => date('Y-m-d H:i:s'),
        ];
    
        $this->db->where('user_id', $user_id);
    
        if ($this->db->update('tb_user_resume_templates', $update_data)) {
            echo json_encode(['success' => true, 'message' => 'Education record deleted successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to delete Education record']);
        }
    }
    
    public function fetch_internship_data() { 
        $user_id = $this->user_id; // Assuming session-based user ID retrieval
        $template_id = $this->input->post('template_id');
        
        // Fetch the template data
        $templateData = $this->ResumeTemplateModel->get_template_data($user_id, $template_id);
       
        if ($templateData) {
            // Decode the placeholders JSON
            $placeholders = json_decode($templateData['placeholders'], true);
            if ($placeholders && isset($placeholders['internships'])) {
                $internships = $placeholders['internships']; // Extract the internship data
                
                // Render internship structure
                $internshipList = $this->_internship_structure($internships, $placeholders['labelInternships'] ?? 'Internships');
            
                echo json_encode([
                    'success' => true,
                    'internshipList' => $internshipList
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'No internship data found in the template.'
                ]);
            }
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'No data found for the given user and template.'
            ]);
        }
    }
    
    private function _internship_structure($internshipList, $sectionTitle) {
        // Return an empty string if internship data is empty
        if (empty($internshipList)) {
            return '';
        }
    
        $html = "<h2 class='text-lg font-semibold mb-2'>{$sectionTitle}</h2>
                 <ul id='internship-list' class='space-y-2'>";
    
        foreach ($internshipList as $internship) {
            $internshipId = htmlspecialchars($internship['id'] ?? '');
            $jobTitle = htmlspecialchars($internship['jobTitle'] ?? '');
            $employerName = htmlspecialchars($internship['employerName'] ?? '');
            $startDate = htmlspecialchars($internship['startDate'] ?? '');
            $endDate = htmlspecialchars($internship['endDate'] ?? '');
            $location = htmlspecialchars($internship['location'] ?? '');
            $jobDescription = htmlspecialchars($internship['jobDescription'] ?? '');
    
            $html .= "<li class='bg-gray-100 p-3 rounded' data-id='{$internshipId}'>
                        <div class='flex justify-between items-center'>
                            <div class='editable-internship'>
                                <strong>{$jobTitle}</strong> at <em>{$employerName}</em> 
                                ({$startDate} - {$endDate})
                                <br>
                                <span>{$location}</span><br>
                                <span>{$jobDescription}</span>
                            </div>
                            <button class='delete-internship-btn text-red-500'>Delete</button>
                        </div>
                        <div class='edit-internship-form hidden mt-2'>
                            <input type='text' class='edit-job-title border p-1 rounded w-full' value='{$jobTitle}' placeholder='Job Title'>
                            <input type='text' class='edit-employer-name border p-1 rounded w-full' value='{$employerName}' placeholder='Employer Name'>
                            <input type='text' class='edit-start-date border p-1 rounded w-1/3' value='{$startDate}' placeholder='Start Date'>
                            <input type='text' class='edit-end-date border p-1 rounded w-1/3' value='{$endDate}' placeholder='End Date'>
                            <input type='text' class='edit-location border p-1 rounded w-full' value='{$location}' placeholder='Location'>
                            <textarea class='edit-job-description border p-1 rounded w-full' placeholder='Job Description'>{$jobDescription}</textarea>
                            <button class='update-internship-btn bg-blue-500 text-white px-3 py-1 rounded mt-2' data-id='{$internshipId}'>Save</button>
                        </div>
                    </li>";
        }
    
        $html .= "</ul>";
        return $html;
    }
    
    public function save_internship() {
        $template_id = $this->input->post('template_id');
        $user_id = $this->user_id; // Assuming session-based user ID retrieval
        $internship_data = json_decode($this->input->post('placeholders'), true);
    
        // Validate input
        if (empty($template_id) || empty($internship_data)) {
            echo json_encode(['success' => false, 'message' => 'Template ID and internship data are required']);
            return;
        }
    
        if (!isset($internship_data['internship']) || !is_array($internship_data['internship'])) {
            echo json_encode(['success' => false, 'message' => 'Invalid internship data']);
            return;
        }
    
        // Fetch existing data
        $template_data = $this->db->select('placeholders')
                                  ->from('tb_user_resume_templates')
                                  ->where('user_id', $user_id)
                                  ->where('template_id', $template_id)
                                  ->get()
                                  ->row_array();
    
        $existing_placeholders = !empty($template_data['placeholders'])
            ? json_decode($template_data['placeholders'], true)
            : [];
    
        // Ensure internships exist
        if (!isset($existing_placeholders['internships'])) {
            $existing_placeholders['internships'] = [];
        }
    
        $existing_internships = $existing_placeholders['internships'];
    
        // Process new internship data
        foreach ($internship_data['internship'] as $new_entry) {
            $is_updated = false;
    
            if (!empty($new_entry['id'])) {
                // Update existing entry based on ID
                foreach ($existing_internships as &$existing_entry) {
                    if ($existing_entry['id'] === $new_entry['id']) {
                        $existing_entry = $new_entry;
                        $is_updated = true;
                        break;
                    }
                }
            }
    
            if (!$is_updated) {
                // Assign a unique ID for new entries
                $new_entry['id'] = uniqid('intern_');
                $existing_internships[] = $new_entry;
            }
        }
    
        $existing_placeholders['internships'] = $existing_internships;
        $updated_placeholders = json_encode($existing_placeholders);
    
        $update_data = [
            'placeholders' => $updated_placeholders,
            'updated_at' => date('Y-m-d H:i:s'),
        ];
    
        // Update the placeholders if the template exists or insert a new one if it doesn't
        $this->db->where('user_id', $user_id)
                 ->where('template_id', $template_id);
    
        if ($this->db->update('tb_user_resume_templates', $update_data)) {
            echo json_encode(['success' => true, 'message' => 'Internship data updated successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update internship data']);
        }
    }

    public function delete_internship() {
        $user_id = $this->user_id; // Retrieve user ID from session or other source
        $input = json_decode(file_get_contents('php://input'), true); // Get the raw POST data and decode it
    
        if (empty($input) || !isset($input['id'])) {
            echo json_encode(['success' => false, 'message' => 'Invalid internships data']);
            return;
        }
    
        // Fetch existing resume template data for the user
        $existing_data = $this->db->select('placeholders')
                                  ->from('tb_user_resume_templates')
                                  ->where('user_id', $user_id)
                                  ->get()
                                  ->row_array();
    
        $placeholders = !empty($existing_data['placeholders']) ? json_decode($existing_data['placeholders'], true) : [];
    
        if (!isset($placeholders['internships'])) {
            echo json_encode(['success' => false, 'message' => 'No internships found']);
            return;
        }
    
        // Remove the language entry based on ID
        foreach ($placeholders['internships'] as $key => $language) {
            if ($language['id'] === $input['id']) {
                unset($placeholders['internships'][$key]);
                break;
            }
        }
    
        // Reindex the array and update the placeholders
        $placeholders['internships'] = array_values($placeholders['internships']);
        $updated_placeholders = json_encode($placeholders);
    
        // Update the database with the new data
        $update_data = [
            'placeholders' => $updated_placeholders,
            'updated_at' => date('Y-m-d H:i:s'),
        ];
    
        $this->db->where('user_id', $user_id);
    
        if ($this->db->update('tb_user_resume_templates', $update_data)) {
            echo json_encode(['success' => true, 'message' => 'Internships record deleted successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to delete Internships record']);
        }
    }
    
    public function fetch_certification_data() {
        $user_id = $this->user_id; // Assuming session-based user ID retrieval
        $template_id = $this->input->post('template_id');
        
        // Fetch the template data
        $templateData = $this->ResumeTemplateModel->get_template_data($user_id, $template_id);
       
        if ($templateData) {
            // Decode the placeholders JSON
            $placeholders = json_decode($templateData['placeholders'], true);
            if ($placeholders && isset($placeholders['certifications'])) {
                $certifications = $placeholders['certifications']; // Extract the certifications data
                
                // Render certifications structure
                $certificationList = $this->_certification_structure($certifications, $placeholders['labelCertifications'] ?? 'Certifications');
            
                echo json_encode([
                    'success' => true,
                    'certificationList' => $certificationList
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'No certification data found in the template.'
                ]);
            }
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'No data found for the given user and template.'
            ]);
        }
    }

    private function _certification_structure($certificationList, $sectionTitle) {
        // Return an empty string if certification data is empty
        if (empty($certificationList)) {
            return '';
        }
    
        $html = "<h2 class='text-lg font-semibold mb-2'>{$sectionTitle}</h2>
                 <ul id='certification-list' class='space-y-2'>";
    
        foreach ($certificationList as $certification) {
            $certificationId = htmlspecialchars($certification['id'] ?? '');
            $certificationName = htmlspecialchars($certification['certificationName'] ?? '');
            $issuingAuthority = htmlspecialchars($certification['issuingAuthority'] ?? '');
            $dateIssued = htmlspecialchars($certification['dateIssued'] ?? '');
            $expiryDate = htmlspecialchars($certification['expiryDate'] ?? '');
    
            $html .= "<li class='bg-gray-100 p-3 rounded' data-id='{$certificationId}'>
                        <div class='flex justify-between items-center'>
                            <div class='editable-certification'>
                                <strong>{$certificationName}</strong> by <em>{$issuingAuthority}</em>
                                <br>
                                <span>{$dateIssued}</span><br>
                                <span>{$expiryDate}</span>
                            </div>
                            <button class='delete-certification-btn text-red-500'>Delete</button>
                        </div>
                        <div class='edit-certification-form hidden mt-2'>
                            <input type='text' class='edit-certification-name border p-1 rounded w-full' value='{$certificationName}' placeholder='Certification Name'>
                            <input type='text' class='edit-issuing-authority border p-1 rounded w-full' value='{$issuingAuthority}' placeholder='Issuing Authority'>
                            <input type='text' class='edit-date-issued border p-1 rounded w-1/3' value='{$dateIssued}' placeholder='Date Issued'>
                            <input type='text' class='edit-expiry-date border p-1 rounded w-1/3' value='{$expiryDate}' placeholder='Expiry Date'>
                            <button class='update-certification-btn bg-blue-500 text-white px-3 py-1 rounded mt-2' data-id='{$certificationId}'>Save</button>
                        </div>
                    </li>";
        }
    
        $html .= "</ul>";
        return $html;
    }
    
    public function save_certification() {
        $template_id = $this->input->post('template_id');
        $user_id = $this->user_id; // Assuming session-based user ID retrieval
        $certification_data = json_decode($this->input->post('placeholders'), true);
    
        // Validate input
        if (empty($template_id) || empty($certification_data)) {
            echo json_encode(['success' => false, 'message' => 'Template ID and certification data are required']);
            return;
        }
    
        if (!isset($certification_data['certification']) || !is_array($certification_data['certification'])) {
            echo json_encode(['success' => false, 'message' => 'Invalid certification data']);
            return;
        }
    
        // Fetch existing data
        $template_data = $this->db->select('placeholders')
                                  ->from('tb_user_resume_templates')
                                  ->where('user_id', $user_id)
                                  ->where('template_id', $template_id)
                                  ->get()
                                  ->row_array();
    
        $existing_placeholders = !empty($template_data['placeholders'])
            ? json_decode($template_data['placeholders'], true)
            : [];
    
        // Ensure certifications exist
        if (!isset($existing_placeholders['certifications'])) {
            $existing_placeholders['certifications'] = [];
        }
    
        $existing_certifications = $existing_placeholders['certifications'];
    
        // Process new certification data
        foreach ($certification_data['certification'] as $new_entry) {
            $is_updated = false;
    
            if (!empty($new_entry['id'])) {
                // Update existing entry based on ID
                foreach ($existing_certifications as &$existing_entry) {
                    if ($existing_entry['id'] === $new_entry['id']) {
                        $existing_entry = $new_entry;
                        $is_updated = true;
                        break;
                    }
                }
            }
    
            if (!$is_updated) {
                // Assign a unique ID for new entries
                $new_entry['id'] = uniqid('cert_');
                $existing_certifications[] = $new_entry;
            }
        }
    
        $existing_placeholders['certifications'] = $existing_certifications;
        $updated_placeholders = json_encode($existing_placeholders);
    
        $update_data = [
            'placeholders' => $updated_placeholders,
            'updated_at' => date('Y-m-d H:i:s'),
        ];
    
        // Update the placeholders if the template exists or insert a new one if it doesn't
        $this->db->where('user_id', $user_id)
                 ->where('template_id', $template_id);
    
        if ($this->db->update('tb_user_resume_templates', $update_data)) {
            echo json_encode(['success' => true, 'message' => 'Certification data updated successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update certification data']);
        }
    }

    public function delete_certification() {
        $user_id = $this->user_id; // Retrieve user ID from session or other source
        $input = json_decode(file_get_contents('php://input'), true); // Get the raw POST data and decode it
    
        if (empty($input) || !isset($input['id'])) {
            echo json_encode(['success' => false, 'message' => 'Invalid certifications data']);
            return;
        }
    
        // Fetch existing resume template data for the user
        $existing_data = $this->db->select('placeholders')
                                  ->from('tb_user_resume_templates')
                                  ->where('user_id', $user_id)
                                  ->get()
                                  ->row_array();
    
        $placeholders = !empty($existing_data['placeholders']) ? json_decode($existing_data['placeholders'], true) : [];
    
        if (!isset($placeholders['certifications'])) {
            echo json_encode(['success' => false, 'message' => 'No certifications found']);
            return;
        }
    
        // Remove the language entry based on ID
        foreach ($placeholders['certifications'] as $key => $language) {
            if ($language['id'] === $input['id']) {
                unset($placeholders['certifications'][$key]);
                break;
            }
        }
    
        // Reindex the array and update the placeholders
        $placeholders['certifications'] = array_values($placeholders['certifications']);
        $updated_placeholders = json_encode($placeholders);
    
        // Update the database with the new data
        $update_data = [
            'placeholders' => $updated_placeholders,
            'updated_at' => date('Y-m-d H:i:s'),
        ];
    
        $this->db->where('user_id', $user_id);
    
        if ($this->db->update('tb_user_resume_templates', $update_data)) {
            echo json_encode(['success' => true, 'message' => 'Certifications record deleted successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to delete Certifications record']);
        }
    }
    
    public function fetch_project_data() {
        $user_id = $this->user_id; // Assuming session-based user ID retrieval
        $template_id = $this->input->post('template_id');
    
        // Fetch the template data
        $templateData = $this->ResumeTemplateModel->get_template_data($user_id, $template_id);
    
        if ($templateData) {
            // Decode the placeholders JSON
            $placeholders = json_decode($templateData['placeholders'], true);
            if ($placeholders && isset($placeholders['projects'])) {
                $projects = $placeholders['projects']; // Extract project data
                // Render project structure
                $projectList = $this->_project_structure($projects, $placeholders['labelProjects'] ?? 'Projects');
                echo json_encode([
                    'success' => true,
                    'projectList' => $projectList
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'No project data found in the template.'
                ]);
            }
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'No data found for the given user and template.'
            ]);
        }
    }
    
    private function _project_structure($projectList, $sectionTitle) {
        // Return an empty string if project data is empty
        if (empty($projectList)) {
            return '';
        }
    
        $html = "<h2 class='text-lg font-semibold mb-2'>{$sectionTitle}</h2>
                 <ul id='project-list' class='space-y-2'>";
    
        foreach ($projectList as $project) {
            $projectId = htmlspecialchars($project['id'] ?? '');
            $projectName = htmlspecialchars($project['projectName'] ?? '');
            $description = htmlspecialchars($project['description'] ?? '');
            $role = htmlspecialchars($project['role'] ?? '');
            $technologiesUsed = htmlspecialchars($project['technologiesUsed'] ?? '');
            $outcome = htmlspecialchars($project['outcome'] ?? '');
            $projectUrl = htmlspecialchars($project['projectUrl'] ?? '');
    
            $html .= "<li class='bg-gray-100 p-3 rounded' data-id='{$projectId}'>
                        <div class='flex justify-between items-center'>
                            <div class='editable-project'>
                                <strong>{$projectName}</strong><br>
                                <em>{$role}</em> - {$description}<br>
                                <span>{$technologiesUsed}</span><br>
                                <span>{$outcome}</span><br>
                                <span><a href='{$projectUrl}' target='_blank'>{$projectUrl}</a></span>
                            </div>
                            <button class='delete-project-btn text-red-500'>Delete</button>
                        </div>
                        <div class='edit-project-form hidden mt-2'>
                            <input type='text' class='edit-project-name border p-1 rounded w-full' value='{$projectName}' placeholder='Project Name'>
                            <textarea class='edit-project-description border p-1 rounded w-full' placeholder='Description'>{$description}</textarea>
                            <input type='text' class='edit-project-role border p-1 rounded w-full' value='{$role}' placeholder='Role'>
                            <input type='text' class='edit-technologies-used border p-1 rounded w-full' value='{$technologiesUsed}' placeholder='Technologies Used'>
                            <input type='text' class='edit-project-outcome border p-1 rounded w-full' value='{$outcome}' placeholder='Outcome'>
                            <input type='url' class='edit-project-url border p-1 rounded w-full' value='{$projectUrl}' placeholder='Project URL'>
                            <button class='update-project-btn bg-blue-500 text-white px-3 py-1 rounded mt-2' data-id='{$projectId}'>Save</button>
                        </div>
                    </li>";
        }
    
        $html .= "</ul>";
        return $html;
    }

    public function save_project () {
        $template_id = $this->input->post('template_id');
        $user_id = $this->user_id; // Assuming session-based user ID retrieval
        $project_data = json_decode($this->input->post('placeholders'), true);
        
        // Validate input
        if (empty($template_id) || empty($project_data)) {
            echo json_encode(['success' => false, 'message' => 'Template ID and project data are required']);
            return;
        }
    
        if (!isset($project_data['project']) || !is_array($project_data['project'])) {
            echo json_encode(['success' => false, 'message' => 'Invalid project data']);
            return;
        }
    
        // Fetch existing data
        $template_data = $this->db->select('placeholders')
                                  ->from('tb_user_resume_templates')
                                  ->where('user_id', $user_id)
                                  ->where('template_id', $template_id)
                                  ->get()
                                  ->row_array();
        
        $existing_placeholders = !empty($template_data['placeholders'])
            ? json_decode($template_data['placeholders'], true)
            : [];
    
        // Ensure projects exist
        if (!isset($existing_placeholders['projects'])) {
            $existing_placeholders['projects'] = [];
        }
    
        $existing_projects = $existing_placeholders['projects'];
    
        // Process new project data
        foreach ($project_data['project'] as $new_project) {
            $is_updated = false;
    
            if (!empty($new_project['id'])) {
                // Update existing entry based on ID
                foreach ($existing_projects as &$existing_project) {
                    if ($existing_project['id'] === $new_project['id']) {
                        $existing_project = $new_project;
                        $is_updated = true;
                        break;
                    }
                }
            }
    
            if (!$is_updated) {
                // Assign a unique ID for new entries
                $new_project['id'] = uniqid('project_');
                $existing_projects[] = $new_project;
            }
        }
    
        $existing_placeholders['projects'] = $existing_projects;
        $updated_placeholders = json_encode($existing_placeholders);
    
        $update_data = [
            'placeholders' => $updated_placeholders,
            'updated_at' => date('Y-m-d H:i:s'),
        ];
    
        // Update the placeholders if the template exists or insert a new one if it doesn't
        $this->db->where('user_id', $user_id)
                 ->where('template_id', $template_id);
    
        if ($this->db->update('tb_user_resume_templates', $update_data)) {
            echo json_encode(['success' => true, 'message' => 'Project data updated successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update project data']);
        }
    }

    public function delete_project () {
        $user_id = $this->user_id; // Assuming session-based user ID retrieval
        $input = json_decode(file_get_contents('php://input'), true); // Get the raw POST data and decode it
    
        if (empty($input) || !isset($input['id'])) {
            echo json_encode(['success' => false, 'message' => 'Invalid project data']);
            return;
        }
    
        // Fetch existing resume template data for the user
        $existing_data = $this->db->select('placeholders')
                                  ->from('tb_user_resume_templates')
                                  ->where('user_id', $user_id)
                                  ->get()
                                  ->row_array();
    
        $placeholders = !empty($existing_data['placeholders']) ? json_decode($existing_data['placeholders'], true) : [];
    
        if (!isset($placeholders['projects'])) {
            echo json_encode(['success' => false, 'message' => 'No projects found']);
            return;
        }
    
        // Remove the project entry based on ID
        foreach ($placeholders['projects'] as $key => $project) {
            if ($project['id'] === $input['id']) {
                unset($placeholders['projects'][$key]);
                break;
            }
        }
    
        // Reindex the array and update the placeholders
        $placeholders['projects'] = array_values($placeholders['projects']);
        $updated_placeholders = json_encode($placeholders);
    
        // Update the database with the new data
        $update_data = [
            'placeholders' => $updated_placeholders,
            'updated_at' => date('Y-m-d H:i:s'),
        ];
    
        $this->db->where('user_id', $user_id);
    
        if ($this->db->update('tb_user_resume_templates', $update_data)) {
            echo json_encode(['success' => true, 'message' => 'Project record deleted successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to delete project record']);
        }
    }
    
    public function fetch_hobbies_data() {
        $user_id = $this->user_id; // Assuming session-based user ID retrieval
        $template_id = $this->input->post('template_id');
    
        // Fetch the template data
        $templateData = $this->ResumeTemplateModel->get_template_data($user_id, $template_id);
    
        if ($templateData) {
            // Decode the placeholders JSON
            $placeholders = json_decode($templateData['placeholders'], true);
            if ($placeholders && isset($placeholders['hobbies'])) {
                $hobbies = $placeholders['hobbies']; // Extract hobbies data
                // Render hobbies structure
                $hobbiesList = $this->_hobbies_structure($hobbies, $placeholders['labelHobbies'] ?? 'Hobbies');
                echo json_encode([
                    'success' => true,
                    'hobbiesList' => $hobbiesList
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'No hobbies data found in the template.'
                ]);
            }
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'No data found for the given user and template.'
            ]);
        }
    }
    
    private function _hobbies_structure($hobbiesList, $sectionTitle) {
        // Return an empty string if hobbies data is empty
        if (empty($hobbiesList)) {
            return '';
        }
    
        $html = "<h2 class='text-lg font-semibold mb-2'>{$sectionTitle}</h2>
                 <ul id='hobbies-list' class='space-y-2'>";
    
        foreach ($hobbiesList as $hobby) {
            $hobbyId = htmlspecialchars($hobby['id'] ?? '');
            $hobbyName = htmlspecialchars($hobby['hobbyName'] ?? '');
            $hobbyDescription = htmlspecialchars($hobby['description'] ?? ''); // Added hobby description
    
            $html .= "<li class='bg-gray-100 p-3 rounded' data-id='{$hobbyId}'>
                        <div class='flex justify-between items-center'>
                            <div class='editable-hobby'>
                                <strong>{$hobbyName}</strong>
                                <p class='text-gray-600'>{$hobbyDescription}</p>
                            </div>
                            <button class='delete-hobby-btn text-red-500'>Delete</button>
                        </div>
                        <div class='edit-hobby-form hidden mt-2'>
                            <input type='text' class='edit-hobby-name border p-1 rounded w-full' value='{$hobbyName}' placeholder='Hobby Name'>
                            <textarea class='edit-hobby-description border p-1 rounded w-full mt-2' placeholder='Hobby Description'>{$hobbyDescription}</textarea>
                            <button class='update-hobby-btn bg-blue-500 text-white px-3 py-1 rounded mt-2' data-id='{$hobbyId}'>Save</button>
                        </div>
                    </li>";
        }
    
        $html .= "</ul>";
        return $html;
    }

    public function save_hobbies() {
        $template_id = $this->input->post('template_id');
        $user_id = $this->user_id; // Assuming session-based user ID retrieval
        $hobbies_data = json_decode($this->input->post('placeholders'), true);
    
        // Validate input
        if (empty($template_id) || empty($hobbies_data)) {
            echo json_encode(['success' => false, 'message' => 'Template ID and hobbies data are required']);
            return;
        }
    
        if (!isset($hobbies_data['hobbies']) || !is_array($hobbies_data['hobbies'])) {
            echo json_encode(['success' => false, 'message' => 'Invalid hobbies data']);
            return;
        }
    
        // Fetch existing data
        $template_data = $this->db->select('placeholders')
                                  ->from('tb_user_resume_templates')
                                  ->where('user_id', $user_id)
                                  ->where('template_id', $template_id)
                                  ->get()
                                  ->row_array();
    
        $existing_placeholders = !empty($template_data['placeholders'])
            ? json_decode($template_data['placeholders'], true)
            : [];
    
        // Ensure hobbies exist
        if (!isset($existing_placeholders['hobbies'])) {
            $existing_placeholders['hobbies'] = [];
        }
    
        $existing_hobbies = $existing_placeholders['hobbies'];
    
        // Process new hobbies data
        foreach ($hobbies_data['hobbies'] as $new_hobby) {
            $is_updated = false;
    
            if (!empty($new_hobby['id'])) {
                // Update existing entry based on ID
                foreach ($existing_hobbies as &$existing_hobby) {
                    if ($existing_hobby['id'] === $new_hobby['id']) {
                        $existing_hobby = $new_hobby;
                        $is_updated = true;
                        break;
                    }
                }
            }
    
            if (!$is_updated) {
                // Assign a unique ID for new entries
                $new_hobby['id'] = uniqid('hobby_');
                $existing_hobbies[] = $new_hobby;
            }
        }
    
        $existing_placeholders['hobbies'] = $existing_hobbies;
        $updated_placeholders = json_encode($existing_placeholders);
    
        $update_data = [
            'placeholders' => $updated_placeholders,
            'updated_at' => date('Y-m-d H:i:s'),
        ];
    
        // Update the placeholders if the template exists or insert a new one if it doesn't
        $this->db->where('user_id', $user_id)
                 ->where('template_id', $template_id);
    
        if ($this->db->update('tb_user_resume_templates', $update_data)) {
            echo json_encode(['success' => true, 'message' => 'Hobbies data updated successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update hobbies data']);
        }
    }
    
    public function delete_hobby() {
        $user_id = $this->user_id; // Assuming session-based user ID retrieval
        $input = json_decode(file_get_contents('php://input'), true); // Get the raw POST data and decode it
    
        if (empty($input) || !isset($input['id'])) {
            echo json_encode(['success' => false, 'message' => 'Invalid hobbies data']);
            return;
        }
    
        // Fetch existing resume template data for the user
        $existing_data = $this->db->select('placeholders')
                                  ->from('tb_user_resume_templates')
                                  ->where('user_id', $user_id)
                                  ->get()
                                  ->row_array();
    
        $placeholders = !empty($existing_data['placeholders']) ? json_decode($existing_data['placeholders'], true) : [];
    
        if (!isset($placeholders['hobbies'])) {
            echo json_encode(['success' => false, 'message' => 'No hobbies found']);
            return;
        }
    
        // Remove the hobby entry based on ID
        foreach ($placeholders['hobbies'] as $key => $hobby) {
            if ($hobby['id'] === $input['id']) {
                unset($placeholders['hobbies'][$key]);
                break;
            }
        }
    
        // Reindex the array and update the placeholders
        $placeholders['hobbies'] = array_values($placeholders['hobbies']);
        $updated_placeholders = json_encode($placeholders);
    
        // Update the database with the new data
        $update_data = [
            'placeholders' => $updated_placeholders,
            'updated_at' => date('Y-m-d H:i:s'),
        ];
    
        $this->db->where('user_id', $user_id);
    
        if ($this->db->update('tb_user_resume_templates', $update_data)) {
            echo json_encode(['success' => true, 'message' => 'Hobby record deleted successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to delete hobby record']);
        }
    }
    
    public function fetch_references_data() {
        $user_id = $this->user_id; // Assuming session-based user ID retrieval
        $template_id = $this->input->post('template_id');
    
        // Fetch the template data
        $templateData = $this->ResumeTemplateModel->get_template_data($user_id, $template_id);
    
        if ($templateData) {
            // Decode the placeholders JSON
            $placeholders = json_decode($templateData['placeholders'], true);
            if ($placeholders && isset($placeholders['references'])) {
                $references = $placeholders['references']; // Extract references data
                // Render references structure
                $referencesList = $this->_references_structure($references, $placeholders['labelReferences'] ?? 'References');
                echo json_encode([
                    'success' => true,
                    'referencesList' => $referencesList
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'No references data found in the template.'
                ]);
            }
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'No data found for the given user and template.'
            ]);
        }
    }
    
    private function _references_structure($referencesList, $sectionTitle) {
        if (empty($referencesList)) {
            return '';
        }
    
        $html = "<h2 class='text-lg font-semibold mb-2'>{$sectionTitle}</h2>
                 <ul id='references-list' class='space-y-2'>";
    
        foreach ($referencesList as $reference) {
            $referenceId = htmlspecialchars($reference['id'] ?? '');
            $referenceName = htmlspecialchars($reference['referenceName'] ?? '');
            $relationship = htmlspecialchars($reference['relationship'] ?? '');
            $email = htmlspecialchars($reference['email'] ?? '');
            $phone = htmlspecialchars($reference['phone'] ?? '');
            $labelReferenceName = htmlspecialchars($reference['labelReferenceName'] ?? '');
            $labelRelationship = htmlspecialchars($reference['labelRelationship'] ?? '');
            $labelEmail = htmlspecialchars($reference['labelEmail'] ?? '');
            $labelPhone = htmlspecialchars($reference['labelPhone'] ?? '');
    
            $html .= "<li class='bg-gray-100 p-3 rounded' data-id='{$referenceId}'>
                        <div class='flex justify-between items-center'>
                            <div class='editable-reference'>
                                <strong>{$referenceName}</strong><br>
                                <span>{$relationship}</span><br>
                                <span>{$email}</span><br>
                                <span>{$phone}</span>
                            </div>
                            <button class='delete-reference-btn text-red-500'>Delete</button>
                        </div>
                        <div class='edit-reference-form hidden mt-2'>
                            <input type='text' class='edit-reference-name border p-1 rounded w-full' value='{$referenceName}' placeholder='{$labelReferenceName}'>
                            <input type='text' class='edit-relationship border p-1 rounded w-full mt-2' value='{$relationship}' placeholder='{$labelRelationship}'>
                            <input type='email' class='edit-email border p-1 rounded w-full mt-2' value='{$email}' placeholder='{$labelEmail}'>
                            <input type='text' class='edit-phone border p-1 rounded w-full mt-2' value='{$phone}' placeholder='{$labelPhone}'>
                            <button class='update-reference-btn bg-blue-500 text-white px-3 py-1 rounded mt-2' data-id='{$referenceId}'>Save</button>
                        </div>
                    </li>";
        }
    
        $html .= "</ul>";
        return $html;
    }

    public function save_references() {
        $template_id = $this->input->post('template_id');
        $user_id = $this->user_id; // Assuming session-based user ID retrieval
        $references_data = json_decode($this->input->post('placeholders'), true);
    
        // Validate input
        if (empty($template_id) || empty($references_data)) {
            echo json_encode(['success' => false, 'message' => 'Template ID and references data are required']);
            return;
        }
    
        if (!isset($references_data['references']) || !is_array($references_data['references'])) {
            echo json_encode(['success' => false, 'message' => 'Invalid references data']);
            return;
        }
    
        // Fetch existing data
        $template_data = $this->db->select('placeholders')
                                  ->from('tb_user_resume_templates')
                                  ->where('user_id', $user_id)
                                  ->where('template_id', $template_id)
                                  ->get()
                                  ->row_array();
    
        $existing_placeholders = !empty($template_data['placeholders'])
            ? json_decode($template_data['placeholders'], true)
            : [];
    
        // Ensure references exist
        if (!isset($existing_placeholders['references'])) {
            $existing_placeholders['references'] = [];
        }
    
        $existing_references = $existing_placeholders['references'];
    
        // Process new references data
        foreach ($references_data['references'] as $new_reference) {
            $is_updated = false;
    
            if (!empty($new_reference['id'])) {
                // Update existing entry based on ID
                foreach ($existing_references as &$existing_reference) {
                    if ($existing_reference['id'] === $new_reference['id']) {
                        $existing_reference = $new_reference;
                        $is_updated = true;
                        break;
                    }
                }
            }
    
            if (!$is_updated) {
                // Assign a unique ID for new entries
                $new_reference['id'] = uniqid('reference_');
                $existing_references[] = $new_reference;
            }
        }
    
        $existing_placeholders['references'] = $existing_references;
        $updated_placeholders = json_encode($existing_placeholders);
    
        $update_data = [
            'placeholders' => $updated_placeholders,
            'updated_at' => date('Y-m-d H:i:s'),
        ];
    
        // Update the placeholders if the template exists or insert a new one if it doesn't
        $this->db->where('user_id', $user_id)
                 ->where('template_id', $template_id);
    
        if ($this->db->update('tb_user_resume_templates', $update_data)) {
            echo json_encode(['success' => true, 'message' => 'References data updated successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update references data']);
        }
    }
    
    public function delete_reference() {
        $user_id = $this->user_id; // Assuming session-based user ID retrieval
        $input = json_decode(file_get_contents('php://input'), true); // Get the raw POST data and decode it
    
        if (empty($input) || !isset($input['id'])) {
            echo json_encode(['success' => false, 'message' => 'Invalid references data']);
            return;
        }
    
        // Fetch existing resume template data for the user
        $existing_data = $this->db->select('placeholders')
                                  ->from('tb_user_resume_templates')
                                  ->where('user_id', $user_id)
                                  ->get()
                                  ->row_array();
    
        $placeholders = !empty($existing_data['placeholders']) ? json_decode($existing_data['placeholders'], true) : [];
    
        if (!isset($placeholders['references'])) {
            echo json_encode(['success' => false, 'message' => 'No references found']);
            return;
        }
    
        // Remove the reference entry based on ID
        foreach ($placeholders['references'] as $key => $reference) {
            if ($reference['id'] === $input['id']) {
                unset($placeholders['references'][$key]);
                break;
            }
        }
    
        // Reindex the array and update the placeholders
        $placeholders['references'] = array_values($placeholders['references']);
        $updated_placeholders = json_encode($placeholders);
    
        // Update the database with the new data
        $update_data = [
            'placeholders' => $updated_placeholders,
            'updated_at' => date('Y-m-d H:i:s'),
        ];
    
        $this->db->where('user_id', $user_id);
    
        if ($this->db->update('tb_user_resume_templates', $update_data)) {
            echo json_encode(['success' => true, 'message' => 'Reference record deleted successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to delete reference record']);
        }
    }
    
    public function fetch_extra_curricular_activities_data() {
        $user_id = $this->user_id; // Assuming session-based user ID retrieval
        $template_id = $this->input->post('template_id');
    
        // Fetch the template data
        $templateData = $this->ResumeTemplateModel->get_template_data($user_id, $template_id);
    
        if ($templateData) {
            // Decode the placeholders JSON
            $placeholders = json_decode($templateData['placeholders'], true);
            if ($placeholders && isset($placeholders['extraCurricularActivities'])) {
                $activities = $placeholders['extraCurricularActivities']; // Extract activities data
                // Render extra-curricular activities structure
                $activitiesList = $this->_extra_curricular_activities_structure($activities, $placeholders['labelExtraCurricularActivities'] ?? 'Extra-curricular Activities');
                echo json_encode([
                    'success' => true,
                    'activitiesList' => $activitiesList
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'No extra-curricular activities data found in the template.'
                ]);
            }
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'No data found for the given user and template.'
            ]);
        }
    }
    
    private function _extra_curricular_activities_structure($activitiesList, $sectionTitle) {
        if (empty($activitiesList)) {
            return '';
        }
    
        $html = "<h2 class='text-lg font-semibold mb-2'>{$sectionTitle}</h2>
                 <ul id='activities-list' class='space-y-2'>";
    
        foreach ($activitiesList as $activity) {
            $activityId = htmlspecialchars($activity['id'] ?? '');
            $activityName = htmlspecialchars($activity['activityName'] ?? '');
            $position = htmlspecialchars($activity['position'] ?? '');
            $description = htmlspecialchars($activity['description'] ?? '');
            $labelActivityName = htmlspecialchars($activity['labelActivityName'] ?? 'Activity Name');
            $labelPosition = htmlspecialchars($activity['labelPosition'] ?? 'Position');
            $labelDescription = htmlspecialchars($activity['labelDescription'] ?? 'Description');
    
            $html .= "<li class='bg-gray-100 p-3 rounded' data-id='{$activityId}'>
                        <div class='flex justify-between items-center'>
                            <div class='editable-activity'>
                                <strong>{$activityName}</strong><br>
                                <span>{$position}</span><br>
                                <span>{$description}</span>
                            </div>
                            <button class='delete-activity-btn text-red-500'>Delete</button>
                        </div>
                        <div class='edit-activity-form hidden mt-2'>
                            <input type='text' class='edit-activity-name border p-1 rounded w-full' value='{$activityName}' placeholder='{$labelActivityName}'>
                            <input type='text' class='edit-position border p-1 rounded w-full mt-2' value='{$position}' placeholder='{$labelPosition}'>
                            <textarea class='edit-description border p-1 rounded w-full mt-2' placeholder='{$labelDescription}'>{$description}</textarea>
                            <button class='update-activity-btn bg-blue-500 text-white px-3 py-1 rounded mt-2' data-id='{$activityId}'>Save</button>
                        </div>
                    </li>";
        }
    
        $html .= "</ul>";
        return $html;
    }
    
    public function save_extra_curricular_activities() {
        $template_id = $this->input->post('template_id');
        $user_id = $this->user_id; // Assuming session-based user ID retrieval
        $activities_data = json_decode($this->input->post('placeholders'), true);
    
        // Validate input
        if (empty($template_id) || empty($activities_data)) {
            echo json_encode(['success' => false, 'message' => 'Template ID and activities data are required']);
            return;
        }
    
        if (!isset($activities_data['activities']) || !is_array($activities_data['activities'])) {
            echo json_encode(['success' => false, 'message' => 'Invalid extra-curricular activities data']);
            return;
        }
    
        // Fetch existing data
        $template_data = $this->db->select('placeholders')
                                  ->from('tb_user_resume_templates')
                                  ->where('user_id', $user_id)
                                  ->where('template_id', $template_id)
                                  ->get()
                                  ->row_array();
    
        $existing_placeholders = !empty($template_data['placeholders'])
            ? json_decode($template_data['placeholders'], true)
            : [];
    
        // Ensure extra-curricular activities exist
        if (!isset($existing_placeholders['extraCurricularActivities'])) {
            $existing_placeholders['extraCurricularActivities'] = [];
        }
    
        $existing_activities = $existing_placeholders['extraCurricularActivities'];
    
        // Process new activities data
        foreach ($activities_data['activities'] as $new_activity) {
            $is_updated = false;
    
            if (!empty($new_activity['id'])) {
                // Update existing entry based on ID
                foreach ($existing_activities as &$existing_activity) {
                    if ($existing_activity['id'] === $new_activity['id']) {
                        $existing_activity = $new_activity;
                        $is_updated = true;
                        break;
                    }
                }
            }
    
            if (!$is_updated) {
                // Assign a unique ID for new entries
                $new_activity['id'] = uniqid('activity_');
                $existing_activities[] = $new_activity;
            }
        }
    
        $existing_placeholders['extraCurricularActivities'] = $existing_activities;
        $updated_placeholders = json_encode($existing_placeholders);
    
        $update_data = [
            'placeholders' => $updated_placeholders,
            'updated_at' => date('Y-m-d H:i:s'),
        ];
    
        // Update the placeholders if the template exists or insert a new one if it doesn't
        $this->db->where('user_id', $user_id)
                 ->where('template_id', $template_id);
    
        if ($this->db->update('tb_user_resume_templates', $update_data)) {
            echo json_encode(['success' => true, 'message' => 'Extra-curricular activities data updated successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update extra-curricular activities data']);
        }
    }
    
    public function delete_activity() {
        $user_id = $this->user_id; // Assuming session-based user ID retrieval
        $input = json_decode(file_get_contents('php://input'), true); // Get the raw POST data and decode it
    
        if (empty($input) || !isset($input['id'])) {
            echo json_encode(['success' => false, 'message' => 'Invalid activities data']);
            return;
        }
    
        // Fetch existing resume template data for the user
        $existing_data = $this->db->select('placeholders')
                                  ->from('tb_user_resume_templates')
                                  ->where('user_id', $user_id)
                                  ->get()
                                  ->row_array();
    
        $placeholders = !empty($existing_data['placeholders']) ? json_decode($existing_data['placeholders'], true) : [];
    
        if (!isset($placeholders['extraCurricularActivities'])) {
            echo json_encode(['success' => false, 'message' => 'No extra-curricular activities found']);
            return;
        }
    
        // Remove the activity entry based on ID
        foreach ($placeholders['extraCurricularActivities'] as $key => $activity) {
            if ($activity['id'] === $input['id']) {
                unset($placeholders['extraCurricularActivities'][$key]);
                break;
            }
        }
    
        // Reindex the array and update the placeholders
        $placeholders['extraCurricularActivities'] = array_values($placeholders['extraCurricularActivities']);
        $updated_placeholders = json_encode($placeholders);
    
        // Update the database with the new data
        $update_data = [
            'placeholders' => $updated_placeholders,
            'updated_at' => date('Y-m-d H:i:s'),
        ];
    
        $this->db->where('user_id', $user_id);
    
        if ($this->db->update('tb_user_resume_templates', $update_data)) {
            echo json_encode(['success' => true, 'message' => 'Activity record deleted successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to delete activity record']);
        }
    }
    
    public function fetch_custom_sections_data() {
        $user_id = $this->user_id; // Assuming session-based user ID retrieval
        $template_id = $this->input->post('template_id');
    
        // Fetch the template data
        $templateData = $this->ResumeTemplateModel->get_template_data($user_id, $template_id);
    
        if ($templateData) {
            // Decode the placeholders JSON
            $placeholders = json_decode($templateData['placeholders'], true);
            if ($placeholders && isset($placeholders['customSections'])) {
                $customSections = $placeholders['customSections']; // Extract custom sections data
                $labelSectionTitle = $placeholders['labelSectionTitle'] ?? 'Custom Sections';
    
                // Render custom sections structure
                $customSectionsHtml = $this->_custom_sections_structure($customSections, $labelSectionTitle);
                echo json_encode([
                    'success' => true,
                    'customSectionsHtml' => $customSectionsHtml
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'No custom sections data found in the template.'
                ]);
            }
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'No data found for the given user and template.'
            ]);
        }
    }
    
    private function _custom_sections_structure($customSections, $labelSectionTitle) {
        if (empty($customSections)) {
            return '';
        }
    
        $html = "<h2 class='text-lg font-semibold mb-2'>{$labelSectionTitle}</h2>
                 <ul id='custom-sections-list' class='space-y-4'>";
    
        foreach ($customSections as $section) {
            $sectionId = htmlspecialchars($section['id'] ?? '');
            $sectionTitle = htmlspecialchars($section['sectionTitle'] ?? '');
            $title = htmlspecialchars($section['title'] ?? '');
            $description = htmlspecialchars($section['description'] ?? '');
            $date = htmlspecialchars($section['date'] ?? '');
            $labelTitle = htmlspecialchars($section['labelTitle'] ?? '');
            $labelDescription = htmlspecialchars($section['labelDescription'] ?? '');
            $labelDate = htmlspecialchars($section['labelDate'] ?? '');
    
            $html .= "<li class='bg-gray-100 p-4 rounded' data-id='{$sectionId}'>
                        <div class='flex justify-between items-center'>
                            <div class='editable-section'>
                                <strong>{$sectionTitle}</strong><br>
                                 <span>{$title}</span><br>
                                <span>{$description}</span><br>
                                <span>{$date}</span>
                            </div>
                            <button class='delete-section-btn text-red-500'>Delete</button>
                        </div>
                        <div class='edit-section-form hidden mt-2'>
                            <input type='text' class='edit-section-title border p-1 rounded w-full' value='{$sectionTitle}' placeholder=''>
                            <input type='text' class='edit-title border p-1 rounded w-full' value='{$title}' placeholder='{$labelTitle}'>
                            <textarea class='edit-description border p-1 rounded w-full mt-2' placeholder='{$labelDescription}'>{$description}</textarea>
                            <input type='date' class='edit-date border p-1 rounded w-full mt-2' value='{$date}' placeholder='{$labelDate}'>
                            <button class='update-section-btn bg-blue-500 text-white px-3 py-1 rounded mt-2' data-id='{$sectionId}'>Save</button>
                        </div>
                    </li>";
        }
    
        $html .= "</ul>";
        return $html;
    }

    public function save_custom_section() {
        $template_id = $this->input->post('template_id');
        $user_id = $this->user_id; // Assuming session-based user ID retrieval
        $custom_sections_data = json_decode($this->input->post('placeholders'), true);
    
        // Validate input
        if (empty($template_id) || empty($custom_sections_data)) {
            echo json_encode(['success' => false, 'message' => 'Template ID and customSections data are required']);
            return;
        }
    
        if (!isset($custom_sections_data['customSections']) || !is_array($custom_sections_data['customSections'])) {
            echo json_encode(['success' => false, 'message' => 'Invalid customSections data']);
            return;
        }
    
        // Fetch existing data
        $template_data = $this->db->select('placeholders')
                                  ->from('tb_user_resume_templates')
                                  ->where('user_id', $user_id)
                                  ->where('template_id', $template_id)
                                  ->get()
                                  ->row_array();
    
        $existing_placeholders = !empty($template_data['placeholders'])
            ? json_decode($template_data['placeholders'], true)
            : [];
    
        // Ensure customSections exist
        if (!isset($existing_placeholders['customSections'])) {
            $existing_placeholders['customSections'] = [];
        }
    
        $existing_sections = $existing_placeholders['customSections'];
    
        // Process new custom sections data
        foreach ($custom_sections_data['customSections'] as $new_section) {
            $is_updated = false;
    
            if (!empty($new_section['id'])) {
                // Update existing entry based on ID
                foreach ($existing_sections as &$existing_section) {
                    if ($existing_section['id'] === $new_section['id']) {
                        $existing_section = $new_section;
                        $is_updated = true;
                        break;
                    }
                }
            }
    
            if (!$is_updated) {
                // Assign a unique ID for new entries
                $new_section['id'] = uniqid('section_');
                $existing_sections[] = $new_section;
            }
        }
    
        $existing_placeholders['customSections'] = $existing_sections;
        $updated_placeholders = json_encode($existing_placeholders);
    
        $update_data = [
            'placeholders' => $updated_placeholders,
            'updated_at' => date('Y-m-d H:i:s'),
        ];
    
        // Update the placeholders if the template exists or insert a new one if it doesn't
        $this->db->where('user_id', $user_id)
                 ->where('template_id', $template_id);
    
        if ($this->db->update('tb_user_resume_templates', $update_data)) {
            echo json_encode(['success' => true, 'message' => 'customSections data updated successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update customSections data']);
        }
    }

    public function delete_section() {
        $user_id = $this->user_id; // Assuming session-based user ID retrieval
        $input = json_decode(file_get_contents('php://input'), true); // Get the raw POST data and decode it
    
        if (empty($input) || !isset($input['id'])) {
            echo json_encode(['success' => false, 'message' => 'Invalid customSections data']);
            return;
        }
    
        // Fetch existing resume template data for the user
        $existing_data = $this->db->select('placeholders')
                                  ->from('tb_user_resume_templates')
                                  ->where('user_id', $user_id)
                                  ->get()
                                  ->row_array();
    
        $placeholders = !empty($existing_data['placeholders']) ? json_decode($existing_data['placeholders'], true) : [];
    
        if (!isset($placeholders['customSections'])) {
            echo json_encode(['success' => false, 'message' => 'No custom sections found']);
            return;
        }
    
        // Remove the custom section entry based on ID
        foreach ($placeholders['customSections'] as $key => $custom_section) {
            if ($custom_section['id'] === $input['id']) {
                unset($placeholders['customSections'][$key]);
                break;
            }
        }
    
        // Reindex the array and update the placeholders
        $placeholders['customSections'] = array_values($placeholders['customSections']);
        $updated_placeholders = json_encode($placeholders);
    
        // Update the database with the new data
        $update_data = [
            'placeholders' => $updated_placeholders,
            'updated_at' => date('Y-m-d H:i:s'),
        ];
    
        $this->db->where('user_id', $user_id);
    
        if ($this->db->update('tb_user_resume_templates', $update_data)) {
            echo json_encode(['success' => true, 'message' => 'Custom section deleted successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to delete custom section']);
        }
    }

    

}