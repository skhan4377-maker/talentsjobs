<div class="max-w-6xl mx-auto px-4 py-8 grid grid-cols-1 lg:grid-cols-3 gap-6">

    <!-- Left: Interview Details -->
    <div class="lg:col-span-2 bg-white border rounded-xl shadow-sm p-5">
        <div class="flex items-center gap-4 mb-4">
			<?php if (!empty($interview->employer_logo)): ?>
				<img 
					src="<?= base_url($interview->employer_logo); ?>" 
					class="w-14 h-14 rounded-full object-cover border" 
					alt="Employer Logo">
			<?php else: 
				// Generate initials from company name
				$words = explode(' ', $interview->company_name);
				$initials = '';
				foreach ($words as $w) {
					$initials .= strtoupper(substr($w, 0, 1));
				}
				$initials = substr($initials, 0, 2); // max 2 letters
			?>
				<div class="w-14 h-14 rounded-full bg-gray-300 flex items-center justify-center border text-gray-700 font-semibold text-lg">
					<?= $initials ?>
				</div>
			<?php endif; ?>

			<div>
				<h2 class="text-lg font-semibold text-gray-800"><?= htmlspecialchars($interview->job_title) ?></h2>
				<p class="text-sm text-gray-600"><?= htmlspecialchars($interview->company_name) ?></p>
				<p class="text-xs text-gray-500">
					Stage: <strong><?= $interview->ApplicationStage ?></strong>
				</p>
			</div>
		</div>


        <div class="grid grid-cols-2 gap-x-4 gap-y-3 text-sm text-gray-700 mb-4">
            <div>
                <span class="font-medium">Date:</span><br>
                <?= date('d M Y', strtotime($interview->interview_date)) ?>
            </div>
            <div>
                <span class="font-medium">Time:</span><br>
                <?= date('h:i A', strtotime($interview->interview_time)) ?>
            </div>
            <div>
                <span class="font-medium">Type:</span><br>
                <?= htmlspecialchars($interview->interview_type) ?>
            </div>
            <div>
                <span class="font-medium">Status:</span><br>
                <?php
                    $status = strtolower($interview->status);
                    $badgeClasses = [
                        'scheduled' => 'bg-blue-100 text-blue-800',
                        'completed' => 'bg-green-100 text-green-800',
                        'canceled' => 'bg-red-100 text-red-800',
                        'rescheduled' => 'bg-yellow-100 text-yellow-800'
                    ];
                ?>
                <span class="inline-block px-2 py-0.5 rounded text-xs font-medium <?= $badgeClasses[$status] ?? 'bg-gray-200 text-gray-700' ?>">
                    <?= ucfirst($interview->status) ?>
                </span>
            </div>
        </div>

        <?php if (!empty($interview->notes)): ?>
            <div class="text-xs text-gray-600 mb-3">
                <span class="font-medium text-gray-700">Notes:</span>
                <div class="mt-1 bg-gray-50 border rounded p-2 whitespace-pre-line">
                    <?= nl2br(htmlspecialchars($interview->notes)) ?>
                </div>
            </div>
        <?php endif; ?>

       <!-- Interview Link Section -->
		<div class="col-span-2 mb-3">
			<span class="font-medium">Interview Link:</span><br>
			<?php if (!empty($interview->interview_link)): ?>
				<?php if (!empty($profile['is_verified']) && $profile['is_verified'] == 1): ?>
					<a href="<?= htmlspecialchars($interview->interview_link) ?>" target="_blank" class="text-blue-600 hover:underline break-all">
						<?= htmlspecialchars($interview->interview_link) ?>
					</a>
				<?php else: ?>
					<span class="text-yellow-600 text-xs">
						Verify your email to view the interview link. 
						<a href="<?= base_url('candidate/profile') ?>" class="underline text-blue-600">Go to Profile</a>
					</span>
				<?php endif; ?>
			<?php else: ?>
				<span class="text-gray-500 text-xs">Not provided by employer</span>
			<?php endif; ?>
		</div>

		<!-- Contact Section -->       
		<div class="text-xs text-gray-600 border-t pt-3 mt-4">
			<span class="font-medium text-gray-700">Contact:</span><br>
			<?php if (!empty($profile['is_verified']) && $profile['is_verified'] ==1): ?>
				Email: <a href="mailto:<?= htmlspecialchars($interview->employer_email) ?>" class="text-blue-600 hover:underline"><?= $interview->employer_email ?></a><br>
				Phone: <?= htmlspecialchars($interview->employer_mobile) ?>
			<?php else: ?>
				<span class="text-yellow-600">
					Verify your email to see employer contact details. 
					<a href="<?= base_url('candidate/profile') ?>" class="underline text-blue-600">Go to Profile</a>
				</span>
			<?php endif; ?>
		</div>
    </div>

 
	<!-- Right: Recommended Jobs -->
	<div class="space-y-4">
		<h3 class="text-lg font-semibold text-gray-800 mb-2">Similar Jobs You May Like</h3>

		<?php if (!empty($mightBeLike)): ?>
			<?php foreach (array_slice($mightBeLike, 0, 5) as $job): 
				$companyName = ucfirst(htmlspecialchars(strip_tags($job['company_name'] ?? ''), ENT_QUOTES));
				$jobTitle    = ucfirst(htmlspecialchars(strip_tags($job['job_title'] ?? ''), ENT_QUOTES));
				$jobSlug     = generateJobSlug($job);

				// Logo or initials
				$logo       = !empty($job['logo']) ? base_url($job['logo']) : '';
				$initial    = !empty($companyName) ? strtoupper(substr($companyName, 0, 2)) : 'J';

				// Cities
				$city = "Multiple Locations";
				if (!empty($job['job_locations'])) {
					$cities = explode(',', $job['job_locations']);
					$city   = ucfirst(trim($cities[0]));
				}

				// Experience
				$experience = !empty($job['experience_range']) ? $job['experience_range'] . ' yrs' : 'Not Mentioned';

				// Salary
				$salary = $job['salary_range'] ?? '';
				if (is_numeric($salary) && floatval($salary) > 0) {
					$salaryVal = floatval($salary);
					if ($salaryVal >= 100000) {
						$salaryDisplay = '₹' . number_format($salaryVal / 100000, 0) . 'L';
					} elseif ($salaryVal >= 1000) {
						$salaryDisplay = '₹' . number_format($salaryVal / 1000, 0) . 'k';
					} else {
						$salaryDisplay = '₹' . number_format($salaryVal, 0);
					}
				} else {
					$salaryDisplay = !empty($salary) ? '₹' . htmlspecialchars($salary, ENT_QUOTES) : 'Not Disclosed';
				}
			?>
			<div class="bg-white border rounded-lg p-4 shadow-sm hover:shadow-md transition flex items-start gap-3">
				
				<!-- Logo or Initials -->
				<?php if ($logo): ?>
					<img src="<?= $logo ?>" alt="<?= $companyName ?>" class="flex-shrink-0 w-12 h-12 rounded-full object-cover">
				<?php else: ?>
					<div class="flex-shrink-0 w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold text-sm">
						<?= $initial ?>
					</div>
				<?php endif; ?>

				<!-- Job Info -->
				<div class="flex-1">
					<h4 class="text-sm font-semibold text-gray-800 mb-1">
						<a href="<?= base_url('job-detail/' . $jobSlug) ?>" class="hover:text-blue-600">
							<?= $jobTitle ?>
						</a>
					</h4>
					<p class="text-xs text-gray-500 mb-1"><?= $companyName ?></p>
					<p class="text-xs text-gray-600 mb-1"><?= $city ?> | <?= $experience ?></p>
					<p class="text-xs text-gray-600 mb-2">Salary: <?= $salaryDisplay ?></p>
					<a href="<?= base_url('job-detail/' . $jobSlug) ?>" 
					   class="inline-block text-blue-600 text-xs font-medium hover:underline">
						View Job
					</a>
				</div>
			</div>
			<?php endforeach; ?>
		<?php else: ?>
			<p class="text-gray-500 text-sm">No recommended jobs found.</p>
		<?php endif; ?>
	</div>



</div>
