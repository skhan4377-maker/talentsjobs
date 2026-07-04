<script async src="https://www.google.com/adsense/search/ads.js"></script>

<script>
(function(g,o){
  g[o]=g[o]||function(){
    (g[o]['q']=g[o]['q']||[]).push(arguments)
  },
  g[o]['t']=1*new Date
})(window,'_googCsa');
</script>



<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-153460368-1"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());
  gtag('config', 'UA-153460368-1');
</script>
<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-KMJZ4YLZK5"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());
  gtag('config', 'G-KMJZ4YLZK5');
</script>

<?php if (!empty($post_details)) {
  $post_id = $post_details['job_id'];
  $job_title = $post_details['job_title'];
  $cities = !empty($post_details['job_locations']) ? explode(', ', $post_details['job_locations']) : [];
  $employee_company_name = $post_details['company_name'];
  $employee_designation = $post_details['employee_designation'];
  
  $salaryTypeRaw  = $post_details['salary_type'];
  $salaryRangeRaw = $post_details['salary_range'];
  list($minRaw, $maxRaw) = array_map('trim', explode('-', $salaryRangeRaw));
  $minFormatted = '₹ ' . number_format((float)$minRaw, 0);
  $maxFormatted = '₹ ' . number_format((float)$maxRaw, 0);
  $salaryTypeClean = str_replace('-', ' ', strtolower($salaryTypeRaw));
  $salaryTypeClean = ucwords($salaryTypeClean);
  $salary_range = "{$minFormatted} – {$maxFormatted} {$salaryTypeClean}";

  $min_salary = $post_details['min_salary'];
  $max_salary = $post_details['max_salary'];
  $industry_name = $post_details['post_industry_name'];
  $employer_industry_name = $post_details['employer_industry_name'];
  $job_type = $post_details['job_type'];
  $company_contact = $post_details['employer_mobile'];
  $work_email = $post_details['employer_email'];
  $about_company = $post_details['about_company'];
  $job_description = $post_details['job_description'];
  $experience_range = $post_details['experience_range'];
  $qualification = $post_details['education'];
  $logo = $post_details['logo'];
  $call_status = $post_details['call_status'];
  $post_date = $post_details['post_date'];
  $positions_open = $post_details['positions_open'];  
  $is_verified = $post_details['is_verified'];
  
  if (!empty($post_details['deadline_date']) && $post_details['deadline_date'] < date('Y-m-d')) {
    $expired_message = "This job has expired.";
  }
} ?>
<!-- ==================== MAIN SECTION (COMPACT) ==================== -->
<section class="bg-white pt-24 pb-20 md:pt-28 md:pb-24">
  <div class="container mx-auto px-3 sm:px-4 max-w-7xl">
    <div class="flex flex-col lg:flex-row gap-4 lg:gap-8">

      <div class="lg:flex-[0.75]">
        <!-- Job Header Card (sticky on mobile) -->
       <div class="bg-white rounded-xl md:rounded-2xl shadow-sm md:shadow-lg p-4 md:p-6 lg:p-8 mb-4 md:mb-6 sticky top-20 z-40 md:static">
          <?php //if (!empty($expired_message)): ?>
            <!---<div class="mb-3 p-2 rounded bg-yellow-100 border border-yellow-300 text-yellow-800 text-xs flex items-center gap-1">
              <i class="fas fa-exclamation-triangle"></i> <?= $expired_message ?>
            </div>--->
          <?php //endif; ?>

          <div class="flex flex-col md:flex-row justify-between gap-3 md:gap-4">
            <!-- Logo + Title -->
            <div class="flex items-start gap-3">
              <!-- Logo -->
              <div class="flex-shrink-0">
				  <?php
					$companyName = trim($employee_company_name ?? '');
					$initials = 'CO';
					if ($companyName !== '') {
					  $parts = preg_split('/\s+/', $companyName);
					  if (count($parts) >= 2) {
						$initials = mb_strtoupper(mb_substr($parts[0], 0, 1) . mb_substr($parts[1], 0, 1));
					  } else {
						$letters = preg_replace('/[^A-Za-z0-9]/u', '', $companyName);
						$initials = mb_strtoupper(mb_substr($letters, 0, 2)) ?: 'CO';
					  }
					}
					$placeholderDataUri = svg_initials_data_uri($initials);
				  ?>

				  <?php if (!empty($logo)): ?>
					<?php $logoUrl = preg_match('/^https?:\/\//', $logo) ? $logo : base_url(ltrim($logo, '/')); ?>
					<img src="<?= htmlspecialchars($logoUrl) ?>" 
						 alt="<?= htmlspecialchars($employee_company_name ?? 'Company') ?>"
						 class="w-10 h-10 md:w-16 md:h-16 lg:w-20 lg:h-20 rounded-lg md:rounded-xl shadow object-contain p-2 bg-white border border-gray-100">
				  <?php else: ?>
					<img src="<?= $placeholderDataUri ?>" 
						 alt="<?= htmlspecialchars($employee_company_name ?? 'Company') ?>"
						 class="w-10 h-10 md:w-16 md:h-16 lg:w-20 lg:h-20 rounded-lg md:rounded-xl shadow object-contain p-2 bg-white border border-gray-100">
				  <?php endif; ?>
				</div>

              <!-- Job Info -->
              <div class="space-y-1 md:space-y-2">
                <div class="flex flex-wrap items-center gap-1">
                  <span class="text-xs font-medium bg-blue-100 text-blue-800 px-2 py-0.5 rounded-full">
                    <?= ucfirst($job_type ?? '') ?>
                  </span>
                  <?php if(!empty($is_verified)): ?>
                    <span class="inline-flex items-center text-xs bg-emerald-100 text-emerald-700 px-2 py-0.5 rounded-full">
                      <i class="fas fa-check-circle mr-1"></i>Verified
                    </span>
                  <?php endif; ?>
                </div>
                <h1 class="text-lg md:text-2xl lg:text-3xl font-bold text-[#1a0dab] leading-tight 
           md:whitespace-nowrap md:overflow-hidden md:text-ellipsis max-w-full">
                  <?= ucfirst($job_title ?? '') ?>
                </h1>
                <div class="flex items-center gap-1 text-sm md:text-base">
                  <i class="fas fa-building text-gray-500"></i>
                  <span class="text-[#1a0dab] font-medium"><?= htmlspecialchars(ucfirst($employee_company_name ?? '')) ?></span>
                </div>
              </div>
            </div>
            <!-- Desktop Action Buttons -->
            <div class="hidden md:block shrink-0">
              <?php if($this->session->userdata('logged_in') && $this->session->userdata('role') === 'candidate'): ?>
                <div class="flex flex-col gap-2">
                  <button id="applyButton" data-postid="<?= $post_id ?>"
                    class="inline-flex items-center justify-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg shadow transition">
                    <span class="btn-text flex items-center"><i class="fas fa-bolt mr-1"></i>Quick Apply</span>
                    <span class="btn-loader hidden"><i class="fas fa-spinner fa-spin mr-1"></i>Applying...</span>
                  </button>
                  <?php $job_slug = make_slug($job_title); ?>
                  <a href="<?= base_url('browse-jobs/' . $job_slug . '?key_word=' . urlencode(ucfirst($job_title))) ?>"
                     class="inline-flex items-center justify-center px-4 py-2 border border-gray-300 text-gray-700 bg-white hover:bg-gray-100 text-sm font-medium rounded-lg shadow-sm transition">
                     <i class="fas fa-search mr-1 text-gray-500"></i>Similar
                  </a>
                </div>
              <?php else: ?>
                <!-- Guest: single Apply button redirects to login -->
                <div class="flex flex-col gap-2">
                  <button onclick="redirectTo('<?= base_url('auth/login') ?>')"
                    class="inline-flex items-center justify-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg shadow transition">
                    <i class="fas fa-bolt mr-1"></i>Apply
                  </button>
                  <?php $job_slug = make_slug($job_title); ?>
                  <a href="<?= base_url('browse-jobs/' . $job_slug . '?key_word=' . urlencode(ucfirst($job_title))) ?>"
                     class="inline-flex items-center justify-center px-4 py-2 border border-gray-300 text-gray-700 bg-white hover:bg-gray-100 text-sm font-medium rounded-lg shadow-sm transition">
                     <i class="fas fa-search mr-1 text-gray-500"></i>Similar
                  </a>
                </div>
              <?php endif; ?>
            </div>
          </div>
          
          <!-- Mobile Sticky Apply Bar -->
          <div class="md:hidden fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 shadow-lg p-4 z-50">
			  <div class="flex gap-3">
				<?php if ($this->session->userdata('logged_in')): ?>
				  <button id="mobileApply" data-postid="<?= $post_id ?>"
					class="flex-1 inline-flex items-center justify-center px-5 py-3 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg shadow">
					<span class="btn-text flex items-center">
					  <i class="fas fa-bolt mr-2"></i>Apply
					</span>
					<span class="btn-loader hidden">
					  <i class="fas fa-spinner fa-spin mr-2"></i>...
					</span>
				  </button>
				<?php else: ?>
				  <button onclick="redirectTo('<?= base_url('auth/login') ?>')"
					class="flex-1 inline-flex items-center justify-center px-5 py-3 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg shadow">
					<i class="fas fa-bolt mr-2"></i>Apply
				  </button>
				<?php endif; ?>
			  </div>
			</div>
        </div>
      
        <div id="afscontainer1" class="mt-4"></div>
        <div id="relatedsearches1"></div>
                   
        <!-- Tabs Navigation (compact) -->
        <div class="sticky top-14 md:top-16 bg-white z-30 border-b border-gray-200">
          <div class="flex overflow-x-auto scrollbar-hide">
            <button class="tab-btn flex-shrink-0 px-3 py-2 md:px-5 md:py-3 text-xs md:text-sm font-medium whitespace-nowrap border-b-2 transition-colors duration-200 text-blue-600 border-blue-600"
                    data-target="overview">Overview</button>
            <button class="tab-btn flex-shrink-0 px-3 py-2 md:px-5 md:py-3 text-xs md:text-sm font-medium whitespace-nowrap border-b-2 border-transparent text-gray-500 hover:text-blue-600 transition-colors duration-200"
                    data-target="description">Description</button>
            <button class="tab-btn flex-shrink-0 px-3 py-2 md:px-5 md:py-3 text-xs md:text-sm font-medium whitespace-nowrap border-b-2 border-transparent text-gray-500 hover:text-blue-600 transition-colors duration-200"
                    data-target="requirements">Requirements</button>
            <button class="tab-btn flex-shrink-0 px-3 py-2 md:px-5 md:py-3 text-xs md:text-sm font-medium whitespace-nowrap border-b-2 border-transparent text-gray-500 hover:text-blue-600 transition-colors duration-200"
                    data-target="company">Company</button>
          </div>
        </div>

        <!-- Tab Contents -->
        <div class="bg-white rounded-lg md:rounded-2xl shadow-sm md:shadow-lg p-4 md:p-6 mt-4">
          <!-- Overview Tab -->
          <div id="overview" class="tab-content active">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
              <!-- Locations -->
              <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                <div class="bg-blue-100 p-2 rounded mr-3">
                  <i class="fas fa-map-marker-alt text-blue-600 text-sm md:text-base"></i>
                </div>
                <div class="flex-1 min-w-0">
                  <p class="text-xs text-gray-500">Locations</p>
                  <div class="flex flex-wrap gap-1 mt-1">
                    <?php
                      $totalCities = count($cities);
                      $maxShow = 2;
                    ?>
                    <?php if(!empty($cities)): ?>
                      <?php foreach(array_slice($cities, 0, $maxShow) as $city): ?>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs bg-blue-100 text-blue-800"><?= ucfirst(trim($city)) ?></span>
                      <?php endforeach; ?>
                      <?php if($totalCities > $maxShow): $remaining = array_slice($cities, $maxShow); ?>
                        <div class="extra-cities-container hidden flex-wrap gap-1 mt-0.5">
                          <?php foreach($remaining as $city): ?>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs bg-blue-100 text-blue-800"><?= ucfirst(trim($city)) ?></span>
                          <?php endforeach; ?>
                        </div>
                        <button type="button" class="show-more-cities text-xs px-1.5 py-0.5 rounded bg-gray-200 text-green-700 font-medium">+<?= count($remaining) ?> more</button>
                      <?php endif; ?>
                    <?php else: ?>
                      <span class="text-gray-500 text-xs">Remote Jobs</span>
                    <?php endif; ?>
                  </div>
                </div>
              </div>
              <!-- Salary -->
              <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                <div class="bg-blue-100 p-2 rounded mr-3"><i class="fas fa-coins text-blue-600 text-sm md:text-base"></i></div>
                <div><p class="text-xs text-gray-500">Salary</p><p class="text-sm font-medium"><?= $salary_range ?></p></div>
              </div>
              <!-- Experience -->
              <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                <div class="bg-blue-100 p-2 rounded mr-3"><i class="fas fa-briefcase text-blue-600 text-sm md:text-base"></i></div>
                <div><p class="text-xs text-gray-500">Experience</p><p class="text-sm font-medium"><?= $experience_range ?> years</p></div>
              </div>
              <!-- Posted -->
              <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                <div class="bg-blue-100 p-2 rounded mr-3"><i class="fas fa-clock text-blue-600 text-sm md:text-base"></i></div>
                <div><p class="text-xs text-gray-500">Posted</p><p class="text-sm font-medium"><?= timeAgo(strtotime($post_date)) ?></p></div>
              </div>
            </div>
          </div>

          <!-- Description Tab -->
          <div id="description" class="tab-content hidden">
            <h3 class="text-lg md:text-xl font-bold mb-3">Job Description</h3>
            <div class="prose prose-sm max-w-none"><?= $job_description ?></div>
          </div>

          <!-- Requirements Tab -->
          <div id="requirements" class="tab-content hidden">
            <h3 class="text-lg md:text-xl font-bold mb-3">Requirements</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
              <!-- Education -->
              <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                <div class="bg-blue-100 p-2 rounded mr-3"><i class="fas fa-graduation-cap text-blue-600 text-sm md:text-base"></i></div>
                <div><p class="text-xs text-gray-500">Education</p><p class="text-sm font-medium"><?= ucfirst($qualification) ?></p></div>
              </div>
              <?php if (!empty($post_details['skills'])): 
                $skills = array_map('trim', explode(',', $post_details['skills']));
                $totalSkills = count($skills);
                $maxShow = 2;
              ?>
              <div class="flex items-start p-3 bg-gray-50 rounded-lg">
                <div class="bg-blue-100 p-2 rounded mr-3 mt-1"><i class="fas fa-tools text-blue-600 text-sm md:text-base"></i></div>
                <div class="flex-1">
                  <p class="text-xs text-gray-500 mb-1">Skills</p>
                  <div class="flex flex-wrap gap-1">
                    <?php foreach(array_slice($skills, 0, $maxShow) as $skill): ?>
                      <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs bg-blue-100 text-blue-800"><?= ucfirst($skill) ?></span>
                    <?php endforeach; ?>
                    <?php if($totalSkills > $maxShow): $remaining = array_slice($skills, $maxShow); ?>
                      <div class="extra-skills-container hidden flex-wrap gap-1 mt-0.5">
                        <?php foreach($remaining as $skill): ?>
                          <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs bg-blue-100 text-blue-800"><?= ucfirst($skill) ?></span>
                        <?php endforeach; ?>
                      </div>
                      <button type="button" class="show-more-skills text-xs px-1.5 py-0.5 rounded bg-gray-200 text-blue-700 font-medium">+<?= count($remaining) ?> more</button>
                    <?php endif; ?>
                  </div>
                </div>
              </div>
              <?php endif; ?>
              <?php if (!empty($positions_open)): ?>
              <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                <div class="bg-blue-100 p-2 rounded mr-3"><i class="fas fa-users text-blue-600 text-sm md:text-base"></i></div>
                <div><p class="text-xs text-gray-500">Openings</p><p class="text-sm font-medium"><?= (int)$positions_open ?> Position<?= ((int)$positions_open > 1) ? 's' : '' ?></p></div>
              </div>
              <?php endif; ?>
            </div>
          </div>

          <!-- Company Tab -->
          <div id="company" class="tab-content hidden">
            <h3 class="text-lg md:text-xl font-bold mb-3 flex items-center gap-2">
              About Company
              <?php if($is_verified): ?><span class="inline-flex items-center text-xs bg-emerald-100 text-emerald-700 px-2 py-0.5 rounded-full"><i class="fas fa-check-circle mr-1"></i>Verified</span><?php endif; ?>
            </h3>
            <div class="space-y-3">
              <div class="flex items-center gap-3">
                <div class="bg-blue-100 p-3 rounded-lg"><i class="fas fa-building text-xl text-blue-600"></i></div>
                <div><h4 class="font-semibold text-base"><?= htmlspecialchars($employee_company_name) ?></h4><p class="text-xs text-gray-600"><?= htmlspecialchars($employer_industry_name) ?></p></div>
              </div>
              <div class="grid grid-cols-2 gap-2">
                <div class="bg-gray-50 p-3 rounded-lg"><p class="text-xs text-gray-500">Company Size</p><p class="text-sm font-medium"><?= $post_details['company_size'] ?> Employees</p></div>
                <div class="bg-gray-50 p-3 rounded-lg"><p class="text-xs text-gray-500">Founded</p><p class="text-sm font-medium"><?= date('F j, Y', strtotime($post_details['company_founded'])) ?></p></div>
              </div>
              <div class="prose prose-sm max-w-none"><?= $post_details['about_company'] ?></div>
            </div>
          </div>

          <!-- Ad Section -->
          <div class="mt-4 mx-auto max-w-4xl px-2">
            <div class="bg-white rounded-lg shadow-sm overflow-hidden p-3"><?= $this->load->view('common/header_ads_tj', '', TRUE)?></div>
          </div>
        </div>
        
	  </div>

      <!-- Sidebar (compact) -->
      <div class="lg:flex-[0.25] mt-4 lg:mt-0">
        <div class="sticky top-20 space-y-4">
          <!-- Similar Jobs -->
          <div class="bg-white rounded-lg md:rounded-xl shadow-sm md:shadow-lg p-4">
            <h3 class="text-base font-bold mb-3">Similar Jobs</h3>
            <div class="space-y-3">
              <?php foreach ($mightBeLike as $key): ?>
                <a href="<?= site_url($key['slug']) ?>?key_word=<?= $key['job_title'] ?>" class="block group">
                  <div class="p-3 hover:bg-blue-50 rounded-lg border border-gray-200 transition">
                    <h4 class="font-semibold text-sm group-hover:text-blue-600 truncate"><?= mb_strimwidth(ucfirst($key['job_title']), 0, 30, "...") ?></h4>
                    <p class="text-xs text-gray-600 mt-1 truncate"><?= $key['company_name'] ?></p>
                    <?php $cities = !empty($key['job_locations']) ? array_map('trim', explode(', ', $key['job_locations'])) : []; ?>
                    <?php if(!empty($cities)): ?>
                     <div class="flex flex-wrap gap-1 mt-2">
                        <?php $first = array_slice($cities, 0, 1); $remaining = array_slice($cities, 1); ?>
                        <?php foreach($first as $city): ?>
                          <span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-xs bg-blue-100 text-blue-800"><?= ucfirst($city) ?></span>
                        <?php endforeach; ?>
                        <?php if(count($remaining) > 0): ?>
                          <div class="extra-cities-side hidden flex-wrap gap-1 mt-0.5">
                            <?php foreach($remaining as $city): ?>
                              <span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-xs bg-blue-100 text-blue-800"><?= ucfirst($city) ?></span>
                            <?php endforeach; ?>
                          </div>
                          <button type="button" class="show-more-cities-side text-xs px-1.5 py-0.5 rounded bg-gray-200 text-blue-700 font-medium">+<?= count($remaining) ?></button>
                        <?php endif; ?>
                      </div>
                    <?php endif; ?>
                  </div>
                </a>
              <?php endforeach; ?>
            </div>
          </div>
			
			<!-- PROFILE BOOSTER ADS -->
			<div class="mt-4">

				<!-- ADS LABEL -->
				<div class="flex items-center justify-center gap-2 mb-2">
					<span class="h-px flex-1 bg-gray-200"></span>

					<span class="text-[10px] uppercase tracking-wider text-gray-400 font-medium">
						Sponsored
					</span>

					<span class="h-px flex-1 bg-gray-200"></span>
				</div>

				<!-- ADS CARD -->
				<a href="<?= base_url('career-services') ?>"
				   class="group block rounded-xl overflow-hidden border border-blue-100 bg-white hover:border-blue-300 transition-all duration-300 shadow-sm">

					<!-- TOP BAR -->
					<div class="bg-gradient-to-r from-blue-50 to-indigo-50 px-3 py-2 border-b border-blue-100">

						<div class="flex items-center gap-2">

							<div class="w-9 h-9 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0">
								🚀
							</div>

							<div class="min-w-0">
								<h3 class="text-sm font-semibold text-gray-900 truncate">
									Profile Booster
								</h3>

								<p class="text-[11px] text-gray-600 truncate">
									Get more recruiter visibility instantly
								</p>
							</div>

						</div>

					</div>

					<!-- CONTENT -->
					<div class="p-3 bg-gradient-to-br from-white via-blue-50 to-indigo-50">

						<!-- TITLE -->
						<div class="mb-3">

							<span class="inline-flex items-center px-2 py-1 rounded-full bg-green-100 text-green-700 text-[10px] font-semibold mb-2">
								BOOST YOUR PROFILE
							</span>

							<h2 class="text-lg font-bold text-gray-900 leading-tight">
								Get 5x More
								<span class="text-blue-600">Profile Views</span>
							</h2>

							<p class="mt-1 text-xs text-gray-600 leading-relaxed">
								Rank higher in recruiter searches and unlock verified employer contacts.
							</p>

						</div>

						<!-- FEATURES -->
						<div class="grid grid-cols-2 gap-2 mb-3">

							<div class="bg-white rounded-lg p-2 border border-gray-100">
								<div class="flex items-center gap-2">
									<span class="text-sm">📈</span>

									<div class="min-w-0">
										<p class="text-[11px] font-semibold text-gray-900 truncate">
											Top Ranking
										</p>

										<p class="text-[10px] text-gray-500 truncate">
											Higher visibility
										</p>
									</div>
								</div>
							</div>

							<div class="bg-white rounded-lg p-2 border border-gray-100">
								<div class="flex items-center gap-2">
									<span class="text-sm">👀</span>

									<div class="min-w-0">
										<p class="text-[11px] font-semibold text-gray-900 truncate">
											5x Views
										</p>

										<p class="text-[10px] text-gray-500 truncate">
											More recruiter reach
										</p>
									</div>
								</div>
							</div>

							<div class="bg-white rounded-lg p-2 border border-gray-100">
								<div class="flex items-center gap-2">
									<span class="text-sm">📊</span>

									<div class="min-w-0">
										<p class="text-[11px] font-semibold text-gray-900 truncate">
											Weekly Reports
										</p>

										<p class="text-[10px] text-gray-500 truncate">
											Track performance
										</p>
									</div>
								</div>
							</div>

							<div class="bg-white rounded-lg p-2 border border-gray-100">
								<div class="flex items-center gap-2">
									<span class="text-sm">✅</span>

									<div class="min-w-0">
										<p class="text-[11px] font-semibold text-gray-900 truncate">
											Verified HR
										</p>

										<p class="text-[10px] text-gray-500 truncate">
											Unlimited access
										</p>
									</div>
								</div>
							</div>

						</div>

						<!-- CTA -->
						<div class="flex items-center gap-2">

							<div class="flex-1 bg-blue-600 group-hover:bg-blue-700 text-white text-center py-2 rounded-lg text-sm font-semibold transition">
								Boost Profile
							</div>

							<div class="px-3 py-2 rounded-lg border border-blue-200 text-blue-600 text-xs font-medium bg-white">
								View Plans
							</div>

						</div>

					</div>

				</a>

			</div>

      <!-- Recruiter Info -->
        <div class="bg-white rounded-lg md:rounded-xl shadow-sm md:shadow-lg p-4">
        
          <h3 class="text-base font-bold mb-3">Recruiter Info</h3>
        
          <?php
            $hasActivePlan = $hasActivePlan ?? false;
        
            $isLoggedIn = $this->session->userdata('logged_in')
                          && $this->session->userdata('role') === 'candidate';
          ?>
        
          <?php if (!$isLoggedIn): ?>
        
            <!-- Guest User Login Box -->
            <div class="relative rounded-xl overflow-hidden border border-dashed border-gray-300 bg-gray-50 min-h-[170px] flex items-center justify-center p-4">
        
              <!-- Fake blurred data -->
              <div class="filter blur-sm opacity-40 pointer-events-none space-y-3 text-xs w-full">
        
                <div class="flex items-center gap-2">
                  <span class="text-gray-400 font-medium">Contact:</span>
                  <span class="text-gray-600">+91-******123</span>
                </div>
        
                <div class="flex items-center gap-2">
                  <span class="text-gray-400 font-medium">Email:</span>
                  <span class="text-gray-600">e***@company.com</span>
                </div>
        
              </div>
        
              <!-- Overlay -->
              <div class="absolute inset-0 z-10 flex flex-col items-center justify-center bg-white/90 backdrop-blur-sm p-4 text-center">
        
                <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center mb-3">
                  <i class="fas fa-user-lock text-blue-600 text-lg"></i>
                </div>
        
                <p class="text-sm font-semibold text-gray-800">
                  Login to view recruiter details
                </p>
        
                <p class="text-xs text-gray-500 mt-1 mb-3">
                  Please login as candidate to continue
                </p>
        
                <a href="<?= base_url('auth/login') ?>"
                   class="inline-flex items-center px-5 py-2.5 mt-1 bg-gradient-to-r from-blue-600 to-indigo-600 text-white text-sm font-medium rounded-full hover:shadow-lg hover:scale-[1.02] transition-all duration-200">
        
                  <i class="fas fa-sign-in-alt mr-2"></i>
                  Login Now
        
                </a>
        
              </div>
        
            </div>
        
          <?php elseif ($hasActivePlan): ?>

            <!-- Premium User Contact Details -->
            <div class="space-y-3">
            
              <!-- Phone -->
              <div class="flex items-start justify-between gap-3 p-3 rounded-lg bg-gray-50 border border-gray-100">
            
                <div class="flex items-start gap-3">
            
                  <div class="bg-blue-100 p-2 rounded-lg">
                    <i class="fas fa-phone-alt text-blue-600 text-sm"></i>
                  </div>
            
                  <div class="min-w-0">
            
                    <p class="text-xs text-gray-500 mb-0.5">
                      Contact
                    </p>
            
                    <?php if ($call_status == 1): ?>
            
                      <!-- Hidden Real Number -->
                      <span id="phoneText"
                            class="text-sm font-semibold text-gray-800 break-all hidden">
            
                        <?= htmlspecialchars($company_contact) ?>
            
                      </span>
            
                      <!-- Default Mask -->
                      <span id="phoneHidden"
                            class="text-sm text-gray-400">
            
                        **********
            
                      </span>
            
                    <?php else: ?>
            
                      <span class="text-xs text-gray-400">
                        Mobile hidden
                      </span>
            
                    <?php endif; ?>
            
                  </div>
            
                </div>
            
                <?php if ($call_status == 1): ?>
            
                  <button type="button"
                          id="togglePhoneBtn"
                          class="text-xs bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded-lg transition">
            
                    Show
            
                  </button>
            
                <?php endif; ?>
            
              </div>
            
              <!-- Email -->
              <div class="flex items-start justify-between gap-3 p-3 rounded-lg bg-gray-50 border border-gray-100">
            
                <div class="flex items-start gap-3">
            
                  <div class="bg-blue-100 p-2 rounded-lg">
                    <i class="fas fa-envelope text-blue-600 text-sm"></i>
                  </div>
            
                  <div class="min-w-0">
            
                    <p class="text-xs text-gray-500 mb-0.5">
                      Email
                    </p>
            
                    <!-- Hidden Real Email -->
                    <span id="emailText"
                          class="text-sm font-semibold text-gray-800 break-all hidden">
            
                      <?= htmlspecialchars($work_email) ?>
            
                    </span>
            
                    <!-- Default Mask -->
                    <span id="emailHidden"
                          class="text-sm text-gray-400">
            
                      e***@company.com
            
                    </span>
            
                  </div>
            
                </div>
            
                <button type="button"
                        id="toggleEmailBtn"
                        class="text-xs bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded-lg transition">
            
                  Show
            
                </button>
            
              </div>
            
            </div>
            
            <?php else: ?>
        
            <!-- Logged In But No Premium -->
            <div class="relative rounded-xl overflow-hidden border border-dashed border-gray-300 bg-gray-50 min-h-[170px] flex items-center justify-center p-4">
        
              <!-- Fake blurred data -->
              <div class="filter blur-sm opacity-40 pointer-events-none space-y-3 text-xs w-full">
        
                <div class="flex items-center gap-2">
                  <span class="text-gray-400 font-medium">Contact:</span>
                  <span class="text-gray-600">+91-******123</span>
                </div>
        
                <div class="flex items-center gap-2">
                  <span class="text-gray-400 font-medium">Email:</span>
                  <span class="text-gray-600">e***@company.com</span>
                </div>
        
              </div>
        
              <!-- Overlay -->
              <div class="absolute inset-0 z-10 flex flex-col items-center justify-center bg-white/90 backdrop-blur-sm p-4 text-center">
        
                <div class="w-12 h-12 rounded-full bg-amber-100 flex items-center justify-center mb-3">
                  <i class="fas fa-lock text-amber-600 text-lg"></i>
                </div>
        
                <p class="text-sm font-semibold text-gray-800">
                  Unlock contact details
                </p>
        
                <p class="text-xs text-gray-500 mt-1 mb-3">
                  Upgrade to premium plan to access recruiter contact info
                </p>
        
                <a href="<?= base_url('career-services') ?>"
                   class="inline-flex items-center px-5 py-2.5 mt-1 bg-gradient-to-r from-amber-500 to-orange-600 text-white text-sm font-medium rounded-full hover:shadow-lg hover:scale-[1.02] transition-all duration-200">
        
                  <i class="fas fa-crown mr-2"></i>
                  View Premium Plans
        
                </a>
        
              </div>
        
            </div>
        
          <?php endif; ?>
        
        </div>
        </div>
      </div>
    </div>
	
	
  </div>
</section>

<?php //$this->load->view('templates/resume_templates_slider'); ?>

<!-- Complete Profile Modal (compact) -->
<div id="completeProfileModal" class="fixed inset-0 z-50 hidden bg-black bg-opacity-50 backdrop-blur-sm transition-opacity duration-300">
  <div class="flex items-center justify-center min-h-screen px-3">
    <div class="bg-white rounded-xl w-full max-w-md transform transition-all duration-300 scale-95 opacity-0 max-h-[90vh] overflow-y-auto shadow-xl">
      <div class="p-4 border-b flex justify-between items-center">
        <h3 class="text-base font-bold">Complete Your Profile</h3>
        <button id="modalClose" class="text-xl text-gray-500 hover:text-gray-700">&times;</button>
      </div>
      <div class="p-4 space-y-4">
        <form id="completeProfileForm" class="space-y-4">
          <input type="hidden" name="complete_profle" value="update">
          <label class="relative block">
            <select name="work_status" class="peer w-full px-3 py-2 rounded-lg border border-gray-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-300 outline-none transition text-sm">
              <option value="Fresher" <?= (isset($candidate_information['work_status']) && $candidate_information['work_status'] == 'Fresher') ? 'selected' : '' ?>>Fresher</option>
              <option value="Experience" <?= (isset($candidate_information['work_status']) && $candidate_information['work_status'] == 'Experience') ? 'selected' : '' ?>>Experience</option>
            </select>
            <span class="absolute left-3 top-2 text-gray-400 text-xs transition-all peer-focus:-top-2 peer-focus:text-blue-600">Work Status</span>
          </label>

          <!-- Industry -->
          <label class="relative block">
            <input type="text" id="industry_input" name="industry_name" placeholder=" "
                   data-url="<?= base_url('Common/get_search_data?type=industry') ?>"
                   class="peer w-full px-3 py-2 rounded-lg border border-gray-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-300 outline-none transition text-sm"
                   autocomplete="off">
            <span class="absolute left-3 top-2 text-gray-400 text-xs transition-all peer-placeholder-shown:top-2.5 peer-placeholder-shown:text-gray-500 peer-placeholder-shown:text-sm peer-focus:-top-2 peer-focus:text-blue-600">Industry</span>
            <input type="hidden" name="industry_id" id="industry_id">
            <ul id="industry_list" class="absolute z-50 w-full bg-white shadow-lg rounded-b-lg mt-1 max-h-60 overflow-y-auto"></ul>
          </label>

          <!-- Functional Area -->
          <label class="relative block">
            <input type="text" id="functional_input" name="functional_name" placeholder=" "
                   data-url="<?= base_url('Common/get_search_data?type=functional_area') ?>"
                   class="peer w-full px-3 py-2 rounded-lg border border-gray-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-300 outline-none transition text-sm"
                   autocomplete="off">
            <span class="absolute left-3 top-2 text-gray-400 text-xs transition-all peer-placeholder-shown:top-2.5 peer-placeholder-shown:text-gray-500 peer-placeholder-shown:text-sm peer-focus:-top-2 peer-focus:text-blue-600">Functional Area</span>
            <input type="hidden" name="functional_id" id="functional_id">
            <ul id="functional_list" class="absolute z-50 w-full bg-white shadow-lg rounded-b-lg mt-1 max-h-60 overflow-y-auto"></ul>
          </label>

          <!-- Location -->
          <label class="relative block">
            <input type="text" id="city_input_employer" name="city_name" placeholder=" "
                   data-url="<?= base_url('Common/get_search_data') ?>"
                   class="peer w-full px-3 py-2 rounded-lg border border-gray-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-300 outline-none transition text-sm"
                   autocomplete="off">
            <span class="absolute left-3 top-2 text-gray-400 text-xs transition-all peer-placeholder-shown:top-2.5 peer-placeholder-shown:text-gray-500 peer-placeholder-shown:text-sm peer-focus:-top-2 peer-focus:text-blue-600">Location (min 2 chars)</span>
            <input type="hidden" name="city_id" id="city_id_employer">
            <ul id="city_list_employer" class="absolute z-50 w-full bg-white shadow-lg rounded-b-lg mt-1 max-h-60 overflow-y-auto"></ul>
          </label>

          <button type="submit" class="w-full bg-gradient-to-r from-blue-600 to-purple-600 text-white py-2 px-3 rounded-lg text-sm font-semibold hover:shadow-lg transition flex items-center justify-center gap-2">
            <span class="btn-text">Save & Continue <i class="fas fa-arrow-right"></i></span>
            <span class="btn-loader hidden"><i class="fas fa-spinner fa-spin"></i>Saving...</span>
          </button>
        </form>
      </div>
    </div>
  </div>
</div>

<script>

// GET PARAM
function getParam(name) {
  const params = new URLSearchParams(window.location.search);
  return params.get(name);
}

// CLEAN FUNCTION
function cleanKeyword(text) {
  return decodeURIComponent(text || '')
    .replace(/,/g, ' ')
    .replace(/\+/g, ' ')
    .replace(/\s+/g, ' ')
    .trim();
}

// PRIORITY:
// 1. JOB TITLE (BEST)
// 2. key_word param
// 3. fallback

let keyword = "";

// ✅ 1. JOB TITLE
if (typeof jobTitle !== "undefined" && jobTitle.length > 3) {
  keyword = cleanKeyword(jobTitle);
}

// ✅ 2. URL PARAM
if (!keyword) {
  keyword = cleanKeyword(getParam("key_word"));
}

// ✅ 3. FALLBACK
if (!keyword) {
  keyword = "Jobs";
}

// OPTIONAL: remove weak words
const stopWords = ["for","and","to","the","now","years","apply"];
keyword = keyword
  .split(" ")
  .filter(w => !stopWords.includes(w.toLowerCase()))
  .join(" ");

// LIMIT WORDS
keyword = keyword.split(" ").slice(0, 5).join(" ");

console.log("Final Ads Query:", keyword);

var pageOptions = {
  "pubId": "partner-pub-9268075008862469",
  "query": keyword,
  "styleId": "8476968910",
  "adsafe": "high",
  "relatedSearchTargeting": "query",
  "resultsPageBaseUrl": "https://talentsjobs.in/browse-jobs/",
  "resultsPageQueryParam": "key_word"
};

var adblock1 = {
  "container": "afscontainer1"
};

var rsblock1 = {
  "container": "relatedsearches1",
  "relatedSearches": 10
};

_googCsa('ads', pageOptions, adblock1, rsblock1);

</script>
<script>

$(document).on('click', '#togglePhoneBtn', function () {

    $('#phoneText').toggleClass('hidden');
    $('#phoneHidden').toggleClass('hidden');

    if ($('#phoneText').hasClass('hidden')) {
        $(this).text('Show');
    } else {
        $(this).text('Hide');
    }

});

$(document).on('click', '#toggleEmailBtn', function () {

    $('#emailText').toggleClass('hidden');
    $('#emailHidden').toggleClass('hidden');

    if ($('#emailText').hasClass('hidden')) {
        $(this).text('Show');
    } else {
        $(this).text('Hide');
    }

});

</script>
<script>
// Tab Functionality
document.querySelectorAll('.tab-btn').forEach(tab => {
  tab.addEventListener('click', function() {
    const target = this.dataset.target;
    document.querySelectorAll('.tab-btn').forEach(t => {
      t.classList.remove('text-blue-600', 'border-blue-600');
      t.classList.add('text-gray-500', 'border-transparent');
    });
    this.classList.remove('text-gray-500', 'border-transparent');
    this.classList.add('text-blue-600', 'border-blue-600');
    document.querySelectorAll('.tab-content').forEach(content => {
      content.classList.toggle('hidden', content.id !== target);
    });
  });
});

// Show more/less for cities and skills
$(document).on('click', '.show-more-cities', function(e) {
  e.preventDefault(); e.stopPropagation();
  let extra = $(this).prev('.extra-cities-container');
  extra.toggleClass('hidden flex-wrap gap-1 mt-0.5');
  $(this).text(extra.hasClass('hidden') ? '+' + extra.children('span').length + ' more' : 'Show less');
});
$(document).on('click', '.show-more-skills', function() {
  let extra = $(this).siblings('.extra-skills-container');
  extra.toggleClass('hidden flex-wrap gap-1 mt-0.5');
  $(this).text(extra.hasClass('hidden') ? '+' + extra.children('span').length + ' more' : 'Show less');
});
$(document).on('click', '.show-more-cities-side', function(e) {
  e.preventDefault(); e.stopPropagation();
  let extra = $(this).prev('.extra-cities-side');
  extra.toggleClass('hidden flex-wrap gap-1 mt-0.5');
  $(this).text(extra.hasClass('hidden') ? '+' + extra.children('span').length : 'Show less');
});

function redirectTo(url) { window.location.href = url; }

// Toastr config
toastr.options = { closeButton: true, progressBar: true, positionClass: 'toast-top-right', timeOut: 3000 };

<?php if($this->session->userdata('logged_in') && $this->session->userdata('role') === 'candidate'): ?>
// Candidate-specific JS (apply, modal, autocomplete) – using global CSRF functions
$(document).ready(function () {
  // AutoCompleteWidget initialization (ensure it exists)
  if (typeof AutoCompleteWidget !== 'undefined') {
    new AutoCompleteWidget({
      inputSelector: '#city_input_employer',
      hiddenSelector: '#city_id_employer',
      listSelector: '#city_list_employer',
      apiUrl: '<?= base_url("Common/get_cities") ?>',
      minChars: 2,
      multiSelect: false,
      maxResults: 10
    });
    new AutoCompleteWidget({
      inputSelector: '#functional_input',
      hiddenSelector: '#functional_id',
      listSelector: '#functional_list',
      apiUrl: '<?= base_url("Common/get_search_data?type=functional_area") ?>',
      minChars: 1,
      multiSelect: false,
      maxResults: 10
    });
    new AutoCompleteWidget({
      inputSelector: '#industry_input',
      hiddenSelector: '#industry_id',
      listSelector: '#industry_list',
      apiUrl: '<?= base_url("Common/get_search_data?type=industry") ?>',
      minChars: 1,
      multiSelect: false,
      maxResults: 10
    });
  }

  function openModal() {
    $('#completeProfileModal').removeClass('hidden').css('opacity', '1');
    $('#completeProfileModal .bg-white').removeClass('scale-95 opacity-0').addClass('scale-100 opacity-100');
  }
  function closeModal() {
    $('#completeProfileModal .bg-white').removeClass('scale-100 opacity-100').addClass('scale-95 opacity-0');
    setTimeout(() => $('#completeProfileModal').addClass('hidden').css('opacity', '0'), 300);
  }
  $('#modalClose, #completeProfileModal').on('click', function(e) { if (e.target === this) closeModal(); });
  $(document).on('keydown', function(e) { if (e.key === 'Escape') closeModal(); });
    
    // Define slugify function
   function make_slug(string) {
        return string.toLowerCase()
            .replace(/[^a-z0-9]+/g, '-')
            .replace(/^-+|-+$/g, '');
    }

  // Apply button
  $(document).on('click', '#applyButton, #mobileApply', function () {
    const button = $(this);
    const postId = button.data('postid');
    button.find('.btn-text').addClass('hidden');
    button.find('.btn-loader').removeClass('hidden');
    button.prop('disabled', true);
    const csrf_token = getCSRFToken();
    const csrf_name  = getCSRFName();
    $.ajax({
      url: '<?= base_url('candidate/Applied/submitJobApplication') ?>',
      method: 'POST',
      data: { pid: postId, [csrf_name]: csrf_token },
      dataType: 'json',
      complete: function () {
        button.find('.btn-text').removeClass('hidden');
        button.find('.btn-loader').addClass('hidden');
        button.prop('disabled', false);
      },
      success: function (response) {
        if (response.csrf_token) updateCSRFToken(response.csrf_token, response.csrf_name);
        if (response.error_msg) { openModal(); return; }
        toastr.success(response.success_msg);
        setTimeout(function () {
          const job_slug = make_slug(response.similar_job + '-jobs');
          const query = encodeURIComponent(response.similar_job);
          window.location.href = '<?= base_url("browse-jobs/") ?>' + job_slug + '?key_word=' + query;
        }, 1000);
      },
      error: function () { toastr.error('An error occurred. Please try again.'); }
    });
  });

  // Modal form
  $('#completeProfileForm').on('submit', function (e) {
    e.preventDefault();
    const form = $(this);
    const submitBtn = form.find('button[type="submit"]');
    submitBtn.find('.btn-text').addClass('hidden');
    submitBtn.find('.btn-loader').removeClass('hidden');
    submitBtn.prop('disabled', true);
    const csrf_token = getCSRFToken();
    const csrf_name  = getCSRFName();
    $.ajax({
      url: '<?= base_url('candidate/Applied/submitJobApplication') ?>',
      method: 'POST',
      data: form.serialize() + '&pid=' + $('#applyButton').data('postid') + '&' + csrf_name + '=' + csrf_token,
      dataType: 'json',
      complete: function () {
        submitBtn.find('.btn-text').removeClass('hidden');
        submitBtn.find('.btn-loader').addClass('hidden');
        submitBtn.prop('disabled', false);
      },
      success: function (response) {
        if (response.csrf_token) updateCSRFToken(response.csrf_token, response.csrf_name);
        if (response.success_msg) {
          closeModal();
          toastr.success(response.success_msg);
          setTimeout(function () {
            const job_slug = make_slug(response.similar_job + '-jobs');
            const query = encodeURIComponent(response.similar_job);
            window.location.href = '<?= base_url("browse-jobs/") ?>' + job_slug + '?key_word=' + query;
          }, 1000);
        } else {
          toastr.error(response.error_msg);
        }
      },
      error: function () { toastr.error('Failed to save profile. Please try again.'); }
    });
  });
});
<?php endif; ?>
</script>

<!-- JSON-LD for SEO -->
<?php
  $first_city = !empty($cities) ? trim($cities[0]) : 'Anywhere';
  $dynamic_description = "Job opening for {$job_title} at {$employee_company_name} in {$first_city}. Requires {$experience_range} years of experience and qualification: {$qualification}. Apply now.";
  $validThrough = date('Y-m-d\TH:i:s', strtotime('+1 year'));
?>
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "JobPosting",
  "title": <?= json_encode($job_title ?? 'Job Opening') ?>,
  "description": <?= json_encode($dynamic_description) ?>,
  "identifier": { "@type": "PropertyValue", "name": <?= json_encode($employee_company_name ?? 'Company') ?>, "value": <?= json_encode($post_id) ?> },
  "datePosted": "<?= date('Y-m-d') ?>",
  "validThrough": <?= json_encode($validThrough) ?>,
  "qualifications": <?= json_encode($qualification ?? 'Any Graduate') ?>,
  "employmentType": <?= json_encode($job_type ?? 'Full-time') ?>,
  "hiringOrganization": { "@type": "Organization", "name": <?= json_encode(($industry_name ?? '') . ' ' . ($employee_company_name ?? '')) ?>, "sameAs": <?= json_encode(SITE_URL) ?> },
  "jobLocation": { "@type": "Place", "address": { "@type": "PostalAddress", "streetAddress": "Sector - 10", "addressLocality": <?= json_encode($first_city) ?>, "addressRegion": "India", "postalCode": "110085", "addressCountry": "IN" } },
  "baseSalary": { "@type": "MonetaryAmount", "currency": "INR", "value": { "@type": "QuantitativeValue", "minValue": <?= json_encode($min_salary ?? 500000) ?>, "maxValue": <?= json_encode($max_salary ?? 4500000) ?>, "unitText": <?= json_encode($salary_type ?? 'YEAR') ?> } }
}
</script>
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "BreadcrumbList",
  "itemListElement": [
    { "@type": "ListItem", "position": 1, "item": { "@id": <?= json_encode(base_url()) ?>, "name": "Home" } },
    { "@type": "ListItem", "position": 2, "item": { "@id": <?= json_encode(base_url('browse-jobs')) ?>, "name": "Browse Jobs" } },
    { "@type": "ListItem", "position": 3, "item": { "@id": <?= json_encode(base_url('browse-jobs/' . make_slug(!empty($industry_name) ? $industry_name : 'industry') . '?industry[]=' . urlencode(!empty($industry_name) ? $industry_name : 'industry'))) ?>, "name": <?= json_encode(!empty($industry_name) ? $industry_name : 'Industry') ?> } },
    { "@type": "ListItem", "position": 4, "item": { "@id": <?= json_encode(base_url('browse-jobs/' . make_slug(!empty($job_type) ? $job_type : 'full-time') . '?job_type[]=' . urlencode(!empty($job_type) ? $job_type : 'full-time'))) ?>, "name": <?= json_encode(!empty($job_type) ? $job_type : 'Job Type') ?> } },
    { "@type": "ListItem", "position": 5, "item": { "@id": <?= json_encode(current_url()) ?>, "name": <?= json_encode(!empty($job_title) ? $job_title : 'Job Detail') ?> } }
  ]
}
</script>
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "WebSite",
  "url": <?= json_encode(SITE_URL) ?>,
  "name": "Talents Jobs - MNC Jobs",
  "potentialAction": {
    "@type": "SearchAction",
    "target": { "@type": "EntryPoint", "urlTemplate": <?= json_encode(base_url('browse-jobs/' . make_slug($job_title) . '?key_word={search_term_string}')) ?> },
    "query-input": "required name=search_term_string"
  }
}
</script>