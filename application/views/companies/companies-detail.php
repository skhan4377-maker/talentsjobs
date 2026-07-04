<section class="max-w-7xl mx-auto px-4 sm:px-5 lg:px-6 py-8 sm:py-12 bg-gradient-to-b from-white via-gray-50 to-white">

 <!-- Company Header -->
<!-- Company Header -->
<div class="bg-gradient-to-r from-indigo-50 via-blue-50 to-purple-50 rounded-2xl shadow-xl overflow-hidden relative mt-6 sm:mt-8 border border-gray-200">
  <div class="absolute inset-0 bg-[url('/assets/noise.png')] opacity-5"></div>
  
  <div class="p-6 sm:p-8 flex flex-col md:flex-row items-center gap-6 sm:gap-10 relative">
    
    <!-- ✅ Logo / Initials -->
    <div class="relative mt-4 sm:mt-6">
      <?php if (!empty($company_data['logo'])): ?>
        <img src="<?= base_url($company_data['logo']) ?>"
             class="w-24 h-24 sm:w-28 sm:h-28 rounded-2xl border-4 border-white shadow-md object-cover bg-white"
             alt="<?= htmlspecialchars($company_data['company_name']) ?>">
      <?php else: ?>
        <?php 
          $initials = strtoupper(substr(preg_replace("/[^A-Za-z]/", "", $company_data['company_name']), 0, 2)) ?: "CO"; 
        ?>
        <div class="w-24 h-24 sm:w-28 sm:h-28 rounded-2xl border-4 border-white shadow-md bg-gradient-to-br from-indigo-200 to-purple-300 flex items-center justify-center text-indigo-900 font-bold text-2xl">
          <?= $initials ?>
        </div>
      <?php endif; ?>

      <!-- ⭐ Rating -->
      <div class="absolute -bottom-3 -right-3 bg-indigo-600 text-white px-3 py-1.5 rounded-full shadow-md text-sm font-bold">
        ⭐ <?= isset($company_data['rating']) ? $company_data['rating'] : '4.8' ?>
      </div>
    </div>

    <!-- ✅ Company Info -->
    <div class="text-gray-800 flex-1">
      <h1 class="text-2xl sm:text-3xl font-extrabold mb-3 text-center md:text-left tracking-tight">
        <?= htmlspecialchars($company_data['company_name']) ?>
      </h1>

      <div class="flex flex-wrap justify-center md:justify-start gap-2 sm:gap-3">
        <?php if (!empty($company_data['industry_name'])): ?>
          <span class="px-4 py-1.5 bg-indigo-100 text-indigo-700 rounded-full text-sm shadow-sm">🚀 <?= htmlspecialchars($company_data['industry_name']) ?></span>
        <?php endif; ?>
        <?php if (!empty($company_data['recuiter_type'])): ?>
          <span class="px-4 py-1.5 bg-purple-100 text-purple-700 rounded-full text-sm shadow-sm">🏢 <?= htmlspecialchars($company_data['recuiter_type']) ?></span>
        <?php endif; ?>
        <?php if (!empty($company_data['company_founded'])):
          $formattedFounded = date('F Y', strtotime($company_data['company_founded']));
        ?>
          <span class="px-4 py-1.5 bg-blue-100 text-blue-700 rounded-full text-sm shadow-sm">📅 Since <?= htmlspecialchars($formattedFounded) ?></span>
        <?php endif; ?>
      </div>
    </div>

    <!-- ✅ Follow Button -->
    <button class="mt-4 md:mt-0 px-6 py-2.5 bg-indigo-600 text-white rounded-xl font-semibold shadow-md hover:bg-indigo-700 transition">
      + Follow Company
    </button>
  </div>
</div>


  <!-- Tabs -->
	<div class="mt-10 border-b border-gray-200/50">
	  <div class="flex space-x-6 sm:space-x-8 relative overflow-x-auto">
		<button class="tab-button pb-3 px-1 text-base sm:text-lg font-semibold text-purple-600 relative" data-tab="jobs">
		  Jobs (<?= isset($jobs) ? count($jobs) : '0' ?>)
		  <div class="tab-underline absolute bottom-0 left-0 right-0 h-1 bg-purple-600 transition-all"></div>
		</button>
		<button class="tab-button pb-3 px-1 text-base sm:text-lg font-semibold text-gray-600 hover:text-purple-600 relative" data-tab="overview">
		  Overview
		  <div class="tab-underline absolute bottom-0 left-0 right-0 h-1 bg-purple-600 opacity-0 transition-all"></div>
		</button>
	  </div>
	</div>


	<!-- Overview Tab -->
	<div id="overview" class="tab-content py-6 sm:py-8 space-y-6 hidden">
		<div class="bg-white/90 backdrop-blur-md rounded-xl p-6 sm:p-8 shadow border border-white/40">
		  <h2 class="text-xl sm:text-2xl font-bold text-gray-800 mb-4">✨ About Us</h2>
		  <p class="text-gray-700 text-sm sm:text-base leading-relaxed">
			<?= !empty($company_data['about_company']) ? htmlspecialchars($company_data['about_company']) : 'No company description available.' ?>
		  </p>
		</div>

		<div class="bg-white/90 backdrop-blur-md rounded-xl p-6 sm:p-8 shadow border border-white/40">
		  <h2 class="text-xl sm:text-2xl font-bold text-gray-800 mb-4">📦 Departments Hiring</h2>
		  <div class="grid md:grid-cols-2 gap-4 sm:gap-6">
			<?php foreach ($departments as $dept): ?>
			  <div class="flex items-center gap-4 p-3 hover:bg-purple-50 rounded-xl transition">
				<div class="p-3 bg-purple-100 rounded-lg text-xl">📁</div>
				<div>
				  <h3 class="font-semibold text-base"><?= ucfirst($dept['department']) ?></h3>
				  <p class="text-gray-600 text-sm"><?= $dept['total_openings'] ?> openings available</p>
				</div>
			  </div>
			<?php endforeach; ?>
		  </div>
		</div>

		<div class="grid md:grid-cols-2 gap-4 sm:gap-6">
		  <div class="bg-white/90 backdrop-blur-md rounded-xl p-6 shadow border border-white/40">
			<h3 class="text-lg font-bold mb-3">🏢 Company Details</h3>
			<div class="space-y-2 text-gray-700 text-sm">
			  <?php if (!empty($company_data['company_type'])): ?>
				<p>📌 Type: <?= htmlspecialchars($company_data['company_type']) ?></p>
			  <?php endif; ?>
			  <?php if (!empty($company_data['company_size'])): ?>
				<p>👥 Employees: <?= htmlspecialchars($company_data['company_size']) ?></p>
			  <?php endif; ?>
			  <?php if (!empty($company_data['company_website'])): ?>
				<p>🌐 Website: 
				  <a href="<?= htmlspecialchars($company_data['company_website']) ?>" class="text-purple-600 hover:underline">
					<?= htmlspecialchars($company_data['company_website']) ?>
				  </a>
				</p>
			  <?php endif; ?>
			</div>
		  </div>

		  <div class="bg-white/90 backdrop-blur-md rounded-xl p-6 shadow border border-white/40">
			<h3 class="text-lg font-bold mb-3">📍 Locations</h3>
			<div class="space-y-2 text-gray-700 text-sm">
			  <?php if (!empty($company_data['city_name'])): ?>
				<p>🏛️ Headquarters: <?= htmlspecialchars($company_data['city_name']) ?></p>
			  <?php endif; ?>
			  <?php if (!empty($company_data['company_founded'])):
				$formattedFounded = date('F Y', strtotime($company_data['company_founded']));
				$foundedDate = new DateTime($company_data['company_founded']);
				$years = $foundedDate->diff(new DateTime())->y;
				$yearsAgo = $years > 0 ? " ($years year" . ($years > 1 ? 's' : '') . " ago)" : '';
			  ?>
				<p>🌍 Founded: <?= htmlspecialchars($formattedFounded) ?><?= htmlspecialchars($yearsAgo) ?></p>
			  <?php endif; ?>
			</div>
		  </div>
		</div>
    </div>

	<!-- Jobs Tab -->
	<div id="jobs" class="tab-content py-6">
	  <div class="bg-white/90 backdrop-blur-md rounded-xl p-6 sm:p-8 shadow border border-white/40">
		<h2 class="text-xl sm:text-2xl font-bold text-gray-800 mb-6">🚀 Current Openings</h2>

		<?php if (!empty($jobs)): ?>
		  <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
			<?php foreach ($jobs as $job): ?>
			  <?php $jobSlug = generateJobSlug($job); ?>

			  <div class="bg-white rounded-xl shadow-md hover:shadow-lg border border-gray-100 p-5 transition-all flex flex-col">
				
				<!-- ✅ employer_logo + Job Title -->
				<div class="flex items-center gap-3 mb-2">
				  <?php if (!empty($job['employer_logo'])): ?>
					<img src="<?= base_url($job['employer_logo']) ?>" 
						 alt="<?= htmlspecialchars($job['company_name']) ?>" 
						 class="w-12 h-12 rounded-lg border object-cover">
				  <?php else: ?>
					<?php 
					  $initials = strtoupper(substr(preg_replace("/[^A-Za-z]/", "", $job['company_name']), 0, 2)) ?: "CO"; 
					?>
					<div class="w-12 h-12 rounded-lg flex items-center justify-center bg-gradient-to-r from-purple-500 to-indigo-600 text-white font-bold">
					  <?= $initials ?>
					</div>
				  <?php endif; ?>

				  <h3 class="font-semibold text-base text-gray-800 line-clamp-1">
					<a href="<?= base_url('job-detail/'.$jobSlug) ?>"
					   class="hover:text-purple-600 transition">
					  <?= ucfirst(htmlspecialchars($job['job_title'])) ?>
					</a>
				  </h3>
				</div>

				<!-- ✅ Company Name -->
				<p class="text-sm text-gray-600 mb-3 line-clamp-1">
				  <?= htmlspecialchars($job['company_name']) ?>
				  <span class="ml-1 text-xs text-gray-500"><?= isset($job['rating']) ? "⭐ ".$job['rating'] : "⭐ 4.5" ?></span>
				</p>

				<!-- ✅ Job Tags -->
				<div class="flex flex-wrap gap-1 mb-3">
				  <?php if (!empty($job['job_type'])): ?>
					<span class="text-xs bg-green-100 text-green-700 px-2 py-0.5 rounded-full"><?= ucfirst($job['job_type']) ?></span>
				  <?php endif; ?>
				  <?php if (!empty($job['company_type'])): ?>
					<span class="text-xs bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full"><?= $job['company_type'] ?></span>
				  <?php endif; ?>
				</div>

				<!-- ✅ Job Details -->
				<div class="text-sm text-gray-600 space-y-1 flex-1">
				  <?php if (!empty($job['min_experience']) || !empty($job['max_experience'])): ?>
					<p>📆 <?= htmlspecialchars($job['min_experience']) ?> - <?= htmlspecialchars($job['max_experience']) ?> yrs</p>
				  <?php endif; ?>
				  <?php if (!empty($job['min_salary']) || !empty($job['max_salary'])): ?>
					<p>💰 <?= number_format($job['min_salary']) ?> - <?= number_format($job['max_salary']) ?><?= !empty($job['salary_type']) ? '/'.ucfirst($job['salary_type']) : '' ?></p>
				  <?php endif; ?>
				  <p>📍 <?= !empty($job['city_names']) ? htmlspecialchars($job['city_names']) : 'Remote' ?></p>
				</div>

				<!-- ✅ Footer -->
				<div class="flex justify-between items-center text-xs text-gray-400 mt-4 pt-3 border-t">
				  <span><?= date("M d, Y", strtotime($job['created_at'])) ?></span>
				  <button class="hover:text-purple-600"><i class="far fa-bookmark"></i></button>
				</div>
			  </div>
			<?php endforeach; ?>
		  </div>
		<?php else: ?>
		  <div class="text-center text-gray-500 py-10">
			🌟 No openings found - Check back later!
		  </div>
		<?php endif; ?>
	  </div>
	</div>


</section>


<script>
    // Tab Switching Logic
     document.querySelectorAll('.tab-button').forEach(button => {
		button.addEventListener('click', () => {
		  const selectedTab = button.dataset.tab;

		  // Hide all tabs
		  document.querySelectorAll('.tab-content').forEach(tab => tab.classList.add('hidden'));

		  // Show selected tab
		  document.getElementById(selectedTab).classList.remove('hidden');

		  // Reset tab button styles
		  document.querySelectorAll('.tab-button').forEach(btn => {
			btn.classList.remove('text-purple-600');
			btn.classList.add('text-gray-600');
			btn.querySelector('.tab-underline').classList.add('opacity-0');
		  });

		  // Activate current tab button
		  button.classList.add('text-purple-600');
		  button.classList.remove('text-gray-600');
		  button.querySelector('.tab-underline').classList.remove('opacity-0');
		});
	  });


    // Horizontal Scroll with Drag for Jobs tab
    const jobStrip = document.querySelector('#jobs .scroll-smooth');
    let isDown = false;
    let startX;
    let scrollLeft;

    // Scroll Indicators Update Function
    function updateScrollIndicators() {
        if (!jobStrip) return;
        // Calculate the maximum scrollable width
        const scrollWidth = jobStrip.scrollWidth - jobStrip.clientWidth;
        const scrollPos = jobStrip.scrollLeft;
        const indicators = document.querySelectorAll('#jobs .scroll-indicator');
        if(indicators.length === 0) return;
        
        // Calculate active indicator index based on scroll percentage
        const activeIndex = Math.round((scrollPos / scrollWidth) * (indicators.length - 1));
        
        indicators.forEach((indicator, index) => {
            indicator.classList.toggle('bg-purple-600', index === activeIndex);
            indicator.classList.toggle('bg-gray-300', index !== activeIndex);
        });
    }

    if(jobStrip) {
        // Mouse Drag Handlers
        jobStrip.addEventListener('mousedown', (e) => {
            isDown = true;
            jobStrip.style.cursor = 'grabbing';
            startX = e.pageX - jobStrip.offsetLeft;
            scrollLeft = jobStrip.scrollLeft;
        });

        jobStrip.addEventListener('mouseleave', () => {
            isDown = false;
            jobStrip.style.cursor = 'grab';
        });

        jobStrip.addEventListener('mouseup', () => {
            isDown = false;
            jobStrip.style.cursor = 'grab';
            updateScrollIndicators();
        });

        jobStrip.addEventListener('mousemove', (e) => {
            if(!isDown) return;
            e.preventDefault();
            const x = e.pageX - jobStrip.offsetLeft;
            // Adjust the multiplier if needed for smooth dragging; 
            // remove multiplier for direct control or add one for faster movement.
            const walk = (x - startX);
            jobStrip.scrollLeft = scrollLeft - walk;
        });

        // Update scroll indicators on scroll
        jobStrip.addEventListener('scroll', updateScrollIndicators);

        // Initial setup of scroll indicators
        updateScrollIndicators();
    }
	
	
</script>
<style>
    /* Ensure the scroll indicators change smoothly */
    .scroll-indicator {
        transition: background-color 0.3s ease;
    }
	
    .bg-noise {
        background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 200 200' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noiseFilter'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.65' numOctaves='3' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23noiseFilter)'/%3E%3C/svg%3E");
    }

    .scrollbar-hide::-webkit-scrollbar {
        display: none;
    }

    .line-clamp-1 {
        display: -webkit-box;
        -webkit-line-clamp: 1;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .snap-x {
        scroll-snap-type: x mandatory;
    }

    .snap-center {
        scroll-snap-align: center;
    }
	
	@media (max-width: 768px) {
        .scroll-smooth {
            scroll-snap-type: x mandatory;
        }
    
        .snap-center {
            scroll-snap-align: center;
        }
    }
</style>
