<div class="max-w-6xl mx-auto grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6">

    <!-- Left: Interview Details -->
    <div class="lg:col-span-2 bg-white border rounded-xl shadow-sm p-3 sm:p-5">
        <div class="flex items-center gap-3 sm:gap-4 mb-3 sm:mb-4">
            <?php if (!empty($interview->employer_logo)): ?>
                <img 
                    src="<?= base_url($interview->employer_logo); ?>" 
                    class="w-10 h-10 sm:w-14 sm:h-14 rounded-full object-cover border" 
                    alt="Employer Logo">
            <?php else: 
                $words = explode(' ', $interview->company_name);
                $initials = '';
                foreach ($words as $w) {
                    $initials .= strtoupper(substr($w, 0, 1));
                }
                $initials = substr($initials, 0, 2);
            ?>
                <div class="w-10 h-10 sm:w-14 sm:h-14 rounded-full bg-gray-300 flex items-center justify-center border text-gray-700 font-semibold text-base sm:text-lg">
                    <?= $initials ?>
                </div>
            <?php endif; ?>

            <div>
                <h2 class="text-base sm:text-lg font-semibold text-gray-800"><?= htmlspecialchars($interview->job_title) ?></h2>
                <p class="text-xs sm:text-sm text-gray-600"><?= htmlspecialchars($interview->company_name) ?></p>
                <p class="text-xs text-gray-500">
                    Stage: <strong><?= $interview->ApplicationStage ?></strong>
                </p>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-4 gap-y-2 sm:gap-y-3 text-sm text-gray-700 mb-4">
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
                    <a href="<?= htmlspecialchars($interview->interview_link) ?>" target="_blank" class="text-blue-600 hover:underline break-all text-xs sm:text-sm">
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
                Email: <a href="mailto:<?= htmlspecialchars($interview->employer_email) ?>" class="text-blue-600 hover:underline break-all"><?= $interview->employer_email ?></a><br>
                Phone: <?= htmlspecialchars($interview->employer_mobile) ?>
            <?php else: ?>
                <span class="text-yellow-600">
                    Verify your email to see employer contact details. 
                    <a href="<?= base_url('candidate/profile') ?>" class="underline text-blue-600">Go to Profile</a>
                </span>
            <?php endif; ?>
        </div>
    </div>

    <!-- Right: Recommended Jobs - Strip Layout (Responsive) -->
    <div class="space-y-3 sm:space-y-4">
        <div class="flex items-center justify-between">
            <h3 class="text-base sm:text-lg font-semibold text-gray-800">Similar Jobs You May Like</h3>
            <a href="<?= base_url('browse-jobs') ?>" class="text-blue-600 hover:text-blue-700 text-xs font-medium flex items-center gap-1">
                View All
                <i class="fas fa-arrow-right text-xs"></i>
            </a>
        </div>

        <?php if (!empty($mightBeLike)): ?>
            <div class="space-y-2 sm:space-y-3">
                <?php foreach (array_slice($mightBeLike, 0, 5) as $job): 
                    $companyName = ucfirst(htmlspecialchars(strip_tags($job['company_name'] ?? ''), ENT_QUOTES));
                    $shortCompany = strlen($companyName) > 25 ? substr($companyName, 0, 25) . '…' : $companyName;
                    
                    $hasLogo = !empty($job['logo']);
                    if ($hasLogo) {
                        $logoHtml = '<img src="'.base_url($job['logo']).'" alt="'.$companyName.'" class="w-full h-full object-cover rounded-md" />';
                    } else {
                        $initials = strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $companyName), 0, 2));
                        if (empty($initials)) $initials = "CO";
                        $logoHtml = '<div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-blue-500 to-purple-600 text-white font-bold rounded-md text-xs uppercase">'
                                   .$initials.'</div>';
                    }
                    
                    $jobTitle = ucfirst(htmlspecialchars(strip_tags($job['job_title'] ?? ''), ENT_QUOTES));
                    $shortTitle = strlen($jobTitle) > 30 ? substr($jobTitle, 0, 30) . '…' : $jobTitle;
                    
                    $createdAt = isset($job['created_at']) ? strtotime($job['created_at']) : time();
                    $timeAgo = timeAgo($createdAt);
                    
                    $cities = [];
                    if (!empty($job['job_locations'])) {
                        $rawCities = explode(',', $job['job_locations']);
                        foreach ($rawCities as $c) {
                            $c = trim($c);
                            if ($c !== '') $cities[] = $c;
                        }
                    }
                    
                    $salaryDisplay = 'Not disclosed';
                    $minSalary = isset($job['min_salary']) ? floatval($job['min_salary']) : 0;
                    $maxSalary = isset($job['max_salary']) ? floatval($job['max_salary']) : 0;
                    $salaryRange = $job['salary_range'] ?? '';
                    
                    if ($minSalary > 0 && $maxSalary > 0) {
                        $salaryDisplay = formatSalary($minSalary) . ' - ' . formatSalary($maxSalary);
                    } elseif ($minSalary > 0) {
                        $salaryDisplay = formatSalary($minSalary) . ' and above';
                    } elseif ($maxSalary > 0) {
                        $salaryDisplay = 'Up to ' . formatSalary($maxSalary);
                    } elseif (!empty($salaryRange) && $salaryRange !== '0') {
                        $salaryDisplay = htmlspecialchars($salaryRange, ENT_QUOTES);
                    }
                    
                    $jobType = $job['job_type'] ?? null;
                    $jobSlug = $job['slug'];
                ?>
                <div class="bg-white rounded-lg border border-gray-100 p-2 sm:p-3 hover:shadow-sm transition-shadow duration-200">
                    <div class="flex flex-row items-start gap-2 sm:gap-3">
                        <!-- Logo -->
                        <div class="flex-shrink-0 w-8 h-8 sm:w-10 sm:h-10 rounded-md overflow-hidden border border-gray-100 shadow-sm">
                            <?= $logoHtml ?>
                        </div>
                        
                        <!-- Main Info -->
                        <div class="flex-1 min-w-0">
                            <div class="flex flex-wrap items-baseline gap-1 mb-1">
                                <a href="<?= base_url($jobSlug) ?>" class="text-xs sm:text-sm font-semibold text-gray-900 hover:text-blue-600 truncate">
                                    <?= $shortTitle ?>
                                </a>
                                <span class="text-[10px] sm:text-xs text-gray-500">at</span>
                                <span class="text-[10px] sm:text-xs font-medium text-gray-700 truncate"><?= $shortCompany ?></span>
                            </div>
                            <div class="flex flex-wrap items-center gap-x-2 gap-y-1 text-[10px] sm:text-xs text-gray-500">
                                <!-- Location -->
                                <span class="inline-flex items-center truncate max-w-[120px] sm:max-w-[150px]">
                                    <i class="fas fa-map-marker-alt text-[10px] sm:text-xs mr-1 text-gray-400"></i>
                                    <?php if (!empty($cities)): ?>
                                        <span class="truncate"><?= htmlspecialchars(implode(', ', array_slice($cities, 0, 2))) ?></span>
                                        <?php if (count($cities) > 2): ?>
                                            <span class="ml-1 text-gray-400">+<?= count($cities) - 2 ?></span>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <span>Multiple Locations</span>
                                    <?php endif; ?>
                                </span>
                                <!-- Salary -->
                                <span class="inline-flex items-center whitespace-nowrap">
                                    <i class="fas fa-rupee-sign text-[10px] sm:text-xs mr-1 text-gray-400"></i>
                                    <?= $salaryDisplay ?>
                                </span>
                                <!-- Job Type -->
                                <?php if (!empty($jobType)): ?>
                                <span class="inline-flex items-center whitespace-nowrap">
                                    <i class="fas fa-briefcase text-[10px] sm:text-xs mr-1 text-gray-400"></i>
                                    <?= ucfirst($jobType) ?>
                                </span>
                                <?php endif; ?>
                                <!-- Time -->
                                <span class="text-gray-400 text-[10px] sm:text-xs whitespace-nowrap">
                                    <i class="far fa-clock text-[10px] sm:text-xs mr-1"></i><?= $timeAgo ?>
                                </span>
                            </div>
                        </div>
                        
                        <!-- Apply Button -->
                        <div class="flex-shrink-0">
                            <a href="<?= base_url($jobSlug) ?>" 
                               class="inline-flex items-center px-2 py-1 sm:px-3 sm:py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-[10px] sm:text-xs font-medium rounded-md transition-colors">
                                Apply
                                <i class="fas fa-arrow-right text-[8px] sm:text-xs ml-0.5 sm:ml-1"></i>
                            </a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p class="text-gray-500 text-xs sm:text-sm">No similar jobs found.</p>
        <?php endif; ?>
    </div>
</div>