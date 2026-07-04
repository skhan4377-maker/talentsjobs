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
    padding: 10px;
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
        padding: 8px;
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
            background-color: #007bff;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            font-size: 15px;
            font-weight: bold;
            cursor: pointer;
            margin: 10px 0;
            display: inline-block;
        }
        
        .toggle-btn:hover {
            background-color: #0056b3;
        }
        
        /* Adjust input field font size and padding for compact look */
        .form-input {
            font-size: 14px;
            padding: 10px;
        }
    </style>
<style>

                .skills-section {
                      font-family: Arial, sans-serif;
                      margin: 20px;
                    }
                    
                    .skill-tags {
                      display: flex;
                      flex-wrap: wrap;
                      gap: 10px;
                      margin: 10px 0;
                    }
                    
                    .skill-tag {
                      background-color: #f0f0f0;
                      padding: 8px 12px;
                      border-radius: 16px;
                      cursor: pointer;
                      font-size: 14px;
                    }
                    
                    .skill-tag:hover {
                      background-color: #dce7ff;
                    }
                    
                    .add-skill-section {
                      margin-top: 20px;
                    }
                    
                    .skill-entry {
                      display: flex;
                      align-items: center;
                      gap: 10px;
                      margin-bottom: 10px;
                    }
                    
                    .skill-input {
                      padding: 8px;
                      border: 1px solid #ccc;
                      border-radius: 4px;
                    }
                    
                    .skill-level {
                      padding: 5px;
                      border: 1px solid #ccc;
                      border-radius: 4px;
                    }
                    
                    #add-skill-btn {
                      padding: 8px 12px;
                      background-color: #007bff;
                      color: #fff;
                      border: none;
                      border-radius: 4px;
                      cursor: pointer;
                    }
                    
                    #add-skill-btn:hover {
                      background-color: #0056b3;
                    }
                    
                  .selected-tag {
                      background-color: #007bff;
                      color: #fff;
                      border: 1px solid #0056b3;
                    }
                    
                    .skill-input {
                      width: 200px;
                    }
    
    
    
                </style>

    <?php $this->load->view('common/inc/header');?>
    
    <div style="display: flex; flex-direction: row;">
        <div class="left-container">
            <div class="resume-container" id="resume">
                <!-- Basic form with two columns and toggle section added here -->
                <form id="resumeForm" style="margin: auto; padding: 20px; border: 1px solid #ddd; border-radius: 8px; background-color: #f9f9f9;">
                    <h3 style="text-align: center; color: #333;">Create Your Resume</h3>
                    
                    <!-- Basic Information Fields -->
                    <div class="form-row" style="display: flex; gap: 15px; margin-bottom: 15px;">
                        <div class="form-col" style="flex: 1;">
                            <label for="name" style="font-weight: bold;">Full Name:</label>
                            <input type="text" id="name" name="name"  placeholder="e.g., John Doe" class="form-input" style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px;">
                        </div>
                        <div class="form-col" style="flex: 1;">
                            <label for="email" style="font-weight: bold;">Email Address:</label>
                            <input type="email" id="email" name="email"  placeholder="e.g., john.doe@example.com" class="form-input" style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px;">
                        </div>
                    </div>
                
                    <div class="form-row" style="display: flex; gap: 15px; margin-bottom: 15px;">
                        <div class="form-col" style="flex: 1;">
                            <label for="phone" style="font-weight: bold;">Contact Number:</label>
                            <input type="text" id="phone" name="phone"  placeholder="e.g., +1 234 567 8900" class="form-input" style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px;">
                        </div>
                        <div class="form-col" style="flex: 1;">
                            <label for="jobTitle" style="font-weight: bold;">Current Job Title:</label>
                            <input type="text" id="jobTitle" name="jobTitle"  placeholder="e.g., Software Engineer"
                            class="form-input" style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px;">
                        </div>
                    </div>
                
                    <div class="form-row" style="display: flex; gap: 15px; margin-bottom: 15px;">
                        <div class="form-col" style="flex: 1;">
                            <label for="city-search-input" style="font-weight: bold;">City:</label>
                            <input type="text" id="city-search-input" name="city"  placeholder="e.g., New York" class="form-input" style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px;">
                            <input type="hidden" id="hiddenLocationId" name="hiddenLocationId">
                        </div>
                        <div class="form-col" style="flex: 1;">
                            <label for="country" style="font-weight: bold;">Country:</label>
                            <input type="text" id="country" name="country"  placeholder="e.g., United States" class="form-input" style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px;">
                        </div>
                    </div>
                
                    <!-- Additional Details Section (Hidden by Default) -->
                    <div id="additionalDetails" style="display: none;">
                        <div class="form-row" style="display: flex; gap: 15px; margin-bottom: 15px;">
                            <div class="form-col" style="flex: 1;">
                                <label for="address" style="font-weight: bold;">Residential Address:</label>
                                <input type="text" id="address" name="address" placeholder="e.g., 123 Main St, Apt 4B" class="form-input" style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px;">
                            </div>
                            <div class="form-col" style="flex: 1;">
                                <label for="postal" style="font-weight: bold;">Postal Code:</label>
                                <input type="text" id="postal" name="postal" placeholder="e.g., 10001" class="form-input" style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px;">
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
                                    style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px;">
                            </div>
                        </div>

                        
                    </div>
                    
                    <!-- Toggle Button for Additional Details -->
                    <button type="button" id="toggleDetails" class="toggle-btn" style="width: 100%; padding: 10px; margin-top: 10px; background-color: #007bff; color: #fff; border: none; border-radius: 5px;">Show Additional Details</button>
                    
                    <!-- Professional Summary Section -->
                    <div class="row" style="margin-bottom: 15px;">
                        <label for="resume_headline" style="font-weight: bold;">Professional Summary</label>
                        <p style="font-size: 0.8em; color: #666;">Write 2–4 short, energetic sentences about how great you are. Mention the role and your achievements. List your skills and motivation.</p>
                        <textarea id="resume_headline" name="resume_headline" placeholder="Curious science teacher with 8+ year of experiece and a track record of..." rows="8" class="form-input" style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px;"></textarea>
                        <p style="font-size: 0.9em; color: #666;">Tip: Aim for 400–600 characters to increase your interview chances.</p>
                    </div>
                    
                    <input type="hidden" id="employment_id" value="">
                    <div id="employmentSection" style="max-width: 800px; margin: auto;">
                        <h2 style="color: #333;">Employment History</h2>
                        <p style="color: #666;">Show your relevant experience (last 10 years). Use bullet points to note your achievements, if possible – use numbers/facts (Achieved X, measured by Y, by doing Z).</p>
                    
                        <!-- Existing Employment Entries will load here -->
                        <div id="employmentEntries"></div>
                    
                        <!-- Form to add new employment -->
                        <div id="employmentSection">
                            <!-- Button to display employment form -->
                            <button type="button" id="addEmploymentButton" style="margin-top: 20px;">Add Employment</button>
                        
                            <!-- Hidden form for adding employment -->
                            <div id="employmentForm" style="display: none; border: 1px solid #ddd; padding: 15px; border-radius: 8px; background-color: #f9f9f9; margin-top: 20px;">
                                
                                <div style="display: flex; gap: 15px; margin-bottom: 15px;">
                                    <div style="flex: 1;">
                                        <label>Job Title:</label>
                                        <input type="text" id="cjobTitle" placeholder="Job Title" style="width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #ccc;">
                                    </div>
                                    <div style="flex: 1;">
                                        <label>Employer:</label>
                                        <input type="text" id="cemployer" placeholder="Employer" style="width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #ccc;">
                                    </div>
                                </div>
                                <div style="display: flex; gap: 15px; margin-bottom: 15px;">
                                    <div style="flex: 1;">
                                        <label for="cstartDate">Start Date:</label>
                                        <input type="text" id="cstartDate" placeholder="YYYY / MM" style="width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #ccc;">
                                    </div>
                                    <div style="flex: 1; display: flex; align-items: center; gap: 10px;">
                                        <div style="flex: 1;">
                                            <label for="cendDate">End Date:</label>
                                            <input type="text" id="cendDate" placeholder="YYYY / MM" style="width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #ccc;">
                                            <div style="flex:1; align-items: center; gap: 5px;">
                                            <input type="checkbox" id="currentWork" style="width: 20px; height: 20px; cursor: pointer;">
                                            <label for="currentWork" style="font-size: 14px; cursor: pointer;">Currently Work Here</label>
                                        </div>
                                        </div>
                                        
                                    </div>
                                </div>
                                <div style="display: flex; gap: 15px; margin-bottom: 15px;"> 
                                  <div style="flex: 1;">
                                        <label>City:</label>
                                        <input type="text" id="work_location" placeholder="" style="width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #ccc;">
                                    </div>
                                 </div>    
                                <div style="margin-bottom: 15px;">
                                    <label>Professional Summary:</label>
                                    <textarea id="description" placeholder="e.g., Created and implemented lesson plans..." rows="4" style="width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #ccc;"></textarea>
                                </div>
                                <!-- Button for saving employment entry -->
                                <button type="button" id="saveEmploymentButton">Add Employment</button>
                            </div>
                        </div>
        
                    </div>
                    
                    
                    <input type="hidden" id="education_id" value="">
                    <div id="educationSection" style="max-width: 800px; margin: auto;">
                        <h2 style="color: #333;">Education History</h2>
                        <p style="color: #666;">Show your educational qualifications. Include relevant degrees or certifications.</p>
                    
                        <!-- Existing Education Entries will load here -->
                        <div id="educationEntries"></div>
                    
                        <!-- Form to add new education -->
                        <div id="educationForm" style="display: none; border: 1px solid #ddd; padding: 15px; border-radius: 8px; background-color: #f9f9f9; margin-top: 20px;">
                            <div style="display: flex; gap: 15px; margin-bottom: 15px;">
                                <div style="flex: 1;">
                                    <label>Degree:</label>
                                    <input type="text" id="degree" placeholder="e.g., Bachelor of Science" style="width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #ccc;">
                                </div>
                                <div style="flex: 1;">
                                    <label>Institution:</label>
                                    <input type="text" id="institution" placeholder="e.g., University Name" style="width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #ccc;">
                                </div>
                            </div>
                            <div style="display: flex; gap: 15px; margin-bottom: 15px;">
                                <div style="flex: 1;">
                                    <label>Start Year:</label>
                                    <input type="text" id="startYear" placeholder="YYYY / MM" style="width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #ccc;">
                                </div>
                                <div style="flex: 1;">
                                    <label>End Year:</label>
                                    <input type="text" id="endYear" placeholder="YYYY / MM" style="width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #ccc;">
                                    <div style="margin-top: 5px;">
                                        <input type="checkbox" id="currentlyStudying" />
                                        <label for="currentlyStudying">Currently Studying Here</label>
                                    </div>
                                </div>
                            </div>
                            <div style="margin-bottom: 15px;">
                                <label>Field of Study:</label>
                                <input type="text" id="fieldOfStudy" placeholder="e.g., Computer Science" style="width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #ccc;">
                            </div>
                            <!-- Save Education Button -->
                            <button type="button" id="saveEducationButton">Add Education</button>
                        </div>
                    
                        <!-- Button to display education form -->
                        <button type="button" id="addEducationButton" style="margin-top: 20px;">Add Education</button>
                    </div>
                    
                    <input type="hidden" id="link_id" value="">
                    <div style="max-width: 800px; margin: auto;">
                            <h2 style="color: #333;">Website & Social Links</h2>
                            <p style="color: #666;">Provide your website or social media links. Include LinkedIn, Portfolio, GitHub, etc.</p>
                        
                            <!-- Existing Links Entries will load here -->
                            <div id="linksEntries"></div>
                        
                            <!-- Form to add new website/social links -->
                            <div id="linksForm" style="display: none; border: 1px solid #ddd; padding: 15px; border-radius: 8px; background-color: #f9f9f9; margin-top: 20px;">
                                <div style="margin-bottom: 15px;">
                                    <label>Link Label:</label>
                                    <input type="text" id="linkLabel" placeholder="e.g., LinkedIn, Portfolio" style="width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #ccc;">
                                </div>
                                <div style="margin-bottom: 15px;">
                                    <label>Link URL:</label>
                                    <input type="text" id="linkURL" placeholder="e.g., https://www.linkedin.com/in/yourname" style="width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #ccc;">
                                </div>
                                <button type="button" id="saveLinkButton">Add Link</button>
                            </div>
                        
                            <!-- Button to display links form -->
                            <button type="button" id="addLinksButton" style="margin-top: 20px;">Add More Links</button>
                        </div>
                    <div class="skills-section">
                            <h3>Skills</h3>
                            <p>
                              Choose 5 important skills that show you fit the position. Make sure they match the key skills mentioned in the job listing.
                            </p>
                        
                            <label>
                              <input type="checkbox" id="toggle-experience-level" />
                              Don't show experience level
                            </label>
                        
                            <div class="skill-tags">
                              <!-- Skills will be dynamically injected here -->
                            </div>
                        
                            <div class="add-skill-section">
                              <div class="skill-entry"></div>
                              <button id="add-skill-btn">+ Add more skill</button>
                            </div>
                    </div>
                    <div id="response" style="margin-top: 15px;"></div>
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
 
     
    // Function to load cities data on page load
    function loadCitiesData() {
          $.ajax({
              url: '<?= base_url('Common/getCities') ?>',
              dataType: 'json',
              success: function (data) {
                  citiesData = data;
                  // Initialize autocomplete after cities data is loaded
                  initializeCityAutocomplete();
              }
          });
      }
      
    function initializeCityAutocomplete() {
          var cityInput = $('#city-search-input'); // Use the ID of the input field
          cityInput.autocomplete({
              source: function (request, response) {
                  var terms = request.term.split(/\s*,\s*/);
                  var lastTerm = terms.pop().toLowerCase();
                  var maxResults = 10;
      
                  // Assuming citiesData is already populated
                  var filteredData = $.grep(citiesData, function (item) {
                      return item.city_name.toLowerCase().indexOf(lastTerm) !== -1;
                  });
      
                  var formattedData = $.map(filteredData.slice(0, maxResults), function (item) {
                      return {
                          label: item.city_name,
                          value: item.id
                      };
                  });
                  response(formattedData);
              },
              minLength: 1,
              delay: 0,
              appendTo: cityInput.parent(), // Append to the parent container of the input field
              select: function (event, ui) {
                  $('input[name="hiddenLocationId"]').val(ui.item.value);
                  cityInput.val(ui.item.label);
                  return false;
              }
          });
      }
      
    function loadJobProfileData() {
          $.ajax({
              url: '<?= base_url('Common/getJobProfile') ?>',
              dataType: 'json',
              success: function (data) {
                  initializeDesignationAutocomplete(data);
              }
          });
      }
    
    function initializeDesignationAutocomplete(jobProfileData) {
          var designationInput = $('#jobTitle');
      
          designationInput.autocomplete({
              source: function (request, response) {
                  var terms = request.term.split(/,\s*/);
                  var lastTerm = terms.pop().toLowerCase();
                  var maxResults = 10;
                  if (!jobProfileData || !Array.isArray(jobProfileData)) {
                      console.error('Job profile data is invalid or not an array.');
                      return;
                  }
      
                  var filteredData = $.grep(jobProfileData, function (item) {
                      return item.profile && typeof item.profile === 'string' &&
                          item.profile.toLowerCase().indexOf(lastTerm) !== -1;
                  });
      
                  var formattedData = $.map(filteredData.slice(0, maxResults), function (item) {
                      return {
                          label: item.profile,
                          value: item.profile
                      };
                  });
      
                  response(formattedData);
              },
              minLength: 1,
              delay: 0,
              appendTo: designationInput.parent(), // Append to the parent container of the input field
          });
      }
      
    $(document).ready(function () {

    loadCitiesData();
    initializeCityAutocomplete();
    loadJobProfileData();
    initializeDesignationAutocomplete();
 
 
   $("#toggleDetails").on("click", function (e) {
      e.preventDefault();
      const button = $(this);
      const additionalDetails = $("#additionalDetails");
      additionalDetails.toggle(); // Show or hide details
      button.text(additionalDetails.is(":visible") ? "Hide Additional Details" : "Show Additional Details");
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
    
   function loadTemplate() {
      const templateId = getResumeIdFromUrl();
      $("#loader").show();
      $.ajax({
         url: "<?= base_url('career-services/ResumeBuilder/preview') ?>",
         method: "POST",
         contentType: "application/json",
         data: JSON.stringify({
            id: templateId
         }),
         dataType: "json",
         success: function (data) {
            if (data.css_file) {
               const linkElement = $("<link>", {
                  rel: "stylesheet",
                  href: "<?= base_url('/') ?>" + data.css_file
               });
               $("head").append(linkElement);
            }
            $("#resumePreview").html(data.template_html);
            $("#modalContent").html(data.template_html); // Load the content into the
         },
         error: function () {
            alert("Error loading template.");
         },
         complete: function () {
            $("#loader").hide();
         }
      });
   }
   loadTemplate();

   function getCandidateDetails() {
      $.ajax({
         url: "<?= base_url('career-services/ResumeBuilder/getCandidateDetails') ?>",
         method: "GET",
         dataType: "json",
         success: function (response) {
            if (response.success) {
               populateCandidateForm(response.candidate_info);
            } else {
               console.error("Error fetching candidate details:", response.message);
            }
         },
         error: function () {
            alert("Error loading candidate details.");
         }
      });
   }
   // Initialize functions on page load
   getCandidateDetails();
    
   // Update resume data on input changes with debounce
    $("#resumeForm input, #resumeForm textarea").on("input change", debounce(updateResumeData, 300));

   /**
    * Save resume data via AJAX.
    */
   function updateResumeData() {
      const formData = {
         name: $("#name").val(),
         email: $("#email").val(),
         phone: $("#phone").val(),
         jobTitle: $("#jobTitle").val(),
         city: $("#hiddenLocationId").val(),
         country: $("#country").val(),
         resume_headline: $("#resume_headline").val(),
         postal: $("#postal").val(),
         dob: $("#dob").val(), // Ensure proper format
         address: $("#address").val(),
         id: getResumeIdFromUrl()
      };

      $.ajax({
         url: "<?= site_url('career-services/ResumeBuilder/submit_resume') ?>",
         method: "POST",
         contentType: "application/json",
         data: JSON.stringify(formData),
         dataType: "json",
         success: function (response) {
            if (response.status === "success") {
               $("#response").html("<p>Resume data updated successfully!</p>");
               loadTemplate();
            } else {
               $("#response").html(`<p>Error: ${response.message}</p>`);
            }
         },
         error: function () {
            $("#response").html("<p>An error occurred while updating the resume data.</p>");
         }
      });
   }
    
     /**
    * Populate the form with candidate data.
    */
   function populateCandidateForm(data) {
      $("#name").val(data.name || "");
      $("#email").val(data.email || "");
      $("#phone").val(data.candidate_mobile || "");
      $("#jobTitle").val(data.designations || "");
      $("#city-search-input").val(data.city_name || "");
      $("#hiddenLocationId").val(data.current_location);
      $("#country").val(data.country_code || "");
      $("#resume_headline").val(data.resume_headline || "");
      $("#postal").val(data.postal || "");
      // If dob exists, update the flatpickr date
        if (data.dob) {
            const dobPicker = document.querySelector("#dob")._flatpickr;
            if (dobPicker) {
                dobPicker.setDate(data.dob, true); // Set the date and trigger the input update
            }
        }
      $("#address").val(data.residential_address || "");
   }
   
   
   function loadEmploymentEntries() {
      $.ajax({
         url: "<?= base_url('career-services/ResumeBuilder/getEmploymentEntries') ?>",
         method: "GET",
         dataType: "json",
         success: function (response) {
            if (response.status === "success") {
               populateEmploymentEntries(response.data);
            } else {
               console.log(response.message);
            }
         },
         error: function () {
            console.error("Error fetching employment entries.");
         }
      });
   }
   loadEmploymentEntries();


   // Show employment form on button click
   $("#addEmploymentButton").on("click", function () {
      resetEmploymentForm(); // Reset the form for new entry
      $("#employmentForm").show();
      $(this).hide();
   });

   // Save employment details
   $("#saveEmploymentButton").on("click", function () {
      $(this).prop("disabled", true);
      saveEmployment();
   });


   // Handle "Currently Work Here" toggle
    $("#currentWork").on("change", function () {
       const isChecked = $(this).is(":checked");
       const endDateField = $("#cendDate");
    
       if (isChecked) {
          endDateField.val("").prop("disabled", true); // Clear and disable End Date field
       } else {
          endDateField.prop("disabled", false); // Enable End Date field
       }
    });
        

   /**
    * Save employment data via AJAX.
    */
   // Save employment data via AJAX.
    function saveEmployment() {
       const employmentData = {
          id: $("#employment_id").val(),
          designation: $("#cjobTitle").val(),
          company_name: $("#cemployer").val(),
          cstartDate: $("#cstartDate").val(),
          cendDate: $("#currentWork").is(":checked") ? null : $("#cendDate").val(), // Set null if currently working
          work_location: $("#work_location").val(),
          description: $("#description").val(),
          is_current_employment: $("#currentWork").is(":checked") ? 1 : 0 // Capture current work status
       };
    
       $.ajax({
          type: "POST",
          url: "<?= base_url('career-services/ResumeBuilder/saveEmployment') ?>",
          data: employmentData,
          dataType: "json",
          success: function (response) {
             loadEmploymentEntries();
             handleEmploymentResponse(response, employmentData);
          },
          error: function () {
             showFeedback("An error occurred. Please try again.", "error");
          },
          complete: function () {
             $("#saveEmploymentButton").prop("disabled", false);
          }
       });
    }

   /**
    * Handle response from employment save API.
    */

   function handleEmploymentResponse(response, employmentData) {
      if (response.status === "inserted") {
         $("#employment_id").val(response.id);
         showFeedback("Employment added successfully!");
      } else if (response.status === "updated") {
         showFeedback("Employment updated successfully!");
      }
      loadEmploymentEntries(); // Reload employment entries
      resetEmploymentForm(); // Reset form after save
   }

   /**
    * Populate the employment entries section.
    */
   function populateEmploymentEntries(entries) {
      const container = $("#employmentEntries");
      container.empty(); // Clear existing entries

      entries.forEach(entry => {
         const entryHtml = `
                <div class="employment-entry" data-id="${entry.id}" 
                     data-designation="${entry.designation}" 
                     data-company-name="${entry.company_name}" 
                     data-joining-year="${entry.joining_year}" 
                     data-worked-till-year="${entry.worked_till_year}" 
                     data-work-location="${entry.work_location}" 
                     data-currentemployment="${entry.is_current_employment}" 
                     data-job-profile="${entry.job_profile}" 
                     style="border: 1px solid #ddd; padding: 15px; border-radius: 8px; background-color: #fff; margin-bottom: 15px; position: relative;">
                    <h4 style="margin: 0;">${entry.designation} at ${entry.company_name}</h4>
                    <p style="color: #666; margin: 5px 0;">${entry.joining_year} - ${entry.worked_till_year} | ${entry.work_location}</p>
                    <p style="margin: 10px 0;">${entry.job_profile}</p>
                    <button class="delete-employment" 
                            data-id="${entry.id}" 
                            style="position: absolute; top: 10px; right: 10px; background: red; color: white; border: none; border-radius: 50%; width: 25px; height: 25px; cursor: pointer;">&times;</button>
                </div>
            `;
         container.append(entryHtml);
      });


      // Add click event to populate the form with entry data
      // Add click event to populate the form with entry data
    $(".employment-entry").on("click", function () {
        const entry = $(this).data();

        $("#employment_id").val(entry.id);
        $("#cjobTitle").val(entry.designation);
        $("#cemployer").val(entry.companyName);
        $("#work_location").val(entry.workLocation);
        $("#description").val(entry.jobProfile);
        
        // Set the "Currently Working Here" checkbox
        $("#currentWork").prop("checked", entry.currentemployment == 1);

        // Populate the Flatpickr date inputs
        const startDatePicker = document.querySelector("#cstartDate")._flatpickr;
        if (startDatePicker) {
            startDatePicker.setDate(entry.joiningYear || null, true);
        } else {
            $("#cstartDate").val(entry.joiningYear || "");
        }

        const endDatePicker = document.querySelector("#cendDate")._flatpickr;
        if (entry.isCurrent == 1) {
            endDatePicker.clear();
            $("#cendDate").prop("disabled", true); // Disable End Date if currently working
        } else {
            if (endDatePicker) {
                endDatePicker.setDate(entry.workedTillYear || null, true);
            } else {
                $("#cendDate").val(entry.workedTillYear || "");
            }
            $("#cendDate").prop("disabled", false); // Enable End Date otherwise
        }

        $("#employmentForm").show();
        $("#addEmploymentButton").hide();
        $("#saveEmploymentButton").text("Update Employment");
    });


      // Add click event for delete buttons
      $(".delete-employment").on("click", function (e) {
         e.stopPropagation(); // Prevent triggering the parent click event
         const employmentId = $(this).data("id");
         deleteEmployment(employmentId);
      });
   }

   /**
    * Reset the employment form.
    */
   function resetEmploymentForm() {
      $("#employment_id").val("");
      $("#cjobTitle").val("");
      $("#cemployer").val("");
      $("#cstartDate").val("");
      $("#cendDate").val("");
      $("#work_location").val("");
      $("#description").val("");
      $("#employmentForm").hide();
      $("#addEmploymentButton").show();
      $("#saveEmploymentButton").text("Add Employment"); // Reset button text
   }

   /**
    * Delete employment entry via AJAX.
    */
   function deleteEmployment(employmentId) {
      if (confirm("Are you sure you want to delete this employment entry?")) {
         $.ajax({
            type: "POST",
            url: "<?= base_url('career-services/ResumeBuilder/deleteEmployment') ?>",
            data: {
               id: employmentId
            },
            dataType: "json",
            success: function (response) {
               if (response.status === "success") {
                  showFeedback("Employment deleted successfully!");
                  loadEmploymentEntries(); // Reload entries
               } else {
                  showFeedback("Error deleting employment: " + response.message, "error");
               }
            },
            error: function () {
               showFeedback("An error occurred. Please try again.", "error");
            }
         });
      }
   }


   /**
    * Show feedback messages to the user.
    */
   function showFeedback(message, type = "success") {
      $("#response").html(`<div class="alert alert-${type}">${message}</div>`).fadeIn();
      setTimeout(() => $("#response").fadeOut(), 3000);
   }

  
   /**
    * Get the resume ID from the URL.
    */
   function getResumeIdFromUrl() {
      const pathSegments = window.location.pathname.split("/");
      return pathSegments[2]; // Adjust as per URL structure
   }


   // Show education form
   $("#addEducationButton").on("click", function () {
      resetEducationForm(); // Reset form for new entry
      $("#educationForm").show();
      $(this).hide();

   });

   // Save education details
   $("#saveEducationButton").on("click", function () {
      $(this).prop("disabled", true);
      saveEducation();
   });

    // Toggle End Year input based on the "Currently Studying Here" checkbox
    $("#currentlyStudying").on("change", function () {
        if ($(this).is(":checked")) {
            $("#endYear").val("").prop("disabled", true);
        } else {
            $("#endYear").prop("disabled", false);
        }
    });
    
        


   // Update saveEducation function to include the checkbox value
    function saveEducation() {
        const educationData = {
            id: $("#education_id").val(),
            degree: $("#degree").val(),
            institution: $("#institution").val(),
            startYear: $("#startYear").val(),
            endYear: $("#currentlyStudying").is(":checked") ? null : $("#endYear").val(),
            currentlyStudying: $("#currentlyStudying").is(":checked") ? 1 : 0,
            fieldOfStudy: $("#fieldOfStudy").val(),
        };
    
        $.ajax({
            type: "POST",
            url: "<?= base_url('career-services/EducationController/addEducation') ?>",
            data: educationData,
            dataType: "json",
            success: function (response) {
                handleEducationResponse(response, educationData);
                loadTemplate();
            },
            error: function () {
                showFeedback("An error occurred. Please try again.", "error");
            },
            complete: function () {
                $("#saveEducationButton").prop("disabled", false);
            }
        });
    }

   function handleEducationResponse(response, educationData) {
      if (response.status === "inserted") {
         $("#education_id").val(response.id); // Update the education ID for the new record
         showFeedback("Education added successfully!");
      } else if (response.status === "updated") {
         showFeedback("Education updated successfully!");
      } else {
         showFeedback(response.message, "error");
      }
      loadEducationEntries(); // Reload education entries
      resetEducationForm(); // Reset form after save
   }

   /**
    * Fetch and display all education entries on page load.
    */
   
   function loadEducationEntries() {
      $.ajax({
         url: "<?= base_url('career-services/EducationController/getEducation') ?>",
         method: "GET",
         dataType: "json",
         success: function (response) {
            if (response.status === "success") {
               populateEducationEntries(response.data);
            } else {
               console.log(response.message);
            }
         },
         error: function () {
            console.error("Error fetching education entries.");
         }
      });
   }

   loadEducationEntries();
   
   function populateEducationEntries(entries) {
      const container = $("#educationEntries");
      container.empty(); // Clear existing entries

        entries.forEach(entry => {
            // If field_of_study is empty, set it to a default value (like 'N/A' or leave it blank)
            const fieldOfStudy = entry.field_of_study ? entry.field_of_study : 'N/A';
    
            const entryHtml = `
                <div class="education-entry" data-id="${entry.id}" 
                     data-degree="${entry.degree}" 
                     data-institution="${entry.institution}" 
                     data-start-year="${entry.start_year}" 
                     data-end-year="${entry.end_year}" 
                     data-field-of-study="${entry.field_of_study}" 
                     data-currentstudying="${entry.currently_studying}"
                     style="border: 1px solid #ddd; padding: 15px; border-radius: 8px; background-color: #fff; margin-bottom: 15px; position: relative;">
                    <h4 style="margin: 0;">${entry.degree} - ${fieldOfStudy}</h4>
                    <p style="color: #666; margin: 5px 0;">${entry.institution}</p>
                    <p style="color: #666; margin: 5px 0;">${entry.start_year} - ${entry.end_year ? entry.end_year : 'Present'}</p>
                    <button type="button" class="delete-education" 
                            data-id="${entry.id}" 
                            style="position: absolute; top: 10px; right: 10px; background: red; color: white; border: none; border-radius: 50%; width: 25px; height: 25px; cursor: pointer;">&times;</button>
                    <button type="button" class="edit-education" 
                            data-id="${entry.id}" 
                            style="position: absolute; top: 10px; right: 40px; background: blue; color: white; border: none; border-radius: 50%; width: 25px; height: 25px; cursor: pointer;">✎</button>
                </div>
            `;
            container.append(entryHtml);
        });

       // Add click event to each education entry to populate the form for update
        $(".education-entry").on("click", function () {
            const entry = $(this);
        
            // Populate the other form fields
            $("#education_id").val(entry.data("id"));
            $("#degree").val(entry.data("degree"));
            $("#institution").val(entry.data("institution"));
            $("#fieldOfStudy").val(entry.data("field-of-study"));
        
            // Set the "Currently Working Here" checkbox
            $("#currentlyStudying").prop("checked", entry.data("currentstudying") == 1); // Correctly access data-currentstudying
            
            // Populate the Flatpickr date inputs
            const startYearPicker = document.querySelector("#startYear")._flatpickr;
            if (startYearPicker) {
                startYearPicker.setDate(entry.data("start-year") || null, true); // Set date and update input
            } else {
                $("#startYear").val(entry.data("start-year") || ""); // Fallback if Flatpickr is not initialized
            }
        
            const endYearPicker = document.querySelector("#endYear")._flatpickr;
            if (endYearPicker) {
                endYearPicker.setDate(entry.data("end-year") || null, true); // Set date and update input
            } else {
                $("#endYear").val(entry.data("end-year") || ""); // Fallback if Flatpickr is not initialized
            }
        
            // Show the education form
            $("#educationForm").show();
            $("#saveEducationButton").text("Update Education");
            $("#addEducationButton").hide();
        });


   }

   /**
    * Reset the education form.
    */
   function resetEducationForm() {
      $("#education_id").val("");
      $("#degree").val("");
      $("#institution").val("");
      $("#startYear").val("");
      $("#endYear").val("");
      $("#fieldOfStudy").val("");
      $("#educationForm").hide();
      $("#addEducationButton").show();
      $("#saveEducationButton").text("Add Education");
   }

   /**
    * Handle click events for dynamically added delete buttons.
    */
   $(document).on("click", ".delete-education", function (e) {
      e.preventDefault(); // Prevent default behavior
      e.stopPropagation(); // Prevent event bubbling
      const educationId = $(this).data("id");
      deleteEducation(educationId);
   });

   /**
    * Delete education entry via AJAX
    */
   function deleteEducation(educationId) {
      if (confirm("Are you sure you want to delete this education entry?")) {
         $.ajax({
            type: "POST",
            url: "<?= base_url('career-services/EducationController/deleteEducation') ?>",
            data: {
               id: educationId
            },
            dataType: "json",
            success: function (response) {
               if (response.status === true) { // Updated check for true success
                  showFeedback("Education deleted successfully!");
                  loadEducationEntries(); // Reload entries
               } else {
                  showFeedback("Error deleting education: " + response.message, "error");
               }
            },
            error: function () {
               showFeedback("An error occurred. Please try again.", "error");
            }
         });
      }
   }


   // Show link form
   $("#addLinksButton").on("click", function () {
      resetLinkForm(); // Reset form for new entry
      $("#linksForm").show();
      $(this).hide();
   });

   // Save link details
   $("#saveLinkButton").on("click", function () {
      $(this).prop("disabled", true);
      saveLink();
   });

   function saveLink() {
      const linkData = {
         id: $("#link_id").val(), // Include the ID if updating
         label: $("#linkLabel").val(),
         url: $("#linkURL").val(),
      };

      $.ajax({
         type: "POST",
         url: "<?= base_url('career-services/WebsiteSocialController/saveLink') ?>",
         data: linkData,
         dataType: "json",
         success: function (response) {
            loadTemplate();
            handleLinkResponse(response, linkData);
         },
         error: function () {
            showFeedback("An error occurred. Please try again.", "error");
         },
         complete: function () {
            $("#saveLinkButton").prop("disabled", false); // Re-enable button
         }
      });
   }

   function handleLinkResponse(response, linkData) {
      if (response.status === "inserted") {
         $("#link_id").val(response.id); // Update the link ID for the new record
         showFeedback("Link added successfully!");
      } else if (response.status === "updated") {
         showFeedback("Link updated successfully!");
      } else {
         showFeedback(response.message, "error");
      }
      loadLinksEntries(); // Reload link entries
      resetLinkForm(); // Reset form after save
   }

   /**
    * Fetch and display all link entries on page load.
    */
   loadLinksEntries();

   function loadLinksEntries() {
      $.ajax({
         url: "<?= base_url('career-services/WebsiteSocialController/getLinks') ?>",
         method: "GET",
         dataType: "json",
         success: function (response) {
            if (response.status === "success") {
               populateLinksEntries(response.data);
            } else {
               console.log(response.message);
            }
         },
         error: function () {
            console.error("Error fetching link entries.");
         }
      });
   }

   function populateLinksEntries(entries) {
      const container = $("#linksEntries");
      container.empty(); // Clear existing entries

      entries.forEach(entry => {
         const entryHtml = `
                <div class="link-entry" data-id="${entry.id}" 
                     data-label="${entry.label}" 
                     data-url="${entry.url}" 
                     style="border: 1px solid #ddd; padding: 15px; border-radius: 8px; background-color: #fff; margin-bottom: 15px; position: relative;">
                    <h4 style="margin: 0;">${entry.label}</h4>
                    <p style="color: #666; margin: 5px 0;">${entry.url}</p>
                    <button type="button" class="delete-link" 
                            data-id="${entry.id}" 
                            style="position: absolute; top: 10px; right: 10px; background: red; color: white; border: none; border-radius: 50%; width: 25px; height: 25px; cursor: pointer;">&times;</button>
                    <button type="button" class="edit-link" 
                            data-id="${entry.id}" 
                            style="position: absolute; top: 10px; right: 40px; background: blue; color: white; border: none; border-radius: 50%; width: 25px; height: 25px; cursor: pointer;">✎</button>
                </div>
            `;
         container.append(entryHtml);
      });

      // Add click event to each link entry to populate the form for update
      $(".link-entry").on("click", function () {
         const entry = $(this);
         $("#link_id").val(entry.data("id"));
         $("#linkLabel").val(entry.data("label"));
         $("#linkURL").val(entry.data("url"));
         $("#linksForm").show();
         $("#saveLinkButton").text("Update Link");
         $("#addLinksButton").hide();
      });
   }

   /**
    * Reset the link form.
    */
   function resetLinkForm() {
      $("#link_id").val("");
      $("#linkLabel").val("");
      $("#linkURL").val("");
      $("#linksForm").hide();
      $("#addLinksButton").show();
      $("#saveLinkButton").text("Add Link");
   }

   /**
    * Handle click events for dynamically added delete buttons.
    */
   $(document).on("click", ".delete-link", function (e) {
      e.preventDefault(); // Prevent default behavior
      e.stopPropagation(); // Prevent event bubbling
      const linkId = $(this).data("id");
      deleteLink(linkId);
   });

   /**
    * Delete link entry via AJAX
    */
   function deleteLink(linkId) {
      if (confirm("Are you sure you want to delete this link entry?")) {
         $.ajax({
            type: "POST",
            url: "<?= base_url('career-services/WebsiteSocialController/deleteLink') ?>",
            data: {
               id: linkId
            },
            dataType: "json",
            success: function (response) {
               if (response.status === true) { // Updated check for true success
                  showFeedback("Link deleted successfully!");
                  loadLinksEntries(); // Reload entries
               } else {
                  showFeedback("Error deleting link: " + response.message, "error");
               }
            },
            error: function () {
               showFeedback("An error occurred. Please try again.", "error");
            }
         });
      }
   }

   // Toggle experience level visibility
   $("#toggle-experience-level").on("change", function () {
      $(".skill-level").closest("label").toggle(!$(this).is(":checked"));
   });

   // Fetch skills dynamically using AJAX
   function fetchSkills() {
      $.ajax({
         url: "<?php echo site_url('career-services/SkillsController/fetch_skills'); ?>",
         method: "GET",
         dataType: "json",
         success: handleSkillsResponse,
         error: handleAjaxError("fetch skills")
      });
   }
   // Initialize by fetching skills
   fetchSkills();

   // Handle skills response from AJAX request
   function handleSkillsResponse(response) {
      if (response.status === 'success') {
         $(".skill-tags").empty(); // This clears only the skill tags
         response.data.forEach(skill => {
            // Append skill tags only if they are not already present
            if (!$(`.skill-tag:contains('${skill}')`).length) {
               $(".skill-tags").append(createSkillTag(skill));
            }
         });
      } else {
         alert(response.message);
      }
   }

   // Handle AJAX errors
   function handleAjaxError(action) {
      return function () {
         alert(`Error fetching ${action}.`);
      };
   }

   // Create skill tag element
   function createSkillTag(skill) {
      return `<span class="skill-tag">${skill}</span>`;
   }

   // Fetch candidate skills dynamically using AJAX
   function fetchCandidateSkills() {
      $.ajax({
         url: "<?php echo site_url('career-services/SkillsController/fetch_candidate_skills'); ?>",
         method: "GET",
         dataType: "json",
         success: handleCandidateSkillsResponse,
         error: handleAjaxError("fetch candidate skills")
      });
   }

   // Handle candidate skills response
   function handleCandidateSkillsResponse(response) {
      if (response.status === 'success') {
         // Clear only skill entries, not skill tags
         $(".add-skill-section .skill-entry").remove();

         response.data.forEach(skill => {
            addSkillInput(skill);
         });
      } else {
         console.log(response.message);
      }
   }

  // Add skill input dynamically with hidden skill ID
    function addSkillInput(skill) {
        const skillEntryHtml = `
            <div class="skill-entry" data-skill="${skill.skill}">
                <input type="hidden" class="skill-id" value="${skill.id}" />  <!-- Hidden skill ID -->
                <input type="text" value="${skill.skill}" class="skill-input" />
                <label>
                    Level:
                    <select class="skill-level">
                        <option value="beginner" ${skill.level === 'beginner' ? 'selected' : ''}>Beginner</option>
                        <option value="intermediate" ${skill.level === 'intermediate' ? 'selected' : ''}>Intermediate</option>
                        <option value="expert" ${skill.level === 'expert' ? 'selected' : ''}>Expert</option>
                    </select>
                </label>
                <button class="remove-skill-btn">Remove</button>
            </div>
        `;
        $(".add-skill-section").append(skillEntryHtml);
    }

    // Send AJAX request to update skill level and skill text
    $(document).on('input', '.skill-input, .skill-level', function() {
        const skillEntry = $(this).closest('.skill-entry');
        const skill = skillEntry.find('.skill-input').val();
        const level = skillEntry.find('.skill-level').val();
        const skillId = skillEntry.find('.skill-id').val();  // Get the hidden skill ID
       
        const data = {
            skill: skill,
            level: level,
            skill_id: skillId  // Include skill ID in the request
        };
    
        // Send the data via AJAX
        $.ajax({
            url: "<?php echo site_url('career-services/SkillsController/update_skill_level'); ?>",
            type: 'POST',
            data: data,
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    console.log(response.message);
                    // Optionally, show success message on the UI
                } else {
                    console.error(response.message);
                    // Optionally, show error message on the UI
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error: ' + error);
                // Optionally, show error message on the UI
            }
        });
    });


   // Remove skill input field dynamically and delete from the database
    function removeSkillInput(skill) {
        // Remove the input field
        const skillEntry = $(`.skill-entry[data-skill="${skill}"]`);
        const skillId = skillEntry.find('.skill-id').val(); // Get the hidden skill ID
    
        // Remove from database
        $.ajax({
            url: "<?php echo site_url('career-services/SkillsController/delete_skill'); ?>",
            method: "POST",
            data: {
                skill: skill,
                skill_id: skillId  // Include skill ID in the request
            },
            dataType: "json",
            success: function (response) {
                if (response.status === 'success') {
                    loadTemplate();
                    console.log(response.message);
                } else {
                    console.log(response.message);
                }
            },
            error: function () {
                alert("Error deleting skill.");
            }
        });
    }

   // Handle skill tag click (add/remove)
   $(document).on("click", ".skill-tag", function () {
      const skill = $(this).text();
      $(this).toggleClass("selected-tag");
      if ($(this).hasClass("selected-tag")) {
         addSkillInput({
            skill
         });
         insertSkillToDatabase(skill);
         loadTemplate();
      } else {
         removeSkillInput(skill);
         loadTemplate();
         //deleteSkillFromDatabase(skill);
      }
   });

   // Insert skill into database
   function insertSkillToDatabase(skill) {
      $.ajax({
         url: "<?php echo site_url('career-services/SkillsController/insert_skill'); ?>",
         method: "POST",
         data: {
            skill: skill
         },
         dataType: "json",
         success: handleInsertSkillResponse,
         error: handleAjaxError("insert skill")
      });
   }

   // Handle skill insertion response
   function handleInsertSkillResponse(response) {
      if (response.status === 'error') {
         alert(response.message);
      }
   }

   // Add new skill input manually and insert it into the database
    $("#add-skill-btn").on("click", function (e) {
        e.preventDefault();
        const newSkillInput = `
            <div class="skill-entry">
                <input type="text" placeholder="Enter a skill" class="skill-input" />
                <label>
                    Level:
                    <select class="skill-level">
                        <option value="beginner">Beginner</option>
                        <option value="intermediate">Intermediate</option>
                        <option value="expert">Expert</option>
                    </select>
                </label>
                <button class="remove-skill-btn">Remove</button>
            </div>
        `;
    
        // Append the new skill input field
        $(".add-skill-section").append(newSkillInput);
    
        // Get the newly added skill value
        const skillValue = $(".skill-input:last").val();
    
        // Insert the skill into the database
        insertSkillToDatabase(skillValue);
        fetchCandidateSkills();
    });

   // Remove skill entry on click of remove button
   $(document).on("click", ".remove-skill-btn", function () {
      const parentEntry = $(this).closest(".skill-entry");
      const skillValue = parentEntry.find(".skill-input").val();
      removeSkillInput(skillValue);
      parentEntry.remove();
   });

   // Handle skill level change or input
    $(document).on("change", ".skill-level", function () {
       const parentEntry = $(this).closest(".skill-entry");
       const skillId = parentEntry.find(".skill-id").val(); // Get the hidden skill ID
       const skill = parentEntry.find(".skill-input").val(); // Get the skill text
       const level = $(this).val(); // Get the skill level
    
       // Ensure skill ID is defined before updating
       if (skillId) {
          // Update skill level in the database
          updateSkillLevel(skillId, skill, level);
       } else {
          alert("Skill ID is undefined.");
       }
    });
    
    // Update skill level in database
    function updateSkillLevel(skillId, skill, level) {
       $.ajax({
          url: "<?php echo site_url('career-services/SkillsController/update_skill_level'); ?>",
          method: "POST",
          data: {
             skill_id: skillId,
             skill: skill,
             level: level
          },
          dataType: "json",
          success: function (response) {
             if (response.status !== 'success') {
                alert(response.message);
             }
          },
          error: function () {
             alert("Error updating skill level.");
          }
       });
    }
    
   fetchCandidateSkills();

});
</script>
<?php $this->load->view('common/inc/footer');?>
