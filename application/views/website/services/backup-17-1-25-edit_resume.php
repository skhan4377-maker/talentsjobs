<?php $this->load->view('common/inc/top-header');?>
<!-- jQuery UI CSS -->
<link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
<!-- jQuery UI JavaScript -->
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
<!-- Flatpickr CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<!-- Flatpickr JS -->
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
 
<style>
/* Left Container Styling */
.left-container {
    width: 50%;
    padding: 15px;
    overflow-y: auto;
    max-height: 100vh;
    background-color: #f9fbfd;
    border-right: 1px solid #e0e6ed;
    margin-top:20px;
}

/* Resume Container Styling */
.resume-container {
    background-color: #ffffff;
    padding: 15px;
    border-radius: 8px;
    box-shadow: 0 3px 15px rgba(0, 0, 0, 0.1);
    margin-top: 5%;
}

/* Right Container Styling */
.right-container {
    width: 50%;
    overflow-y: auto;
    max-height: 100vh;
    padding: 15px;
    background-color: #f2f4f8;
    display: block;
    flex-direction: column;
    justify-content: center;
    align-items: center;
   margin-top:20px;
}

/* Preview Button Styling */
.preview-button {
    display: none;
    position: fixed;
    bottom: 15px;
    left: 50%;
    transform: translateX(-50%);
    padding: 10px 20px;
    background-color: #007bff;
    color: #fff;
    border-radius: 20px;
    font-size: 14px;
    font-weight: bold;
    cursor: pointer;
    z-index: 1000;
    box-shadow: 0px 4px 12px rgba(0, 123, 255, 0.4);
    transition: all 0.3s;
}

.preview-button:hover {
    background-color: #0056b3;
    box-shadow: 0px 6px 18px rgba(0, 123, 255, 0.6);
}

/* Modal Styling */
.modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    justify-content: center;
    align-items: center;
}

.modal-content {
    width: 85%;
    max-width: 500px;
    height: 75%;
    background-color: #fff;
    border-radius: 10px;
    padding: 15px;
    position: relative;
    box-shadow: 0 3px 15px rgba(0, 0, 0, 0.3);
}

.close-modal {
    position: absolute;
    top: 10px;
    right: 15px;
    font-size: 20px;
    cursor: pointer;
    color: #333;
}

/* Form Styling */
.form-input {
    width: 100%;
    padding: 12px 16px;
    margin: 6px 0;
    border: 1px solid #ddd;
    border-radius: 6px;
    font-size: 14px;
    transition: border-color 0.3s;
}

.form-input:focus {
    border-color: #007bff;
}

.submit-btn {
    background-color: #28a745;
    color: #fff;
    padding: 10px 20px;
    border: none;
    border-radius: 6px;
    font-size: 14px;
    font-weight: bold;
    cursor: pointer;
    box-shadow: 0 3px 12px rgba(40, 167, 69, 0.4);
    transition: background-color 0.3s, box-shadow 0.3s;
}

.submit-btn:hover {
    background-color: #218838;
    box-shadow: 0 5px 18px rgba(40, 167, 69, 0.6);
}

/* Headings */
h3 {
    margin-bottom: 15px;
    font-size: 18px;
    font-weight: bold;
    color: #333;
}

/* Label Styling */
label {
    font-size: 13px;
    color: #555;
    font-weight: bold;
}

/* Loader */
.loader {
    border: 5px solid #f3f3f3;
    border-top: 5px solid #007bff;
    border-radius: 50%;
    width: 30px;
    height: 30px;
    animation: spin 1s linear infinite;
    display: none;
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
}

.resume-preview {
    margin-top: 30px;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* Responsive Design for Mobile */
@media (max-width: 768px) {
    body {
        flex-direction: column;
        height: auto;
        background-color: #f2f4f8;
    }

    .left-container, .right-container {
        width: 100%;
        padding: 10px;
    }

    .preview-button {
        display: block;
    }

    .right-container {
        display: none;
    }

    .form-input {
        font-size: 12px;
        padding: 12px 16px;
    }

    .submit-btn {
        font-size: 12px;
        padding: 8px 16px;
    }

    label {
        font-size: 12px;
    }

    h3 {
        font-size: 16px;
    }
}
</style>
<style>
        /* Additional styling for two-column layout */
        .form-row {
            display: flex;
            gap: 20px;
            margin-bottom: 15px;
        }
        
        .form-col {
            flex: 1;
        }
        
        /* Toggle Button Styling */
        .toggle-btn {
            color: #007bff;
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            font-size: 15px;
            font-weight: bold;
            cursor: pointer;
            margin: 10px 0;
            display: inline-block;
        }
      
        
        /* Adjust input field font size and padding for compact look */
        .form-input {
            font-size: 14px;
            padding: 12px 16px;
        }
    </style>
    
    
<style>
    .resume-menu {
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
        margin-bottom: 20px;
        justify-content: center;
    }

    .resume-menu-item {
        display: flex;
        align-items: center;
        gap: 10px;
        cursor: pointer;
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 5px;
        transition: background-color 0.3s;
        max-width: 150px;
        justify-content: center;
    }

    .resume-menu-item:hover {
        background-color: #f0f0f0;
    }

    .resume-menu-item img {
        width: 20px;
        height: 20px;
    }

    .resume-form-section {
        display: none;
        margin-top: 20px;
        border: 1px solid #ddd;
        padding: 20px;
        border-radius: 5px;
    }

    .resume-form-section.active {
        display: block;
    }

    .resume-form-group {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 10px;
        flex-wrap: wrap;
    }

    .resume-form-group input,
    .resume-form-group select,
    .resume-form-group textarea {
        width: 100%;
        padding: 8px;
        border: 1px solid #ddd;
        border-radius: 5px;
        font-size: 16px;
    }

    .resume-add-button {
        color: #007bff;
        cursor: pointer;
        font-size: 14px;
        text-decoration: underline;
    }

    .save-button {
        background-color: #28a745;
        color: white;
        border: none;
        padding: 10px 20px;
        font-size: 16px;
        border-radius: 5px;
        cursor: pointer;
    }

    .save-button:hover {
        background-color: #218838;
    }

    @media (max-width: 768px) {
        .resume-menu-item {
            width: 45%;
            max-width: 120px;
        }

        .resume-form-group input,
        .resume-form-group select,
        .resume-form-group textarea {
            font-size: 14px;
        }

        .resume-add-button {
            font-size: 12px;
        }

        .save-button {
            font-size: 14px;
        }
    }

    @media (max-width: 480px) {
        .resume-menu-item {
            width: 100%;
            justify-content: flex-start;
            padding-left: 15px;
        }

        .resume-form-group input,
        .resume-form-group select,
        .resume-form-group textarea {
            font-size: 12px;
        }

        .resume-add-button {
            font-size: 10px;
        }

        .save-button {
            font-size: 12px;
        }
    }
</style>
<style>
/*.skills-section {
    padding: 20px;
    background-color: #ffffff;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    font-family: Arial, sans-serif;
    margin: auto;
}

.section-title {
    font-size: 24px;
    font-weight: bold;
    color: #333;
    margin-bottom: 10px;
}

.section-description {
    font-size: 14px;
    color: #666;
    margin-bottom: 20px;
}

.skill-tags {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
}

.skill-tag {
    background-color: #f1f1f1;
    padding: 8px 12px;
    border-radius: 20px;
    font-size: 14px;
    color: #333;
    display: flex;
    align-items: center;
    justify-content: space-between;
    border: 1px solid #ccc;
    max-width: 200px; 
    word-break: break-word;  
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.skill-tag.selected-skill {
    background-color: #e8f8e8;
    border-color: #28a745;
}

.btn-remove-skill {
    background: transparent;
    border: none;
    color: #ff5e57;
    font-size: 16px;
    cursor: pointer;
    font-weight: bold;
}

.btn-remove-skill:hover {
    color: #d9534f;
}

.add-skill-section h4 {
    margin-bottom: 10px;
    font-size: 18px;
    color: #333;
}

.skill-entry {
    display: flex;
    gap: 10px;
    align-items: center;
    flex-wrap: wrap;
}

.input-skill {
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 5px;
    flex: 1;
    font-size: 14px;
}

.select-level {
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 5px;
    flex: 1;
    font-size: 14px;
}

.btn-add-skill {
    background-color: #007bff;
    color: #fff;
    padding: 10px 20px;
    border: none;
    border-radius: 5px;
    font-size: 14px;
    cursor: pointer;
    transition: background-color 0.3s;
}

.btn-add-skill:hover {
    background-color: #0056b3;
}


.available-skills {
    margin-bottom: 20px;
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    border: 1px solid #ccc;
    padding: 10px;
    border-radius: 5px;
    background-color: #f9f9f9;
}

.selected-skills {
    margin-top: 20px;
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    border: 1px solid #28a745;
    padding: 10px;
    border-radius: 5px;
    background-color: #e8f8e8;
}

.selected-skills h4 {
    width: 100%;
    margin-bottom: 10px;
    font-size: 18px;
    color: #28a745;
}

@media (max-width: 768px) {
    .skill-entry {
        flex-direction: column;
    }

    .input-skill,
    .select-level {
        width: 100%;
    }
}*/
</style>
    <?php $this->load->view('common/inc/header');?>
    
    <div style="display: flex; flex-direction: row;">
        
        <div class="left-container">
            <div class="resume-container" id="resume">
                <!-- Basic form with two columns and toggle section added here -->
                <form id="resumeForm" style="margin: auto; padding: 20px; border: 1px solid #ddd; border-radius: 8px; background-color: #f9f9f9;">
                    <input type="hidden" name="template_id" id="templateId" value="<?=$this->uri->segment(2)?>">
                    <h3 style="text-align: center; color: #333;">Create Your Resume</h3>
                    <div class="form-row" style="display: flex; align-items: center; gap: 15px; margin-bottom: 15px; flex-wrap: wrap;">
                        <div class="form-col" style="flex: 2;">
                            <h3 style="color: #333;">Personal details</h3>
                        </div>
                    </div>
                        
                    <div class="form-row" style="display: flex; align-items: center; gap: 15px; margin-bottom: 15px; flex-wrap: wrap;">
                        <div class="form-col" style="flex: 1;">
                            <p for="jobTitle" style="font-weight: bold;">Job Title:</p>
                            <input type="text" id="jobProfile" name="jobProfile" placeholder="e.g., Software Developer" 
                            class="form-input" style="width: 100%; border: 1px solid #ccc; border-radius: 5px; font-size: 14px;">
                        </div>
                        <div class="form-col" style="display: flex; align-items: center; gap: 10px;">
                            <div id="uploadPhotoContainer" style="width: 70px; height: 70px; border: 1px solid #ccc; border-radius: 5px; overflow: hidden; display: flex; justify-content: center; align-items: center; background-color: #f0f0f0;">
                                <img id="previewImage" src="placeholder.png" alt="User Photo" style="max-width: 100%; max-height: 100%; display: none;">
                                <svg width="40px" height="40px" viewBox="0 0 40 40" version="1.1" xmlns="http://www.w3.org/2000/svg"><path d="M7,35 L33,35 L33,30 L24.9627594,26.8044586 C24.5698041,26.6156354 24.3340309,26.2379889 24.3340309,25.8225778 L24.3340309,24.6518737 C24.3340309,24.3875212 24.412622,24.1231687 24.6090996,23.9343454 C25.9058522,22.5370535 26.770354,20.4599979 27.0061272,19.8557636 C27.0454227,19.7424697 27.1240138,19.6291757 27.2026048,19.5536464 C27.438378,19.3648232 27.8313333,18.8738828 28.1064021,17.7031787 C28.3421753,16.5702393 27.9099244,15.9282403 27.6348557,15.6261232 C27.4776736,15.4750646 27.3990825,15.2862413 27.3990825,15.0974181 L27.3990825,10.2257787 C26.8882406,7.43119483 25.1592371,6.1094322 23.4302337,5.50519785 C21.5833436,4.82543421 18.2825188,4.82543421 16.3963333,5.5429625 C14.7459209,6.18496149 13.056213,7.46895948 12.5846666,10.2257787 L12.5846666,15.0974181 C12.5846666,15.2862413 12.5060755,15.4750646 12.3488934,15.6261232 C12.0738246,15.9282403 11.6415738,16.5702393 11.877347,17.7031787 C12.1131202,18.8738828 12.545371,19.3648232 12.7811442,19.5536464 C12.8597353,19.6291757 12.9383264,19.7424697 12.9776219,19.8557636 C13.2133951,20.4977626 14.0778968,22.4992889 15.3353539,23.8965808 C15.5318315,24.1231687 15.6497181,24.4252858 15.6497181,24.727403 L15.6497181,25.7470485 C15.6497181,26.2379889 15.3746494,26.6534 14.903103,26.8799879 L7,30 L7,35 Z"></path></svg>
                            </div>
                            <label for="uploadPhoto" style="cursor: pointer; color: #007bff; font-weight: bold; font-size: 14px;">Upload Photo</label>
                            <input type="file" id="uploadPhoto" name="uploadPhoto" accept="image/*" style="display: none;" onchange="previewUploadedImage(event)">
                        </div>
                    </div>
                    
                    <div class="form-row" style="display: flex; gap: 15px; margin-bottom: 15px;">
                        <div class="form-col" style="flex: 1;">
                            <label for="name" style="font-weight: bold;">Full Name:</label>
                            <input type="text" id="name" name="name"  placeholder="e.g., John Doe" class="form-input" style="width: 100%; border: 1px solid #ccc; border-radius: 5px;">
                        </div>
                        <div class="form-col" style="flex: 1;">
                            <label for="email" style="font-weight: bold;">Email Address:</label>
                            <input type="email" id="email" name="email"  placeholder="e.g., john.doe@example.com" class="form-input" style="width: 100%; 10px; border: 1px solid #ccc; border-radius: 5px;">
                        </div>
                    </div>
                
                    <div class="form-row" style="display: flex; gap: 15px; margin-bottom: 15px;">
                            <div class="form-col" style="flex: 1;">
                                <label for="dob" style="font-weight: bold;">Date of Birth:</label>
                                <input 
                                    type="text" 
                                    id="dob" 
                                    name="dob" 
                                    placeholder="MM/DD/YYYY" 
                                    class="form-input" 
                                    style="width: 100%; border: 1px solid #ccc; border-radius: 5px;">
                            </div>
                            
                              <div class="form-col" style="flex: 1;">
                                <label for="phone" style="font-weight: bold;">Contact Number:</label>
                                <input type="text" id="phone" name="phone"  placeholder="e.g., +1 234 567 8900" class="form-input" style="width: 100%; border: 1px solid #ccc; border-radius: 5px;">
                            </div>
                        </div>
                
                    <div class="form-row" style="display: flex; gap: 15px; margin-bottom: 15px;">
                        <div class="form-col" style="flex: 1;">
                            <label for="city" style="font-weight: bold;">City:</label>
                            <input type="text" id="city" name="city"  placeholder="e.g., New York" class="form-input" style="width: 100%; border: 1px solid #ccc; border-radius: 5px;">
                            <input type="hidden" id="hiddenLocationId" name="hiddenLocationId">
                        </div>
                        <div class="form-col" style="flex: 1;">
                            <label for="country" style="font-weight: bold;">Country:</label>
                            <input type="text" id="country" name="country"  placeholder="e.g., United States" class="form-input" style="width: 100%; border: 1px solid #ccc; border-radius: 5px;">
                        </div>
                    </div>
                
                    <!-- Additional Details Section (Hidden by Default) -->
                    <div id="additionalDetails" style="display: none;">
                        <div class="form-row" style="display: flex; gap: 15px; margin-bottom: 15px;">
                            <div class="form-col" style="flex: 1;">
                                <label for="address" style="font-weight: bold;">Residential Address:</label>
                                <input type="text" id="address" name="address" placeholder="e.g., 123 Main St, Apt 4B" class="form-input" style="width: 100%;border: 1px solid #ccc; border-radius: 5px;">
                            </div>
                            <div class="form-col" style="flex: 1;">
                                <label for="postal" style="font-weight: bold;">Postal Code:</label>
                                <input type="text" id="postal" name="postal" placeholder="e.g., 10001" class="form-input" style="width: 100%; border: 1px solid #ccc; border-radius: 5px;">
                            </div>
                        </div>
                    </div>
                    
                    <!-- Toggle Button for Additional Details -->
                    <a type="button" id="toggleDetails" class="toggle-btn" style="width: 100%; padding: 10px; margin-top: 10px; color:rgb(26, 145, 240); border: none; border-radius: 5px;">Show Additional Details</a>
                    
                    <!-- Professional Summary Section -->
                    <div class="row" style="margin-bottom: 15px;">
                        <h3 for="resume_headline" style="font-weight: bold;">Professional Summary</h3>
                        <p style="font-size: 1em; color: #666;">Write 2–4 short, energetic sentences about how great you are. Mention the role and your achievements. List your skills and motivation.</p>
                        <textarea id="resume_headline" name="resume_headline" placeholder="Curious science teacher with 8+ year of experiece and a track record of..." rows="8" class="form-input" style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px;"></textarea>
                        <p style="font-size: 0.9em; color: #666;">Tip: Aim for 400–600 characters to increase your interview chances.</p>
                    </div>
                    
                    <div id="employmentContainer" style="max-width: 800px; margin: auto;">
                        <h3 style="color: #333;">Employment History</h3>
                        <p style="color: #666;">Show your relevant experience (last 10 years). Use bullet points to note your achievements, if possible – use numbers/facts (Achieved X, measured by Y, by doing Z).</p>
                        
                        <input type="hidden" name="employment" id="employment" value="employment">
                        <!-- Existing Employment Entries will load here -->
                        <div id="employmentEntries"></div>
                    
                        <!-- Form to add new employment -->
                        <button type="button" id="addEmploymentButton" style="margin-top: 20px;">Add Employment</button>
                        <div id="employmentForm" style="display: none; border: 1px solid #ddd; padding: 15px; border-radius: 8px; background-color: #f9f9f9; margin-top: 20px;">
                            <div style="display: flex; gap: 15px; margin-bottom: 15px;">
                                <div style="flex: 1;">
                                    <label>Job Title:</label>
                                    <input type="text" class="form-input" name="jobTitle" id="jobTitle" placeholder="Job Title" style="width: 100%;border-radius: 5px; border: 1px solid #ccc;">
                                </div>
                                <div style="flex: 1;">
                                    <label>Employer:</label>
                                    <input type="text" class="form-input" name="employer" id="employmentCompany" placeholder="Employer" style="width: 100%; border-radius: 5px; border: 1px solid #ccc;">
                                </div>
                            </div>
                            <div style="display: flex; gap: 15px; margin-bottom: 15px;">
                                <div style="flex: 1;">
                                    <label for="employmentStartDate">Start Date:</label>
                                    <input type="text" class="form-input" name="startDate" id="employmentStartDate" placeholder="YYYY / MM" style="width: 100%; border-radius: 5px; border: 1px solid #ccc;">
                                </div>
                                <div style="flex: 1; display: flex; align-items: center; gap: 10px;">
                                    <div style="flex: 1;">
                                        <label for="employmentEndDate">End Date:</label>
                                        <input type="text" name="endDate" id="employmentEndDate" placeholder="YYYY / MM" style="width: 100%; border-radius: 5px; border: 1px solid #ccc;">
                                        <div style="flex:1; align-items: center; gap: 5px;">
                                            <input type="checkbox" class="form-input" name="currentWork" id="currentWork" style="width: 20px; height: 20px; cursor: pointer;">
                                            <label for="currentWork" style="font-size: 14px; cursor: pointer;">Currently Work Here</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div style="display: flex; gap: 15px; margin-bottom: 15px;"> 
                                  <div style="flex: 1;">
                                        <label>City:</label>
                                        <input type="text" class="form-input" name="workLocation" id="workLocation" placeholder="" style="width: 100%;border-radius: 5px; border: 1px solid #ccc;">
                                    </div>
                                 </div>    
                                <div style="margin-bottom: 15px;">
                                    <label>Professional Summary:</label>
                                    <textarea id="description" class="form-input" name="description" placeholder="e.g., Created and implemented lesson plans..." rows="4" style="width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #ccc;"></textarea>
                                </div>
                            <!-- Button for saving employment entry -->
                            <button type="button" id="saveEmploymentButton">Add Employment</button>
                        </div>
                    </div>
                    
                    <div id="educationSection" style="max-width: 800px; margin: auto;">
                        <h3 style="color: #333;">Education History</h3>
                        <p style="color: #666;">Show your educational qualifications. Include relevant degrees or certifications.</p>
                    
                        <!-- Existing Education Entries will load here -->
                        <div id="educationEntries"></div>
                    
                        <!-- Form to add new education -->
                        <div id="educationForm" style="display: none; border: 1px solid #ddd;border-radius: 8px; background-color: #f9f9f9; margin-top: 20px;">
                            <div style="display: flex; gap: 15px; margin-bottom: 15px;">
                                <div style="flex: 1;">
                                    <label>Degree:</label>
                                    <input type="text" class="form-input" id="degree" placeholder="e.g., Bachelor of Science" style="width: 100%;border-radius: 5px; border: 1px solid #ccc;">
                                </div>
                                <div style="flex: 1;">
                                    <label>Institution:</label>
                                    <input type="text" class="form-input" id="institution" placeholder="e.g., University Name" style="width: 100%; border-radius: 5px; border: 1px solid #ccc;">
                                </div>
                            </div>
                            <div style="display: flex; gap: 15px; margin-bottom: 15px;">
                                <div style="flex: 1;">
                                    <label>Start Year:</label>
                                    <input type="text" id="startYear" placeholder="YYYY / MM" style="width: 100%;border-radius: 5px; border: 1px solid #ccc;">
                                </div>
                                <div style="flex: 1;">
                                    <label>End Year:</label>
                                    <input type="text" id="endYear" placeholder="YYYY / MM" style="width: 100%; border-radius: 5px; border: 1px solid #ccc;">
                                    <div style="margin-top: 5px;">
                                        <input type="checkbox" id="currentlyStudying" />
                                        <label for="currentlyStudying">Currently Studying Here</label>
                                    </div>
                                </div>
                            </div>
                            <div style="margin-bottom: 15px;">
                                <label>Field of Study:</label>
                                <input type="text" class="form-input" id="fieldOfStudy" placeholder="e.g., Computer Science" style="width: 100%;border-radius: 5px; border: 1px solid #ccc;">
                            </div>
                            <!-- Save Education Button -->
                            <button type="button" id="saveEducationButton">Add Education</button>
                        </div>
                    
                        <!-- Button to display education form -->
                        <button type="button" id="addEducationButton" style="margin-top: 20px;">Add Education</button>
                    </div>

                    <input type="hidden" id="link_id" value="">
                    <div style="max-width: 800px; margin: auto;">
                            <h3 style="color: #333;">Website & Social Links</h3>
                            <p style="color: #666;">Provide your website or social media links. Include LinkedIn, Portfolio, GitHub, etc.</p>
                        
                            <!-- Existing Links Entries will load here -->
                            <div id="linksEntries"></div>
                        
                            <!-- Form to add new website/social links -->
                            <div id="linksForm" style="display: none; border: 1px solid #ddd;border-radius: 8px; background-color: #f9f9f9; margin-top: 20px;">
                                <div style="margin-bottom: 15px;">
                                    <label>Link Label:</label>
                                    <input type="text" class="form-input" id="linkLabel" placeholder="e.g., LinkedIn, Portfolio" style="width: 100%; border-radius: 5px; border: 1px solid #ccc;">
                                </div>
                                <div style="margin-bottom: 15px;">
                                    <label>Link URL:</label>
                                    <input type="text" class="form-input" id="linkURL" placeholder="e.g., https://www.linkedin.com/in/yourname" style="width: 100%;border-radius: 5px; border: 1px solid #ccc;">
                                </div>
                                <button type="button" id="saveLinkButton">Add Link</button>
                            </div>
                        
                            <!-- Button to display links form -->
                            <button type="button" id="addLinksButton" style="margin-top: 20px;">Add More Links</button>
                        </div>
                        
                        <!--<div class="skills-section">
                            <h3>Skills</h3>
                            <p>Choose 5 important skills that show you fit the position. Make sure they match the key skills mentioned in the job listing.</p>
                            <h4>Available Skills</h4>
                            <div class="available-skills"> </div>
                            
                           <h4>Selected Skills</h4>
                            <div class="selected-skills skill-tags">
                                <span class="skill-tag selected-skill">Python 
                                    <button class="btn-remove-skill">×</button>
                                </span>
                            </div>
                            <div class="add-skill-section">
                                <button id="add-skill-btn" class="btn-add-skill">+ Add more skill</button>
                                <div class="skill-entry-container">
                                  
                                </div>
                            </div>
                        </div>-->
                    
                        <div class="form-row" style="display: flex; align-items: center; gap: 15px; margin-bottom: 15px; flex-wrap: wrap;">
                            <h3 style="color: #333;">Add Section</h3>
                        </div>
                   
                        <div class="form-row" style="display: flex; align-items: center; gap: 15px; margin-bottom: 15px; flex-wrap: wrap;">
                            <!-- Existing Language Entries will load here -->
                            <div id="languageEntries"></div>
                            <div style="max-width: 800px; margin: auto;" id="languages" class="resume-form-section">
                            <h3 style="color: #333;">Languages</h3>
                            <p style="color: #666;">Add the languages you know and your proficiency levels.</p>
                        
                            <!-- Form to add new languages -->
                            <div id="languageForm" style="
                                border: 1px solid #ddd; 
                                border-radius: 8px; 
                                background-color: #f9f9f9; 
                                margin-top: 20px; 
                                position: relative; 
                                padding: 15px;">
                                <!-- Close Button -->
                                <button type="button" id="closeLanguageFormButton" style="
                                        position: absolute; 
                                        top: 10px; 
                                        right: 10px; 
                                        background-color: transparent; 
                                        color: #f00; 
                                        border: none; 
                                        font-size: 18px; 
                                        font-weight: bold; 
                                        cursor: pointer;
                                    ">×</button>
                        
                                <div style="margin-bottom: 15px;">
                                    <label>Language:</label>
                                    <input type="text" class="form-input" id="languageName" placeholder="e.g., English" style="width: 100%; border-radius: 5px; border: 1px solid #ccc;">
                                </div>
                                <div style="margin-bottom: 15px;">
                                    <label>Proficiency:</label>
                                    <select id="languageProficiency" style="width: 100%; border-radius: 5px; border: 1px solid #ccc;">
                                        <option value="">Select Proficiency</option>
                                        <option value="Beginner">Beginner</option>
                                        <option value="Intermediate">Intermediate</option>
                                        <option value="Advanced">Advanced</option>
                                        <option value="Fluent">Fluent</option>
                                        <option value="Native">Native</option>
                                    </select>
                                </div>
                                <button type="button" id="saveLanguageButton">Add Language</button>
                            </div>
                        
                            <!-- Button to display language form -->
                            <button type="button" id="addLanguageButton" style="margin-top: 20px;">Add More Languages</button>
                        </div>

                            <div id="hobbiesEntries"></div>
                        
                            <div id="hobbies" class="resume-form-section" style="border: 1px solid #ddd;border-radius: 8px; background-color: #f9f9f9; margin-top: 20px; position: relative; padding: 15px;">
                            <h2>Hobbies</h2>
                            <!-- Close Button -->
                            <button type="button" id="closeHobbyFormButton" style="
                                position: absolute;
                                top: 10px;
                                right: 10px;
                                background-color: transparent;
                                color: #f00;
                                border: none;
                                font-size: 18px;
                                font-weight: bold;
                                cursor: pointer;
                            ">×</button>
                            <div id="hobby-fields">
                                <label>What do you like?:</label>
                                <div class="resume-form-group">
                                    <textarea id="hobby" name="hobby" placeholder="e.g. Skiing, Skydiving, Painting"></textarea>
                                </div>
                            </div>
                            <button type="button" id="save-hobby" class="save-button">Save Hobby</button>
                        </div>

                        
                        <div id="courseEntries"></div>
                        <!-- Courses Section -->
                        <div id="courses" class="resume-form-section">
                                <h2>Courses</h2>
                                <p>Add the courses you have completed, along with the details of the institution and duration.</p>
                                <div id="courseForm" style="border: 1px solid #ddd;border-radius: 8px;background-color: #f9f9f9;margin-top: 20px;position: relative;padding: 15px;">
                                    <!-- Close Button -->
                                    <button type="button" id="closeCourseFormButton" style="position: absolute;top: 10px;right: 10px;background-color: transparent;color: #f00;border: none;font-size: 18px;font-weight: bold;cursor: pointer;">×</button>
                            
                                    <div style="margin-bottom: 15px;">
                                        <label>Course Name:</label>
                                        <input type="text" class="form-input" id="courseName" placeholder="e.g., Data Science Certification" style="width: 100%; border-radius: 5px; border: 1px solid #ccc;">
                                    </div>
                                    <div style="margin-bottom: 15px;">
                                        <label>Institution:</label>
                                        <input type="text" class="form-input" id="courseInstitution" placeholder="e.g., Coursera" style="width: 100%; border-radius: 5px; border: 1px solid #ccc;">
                                    </div>
                                    <div style="margin-bottom: 15px;">
                                        <label>Start Date:</label>
                                        <input type="text" class="form-input" id="courseStartDate" placeholder="e.g., Jan 2023" style="width: 100%; border-radius: 5px; border: 1px solid #ccc;">
                                    </div>
                                    <div style="margin-bottom: 15px;">
                                        <label>End Date:</label>
                                        <input type="text" class="form-input" id="courseEndDate" placeholder="e.g., Jun 2023" style="width: 100%; border-radius: 5px; border: 1px solid #ccc;">
                                    </div>
                                    <button type="button" id="saveCourseButton">Save Course</button>
                                </div>
                            
                                <button type="button" id="addCourseButton" style="margin-top: 20px;">+ Add one more course</button>
                            </div>

                        
                         <!-- Existing Internships Entries will load here -->
                        <div id="internshipEntries"></div>
                        <div style="max-width: 800px; margin: auto;" id="internships" class="resume-form-section">
                            <h3 style="color: #333;">Internships</h3>
                           
                            <!-- Form to add new internships -->
                            <div id="internshipForm" style="
                                border: 1px solid #ddd; 
                                border-radius: 8px; 
                                background-color: #f9f9f9; 
                                margin-top: 20px; 
                                position: relative; /* Make this the reference for the close button's positioning */
                                padding: 15px;
                            ">
                            <!-- Close Button -->
                            <button type="button" id="closeFormButton" style="
                                    position: absolute; 
                                    top: 10px; 
                                    right: 10px; 
                                    background-color: transparent; 
                                    color: #f00; 
                                    border: none; 
                                    font-size: 18px; 
                                    font-weight: bold; 
                                    cursor: pointer;
                                ">×</button>

                               
                                <div style="margin-bottom: 15px;">
                                    <label>Job Title:</label>
                                    <input type="text" class="form-input" id="internshipJobTitle" placeholder="e.g., Software Intern" style="width: 100%; border-radius: 5px; border: 1px solid #ccc;">
                                </div>
                                <div style="margin-bottom: 15px;">
                                    <label>Employer:</label>
                                    <input type="text" class="form-input" id="internshipEmployer" placeholder="e.g., Google" style="width: 100%; border-radius: 5px; border: 1px solid #ccc;">
                                </div>
                                <div style="margin-bottom: 15px;">
                                    <label>Start Date:</label>
                                    <input type="date" class="form-input" id="internshipStartDate" style="width: 100%; border-radius: 5px; border: 1px solid #ccc;">
                                </div>
                                <div style="margin-bottom: 15px;">
                                    <label>End Date:</label>
                                    <input type="date" class="form-input" id="internshipEndDate" style="width: 100%; border-radius: 5px; border: 1px solid #ccc;">
                                </div>
                                <div style="margin-bottom: 15px;">
                                    <label>City:</label>
                                    <input type="text" class="form-input" id="internshipCity" placeholder="e.g., San Francisco" style="width: 100%; border-radius: 5px; border: 1px solid #ccc;">
                                </div>
                                <div style="margin-bottom: 15px;">
                                    <label>Description:</label>
                                    <textarea id="internshipDescription" placeholder="e.g., Developed new features for the company's main product" style="width: 100%; border-radius: 5px; border: 1px solid #ccc;"></textarea>
                                </div>
                                <button type="button" id="saveInternshipButton">Add Internship</button>
                            </div>
                        
                            <!-- Button to display internship form -->
                            <button type="button" id="addInternshipButton" style="margin-top: 20px;">Add More Internships</button>
                        </div>
                        
                        
                        <div id="customSectionEntries"></div>
                        <div style="max-width: 800px; margin: auto;" id="customSectionFields" class="resume-form-section">
                            <h3 style="color: #333;">Custom Section</h3>
                          
                            <!-- Form to add new custom experiences -->
                            <div id="customSectionForm" style="
                                border: 1px solid #ddd; 
                                border-radius: 8px; 
                                background-color: #f9f9f9; 
                                margin-top: 20px; 
                                position: relative; /* Make this the reference for the close button's positioning */
                                padding: 15px;
                            ">
                            <!-- Close Button -->
                            <button type="button" id="closeCustomSectionFormButton" style="
                                    position: absolute; 
                                    top: 10px; 
                                    right: 10px; 
                                    background-color: transparent; 
                                    color: #f00; 
                                    border: none; 
                                    font-size: 18px; 
                                    font-weight: bold; 
                                    cursor: pointer;
                                ">×</button>
                           
                            <div style="margin-bottom: 15px;">
                                <label>Aсtivity name, job title, book title etc.</label>
                                <input type="text" class="form-input" id="customSectionJobTitle" placeholder="" style="width: 100%; border-radius: 5px; border: 1px solid #ccc;">
                            </div>
                            <div style="margin-bottom: 15px;">
                                <label>City:</label>
                                <input type="text" class="form-input" id="customSectionCity" placeholder="e.g., San Francisco" style="width: 100%; border-radius: 5px; border: 1px solid #ccc;">
                            </div>
                         
                            <div style="margin-bottom: 15px;">
                                <label>Start & End Date</label>
                                <input type="date" class="form-input" id="customSectionStartDate" style="width: 100%; border-radius: 5px; border: 1px solid #ccc;">
                                 <input type="date" class="form-input" id="customSectionEndDate" style="width: 100%; border-radius: 5px; border: 1px solid #ccc;">
                            </div>
                            <div style="margin-bottom: 15px;">
                                <label>Description:</label>
                                <textarea id="customSectionDescription" placeholder="e.g., Developed new features for the company's main product" style="width: 100%; border-radius: 5px; border: 1px solid #ccc;"></textarea>
                            </div>
                            <button type="button" id="saveCustomSectionButton">Add Custom Item</button>
                        </div>
                        
                        <!-- Button to display form -->
                        <button type="button" id="addCustomSectionButton" style="margin-top: 20px;">Add one more item</button>
                        </div>

                        
                        <div id="extracurricularSectionEntries"></div>
                        <!-- Custom Section Fields -->
                        <div id="extracurricularSectionFields" class="resume-form-section">
                            <h3 style="color: #333;">Extra-curricular Activities</h3>
                        
                            <!-- Form to add new extra-curricular activities -->
                            <div id="extracurricularSectionForm" style="
                                border: 1px solid #ddd; 
                                border-radius: 8px; 
                                background-color: #f9f9f9; 
                                margin-top: 20px; 
                                position: relative; /* Make this the reference for the close button's positioning */
                                padding: 15px;
                            ">
                                <!-- Close Button -->
                                <button type="button" id="closeExtracurricularSectionFormButton" style="
                                    position: absolute; 
                                    top: 10px; 
                                    right: 10px; 
                                    background-color: transparent; 
                                    color: #f00; 
                                    border: none; 
                                    font-size: 18px; 
                                    font-weight: bold; 
                                    cursor: pointer;
                                ">×</button>
                        
                                <!-- Activity Title -->
                                <div style="margin-bottom: 15px;">
                                    <label for="extracurricularSectionJobTitle">Function Title</label>
                                    <input type="text" class="form-input" id="extracurricularSectionJobTitle" placeholder="e.g., Event Coordinator" style="width: 100%; border-radius: 5px; border: 1px solid #ccc;">
                                </div>
                        
                                <!-- Employer -->
                                <div style="margin-bottom: 15px;">
                                    <label for="extracurricularSectionEmployer">Employer</label>
                                    <input type="text" class="form-input" id="extracurricularSectionEmployer" placeholder="e.g., Non-profit Org" style="width: 100%; border-radius: 5px; border: 1px solid #ccc;">
                                </div>
                        
                                <!-- City -->
                                <div style="margin-bottom: 15px;">
                                    <label for="extracurricularSectionCity">City</label>
                                    <input type="text" class="form-input" id="extracurricularSectionCity" placeholder="e.g., San Francisco" style="width: 100%; border-radius: 5px; border: 1px solid #ccc;">
                                </div>
                        
                                <!-- Start & End Date -->
                                <div style="margin-bottom: 15px;">
                                    <label for="extracurricularSectionStartDate">Start & End Date</label>
                                    <input type="date" class="form-input" id="extracurricularSectionStartDate" style="width: 48%; border-radius: 5px; border: 1px solid #ccc; margin-right: 4%;"> 
                                    <input type="date" class="form-input" id="extracurricularSectionEndDate" style="width: 48%; border-radius: 5px; border: 1px solid #ccc;">
                                </div>
                        
                                <!-- Description -->
                                <div style="margin-bottom: 15px;">
                                    <label for="extracurricularSectionDescription">Description</label>
                                    <textarea id="extracurricularSectionDescription" placeholder="e.g., Coordinated charity events for the community" style="width: 100%; border-radius: 5px; border: 1px solid #ccc;"></textarea>
                                </div>
                        
                                <!-- Save Button -->
                                <a id="saveExtracurricularSectionButton">Add Extra-curricular Activity</a>
                            </div>
                        
                            <!-- Button to display form -->
                            <a id="addExtracurricularSectionButton" style="margin-top: 20px;">Add one more activity</a>
                        </div>


                        
                        

                        <div class="resume-menu">
                            <div class="resume-menu-item" data-section="customSectionFields">
                                <img src="custom-section.png" alt="custom-section">
                                <span>Custom Section</span>
                            </div>
                            <div class="resume-menu-item" data-section="extracurricularSectionFields">
                                <img src="extra-curricular-activities.png" alt="extra-curricular-activities">
                                <span>Extra-curricular Activities</span>
                            </div>
                            <div class="resume-menu-item" data-section="languages">
                              <img src="language-icon.png" alt="Languages">
                              <span>Languages</span>
                            </div>
                            <div class="resume-menu-item" data-section="hobbies">
                              <img src="hobby-icon.png" alt="Hobbies">
                              <span>Hobbies</span>
                            </div>
                            <div class="resume-menu-item" data-section="courses">
                              <img src="course-icon.png" alt="Courses">
                              <span>Courses</span>
                            </div>
                            <div class="resume-menu-item" data-section="internships">
                              <img src="internship-icon.png" alt="Internships">
                              <span>Internships</span>
                            </div>
                            
                        </div>
                          
                    </div>
                    
                </form>
            </div>
        </div>
        
        <div class="right-container">
            <!-- Loader while fetching template -->
            <div id="loader" class="loader"></div>
            <!-- Resume preview container -->
            <div id="resumePreview" class="resume-preview"></div>
        </div>
            
        <!-- Preview button for mobile -->
        <div class="preview-button" id="openModalButton">Preview Resume</div>
            
        <!-- Modal for mobile Resume preview -->
        <div class="modal" id="previewModal" style="display:none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0, 0, 0, 0.5); justify-content: center; align-items: center;">
            <div class="modal-content" style="background: white; width: 90%; height: 90%; overflow-y: auto; padding: 20px;">
                <span class="close-modal" onclick="closeModal()">&times;</span>
                <div id="modalContent"></div>
            </div>
        </div>
        
    </div>
    
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            // Initialize Flatpickr for DOB
            flatpickr("#dob", {
                dateFormat: "Y-m-d", // Match the backend format
                maxDate: "today",   // Prevent future dates
                altInput: true,     // User-friendly display
                altFormat: "F j, Y", // e.g., December 3, 1992
                defaultDate: null   // Start with no pre-selected date
            });
    
            // Initialize Flatpickr for Start Date
            flatpickr("#cstartDate", {
                dateFormat: "Y-m",      // Format: Year / Month
                altInput: true,         // User-friendly display
                altFormat: "F Y",       // e.g., December 2024
                defaultDate: null,      // Start with no pre-selected date
                maxDate: "today",       // Prevent future dates
                onChange: function (selectedDates, dateStr) {
                    // Update End Date picker to start from selected start date
                    const endDatePicker = document.querySelector("#cendDate")._flatpickr;
                    if (endDatePicker) {
                        endDatePicker.set("minDate", dateStr); // Set minimum date
                    }
                }
            });
    
            // Initialize Flatpickr for End Date
            flatpickr("#cendDate", {
                dateFormat: "Y-m",      // Format: Year / Month
                altInput: true,         // User-friendly display
                altFormat: "F Y",       // e.g., December 2024
                defaultDate: null,      // Start with no pre-selected date
                minDate: null,          // Start with no minimum date
                maxDate: "today",       // Prevent future dates
                onChange: function (selectedDates, dateStr) {
                    // Optionally, logic to handle changes to end date
                }
            });
        
           // Initialize Flatpickr for Start Year
            flatpickr("#startYear", {
                dateFormat: "Y-m",      // Format: Year and Month
                altInput: true,         // User-friendly display
                altFormat: "F Y",       // e.g., December 2024
                allowInput: true,       // Allow manual input
                defaultDate: null,      // Start with no pre-selected date
                maxDate: "today",       // Prevent future dates
                onChange: function (selectedDates, dateStr) {
                    // Update End Year picker to start from the selected start date
                    const endYearPicker = document.querySelector("#endYear")._flatpickr;
                    if (endYearPicker) {
                        endYearPicker.set("minDate", dateStr); // Set minimum date for end year
                    }
                }
            });
        
            // Initialize Flatpickr for End Year
            flatpickr("#endYear", {
                dateFormat: "Y-m",      // Format: Year and Month
                altInput: true,         // User-friendly display
                altFormat: "F Y",       // e.g., December 2024
                allowInput: true,       // Allow manual input
                defaultDate: null,      // Start with no pre-selected date
                maxDate: "today",       // Prevent future dates
                minDate: null           // No restriction on minimum date initially
            });
        
    });
    
        function previewUploadedImage(event) {
            const file = event.target.files[0];
            const previewImage = document.getElementById('previewImage');
            const placeholderIcon = document.getElementById('placeholderIcon');
                    
            if (file) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    previewImage.src = e.target.result;
                    previewImage.style.display = 'block';
                    placeholderIcon.style.display = 'none';
                    };
                    reader.readAsDataURL(file);
                }
            }
            
    </script>
    
    <script>
        $(document).ready(function () {
            let educationList = [];
            let editingEducationIndex = null;
        
            // Fetch education data
            function fetchEducationData() {
                const templateId = '<?=$this->uri->segment(2)?>';
                $.ajax({
                    url: '<?= base_url("career-services/ResumeBuilder/fetch_education_data") ?>',
                    type: 'POST',
                    data: { template_id: templateId },
                    dataType: 'json',
                    success: function (response) {
                        if (response.success) {
                            educationList = response.educationList || [];
                            renderEducationEntries();
                            console.log("Education data fetched successfully.");
                        } else {
                            console.error("Failed to fetch education data:", response.message);
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error("Error fetching education data:", error);
                    }
                });
            }
        
            fetchEducationData();
        
            // Render education entries
            function renderEducationEntries() {
                const educationEntriesContainer = $("#educationEntries");
                educationEntriesContainer.empty();
        
                educationList.forEach((entry, index) => {
                    educationEntriesContainer.append(`
                        <div id="${entry.id}" style="border: 1px solid #ddd; padding: 15px; border-radius: 8px; background-color: #f9f9f9; margin-bottom: 10px;">
                            <h4 style="margin: 0 0 10px;">${entry.degree} at ${entry.institution}</h4>
                            <p>Start Year: ${entry.startYear} | End Year: ${entry.endYear}</p>
                            <button type="button" class="editEducationButton" data-index="${index}" style="margin-right: 10px;">Edit</button>
                            <button type="button" class="deleteEducationButton" data-index="${index}">Delete</button>
                        </div>
                    `);
                });
            }
        
            // Add new education
            $("#addEducationButton").on("click", function () {
                clearEducationForm();
                $("#educationForm").show();
                $("#saveEducationButton").text("Add Education");
            });
        
            // Save or Update education entry
            $("#saveEducationButton").on("click", function () {
                const educationData = {
                    degree: $("#degree").val().trim(),
                    institution: $("#institution").val().trim(),
                    startYear: $("#startYear").val().trim(),
                    endYear: $("#endYear").val().trim(),
                    fieldOfStudy: $("#fieldOfStudy").val().trim(),
                };
        
                //if (!educationData.degree || !educationData.institution || !educationData.startYear || (!$("#currentlyStudying").is(":checked") && !educationData.endYear)) {
                   // alert("Please fill in all required fields.");
                    //return;
                //}
        
                if ($("#currentlyStudying").is(":checked")) {
                    educationData.endYear = "Present";
                }
        
                if (editingEducationIndex !== null) {
                    educationData.id = educationList[editingEducationIndex].id; // Update ID for existing entry
                    educationList[editingEducationIndex] = educationData; // Update existing entry
                } else {
                    educationData.id = generateUniqueId(); // Unique ID for new entry
                    educationList.push(educationData); // Add new education entry
                }
        
                renderEducationEntries();
                updateEducationsInDatabase(); // Update server with education data
                clearEducationForm();
            });
        
            function generateUniqueId() {
                return 'education_' + Date.now() + '_' + Math.floor(Math.random() * 1000);
            }
        
            function updateEducationsInDatabase() {
                const templateId = '<?=$this->uri->segment(2)?>';
                const placeholders = { education: educationList };
        
                $.ajax({
                    url: '<?= base_url("career-services/ResumeBuilder/save_educations") ?>',
                    type: 'POST',
                    data: { template_id: templateId, placeholders: JSON.stringify(placeholders) },
                    dataType: 'json',
                    success: function (response) {
                        console.log("Server response:", response);
                    },
                    error: function (xhr, status, error) {
                        console.error("Error:", xhr.responseText || error);
                    }
                });
            }
        
            $(document).on("click", ".editEducationButton", function () {
                const index = $(this).data("index");
                const educationData = educationList[index];
                editingEducationIndex = index;
        
                populateEducationForm(educationData);
                $("#educationForm").show();
                $("#saveEducationButton").text("Update Education");
            });
        
            $(document).on("click", ".deleteEducationButton", function () {
                const index = $(this).data("index");
                const educationId = educationList[index].id;
        
                educationList.splice(index, 1);
                renderEducationEntries();
        
                deleteEducationFromDatabase(educationId);
            });
        
            function deleteEducationFromDatabase(educationId) {
                const templateId = '<?=$this->uri->segment(2)?>';
                $.ajax({
                    url: '<?= base_url("career-services/ResumeBuilder/delete_education") ?>',
                    type: 'POST',
                    contentType: 'application/json',
                    data: JSON.stringify({ id: educationId, template_id: templateId }),
                    dataType: 'json',
                    success: function (response) {
                        if (response.success) {
                            console.log(response.message);
                        } else {
                            console.error("Failed to delete education:", response.message);
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error("Error:", xhr.responseText || error);
                    }
                });
            }
        
            function populateEducationForm(data) {
                $("#degree").val(data.degree);
                $("#institution").val(data.institution);
                $("#startYear").val(data.startYear);
                $("#endYear").val(data.endYear === "Present" ? "" : data.endYear);
                $("#fieldOfStudy").val(data.fieldOfStudy);
                $("#currentlyStudying").prop("checked", data.endYear === "Present");
                $("#educationForm").show();
            }
        
            function clearEducationForm() {
                $("#degree, #institution, #startYear, #endYear, #fieldOfStudy").val("");
                $("#currentlyStudying").prop("checked", false);
                $("#educationForm").hide();
                $("#saveEducationButton").text("Add Education");
                editingEducationIndex = null;
            }
        
            $(document).on("click", "#closeEducationFormButton", function () {
                clearEducationForm();
            });
        });

        $(document).ready(function () {
            let employmentList = [];
            let editingEmploymentIndex = null;
        
            // Fetch employment data
            function fetchEmploymentData() {
                const templateId = '<?=$this->uri->segment(2)?>';
                $.ajax({
                    url: '<?= base_url("career-services/ResumeBuilder/fetch_employment_data") ?>',
                    type: 'POST',
                    data: { template_id: templateId },
                    dataType: 'json',
                    success: function (response) {
                        if (response.success) {
                            employmentList = response.employmentList || [];
                            renderEmploymentEntries();
                            console.log("Employment data fetched successfully.");
                        } else {
                            console.error("Failed to fetch employment data:", response.message);
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error("Error fetching employment data:", error);
                    }
                });
            }
        
            fetchEmploymentData();
        
            // Render employment entries
            function renderEmploymentEntries() {
                const employmentEntriesContainer = $("#employmentEntries");
                employmentEntriesContainer.empty();
        
                employmentList.forEach((entry, index) => {
                    employmentEntriesContainer.append(`
                        <div id="${entry.id}" style="border: 1px solid #ddd; padding: 15px; border-radius: 8px; background-color: #f9f9f9; margin-bottom: 10px;">
                            <h4 style="margin: 0 0 10px;">${entry.jobTitle} at ${entry.employer}</h4>
                            <p>Start Date: ${entry.startDate} | End Date: ${entry.endDate}</p>
                            <button type="button" class="editEmploymentButton" data-index="${index}" style="margin-right: 10px;">Edit</button>
                            <button type="button" class="deleteEmploymentButton" data-index="${index}">Delete</button>
                        </div>
                    `);
                });
            }
        
            // Add new employment
            $("#addEmploymentButton").on("click", function () {
                clearEmploymentForm();
                $("#employmentForm").show();
                $("#saveEmploymentButton").text("Add Employment");
            });
        
            // Save or Update employment entry
            $("#saveEmploymentButton").on("click", function () {
                const employmentData = {
                    jobTitle: $("#jobTitle").val().trim(),
                    employer: $("#employmentCompany").val().trim(),
                    startDate: $("#employmentStartDate").val().trim(),
                    endDate: $("#employmentEndDate").val().trim(),
                    workLocation: $("#workLocation").val().trim(),
                    description: $("#description").val().trim(),
                };
        
                //if (!employmentData.jobTitle || !employmentData.employer || !employmentData.startDate || (!$("#currentWork").is(":checked") && !employmentData.endDate)) {
                    //alert("Please fill in all required fields.");
                    //return;
                //}
        
                if ($("#currentWork").is(":checked")) {
                    employmentData.endDate = "Present";
                }
        
                if (editingEmploymentIndex !== null) {
                    employmentData.id = employmentList[editingEmploymentIndex].id; // Update ID for existing entry
                    employmentList[editingEmploymentIndex] = employmentData; // Update existing entry
                } else {
                    employmentData.id = generateUniqueId(); // Unique ID for new entry
                    employmentList.push(employmentData); // Add new employment entry
                }
        
                renderEmploymentEntries();
                updateEmploymentsInDatabase(); // Update server with employment data
                clearEmploymentForm();
            });
        
            function generateUniqueId() {
                return 'employment_' + Date.now() + '_' + Math.floor(Math.random() * 1000);
            }
        
            function updateEmploymentsInDatabase() {
                const templateId = '<?=$this->uri->segment(2)?>';
                const placeholders = { employment: employmentList };
        
                $.ajax({
                    url: '<?= base_url("career-services/ResumeBuilder/save_employments") ?>',
                    type: 'POST',
                    data: { template_id: templateId, placeholders: JSON.stringify(placeholders) },
                    dataType: 'json',
                    success: function (response) {
                        console.log("Server response:", response);
                    },
                    error: function (xhr, status, error) {
                        console.error("Error:", xhr.responseText || error);
                    }
                });
            }
        
            $(document).on("click", ".editEmploymentButton", function () {
                const index = $(this).data("index");
                const employmentData = employmentList[index];
                editingEmploymentIndex = index;
        
                populateEmploymentForm(employmentData);
                $("#employmentForm").show();
                $("#saveEmploymentButton").text("Update Employment");
            });
        
            $(document).on("click", ".deleteEmploymentButton", function () {
                const index = $(this).data("index");
                const employmentId = employmentList[index].id;
        
                employmentList.splice(index, 1);
                renderEmploymentEntries();
        
                deleteEmploymentFromDatabase(employmentId);
            });
        
            function deleteEmploymentFromDatabase(employmentId) {
                const templateId = '<?=$this->uri->segment(2)?>';
                $.ajax({
                    url: '<?= base_url("career-services/ResumeBuilder/delete_employment") ?>',
                    type: 'POST',
                    contentType: 'application/json',
                    data: JSON.stringify({ id: employmentId, template_id: templateId }),
                    dataType: 'json',
                    success: function (response) {
                        if (response.success) {
                            console.log(response.message);
                        } else {
                            console.error("Failed to delete employment:", response.message);
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error("Error:", xhr.responseText || error);
                    }
                });
            }
        
            function populateEmploymentForm(data) {
                $("#jobTitle").val(data.jobTitle);
                $("#employmentCompany").val(data.employer);
                $("#employmentStartDate").val(data.startDate);
                $("#employmentEndDate").val(data.endDate === "Present" ? "" : data.endDate);
                $("#workLocation").val(data.workLocation),
                $("#description").val(data.description);
                $("#currentWork").prop("checked", data.endDate === "Present");
                $("#employmentForm").show();
            }
        
            function clearEmploymentForm() {
                $("#jobTitle, #employmentCompany, #employmentStartDate, #employmentEndDate, #workLocation, #description").val("");
                $("#currentWork").prop("checked", false);
                $("#employmentForm").hide();
                $("#saveEmploymentButton").text("Add Employment");
                editingEmploymentIndex = null;
            }
        
            $(document).on("click", "#closeEmploymentFormButton", function () {
                clearEmploymentForm();
            });
        });

        $(document).ready(function () {
            let hobbyData = ''; // To store the hobby data
        
            // Fetch hobby data
            function fetchHobbyData() {
                const templateId = '<?=$this->uri->segment(2)?>';
                $.ajax({
                    url: '<?= base_url("career-services/ResumeBuilder/fetch_hobby_data") ?>',
                    type: 'POST',
                    data: { template_id: templateId },
                    dataType: 'json',
                    success: function (response) {
                        if (response.success) {
                            hobbyData = response.hobbyData || '';
                            renderHobbyEntry();
                            console.log("Hobby data fetched successfully.");
                        } else {
                            console.error("Failed to fetch hobby data:", response.message);
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error("Error fetching hobby data:", error);
                    }
                });
            }
        
            // Render hobby entry with Edit and Delete buttons
            function renderHobbyEntry() {
                const hobbyEntriesContainer = $("#hobbiesEntries");
                hobbyEntriesContainer.empty(); // Clear any existing content
        
                if (hobbyData) {
                    // Display hobby data in a div with Edit and Delete buttons
                    hobbyEntriesContainer.html(`
                        <div id="hobby-entry" style="border: 1px solid #ddd; padding: 15px; border-radius: 8px; background-color: #f9f9f9; margin-bottom: 10px;">
                            <p id="hobby-text">${hobbyData}</p>
                            <button type="button" id="edit-hobby" style="margin-right: 10px;">Edit</button>
                            <button type="button" id="delete-hobby">Delete</button>
                        </div>
                    `);
                }
            }
        
            // Handle click on the Edit button
            $(document).on('click', '#edit-hobby', function () {
                const hobbyText = $("#hobby-text").text(); // Get the hobby text
                $("#hobby").val(hobbyText); // Set the hobby data into the textarea
        
                // Change the button text to "Update Hobby"
                 $("#save-hobby").text("Update Hobby");
                 $("#hobbies").addClass("active");
            });
        
            // Fetch hobby data when the page is ready
            fetchHobbyData();
        
            // Save or Update Hobby
            $("#save-hobby").on("click", function () {
                const hobbyText = $("#hobby").val().trim();
        
                //if (!hobbyText) {
                    //alert("Please enter a hobby.");
                    //return;
                //}
        
                const templateId = '<?=$this->uri->segment(2)?>';
        
                // AJAX request to save or update hobby
                $.ajax({
                    url: '<?= base_url("career-services/ResumeBuilder/save_hobby") ?>',
                    type: 'POST',
                    data: {
                        template_id: templateId,
                        hobby: hobbyText
                    },
                    dataType: 'json',
                    success: function (response) {
                        if (response.success) {
                            alert(response.message);
                            hobbyData = hobbyText; // Update the local hobbyData
                            renderHobbyEntry(); // Re-render the hobby entry to reflect changes
                        } else {
                            alert(response.message);
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error("Error saving hobby:", error);
                    }
                });
            });
            
             // Handle close button click to hide the hobby form
            $("#closeHobbyFormButton").on("click", function () {
               $("#hobbies").removeClass("active");
            });
            
        });

        $(document).ready(function () {
            let courseList = [];
            let editingCourseIndex = null;
        
            // Fetch course data
            function fetchCourseData() {
                const templateId = '<?=$this->uri->segment(2)?>';
                $.ajax({
                    url: '<?= base_url("career-services/ResumeBuilder/fetch_course_data") ?>',
                    type: 'POST',
                    data: { template_id: templateId },
                    dataType: 'json',
                    success: function (response) {
                        if (response.success) {
                            courseList = response.courseList || [];
                            renderCourseEntries();
                            console.log("Course data fetched successfully.");
                        } else {
                            console.error("Failed to fetch course data:", response.message);
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error("Error fetching course data:", error);
                    }
                });
            }
        
            fetchCourseData();
        
            // Render course entries
            function renderCourseEntries() {
                const courseEntriesContainer = $("#courseEntries");
                courseEntriesContainer.empty();
        
                courseList.forEach((entry, index) => {
                    courseEntriesContainer.append(`
                        <div id="${entry.id}" style="border: 1px solid #ddd; padding: 15px; border-radius: 8px; background-color: #f9f9f9; margin-bottom: 10px;">
                            <h4 style="margin: 0 0 10px;">${entry.courseName} - ${entry.institution}</h4>
                            <p>Start Date: ${entry.startDate} | End Date: ${entry.endDate}</p>
                            <button type="button" class="editCourseButton" data-index="${index}" style="margin-right: 10px;">Edit</button>
                            <button type="button" class="deleteCourseButton" data-index="${index}">Delete</button>
                        </div>
                    `);
                });
            }
        
            // Add new course
            $("#addCourseButton").on("click", function () {
                clearCourseForm();
                $("#courseForm").show();
                $("#saveCourseButton").text("Add Course");
            });
        
            function generateUniqueId() {
                return 'course_' + Date.now() + '_' + Math.floor(Math.random() * 1000);
            }
        
            $("#saveCourseButton").on("click", function () {
                const courseData = {
                    courseName: $("#courseName").val().trim(),
                    institution: $("#courseInstitution").val().trim(),
                    startDate: $("#courseStartDate").val().trim(),
                    endDate: $("#courseEndDate").val().trim(),
                };
        
                if (!courseData.courseName || !courseData.institution || !courseData.startDate || !courseData.endDate) {
                    alert("Please fill in all required fields.");
                    return;
                }
        
                if (editingCourseIndex !== null) {
                    courseData.id = courseList[editingCourseIndex].id; // Send ID for update
                    courseList[editingCourseIndex] = courseData; // Update existing entry
                } else {
                    courseData.id = generateUniqueId(); // Ensure unique ID for new entry
                    courseList.push(courseData); // Add new course entry
                }
        
                renderCourseEntries();
                updateCoursesInDatabase(courseData); // Update server with course data
                clearCourseForm();
            });
        
            function updateCoursesInDatabase(courseData) {
                const templateId = '<?=$this->uri->segment(2)?>';
                const placeholders = {
                    courses: courseList,
                };
        
                $.ajax({
                    url: '<?= base_url("career-services/ResumeBuilder/save_courses") ?>',
                    type: 'POST',
                    data: { template_id: templateId, placeholders: JSON.stringify(placeholders) },
                    dataType: 'json',
                    success: function (response) {
                        console.log("Server response:", response);
                    },
                    error: function (xhr, status, error) {
                        console.error("Error:", xhr.responseText || error);
                    }
                });
            }
        
            $(document).on("click", ".editCourseButton", function () {
                const index = $(this).data("index");
                const courseData = courseList[index];
                editingCourseIndex = index;
        
                populateCourseForm(courseData);
                $("#courses").addClass("active");
            });
        
            $(document).on("click", ".deleteCourseButton", function () {
                const index = $(this).data("index");
                const courseId = courseList[index].id;
        
                courseList.splice(index, 1);
                renderCourseEntries();
        
                deleteCourseFromDatabase(courseId);
            });
        
            function deleteCourseFromDatabase(courseId) {
                const templateId = '<?=$this->uri->segment(2)?>';
                $.ajax({
                    url: '<?= base_url("career-services/ResumeBuilder/delete_course") ?>',
                    type: 'POST',
                    contentType: 'application/json',
                    data: JSON.stringify({ id: courseId, template_id: templateId }),
                    dataType: 'json',
                    success: function (response) {
                        if (response.success) {
                            console.log(response.message);
                        } else {
                            console.error("Failed to delete course:", response.message);
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error("Error:", xhr.responseText || error);
                    }
                });
            }
        
            function populateCourseForm(data) {
                $("#courseName").val(data.courseName);
                $("#courseInstitution").val(data.institution);
                $("#courseStartDate").val(data.startDate);
                $("#courseEndDate").val(data.endDate);
                $("#courseForm").show();
                $("#saveCourseButton").text("Update Course");
            }
        
            function clearCourseForm() {
                $("#courseName").val('');
                $("#courseInstitution").val('');
                $("#courseStartDate").val('');
                $("#courseEndDate").val('');
                $("#courseForm").hide();
                $("#saveCourseButton").text("Add Course");
                editingCourseIndex = null;
            }
        
            $(document).on("click", "#closeCourseFormButton", function () {
                $("#courseForm").fadeOut();
                clearCourseForm();
            });
            
        });
    
        $(document).ready(function () {
            let languageList = [];
            let editingLanguageIndex = null;
        
            // Fetch language data
            function fetchLanguageData() {
                const templateId = '<?=$this->uri->segment(2)?>';
                $.ajax({
                    url: '<?= base_url("career-services/ResumeBuilder/fetch_language_data") ?>',
                    type: 'POST',
                    data: { template_id: templateId },
                    dataType: 'json',
                    success: function (response) {
                        if (response.success) {
                            languageList = response.languageList || [];
                            renderLanguageEntries();
                            console.log("Language data fetched successfully.");
                        } else {
                            console.error("Failed to fetch language data:", response.message);
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error("Error fetching language data:", error);
                    }
                });
            }
        
            fetchLanguageData();
        
            // Render language entries
            function renderLanguageEntries() {
                const languageEntriesContainer = $("#languageEntries");
                languageEntriesContainer.empty();
        
                languageList.forEach((entry, index) => {
                    languageEntriesContainer.append(`
                        <div id="${entry.id}" style="border: 1px solid #ddd; padding: 15px; border-radius: 8px; background-color: #f9f9f9; margin-bottom: 10px;">
                            <h4 style="margin: 0 0 10px;">${entry.language} (${entry.proficiency})</h4>
                            <button type="button" class="editLanguageButton" data-index="${index}" style="margin-right: 10px;">Edit</button>
                            <button type="button" class="deleteLanguageButton" data-index="${index}">Delete</button>
                        </div>
                    `);
                });
            }
        
            // Add new language
            $("#addLanguageButton").on("click", function () {
                clearLanguageForm();
                $("#languageForm").show();
                $("#saveLanguageButton").text("Add");
            });
        
            function generateUniqueId() {
                return 'lang_' + Date.now() + '_' + Math.floor(Math.random() * 1000);
            }
        
            $("#saveLanguageButton").on("click", function () {
                const languageData = {
                    language: $("#languageName").val().trim(),
                    proficiency: $("#languageProficiency").val(),
                };
        
                if (!languageData.language || !languageData.proficiency) {
                    alert("Please fill in all required fields.");
                    return;
                }
        
                if (editingLanguageIndex !== null) {
                    languageData.id = languageList[editingLanguageIndex].id; // Send ID for update
                    languageList[editingLanguageIndex] = languageData;  // Update existing entry in the list
                } else {
                    // Add new language entry to the list
                    languageData.id = generateUniqueId();  // Ensure unique ID for new entry
                    languageList.push(languageData);
                }
        
                renderLanguageEntries();
                updateLanguagesInDatabase(languageData); // Pass the specific language data
                clearLanguageForm();
            });
        
            function updateLanguagesInDatabase(languageData) {
                const templateId = '<?=$this->uri->segment(2)?>';
                const placeholders = {
                    languages: languageList
                };
        
                $.ajax({
                    url: '<?= base_url("career-services/ResumeBuilder/save_languages") ?>',
                    type: 'POST',
                    data: { template_id: templateId, placeholders: JSON.stringify(placeholders) },
                    dataType: 'json',
                    success: function (response) {
                        console.log("Server response:", response);
                    },
                    error: function (xhr, status, error) {
                        console.error("Error:", xhr.responseText || error);
                    }
                });
            }
        
            $(document).on("click", ".editLanguageButton", function () {
                const index = $(this).data("index");
                const languageData = languageList[index];
                editingLanguageIndex = index;
        
                populateLanguageForm(languageData);
                $("#languages").addClass("active");
            });
        
            $(document).on("click", ".deleteLanguageButton", function () {
                const index = $(this).data("index");
                const languageId = languageList[index].id;
        
                languageList.splice(index, 1);
                renderLanguageEntries();
        
                deleteLanguageFromDatabase(languageId);
            });
        
            function deleteLanguageFromDatabase(languageId) {
                const templateId = '<?=$this->uri->segment(2)?>';
                $.ajax({
                    url: '<?= base_url("career-services/ResumeBuilder/delete_language") ?>',
                    type: 'POST',
                    contentType: 'application/json',
                    data: JSON.stringify({ id: languageId, template_id: templateId }),
                    dataType: 'json',
                    success: function (response) {
                        if (response.success) {
                            console.log(response.message);
                        } else {
                            console.error("Failed to delete language:", response.message);
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error("Error:", xhr.responseText || error);
                    }
                });
            }
        
            function populateLanguageForm(data) {
                $("#languageName").val(data.language);
                $("#languageProficiency").val(data.proficiency);
                $("#languageForm").show();
                $("#saveLanguageButton").text("Update");
            }
        
            function clearLanguageForm() {
                $("#languageName").val('');
                $("#languageProficiency").val('');
                $("#languageForm").hide();
                $("#saveLanguageButton").text("Add");
                editingLanguageIndex = null;
            }
        
            $(document).on("click", "#closeLanguageFormButton", function () {
                $("#languageForm").fadeOut();
                clearLanguageForm();
            });
        });

        $(document).ready(function () {
        
        // Scroll to the section when menu item is clicked
        $(".resume-menu-item").click(function () {
            const sectionId = $(this).data("section");
            const section = $("#" + sectionId);
    
            $('html, body').animate({
                scrollTop: section.offset().top
            }, 500);
    
            section.addClass("active");
        });
    
        let internshipList = [];
        let editingInternshipIndex = null;

        // Fetch internships data
        function fetchInternshipsData() {
            const templateId = '<?=$this->uri->segment(2)?>';
            $.ajax({
                url: '<?= base_url("career-services/ResumeBuilder/fetch_internship_data") ?>',
                type: 'POST',
                data: { template_id: templateId },
                dataType: 'json',
                success: function (response) {
                    if (response.success) {
                        internshipList = response.internshipList || [];
                        renderInternshipEntries();
                        console.log("Internship data fetched successfully.");
                    } else {
                        console.error("Failed to fetch internship data:", response.message);
                    }
                },
                error: function (xhr, status, error) {
                    console.error("Error fetching internship data:", error);
                }
            });
        }
    
        fetchInternshipsData();
    
        // Render internship entries
        function renderInternshipEntries() {
            const internshipEntriesContainer = $("#internshipEntries");
            internshipEntriesContainer.empty();
    
            internshipList.forEach((entry, index) => {
                internshipEntriesContainer.append(`
                    <div id="${entry.id}" style="border: 1px solid #ddd; padding: 15px; border-radius: 8px; background-color: #f9f9f9; margin-bottom: 10px;">
                        <h4 style="margin: 0 0 10px;">${entry.job_title} at ${entry.employer}</h4>
                        <p style="margin: 0; color: #666;">${entry.start_date} - ${entry.end_date}, ${entry.city}</p>
                        <p style="margin: 10px 0; color: #333;">${entry.description}</p>
                        <button type="button" class="editInternshipButton" data-index="${index}" style="margin-right: 10px;">Edit</button>
                        <button type="button" class="deleteInternshipButton" data-index="${index}">Delete</button>
                    </div>
                `);
            });
        }
    
        // Add new internship
        $("#addInternshipButton").on("click", function () {
            clearInternshipForm();
            $("#internshipForm").show();
            $("#saveInternshipButton").text("Add");
        });
        
        function generateUniqueId() {
            return 'intern_' + Date.now() + '_' + Math.floor(Math.random() * 1000);
        }
        
        $("#saveInternshipButton").on("click", function () {
            const internshipData = {
                job_title: $("#internshipJobTitle").val().trim(),
                employer: $("#internshipEmployer").val().trim(),
                start_date: $("#internshipStartDate").val(),
                end_date: $("#internshipEndDate").val(),
                city: $("#internshipCity").val().trim(),
                description: $("#internshipDescription").val().trim(),
            };
        
            if (!internshipData.job_title || !internshipData.employer) {
                alert("Please fill in all required fields.");
                return;
            }
        
            if (editingInternshipIndex !== null) {
                internshipData.id = internshipList[editingInternshipIndex].id; // Send ID for update
                internshipList[editingInternshipIndex] = internshipData;  // Update existing entry in the list
            } else {
                // Add new internship entry to the list
                internshipData.id = generateUniqueId();  // Ensure unique ID for new entry
                internshipList.push(internshipData);
            }
        
            renderInternshipEntries();
            updateInternshipsInDatabase(internshipData); // Pass the specific internship data
            clearInternshipForm();
        });

        function updateInternshipsInDatabase(internshipData) {
            const templateId = '<?=$this->uri->segment(2)?>';
            const placeholders = {
                internships: internshipList
            };
        
            // Update the internship in the list
            if (internshipData.id) {
                const index = internshipList.findIndex(entry => entry.id === internshipData.id);
                if (index !== -1) {
                    internshipList[index] = internshipData;
                }
            }
        
            $.ajax({
                url: '<?= base_url("career-services/ResumeBuilder/save_internships_labels") ?>',
                type: 'POST',
                data: { template_id: templateId, placeholders: JSON.stringify(placeholders)},
                dataType: 'json',
                success: function (response) {
                    console.log("Server response:", response);
                },
                error: function (xhr, status, error) {
                    console.error("Error:", xhr.responseText || error);
                }
            });
        }

        $(document).on("click", ".editInternshipButton", function () {
            const index = $(this).data("index");
            const internshipData = internshipList[index];
            editingInternshipIndex = index;
        
            // Populate the form with the selected internship data
            populateInternshipForm(internshipData);
        
            // Add the 'active' class to the section
            $("#internships").addClass("active");
        
        });
    
       
        $(document).on("click", ".deleteInternshipButton", function () {
            const index = $(this).data("index");
            const internshipId = internshipList[index].id;
        
            // Remove the entry from the UI
            internshipList.splice(index, 1);
            renderInternshipEntries();
        
            // Send delete request to the server
            deleteInternshipFromDatabase(internshipId);
        });
        
        // Function to handle delete request
        function deleteInternshipFromDatabase(internshipId) {
            const templateId = '<?=$this->uri->segment(2)?>'; // Template ID for context
            $.ajax({
                url: '<?= base_url("career-services/ResumeBuilder/delete_internship") ?>',
                type: 'POST',
                contentType: 'application/json',
                data: JSON.stringify({ id: internshipId, template_id: templateId }),
                dataType: 'json',
                success: function (response) {
                    if (response.success) {
                        console.log(response.message);
                    } else {
                        console.error("Failed to delete internship:", response.message);
                    }
                },
                error: function (xhr, status, error) {
                    console.error("Error:", xhr.responseText || error);
                }
            });
        }

    
        function populateInternshipForm(data) {
            $("#internshipJobTitle").val(data.job_title);
            $("#internshipEmployer").val(data.employer);
            $("#internshipStartDate").val(data.start_date);
            $("#internshipEndDate").val(data.end_date);
            $("#internshipCity").val(data.city);
            $("#internshipDescription").val(data.description);
            $("#internshipForm").show();
            $("#saveInternshipButton").text("Update");
        }
    
        function clearInternshipForm() {
            $("#internshipJobTitle").val('');
            $("#internshipEmployer").val('');
            $("#internshipStartDate").val('');
            $("#internshipEndDate").val('');
            $("#internshipCity").val('');
            $("#internshipDescription").val('');
            $("#internshipForm").hide();
            $("#saveInternshipButton").text("Add");
            editingInternshipIndex = null;
        }
        
         // Close the internship form when the "X" button is clicked
        $(document).on("click", "#closeFormButton", function () {
            $("#internshipForm").fadeOut(); // Hide the form
            clearInternshipForm(); // Clear form fields
        });

    });
        
    </script>
    
    <script>
    
    function closeModal() {
        $("#previewModal").css("display", "none");
    }

    /**
     * Debounce function to delay execution.
     */
    function debounce(func, delay) {
        let debounceTimer;
        return function (...args) {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => func.apply(this, args), delay);
        };
    }

    $("#closeModalButton").on("click", function () {
        closeModal();
    });

    $(document).ready(function () {
        /**
         * Get the resume ID from the URL.
         */
        function getResumeIdFromUrl() {
            const pathSegments = window.location.pathname.split("/");
            return pathSegments[2]; // Adjust as per URL structure
        }
        
        function fetchCandidateDetails() {
            $.ajax({
                url: "<?= base_url('career-services/ResumeBuilder/extractCandidateDetails') ?>",
                method: "GET",
                dataType: "json",
                success: function (response) {
                    if (response.success) {
                        console.log("Candidate Details:", response.data);
                        populateFormWithCandidateDetails(response.data); // Populate the form
                    } else {
                        console.error("Error:", response.message);
                    }
                },
                error: function () {
                    alert("Error loading candidate details.");
                }
            });
        }
        
        // Initialize functions on page load
        fetchCandidateDetails();
        
       /**
         * Populate the form fields with the extracted data.
         */
        function populateFormWithCandidateDetails (data) {
            $("#name").val(data.name || "");
            $("#email").val(data.email || "");
            $("#phone").val(data.phone || "");
            $("#jobProfile").val(data.jobProfile || "");
            $("#city").val(data.city || "");
            $("#country").val(data.country || "");
            $("#postal").val(data.postal || "");
            $("#address").val(data.address || "");
            $("#resume_headline").val(data.resume_headline || "");
            // If dob exists, update the flatpickr date
           
            if (data.dob) {
                const dobPicker = document.querySelector("#dob")._flatpickr;
                if (dobPicker) {
                    dobPicker.setDate(data.dob, true); // Set the date and trigger the input update
                }
            }
        }
       
        function updatePreview() {
            const formData = new FormData(document.getElementById('resumeForm'));
            fetch('<?= base_url("career-services/ResumeBuilder/preview"); ?>', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                    if (!response.ok) {
                        throw new Error('Failed to load preview');
                    }
                    return response.text();
                })
            .then(html => {
                    document.getElementById('resumePreview').innerHTML = html;
                })
            .catch(error => {
                    console.error(error);
                    alert("Error loading preview. Please try again.");
                });
        }

        // Debounced update preview on input change
        const debouncedUpdatePreview = debounce(updatePreview, 500);

        // Bind input events for form fields (initial and dynamically added)
        function bindInputEvents() {
            $("input, textarea").on("input", debouncedUpdatePreview);
        }

        // Initial binding for already existing inputs
        bindInputEvents();

        // Toggle additional details section visibility
        $("#toggleDetails").on("click", function (e) {
            e.preventDefault();
            const button = $(this);
            const additionalDetails = $("#additionalDetails");
            additionalDetails.toggle(); // Show or hide details
            button.text(additionalDetails.is(":visible") ? "Hide Additional Details" : "Show Additional Details");
        });
    
        function updateResumeTemplate() {
            const formData = new FormData(document.getElementById('resumeForm'));
        
            // AJAX call to update resume template
            fetch('<?=base_url("career-services/ResumeBuilder/update_resume_template")?>', {
                method: 'POST',
                body: formData,
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('resumePreview').innerHTML = data.updatedHtml;
                } else {
                    console.error(data.message);
                }
            })
            .catch(error => {
                console.error('Error updating resume:', error);
            });
        }
        
        // Add event listeners to form inputs
        document.querySelectorAll('#resumeForm input, #resumeForm textarea, #resumeForm select').forEach(input => {
            input.addEventListener('input', updateResumeTemplate); // Trigger on input changes
        });

        /**
         * Load the resume template.
         */
        // Modal functionality for mobile preview
        $("#openModalButton").on("click", function () {
            openModal();
        });

        function openModal() {
            $("#previewModal").css("display", "flex");
            loadTemplate(); // Load template content inside modal
        }
        
        // Initial preview update
        updatePreview();
        
    });
    
    $(document).ready(function () {
        let linksList = [];
        let editingIndex = null;
    
        // Fetch social links data
        function fetchLinksData() {
            const templateId = '<?=$this->uri->segment(2)?>';
    
            $.ajax({
                url: '<?= base_url("career-services/ResumeBuilder/fetch_links_data") ?>',
                type: 'POST',
                data: { template_id: templateId },
                dataType: 'json',
                success: function (response) {
                    if (response.success) {
                        linksList = response.linksList || [];
                        renderLinksEntries();
                        console.log("Social links data fetched successfully.");
                    } else {
                        console.error("Failed to fetch links data:", response.message);
                    }
                },
                error: function (xhr, status, error) {
                    console.error("Error fetching links data:", error);
                }
            });
        }
    
        fetchLinksData();
    
        // Render social links entries
        function renderLinksEntries() {
            const linksEntriesContainer = $("#linksEntries");
            linksEntriesContainer.empty();
    
            linksList.forEach((entry, index) => {
                linksEntriesContainer.append(`
                    <div id="linkEntry-${index}" style="border: 1px solid #ddd; padding: 15px; border-radius: 8px; background-color: #f9f9f9; margin-bottom: 10px;">
                        <h4 style="margin: 0 0 10px;">${entry.label}</h4>
                        <p style="margin: 0; color: #666;">${entry.url}</p>
                        <button type="button" class="editLinkButton" data-index="${index}" style="margin-right: 10px;">Edit</button>
                        <button type="button" class="deleteLinkButton" data-index="${index}">Delete</button>
                    </div>
                `);
            });
        }
    
        // Add new link
        $("#addLinksButton").on("click", function () {
            clearLinkForm();
            $("#linksForm").show();
            $("#saveLinkButton").text("Add"); // Ensure button text is "Add" when adding a new link
        });
    
        $("#saveLinkButton").on("click", function () {
            const linkData = {
                label: $("#linkLabel").val().trim(),
                url: $("#linkURL").val().trim()
            };
        
            if (!linkData.label || !linkData.url) {
                alert("Please fill in both fields (Label and URL).");
                return;
            }
        
            if (editingIndex !== null) {
                updateSocialLinkEntry(); // Update existing entry
            } else {
                linksList.push(linkData); // Add new entry
                renderLinksEntries(); // Re-render entries
                updateLinksInDatabase(); // Sync with backend
                clearLinkForm(); // Reset form
            }
        });
    
    
        // Update links in the database
        function updateLinksInDatabase() {
            const templateId = '<?=$this->uri->segment(2)?>';
    
            const placeholders = {
                socialLinks: linksList
            };
    
            $.ajax({
                url: '<?=base_url("career-services/ResumeBuilder/insert_links_resume_template")?>',
                type: 'POST',
                data: {
                    template_id: templateId,
                    placeholders: JSON.stringify(placeholders)
                },
                dataType: 'json',
                success: function (response) {
                    if (response.success) {
                        console.log("Social links updated successfully in database.");
                    } else {
                        console.error("Failed to update social links:", response.message);
                    }
                },
                error: function (xhr, status, error) {
                    console.error("Error updating social links in database:", error);
                }
            });
        }
        
        function updateSocialLinkEntry() {
            const socialLinkData = {
                template_id: '<?=$this->uri->segment(2)?>',
                label: $("#linkLabel").val().trim(),
                url: $("#linkURL").val().trim(),
            };
        
            if (!socialLinkData.label || !socialLinkData.url) {
                alert("Please fill in both fields (Label and URL).");
                return;
            }
        
            if (editingIndex !== null) {
                // Update local data
                linksList[editingIndex] = socialLinkData; // Update the entry in the list
                renderLinksEntries(); // Re-render entries
        
                // Send the updated data to the backend
                $.ajax({
                    url: '<?= base_url("career-services/ResumeBuilder/update_social_link_entry") ?>',
                    type: 'POST',
                    data: {
                        index: editingIndex,
                        data: JSON.stringify(socialLinkData)
                    },
                    dataType: 'json',
                    success: function (response) {
                        if (response.success) {
                            console.log("Social link entry updated successfully.");
                            editingIndex = null; // Reset the editing index
                            clearLinkForm(); // Clear the form
                        } else {
                            console.error("Failed to update social link entry:", response.message);
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error("Error updating social link entry:", error);
                    }
                });
            }
        }
    
        // Edit link
        $(document).on("click", ".editLinkButton", function () {
            const index = $(this).data("index");
            const linkData = linksList[index];
            editingIndex = index;  // Set the index for editing
            populateLinkForm(linkData);
        });
        
        // Populate form for editing
        function populateLinkForm(data) {
            $("#linkLabel").val(data.label);
            $("#linkURL").val(data.url);
    
            // Show the form and update button text
            $("#linksForm").show();
            $("#saveLinkButton").text("Update");
        }
    
        function clearLinkForm() {
            $("#linkLabel").val('');
            $("#linkURL").val('');
            $("#linksForm").hide();
            $("#saveLinkButton").text("Add"); // Reset button text to "Add" when clearing the form
            editingIndex = null; // Reset editing index
        }
    
        // Delete education entry
        $(document).on("click", ".deleteLinkButton", function () {
                const index = $(this).data("index");
                $.ajax({
                    url: '<?= base_url("career-services/ResumeBuilder/delete_social_link_entry") ?>', // Controller function to delete a specific entry
                    type: 'POST',
                    data: { index: index },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            console.log("Education entry deleted successfully.");
                            linksList.splice(index, 1); // Remove entry from the list
                            renderLinksEntries(); // Re-render entries
                            updateLinksInDatabase(); // Sync with backend
                        } else {
                            console.error("Failed to delete education entry:", response.message);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("Error deleting education entry:", error);
                    }
                });
            });
       
    
    });
    
    /*$(document).ready(function () {
    
        const templateId = '<?//=$this->uri->segment(2)?>';
    
        // Fetch user-selected skills and display them
        function fetchSelectedSkills() {
            $.ajax({
                url: "<?php echo site_url('career-services/ResumeBuilder/fetch_selected_skill_data'); ?>",
                method: "POST",
                data: { template_id: templateId },
                dataType: "json",
                success: function (response) {
                    if (response.success) {
                        $(".selected-skills").empty(); // Clear existing selected skills
                        response.skillsList.forEach(skill => {
                            const skillTag = createSkillTag(skill.id, `${skill.skill} (${skill.level})`);
                            $(".selected-skills").append(skillTag);
                        });
                    } else {
                        alert(response.message || "Failed to fetch selected skills.");
                    }
                },
                error: function () {
                    alert("An unexpected error occurred while fetching selected skills.");
                }
            });
        }
    
        // Fetch available skills
        function fetchSkills() {
                $.ajax({
                    url: "<?php echo site_url('career-services/ResumeBuilder/fetch_skills'); ?>",
                    method: "GET",
                    dataType: "json",
                    success: function (response) {
                        if (response.success) { // Ensure you're checking for 'success' instead of 'status'
                            $(".available-skills").empty(); // Clear existing available skills
                            
                            // Check if skills list is not empty
                            if (response.skillsList && response.skillsList.length > 0) {
                                // Loop through the skills and append them
                                response.skillsList.forEach(skill => {
                                    $(".available-skills").append(createSkillTag(skill, skill));
                                });
                            } else {
                                $(".available-skills").append("<p>No skills available for this job title.</p>");
                            }
                        } else {
                            alert(response.message || "No skills data found.");
                        }
                    },
                    error: function () {
                        alert("An error occurred while fetching available skills.");
                    }
                });
            }
            
        // Create skill tag element with ID
        function createSkillTag(id, skill) {
                return `
                    <div class="skill-tag" data-id="${id}" data-skill="${skill}">
                        ${skill}
                        <span class="remove-skill">X</span>
                    </div>
                `;
            }
    
        // Initialize by fetching both selected and available skills
        fetchSelectedSkills();
        fetchSkills();
    
        // Handle Available Skills click to open input
        $(".available-skills").on("click", ".skill-tag", function () {
                const id = $(this).data("id");
                const skillData = $(this).data("skill");
                const skillMatch = skillData.match(/^(.*)\s\((.*)\)$/);
                const skill = skillMatch ? skillMatch[1] : skillData;
                const level = skillMatch ? skillMatch[2] : "novice";
        
                // Append skill entry
                addSkillEntry(id, skill, level, true, null, ".skill-entry-container");
            });
        
        // Handle click on skill tag in selected skills for editing
        $(".selected-skills").on("click", ".skill-tag", function () {
                const id = $(this).data("id");
                const skillData = $(this).data("skill");
                const skillMatch = skillData.match(/^(.*)\s\((.*)\)$/);
                const skill = skillMatch ? skillMatch[1] : skillData;
                const level = skillMatch ? skillMatch[2] : "novice";
            
                // Append skill entry near the "Add More Skill" button
                addSkillEntry(id, skill, level, true, null, ".skill-entry-container");
            });
            
        // Add skill entry for editing
        function addSkillEntry(id = null, skill = "", level = "novice", isEditing = false, targetTag = null, containerSelector = "") {
                const skillEntryHTML = `
                    <div class="skill-entry" data-id="${id}">
                        <input type="text" class="input-skill" placeholder="Enter skill" value="${skill}" />
                        <select class="select-level">
                            <option value="novice" ${level === "novice" ? "selected" : ""}>Novice</option>
                            <option value="beginner" ${level === "beginner" ? "selected" : ""}>Beginner</option>
                            <option value="skilled" ${level === "skilled" ? "selected" : ""}>Skilled</option>
                            <option value="experienced" ${level === "experienced" ? "selected" : ""}>Experienced</option>
                            <option value="expert" ${level === "expert" ? "selected" : ""}>Expert</option>
                        </select>
                        <button type="button" class="btn-save-skill">Save</button>
                        <button type="button" class="btn-remove-skill" data-id="${id}">Remove</button>
                    </div>
                `;
            
                // Append the skill entry near the "Add More Skill" button
                $(containerSelector).append(skillEntryHTML);
            }
    
        // Remove selected skill when "X" is clicked
        $(document).on("click", ".btn-remove-skill", function (event) {
                event.stopPropagation();
                const id = $(this).data("id"); // Get the ID from the button's data-id attribute
            
                // Confirm removal
                //if (!confirm("Are you sure you want to remove this skill?")) return;
            
                // AJAX call to remove skill from the backend
                $.ajax({
                    url: "<?php echo site_url('career-services/ResumeBuilder/delete_skills'); ?>",
                    method: "POST",
                    data: { id: id }, // Send the skill id to be deleted
                    dataType: "json",
                    success: function (response) {
                        if (response.success) {
                            // Remove the skill tag from the DOM after successful deletion
                            $(`.skill-tag[data-id="${id}"]`).remove();
                            //alert(response.message);  // Success message
                        } else {
                            alert(response.message || "Failed to remove skill.");
                        }
                    },
                    error: function () {
                        alert("An error occurred while removing the skill.");
                    }
                });
            });
        
        // Add new skill input field
        $("#add-skill-btn").on("click", function (event) {
                event.preventDefault();
                // Ensure the skill-entry-container is visible and ready for the new entry
                addSkillEntry(null, "", "novice", false, null, ".skill-entry-container");
            });
            
        // Save skill (insert or update)
        $(document).on("click", ".btn-save-skill", function () {
        const skillEntry = $(this).closest(".skill-entry");
        const id = skillEntry.data("id") || null;
        const skill = skillEntry.find(".input-skill").val().trim();
        const level = skillEntry.find(".select-level").val();
    
        if (!skill) {
            alert("Skill name cannot be empty.");
            return;
        }
    
        const skillData = {
            skills: [{
                id: id,
                skill: skill,
                level: level
            }]
        };
    
        // AJAX call to save the skill
        $.ajax({
            url: "<?php echo site_url('career-services/ResumeBuilder/add_update_skills'); ?>",
            method: "POST",
            data: {
                template_id: templateId,
                skills: JSON.stringify(skillData)
            },
            dataType: "json",
            success: function (response) {
                if (response.success) {
                    fetchSelectedSkills(); // Refresh selected skills
                    fetchSkills(); // Refresh available skills
                    skillEntry.remove(); // Remove the input form
                } else {
                    alert(response.message || "Failed to save skill.");
                }
            },
            error: function () {
                alert("An error occurred while saving the skill.");
            }
        });
    });
        
      
    });*/


    
</script>



    
    <?php $this->load->view('common/inc/footer');?>
