<nav x-data="{ isOpen: false, isSearchOpen: false }" class="bg-white shadow-sm fixed w-full z-50 border-b border-gray-100">
	
    <!-- Desktop Navigation (Better Left & Right Side Gap, No Wrap) -->
    <div class="hidden lg:flex items-center justify-between px-3 py-2 w-full max-w-[1440px] mx-auto">
      
      <!-- Left Section -->
      <div class="flex items-center space-x-3 xl:space-x-5">
        <a href="<?=base_url('')?>" class="flex items-center flex-shrink-0">
          <img src="<?=base_url('assets/frontend/logo.png')?>" alt="Logo" class="h-8 transition-all hover:scale-105">
        </a>
        
        <!-- Main Menu (with adjusted gap) -->
        <div class="flex items-center space-x-3 xl:space-x-5 text-sm">
         
          <!-- Jobs Dropdown (highlighted bold black) -->
          <div class="relative group">
            <a href="<?= base_url('browse-jobs'); ?>" 
               class="flex items-center space-x-1 font-bold text-gray-900 hover:text-blue-600 transition-colors py-2 whitespace-nowrap" 
               target="_blank">
              <span>Jobs</span>
              <svg class="w-4 h-4 mt-0.5 text-gray-600 group-hover:text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
              </svg>
            </a>
    
            <!-- Dropdown Content (full) -->
            <div class="absolute top-full left-0 hidden group-hover:block animate-fadeIn">
              <div class="bg-white shadow-xl rounded-lg p-6 w-[1000px] max-w-[95vw] border border-gray-100 mt-2">
                <div class="grid grid-cols-3 gap-6">
                  <!-- Popular Categories -->
                  <div>
                    <h6 class="font-semibold text-gray-800 mb-3">Popular Categories</h6>
                    <ul class="space-y-2.5">
                      <?php
                      $industries = [
                        ['name' => 'Information Technology'],
                        ['name' => 'Banking & Finance'],
                        ['name' => 'Healthcare'],
                        ['name' => 'Construction'],
                        ['name' => 'Education'],
                        ['name' => 'Retail'],
                      ];
                      foreach ($industries as $industry): 
                        $slug = make_slug($industry['name']) . "-jobs";
                        $queryValue = strtolower($industry['name']) . " jobs";
                        $query = http_build_query(['key_word' => $queryValue]);
                      ?>
                        <li>
                          <a href="<?= base_url('browse-jobs/' . $slug . '?' . $query); ?>" 
                             class="text-gray-800 hover:text-blue-600" 
                             target="_blank">
                            <?= htmlspecialchars($industry['name']); ?>
                          </a>
                        </li>
                      <?php endforeach; ?>
                    </ul>
                  </div>
    
                  <!-- Jobs in Demand -->
                  <div>
                    <h6 class="font-semibold text-gray-800 mb-3">Jobs in Demand</h6>
                    <ul class="space-y-2.5">
                      <?php
                      $trendingJobs = [
                        ['label' => 'AI Engineer'],                        
                        ['label' => 'Data Analyst'],                          
                        ['label' => 'Product Manager'],
                        ['label' => 'UX Designer'],
                        ['label' => 'DevOps Engineer'],
                        ['label' => 'Digital Marketing Expert'],
                      ];
                      foreach ($trendingJobs as $job): 
                        $slug = make_slug($job['label']) . "-jobs";
                        $queryValue = strtolower($job['label']) . " jobs";
                        $query = http_build_query(['key_word' => $queryValue]);
                      ?>
                        <li>
                          <a href="<?= base_url('browse-jobs/' . $slug . '?' . $query); ?>"
                             class="text-gray-800 hover:text-blue-600"
                             target="_blank">
                            <?= htmlspecialchars($job['label']); ?>
                          </a>
                        </li>
                      <?php endforeach; ?>
                    </ul>
                  </div>
    
                  <!-- Jobs by Location -->
                  <div>
                    <h6 class="font-semibold text-gray-800 mb-3">Jobs by Location</h6>
                    <ul class="space-y-2.5">
                      <?php
                      $trendingCities = [
                        ['name' => 'Delhi'], ['name' => 'Mumbai'], ['name' => 'Bengaluru'],
                        ['name' => 'Hyderabad'], ['name' => 'Chennai'], ['name' => 'Pune'],
                        ['name' => 'Lucknow'],
                      ];
                      foreach ($trendingCities as $city): 
                        $slug = 'jobs-in-' . make_slug($city['name']);
                        $queryValue = strtolower($city['name']) . " jobs";
                        $query = http_build_query(['key_word' => $queryValue]);
                      ?>
                        <li>
                          <a href="<?= base_url('browse-jobs/' . $slug . '?' . $query); ?>" 
                             class="text-gray-800 hover:text-blue-600"
                             target="_blank">
                            Jobs in <?= htmlspecialchars($city['name']); ?>
                          </a>
                        </li>
                      <?php endforeach; ?>
                    </ul>
                  </div>
                </div>
              </div>
            </div>
          </div>
    
          <a href="<?=base_url('bio');?>" class="flex items-center space-x-1 font-bold text-gray-900 hover:text-blue-600 transition-colors py-2 whitespace-nowrap">Latest Jobs</a>
          <a href="<?=base_url('companies/hiring');?>" class="flex items-center space-x-1 font-bold text-gray-900 hover:text-blue-600 transition-colors py-2 whitespace-nowrap">Top Companies</a>    
          
          <?php if(!$this->session->userdata('logged_in') || $this->session->userdata('role') === 'employer'): ?>
          <!-- For Employers Dropdown -->
          <div class="relative group">
            <button class="flex items-center space-x-1 font-bold text-gray-900 hover:text-blue-600 transition-colors py-2 whitespace-nowrap">
              <span>For Employers</span>
              <svg class="w-4 h-4 text-gray-600 group-hover:text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
              </svg>
            </button>
            <div class="absolute top-full left-0 hidden group-hover:block animate-fadeIn">
              <div class="bg-white shadow-xl rounded-lg p-2 w-48 mt-2 border border-gray-100">
                <?php if($this->session->userdata('logged_in') && $this->session->userdata('role') === 'employer'): ?>
                  <a href="<?= base_url('employer/jobs/create'); ?>" class="block px-4 py-2.5 text-gray-800 hover:bg-gray-50 rounded-md">Post Jobs</a>
                  <a href="<?= base_url('employer/dashboard'); ?>" class="block px-4 py-2.5 text-gray-800 hover:bg-gray-50 rounded-md">Employer Dashboard</a>
                <?php else: ?>
                  <a href="<?= base_url('recruit/client-registration-form'); ?>" class="block px-4 py-2.5 text-gray-800 hover:bg-gray-50 rounded-md">Post Jobs</a>
                  <a href="<?= base_url('auth/login'); ?>" class="block px-4 py-2.5 text-gray-800 hover:bg-gray-50 rounded-md">Employer Dashboard</a>
                <?php endif; ?>
              </div>
            </div>
          </div>
          <?php endif; ?>
          
          <a href="<?=base_url('blogs');?>" class="flex items-center space-x-1 font-bold text-gray-900 hover:text-blue-600 transition-colors py-2 whitespace-nowrap">Blog</a> 
		  
		  <!-- Career Services (Dropdown or single link) -->
			<!-- Career Services Dropdown -->
			<div class="relative group">
				<button class="flex items-center space-x-1 font-bold text-gray-900 hover:text-blue-600 transition-colors py-2 whitespace-nowrap">
					<span>Career Services</span>
					<svg class="w-4 h-4 mt-0.5 text-gray-600 group-hover:text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
					</svg>
				</button>
				<div class="absolute top-full left-0 hidden group-hover:block animate-fadeIn">
					<div class="bg-white shadow-xl rounded-lg p-2 w-56 mt-2 border border-gray-100">
						<a href="<?= base_url('career-services'); ?>" class="block px-4 py-2.5 text-gray-800 hover:bg-gray-50 rounded-md">All Services</a>
						<a href="<?= base_url('resume-templates'); ?>" class="block px-4 py-2.5 text-gray-800 hover:bg-gray-50 rounded-md">Resume Templates</a>
						<!-- Add more service links as needed -->
					</div>
				</div>
			</div>
		  
        </div>
      </div>
    
      <!-- Right Section (Gap increased: gap-3 xl:gap-4) -->
      <div class="flex items-center gap-3 xl:gap-4 flex-shrink-0">
        <button @click="isSearchOpen = true" class="flex items-center gap-1 bg-gradient-to-r from-white via-gray-100 to-white hover:from-blue-50 hover:to-blue-100 text-gray-900 hover:text-blue-600 px-4 py-2 rounded-full transition-all duration-300 shadow-md hover:shadow-lg border border-gray-200 text-sm">
          <i class="fas fa-search text-base"></i>
          <span class="hidden xl:inline font-semibold tracking-wide">Search Job Here</span>
        </button>
    
        <?php if(!$this->session->userdata('logged_in')): ?>
        <a href="<?= base_url('recruit/client-registration-form'); ?>" class="bg-gradient-to-r from-blue-600 to-blue-700 text-white px-4 py-2 rounded-full hover:shadow-lg transition-shadow duration-200 font-medium text-sm whitespace-nowrap">
          Free Job Post
        </a>
        <?php endif; ?>
    
        <div class="flex items-center gap-2">
          <a href="<?=base_url('cart')?>" class="text-gray-600 hover:text-blue-600 relative">
            <i class="fas fa-shopping-cart text-sm"></i>
            <span id="cart-count" class="absolute -top-1.5 -right-2 bg-orange-500 text-white text-xs rounded-full w-4 h-4 flex items-center justify-center">0</span>
          </a>
    
          <!-- Notification Bell -->
          <div class="relative group">
            <button id="desktopNotificationBtn" class="p-1.5 text-gray-700 rounded-full relative">
              <i class="fas fa-bell text-lg"></i>
              <span class="notification-badge absolute top-0 right-0 w-4 h-4 bg-red-500 rounded-full text-white text-xs flex items-center justify-center hidden count-badge">0</span>
            </button>
            <div id="desktopNotificationDropdown" class="hidden absolute right-0 mt-2 w-80 bg-white rounded-xl shadow-2xl border">
              <div class="p-4">
                <h3 class="font-semibold mb-3 text-gray-900">Notifications (<span class="notification-title-count">0</span>)</h3>
                <div class="space-y-3 max-h-60 overflow-y-auto notification-container">
                  <!-- Notifications will be dynamically inserted here -->
                </div>
              </div>
            </div>
          </div>
    
          <!-- Profile / Login -->
          <?php if($this->session->userdata('logged_in')): ?>
			  <div class="relative group">
				<button class="flex items-center gap-1 text-gray-900 hover:text-blue-600">
				  <!-- Avatar with premium crown overlay -->
				  <div class="relative">
					<img src="<?= $this->session->userdata('logo') ? $this->session->userdata('logo') : base_url('assets/frontend/default-avatar.png'); ?>" 
						 class="w-8 h-8 rounded-full object-cover border border-gray-200" 
						 alt="Profile">
					<?php 
						$role = $this->session->userdata('role');
						$hasPlan = $this->session->userdata('has_active_plan');
						if ($role === 'candidate' && $hasPlan): 
					?>
					  <span class="absolute -bottom-0.5 -right-0.5 inline-flex items-center justify-center w-4 h-4 rounded-full text-[8px] bg-gradient-to-br from-yellow-300 to-amber-500 text-white shadow-sm" 
							title="Premium Subscriber">
						<i class="fas fa-crown"></i>
					  </span>
					<?php endif; ?>
				  </div>
				  <!-- Dropdown arrow (वैसा ही रखें) -->
				  <svg class="w-3 h-3 mt-0.5 text-gray-600 group-hover:text-blue-600 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
				  </svg>
				</button>

				<!-- Dropdown (कोई बदलाव नहीं) -->
				<div class="absolute right-0 mt-2 w-48 bg-white shadow-xl rounded-lg py-2 invisible opacity-0 group-hover:visible group-hover:opacity-100 transition-all duration-200 border border-gray-100 z-50 origin-top-right transform">
				  <div class="pointer-events-auto">
					<?php $role = $this->session->userdata('role'); if ($role === 'candidate') { ?>
					  <a href="<?= base_url('candidate/profile') ?>" class="block px-4 py-2.5 text-gray-800 hover:bg-gray-50">Profile</a>
					<?php } elseif ($role === 'employer') { ?>
					  <a href="<?= base_url('employer/profile') ?>" class="block px-4 py-2.5 text-gray-800 hover:bg-gray-50">Profile</a>
					<?php } ?>
					<a href="<?= base_url('settings') ?>" class="block px-4 py-2.5 text-gray-800 hover:bg-gray-50">Settings</a>
					<?php if ($role === 'employer'): ?>
					  <a href="<?= base_url('employer/employer-plans') ?>" class="block px-4 py-2.5 text-gray-800 hover:bg-gray-50">My Plan</a>
					<?php endif; ?>
					<div class="border-t my-2"></div>
					<a href="<?= base_url('auth/logout') ?>" class="block px-4 py-2.5 text-red-600 hover:bg-red-50">Logout</a>
				  </div>
				</div>
			  </div>
			<?php else: ?>
          <div class="h-6 w-px bg-gray-200"></div>
          <a href="<?= base_url('auth/login') ?>" class="text-gray-900 hover:text-blue-600 font-medium text-sm whitespace-nowrap">Login</a>
          <a href="<?= base_url('registration/candidate') ?>" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors font-medium shadow-sm text-sm whitespace-nowrap">Register</a>
          <?php endif; ?>
        </div>
      </div>
    </div>

	<!-- Mobile Menu -->
    <div class="lg:hidden flex items-center justify-between p-4">
      <button @click="isOpen = !isOpen" class="text-gray-600 hover:text-gray-800">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path x-show="!isOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
          <path x-show="isOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" class="transition-all duration-200"/>
        </svg>
      </button>
      
      
      <div class="flex items-center gap-4">
		<!-- Mobile Search Icon -->
		<!-- Fancy Mobile Search Icon -->
		<button @click="isSearchOpen = true" class="w-[190px] flex items-center justify-center gap-2 bg-gradient-to-r from-white via-gray-100 to-white hover:from-blue-50 hover:to-blue-100 text-gray-700 hover:text-blue-600 px-5 py-2 rounded-full transition-all duration-300 shadow-md hover:shadow-lg border border-gray-200">
		  <i class="fas fa-search text-base"></i>
		  <span class="text-sm font-semibold tracking-wide">Search Jobs</span>
		</button>
	
        <div class="relative group inline-block">
          <a href="<?=base_url('cart')?>" class="text-gray-600 hover:text-gray-800 relative">
            <i class="fas fa-shopping-cart"></i>
            <span id="cart-count-mobile" class="absolute -top-1 -right-2 bg-orange-500 text-white rounded-full text-xs px-1.5 py-0.5">0</span>
          </a>
        </div>
		
        <div class="relative group inline-block">
          <button id="notificationBtn" class="p-2 text-gray-600 rounded-full relative">
				<i class="fas fa-bell text-xl"></i>
				<span class="notification-badge absolute top-0 right-0 w-5 h-5 bg-red-500 rounded-full text-white text-xs flex items-center justify-center hidden count-badge">
					0
				</span>
			</button>            
			<div id="notificationDropdown" class="hidden absolute right-0 mt-2 w-72 bg-white rounded-xl shadow-2xl border">
				<div class="p-4">
					<h3 class="font-semibold mb-3">Notifications (<span class="notification-title-count">0</span>)</h3>
					<div class="space-y-3 max-h-60 overflow-y-auto notification-container">
						<!-- Notifications will be dynamically inserted here -->
					</div>
				</div>
			</div>
        </div>	
		
        </div>      
	</div>
       
    <!-- Mobile Menu Overlay -->
   <div x-show="isOpen" x-cloak class="lg:hidden fixed inset-0 bg-black/30 z-40" @click="isOpen = false"></div>

	<!-- Mobile Sidebar -->
	<div x-show="isOpen" x-cloak class="lg:hidden fixed inset-y-0 left-0 w-64 bg-white z-50 transform transition-transform duration-300 shadow-xl">
		  <div class="p-5 border-b flex items-center justify-between">
			<img src="<?=base_url('assets/frontend/logo.png')?>" alt="Logo" class="h-8">
			<button @click="isOpen = false" class="text-gray-500 hover:text-gray-700">
			  <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
				<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
			  </svg>
			</button>
		  </div>
		
		<div class="overflow-y-auto h-[calc(100vh-4rem)] p-3 space-y-2">		
			<!-- Home Menu -->
				<a href="<?= base_url('') ?>" class="block py-2 px-3 text-gray-700 hover:bg-blue-50 rounded-md font-medium text-sm">
				  <i class="fas fa-home mr-2 w-4"></i>Home
				</a>							
			
			   <!-- Primary Navigation -->
			   <div class="space-y-1">
				<a href="<?= base_url('browse-jobs') ?>" class="block py-2 px-3 text-gray-700 hover:bg-blue-50 rounded-md font-medium text-sm">
					  <i class="fas fa-briefcase mr-2 w-4"></i>Jobs
					</a>

					<a href="<?= base_url('bio') ?>" class="block py-2 px-3 text-gray-700 hover:bg-blue-50 rounded-md font-medium text-sm">
					  <i class="fas fa-clock mr-2 w-4"></i>Latest Jobs
					</a>
				  
				  <a href="<?= base_url('companies/hiring'); ?>" class="block py-2 px-3 text-gray-700 hover:bg-blue-50 rounded-md text-sm">
					<i class="fas fa-building mr-2 w-4"></i>Top Companies
				  </a>
				  <a href="<?= base_url('blogs'); ?>" class="block py-2 px-3 text-gray-700 hover:bg-blue-50 rounded-md text-sm">
					<i class="fas fa-blog mr-2 w-4"></i>Blog
				  </a>
				  
				  <a href="#" class="block py-2 px-3 text-gray-700 hover:bg-blue-50 rounded-md font-medium text-sm">
					<i class="fas fa-concierge-bell mr-2 w-4"></i>Career Services
				</a>
				<div  class="ml-6 space-y-1">
					<a href="<?= base_url('career-services') ?>" class="block py-1.5 px-3 text-gray-600 hover:text-blue-600 hover:bg-blue-50 rounded-md text-sm">All Services</a>
					<a href="<?= base_url('resume-templates') ?>" class="block py-1.5 px-3 text-gray-600 hover:text-blue-600 hover:bg-blue-50 rounded-md text-sm">Resume Templates</a>
				</div>
				</div>
		
			 <!-- Explore More -->
			<div class="pt-2 border-t border-gray-100">
				<h6 class="text-xs font-semibold text-gray-800 mb-1">Explore More</h6>
				<ul class="space-y-1">
				  <?php
				  $exploreJobs = [
					['label' => 'Part Time',  'icon' => 'fas fa-map-marker-alt', 'text' => 'Jobs Near Me'],
					['label' => 'Work From Home',        'icon' => 'fas fa-home',           'text' => 'Work From Home'],
					['label' => 'Internship', 'icon' => 'fas fa-user-graduate',  'text' => 'Paid Internship'],
					['label' => 'Freelance',  'icon' => 'fas fa-laptop-code',    'text' => 'Find Freelancer Work'],
				  ];

				  foreach ($exploreJobs as $job): 
					// slug
					$slug = make_slug($job['label']) . "-jobs";

					// query string without hyphens
					$queryValue = strtolower($job['label']) . " jobs";

					$query = http_build_query([
					  'key_word' => $queryValue
					]);

				  ?>
					<li>
					  <a 
						href="<?= base_url('browse-jobs/' . $slug . '?' . $query); ?>" 
						class="block py-1.5 px-3 text-gray-600 hover:text-blue-600 hover:bg-blue-50 rounded-md text-sm"
					  >
						<i class="<?= htmlspecialchars($job['icon']); ?> mr-2 w-4"></i>
						<?= htmlspecialchars($job['text']); ?>
					  </a>
					</li>
				  <?php endforeach; ?>
				</ul>

			</div>

	  			  
			  <?php if (!$this->session->userdata('logged_in')): ?>
				  <div class="pt-2 border-t border-gray-100">
					<div class="mb-1 text-sm font-medium text-gray-700">Account</div>
					<div class="space-y-1">
					  <ul class="space-y-1 text-left">
						<a href="<?= base_url('auth/login') ?>" class="block py-1.5 px-3 text-blue-700 hover:text-white hover:bg-blue-600 rounded-md text-sm font-semibold">
						  <i class="fas fa-sign-in-alt mr-2 w-4"></i>Login
						</a>

						<a href="<?= base_url('registration/candidate') ?>" class="block py-1.5 px-3 text-blue-700 hover:text-white hover:bg-blue-600 rounded-md text-sm font-semibold">
						  <i class="fas fa-user-plus mr-2 w-4"></i>Register
						</a>
					  </ul>
					</div>
				  </div>
			 <?php endif; ?>
	
			
			<!-- For Employers -->
			<?php if (!$this->session->userdata('logged_in') || $this->session->userdata('role') === 'employer'): ?>
			  <div class="pt-2 border-t border-gray-100">
				<div class="mb-1 text-sm font-medium text-gray-700">For Employers</div>
				<div class="space-y-1">
				  <?php if ($this->session->userdata('logged_in') && $this->session->userdata('role') === 'employer'): ?>
					<a href="<?= base_url('employer/jobs/create'); ?>" class="block py-1.5 px-3 text-gray-600 hover:text-blue-600 hover:bg-blue-50 rounded-md text-sm">
					  <i class="fas fa-plus-circle mr-2 w-4"></i>Post Jobs
					</a>
					<a href="<?= base_url('employer/dashboard'); ?>" class="block py-1.5 px-3 text-gray-600 hover:text-blue-600 hover:bg-blue-50 rounded-md text-sm">
					  <i class="fas fa-chart-line mr-2 w-4"></i>Employer Dashboard
					</a>
				  <?php else: ?>
					<a href="<?= base_url('recruit/client-registration-form'); ?>" class="block py-1.5 px-3 text-gray-600 hover:text-blue-600 hover:bg-blue-50 rounded-md text-sm">
					  <i class="fas fa-plus-circle mr-2 w-4"></i>Post Jobs
					</a>
					<a href="<?= base_url('auth/login'); ?>" class="block py-1.5 px-3 text-gray-600 hover:text-blue-600 hover:bg-blue-50 rounded-md text-sm">
					  <i class="fas fa-chart-line mr-2 w-4"></i>Employer Dashboard
					</a>
				  <?php endif; ?>
				</div>
			  </div>
			<?php endif; ?>

			<!-- Popular Categories -->
			<div class="pt-2 border-t border-gray-100">
				<h6 class="text-xs font-semibold text-gray-800 mb-1">Popular Categories</h6>
				<ul class="space-y-1">
				  <?php
				  $industries = [
					['name' => 'Information Technology'],
					['name' => 'Banking & Finance'],
					['name' => 'Healthcare'],
					['name' => 'Construction'],
					['name' => 'Education'],
					['name' => 'Retail'],
				  ];

				  foreach ($industries as $industry):

					  // slug → hyphens
					  $slug = make_slug($industry['name']) . "-jobs";

					  // query string → no hyphens
					  $queryValue = strtolower($industry['name']) . " jobs";

					  $query = http_build_query([
						'key_word' => $queryValue
					  ]);
				  ?>
					<li>
					  <a 
						href="<?= base_url('browse-jobs/' . $slug . '?' . $query); ?>" 
						class="block py-1.5 px-3 text-gray-600 hover:text-blue-600 hover:bg-blue-50 rounded-md text-sm"
						target="_blank"
					  >
						<?= htmlspecialchars($industry['name']); ?>
					  </a>
					</li>
				  <?php endforeach; ?>
				</ul>

			</div>


		<!-- Jobs in Demand -->
		<div class="pt-2 border-t border-gray-100">
			<h6 class="text-xs font-semibold text-gray-800 mb-1">Jobs in Demand</h6>
			<ul class="space-y-1">
			  <?php
			  $trendingJobs = [
				['label' => 'AI Engineer'],
				['label' => 'Data Analyst'],
				['label' => 'Product Manager'],
				['label' => 'UX Designer'],
				['label' => 'DevOps Engineer'],
				['label' => 'Digital Marketing Expert'],
			  ];

			  foreach ($trendingJobs as $job):

				  // slug → hyphens
				  $slug = make_slug($job['label']) . "-jobs";

				  // query string → original + " jobs"
				  $queryValue = strtolower($job['label']) . " jobs";

				  $query = http_build_query([
					'key_word' => $queryValue
				  ]);
			  ?>
				<li>
				  <a 
					href="<?= base_url('browse-jobs/' . $slug . '?' . $query); ?>"
					class="block py-1.5 px-3 text-gray-600 hover:text-blue-600 hover:bg-blue-50 rounded-md text-sm"
					target="_blank"
				  >
					<?= htmlspecialchars($job['label']); ?>
				  </a>
				</li>
			  <?php endforeach; ?>
			</ul>

		</div>


			<!-- Jobs by Location -->
		<div class="pt-2 border-t border-gray-100">
			<h6 class="text-xs font-semibold text-gray-800 mb-1">Jobs by Location</h6>
			<ul class="space-y-1">
			  <?php
			  $trendingCities = [
				['name' => 'Delhi'],
				['name' => 'Mumbai'],
				['name' => 'Bengaluru'],
				['name' => 'Hyderabad'],
				['name' => 'Chennai'],
				['name' => 'Pune'],
				['name' => 'Lucknow'],
			  ];

			  foreach ($trendingCities as $city):

				  // slug → hyphens
				  $slug = 'jobs-in-' . make_slug($city['name']);

				  // query string → original + " jobs"
				  $queryValue = "jobs in " . strtolower($city['name']);

				  $query = http_build_query([
					'key_word' => $queryValue
				  ]);

			  ?>
				<li>
				  <a 
					href="<?= base_url('browse-jobs/' . $slug . '?' . $query); ?>"
					class="block py-1.5 px-3 text-gray-600 hover:text-blue-600 hover:bg-blue-50 rounded-md text-sm"
				  >
					Jobs in <?= htmlspecialchars($city['name']); ?>
				  </a>
				</li>
			  <?php endforeach; ?>
			</ul>

		</div>

		<!-- 👤 Logged-in User Section -->
		<?php if ($this->session->userdata('logged_in')): ?>
			<div class="pt-2 border-t border-gray-100">
				<div class="space-y-1">
				  <?php $role = $this->session->userdata('role'); ?>
				  
				  <?php if ($role === 'candidate'): ?>
					<a href="<?= base_url('candidate/profile') ?>" class="block py-1.5 px-3 text-gray-600 hover:text-blue-600 hover:bg-blue-50 rounded-md text-sm">
					  <i class="fas fa-user mr-2 w-4"></i>Profile
					  <?php if ($this->session->userdata('has_active_plan')): ?>
						<i class="fas fa-crown text-yellow-500 ml-1 text-xs"></i>
					  <?php endif; ?>
					</a>
				  <?php elseif ($role === 'employer'): ?>
					<a href="<?= base_url('employer/profile') ?>" class="block py-1.5 px-3 text-gray-600 hover:text-blue-600 hover:bg-blue-50 rounded-md text-sm">
					  <i class="fas fa-user-tie mr-2 w-4"></i>Profile
					</a>
				  <?php endif; ?>

				  <a href="<?= base_url('settings') ?>" class="block py-1.5 px-3 text-gray-600 hover:text-blue-600 hover:bg-blue-50 rounded-md text-sm">
					<i class="fas fa-cog mr-2 w-4"></i>Settings
				  </a>

				  <?php if ($role === 'candidate'): ?>
					<!--<a href="<?= base_url('career-plans') ?>" class="block py-1.5 px-3 text-gray-600 hover:text-blue-600 hover:bg-blue-50 rounded-md text-sm">
					  <i class="fas fa-crown mr-2 w-4"></i>Purchased Plans
					</a>-->
				  <?php elseif ($role === 'employer'): ?>
					<a href="<?= base_url('employer/employer-plans') ?>" class="block py-1.5 px-3 text-gray-600 hover:text-blue-600 hover:bg-blue-50 rounded-md text-sm">
					  <i class="fas fa-crown mr-2 w-4"></i>My Plan
					</a>
				  <?php endif; ?>

				  <a href="<?= base_url('auth/logout') ?>" class="block py-1.5 px-3 text-red-600 hover:bg-red-50 rounded-md text-sm">
					<i class="fas fa-sign-out-alt mr-2 w-4"></i>Logout
				  </a>
				</div>
			  </div>
		<?php endif; ?>

		</div>  
		</div>
	
	<!-- Search Modal - Single for Both Devices -->
	<div x-show="isSearchOpen" x-cloak class="fixed inset-0 z-50">
			<!-- Overlay -->
			<div x-show="isSearchOpen" 
				 @click="isSearchOpen = false"
				 class="fixed inset-0 bg-black/30 transition-opacity"
				 x-transition:enter="ease-out duration-300"
				 x-transition:enter-start="opacity-0"
				 x-transition:enter-end="opacity-100"
				 x-transition:leave="ease-in duration-200"
				 x-transition:leave-start="opacity-100"
				 x-transition:leave-end="opacity-0">
			</div>

			<!-- Modal Container (centered) -->
			<div x-show="isSearchOpen"
				 class="fixed inset-0 lg:flex lg:items-center lg:justify-center"
				 @keydown.escape.window="isSearchOpen = false">
				
				<!-- Scrollable Content Area -->
				<div class="h-full lg:h-auto bg-white w-full lg:max-w-2xl lg:rounded-xl shadow-xl overflow-y-auto">
					<div class="p-6">
						<!-- Header -->
						<div class="flex justify-between items-center mb-6">
							<h3 class="text-xl font-semibold">Search Jobs</h3>
							<button @click="isSearchOpen = false" 
									class="text-gray-500 hover:text-gray-700 p-2">
								<i class="fas fa-times text-lg"></i>
							</button>
						</div>

						<!-- Search Form -->
						<form action="<?= base_url('browse-jobs'); ?>" method="GET" class="space-y-4">
							<!-- Job Title with Clear Button (hidden initially) -->
							<div class="relative">
								<label class="block text-sm font-medium text-gray-700 mb-2">Job Title</label>
								<div class="relative">
									<input type="text" 
										   id="job_profile_input" 
										   autocomplete="off"
										   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 pr-10"
										   placeholder="Software Engineer, Marketing Manager...">
									<button type="button"
											class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 clear-input clear-input-btn hidden"
											data-input="job_profile_input"
											data-list="job_profile_list"
											data-hidden="job_profile_id">
										<i class="fas fa-times-circle"></i>
									</button>
								</div>
								<input type="hidden" name="key_word" id="job_profile_id">
							</div>

							<!-- Experience & Location Grid -->
							<div class="grid gap-4 lg:grid-cols-2">
								<!-- Experience -->
								<div>
									<label class="block text-sm font-medium text-gray-700 mb-2">Experience</label>
									<select name="experience" 
											class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
										<option value="">Any Experience</option>
										<option value="0-1">Fresher (0-1 yrs)</option>
										<option value="1-3">1-3 years</option>
										<option value="3-5">3-5 years</option>
										<option value="5+">5+ years</option>
									</select>
								</div>

								<!-- Location with Clear Button (hidden initially) -->
								<div class="relative">
									<label class="block text-sm font-medium text-gray-700 mb-2">Location</label>
									<div class="relative">
										<input type="text" 
											   id="city_input"
											   autocomplete="off"
											   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 pr-10"
											   placeholder="City or State">
										<button type="button"
												class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 clear-input clear-input-btn hidden"
												data-input="city_input"
												data-list="city_list"
												data-hidden="city_id">
											<i class="fas fa-times-circle"></i>
										</button>
									</div>
									<input type="hidden" name="locations" id="city_id">
								</div>
							</div>

							<!-- Submit Button -->
							<button type="submit" 
									class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-6 rounded-lg transition-colors">
								Search Jobs <i class="fas fa-search ml-2"></i>
							</button>
						</form>
					</div>
				</div>

				<!-- Dropdown Lists (positioned outside scrollable area) -->
				<ul id="job_profile_list" 
					class="fixed hidden bg-white shadow-lg rounded-lg border border-gray-200 max-h-60 overflow-y-auto z-[100]"
					style="min-width: 200px;"></ul>
				<ul id="city_list" 
					class="fixed hidden bg-white shadow-lg rounded-lg border border-gray-200 max-h-60 overflow-y-auto z-[100]"
					style="min-width: 200px;"></ul>
			</div>
		</div>
</nav>