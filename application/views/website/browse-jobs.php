<script async src="https://www.google.com/adsense/search/ads.js"></script>

<script type="text/javascript">
(function(g,o){
  g[o]=g[o]||function(){
    (g[o]['q']=g[o]['q']||[]).push(arguments)
  },
  g[o]['t']=1*new Date
})(window,'_googCsa');
</script>



<section class="bg-gray-50 pt-24 pb-4">
  <div class="container mx-auto px-4">   
    

    <?= $this->load->view('common/header_ads_tj', '', TRUE) ?>    
  
    <!-- Filters Section - Sticky on desktop -->
    <div class="mb-6 flex flex-wrap gap-2 sm:gap-4 items-center pt-16 sm:sticky sm:top-[64px] sm:z-40 sm:bg-gray-50 sm:pt-4 sm:pb-2">
     <button
		class="sm:hidden fixed bottom-6 right-4 z-50 bg-blue-600 text-white p-3 rounded-full shadow-lg hover:bg-blue-700 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
		onclick="openFilters()"
		aria-label="Open filters">
		<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
			<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
		</svg>
	</button>

      <!-- Desktop Filters -->
      <div class="hidden sm:flex gap-3">

        <!-- Salary Filter -->
        <div class="relative">
          <button class="filter-btn flex items-center bg-white px-4 py-2 rounded-lg border border-gray-300 hover:border-blue-500 shadow-sm">
            <span class="mr-2">Salary (INR)</span>
            <span class="count-badge ml-2 text-blue-600 font-medium hidden"></span>
            <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
          </button>
          <div class="filter-dropdown absolute top-full left-0 mt-2 bg-white p-3 rounded-lg shadow-xl border w-64 hidden z-50"> <!-- FIX: width increased to w-64 -->
            <label class="flex items-center p-2 hover:bg-gray-100 rounded cursor-pointer">
              <input type="checkbox" name="salary[]" value="0-3" class="mr-2 flex-shrink-0">
              <span class="truncate flex-1">₹0L - ₹3L</span>
            </label>
            <label class="flex items-center p-2 hover:bg-gray-100 rounded cursor-pointer">
              <input type="checkbox" name="salary[]" value="3-5" class="mr-2 flex-shrink-0">
              <span class="truncate flex-1">₹3L - ₹5L</span>
            </label>
            <label class="flex items-center p-2 hover:bg-gray-100 rounded cursor-pointer">
              <input type="checkbox" name="salary[]" value="5-7" class="mr-2 flex-shrink-0">
              <span class="truncate flex-1">₹5L - ₹7L</span>
            </label>
            <label class="flex items-center p-2 hover:bg-gray-100 rounded cursor-pointer">
              <input type="checkbox" name="salary[]" value="7-10" class="mr-2 flex-shrink-0">
              <span class="truncate flex-1">₹7L - ₹10L</span>
            </label>
            <label class="flex items-center p-2 hover:bg-gray-100 rounded cursor-pointer">
              <input type="checkbox" name="salary[]" value="10-999" class="mr-2 flex-shrink-0">
              <span class="truncate flex-1">₹10L & above</span>
            </label>
            <div class="mt-3 flex gap-2 border-t pt-3">
              <button class="apply-filter px-3 py-1 bg-blue-500 text-white rounded text-sm w-full">Apply</button>
              <button class="reset-filter px-3 py-1 bg-gray-200 text-gray-700 rounded text-sm w-full">Reset</button>
            </div>
          </div>
        </div>

        <!-- Job Type Filter -->
        <div class="relative">
          <button class="filter-btn flex items-center bg-white px-4 py-2 rounded-lg border border-gray-300 hover:border-blue-500 shadow-sm">
            <span class="mr-2">Job Type</span>
            <span class="count-badge ml-2 text-blue-600 font-medium hidden"></span>
            <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
          </button>
          <div class="filter-dropdown absolute top-full left-0 mt-2 bg-white p-3 rounded-lg shadow-xl border w-64 max-h-64 overflow-y-auto hidden z-50"> <!-- FIX: width increased to w-64 -->
            <div class="sticky top-0 bg-white pb-2">
              <input type="text" placeholder="Search job types..." 
                     class="filter-search w-full px-2 py-1 text-sm border rounded" 
                     data-search-target="job_type">
            </div>
            <div class="scroll-container space-y-1">
              <?php foreach($activeJobTypes as $jobType): ?>
                <label class="flex items-center p-2 hover:bg-gray-100 rounded cursor-pointer whitespace-nowrap"> <!-- FIX: added truncation classes -->
                  <input type="checkbox" 
                         name="job_type[]" 
                         value="<?= htmlspecialchars($jobType['job_type']) ?>" 
                         class="mr-2 flex-shrink-0">
                  <span class="truncate flex-1"><?= ucfirst(str_replace('_', ' ', $jobType['job_type'])) ?></span>
                  <span class="text-xs text-gray-500 ml-2 flex-shrink-0">(<?= $jobType['job_count'] ?>)</span>
                </label>
              <?php endforeach; ?>
            </div>
            <div class="sticky bottom-0 bg-white pt-3 mt-2 border-t">
              <div class="flex gap-2">
                <button class="apply-filter px-3 py-1 bg-blue-500 text-white rounded text-sm w-full">Apply</button>
                <button class="reset-filter px-3 py-1 bg-gray-200 text-gray-700 rounded text-sm w-full">Reset</button>
              </div>
            </div>
          </div>
        </div>

        <!-- Industry Filter -->
        <div class="relative">
          <button class="filter-btn flex items-center bg-white px-4 py-2 rounded-lg border border-gray-300 hover:border-blue-500 shadow-sm">
            <span class="mr-2">Industry</span>
            <span class="count-badge ml-2 text-blue-600 font-medium hidden"></span>
            <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
          </button>
          <div class="filter-dropdown absolute top-full left-0 mt-2 bg-white p-3 rounded-lg shadow-xl border w-64 max-h-64 overflow-y-auto hidden z-50"> <!-- FIX: width increased to w-64 -->
            <div class="sticky top-0 bg-white pb-2">
              <input type="text" placeholder="Search industries..." 
                     class="filter-search w-full px-2 py-1 text-sm border rounded" 
                     data-search-target="industry">
            </div>
            <div class="scroll-container space-y-1">
              <?php foreach($activeIndustries as $industry): ?>
                <label class="flex items-center p-2 hover:bg-gray-100 rounded cursor-pointer whitespace-nowrap"> <!-- FIX: added truncation -->
                  <input type="checkbox" 
                         name="industry[]" 
                         value="<?= $industry['industry_id'] ?>" 
                         class="mr-2 flex-shrink-0">
                  <span class="truncate flex-1"><?= htmlspecialchars($industry['industry_name']) ?></span>
                  <span class="text-xs text-gray-500 ml-2 flex-shrink-0">(<?= $industry['job_count'] ?>)</span>
                </label>
              <?php endforeach; ?>
            </div>
            <div class="sticky bottom-0 bg-white pt-3 mt-2 border-t">
              <div class="flex gap-2">
                <button class="apply-filter px-3 py-1 bg-blue-500 text-white rounded text-sm w-full">Apply</button>
                <button class="reset-filter px-3 py-1 bg-gray-200 text-gray-700 rounded text-sm w-full">Reset</button>
              </div>
            </div>
          </div>
        </div>

        <!-- Education Filter -->
        <div class="relative">
          <button class="filter-btn flex items-center bg-white px-4 py-2 rounded-lg border border-gray-300 hover:border-blue-500 shadow-sm">
            <span class="mr-2">Education</span>
            <span class="count-badge ml-2 text-blue-600 font-medium hidden"></span>
            <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
          </button>
          <div class="filter-dropdown absolute top-full left-0 mt-2 bg-white p-3 rounded-lg shadow-xl border w-64 max-h-64 overflow-y-auto hidden z-50"> <!-- FIX: width increased to w-64 -->
            <div class="sticky top-0 bg-white pb-2">
              <input type="text" placeholder="Search education..." 
                     class="filter-search w-full px-2 py-1 text-sm border rounded" 
                     data-search-target="education">
            </div>
            <div class="scroll-container space-y-1">
              <?php foreach($activeEducations as $edu): ?>
                <label class="flex items-center p-2 hover:bg-gray-100 rounded cursor-pointer whitespace-nowrap"> <!-- FIX: added truncation -->
                  <input type="checkbox" 
                         name="education[]" 
                         value="<?= htmlspecialchars($edu['education']) ?>" 
                         class="mr-2 flex-shrink-0">
                  <span class="truncate flex-1"><?= formatEducation($edu['education']) ?></span>
                  <span class="text-xs text-gray-500 ml-2 flex-shrink-0">(<?= $edu['job_count'] ?>)</span>
                </label>
              <?php endforeach; ?>
            </div>
            <div class="sticky bottom-0 bg-white pt-3 mt-2 border-t">
              <div class="flex gap-2">
                <button class="apply-filter px-3 py-1 bg-blue-500 text-white rounded text-sm w-full">Apply</button>
                <button class="reset-filter px-3 py-1 bg-gray-200 text-gray-700 rounded text-sm w-full">Reset</button>
              </div>
            </div>
          </div>
        </div>

        <!-- Clear All Button -->
        <button onclick="clearAllFilters()" 
                class="flex items-center px-4 py-2 text-red-600 hover:text-red-700 hover:bg-red-50 rounded-lg transition-colors border border-red-100">
          <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
          </svg>
          Clear All
        </button>

      </div>
    </div>
    

    <!-- Main Content -->
    <div class="flex flex-col lg:flex-row gap-6">
      <!-- Job Listings -->
      <main class="flex-1">
  
        <div class="bg-white rounded-xl shadow-xs">
          <!-- Popular Jobs Section -->
          <div class="my-6 md:my-8">
            <h2 class="text-lg md:text-xl font-semibold text-gray-900 mb-3 md:mb-4">Search Popular Jobs</h2>
            <div class="flex flex-wrap gap-2 md:gap-3 justify-center">
              <?php 
              $popular_jobs = [
                "Data Entry", "Remote jobs", "Fresher jobs", "Work from Home", "Internship jobs",
                "Part Time", "Copy Paste", "Data Analyst", "Finance", "Sales jobs",
                "IT jobs", "HR Executive", "Manual Testing", "Banking", "Digital Marketing"
              ];
              foreach ($popular_jobs as $job_title): 
                $slug = make_slug($job_title) . "-jobs";
                $query = http_build_query(['key_word' => $job_title]);
              ?>
              <a href="<?= base_url('browse-jobs/' . $slug . '?' . $query) ?>" class="inline-block">
                <span class="inline-block px-4 py-1.5 md:px-5 md:py-2 text-xs md:text-sm font-medium rounded-full bg-gradient-to-r from-indigo-100 via-purple-100 to-pink-100 text-indigo-800 shadow-sm hover:shadow-md transition duration-200 whitespace-nowrap">
                  <?= htmlspecialchars($job_title) ?>
                </span>
              </a>
              <?php endforeach; ?>
            </div>
          </div>
         
		  
           <!-- Search Jobs Header -->
          <div class="p-4 sm:p-6 border-b border-gray-100">
            <div class="flex flex-wrap items-center justify-between gap-2">
              <h2 class="text-lg sm:text-xl font-bold text-gray-900">Search Jobs</h2>
              <span class="text-xs sm:text-sm text-gray-500 bg-gray-50 px-2 sm:px-3 py-1 rounded-lg total_rows">
                122,840 results</span>
            </div>
          </div>         
         

		  <div id="activeFilters" class="px-4 sm:px-6 py-2 flex flex-wrap gap-2"></div>
		  
          
			<div class="mx-3 sm:mx-4 mb-4 bg-gradient-to-r from-green-50 to-teal-50 rounded-md p-3 flex flex-col sm:flex-row items-center justify-between border border-green-100 shadow-sm">
  
			  <div class="flex items-center gap-2 mb-2 sm:mb-0">
				
				<svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
				  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
					d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"/>
				</svg>

				<div>
				  <h3 class="font-medium text-gray-800 text-sm sm:text-base">
					Real-time job updates
				  </h3>
				  <p class="text-xs sm:text-sm text-gray-600">
					Join our WhatsApp community
				  </p>
				</div>

			  </div>

			  <a href="https://whatsapp.com/channel/0029VbAvEvS3wtb2IU2lYL3H" target="_blank"
				class="inline-flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white px-3 py-1.5 rounded-md text-sm font-medium transition-all shadow-sm whitespace-nowrap">
				
				<!-- WhatsApp Icon -->
				<svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
				  <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.48 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.449l-6.305 1.655zm6.972-3.807c1.589.965 3.404 1.474 5.27 1.474 5.458 0 9.899-4.44 9.899-9.899 0-2.643-1.028-5.126-2.897-6.994-1.867-1.868-4.35-2.897-6.992-2.897-5.459 0-9.899 4.44-9.899 9.899 0 1.757.457 3.479 1.325 5.006l-1.106 4.038 4.1-1.026z"/>
				</svg>
				WhatsApp Community
			  </a>
			</div>
	  
	       <div id="afscontainer1"></div>
            <div id="relatedsearches1"></div>
          
          <!-- Job Cards Container -->
          <div class="divide-y divide-gray-100 filter_data" id="jobCardsContainer"></div>

          <!-- Pagination -->
          <div class="p-4 border-t border-gray-100" id="pagination_link"></div>
        </div>
      </main>     
     
		 <!-- PHP Data Array -->
		<?php
			$baseLogoPath = 'assets/frontend/company/';

			$candidates = [
			  ['name'=>'Aarav Singh','edu'=>'B.Tech','logo'=> $baseLogoPath . 'aarav-singh.png','role'=>'Software Engineer'],
			  ['name'=>'Sneha Patel','edu'=>'B.Des','logo'=> $baseLogoPath . 'sneha-patel.png','role'=>'UI/UX Designer'],
			  ['name'=>'Rahul Sharma','edu'=>'M.Sc','logo'=> $baseLogoPath . 'rahul-sharma.png','role'=>'Data Scientist'],
			  ['name'=>'Priya Reddy','edu'=>'M.Tech','logo'=> $baseLogoPath . 'priya-reddy.jpeg','role'=>'DevOps Engineer'],
			  ['name'=>'Vikram Kumar','edu'=>'MBA','logo'=> $baseLogoPath . 'vikram-kumar.jpeg','role'=>'Product Manager'],
			  ['name'=>'Ananya Gupta','edu'=>'BCA','logo'=> $baseLogoPath . 'ananya-gupta.jpeg','role'=>'Frontend Developer'],
			  ['name'=>'Karan Mehta','edu'=>'B.Tech','logo'=> $baseLogoPath . 'karan-mehta.jpeg','role'=>'Backend Developer'],
			  ['name'=>'Riya Desai','edu'=>'MCA','logo'=> $baseLogoPath . 'riya-desai.jpeg','role'=>'Full Stack Developer'],
			  ['name'=>'Aditya Singh','edu'=>'B.Sc','logo'=> $baseLogoPath . 'aditya-singh.jpeg','role'=>'Data Analyst'],
			  ['name'=>'Neha Sharma','edu'=>'B.Com','logo'=> $baseLogoPath . 'neha-sharma.jpeg','role'=>'Finance Analyst'],
			  ['name'=>'Manish Rao','edu'=>'B.E.','logo'=> $baseLogoPath . 'manish-rao.jpeg','role'=>'Network Engineer'],
			  ['name'=>'Pooja Jain','edu'=>'BBA','logo'=> $baseLogoPath . 'pooja-jain.jpeg','role'=>'HR Specialist'],
			  ['name'=>'Suresh Patel','edu'=>'M.Tech','logo'=> $baseLogoPath . 'suresh-patel.jpeg','role'=>'Cloud Architect'],
			  ['name'=>'Divya Nair','edu'=>'Ph.D','logo'=> $baseLogoPath . 'divya-nair.jpeg','role'=>'Research Scientist'],
			  ['name'=>'Rohit Verma','edu'=>'BCA','logo'=> $baseLogoPath . 'rohit-verma.jpeg','role'=>'QA Engineer'],
			];
		?>

		
		<?php
			$companies = [
				['name' => 'Ola Cabs', 'logo' => 'assets/frontend/company/ola-cabs.png', 'positions' => '25 open positions', 'url' => '#'],
				['name' => 'ZS', 'logo' => 'assets/frontend/company/zs.png', 'positions' => 'Open positions', 'url' => '#'],
				['name' => 'Accenture', 'logo' => 'assets/frontend/company/accenture.png', 'positions' => 'Open positions', 'url' => '#'],
				['name' => 'Electronic Arts', 'logo' => 'assets/frontend/company/electronic-arts.jpeg', 'positions' => 'Open positions', 'url' => '#'],
				['name' => 'IQVIA', 'logo' => 'assets/frontend/company/iqvia.jpeg', 'positions' => 'Open positions', 'url' => '#'],
				['name' => 'GSN Games', 'logo' => 'assets/frontend/company/gsn-games.jpeg', 'positions' => 'Open positions', 'url' => '#'],
				['name' => 'SendinBlue', 'logo' => 'assets/frontend/company/sendinblue.jpeg', 'positions' => 'Open positions', 'url' => '#'],
				['name' => 'Meesho', 'logo' => 'assets/frontend/company/meesho.jpeg', 'positions' => 'Open positions', 'url' => '#'],
				['name' => 'Cognizant Technology Solutions', 'logo' => 'assets/frontend/company/cognizant-technology-solutions.jpeg', 'positions' => 'Open positions', 'url' => '#'],
				['name' => 'TA Digital', 'logo' => 'assets/frontend/company/ta-digital.jpeg', 'positions' => 'Open positions', 'url' => '#'],
				['name' => 'Oceaneering', 'logo' => 'assets/frontend/company/oceaneering.jpeg', 'positions' => 'Open positions', 'url' => '#'],
				['name' => 'Oportun', 'logo' => 'assets/frontend/company/oportun.jpeg', 'positions' => 'Open positions', 'url' => '#'],
				['name' => 'ICE Mortgage Technology', 'logo' => 'assets/frontend/company/ice-mortgage-technology.jpeg', 'positions' => 'Open positions', 'url' => '#'],
				['name' => 'Metasoft Technologies India', 'logo' => 'assets/frontend/company/metasoft-technologies-india.jpeg', 'positions' => 'Open positions', 'url' => '#'],
				['name' => 'Shell', 'logo' => 'assets/frontend/company/shell.jpeg', 'positions' => 'Open positions', 'url' => '#'],
				['name' => 'Hexaware Technologies', 'logo' => 'assets/frontend/company/hexaware-technologies.jpeg', 'positions' => 'Open positions', 'url' => '#'],
				['name' => 'CDK Global', 'logo' => 'assets/frontend/company/cdk-global.jpeg', 'positions' => 'Open positions', 'url' => '#'],
				['name' => 'Atos', 'logo' => 'assets/frontend/company/atos.jpeg', 'positions' => 'Open positions', 'url' => '#'],
				['name' => 'Virtusa', 'logo' => 'assets/frontend/company/virtusa.jpeg', 'positions' => 'Open positions', 'url' => '#'],
				['name' => 'Rain Carbon Inc.', 'logo' => 'assets/frontend/company/rain-carbon-inc.jpeg', 'positions' => 'Open positions', 'url' => '#'],
				['name' => 'WISSEN', 'logo' => 'assets/frontend/company/wissen.jpeg', 'positions' => 'Open positions', 'url' => '#'],
				['name' => 'BuildingBlocks', 'logo' => 'assets/frontend/company/buildingblocks.jpeg', 'positions' => 'Open positions', 'url' => '#'],
				['name' => 'Accolite Digital', 'logo' => 'assets/frontend/company/accolite-digital.jpeg', 'positions' => 'Open positions', 'url' => '#'],
				['name' => 'TVS', 'logo' => 'assets/frontend/company/tvs.jpeg', 'positions' => 'Open positions', 'url' => '#'],
				['name' => 'NetCracker', 'logo' => 'assets/frontend/company/netcracker.jpeg', 'positions' => 'Open positions', 'url' => '#'],
				['name' => 'Infoedge', 'logo' => 'assets/frontend/company/infoedge.jpeg', 'positions' => 'Open positions', 'url' => '#'],
				['name' => 'Focaloid Technologies', 'logo' => 'assets/frontend/company/focaloid-technologies.jpeg', 'positions' => 'Open positions', 'url' => '#'],
				['name' => 'INTERFACE.ai', 'logo' => 'assets/frontend/company/interface-ai.jpeg', 'positions' => 'Open positions', 'url' => '#'],
				['name' => 'Sify', 'logo' => 'assets/frontend/company/sify.jpeg', 'positions' => 'Open positions', 'url' => '#'],
				['name' => 'VDart', 'logo' => 'assets/frontend/company/vdart.jpeg', 'positions' => 'Open positions', 'url' => '#'],
				['name' => 'Zycus', 'logo' => 'assets/frontend/company/zycus.jpeg', 'positions' => 'Open positions', 'url' => '#']
			];
			?>
		
		<!-- 2) Aside container with Swiper slider -->
		<aside class="w-full lg:w-[480px] xl:w-[520px]">
			<div class="bg-white p-6 rounded-xl shadow-xs border border-gray-100">
			<!-- Heading -->
			<h2 class="text-xl font-semibold mb-4 text-gray-900">Candidates Hired</h2>

			<div class="relative">
			  <!-- Swiper wrapper -->
			  <div class="swiper candidates-slider">
				<div class="swiper-wrapper">
				  <?php foreach($candidates as $c): ?>
				<div class="swiper-slide">
					  <div class="flex flex-col items-center bg-white rounded-xl p-4 border border-gray-200 shadow-sm">
						<!-- Education Badge -->
						<span class="bg-indigo-100 text-indigo-800 text-xs font-medium px-2 py-1 rounded-full mb-3">
						  <?= htmlspecialchars($c['edu']) ?>
						</span>
						
						<!-- Name -->
						<h3 class="text-base font-semibold text-gray-900 mb-3 text-center">
						  <?= htmlspecialchars($c['name']) ?>
						</h3>
						
						<!-- Company Logo -->
						<div class="w-16 h-16 rounded-full overflow-hidden border-2 border-gray-200 mb-3">
						  <img src="<?=base_url(htmlspecialchars($c['logo']))?>"
							   alt="Logo for <?= htmlspecialchars($c['name']) ?>"
							   class="object-contain w-full h-full">
						</div>
						
						<!-- Updated Role Badge -->
						<span class="bg-green-100 text-green-800 text-xs font-medium px-3 py-1 rounded-full max-w-[160px] sm:max-w-[200px] truncate">
						  <?= htmlspecialchars($c['role']) ?>
						</span>
					  </div>
					</div>
				  <?php endforeach; ?>
				</div>
			  </div>

			  <!-- Navigation Arrows -->
			  <button
				class="absolute left-0 top-1/2 transform -translate-y-1/2 z-10 p-1 bg-white rounded-full shadow hover:bg-gray-100"
				id="cand-prev"
				aria-label="Previous"
			  >
				<svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
				  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
				</svg>
			  </button>
			  <button
				class="absolute right-0 top-1/2 transform -translate-y-1/2 z-10 p-1 bg-white rounded-full shadow hover:bg-gray-100"
				id="cand-next"
				aria-label="Next"
			  >
				<svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
				  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
				</svg>
			  </button>
			</div>
		  </div>
		
		<div class="mt-6 bg-white p-4 sm:p-6 rounded-xl shadow-xs border border-gray-100">
            <h2 class="text-xl font-semibold mb-4 text-gray-900">Featured Companies</h2>
            <div class="relative">
                <div class="swiper featured-companies-box">
                    <div class="swiper-wrapper">
                        <?php foreach ($companies as $company): ?>
                            <div class="swiper-slide flex items-center justify-center p-1">   <!-- reduced from p-2 -->
                                <a href="javascript:void(0)"
                                   class="group block w-full h-full p-2 rounded-xl border border-gray-200 hover:border-blue-400 transition-colors flex items-center justify-center">  <!-- reduced from p-4 -->
                                    <img src="<?=base_url($company['logo'])?>"
                                         alt="<?= htmlspecialchars($company['name']) ?>"
                                         class="w-12 h-12 rounded-lg object-contain" />  <!-- image size unchanged -->
                                </a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
        
                <!-- Navigation (unchanged) -->
                <button class="absolute left-0 top-1/2 transform -translate-y-1/2 z-10 p-1 bg-white rounded-full shadow hover:bg-gray-100" id="feat-prev" aria-label="Previous">
                    <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </button>
                <button class="absolute right-0 top-1/2 transform -translate-y-1/2 z-10 p-1 bg-white rounded-full shadow hover:bg-gray-100" id="feat-next" aria-label="Next">
                    <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </button>
            </div>
        </div>
		
		<!-- ADS LABEL -->
		<div class="flex items-center justify-center gap-2 mb-2">
			<span class="h-px flex-1 bg-gray-200"></span>

			<span class="text-[10px] uppercase tracking-wider text-gray-400 font-medium">
				Sponsored
			</span>

			<span class="h-px flex-1 bg-gray-200"></span>
		</div>

		<!-- COMPACT PROMO CARD -->
		<div class="bg-white p-2.5 rounded-xl border border-gray-100 overflow-hidden">

			<a href="<?= base_url('career-services') ?>" 
			   class="group block rounded-xl overflow-hidden border border-blue-100 hover:border-blue-300 transition-all duration-300">

				<!-- TOP BAR -->
				<div class="bg-gradient-to-r from-green-50 via-emerald-50 to-teal-50 px-3 py-2 border-b border-green-100">

					<div class="flex items-center justify-between gap-2">

						<div class="flex items-center gap-2 min-w-0">

							<div class="w-8 h-8 rounded-full bg-green-100 flex items-center justify-center text-sm flex-shrink-0">
								🚀
							</div>

							<div class="min-w-0">
								<h3 class="text-xs sm:text-sm font-semibold text-gray-900 truncate">
									Get 5x More Visibility
								</h3>

								<p class="text-[11px] text-gray-600 truncate">
									Resume Builder + Profile Boost
								</p>
							</div>

						</div>

						<span class="hidden md:flex items-center px-3 py-1.5 rounded-lg bg-green-600 text-white text-xs font-medium whitespace-nowrap">
							Upgrade →
						</span>

					</div>

				</div>

				<!-- CONTENT -->
				<div class="p-3 bg-gradient-to-br from-blue-50 via-white to-indigo-50">

					<!-- TITLE -->
					<div class="mb-3">

						<span class="inline-flex items-center px-2 py-1 rounded-full bg-blue-100 text-blue-700 text-[10px] font-semibold mb-2">
							MOST POPULAR
						</span>

						<h2 class="text-lg sm:text-xl font-bold text-gray-900 leading-tight">
							Resume Builder +
							<span class="text-blue-600">Profile Boost</span>
						</h2>

						<p class="mt-1 text-xs sm:text-sm text-gray-600 leading-relaxed">
							ATS resumes + recruiter visibility boost.
						</p>

					</div>

					<!-- FEATURES -->
					<div class="grid grid-cols-2 gap-2 mb-3">

						<div class="bg-white rounded-lg p-2 border border-gray-100">
							<div class="flex items-center gap-2">
								<span class="text-sm">📄</span>

								<div class="min-w-0">
									<p class="text-[11px] sm:text-xs font-semibold text-gray-900 truncate">
										20+ Templates
									</p>

									<p class="text-[10px] text-gray-500 truncate">
										ATS Friendly
									</p>
								</div>
							</div>
						</div>

						<div class="bg-white rounded-lg p-2 border border-gray-100">
							<div class="flex items-center gap-2">
								<span class="text-sm">📈</span>

								<div class="min-w-0">
									<p class="text-[11px] sm:text-xs font-semibold text-gray-900 truncate">
										5x Views
									</p>

									<p class="text-[10px] text-gray-500 truncate">
										Higher Reach
									</p>
								</div>
							</div>
						</div>

						<div class="bg-white rounded-lg p-2 border border-gray-100">
							<div class="flex items-center gap-2">
								<span class="text-sm">📊</span>

								<div class="min-w-0">
									<p class="text-[11px] sm:text-xs font-semibold text-gray-900 truncate">
										Analytics
									</p>

									<p class="text-[10px] text-gray-500 truncate">
										Track Insights
									</p>
								</div>
							</div>
						</div>

						<div class="bg-white rounded-lg p-2 border border-gray-100">
							<div class="flex items-center gap-2">
								<span class="text-sm">✅</span>

								<div class="min-w-0">
									<p class="text-[11px] sm:text-xs font-semibold text-gray-900 truncate">
										Verified HR
									</p>

									<p class="text-[10px] text-gray-500 truncate">
										Unlimited Access
									</p>
								</div>
							</div>
						</div>

					</div>

					<!-- CTA -->
					<div class="flex gap-2">

						<button class="flex-1 bg-blue-600 hover:bg-blue-700 text-white py-2 rounded-lg text-sm font-semibold transition">
							Get Started
						</button>

						<button class="px-4 py-2 rounded-lg border border-blue-200 text-blue-600 hover:bg-blue-50 text-sm font-medium transition">
							Demo
						</button>

					</div>

				</div>

			</a>

		</div>
		
		</aside>
   
    </div>
  </div>
</section>

<!-- Mobile Filters Modal (Slide Drawer) -->
<div id="mobileFiltersModal" class="fixed inset-0 z-50 hidden">
  <div class="absolute inset-0 bg-black bg-opacity-50" onclick="closeFilters()"></div>
  <div class="absolute right-0 top-0 h-full w-full max-w-md bg-white shadow-xl transform transition-transform duration-300 translate-x-full" id="mobileDrawer">
    <!-- Modal Header -->
    <div class="sticky top-0 bg-white p-4 border-b flex justify-between items-center">
      <h3 class="text-lg font-bold">Filters</h3>
      <button onclick="closeFilters()" class="text-gray-500 hover:text-gray-700">✕</button>
    </div>

    <!-- Modal Content -->
    <div class="p-4 space-y-6 overflow-y-auto max-h-[calc(100vh-8rem)]">
      <!-- Salary -->
      <div>
        <h4 class="font-medium mb-3">Salary Range (₹ Lakh)</h4>
        <div class="space-y-2">
          <label class="flex items-center space-x-3">
            <input type="checkbox" name="salary[]" value="0-3" class="form-checkbox h-5 w-5 text-blue-600">
            <span class="text-gray-700">₹0L - ₹3L</span>
          </label>
          <label class="flex items-center space-x-3">
            <input type="checkbox" name="salary[]" value="3-5" class="form-checkbox h-5 w-5 text-blue-600">
            <span class="text-gray-700">₹3L - ₹5L</span>
          </label>
          <label class="flex items-center space-x-3">
            <input type="checkbox" name="salary[]" value="5-7" class="form-checkbox h-5 w-5 text-blue-600">
            <span class="text-gray-700">₹5L - ₹7L</span>
          </label>
          <label class="flex items-center space-x-3">
            <input type="checkbox" name="salary[]" value="7-10" class="form-checkbox h-5 w-5 text-blue-600">
            <span class="text-gray-700">₹7L - ₹10L</span>
          </label>
          <label class="flex items-center space-x-3">
            <input type="checkbox" name="salary[]" value="10-999" class="form-checkbox h-5 w-5 text-blue-600">
            <span class="text-gray-700">₹10L+</span>
          </label>
        </div>
      </div>

      <!-- Job Type -->
      <?php if(!empty($activeJobTypes)): ?>
      <div>
        <h4 class="font-medium mb-3">Job Type</h4>
        <input type="text" class="filter-search w-full px-2 py-1 mb-3 text-sm border rounded" data-search-target="job_type" placeholder="Search job types…" />
        <div class="space-y-2">
          <?php foreach($activeJobTypes as $jobType): ?>
          <label class="flex items-center space-x-3">
            <input type="checkbox" name="job_type[]" value="<?= htmlspecialchars($jobType['job_type']) ?>" class="form-checkbox h-5 w-5 text-blue-600">
            <span class="text-gray-700">
              <?= ucfirst(str_replace('_', ' ', $jobType['job_type'])) ?>
              <span class="text-xs text-gray-500 ml-2">(<?= $jobType['job_count'] ?>)</span>
            </span>
          </label>
          <?php endforeach; ?>
        </div>
      </div>
      <?php endif; ?>

      <!-- Industry -->
      <?php if(!empty($activeIndustries)): ?>
      <div>
        <h4 class="font-medium mb-3">Industry</h4>
        <input type="text" class="filter-search w-full px-2 py-1 mb-3 text-sm border rounded" data-search-target="industry" placeholder="Search industries…" />
        <div class="space-y-2">
          <?php foreach($activeIndustries as $industry): ?>
          <label class="flex items-center space-x-3">
            <input type="checkbox" name="industry[]" value="<?= $industry['industry_id'] ?>" class="form-checkbox h-5 w-5 text-blue-600">
            <span class="text-gray-700">
              <?= htmlspecialchars($industry['industry_name']) ?>
              <span class="text-xs text-gray-500 ml-2">(<?= $industry['job_count'] ?>)</span>
            </span>
          </label>
          <?php endforeach; ?>
        </div>
      </div>
      <?php endif; ?>

      <!-- Education -->
      <?php if(!empty($activeEducations)): ?>
      <div>
        <h4 class="font-medium mb-3">Education</h4>
        <input type="text" class="filter-search w-full px-2 py-1 mb-3 text-sm border rounded" data-search-target="education" placeholder="Search education…" />
        <div class="space-y-2">
          <?php foreach($activeEducations as $edu): ?>
          <label class="flex items-center space-x-3">
            <input type="checkbox" name="education[]" value="<?= htmlspecialchars($edu['education']) ?>" class="form-checkbox h-5 w-5 text-blue-600">
            <span class="text-gray-700">
              <?= formatEducation($edu['education']) ?>
              <span class="text-xs text-gray-500 ml-2">(<?= $edu['job_count'] ?>)</span>
            </span>
          </label>
          <?php endforeach; ?>
        </div>
      </div>
      <?php endif; ?>
    </div>

    <!-- Modal Footer -->
    <div class="sticky bottom-0 bg-white p-4 border-t flex justify-end gap-3">
      <button onclick="clearAllFilters()" class="px-4 py-2 text-gray-700 hover:bg-gray-50 rounded-lg">Reset</button>
      <button onclick="closeFilters(); applyMobileFilters()" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Apply</button>
    </div>
  </div>
</div>


<script>

    function getParam(name) {
      const params = new URLSearchParams(window.location.search);
      return params.get(name);
    }
    
    // ✅ GET & CLEAN KEYWORD
    let keyword = getParam("key_word");
    
    if (keyword) {
      keyword = decodeURIComponent(keyword)
                  .replace(/,/g, ' ')     // comma हटाओ
                  .replace(/\+/g, ' ')    // + हटाओ
                  .replace(/\s+/g, ' ')   // extra space हटाओ
                  .trim();
    } else {
      keyword = "Jobs";
    }
    
    // ✅ OPTIONAL: LIMIT TO 1–2 MAIN TERMS (Better Ad Matching)
    let keywordsArray = keyword.split(" ");
    keyword = keywordsArray.slice(0, 4).join(" ");
    
    console.log("Final Ads Query:", keyword);
    
    var pageOptions = {
      "pubId": "partner-pub-9268075008862469",
      "query": keyword, // 🔥 FINAL FIX
      "styleId": "8476968910",
      "adsafe": "high",
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
    
<script id="a1k9d2">
const industryMap = {
  <?php foreach($activeIndustries as $industry): ?>
    "<?= $industry['industry_id'] ?>": "<?= htmlspecialchars($industry['industry_name']) ?>",
  <?php endforeach; ?>
};
</script>

<script>
(function($){
  let isCategoriesModalOpen = false;

  // Mobile drawer controls
  window.openFilters = function() {
    syncMobileFilters(); // sync checked state from desktop before opening
    $('#mobileFiltersModal').removeClass('hidden');
    setTimeout(() => $('#mobileDrawer').removeClass('translate-x-full'), 10);
  };
  window.closeFilters = function() {
    if (!isCategoriesModalOpen) {
      $('#mobileDrawer').addClass('translate-x-full');
      setTimeout(() => {
        $('#mobileFiltersModal').addClass('hidden');
        // Reset search inputs and show all options
        $('#mobileFiltersModal .filter-search').val('');
        $('#mobileFiltersModal .space-y-2 label').show();
      }, 300);
    }
  };

  // Helper
  function isDesktopScreen() {
    return window.innerWidth >= 768;
  }

  function generateSkeletonLoaders() {
        let html = '';
        for (let i = 0; i < 10; i++) {
            html += `
            <div class="bg-white rounded-lg border border-gray-100 p-3 animate-pulse">
                <div class="flex flex-row items-start gap-3">
                    <!-- Logo placeholder -->
                    <div class="flex-shrink-0 w-10 h-10 bg-gray-200 rounded-md"></div>
    
                    <!-- Main info -->
                    <div class="flex-1 min-w-0">
                        <div class="flex flex-wrap items-baseline gap-1 mb-1">
                            <div class="h-4 bg-gray-200 rounded w-32"></div>
                            <div class="w-6 h-3 bg-gray-200 rounded"></div>
                            <div class="h-3 bg-gray-200 rounded w-20"></div>
                        </div>
                        <div class="flex flex-wrap items-center gap-x-2 gap-y-1">
                            <div class="h-4 bg-gray-200 rounded w-16"></div>
                            <div class="h-4 bg-gray-200 rounded w-24"></div>
                            <div class="h-4 bg-gray-200 rounded w-20"></div>
                            <div class="h-4 bg-gray-200 rounded w-16"></div>
                            <div class="h-4 bg-gray-200 rounded w-12"></div>
                        </div>
                    </div>
    
                    <!-- Actions placeholder -->
                    <div class="flex items-center gap-2 flex-shrink-0">
                        <div class="h-8 w-20 bg-gray-200 rounded-md"></div>
                        <div class="h-8 w-8 bg-gray-200 rounded-full"></div>
                    </div>
                </div>
            </div>`;
        }
        $('#jobCardsContainer').html(html);
  }

  
  // AJAX filtering
  function filterData() {
    const params = getParamsFromSlug();
    $.ajax({
      url: "<?= base_url('fetch_data') ?>?" + params.toString(),
      method: "GET",
      dataType: "JSON",
      beforeSend() {
        $('.filter_data').empty();
        generateSkeletonLoaders();
      },
      success(data) {
		  $('.skeleton-loader').hide();
		  $('.filter_data').html(data.result);
		  $('#pagination_link').html(data.pagination_link);
		  $('.total_rows').text(data.total_rows + ' results');
          
		  renderActiveFilters(); // ✅ ADD THIS LINE

		  if (data.meta) {
			document.title = data.meta.title;
			$('meta[name=description]').attr('content', data.meta.description);
		  }
		},
      error(xhr, status) {
        $('.filter_data').empty();
        $('.skeleton-loader').hide();
      }
    });
  }

  function getParamsFromSlug() {
    const params = new URLSearchParams(window.location.search);

		// ✅ agar already query hai to use karo
		if ([...params.keys()].length > 0) return params;

		const path = window.location.pathname;
		const slug = path.split('/').pop();

		// ❌ FIX: browse-jobs ko keyword mat banao
		if (slug === 'browse-jobs') {
			return params; // empty return
		}

		let keyword = slug.replace(/-/g, ' ');
		let location = '';

		const inIndex = keyword.toLowerCase().lastIndexOf(" in ");
		if (inIndex !== -1) {
			location = keyword.slice(inIndex + 4).trim();
			keyword  = keyword.slice(0, inIndex).trim();
		}

		keyword = keyword.replace(/\b(jobs?|vacanc(y|ies)|hiring)\b/gi, '').trim();
		keyword = keyword.split(" ").map(w => w.charAt(0).toUpperCase() + w.slice(1)).join(" ");

		if (keyword) params.set("key_word", keyword);
		if (location) params.set("locations", location);

		return params;
	}

  // ==================================================
    // Favorite / Unfavorite Handler (FIXED)
    // ==================================================
    function handleFavorite(pid, $btn) {
        const csrfName = getCSRFName();
        const csrfHash = getCSRFToken();
    
        // Optional: disable button to prevent double-click
        $btn.prop('disabled', true).css('opacity', '0.7');
    
        $.ajax({
            url: "<?= base_url('candidate/Applied/toggleFavoriteStatus') ?>",
            type: "POST",
            data: { pid: pid, [csrfName]: csrfHash },
            dataType: "json",
            success: function (resp) {
                if (resp.csrf_token) updateCSRFToken(resp.csrf_token, csrfName);
                if (resp.status === 'success') {
                    const isFavorited = resp.action === 'favorited';
                    // Update button data attribute
                    $btn.data('favorite', isFavorited ? '1' : '0');
                    // Update button class
                    if (isFavorited) {
                        $btn.addClass('favorited');
                    } else {
                        $btn.removeClass('favorited');
                    }
                    // Update inner <i> icon classes
                    const $icon = $btn.find('i');
                    if (isFavorited) {
                        $icon.removeClass('text-gray-500 hover:text-blue-500').addClass('text-blue-600');
                    } else {
                        $icon.removeClass('text-blue-600').addClass('text-gray-500 hover:text-blue-500');
                    }
                } else {
                    console.error('Favorite error:', resp.message);
                    alert(resp.message || 'Could not update favorite.');
                }
            },
            error: function (xhr) {
                console.error('AJAX error:', xhr);
                if (xhr.status === 403) {
                    alert('Session expired. Please refresh and try again.');
                } else {
                    alert('Network error. Please try again.');
                }
            },
            complete: function () {
                // Re-enable button
                $btn.prop('disabled', false).css('opacity', '');
            }
        });
    }
    
    // ==================================================
    // Favorite Click Handler (FIXED)
    // ==================================================
    $(document).on('click', '.favorite-icon', function(e) {
        e.preventDefault();
        const $btn = $(this);
        const pid = $btn.data('pid');
        const userRole = '<?= $this->session->userdata('role') ?>';
    
        if (userRole !== 'candidate') {
            alert('Please log in as a candidate to save jobs.');
            window.location.href = '<?= base_url('auth/login') ?>';
            return;
        }
        handleFavorite(pid, $btn);
    });

  // Clear all filters
  window.clearAllFilters = function() {
	  const params = new URLSearchParams(window.location.search);
	  ['salary[]', 'job_type[]', 'industry[]', 'education[]', 'page'].forEach(p => params.delete(p));

	  window.history.pushState(null, '', window.location.pathname);

	  $('.filter-dropdown input[type="checkbox"]').prop('checked', false);
	  $('.count-badge').addClass('hidden').text('');
	  $('.filter-dropdown').hide();

	  filterData();
	  renderActiveFilters(); // ✅ ADD THIS

	  if (window.innerWidth < 640) closeFilters();
	};

  // Sync mobile filters with desktop checked state
  function syncMobileFilters() {
    $('#mobileFiltersModal input[type="checkbox"]').each(function() {
      var name = $(this).attr('name');
      var value = $(this).val();
      $(this).prop('checked', $(`.filter-dropdown input[name="${name}"][value="${value}"]`).prop('checked'));
    });
  }

  // Apply mobile filters
  window.applyMobileFilters = function() {
    const checked = $('#mobileFiltersModal input[type="checkbox"]:checked');
    const params = new URLSearchParams(window.location.search);
    ['salary[]','job_type[]','industry[]','education[]','page'].forEach(p => params.delete(p));
    checked.each(function(){ params.append(this.name, this.value); });
    window.history.pushState(null, '', window.location.pathname + '?' + params.toString());
    // Also update desktop checkboxes
    checked.each(function(){
      $(`.filter-dropdown input[name="${this.name}"][value="${this.value}"]`).prop('checked', true);
    });
    // Update count badges after syncing
    $('.filter-dropdown').each(function() {
      var $dropdown = $(this);
      var count = $dropdown.find('input:checked').length;
      var $btn = $dropdown.siblings('.filter-btn');
      var $badge = $btn.find('.count-badge');
      if (count) {
        $badge.text(count).removeClass('hidden');
      } else {
        $badge.addClass('hidden').text('');
      }
    });
    filterData();
    closeFilters();
  };

  // Filter search (fixed for both desktop dropdown and mobile drawer)
  function initializeFilterSearch() {
    $('.filter-search').on('input', function() {
      const searchTerm = $(this).val().toLowerCase();
      let $container;

      // Try to find the parent dropdown (desktop case)
      $container = $(this).closest('.filter-dropdown').find('.scroll-container, .space-y-2');
      if (!$container.length) {
        // Not in dropdown → mobile drawer: find the next .space-y-2 container
        $container = $(this).nextAll('.space-y-2').first();
      }

      $container.find('label').each(function() {
        const text = $(this).text().toLowerCase();
        $(this).toggle(text.includes(searchTerm));
      });
    });
  }

  $(function(){
    generateSkeletonLoaders();
    filterData();
	 renderActiveFilters(); // ✅ ADD THIS
    initializeFilterSearch();

    // Desktop filter button click: hide others and toggle current
    $('.filter-btn').on('click', function(e) {
      e.stopPropagation();
      // Hide all other dropdowns
      $('.filter-dropdown').not($(this).siblings('.filter-dropdown')).hide();
      // Toggle current dropdown
      $(this).siblings('.filter-dropdown').toggle();
    });

    // Stop propagation inside dropdown so it doesn't close when clicking on checkboxes/search
    $('.filter-dropdown').on('click', function(e) {
      e.stopPropagation();
    });

    // Close dropdowns when clicking outside
    $(document).on('click', function(e) {
      if (!$(e.target).closest('.filter-dropdown, .filter-btn').length) {
        $('.filter-dropdown').hide();
      }
    });

    // Apply & reset filter buttons
    $('.apply-filter').on('click', function() {
      const dropdown = $(this).closest('.filter-dropdown');
      const checkboxes = dropdown.find('input[type="checkbox"]:checked');
      const params = new URLSearchParams(window.location.search);
      const name = checkboxes.first().attr('name');
      if (checkboxes.length > 0) {
        params.delete(name);
        checkboxes.each((_, cb) => params.append(name, cb.value));
        window.history.pushState(null, '', window.location.pathname + '?' + params);
      }
      dropdown.hide();
      filterData();
    });

    $('.reset-filter').on('click', function() {
      const dropdown = $(this).closest('.filter-dropdown');
      const name = dropdown.find('input[type="checkbox"]').first().attr('name');
      const params = new URLSearchParams(window.location.search);
      params.delete(name);
      window.history.pushState(null, '', window.location.pathname + '?' + params);
      dropdown.find('input[type="checkbox"]').prop('checked', false);
      // Update count badge for this dropdown
      var $btn = dropdown.siblings('.filter-btn');
      var $badge = $btn.find('.count-badge');
      $badge.addClass('hidden').text('');
      filterData();
    });

    // Update count badges when checkboxes change
    $(document).on('change', '.filter-dropdown input[type="checkbox"]', function() {
      var $dropdown = $(this).closest('.filter-dropdown');
      var $btn = $dropdown.siblings('.filter-btn');
      var count = $dropdown.find('input:checked').length;
      var $badge = $btn.find('.count-badge');
      if (count) {
        $badge.text(count).removeClass('hidden');
      } else {
        $badge.addClass('hidden').text('');
      }
    });

    // Pre-check based on URL
    const params = new URLSearchParams(window.location.search);
    ['salary[]','job_type[]','industry[]','education[]'].forEach(param => {
      params.getAll(param).forEach(val => {
        const $cb = $(`input[name="${param}"][value="${val}"]`);
        if ($cb.length) $cb.prop('checked', true).trigger('change');
      });
    });

    // Pagination
    $(document).on('click', '#pagination_link button', function(e){
      e.preventDefault();
      const page = $(this).data('ci-pagination-page') || $(this).text();
      const sp = new URLSearchParams(window.location.search);
      sp.set('page', page);
      window.history.pushState(null, '', '?' + sp.toString());
      $('.skeleton-loader').show();
      filterData();
      $('html, body').animate({ scrollTop: $('#jobCardsContainer').offset().top - 50 }, 400);
    });
    window.addEventListener('popstate', filterData);

      // Mobile filter sync before opening
    $('#openFiltersBtn').on('click', function() {
      syncMobileFilters();
      openFilters();
    });
  });
	
// ================= ACTIVE FILTER TAGS =================

function renderActiveFilters() {
  const params = new URLSearchParams(window.location.search);
  const container = $('#activeFilters');
  container.empty();

  // ✅ Allowed params only
  const allowedKeys = [
    'key_word',
    'locations',
    'experience',
    'salary[]',
    'job_type[]',
    'industry[]',
    'education[]'
  ];

  params.forEach((value, key) => {

    // ❌ Skip Google tracking params
    if (!allowedKeys.includes(key)) return;

    // ❌ Skip empty
    if (!value || value.trim() === '') return;

    let label = value;

    if (key === 'industry[]') {
      label = industryMap[value] || value;
    } 
    else if (key === 'key_word') {
      // decode + clean
      let keywords = decodeURIComponent(value)
        .split(',')
        .map(k => k.trim())
        .filter(k => k.length > 0);
    
      keywords.forEach(k => {
    
        const tag = $(`
          <span class="flex items-center gap-1 bg-blue-100 text-blue-800 text-xs px-3 py-1 rounded-full">
            ${k}
            <button class="ml-1 text-blue-600 hover:text-red-500 font-bold remove-tag">✕</button>
          </span>
        `);
    
        tag.find('.remove-tag')
           .data('key', 'key_word')
           .data('value', k);
    
        container.append(tag);
      });
    
      return; // 🚨 IMPORTANT (duplicate avoid)
    } 
    else {
      label = value.replace(/_/g, ' ');
    }

    const tag = $(`
      <span class="flex items-center gap-1 bg-blue-100 text-blue-800 text-xs px-3 py-1 rounded-full">
        ${label}
        <button class="ml-1 text-blue-600 hover:text-red-500 font-bold remove-tag">✕</button>
      </span>
    `);

    tag.find('.remove-tag')
       .data('key', key)
       .data('value', value);

    container.append(tag);
  });

  container.toggle(container.children().length > 0);
}

$(document).on('click', '.remove-tag', function() {

  const key = $(this).data('key');
  let value = $(this).data('value').trim();

  const params = new URLSearchParams(window.location.search);

  if (key === 'key_word') {

    let current = params.get('key_word') || '';

    let keywords = decodeURIComponent(current)
      .split(',')
      .map(k => k.trim())          // ✅ trim जरूरी
      .filter(k => k.length > 0);  // empty हटाओ

    // ✅ remove clicked keyword
    keywords = keywords.filter(k => k.toLowerCase() !== value.toLowerCase());

    params.delete('key_word');

    if (keywords.length > 0) {
      params.set('key_word', keywords.join(', ')); // clean rebuild
    }

  } else {
    let values = params.getAll(key).filter(v => v !== value);
    params.delete(key);
    values.forEach(v => params.append(key, v));
  }

  window.history.pushState(null, '', '?' + params.toString());

  filterData();
  renderActiveFilters();
});

  // Swiper initializations (ensure they run after DOM ready)
     new Swiper(".featured-companies-box", {
        slidesPerView: 2,
        spaceBetween: 8,
        loop: true,
        autoplay: { delay: 2500, disableOnInteraction: false },
        breakpoints: { 640: { slidesPerView: 4 }, 1024: { slidesPerView: 5 } },
        navigation: { nextEl: "#feat-next", prevEl: "#feat-prev" }
    });

  new Swiper(".candidates-slider", {
    slidesPerView: 1,
    spaceBetween: 12,
    loop: true,
    autoplay: { delay: 3000, disableOnInteraction: false },
    navigation: { nextEl: "#cand-next", prevEl: "#cand-prev" },
    breakpoints: { 640: { slidesPerView: 2 }, 1024: { slidesPerView: 3 } }
  });
})(jQuery);
</script>
