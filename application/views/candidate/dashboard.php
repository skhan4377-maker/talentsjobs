<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-153460368-1"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());
  gtag('config', 'UA-153460368-1');
</script>

<!-- Dashboard Stats Grid -->
<div class="grid grid-cols-2 md:grid-cols-3 gap-3 mb-6">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-3 flex items-center">
        <div class="flex-grow">
            <p class="text-gray-500 text-xs">Shortlisted Jobs</p>
            <p class="text-xl font-bold text-gray-800 mt-1" id="shortlist">0</p>
        </div>
        <div class="p-2 bg-blue-100 rounded-lg">
            <i class="fas fa-bookmark text-blue-600 text-base"></i>
        </div>
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-3 flex items-center">
        <div class="flex-grow">
            <p class="text-gray-500 text-xs">Hired Jobs</p>
            <p class="text-xl font-bold text-gray-800 mt-1" id="hired">0</p>
        </div>
        <div class="p-2 bg-green-100 rounded-lg">
            <i class="fas fa-check text-green-600 text-base"></i>
        </div>
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-3 flex items-center">
        <div class="flex-grow">
            <p class="text-gray-500 text-xs">Under Review</p>
            <p class="text-xl font-bold text-gray-800 mt-1" id="review">0</p>
        </div>
        <div class="p-2 bg-yellow-100 rounded-lg">
            <i class="fas fa-eye text-yellow-600 text-base"></i>
        </div>
    </div>
</div>

<!-- PREMIUM PLAN SECTION START -->
<?php if (!empty($active_plan)): ?>
<div class="mb-6">
    <!-- Profile Booster Active Banner -->
    <div class="bg-gradient-to-r from-orange-500 via-red-500 to-pink-600 rounded-xl p-5 text-white shadow-lg mb-4">
        <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4">
            <div>
                <div class="flex items-center gap-2 mb-2">
                    <i class="fas fa-rocket text-yellow-300 text-xl"></i>
                    <h3 class="text-xl font-bold"><?= htmlspecialchars($active_plan['feature_name']) ?> Active</h3>
                </div>
                <p class="text-sm text-white/90">
                    Your profile is currently receiving higher visibility in recruiter searches.
                </p>
            </div>
            <div class="text-left md:text-right">
                <div class="text-sm opacity-90">Valid Till</div>
                <div class="font-bold text-lg"><?= $active_plan['end_date_formatted'] ?></div>
                <?php if ($active_plan['days_remaining'] <= 7 && !empty($active_plan['expiry_timestamp'])): ?>
                    <!-- Live Countdown Timer -->
                    <div class="text-sm mt-1" id="plan-expiry-countdown" data-expiry="<?= $active_plan['expiry_timestamp'] ?>">
                        <span id="plan-days"><?= $active_plan['days_remaining'] ?></span>d 
                        <span id="plan-hours">00</span>h 
                        <span id="plan-mins">00</span>m 
                        <span id="plan-secs">00</span>s remaining
                    </div>
                <?php else: ?>
                    <div class="text-sm mt-1"><?= $active_plan['days_remaining'] ?> Days Remaining</div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Premium Stats -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-4">
        <div class="bg-white rounded-xl border border-gray-100 p-4 text-center shadow-sm">
            <i class="fas fa-eye text-blue-500 text-xl mb-2"></i>
            <div class="text-xs text-gray-500">Profile Views</div>
            <div class="text-2xl font-bold">128</div>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 p-4 text-center shadow-sm">
            <i class="fas fa-search text-green-500 text-xl mb-2"></i>
            <div class="text-xs text-gray-500">Search Appearances</div>
            <div class="text-2xl font-bold">542</div>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 p-4 text-center shadow-sm">
            <i class="fas fa-user-tie text-purple-500 text-xl mb-2"></i>
            <div class="text-xs text-gray-500">Recruiter Views</div>
            <div class="text-2xl font-bold">12</div>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 p-4 text-center shadow-sm">
            <i class="fas fa-star text-orange-500 text-xl mb-2"></i>
            <div class="text-xs text-gray-500">Boost Status</div>
            <div class="text-lg font-bold text-green-600">Active</div>
        </div>
    </div>

    <!-- Boost Status -->
    <div class="bg-green-50 border border-green-200 rounded-xl p-4 mb-4">
        <h4 class="font-semibold text-green-800 mb-3">🚀 Boost Status</h4>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-2 text-sm">
            <div>✅ Boost Active</div>
            <div>✅ Priority Ranking Enabled</div>
            <div>✅ Featured Candidate Enabled</div>
        </div>
        <p class="text-sm text-green-700 mt-3">
            Your profile is being shown at higher positions in recruiter searches.
        </p>
    </div>

    <!-- Recruiter Activity -->
    <div class="bg-white rounded-xl border border-gray-100 p-4 shadow-sm mb-4">
        <h4 class="font-semibold mb-4">👔 Recruiter Activity</h4>
        <div class="grid grid-cols-3 gap-3 text-center">
            <div>
                <div class="text-2xl font-bold text-blue-600">12</div>
                <div class="text-xs text-gray-500">Recruiters Viewed</div>
            </div>
            <div>
                <div class="text-2xl font-bold text-green-600">4</div>
                <div class="text-xs text-gray-500">Companies Interested</div>
            </div>
            <div>
                <div class="text-2xl font-bold text-purple-600">8</div>
                <div class="text-xs text-gray-500">Contact Unlocks</div>
            </div>
        </div>
    </div>

    <!-- Premium Benefits -->
    <div class="bg-white rounded-xl border border-gray-100 p-4 shadow-sm mb-4">
        <h4 class="font-semibold mb-4">⭐ Your Premium Benefits</h4>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-2 text-sm">
            <div>✔ Priority in candidate search</div>
            <div>✔ Higher profile visibility</div>
            <div>✔ Featured badge on profile</div>
            <div>✔ More recruiter exposure</div>
            <div>✔ Increased interview chances</div>
            <div>✔ Better search ranking</div>
        </div>
    </div>

    <!-- Performance Analytics -->
    <div class="bg-white rounded-xl border border-gray-100 p-4 shadow-sm">
        <h4 class="font-semibold mb-4">📊 Last 30 Days Performance</h4>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead><tr class="border-b"><th class="text-left py-2">Metric</th><th class="text-right py-2">Count</th></tr></thead>
                <tbody>
                    <tr class="border-b"><td class="py-2">Profile Views</td><td class="text-right">128</td></tr>
                    <tr class="border-b"><td class="py-2">Recruiter Searches</td><td class="text-right">542</td></tr>
                    <tr class="border-b"><td class="py-2">Shortlists</td><td class="text-right">14</td></tr>
                    <tr><td class="py-2">Applications</td><td class="text-right">23</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php endif; ?>
<!-- PREMIUM PLAN SECTION END -->

<?= $this->load->view('common/header_ads_tj', '', TRUE) ?>

<!-- Profile Completeness Banner -->
<?php if ($is_active && $profile_completeness['percentage'] < 100): ?>
<div class="bg-gradient-to-r from-blue-500 to-purple-600 text-white rounded-xl p-4 mb-6 shadow-lg">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div class="flex-1">
            <div class="flex items-center mb-2">
                <i class="fas fa-user-circle mr-2 text-sm"></i>
                <span class="font-semibold text-sm">Profile: <?= $profile_completeness['percentage'] ?>% Complete</span>
            </div>
            <div class="w-full bg-white/30 rounded-full h-1.5 mb-2">
                <div class="bg-yellow-400 h-1.5 rounded-full" style="width: <?= $profile_completeness['percentage'] ?>%"></div>
            </div>
            <p class="text-white/90 text-xs leading-relaxed">
                Complete your profile for better job matches. Missing:
                <?= implode(', ', array_slice($profile_completeness['missing_fields'], 0, 2)) ?>
                <?= count($profile_completeness['missing_fields']) > 2 ? '...' : '' ?>
            </p>
        </div>
        <div class="shrink-0">
            <a href="<?= site_url('candidate/profile') ?>" class="px-4 py-1.5 bg-white text-blue-600 rounded-lg hover:bg-opacity-90 transition-all text-xs font-medium whitespace-nowrap">Complete Now</a>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Additional Stats Cards -->
<div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-6">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-3 text-center">
        <i class="fas fa-file-alt text-blue-500 text-xl mb-1"></i>
        <p class="text-gray-600 text-xs">Total Applied</p>
        <p class="text-xl font-bold text-gray-800"><?= $additional_stats['applied_total'] ?? 0 ?></p>
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-3 text-center">
        <i class="fas fa-heart text-red-500 text-xl mb-1"></i>
        <p class="text-gray-600 text-xs">Saved Jobs</p>
        <p class="text-xl font-bold text-gray-800"><?= $additional_stats['saved_total'] ?? 0 ?></p>
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-3 text-center">
        <i class="fas fa-download text-green-500 text-xl mb-1"></i>
        <p class="text-gray-600 text-xs">Resume Downloads</p>
        <p class="text-xl font-bold text-gray-800"><?= $additional_stats['resume_downloads'] ?? 0 ?></p>
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-3 text-center">
        <i class="fas fa-palette text-purple-500 text-xl mb-1"></i>
        <p class="text-gray-600 text-xs">Template Uses</p>
        <p class="text-xl font-bold text-gray-800"><?= $additional_stats['template_uses'] ?? 0 ?></p>
    </div>
</div>

<!-- Application Activity Chart -->
<?php if (!empty($additional_stats['application_activity'])): ?>
<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 mb-6">
    <div class="flex items-center gap-2 mb-4">
        <div class="w-1.5 h-6 bg-gradient-to-b from-teal-600 to-blue-600 rounded-full"></div>
        <h2 class="text-lg font-bold text-gray-900">Application Activity (Last 6 Months)</h2>
    </div>
    <canvas id="applicationChart" height="100"></canvas>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctx = document.getElementById('applicationChart').getContext('2d');
new Chart(ctx, {
    type: 'line',
    data: {
        labels: <?= json_encode(array_column($additional_stats['application_activity'], 'month')) ?>,
        datasets: [{
            label: 'Applications',
            data: <?= json_encode(array_column($additional_stats['application_activity'], 'count')) ?>,
            borderColor: '#3b82f6',
            backgroundColor: 'rgba(59,130,246,0.1)',
            tension: 0.3,
            fill: true
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: { legend: { position: 'top' } }
    }
});
</script>
<?php endif; ?>

<!-- Recommended Jobs Section -->
<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 mb-6">
    <div class="flex flex-col md:flex-row items-start md:items-center justify-between mb-4 gap-2">
        <div class="flex items-center gap-2">
            <div class="w-1.5 h-6 bg-gradient-to-b from-blue-600 to-purple-600 rounded-full"></div>
            <h2 class="text-lg font-bold text-gray-900">Recommended For You</h2>
        </div>
        <a href="<?= base_url('browse-jobs') ?>" class="group flex items-center gap-1 px-3 py-1.5 bg-gray-50 hover:bg-gray-100 text-gray-700 rounded-lg transition-all text-xs font-medium border border-gray-200">
            View All <i class="fas fa-arrow-right transform group-hover:translate-x-0.5 transition-transform text-xs"></i>
        </a>
    </div>

    <div class="space-y-3">
        <?php foreach (array_slice($mightBeLike, 0, 3) as $key):
            $companyName = ucfirst(htmlspecialchars(strip_tags($key['company_name'] ?? ''), ENT_QUOTES));
            $shortCompany = strlen($companyName) > 25 ? substr($companyName, 0, 25) . '…' : $companyName;
            $hasLogo = !empty($key['logo']);
            $logoHtml = $hasLogo ? '<img src="'.base_url($key['logo']).'" class="w-full h-full object-cover rounded-md" />' : '<div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-blue-500 to-purple-600 text-white font-bold rounded-md text-xs uppercase">'.strtoupper(substr(preg_replace('/[^A-Za-z]/','',$companyName),0,2) ?: 'CO').'</div>';

            $jobTitle = ucfirst(htmlspecialchars(strip_tags($key['job_title'] ?? ''), ENT_QUOTES));
            $shortTitle = strlen($jobTitle) > 30 ? substr($jobTitle, 0, 30) . '…' : $jobTitle;

            $timeAgo = timeAgo(strtotime($key['created_at'] ?? time()));
            $cities = [];
            if (!empty($key['job_locations'])) {
                foreach (explode(',', $key['job_locations']) as $c) { if ($c = trim($c)) $cities[] = $c; }
            }

            $salaryDisplay = 'Not disclosed';
            $min = isset($key['min_salary']) ? floatval($key['min_salary']) : 0;
            $max = isset($key['max_salary']) ? floatval($key['max_salary']) : 0;
            if ($min > 0 && $max > 0) $salaryDisplay = formatSalary($min).' - '.formatSalary($max);
            elseif ($min > 0) $salaryDisplay = formatSalary($min).' and above';
            elseif ($max > 0) $salaryDisplay = 'Up to '.formatSalary($max);
            elseif (!empty($key['salary_range']) && $key['salary_range'] != '0') $salaryDisplay = htmlspecialchars($key['salary_range'], ENT_QUOTES);

            $jobType = $key['job_type'] ?? null;
            $jobSlug = $key['slug'];
        ?>
        <div class="bg-white rounded-lg border border-gray-100 p-3 hover:shadow-sm transition-shadow duration-200">
            <div class="flex flex-row items-start gap-3">
                <div class="flex-shrink-0 w-10 h-10 rounded-md overflow-hidden border border-gray-100 shadow-sm"><?= $logoHtml ?></div>
                <div class="flex-1 min-w-0">
                    <div class="flex flex-wrap items-baseline gap-1 mb-1">
                        <a href="<?= base_url($jobSlug) ?>" class="text-sm font-semibold text-gray-900 hover:text-blue-600 truncate"><?= $shortTitle ?></a>
                        <span class="text-xs text-gray-500">at</span>
                        <span class="text-xs font-medium text-gray-700 truncate"><?= $shortCompany ?></span>
                    </div>
                    <div class="flex flex-wrap items-center gap-x-2 gap-y-1 text-xs text-gray-500">
                        <span class="inline-flex items-center truncate max-w-[150px]">
                            <i class="fas fa-map-marker-alt text-xs mr-1 text-gray-400"></i>
                            <?php if (!empty($cities)): ?>
                                <?= htmlspecialchars(implode(', ', array_slice($cities, 0, 2))) ?>
                                <?php if (count($cities) > 2): ?><span class="ml-1 text-gray-400">+<?= count($cities) - 2 ?></span><?php endif; ?>
                            <?php else: ?>
                                Multiple Locations
                            <?php endif; ?>
                        </span>
                        <span class="inline-flex items-center whitespace-nowrap"><i class="fas fa-rupee-sign text-xs mr-1 text-gray-400"></i><?= $salaryDisplay ?></span>
                        <?php if ($jobType): ?><span class="inline-flex items-center whitespace-nowrap"><i class="fas fa-briefcase text-xs mr-1 text-gray-400"></i><?= ucfirst($jobType) ?></span><?php endif; ?>
                        <span class="text-gray-400 text-xs whitespace-nowrap"><i class="far fa-clock text-xs mr-1"></i><?= $timeAgo ?></span>
                    </div>
                </div>
                <div class="flex items-center gap-2 flex-shrink-0">
                    <a href="<?= base_url($jobSlug) ?>" class="inline-flex items-center px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-medium rounded-md transition-colors">Apply <i class="fas fa-arrow-right text-xs ml-1"></i></a>
                    <button class="bookmark-job w-8 h-8 flex items-center justify-center rounded-full hover:bg-gray-100 transition-colors" data-job-id="<?= $key['job_id'] ?>" data-favorited="0"><i class="far fa-bookmark text-gray-500 text-sm"></i></button>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Quick Actions -->
<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 mb-6">
    <div class="flex items-center gap-2 mb-4">
        <div class="w-1.5 h-6 bg-gradient-to-b from-green-600 to-blue-600 rounded-full"></div>
        <h2 class="text-lg font-bold text-gray-900">Quick Actions</h2>
    </div>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
        <a href="<?= base_url('browse-jobs') ?>" class="flex flex-col items-center p-3 bg-blue-50 hover:bg-blue-100 rounded-lg transition-colors group">
            <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center mb-2 group-hover:scale-110 transition-transform"><i class="fas fa-search text-white text-sm"></i></div>
            <span class="text-xs font-medium text-gray-700 text-center">Browse Jobs</span>
        </a>
        <a href="<?= base_url('candidate/profile') ?>" class="flex flex-col items-center p-3 bg-green-50 hover:bg-green-100 rounded-lg transition-colors group">
            <div class="w-8 h-8 bg-green-600 rounded-lg flex items-center justify-center mb-2 group-hover:scale-110 transition-transform"><i class="fas fa-user-edit text-white text-sm"></i></div>
            <span class="text-xs font-medium text-gray-700 text-center">Update Profile</span>
        </a>
        <a href="<?= base_url('job/myapply') ?>" class="flex flex-col items-center p-3 bg-purple-50 hover:bg-purple-100 rounded-lg transition-colors group">
            <div class="w-8 h-8 bg-purple-600 rounded-lg flex items-center justify-center mb-2 group-hover:scale-110 transition-transform"><i class="fas fa-file-alt text-white text-sm"></i></div>
            <span class="text-xs font-medium text-gray-700 text-center">Applications</span>
        </a>
        <a href="<?= base_url('job/favourite') ?>" class="flex flex-col items-center p-3 bg-red-50 hover:bg-red-100 rounded-lg transition-colors group">
            <div class="w-8 h-8 bg-red-600 rounded-lg flex items-center justify-center mb-2 group-hover:scale-110 transition-transform"><i class="fas fa-heart text-white text-sm"></i></div>
            <span class="text-xs font-medium text-gray-700 text-center">Saved Jobs</span>
        </a>
    </div>
</div>

<!-- Blog Slider -->
<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 mb-6">
    <div class="flex items-center justify-between mb-4">
        <div class="flex items-center gap-2">
            <div class="w-1.5 h-6 bg-gradient-to-b from-orange-600 to-red-600 rounded-full"></div>
            <h2 class="text-lg font-bold text-gray-900">Career Insights</h2>
        </div>
        <a href="<?= base_url('blogs') ?>" class="text-blue-600 hover:text-blue-700 flex items-center text-xs">View All <i class="fas fa-arrow-right ml-1 text-xs"></i></a>
    </div>
    <div class="swiper blog-slider">
        <div class="swiper-wrapper">
            <?php if (!empty($blogs)): foreach (array_slice($blogs, 0, 4) as $row): ?>
            <div class="swiper-slide">
                <a href="<?= base_url('blog-detail/'.$row['slug']) ?>" class="block relative">
                    <div class="relative group overflow-hidden rounded-lg h-48">
                        <img src="<?= !empty($row['blogs_banner']) ? base_url('uploads/blogs/'.$row['blogs_banner']) : base_url('uploads/blogs/noimage.png') ?>" class="w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-300" alt="<?= htmlspecialchars($row['blogs_title']) ?>">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent p-4 flex flex-col justify-end">
                            <span class="text-xs text-white/80 mb-1"><?= htmlspecialchars($row['category_name']) ?></span>
                            <h3 class="text-sm font-semibold text-white mb-1 leading-tight"><?= htmlspecialchars($row['blogs_title']) ?></h3>
                            <div class="flex items-center text-xs text-white/80"><span class="mr-3"><i class="fas fa-clock mr-1"></i> <?= date('M d, Y', strtotime($row['created_at'])) ?></span></div>
                        </div>
                    </div>
                </a>
            </div>
            <?php endforeach; else: ?>
            <div class="swiper-slide"><div class="bg-gray-50 rounded-lg h-48 flex items-center justify-center"><p class="text-center text-gray-500 text-sm">No blogs available</p></div></div>
            <?php endif; ?>
        </div>
        <div class="swiper-pagination mt-3"></div>
    </div>
</div>

<!-- Top Companies Slider -->
<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
    <div class="flex items-center justify-between mb-4">
        <div class="flex items-center gap-2">
            <div class="w-1.5 h-6 bg-gradient-to-b from-indigo-600 to-purple-600 rounded-full"></div>
            <h2 class="text-lg font-bold text-gray-900">Hiring Companies</h2>
        </div>
        <a href="<?= base_url('companies/hiring') ?>" class="text-blue-600 hover:text-blue-700 flex items-center text-xs">View All <i class="fas fa-arrow-right ml-1 text-xs"></i></a>
    </div>
    <div class="swiper companies-slider">
        <div class="swiper-wrapper">
            <?php if (!empty($companies)): foreach (array_slice($companies, 0, 6) as $company): $hasLogo = !empty($company['logo']); $companyName = htmlspecialchars($company['company_name'] ?? 'Company'); $limitedName = strlen($companyName)>18 ? substr($companyName,0,18).'…' : $companyName; ?>
            <div class="swiper-slide">
                <div class="border border-gray-200 rounded-lg p-3 flex items-center hover:shadow-sm transition-shadow bg-white">
                    <div class="w-10 h-10 rounded-lg mr-3 flex-shrink-0 bg-gray-100 flex items-center justify-center border">
                        <?php if ($hasLogo): ?><img src="<?= base_url($company['logo']) ?>" class="w-full h-full object-contain p-1" /><?php else: ?><span class="text-gray-700 font-bold text-xs uppercase"><?= strtoupper(substr(preg_replace('/[^A-Za-z]/','',$companyName),0,2)) ?: 'CO' ?></span><?php endif; ?>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 class="font-semibold text-gray-900 text-sm truncate"><?= $limitedName ?></h3>
                        <p class="text-gray-600 text-xs truncate"><?= htmlspecialchars($company['positions_open'] ?? 'Open Positions') ?> openings</p>
                        <span class="inline-block text-xs bg-green-100 text-green-800 px-1.5 py-0.5 rounded-full mt-1">Hiring</span>
                    </div>
                </div>
            </div>
            <?php endforeach; else: ?>
            <div class="swiper-slide"><div class="border border-gray-200 rounded-lg p-4 flex items-center justify-center"><p class="text-center text-gray-500 text-sm">No companies available</p></div></div>
            <?php endif; ?>
        </div>
        <div class="swiper-pagination mt-3"></div>
    </div>
</div>

<!-- Live Countdown Script (only if plan expiring soon) -->
<?php if (!empty($active_plan) && $active_plan['days_remaining'] <= 7 && !empty($active_plan['expiry_timestamp'])): ?>
<script>
(function() {
    const timerEl = document.getElementById('plan-expiry-countdown');
    if (!timerEl) return;
    const expiryMs = parseInt(timerEl.getAttribute('data-expiry'));
    if (isNaN(expiryMs)) return;

    const daysSpan = document.getElementById('plan-days');
    const hoursSpan = document.getElementById('plan-hours');
    const minsSpan = document.getElementById('plan-mins');
    const secsSpan = document.getElementById('plan-secs');

    function update() {
        const now = Date.now();
        let diff = expiryMs - now;
        if (diff <= 0) {
            timerEl.innerHTML = '<span class="text-red-300">Plan Expired</span>';
            return;
        }
        const days = Math.floor(diff / (1000 * 60 * 60 * 24));
        const hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((diff % (1000 * 60)) / 1000);

        daysSpan.textContent = days;
        hoursSpan.textContent = String(hours).padStart(2, '0');
        minsSpan.textContent = String(minutes).padStart(2, '0');
        secsSpan.textContent = String(seconds).padStart(2, '0');
    }
    update();
    setInterval(update, 1000);
})();
</script>
<?php endif; ?>

<script>
$(document).ready(function() {
    // AJAX dashboard counts
    $.ajax({
        url: '<?= base_url('candidate/Dashboard/ajaxGetDashboardCounts') ?>',
        type: 'GET',
        dataType: 'json',
        success: function(data) {
            if (data) {
                $('#shortlist').text(data.shortlist);
                $('#hired').text(data.hired);
                $('#review').text(data.review);
            }
        }
    });

    // Swipers
    new Swiper('.blog-slider', {
        slidesPerView: 1, spaceBetween: 16,
        pagination: { el: '.blog-slider .swiper-pagination', clickable: true },
        breakpoints: { 640: { slidesPerView: 2 }, 1024: { slidesPerView: 3 } }
    });
    new Swiper('.companies-slider', {
        slidesPerView: 1, spaceBetween: 12,
        pagination: { el: '.companies-slider .swiper-pagination', clickable: true },
        breakpoints: { 480: { slidesPerView: 2 }, 768: { slidesPerView: 3 }, 1024: { slidesPerView: 4 } }
    });

    // City dropdown click handling
    $(document).on('click', function(e) {
        $('.extra-cities-dropdown').removeClass('show');
    });
    $(document).on('click', '.show-more-cities', function(e) {
        e.stopPropagation();
        $(this).siblings('.extra-cities-dropdown').toggleClass('show');
    });
});
</script>