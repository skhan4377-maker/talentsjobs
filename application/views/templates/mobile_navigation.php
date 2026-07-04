<!-- mobile_navigation.php -->
<?php if ($this->session->userdata('logged_in')): ?>
<!-- Mobile Menu Toggle Button - Compact & Professional -->
<div class="fixed lg:hidden z-50" style="bottom: 20px; right: 20px;">
  <button id="mobileMenuButton" 
          class="group flex items-center justify-center w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 text-white rounded-full shadow-lg hover:shadow-xl hover:shadow-blue-500/30 hover:scale-105 active:scale-95 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-offset-2"
          aria-label="Open Menu"
          title="Open Menu">
    
    <!-- Animated Hamburger Icon -->
    <div class="relative w-5 h-4">
      <span class="absolute top-0 left-0 w-5 h-0.5 bg-white rounded-full transition-all duration-300 group-hover:top-1.5 group-hover:rotate-45"></span>
      <span class="absolute top-1.5 left-0 w-5 h-0.5 bg-white rounded-full transition-all duration-300 group-hover:opacity-0"></span>
      <span class="absolute bottom-0 left-0 w-5 h-0.5 bg-white rounded-full transition-all duration-300 group-hover:bottom-1.5 group-hover:-rotate-45"></span>
    </div>
    
    <!-- Notification Badge (if any) -->
    <?php if($this->session->userdata('role') == 'employer' && isset($pending_interviews) && $pending_interviews > 0): ?>
      <span class="absolute -top-1 -right-1 flex items-center justify-center min-w-4 h-4 px-1 bg-red-500 text-white text-[10px] font-bold rounded-full animate-pulse border border-white">
        <?= $pending_interviews > 9 ? '9+' : $pending_interviews ?>
      </span>
    <?php endif; ?>
  </button>
</div>

<!-- Slide-From-Right Mobile Menu -->
<div id="mobileMenu" class="fixed inset-0 z-50 lg:hidden hidden" x-data="{
    openSection: null,
    toggleSection(section) {
        this.openSection = (this.openSection === section) ? null : section;
    },
    isOpen(section) {
        return this.openSection === section;
    }
}">

  <!-- Overlay -->
  <div class="absolute inset-0 bg-black/50" id="mobileMenuOverlay"></div>
  
  <!-- Menu Panel (Slides from right) -->
  <div class="absolute right-0 top-0 h-full w-80 bg-white shadow-xl transform transition-transform duration-300 translate-x-full overflow-hidden flex flex-col">
    
    <!-- Menu Header -->
    <div class="flex justify-between items-center p-4 border-b bg-gray-50">
      <div class="flex items-center gap-3">
        <i class="fas fa-home text-blue-600 text-lg"></i>
        <h3 class="text-lg font-semibold">Navigation Menu</h3>
      </div>
      <button id="closeMobileMenu" class="p-2 text-gray-500 hover:text-gray-700">
        <i class="fas fa-times text-xl"></i>
      </button>
    </div>

    <!-- Scrollable Menu Content -->
    <div class="overflow-y-auto flex-1 px-4 py-4 space-y-1">
      <?php if ($this->session->userdata('role') == 'candidate'): ?>
      
        <?php if (can('dashboard')): ?>
          <a href="<?= base_url('candidate/dashboard') ?>" class="flex items-center p-3 rounded-lg hover:bg-blue-50 text-gray-700 hover:text-blue-600">
            <i class="fas fa-home mr-3 text-blue-500 w-6 text-center"></i>
            <span>Dashboard</span>
          </a>
        <?php endif; ?>
        
        <?php if (can('saved_jobs')): ?>
          <a href="<?= base_url('job/favourite') ?>" class="flex items-center p-3 rounded-lg hover:bg-blue-50 text-gray-700 hover:text-blue-600">
            <i class="fas fa-heart mr-3 text-red-500 w-6 text-center"></i>
            <span>Saved Jobs</span>
          </a>
        <?php endif; ?>
        
        <?php if (can('recommended_jobs')): ?>
          <?php $slug = make_slug('Recommended') . "-jobs"; ?>
          <a href="<?= base_url('browse-jobs/' . $slug . '?recommended=1') ?>" 
             class="flex items-center p-3 rounded-lg hover:bg-blue-50 text-gray-700 hover:text-blue-600">
            <i class="fas fa-briefcase mr-3 text-green-500 w-6 text-center"></i>
            <span>Recommended Jobs</span>
          </a>
        <?php endif; ?>
        
        <?php if (can('my_applications')): ?>
          <a href="<?= base_url('job/myapply') ?>" class="flex items-center p-3 rounded-lg hover:bg-blue-50 text-gray-700 hover:text-blue-600">
            <i class="fas fa-clipboard-check mr-3 text-purple-500 w-6 text-center"></i>
            <span>My Applications</span>
          </a>
        <?php endif; ?>
        
        <?php if (can('edit_profile')): ?>
          <a href="<?= base_url('candidate/profile') ?>" class="flex items-center p-3 rounded-lg hover:bg-blue-50 text-gray-700 hover:text-blue-600">
            <i class="fas fa-user-edit mr-3 text-yellow-500 w-6 text-center"></i>
            <span>Edit Profile</span>
          </a>
        <?php endif; ?>
        
        <?php if (can('hiring_companies')): ?>
          <a href="<?= base_url('companies/hiring') ?>" class="flex items-center p-3 rounded-lg hover:bg-blue-50 text-gray-700 hover:text-blue-600">
            <i class="fas fa-building mr-3 text-indigo-500 w-6 text-center"></i>
            <span>Hiring Companies</span>
          </a>
        <?php endif; ?>
        
        <!-- More Options Toggle -->
        <div>
          <button @click="toggleSection('candidateMore')" class="flex items-center justify-between w-full p-3 rounded-lg hover:bg-blue-50 text-gray-700">
            <div class="flex items-center">
              <i class="fas fa-ellipsis-h mr-3 text-gray-500 w-6 text-center"></i>
              <span>More Options</span>
            </div>
            <i class="fas fa-chevron-right text-xs transition-transform duration-200" 
               :class="{'rotate-90': isOpen('candidateMore')}"></i>
          </button>
          
          <!-- Candidate More Options Submenu -->
          <div x-show="isOpen('candidateMore')" x-collapse class="ml-8 space-y-1">
            <?php if (can('downloads')): ?>
              <a href="#" class="flex items-center p-2 rounded-lg hover:bg-gray-100 text-gray-600 text-sm">
                <i class="fas fa-download mr-3 text-xs w-4 text-center"></i>
                <span>My Downloads</span>
              </a>
            <?php endif; ?>
            
            <?php if (can('purchases')): ?>
				<a href="<?= site_url('my-purchases') ?>" 
				   class="flex items-center px-6 py-3 <?= (strpos($current_url, 'my-purchases') === 0) 
						? 'bg-blue-50 border-l-4 border-blue-500 text-blue-600 font-medium dark:bg-gray-700' 
						: 'text-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700 dark:text-gray-300' ?>">
					
					<i class="fas fa-wallet mr-3 text-green-500"></i> 
					My Plans
				</a>
			<?php endif; ?>


			<?php if (can('draft_resumes')): ?>
				<a href="<?= site_url('my-resumes') ?>" 
				   class="flex items-center px-6 py-3 <?= (strpos($current_url, 'my-resumes') === 0) 
						? 'bg-blue-50 border-l-4 border-blue-500 text-blue-600 font-medium dark:bg-gray-700' 
						: 'text-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700 dark:text-gray-300' ?>">
					
					<i class="fas fa-file-alt mr-3 text-blue-500"></i> 
					My Resumes
				</a>
			<?php endif; ?>
			
            
            <?php if (can('career_plans')): ?>
              <a href="#" class="flex items-center p-2 rounded-lg hover:bg-gray-100 text-gray-600 text-sm">
                <i class="fas fa-certificate mr-3 text-xs w-4 text-center"></i>
                <span>Career Plans</span>
              </a>
            <?php endif; ?>
         
          </div>
        </div>
        
      <?php elseif ($this->session->userdata('role') == 'employer'): ?>
      
        <?php if (can('dashboard')): ?>
          <a href="<?= base_url('employer/dashboard') ?>" class="flex items-center p-3 rounded-lg hover:bg-blue-50 text-gray-700 hover:text-blue-600">
            <i class="fas fa-home mr-3 text-blue-500 w-6 text-center"></i>
            <span>Dashboard</span>
          </a>
        <?php endif; ?>
        
       <?php if (can('jobs')): ?>

		  <!-- Free Post Jobs -->
		  <a href="<?= base_url('employer/jobs/create') ?>" 
			 class="flex items-center p-3 rounded-lg hover:bg-blue-50 text-gray-700 hover:text-blue-600">
			<i class="fas fa-gift mr-3 text-blue-500 w-6 text-center"></i>
			<span>Free Post Jobs</span>
			<span class="ml-auto bg-green-100 text-green-700 text-xs px-2 py-0.5 rounded-full">
			  FREE
			</span>
		  </a>

		  <!-- Manage Jobs -->
		  <a href="<?= base_url('employer/jobs') ?>" 
			 class="flex items-center p-3 rounded-lg hover:bg-blue-50 text-gray-700 hover:text-blue-600">
			<i class="fas fa-briefcase mr-3 text-green-500 w-6 text-center"></i>
			<span>Manage Jobs</span>
		  </a>

		<?php endif; ?>

        
        <?php if (can('applications')): ?>
          <a href="<?= base_url('employer/applications') ?>" class="flex items-center p-3 rounded-lg hover:bg-blue-50 text-gray-700 hover:text-blue-600">
            <i class="fas fa-file-alt mr-3 text-purple-500 w-6 text-center"></i>
            <span>Applications</span>
          </a>
        <?php endif; ?>
        
        <?php if (can('interviews')): ?>
          <a href="<?= base_url('employer/interviews') ?>" class="flex items-center p-3 rounded-lg hover:bg-blue-50 text-gray-700 hover:text-blue-600">
            <i class="fas fa-calendar-alt mr-3 text-yellow-500 w-6 text-center"></i>
            <span>Interviews</span>
            <?php if(isset($pending_interviews) && $pending_interviews > 0): ?>
              <span class="ml-auto bg-red-500 text-white rounded-full px-2 py-0.5 text-xs"><?= $pending_interviews ?></span>
            <?php endif; ?>
          </a>
        <?php endif; ?>
        
        <?php if (can('edit_profile')): ?>
          <a href="<?= base_url('my-profile') ?>" class="flex items-center p-3 rounded-lg hover:bg-blue-50 text-gray-700 hover:text-blue-600">
            <i class="fas fa-user-edit mr-3 text-indigo-500 w-6 text-center"></i>
            <span>Edit Profile</span>
          </a>
        <?php endif; ?>
        
        <!-- More Options Toggle for Employer -->
        <div>
          <button @click="toggleSection('employerMore')" class="flex items-center justify-between w-full p-3 rounded-lg hover:bg-blue-50 text-gray-700">
            <div class="flex items-center">
              <i class="fas fa-ellipsis-h mr-3 text-gray-500 w-6 text-center"></i>
              <span>More Options</span>
            </div>
            <i class="fas fa-chevron-right text-xs transition-transform duration-200" 
               :class="{'rotate-90': isOpen('employerMore')}"></i>
          </button>
          
          <!-- Employer More Options Submenu -->
          <div x-show="isOpen('employerMore')" x-collapse class="ml-8 space-y-1">
            <?php if (can('my_plan')): ?>
              <a href="<?= base_url('employer/employer-plans') ?>" class="flex items-center p-2 rounded-lg hover:bg-gray-100 text-gray-600 text-sm">
                <i class="fas fa-wallet mr-3 text-xs w-4 text-center"></i>
                <span>My Plan</span>
              </a>
            <?php endif; ?>
            
            <?php if (can('viewed_resume')): ?>
              <a href="<?= base_url('search/SearchCandidates') ?>" class="flex items-center p-2 rounded-lg hover:bg-gray-100 text-gray-600 text-sm">
                <i class="fas fa-eye mr-3 text-xs w-4 text-center"></i>
                <span>Viewed Resumes</span>
              </a>
            <?php endif; ?>
          </div>
        </div>
        
      <?php else: ?>
      
        <?php if (can('dashboard')): ?>
          <a href="<?= base_url('admin/dashboard') ?>" class="flex items-center p-3 rounded-lg hover:bg-blue-50 text-gray-700 hover:text-blue-600">
            <i class="fas fa-home mr-3 text-blue-500 w-6 text-center"></i>
            <span>Dashboard</span>
          </a>
        <?php endif; ?>
        
        <!-- Admin Main Menu Items -->
        <?php if (can('manage_employers')): ?>
          <a href="<?= base_url('admin/employers/AdminEmployer/employers') ?>" class="flex items-center p-3 rounded-lg hover:bg-blue-50 text-gray-700 hover:text-blue-600">
            <i class="fas fa-building mr-3 text-green-500 w-6 text-center"></i>
            <span>Employers</span>
          </a>
        <?php endif; ?>
        
        <?php if (can('manage_candidates')): ?>
          <a href="<?= base_url('admin/candidates/AdminCandidate/candidates') ?>" class="flex items-center p-3 rounded-lg hover:bg-blue-50 text-gray-700 hover:text-blue-600">
            <i class="fas fa-users mr-3 text-purple-500 w-6 text-center"></i>
            <span>Candidates</span>
          </a>
        <?php endif; ?>
        
        <?php if (can('manage_job_posts')): ?>
          <a href="<?= base_url('admin/jobs/AdminJobs/jobs') ?>" class="flex items-center p-3 rounded-lg hover:bg-blue-50 text-gray-700 hover:text-blue-600">
            <i class="fas fa-briefcase mr-3 text-yellow-500 w-6 text-center"></i>
            <span>Job Posts</span>
          </a>
        <?php endif; ?>
        
        <!-- Admin Toggle Sections -->
        
        <!-- Services Toggle -->
        <?php if (can('manage_services')): ?>
        <div>
          <button @click="toggleSection('services')" class="flex items-center justify-between w-full p-3 rounded-lg hover:bg-blue-50 text-gray-700">
            <div class="flex items-center">
              <i class="fas fa-cogs mr-3 text-indigo-500 w-6 text-center"></i>
              <span>Services</span>
            </div>
            <i class="fas fa-chevron-right text-xs transition-transform duration-200" 
               :class="{'rotate-90': isOpen('services')}"></i>
          </button>
          
          <div x-show="isOpen('services')" x-collapse class="ml-8 space-y-1">
            <a href="<?= base_url('admin/services/Services') ?>" class="flex items-center p-2 rounded-lg hover:bg-gray-100 text-gray-600 text-sm">
              <i class="fas fa-plus mr-3 text-xs w-4 text-center"></i>
              <span>Add Service</span>
            </a>           
            <a href="<?= base_url('admin/features/Features') ?>" class="flex items-center p-2 rounded-lg hover:bg-gray-100 text-gray-600 text-sm">
              <i class="fas fa-cog mr-3 text-xs w-4 text-center"></i>
              <span>Add Feature</span>
            </a>
           
            <a href="<?= base_url('admin/features/Plans') ?>" class="flex items-center p-2 rounded-lg hover:bg-gray-100 text-gray-600 text-sm">
              <i class="fas fa-clock mr-3 text-xs w-4 text-center"></i>
              <span>Feature Plans</span>
            </a>
            <a href="<?= base_url('admin/features/Bundles') ?>" class="flex items-center p-2 rounded-lg hover:bg-gray-100 text-gray-600 text-sm">
              <i class="fas fa-list mr-3 text-xs w-4 text-center"></i>
              <span>Manage Bundles</span>
            </a>
          </div>
        </div>
        <?php endif; ?>
        
        <!-- Resume Templates Toggle -->
        <?php if (can('manage_resume_templates')): ?>
        <div>
          <button @click="toggleSection('resumeTemplates')" class="flex items-center justify-between w-full p-3 rounded-lg hover:bg-blue-50 text-gray-700">
            <div class="flex items-center">
              <i class="fas fa-file-alt mr-3 text-red-500 w-6 text-center"></i>
              <span>Resume Templates</span>
            </div>
            <i class="fas fa-chevron-right text-xs transition-transform duration-200" 
               :class="{'rotate-90': isOpen('resumeTemplates')}"></i>
          </button>
          
          <div x-show="isOpen('resumeTemplates')" x-collapse class="ml-8 space-y-1">
            <a href="<?= base_url('admin/manage-services/ResumeTemplates/add') ?>" class="flex items-center p-2 rounded-lg hover:bg-gray-100 text-gray-600 text-sm">
              <i class="fas fa-plus mr-3 text-xs w-4 text-center"></i>
              <span>Add Template</span>
            </a>
            <a href="<?= base_url('admin/manage-services/ResumeTemplates') ?>" class="flex items-center p-2 rounded-lg hover:bg-gray-100 text-gray-600 text-sm">
              <i class="fas fa-list mr-3 text-xs w-4 text-center"></i>
              <span>All Templates</span>
            </a>
          </div>
        </div>
        <?php endif; ?>
        
        <!-- Admin Roles Toggle -->
        <?php if (can('manage_roles')): ?>
        <div>
          <button @click="toggleSection('adminRoles')" class="flex items-center justify-between w-full p-3 rounded-lg hover:bg-blue-50 text-gray-700">
            <div class="flex items-center">
              <i class="fas fa-user-shield mr-3 text-orange-500 w-6 text-center"></i>
              <span>Admin Roles</span>
            </div>
            <i class="fas fa-chevron-right text-xs transition-transform duration-200" 
               :class="{'rotate-90': isOpen('adminRoles')}"></i>
          </button>
          
          <div x-show="isOpen('adminRoles')" x-collapse class="ml-8 space-y-1">
            <!-- Manage Roles -->
					<a href="<?= base_url('admin/AdminRole') ?>"
					   class="flex items-center py-2 px-3 rounded hover:bg-gray-200">
					   <i class="fas fa-list mr-2 text-xs"></i>
					   Manage Roles
					</a>

					<!-- Create Role -->
					<a href="<?= base_url('admin/AdminRole/create') ?>"
					   class="flex items-center py-2 px-3 rounded hover:bg-gray-200">
					   <i class="fas fa-plus-circle mr-2 text-xs"></i>
					   Create Role
					</a>
          </div>
        </div>
        <?php endif; ?>
        
        <!-- Other Admin Links -->
        <?php if (can('manage_email_crons')): ?>
          <a href="<?= base_url('admin/cron/Manage_cron') ?>" class="flex items-center p-3 rounded-lg hover:bg-blue-50 text-gray-700 hover:text-blue-600">
            <i class="fas fa-envelope-open-text mr-3 text-teal-500 w-6 text-center"></i>
            <span>Email Cron Jobs</span>
          </a>
          
          <a href="<?= base_url('admin/manage-campaigns/campaigns') ?>" class="flex items-center p-3 rounded-lg hover:bg-blue-50 text-gray-700 hover:text-blue-600">
            <i class="fas fa-bullhorn mr-3 text-pink-500 w-6 text-center"></i>
            <span>Campaigns</span>
          </a>
        <?php endif; ?>
		
		 <!-- NEW: Bio Management for Admin -->
		  <?php if (can('bio_manage')): ?>
			<a href="<?= base_url('bio/manage') ?>" class="flex items-center p-3 rounded-lg hover:bg-blue-50 text-gray-700 hover:text-blue-600">
			  <i class="fas fa-edit mr-3 text-indigo-500 w-6 text-center"></i>
			  <span>Bio Management</span>
			</a>
		  <?php endif; ?>
        
        <?php if (can('support_enquiries')): ?>
          <a href="<?= site_url('admin/support') ?>" class="flex items-center p-3 rounded-lg hover:bg-blue-50 text-gray-700 hover:text-blue-600">
            <i class="fas fa-headset mr-3 text-blue-500 w-6 text-center"></i>
            <span>Support Enquiries</span>
          </a>
        <?php endif; ?>
        
        <?php if (can('manage_blogs')): ?>
          <a href="<?= base_url('admin/blogs/BlogController') ?>" class="flex items-center p-3 rounded-lg hover:bg-blue-50 text-gray-700 hover:text-blue-600">
            <i class="fas fa-blog mr-3 text-green-500 w-6 text-center"></i>
            <span>Manage Blogs</span>
          </a>
        <?php endif; ?>
			
        
        <?php if (can('manage_job_applications')): ?>
          <a href="<?= base_url('admin/jobs/AdminApplications/applications') ?>" class="flex items-center p-3 rounded-lg hover:bg-blue-50 text-gray-700 hover:text-blue-600">
            <i class="fas fa-file-alt mr-3 text-purple-500 w-6 text-center"></i>
            <span>Applications</span>
          </a>
        <?php endif; ?>
        
      <?php endif; ?>
      
      <!-- Common Links for All Users -->
      <?php if (can('settings')): ?>
        <a href="<?= base_url('settings') ?>" class="flex items-center p-3 rounded-lg hover:bg-blue-50 text-gray-700 hover:text-blue-600">
          <i class="fas fa-cog mr-3 text-gray-500 w-6 text-center"></i>
          <span>Account Settings</span>
        </a>
      <?php endif; ?>
      
      <a href="<?= base_url('blogs') ?>" class="flex items-center p-3 rounded-lg hover:bg-blue-50 text-gray-700 hover:text-blue-600">
        <i class="fas fa-blog mr-3 text-orange-500 w-6 text-center"></i>
        <span>Latest Blogs</span>
      </a>
      
      <!-- Logout Button -->
      <a href="<?= base_url('auth/logout') ?>" class="flex items-center p-3 rounded-lg hover:bg-red-50 text-red-600 hover:text-red-700 mt-4 border-t">
        <i class="fas fa-sign-out-alt mr-3 w-6 text-center"></i>
        <span>Logout</span>
      </a>
    </div>
  </div>
</div>
<?php endif; ?>