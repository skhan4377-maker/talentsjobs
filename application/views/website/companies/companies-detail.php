<!-- application/views/website/companies/companies-detail.php -->
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-20 pb-8 sm:pb-12">
  
  <!-- Company Header Card -->
  <div class="bg-gradient-to-r from-indigo-50 via-blue-50 to-purple-50 rounded-2xl shadow-lg overflow-hidden border border-gray-200/50 mb-8">
    <div class="p-6 sm:p-8 flex flex-col md:flex-row items-center gap-6">
      
      <!-- Logo / Initials -->
      <div class="relative shrink-0">
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
        <!-- Rating badge -->
        <div class="absolute -bottom-2 -right-2 bg-indigo-600 text-white px-3 py-1.5 rounded-full shadow-md text-sm font-bold">
          ⭐ <?= isset($company_data['rating']) ? $company_data['rating'] : '4.8' ?>
        </div>
      </div>

      <!-- Company Info -->
      <div class="flex-1 text-center md:text-left">
        <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-800 mb-3 tracking-tight">
          <?= htmlspecialchars($company_data['company_name']) ?>
        </h1>
        <div class="flex flex-wrap justify-center md:justify-start gap-2">
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

      <!-- Follow Button (login required) -->
      <?php if ($is_logged_in): ?>
        <button class="shrink-0 px-6 py-2.5 bg-indigo-600 text-white rounded-xl font-semibold shadow-md hover:bg-indigo-700 transition focus:outline-none focus:ring-2 focus:ring-indigo-500">
          + Follow Company
        </button>
      <?php else: ?>
        <a href="<?= base_url('auth/login?redirect='.urlencode(current_url())) ?>" 
           class="shrink-0 px-6 py-2.5 bg-indigo-600 text-white rounded-xl font-semibold shadow-md hover:bg-indigo-700 transition focus:outline-none focus:ring-2 focus:ring-indigo-500">
          Login to Follow
        </a>
      <?php endif; ?>
    </div>
  </div>

  <!-- Tabs -->
  <div class="border-b border-gray-200 mb-6">
    <div class="flex space-x-8">
      <button class="tab-button pb-3 px-1 text-base sm:text-lg font-semibold text-indigo-600 border-b-2 border-indigo-600" data-tab="jobs">
        Jobs (<?= isset($jobs) ? count($jobs) : '0' ?>)
      </button>
      <button class="tab-button pb-3 px-1 text-base sm:text-lg font-semibold text-gray-500 hover:text-indigo-600 border-b-2 border-transparent" data-tab="overview">
        Overview
      </button>
    </div>
  </div>

  <div id="jobs" class="tab-content block">
  <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 sm:p-8">
    <h2 class="text-xl sm:text-2xl font-bold text-gray-800 mb-6 flex items-center gap-2">
      <i class="fas fa-briefcase text-indigo-600"></i> Current Openings
    </h2>

    <?php if (!empty($jobs)): ?>
      <div class="space-y-3">
        <?php foreach ($jobs as $job): ?>
          <?php
            // Prepare data
            $jobId = $job['job_id'] ?? '';
            $jobSlug = $job['slug'];
            $companyName = $job['company_name'] ?? 'Company';
            $jobTitle = ucfirst(htmlspecialchars($job['job_title'] ?? 'Untitled'));
            $jobType = $job['job_type'] ?? '';
            $minExp = $job['min_experience'] ?? '0';
            $maxExp = $job['max_experience'] ?? '0';
            $minSal = isset($job['min_salary']) ? number_format($job['min_salary'], 0) : '0';
            $maxSal = isset($job['max_salary']) ? number_format($job['max_salary'], 0) : '0';
            $salaryType = $job['salary_type'] ?? '';
            $salaryText = "₹ $minSal - $maxSal " . ucfirst($salaryType);
            $expText = "$minExp - $maxExp yrs";
            $postDate = !empty($job['created_at']) ? strtotime($job['created_at']) : time();
            $timeAgo = timeAgo($postDate); // ensure helper is loaded
            $isPaid = !empty($job['is_paid']) && $job['is_paid'] == 1;

            // Company logo / initials
            if (!empty($job['employer_logo'])) {
                $thumbnail = '<img src="' . base_url($job['employer_logo']) . '" 
                    class="w-full h-full object-cover rounded-md" 
                    alt="' . htmlspecialchars($companyName) . '" />';
            } else {
                $initials = strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $companyName), 0, 2));
                if (empty($initials)) $initials = "CO";
                $thumbnail = '<div class="w-full h-full flex items-center justify-center bg-gray-200 text-gray-600 font-bold rounded-md text-xs uppercase">'
                            . $initials . '</div>';
            }

            // Cities pills with Show More (if multiple)
            $citiesArray = !empty($job['city_names']) ? array_map('trim', explode(',', $job['city_names'])) : [];
            $cityDisplay = '<div class="inline-flex flex-wrap items-center gap-1">';
            if (!empty($citiesArray)) {
                $visibleCities = array_slice($citiesArray, 0, 2);
                $remainingCities = array_slice($citiesArray, 2);
                foreach ($visibleCities as $c) {
                    $cityDisplay .= '<span class="city-pill px-1.5 py-0.5 rounded-full text-xs font-normal bg-gray-100 text-gray-800">
                                        <i class="fa fa-map-marker mr-1 text-gray-500 text-[10px]"></i>' . htmlspecialchars($c) . '</span>';
                }
                if (!empty($remainingCities)) {
                    $cityDisplay .= '<span class="extra-cities hidden inline-flex flex-wrap gap-1">';
                    foreach ($remainingCities as $c) {
                        $cityDisplay .= '<span class="city-pill px-1.5 py-0.5 rounded-full text-xs font-normal bg-gray-100 text-gray-800">
                                            <i class="fa fa-map-marker mr-1 text-gray-500 text-[10px]"></i>' . htmlspecialchars($c) . '</span>';
                    }
                    $cityDisplay .= '</span>';
                    $cityDisplay .= '<button type="button" class="show-more-cities text-xs px-1.5 py-0.5 rounded bg-gray-100 hover:bg-gray-200 text-blue-600 font-medium">
                                        +' . count($remainingCities) . '</button>';
                }
            }
            $cityDisplay .= '</div>';
          ?>

          <!-- Strip Card -->
          <div class="bg-white rounded-lg border border-gray-100 p-2 sm:p-3 hover:shadow-sm transition-shadow duration-200 relative">
            <?php if ($isPaid): ?>
              <div class="absolute top-0 left-0">
                <span class="px-2 py-0.5 text-xs font-semibold rounded-tr-lg rounded-bl-lg 
                             bg-gradient-to-r from-yellow-500 to-amber-600 text-white shadow-sm">
                  <i class="fa fa-crown mr-1 text-xs"></i> Premium
                </span>
              </div>
            <?php endif; ?>

            <div class="flex flex-row items-start gap-2 sm:gap-3">
              <!-- Logo -->
              <div class="flex-shrink-0 w-10 h-10 rounded-md overflow-hidden border border-gray-100 shadow-sm">
                <?= $thumbnail ?>
              </div>

              <!-- Main info -->
              <div class="flex-1 min-w-0">
                <div class="flex flex-wrap items-baseline gap-x-1 gap-y-0.5 mb-1">
                  <a href="<?= site_url($jobSlug) ?>" class="text-sm font-semibold text-gray-900 hover:text-blue-600 truncate max-w-full">
                    <?= $jobTitle ?>
                  </a>
                  <span class="text-xs text-gray-500 whitespace-nowrap">at</span>
                  <span class="text-xs font-medium text-gray-700 truncate max-w-full">
                    <?= htmlspecialchars($companyName) ?>
                  </span>
                </div>

                <!-- Details row (pills) -->
                <div class="flex flex-wrap items-center gap-x-1.5 gap-y-0.5 text-xs text-gray-500">
                  <?= $cityDisplay ?>
                  <span class="inline-flex items-center whitespace-nowrap">
                    <i class="fa fa-money text-gray-500 text-xs mr-1"></i><?= $salaryText ?>
                  </span>
                  <span class="inline-flex items-center whitespace-nowrap">
                    <i class="fa fa-briefcase text-gray-500 text-xs mr-1"></i><?= $expText ?>
                  </span>
                  <span class="text-gray-400 text-xs whitespace-nowrap">
                    <i class="fa fa-clock-o text-gray-500 text-xs mr-1"></i><?= $timeAgo ?>
                  </span>
                </div>
              </div>

              <!-- Apply button (only action) -->
              <div class="flex items-center gap-1.5 sm:gap-2 flex-shrink-0">
                <a href="<?= site_url($jobSlug) ?>" 
                   class="inline-flex items-center px-2.5 sm:px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-medium rounded-md transition-colors whitespace-nowrap">
                  Apply
                  <i class="fa fa-arrow-right text-xs ml-1"></i>
                </a>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php else: ?>
      <div class="text-center text-gray-500 py-10">
        <i class="fas fa-search text-3xl mb-3 text-gray-300"></i>
        <p>No active openings at the moment. Check back later!</p>
      </div>
    <?php endif; ?>
  </div>
</div>
 
 <!-- Overview Tab -->
  <div id="overview" class="tab-content hidden">
    <div class="space-y-6">
      
      <!-- About Us -->
      <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 sm:p-8">
        <h2 class="text-xl sm:text-2xl font-bold text-gray-800 mb-4 flex items-center gap-2">
          <i class="fas fa-building text-indigo-600"></i> About Us
        </h2>
        <p class="text-gray-700 text-sm sm:text-base leading-relaxed">
          <?= !empty($company_data['about_company']) ? nl2br(htmlspecialchars($company_data['about_company'])) : 'No company description available.' ?>
        </p>
      </div>

      <!-- Departments Hiring -->
      <?php if (!empty($departments)): ?>
      <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 sm:p-8">
        <h2 class="text-xl sm:text-2xl font-bold text-gray-800 mb-4 flex items-center gap-2">
          <i class="fas fa-users text-indigo-600"></i> Departments Hiring
        </h2>
        <div class="grid md:grid-cols-2 gap-4">
          <?php foreach ($departments as $dept): ?>
            <div class="flex items-center gap-4 p-3 hover:bg-indigo-50 rounded-lg transition">
              <div class="p-3 bg-indigo-100 rounded-lg text-xl">📁</div>
              <div>
                <h3 class="font-semibold text-gray-800"><?= ucfirst($dept['department']) ?></h3>
                <p class="text-sm text-gray-600"><?= $dept['total_openings'] ?> opening<?= $dept['total_openings'] > 1 ? 's' : '' ?></p>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
      <?php endif; ?>

      <!-- Company Details & Locations Grid -->
      <div class="grid md:grid-cols-2 gap-6">
        
        <!-- Company Details -->
       <!-- Company Details -->
		<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
		  <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
			<i class="fas fa-info-circle text-indigo-600"></i> Company Details
		  </h3>
		  <div class="space-y-3 text-sm">
			<?php if (!empty($company_data['company_type'])): ?>
			  <p class="flex items-start gap-2">
				<span class="font-medium text-gray-600 min-w-[90px]">Type:</span>
				<span class="text-gray-800"><?= htmlspecialchars($company_data['company_type']) ?></span>
			  </p>
			<?php endif; ?>
			<?php if (!empty($company_data['company_size'])): ?>
			  <p class="flex items-start gap-2">
				<span class="font-medium text-gray-600 min-w-[90px]">Employees:</span>
				<span class="text-gray-800"><?= htmlspecialchars($company_data['company_size']) ?></span>
			  </p>
			<?php endif; ?>
			<?php if (!empty($company_data['company_website'])): ?>
			  <p class="flex items-start gap-2">
				<span class="font-medium text-gray-600 min-w-[90px]">Website:</span>
				<a href="<?= htmlspecialchars($company_data['company_website']) ?>" target="_blank" rel="noopener noreferrer" class="text-indigo-600 hover:underline truncate">
				  <?= htmlspecialchars($company_data['company_website']) ?>
				</a>
			  </p>
			<?php endif; ?>

			<!-- Email - conditional based on login -->
			<?php if (!empty($company_data['email'])): ?>
			  <p class="flex items-start gap-2">
				<span class="font-medium text-gray-600 min-w-[90px]">Email:</span>
				<?php if ($isLoggedIn): ?>
				  <a href="mailto:<?= htmlspecialchars($company_data['email']) ?>" class="text-indigo-600 hover:underline truncate">
					<?= htmlspecialchars($company_data['email']) ?>
				  </a>
				<?php else: ?>
				  <span class="text-gray-500 italic">
					<a href="<?= base_url('auth/login') ?>" class="text-indigo-600 hover:underline">Login</a> to view email
				  </span>
				<?php endif; ?>
			  </p>
			<?php endif; ?>

			<!-- Phone - conditional based on login -->
			<?php if (!empty($company_data['mobile'])): ?>
			  <p class="flex items-start gap-2">
				<span class="font-medium text-gray-600 min-w-[90px]">Phone:</span>
				<?php if ($isLoggedIn): ?>
				  <span class="text-gray-800"><?= htmlspecialchars($company_data['mobile']) ?></span>
				<?php else: ?>
				  <span class="text-gray-500 italic">
					<a href="<?= base_url('auth/login') ?>" class="text-indigo-600 hover:underline">Login</a> to view phone
				  </span>
				<?php endif; ?>
			  </p>
			<?php endif; ?>
		  </div>
		</div>
        <!-- Locations -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
          <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
            <i class="fas fa-map-marker-alt text-indigo-600"></i> Locations
          </h3>
          <div class="space-y-3 text-sm">
            <?php if (!empty($company_data['city_name'])): ?>
              <p class="flex items-start gap-2">
                <span class="font-medium text-gray-600 min-w-[90px]">Headquarters:</span>
                <span class="text-gray-800"><?= htmlspecialchars($company_data['city_name']) ?></span>
              </p>
            <?php endif; ?>
            <?php if (!empty($company_data['company_founded'])): 
              $foundedDate = new DateTime($company_data['company_founded']);
              $years = $foundedDate->diff(new DateTime())->y;
              $yearsAgo = $years > 0 ? " ($years year" . ($years > 1 ? 's' : '') . " ago)" : '';
            ?>
              <p class="flex items-start gap-2">
                <span class="font-medium text-gray-600 min-w-[90px]">Founded:</span>
                <span class="text-gray-800"><?= date('F Y', strtotime($company_data['company_founded'])) . $yearsAgo ?></span>
              </p>
            <?php endif; ?>
            <?php if (!empty($company_data['company_address'])): ?>
              <p class="flex items-start gap-2">
                <span class="font-medium text-gray-600 min-w-[90px]">Address:</span>
                <span class="text-gray-800"><?= htmlspecialchars($company_data['company_address']) ?></span>
              </p>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
  </div>

</div>

<script>
// Tab switching
document.querySelectorAll('.tab-button').forEach(button => {
  button.addEventListener('click', () => {
    const tabName = button.dataset.tab;

    // Update button styles
    document.querySelectorAll('.tab-button').forEach(btn => {
      btn.classList.remove('text-indigo-600', 'border-indigo-600');
      btn.classList.add('text-gray-500', 'border-transparent');
    });
    button.classList.add('text-indigo-600', 'border-indigo-600');
    button.classList.remove('text-gray-500', 'border-transparent');

    // Show/hide content
    document.querySelectorAll('.tab-content').forEach(content => {
      content.classList.add('hidden');
    });
    document.getElementById(tabName).classList.remove('hidden');
  });
});
</script>