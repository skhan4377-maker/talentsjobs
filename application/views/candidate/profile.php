
<!-- Global AJAX Loader -->
<div id="globalLoader" class="fixed inset-0 bg-black/50 z-[9999] flex items-center justify-center hidden">
    <div class="w-10 h-10 border-4 border-gray-200 border-t-[#1D3557] rounded-full animate-spin"></div>
</div>

<!-- Main Profile Card -->
<div class="max-w-7xl mx-auto my-8 bg-white rounded-xl shadow-xl overflow-hidden">
    <!-- Banner Section -->
    <div class="relative w-full h-40 bg-gradient-to-br from-[#1D3557] to-[#457B9D] rounded-t-lg mb-12">
        <div class="absolute inset-0 bg-gradient-to-r from-white/20 to-transparent"></div>

        <div class="absolute -bottom-12 left-4">
            <div class="relative group">
                <img id="profileImage" src="#"
                     class="w-24 h-24 rounded-full border-4 border-white shadow-[0_10px_15px_-3px_rgba(29,53,87,0.2)] group-hover:scale-105 transition-transform"
                     alt="Profile">

                <!-- Upload Icon -->
                <label for="fileInput" class="absolute bottom-2 right-2 bg-black/50 rounded-full p-1.5 cursor-pointer transition-all duration-200 hover:bg-black/70 hover:scale-110">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-white">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 015.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 00-1.134-.175 2.31 2.31 0 01-1.64-1.055l-.822-1.316a2.192 2.192 0 00-1.736-1.039 48.774 48.774 0 00-5.232 0 2.192 2.192 0 00-1.736 1.039l-.821 1.316z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0zM18.75 10.5h.008v.008h-.008V10.5z" />
                    </svg>
                    <input type="file" id="fileInput" class="hidden" accept="image/*" name="image">
                </label>
            </div>
        </div>

        <!-- Status Message -->
        <div id="uploadStatus" class="text-center mt-2 text-sm text-gray-700"></div>

        <!-- Profile Status Badges -->
        <div class="absolute top-4 right-4 flex flex-wrap gap-2 items-center">
            <span id="profileStatusBadge"></span>
            <div class="bg-blue-500/90 text-white px-3 py-1 rounded-full flex items-center gap-1 shadow-md" title="Profile Completion">
                <div class="w-4 h-4 border-2 border-white rounded-full flex items-center justify-center">
                    <span class="text-[10px] font-bold">85%</span>
                </div>
                <span class="text-xs font-medium">Complete</span>
            </div>
        </div>

       <!-- Email Verification Status (left side) -->
		<div class="absolute top-4 left-4" id="emailVerificationStatus"></div>

		<!-- Profile Status Badges (right side) -->
		<div class="absolute top-4 right-4 flex flex-wrap gap-2 items-center">
			<span id="profileStatusBadge"></span>
			<div class="bg-blue-500/90 text-white px-3 py-1 rounded-full flex items-center gap-1 shadow-md" title="Profile Completion">
				<div class="w-4 h-4 border-2 border-white rounded-full flex items-center justify-center">
					<span class="text-[10px] font-bold">85%</span>
				</div>
				<span class="text-xs font-medium">Complete</span>
			</div>

			<!-- ✅ Premium Badge (improved design) -->
			<?php if ($this->session->userdata('has_active_plan')): ?>
				<span class="bg-gradient-to-r from-yellow-400 to-amber-500 text-white px-3 py-1 rounded-full flex items-center gap-1 shadow-md" title="Premium Subscriber">
					<i class="fas fa-crown text-xs"></i>
					<span class="text-xs font-medium">Premium</span>
				</span>
			<?php endif; ?>
		</div>

        <!-- Upload Loader & Progress -->
        <div id="uploadLoader" class="hidden absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 z-10">
            <div class="flex flex-col items-center space-y-2 bg-black/80 px-4 py-3 rounded-lg">
                <div class="w-8 h-8 border-4 border-white/20 rounded-full animate-spin border-t-white"></div>
                <div class="w-32 h-1.5 bg-gray-400/30 rounded-full overflow-hidden">
                    <div id="progressBar" class="h-full bg-white transition-all duration-300" style="width: 0%"></div>
                </div>
                <span id="progressPercent" class="text-xs font-medium text-white">0%</span>
            </div>
        </div>
    </div>

    <!-- Profile Header -->
    <div class="flex flex-col md:flex-row justify-between items-start mb-6 px-6">
        <div>
            <h1 id="profileName" class="text-3xl font-bold text-gray-900 mb-1"></h1>
            <p id="profileTitle" class="text-lg text-[#1D3557] font-medium"></p>
        </div>

        <!-- Edit button opens modal for Basic Details by default -->
        <button id="editProfileBtn" class="mt-4 md:mt-0 px-6 py-2.5 bg-gradient-to-r from-[#1D3557] to-[#457B9D] text-white rounded-xl shadow-lg hover:shadow-xl transition-shadow" data-edit="basic">
            <i class="fas fa-edit mr-2"></i>Edit Profile
        </button>
    </div>
</div>

<!-- Basic Details Section -->
<div class="max-w-7xl mx-auto my-8 bg-white rounded-xl shadow-md p-6" id="section-basic">
    <div class="flex justify-between items-center border-b border-gray-200 pb-3 mb-4">
        <h3 class="text-xl font-semibold text-[#1D3557]"><i class="fas fa-user mr-2"></i>Basic Details</h3>
        <button type="button" data-edit="basic" class="text-[#457B9D] hover:text-[#1D3557] transition-colors">
            <i class="fas fa-edit"></i>
        </button>
    </div>
    <p><strong>Name:</strong> <span id="basicName"></span></p>
    <p><strong>Date of Birth:</strong> <span id="basicDob"></span></p>
    <p><strong>Gender:</strong> <span id="basicGender"></span></p>
</div>

<!-- Personal Details Section -->
<div class="max-w-7xl mx-auto my-8 bg-white rounded-xl shadow-md p-6" id="section-personal">
    <div class="flex justify-between items-center border-b border-gray-200 pb-3 mb-4">
        <h3 class="text-xl font-semibold text-[#1D3557]"><i class="fas fa-address-card mr-2"></i>Personal Details</h3>
        <button type="button" data-edit="personal" class="text-[#457B9D] hover:text-[#1D3557] transition-colors">
            <i class="fas fa-edit"></i>
        </button>
    </div>
    <p><strong>Email:</strong> <span id="personalEmail"></span></p>
    <p><strong>Mobile:</strong> <span id="personalMobile"></span></p>
    <p><strong>Address:</strong> <span id="candidateAddress"></span></p>
    <p><strong>City:</strong> <span id="candidateCity"></span></p>
</div>

<!-- Career Information Section -->
<div class="max-w-7xl mx-auto my-8 bg-white rounded-xl shadow-md p-6" id="section-career">
    <div class="flex justify-between items-center border-b border-gray-200 pb-3 mb-4">
        <h3 class="text-xl font-semibold text-[#1D3557]"><i class="fas fa-briefcase mr-2"></i>Career Information</h3>
    </div>
    <p><strong>Work Status:</strong> <span id="candidateWorkStatus"></span></p>
    <p><strong>Total Experience:</strong> <span id="candidateExperience"></span></p>
    <p><strong>Industry:</strong> <span id="candidateIndustry"></span></p>
    <p><strong>Functional Area:</strong> <span id="candidateFunctional"></span></p>
</div>

<!-- Resume Section -->
<div class="max-w-7xl mx-auto my-8 bg-white rounded-xl shadow-md p-6" id="section-resume">
    <div class="flex justify-between items-center border-b border-gray-200 pb-3 mb-4">
        <h3 class="text-xl font-semibold text-[#1D3557]"><i class="fas fa-file-alt mr-2"></i>Resume</h3>
        <button type="button" data-edit="resume" class="text-[#457B9D] hover:text-[#1D3557] transition-colors">
            <i class="fas fa-edit"></i>
        </button>
    </div>
    <p><strong>Resume Headline:</strong> <span id="resumeHeadline"></span></p>
    <div id="resumeFileContainer"></div>
</div>

<!-- About Section -->
<div class="max-w-7xl mx-auto my-8 bg-white rounded-xl shadow-md p-6" id="section-about">
    <div class="flex justify-between items-center border-b border-gray-200 pb-3 mb-4">
        <h3 class="text-xl font-semibold text-[#1D3557]"><i class="fas fa-info-circle mr-2"></i>About</h3>
        <button type="button" data-edit="about" class="text-[#457B9D] hover:text-[#1D3557] transition-colors">
            <i class="fas fa-edit"></i>
        </button>
    </div>
    <p id="aboutText"></p>
    <p><strong>Career Objective:</strong> <span id="candidateObjective"></span></p>
</div>

<!-- Skills Section -->
<div class="max-w-7xl mx-auto my-8 bg-white rounded-xl shadow-md p-6" id="section-skills">
    <div class="flex justify-between items-center border-b border-gray-200 pb-3 mb-4">
        <h3 class="text-xl font-semibold text-[#1D3557]"><i class="fas fa-lightbulb mr-2"></i>Skills</h3>
        <button type="button" data-edit="skills" class="text-[#457B9D] hover:text-[#1D3557] transition-colors">
            <i class="fas fa-edit"></i>
        </button>
    </div>
    <p id="skillsList"></p>
</div>

<!-- Education Section -->
<div class="max-w-7xl mx-auto my-8 bg-white rounded-xl shadow-md p-6" id="section-education">
    <div class="flex justify-between items-center border-b border-gray-200 pb-3 mb-4">
        <h3 class="text-xl font-semibold text-[#1D3557]"><i class="fas fa-graduation-cap mr-2"></i>Education</h3>
        <div class="flex gap-2">
            <button type="button" data-add="education" class="text-[#457B9D] hover:text-[#1D3557] transition-colors">
                <i class="fas fa-plus"></i>
            </button>
        </div>
    </div>
    <div id="educationDisplay" class="loading-indicator text-gray-500 italic">Loading education...</div>
</div>

<!-- Work Experience Section -->
<div class="max-w-7xl mx-auto my-8 bg-white rounded-xl shadow-md p-6" id="section-work">
    <div class="flex justify-between items-center border-b border-gray-200 pb-3 mb-4">
        <h3 class="text-xl font-semibold text-[#1D3557]"><i class="fas fa-briefcase mr-2"></i>Work Experience</h3>
        <div class="flex gap-2">
            <button type="button" data-add="work" class="text-[#457B9D] hover:text-[#1D3557] transition-colors">
                <i class="fas fa-plus"></i>
            </button>
        </div>
    </div>
    <div id="workExperienceDisplay" class="loading-indicator text-gray-500 italic">Loading work experience...</div>
</div>

<!-- Job Preferences Section -->
<div class="max-w-7xl mx-auto my-8 bg-white rounded-xl shadow-md p-6" id="section-job_preferences">
    <div class="flex justify-between items-center border-b border-gray-200 pb-3 mb-4">
        <h3 class="text-xl font-semibold text-[#1D3557]"><i class="fas fa-briefcase mr-2"></i>Job Preferences</h3>
        <button type="button" data-edit="job_preferences" class="text-[#457B9D] hover:text-[#1D3557] transition-colors">
            <i class="fas fa-edit"></i>
        </button>
    </div>
    <p><strong>Preferred Location:</strong> <span id="jobPreferredLocation"></span></p>
    <p><strong>Postal Code:</strong> <span id="jobPostal"></span></p>
    <p><strong>Notice Period:</strong> <span id="jobNoticePeriod"></span></p>
    <p><strong>Current CTC:</strong> <span id="jobCurrentCTC"></span></p>
</div>

<!-- Online Profiles Section -->
<div class="max-w-7xl mx-auto my-8 bg-white rounded-xl shadow-md p-6" id="section-links">
    <div class="flex justify-between items-center border-b border-gray-200 pb-3 mb-4">
        <h3 class="text-xl font-semibold text-[#1D3557]"><i class="fas fa-link mr-2"></i>Online Profiles</h3>
    </div>
    <p><strong>LinkedIn:</strong> <span id="candidateLinkedIn"></span></p>
    <p><strong>Portfolio:</strong> <span id="candidatePortfolio"></span></p>
</div>

<!-- ==================== TEMPLATES ==================== -->

<!-- Personal Details Template -->
<template id="personalDetailsTemplate">
    <form id="personalDetailsForm" class="space-y-6 commonForm" data-section="personal">
        <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
        <input type="hidden" name="form_type" value="personal">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
            <input type="email" name="email" class="w-full p-3 rounded-lg border border-gray-200 bg-gray-100 focus:outline-none focus:border-transparent focus:ring-2 focus:ring-[#1D3557]/50 transition-all" readonly>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Mobile</label>
            <input type="text" name="mobile" class="w-full p-3 rounded-lg border border-gray-200 focus:outline-none focus:border-transparent focus:ring-2 focus:ring-[#1D3557]/50 transition-all" required>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
            <textarea name="address" class="w-full p-3 rounded-lg border border-gray-200 focus:outline-none focus:border-transparent focus:ring-2 focus:ring-[#1D3557]/50 transition-all" rows="2"></textarea>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Place of Birth</label>
            <input type="text" name="placeOfBirth" class="w-full p-3 rounded-lg border border-gray-200 focus:outline-none focus:border-transparent focus:ring-2 focus:ring-[#1D3557]/50 transition-all">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">City</label>
            <input type="text" id="city_input" name="city_name" class="w-full p-3 rounded-lg border border-gray-200 focus:outline-none focus:border-transparent focus:ring-2 focus:ring-[#1D3557]/50 transition-all">
            <input type="hidden" name="city_id" id="city_id">
            <ul id="city_list" class="absolute z-50 w-full bg-white shadow-lg rounded-b-lg mt-1 max-h-60 overflow-y-auto hidden"></ul>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Functional Area</label>
            <input type="text" id="functional_input" name="functional_name" class="w-full p-3 rounded-lg border border-gray-200 focus:outline-none focus:border-transparent focus:ring-2 focus:ring-[#1D3557]/50 transition-all">
            <input type="hidden" name="functional_id" id="functional_id">
            <ul id="functional_list" class="absolute z-50 w-full bg-white shadow-lg rounded-b-lg mt-1 max-h-60 overflow-y-auto hidden"></ul>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Industry</label>
            <input type="text" id="industry_input" name="industry_name" class="w-full p-3 rounded-lg border border-gray-200 focus:outline-none focus:border-transparent focus:ring-2 focus:ring-[#1D3557]/50 transition-all">
            <input type="hidden" name="industry_id" id="industry_id">
            <ul id="industry_list" class="absolute z-50 w-full bg-white shadow-lg rounded-b-lg mt-1 max-h-60 overflow-y-auto hidden"></ul>
        </div>
        <button type="submit" class="w-full px-4 py-3 bg-gradient-to-r from-[#1D3557] to-[#457B9D] text-white rounded-xl hover:shadow-lg transition-shadow">
            <i class="fas fa-save mr-2"></i>Save Personal
        </button>
    </form>
</template>

<!-- Basic Details Template -->
<template id="basicDetailsTemplate">
    <form id="basicDetailsForm" class="space-y-6 commonForm" data-section="basic">
        <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
        <input type="hidden" name="form_type" value="basic">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">First Name</label>
            <input type="text" name="first_name" class="w-full p-3 rounded-lg border border-gray-200 focus:outline-none focus:border-transparent focus:ring-2 focus:ring-[#1D3557]/50 transition-all" required>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Last Name</label>
            <input type="text" name="last_name" class="w-full p-3 rounded-lg border border-gray-200 focus:outline-none focus:border-transparent focus:ring-2 focus:ring-[#1D3557]/50 transition-all" required>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Designation</label>
            <div class="relative">
                <input type="text" id="job_profile_input" name="designation" class="w-full p-3 rounded-lg border border-gray-200 focus:outline-none focus:border-transparent focus:ring-2 focus:ring-[#1D3557]/50 transition-all" placeholder="Search job profiles..." autocomplete="off">
                <input type="hidden" id="job_profile_id">
                <ul id="job_profile_list" class="absolute z-50 w-full bg-white shadow-lg rounded-b-lg mt-1 max-h-60 overflow-y-auto hidden"></ul>
            </div>
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Date of Birth</label>
                <input type="date" name="dob" class="w-full p-3 rounded-lg border border-gray-200 focus:outline-none focus:border-transparent focus:ring-2 focus:ring-[#1D3557]/50 transition-all">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Gender</label>
                <select name="gender" class="w-full p-3 rounded-lg border border-gray-200 focus:outline-none focus:border-transparent focus:ring-2 focus:ring-[#1D3557]/50 transition-all">
                    <option value="" disabled selected>Select Gender</option>
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                    <option value="Other">Other</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Work Status</label>
                <select name="work_status" class="w-full p-3 rounded-lg border border-gray-200 focus:outline-none focus:border-transparent focus:ring-2 focus:ring-[#1D3557]/50 transition-all">
                    <option value="Fresher">Fresher</option>
                    <option value="Experience">Experience</option>
                </select>
            </div>
            <div class="grid grid-cols-2 gap-2">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Years</label>
                    <input type="number" name="experience_years" class="w-full p-3 rounded-lg border border-gray-200 focus:outline-none focus:border-transparent focus:ring-2 focus:ring-[#1D3557]/50 transition-all" placeholder="Years" min="0" max="50">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Months</label>
                    <input type="number" name="experience_months" class="w-full p-3 rounded-lg border border-gray-200 focus:outline-none focus:border-transparent focus:ring-2 focus:ring-[#1D3557]/50 transition-all" placeholder="Months" min="0" max="11">
                </div>
            </div>
        </div>
        <button type="submit" class="w-full px-4 py-3 bg-gradient-to-r from-[#1D3557] to-[#457B9D] text-white rounded-xl hover:shadow-lg transition-shadow">
            <i class="fas fa-save mr-2"></i>Save Basic Details
        </button>
    </form>
</template>

<!-- Resume Template -->
<template id="resumeTemplate">
    <form id="resumeForm" class="space-y-6 commonForm" data-section="resume">
        <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
        <input type="hidden" name="form_type" value="resume">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Resume Headline</label>
            <textarea name="resume_headline" class="w-full p-3 rounded-lg border border-gray-200 focus:outline-none focus:border-transparent focus:ring-2 focus:ring-[#1D3557]/50 transition-all" rows="2" required></textarea>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Upload Resume</label>
            <input type="file" name="resume" class="w-full p-3 rounded-lg border border-gray-200 focus:outline-none focus:border-transparent focus:ring-2 focus:ring-[#1D3557]/50 transition-all">
        </div>
        <button type="submit" class="w-full px-4 py-3 bg-gradient-to-r from-[#1D3557] to-[#457B9D] text-white rounded-xl hover:shadow-lg transition-shadow">
            <i class="fas fa-save mr-2"></i>Save Resume
        </button>
    </form>
</template>

<!-- About Template -->
<template id="aboutTemplate">
    <form id="aboutForm" class="space-y-6 commonForm" data-section="about">
        <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
        <input type="hidden" name="form_type" value="about">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">About</label>
            <textarea name="about" class="w-full p-3 rounded-lg border border-gray-200 focus:outline-none focus:border-transparent focus:ring-2 focus:ring-[#1D3557]/50 transition-all" rows="4"></textarea>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Career Objective</label>
            <textarea name="objective" class="w-full p-3 rounded-lg border border-gray-200 focus:outline-none focus:border-transparent focus:ring-2 focus:ring-[#1D3557]/50 transition-all" rows="3"></textarea>
        </div>
        <button type="submit" class="w-full px-4 py-3 bg-gradient-to-r from-[#1D3557] to-[#457B9D] text-white rounded-xl hover:shadow-lg transition-shadow">
            <i class="fas fa-save mr-2"></i>Save About
        </button>
    </form>
</template>

<!-- Skills Template -->
<template id="skillsTemplate">
    <form id="skillsForm" class="space-y-6 commonForm" data-section="skills">
        <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
        <input type="hidden" name="form_type" value="skills">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Skills</label>
            <div class="border border-gray-200 rounded-lg p-4">
                <div class="flex flex-wrap gap-2 mb-2 tags-display"></div>
                <input type="text" class="tag-input w-full p-2 rounded-lg border border-gray-200 focus:outline-none focus:border-transparent focus:ring-2 focus:ring-[#1D3557]/50 transition-all" placeholder="Type a skill and press Enter">
                <p class="text-sm text-gray-500 mt-1">Add multiple skills by pressing Enter or comma (max 5)</p>
            </div>
            <input type="hidden" name="skills" id="skillsHiddenInput">
        </div>
        <button type="submit" class="w-full px-4 py-3 bg-gradient-to-r from-[#1D3557] to-[#457B9D] text-white rounded-xl hover:shadow-lg transition-shadow">
            <i class="fas fa-save mr-2"></i>Save Skills
        </button>
    </form>
</template>

<!-- Education Template -->
<template id="educationTemplate">
    <form id="educationForm" class="space-y-6 commonForm" data-section="education">
        <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
        <input type="hidden" name="form_type" value="education">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Degree Name</label>
            <input type="text" name="degreeName" class="w-full p-3 rounded-lg border border-gray-200 focus:outline-none focus:border-transparent focus:ring-2 focus:ring-[#1D3557]/50 transition-all" required>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Institution Name</label>
            <input type="text" name="institutionName" class="w-full p-3 rounded-lg border border-gray-200 focus:outline-none focus:border-transparent focus:ring-2 focus:ring-[#1D3557]/50 transition-all" required>
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Start Year</label>
                <input type="number" name="startYear" class="w-full p-3 rounded-lg border border-gray-200 focus:outline-none focus:border-transparent focus:ring-2 focus:ring-[#1D3557]/50 transition-all" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">End Year</label>
                <input type="number" name="endYear" class="w-full p-3 rounded-lg border border-gray-200 focus:outline-none focus:border-transparent focus:ring-2 focus:ring-[#1D3557]/50 transition-all" required>
            </div>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Field of Study</label>
            <input type="text" name="fieldOfStudy" class="w-full p-3 rounded-lg border border-gray-200 focus:outline-none focus:border-transparent focus:ring-2 focus:ring-[#1D3557]/50 transition-all" required>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Honors</label>
            <input type="text" name="honors" class="w-full p-3 rounded-lg border border-gray-200 focus:outline-none focus:border-transparent focus:ring-2 focus:ring-[#1D3557]/50 transition-all">
        </div>
        <input type="hidden" name="id" value="">
        <button type="submit" class="w-full px-4 py-3 bg-gradient-to-r from-[#1D3557] to-[#457B9D] text-white rounded-xl hover:shadow-lg transition-shadow">
            <i class="fas fa-save mr-2"></i>Save Education
        </button>
    </form>
</template>

<!-- Work Experience Template -->
<template id="workExperienceTemplate">
    <form id="workExperienceForm" class="space-y-6 commonForm" data-section="work">
        <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
        <input type="hidden" name="form_type" value="work">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Company Name</label>
            <input type="text" name="employer_name" class="w-full p-3 rounded-lg border border-gray-200 focus:outline-none focus:border-transparent focus:ring-2 focus:ring-[#1D3557]/50 transition-all">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Position</label>
            <input type="text" name="job_title" class="w-full p-3 rounded-lg border border-gray-200 focus:outline-none focus:border-transparent focus:ring-2 focus:ring-[#1D3557]/50 transition-all">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Job Type</label>
            <select name="job_type" class="w-full p-3 rounded-lg border border-gray-200 focus:outline-none focus:border-transparent focus:ring-2 focus:ring-[#1D3557]/50 transition-all">
                <option value="" disabled selected>Select job type</option>
                <option value="Full-Time">Full-Time</option>
                <option value="Part-Time">Part-Time</option>
                <option value="Contract">Contract</option>
                <option value="Internship">Internship</option>
                <option value="Temporary">Temporary</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Work Location</label>
            <input type="text" name="work_location" class="w-full p-3 rounded-lg border border-gray-200 focus:outline-none focus:border-transparent focus:ring-2 focus:ring-[#1D3557]/50 transition-all">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
            <input type="date" name="start_date" class="w-full p-3 rounded-lg border border-gray-200 focus:outline-none focus:border-transparent focus:ring-2 focus:ring-[#1D3557]/50 transition-all">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
            <input type="date" name="end_date" class="w-full p-3 rounded-lg border border-gray-200 focus:outline-none focus:border-transparent focus:ring-2 focus:ring-[#1D3557]/50 transition-all">
        </div>
        <div class="flex items-center">
            <input type="checkbox" name="is_current" id="is_current" class="mr-2">
            <label for="is_current" class="text-sm text-gray-700">I am currently employed here</label>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Responsibilities</label>
            <textarea name="responsibilities" class="w-full p-3 rounded-lg border border-gray-200 focus:outline-none focus:border-transparent focus:ring-2 focus:ring-[#1D3557]/50 transition-all" rows="3"></textarea>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Achievements</label>
            <textarea name="achievements" class="w-full p-3 rounded-lg border border-gray-200 focus:outline-none focus:border-transparent focus:ring-2 focus:ring-[#1D3557]/50 transition-all" rows="3"></textarea>
        </div>
        <input type="hidden" name="id" value="">
        <button type="submit" class="w-full px-4 py-3 bg-gradient-to-r from-[#1D3557] to-[#457B9D] text-white rounded-xl hover:shadow-lg transition-shadow">
            <i class="fas fa-save mr-2"></i>Save Work Experience
        </button>
    </form>
</template>

<!-- Job Preferences Template -->
<template id="jobPreferencesTemplate">
    <form id="jobPreferencesForm" class="space-y-6 commonForm" data-section="job_preferences">
        <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
        <input type="hidden" name="form_type" value="job_preferences">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Preferred Location</label>
            <div class="relative">
                <input type="text" id="preferred_location" placeholder="Search cities (min 2 characters)" data-url="<?= base_url('Common/get_cities') ?>" class="w-full p-3 rounded-lg border border-gray-200 focus:outline-none focus:border-transparent focus:ring-2 focus:ring-[#1D3557]/50 transition-all" autocomplete="off">
                <input type="hidden" name="preferred_locations" id="preferred_locations">
                <ul id="city_list" class="absolute z-50 w-full bg-white shadow-lg rounded-b-lg mt-1 max-h-60 overflow-y-auto hidden"></ul>
                <div id="selected_preferred_cities" class="flex flex-wrap gap-2 mt-2"></div>
                <p class="text-sm text-gray-500 mt-1">Max 5 locations can be selected</p>
            </div>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Postal Code</label>
            <input type="text" name="postal" class="w-full p-3 rounded-lg border border-gray-200 focus:outline-none focus:border-transparent focus:ring-2 focus:ring-[#1D3557]/50 transition-all">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Notice Period</label>
            <select name="notice_period" class="w-full p-3 rounded-lg border border-gray-200 focus:outline-none focus:border-transparent focus:ring-2 focus:ring-[#1D3557]/50 transition-all">
                <option value="" disabled selected>Select Notice Period</option>
                <option value="Immediate">Immediate</option>
                <option value="15 days">15 days</option>
                <option value="30 days">30 days</option>
                <option value="45 days">45 days</option>
                <option value="60 days">60 days</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Current CTC</label>
            <input type="text" name="current_ctc" class="w-full p-3 rounded-lg border border-gray-200 focus:outline-none focus:border-transparent focus:ring-2 focus:ring-[#1D3557]/50 transition-all" placeholder="e.g. 360000 for ₹3.6 Lakh">
        </div>
        <button type="submit" class="w-full px-4 py-3 bg-gradient-to-r from-[#1D3557] to-[#457B9D] text-white rounded-xl hover:shadow-lg transition-shadow">
            <i class="fas fa-save mr-2"></i>Save Job Preferences
        </button>
    </form>
</template>

<!-- Online Profiles Template -->
<template id="linksTemplate">
    <form id="linksForm" class="space-y-6 commonForm" data-section="links">
        <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
        <input type="hidden" name="form_type" value="links">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">LinkedIn Profile</label>
            <input type="url" name="linkedinProfile" class="w-full p-3 rounded-lg border border-gray-200 focus:outline-none focus:border-transparent focus:ring-2 focus:ring-[#1D3557]/50 transition-all">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Portfolio URL</label>
            <input type="url" name="portfolioUrl" class="w-full p-3 rounded-lg border border-gray-200 focus:outline-none focus:border-transparent focus:ring-2 focus:ring-[#1D3557]/50 transition-all">
        </div>
        <button type="submit" class="w-full px-4 py-3 bg-gradient-to-r from-[#1D3557] to-[#457B9D] text-white rounded-xl hover:shadow-lg transition-shadow">
            <i class="fas fa-save mr-2"></i>Save Links
        </button>
    </form>
</template>

<!-- ==================== MODALS ==================== -->

<!-- Modal Overlay -->
<div id="modalOverlay" class="fixed inset-0 bg-black/50 z-40 opacity-0 pointer-events-none transition-opacity duration-300"></div>

<!-- Modal Container (Desktop) -->
<div id="modal" class="fixed top-0 right-0 h-full w-full md:w-[90%] md:max-w-4xl bg-white shadow-[-8px_0_32px_rgba(0,0,0,0.1)] z-[99] translate-x-full opacity-0 transition-all duration-300 ease-[cubic-bezier(0.4,0,0.2,1)] overflow-y-auto">
    <!-- Desktop Navigation -->
    <div id="desktopNav" class="hidden md:block w-60 bg-white border-r border-gray-300 h-full float-left">
        <ul class="divide-y divide-gray-200">
            <li><button type="button" class="desktop-nav-btn w-full text-left px-4 py-3 text-gray-900 hover:bg-gray-100 transition-colors" data-edit="basic">Basic Details</button></li>
            <li><button type="button" class="desktop-nav-btn w-full text-left px-4 py-3 text-gray-900 hover:bg-gray-100 transition-colors" data-edit="personal">Personal Details</button></li>
            <li><button type="button" class="desktop-nav-btn w-full text-left px-4 py-3 text-gray-900 hover:bg-gray-100 transition-colors" data-edit="about">About</button></li>
            <li><button type="button" class="desktop-nav-btn w-full text-left px-4 py-3 text-gray-900 hover:bg-gray-100 transition-colors" data-edit="skills">Skills</button></li>
            <li><button type="button" class="desktop-nav-btn w-full text-left px-4 py-3 text-gray-900 hover:bg-gray-100 transition-colors" data-edit="resume">Resume</button></li>
            <li><button type="button" class="desktop-nav-btn w-full text-left px-4 py-3 text-gray-900 hover:bg-gray-100 transition-colors" data-edit="education">Education</button></li>
            <li><button type="button" class="desktop-nav-btn w-full text-left px-4 py-3 text-gray-900 hover:bg-gray-100 transition-colors" data-edit="work">Work Experience</button></li>
            <li><button type="button" class="desktop-nav-btn w-full text-left px-4 py-3 text-gray-900 hover:bg-gray-100 transition-colors" data-edit="job_preferences">Job Preferences</button></li>
            <li><button type="button" class="desktop-nav-btn w-full text-left px-4 py-3 text-gray-900 hover:bg-gray-100 transition-colors" data-edit="links">Online Profiles</button></li>
        </ul>
    </div>

    <!-- Desktop Form Content Area -->
    <div id="desktopFormContent" class="md:ml-60 p-6 relative">
        <div class="flex items-center mb-4 space-x-2">
            <button type="button" id="backBtn" class="p-2 hover:bg-gray-100 rounded-lg transition-colors md:hidden">
                <svg class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </button>
            <span id="modalHeaderLabel" class="text-2xl font-bold text-gray-900"></span>
            <button type="button" id="closeBtn" class="absolute top-4 right-4 p-2 hover:bg-gray-200 rounded-lg transition-colors">
                <i class="fas fa-times text-gray-700"></i>
            </button>
        </div>
        <div id="modalContent" class="mt-8 space-y-6"></div>
    </div>
</div>

<!-- Inner Modal (Mobile) -->
<div id="innerModal" class="fixed top-0 right-0 h-full w-full bg-white z-[99] translate-x-full transition-transform duration-300 ease-[cubic-bezier(0.4,0,0.2,1)] overflow-y-auto md:hidden">
    <div class="p-6 pb-20">
        <div class="flex justify-between items-center mb-4">
            <button type="button" id="innerBackBtn" class="flex items-center text-[#1D3557] hover:text-[#457B9D] font-medium transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Back
            </button>
            <button type="button" id="innerCloseBtn" class="p-2 hover:bg-gray-100 rounded-lg transition-colors">
                <i class="fas fa-times text-gray-700"></i>
            </button>
        </div>
        <ul class="bg-white mb-6">
            <li class="border-b border-gray-300"><button type="button" class="inner-nav-btn w-full flex items-center justify-between px-4 py-4 text-gray-900 text-base hover:bg-gray-50 transition-colors" data-edit="basic"><span>Basic Details</span><i class="fas fa-chevron-right text-gray-500"></i></button></li>
            <li class="border-b border-gray-300"><button type="button" class="inner-nav-btn w-full flex items-center justify-between px-4 py-4 text-gray-900 text-base hover:bg-gray-50 transition-colors" data-edit="personal"><span>Personal Details</span><i class="fas fa-chevron-right text-gray-500"></i></button></li>
            <li class="border-b border-gray-300"><button type="button" class="inner-nav-btn w-full flex items-center justify-between px-4 py-4 text-gray-900 text-base hover:bg-gray-50 transition-colors" data-edit="about"><span>About</span><i class="fas fa-chevron-right text-gray-500"></i></button></li>
            <li class="border-b border-gray-300"><button type="button" class="inner-nav-btn w-full flex items-center justify-between px-4 py-4 text-gray-900 text-base hover:bg-gray-50 transition-colors" data-edit="skills"><span>Skills</span><i class="fas fa-chevron-right text-gray-500"></i></button></li>
            <li class="border-b border-gray-300"><button type="button" class="inner-nav-btn w-full flex items-center justify-between px-4 py-4 text-gray-900 text-base hover:bg-gray-50 transition-colors" data-edit="resume"><span>Resume</span><i class="fas fa-chevron-right text-gray-500"></i></button></li>
            <li class="border-b border-gray-300"><button type="button" class="inner-nav-btn w-full flex items-center justify-between px-4 py-4 text-gray-900 text-base hover:bg-gray-50 transition-colors" data-edit="education"><span>Education</span><i class="fas fa-chevron-right text-gray-500"></i></button></li>
            <li class="border-b border-gray-300"><button type="button" class="inner-nav-btn w-full flex items-center justify-between px-4 py-4 text-gray-900 text-base hover:bg-gray-50 transition-colors" data-edit="work"><span>Work Experience</span><i class="fas fa-chevron-right text-gray-500"></i></button></li>
            <li class="border-b border-gray-300"><button type="button" class="inner-nav-btn w-full flex items-center justify-between px-4 py-4 text-gray-900 text-base hover:bg-gray-50 transition-colors" data-edit="job_preferences"><span>Job Preferences</span><i class="fas fa-chevron-right text-gray-500"></i></button></li>
            <li><button type="button" class="inner-nav-btn w-full flex items-center justify-between px-4 py-4 text-gray-900 text-base hover:bg-gray-50 transition-colors" data-edit="links"><span>Online Profiles</span><i class="fas fa-chevron-right text-gray-500"></i></button></li>
        </ul>
    </div>
</div>

<!-- ==================== AUTO-COMPLETE WIDGET SCRIPT ==================== -->
<script src="<?= base_url('assets/frontend/js/auto-complete-widget.js') ?>" defer></script>
<script>
(function() {
    'use strict';

    // Global AJAX loader
    let activeRequests = 0;
    $(document).ajaxSend(() => {
        activeRequests++;
        $('#globalLoader').removeClass('hidden');
    });
    $(document).ajaxComplete(() => {
        activeRequests--;
        if (activeRequests <= 0) {
            $('#globalLoader').addClass('hidden');
        }
    });

    // Toast notification
    function showToast(message, type = 'info') {
        const toast = document.createElement('div');
        toast.className = `fixed bottom-4 right-4 z-[9999] px-6 py-3 rounded-lg text-white shadow-lg transition-transform transform translate-x-0 ${
            type === 'success' ? 'bg-green-500' : type === 'error' ? 'bg-red-500' : 'bg-blue-500'
        }`;
        toast.textContent = message;
        document.body.appendChild(toast);
        setTimeout(() => {
            toast.style.transform = 'translateX(150%)';
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }

    // ==================== AUTOCOMPLETE INITIALIZER ====================
    function initAutoCompleteWidgets() {
        if (typeof AutoCompleteWidget === 'undefined') {
            console.warn('AutoCompleteWidget not loaded yet, retrying in 200ms');
            setTimeout(initAutoCompleteWidgets, 200);
            return;
        }

        // Job Profile
        const jobInput = document.querySelector('#job_profile_input');
        if (jobInput && !jobInput.hasAttribute('data-widget-initialized')) {
            try {
                new AutoCompleteWidget({
                    inputSelector: '#job_profile_input',
                    hiddenSelector: '#job_profile_id',
                    listSelector: '#job_profile_list',
                    apiUrl: BASE_URL + 'Common/get_search_data?type=job_profile',
                    minChars: 1,
                    multiSelect: false,
                    maxResults: 5
                });
                jobInput.setAttribute('data-widget-initialized', 'true');
            } catch (e) { console.error('Job Profile autocomplete error:', e); }
        }

        // City (single)
        const cityInput = document.querySelector('#city_input');
        if (cityInput && !cityInput.hasAttribute('data-widget-initialized')) {
            try {
                new AutoCompleteWidget({
                    inputSelector: '#city_input',
                    hiddenSelector: '#city_id',
                    listSelector: '#city_list',
                    apiUrl: BASE_URL + 'Common/get_cities',
                    minChars: 2,
                    multiSelect: false,
                    maxResults: 10
                });
                cityInput.setAttribute('data-widget-initialized', 'true');
            } catch (e) { console.error('City autocomplete error:', e); }
        }

        // Preferred Location (multi)
        const prefInput = document.querySelector('#preferred_location');
        if (prefInput && !prefInput.hasAttribute('data-widget-initialized')) {
            try {
                new AutoCompleteWidget({
                    inputSelector: '#preferred_location',
                    hiddenSelector: '#preferred_locations',
                    listSelector: '#city_list',
                    apiUrl: BASE_URL + 'Common/get_cities',
                    minChars: 2,
                    multiSelect: true,
                    maxResults: 10,
                    maxSelections: 5
                });
                prefInput.setAttribute('data-widget-initialized', 'true');
            } catch (e) { console.error('Preferred Location autocomplete error:', e); }
        }

        // Functional Area
        const funcInput = document.querySelector('#functional_input');
        if (funcInput && !funcInput.hasAttribute('data-widget-initialized')) {
            try {
                new AutoCompleteWidget({
                    inputSelector: '#functional_input',
                    hiddenSelector: '#functional_id',
                    listSelector: '#functional_list',
                    apiUrl: BASE_URL + 'Common/get_search_data?type=functional_area',
                    minChars: 1,
                    multiSelect: false,
                    maxResults: 5
                });
                funcInput.setAttribute('data-widget-initialized', 'true');
            } catch (e) { console.error('Functional Area autocomplete error:', e); }
        }

        // Industry
        const industryInput = document.querySelector('#industry_input');
        if (industryInput && !industryInput.hasAttribute('data-widget-initialized')) {
            try {
                new AutoCompleteWidget({
                    inputSelector: '#industry_input',
                    hiddenSelector: '#industry_id',
                    listSelector: '#industry_list',
                    apiUrl: BASE_URL + 'Common/get_search_data?type=industry',
                    minChars: 1,
                    multiSelect: false,
                    maxResults: 5
                });
                industryInput.setAttribute('data-widget-initialized', 'true');
            } catch (e) { console.error('Industry autocomplete error:', e); }
        }
    }

    // ==================== MODAL ELEMENTS & HELPERS ====================
    const modal = document.getElementById('modal');
    const modalOverlay = document.getElementById('modalOverlay');
    const modalContent = document.getElementById('modalContent');
    const modalHeaderLabel = document.getElementById('modalHeaderLabel');
    const innerModal = document.getElementById('innerModal');

    const templateMap = {
        basic: 'basicDetailsTemplate',
        personal: 'personalDetailsTemplate',
        about: 'aboutTemplate',
        skills: 'skillsTemplate',
        resume: 'resumeTemplate',
        education: 'educationTemplate',
        work: 'workExperienceTemplate',
        job_preferences: 'jobPreferencesTemplate',
        links: 'linksTemplate'
    };

    const isDesktop = () => window.innerWidth >= 768;

    function updateModalHeaderLabel(section) {
        modalHeaderLabel.textContent = "Edit " + section.charAt(0).toUpperCase() + section.slice(1);
    }

    function loadTemplateIntoModal(section) {
        const templateId = templateMap[section] || templateMap['basic'];
        modalContent.innerHTML = document.getElementById(templateId).innerHTML;
        initAutoCompleteWidgets();
    }

    // Open modal
    function openModal(section, recordId = null) {
        updateModalHeaderLabel(section);
        loadTemplateIntoModal(section);

        if (recordId && (section === 'work' || section === 'education')) {
            fetchRecordDetails(section, recordId);
        }

        modal.classList.add('translate-x-0', 'opacity-100');
        modal.classList.remove('translate-x-full', 'opacity-0');
        modalOverlay.classList.add('opacity-100', 'pointer-events-auto');
        modalOverlay.classList.remove('opacity-0', 'pointer-events-none');

        document.querySelectorAll('.desktop-nav-btn').forEach(btn => {
            btn.classList.toggle('bg-gray-100', btn.dataset.edit === section);
        });
        document.querySelectorAll('.inner-nav-btn').forEach(btn => {
            btn.classList.toggle('bg-gray-50', btn.dataset.edit === section);
        });

        fetchCandidateDetails();
    }

    // Close modal
    function closeModal() {
        modal.classList.add('translate-x-full', 'opacity-0');
        modal.classList.remove('translate-x-0', 'opacity-100');
        modalOverlay.classList.add('opacity-0', 'pointer-events-none');
        modalOverlay.classList.remove('opacity-100', 'pointer-events-auto');
        innerModal.classList.add('translate-x-full');
        innerModal.classList.remove('translate-x-0');
    }

    // ==================== NAVIGATION BINDING ====================
    function bindDesktopNav() {
        document.querySelectorAll('.desktop-nav-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const section = btn.dataset.edit;
                updateModalHeaderLabel(section);
                modalContent.innerHTML = document.getElementById(templateMap[section] || templateMap['basic']).innerHTML;
                initAutoCompleteWidgets();
                document.querySelectorAll('.desktop-nav-btn').forEach(b => b.classList.remove('bg-gray-100'));
                btn.classList.add('bg-gray-100');
            });
        });
    }

    function bindInnerModal() {
        if (!isDesktop()) {
            $('#backBtn, #innerBackBtn, #innerCloseBtn').off('click');
            $('.inner-nav-btn').off('click');

            $('#backBtn').on('click', () => {
                innerModal.classList.add('translate-x-0');
                innerModal.classList.remove('translate-x-full');
            });
            $('#innerBackBtn').on('click', () => {
                innerModal.classList.add('translate-x-full');
                innerModal.classList.remove('translate-x-0');
            });
            $('#innerCloseBtn').on('click', () => {
                innerModal.classList.add('translate-x-full');
                innerModal.classList.remove('translate-x-0');
                closeModal();
            });
            $('.inner-nav-btn').on('click', function () {
                const section = $(this).data('edit');
                updateModalHeaderLabel(section);
                modalContent.innerHTML = document.getElementById(templateMap[section] || templateMap['basic']).innerHTML;
                initAutoCompleteWidgets();
                innerModal.classList.add('translate-x-full');
                innerModal.classList.remove('translate-x-0');
            });
        }
    }

    // ==================== AJAX SUCCESS HANDLER ====================
    function handleAjaxSuccess(response, section = null) {
        if (response.csrf_token) {
            updateCSRFToken(response.csrf_token, response.csrf_name || getCSRFName());
        }
        if (response.success) {
            showToast('✅ ' + response.message, 'success');
            fetchCandidateDetails();
            if (section === 'work' || section === 'education') {
                fetchDataDisplay(section);
            }
            closeModal();
        } else {
            showToast('❌ ' + response.message, 'error');
        }
    }

   // ==================== FORM SUBMISSION ====================
	$(document).on('submit', '.commonForm', function (e) {
		e.preventDefault();
		const $form = $(this);
		const section = $form.data('section');
		const $btn = $form.find('button[type="submit"]');
		const orig = $btn.html();

		if (!validateForm($form)) return;

		$btn.prop('disabled', true).html(`
			<div class="inline-flex items-center gap-2">
				<div class="w-5 h-5 border-2 border-white border-t-transparent rounded-full animate-spin"></div>
				Saving...
			</div>
		`);

		// --- Determine the correct endpoint ---
		const formType = $form.find('[name="form_type"]').val();
		let url = BASE_URL + 'candidate/Profile/save_detail';

		if (formType === 'skills') {
			url = BASE_URL + 'candidate/Profile/save_skills';
		} else if (formType === 'work') {
			url = BASE_URL + 'candidate/Profile/save_work';
		} else if (formType === 'education') {
			url = BASE_URL + 'candidate/Profile/save_education';
		}
		// -----------------------------------

		const formData = new FormData(this);
		formData.append(getCSRFName(), getCSRFToken());

		$.ajax({
			url: url,   // ← use the correct URL here
			type: 'POST',
			data: formData,
			contentType: false,
			processData: false,
			dataType: 'json',
			success: (response) => handleAjaxSuccess(response, section),
			error: (xhr) => {
				const errorMsg = xhr.responseJSON?.message || 'Network error';
				showToast('❌ ' + errorMsg, 'error');
				if (xhr.status === 403 && errorMsg.includes('CSRF')) {
					fetchCandidateDetails();
					showToast('🔄 Security token refreshed. Try again.', 'info');
				}
			},
			complete: () => {
				$btn.prop('disabled', false).html(orig);
			}
		});
	});
    function validateForm($form) {
        const formType = $form.find('input[name="form_type"]').val();
        return true;
    }

    // ==================== FETCH CANDIDATE DETAILS ====================
    function fetchCandidateDetails() {
        $.ajax({
            url: BASE_URL + 'candidate/Profile/get_candidate_details',
            type: 'GET',
            dataType: 'json',
            success: (response) => {
                if (!response.success) {
                    showToast('❌ Failed to fetch details.', 'error');
                    return;
                }
                const data = response.data;
                if (response.csrf_token) {
                    updateCSRFToken(response.csrf_token, response.csrf_name || getCSRFName());
                }

                populateFormFields(data);
                renderCandidateProfile(data);
            },
            error: () => showToast('❌ Error fetching data.', 'error')
        });
    }

    function populateFormFields(data) {
        $('input[name="first_name"]').val(data.name);
        $('input[name="last_name"]').val(data.last_name);
        $('input[name="designation"]').val(data.designations);
        $('input[name="dob"]').val(data.dob);
        $('select[name="gender"]').val(data.gender);
        $('select[name="work_status"]').val(data.work_status).trigger('change');
        $('input[name="experience_years"]').val(data.total_experience_years);
        $('input[name="experience_months"]').val(data.total_experience_months);
        $('textarea[name="resume_headline"]').val(data.resume_headline);
        $('textarea[name="about"]').val(data.about);
        $('textarea[name="objective"]').val(data.objective);
        $('input[name="linkedinProfile"]').val(data.linkedinProfile);
        $('input[name="portfolioUrl"]').val(data.portfolioUrl);
        $('input[name="email"]').val(data.email);
        $('input[name="mobile"]').val(data.mobile);
        $('input[name="city_name"]').val(data.city_name);
        $('input[name="city_id"]').val(data.city_id);
        $('input[name="functional_name"]').val(data.functional_area);
        $('input[name="functional_id"]').val(data.functional_id);
        $('input[name="industry_name"]').val(data.industry_name);
        $('input[name="industry_id"]').val(data.industry_id);
        $('input[name="postal"]').val(data.postal);
        $('select[name="notice_period"]').val(data.notice_period);
        $('input[name="current_ctc"]').val(data.current_ctc);
        $('textarea[name="address"]').val(data.address);
        $('input[name="placeOfBirth"]').val(data.placeOfBirth);

        // Skills
        if (data.skills) {
            const container = $('.border.border-gray-200.rounded-lg.p-4');
            container.find('.tags-display').empty();
            $('#skillsHiddenInput').val('');
            (typeof data.skills === 'string' ? data.skills.split(',') : data.skills || [])
                .map(s => typeof s === 'string' ? s.trim() : String(s))
                .filter(Boolean)
                .forEach(skill => addSkillTag(skill, container));
        }

        // ========== FIXED: Preferred Locations (handles both string and array) ==========
        if (data.preferred_location_ids) {
            // Normalize ids to array
            let ids = data.preferred_location_ids;
            if (typeof ids === 'string') {
                ids = ids.split(',').map(id => id.trim()).filter(Boolean);
            } else if (Array.isArray(ids)) {
                ids = ids.map(id => String(id).trim()).filter(Boolean);
            } else {
                ids = [];
            }

            // Normalize names to array
            let names = data.preferred_location_names || [];
            if (typeof names === 'string') {
                names = names.split(',').map(n => n.trim()).filter(Boolean);
            } else if (!Array.isArray(names)) {
                names = [];
            }

            const selectedContainer = $('#selected_preferred_cities');
            selectedContainer.empty();

            ids.forEach((id, index) => {
                const name = names[index] ? names[index].trim() : '';
                if (name) {
                    const tag = `
                        <span class="tag-item bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm flex items-center">
                            <span class="city-name">${name}</span>
                            <button type="button" class="remove-city ml-2 text-blue-600 hover:text-blue-800" data-id="${id}">&times;</button>
                        </span>
                    `;
                    selectedContainer.append(tag);
                }
            });

            $('#preferred_locations').val(ids.join(','));
        }
    }

    function renderCandidateProfile(data) {
        $('#profileName').text((data.name || '') + ' ' + (data.last_name || ''));
        $('#profileTitle').text(data.designations || 'No Title');
        if (data.logo) {
            $('#profileImage').attr('src', BASE_URL + data.logo + '?t=' + Date.now());
        } else {
            $('#profileImage').attr('src', '#');
        }

        $('#basicName').text((data.name || '') + ' ' + (data.last_name || ''));
        $('#basicDob').text(data.dob || '');
        $('#basicGender').text(data.gender || '');

        let emailHtml = `<i class="fas fa-envelope mr-1 text-gray-500"></i> ${data.email || '[Not Set]'} `;
        emailHtml += data.is_verified == 1
            ? '<span class="text-emerald-600 font-semibold"><i class="fas fa-check-circle mr-1"></i>Verified</span>'
            : '<span class="text-amber-600 font-semibold"><i class="fas fa-exclamation-circle mr-1"></i>Unverified</span>';
        $('#personalEmail').html(emailHtml);

        $('#personalMobile').text(data.mobile || '');
        $('#candidateAddress').text(data.address || '[Not Set]');
        $('#candidateCity').text(data.city_name || '[Not Set]');
        $('#candidateWorkStatus').text(data.work_status || '[Not Set]');
        $('#candidateExperience').text(
            (data.total_experience_years || 0) + ' Years ' +
            (data.total_experience_months || 0) + ' Months'
        );
        $('#candidateIndustry').text(data.industry_name || '[Not Set]');
        $('#candidateFunctional').text(data.functional_area || '[Not Set]');
        $('#resumeHeadline').text(data.resume_headline || '');
        $('#aboutText').text(data.about || '');
        $('#candidateObjective').text(data.objective || '[Not Set]');

        // Skills as tags
        let skillsHtml = '';
        if (data.skills) {
            const skills = typeof data.skills === 'string' ? data.skills.split(',') : data.skills;
            skillsHtml = skills.map(s => `<span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm">${s.trim()}</span>`).join(' ');
        }
        $('#skillsList').html(skillsHtml || '[Not Set]');

        // ========== FIXED: Preferred locations display ==========
        let locHtml = '';
        if (data.preferred_location_names) {
            let names = data.preferred_location_names;
            if (typeof names === 'string') {
                names = names.split(',').map(n => n.trim()).filter(Boolean);
            } else if (Array.isArray(names)) {
                names = names.map(n => String(n).trim()).filter(Boolean);
            } else {
                names = [];
            }
            locHtml = names.map(l => `<span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm">${l}</span>`).join(' ');
        }
        $('#jobPreferredLocation').html(locHtml || '[Not Set]');

        $('#jobPostal').text(data.postal || '[Not Set]');
        $('#jobNoticePeriod').text(data.notice_period || '[Not Set]');
        $('#jobCurrentCTC').text(data.current_ctc ? parseInt(data.current_ctc).toLocaleString('en-IN') : '[Not Set]');

        $('#candidateLinkedIn').html(data.linkedinProfile ? `<a href="${data.linkedinProfile}" target="_blank" class="text-[#1D3557] hover:underline">${data.linkedinProfile}</a>` : '[Not Set]');
        $('#candidatePortfolio').html(data.portfolioUrl ? `<a href="${data.portfolioUrl}" target="_blank" class="text-[#1D3557] hover:underline">${data.portfolioUrl}</a>` : '[Not Set]');

        $('#emailVerificationStatus').html(data.is_verified == 1
            ? `<div class="bg-emerald-500/90 text-white px-3 py-1 rounded-full flex items-center gap-1 shadow-md"><i class="fas fa-envelope text-xs"></i><span class="text-xs font-medium">Verified</span></div>`
            : `<div class="bg-amber-500/90 text-white px-3 py-1 rounded-full flex items-center gap-1 shadow-md"><i class="fas fa-exclamation-triangle text-xs"></i><span class="text-xs font-medium">Unverified</span><button class="ml-1 underline hover:opacity-80 resend-email">Resend</button></div>`
        );

        $('#profileStatusBadge').html(data.status === 'active'
            ? `<div class="bg-green-500/90 text-white px-3 py-1 rounded-full flex items-center gap-1 shadow-md"><i class="fas fa-check-circle text-xs"></i><span class="text-xs font-medium">Active</span></div>`
            : `<div class="bg-red-500/90 text-white px-3 py-1 rounded-full flex items-center gap-1 shadow-md"><i class="fas fa-exclamation-circle text-xs"></i><span class="text-xs font-medium">Inactive</span></div>`
        );

        if (data.resume) {
            $('#resumeFileContainer').html(`
                <div class="p-4 bg-white border border-gray-200 rounded shadow-sm flex flex-col sm:flex-row items-start sm:items-center justify-between">
                    <a href="${BASE_URL + data.resume}" target="_blank" class="text-blue-600 underline mb-2 sm:mb-0">View Resume</a>
                    <button id="deleteResumeBtn" class="ml-0 sm:ml-4 px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600 transition">Delete</button>
                </div>
            `).show();
        } else {
            $('#resumeFileContainer').html(`
                <div class="p-4 bg-gray-100 border border-dashed border-gray-300 rounded text-center">
                    <p class="text-gray-600">No Resume Uploaded</p>
                    <button data-edit="resume" class="mt-2 px-3 py-1 bg-blue-500 text-white rounded hover:bg-blue-600 transition">Upload Resume</button>
                </div>
            `).show();
        }
    }

    // ==================== FETCH WORK/EDUCATION DISPLAY ====================
    function fetchDataDisplay(section) {
        $.ajax({
            url: BASE_URL + `candidate/Profile/get_${section}`,
            type: 'GET',
            dataType: 'json',
            success: (response) => {
                const container = section === 'work' ? $('#workExperienceDisplay') : $('#educationDisplay');
                container.empty();
                if (response.success && response.data.length > 0) {
                    response.data.forEach(record => {
                        const html = generateHTML(section, record);
                        container.append(html);
                    });
                } else {
                    container.append(`<p class="text-gray-500">No ${section} records available.</p>`);
                }
                if (response.csrf_token) {
                    updateCSRFToken(response.csrf_token, response.csrf_name || getCSRFName());
                }
            },
            error: () => showToast(`❌ Error fetching ${section} data.`, 'error')
        });
    }

    function generateHTML(section, record) {
        if (section === 'work') {
            return `
                <div class="flex justify-between items-start gap-4 p-4 border border-gray-200 rounded-lg mb-4 bg-gray-50">
                    <div class="flex-1">
                        <p><strong>Company:</strong> ${record.employer_name}</p>
                        <p><strong>Role:</strong> ${record.job_title}</p>
                        <p><strong>Duration:</strong> ${record.start_date} - ${record.end_date || 'Present'}</p>
                        <p><strong>Job Type:</strong> ${record.job_type || 'N/A'}</p>
                        <p><strong>Location:</strong> ${record.work_location || 'N/A'}</p>
                        ${record.responsibilities ? `<p><strong>Responsibilities:</strong> ${record.responsibilities}</p>` : ''}
                        ${record.achievements ? `<p><strong>Achievements:</strong> ${record.achievements}</p>` : ''}
                    </div>
                    <div class="flex gap-2">
                        <button data-edit="work" data-id="${record.id}" class="bg-[#457B9D] text-white rounded-full w-10 h-10 hover:bg-[#1D3557] transition-colors" title="Edit">✏️</button>
                        <button data-delete="work" data-id="${record.id}" class="bg-[#457B9D] text-white rounded-full w-10 h-10 hover:bg-[#1D3557] transition-colors" title="Delete">🗑️</button>
                    </div>
                </div>
            `;
        } else if (section === 'education') {
            return `
                <div class="flex justify-between items-start gap-4 p-4 border border-gray-200 rounded-lg mb-4 bg-gray-50">
                    <div class="flex-1">
                        <p><strong>Degree:</strong> ${record.degreeName}</p>
                        <p><strong>Field of Study:</strong> ${record.fieldOfStudy || 'N/A'}</p>
                        <p><strong>Institution:</strong> ${record.institutionName}</p>
                        <p><strong>Duration:</strong> ${record.startYear} - ${record.endYear}</p>
                        ${record.honors ? `<p><strong>Honors:</strong> ${record.honors}</p>` : ''}
                    </div>
                    <div class="flex gap-2">
                        <button data-edit="education" data-id="${record.id}" class="bg-[#457B9D] text-white rounded-full w-10 h-10 hover:bg-[#1D3557] transition-colors" title="Edit">✏️</button>
                        <button data-delete="education" data-id="${record.id}" class="bg-[#457B9D] text-white rounded-full w-10 h-10 hover:bg-[#1D3557] transition-colors" title="Delete">🗑️</button>
                    </div>
                </div>
            `;
        }
        return '';
    }

    function fetchRecordDetails(section, recordId) {
        const urls = {
            work: BASE_URL + 'candidate/Profile/get_work_experience_by_id',
            education: BASE_URL + 'candidate/Profile/get_education_by_id'
        };
        const formSelectors = {
            work: '#workExperienceForm',
            education: '#educationForm'
        };
        if (!urls[section]) return;
        $.ajax({
            url: urls[section],
            type: 'GET',
            data: { id: recordId },
            dataType: 'json',
            success: (response) => {
                if (response.success) {
                    const $form = $(formSelectors[section]);
                    const data = response.data;
                    for (let key in data) {
                        $form.find(`[name="${key}"]`).val(data[key]);
                    }
                    $form.find('input[name="id"]').val(data.id);
                    if (data.is_current) {
                        $form.find('[name="is_current"]').prop('checked', true);
                    }
                    if (response.csrf_token) {
                        updateCSRFToken(response.csrf_token, response.csrf_name || getCSRFName());
                    }
                } else {
                    showToast(`❌ Failed to load ${section} details.`, 'error');
                }
            },
            error: () => showToast(`❌ Error fetching ${section} details.`, 'error')
        });
    }

    // ==================== SKILLS TAGS ====================
    function addSkillTag(skill, container) {
        container.find('.tags-display').append(`
            <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm flex items-center">
                <span class="tag-text">${skill}</span>
                <button type="button" class="ml-2 text-blue-600 hover:text-blue-800">&times;</button>
            </span>
        `);
        updateSkillsHidden();
    }

    function updateSkillsHidden() {
        const skills = $('.tag-text').map(function() { return $(this).text().trim(); }).get().join(',');
        $('#skillsHiddenInput').val(skills);
    }

    $(document).on('keydown', '.tag-input', function (e) {
        if (e.which === 13 || e.which === 188) {
            e.preventDefault();
            let input = $(this);
            let val = input.val().trim();
            if (val.endsWith(',')) val = val.slice(0, -1).trim();
            if (val) {
                if (val.length > 30) {
                    showToast('Skill must be 30 characters or less.', 'error');
                } else if ($('.tag-item').length >= 5) {
                    showToast('You can only add up to 5 skills.', 'error');
                } else {
                    addSkillTag(val, input.closest('.border.border-gray-200.rounded-lg.p-4'));
                }
            }
            input.val('');
        }
    });

    $(document).on('click', '.tag-remove, .bg-blue-100 button', function () {
        $(this).closest('.bg-blue-100').remove();
        updateSkillsHidden();
    });

    // ==================== DELETE HANDLER ====================
    $(document).on('click', '[data-delete]', function () {
        const section = $(this).attr('data-delete');
        const recordId = $(this).attr('data-id');
        if (confirm(`Are you sure you want to delete this ${section} record?`)) {
            $.ajax({
                url: BASE_URL + `candidate/Profile/delete_${section}`,
                type: 'POST',
                data: { id: recordId, [getCSRFName()]: getCSRFToken() },
                dataType: 'json',
                success: (response) => {
                    if (response.success) {
                        showToast('✅ Record deleted successfully.', 'success');
                        fetchDataDisplay(section);
                        if (response.csrf_token) updateCSRFToken(response.csrf_token, response.csrf_name || getCSRFName());
                    } else {
                        showToast('❌ ' + response.message, 'error');
                    }
                },
                error: () => showToast(`❌ Error deleting ${section} record.`, 'error')
            });
        }
    });

    // ==================== IMAGE UPLOAD ====================
    $(document).on('change', '#fileInput', function () {
        const file = this.files[0];
        if (!file) return;
        const $loader = $('#uploadLoader').removeClass('hidden');
        const formData = new FormData();
        formData.append('image', file);
        formData.append(getCSRFName(), getCSRFToken());

        $.ajax({
            url: BASE_URL + 'candidate/Profile/upload_image',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            dataType: 'json',
            xhr: () => {
                const xhr = new XMLHttpRequest();
                xhr.upload.addEventListener('progress', evt => {
                    if (evt.lengthComputable) {
                        const percent = Math.round((evt.loaded / evt.total) * 100);
                        $('#progressBar').css('width', percent + '%');
                        $('#progressPercent').text(percent + '%');
                    }
                });
                return xhr;
            },
            success: (response) => {
                $loader.addClass('hidden');
                if (response.success) {
                    showToast('✅ Image updated successfully!', 'success');
                    $('#profileImage').attr('src', BASE_URL + response.image_url + '?t=' + Date.now());
                    if (response.csrf_token) updateCSRFToken(response.csrf_token, response.csrf_name || getCSRFName());
                } else {
                    showToast('❌ ' + response.error_msg, 'error');
                }
            },
            error: () => {
                $loader.addClass('hidden');
                showToast('❌ Upload failed!', 'error');
            }
        });
    });

    // ==================== RESUME DELETE ====================
    $(document).on('click', '#deleteResumeBtn', function () {
        const resumeFile = $('#resumeFileLink').attr('href').replace(BASE_URL, '');
        $.ajax({
            url: BASE_URL + 'candidate/Profile/deleteResume',
            type: 'POST',
            data: { resume: resumeFile, [getCSRFName()]: getCSRFToken() },
            dataType: 'json',
            success: (response) => {
                if (response.success) {
                    showToast('✅ Resume deleted successfully.', 'success');
                    fetchCandidateDetails();
                    if (response.csrf_token) updateCSRFToken(response.csrf_token, response.csrf_name || getCSRFName());
                } else {
                    showToast('❌ Failed to delete resume.', 'error');
                }
            },
            error: () => showToast('❌ Error deleting resume.', 'error')
        });
    });

    // ==================== REMOVE CITY FROM PREFERRED ====================
    $(document).on('click', '.remove-city', function () {
        const cityId = $(this).data('id');
        $(this).closest('.tag-item').remove();
        const remainingIds = $('#selected_preferred_cities .remove-city').map(function() { return $(this).data('id'); }).get().join(',');
        $('#preferred_locations').val(remainingIds);
    });

    // ==================== EMAIL VERIFICATION ====================
    $(document).on('click', '.resend-email', function (e) {
        e.preventDefault();
        showToast('Resending verification email...', 'info');
        $.ajax({
            url: BASE_URL + 'auth/Verifications/ajaxInitiateEmailVerification',
            type: 'GET',
            dataType: 'json',
            success: (response) => {
                if (response.status === 'success') {
                    showToast('✅ ' + response.message, 'success');
                    if (response.csrf_token) updateCSRFToken(response.csrf_token, response.csrf_name || getCSRFName());
                } else {
                    showToast('❌ ' + response.message, 'error');
                }
            },
            error: () => showToast('❌ Error sending verification', 'error')
        });
    });

    // ==================== MUTATION OBSERVER FOR AUTOCOMPLETE ====================
    const observer = new MutationObserver(() => {
        initAutoCompleteWidgets();
    });
    if (modalContent) observer.observe(modalContent, { childList: true, subtree: true });

    // ==================== EVENT LISTENERS ====================
    $(document).on('click', '[data-edit]', function () {
        const section = $(this).data('edit');
        const recordId = $(this).data('id');
        openModal(section, recordId);
    });

    $(document).on('click', '[data-add]', function () {
        const section = $(this).data('add');
        openModal(section);
    });

    $('#closeBtn, #modalOverlay').on('click', closeModal);

    $(document).on('change', 'select[name="work_status"]', function () {
        const $expFields = $('input[name="experience_years"], input[name="experience_months"]').closest('.grid-cols-2');
        if ($(this).val() === 'Experience') {
            $expFields.show();
        } else {
            $expFields.hide();
            $('input[name="experience_years"], input[name="experience_months"]').val('');
        }
    });

    // ==================== INITIALIZE ====================
    window.addEventListener('load', () => {
        fetchCandidateDetails();
        fetchDataDisplay('work');
        fetchDataDisplay('education');
        openModal('basic');
        if (isDesktop()) {
            bindDesktopNav();
        } else {
            bindInnerModal();
        }
        $('select[name="work_status"]').trigger('change');
    });

    let resizeTimer;
    window.addEventListener('resize', () => {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(() => {
            if (!isDesktop()) bindInnerModal();
        }, 250);
    });
})();
</script>
