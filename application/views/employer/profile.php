
<!-- Global AJAX Loader -->
<div id="globalLoader" class="fixed inset-0 bg-black/50 z-[9999] flex items-center justify-center hidden">
    <div class="w-10 h-10 border-4 border-gray-200 border-t-[#1D3557] rounded-full animate-spin"></div>
</div>

<!-- Main Company Profile Card -->
<div class="max-w-7xl mx-auto my-8 bg-white rounded-xl shadow-xl overflow-hidden">
    <!-- Banner Section -->
    <div class="relative w-full h-40 bg-gradient-to-br from-[#1D3557] to-[#457B9D] rounded-t-lg mb-12">
        <div class="absolute inset-0 bg-gradient-to-r from-white/20 to-transparent"></div>

        <!-- Company Logo Section -->
        <div class="absolute -bottom-12 left-4">
            <div class="relative group">
                <img id="logo-preview" src="#"
                     class="w-24 h-24 rounded-full border-4 border-white shadow-[0_10px_15px_-3px_rgba(29,53,87,0.2)] group-hover:scale-105 transition-transform"
                     alt="Company Logo">
                <!-- Logo Upload -->
                <label for="logoUpload" class="absolute bottom-2 right-2 bg-black/50 rounded-full p-1.5 cursor-pointer hover:bg-black/70 hover:scale-110 transition-all">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-white">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 015.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 00-1.134-.175 2.31 2.31 0 01-1.64-1.055l-.822-1.316a2.192 2.192 0 00-1.736-1.039 48.774 48.774 0 00-5.232 0 2.192 2.192 0 00-1.736 1.039l-.821 1.316z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0zM18.75 10.5h.008v.008h-.008V10.5z" />
                    </svg>
                    <input type="file" id="logoUpload" class="hidden" accept="image/*" name="logo">
                </label>
            </div>
        </div>

        <!-- Status Messages -->
        <div id="uploadStatus" class="text-center mt-2 text-sm text-gray-700"></div>

        <!-- Company Status Badges -->
        <div class="absolute top-4 right-4 flex flex-wrap gap-2 items-center">
            <div class="bg-purple-500/90 text-white px-3 py-1 rounded-full flex items-center gap-1 shadow-md" title="Membership Type">
                <div class="w-4 h-4 border-2 border-white rounded-full flex items-center justify-center">
                    <span class="text-[10px] font-bold">★</span>
                </div>
                <span class="text-xs font-medium" id="membershipType">Premium</span>
            </div>
            <div class="bg-blue-500/90 text-white px-3 py-1 rounded-full flex items-center gap-1 shadow-md" title="Profile Completion">
                <div class="w-4 h-4 border-2 border-white rounded-full flex items-center justify-center">
                    <span class="text-[10px] font-bold">85%</span>
                </div>
                <span class="text-xs font-medium">Complete</span>
            </div>
        </div>

        <!-- Verification Status -->
        <div class="absolute top-4 left-4" id="emailVerificationStatus"></div>

        <!-- Upload Loader -->
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

    <!-- Company Header -->
    <div class="flex flex-col md:flex-row justify-between items-start mb-6 px-6">
        <div>
            <h1 id="companyNameHeader" class="text-3xl font-bold text-gray-900 mb-1"></h1>
            <p id="companyIndustry" class="text-lg text-[#1D3557] font-medium"></p>
        </div>
        <button type="button" class="mt-4 md:mt-0 px-6 py-2.5 bg-gradient-to-r from-[#1D3557] to-[#457B9D] text-white rounded-xl shadow-lg hover:shadow-xl transition-shadow" data-edit="company">
            <i class="fas fa-edit mr-2"></i>Edit Company Profile
        </button>
    </div>
</div>

<!-- Company Basic Display -->
<div class="max-w-7xl mx-auto my-8 bg-white rounded-xl shadow-md p-6" id="section-companyBasic" data-section="companyBasic">
    <div class="flex justify-between items-center border-b border-gray-200 pb-3 mb-4">
        <h3 class="text-xl font-semibold text-[#1D3557]"><i class="fas fa-building mr-2"></i>Company Basic</h3>
        <button type="button" data-edit="companyBasic" title="Edit Company Basic" class="text-[#457B9D] hover:text-[#1D3557] transition-colors">
            <i class="fas fa-edit"></i>
        </button>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <p><strong>Company Name:</strong> <span id="companyName"></span></p>
        <p><strong>Founded:</strong> <span id="companyFounded"></span></p>
        <p><strong>Company Size:</strong> <span id="companySize"></span></p>
        <p><strong>Industry:</strong> <span id="industry_name"></span></p>
        <p><strong>Expertise & Specialization:</strong></p>
        <div id="expertiseTagsDisplay" class="flex flex-wrap gap-2 mt-1"></div>
    </div>
</div>

<!-- Company Details Display -->
<div class="max-w-7xl mx-auto my-8 bg-white rounded-xl shadow-md p-6" id="section-companyExtra" data-section="companyExtra">
    <div class="flex justify-between items-center border-b border-gray-200 pb-3 mb-4">
        <h3 class="text-xl font-semibold text-[#1D3557]"><i class="fas fa-info-circle mr-2"></i>Company Details</h3>
        <button type="button" data-edit="companyExtra" title="Edit Company Details" class="text-[#457B9D] hover:text-[#1D3557] transition-colors">
            <i class="fas fa-edit"></i>
        </button>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <p><strong>Type:</strong> <span id="companyType"></span></p>
        <p><strong>Recruiter Type:</strong> <span id="recruiterType"></span></p>
        <p><strong>Website:</strong> <span id="companyWebsite"></span></p>
        <p><strong>Location:</strong> <span id="city_name"></span></p>
        <div class="col-span-full">
            <p><strong>Address:</strong></p>
            <p id="companyAddress" class="text-gray-700"></p>
        </div>
    </div>
</div>

<!-- About Company Display -->
<div class="max-w-7xl mx-auto my-8 bg-white rounded-xl shadow-md p-6" id="section-aboutCompany" data-section="aboutCompany">
    <div class="flex justify-between items-center border-b border-gray-200 pb-3 mb-4">
        <h3 class="text-xl font-semibold text-[#1D3557]"><i class="fas fa-building mr-2"></i>About Company</h3>
        <button type="button" data-edit="aboutCompany" title="Edit About Company" class="text-[#457B9D] hover:text-[#1D3557] transition-colors">
            <i class="fas fa-edit"></i>
        </button>
    </div>
    <div>
        <p id="aboutCompany" class="text-gray-700"></p>
    </div>
</div>

<!-- Contact Basic Display -->
<div class="max-w-7xl mx-auto my-8 bg-white rounded-xl shadow-md p-6" id="section-contactBasic" data-section="contactBasic">
    <div class="flex justify-between items-center border-b border-gray-200 pb-3 mb-4">
        <h3 class="text-xl font-semibold text-[#1D3557]"><i class="fas fa-user mr-2"></i>Contact Basic</h3>
        <button type="button" data-edit="contactBasic" title="Edit Contact Basic" class="text-[#457B9D] hover:text-[#1D3557] transition-colors">
            <i class="fas fa-edit"></i>
        </button>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <p><strong>Name:</strong> <span id="contactPerson"></span></p>
        <p><strong>Email:</strong> <span id="email"></span></p>
        <p><strong>Mobile:</strong> <span id="mobile"></span></p>
        <p><strong>Alternate:</strong> <span id="alternateContact"></span></p>
    </div>
</div>

<!-- Contact Details Display -->
<div class="max-w-7xl mx-auto my-8 bg-white rounded-xl shadow-md p-6" id="section-contactExtra" data-section="contactExtra">
    <div class="flex justify-between items-center border-b border-gray-200 pb-3 mb-4">
        <h3 class="text-xl font-semibold text-[#1D3557]"><i class="fas fa-address-card mr-2"></i>Contact Details</h3>
        <button type="button" data-edit="contactExtra" title="Edit Contact Details" class="text-[#457B9D] hover:text-[#1D3557] transition-colors">
            <i class="fas fa-edit"></i>
        </button>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <p><strong>Designation:</strong> <span id="profileTitle"></span></p>
        <p><strong>Gender:</strong> <span id="gender"></span></p>
    </div>
</div>

<!-- Account Settings Display -->
<div class="max-w-7xl mx-auto my-8 bg-white rounded-xl shadow-md p-6" id="section-accountSettings" data-section="accountSettings">
    <div class="flex justify-between items-center border-b border-gray-200 pb-3 mb-4">
        <h3 class="text-xl font-semibold text-[#1D3557]"><i class="fas fa-cog mr-2"></i>Account Settings</h3>
        <button type="button" data-edit="accountSettings" title="Edit Account Settings" class="text-[#457B9D] hover:text-[#1D3557] transition-colors">
            <i class="fas fa-edit"></i>
        </button>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <p><strong>Status:</strong> <span id="accountStatusText"></span></p>
        <p><strong>Membership:</strong> <span id="membershipType"></span></p>
        <p><strong>Email Status:</strong> <span id="emailStatus"></span></p>
        <p><strong>Call Status:</strong> <span id="callStatus"></span></p>
    </div>
</div>

<!-- System Info Display -->
<div class="max-w-7xl mx-auto my-8 bg-white rounded-xl shadow-md p-6" id="section-systemInfo" data-section="systemInfo">
    <div class="flex justify-between items-center border-b border-gray-200 pb-3 mb-4">
        <h3 class="text-xl font-semibold text-[#1D3557]"><i class="fas fa-database mr-2"></i>System Information</h3>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <p><strong>Created At:</strong> <span id="createdAt"></span></p>
        <p><strong>Last Login:</strong> <span id="lastLogin"></span></p>
        <p><strong>Updated At:</strong> <span id="updatedAt"></span></p>
    </div>
</div>

<!-- Company Basic Template -->
<template id="companyBasicTemplate">
    <form id="companyBasicForm" class="space-y-6 commonForm" data-section="companyBasic">
        <!-- CSRF Token - no ID to avoid duplicate -->
        <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
        <input type="hidden" name="form_type" value="companyBasic">
        <div class="grid grid-cols-1 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Company Name *</label>
                <input type="text" name="company_name" class="w-full p-3 rounded-lg border border-gray-200 focus:outline-none focus:border-transparent focus:ring-2 focus:ring-[#1D3557]/50 transition-all">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Founded Date</label>
                <input type="date" name="company_founded" class="w-full p-3 rounded-lg border border-gray-200 focus:outline-none focus:border-transparent focus:ring-2 focus:ring-[#1D3557]/50 transition-all">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Company Size</label>
                <select name="company_size" class="w-full p-3 rounded-lg border border-gray-200 focus:outline-none focus:border-transparent focus:ring-2 focus:ring-[#1D3557]/50 transition-all">
                    <option value="1-10">1-10</option>
                    <option value="11-50">11-50</option>
                    <option value="51-200">51-200</option>
                    <option value="201-500">201-500</option>
                    <option value="501-1000">501-1000</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Industry</label>
                <input type="search" id="industry_input" name="industry_name" class="w-full p-3 rounded-lg border border-gray-200 focus:outline-none focus:border-transparent focus:ring-2 focus:ring-[#1D3557]/50 transition-all" placeholder="Start typing to search industries..." autocomplete="off">
                <input type="hidden" name="industry_id" id="industry_id">
                <ul id="industry_list" class="absolute z-50 w-full bg-white shadow-lg rounded-b-lg mt-1 max-h-60 overflow-y-auto hidden"></ul>
            </div>
            <div class="border border-gray-200 rounded-lg p-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Expertise & Specialization</label>
                <div class="flex flex-wrap gap-2 mb-2 tags-display"></div>
                <input type="text" class="expertise-input w-full p-2 rounded-lg border border-gray-200 focus:outline-none focus:border-transparent focus:ring-2 focus:ring-[#1D3557]/50 transition-all" placeholder="Type one and press Enter…" autocomplete="off">
                <input type="hidden" name="expertise_specialization" id="expertiseHiddenInput" value="">
            </div>
        </div>
        <button type="submit" class="w-full px-4 py-3 bg-gradient-to-r from-[#1D3557] to-[#457B9D] text-white rounded-xl hover:shadow-lg transition-shadow">
            <i class="fas fa-save mr-2"></i>Save Company Basic
        </button>
    </form>
</template>

<!-- Company Extra Template -->
<template id="companyExtraTemplate">
    <form id="companyExtraForm" class="space-y-6 commonForm" data-section="companyExtra">
        <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
        <input type="hidden" name="form_type" value="companyExtra">
        <div class="grid grid-cols-1 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Company Type</label>
                <select name="company_type" class="w-full p-3 rounded-lg border border-gray-200 focus:outline-none focus:border-transparent focus:ring-2 focus:ring-[#1D3557]/50 transition-all">
                    <option value="Foreign MNC">Foreign MNC</option>
                    <option value="Indian MNC">Indian MNC</option>
                    <option value="Corporate">Corporate</option>
                    <option value="Startup">Startup</option>
                    <option value="Govt/PSU">Govt/PSU</option>
                    <option value="Others">Others</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Recruiter Type</label>
                <select name="recuiter_type" class="w-full p-3 rounded-lg border border-gray-200 focus:outline-none focus:border-transparent focus:ring-2 focus:ring-[#1D3557]/50 transition-all">
                    <option value="Direct Employer">Direct Employer</option>
                    <option value="Recruitment Firm">Recruitment Firm</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Website</label>
                <input type="url" name="company_website" class="w-full p-3 rounded-lg border border-gray-200 focus:outline-none focus:border-transparent focus:ring-2 focus:ring-[#1D3557]/50 transition-all">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">City</label>
                <input type="text" id="city_input" class="w-full p-3 rounded-lg border border-gray-200 focus:outline-none focus:border-transparent focus:ring-2 focus:ring-[#1D3557]/50 transition-all" placeholder="Search cities (min 2 characters)" autocomplete="off">
                <input type="hidden" name="city_id" id="city_id">
                <ul id="city_list" class="absolute z-50 w-full bg-white shadow-lg rounded-b-lg mt-1 max-h-60 overflow-y-auto hidden"></ul>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                <textarea name="company_address" class="w-full p-3 rounded-lg border border-gray-200 focus:outline-none focus:border-transparent focus:ring-2 focus:ring-[#1D3557]/50 transition-all" rows="3"></textarea>
            </div>
        </div>
        <button type="submit" class="w-full px-4 py-3 bg-gradient-to-r from-[#1D3557] to-[#457B9D] text-white rounded-xl hover:shadow-lg transition-shadow">
            <i class="fas fa-save mr-2"></i>Save Company Details
        </button>
    </form>
</template>

<!-- Contact Basic Template -->
<template id="contactBasicTemplate">
    <form id="contactBasicForm" class="space-y-6 commonForm" data-section="contactBasic">
        <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
        <input type="hidden" name="form_type" value="contactBasic">
        <div class="grid grid-cols-1 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">First Name *</label>
                <input type="text" name="name" class="w-full p-3 rounded-lg border border-gray-200 focus:outline-none focus:border-transparent focus:ring-2 focus:ring-[#1D3557]/50 transition-all">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Last Name</label>
                <input type="text" name="last_name" class="w-full p-3 rounded-lg border border-gray-200 focus:outline-none focus:border-transparent focus:ring-2 focus:ring-[#1D3557]/50 transition-all">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
                <input type="email" name="email" class="w-full p-3 rounded-lg border border-gray-200 focus:outline-none focus:border-transparent focus:ring-2 focus:ring-[#1D3557]/50 transition-all bg-gray-100" disabled>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Mobile *</label>
                <input type="tel" name="mobile" class="w-full p-3 rounded-lg border border-gray-200 focus:outline-none focus:border-transparent focus:ring-2 focus:ring-[#1D3557]/50 transition-all">
            </div>
        </div>
        <button type="submit" class="w-full px-4 py-3 bg-gradient-to-r from-[#1D3557] to-[#457B9D] text-white rounded-xl hover:shadow-lg transition-shadow">
            <i class="fas fa-save mr-2"></i>Save Contact Basic
        </button>
    </form>
</template>

<!-- Contact Extra Template -->
<template id="contactExtraTemplate">
    <form id="contactExtraForm" class="space-y-6 commonForm" data-section="contactExtra">
        <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
        <input type="hidden" name="form_type" value="contactExtra">
        <div class="grid grid-cols-1 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Designation *</label>
                <input type="text" id="job_profile_input" name="employee_designation" class="w-full p-3 rounded-lg border border-gray-200 focus:outline-none focus:border-transparent focus:ring-2 focus:ring-[#1D3557]/50 transition-all" placeholder="Search job profiles..." autocomplete="off">
                <input type="hidden" id="job_profile_id">
                <ul id="job_profile_list" class="absolute z-50 bg-white shadow-lg rounded-b-lg mt-1 max-h-60 overflow-y-auto hidden"></ul>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Gender</label>
                <select name="gender" class="w-full p-3 rounded-lg border border-gray-200 focus:outline-none focus:border-transparent focus:ring-2 focus:ring-[#1D3557]/50 transition-all">
                    <option value="">Select</option>
                    <option>Male</option>
                    <option>Female</option>
                    <option>Other</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Alternate Contact</label>
                <input type="tel" name="alternate_contact" class="w-full p-3 rounded-lg border border-gray-200 focus:outline-none focus:border-transparent focus:ring-2 focus:ring-[#1D3557]/50 transition-all">
            </div>
        </div>
        <button type="submit" class="w-full px-4 py-3 bg-gradient-to-r from-[#1D3557] to-[#457B9D] text-white rounded-xl hover:shadow-lg transition-shadow">
            <i class="fas fa-save mr-2"></i>Save Contact Details
        </button>
    </form>
</template>

<!-- About Company Template -->
<template id="aboutCompanyTemplate">
    <form id="aboutCompanyForm" class="space-y-6 commonForm" data-section="aboutCompany">
        <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
        <input type="hidden" name="form_type" value="aboutCompany">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">About Company *</label>
            <textarea name="about_company" class="w-full p-3 rounded-lg border border-gray-200 focus:outline-none focus:border-transparent focus:ring-2 focus:ring-[#1D3557]/50 transition-all" rows="6"></textarea>
        </div>
        <button type="submit" class="w-full px-4 py-3 bg-gradient-to-r from-[#1D3557] to-[#457B9D] text-white rounded-xl hover:shadow-lg transition-shadow">
            <i class="fas fa-save mr-2"></i>Save About Company
        </button>
    </form>
</template>

<!-- Account Settings Template -->
<template id="accountSettingsTemplate">
    <form id="accountSettingsForm" class="space-y-6 commonForm" data-section="accountSettings">
        <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
        <input type="hidden" name="form_type" value="accountSettings">
        <div class="grid grid-cols-1 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email Status</label>
                <select name="email_status" class="w-full p-3 rounded-lg border border-gray-200 focus:outline-none focus:border-transparent focus:ring-2 focus:ring-[#1D3557]/50 transition-all">
                    <option value="1">Active</option>
                    <option value="0">Inactive</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Call Status</label>
                <select name="call_status" class="w-full p-3 rounded-lg border border-gray-200 focus:outline-none focus:border-transparent focus:ring-2 focus:ring-[#1D3557]/50 transition-all">
                    <option value="1">Enabled</option>
                    <option value="0">Disabled</option>
                </select>
            </div>
        </div>
        <button type="submit" class="w-full px-4 py-3 bg-gradient-to-r from-[#1D3557] to-[#457B9D] text-white rounded-xl hover:shadow-lg transition-shadow">
            <i class="fas fa-save mr-2"></i>Save Account Settings
        </button>
    </form>
</template>

<!-- Modal Overlay -->
<div id="modalOverlay" class="fixed inset-0 bg-black/50 z-40 opacity-0 pointer-events-none transition-opacity duration-300"></div>

<!-- Premium Drawer Modal (Desktop: not full page, solid white) -->
<div id="modal" class="fixed top-0 right-0 h-full w-full md:w-[90%] md:max-w-4xl bg-white shadow-[-8px_0_32px_rgba(0,0,0,0.1)] z-[99] translate-x-full opacity-0 transition-all duration-300 ease-[cubic-bezier(0.4,0,0.2,1)] overflow-y-auto">
    <!-- Desktop Navigation -->
    <div id="desktopNav" class="hidden md:block w-60 bg-white border-r border-gray-300 h-full float-left">
        <ul class="divide-y divide-gray-200">
            <li><button type="button" class="desktop-nav-btn w-full text-left px-4 py-3 text-gray-900 hover:bg-gray-100 transition-colors" data-edit="companyBasic">Company Basic</button></li>
            <li><button type="button" class="desktop-nav-btn w-full text-left px-4 py-3 text-gray-900 hover:bg-gray-100 transition-colors" data-edit="companyExtra">Company Details</button></li>
            <li><button type="button" class="desktop-nav-btn w-full text-left px-4 py-3 text-gray-900 hover:bg-gray-100 transition-colors" data-edit="contactBasic">Contact Basic</button></li>
            <li><button type="button" class="desktop-nav-btn w-full text-left px-4 py-3 text-gray-900 hover:bg-gray-100 transition-colors" data-edit="contactExtra">Contact Details</button></li>
            <li><button type="button" class="desktop-nav-btn w-full text-left px-4 py-3 text-gray-900 hover:bg-gray-100 transition-colors" data-edit="aboutCompany">About Company</button></li>
            <li><button type="button" class="desktop-nav-btn w-full text-left px-4 py-3 text-gray-900 hover:bg-gray-100 transition-colors" data-edit="accountSettings">Account Settings</button></li>
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

<!-- Inner Modal (Mobile) - solid background -->
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
            <li class="border-b border-gray-300"><button type="button" class="inner-nav-btn w-full flex items-center justify-between px-4 py-4 text-gray-900 text-base hover:bg-gray-50 transition-colors" data-edit="companyBasic"><span>Company Basic</span><i class="fas fa-chevron-right text-gray-500"></i></button></li>
            <li class="border-b border-gray-300"><button type="button" class="inner-nav-btn w-full flex items-center justify-between px-4 py-4 text-gray-900 text-base hover:bg-gray-50 transition-colors" data-edit="companyExtra"><span>Company Details</span><i class="fas fa-chevron-right text-gray-500"></i></button></li>
            <li class="border-b border-gray-300"><button type="button" class="inner-nav-btn w-full flex items-center justify-between px-4 py-4 text-gray-900 text-base hover:bg-gray-50 transition-colors" data-edit="contactBasic"><span>Contact Basic</span><i class="fas fa-chevron-right text-gray-500"></i></button></li>
            <li class="border-b border-gray-300"><button type="button" class="inner-nav-btn w-full flex items-center justify-between px-4 py-4 text-gray-900 text-base hover:bg-gray-50 transition-colors" data-edit="contactExtra"><span>Contact Details</span><i class="fas fa-chevron-right text-gray-500"></i></button></li>
            <li class="border-b border-gray-300"><button type="button" class="inner-nav-btn w-full flex items-center justify-between px-4 py-4 text-gray-900 text-base hover:bg-gray-50 transition-colors" data-edit="aboutCompany"><span>About Company</span><i class="fas fa-chevron-right text-gray-500"></i></button></li>
            <li><button type="button" class="inner-nav-btn w-full flex items-center justify-between px-4 py-4 text-gray-900 text-base hover:bg-gray-50 transition-colors" data-edit="accountSettings"><span>Account Settings</span><i class="fas fa-chevron-right text-gray-500"></i></button></li>
        </ul>
    </div>
</div>

<!-- Scripts -->
<script src="<?= base_url('assets/frontend/js/auto-complete-widget.js') ?>" defer></script>
<script>
(function() {
    'use strict';

    // ==================== Global AJAX Loader ====================
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

    // ==================== CSRF Helpers (from meta tags) ====================
    function getCSRFToken() {
        return document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    }

    function getCSRFName() {
        return document.querySelector('meta[name="csrf-name"]').getAttribute('content');
    }

    function updateCSRFToken(token, name) {
        document.querySelector('meta[name="csrf-token"]').setAttribute('content', token);
        document.querySelector('meta[name="csrf-name"]').setAttribute('content', name);
        // Update all hidden inputs with the new token
        $(`input[name="${name}"]`).val(token);
    }

    // ==================== Toast Notification ====================
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

    // ==================== Autocomplete Widget Initializer ====================
    function initAutoCompleteWidgets() {
        if (typeof AutoCompleteWidget === 'undefined') {
            console.warn('AutoCompleteWidget not loaded yet, retrying in 200ms');
            setTimeout(initAutoCompleteWidgets, 200);
            return;
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
                console.log('Industry autocomplete initialized');
            } catch (e) {
                console.error('Industry autocomplete error:', e);
            }
        }

        // City
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
                console.log('City autocomplete initialized');
            } catch (e) {
                console.error('City autocomplete error:', e);
            }
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
                console.log('Job Profile autocomplete initialized');
            } catch (e) {
                console.error('Job Profile autocomplete error:', e);
            }
        }
    }

    // ==================== Modal Elements & Helpers ====================
    const modal = document.getElementById('modal');
    const modalOverlay = document.getElementById('modalOverlay');
    const modalContent = document.getElementById('modalContent');
    const modalHeaderLabel = document.getElementById('modalHeaderLabel');
    const innerModal = document.getElementById('innerModal');

    const templateMap = {
        companyBasic: 'companyBasicTemplate',
        companyExtra: 'companyExtraTemplate',
        contactBasic: 'contactBasicTemplate',
        contactExtra: 'contactExtraTemplate',
        aboutCompany: 'aboutCompanyTemplate',
        accountSettings: 'accountSettingsTemplate',
    };

    const isDesktop = () => window.innerWidth >= 768;

    function updateModalHeaderLabel(section) {
        modalHeaderLabel.textContent = "Edit " + section.charAt(0).toUpperCase() + section.slice(1);
    }

    function loadTemplateIntoModal(section) {
        const templateId = templateMap[section] || templateMap['companyBasic'];
        modalContent.innerHTML = document.getElementById(templateId).innerHTML;
        // Initialize autocomplete widgets after content is inserted
        initAutoCompleteWidgets();
    }

    // Open modal
    function openModal(section, recordId = null) {
        updateModalHeaderLabel(section);
        loadTemplateIntoModal(section);

        // Show modal using Tailwind classes
        modal.classList.add('translate-x-0', 'opacity-100');
        modal.classList.remove('translate-x-full', 'opacity-0');
        modalOverlay.classList.add('opacity-100', 'pointer-events-auto');
        modalOverlay.classList.remove('opacity-0', 'pointer-events-none');

        // Update active nav buttons
        document.querySelectorAll('.desktop-nav-btn').forEach(btn => {
            btn.classList.toggle('bg-gray-100', btn.dataset.edit === section);
        });
        document.querySelectorAll('.inner-nav-btn').forEach(btn => {
            btn.classList.toggle('bg-gray-50', btn.dataset.edit === section);
        });

        fetchEmployerDetails();
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

    // ==================== Handle AJAX Success ====================
    function handleAjaxSuccess(response) {
        if (response.csrf_token) {
            updateCSRFToken(response.csrf_token, response.csrf_name || getCSRFName());
        }
        if (response.success) {
            showToast('✅ ' + response.message, 'success');
            fetchEmployerDetails();
            closeModal();
        } else {
            showToast('❌ ' + response.message, 'error');
        }
    }

    // ==================== Bind Navigation ====================
    function bindDesktopNav() {
        document.querySelectorAll('.desktop-nav-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const section = btn.dataset.edit;
                updateModalHeaderLabel(section);
                modalContent.innerHTML = document.getElementById(templateMap[section] || templateMap['companyBasic']).innerHTML;
                initAutoCompleteWidgets(); // re-init for new section
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
                modalContent.innerHTML = document.getElementById(templateMap[section] || templateMap['companyBasic']).innerHTML;
                initAutoCompleteWidgets(); // re-init for new section
                innerModal.classList.add('translate-x-full');
                innerModal.classList.remove('translate-x-0');
            });
        }
    }

    // ==================== Form Submission ====================
    $(document).on('submit', '.commonForm', function (e) {
        e.preventDefault();
        const $form = $(this);
        const $btn = $form.find('button[type="submit"]');
        const orig = $btn.html();

        $btn.prop('disabled', true).html(`
            <div class="inline-flex items-center gap-2">
                <div class="w-5 h-5 border-2 border-white border-t-transparent rounded-full animate-spin"></div>
                Saving...
            </div>
        `);

        const formData = new FormData(this);
        formData.append(getCSRFName(), getCSRFToken());

        $.ajax({
            url: BASE_URL + 'employer/Profile/save_detail',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            dataType: 'json',
            success: handleAjaxSuccess,
            error: (xhr) => {
                const errorMsg = xhr.responseJSON?.message || 'Network error';
                showToast('❌ ' + errorMsg, 'error');
                if (xhr.status === 403 && errorMsg.includes('CSRF')) {
                    fetchEmployerDetails();
                    showToast('🔄 Security token refreshed. Try again.', 'info');
                }
            },
            complete: () => {
                $btn.prop('disabled', false).html(orig);
            }
        });
    });

    // ==================== Logo Upload ====================
    $(document).on('change', '#logoUpload', function () {
        const file = this.files[0];
        if (!file) return;

        const $loader = $('#uploadLoader').removeClass('hidden');
        const formData = new FormData();
        formData.append('logo', file);
        formData.append(getCSRFName(), getCSRFToken());

        $.ajax({
            url: BASE_URL + 'employer/Profile/upload_image',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            dataType: 'json',
            success: (response) => {
                $loader.addClass('hidden');
                if (response.success) {
                    showToast('✅ Logo updated!', 'success');
                    $('#logo-preview, #profileImage').attr('src', response.image_url + '?t=' + Date.now());
                    if (response.csrf_token) {
                        updateCSRFToken(response.csrf_token, response.csrf_name || getCSRFName());
                    }
                } else {
                    showToast('❌ ' + response.error_msg, 'error');
                }
            },
            error: () => {
                $loader.addClass('hidden');
                showToast('❌ Upload failed', 'error');
            }
        });
    });

    // ==================== Fetch Employer Details ====================
    function fetchEmployerDetails() {
        $.ajax({
            url: BASE_URL + 'employer/Profile/get_employer_details',
            type: 'GET',
            dataType: 'json',
            success: (response) => {
                if (!response.success) {
                    alert('Failed to fetch details.');
                    return;
                }
                const data = response.data;
                if (response.csrf_token) {
                    updateCSRFToken(response.csrf_token, response.csrf_name || getCSRFName());
                }

                // Populate form fields (if any)
                $('input[name="name"]').val(data.name);
                $('input[name="last_name"]').val(data.last_name);
                $('input[name="email"]').val(data.email);
                $('input[name="mobile"]').val(data.mobile);
                $('input[name="employee_designation"]').val(data.employee_designation);
                $('select[name="gender"]').val(data.gender);
                $('input[name="alternate_contact"]').val(data.alternate_contact);
                $('input[name="company_name"]').val(data.company_name);
                $('input[name="company_founded"]').val(data.company_founded);
                $('select[name="company_size"]').val(data.company_size);
                if (data.industry_id && data.industry_name) {
                    $('#industry_input').val(data.industry_name);
                    $('#industry_id').val(data.industry_id);
                }
                $('select[name="company_type"]').val(data.company_type);
                $('select[name="recuiter_type"]').val(data.recuiter_type);
                $('input[name="company_website"]').val(data.company_website);
                if (data.city_id && data.city_name) {
                    $('#city_input').val(data.city_name);
                    $('#city_id').val(data.city_id);
                }
                $('textarea[name="company_address"]').val(data.company_address);
                $('textarea[name="about_company"]').val(data.about_company);
                $('select[name="email_status"]').val(data.email_status);
                $('select[name="call_status"]').val(data.call_status);

                // Expertise tags
                if (data.expertise_specialization) {
                    const container = $('.border.border-gray-200.rounded-lg.p-4'); // expertise container
                    container.find('.tags-display').empty();
                    $('#expertiseHiddenInput').val('');
                    data.expertise_specialization.split(',').map(s => s.trim()).filter(Boolean).forEach(text => addExpertiseTag(text, container));
                }

                renderEmployerProfile(data);
            },
            error: () => alert('Error fetching data.')
        });
    }

    // ==================== Render Profile Display ====================
    function renderEmployerProfile(data) {
        $('#profileName').text(data.company_name || 'No Company Name');
        $('#profileTitle').text(data.employee_designation || 'No Designation');
        $('#gender').text(data.gender || 'No Gender');

        if (data.logo) {
            const logoUrl = BASE_URL + data.logo + '?t=' + Date.now();
            $('#logo-preview, #profileImage').attr('src', logoUrl);
        } else {
            $('#logo-preview, #profileImage').attr('src', '#');
        }

        let statusText;
        switch (data.status) {
            case 'inactive': statusText = '<span class="text-red-600 font-medium">Inactive</span>'; break;
            case 'under_review': statusText = '<span class="text-yellow-600 font-medium">Under Review</span>'; break;
            case 'rejected': statusText = '<span class="text-red-700 font-medium">Rejected</span>'; break;
            default: statusText = '<span class="text-green-600 font-medium">Active</span>';
        }
        $('#accountStatusText').html(statusText);

        $('#membershipType').text(data.membership_type || 'N/A');
        $('#emailStatus').text(data.email_status == 1 ? 'Active' : 'Inactive');
        $('#callStatus').text(data.call_status == 1 ? 'Enabled' : 'Disabled');

        // Expertise tags display
        const expertiseContainer = $('#expertiseTagsDisplay');
        expertiseContainer.empty();
        if (data.expertise_specialization) {
            data.expertise_specialization.split(',').map(s => s.trim()).filter(Boolean).forEach(text => {
                expertiseContainer.append(`<span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm">${text}</span>`);
            });
        } else {
            expertiseContainer.append('<span class="text-gray-500">No expertise added.</span>');
        }

        $('#companyName').text(data.company_name || '');
        $('#companyFounded').text(data.company_founded || '');
        $('#companySize').text(data.company_size || '');
        $('#companyType').text(data.company_type || '');
        $('#industry_name').text(data.industry_name || '');
        $('#city_name').text(data.city_name || '');
        $('#recruiterType').text(data.recuiter_type || '');
        $('#companyAddress').text(data.company_address || '');
        $('#companyWebsite').html(data.company_website ? `<a href="${data.company_website}" target="_blank" class="text-[#1D3557] hover:underline">${data.company_website}</a>` : 'N/A');
        $('#aboutCompany').text(data.about_company || 'No company description.');
        $('#email').html(`<i class="fas fa-envelope mr-1 text-gray-500"></i> ${data.email || '[Not Set]'} ${data.is_verified == 1 ? '<span class="text-emerald-600 font-semibold"><i class="fas fa-check-circle mr-1"></i>Verified</span>' : '<span class="text-amber-600 font-semibold"><i class="fas fa-exclamation-circle mr-1"></i>Unverified</span>'}`);
        $('#mobile').text(data.mobile || '');
        $('#alternateContact').text(data.alternate_contact || '');
        $('#contactPerson').text((data.name || '') + (data.last_name ? ' ' + data.last_name : ''));
        $('#lastLogin').text(data.last_login || '');
        $('#createdAt').text(data.created_at || '');
        $('#updatedAt').text(data.updated_at || '');

        $('#emailVerificationStatus').html(data.is_verified == 1
            ? `<div class="bg-emerald-500/90 text-white px-3 py-1 rounded-full flex items-center gap-1 shadow-md"><i class="fas fa-envelope text-xs"></i><span class="text-xs font-medium">Verified</span></div>`
            : `<div class="bg-amber-500/90 text-white px-3 py-1 rounded-full flex items-center gap-1 shadow-md"><i class="fas fa-exclamation-triangle text-xs"></i><span class="text-xs font-medium">Unverified</span><button class="ml-1 underline hover:opacity-80 resend-email">Resend</button></div>`
        );
    }

    // ==================== Expertise Tags Helpers ====================
    function updateExpertiseHidden() {
        const all = $('.expertise-text').map(function () { return $(this).text().trim(); }).get().join(',');
        $('#expertiseHiddenInput').val(all);
    }

    function addExpertiseTag(text, container) {
        container.find('.tags-display').append(`
            <span class="expertise-item bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm flex items-center">
                <span class="expertise-text">${text}</span>
                <button type="button" class="expertise-remove ml-2 text-green-800 hover:text-green-900">&times;</button>
            </span>
        `);
        updateExpertiseHidden();
    }

    $(document).on('keydown', '.expertise-input', function (e) {
        if (e.which === 13 || e.which === 188) {
            e.preventDefault();
            let input = $(this), val = input.val().trim();
            if (val.endsWith(',')) val = val.slice(0, -1).trim();
            if (val) {
                if (val.length > 50) alert('Expertise should be 50 characters or less.');
                else addExpertiseTag(val, input.closest('.border.border-gray-200.rounded-lg.p-4'));
            }
            input.val('');
        }
    });

    $(document).on('click', '.expertise-remove', function () {
        $(this).closest('.expertise-item').remove();
        updateExpertiseHidden();
    });

    // ==================== Resend Email Verification ====================
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
                    if (response.csrf_token) {
                        updateCSRFToken(response.csrf_token, response.csrf_name || getCSRFName());
                    }
                } else {
                    showToast('❌ ' + response.message, 'error');
                }
            },
            error: () => showToast('❌ Error sending verification', 'error')
        });
    });

    // ==================== Event Listeners ====================
    $(document).on('click', '[data-edit]', function () {
        openModal($(this).data('edit'));
    });

    $('#closeBtn, #modalOverlay').on('click', closeModal);

    // ==================== MutationObserver to catch dynamically added forms ====================
    const observer = new MutationObserver((mutations) => {
        mutations.forEach((mutation) => {
            if (mutation.addedNodes.length) {
                initAutoCompleteWidgets();
            }
        });
    });
    if (modalContent) {
        observer.observe(modalContent, { childList: true, subtree: true });
    }

    // ==================== Initialize on Load ====================
    window.addEventListener('load', () => {
        fetchEmployerDetails(); // initial load of data
        openModal('companyBasic');
        if (isDesktop()) {
            bindDesktopNav();
        } else {
            bindInnerModal();
        }
    });

    // ==================== Handle Window Resize ====================
    let resizeTimer;
    window.addEventListener('resize', () => {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(() => {
            if (!isDesktop()) {
                bindInnerModal();
            }
        }, 250);
    });

})();
</script>