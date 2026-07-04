<?php 
	$current_url = uri_string();
    $full_current_url = current_url();
    $role = $this->session->userdata('role');
?>
<!-- Fixed Sidebar -->
<aside class="fixed inset-y-0 left-0 w-64 bg-white shadow-lg z-50 max-lg:hidden transition-transform dark:bg-gray-800 overflow-y-auto"
       x-data="sidebar"
       x-init="init">
   <div class="p-6">
      <h2 class="text-2xl font-bold text-blue-600 dark:text-blue-400">
         <a href="<?= base_url() ?>" class="hover:underline">
         <?= SITE_NAME ?>
         </a>
      </h2>
   </div>
   <nav class="mt-6">
    
      <!-- Home Menu (Always visible) -->
		  <a href="<?= base_url() ?>" 
			 class="flex items-center px-6 py-3 <?= ($current_url == '') ? 'bg-blue-50 border-l-4 border-blue-500 text-gray-700 dark:bg-gray-700' : 'text-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700 dark:text-gray-300' ?>">
		  <i class="fas fa-home mr-3"></i> Home
		  </a>
		<?php if ($role == 'candidate'): ?>
		  <!-- Candidate Menu -->
		  <?php if (can('dashboard')): ?>
			  <a href="<?= base_url('candidate/dashboard') ?>" 
				 class="flex items-center px-6 py-3 <?= ($current_url == 'candidate/dashboard') ? 'bg-blue-50 border-l-4 border-blue-500 text-gray-700 dark:bg-gray-700' : 'text-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700 dark:text-gray-300' ?>">
			  <i class="fas fa-tachometer-alt mr-3"></i> Dashboard
			  </a>
		  <?php endif; ?>
		  <?php if (can('recommended_jobs')): ?>
			 <?php $slug = make_slug('Recommended') . "-jobs"; ?>
				<a href="<?= base_url('browse-jobs/' . $slug . '?recommended=1') ?>" 
				   class="flex items-center px-6 py-3 <?= (strpos($current_url, 'browse-jobs') === 0 && strpos($_SERVER['QUERY_STRING'], 'recommended=1') !== false) 
						? 'bg-blue-50 border-l-4 border-blue-500 text-gray-700 dark:bg-gray-700' 
						: 'text-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700 dark:text-gray-300' ?>">
					<i class="fas fa-briefcase mr-3"></i> Recommended Jobs
				</a>
		  <?php endif; ?>
		  
		  <?php if (can('my_applications')): ?>
			  <a href="<?= base_url('job/myapply') ?>" 
				 class="flex items-center px-6 py-3 <?= (strpos($current_url, 'job/myapply') === 0) ? 'bg-blue-50 border-l-4 border-blue-500 text-gray-700 dark:bg-gray-700' : 'text-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700 dark:text-gray-300' ?>">
			  <i class="fas fa-clipboard-check mr-3"></i> My Applications
			  </a>
		  <?php endif; ?>
		  <?php if (can('saved_jobs')): ?>
			  <a href="<?= base_url('job/favourite') ?>" 
				 class="flex items-center px-6 py-3 <?= (strpos($current_url, 'job/favourite') === 0) ? 'bg-blue-50 border-l-4 border-blue-500 text-gray-700 dark:bg-gray-700' : 'text-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700 dark:text-gray-300' ?>">
				<i class="fas fa-heart mr-3"></i> My Saved Jobs
			  </a>
		  <?php endif; ?>
		  
		  <?php if (can('downloads')): ?>
			  <a href="#<?= site_url('my-downloads') ?>" 
				 class="flex items-center px-6 py-3 <?= (strpos($current_url, 'my-downloads') === 0) ? 'bg-blue-50 border-l-4 border-blue-500 text-gray-700 dark:bg-gray-700' : 'text-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700 dark:text-gray-300' ?>">
				<i class="fas fa-download mr-3"></i> My Downloads (Soon)
			  </a>
		  <?php endif; ?>
		  <?php if (can('purchases')): ?>
			   <a href="<?= site_url('my-purchases') ?>" 
				   class="flex items-center px-6 py-3 
				   <?= (strpos($current_url, 'my-purchases') === 0) 
						? 'bg-blue-50 border-l-4 border-blue-500 text-blue-600 font-medium' 
						: 'text-gray-600 hover:bg-gray-100' ?>">
					<i class="fas fa-shopping-cart mr-3"></i> My Plans
				</a>		  
				
		  <?php endif; ?>
		  
		  <?php if (can('draft_resumes')): ?>
				<a href="<?= site_url('my-resumes') ?>" 
				   class="flex items-center px-6 py-3 
				   <?= (strpos($current_url, 'my-resumes') === 0) 
						? 'bg-blue-50 border-l-4 border-blue-500 text-blue-600 font-medium' 
						: 'text-gray-600 hover:bg-gray-100' ?>">
					<i class="fas fa-file-alt mr-3"></i> My Resumes
				</a>
			<?php endif; ?>
			
		 
				
		  <?php if (can('career_plans')): ?>
			  <a href="#<?= site_url('career-plans') ?>" 
				 class="flex items-center px-6 py-3 <?= (strpos($current_url, 'career-plans') === 0) ? 'bg-blue-50 border-l-4 border-blue-500 text-gray-700 dark:bg-gray-700' : 'text-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700 dark:text-gray-300' ?>">
			  <i class="fas fa-certificate mr-3"></i> My Career Plans (Soon)
			  </a>
		  <?php endif; ?>
		  
	     

		  <?php if (can('edit_profile')): ?>
			  <a href="<?= base_url('candidate/profile') ?>" 
				 class="flex items-center px-6 py-3 <?= (strpos($current_url, 'candidate/profile') === 0) ? 'bg-blue-50 border-l-4 border-blue-500 text-gray-700 dark:bg-gray-700' : 'text-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700 dark:text-gray-300' ?>">
			  <i class="fas fa-user-edit mr-3"></i> Edit Profile
			  </a>
		  <?php endif; ?>
		  
		  
       <?php elseif ($role == 'employer'): ?>    
		  <!-- Employer Menu -->
		  <?php if (can('dashboard')): ?>
			  <a href="<?= base_url('employer/dashboard') ?>" 
				 class="flex items-center px-6 py-3 <?= ($current_url == 'employer/dashboard') ? 'bg-blue-50 border-l-4 border-blue-500 text-gray-700 dark:bg-gray-700' : 'text-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700 dark:text-gray-300' ?>">
			  <i class="fas fa-tachometer-alt mr-3"></i> Dashboard
			  </a>
		  <?php endif; ?>
		 
		 <?php if (can('jobs')): ?>

			  <!-- Free Post Jobs -->
			  <a href="<?= base_url('employer/jobs/create') ?>" 
				 class="flex items-center px-6 py-3 <?= 
					(strpos($current_url, 'employer/jobs/create') === 0) 
					? 'bg-blue-50 border-l-4 border-blue-500 text-gray-700 dark:bg-gray-700' 
					: 'text-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700 dark:text-gray-300' ?>">
				 
				 <i class="fas fa-gift mr-3 text-blue-500"></i> 
				 Free Post Jobs
				 
				 <span class="ml-auto bg-green-100 text-green-700 text-xs px-2 py-0.5 rounded-full">
					FREE
				 </span>
			  </a> 

			  <!-- Manage Jobs -->
			  <a href="<?= base_url('employer/jobs') ?>" 
				 class="flex items-center px-6 py-3 <?= 
					(strpos($current_url, 'employer/jobs') === 0 
					 && strpos($current_url, 'employer/jobs/create') === false)
					? 'bg-blue-50 border-l-4 border-blue-500 text-gray-700 dark:bg-gray-700' 
					: 'text-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700 dark:text-gray-300' ?>">
				 
				 <i class="fas fa-briefcase mr-3"></i> 
				 Manage Jobs
			  </a>

			<?php endif; ?>


		  <?php if (can('applications')): ?>
			  <a href="<?= base_url('employer/applications') ?>" 
				 class="flex items-center px-6 py-3 <?= (strpos($current_url, 'employer/applications') === 0) ? 'bg-blue-50 border-l-4 border-blue-500 text-gray-700 dark:bg-gray-700' : 'text-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700 dark:text-gray-300' ?>">
			  <i class="fas fa-file-alt mr-3"></i> Candidate Applications
			  </a>
			  <?php endif; ?>
			  <?php if (can('interviews')): ?>
				  <a href="<?= base_url('employer/interviews') ?>" 
					 class="flex items-center px-6 py-3 <?= (strpos($current_url, 'employer/interviews') === 0) ? 'bg-blue-50 border-l-4 border-blue-500 text-gray-700 dark:bg-gray-700' : 'text-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700 dark:text-gray-300' ?>">
				  <i class="fas fa-calendar-alt mr-3"></i> Interviews
				  <?php if (isset($pending_interviews) && $pending_interviews > 0): ?>
				  <span class="ml-2 bg-red-500 text-white rounded-full px-2 py-1 text-xs">
				  <?= $pending_interviews ?>
				  </span>
				  <?php endif; ?>
				  </a>
			  <?php endif; ?>
			  
			  <?php if (can('my_plan')): ?>
			  <a href="<?= base_url('employer/employer-plans') ?>" 
				 class="flex items-center px-6 py-3 <?= ($current_url == 'employer/employer-plans') ? 'bg-blue-50 border-l-4 border-blue-500 text-gray-700 dark:bg-gray-700' : 'text-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700 dark:text-gray-300' ?>">
			  <i class="fas fa-wallet mr-3"></i> My Plan
			  </a>
			  <?php endif; ?>
			  <?php if (can('viewed_resume')): ?>
			  <a href="<?= base_url('search/SearchCandidates') ?>" 
				 class="flex items-center px-6 py-3 <?= ($current_url == 'search/SearchCandidates') ? 'bg-blue-50 border-l-4 border-blue-500 text-gray-700 dark:bg-gray-700' : 'text-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700 dark:text-gray-300' ?>">
			  <i class="fas fa-eye mr-3"></i> Viewed Resume
			  </a>
			  <?php endif; ?>
			  <?php if (can('edit_profile')): ?>
			  <a href="<?= base_url('my-profile') ?>" 
				 class="flex items-center px-6 py-3 <?= (strpos($current_url, 'employer/profile') === 0) ? 'bg-blue-50 border-l-4 border-blue-500 text-gray-700 dark:bg-gray-700' : 'text-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700 dark:text-gray-300' ?>">
			  <i class="fas fa-user-edit mr-3"></i> Edit Profile
			  </a>
		  <?php endif; ?>
	  			 
         <?php elseif ($role !== 'employer' && $role !== 'candidate'): ?>
		  
			  <?php if (can('dashboard')): ?>
				  <a href="<?= base_url('admin/dashboard') ?>" 
					 class="flex items-center px-6 py-3 <?= ($current_url == 'admin/dashboard') ? 'bg-blue-50 border-l-4 border-blue-500 text-gray-700 dark:bg-gray-700' : 'text-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700 dark:text-gray-300' ?>">
				  <i class="fas fa-tachometer-alt mr-3"></i> Dashboard
				  </a>
			  <?php endif; ?>
			  <?php if (can('manage_employers')): ?>
				  <a href="<?= base_url('admin/employers/AdminEmployer/employers') ?>" 
					 class="flex items-center px-6 py-3 <?= (strpos(current_url(), base_url('admin/employers/AdminEmployer/employers')) === 0) ? 'bg-blue-50 border-l-4 border-blue-500 text-gray-700' : 'text-gray-600 hover:bg-gray-100' ?>">
				  <i class="fas fa-building mr-3"></i> Employers
				  </a>
			  <?php endif; ?>
			  <?php if (can('manage_candidates')): ?>
				  <a href="<?= base_url('admin/candidates/AdminCandidate/candidates') ?>" 
					 class="flex items-center px-6 py-3 <?= (strpos($current_url, 'admin/candidates/AdminCandidate/candidates') === 0) ? 'bg-blue-50 border-l-4 border-blue-500 text-gray-700 dark:bg-gray-700' : 'text-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700 dark:text-gray-300' ?>">
				  <i class="fas fa-users mr-3"></i> Candidates
				  </a>
			  <?php endif; ?>
			  <?php if (can('manage_job_posts')): ?>
				  <a href="<?= base_url('admin/jobs/AdminJobs/jobs') ?>" 
					 class="flex items-center px-6 py-3 <?= (strpos($current_url, 'admin/jobs/AdminJobs/jobs') === 0) ? 'bg-blue-50 border-l-4 border-blue-500 text-gray-700 dark:bg-gray-700' : 'text-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700 dark:text-gray-300' ?>">
				  <i class="fas fa-briefcase mr-3"></i> Job Posts
				  </a>
			  <?php endif; ?>			
			  <?php if (can('manage_job_applications')): ?>
				  <a href="<?= base_url('admin/jobs/AdminApplications/applications') ?>" 
					 class="flex items-center px-6 py-3 <?= (strpos($current_url, 'admin/jobs/AdminApplications/applications') === 0) ? 'bg-blue-50 border-l-4 border-blue-500 text-gray-700 dark:bg-gray-700' : 'text-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700 dark:text-gray-300' ?>">
				  <i class="fas fa-file-alt mr-3"></i> Applications
				  </a>
			  <?php endif; ?>
			  
			  <?php if (can('manage_blogs')): ?>
				  <a href="<?= base_url('admin/blogs/BlogController') ?>" 
					 class="flex items-center px-6 py-3 <?= (strpos($current_url, 'admin/blogs/BlogController') === 0) ? 'bg-blue-50 border-l-4 border-blue-500 text-gray-700 dark:bg-gray-700' : 'text-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700 dark:text-gray-300' ?>">
				  <i class="fas fa-blog mr-3"></i> Manage Blogs
				  </a>
			  <?php endif; ?>
			 

				<?php if (can('manage_email_crons')): ?>
				  <!-- Email Cron Jobs -->
				  <a href="<?= base_url('admin/cron/Manage_cron') ?>" 
					 class="flex items-center px-6 py-3 <?= (strpos($current_url, 'admin/cron/Manage_cron') === 0) ? 'bg-blue-50 border-l-4 border-blue-500 text-gray-700 dark:bg-gray-700' : 'text-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700 dark:text-gray-300' ?>">
					<i class="fas fa-envelope-open-text mr-3"></i> Email Cron Jobs
				  </a>
					
					<!-- Campaigns -->
				<a href="<?= base_url('admin/manage-campaigns/campaigns') ?>" 
				   class="flex items-center px-6 py-3 <?= (strpos($current_url, 'admin/manage-campaigns/campaigns') === 0) ? 'bg-blue-50 border-l-4 border-blue-500 text-gray-700 dark:bg-gray-700' : 'text-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700 dark:text-gray-300' ?>">
					<i class="fas fa-bullhorn mr-3"></i> Campaigns
				</a>

				<?php endif; ?>
				
				<!-- Bio Management (CRUD) - Only for admin roles -->
				<?php if (can('bio_manage')): ?>
					<a href="<?= base_url('bio/manage') ?>" 
					   class="flex items-center px-6 py-3 <?= (strpos($current_url, 'bio/manage') === 0) ? 'bg-blue-50 border-l-4 border-blue-500 text-gray-700 dark:bg-gray-700' : 'text-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700 dark:text-gray-300' ?>">
						<i class="fas fa-edit mr-3"></i> Bio Management
					</a>
				<?php endif; ?>
				
				<?php if (can('support_enquiries')): ?>		  

				  <!-- Support Enquiries -->
				  <a href="<?= site_url('admin/support') ?>"
					 class="flex items-center px-6 py-3 <?= (strpos($current_url, 'admin/support') === 0) ? 'bg-blue-50 border-l-4 border-blue-500 text-gray-700 dark:bg-gray-700' : 'text-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700 dark:text-gray-300' ?>">
					<i class="fas fa-headset mr-3"></i> Support Enquiries
				  </a>
				<?php endif; ?>

			  
			  <?php
				 // ✅ Current URL check for active menu states
				 $is_services_active = strpos($full_current_url, 'admin/features') !== false;
				 $is_resume_active = strpos($full_current_url, 'admin/features/ResumeTemplates') !== false;
				 $is_role_active = strpos($full_current_url, 'admin/AdminRole') !== false;
			    ?>

			  <?php if (can('manage_services') || can('manage_resume_templates')): ?>
			  <!-- Services Menu -->
			  <div x-data="{ 
				  open: <?= $is_services_active ? 'true' : 'false' ?>,
				  toggle() {
					  this.open = !this.open;
					  localStorage.setItem('services_menu_open', this.open);
				  },
				  init() {
					  // ✅ Page reload पर localStorage से state restore करें
					  const saved = localStorage.getItem('services_menu_open');
					  if (saved !== null) {
						  this.open = saved === 'true';
					  } else {
						  // ✅ यदि current page इस menu के अंतर्गत है तो open करें
						  this.open = <?= $is_services_active ? 'true' : 'false' ?>;
					  }
				  }
			  }" 
			  x-init="init()" 
			  class="w-full">
				 <a href="javascript:void(0);" @click="toggle()"
					class="flex items-center justify-between px-6 py-3 w-full <?= $is_services_active ? 'bg-blue-50 border-l-4 border-blue-500 text-gray-700 dark:bg-gray-700' : 'text-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700 dark:text-gray-300' ?>">
					<div class="flex items-center">
					   <i class="fas fa-layer-group mr-3"></i>
					   <span>Services</span>
					</div>
					<i :class="open ? 'fas fa-chevron-up' : 'fas fa-chevron-down'" class="text-xs transition-all"></i>
				 </a>
				 <div x-show="open" class="pl-12 pr-6 py-3 space-y-1" x-cloak style="display: <?= $is_services_active ? 'block' : 'none' ?>;">
					<a href="<?= base_url('admin/services/Services') ?>"
					   class="block text-sm py-2 rounded-md <?= strpos($full_current_url, 'admin/services/Services') !== false ? 'text-blue-600 font-medium' : 'text-gray-600 hover:text-blue-600' ?>">
					<i class="fas fa-plus mr-2"></i> Add Service
					</a>
					
					<a href="<?= base_url('admin/features/Features') ?>"
					   class="block text-sm py-2 rounded-md <?= strpos($full_current_url, 'admin/features/Features') !== false ? 'text-blue-600 font-medium' : 'text-gray-600 hover:text-blue-600' ?>">
						<i class="fas fa-cog mr-2"></i> Add Feature
					</a>
					<a href="<?= base_url('admin/features/Plans') ?>"
					   class="block text-sm py-2 rounded-md <?= strpos($full_current_url, 'admin/features/Plans') !== false && !strpos($full_current_url, 'admin/features/Plans') ? 'text-blue-600 font-medium' : 'text-gray-600 hover:text-blue-600' ?>">
						<i class="fas fa-list mr-2"></i> Feature Plans
					</a>
					<a href="<?= base_url('admin/features/Bundles') ?>"
					   class="block text-sm py-2 rounded-md <?= strpos($full_current_url, 'admin/features/Bundles') !== false ? 'text-blue-600 font-medium' : 'text-gray-600 hover:text-blue-600' ?>">
						<i class="fas fa-clock mr-2"></i> Manage Bundles
					</a>
										
				 </div>
			  </div>
			  <?php endif; ?>
			  
			  <?php if (can('manage_resume_templates')): ?>
    			  <!-- Resume Templates Menu -->
    			  <div x-data="{ 
    				  open: <?= $is_resume_active ? 'true' : 'false' ?>,
    				  toggle() {
    					  this.open = !this.open;
    					  localStorage.setItem('resume_menu_open', this.open);
    				  },
    				  init() {
    					  const saved = localStorage.getItem('resume_menu_open');
    					  if (saved !== null) {
    						  this.open = saved === 'true';
    					  } else {
    						  this.open = <?= $is_resume_active ? 'true' : 'false' ?>;
    					  }
    				  }
    			  }" 
    			  x-init="init()" 
    			  class="w-full">
    				 <a href="javascript:void(0);" @click="toggle()"
    					class="flex items-center justify-between px-6 py-3 w-full <?= $is_resume_active ? 'bg-blue-50 border-l-4 border-blue-500 text-gray-700 dark:bg-gray-700' : 'text-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700 dark:text-gray-300' ?>">
    					<div class="flex items-center">
    					   <i class="fas fa-file-alt mr-3"></i>
    					   <span>Resume Templates</span>
    					</div>
    					<i :class="open ? 'fas fa-chevron-up' : 'fas fa-chevron-down'" class="text-xs transition-all"></i>
    				 </a>
    				 <div x-show="open" class="pl-12 pr-6 py-3 space-y-1" x-cloak style="display: <?= $is_resume_active ? 'block' : 'none' ?>;">
    					<a href="<?= base_url('admin/features/ResumeTemplates/add') ?>"
    					   class="block text-sm py-2 rounded-md <?= strpos($full_current_url, 'admin/manage-services/ResumeTemplates/add') !== false ? 'text-blue-600 font-medium' : 'text-gray-600 hover:text-blue-600' ?>">
    					<i class="fas fa-plus mr-2"></i> Add Template
    					</a>
    					<a href="<?= base_url('admin/features/ResumeTemplates') ?>"
    					   class="block text-sm py-2 rounded-md <?= $full_current_url === base_url('admin/features/ResumeTemplates') ? 'text-blue-600 font-medium' : 'text-gray-600 hover:text-blue-600' ?>">
    					<i class="fas fa-list mr-2"></i> All Templates
    					</a>
    				 </div>
    			  </div>
			  <?php endif; ?>
			  
			  
			  <?php if (can('manage_purchase_plans')): ?>
				<a href="<?= base_url('admin/purchase-plans') ?>" 
				   class="flex items-center px-6 py-3 <?= (strpos($current_url, 'admin/purchase-plans') === 0) ? 'bg-blue-50 border-l-4 border-blue-500 text-gray-700 dark:bg-gray-700' : 'text-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700 dark:text-gray-300' ?>">
					<i class="fas fa-shopping-bag mr-3"></i> Purchase Plans
				</a>
				<?php endif; ?>

				<?php if (can('manage_refunds')): ?>
				<a href="<?= base_url('admin/refunds') ?>" 
				   class="flex items-center px-6 py-3 <?= (strpos($current_url, 'admin/refunds') === 0) ? 'bg-blue-50 border-l-4 border-blue-500 text-gray-700 dark:bg-gray-700' : 'text-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700 dark:text-gray-300' ?>">
					<i class="fas fa-undo-alt mr-3"></i> Refund Requests
				</a>
			 <?php endif; ?>

			<?php if (can('manage_roles')): ?>
				<?php $is_role_active = strpos($full_current_url, 'admin/AdminRole') !== false; ?>

				<div x-data="{ 
					open: <?= $is_role_active ? 'true' : 'false' ?>,
					toggle() {
						this.open = !this.open;
						localStorage.setItem('roles_menu_open', this.open);
					},
					init() {
						const saved = localStorage.getItem('roles_menu_open');
						if (saved !== null) {
							this.open = saved === 'true';
						} else {
							this.open = <?= $is_role_active ? 'true' : 'false' ?>;
						}
					}
				}" x-init="init()" class="w-full">

					<!-- Parent Menu -->
					<a href="javascript:void(0);" @click="toggle()"
					   class="flex items-center justify-between px-6 py-3 w-full <?= $is_role_active ? 'bg-blue-50 border-l-4 border-blue-500 text-gray-700 dark:bg-gray-700' : 'text-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700 dark:text-gray-300' ?>">
					   
					   <div class="flex items-center">
						  <i class="fas fa-user-shield mr-3"></i>
						  <span>Admin Roles</span>
					   </div>

					   <i :class="open ? 'fas fa-chevron-up' : 'fas fa-chevron-down'" class="text-xs transition-all"></i>
					</a>

					<!-- Submenu Items -->
					<div x-show="open" class="pl-12 pr-6 py-3 space-y-1" x-cloak style="display: <?= $is_role_active ? 'block' : 'none' ?>;">

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
			
			
			  
			  <?php if (can('edit_profile')): ?>
				  <a href="<?= base_url('my-profile') ?>" 
					 class="flex items-center px-6 py-3 <?= (strpos($current_url, 'employer/profile') === 0) ? 'bg-blue-50 border-l-4 border-blue-500 text-gray-700 dark:bg-gray-700' : 'text-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700 dark:text-gray-300' ?>">
					<i class="fas fa-user-edit mr-3"></i> Edit Profile
				  </a>
			  <?php endif; ?>
		  
		   <?php endif; ?>	
		
		  <?php if (can('settings')): ?>
			  <a href="<?= base_url('settings') ?>" 
				 class="flex items-center px-6 py-3 <?= (strpos($current_url, 'settings') === 0) ? 'bg-blue-50 border-l-4 border-blue-500 text-gray-700 dark:bg-gray-700' : 'text-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700 dark:text-gray-300' ?>">
			  <i class="fas fa-cog mr-3"></i> Account Settings
			  </a>
		  <?php endif; ?> 
		  
		 <?php if (can('hiring_companies')): ?>
			  <a href="<?= base_url('companies/hiring') ?>" 
				 class="flex items-center px-6 py-3 <?= (strpos($current_url, 'companies/hiring') === 0) ? 'bg-blue-50 border-l-4 border-blue-500 text-gray-700 dark:bg-gray-700' : 'text-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700 dark:text-gray-300' ?>">
			  <i class="fas fa-building mr-3"></i> Hiring Companies
			  </a>
		 <?php endif; ?>
		  
		  <a href="<?= base_url('blogs') ?>" 
			 class="flex items-center px-6 py-3 <?= (strpos($current_url, 'blogs') === 0) ? 'bg-blue-50 border-l-4 border-blue-500 text-gray-700 dark:bg-gray-700' : 'text-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700 dark:text-gray-300' ?>">
			<i class="fas fa-blog mr-3"></i> Latest Blogs
		  </a>				
   </nav>
</aside>