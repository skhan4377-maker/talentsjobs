<link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
<script src="https://unpkg.com/@heroicons/v2.0.18/24/outline/index.js"></script>
<!-- Right Column - Preview -->
<!-- head में या body के सबसे ऊपर -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.2/html2pdf.bundle.min.js"></script>
  
 <!-- SEO Meta Tags -->
<meta name="description" content="Build your professional resume effortlessly with our state-of-the-art resume builder. Designed for talents and job seekers, create a standout resume to land your dream job.">
<meta name="keywords" content="resume builder, talents, jobs, CV builder, professional resume, career, job search, talent management, career development">
<meta name="author" content="Your Company Name">
<meta name="robots" content="index, follow">
<!-- Open Graph / Facebook -->
<meta property="og:title" content="Professional Resume Builder for Talents & Jobs">
<meta property="og:description" content="Create a stunning resume that showcases your skills and talents. Get noticed by top employers and accelerate your career.">
<meta property="og:image" content="">
<meta property="og:url" content="">
<meta property="og:type" content="website">
<!-- Twitter Card -->
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="Professional Resume Builder for Talents & Jobs">
<meta name="twitter:description" content="Create a standout resume and showcase your talents to land your dream job. Try our resume builder today!">
<meta name="twitter:image" content="">


  <style>
    @media (max-width: 640px) {
      .action-btn {
        padding: 6px 12px; /* Mobile padding */
        font-size: 12px; /* Chhota font size */
      }
    }
	
	.toast-enter {
	  transform: translateX(150%);
	}
	.toast-enter-active {
	  transform: translateX(0);
	  transition: all 0.3s ease-out;
	}


  </style>

	<style>
	.experience-card {
	  border-color: #e5e7eb;
	  transition: all 0.3s ease;
	}

	.experience-card:hover {
	  transform: translateY(-2px);
	}

	#editExperienceBtn {
	  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
	  transition: all 0.3s ease;
	}

	#editExperienceBtn:hover {
	  transform: scale(1.05);
	  box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
	}
	</style>

 <section class="bg-gray-50 py-12">
   <!-- Main Container -->
   <div class="mx-auto p-6">
      <div class="flex flex-col md:flex-row gap-8">
         <!-- Left Column -->
        <div class="w-full md:w-1/2 space-y-8">
            <!-- Resume Form Card -->
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 transition-all duration-300 hover:shadow-xl">
               <div class="p-8">
                  <h3 class="text-3xl font-bold text-gray-900 mb-8 bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">
                     Build Your Professional Resume
                  </h3>
                  <!-- Message container (hidden by default) -->
                  <div id="message" class="p-4 mb-4 text-sm text-blue-700 bg-blue-100 rounded-lg border border-blue-200 shadow" role="alert"></div>
					<form id="resumeForm" enctype="multipart/form-data" class="space-y-6 text-sm text-slate-700">

					  <!-- Profile Photo -->
					  <div class="space-y-2">
						<label class="block font-medium">Profile Photo</label>
						<div class="flex items-center gap-4">
						  <div class="cursor-pointer relative" onclick="profilePhotoUpload_openModal()">
							<div class="w-20 h-20 rounded-full bg-gradient-to-br from-blue-50 to-purple-50 border-2 border-dashed border-blue-200 flex items-center justify-center overflow-hidden transition hover:border-purple-300">
							  <img id="previewImage" src="https://talentsjobs.in/uploads/resume_photos/noimage.png" class="w-full h-full object-cover hidden rounded-full">
							  <svg id="uploadIcon" class="w-6 h-6 text-blue-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
							  </svg>
							</div>
						  </div>
						  <p class="text-xs text-gray-500 leading-snug">
							<strong class="text-blue-600">Upload Requirements:</strong><br>
							• Square (1:1)<br>• Max 5MB<br>• JPG, PNG, GIF
						  </p>
						</div>
					  </div>

					  <!-- Primary Info -->
					  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
						<div>
						  <label for="first_name" class="block font-medium">First Name <span class="text-red-500">*</span></label>
						  <input type="text" id="first_name" name="name" placeholder="John" class="w-full px-3 py-2.5 rounded-lg border border-gray-200 focus:ring-2 focus:ring-blue-500">
						</div>
						<div>
						  <label for="last_name" class="block font-medium">Last Name <span class="text-red-500">*</span></label>
						  <input type="text" id="last_name" name="last_name" placeholder="Doe" class="w-full px-3 py-2.5 rounded-lg border border-gray-200 focus:ring-2 focus:ring-blue-500">
						</div>
						<div class="col-span-full md:col-span-1">
						  <label for="resume_job_profile_input" class="block font-medium">Job Title <span class="text-red-500">*</span></label>
						  <input type="text" id="resume_job_profile_input" name="designations" placeholder="Software Developer" required class="w-full px-3 py-2.5 rounded-lg border border-gray-200 focus:ring-2 focus:ring-blue-500">
						  <input type="hidden" id="resume_job_profile_id">
						  <ul id="resume_job_profile_list" class="absolute z-50 bg-white shadow-lg rounded-b-lg mt-1 max-h-60 overflow-y-auto hidden"></ul>
						</div>
						<div>
						  <label for="email" class="block font-medium">Email <span class="text-red-500">*</span></label>
						  <input type="email" id="email" name="email" placeholder="john@example.com" required class="w-full px-3 py-2.5 rounded-lg border border-gray-200 focus:ring-2 focus:ring-blue-500">
						</div>
						<div>
						  <label for="mobile" class="block font-medium">Phone Number</label>
						  <input type="tel" id="mobile" name="mobile" placeholder="+1 (555) 000-0000" class="w-full px-3 py-2.5 rounded-lg border border-gray-200 focus:ring-2 focus:ring-blue-500">
						</div>
					  </div>

					  <!-- Additional Info -->
					  <div id="additionalDetails" class="space-y-6 hidden transition-all duration-300">
						
						<!-- Location Info -->
						<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
						  <div>
							<label for="resume_city_input" class="block font-medium">City</label>
							<input type="text" id="resume_city_input" name="city_name" placeholder="New York" data-url="<?= base_url('Common/get_search_data') ?>" autocomplete="off" class="w-full px-3 py-2.5 rounded-lg border border-gray-200 focus:ring-2 focus:ring-blue-500">
							<input type="hidden" id="resume_city_id" name="city_id">
							<ul id="resume_city_list" class="absolute z-50 w-full bg-white shadow-lg rounded-b-lg mt-1 max-h-60 overflow-y-auto hidden"></ul>
						  </div>
						  <div>
							<label for="dob" class="block font-medium">Date of Birth</label>
							<input type="date" id="dob" name="dob" class="w-full px-3 py-2.5 rounded-lg border border-gray-200 focus:ring-2 focus:ring-blue-500">
						  </div>
						</div>

						<!-- Country + Place -->
						<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
						  <div>
							<label for="resume_country_input" class="block font-medium">Country</label>
							<input type="text" id="resume_country_input" name="country" placeholder="United States" data-url="<?= base_url('Common/get_countries') ?>" autocomplete="off" class="w-full px-3 py-2.5 rounded-lg border border-gray-200 focus:ring-2 focus:ring-blue-500">
							<input type="hidden" name="country_id" id="resume_country_id">
							<ul id="resume_country_list" class="absolute z-50 w-full bg-white shadow-lg rounded-b-lg mt-1 max-h-60 overflow-y-auto hidden"></ul>
						  </div>
						  <div>
							<label for="placeOfBirth" class="block font-medium">Place of Birth</label>
							<input type="text" id="placeOfBirth" name="placeOfBirth" placeholder="Los Angeles" class="w-full px-3 py-2.5 rounded-lg border border-gray-200 focus:ring-2 focus:ring-blue-500">
						  </div>
						</div>

						<!-- Address -->
						<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
						  <div>
							<label for="postal" class="block font-medium">Postal Code</label>
							<input type="text" id="postal" name="postal" placeholder="10001" class="w-full px-3 py-2.5 rounded-lg border border-gray-200 focus:ring-2 focus:ring-blue-500">
						  </div>
						  <div>
							<label for="address" class="block font-medium">Full Address</label>
							<input type="text" id="address" name="address" placeholder="1234 Elm Street, NY" class="w-full px-3 py-2.5 rounded-lg border border-gray-200 focus:ring-2 focus:ring-blue-500">
						  </div>
						</div>

						<!-- Portfolio -->
						<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
						  <div>
							<label for="portfolioUrl" class="block font-medium">Portfolio URL</label>
							<input type="url" id="portfolioUrl" name="portfolioUrl" placeholder="https://yourportfolio.com" class="w-full px-3 py-2.5 rounded-lg border border-gray-200 focus:ring-2 focus:ring-blue-500">
						  </div>
						</div>

						<!-- Objective -->
						<div>
						  <label for="objective" class="block font-medium">Career Objective</label>
						  <textarea id="objective" name="objective" rows="4" placeholder="Detail your professional goals and aspirations..." class="w-full px-3 py-2.5 rounded-lg border border-gray-200 focus:ring-2 focus:ring-blue-500"></textarea>
						</div>
					  </div>
					  
					  <!-- Toggle Button -->
					  <button type="button" id="toggleDetails" aria-expanded="false" class="w-full flex items-center justify-center gap-2 text-blue-600 hover:text-blue-800 font-medium transition group">
						<span class="group-aria-expanded:hidden">Show Additional Details</span>
						<span class="hidden group-aria-expanded:inline">Hide Additional Details</span>
						<svg class="w-5 h-5 transition-transform group-aria-expanded:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
						</svg>
					  </button>
					</form>

			   </div>
            </div>
			
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100" data-show-for="experienced">
               <div class="p-4 md:p-8">
                  <div class="flex items-center justify-between gap-4 mb-6 md:mb-8">
                     <h2 class="text-lg md:text-2xl font-bold text-gray-900 flex items-center gap-2 truncate">
                        <span class="p-2 md:p-3 bg-purple-100 rounded-xl shrink-0">
                           <svg class="w-5 h-5 md:w-6 md:h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                           </svg>
                        </span>
                       <!-- Employment Section -->
						<span class="truncate" id="employmentTitle">Employment</span>
						<button class="edit-section-btn p-1 hover:bg-gray-100 rounded-lg" data-section="employment">
						  <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
						  </svg>
						</button>
                     </h2>
                     <button id="addEmployment" class="flex items-center gap-2 bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 md:px-5 md:py-3 rounded-xl transition-all duration-300 shadow-sm hover:shadow-md text-sm md:text-base shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                           <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        <span class="hidden md:inline">Add Position</span>
                     </button>
                  </div>
                  <!-- Employment Items -->
                  <div id="employmentContainer" class="space-y-6" >
                     <div id="showEmploymentContainder" class="space-y-6"></div>
                     <!-- Expanded Content -->
                     <div class="hidden mt-6 space-y-6">
                        <!-- Form fields... -->
                     </div>
                  </div>
               </div>
            </div>
			
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100" data-show-for="both">
               <div class="p-4 md:p-8">
                  <div class="flex items-center justify-between gap-4 mb-6 md:mb-8">
                     <h2 class="text-lg md:text-2xl font-bold text-gray-900 flex items-center gap-2 truncate">
                        <span class="p-2 md:p-3 bg-purple-100 rounded-xl shrink-0">
                           <svg class="w-5 h-5 md:w-6 md:h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                           </svg>
                        </span>
                       <!-- Skills Section -->
						<span class="truncate" id="skillsTitle">Skills</span>
						<button class="edit-section-btn p-1 hover:bg-gray-100 rounded-lg" data-section="skills">
						  <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
						  </svg>
						</button>
                     </h2>
                     <button id="addSkillBtn" class="flex items-center gap-2 bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 md:px-5 md:py-3 rounded-xl transition-all duration-300 shadow-sm hover:shadow-md text-sm md:text-base shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                           <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        <span class="hidden md:inline">Add Skill</span>
                     </button>
                  </div>
                  <!-- Skills Container -->
                  <div id="skillsContainer" class="flex flex-wrap gap-3">
                     <!-- Skill items will be added here -->
                  </div>
                  <!-- Add Skill Input (Initially Hidden) -->
                  <div id="skillInputContainer" class="hidden mt-6 space-y-4">
                     <div class="flex flex-col md:flex-row gap-4">
                        <input type="text" id="skillInput" placeholder="Enter skill (e.g., Project Management)" class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 outline-none">
                        <div class="flex gap-3">
                           <button id="saveSkillBtn" class="bg-purple-600 text-white px-6 py-2 rounded-lg hover:bg-purple-700 transition-colors w-full md:w-auto">
                           Save Skill
                           </button>
                           <button id="cancelSkillBtn" class="bg-gray-100 text-gray-600 px-6 py-2 rounded-lg hover:bg-gray-200 transition-colors w-full md:w-auto">
                           Cancel
                           </button>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
						
            <!-- Internship History Card -->
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 mt-10" data-show-for="fresher">
               <div class="p-4 md:p-8">
                  <div class="flex items-center justify-between gap-4 mb-6 md:mb-8">
                     <h2 class="text-lg md:text-2xl font-bold text-gray-900 flex items-center gap-2 truncate">
                        <span class="p-2 md:p-3 bg-blue-100 rounded-xl shrink-0">
                           <svg class="w-5 h-5 md:w-6 md:h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 12h.01M16 12h.01M8 12h.01"/>
                           </svg>
                        </span>
                        <!-- Internship Section -->
						<span class="truncate" id="internshipTitle">Internship</span>
						<button class="edit-section-btn p-1 hover:bg-gray-100 rounded-lg" data-section="internship">
						  <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
						  </svg>
						</button>
                     </h2>
                     <button id="addInternship" class="flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 md:px-5 md:py-3 rounded-xl transition-all duration-300 shadow-sm hover:shadow-md text-sm md:text-base shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                           <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        <span class="hidden md:inline">Add Internship</span>
                     </button>
                  </div>
                  <!-- Internship Items Container -->
                  <div id="internshipContainer" class="space-y-6">
                     <div id="showInternshipContainer" class="space-y-6"></div>
                  </div>
               </div>
            </div>
            
			<!-- Education History Card -->
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 mt-10" data-show-for="both">
               <div class="p-4 md:p-8">
                  <div class="flex items-center justify-between gap-4 mb-6 md:mb-8">
                     <h2 class="text-lg md:text-2xl font-bold text-gray-900 flex items-center gap-2 truncate">
                        <span class="p-2 md:p-3 bg-blue-100 rounded-xl shrink-0">
                           <svg class="w-5 h-5 md:w-6 md:h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 14.5V9.75m0 0l3 3m-3-3l-3 3M4.5 19.5h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z"/>
                           </svg>
                        </span>
                       <!-- Education Section -->
						<span class="truncate" id="educationTitle">Education</span>
						<button class="edit-section-btn p-1 hover:bg-gray-100 rounded-lg" data-section="education">
						  <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
						  </svg>
						</button>
                     </h2>
                     <button id="addEducation" class="flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 md:px-5 md:py-3 rounded-xl transition-all duration-300 shadow-sm hover:shadow-md text-sm md:text-base shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                           <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        <span class="hidden md:inline">Add Education</span>
                     </button>
                  </div>
                  <!-- Education Items Container -->
                  <div id="educationContainer" class="space-y-6">
                     <div id="showEducationContainer" class="space-y-6"></div>
                  </div>
               </div>
            </div>
            
			<!-- Project History Card -->
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 mt-10" data-show-for="experienced">
               <div class="p-4 md:p-8">
                  <div class="flex items-center justify-between gap-4 mb-6 md:mb-8">
                     <h2 class="text-lg md:text-2xl font-bold text-gray-900 flex items-center gap-2 truncate">
                        <span class="p-2 md:p-3 bg-blue-100 rounded-xl shrink-0">
                           <svg class="w-5 h-5 md:w-6 md:h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25z"/>
                           </svg>
                        </span>
                        <!-- Project Section -->
						<span class="truncate" id="projectTitle">Project</span>
						<button class="edit-section-btn p-1 hover:bg-gray-100 rounded-lg" data-section="project">
						  <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
						  </svg>
						</button>
                     </h2>
                     <button id="addProject" class="flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 md:px-5 md:py-3 rounded-xl transition-all duration-300 shadow-sm hover:shadow-md text-sm md:text-base shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                           <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        <span class="hidden md:inline">Add Project</span>
                     </button>
                  </div>
                  <!-- Project Items Container -->
                  <div id="projectContainer" class="space-y-6">
                     <div id="showProjectContainer" class="space-y-6"></div>
                  </div>
               </div>
            </div>
            
			<!-- Certification History Card -->
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 mt-10" data-show-for="experienced">
               <div class="p-4 md:p-8">
                  <div class="flex items-center justify-between gap-4 mb-6 md:mb-8">
                     <h2 class="text-lg md:text-2xl font-bold text-gray-900 flex items-center gap-2 truncate">
                        <span class="p-2 md:p-3 bg-blue-100 rounded-xl shrink-0">
                           <svg class="w-5 h-5 md:w-6 md:h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/>
                           </svg>
                        </span>
                        <!-- Certification Section -->
						<span class="truncate" id="certificationTitle">Certification</span>
						<button class="edit-section-btn p-1 hover:bg-gray-100 rounded-lg" data-section="certification">
						  <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
						  </svg>
						</button>
                     </h2>
                     <button id="addCertification" class="flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 md:px-5 md:py-3 rounded-xl transition-all duration-300 shadow-sm hover:shadow-md text-sm md:text-base shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                           <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        <span class="hidden md:inline">Add Certification</span>
                     </button>
                  </div>
                  <!-- Certification Items Container -->
                  <div id="certificationContainer" class="space-y-6">
                     <div id="showCertificationContainer" class="space-y-6"></div>
                  </div>
               </div>
            </div>
            
			<!-- Language History Card -->
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 mt-10" data-show-for="both">
               <div class="p-4 md:p-8">
                  <div class="flex items-center justify-between gap-4 mb-6 md:mb-8">
                     <h2 class="text-lg md:text-2xl font-bold text-gray-900 flex items-center gap-2 truncate">
                        <span class="p-2 md:p-3 bg-blue-100 rounded-xl shrink-0">
                           <svg class="w-5 h-5 md:w-6 md:h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10.5 21l5.25-11.25L21 21m-9-3h7.5M3 5.621a48.474 48.474 0 016-.371m0 0c1.12 0 2.233.038 3.334.114M9 5.25V3m3.334 2.364C11.176 10.658 7.69 15.08 3 17.502m9.334-12.138c.896.061 1.785.147 2.666.257m-4.589 8.495a18.023 18.023 0 01-3.827-5.802"/>
                           </svg>
                        </span>
                       <!-- Language Section -->
						<span class="truncate" id="languageTitle">Language</span>
						<button class="edit-section-btn p-1 hover:bg-gray-100 rounded-lg" data-section="language">
						  <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
						  </svg>
						</button>
                     </h2>
                     <button id="addLanguage" class="flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 md:px-5 md:py-3 rounded-xl transition-all duration-300 shadow-sm hover:shadow-md text-sm md:text-base shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                           <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        <span class="hidden md:inline">Add Language</span>
                     </button>
                  </div>
                  <!-- Language Items Container -->
                  <div id="languageContainer" class="space-y-6">
                     <div id="showLanguageContainer" class="space-y-6"></div>
                  </div>
               </div>
            </div>
            
			<!-- Course History Card -->
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 mt-10" data-show-for="fresher">
               <div class="p-4 md:p-8">
                  <div class="flex items-center justify-between gap-4 mb-6 md:mb-8">
                     <h2 class="text-lg md:text-2xl font-bold text-gray-900 flex items-center gap-2 truncate">
                        <span class="p-2 md:p-3 bg-blue-100 rounded-xl shrink-0">
                           <svg class="w-5 h-5 md:w-6 md:h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/>
                           </svg>
                        </span>
                       <!-- Course Section -->
						<span class="truncate" id="courseTitle">Course</span>
						<button class="edit-section-btn p-1 hover:bg-gray-100 rounded-lg" data-section="course">
						  <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
						  </svg>
						</button>
                     </h2>
                     <button id="addCourse" class="flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 md:px-5 md:py-3 rounded-xl transition-all duration-300 shadow-sm hover:shadow-md text-sm md:text-base shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                           <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        <span class="hidden md:inline">Add Course</span>
                     </button>
                  </div>
                  <!-- Course Items Container -->
                  <div id="courseContainer" class="space-y-6">
                     <div id="showCourseContainer" class="space-y-6"></div>
                  </div>
               </div>
            </div>
            
			<!-- Award History Card -->
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 mt-10" data-show-for="both">
               <div class="p-4 md:p-8">
                  <div class="flex items-center justify-between gap-4 mb-6 md:mb-8">
                     <h2 class="text-lg md:text-2xl font-bold text-gray-900 flex items-center gap-2 truncate">
                        <span class="p-2 md:p-3 bg-blue-100 rounded-xl shrink-0">
                           <svg class="w-5 h-5 md:w-6 md:h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16.5 18.75h-9m9 0a3 3 0 013 3h-15a3 3 0 013-3m9 0v-3.375c0-.621-.503-1.125-1.125-1.125h-.871M7.5 18.75v-3.375c0-.621.504-1.125 1.125-1.125h.872m5.007 0H9.497m5.007 0a7.454 7.454 0 01-.982-3.172M9.497 14.25a7.454 7.454 0 00.981-3.172M5.25 4.236c-.982.143-1.954.317-2.916.52A6.003 6.003 0 007.73 9.728M5.25 4.236V4.5c0 2.108.966 3.99 2.48 5.228M5.25 4.236V2.721C7.456 2.41 9.71 2.25 12 2.25c2.291 0 4.545.16 6.75.47v1.516M7.73 9.728a6.726 6.726 0 002.748 1.35m8.272-6.842V4.5c0 2.108-.966 3.99-2.48 5.228m2.48-5.492a46.32 46.32 0 012.916.52 6.003 6.003 0 01-5.395 4.972m0 0a6.726 6.726 0 01-2.749 1.35m0 0a6.772 6.772 0 01-3.044 0"/>
                           </svg>
                        </span>
                        <!-- Award Section -->
						<span class="truncate" id="awardTitle">Award</span>
						<button class="edit-section-btn p-1 hover:bg-gray-100 rounded-lg" data-section="award">
						  <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
						  </svg>
						</button>
                     </h2>
                     <button id="addAward" class="flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 md:px-5 md:py-3 rounded-xl transition-all duration-300 shadow-sm hover:shadow-md text-sm md:text-base shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                           <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        <span class="hidden md:inline">Add Award</span>
                     </button>
                  </div>
                  <!-- Award Items Container -->
                  <div id="awardContainer" class="space-y-6">
                     <div id="showAwardContainer" class="space-y-6"></div>
                  </div>
               </div>
            </div>
            
			<!-- Extra Curricular Activities History Card -->
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 mt-10" data-show-for="fresher">
               <div class="p-4 md:p-8">
                  <div class="flex items-center justify-between gap-4 mb-6 md:mb-8">
                     <h2 class="text-lg md:text-2xl font-bold text-gray-900 flex items-center gap-2 truncate">
                        <span class="p-2 md:p-3 bg-blue-100 rounded-xl shrink-0">
                           <svg class="w-5 h-5 md:w-6 md:h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z"/>
                           </svg>
                        </span>
                        <!-- Extra Curricular Activities Section -->
						<span class="truncate" id="extracurricularTitle">Extra Curricular Activities</span>
						<button class="edit-section-btn p-1 hover:bg-gray-100 rounded-lg" data-section="extracurricular">
						  <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
						  </svg>
						</button>
                     </h2>
                     <button id="addActivity" class="flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 md:px-5 md:py-3 rounded-xl transition-all duration-300 shadow-sm hover:shadow-md text-sm md:text-base shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                           <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        <span class="hidden md:inline">Add Activity</span>
                     </button>
                  </div>
                  <!-- Extra Curricular Activities Container -->
                  <div id="activityContainer" class="space-y-6">
                     <div id="showActivityContainer" class="space-y-6"></div>
                  </div>
               </div>
            </div>
            
			<!-- Hobby History Card -->
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 mt-10" data-show-for="both">
               <div class="p-4 md:p-8">
                  <div class="flex items-center justify-between gap-4 mb-6 md:mb-8">
                     <h2 class="text-lg md:text-2xl font-bold text-gray-900 flex items-center gap-2 truncate">
                        <span class="p-2 md:p-3 bg-blue-100 rounded-xl shrink-0">
                           <svg class="w-5 h-5 md:w-6 md:h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.53 16.122a3 3 0 00-5.78 1.128 2.25 2.25 0 01-2.4 2.245 4.5 4.5 0 008.4-2.245c0-.399-.078-.78-.22-1.128zm0 0a15.998 15.998 0 003.388-1.62m-5.043-.025a15.994 15.994 0 011.622-3.395m3.42 3.42a15.995 15.995 0 004.764-4.648l3.876-5.814a1.151 1.151 0 00-1.597-1.597L14.146 6.32a15.996 15.996 0 00-4.649 4.763m3.42 3.42a6.776 6.776 0 00-3.42-3.42"/>
                           </svg>
                        </span>
                        <!-- Hobby Section -->
						<span class="truncate" id="hobbyTitle">Hobby</span>
						<button class="edit-section-btn p-1 hover:bg-gray-100 rounded-lg" data-section="hobby">
						  <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
						  </svg>
						</button>
                     </h2>
                     <button id="addHobby" class="flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 md:px-5 md:py-3 rounded-xl transition-all duration-300 shadow-sm hover:shadow-md text-sm md:text-base shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                           <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        <span class="hidden md:inline">Add Hobby</span>
                     </button>
                  </div>
                  <!-- Hobby Items Container -->
                  <div id="hobbyContainer" class="space-y-6">
                     <div id="showHobbyContainer" class="space-y-6"></div>
                  </div>
               </div>
            </div>
         
		</div>
        
		<!-- Your Right Column -->
		<div class="w-full md:w-1/2 hidden md:block sticky top-6 h-[calc(100vh-2rem)]"> <!-- Height adjusted -->
			  <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-2 transition-all duration-300 hover:shadow-2xl flex flex-col h-full">
                <div class="bg-gray-700 rounded-lg mb-4 shrink-0">
                  <div class="w-full flex items-center justify-between bg-gray-700 p-2 rounded-md">
                     <div class="flex items-center gap-2">
                        <button class="text-white text-sm flex items-center gap-1">
                        <span class="grid grid-cols-2 gap-0.5 w-4 h-4">
                        <span class="bg-white w-1.5 h-1.5"></span>
                        <span class="bg-white w-1.5 h-1.5"></span>
                        <span class="bg-white w-1.5 h-1.5"></span>
                        <span class="bg-white w-1.5 h-1.5"></span>
                        </span>
                        Select template
                        </button>
                     </div>
                     <div class="flex items-center gap-1.5">
                        <button id="downloadBtn"
                           class="export-btn bg-blue-500 text-white px-3 py-1.5 rounded-md text-sm font-medium"
                           data-template-id="<?=$this->uri->segment(2)?>"
                           data-template-type="paid"
                           data-export-type="pdf">
                          Download PDF
                        </button>
                        <div class="relative">
                           <button id="moreOptions" class="bg-blue-500 text-white px-2.5 py-1.5 rounded-md text-sm">•••</button>
                           <div id="dropdown" class="hidden absolute right-0 mt-1 w-32 bg-white text-gray-800 shadow-sm rounded-sm">
                              <button id="downloadDocx" class="flex items-center gap-1.5 px-3 py-1.5 hover:bg-gray-100 text-xs w-full" 
                                 data-template-id="<?//=$this->uri->segment(2)?>" 
                                 data-export-type="docx">
                                 <svg class="w-3.5 h-3.5">
                                    <!-- Word icon SVG -->
                                 </svg>
                                 Save as DOCX
                              </button>
                              <button id="downloadTxt" class="flex items-center gap-1.5 px-3 py-1.5 hover:bg-gray-100 text-xs w-full" 
                                 data-template-id="<?//=$this->uri->segment(2)?>" 
                                 data-export-type="txt">
                                 <svg class="w-3.5 h-3.5">
                                    <!-- Text icon SVG -->
                                 </svg>
                                 Save as TXT
                              </button>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
				 <div class="flex-1 bg-white rounded-lg shadow-sm overflow-hidden">
				  <div id="resumePreview" class="h-full overflow-auto p-1 text-sm">
					<div class="transform scale-90 origin-top-left w-[112%]">
                     <!-- Dynamic resume content here -->
                  </div>
                  </div>
				</div>
            </div>
        </div>		 
      </div>
	</div>
   
   
</section>

	<!-- Mobile Resume Modal (for mobile devices) -->
   <div id="resumeModal" class="fixed inset-0 bg-black bg-opacity-50 backdrop-blur-sm z-50 hidden transition-opacity">
      <div class="absolute bottom-0 w-full bg-white rounded-t-2xl max-h-[90vh] overflow-y-auto sm:bottom-auto sm:top-1/2 sm:left-1/2 sm:-translate-x-1/2 sm:-translate-y-1/2 sm:max-w-md sm:rounded-xl flex flex-col">
         <div class="flex justify-between items-center p-6 border-b">
            <h3 class="text-xl font-bold" id="resumeModalTitle">Complete Form</h3>
            <button onclick="closeResumeModal()" class="text-gray-500 hover:text-gray-700">
               <svg class="w-6 h-6">
                  <use xlink:href="#x-mark" />
               </svg>
            </button>
         </div>
			<form id="resumeFormMobile" enctype="multipart/form-data" class="p-4 flex-grow overflow-y-auto space-y-6 text-sm text-slate-700">
			  <input type="hidden" name="form_source" value="modal_form">

			  <!-- Personal Information -->
			  <div class="space-y-3">
				<h4 class="text-base font-semibold text-gray-800 border-b pb-2">Personal Information</h4>
				<div class="space-y-3">
				  <!-- City -->
				  <div class="relative">
					<label for="resume_mobileCity" class="block font-medium">City</label>
					<input type="text" id="resume_mobileCity" name="city_name" placeholder="City" 
						   data-url="<?= base_url('Common/get_search_data') ?>"
						   class="w-full px-3 py-2.5 rounded-lg border border-gray-200 focus:ring-2 focus:ring-blue-500">
					<input type="hidden" id="resume_mobile_city_id_hidden" name="city_id">
					<ul id="resume_mobile_city_list" class="absolute z-50 w-full bg-white shadow-lg rounded-b-lg mt-1 max-h-60 overflow-y-auto"></ul>
				  </div>

				  <!-- Country -->
				  <div class="relative">
					<label for="resume_mobileCountry" class="block font-medium">Country</label>
					<input type="text" id="resume_mobileCountry" name="country" placeholder="Country"
						   class="w-full px-3 py-2.5 rounded-lg border border-gray-200 focus:ring-2 focus:ring-blue-500" autocomplete="off">
					<input type="hidden" id="resume_mobile_country_id_hidden" name="country_id">
					<ul id="resume_mobile_country_list" class="absolute z-50 w-full bg-white shadow-lg rounded-b-lg mt-1 max-h-60 overflow-y-auto"></ul>
				  </div>
				</div>
			  </div>

			  <!-- Address Details -->
			  <div class="space-y-3">
				<h4 class="text-base font-semibold text-gray-800 border-b pb-2">Address Details</h4>
				<div class="space-y-3">
				  <input type="text" id="resume_mobileAddress" name="address" placeholder="Street Address"
						 class="w-full px-3 py-2.5 rounded-lg border border-gray-200 focus:ring-2 focus:ring-blue-500">
				  <input type="text" id="resume_mobilePostalCode" name="postal" placeholder="Postal / ZIP Code"
						 class="w-full px-3 py-2.5 rounded-lg border border-gray-200 focus:ring-2 focus:ring-blue-500">
				  <input type="text" id="resume_placeOfBirth" name="placeOfBirth" placeholder="Place of Birth"
						 class="w-full px-3 py-2.5 rounded-lg border border-gray-200 focus:ring-2 focus:ring-blue-500">
				</div>
			  </div>

			  <!-- Additional Info -->
			  <div class="space-y-3">
				<h4 class="text-base font-semibold text-gray-800 border-b pb-2">Additional Information</h4>
				<div class="space-y-3">
				  <input type="date" id="resume_dateOfBirth" name="dob"
						 class="w-full px-3 py-2.5 rounded-lg border border-gray-200 focus:ring-2 focus:ring-blue-500">
				  <input type="url" id="resume_portfolioUrl" name="portfolioUrl" placeholder="Portfolio URL"
						 class="w-full px-3 py-2.5 rounded-lg border border-gray-200 focus:ring-2 focus:ring-blue-500">
				</div>
			  </div>

			  <!-- Summary -->
			  <div class="space-y-3">
				<h4 class="text-base font-semibold text-gray-800 border-b pb-2">Profile Summary</h4>
				<textarea id="resume_objective" name="objective" rows="4" placeholder="Professional Summary"
						  class="w-full px-3 py-2.5 rounded-lg border border-gray-200 focus:ring-2 focus:ring-blue-500"></textarea>
			  </div>
			</form>

		 <!-- Sticky Footer with Buttons -->
         <div class="sticky bottom-0 bg-white p-4 border-t flex justify-end space-x-4">
            <button onclick="closeResumeModal()" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">Cancel</button>
            <button type="submit" form="resumeFormMobile" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Save</button>
         </div>
      </div>
   </div>
   
	 
	<!-- Mobile Modal -->
	<div id="itemModal" class="fixed inset-0 bg-black bg-opacity-50 backdrop-blur-sm z-50 hidden transition-opacity">
		  <div class="absolute bottom-0 w-full bg-white rounded-t-xl px-5 py-4 max-h-[90vh] overflow-y-auto text-sm transform scale-[0.96]
					  sm:bottom-auto sm:top-1/2 sm:left-1/2 sm:-translate-x-1/2 sm:-translate-y-1/2 sm:max-w-sm sm:rounded-xl">
			
			<!-- Header -->
			<div class="flex justify-between items-center mb-4">
			  <h3 class="text-base font-semibold" id="modalTitle">Add Employment</h3>
			  <button onclick="closeMobileModal()" class="text-gray-500 hover:text-gray-700">
				<svg class="w-5 h-5">
				  <use xlink:href="#x-mark" />
				</svg>
			  </button>
			</div>

			<!-- Dynamic Form -->
			<form id="modalForm" class="space-y-3">
			  <!-- Content inserted dynamically -->
			</form>
		  </div>
		</div>


	
   <!-- Upload Photo Modal -->
   <div id="profilePhotoUpload_modal" class="fixed inset-0 bg-black bg-opacity-40 backdrop-blur-sm flex items-center justify-center z-50 hidden">
      <div class="bg-white rounded-2xl p-6 w-[90%] max-w-md shadow-2xl relative transition-all transform scale-95">
         <!-- Close Button -->
         <button onclick="profilePhotoUpload_closeModal()" type="button" class="absolute top-3 right-3 text-gray-400 hover:text-gray-600">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
               <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
         </button>
         <!-- Header -->
         <h2 class="text-xl font-semibold text-center text-gray-700 mb-4">Upload Profile Photo</h2>
         <!-- Upload Box -->
         <label for="profilePhotoUpload_fileInput" class="block border-2 border-dashed border-blue-300 rounded-xl p-6 text-center cursor-pointer transition hover:bg-blue-50">
            <div class="flex flex-col items-center space-y-2">
               <svg class="w-12 h-12 text-blue-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round"
                     d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
               </svg>
               <p class="text-sm text-gray-500">Click to browse or drag your photo here</p>
               <span class="text-xs text-gray-400">(JPG, PNG, GIF • Max 5MB)</span>
            </div>
            <input type="file" id="profilePhotoUpload_fileInput" accept="image/*" class="hidden">
         </label>
         <!-- Action Buttons -->
         <div class="mt-6 flex justify-end space-x-3">
            <button onclick="profilePhotoUpload_closeModal()" type="button" class="px-4 py-2 text-sm bg-gray-100 text-gray-600 rounded-lg hover:bg-gray-200 transition">Cancel</button>
            <button id="profilePhotoUpload_uploadBtn" onclick="profilePhotoUpload_uploadFromModal()" class="px-4 py-2 text-sm bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
            Upload
            </button>
         </div>
      </div>
   </div>
   
	<!-- Mobile Preview Button - Right Side -->
	<button 
	  id="openModalButton" 
	  class="md:hidden fixed bottom-8 right-8 bg-gradient-to-br from-blue-500 to-purple-600 text-white p-4 rounded-full shadow-xl hover:shadow-2xl transition-all duration-300 hover:scale-105 z-50">
	  <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
		<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
	  </svg>
	</button>
   
	<div id="previewModal"  class="fixed inset-0 bg-black/60 z-50 hidden">
		<div id="previewModalContent" class="bg-white flex flex-col transition-all duration-300 opacity-0 transform scale-90
				w-full h-[100dvh] mx-0 rounded-none
				md:relative md:top-1/2 md:left-1/2 md:-translate-x-1/2 md:-translate-y-1/2
				md:max-w-md md:max-h-[80vh] md:w-auto md:h-auto md:rounded-2xl md:shadow-2xl">

			 <!-- Header -->
			 <div class="flex justify-between items-center px-6 py-4
				bg-gradient-to-r from-blue-500 to-purple-600
				sticky top-0">
					<h3 class="text-xl font-bold text-white">Resume Preview</h3>
				
				   <!-- Download Buttons Container -->
					<div class="flex items-center gap-2">
						<!-- PDF Button -->
						<button id="downloadBtn" class="export-btn bg-white/20 hover:bg-white/30 text-white px-3 py-1.5 rounded-md text-sm font-medium"
						  data-template-id="<?=$this->uri->segment(2)?>"
						  data-template-type="paid"
						  data-export-type="pdf" data-preview-source="modalContent">
						  PDF
						</button>
						
						<!-- DOCX Button -->
						<button class="export-btn bg-white/20 hover:bg-white/30 text-white px-3 py-1.5 rounded-md text-sm font-medium"
						  data-template-id="<?=$this->uri->segment(2)?>"
						  data-template-type="paid" 
						  data-export-type="docx">
						  DOCX
						</button>
					
						<button onclick="closeModal()"
						   class="text-white hover:text-gray-200 text-2xl leading-none">
						&times;
						</button>
					</div>
			 </div>
			 <!-- Content -->
			 <div id="modalContent" class="p-2 flex-1 overflow-y-auto">
				<div class="transform scale-90 origin-top-left w-[112%]">
				<!-- dynamically injected HTML goes here -->
			 </div>
		  </div>	  
		</div>
		</div>
   
   
   <!-- SVG Icons (only one set needed) -->
   <svg xmlns="http://www.w3.org/2000/svg" class="hidden">
      <!-- X Mark Icon -->
      <symbol id="x-mark" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none">
         <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
      </symbol>
      <!-- Briefcase Icon -->
      <symbol id="briefcase" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none">
         <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 14.15v4.25c0 1.094-.787 2.036-1.872 2.18-2.087.277-4.216.42-6.378.42s-4.291-.143-6.378-.42c-1.085-.144-1.872-1.086-1.872-2.18v-4.25m16.5 0a2.18 2.18 0 00.75-1.661V8.706c0-1.081-.768-2.015-1.837-2.175a48.114 48.114 0 00-3.413-.387m4.5 8.006c-.194.165-.42.295-.673.38A23.978 23.978 0 0112 15.75c-2.648 0-5.195-.429-7.577-1.22a2.016 2.016 0 01-.673-.38m0 0A2.18 2.18 0 013 12.489V8.706c0-1.081.768-2.015 1.837-2.175a48.111 48.111 0 013.413-.387m7.5 0V5.25A2.25 2.25 0 0013.5 3h-3a2.25 2.25 0 00-2.25 2.25v.894m7.5 0a48.667 48.667 0 00-7.5 0M12 12.75h.008v.008H12v-.008z"/>
      </symbol>
      <!-- Pencil Icon -->
      <symbol id="pencil" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none">
         <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.863 4.487zm0 0L19.5 7.125"/>
      </symbol>
      <!-- Chevron Down Icon -->
      <symbol id="chevron-down" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none">
         <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/>
      </symbol>
   </svg>


	<!-- Experience Selection Modal -->
	<div id="experienceModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-[60] hidden flex items-center justify-center p-3">
	  <div class="bg-white rounded-2xl w-full max-w-md p-4 sm:p-6 relative mx-2 transform scale-95 transition-all">
		
		<!-- Header -->
		<div class="text-center mb-4 sm:mb-6">
		  <h3 class="text-lg sm:text-2xl font-bold text-gray-800 mb-1 sm:mb-2">Select Your Experience Level</h3>
		  <p class="text-gray-500 text-sm sm:text-base">Help us tailor your resume experience</p>
		</div>

		<!-- Selection Cards -->
		<div class="grid grid-cols-2 gap-3 sm:gap-4">
		  
		  <!-- Fresher -->
		  <div onclick="selectExperience('fresher')"
			class="experience-card border-2 hover:border-purple-500 cursor-pointer rounded-xl p-4 sm:p-6 transition-all"
			data-value="fresher">
			<div class="text-center">
			  <div class="mx-auto mb-3 w-10 h-10 sm:w-12 sm:h-12 bg-purple-100 rounded-lg flex items-center justify-center">
				<svg class="w-5 h-5 sm:w-6 sm:h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
				  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/>
				  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/>
				</svg>
			  </div>
			  <h4 class="text-sm sm:text-lg font-semibold mb-1">Fresher</h4>
			  <p class="text-xs sm:text-sm text-gray-500">Recently graduated or less than 1 year experience</p>
			</div>
		  </div>

		  <!-- Experienced -->
		  <div onclick="selectExperience('experienced')"
			class="experience-card border-2 hover:border-blue-500 cursor-pointer rounded-xl p-4 sm:p-6 transition-all"
			data-value="experienced">
			<div class="text-center">
			  <div class="mx-auto mb-3 w-10 h-10 sm:w-12 sm:h-12 bg-blue-100 rounded-lg flex items-center justify-center">
				<svg class="w-5 h-5 sm:w-6 sm:h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
				  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
				</svg>
			  </div>
			  <h4 class="text-sm sm:text-lg font-semibold mb-1">Experienced</h4>
			  <p class="text-xs sm:text-sm text-gray-500">1+ years of professional work experience</p>
			</div>
		  </div>
		</div>

		<!-- Footer -->
		<div class="mt-4 sm:mt-6 text-center">
		  <p class="text-xs sm:text-sm text-gray-500">You can change this later in settings</p>
		</div>
	  </div>
	</div>
	

	<!-- Add a hidden field for template_id if needed -->
	<input type="hidden" id="templateId" value="<?=$this->uri->segment(2)?>">
	<!-- Floating Edit Button -->
	<button 
	  id="editExperienceBtn"
	  class="fixed bottom-6 left-6 md:left-auto md:right-6 bg-white border shadow-lg p-3 rounded-full hover:bg-gray-50 hidden z-50"
	  onclick="showExperienceModal()">
	  <svg class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
		<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
	  </svg>
	</button>
	
	
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Attach click handler to all export buttons
    document.querySelectorAll('.export-btn').forEach(button => {
        button.addEventListener('click', function () {
            const templateId = this.dataset.templateId;
            const exportType = this.dataset.exportType;
            const templateType = this.dataset.templateType;

            // Simple validation
            if (!templateId || !exportType) {
                alert('Invalid export request.');
                return;
            }

            // Disable button to prevent multiple clicks
            this.disabled = true;
            this.innerText = 'Processing...';

            // AJAX call
            fetch('<?= base_url('website/services/ResumeDownload/handle_export') ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: new URLSearchParams({
                    template_id: templateId,
                    export_type: exportType
                })
            })
            .then(response => response.json())
            .then(data => {
                this.disabled = false;
                this.innerText = exportType.toUpperCase();

                if (data.status === 'ok' && data.download_url) {
                    window.open(data.download_url, '_blank');
                } else if (data.status === 'redirect' && data.url) {
                    window.location.href = data.url;
                } else {
                    alert(data.message || 'Something went wrong.');
                }
            })
            .catch(err => {
                console.error(err);
                alert('Export failed. Please try again.');
                this.disabled = false;
                this.innerText = exportType.toUpperCase();
            });
        });
    });
});
</script>


<script>
// Define base URL (you can also set it dynamically via PHP)
var base_url = "<?= base_url('website/services/ResumeBuilder/'); ?>";

// ---------------------------
// Cookie Functions
// ---------------------------
function setCookie(name, value, days) {
  const date = new Date();
  date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
  const expires = "expires=" + date.toUTCString();
  document.cookie = name + "=" + value + ";" + expires + ";path=/";
}

function getCookie(name) {
  const nameEQ = name + "=";
  const ca = document.cookie.split(';');
  for (let i = 0; i < ca.length; i++) {
    let c = ca[i];
    while (c.charAt(0) === ' ') c = c.substring(1);
    if (c.indexOf(nameEQ) === 0) return c.substring(nameEQ.length);
  }
  return null;
}

// ---------------------------
// Experience Selection Functions
// ---------------------------
function selectExperience(type) {
  setCookie('userExperience', type, 30);
  toggleSections(type);
  updateModalVisuals(type);
  hideExperienceModal();
  document.getElementById('editExperienceBtn').classList.remove('hidden');

  // Send to server via AJAX
  updateWorkStatusOnServer(type);
}

function updateModalVisuals(selectedType) {
  document.querySelectorAll('.experience-card').forEach(card => {
    card.classList.remove(
      'border-purple-500', 'bg-purple-50',
      'border-blue-500', 'bg-blue-50'
    );
    if (card.dataset.value === selectedType) {
      if (selectedType === 'fresher') {
        card.classList.add('border-purple-500', 'bg-purple-50');
      } else {
        card.classList.add('border-blue-500', 'bg-blue-50');
      }
    }
  });
}

function toggleSections(experienceLevel) {
  const allCards = document.querySelectorAll('[data-show-for]');
  allCards.forEach(card => {
    const showFor = card.getAttribute('data-show-for');
    const shouldShow = 
      experienceLevel === 'fresher' ?
      (showFor === 'fresher' || showFor === 'both') :
      (showFor === 'experienced' || showFor === 'both');
    card.style.display = shouldShow ? 'block' : 'none';
  });

  window.scrollTo({ top: 0, behavior: 'smooth' });
}

// ---------------------------
// Modal Control
// ---------------------------
function showExperienceModal() {
  const currentExp = getCookie('userExperience') || 'fresher';
  updateModalVisuals(currentExp);
  document.getElementById('experienceModal').classList.remove('hidden');
}

function hideExperienceModal() {
  document.getElementById('experienceModal').classList.add('hidden');
}

// ---------------------------
// AJAX to update work_status
// ---------------------------
function updateWorkStatusOnServer(type) {
  $.ajax({
    url: base_url + 'update_work_status',
    method: 'POST',
    data: {
      experience_type: type
    },
    success: function(response) {
      console.log('Work status updated:', response);
    },
    error: function(xhr) {
      console.error('Update failed:', xhr.responseText);
    }
  });
}

// ---------------------------
// Initialization on Load
// ---------------------------
window.addEventListener('load', () => {
  const experience = getCookie('userExperience') || 'fresher';

  toggleSections(experience);

  if (getCookie('userExperience')) {
    updateModalVisuals(experience);
    document.getElementById('editExperienceBtn').classList.remove('hidden');
  } else {
    showExperienceModal();
  }
});

// ---------------------------
// Edit Button Click Event
// ---------------------------
document.getElementById('editExperienceBtn').addEventListener('click', showExperienceModal);
</script>



<script>
	
	async function loadSectionTitle(section) {
		try {
			const titleElement = document.getElementById(`${section}Title`);
			
			// सेक्शन एलिमेंट चेक करें और कंटेंट वेरिफाई करें
			if (!titleElement || titleElement.textContent.trim() === '') {
				console.log(`Skipping empty section: ${section}`);
				return;
			}

			const formData = new FormData();
			formData.append('section', section);

			const response = await fetch('<?= base_url("website/services/ResumeBuilder/get_section_title") ?>', {
				method: 'POST',
				body: formData
			});

			const result = await response.json();
			if (result.success && result.title) {
				titleElement.textContent = result.title;
			}
		} catch (error) {
			console.error(`Failed to load title for ${section}:`, error);
		}
	}
	
	// Call individually for each section (no loop)
	loadSectionTitle('employment');
	loadSectionTitle('skills');
	loadSectionTitle('internship');
	loadSectionTitle('education');
	loadSectionTitle('project');
	loadSectionTitle('certification');
	loadSectionTitle('language');
	loadSectionTitle('course');
	loadSectionTitle('award');
	loadSectionTitle('extracurricular');
	loadSectionTitle('hobby');

	let isEditingTitle = false;

	// जेनरिक एडिट हैण्डलर
	document.querySelectorAll('.edit-section-btn').forEach(button => {
		button.addEventListener('click', function() {
			const section = this.dataset.section;
			const titleElement = document.getElementById(`${section}Title`);
			const originalTitle = titleElement.textContent;

			const input = document.createElement('input');
			input.type = 'text';
			input.value = originalTitle;
			input.className = 'font-bold bg-transparent border-b-2 border-purple-500 outline-none';
			
			titleElement.replaceWith(input);
			input.focus();

			const saveTitle = async () => {
				const newTitle = input.value.trim();
				try {
					const formData = new FormData();				
					formData.append('section', section.toLowerCase()); 
					formData.append('title', newTitle);

					const response = await fetch('<?= base_url("website/services/ResumeBuilder/update_section_title") ?>', {
						method: 'POST',
						body: formData
					});

					const result = await response.json();
					
					if (!result.success) {
						throw new Error(result.message || 'Server error');
					}

					// Refresh the updated title after successful insert/update
					loadSectionTitle(section);

					// Replace input back with span
					const titleElement = document.createElement('span');
					titleElement.id = `${section}Title`;
					titleElement.className = 'truncate';
					titleElement.textContent = newTitle;
					input.replaceWith(titleElement);

				} catch (error) {
					console.error(`Error: ${error.message}`);
				}
			};

			input.addEventListener('blur', saveTitle);
			input.addEventListener('keydown', (e) => {
				if (e.key === 'Enter') saveTitle();
				if (e.key === 'Escape') {
					titleElement.textContent = originalTitle;
					input.replaceWith(titleElement);
				}
			});
		});
	});
</script>

<script>
  // Utility function to detect mobile devices.
  const isMobile = () => /Mobi|Android/i.test(navigator.userAgent);

  // Global variable to track the currently edited employment item in mobile mode.
  let currentMobileItem = null;

  // Toggle Additional Details for Desktop form.
  document.getElementById('toggleDetails').addEventListener('click', function () {
    const detailsDiv = document.getElementById('additionalDetails');
    if (isMobile()) {
      openResumeModal();
    } else {
      if (detailsDiv.style.display === 'none' || detailsDiv.style.display === '') {
        detailsDiv.style.display = 'block';
        this.textContent = 'Show Less Fields';
      } else {
        detailsDiv.style.display = 'none';
        this.textContent = 'Show More Fields';
      }
    }
  });

  // Mobile: Open and close resume modal functions.
  function openResumeModal() {
    document.getElementById('resumeModal').classList.remove('hidden');
  }
  
  function closeResumeModal() {
    document.getElementById('resumeModal').classList.add('hidden');
  }
    
  // Debounce function: delays function execution until after delay milliseconds
	function debounce(func, delay) {
    let timer;
    return function(...args) {
      clearTimeout(timer);
      timer = setTimeout(() => {
        func.apply(this, args);
      }, delay);
    };
   }  

	document.getElementById('previewModal').addEventListener('click', function(e) {
	  if (e.target === this) closeModal();
	});

	function openModal() {
	  const modal = document.getElementById('previewModal');
	  const box = document.getElementById('previewModalContent');
	  modal.classList.remove('hidden');
	  requestAnimationFrame(() => {
		box.classList.remove('scale-90', 'opacity-0');
		box.classList.add('scale-100', 'opacity-100');
	  });
	}

	function closeModal() {
	  const modal = document.getElementById('previewModal');
	  const box = document.getElementById('previewModalContent');
	  box.classList.remove('scale-100', 'opacity-100');
	  box.classList.add('scale-90', 'opacity-0');
	  box.addEventListener('transitionend', () => {
		modal.classList.add('hidden');
	  }, { once: true });
	}

	// Mobile modal close function.
	function closeMobileModal() {
		document.getElementById('itemModal').classList.add('hidden');
	  }
    
    // Toast notification function
    function showToast(message, type = 'info') {
      const toast = document.createElement('div');
      toast.className = `fixed bottom-4 right-4 z-[9999] px-6 py-3 rounded-lg text-white shadow-lg transition-transform transform translate-x-0 ${
        type === 'success' ? 'bg-green-500' : 
        type === 'error' ? 'bg-red-500' : 'bg-blue-500'
      }`;
      toast.textContent = message;
      document.body.appendChild(toast);

      setTimeout(() => {
        toast.style.transform = 'translateX(150%)';
        setTimeout(() => toast.remove(), 300);
      }, 3000);
    }       	

	
// Fetch candidate details and populate both forms.
 document.addEventListener('DOMContentLoaded', () => {    
  
 // Job Profile
const resumeJobProfileInput = document.querySelector('#resume_job_profile_input');
if (resumeJobProfileInput) {
  new AutoCompleteWidget({
    inputSelector: '#resume_job_profile_input',
    hiddenSelector: '#resume_job_profile_id',
    listSelector: '#resume_job_profile_list',
    apiUrl: '<?= base_url("Common/get_search_data?type=job_profile") ?>',
    minChars: 1,
    multiSelect: false,
    maxResults: 5
  });
}

// City (Desktop)
const resumeCityInput = document.querySelector('#resume_city_input');
if (resumeCityInput) {
  new AutoCompleteWidget({
    inputSelector: '#resume_city_input',
    hiddenSelector: '#resume_city_id',
    listSelector: '#resume_city_list',
    apiUrl: '<?= base_url("Common/get_cities") ?>',
    minChars: 2,
    multiSelect: false,
    maxResults: 10
  });
}

// City (Mobile)
const resumeMobileCityInput = document.querySelector('#resume_mobileCity');
if (resumeMobileCityInput) {
  new AutoCompleteWidget({
    inputSelector: '#resume_mobileCity',
    hiddenSelector: '#resume_mobile_city_id_hidden',
    listSelector: '#resume_mobile_city_list',
    apiUrl: '<?= base_url("Common/get_cities") ?>',
    minChars: 2,
    multiSelect: false,
    maxResults: 10
  });
}

// Country (Desktop)
const resumeCountryInput = document.querySelector('#resume_country_input');
if (resumeCountryInput) {
  new AutoCompleteWidget({
    inputSelector: '#resume_country_input',
    hiddenSelector: '#resume_country_id',
    listSelector: '#resume_country_list',
    apiUrl: '<?= base_url("Common/get_countries") ?>',
    minChars: 2,
    multiSelect: false,
    maxResults: 10
  });
}

// Country (Mobile)
const resumeMobileCountryInput = document.querySelector('#resume_mobileCountry');
if (resumeMobileCountryInput) {
  new AutoCompleteWidget({
    inputSelector: '#resume_mobileCountry',
    hiddenSelector: '#resume_mobile_country_id_hidden',
    listSelector: '#resume_mobile_country_list',
    apiUrl: '<?= base_url("Common/get_countries") ?>',
    minChars: 2,
    multiSelect: false,
    maxResults: 10
  });
}

	
	   
		// Define baseUrl using PHP's base_url() helper
		const baseUrl = "<?= base_url() ?>";
		// 1) Grab the modal & button elements
		const previewModal    = document.getElementById('previewModal');
		const openModalButton = document.getElementById('openModalButton');
		const closeModalBtns  = previewModal.querySelectorAll('button');
		

	  // Make modal functions accessible globally
	  window.profilePhotoUpload_openModal = function () {
		document.getElementById('profilePhotoUpload_modal').classList.remove('hidden');
	  };

	  window.profilePhotoUpload_closeModal = function () {
		document.getElementById('profilePhotoUpload_modal').classList.add('hidden');
	  };

	  window.profilePhotoUpload_previewImage = function (fileUrl) {
		const previewImage = document.getElementById('previewImage');
		if (previewImage) {
		  previewImage.src = fileUrl;
		  previewImage.classList.remove('hidden');
		}
	  };
		
	  window.profilePhotoUpload_uploadFromModal = function () {
		const fileInput = document.getElementById('profilePhotoUpload_fileInput');
		const file = fileInput.files[0];
		if (!file) {
		  showToast('Please choose a file before uploading.', 'error');
		  return;
		}

		const uploadButton = document.getElementById('profilePhotoUpload_uploadBtn');
		uploadButton.disabled = true;
		uploadButton.innerHTML = '<span class="spinner"></span> Uploading...';

		const formData = new FormData();
		formData.append('photo', file);

		$.ajax({
		  url: '<?=base_url('website/services/ResumeBuilder/ajax_upload_photo')?>',
		  type: 'POST',
		  data: formData,
		  contentType: false,
		  processData: false,
		  dataType: 'json',
		  success: function (response) {
			if (response.status === 'success') {
			  profilePhotoUpload_previewImage(response.photoUrl);
			  showToast('Photo uploaded successfully!', 'success');
			  updatePreview();
			  profilePhotoUpload_closeModal();
			} else {
			  showToast(response.message, 'error');
			}
		  },
		  error: function () {
			showToast('Connection error - please check network', 'error');
		  },
		  complete: function () {
			uploadButton.disabled = false;
			uploadButton.innerHTML = 'Upload';
			fileInput.value = '';
		  }
		});
	 };
	
	// 3) Attach click handlers
	openModalButton.addEventListener('click', () => {
	  // On mobile, trigger a fresh preview, then open
	  updatePreview();      // reuse your existing preview logic
	  openModal();
	});

	// Close when any “✕” button is clicked
	closeModalBtns.forEach(btn => {
	  btn.addEventListener('click', closeModal);
	});


    function updatePreview() {
      console.log("Updating preview...");
      const formData = new FormData(document.getElementById('resumeForm'));
      // Append template ID from the hidden input field
      const templateId = document.getElementById('templateId').value;
      formData.append('template_id', templateId);
		
		
      fetch('<?= base_url("website/services/ResumeBuilder/preview"); ?>', {
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
        // choose container based on device
		const container = isMobile()
		  ? document.getElementById('modalContent')
		  : document.getElementById('resumePreview');

		container.innerHTML = html;
      })
      .catch(error => {
        console.error(error);
      });
    }

    // Initial preview update
    updatePreview();
     
	fetch(`${baseUrl}website/services/ResumeBuilder/extractCandidateDetails`)
    .then(response => response.json())
    .then(json => {
      if (json.success && json.data) {
        const candidate = json.data;
        
        // Transform backend keys to match the form field names.
        const transformedData = {
          name: candidate.name,
          last_name: candidate.last_name,
          email: candidate.email,
          designations: candidate.designations,
          mobile: candidate.mobile,
          city_name: candidate.city_name || '',
          city_id: candidate.city_id || '',
          country: candidate.country || '',
          country_id: candidate.country_id || '',
          nationality: candidate.nationality || '',       
          placeOfBirth: candidate.placeOfBirth || '',
          postal: candidate.postal || '',
          address: candidate.address || '',
          portfolioUrl: candidate.portfolioUrl || '',
          //linkedinProfile: candidate.linkedinProfile || '',
          dob: candidate.dob || '',
          //resume_headline: candidate.resume_headline || '',
          objective: candidate.objective || '',
          // Assuming candidate.logo holds the relative URL to the photo.
          photoUrl: candidate.logo || ''
        };

        const desktopMapping = {
          name: 'input[name="name"]',
          last_name: 'input[name="last_name"]',
          email: 'input[name="email"]',
          designations: 'input[name="designations"]',
          mobile: 'input[name="mobile"]',
          city_name: 'input[name="city_name"]',
          city_id: 'input[name="city_id"]',
          country: 'input[name="country"]',
          country_id: 'input[name="country_id"]',
          nationality: 'input[name="nationality"]',        
          placeOfBirth: 'input[name="placeOfBirth"]',
          postal: 'input[name="postal"]',
          address: 'input[name="address"]',
          portfolioUrl: 'input[name="portfolioUrl"]',
          //linkedinProfile: 'input[name="linkedinProfile"]',
          dob: 'input[name="dob"]',
          //resume_headline: 'textarea[name="resume_headline"]',
          objective: 'textarea[name="objective"]'
        };

        const mobileMapping = {
          city_name: 'input[name="city_name"]',
          city_id: 'input[name="city_id"]',
          country: 'input[name="country"]',
          country_id: 'input[name="country_id"]',
          nationality: 'input[name="nationality"]', 
          address: 'input[name="address"]',
          postal: 'input[name="postal"]',
          placeOfBirth: 'input[name="placeOfBirth"]',
          dob: 'input[name="dob"]',
          portfolioUrl: 'input[name="portfolioUrl"]',
          //linkedinProfile: 'input[name="linkedinProfile"]',
          objective: 'textarea[name="objective"]',
          //resume_headline: 'textarea[name="resume_headline"]'
        };

        // --- Desktop Form Population ---
        const desktopForm = document.getElementById('resumeForm');
        if (desktopForm) {
          Object.keys(desktopMapping).forEach(key => {
            const field = desktopForm.querySelector(desktopMapping[key]);
            if (field) {
              field.value = transformedData[key] || '';
            }
          });

          // Dynamically update the photo preview if a URL is provided.
          if (transformedData.photoUrl) {
            const previewImage = desktopForm.querySelector('#previewImage');
            if (previewImage) {
              // Prepend baseUrl to the relative photo URL.
              previewImage.src = baseUrl + transformedData.photoUrl;
              previewImage.classList.remove('hidden');
            }
            // Optionally hide the upload icon if it exists.
            const uploadIcon = desktopForm.querySelector('#uploadIcon');
            if (uploadIcon) {
              uploadIcon.style.display = 'none';
            }
          }
        }
        
        // --- Mobile Form Population ---
        const mobileForm = document.getElementById('resumeFormMobile');
        if (mobileForm) {
          Object.keys(mobileMapping).forEach(key => {
            const field = mobileForm.querySelector(mobileMapping[key]);
            if (field) {
              field.value = transformedData[key] || '';
            }
          });
        }
      } else {
        console.error('Candidate details not found:', json.message);
      }
    })
    .catch(error => console.error('Error fetching candidate details:', error));
      
    // Debounced function to send all form data
    const debouncedUpdate = debounce(async () => {
      console.log("Debounced update triggered...");
	  $('#message').html('<p>Saving...</p>');
      const form = document.getElementById('resumeForm');
      const formData = new FormData(form);
      const templateId = document.getElementById('templateId').value;
      // Add required parameters
      formData.append('form_source', 'main_form');
      formData.append('template_id', templateId);
      try {
        const response = await fetch('<?=base_url("website/services/ResumeBuilder/update_resume_template")?>', {
          method: 'POST',
          body: formData
        });
        const json = await response.json();
        if (json.success) {
          showToast(json.message || 'Profile saved successfully!', 'success');
		 // Update saving message to saved
		 $('#message').html('<p>Saved</p>');
	  
          closeResumeModal();
          // Refresh the preview after successful update
          updatePreview();
        } else {
          showToast(json.message || 'Failed to save profile', 'error');
        }
      } catch (error) {
        showToast('Connection error - please check network', 'error');
      }
    }, 1000);

    // Attach input listeners for all fields in the desktop form
    document.querySelectorAll('#resumeForm input, #resumeForm textarea, #resumeForm select').forEach(element => {
      ['input', 'change'].forEach(eventType => {
        element.addEventListener(eventType, debouncedUpdate);
      });
    });

    // Mobile form submission remains the same
    async function handleFormSubmit(event) {
      event.preventDefault();
	  // Show saving message for mobile submission if needed
	  $('#message').html('<p>Saving...</p>');
  
      const formData = new FormData(event.target);
      const templateId = document.getElementById('templateId').value;    
      formData.append('form_source', 'modal_form');
      formData.append('template_id', templateId);
      try {
        const response = await fetch('<?=base_url("website/services/ResumeBuilder/update_resume_template")?>', {
          method: 'POST',
          body: formData
        });
        const json = await response.json();
        if (json.success) {
          showToast(json.message || 'Changes saved successfully!', 'success');
           // Optionally update the preview after mobile form submit too.
		  // Update message after success
          $('#message').html('<p>Saved</p>');	  
          updatePreview();

        } else {
          showToast(json.message || 'Failed to save changes', 'error');
        }
      } catch (error) {
        showToast('Network error - please try again', 'error');
      }
    }

    // Attach the submit handler to the mobile modal form.
    const mobileForm = document.getElementById('resumeFormMobile');
    if (mobileForm) {
      mobileForm.addEventListener('submit', handleFormSubmit);
    }
	    
   // Function to attach click events for the Save buttons in each employment item.
   function attachSaveButtonEvents() {
    document.querySelectorAll('.employment-item .update-btn').forEach(button => {
      button.addEventListener('click', (e) => {
        e.stopPropagation();
        const parentItem = button.closest('.employment-item');
        const data = {
          employment_id: parentItem.querySelector('.employment_id').value.trim(),
          job_title: parentItem.querySelector('.job_title').value.trim(),
          employer_name: parentItem.querySelector('.employer_name').value.trim(),
          start_date: parentItem.querySelector('.start_date').value.trim(),
          end_date: parentItem.querySelector('.end_date').value.trim(),
          is_current: parentItem.querySelector('.is_current').checked ? 1 : 0,
          job_type: parentItem.querySelector('.job_type').value.trim(),
          work_location: parentItem.querySelector('.work_location').value.trim(),
          responsibilities: parentItem.querySelector('.responsibilities').value.trim()
        };
        saveEmploymentRecordCommon(data, parentItem);
      });
    });
   }

  // Load Employment Data and render items.
  async function loadEmploymentData() {
    try {
      const formData = new FormData();
      const templateId = document.getElementById('templateId').value;
      formData.append('template_id', templateId);

      const response = await fetch('<?= base_url("website/services/ResumeBuilder/fetch_work_experiences"); ?>', {
        method: 'POST',
        body: formData
      });

      if (!response.ok) {
        throw new Error(`Network error: ${response.statusText}`);
      }

      const data = await response.json();

      if (data.success) {
        const container = document.getElementById('showEmploymentContainder');
        container.innerHTML = ''; // Clear existing content

        data.employments.forEach(employment => {
          const {
            id = '',
            job_title = '',
            employer_name = '',
            start_date = '',
            end_date = '',
            job_type = '',
            work_location = '',
            is_current = '',
            responsibilities = ''
          } = employment;

          // Process responsibilities.
          let responsibilitiesHtml = '';
          if (Array.isArray(responsibilities)) {
            responsibilitiesHtml = responsibilities.map(line => `• ${line}`).join('<br>');
          } else {
            responsibilitiesHtml = responsibilities;
          }

         // Render each employment item with data attributes for later use.
		// Render each employment item with data attributes for later use.
		const itemHtml = `
		  <div class="employment-item border-l-4 border-purple-600 pl-6 py-4 rounded-lg transition-all duration-300 hover:border-purple-700 hover:bg-gray-50"
			   data-id="${id}"
			   data-job-type="${job_type}"
			   data-work-location="${work_location}"
			   data-start-date="${start_date}"
			   data-end-date="${end_date}"
			   data-is-current="${employment.is_current}"
			   data-responsibilities="${responsibilities}">
			<div class="header cursor-pointer flex justify-between items-center">
			  <div>
				<h3 class="text-lg font-semibold text-gray-900">${job_title}</h3>
				<p class="text-sm text-gray-500 mt-1">
				  ${employer_name} • ${job_type} • ${start_date} - ${employment.is_current == 1 ? "Current" : end_date}
				</p>
			  </div>
			  <div class="arrow-icon transform transition-transform duration-300">
				<svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
				  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" 
						d="M19 9l-7 7-7-7"/>
				</svg>
			  </div>
			</div>
			<div class="content hidden mt-4 space-y-4">
			  <input type="hidden" class="employment_id" value="${id}">
			  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
				<div>
				  <label class="block text-sm font-medium text-gray-700 mb-2">Job Title</label>
				  <input type="text" class="job_title w-full px-3 py-2 border rounded-lg" value="${job_title}" placeholder="Job Title">
				</div>
				<div>
				  <label class="block text-sm font-medium text-gray-700 mb-2">Employer Name</label>
				  <input type="text" class="employer_name w-full px-3 py-2 border rounded-lg" value="${employer_name}" placeholder="Employer Name">
				</div>
			  </div>
			  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
				<div>
				  <label class="block text-sm font-medium text-gray-700 mb-2">Start Date (MM/YYYY)</label>
				  <input type="month" class="start_date w-full px-3 py-2 border rounded-lg" value="${start_date}" placeholder="MM/YYYY">
				</div>
				<div>
				  <label class="block text-sm font-medium text-gray-700 mb-2">End Date (MM/YYYY)</label>
				  <input type="month" class="end_date w-full px-3 py-2 border rounded-lg" value="${end_date}" placeholder="MM/YYYY">
				</div>
				<div>
				  <label class="block text-sm font-medium text-gray-700 mb-2">Is Current?</label>
				  <input type="checkbox" class="is_current" ${employment.is_current == 1 ? 'checked' : ''} value="1">
				</div>
			  </div>
			  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
				<div>
				  <label class="block text-sm font-medium text-gray-700 mb-2">Job Type</label>
				  <select class="job_type w-full px-3 py-2 border rounded-lg">
					<option value="Full-Time" ${job_type === "Full-Time" ? "selected" : ""}>Full-Time</option>
					<option value="Part-Time" ${job_type === "Part-Time" ? "selected" : ""}>Part-Time</option>
					<option value="Contract" ${job_type === "Contract" ? "selected" : ""}>Contract</option>
					<option value="Internship" ${job_type === "Internship" ? "selected" : ""}>Internship</option>
					<option value="Temporary" ${job_type === "Temporary" ? "selected" : ""}>Temporary</option>
				  </select>
				</div>
				<div>
				  <label class="block text-sm font-medium text-gray-700 mb-2">Work Location</label>
				  <input type="text" class="work_location w-full px-3 py-2 border rounded-lg" value="${work_location}" placeholder="Work Location">
				</div>
			  </div>
			  <div>
				<label class="block text-sm font-medium text-gray-700 mb-2">Responsibilities</label>
				<textarea class="responsibilities w-full px-3 py-2 border rounded-lg h-32" placeholder="Responsibilities">${responsibilitiesHtml}</textarea>
			  </div>
			  <div class="actions flex gap-3 mt-4">
				<button type="button" class="update-btn bg-green-600 text-white px-3 py-2 rounded hover:bg-green-700">Save</button>
				<button type="button" class="delete-btn bg-red-600 text-white px-3 py-2 rounded hover:bg-red-700">Delete</button>
			  </div>
			</div>
		  </div>
		`;
          container.insertAdjacentHTML('beforeend', itemHtml);
        });

        // Attach click event listeners for all rendered employment items.
        attachEmploymentItemEvents();
        // Attach save button click events for loaded items.
        attachSaveButtonEvents();
        // Attach delete button events.
        attachDeleteButtonEvents();
      } else {
        console.error("Backend error:", data.message);
        //alert("No employment data found. Please try again.");
      }
    } catch (error) {
      console.error("Error fetching employment data:", error);
      alert("Error loading employment data. Please try again later.");
    }
  }

 // Function to create a new employment item for desktop.
  function createDesktopEmploymentItem() {
    const newItem = document.createElement('div');
    newItem.className = 'employment-item border-l-4 border-purple-600 pl-4 rounded-lg mt-4';
    newItem.innerHTML = `
      <div class="header cursor-pointer flex justify-between items-center">
        <div class="flex-1">
          <h3 class="font-semibold text-gray-800">New Position</h3>
          <p class="text-sm text-gray-600">Company • Start - End</p>
        </div>
        <div class="flex items-center space-x-4">
          <button class="edit-btn text-purple-600 hover:text-purple-800 transition-colors">
            <svg class="w-5 h-5"><use xlink:href="#pencil" /></svg>
          </button>
          <span class="arrow-icon transform transition-transform duration-300">
            <svg class="w-6 h-6 text-gray-600"><use xlink:href="#chevron-down" /></svg>
          </span>
        </div>
      </div>
      <div class="content hidden mt-4 space-y-4">
        <input type="hidden" class="employment_id" value="">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Job Title</label>
            <input type="text" class="job_title w-full px-3 py-2 border rounded-lg" placeholder="Job Title">
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Employer Name</label>
            <input type="text" class="employer_name w-full px-3 py-2 border rounded-lg" placeholder="Employer Name">
          </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Start Date (MM/YYYY)</label>
            <input type="month" class="start_date w-full px-3 py-2 border rounded-lg" placeholder="MM/YYYY">
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">End Date (MM/YYYY)</label>
            <input type="month" class="end_date w-full px-3 py-2 border rounded-lg" placeholder="MM/YYYY">
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Is Current?</label>
            <input type="checkbox" class="is_current" value="1">
          </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Job Type</label>
            <select class="job_type w-full px-3 py-2 border rounded-lg">
              <option value="">Select Job Type</option>
              <option value="Full-Time">Full-Time</option>
              <option value="Part-Time">Part-Time</option>
              <option value="Contract">Contract</option>
              <option value="Internship">Internship</option>
              <option value="Temporary">Temporary</option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Work Location</label>
            <input type="text" class="work_location w-full px-3 py-2 border rounded-lg" placeholder="Work Location">
          </div>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Responsibilities</label>
          <textarea class="responsibilities w-full px-3 py-2 border rounded-lg h-32" placeholder="Responsibilities"></textarea>
        </div>
        <div class="actions flex gap-3 mt-4">
          <button type="button" class="save-btn bg-green-600 text-white px-3 py-2 rounded hover:bg-green-700">Save</button>
          <button type="button" class="delete-btn bg-red-600 text-white px-3 py-2 rounded hover:bg-red-700">Delete</button>
        </div>
      </div>
    `;

    // Toggle content on header click.
    newItem.querySelector('.header').addEventListener('click', () => {
      if (!isMobile()) {
        document.querySelectorAll('.employment-item').forEach(otherItem => {
          if (otherItem !== newItem) {
            const otherContent = otherItem.querySelector('.content');
            const otherArrow = otherItem.querySelector('.arrow-icon');
            otherContent.classList.add('hidden');
            otherArrow.classList.remove('rotate-180');
          }
        });
        const content = newItem.querySelector('.content');
        const arrow = newItem.querySelector('.arrow-icon');
        content.classList.toggle('hidden');
        arrow.classList.toggle('rotate-180');
      } else {
        openMobileModal('employment', 'edit', null, newItem);
      }
    });

    // Save button event.
    newItem.querySelector('.save-btn').addEventListener('click', () => {
      const data = {
        employment_id: newItem.querySelector('.employment_id').value.trim(),
        job_title: newItem.querySelector('.job_title').value.trim(),
        employer_name: newItem.querySelector('.employer_name').value.trim(),
        start_date: newItem.querySelector('.start_date').value.trim(),
        end_date: newItem.querySelector('.end_date').value.trim(),
        is_current: newItem.querySelector('.is_current').checked ? 1 : 0,
        job_type: newItem.querySelector('.job_type').value.trim(),
        work_location: newItem.querySelector('.work_location').value.trim(),
        responsibilities: newItem.querySelector('.responsibilities').value.trim()
      };
      saveEmploymentRecordCommon(data, newItem);
    });

    // Delete button event.
    newItem.querySelector('.delete-btn').addEventListener('click', (e) => {
      e.stopPropagation();
      deleteEmploymentItem(newItem);
    });

    return newItem;
  }
  
  // Function to attach click events to employment items.
  function attachEmploymentItemEvents() {
    document.querySelectorAll('.employment-item').forEach(item => {
      const header = item.querySelector('.header');
      const content = item.querySelector('.content');
      const arrow = item.querySelector('.arrow-icon');
      
      header.addEventListener('click', () => {
        if (!isMobile()) {
          // Desktop mode: Collapse all other items.
          document.querySelectorAll('.employment-item').forEach(otherItem => {
            if (otherItem !== item) {
              const otherContent = otherItem.querySelector('.content');
              const otherArrow = otherItem.querySelector('.arrow-icon');
              otherContent.classList.add('hidden');
              otherArrow.classList.remove('rotate-180');
            }
          });
          // Toggle the current item.
          if (content.classList.contains('hidden')) {
            content.classList.remove('hidden');
            arrow.classList.add('rotate-180');
          } else {
            content.classList.add('hidden');
            arrow.classList.remove('rotate-180');
          }
        } else {
          // Mobile: open the modal for editing.
          openMobileModal('employment', 'edit', null, item);
        }
      });
    });
  }

  // Attach delete button events to employment items.
  function attachDeleteButtonEvents() {
    document.querySelectorAll('.employment-item .delete-btn').forEach(button => {
      button.addEventListener('click', (e) => {
        e.stopPropagation();
        const item = button.closest('.employment-item');
        deleteEmploymentItem(item);
      });
    });
  }

  // Track new desktop employment item.
  let desktopNewEmployment = null;

  // Function to delete an employment item both from the UI and the DB.
  function deleteEmploymentItem(item) {	
    // Retrieve the employment ID; if it's not present, simply remove the item.
    const employmentId = item.querySelector('.employment_id').value.trim();
    
    // If there's no ID, assume this item has not been saved in the database yet.
    if (!employmentId) {
      item.remove();
      //alert("Item removed from the form (not saved in the database).");
      return;
    }
    
    // Prepare payload for the delete request.
    let payload = new URLSearchParams();
    payload.append('employment_id', employmentId);
    payload.append('template_id', document.getElementById('templateId').value);
    
    // Send fetch request to the delete endpoint.
    fetch('<?= base_url("website/services/ResumeBuilder/delete_work_experience") ?>', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: payload.toString()
    })
    .then(response => response.json())
    .then(result => {
      if (result.success) {
        // Successfully deleted in the database. Now remove from the DOM.
        item.remove();
		updatePreview();
        //alert("Record removed successfully from the database and the form.");
      } else {
        alert("Error deleting record from the database: " + result.message);
      }
    })
    .catch(error => {
      alert("Network error while deleting record.");
      console.error("Network error while deleting employment record:", error);
    });
  }
  
	// After (attach to window)
	window.deleteMobileRecord = function() { 
	  const employmentId = document.getElementById('employment_id').value.trim();
	  if (!employmentId) {
		closeMobileModal();
		return;
	  }
	  
	  let payload = new URLSearchParams();
	  payload.append('employment_id', employmentId);
	  payload.append('template_id', document.getElementById('templateId').value);
	  
	  fetch('<?= base_url("website/services/ResumeBuilder/delete_work_experience") ?>', {
		method: 'POST',
		headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
		body: payload.toString()
	  })
	  .then(response => response.json())
	  .then(result => {
		if (result.success) {
		  if (currentMobileItem) {
			currentMobileItem.remove();
			updatePreview();
		  }
		  closeMobileModal();
		} else {
		  alert("Error deleting record: " + result.message);
		}
	  })
	  .catch(error => {
		console.error("Delete error:", error);
		alert("Network error while deleting record.");
	  });
	};
  
  // Common save function for both Desktop and Mobile.
  async function saveEmploymentRecordCommon(dataObj, item = null) {
    let payload = new URLSearchParams();
    payload.append('template_id', document.getElementById('templateId').value);
    payload.append('employment_id', dataObj.employment_id);
    payload.append('job_title', dataObj.job_title);
    payload.append('employer_name', dataObj.employer_name);
    payload.append('start_date', dataObj.start_date);
    payload.append('end_date', dataObj.end_date);
    payload.append('job_type', dataObj.job_type);
    payload.append('work_location', dataObj.work_location);
    payload.append('responsibilities', dataObj.responsibilities);
    payload.append('is_current', dataObj.is_current);

    try {
      const response = await fetch('<?= base_url("website/services/ResumeBuilder/save_or_update_work_experience") ?>', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: payload.toString()
      });
      const result = await response.json();
      if (result.success) {
			// If it's a new entry, remove the temp block so fresh reload shows the saved one
			if (!dataObj.employment_id && item) {
				item.remove();
			}
			// Reload all data after saving
			loadEmploymentData();
			updatePreview();
			// Optional: Update hidden ID if needed (already handled in fresh reload)
			if (item && !dataObj.id && result.data && result.data.id) {
				item.setAttribute('data-id', result.data.id);
				item.querySelector('.employment_id').value = result.data.id;
			}
		}
		 else {
        console.error("Error saving employment record:", result.message);
      }
    } catch (error) {
      console.error("Network error while saving employment record:", error);
    }
  }

  // Add Employment button handler.
  document.getElementById('addEmployment').addEventListener('click', () => {
    if (isMobile()) {
      openMobileModal('employment', 'add');
    } else {
      // Collapse any open employment items.
      document.querySelectorAll('.employment-item').forEach(item => {
        const content = item.querySelector('.content');
        const arrow = item.querySelector('.arrow-icon');
        content.classList.add('hidden');
        arrow.classList.remove('rotate-180');
      });
      desktopNewEmployment = createDesktopEmploymentItem();
      // Expand the new item.
      const newContent = desktopNewEmployment.querySelector('.content');
      const newArrow = desktopNewEmployment.querySelector('.arrow-icon');
      newContent.classList.remove('hidden');
      newArrow.classList.add('rotate-180');
      document.getElementById('employmentContainer').appendChild(desktopNewEmployment);
      // Re-attach delete events for dynamic elements.
      attachDeleteButtonEvents();
    }
  });

  // Mobile event delegation for edit buttons and arrow icons.
  if (isMobile()) {
    document.getElementById('employmentContainer').addEventListener('click', (event) => {
      const btn = event.target.closest('button');
      const arrow = event.target.closest('.arrow-icon');
      if (btn) {
        event.stopPropagation();
        openMobileModal('employment', 'edit', btn);
      } else if (arrow) {
        event.stopPropagation();
        openMobileModal('employment', 'edit', arrow);
      }
    });
  }

  // Mobile modal open function with pre-fill functionality.
  function openMobileModal(section, mode, triggerButton = null, currentItem = null) {
    const modal = document.getElementById('itemModal');
    modal.classList.remove('hidden');
    const modalTitle = document.getElementById('modalTitle');
    const modalForm = document.getElementById('modalForm');

    // Track the current item if editing.
    if (mode === 'edit' && currentItem) {
      currentMobileItem = currentItem;
    } else {
      currentMobileItem = null;
    }

    modalTitle.textContent = mode === 'edit' ? 'Edit Employment' : 'Add Employment';
    
    // If in edit mode and a saved record exists, add a Delete button.
    let deleteButtonHtml = '';
    if (mode === 'edit' && currentItem && currentItem.querySelector('.employment_id').value.trim() !== '') {
      deleteButtonHtml = `<button type="button" onclick="deleteMobileRecord()" class="flex-1 px-4 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700">Delete</button>`;
    }

    modalForm.innerHTML = `
      <input type="hidden" id="employment_id" value="${currentItem ? currentItem.querySelector('.employment_id')?.value || '' : ''}">
      <input type="hidden" id="form_source" value="modal_form">
      <div>
        <label class="block text-sm font-medium mb-2">Job Title</label>
        <input type="text" id="jobTitle" class="w-full px-4 py-3 border rounded-lg" required>
      </div>
      <div>
        <label class="block text-sm font-medium mb-2">Employer Name</label>
        <input type="text" id="employer" class="w-full px-4 py-3 border rounded-lg" required>
      </div>
      <div>
        <label class="block text-sm font-medium mb-2">Job Type</label>
        <select id="jobType" class="w-full px-4 py-3 border rounded-lg">
          <option value="">Select Job Type</option>
          <option value="Full-Time">Full-Time</option>
          <option value="Part-Time">Part-Time</option>
          <option value="Contract">Contract</option>
          <option value="Internship">Internship</option>
          <option value="Temporary">Temporary</option>
        </select>
      </div>
      <div>
        <label class="block text-sm font-medium mb-2">Work Location</label>
        <input type="text" id="workLocation" class="w-full px-4 py-3 border rounded-lg" placeholder="Work Location">
      </div>
      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="block text-sm font-medium mb-2">Start Date</label>
          <input type="month" id="startDate" class="w-full px-4 py-3 border rounded-lg" required>
        </div>
        <div>
          <label class="block text-sm font-medium mb-2">End Date</label>
          <input type="month" id="endDate" class="w-full px-4 py-3 border rounded-lg">
        </div>
      </div>
      <div>
        <label class="block text-sm font-medium mb-2">Is Current?</label>
        <input type="checkbox" id="isCurrent" value="1">
      </div>
      <div>
        <label class="block text-sm font-medium mb-2">Responsibilities</label>
        <textarea id="responsibilities" class="w-full px-4 py-3 border rounded-lg h-32" placeholder="Responsibilities"></textarea>
      </div>
      <div class="flex gap-3 mt-6">
        <button type="button" onclick="closeMobileModal()" class="flex-1 px-4 py-3 border border-gray-300 rounded-lg hover:bg-gray-50">Cancel</button>
        ${deleteButtonHtml}
        <button type="submit" class="flex-1 px-4 py-3 bg-purple-600 text-white rounded-lg hover:bg-purple-700">Save</button>
      </div>
    `;

    // Pre-fill modal fields if editing an existing item.
    if (mode === 'edit' && currentItem) {
      document.getElementById('jobTitle').value = currentItem.querySelector('h3').textContent.trim();
      const parts = currentItem.querySelector('p').textContent.split('•');
      if (parts.length > 0) {
        document.getElementById('employer').value = parts[0].trim();
      }
      const jobTypeValue = currentItem.getAttribute('data-job-type') || '';
      document.getElementById('jobType').value = jobTypeValue;
      const workLocation = currentItem.getAttribute('data-work-location') || '';
      document.getElementById('workLocation').value = workLocation;
      const startDate = currentItem.getAttribute('data-start-date') || '';
      const endDate = currentItem.getAttribute('data-end-date') || '';
      document.getElementById('startDate').value = startDate;
      document.getElementById('endDate').value = endDate;
      const responsibilities = currentItem.getAttribute('data-responsibilities') || '';
      document.getElementById('responsibilities').value = responsibilities;
    }
  }
  
  // Mobile modal form submission using the common save function.
  document.getElementById('modalForm').addEventListener('submit', function (event) {
    event.preventDefault();
    const data = {
      id: '', // Populate if editing an existing record.
      employment_id: document.getElementById('employment_id').value.trim(),
      job_title: document.getElementById('jobTitle').value.trim(),
      employer_name: document.getElementById('employer').value.trim(),
      job_type: document.getElementById('jobType').value.trim(),
      work_location: document.getElementById('workLocation').value.trim(),
      start_date: document.getElementById('startDate').value.trim(),
      end_date: document.getElementById('endDate').value.trim(),
      is_current: document.getElementById('isCurrent').checked ? 1 : 0,
      responsibilities: document.getElementById('responsibilities').value.trim()
    };
    saveEmploymentRecordCommon(data);
    closeMobileModal();
  });


  // Call the function to load employment data on page load.
   loadEmploymentData();
   
   
  // Image preview placeholder function.
  function previewUploadedImage(event) {
    const preview = document.getElementById('previewImage');
    preview.src = URL.createObjectURL(event.target.files[0]);
    preview.classList.remove('hidden');
  }

     
	// Global variable to track the education item being edited in mobile mode.
	let currentEducationMobileItem = null;

	// --- Load Education Data ---
	async function loadEducationData() {
	  try {
		const formData = new FormData();
		const templateId = document.getElementById('templateId').value;
		formData.append('template_id', templateId);
		
		const response = await fetch('<?= base_url("website/services/ResumeBuilder/fetch_educations"); ?>', {
		  method: 'POST',
		  body: formData
		});
		
		if (!response.ok) throw new Error(`Network error: ${response.statusText}`);
		
		const data = await response.json();
		if (data.success) {
		  const container = document.getElementById('showEducationContainer');
		  container.innerHTML = ''; // Clear current content
		  
		  data.educations.forEach(edu => {
			const {
			  id = '',
			  degreeName = '',
			  institutionName = '',
			  startYear = '',
			  endYear = ''
			} = edu;
			
			const itemHtml = `
			  <div class="education-item border-l-4 border-blue-600 pl-6 py-4 rounded-lg transition-all duration-300 hover:border-blue-700 hover:bg-gray-50"
				   data-id="${id}"
				   data-startYear="${startYear}"
				   data-endYear="${endYear}">
				<div class="header cursor-pointer flex justify-between items-center">
				  <div>
					<h3 class="text-lg font-semibold text-gray-900">${degreeName}</h3>
					<p class="text-sm text-gray-500 mt-1">
					  ${institutionName} • ${startYear} - ${endYear || 'Present'}
					</p>
				  </div>
				  <div class="arrow-icon transform transition-transform duration-300">
					<svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
					  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" 
							d="M19 9l-7 7-7-7"/>
					</svg>
				  </div>
				</div>
				<div class="content hidden mt-4 space-y-4">
				  <input type="hidden" class="education_id" value="${id}">
				  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
					<div>
					  <label class="block text-sm font-medium text-gray-700 mb-2">Degree</label>
					  <input type="text" class="degree w-full px-3 py-2 border rounded-lg" value="${degreeName}" placeholder="Degree">
					</div>
					<div>
					  <label class="block text-sm font-medium text-gray-700 mb-2">Institution</label>
					  <input type="text" class="institution w-full px-3 py-2 border rounded-lg" value="${institutionName}" placeholder="Institution">
					</div>
				  </div>
				  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
					<div>
					  <label class="block text-sm font-medium text-gray-700 mb-2">Start Date</label>
					  <input type="month" class="startYear w-full px-3 py-2 border rounded-lg" value="${startYear}" placeholder="Start Date">
					</div>
					<div>
					  <label class="block text-sm font-medium text-gray-700 mb-2">End Date</label>
					  <input type="month" class="endYear w-full px-3 py-2 border rounded-lg" value="${endYear}" placeholder="End Date">
					</div>
				  </div>
				  <div class="actions flex gap-3 mt-4">
					<button type="button" class="update-btn bg-green-600 text-white px-3 py-2 rounded hover:bg-green-700">Save</button>
					<button type="button" class="delete-btn bg-red-600 text-white px-3 py-2 rounded hover:bg-red-700">Delete</button>
				  </div>
				</div>
			  </div>
			`;
			container.insertAdjacentHTML('beforeend', itemHtml);
		  });
		  
		  attachEducationItemEvents();
		  attachEducationSaveButtonEvents();
		  attachEducationDeleteButtonEvents();
		} else {
		  console.error("Backend error:", data.message);
		}
	  } catch (error) {
		console.error("Error fetching education data:", error);
		alert("Error loading education data. Please try again later.");
	  }
	}

	// --- Create a new education item (Desktop mode) ---
	function createDesktopEducationItem() {
	  const newItem = document.createElement('div');
	  newItem.className = 'education-item border-l-4 border-blue-600 pl-4 rounded-lg mt-4';
	  newItem.innerHTML = `
		<div class="header cursor-pointer flex justify-between items-center">
		  <div class="flex-1">
			<h3 class="font-semibold text-gray-800">New Education</h3>
			<p class="text-sm text-gray-600">Institution • Start - End</p>
		  </div>
		  <div class="flex items-center space-x-4">
			<button class="edit-btn text-blue-600 hover:text-blue-800 transition-colors">
			  <svg class="w-5 h-5"><use xlink:href="#pencil" /></svg>
			</button>
			<span class="arrow-icon transform transition-transform duration-300">
			  <svg class="w-6 h-6 text-gray-600"><use xlink:href="#chevron-down" /></svg>
			</span>
		  </div>
		</div>
		<div class="content hidden mt-4 space-y-4">
		  <input type="hidden" class="education_id" value="">
		  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
			<div>
			  <label class="block text-sm font-medium text-gray-700 mb-2">Degree</label>
			  <input type="text" class="degree w-full px-3 py-2 border rounded-lg" placeholder="Degree">
			</div>
			<div>
			  <label class="block text-sm font-medium text-gray-700 mb-2">Institution</label>
			  <input type="text" class="institution w-full px-3 py-2 border rounded-lg" placeholder="Institution">
			</div>
		  </div>
		  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
			<div>
			  <label class="block text-sm font-medium text-gray-700 mb-2">Start Date</label>
			  <input type="month" class="startYear w-full px-3 py-2 border rounded-lg" placeholder="Start Date">
			</div>
			<div>
			  <label class="block text-sm font-medium text-gray-700 mb-2">End Date</label>
			  <input type="month" class="endYear w-full px-3 py-2 border rounded-lg" placeholder="End Date">
			</div>
		  </div>
		  <div class="actions flex gap-3 mt-4">
			<button type="button" class="save-btn bg-green-600 text-white px-3 py-2 rounded hover:bg-green-700">Save</button>
			<button type="button" class="delete-btn bg-red-600 text-white px-3 py-2 rounded hover:bg-red-700">Delete</button>
		  </div>
		</div>
	  `;

	  newItem.querySelector('.header').addEventListener('click', () => {
		if (!isMobile()) {
		  document.querySelectorAll('.education-item').forEach(other => {
			if (other !== newItem) {
			  other.querySelector('.content').classList.add('hidden');
			  other.querySelector('.arrow-icon').classList.remove('rotate-180');
			}
		  });
		  const content = newItem.querySelector('.content');
		  const arrow = newItem.querySelector('.arrow-icon');
		  content.classList.toggle('hidden');
		  arrow.classList.toggle('rotate-180');
		} else {
		  openMobileModalForEducation('edit', null, newItem);
		}
	  });

	  newItem.querySelector('.save-btn').addEventListener('click', () => {
		const data = {
		  education_id: newItem.querySelector('.education_id').value.trim(),
		  degree:        newItem.querySelector('.degree').value.trim(),
		  institution:   newItem.querySelector('.institution').value.trim(),
		  startYear:     newItem.querySelector('.startYear').value.trim(),
		  endYear:       newItem.querySelector('.endYear').value.trim()
		};
		saveEducationRecordCommon(data, newItem);
	  });

	  newItem.querySelector('.delete-btn').addEventListener('click', e => {
		e.stopPropagation();
		deleteEducationItem(newItem);
	  });

	  return newItem;
	}

	
	// --- Attach click events (Desktop behavior) ---
	function attachEducationItemEvents() {
	  document.querySelectorAll('.education-item').forEach(item => {
		const header = item.querySelector('.header');
		const content = item.querySelector('.content');
		const arrow = item.querySelector('.arrow-icon');
		
		header.addEventListener('click', () => {
		  if (!isMobile()) {
			document.querySelectorAll('.education-item').forEach(otherItem => {
			  if (otherItem !== item) {
				otherItem.querySelector('.content').classList.add('hidden');
				otherItem.querySelector('.arrow-icon').classList.remove('rotate-180');
			  }
			});
			content.classList.toggle('hidden');
			arrow.classList.toggle('rotate-180');
		  } else {
			openMobileModalForEducation('edit', null, item);
		  }
		});
	  });
	}

	// --- Attach Save and Delete events ---
	function attachEducationSaveButtonEvents() {
	  document.querySelectorAll('.education-item .update-btn').forEach(button => {
		button.addEventListener('click', e => {
		  e.stopPropagation();
		  const parent = button.closest('.education-item');
		  const data = {
			education_id: parent.querySelector('.education_id').value.trim(),
			degree:        parent.querySelector('.degree').value.trim(),
			institution:   parent.querySelector('.institution').value.trim(),			
			startYear:     parent.querySelector('.startYear').value.trim(),
			endYear:       parent.querySelector('.endYear').value.trim()
			
		  };
		  saveEducationRecordCommon(data, parent);
		});
	  });
	}

	function attachEducationDeleteButtonEvents() {
	  document.querySelectorAll('.education-item .delete-btn').forEach(button => {
		button.addEventListener('click', e => {
		  e.stopPropagation();
		  deleteEducationItem(button.closest('.education-item'));
		});
	  });
	}

	// --- Delete an Education Record (Desktop) ---
	function deleteEducationItem(item) {
	  //if (!confirm("Are you sure you want to delete this record?")) return;
	  
	  const educationId = item.querySelector('.education_id').value.trim();
	  if (!educationId) {
		item.remove();
		//alert("Item removed from the form (not saved in the database).");
		return;
	  }
	  
	  const payload = new URLSearchParams();
	  payload.append('education_id', educationId);
	  payload.append('template_id', document.getElementById('templateId').value);
	  
	  fetch('<?= base_url("website/services/ResumeBuilder/delete_education") ?>', {
		method: 'POST',
		headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
		body: payload.toString()
	  })
	  .then(res => res.json())
	  .then(result => {
		if (result.success) {
		  item.remove();
		  updatePreview();
		  //alert("Record removed successfully.");

		} else {
		  alert("Error deleting record: " + result.message);
		}
	  })
	  .catch(err => {
		alert("Network error while deleting record.");
		console.error("Error deleting education record:", err);
	  });
	}

	// --- Save/Update Education Record ---
	async function saveEducationRecordCommon(dataObj, item = null) {
	  const payload = new URLSearchParams();
	  payload.append('template_id',      document.getElementById('templateId').value);
	  payload.append('education_id',     dataObj.education_id);
	  payload.append('degreeName',       dataObj.degree);
	  payload.append('institutionName',  dataObj.institution); 
	  payload.append('startYear',        dataObj.startYear);
	  payload.append('endYear',          dataObj.endYear); 
	  try {
		const response = await fetch('<?= base_url("website/services/ResumeBuilder/save_or_update_education") ?>', {
		  method: 'POST',
		  headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
		  body: payload.toString()
		});
		const result = await response.json();
		if (result.success) {
		  if (!dataObj.education_id && item) {
			item.remove(); // remove the temp new block from DOM
		  }

		  loadEducationData();     // reload fresh education list
		  updatePreview();         // update resume preview

		  if (item && !dataObj.education_id && result.data?.id) {
			item.setAttribute('data-id', result.data.id);
			item.querySelector('.education_id').value = result.data.id;
		  }
		} else {
		  console.error("Error saving education record:", result.message);
		}
	  } catch (error) {
		console.error("Network error while saving education record:", error);
	  }
	}

	// --- Open the Mobile Modal for Education ---
	function openMobileModalForEducation(mode, triggerButton = null, currentItem = null) {
	  const modal      = document.getElementById('itemModal');
	  const modalTitle = document.getElementById('modalTitle');
	  const modalForm  = document.getElementById('modalForm');
	  
	  modal.classList.remove('hidden');
	  currentEducationMobileItem = (mode === 'edit' ? currentItem : null);
	  modalTitle.textContent = mode === 'edit' ? 'Edit Education' : 'Add Education';
	  
	  let deleteButtonHtml = '';
	  if (mode === 'edit' && currentItem?.querySelector('.education_id').value) {
		deleteButtonHtml = `<button type="button" onclick="deleteMobileEducationRecord()" class="flex-1 px-4 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700">Delete</button>`;
	  }
	  
	  modalForm.innerHTML = `
		<input type="hidden" id="education_id" value="${currentItem?.querySelector('.education_id')?.value || ''}">
		<input type="hidden" id="form_source" value="modal_form">
		<div>
		  <label class="block text-sm font-medium mb-2">Degree</label>
		  <input type="text" id="educationDegree" class="w-full px-4 py-3 border rounded-lg" required>
		</div>
		<div>
		  <label class="block text-sm font-medium mb-2">Institution</label>
		  <input type="text" id="educationInstitution" class="w-full px-4 py-3 border rounded-lg" required>
		</div>
		<div class="grid grid-cols-2 gap-4">
		  <div>
			<label class="block text-sm font-medium mb-2">Start Date</label>
			<input type="month" id="educationStartYear" class="w-full px-4 py-3 border rounded-lg" required>
		  </div>
		  <div>
			<label class="block text-sm font-medium mb-2">End Date</label>
			<input type="month" id="educationEndYear" class="w-full px-4 py-3 border rounded-lg">
		  </div>
		</div>
		<div class="flex gap-3 mt-6">
		  <button type="button" onclick="closeMobileModal()" class="flex-1 px-4 py-3 border border-gray-300 rounded-lg hover:bg-gray-50">Cancel</button>
		  ${deleteButtonHtml}
		  <button type="submit" class="flex-1 px-4 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Save</button>
		</div>
	  `;
	  
	  if (mode === 'edit' && currentItem) {
		document.getElementById('educationDegree').value      = currentItem.querySelector('.degree')?.value || currentItem.querySelector('h3').textContent.trim();
		document.getElementById('educationInstitution').value = currentItem.querySelector('.institution')?.value || '';
		document.getElementById('educationStartYear').value   = currentItem.querySelector('.startYear')?.value || '';
		document.getElementById('educationEndYear').value     = currentItem.querySelector('.endYear')?.value || '';
	  }
	}


	// --- Delete Education Record from Mobile Modal ---
	window.deleteMobileEducationRecord = function() { 
	  //if (!confirm("Are you sure you want to delete this record?")) return;
	  const educationId = document.getElementById('education_id').value.trim();
	  if (!educationId) {
		closeMobileModal();
		//alert("This record has not been saved in the database.");
		return;
	  }
	  const payload = new URLSearchParams();
	  payload.append('education_id', educationId);
	  payload.append('template_id', document.getElementById('templateId').value);
	  
	  fetch('<?= base_url("website/services/ResumeBuilder/delete_education") ?>', {
		method: 'POST',
		headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
		body: payload.toString()
	  })
	  .then(res => res.json())
	  .then(result => {
		if (result.success) {
		  currentEducationMobileItem?.remove();
		  updatePreview();
		  closeMobileModal();
		} else {
		  alert("Error deleting record: " + result.message);
		}
	  })
	  .catch(err => {
		alert("Network error while deleting record.");
		console.error(err);
	  });
	}

	// --- Handle modal form submission (for education) ---
	document.getElementById('modalForm').addEventListener('submit', event => {
	  event.preventDefault();
	  if (document.getElementById('educationDegree')) {
		const data = {
		  education_id:   document.getElementById('education_id').value.trim(),
		  degree:         document.getElementById('educationDegree').value.trim(),
		  institution:    document.getElementById('educationInstitution').value.trim(),		  
		  startYear:      document.getElementById('educationStartYear').value.trim(),
		  endYear:        document.getElementById('educationEndYear').value.trim()		 
		};
		saveEducationRecordCommon(data);
		closeMobileModal();
	  }
	});

	// --- Add Education button handler (Desktop & Mobile) ---
	document.getElementById('addEducation').addEventListener('click', () => {
	  if (isMobile()) {
		openMobileModalForEducation('add');
	  } else {
		document.querySelectorAll('.education-item').forEach(item => {
		  item.querySelector('.content').classList.add('hidden');
		  item.querySelector('.arrow-icon').classList.remove('rotate-180');
		});
		const newEdu = createDesktopEducationItem();
		newEdu.querySelector('.content').classList.remove('hidden');
		newEdu.querySelector('.arrow-icon').classList.add('rotate-180');
		document.getElementById('educationContainer').appendChild(newEdu);
		attachEducationDeleteButtonEvents();
	  }
	});

	// Finally, load the education data when the page loads.
	loadEducationData();


	// Global variable to track the language item being edited in mobile mode.
	let currentLanguageMobileItem = null;

	// --- Load Language Data ---
	async function loadLanguageData() {
	  try {
		const formData = new FormData();
		const templateId = document.getElementById('templateId').value;
		formData.append('template_id', templateId);
		
		const response = await fetch('<?= base_url("website/services/ResumeBuilder/fetch_languages"); ?>', {
		  method: 'POST',
		  body: formData
		});
		
		if (!response.ok) throw new Error(`Network error: ${response.statusText}`);
		
		const data = await response.json();
		if (data.success) {
		  const container = document.getElementById('showLanguageContainer');
		  container.innerHTML = ''; // Clear current content
		  
		  data.languages.forEach(lang => {
			const {
			  id = '',
			  languageName = '',
			  proficiencyLevel = ''
			} = lang;
			
			const itemHtml = `
				  <div class="language-item border-l-4 border-blue-600 pl-6 py-4 rounded-lg transition-all duration-300 hover:border-blue-700 hover:bg-gray-50"
					   data-id="${id}"
					   data-proficiencyLevel="${proficiencyLevel}">
					   
					<div class="header cursor-pointer flex justify-between items-center">
					  <div>
						<h3 class="text-lg font-semibold text-gray-900">${languageName}</h3>
						<p class="text-sm text-gray-500 mt-1">${proficiencyLevel}</p>
					  </div>
					  <div class="arrow-icon transform transition-transform duration-300">
						<svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 9l-7 7-7-7"/>
						</svg>
					  </div>
					</div>

					<div class="content hidden mt-4 space-y-4">
					  <input type="hidden" class="language_id" value="${id}">

					  <div class="grid grid-cols-2 gap-4">
						<div>
						  <label class="block text-sm font-medium text-gray-700 mb-2">Language Name</label>
						  <input type="text" class="languageName w-full px-3 py-2 border rounded-lg" value="${languageName}" placeholder="Language Name">
						</div>
						<div>
						  <label class="block text-sm font-medium text-gray-700 mb-2">Proficiency Level</label>
						  <select class="proficiencyLevel w-full px-3 py-2 border rounded-lg">
							<option value="">Select Proficiency Level</option>
							<option value="Beginner" ${proficiencyLevel === 'Beginner' ? 'selected' : ''}>Beginner</option>
							<option value="Intermediate" ${proficiencyLevel === 'Intermediate' ? 'selected' : ''}>Intermediate</option>
							<option value="Advanced" ${proficiencyLevel === 'Advanced' ? 'selected' : ''}>Advanced</option>
							<option value="Fluent" ${proficiencyLevel === 'Fluent' ? 'selected' : ''}>Fluent</option>
						  </select>
						</div>
					  </div>

					  <div class="actions flex gap-3 mt-4">
						<button type="button" class="update-btn bg-green-600 text-white px-3 py-2 rounded hover:bg-green-700">Save</button>
						<button type="button" class="delete-btn bg-red-600 text-white px-3 py-2 rounded hover:bg-red-700">Delete</button>
					  </div>
					</div>
				  </div>
				`;

			container.insertAdjacentHTML('beforeend', itemHtml);
		  });
		  
		  attachLanguageItemEvents();
		  attachLanguageSaveButtonEvents();
		  attachLanguageDeleteButtonEvents();
		} else {
		  console.error("Backend error:", data.message);
		  //alert("No language data found. Please try again.");
		}
	  } catch (error) {
		console.error("Error fetching language data:", error);
		alert("Error loading language data. Please try again later.");
	  }
	}

	// --- Attach click events (Desktop behavior) ---
	function attachLanguageItemEvents() {
	  document.querySelectorAll('.language-item').forEach(item => {
		const header = item.querySelector('.header');
		const content = item.querySelector('.content');
		const arrow = item.querySelector('.arrow-icon');
		
		header.addEventListener('click', () => {
		  if (!isMobile()) {
			document.querySelectorAll('.language-item').forEach(otherItem => {
			  if (otherItem !== item) {
				otherItem.querySelector('.content').classList.add('hidden');
				otherItem.querySelector('.arrow-icon').classList.remove('rotate-180');
			  }
			});
			content.classList.toggle('hidden');
			arrow.classList.toggle('rotate-180');
		  } else {
			openMobileModalForLanguage('edit', null, item);
		  }
		});
	  });
	}
	
	// --- Create a new language item (Desktop mode) ---
	function createDesktopLanguageItem() {
	  const newItem = document.createElement('div');
	  newItem.className = 'language-item border-l-4 border-blue-600 pl-4 rounded-lg mt-4';
	  newItem.innerHTML = `
		  <div class="header cursor-pointer flex justify-between items-center">
			<div class="flex-1">
			  <h3 class="font-semibold text-gray-800">New Language</h3>
			  <p class="text-sm text-gray-600">Proficiency Level</p>
			</div>
			<div class="flex items-center space-x-4">
			  <button class="edit-btn text-blue-600 hover:text-blue-800 transition-colors">
				<svg class="w-5 h-5"><use xlink:href="#pencil" /></svg>
			  </button>
			  <span class="arrow-icon transform transition-transform duration-300">
				<svg class="w-6 h-6 text-gray-600"><use xlink:href="#chevron-down" /></svg>
			  </span>
			</div>
		  </div>
		  <div class="content hidden mt-4 space-y-4">
			<input type="hidden" class="language_id" value="">

			<div class="grid grid-cols-2 gap-4">
			  <div>
				<label class="block text-sm font-medium text-gray-700 mb-2">Language Name</label>
				<input type="text" class="languageName w-full px-3 py-2 border rounded-lg" placeholder="Language Name">
			  </div>
			  <div>
				<label class="block text-sm font-medium text-gray-700 mb-2">Proficiency Level</label>
				<select class="proficiencyLevel w-full px-3 py-2 border rounded-lg">
				  <option value="">Select Proficiency Level</option>
				  <option value="Beginner">Beginner</option>
				  <option value="Intermediate">Intermediate</option>
				  <option value="Advanced">Advanced</option>
				  <option value="Fluent">Fluent</option>
				</select>
			  </div>
			</div>

			<div class="actions flex gap-3 mt-4">
			  <button type="button" class="save-btn bg-green-600 text-white px-3 py-2 rounded hover:bg-green-700">Save</button>
			  <button type="button" class="delete-btn bg-red-600 text-white px-3 py-2 rounded hover:bg-red-700">Delete</button>
			</div>
		  </div>
		`;


	  newItem.querySelector('.header').addEventListener('click', () => {
		if (!isMobile()) {
		  document.querySelectorAll('.language-item').forEach(other => {
			if (other !== newItem) {
			  other.querySelector('.content').classList.add('hidden');
			  other.querySelector('.arrow-icon').classList.remove('rotate-180');
			}
		  });
		  const content = newItem.querySelector('.content');
		  const arrow = newItem.querySelector('.arrow-icon');
		  content.classList.toggle('hidden');
		  arrow.classList.toggle('rotate-180');
		} else {
		  openMobileModalForLanguage('edit', null, newItem);
		}
	  });

	  newItem.querySelector('.save-btn').addEventListener('click', () => {
		const data = {
		  language_id:      newItem.querySelector('.language_id').value.trim(),
		  languageName:     newItem.querySelector('.languageName').value.trim(),
		  proficiencyLevel: newItem.querySelector('.proficiencyLevel').value.trim()
		};
		saveLanguageRecordCommon(data, newItem);
	  });

	  newItem.querySelector('.delete-btn').addEventListener('click', e => {
		e.stopPropagation();
		deleteLanguageItem(newItem);
	  });

	  return newItem;
	}
	
	// --- Attach Save and Delete events ---
	function attachLanguageSaveButtonEvents() {
	  document.querySelectorAll('.language-item .update-btn').forEach(button => {
		button.addEventListener('click', e => {
		  e.stopPropagation();
		  const parent = button.closest('.language-item');
		  const data = {
			language_id:      parent.querySelector('.language_id').value.trim(),
			languageName:     parent.querySelector('.languageName').value.trim(),
			proficiencyLevel: parent.querySelector('.proficiencyLevel').value.trim()
		  };
		  saveLanguageRecordCommon(data, parent);
		});
	  });
	}

	function attachLanguageDeleteButtonEvents() {
	  document.querySelectorAll('.language-item .delete-btn').forEach(button => {
		button.addEventListener('click', e => {
		  e.stopPropagation();
		  deleteLanguageItem(button.closest('.language-item'));
		});
	  });
	}

	// --- Delete a Language Record (Desktop) ---
	function deleteLanguageItem(item) {	  
	  const languageId = item.querySelector('.language_id').value.trim();
	  if (!languageId) {
		item.remove();
		//alert("Item removed from the form (not saved in the database).");
		return;
	  }
	  
	  const payload = new URLSearchParams();
	  payload.append('language_id', languageId);
	  payload.append('template_id', document.getElementById('templateId').value);
	  
	  fetch('<?= base_url("website/services/ResumeBuilder/delete_language") ?>', {
		method: 'POST',
		headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
		body: payload.toString()
	  })
	  .then(res => res.json())
	  .then(result => {
		if (result.success) {
		  item.remove();
		  updatePreview();
		  //alert("Record removed successfully.");
		} else {
		  alert("Error deleting record: " + result.message);
		}
	  })
	  .catch(err => {
		alert("Network error while deleting record.");
		console.error("Error deleting language record:", err);
	  });
	}

	// --- Save/Update Language Record ---
	async function saveLanguageRecordCommon(dataObj, item = null) {
	  const payload = new URLSearchParams();
	  payload.append('template_id',      document.getElementById('templateId').value);
	  payload.append('language_id',      dataObj.language_id);
	  payload.append('languageName',     dataObj.languageName);
	  payload.append('proficiencyLevel', dataObj.proficiencyLevel);
	  
	  try {
		const response = await fetch('<?= base_url("website/services/ResumeBuilder/save_or_update_language") ?>', {
		  method: 'POST',
		  headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
		  body: payload.toString()
		});
		const result = await response.json();
		if (result.success) {
		  if (!dataObj.language_id && item) {
			item.remove(); // remove the temporary DOM block
		  }

		  loadLanguageData();     // reload fresh list
		  updatePreview();        // update resume preview

		  if (item && !dataObj.language_id && result.data?.id) {
			item.setAttribute('data-id', result.data.id);
			item.querySelector('.language_id').value = result.data.id;
		  }
		} else {
		  console.error("Error saving language record:", result.message);
		}
	  } catch (error) {
		console.error("Network error while saving language record:", error);
	  }
	}

	// --- Open the Mobile Modal for Language ---
	function openMobileModalForLanguage(mode, triggerButton = null, currentItem = null) {
	  const modal      = document.getElementById('itemModal');
	  const modalTitle = document.getElementById('modalTitle');
	  const modalForm  = document.getElementById('modalForm');
	  
	  modal.classList.remove('hidden');
	  currentLanguageMobileItem = (mode === 'edit' ? currentItem : null);
	  modalTitle.textContent = mode === 'edit' ? 'Edit Language' : 'Add Language';
	  
	  let deleteButtonHtml = '';
	  if (mode === 'edit' && currentItem?.querySelector('.language_id').value) {
		deleteButtonHtml = `<button type="button" onclick="deleteMobileLanguageRecord()" class="flex-1 px-4 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700">Delete</button>`;
	  }
	  
	  modalForm.innerHTML = `
		<input type="hidden" id="language_id" value="${currentItem?.querySelector('.language_id')?.value || ''}">
		<input type="hidden" id="form_source" value="modal_form">
		<div>
		  <label class="block text-sm font-medium mb-2">Language Name</label>
		  <input type="text" id="languageName" class="w-full px-4 py-3 border rounded-lg" required>
		</div>
		<div>
		  <label class="block text-sm font-medium mb-2">Proficiency Level</label>
		  <select id="proficiencyLevel" class="w-full px-4 py-3 border rounded-lg" required>
			<option value="">Select Proficiency Level</option>
			<option value="Beginner">Beginner</option>
			<option value="Intermediate">Intermediate</option>
			<option value="Advanced">Advanced</option>
			<option value="Fluent">Fluent</option>
		  </select>
		</div>

		<div class="flex gap-3 mt-6">
		  <button type="button" onclick="closeMobileModal()" class="flex-1 px-4 py-3 border border-gray-300 rounded-lg hover:bg-gray-50">Cancel</button>
		  ${deleteButtonHtml}
		  <button type="submit" class="flex-1 px-4 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Save</button>
		</div>
	  `;
	  
	  if (mode === 'edit' && currentItem) {
		document.getElementById('languageName').value       = currentItem.querySelector('.languageName').value || currentItem.querySelector('h3').textContent.trim();
		document.getElementById('proficiencyLevel').value   = currentItem.querySelector('.proficiencyLevel').value || '';
	  }
	}

	// --- Delete Language Record from Mobile Modal ---
	window.deleteMobileLanguageRecord = function() { 
	  //if (!confirm("Are you sure you want to delete this record?")) return;
	  const languageId = document.getElementById('language_id').value.trim();
	  if (!languageId) {
		closeMobileModal();
		//alert("This record has not been saved in the database.");
		return;
	  }
	  const payload = new URLSearchParams();
	  payload.append('language_id', languageId);
	  payload.append('template_id', document.getElementById('templateId').value);
	  
	  fetch('<?= base_url("website/services/ResumeBuilder/delete_language") ?>', {
		method: 'POST',
		headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
		body: payload.toString()
	  })
	  .then(res => res.json())
	  .then(result => {
		if (result.success) {
		  currentLanguageMobileItem?.remove();
		  updatePreview();
		  closeMobileModal();
		  //alert("Record deleted successfully.");
		} else {
		  alert("Error deleting record: " + result.message);
		}
	  })
	  .catch(err => {
		alert("Network error while deleting record.");
		console.error(err);
	  });
	}

	// --- Handle modal form submission (for language) ---
	document.getElementById('modalForm').addEventListener('submit', event => {
	  event.preventDefault();
	  if (document.getElementById('languageName')) {
		const data = {
		  language_id:      document.getElementById('language_id').value.trim(),
		  languageName:     document.getElementById('languageName').value.trim(),
		  proficiencyLevel: document.getElementById('proficiencyLevel').value.trim()
		};
		saveLanguageRecordCommon(data);
		closeMobileModal();
	  }
	});

	// --- Add Language button handler (Desktop & Mobile) ---
	document.getElementById('addLanguage').addEventListener('click', () => {
	  if (isMobile()) {
		openMobileModalForLanguage('add');
	  } else {
		document.querySelectorAll('.language-item').forEach(item => {
		  item.querySelector('.content').classList.add('hidden');
		  item.querySelector('.arrow-icon').classList.remove('rotate-180');
		});
		const newLang = createDesktopLanguageItem();
		newLang.querySelector('.content').classList.remove('hidden');
		newLang.querySelector('.arrow-icon').classList.add('rotate-180');
		document.getElementById('languageContainer').appendChild(newLang);
		attachLanguageDeleteButtonEvents();
	  }
	});

	// Finally, load the language data when the page loads.
	loadLanguageData();

	// Global variable to track the internship item being edited in mobile mode.
	let currentInternshipMobileItem = null;

	// --- Load Internship Data ---
	async function loadInternshipData() {
	  try {
		const formData = new FormData();
		const templateId = document.getElementById('templateId').value;
		formData.append('template_id', templateId);
		
		const response = await fetch('<?= base_url("website/services/ResumeBuilder/fetch_internships"); ?>', {
		  method: 'POST',
		  body: formData
		});
		
		if (!response.ok) throw new Error(`Network error: ${response.statusText}`);
		
		const data = await response.json();
		if (data.success) {
		  const container = document.getElementById('showInternshipContainer');
		  container.innerHTML = ''; // Clear current content
		  
		  data.internships.forEach(intern => {
			const {
			  id = '',
			  jobTitle = '',
			  employerName = '',
			  startDate = '',
			  endDate = '',
			  location = '',
			  jobDescription = ''
			} = intern;
			
			const itemHtml = `
			  <div class="internship-item border-l-4 border-blue-600 pl-6 py-4 rounded-lg transition-all duration-300 hover:border-blue-700 hover:bg-gray-50"
				   data-id="${id}">
				   
				<div class="header cursor-pointer flex justify-between items-center">
				  <div>
					<h3 class="text-lg font-semibold text-gray-900">${jobTitle}</h3>
					<p class="text-sm text-gray-500 mt-1">${employerName}</p>
				  </div>
				  <div class="arrow-icon transform transition-transform duration-300">
					<svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
					  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 9l-7 7-7-7"/>
					</svg>
				  </div>
				</div>

				<div class="content hidden mt-4 space-y-4">
				  <input type="hidden" class="internship_id" value="${id}">

				  <div class="grid grid-cols-2 gap-4">
					<div>
					  <label class="block text-sm font-medium text-gray-700 mb-2">Job Title</label>
					  <input type="text" class="jobTitle w-full px-3 py-2 border rounded-lg" value="${jobTitle}" placeholder="Job Title">
					</div>
					<div>
					  <label class="block text-sm font-medium text-gray-700 mb-2">Employer Name</label>
					  <input type="text" class="employerName w-full px-3 py-2 border rounded-lg" value="${employerName}" placeholder="Employer Name">
					</div>

					<div>
					  <label class="block text-sm font-medium text-gray-700 mb-2">Start Date</label>
					  <input type="month" class="startDate w-full px-3 py-2 border rounded-lg" value="${startDate}" placeholder="Start Date">
					</div>
					<div>
					  <label class="block text-sm font-medium text-gray-700 mb-2">End Date</label>
					  <input type="month" class="endDate w-full px-3 py-2 border rounded-lg" value="${endDate}" placeholder="End Date">
					</div>

					<div>
					  <label class="block text-sm font-medium text-gray-700 mb-2">Location</label>
					  <input type="text" class="location w-full px-3 py-2 border rounded-lg" value="${location}" placeholder="Location">
					</div>
					<div>
					  <label class="block text-sm font-medium text-gray-700 mb-2">Job Description</label>
					  <textarea class="jobDescription w-full px-3 py-2 border rounded-lg h-[100px]" placeholder="Job Description">${jobDescription}</textarea>
					</div>
				  </div>

				  <div class="actions flex gap-3 mt-4">
					<button type="button" class="update-btn bg-green-600 text-white px-3 py-2 rounded hover:bg-green-700">Save</button>
					<button type="button" class="delete-btn bg-red-600 text-white px-3 py-2 rounded hover:bg-red-700">Delete</button>
				  </div>
				</div>
			  </div>
			`;

			container.insertAdjacentHTML('beforeend', itemHtml);
		  });
		  
		  attachInternshipItemEvents();
		  attachInternshipSaveButtonEvents();
		  attachInternshipDeleteButtonEvents();
		} else {
		  console.error("Backend error:", data.message);
		  //alert("No internship data found. Please try again.");
		}
	  } catch (error) {
		console.error("Error fetching internship data:", error);
		alert("Error loading internship data. Please try again later.");
	  }
	}
	
	// --- Create a new internship item (Desktop mode) ---
	function createDesktopInternshipItem() {
	  const newItem = document.createElement('div');
	  newItem.className = 'internship-item border-l-4 border-blue-600 pl-4 rounded-lg mt-4';
	  newItem.innerHTML = `
		  <div class="header cursor-pointer flex justify-between items-center">
			<div class="flex-1">
			  <h3 class="font-semibold text-gray-800">New Internship</h3>
			  <p class="text-sm text-gray-600">Employer Name</p>
			</div>
			<div class="flex items-center space-x-4">
			  <button class="edit-btn text-blue-600 hover:text-blue-800 transition-colors">
				<svg class="w-5 h-5"><use xlink:href="#pencil" /></svg>
			  </button>
			  <span class="arrow-icon transform transition-transform duration-300">
				<svg class="w-6 h-6 text-gray-600"><use xlink:href="#chevron-down" /></svg>
			  </span>
			</div>
		  </div>
		  <div class="content hidden mt-4 space-y-4">
			<input type="hidden" class="internship_id" value="">
			
			<div class="grid grid-cols-2 gap-4">
			  <div>
				<label class="block text-sm font-medium text-gray-700 mb-2">Job Title</label>
				<input type="text" class="jobTitle w-full px-3 py-2 border rounded-lg" placeholder="Job Title">
			  </div>
			  <div>
				<label class="block text-sm font-medium text-gray-700 mb-2">Employer Name</label>
				<input type="text" class="employerName w-full px-3 py-2 border rounded-lg" placeholder="Employer Name">
			  </div>
			  <div>
				<label class="block text-sm font-medium text-gray-700 mb-2">Start Date</label>
				<input type="month" class="startDate w-full px-3 py-2 border rounded-lg">
			  </div>
			  <div>
				<label class="block text-sm font-medium text-gray-700 mb-2">End Date</label>
				<input type="month" class="endDate w-full px-3 py-2 border rounded-lg">
			  </div>
			  <div>
				<label class="block text-sm font-medium text-gray-700 mb-2">Location</label>
				<input type="text" class="location w-full px-3 py-2 border rounded-lg" placeholder="Location">
			  </div>
			  <div>
				<label class="block text-sm font-medium text-gray-700 mb-2">Job Description</label>
				<textarea class="jobDescription w-full px-3 py-2 border rounded-lg" placeholder="Job Description"></textarea>
			  </div>
			</div>

			<div class="actions flex gap-3 mt-4">
			  <button type="button" class="save-btn bg-green-600 text-white px-3 py-2 rounded hover:bg-green-700">Save</button>
			  <button type="button" class="delete-btn bg-red-600 text-white px-3 py-2 rounded hover:bg-red-700">Delete</button>
			</div>
		  </div>
		`;


	  newItem.querySelector('.header').addEventListener('click', () => {
		if (!isMobile()) {
		  document.querySelectorAll('.internship-item').forEach(other => {
			if (other !== newItem) {
			  other.querySelector('.content').classList.add('hidden');
			  other.querySelector('.arrow-icon').classList.remove('rotate-180');
			}
		  });
		  const content = newItem.querySelector('.content');
		  const arrow = newItem.querySelector('.arrow-icon');
		  content.classList.toggle('hidden');
		  arrow.classList.toggle('rotate-180');
		} else {
		  openMobileModalForInternship('edit', null, newItem);
		}
	  });

	  newItem.querySelector('.save-btn').addEventListener('click', () => {
		const data = {
		  internship_id:   newItem.querySelector('.internship_id').value.trim(),
		  jobTitle:        newItem.querySelector('.jobTitle').value.trim(),
		  employerName:    newItem.querySelector('.employerName').value.trim(),
		  startDate:       newItem.querySelector('.startDate').value.trim(),
		  endDate:         newItem.querySelector('.endDate').value.trim(),
		  location:        newItem.querySelector('.location').value.trim(),
		  jobDescription:  newItem.querySelector('.jobDescription').value.trim()
		};
		saveInternshipRecordCommon(data, newItem);
	  });

	  newItem.querySelector('.delete-btn').addEventListener('click', e => {
		e.stopPropagation();
		deleteInternshipItem(newItem);
	  });

	  return newItem;
	}
	
	// --- Attach click events (Desktop behavior) ---
	function attachInternshipItemEvents() {
	  document.querySelectorAll('.internship-item').forEach(item => {
		const header = item.querySelector('.header');
		const content = item.querySelector('.content');
		const arrow = item.querySelector('.arrow-icon');
		
		header.addEventListener('click', () => {
		  if (!isMobile()) {
			document.querySelectorAll('.internship-item').forEach(otherItem => {
			  if (otherItem !== item) {
				otherItem.querySelector('.content').classList.add('hidden');
				otherItem.querySelector('.arrow-icon').classList.remove('rotate-180');
			  }
			});
			content.classList.toggle('hidden');
			arrow.classList.toggle('rotate-180');
		  } else {
			openMobileModalForInternship('edit', null, item);
		  }
		});
	  });
	}

	// --- Attach Save and Delete events ---
	function attachInternshipSaveButtonEvents() {
	  document.querySelectorAll('.internship-item .update-btn').forEach(button => {
		button.addEventListener('click', e => {
		  e.stopPropagation();
		  const parent = button.closest('.internship-item');
		  const data = {
			internship_id:   parent.querySelector('.internship_id').value.trim(),
			jobTitle:        parent.querySelector('.jobTitle').value.trim(),
			employerName:    parent.querySelector('.employerName').value.trim(),
			startDate:       parent.querySelector('.startDate').value.trim(),
			endDate:         parent.querySelector('.endDate').value.trim(),
			location:        parent.querySelector('.location').value.trim(),
			jobDescription:  parent.querySelector('.jobDescription').value.trim()
		  };
		  saveInternshipRecordCommon(data, parent);
		});
	  });
	}

	function attachInternshipDeleteButtonEvents() {
	  document.querySelectorAll('.internship-item .delete-btn').forEach(button => {
		button.addEventListener('click', e => {
		  e.stopPropagation();
		  deleteInternshipItem(button.closest('.internship-item'));
		});
	  });
	}
	
	// --- Delete an Internship Record (Desktop) ---
	function deleteInternshipItem(item) {	  
	  const internshipId = item.querySelector('.internship_id').value.trim();
	  if (!internshipId) {
		item.remove();
		//alert("Item removed from the form (not saved in the database).");
		return;
	  }
	  
	  const payload = new URLSearchParams();
	  payload.append('internship_id', internshipId);
	  payload.append('template_id', document.getElementById('templateId').value);
	  
	  fetch('<?= base_url("website/services/ResumeBuilder/delete_internship") ?>', {
		method: 'POST',
		headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
		body: payload.toString()
	  })
	  .then(res => res.json())
	  .then(result => {
		if (result.success) {
		  item.remove();
		  updatePreview();
		  //alert("Record removed successfully.");
		} else {
		  alert("Error deleting record: " + result.message);
		}
	  })
	  .catch(err => {
		alert("Network error while deleting record.");
		console.error("Error deleting internship record:", err);
	  });
	}

	// --- Save/Update Internship Record ---
	async function saveInternshipRecordCommon(dataObj, item = null) {
	  const payload = new URLSearchParams();
	  payload.append('template_id', document.getElementById('templateId').value);
	  payload.append('internship_id', dataObj.internship_id);
	  payload.append('jobTitle', dataObj.jobTitle);
	  payload.append('employerName', dataObj.employerName);
	  payload.append('startDate', dataObj.startDate);
	  payload.append('endDate', dataObj.endDate);
	  payload.append('location', dataObj.location);
	  payload.append('jobDescription', dataObj.jobDescription);
	  
	  try {
		const response = await fetch('<?= base_url("website/services/ResumeBuilder/save_or_update_internship") ?>', {
		  method: 'POST',
		  headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
		  body: payload.toString()
		});
		const result = await response.json();
			if (result.success) {
			  if (!dataObj.internship_id && item) {
				item.remove(); // Remove the temporary new entry
			  }

			  loadInternshipData();     // Reload internships from server
			  attachInternshipItemEvents(); // Re-attach events
			  updatePreview();          // Refresh preview

			  if (item && !dataObj.internship_id && result.data?.id) {
				item.setAttribute('data-id', result.data.id);
				item.querySelector('.internship_id').value = result.data.id;
			  }
		 } else {
		  console.error("Error saving internship record:", result.message);
		}
	  } catch (error) {
		console.error("Network error while saving internship record:", error);
	  }
	}

	// --- Open the Mobile Modal for Internship ---
	function openMobileModalForInternship(mode, triggerButton = null, currentItem = null) {
	  const modal      = document.getElementById('itemModal');
	  const modalTitle = document.getElementById('modalTitle');
	  const modalForm  = document.getElementById('modalForm');
	  
	  modal.classList.remove('hidden');
	  currentInternshipMobileItem = (mode === 'edit' ? currentItem : null);
	  modalTitle.textContent = mode === 'edit' ? 'Edit Internship' : 'Add Internship';
	  
	  let deleteButtonHtml = '';
	  if (mode === 'edit' && currentItem?.querySelector('.internship_id').value) {
		deleteButtonHtml = `<button type="button" onclick="deleteMobileInternshipRecord()" class="flex-1 px-4 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700">Delete</button>`;
	  }
	  
	  modalForm.innerHTML = `
		<input type="hidden" id="internship_id" value="${currentItem?.querySelector('.internship_id')?.value || ''}">
		<input type="hidden" id="form_source" value="modal_form">
		<div>
		  <label class="block text-sm font-medium mb-2">Job Title</label>
		  <input type="text" id="jobTitle" class="w-full px-4 py-3 border rounded-lg" required>
		</div>
		<div>
		  <label class="block text-sm font-medium mb-2">Employer Name</label>
		  <input type="text" id="employerName" class="w-full px-4 py-3 border rounded-lg" required>
		</div>
		<div>
		  <label class="block text-sm font-medium mb-2">Start Date</label>
		  <input type="date" id="startDate" class="w-full px-4 py-3 border rounded-lg" required>
		</div>
		<div>
		  <label class="block text-sm font-medium mb-2">End Date</label>
		  <input type="date" id="endDate" class="w-full px-4 py-3 border rounded-lg" required>
		</div>
		<div>
		  <label class="block text-sm font-medium mb-2">Location</label>
		  <input type="text" id="location" class="w-full px-4 py-3 border rounded-lg" required>
		</div>
		<div>
		  <label class="block text-sm font-medium mb-2">Job Description</label>
		  <textarea id="jobDescription" class="w-full px-4 py-3 border rounded-lg" required></textarea>
		</div>
		<div class="flex gap-3 mt-6">
		  <button type="button" onclick="closeMobileModal()" class="flex-1 px-4 py-3 border border-gray-300 rounded-lg hover:bg-gray-50">Cancel</button>
		  ${deleteButtonHtml}
		  <button type="submit" class="flex-1 px-4 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Save</button>
		</div>
	  `;
	  
	  if (mode === 'edit' && currentItem) {
		document.getElementById('jobTitle').value       = currentItem.querySelector('.jobTitle').value || currentItem.querySelector('h3').textContent.trim();
		document.getElementById('employerName').value   = currentItem.querySelector('.employerName').value || '';
		document.getElementById('startDate').value      = currentItem.querySelector('.startDate').value || '';
		document.getElementById('endDate').value        = currentItem.querySelector('.endDate').value || '';
		document.getElementById('location').value       = currentItem.querySelector('.location').value || '';
		document.getElementById('jobDescription').value = currentItem.querySelector('.jobDescription').value || '';
	  }
	}

	// --- Delete Internship Record from Mobile Modal ---
	function deleteMobileInternshipRecord() {
	  //if (!confirm("Are you sure you want to delete this record?")) return;
	  const internshipId = document.getElementById('internship_id').value.trim();
	  if (!internshipId) {
		closeMobileModal();
		//alert("This record has not been saved in the database.");
		return;
	  }
	  const payload = new URLSearchParams();
	  payload.append('internship_id', internshipId);
	  payload.append('template_id', document.getElementById('templateId').value);
	  
	  fetch('<?= base_url("website/services/ResumeBuilder/delete_internship") ?>', {
		method: 'POST',
		headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
		body: payload.toString()
	  })
	  .then(res => res.json())
	  .then(result => {
		if (result.success) {
		  currentInternshipMobileItem?.remove();
		  updatePreview();
		  closeMobileModal();
		  //alert("Record deleted successfully.");
		} else {
		  alert("Error deleting record: " + result.message);
		}
	  })
	  .catch(err => {
		alert("Network error while deleting record.");
		console.error(err);
	  });
	}

	// --- Handle modal form submission (for internship) ---
	document.getElementById('modalForm').addEventListener('submit', event => {
	  event.preventDefault();
	  if (document.getElementById('jobTitle')) {
		const data = {
		  internship_id:   document.getElementById('internship_id').value.trim(),
		  jobTitle:        document.getElementById('jobTitle').value.trim(),
		  employerName:    document.getElementById('employerName').value.trim(),
		  startDate:       document.getElementById('startDate').value.trim(),
		  endDate:         document.getElementById('endDate').value.trim(),
		  location:        document.getElementById('location').value.trim(),
		  jobDescription:  document.getElementById('jobDescription').value.trim()
		};
		saveInternshipRecordCommon(data);
		closeMobileModal();
	  }
	});

	// --- Add Internship button handler (Desktop & Mobile) ---
	document.getElementById('addInternship').addEventListener('click', () => {
	  if (isMobile()) {
		openMobileModalForInternship('add');
	  } else {
		document.querySelectorAll('.internship-item').forEach(item => {
		  item.querySelector('.content').classList.add('hidden');
		  item.querySelector('.arrow-icon').classList.remove('rotate-180');
		});
		const newIntern = createDesktopInternshipItem();
		newIntern.querySelector('.content').classList.remove('hidden');
		newIntern.querySelector('.arrow-icon').classList.add('rotate-180');
		document.getElementById('internshipContainer').appendChild(newIntern);
		attachInternshipDeleteButtonEvents();
	  }
	});

	// Finally, load the internship data when the page loads.
	loadInternshipData();

	// Global variable to track the certification item being edited in mobile mode.
	let currentCertificationMobileItem = null;

	// --- Load Certification Data ---
	async function loadCertificationData() {
	  try {
		const formData = new FormData();
		const templateId = document.getElementById('templateId').value;
		formData.append('template_id', templateId);
		
		const response = await fetch('<?= base_url("website/services/ResumeBuilder/fetch_certifications"); ?>', {
		  method: 'POST',
		  body: formData
		});
		
		if (!response.ok) throw new Error(`Network error: ${response.statusText}`);
		
		const data = await response.json();
		if (data.success) {
		  const container = document.getElementById('showCertificationContainer');
		  container.innerHTML = ''; // Clear current content
		  
		  data.certifications.forEach(cert => {
			const {
			  id = '',
			  certificationName = '',
			  issuingAuthority = '',
			  dateIssued = '',
			  expiryDate = ''
			} = cert;
			
			const itemHtml = `
			  <div class="certification-item border-l-4 border-blue-600 pl-6 py-4 rounded-lg transition-all duration-300 hover:border-blue-700 hover:bg-gray-50"
				   data-id="${id}">
				<div class="header cursor-pointer flex justify-between items-center">
				  <div>
					<h3 class="text-lg font-semibold text-gray-900">${certificationName}</h3>
					<p class="text-sm text-gray-500 mt-1">${issuingAuthority}</p>
				  </div>
				  <div class="arrow-icon transform transition-transform duration-300">
					<svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
					  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
							d="M19 9l-7 7-7-7"/>
					</svg>
				  </div>
				</div>
				<div class="content hidden mt-4 space-y-4">
				  <input type="hidden" class="certification_id" value="${id}">

				  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
					<div>
					  <label class="block text-sm font-medium text-gray-700 mb-2">Certification Name</label>
					  <input type="text" class="certificationName w-full px-3 py-2 border rounded-lg" value="${certificationName}" placeholder="Certification Name" autocomplete="off">
					</div>
					<div>
					  <label class="block text-sm font-medium text-gray-700 mb-2">Issuing Authority</label>
					  <input type="text" class="issuingAuthority w-full px-3 py-2 border rounded-lg" value="${issuingAuthority}" placeholder="e.g., Google, Coursera" autocomplete="off">
					</div>
				  </div>

				  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
					<div>
					  <label class="block text-sm font-medium text-gray-700 mb-2">Date Issued</label>
					  <input type="month" class="dateIssued w-full px-3 py-2 border rounded-lg" value="${dateIssued}" placeholder="MM/YYYY">
					</div>
					<div>
					  <label class="block text-sm font-medium text-gray-700 mb-2">Expiry Date <span class="text-gray-400">(optional)</span></label>
					  <input type="month" class="expiryDate w-full px-3 py-2 border rounded-lg" value="${expiryDate}" placeholder="MM/YYYY">
					</div>
				  </div>

				  <div class="actions flex gap-3 mt-4">
					<button type="button" class="update-btn bg-green-600 text-white px-3 py-2 rounded hover:bg-green-700">Save</button>
					<button type="button" class="delete-btn bg-red-600 text-white px-3 py-2 rounded hover:bg-red-700">Delete</button>
				  </div>
				</div>
			  </div>
			`;
			container.insertAdjacentHTML('beforeend', itemHtml);
		  });
		  
		  attachCertificationItemEvents();
		  attachCertificationSaveButtonEvents();
		  attachCertificationDeleteButtonEvents();
		} else {
		  console.error("Backend error:", data.message);
		}
	  } catch (error) {
		console.error("Error fetching certification data:", error);
		alert("Error loading certification data. Please try again later.");
	  }
	}
	
	function createDesktopCertificationItem() {
	  const newItem = document.createElement('div');
	  newItem.className = 'certification-item border-l-4 border-blue-600 pl-4 rounded-lg mt-4';
	  newItem.innerHTML = `
		<div class="header cursor-pointer flex justify-between items-center">
		  <div class="flex-1">
			<h3 class="font-semibold text-gray-800">New Certification</h3>
			<p class="text-sm text-gray-600">Issuing Authority</p>
		  </div>
		  <div class="flex items-center space-x-4">
			<button class="edit-btn text-blue-600 hover:text-blue-800 transition-colors">
			  <svg class="w-5 h-5"><use xlink:href="#pencil" /></svg>
			</button>
			<span class="arrow-icon transform transition-transform duration-300">
			  <svg class="w-6 h-6 text-gray-600"><use xlink:href="#chevron-down" /></svg>
			</span>
		  </div>
		</div>
		<div class="content hidden mt-4 space-y-4">
		  <input type="hidden" class="certification_id" value="">

		  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
			<div>
			  <label class="block text-sm font-medium text-gray-700 mb-2">Certification Name</label>
			  <input type="text" class="certificationName w-full px-3 py-2 border rounded-lg" placeholder="Certification Name" autocomplete="off">
			</div>
			<div>
			  <label class="block text-sm font-medium text-gray-700 mb-2">Issuing Authority</label>
			  <input type="text" class="issuingAuthority w-full px-3 py-2 border rounded-lg" placeholder="e.g., Coursera, Microsoft" autocomplete="off">
			</div>
		  </div>

		  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
			<div>
			  <label class="block text-sm font-medium text-gray-700 mb-2">Date Issued</label>
			  <input type="month" class="dateIssued w-full px-3 py-2 border rounded-lg" placeholder="MM/YYYY">
			</div>
			<div>
			  <label class="block text-sm font-medium text-gray-700 mb-2">Expiry Date <span class="text-gray-400">(optional)</span></label>
			  <input type="month" class="expiryDate w-full px-3 py-2 border rounded-lg" placeholder="MM/YYYY">
			</div>
		  </div>

		  <div class="actions flex gap-3 mt-4">
			<button type="button" class="save-btn bg-green-600 text-white px-3 py-2 rounded hover:bg-green-700">Save</button>
			<button type="button" class="delete-btn bg-red-600 text-white px-3 py-2 rounded hover:bg-red-700">Delete</button>
		  </div>
		</div>
	  `;

	  // Toggle open/close content
	  newItem.querySelector('.header').addEventListener('click', () => {
		if (!isMobile()) {
		  document.querySelectorAll('.certification-item').forEach(other => {
			if (other !== newItem) {
			  other.querySelector('.content').classList.add('hidden');
			  other.querySelector('.arrow-icon').classList.remove('rotate-180');
			}
		  });
		  const content = newItem.querySelector('.content');
		  const arrow = newItem.querySelector('.arrow-icon');
		  content.classList.toggle('hidden');
		  arrow.classList.toggle('rotate-180');
		} else {
		  openMobileModalForCertification('edit', null, newItem);
		}
	  });

	  // Save button logic
	  newItem.querySelector('.save-btn').addEventListener('click', () => {
		const data = {
		  certification_id:  newItem.querySelector('.certification_id').value.trim(),
		  certificationName: newItem.querySelector('.certificationName').value.trim(),
		  issuingAuthority:  newItem.querySelector('.issuingAuthority').value.trim(),
		  dateIssued:        newItem.querySelector('.dateIssued').value.trim(),
		  expiryDate:        newItem.querySelector('.expiryDate').value.trim()
		};
		saveCertificationRecordCommon(data, newItem);
	  });

	  // Delete button logic
	  newItem.querySelector('.delete-btn').addEventListener('click', e => {
		e.stopPropagation();
		deleteCertificationItem(newItem);
	  });

	  return newItem;
	}
	
	// --- Attach click events (Desktop behavior) ---
	function attachCertificationItemEvents() {
	  document.querySelectorAll('.certification-item').forEach(item => {
		const header = item.querySelector('.header');
		const content = item.querySelector('.content');
		const arrow = item.querySelector('.arrow-icon');
		
		header.addEventListener('click', () => {
		  if (!isMobile()) {
			document.querySelectorAll('.certification-item').forEach(otherItem => {
			  if (otherItem !== item) {
				otherItem.querySelector('.content').classList.add('hidden');
				otherItem.querySelector('.arrow-icon').classList.remove('rotate-180');
			  }
			});
			content.classList.toggle('hidden');
			arrow.classList.toggle('rotate-180');
		  } else {
			openMobileModalForCertification('edit', null, item);
		  }
		});
	  });
	}

	// --- Attach Save and Delete events ---
	function attachCertificationSaveButtonEvents() {
	  document.querySelectorAll('.certification-item .update-btn').forEach(button => {
		button.addEventListener('click', e => {
		  e.stopPropagation();
		  const parent = button.closest('.certification-item');
		  const data = {
			certification_id:  parent.querySelector('.certification_id').value.trim(),
			certificationName: parent.querySelector('.certificationName').value.trim(),
			issuingAuthority:  parent.querySelector('.issuingAuthority').value.trim(),
			dateIssued:        parent.querySelector('.dateIssued').value.trim(),
			expiryDate:        parent.querySelector('.expiryDate').value.trim()
		  };
		  saveCertificationRecordCommon(data, parent);
		});
	  });
	}

	function attachCertificationDeleteButtonEvents() {
	  document.querySelectorAll('.certification-item .delete-btn').forEach(button => {
		button.addEventListener('click', e => {
		  e.stopPropagation();
		  deleteCertificationItem(button.closest('.certification-item'));
		});
	  });
	}

	// --- Delete a Certification Record (Desktop) ---
	function deleteCertificationItem(item) {
	  //if (!confirm("Are you sure you want to delete this record?")) return;
	  
	  const certificationId = item.querySelector('.certification_id').value.trim();
	  if (!certificationId) {
		item.remove();
		//alert("Item removed from the form (not saved in the database).");
		return;
	  }
	  
	  const payload = new URLSearchParams();
	  payload.append('certification_id', certificationId);
	  payload.append('template_id', document.getElementById('templateId').value);
	  
	  fetch('<?= base_url("website/services/ResumeBuilder/delete_certification") ?>', {
		method: 'POST',
		headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
		body: payload.toString()
	  })
	  .then(res => res.json())
	  .then(result => {
		if (result.success) {
		  item.remove();
		  updatePreview();
		  //alert("Record removed successfully.");
		} else {
		  alert("Error deleting record: " + result.message);
		}
	  })
	  .catch(err => {
		alert("Network error while deleting record.");
		console.error("Error deleting certification record:", err);
	  });
	}

	// --- Save/Update Certification Record ---
	async function saveCertificationRecordCommon(dataObj, item = null) {
	  const payload = new URLSearchParams();
	  payload.append('template_id', document.getElementById('templateId').value);
	  payload.append('certification_id', dataObj.certification_id);
	  payload.append('certificationName', dataObj.certificationName);
	  payload.append('issuingAuthority', dataObj.issuingAuthority);
	  payload.append('dateIssued', dataObj.dateIssued);
	  payload.append('expiryDate', dataObj.expiryDate);
	  
	  try {
		const response = await fetch('<?= base_url("website/services/ResumeBuilder/save_or_update_certification") ?>', {
		  method: 'POST',
		  headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
		  body: payload.toString()
		});
		const result = await response.json();
		if (result.success) {
			  if (!dataObj.certification_id && item) {
				item.remove(); // Remove the unsaved temporary DOM block
			  }

			  loadCertificationData();       // Reload certifications
			  attachCertificationItemEvents(); // Re-attach events
			  updatePreview();               // Refresh resume preview

			  if (item && !dataObj.certification_id && result.data?.id) {
				item.setAttribute('data-id', result.data.id);
				item.querySelector('.certification_id').value = result.data.id;
			  }
			}
			else {
					  console.error("Error saving certification record:", result.message);
		}
	  } catch (error) {
		console.error("Network error while saving certification record:", error);
	  }
	}

	// --- Open the Mobile Modal for Certification ---
	function openMobileModalForCertification(mode, triggerButton = null, currentItem = null) {
	  const modal      = document.getElementById('itemModal');
	  const modalTitle = document.getElementById('modalTitle');
	  const modalForm  = document.getElementById('modalForm');
	  
	  modal.classList.remove('hidden');
	  currentCertificationMobileItem = (mode === 'edit' ? currentItem : null);
	  modalTitle.textContent = mode === 'edit' ? 'Edit Certification' : 'Add Certification';
	  
	  let deleteButtonHtml = '';
	  if (mode === 'edit' && currentItem?.querySelector('.certification_id').value) {
		deleteButtonHtml = `<button type="button" onclick="deleteMobileCertificationRecord()" class="flex-1 px-4 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700">Delete</button>`;
	  }
	  
	  modalForm.innerHTML = `
		<input type="hidden" id="certification_id" value="${currentItem?.querySelector('.certification_id')?.value || ''}">
		<input type="hidden" id="form_source" value="modal_form">
		<div>
		  <label class="block text-sm font-medium mb-2">Certification Name</label>
		  <input type="text" id="certificationName" class="w-full px-4 py-3 border rounded-lg" required>
		</div>
		<div>
		  <label class="block text-sm font-medium mb-2">Issuing Authority</label>
		  <input type="text" id="issuingAuthority" class="w-full px-4 py-3 border rounded-lg" required>
		</div>
		<div>
		  <label class="block text-sm font-medium mb-2">Date Issued</label>
		  <input type="month" id="dateIssued" class="w-full px-4 py-3 border rounded-lg" required>
		</div>
		<div>
		  <label class="block text-sm font-medium mb-2">Expiry Date</label>
		  <input type="month" id="expiryDate" class="w-full px-4 py-3 border rounded-lg" required>
		</div>
		<div class="flex gap-3 mt-6">
		  <button type="button" onclick="closeMobileModal()" class="flex-1 px-4 py-3 border border-gray-300 rounded-lg hover:bg-gray-50">Cancel</button>
		  ${deleteButtonHtml}
		  <button type="submit" class="flex-1 px-4 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Save</button>
		</div>
	  `;
	  
	  if (mode === 'edit' && currentItem) {
		document.getElementById('certificationName').value = currentItem.querySelector('.certificationName').value || currentItem.querySelector('h3').textContent.trim();
		document.getElementById('issuingAuthority').value  = currentItem.querySelector('.issuingAuthority').value || '';
		document.getElementById('dateIssued').value        = currentItem.querySelector('.dateIssued').value || '';
		document.getElementById('expiryDate').value        = currentItem.querySelector('.expiryDate').value || '';
	  }
	}

	// --- Delete Certification Record from Mobile Modal ---
	window.deleteMobileCertificationRecord = function() { 
	  //if (!confirm("Are you sure you want to delete this record?")) return;
	  const certificationId = document.getElementById('certification_id').value.trim();
	  if (!certificationId) {
		closeMobileModal();
		//alert("This record has not been saved in the database.");
		return;
	  }
	  const payload = new URLSearchParams();
	  payload.append('certification_id', certificationId);
	  payload.append('template_id', document.getElementById('templateId').value);
	  
	  fetch('<?= base_url("website/services/ResumeBuilder/delete_certification") ?>', {
		method: 'POST',
		headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
		body: payload.toString()
	  })
	  .then(res => res.json())
	  .then(result => {
		if (result.success) {
		  currentCertificationMobileItem?.remove();
		  updatePreview();
		  closeMobileModal();
		  //alert("Record deleted successfully.");
		} else {
		  alert("Error deleting record: " + result.message);
		}
	  })
	  .catch(err => {
		alert("Network error while deleting record.");
		console.error(err);
	  });
	}

	// --- Handle modal form submission (for certification) ---
	document.getElementById('modalForm').addEventListener('submit', event => {
	  event.preventDefault();
	  if (document.getElementById('certificationName')) {
		const data = {
		  certification_id:  document.getElementById('certification_id').value.trim(),
		  certificationName: document.getElementById('certificationName').value.trim(),
		  issuingAuthority:  document.getElementById('issuingAuthority').value.trim(),
		  dateIssued:        document.getElementById('dateIssued').value.trim(),
		  expiryDate:        document.getElementById('expiryDate').value.trim()
		};
		saveCertificationRecordCommon(data);
		closeMobileModal();
	  }
	});

	// --- Add Certification button handler (Desktop & Mobile) ---
	document.getElementById('addCertification').addEventListener('click', () => {
	  if (isMobile()) {
		openMobileModalForCertification('add');
	  } else {
		document.querySelectorAll('.certification-item').forEach(item => {
		  item.querySelector('.content').classList.add('hidden');
		  item.querySelector('.arrow-icon').classList.remove('rotate-180');
		});
		const newCert = createDesktopCertificationItem();
		newCert.querySelector('.content').classList.remove('hidden');
		newCert.querySelector('.arrow-icon').classList.add('rotate-180');
		document.getElementById('certificationContainer').appendChild(newCert);
		attachCertificationDeleteButtonEvents();
	  }
	});

	// Finally, load the certification data when the page loads.
	loadCertificationData();

	// Global variable to track the project item being edited in mobile mode.
	let currentProjectMobileItem = null;

	// --- Load Project Data ---
	async function loadProjectData() {
	  try {
		const formData = new FormData();
		const templateId = document.getElementById('templateId').value;
		formData.append('template_id', templateId);
		
		const response = await fetch('<?= base_url("website/services/ResumeBuilder/fetch_projects"); ?>', {
		  method: 'POST',
		  body: formData
		});
		
		if (!response.ok) throw new Error(`Network error: ${response.statusText}`);
		
		const data = await response.json();
		if (data.success) {
		  const container = document.getElementById('showProjectContainer');
		  container.innerHTML = ''; // Clear current content
		  
		  data.projects.forEach(proj => {
			const {
			  id = '',
			  projectName = '',
			  description = '',
			  role = '',
			  technologiesUsed = '',
			  outcome = '',
			  projectUrl = ''
			} = proj;
			
			const itemHtml = `
			  <div class="project-item border-l-4 border-blue-600 pl-6 py-4 rounded-lg transition-all duration-300 hover:border-blue-700 hover:bg-gray-50"
				   data-id="${id}">
				<div class="header cursor-pointer flex justify-between items-center">
				  <div>
					<h3 class="text-lg font-semibold text-gray-900">${projectName}</h3>
					<p class="text-sm text-gray-500 mt-1">${role}</p>
				  </div>
				  <div class="arrow-icon transform transition-transform duration-300">
					<svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
					  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 9l-7 7-7-7"/>
					</svg>
				  </div>
				</div>
				<div class="content hidden mt-4 space-y-4">
				 <div class="grid grid-cols-2 gap-4">
					  <input type="hidden" class="project_id" value="${id}">
					  <div>
						<label class="block text-sm font-medium text-gray-700 mb-2">Project Name</label>
						<input type="text" class="projectName w-full px-3 py-2 border rounded-lg" value="${projectName}" placeholder="Project Name">
					  </div>
					  <div>
						<label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
						<textarea class="description w-full px-3 py-2 border rounded-lg" placeholder="Description">${description}</textarea>
					  </div>
					  <div>
						<label class="block text-sm font-medium text-gray-700 mb-2">Role</label>
						<input type="text" class="role w-full px-3 py-2 border rounded-lg" value="${role}" placeholder="Role">
					  </div>
					  <div>
						<label class="block text-sm font-medium text-gray-700 mb-2">Technologies Used</label>
						<input type="text" class="technologiesUsed w-full px-3 py-2 border rounded-lg" value="${technologiesUsed}" placeholder="Technologies Used">
					  </div>
					  <div>
						<label class="block text-sm font-medium text-gray-700 mb-2">Outcome</label>
						<textarea class="outcome w-full px-3 py-2 border rounded-lg" placeholder="Outcome">${outcome}</textarea>
					  </div>
					  <div>
						<label class="block text-sm font-medium text-gray-700 mb-2">Project URL</label>
						<input type="url" class="projectUrl w-full px-3 py-2 border rounded-lg" value="${projectUrl}" placeholder="Project URL">
					  </div>
				  </div>
				  <div class="actions flex gap-3 mt-4">
					<button type="button" class="update-btn bg-green-600 text-white px-3 py-2 rounded hover:bg-green-700">Save</button>
					<button type="button" class="delete-btn bg-red-600 text-white px-3 py-2 rounded hover:bg-red-700">Delete</button>
				  </div>
				</div>
			  </div>
			`;
			container.insertAdjacentHTML('beforeend', itemHtml);
		  });
		  
		  attachProjectItemEvents();
		  attachProjectSaveButtonEvents();
		  attachProjectDeleteButtonEvents();
		} else {
		  console.error("Backend error:", data.message);
		  //alert("No project data found. Please try again.");
		}
	  } catch (error) {
		console.error("Error fetching project data:", error);
		alert("Error loading project data. Please try again later.");
	  }
	}
	
	// --- Create a new project item (Desktop mode) ---
	function createDesktopProjectItem() {
	  const newItem = document.createElement('div');
	  newItem.className = 'project-item border-l-4 border-blue-600 pl-4 rounded-lg mt-4';
	  newItem.innerHTML = `
		  <div class="header cursor-pointer flex justify-between items-center">
			<div class="flex-1">
			  <h3 class="font-semibold text-gray-800">New Project</h3>
			  <p class="text-sm text-gray-600">Role</p>
			</div>
			<div class="flex items-center space-x-4">
			  <button class="edit-btn text-blue-600 hover:text-blue-800 transition-colors">
				<svg class="w-5 h-5"><use xlink:href="#pencil" /></svg>
			  </button>
			  <span class="arrow-icon transform transition-transform duration-300">
				<svg class="w-6 h-6 text-gray-600"><use xlink:href="#chevron-down" /></svg>
			  </span>
			</div>
		  </div>
		  <div class="content hidden mt-4 space-y-4">
			<input type="hidden" class="project_id" value="">

			<div class="grid grid-cols-2 gap-4">
			  <div>
				<label class="block text-sm font-medium text-gray-700 mb-2">Project Name</label>
				<input type="text" class="projectName w-full px-3 py-2 border rounded-lg" placeholder="Project Name">
			  </div>
			  <div>
				<label class="block text-sm font-medium text-gray-700 mb-2">Role</label>
				<input type="text" class="role w-full px-3 py-2 border rounded-lg" placeholder="Role">
			  </div>
			  <div class="col-span-2">
				<label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
				<textarea class="description w-full px-3 py-2 border rounded-lg" placeholder="Description"></textarea>
			  </div>
			  <div>
				<label class="block text-sm font-medium text-gray-700 mb-2">Technologies Used</label>
				<input type="text" class="technologiesUsed w-full px-3 py-2 border rounded-lg" placeholder="Technologies Used">
			  </div>
			  <div>
				<label class="block text-sm font-medium text-gray-700 mb-2">Project URL</label>
				<input type="url" class="projectUrl w-full px-3 py-2 border rounded-lg" placeholder="Project URL">
			  </div>
			  <div class="col-span-2">
				<label class="block text-sm font-medium text-gray-700 mb-2">Outcome</label>
				<textarea class="outcome w-full px-3 py-2 border rounded-lg" placeholder="Outcome"></textarea>
			  </div>
			</div>

			<div class="actions flex gap-3 mt-4">
			  <button type="button" class="save-btn bg-green-600 text-white px-3 py-2 rounded hover:bg-green-700">Save</button>
			  <button type="button" class="delete-btn bg-red-600 text-white px-3 py-2 rounded hover:bg-red-700">Delete</button>
			</div>
		  </div>
		`;
		
	  newItem.querySelector('.header').addEventListener('click', () => {
		if (!isMobile()) {
		  document.querySelectorAll('.project-item').forEach(other => {
			if (other !== newItem) {
			  other.querySelector('.content').classList.add('hidden');
			  other.querySelector('.arrow-icon').classList.remove('rotate-180');
			}
		  });
		  const content = newItem.querySelector('.content');
		  const arrow = newItem.querySelector('.arrow-icon');
		  content.classList.toggle('hidden');
		  arrow.classList.toggle('rotate-180');
		} else {
		  openMobileModalForProject('edit', null, newItem);
		}
	  });

	  newItem.querySelector('.save-btn').addEventListener('click', () => {
		const data = {
		  project_id:       newItem.querySelector('.project_id').value.trim(),
		  projectName:      newItem.querySelector('.projectName').value.trim(),
		  description:      newItem.querySelector('.description').value.trim(),
		  role:             newItem.querySelector('.role').value.trim(),
		  technologiesUsed: newItem.querySelector('.technologiesUsed').value.trim(),
		  outcome:          newItem.querySelector('.outcome').value.trim(),
		  projectUrl:       newItem.querySelector('.projectUrl').value.trim()
		};
		saveProjectRecordCommon(data, newItem);
	  });

	  newItem.querySelector('.delete-btn').addEventListener('click', e => {
		e.stopPropagation();
		deleteProjectItem(newItem);
	  });

	  return newItem;
	}
	
	// --- Attach click events (Desktop behavior) ---
	function attachProjectItemEvents() {
	  document.querySelectorAll('.project-item').forEach(item => {
		const header = item.querySelector('.header');
		const content = item.querySelector('.content');
		const arrow = item.querySelector('.arrow-icon');
		
		header.addEventListener('click', () => {
		  if (!isMobile()) {
			document.querySelectorAll('.project-item').forEach(otherItem => {
			  if (otherItem !== item) {
				otherItem.querySelector('.content').classList.add('hidden');
				otherItem.querySelector('.arrow-icon').classList.remove('rotate-180');
			  }
			});
			content.classList.toggle('hidden');
			arrow.classList.toggle('rotate-180');
		  } else {
			openMobileModalForProject('edit', null, item);
		  }
		});
	  });
	}

	// --- Attach Save and Delete events ---
	function attachProjectSaveButtonEvents() {
	  document.querySelectorAll('.project-item .update-btn').forEach(button => {
		button.addEventListener('click', e => {
		  e.stopPropagation();
		  const parent = button.closest('.project-item');
		  const data = {
			project_id:       parent.querySelector('.project_id').value.trim(),
			projectName:      parent.querySelector('.projectName').value.trim(),
			description:      parent.querySelector('.description').value.trim(),
			role:             parent.querySelector('.role').value.trim(),
			technologiesUsed: parent.querySelector('.technologiesUsed').value.trim(),
			outcome:          parent.querySelector('.outcome').value.trim(),
			projectUrl:       parent.querySelector('.projectUrl').value.trim()
		  };
		  saveProjectRecordCommon(data, parent);
		});
	  });
	}

	function attachProjectDeleteButtonEvents() {
	  document.querySelectorAll('.project-item .delete-btn').forEach(button => {
		button.addEventListener('click', e => {
		  e.stopPropagation();
		  deleteProjectItem(button.closest('.project-item'));
		});
	  });
	}

	// --- Delete a Project Record (Desktop) ---
	function deleteProjectItem(item) {
	 
	  const projectId = item.querySelector('.project_id').value.trim();
	  if (!projectId) {
		item.remove();
		//alert("Item removed from the form (not saved in the database).");
		return;
	  }
	  
	  const payload = new URLSearchParams();
	  payload.append('project_id', projectId);
	  payload.append('template_id', document.getElementById('templateId').value);
	  
	  fetch('<?= base_url("website/services/ResumeBuilder/delete_project") ?>', {
		method: 'POST',
		headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
		body: payload.toString()
	  })
	  .then(res => res.json())
	  .then(result => {
		if (result.success) {
		  item.remove();
		  updatePreview();
		  //alert("Record removed successfully.");
		} else {
		  alert("Error deleting record: " + result.message);
		}
	  })
	  .catch(err => {
		alert("Network error while deleting record.");
		console.error("Error deleting project record:", err);
	  });
	}

	// --- Save/Update Project Record ---
	async function saveProjectRecordCommon(dataObj, item = null) {
	  const payload = new URLSearchParams();
	  payload.append('template_id', document.getElementById('templateId').value);
	  payload.append('project_id', dataObj.project_id);
	  payload.append('projectName', dataObj.projectName);
	  payload.append('description', dataObj.description);
	  payload.append('role', dataObj.role);
	  payload.append('technologiesUsed', dataObj.technologiesUsed);
	  payload.append('outcome', dataObj.outcome);
	  payload.append('projectUrl', dataObj.projectUrl);
	  
	  try {
		const response = await fetch('<?= base_url("website/services/ResumeBuilder/save_or_update_project") ?>', {
		  method: 'POST',
		  headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
		  body: payload.toString()
		});
		const result = await response.json();
		if (result.success) {
			  if (!dataObj.project_id && item) {
				item.remove(); // Remove the unsaved temporary DOM block
			  }

			  loadProjectData();           // Reload projects
			  attachProjectItemEvents();   // Re-attach events
			  updatePreview();             // Refresh preview

			  if (item && !dataObj.project_id && result.data?.id) {
				item.setAttribute('data-id', result.data.id);
				item.querySelector('.project_id').value = result.data.id;
			  }
			}
			else {
		  console.error("Error saving project record:", result.message);
		}
	  } catch (error) {
		console.error("Network error while saving project record:", error);
	  }
	}

	// --- Open the Mobile Modal for Project ---
	function openMobileModalForProject(mode, triggerButton = null, currentItem = null) {
	  const modal      = document.getElementById('itemModal');
	  const modalTitle = document.getElementById('modalTitle');
	  const modalForm  = document.getElementById('modalForm');
	  
	  modal.classList.remove('hidden');
	  currentProjectMobileItem = (mode === 'edit' ? currentItem : null);
	  modalTitle.textContent = mode === 'edit' ? 'Edit Project' : 'Add Project';
	  
	  let deleteButtonHtml = '';
	  if (mode === 'edit' && currentItem?.querySelector('.project_id').value) {
		deleteButtonHtml = `<button type="button" onclick="deleteMobileProjectRecord()" class="flex-1 px-4 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700">Delete</button>`;
	  }
	  
	  modalForm.innerHTML = `
		<input type="hidden" id="project_id" value="${currentItem?.querySelector('.project_id')?.value || ''}">
		<input type="hidden" id="form_source" value="modal_form">
		<div>
		  <label class="block text-sm font-medium mb-2">Project Name</label>
		  <input type="text" id="projectName" class="w-full px-4 py-3 border rounded-lg" required>
		</div>
		<div>
		  <label class="block text-sm font-medium mb-2">Description</label>
		  <textarea id="description" class="w-full px-4 py-3 border rounded-lg" required></textarea>
		</div>
		<div>
		  <label class="block text-sm font-medium mb-2">Role</label>
		  <input type="text" id="role" class="w-full px-4 py-3 border rounded-lg" required>
		</div>
		<div>
		  <label class="block text-sm font-medium mb-2">Technologies Used</label>
		  <input type="text" id="technologiesUsed" class="w-full px-4 py-3 border rounded-lg" required>
		</div>
		<div>
		  <label class="block text-sm font-medium mb-2">Outcome</label>
		  <textarea id="outcome" class="w-full px-4 py-3 border rounded-lg" required></textarea>
		</div>
		<div>
		  <label class="block text-sm font-medium mb-2">Project URL</label>
		  <input type="url" id="projectUrl" class="w-full px-4 py-3 border rounded-lg" required>
		</div>
		<div class="flex gap-3 mt-6">
		  <button type="button" onclick="closeMobileModal()" class="flex-1 px-4 py-3 border border-gray-300 rounded-lg hover:bg-gray-50">Cancel</button>
		  ${deleteButtonHtml}
		  <button type="submit" class="flex-1 px-4 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Save</button>
		</div>
	  `;
	  
	  if (mode === 'edit' && currentItem) {
		document.getElementById('projectName').value       = currentItem.querySelector('.projectName').value || currentItem.querySelector('h3').textContent.trim();
		document.getElementById('description').value       = currentItem.querySelector('.description').value || '';
		document.getElementById('role').value              = currentItem.querySelector('.role').value || '';
		document.getElementById('technologiesUsed').value  = currentItem.querySelector('.technologiesUsed').value || '';
		document.getElementById('outcome').value           = currentItem.querySelector('.outcome').value || '';
		document.getElementById('projectUrl').value        = currentItem.querySelector('.projectUrl').value || '';
	  }
	}

	// --- Delete Project Record from Mobile Modal ---
	window.deleteMobileProjectRecord = function() {
	  //if (!confirm("Are you sure you want to delete this record?")) return;
	  const projectId = document.getElementById('project_id').value.trim();
	  if (!projectId) {
		closeMobileModal();
		//alert("This record has not been saved in the database.");
		return;
	  }
	  const payload = new URLSearchParams();
	  payload.append('project_id', projectId);
	  payload.append('template_id', document.getElementById('templateId').value);
	  
	  fetch('<?= base_url("website/services/ResumeBuilder/delete_project") ?>', {
		method: 'POST',
		headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
		body: payload.toString()
	  })
	  .then(res => res.json())
	  .then(result => {
		if (result.success) {
		  currentProjectMobileItem?.remove();
		  updatePreview();
		  closeMobileModal();
		  //alert("Record deleted successfully.");
		} else {
		  alert("Error deleting record: " + result.message);
		}
	  })
	  .catch(err => {
		alert("Network error while deleting record.");
		console.error(err);
	  });
	}

	// --- Handle modal form submission (for project) ---
	document.getElementById('modalForm').addEventListener('submit', event => {
	  event.preventDefault();
	  if (document.getElementById('projectName')) {
		const data = {
		  project_id:       document.getElementById('project_id').value.trim(),
		  projectName:      document.getElementById('projectName').value.trim(),
		  description:      document.getElementById('description').value.trim(),
		  role:             document.getElementById('role').value.trim(),
		  technologiesUsed: document.getElementById('technologiesUsed').value.trim(),
		  outcome:          document.getElementById('outcome').value.trim(),
		  projectUrl:       document.getElementById('projectUrl').value.trim()
		};
		saveProjectRecordCommon(data);
		closeMobileModal();
	  }
	});

	// --- Add Project button handler (Desktop & Mobile) ---
	document.getElementById('addProject').addEventListener('click', () => {
	  if (isMobile()) {
		openMobileModalForProject('add');
	  } else {
		document.querySelectorAll('.project-item').forEach(item => {
		  item.querySelector('.content').classList.add('hidden');
		  item.querySelector('.arrow-icon').classList.remove('rotate-180');
		});
		const newProj = createDesktopProjectItem();
		newProj.querySelector('.content').classList.remove('hidden');
		newProj.querySelector('.arrow-icon').classList.add('rotate-180');
		document.getElementById('projectContainer').appendChild(newProj);
		attachProjectDeleteButtonEvents();
	  }
	});

	// Finally, load the project data when the page loads.
	loadProjectData();

	// Global variable to track the award item being edited in mobile mode.
	let currentAwardMobileItem = null;

	// --- Load Award Data ---
	async function loadAwardData() {
	  try {
		const formData = new FormData();
		const templateId = document.getElementById('templateId').value;
		formData.append('template_id', templateId);
		
		const response = await fetch('<?= base_url("website/services/ResumeBuilder/fetch_awards"); ?>', {
		  method: 'POST',
		  body: formData
		});
		
		if (!response.ok) throw new Error(`Network error: ${response.statusText}`);
		
		const data = await response.json();
		if (data.success) {
		  const container = document.getElementById('showAwardContainer');
		  container.innerHTML = ''; // Clear current content
		  
		  data.awards.forEach(award => {
			const {
			  id = '',
			  awardName = '',
			  awardingOrganization = '',
			  dateAwarded = ''
			} = award;
			
			const itemHtml = `
			  <div class="award-item border-l-4 border-blue-600 pl-6 py-4 rounded-lg transition-all duration-300 hover:border-blue-700 hover:bg-gray-50"
				   data-id="${id}">

				<div class="header cursor-pointer flex justify-between items-center">
				  <div>
					<h3 class="text-lg font-semibold text-gray-900">${awardName}</h3>
					<p class="text-sm text-gray-500 mt-1">${awardingOrganization}</p>
				  </div>
				  <div class="arrow-icon transform transition-transform duration-300">
					<svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
					  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 9l-7 7-7-7"/>
					</svg>
				  </div>
				</div>

				<div class="content hidden mt-4 space-y-4">
				  <input type="hidden" class="award_id" value="${id}">

				  <div class="grid grid-cols-2 gap-4">
					<div>
					  <label class="block text-sm font-medium text-gray-700 mb-2">Award Name</label>
					  <input type="text" class="awardName w-full px-3 py-2 border rounded-lg" value="${awardName}" placeholder="Award Name">
					</div>
					<div>
					  <label class="block text-sm font-medium text-gray-700 mb-2">Awarding Organization</label>
					  <input type="text" class="awardingOrganization w-full px-3 py-2 border rounded-lg" value="${awardingOrganization}" placeholder="Awarding Organization">
					</div>
					<div>
					  <label class="block text-sm font-medium text-gray-700 mb-2">Date Awarded</label>
					  <input type="month" class="dateAwarded w-full px-3 py-2 border rounded-lg" value="${dateAwarded}">
					</div>
				  </div>

				  <div class="actions flex gap-3 mt-4">
					<button type="button" class="update-btn bg-green-600 text-white px-3 py-2 rounded hover:bg-green-700">Save</button>
					<button type="button" class="delete-btn bg-red-600 text-white px-3 py-2 rounded hover:bg-red-700">Delete</button>
				  </div>
				</div>
			  </div>
			`;

			container.insertAdjacentHTML('beforeend', itemHtml);
		  });
		  
		  attachAwardItemEvents();
		  attachAwardSaveButtonEvents();
		  attachAwardDeleteButtonEvents();
		} else {
		  console.error("Backend error:", data.message);
		  //alert("No award data found. Please try again.");
		}
	  } catch (error) {
		console.error("Error fetching award data:", error);
		alert("Error loading award data. Please try again later.");
	  }
	}
	
	// --- Create a new award item (Desktop mode) ---
	function createDesktopAwardItem() {
	  const newItem = document.createElement('div');
	  newItem.className = 'award-item border-l-4 border-blue-600 pl-4 rounded-lg mt-4';
	  newItem.innerHTML = `
		  <div class="header cursor-pointer flex justify-between items-center">
			<div class="flex-1">
			  <h3 class="font-semibold text-gray-800">New Award</h3>
			  <p class="text-sm text-gray-600">Awarding Organization</p>
			</div>
			<div class="flex items-center space-x-4">
			  <button class="edit-btn text-blue-600 hover:text-blue-800 transition-colors">
				<svg class="w-5 h-5"><use xlink:href="#pencil" /></svg>
			  </button>
			  <span class="arrow-icon transform transition-transform duration-300">
				<svg class="w-6 h-6 text-gray-600"><use xlink:href="#chevron-down" /></svg>
			  </span>
			</div>
		  </div>
		  <div class="content hidden mt-4 space-y-4">
			<input type="hidden" class="award_id" value="">

			<div class="grid grid-cols-2 gap-4">
			  <div>
				<label class="block text-sm font-medium text-gray-700 mb-2">Award Name</label>
				<input type="text" class="awardName w-full px-3 py-2 border rounded-lg" placeholder="Award Name">
			  </div>
			  <div>
				<label class="block text-sm font-medium text-gray-700 mb-2">Awarding Organization</label>
				<input type="text" class="awardingOrganization w-full px-3 py-2 border rounded-lg" placeholder="Awarding Organization">
			  </div>
			  <div>
				<label class="block text-sm font-medium text-gray-700 mb-2">Date Awarded</label>
				<input type="month" class="dateAwarded w-full px-3 py-2 border rounded-lg">
			  </div>
			</div>

			<div class="actions flex gap-3 mt-4">
			  <button type="button" class="save-btn bg-green-600 text-white px-3 py-2 rounded hover:bg-green-700">Save</button>
			  <button type="button" class="delete-btn bg-red-600 text-white px-3 py-2 rounded hover:bg-red-700">Delete</button>
			</div>
		  </div>
		`;


	  newItem.querySelector('.header').addEventListener('click', () => {
		if (!isMobile()) {
		  document.querySelectorAll('.award-item').forEach(other => {
			if (other !== newItem) {
			  other.querySelector('.content').classList.add('hidden');
			  other.querySelector('.arrow-icon').classList.remove('rotate-180');
			}
		  });
		  const content = newItem.querySelector('.content');
		  const arrow = newItem.querySelector('.arrow-icon');
		  content.classList.toggle('hidden');
		  arrow.classList.toggle('rotate-180');
		} else {
		  openMobileModalForAward('edit', null, newItem);
		}
	  });

	  newItem.querySelector('.save-btn').addEventListener('click', () => {
		const data = {
		  award_id:            newItem.querySelector('.award_id').value.trim(),
		  awardName:           newItem.querySelector('.awardName').value.trim(),
		  awardingOrganization: newItem.querySelector('.awardingOrganization').value.trim(),
		  dateAwarded:         newItem.querySelector('.dateAwarded').value.trim()
		};
		saveAwardRecordCommon(data, newItem);
	  });

	  newItem.querySelector('.delete-btn').addEventListener('click', e => {
		e.stopPropagation();
		deleteAwardItem(newItem);
	  });

	  return newItem;
	}
	
	// --- Attach click events (Desktop behavior) ---
	function attachAwardItemEvents() {
	  document.querySelectorAll('.award-item').forEach(item => {
		const header = item.querySelector('.header');
		const content = item.querySelector('.content');
		const arrow = item.querySelector('.arrow-icon');
		
		header.addEventListener('click', () => {
		  if (!isMobile()) {
			document.querySelectorAll('.award-item').forEach(otherItem => {
			  if (otherItem !== item) {
				otherItem.querySelector('.content').classList.add('hidden');
				otherItem.querySelector('.arrow-icon').classList.remove('rotate-180');
			  }
			});
			content.classList.toggle('hidden');
			arrow.classList.toggle('rotate-180');
		  } else {
			openMobileModalForAward('edit', null, item);
		  }
		});
	  });
	}

	// --- Attach Save and Delete events ---
	function attachAwardSaveButtonEvents() {
	  document.querySelectorAll('.award-item .update-btn').forEach(button => {
		button.addEventListener('click', e => {
		  e.stopPropagation();
		  const parent = button.closest('.award-item');
		  const data = {
			award_id:            parent.querySelector('.award_id').value.trim(),
			awardName:           parent.querySelector('.awardName').value.trim(),
			awardingOrganization: parent.querySelector('.awardingOrganization').value.trim(),
			dateAwarded:         parent.querySelector('.dateAwarded').value.trim()
		  };
		  saveAwardRecordCommon(data, parent);
		});
	  });
	}

	function attachAwardDeleteButtonEvents() {
	  document.querySelectorAll('.award-item .delete-btn').forEach(button => {
		button.addEventListener('click', e => {
		  e.stopPropagation();
		  deleteAwardItem(button.closest('.award-item'));
		});
	  });
	}

	// --- Delete an Award Record (Desktop) ---
	function deleteAwardItem(item) {
	  //if (!confirm("Are you sure you want to delete this record?")) return;
	  
	  const awardId = item.querySelector('.award_id').value.trim();
	  if (!awardId) {
		item.remove();
	   // alert("Item removed from the form (not saved in the database).");
		return;
	  }
	  
	  const payload = new URLSearchParams();
	  payload.append('award_id', awardId);
	  payload.append('template_id', document.getElementById('templateId').value);
	  
	  fetch('<?= base_url("website/services/ResumeBuilder/delete_award") ?>', {
		method: 'POST',
		headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
		body: payload.toString()
	  })
	  .then(res => res.json())
	  .then(result => {
		if (result.success) {
		  item.remove();
		  updatePreview();
		  //alert("Record removed successfully.");
		} else {
		  alert("Error deleting record: " + result.message);
		}
	  })
	  .catch(err => {
		alert("Network error while deleting record.");
		console.error("Error deleting award record:", err);
	  });
	}

	// --- Save/Update Award Record ---
	async function saveAwardRecordCommon(dataObj, item = null) {
	  const payload = new URLSearchParams();
	  payload.append('template_id', document.getElementById('templateId').value);
	  payload.append('award_id', dataObj.award_id);
	  payload.append('awardName', dataObj.awardName);
	  payload.append('awardingOrganization', dataObj.awardingOrganization);
	  payload.append('dateAwarded', dataObj.dateAwarded);
	  
	  try {
		const response = await fetch('<?= base_url("website/services/ResumeBuilder/save_or_update_award") ?>', {
		  method: 'POST',
		  headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
		  body: payload.toString()
		});
		const result = await response.json();
			if (result.success) {
				if (!dataObj.award_id && item) {
					item.remove(); // Remove unsaved temporary block
				}

				loadAwardData();         // Reload fresh awards
				attachAwardItemEvents(); // Re-attach events
				updatePreview();         // Refresh preview

				if (item && !dataObj.award_id && result.data?.id) {
					item.setAttribute('data-id', result.data.id);
					item.querySelector('.award_id').value = result.data.id;
				}
			}
			else {
		  console.error("Error saving award record:", result.message);
		}
	  } catch (error) {
		console.error("Network error while saving award record:", error);
	  }
	}

	// --- Open the Mobile Modal for Award ---
	function openMobileModalForAward(mode, triggerButton = null, currentItem = null) {
	  const modal      = document.getElementById('itemModal');
	  const modalTitle = document.getElementById('modalTitle');
	  const modalForm  = document.getElementById('modalForm');
	  
	  modal.classList.remove('hidden');
	  currentAwardMobileItem = (mode === 'edit' ? currentItem : null);
	  modalTitle.textContent = mode === 'edit' ? 'Edit Award' : 'Add Award';
	  
	  let deleteButtonHtml = '';
	  if (mode === 'edit' && currentItem?.querySelector('.award_id').value) {
		deleteButtonHtml = `<button type="button" onclick="deleteMobileAwardRecord()" class="flex-1 px-4 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700">Delete</button>`;
	  }
	  
	  modalForm.innerHTML = `
		<input type="hidden" id="award_id" value="${currentItem?.querySelector('.award_id')?.value || ''}">
		<input type="hidden" id="form_source" value="modal_form">
		<div>
		  <label class="block text-sm font-medium mb-2">Award Name</label>
		  <input type="text" id="awardName" class="w-full px-4 py-3 border rounded-lg" required>
		</div>
		<div>
		  <label class="block text-sm font-medium mb-2">Awarding Organization</label>
		  <input type="text" id="awardingOrganization" class="w-full px-4 py-3 border rounded-lg" required>
		</div>
		<div>
		  <label class="block text-sm font-medium mb-2">Date Awarded</label>
		  <input type="month" id="dateAwarded" class="w-full px-4 py-3 border rounded-lg" required>
		</div>
		<div class="flex gap-3 mt-6">
		  <button type="button" onclick="closeMobileModal()" class="flex-1 px-4 py-3 border border-gray-300 rounded-lg hover:bg-gray-50">Cancel</button>
		  ${deleteButtonHtml}
		  <button type="submit" class="flex-1 px-4 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Save</button>
		</div>
	  `;
	  
	  if (mode === 'edit' && currentItem) {
		document.getElementById('awardName').value             = currentItem.querySelector('.awardName').value || currentItem.querySelector('h3').textContent.trim();
		document.getElementById('awardingOrganization').value  = currentItem.querySelector('.awardingOrganization').value || '';
		document.getElementById('dateAwarded').value            = currentItem.querySelector('.dateAwarded').value || '';
	  }
	}

	// --- Delete Award Record from Mobile Modal ---
	window.deleteMobileAwardRecord = function() {
	  //if (!confirm("Are you sure you want to delete this record?")) return;
	  const awardId = document.getElementById('award_id').value.trim();
	  if (!awardId) {
		closeMobileModal();
		//alert("This record has not been saved in the database.");
		return;
	  }
	  const payload = new URLSearchParams();
	  payload.append('award_id', awardId);
	  payload.append('template_id', document.getElementById('templateId').value);
	  
	  fetch('<?= base_url("website/services/ResumeBuilder/delete_award") ?>', {
		method: 'POST',
		headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
		body: payload.toString()
	  })
	  .then(res => res.json())
	  .then(result => {
		if (result.success) {
		  currentAwardMobileItem?.remove();
		  updatePreview();
		  closeMobileModal();
		  //alert("Record deleted successfully.");
		} else {
		  alert("Error deleting record: " + result.message);
		}
	  })
	  .catch(err => {
		alert("Network error while deleting record.");
		console.error(err);
	  });
	}

	// --- Handle modal form submission (for award) ---
	document.getElementById('modalForm').addEventListener('submit', event => {
	  event.preventDefault();
	  if (document.getElementById('awardName')) {
		const data = {
		  award_id:            document.getElementById('award_id').value.trim(),
		  awardName:           document.getElementById('awardName').value.trim(),
		  awardingOrganization: document.getElementById('awardingOrganization').value.trim(),
		  dateAwarded:         document.getElementById('dateAwarded').value.trim()
		};
		saveAwardRecordCommon(data);
		closeMobileModal();
	  }
	});

	// --- Add Award button handler (Desktop & Mobile) ---
	document.getElementById('addAward').addEventListener('click', () => {
	  if (isMobile()) {
		openMobileModalForAward('add');
	  } else {
		document.querySelectorAll('.award-item').forEach(item => {
		  item.querySelector('.content').classList.add('hidden');
		  item.querySelector('.arrow-icon').classList.remove('rotate-180');
		});
		const newAward = createDesktopAwardItem();
		newAward.querySelector('.content').classList.remove('hidden');
		newAward.querySelector('.arrow-icon').classList.add('rotate-180');
		document.getElementById('awardContainer').appendChild(newAward);
		attachAwardDeleteButtonEvents();
	  }
	});

	// Finally, load the award data when the page loads.
	loadAwardData();

	// Global variable to track the hobby item being edited in mobile mode.
	let currentHobbyMobileItem = null;

	// --- Load Hobby Data ---
	async function loadHobbyData() {
	  try {
		const formData = new FormData();
		const templateId = document.getElementById('templateId').value;
		formData.append('template_id', templateId);
		
		const response = await fetch('<?= base_url("website/services/ResumeBuilder/fetch_hobbies"); ?>', {
		  method: 'POST',
		  body: formData
		});
		
		if (!response.ok) throw new Error(`Network error: ${response.statusText}`);
		
		const data = await response.json();
		if (data.success) {
		  const container = document.getElementById('showHobbyContainer');
		  container.innerHTML = ''; // Clear current content
		  
		  data.hobbies.forEach(hobby => {
			const {
			  id = '',
			  hobbyName = '',
			  description = ''
			} = hobby;
			
			const itemHtml = `
			  <div class="hobby-item border-l-4 border-blue-600 pl-6 py-4 rounded-lg transition-all duration-300 hover:border-blue-700 hover:bg-gray-50"
				   data-id="${id}">
				<div class="header cursor-pointer flex justify-between items-center">
				  <div>
					<h3 class="text-lg font-semibold text-gray-900">${hobbyName}</h3>
					<p class="text-sm text-gray-500 mt-1">${description}</p>
				  </div>
				  <div class="arrow-icon transform transition-transform duration-300">
					<svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
					  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
							d="M19 9l-7 7-7-7"/>
					</svg>
				  </div>
				</div>
				<div class="content hidden mt-4 space-y-4">
				  <input type="hidden" class="hobby_id" value="${id}">
				 
				  <div>
					<label class="block text-sm font-medium text-gray-700 mb-2">Hobby Name</label>
					<input type="text" class="hobbyName w-full px-3 py-2 border rounded-lg" value="${hobbyName}" placeholder="Hobby Name">
				  </div>
				  <div>
					<label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
					<textarea class="description w-full px-3 py-2 border rounded-lg" placeholder="Description">${description}</textarea>
				  </div>
				  <div class="actions flex gap-3 mt-4">
					<button type="button" class="update-btn bg-green-600 text-white px-3 py-2 rounded hover:bg-green-700">Save</button>
					<button type="button" class="delete-btn bg-red-600 text-white px-3 py-2 rounded hover:bg-red-700">Delete</button>
				  </div>
				</div>
			  </div>
			`;
			container.insertAdjacentHTML('beforeend', itemHtml);
		  });
		  
		  attachHobbyItemEvents();
		  attachHobbySaveButtonEvents();
		  attachHobbyDeleteButtonEvents();
		} else {
		  console.error("Backend error:", data.message);
		  //alert("No hobby data found. Please try again.");
		}
	  } catch (error) {
		console.error("Error fetching hobby data:", error);
		alert("Error loading hobby data. Please try again later.");
	  }
	}
	
	// --- Create a new hobby item (Desktop mode) ---
	function createDesktopHobbyItem() {
	  const newItem = document.createElement('div');
	  newItem.className = 'hobby-item border-l-4 border-blue-600 pl-4 rounded-lg mt-4';
	  newItem.innerHTML = `
		<div class="header cursor-pointer flex justify-between items-center">
		  <div class="flex-1">
			<h3 class="font-semibold text-gray-800">New Hobby</h3>
			<p class="text-sm text-gray-600">Description</p>
		  </div>
		  <div class="flex items-center space-x-4">
			<button class="edit-btn text-blue-600 hover:text-blue-800 transition-colors">
			  <svg class="w-5 h-5"><use xlink:href="#pencil" /></svg>
			</button>
			<span class="arrow-icon transform transition-transform duration-300">
			  <svg class="w-6 h-6 text-gray-600"><use xlink:href="#chevron-down" /></svg>
			</span>
		  </div>
		</div>
		<div class="content hidden mt-4 space-y-4">
		  <input type="hidden" class="hobby_id" value="">
		  <div>
			<label class="block text-sm font-medium text-gray-700 mb-2">Hobby Name</label>
			<input type="text" class="hobbyName w-full px-3 py-2 border rounded-lg" placeholder="Hobby Name">
		  </div>
		  <div>
			<label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
			<textarea class="description w-full px-3 py-2 border rounded-lg" placeholder="Description"></textarea>
		  </div>
		  <div class="actions flex gap-3 mt-4">
			<button type="button" class="save-btn bg-green-600 text-white px-3 py-2 rounded hover:bg-green-700">Save</button>
			<button type="button" class="delete-btn bg-red-600 text-white px-3 py-2 rounded hover:bg-red-700">Delete</button>
		  </div>
		</div>
	  `;

	  newItem.querySelector('.header').addEventListener('click', () => {
		if (!isMobile()) {
		  document.querySelectorAll('.hobby-item').forEach(other => {
			if (other !== newItem) {
			  other.querySelector('.content').classList.add('hidden');
			  other.querySelector('.arrow-icon').classList.remove('rotate-180');
			}
		  });
		  const content = newItem.querySelector('.content');
		  const arrow = newItem.querySelector('.arrow-icon');
		  content.classList.toggle('hidden');
		  arrow.classList.toggle('rotate-180');
		} else {
		  openMobileModalForHobby('edit', null, newItem);
		}
	  });

	  newItem.querySelector('.save-btn').addEventListener('click', () => {
		const data = {
		  hobby_id:   newItem.querySelector('.hobby_id').value.trim(),
		  hobbyName:  newItem.querySelector('.hobbyName').value.trim(),
		  description: newItem.querySelector('.description').value.trim()
		};
		saveHobbyRecordCommon(data, newItem);
	  });

	  newItem.querySelector('.delete-btn').addEventListener('click', e => {
		e.stopPropagation();
		deleteHobbyItem(newItem);
	  });

	  return newItem;
	}
	
	// --- Attach click events (Desktop behavior) ---
	function attachHobbyItemEvents() {
	  document.querySelectorAll('.hobby-item').forEach(item => {
		const header = item.querySelector('.header');
		const content = item.querySelector('.content');
		const arrow = item.querySelector('.arrow-icon');
		
		header.addEventListener('click', () => {
		  if (!isMobile()) {
			document.querySelectorAll('.hobby-item').forEach(otherItem => {
			  if (otherItem !== item) {
				otherItem.querySelector('.content').classList.add('hidden');
				otherItem.querySelector('.arrow-icon').classList.remove('rotate-180');
			  }
			});
			content.classList.toggle('hidden');
			arrow.classList.toggle('rotate-180');
		  } else {
			openMobileModalForHobby('edit', null, item);
		  }
		});
	  });
	}

	// --- Attach Save and Delete events ---
	function attachHobbySaveButtonEvents() {
	  document.querySelectorAll('.hobby-item .update-btn').forEach(button => {
		button.addEventListener('click', e => {
		  e.stopPropagation();
		  const parent = button.closest('.hobby-item');
		  const data = {
			hobby_id:   parent.querySelector('.hobby_id').value.trim(),
			hobbyName:  parent.querySelector('.hobbyName').value.trim(),
			description: parent.querySelector('.description').value.trim()
		  };
		  saveHobbyRecordCommon(data, parent);
		});
	  });
	}

	function attachHobbyDeleteButtonEvents() {
	  document.querySelectorAll('.hobby-item .delete-btn').forEach(button => {
		button.addEventListener('click', e => {
		  e.stopPropagation();
		  deleteHobbyItem(button.closest('.hobby-item'));
		});
	  });
	}
	// --- Delete a Hobby Record (Desktop) ---
	function deleteHobbyItem(item) {
	  //if (!confirm("Are you sure you want to delete this record?")) return;
	  
	  const hobbyId = item.querySelector('.hobby_id').value.trim();
	  if (!hobbyId) {
		item.remove();
		//alert("Item removed from the form (not saved in the database).");
		return;
	  }
	  
	  const payload = new URLSearchParams();
	  payload.append('hobby_id', hobbyId);
	  payload.append('template_id', document.getElementById('templateId').value);
	  
	  fetch('<?= base_url("website/services/ResumeBuilder/delete_hobby") ?>', {
		method: 'POST',
		headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
		body: payload.toString()
	  })
	  .then(res => res.json())
	  .then(result => {
		if (result.success) {
		  item.remove();
		  updatePreview();
		  //alert("Record removed successfully.");
		} else {
		  alert("Error deleting record: " + result.message);
		}
	  })
	  .catch(err => {
		alert("Network error while deleting record.");
		console.error("Error deleting hobby record:", err);
	  });
	}

	// --- Save/Update Hobby Record ---
	async function saveHobbyRecordCommon(dataObj, item = null) {
	  const payload = new URLSearchParams();
	  payload.append('template_id', document.getElementById('templateId').value);
	  payload.append('hobby_id', dataObj.hobby_id);
	  payload.append('hobbyName', dataObj.hobbyName);
	  payload.append('description', dataObj.description);
	  
	  try {
		const response = await fetch('<?= base_url("website/services/ResumeBuilder/save_or_update_hobby") ?>', {
		  method: 'POST',
		  headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
		  body: payload.toString()
		});
		const result = await response.json();
		if (result.success) {
			  if (!dataObj.hobby_id && item) {
				item.remove(); // Remove unsaved new entry block
			  }

			  loadHobbyData();          // Reload all hobby records
			  attachHobbyItemEvents();  // Reattach click events
			  updatePreview();          // Update resume preview

			  if (item && !dataObj.hobby_id && result.data?.id) {
				item.setAttribute('data-id', result.data.id);
				item.querySelector('.hobby_id').value = result.data.id;
			  }
			} else {
		  console.error("Error saving hobby record:", result.message);
		}
	  } catch (error) {
		console.error("Network error while saving hobby record:", error);
	  }
	}

	// --- Open the Mobile Modal for Hobby ---
	function openMobileModalForHobby(mode, triggerButton = null, currentItem = null) {
	  const modal      = document.getElementById('itemModal');
	  const modalTitle = document.getElementById('modalTitle');
	  const modalForm  = document.getElementById('modalForm');
	  
	  modal.classList.remove('hidden');
	  currentHobbyMobileItem = (mode === 'edit' ? currentItem : null);
	  modalTitle.textContent = mode === 'edit' ? 'Edit Hobby' : 'Add Hobby';
	  
	  let deleteButtonHtml = '';
	  if (mode === 'edit' && currentItem?.querySelector('.hobby_id').value) {
		deleteButtonHtml = `<button type="button" onclick="deleteMobileHobbyRecord()" class="flex-1 px-4 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700">Delete</button>`;
	  }
	  
	  modalForm.innerHTML = `
		<input type="hidden" id="hobby_id" value="${currentItem?.querySelector('.hobby_id')?.value || ''}">
		<input type="hidden" id="form_source" value="modal_form">
		<div>
		  <label class="block text-sm font-medium mb-2">Hobby Name</label>
		  <input type="text" id="hobbyName" class="w-full px-4 py-3 border rounded-lg" required>
		</div>
		<div>
		  <label class="block text-sm font-medium mb-2">Description</label>
		  <textarea id="description" class="w-full px-4 py-3 border rounded-lg" required></textarea>
		</div>
		<div class="flex gap-3 mt-6">
		  <button type="button" onclick="closeMobileModal()" class="flex-1 px-4 py-3 border border-gray-300 rounded-lg hover:bg-gray-50">Cancel</button>
		  ${deleteButtonHtml}
		  <button type="submit" class="flex-1 px-4 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Save</button>
		</div>
	  `;
	  
	  if (mode === 'edit' && currentItem) {
		document.getElementById('hobbyName').value = currentItem.querySelector('.hobbyName').value || currentItem.querySelector('h3').textContent.trim();
		document.getElementById('description').value = currentItem.querySelector('.description').value || '';
	  }
	}

	// --- Delete Hobby Record from Mobile Modal ---
	window.deleteMobileHobbyRecord = function() { 
	  //if (!confirm("Are you sure you want to delete this record?")) return;
	  const hobbyId = document.getElementById('hobby_id').value.trim();
	  if (!hobbyId) {
		closeMobileModal();
		//alert("This record has not been saved in the database.");
		return;
	  }
	  const payload = new URLSearchParams();
	  payload.append('hobby_id', hobbyId);
	  payload.append('template_id', document.getElementById('templateId').value);
	  
	  fetch('<?= base_url("website/services/ResumeBuilder/delete_hobby") ?>', {
		method: 'POST',
		headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
		body: payload.toString()
	  })
	  .then(res => res.json())
	  .then(result => {
		if (result.success) {
		  currentHobbyMobileItem?.remove();
		  updatePreview();
		  closeMobileModal();
		  //alert("Record deleted successfully.");
		} else {
		  alert("Error deleting record: " + result.message);
		}
	  })
	  .catch(err => {
		alert("Network error while deleting record.");
		console.error(err);
	  });
	}

	// --- Handle modal form submission (for hobby) ---
	document.getElementById('modalForm').addEventListener('submit', event => {
	  event.preventDefault();
	  if (document.getElementById('hobbyName')) {
		const data = {
		  hobby_id:   document.getElementById('hobby_id').value.trim(),
		  hobbyName:  document.getElementById('hobbyName').value.trim(),
		  description: document.getElementById('description').value.trim()
		};
		saveHobbyRecordCommon(data);
		closeMobileModal();
	  }
	});

	// --- Add Hobby button handler (Desktop & Mobile) ---
	document.getElementById('addHobby').addEventListener('click', () => {
	  if (isMobile()) {
		openMobileModalForHobby('add');
	  } else {
		document.querySelectorAll('.hobby-item').forEach(item => {
		  item.querySelector('.content').classList.add('hidden');
		  item.querySelector('.arrow-icon').classList.remove('rotate-180');
		});
		const newHobby = createDesktopHobbyItem();
		newHobby.querySelector('.content').classList.remove('hidden');
		newHobby.querySelector('.arrow-icon').classList.add('rotate-180');
		document.getElementById('hobbyContainer').appendChild(newHobby);
		attachHobbyDeleteButtonEvents();
	  }
	});

	// Finally, load the hobby data when the page loads.
	loadHobbyData();


	// Global variable to track the course item being edited in mobile mode.
	let currentCourseMobileItem = null;

	// --- Load Course Data ---
	async function loadCourseData() {
	  try {
		const formData = new FormData();
		const templateId = document.getElementById('templateId').value;
		formData.append('template_id', templateId);
		
		const response = await fetch('<?= base_url("website/services/ResumeBuilder/fetch_courses"); ?>', {
		  method: 'POST',
		  body: formData
		});
		
		if (!response.ok) throw new Error(`Network error: ${response.statusText}`);
		
		const data = await response.json();
		if (data.success) {
		  const container = document.getElementById('showCourseContainer');
		  container.innerHTML = ''; // Clear current content
		  
		  data.courses.forEach(course => {
			const {
			  id = '',
			  courseName = '',
			  instituteName = '',
			  dateCompleted = ''
			} = course;
			
			const itemHtml = `
			  <div class="course-item border-l-4 border-blue-600 pl-6 py-4 rounded-lg transition-all duration-300 hover:border-blue-700 hover:bg-gray-50"
				   data-id="${id}">

				<div class="header cursor-pointer flex justify-between items-center">
				  <div>
					<h3 class="text-lg font-semibold text-gray-900">${courseName}</h3>
					<p class="text-sm text-gray-500 mt-1">${instituteName}</p>
				  </div>
				  <div class="arrow-icon transform transition-transform duration-300">
					<svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
					  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 9l-7 7-7-7"/>
					</svg>
				  </div>
				</div>

				<div class="content hidden mt-4 space-y-4">
				  <input type="hidden" class="course_id" value="${id}">

				  <div class="grid grid-cols-2 gap-4">
					<div>
					  <label class="block text-sm font-medium text-gray-700 mb-2">Course Name</label>
					  <input type="text" class="courseName w-full px-3 py-2 border rounded-lg" value="${courseName}" placeholder="Course Name">
					</div>
					<div>
					  <label class="block text-sm font-medium text-gray-700 mb-2">Institute Name</label>
					  <input type="text" class="instituteName w-full px-3 py-2 border rounded-lg" value="${instituteName}" placeholder="Institute Name">
					</div>
					<div>
					  <label class="block text-sm font-medium text-gray-700 mb-2">Date Completed</label>
					  <input type="month" class="dateCompleted w-full px-3 py-2 border rounded-lg" value="${dateCompleted}">
					</div>
				  </div>

				  <div class="actions flex gap-3 mt-4">
					<button type="button" class="update-btn bg-green-600 text-white px-3 py-2 rounded hover:bg-green-700">Save</button>
					<button type="button" class="delete-btn bg-red-600 text-white px-3 py-2 rounded hover:bg-red-700">Delete</button>
				  </div>
				</div>
			  </div>
			`;

			container.insertAdjacentHTML('beforeend', itemHtml);
		  });
		  
		  attachCourseItemEvents();
		  attachCourseSaveButtonEvents();
		  attachCourseDeleteButtonEvents();
		} else {
		  console.error("Backend error:", data.message);
		  // Optionally, show a message to the user.
		}
	  } catch (error) {
		console.error("Error fetching course data:", error);
		alert("Error loading course data. Please try again later.");
	  }
	}
	
	// --- Create a new course item (Desktop mode) ---
	function createDesktopCourseItem() {
	  const newItem = document.createElement('div');
	  newItem.className = 'course-item border-l-4 border-blue-600 pl-4 rounded-lg mt-4';
	  newItem.innerHTML = `
		  <div class="header cursor-pointer flex justify-between items-center">
			<div class="flex-1">
			  <h3 class="font-semibold text-gray-800">New Course</h3>
			  <p class="text-sm text-gray-600">Institute Name</p>
			</div>
			<div class="flex items-center space-x-4">
			  <button class="edit-btn text-blue-600 hover:text-blue-800 transition-colors">
				<svg class="w-5 h-5"><use xlink:href="#pencil" /></svg>
			  </button>
			  <span class="arrow-icon transform transition-transform duration-300">
				<svg class="w-6 h-6 text-gray-600"><use xlink:href="#chevron-down" /></svg>
			  </span>
			</div>
		  </div>
		  <div class="content hidden mt-4 space-y-4">
			<input type="hidden" class="course_id" value="">

			<div class="grid grid-cols-2 gap-4">
			  <div>
				<label class="block text-sm font-medium text-gray-700 mb-2">Course Name</label>
				<input type="text" class="courseName w-full px-3 py-2 border rounded-lg" placeholder="Course Name">
			  </div>
			  <div>
				<label class="block text-sm font-medium text-gray-700 mb-2">Institute Name</label>
				<input type="text" class="instituteName w-full px-3 py-2 border rounded-lg" placeholder="Institute Name">
			  </div>
			  <div>
				<label class="block text-sm font-medium text-gray-700 mb-2">Date Completed</label>
				<input type="month" class="dateCompleted w-full px-3 py-2 border rounded-lg">
			  </div>
			</div>

			<div class="actions flex gap-3 mt-4">
			  <button type="button" class="save-btn bg-green-600 text-white px-3 py-2 rounded hover:bg-green-700">Save</button>
			  <button type="button" class="delete-btn bg-red-600 text-white px-3 py-2 rounded hover:bg-red-700">Delete</button>
			</div>
		  </div>
		`;
		
	   newItem.querySelector('.header').addEventListener('click', () => {
		if (!isMobile()) {
		  document.querySelectorAll('.course-item').forEach(other => {
			if (other !== newItem) {
			  other.querySelector('.content').classList.add('hidden');
			  other.querySelector('.arrow-icon').classList.remove('rotate-180');
			}
		  });
		  const content = newItem.querySelector('.content');
		  const arrow = newItem.querySelector('.arrow-icon');
		  content.classList.toggle('hidden');
		  arrow.classList.toggle('rotate-180');
		} else {
		  openMobileModalForCourse('edit', null, newItem);
		}
	  });

	  newItem.querySelector('.save-btn').addEventListener('click', () => {
		const data = {
		  course_id:       newItem.querySelector('.course_id').value.trim(),
		  courseName:      newItem.querySelector('.courseName').value.trim(),
		  instituteName:   newItem.querySelector('.instituteName').value.trim(),
		  dateCompleted:   newItem.querySelector('.dateCompleted').value.trim()
		};
		saveCourseRecordCommon(data, newItem);
	  });

	  newItem.querySelector('.delete-btn').addEventListener('click', e => {
		e.stopPropagation();
		deleteCourseItem(newItem);
	  });

	  return newItem;
	}
	
	// --- Attach click events (Desktop behavior) ---
	function attachCourseItemEvents() {
	  document.querySelectorAll('.course-item').forEach(item => {
		const header = item.querySelector('.header');
		const content = item.querySelector('.content');
		const arrow = item.querySelector('.arrow-icon');
		
		header.addEventListener('click', () => {
		  if (!isMobile()) {
			document.querySelectorAll('.course-item').forEach(otherItem => {
			  if (otherItem !== item) {
				otherItem.querySelector('.content').classList.add('hidden');
				otherItem.querySelector('.arrow-icon').classList.remove('rotate-180');
			  }
			});
			content.classList.toggle('hidden');
			arrow.classList.toggle('rotate-180');
		  } else {
			openMobileModalForCourse('edit', null, item);
		  }
		});
	  });
	}

	// --- Attach Save and Delete events ---
	function attachCourseSaveButtonEvents() {
	  document.querySelectorAll('.course-item .update-btn').forEach(button => {
		button.addEventListener('click', e => {
		  e.stopPropagation();
		  const parent = button.closest('.course-item');
		  const data = {
			course_id:       parent.querySelector('.course_id').value.trim(),
			courseName:      parent.querySelector('.courseName').value.trim(),
			instituteName:   parent.querySelector('.instituteName').value.trim(),
			dateCompleted:   parent.querySelector('.dateCompleted').value.trim()
		  };
		  saveCourseRecordCommon(data, parent);
		});
	  });
	}

	function attachCourseDeleteButtonEvents() {
	  document.querySelectorAll('.course-item .delete-btn').forEach(button => {
		button.addEventListener('click', e => {
		  e.stopPropagation();
		  deleteCourseItem(button.closest('.course-item'));
		});
	  });
	}
	

	// --- Delete a Course Record (Desktop) ---
	function deleteCourseItem(item) {
	 
	  const courseId = item.querySelector('.course_id').value.trim();
	  if (!courseId) {
		item.remove();
		//alert("Course removed from the form (not saved in the database).");
		return;
	  }
	  
	  const payload = new URLSearchParams();
	  payload.append('course_id', courseId);
	  payload.append('template_id', document.getElementById('templateId').value);
	  
	  fetch('<?= base_url("website/services/ResumeBuilder/delete_course"); ?>', {
		method: 'POST',
		headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
		body: payload.toString()
	  })
	  .then(res => res.json())
	  .then(result => {
		if (result.success) {
		  item.remove();
		  updatePreview();
		  alert("Course removed successfully.");
		} else {
		  alert("Error deleting course: " + result.message);
		}
	  })
	  .catch(err => {
		alert("Network error while deleting course.");
		console.error("Error deleting course:", err);
	  });
	}

	// --- Save/Update Course Record ---
	async function saveCourseRecordCommon(dataObj, item = null) {
	  const payload = new URLSearchParams();
	  payload.append('template_id', document.getElementById('templateId').value);
	  payload.append('course_id', dataObj.course_id);
	  payload.append('courseName', dataObj.courseName);
	  payload.append('instituteName', dataObj.instituteName);
	  payload.append('dateCompleted', dataObj.dateCompleted);
	  
	  try {
		const response = await fetch('<?= base_url("website/services/ResumeBuilder/save_or_update_course"); ?>', {
		  method: 'POST',
		  headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
		  body: payload.toString()
		});
		const result = await response.json();
			if (result.success) {
				if (!dataObj.course_id && item) {
					item.remove(); // Remove unsaved temporary DOM block
				}

				loadCourseData();         // Reload course list
				attachCourseItemEvents(); // Re-attach any necessary events
				updatePreview();          // Update resume preview

				if (item && !dataObj.course_id && result.data?.id) {
					item.setAttribute('data-id', result.data.id);
					item.querySelector('.course_id').value = result.data.id;
				}
			}
			else {
		  console.error("Error saving course:", result.message);
		}
	  } catch (error) {
		console.error("Network error while saving course:", error);
	  }
	}

	// --- Open the Mobile Modal for Course ---
	function openMobileModalForCourse(mode, triggerButton = null, currentItem = null) {
	  const modal      = document.getElementById('itemModal');
	  const modalTitle = document.getElementById('modalTitle');
	  const modalForm  = document.getElementById('modalForm');
	  
	  modal.classList.remove('hidden');
	  currentCourseMobileItem = (mode === 'edit' ? currentItem : null);
	  modalTitle.textContent = mode === 'edit' ? 'Edit Course' : 'Add Course';
	  
	  let deleteButtonHtml = '';
	  if (mode === 'edit' && currentItem?.querySelector('.course_id').value) {
		deleteButtonHtml = `<button type="button" onclick="deleteMobileCourseRecord()" class="flex-1 px-4 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700">Delete</button>`;
	  }
	  
	  modalForm.innerHTML = `
		<input type="hidden" id="course_id" value="${currentItem?.querySelector('.course_id')?.value || ''}">
		<input type="hidden" id="form_source" value="modal_form">
		<div>
		  <label class="block text-sm font-medium mb-2">Course Name</label>
		  <input type="text" id="courseName" class="w-full px-4 py-3 border rounded-lg" required>
		</div>
		<div>
		  <label class="block text-sm font-medium mb-2">Institute Name</label>
		  <input type="text" id="instituteName" class="w-full px-4 py-3 border rounded-lg" required>
		</div>
		<div>
		  <label class="block text-sm font-medium mb-2">Date Completed</label>
		  <input type="month" id="dateCompleted" class="w-full px-4 py-3 border rounded-lg" required>
		</div>
		<div class="flex gap-3 mt-6">
		  <button type="button" onclick="closeMobileModal()" class="flex-1 px-4 py-3 border border-gray-300 rounded-lg hover:bg-gray-50">Cancel</button>
		  ${deleteButtonHtml}
		  <button type="submit" class="flex-1 px-4 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Save</button>
		</div>
	  `;
	  
	  if (mode === 'edit' && currentItem) {
		document.getElementById('courseName').value      = currentItem.querySelector('.courseName').value || currentItem.querySelector('h3').textContent.trim();
		document.getElementById('instituteName').value   = currentItem.querySelector('.instituteName').value || '';
		document.getElementById('dateCompleted').value   = currentItem.querySelector('.dateCompleted').value || '';
	  }
	}

	// --- Delete Course Record from Mobile Modal ---
	function deleteMobileCourseRecord() {
	  //if (!confirm("Are you sure you want to delete this course?")) return;
	  const courseId = document.getElementById('course_id').value.trim();
	  if (!courseId) {
		closeMobileModal();
		alert("This course has not been saved in the database.");
		return;
	  }
	  const payload = new URLSearchParams();
	  payload.append('course_id', courseId);
	  payload.append('template_id', document.getElementById('templateId').value);
	  
	  fetch('<?= base_url("website/services/ResumeBuilder/delete_course"); ?>', {
		method: 'POST',
		headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
		body: payload.toString()
	  })
	  .then(res => res.json())
	  .then(result => {
		if (result.success) {
		  currentCourseMobileItem?.remove();
		  closeMobileModal();
		  updatePreview();
		  alert("Course deleted successfully.");
		} else {
		  alert("Error deleting course: " + result.message);
		}
	  })
	  .catch(err => {
		alert("Network error while deleting course.");
		console.error(err);
	  });
	}

	// --- Handle modal form submission (for course) ---
	document.getElementById('modalForm').addEventListener('submit', event => {
	  event.preventDefault();
	  if (document.getElementById('courseName')) {
		const data = {
		  course_id:       document.getElementById('course_id').value.trim(),
		  courseName:      document.getElementById('courseName').value.trim(),
		  instituteName:   document.getElementById('instituteName').value.trim(),
		  dateCompleted:   document.getElementById('dateCompleted').value.trim()
		};
		saveCourseRecordCommon(data);
		closeMobileModal();
	  }
	});

	// --- Add Course button handler (Desktop & Mobile) ---
	document.getElementById('addCourse').addEventListener('click', () => {
	  if (isMobile()) {
		openMobileModalForCourse('add');
	  } else {
		document.querySelectorAll('.course-item').forEach(item => {
		  item.querySelector('.content').classList.add('hidden');
		  item.querySelector('.arrow-icon').classList.remove('rotate-180');
		});
		const newCourse = createDesktopCourseItem();
		newCourse.querySelector('.content').classList.remove('hidden');
		newCourse.querySelector('.arrow-icon').classList.add('rotate-180');
		document.getElementById('courseContainer').appendChild(newCourse);
		attachCourseDeleteButtonEvents();
	  }
	});

	// Finally, load the course data when the page loads.
	loadCourseData();


	// Global variable to track the activity item being edited in mobile mode.
	let currentActivityMobileItem = null;

	// --- Load Extra Curricular Activities Data ---
	async function loadActivityData() {
	  try {
		const formData = new FormData();
		const templateId = document.getElementById('templateId').value;
		formData.append('template_id', templateId);
		
		const response = await fetch('<?= base_url("website/services/ResumeBuilder/fetch_extraCurricularActivities"); ?>', {
		  method: 'POST',
		  body: formData
		});
		
		if (!response.ok) throw new Error(`Network error: ${response.statusText}`);
		
		const data = await response.json();
		if (data.success) {
		  const container = document.getElementById('showActivityContainer');
		  container.innerHTML = ''; // Clear current content
		  
		  data.activities.forEach(activity => {
			const { id = '', activityName = '', position = '', description = '' } = activity;
			
			const itemHtml = `
			  <div class="activity-item border-l-4 border-blue-600 pl-6 py-4 rounded-lg transition-all duration-300 hover:border-blue-700 hover:bg-gray-50"
				   data-id="${id}">
				
				<div class="header cursor-pointer flex justify-between items-center">
				  <div>
					<h3 class="text-lg font-semibold text-gray-900">${activityName}</h3>
					<p class="text-sm text-gray-500 mt-1">${position}</p>
				  </div>
				  <div class="arrow-icon transform transition-transform duration-300">
					<svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
					  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 9l-7 7-7-7"/>
					</svg>
				  </div>
				</div>

				<div class="content hidden mt-4 space-y-4">
				  <input type="hidden" class="activity_id" value="${id}">

				  <div class="grid grid-cols-2 gap-4">
					<div>
					  <label class="block text-sm font-medium text-gray-700 mb-2">Activity Name</label>
					  <input type="text" class="activityName w-full px-3 py-2 border rounded-lg" value="${activityName}" placeholder="Activity Name">
					</div>
					<div>
					  <label class="block text-sm font-medium text-gray-700 mb-2">Position</label>
					  <input type="text" class="position w-full px-3 py-2 border rounded-lg" value="${position}" placeholder="Position">
					</div>
				  </div>

				  <div>
					<label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
					<textarea class="description w-full px-3 py-2 border rounded-lg" placeholder="Description">${description}</textarea>
				  </div>

				  <div class="actions flex gap-3 mt-4">
					<button type="button" class="update-btn bg-green-600 text-white px-3 py-2 rounded hover:bg-green-700">Save</button>
					<button type="button" class="delete-btn bg-red-600 text-white px-3 py-2 rounded hover:bg-red-700">Delete</button>
				  </div>
				</div>
			  </div>
			`;

			container.insertAdjacentHTML('beforeend', itemHtml);
		  });
		  
		  attachActivityItemEvents();
		  attachActivitySaveButtonEvents();
		  attachActivityDeleteButtonEvents();
		} else {
		  console.error("Backend error:", data.message);
		}
	  } catch (error) {
		console.error("Error fetching activities:", error);
		alert("Error loading activities. Please try again later.");
	  }
	}
	
	// --- Create a new activity item (Desktop mode) ---
	function createDesktopActivityItem() {
	  const newItem = document.createElement('div');
	  newItem.className = 'activity-item border-l-4 border-blue-600 pl-4 rounded-lg mt-4';
	  newItem.innerHTML = `
		  <div class="header cursor-pointer flex justify-between items-center">
			<div class="flex-1">
			  <h3 class="font-semibold text-gray-800">New Activity</h3>
			  <p class="text-sm text-gray-600">Position</p>
			</div>
			<div class="flex items-center space-x-4">
			  <button class="edit-btn text-blue-600 hover:text-blue-800 transition-colors">
				<svg class="w-5 h-5"><use xlink:href="#pencil" /></svg>
			  </button>
			  <span class="arrow-icon transform transition-transform duration-300">
				<svg class="w-6 h-6 text-gray-600"><use xlink:href="#chevron-down" /></svg>
			  </span>
			</div>
		  </div>
		  <div class="content hidden mt-4 space-y-4">
			<input type="hidden" class="activity_id" value="">

			<div class="grid grid-cols-2 gap-4">
			  <div>
				<label class="block text-sm font-medium text-gray-700 mb-2">Activity Name</label>
				<input type="text" class="activityName w-full px-3 py-2 border rounded-lg" placeholder="Activity Name">
			  </div>
			  <div>
				<label class="block text-sm font-medium text-gray-700 mb-2">Position</label>
				<input type="text" class="position w-full px-3 py-2 border rounded-lg" placeholder="Position">
			  </div>
			</div>

			<div>
			  <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
			  <textarea class="description w-full px-3 py-2 border rounded-lg" placeholder="Description"></textarea>
			</div>

			<div class="actions flex gap-3 mt-4">
			  <button type="button" class="save-btn bg-green-600 text-white px-3 py-2 rounded hover:bg-green-700">Save</button>
			  <button type="button" class="delete-btn bg-red-600 text-white px-3 py-2 rounded hover:bg-red-700">Delete</button>
			</div>
		  </div>
		`;


	  newItem.querySelector('.header').addEventListener('click', () => {
		if (!isMobile()) {
		  document.querySelectorAll('.activity-item').forEach(other => {
			if (other !== newItem) {
			  other.querySelector('.content').classList.add('hidden');
			  other.querySelector('.arrow-icon').classList.remove('rotate-180');
			}
		  });
		  const content = newItem.querySelector('.content');
		  const arrow = newItem.querySelector('.arrow-icon');
		  content.classList.toggle('hidden');
		  arrow.classList.toggle('rotate-180');
		} else {
		  openMobileModalForActivity('edit', null, newItem);
		}
	  });

	  newItem.querySelector('.save-btn').addEventListener('click', () => {
		const data = {
		  activity_id:   newItem.querySelector('.activity_id').value.trim(),
		  activityName:  newItem.querySelector('.activityName').value.trim(),
		  position:      newItem.querySelector('.position').value.trim(),
		  description:   newItem.querySelector('.description').value.trim()
		};
		saveActivityRecordCommon(data, newItem);
	  });

	  newItem.querySelector('.delete-btn').addEventListener('click', e => {
		e.stopPropagation();
		deleteActivityItem(newItem);
	  });

	  return newItem;
	}
	
	// --- Attach click events (Desktop behavior) ---
	function attachActivityItemEvents() {
	  document.querySelectorAll('.activity-item').forEach(item => {
		const header = item.querySelector('.header');
		const content = item.querySelector('.content');
		const arrow = item.querySelector('.arrow-icon');
		
		header.addEventListener('click', () => {
		  if (!isMobile()) {
			document.querySelectorAll('.activity-item').forEach(otherItem => {
			  if (otherItem !== item) {
				otherItem.querySelector('.content').classList.add('hidden');
				otherItem.querySelector('.arrow-icon').classList.remove('rotate-180');
			  }
			});
			content.classList.toggle('hidden');
			arrow.classList.toggle('rotate-180');
		  } else {
			openMobileModalForActivity('edit', null, item);
		  }
		});
	  });
	}

	// --- Attach Save and Delete events ---
	function attachActivitySaveButtonEvents() {
	  document.querySelectorAll('.activity-item .update-btn').forEach(button => {
		button.addEventListener('click', e => {
		  e.stopPropagation();
		  const parent = button.closest('.activity-item');
		  const data = {
			activity_id:   parent.querySelector('.activity_id').value.trim(),
			activityName:  parent.querySelector('.activityName').value.trim(),
			position:      parent.querySelector('.position').value.trim(),
			description:   parent.querySelector('.description').value.trim()
		  };
		  saveActivityRecordCommon(data, parent);
		});
	  });
	}

	function attachActivityDeleteButtonEvents() {
	  document.querySelectorAll('.activity-item .delete-btn').forEach(button => {
		button.addEventListener('click', e => {
		  e.stopPropagation();
		  deleteActivityItem(button.closest('.activity-item'));
		});
	  });
	}

	// --- Delete an Activity Record (Desktop) ---
	function deleteActivityItem(item) {
	  //if (!confirm("Are you sure you want to delete this activity?")) return;	  
	  const activityId = item.querySelector('.activity_id').value.trim();
	  if (!activityId) {
		item.remove();
		//alert("Activity removed from the form (not saved in the database).");
		return;
	  }
	  
	  const payload = new URLSearchParams();
	  payload.append('activity_id', activityId);
	  payload.append('template_id', document.getElementById('templateId').value);
	  
	  fetch('<?= base_url("website/services/ResumeBuilder/delete_extraCurricularActivity"); ?>', {
		method: 'POST',
		headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
		body: payload.toString()
	  })
	  .then(res => res.json())
	  .then(result => {
		if (result.success) {
		  item.remove();
		  updatePreview();
		  alert("Activity removed successfully.");
		} else {
		  alert("Error deleting activity: " + result.message);
		}
	  })
	  .catch(err => {
		alert("Network error while deleting activity.");
		console.error("Error deleting activity:", err);
	  });
	}

	// --- Save/Update Activity Record ---
	async function saveActivityRecordCommon(dataObj, item = null) {
	  const payload = new URLSearchParams();
	  payload.append('template_id', document.getElementById('templateId').value);
	  payload.append('activity_id', dataObj.activity_id);
	  payload.append('activityName', dataObj.activityName);
	  payload.append('position', dataObj.position);
	  payload.append('description', dataObj.description);
	  
	  try {
		const response = await fetch('<?= base_url("website/services/ResumeBuilder/save_or_update_extraCurricularActivity"); ?>', {
		  method: 'POST',
		  headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
		  body: payload.toString()
		});
		const result = await response.json();
			if (result.success) {
			if (!dataObj.activity_id && item) {
				item.remove(); // Remove unsaved block
			}

			loadActivityData();         // Reload activity list
			attachActivityItemEvents(); // Rebind events
			updatePreview();            // Update resume preview

			if (item && !dataObj.activity_id && result.data?.id) {
				item.setAttribute('data-id', result.data.id);
				item.querySelector('.activity_id').value = result.data.id;
			}
		}
		else {
		  console.error("Error saving activity:", result.message);
		}
	  } catch (error) {
		console.error("Network error while saving activity:", error);
	  }
	}

	// --- Open the Mobile Modal for Activity ---
	function openMobileModalForActivity(mode, triggerButton = null, currentItem = null) {
	  const modal      = document.getElementById('itemModal');
	  const modalTitle = document.getElementById('modalTitle');
	  const modalForm  = document.getElementById('modalForm');
	  
	  modal.classList.remove('hidden');
	  currentActivityMobileItem = (mode === 'edit' ? currentItem : null);
	  modalTitle.textContent = mode === 'edit' ? 'Edit Activity' : 'Add Activity';
	  
	  let deleteButtonHtml = '';
	  if (mode === 'edit' && currentItem?.querySelector('.activity_id').value) {
		deleteButtonHtml = `<button type="button" onclick="deleteMobileActivityRecord()" class="flex-1 px-4 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700">Delete</button>`;
	  }
	  
	  modalForm.innerHTML = `
		<input type="hidden" id="activity_id" value="${currentItem?.querySelector('.activity_id')?.value || ''}">
		<input type="hidden" id="form_source" value="modal_form">
		<div>
		  <label class="block text-sm font-medium mb-2">Activity Name</label>
		  <input type="text" id="activityName" class="w-full px-4 py-3 border rounded-lg" required>
		</div>
		<div>
		  <label class="block text-sm font-medium mb-2">Position</label>
		  <input type="text" id="position" class="w-full px-4 py-3 border rounded-lg" required>
		</div>
		<div>
		  <label class="block text-sm font-medium mb-2">Description</label>
		  <textarea id="description" class="w-full px-4 py-3 border rounded-lg" required></textarea>
		</div>
		<div class="flex gap-3 mt-6">
		  <button type="button" onclick="closeMobileModal()" class="flex-1 px-4 py-3 border border-gray-300 rounded-lg hover:bg-gray-50">Cancel</button>
		  ${deleteButtonHtml}
		  <button type="submit" class="flex-1 px-4 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Save</button>
		</div>
	  `;
	  
	  if (mode === 'edit' && currentItem) {
		document.getElementById('activityName').value = currentItem.querySelector('.activityName').value || currentItem.querySelector('h3').textContent.trim();
		document.getElementById('position').value     = currentItem.querySelector('.position').value || '';
		document.getElementById('description').value  = currentItem.querySelector('.description').value || '';
	  }
	}

	// --- Delete Activity Record from Mobile Modal ---
	function deleteMobileActivityRecord() {
	  //if (!confirm("Are you sure you want to delete this activity?")) return;
	  const activityId = document.getElementById('activity_id').value.trim();
	  if (!activityId) {
		closeMobileModal();
		//alert("This activity has not been saved in the database.");
		return;
	  }
	  const payload = new URLSearchParams();
	  payload.append('activity_id', activityId);
	  payload.append('template_id', document.getElementById('templateId').value);
	  
	  fetch('<?= base_url("website/services/ResumeBuilder/delete_extraCurricularActivity"); ?>', {
		method: 'POST',
		headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
		body: payload.toString()
	  })
	  .then(res => res.json())
	  .then(result => {
		if (result.success) {
		  currentActivityMobileItem?.remove();
		  updatePreview();
		  closeMobileModal();
		  alert("Activity deleted successfully.");
		} else {
		  alert("Error deleting activity: " + result.message);
		}
	  })
	  .catch(err => {
		alert("Network error while deleting activity.");
		console.error(err);
	  });
	}

	// --- Handle modal form submission (for activity) ---
	document.getElementById('modalForm').addEventListener('submit', event => {
	  event.preventDefault();
	  if (document.getElementById('activityName')) {
		const data = {
		  activity_id:   document.getElementById('activity_id').value.trim(),
		  activityName:  document.getElementById('activityName').value.trim(),
		  position:      document.getElementById('position').value.trim(),
		  description:   document.getElementById('description').value.trim()
		};
		saveActivityRecordCommon(data);
		closeMobileModal();
	  }
	});

	// --- Add Activity button handler (Desktop & Mobile) ---
	document.getElementById('addActivity').addEventListener('click', () => {
	  if (isMobile()) {
		openMobileModalForActivity('add');
	  } else {
		document.querySelectorAll('.activity-item').forEach(item => {
		  item.querySelector('.content').classList.add('hidden');
		  item.querySelector('.arrow-icon').classList.remove('rotate-180');
		});
		const newActivity = createDesktopActivityItem();
		newActivity.querySelector('.content').classList.remove('hidden');
		newActivity.querySelector('.arrow-icon').classList.add('rotate-180');
		document.getElementById('activityContainer').appendChild(newActivity);
		attachActivityDeleteButtonEvents();
	  }
	});

	// Finally, load the extra curricular activities data when the page loads.
	loadActivityData();	
	
	
	// --- Skills Section: Complete Logic ---

	// Global state for mobile modal
	let currentMobileSection = null;
	let currentMobileItem = null;

	// DOM references
	const skillsContainer     = document.getElementById('skillsContainer');
	const addSkillBtn         = document.getElementById('addSkillBtn');
	const skillInputContainer = document.getElementById('skillInputContainer');
	const skillInput          = document.getElementById('skillInput');
	const saveSkillBtn        = document.getElementById('saveSkillBtn');
	const cancelSkillBtn      = document.getElementById('cancelSkillBtn');

	// 1) Load Skills on page load
	async function loadSkills() {
	  try {
		const fd = new FormData();
		fd.append('template_id', document.getElementById('templateId').value);

		const res = await fetch('<?= base_url("website/services/ResumeBuilder/fetch_skills") ?>', {
		  method: 'POST',
		  body: fd
		});
		const json = await res.json();
		if (json.success) {
		  skillsContainer.innerHTML = '';
		  json.skills.forEach(s => addSkill(s.skill_name, s.id));
		}
	  } catch (err) {
		console.error('Error loading skills:', err);
	  }
	}

	// 2) Render a Skill Tag
	// 2) Render a Skill Tag (स्किल आईडी हटाकर)
	function addSkill(text) {
	  const el = document.createElement('div');
	  el.className = 'skill-item bg-purple-100 text-purple-600 px-4 py-2 rounded-full flex items-center gap-2';
	  el.dataset.skill = text; // Original skill store करें
	  el.innerHTML = `
		<span class="editable-skill">${text}</span>
		<button class="remove-btn">×</button>
	  `;

	  // डेस्कटॉप इनलाइन एडिट
	  if (!isMobile()) {
		const span = el.querySelector('.editable-skill');
		span.style.cursor = 'pointer';
		span.addEventListener('click', e => {
		  e.stopPropagation();
		  startInlineEdit(el, span);
		});
	  } 
	  // मोबाइल मोडल
	  else {
		el.querySelector('.editable-skill').addEventListener('click', () => {
		  openSkillModal('edit', el);
		});
	  }

	  // डिलीट बटन
		 // डिलीट बटन को नए तरीके से
		  el.querySelector('.remove-btn').addEventListener('click', () => {
			deleteSkill(el.dataset.skill); // dataset.skill से नाम लें
			el.remove();
		  });

	  skillsContainer.appendChild(el);
	}

	// Inline edit helper with outside-click commit
	function startInlineEdit(itemEl, span) {
	 const original = itemEl.dataset.skill; // Get from dataset
	  const input = document.createElement('input');
	  input.type = 'text';
	  input.value = original;
	  input.className = 'w-full px-2 py-1 border rounded-md';

	  span.replaceWith(input);
	  input.focus();

	  function commit() {
		//const newValue = input.value.trim() || original;
		 const newValue = input.value.trim();
		if (newValue && newValue !== original) {
		  span.textContent = newValue;
		  itemEl.dataset.skill = newValue; // Update stored value
		  updateSkill(original, newValue); // Pass old & new names
		}
		span.textContent = newValue;
		input.replaceWith(span);
		updatePreview();  // ensure any UI preview updates
		document.removeEventListener('click', outsideClick);
	  }

	  function outsideClick(e) {
		if (!input.contains(e.target)) {
		  commit();
		}
	  }

	  input.addEventListener('blur', commit);
	  document.addEventListener('click', outsideClick);

	  input.addEventListener('keydown', e => {
		if (e.key === 'Enter') {
		  e.preventDefault();
		  commit();
		}
	  });
	}

// 3) Open Skill Modal (Add/Edit) for mobile only
function openSkillModal(mode, item = null) {
  currentMobileSection = 'skill';
  currentMobileItem = item;

  const modal     = document.getElementById('itemModal');
  const title     = document.getElementById('modalTitle');
  const form      = document.getElementById('modalForm');

  modal.classList.remove('hidden');
  title.textContent = mode === 'edit' ? 'Edit Skill' : 'Add Skill';

  const existingValue = item ? item.querySelector('.editable-skill').textContent.trim() : '';

  form.innerHTML = `
    <div class="space-y-4">
      <div>
        <label class="block text-sm font-medium">Skill Name</label>
        <input type="text" id="skillName" class="w-full px-4 py-3 border rounded-lg" 
               value="${existingValue}" placeholder="e.g., Project Management" required>
      </div>
      <div class="flex gap-3">
        <button type="button" id="cancelMobileSkillBtn" 
                class="flex-1 px-4 py-3 border rounded-lg hover:bg-gray-100">Cancel</button>
        ${mode === 'edit' && item?.dataset.id
          ? `<button type="button" id="deleteMobileSkillBtn" 
                    class="flex-1 px-4 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700">Delete</button>`
          : ''}
        <button type="submit" id="updateMobileSkillBtn" 
                class="flex-1 px-4 py-3 bg-purple-600 text-white rounded-lg hover:bg-purple-700">
          ${mode === 'edit' ? 'Update' : 'Add'}
        </button>
      </div>
    </div>`;

	  document.getElementById('cancelMobileSkillBtn').addEventListener('click', closeModal);
	  if (mode === 'edit') {
		document.getElementById('deleteMobileSkillBtn').addEventListener('click', deleteCurrentSkill);
	  }
	}

	// 4) Close Modal
	function closeModal() {
	  document.getElementById('itemModal').classList.add('hidden');
	  document.getElementById('modalForm').innerHTML = '';
	  currentMobileSection = null;
	  currentMobileItem = null;
	}

	// 5) Delete in modal
	// मोबाइल डिलीट को ठीक करें
	function deleteCurrentSkill() {
	  //if (!confirm('Delete this skill?')) return;
	  if (currentMobileItem?.dataset.skill) { // dataset.id की जगह dataset.skill
		deleteSkill(currentMobileItem.dataset.skill); // नाम भेजें
		currentMobileItem.remove();
	  }
	  closeModal();
	}

	// 6) Modal form submission (mobile only)
	document.getElementById('modalForm').addEventListener('submit', async e => {
	  e.preventDefault();
	  if (currentMobileSection !== 'skill') return;

	  const name = document.getElementById('skillName').value.trim();
	  if (!name) return;

	 if (currentMobileItem) {
		// 🔴 ID को dataset से लें और updateSkill को पास करें
		const skillId = currentMobileItem.dataset.skill;
		await updateSkill(skillId, name); // ID-based update
		
		currentMobileItem.querySelector('.editable-skill').textContent = name;
		currentMobileItem.dataset.skill = name; // Optional: नाम भी अपडेट
	  } else {
		// Add new
		addSkill(name);
		await saveSkill(name);
	  }

	  updatePreview();  // update preview after mobile save/update
	  closeModal();
	});

	// 7) API calls
	async function saveSkill(name) {
	  const fd = new FormData();
	  fd.append('template_id', document.getElementById('templateId').value);
	  fd.append('skill_name', name);
	  try {
		const res = await fetch('<?= base_url("website/services/ResumeBuilder/save_skill") ?>', { method: 'POST', body: fd });
		const json = await res.json();
		if (json.success) {
		  const last = skillsContainer.lastElementChild;
		  last.dataset.id = json.skill_id;
		}
	  } catch (err) { console.error(err); }
	}

	// API call update (New)
	async function updateSkill(oldName, newName) {
	  const fd = new FormData();
	  fd.append('old_name', oldName);
	  fd.append('new_name', newName);
	  try {
		await fetch('<?= base_url("website/services/ResumeBuilder/update_skill") ?>', {
		  method: 'POST',
		  body: fd
		});
	  } catch (err) {
		console.error('Update error:', err);
	  }
	}

	// Delete handler को सही करें (नाम के आधार पर डिलीट)
	async function deleteSkill(skillName) { // पैरामीटर नाम बदलें
	  const fd = new FormData();
	  fd.append('skill_name', skillName); // बैकेंड को नाम भेजें
	  try {
		await fetch('<?= base_url("website/services/ResumeBuilder/delete_skill") ?>', { 
		  method: 'POST', 
		  body: fd 
		});
	  } catch (err) { console.error(err); }
	}


	// 8) Desktop "Add Skill" button
	addSkillBtn.addEventListener('click', () => {
	  if (isMobile()) {
		openSkillModal('add');
	  } else {
		if (!skillInputContainer.classList.contains('hidden')) return;
		skillInputContainer.classList.remove('hidden');
		skillInput.focus();
	  }
	});

	// 9) Desktop Save/Cancel handlers
	saveSkillBtn.addEventListener('click', async () => {
	  const name = skillInput.value.trim();
	  if (!name) return;
	  addSkill(name);
	  await saveSkill(name);
	  skillInput.value = '';
	  skillInputContainer.classList.add('hidden');
	  updatePreview();
	});
	cancelSkillBtn.addEventListener('click', () => {
	  skillInput.value = '';
	  skillInputContainer.classList.add('hidden');
	});
	skillInput.addEventListener('keydown', ev => {
	  if (ev.key === 'Enter') {
		ev.preventDefault();
		saveSkillBtn.click();
	  }
	});

	// Initialize
	loadSkills();


});     
</script>

