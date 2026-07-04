<!-- Main Container -->
<div class="bg-white dark:bg-gray-900 rounded-xl shadow-sm p-4">

    <!-- Dashboard Stats Grid -->
    <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-4 gap-3 sm:gap-4 mb-6 sm:mb-8">
        <!-- Active Jobs -->
        <div class="bg-white p-4 sm:p-6 rounded-xl shadow-sm hover:shadow-md transition-shadow flex items-center">
            <div class="flex-grow">
                <p class="text-gray-500 text-xs sm:text-sm">Active Job Postings</p>
                <p class="text-xl sm:text-2xl font-bold text-gray-800 mt-1">
                    <?= number_format($dashboard_stats['active_jobs']) ?>
                </p>
            </div>
            <div class="p-2 sm:p-3 bg-green-100 rounded-lg">
                <i class="fas fa-briefcase text-green-600 text-lg sm:text-xl"></i>
            </div>
        </div>

        <!-- Total Applications -->
        <div class="bg-white p-4 sm:p-6 rounded-xl shadow-sm hover:shadow-md transition-shadow flex items-center">
            <div class="flex-grow">
                <p class="text-gray-500 text-xs sm:text-sm">Total Applications</p>
                <p class="text-xl sm:text-2xl font-bold text-gray-800 mt-1">
                    <?= number_format($dashboard_stats['total_applications']) ?>
                </p>
            </div>
            <div class="p-2 sm:p-3 bg-blue-100 rounded-lg">
                <i class="fas fa-file-alt text-blue-600 text-lg sm:text-xl"></i>
            </div>
        </div>

        <!-- New Applications -->
        <div class="bg-white p-4 sm:p-6 rounded-xl shadow-sm hover:shadow-md transition-shadow flex items-center">
            <div class="flex-grow">
                <p class="text-gray-500 text-xs sm:text-sm">New Applications</p>
                <p class="text-xl sm:text-2xl font-bold text-gray-800 mt-1">
                    <?= number_format($dashboard_stats['new_applications']) ?>
                </p>
                <?php if(!is_null($dashboard_stats['percentage_change'])): ?>
                    <span class="text-xs sm:text-sm <?= ($dashboard_stats['percentage_change'] >= 0) ? 'text-green-600' : 'text-red-600' ?>">
                        <?= ($dashboard_stats['percentage_change'] >= 0 ? '+' : '') ?><?= $dashboard_stats['percentage_change'] ?>%
                        from yesterday
                    </span>
                <?php else: ?>
                    <span class="text-xs sm:text-sm text-gray-500">No previous data</span>
                <?php endif; ?>
            </div>
            <div class="p-2 sm:p-3 bg-purple-100 rounded-lg">
                <i class="fas fa-users text-purple-600 text-lg sm:text-xl"></i>
            </div>
        </div>

        <!-- Interviews Scheduled -->
        <div class="bg-white p-4 sm:p-6 rounded-xl shadow-sm hover:shadow-md transition-shadow flex items-center">
            <div class="flex-grow">
                <p class="text-gray-500 text-xs sm:text-sm">Interviews Scheduled</p>
                <p class="text-xl sm:text-2xl font-bold text-gray-800 mt-1">
                    <?= number_format($dashboard_stats['interviews']) ?>
                </p>
            </div>
            <div class="p-2 sm:p-3 bg-orange-100 rounded-lg">
                <i class="fas fa-calendar-alt text-orange-600 text-lg sm:text-xl"></i>
            </div>
        </div>
    </div>

    <!-- Extra Stats Row (Plan & Notifications) -->
    <div class="grid grid-cols-2 sm:grid-cols-2 gap-3 sm:gap-4 mb-6 sm:mb-8">
        <?php if($extra_stats['plan']['has_plan']): ?>
            <!-- Active Plan Card -->
            <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
                <div class="flex items-center justify-between mb-2">
                    <i class="fas fa-crown text-yellow-500 text-lg"></i>
                    <span class="text-xs text-gray-500"><?= $extra_stats['plan']['days_left'] ?> days left</span>
                </div>
                <h3 class="font-semibold text-gray-800 text-sm"><?= htmlspecialchars($extra_stats['plan']['plan_name']) ?></h3>
                <div class="mt-2 text-xs text-gray-600 space-y-1">
                    <div>📄 Jobs: <?= $extra_stats['plan']['remaining_jobs'] ?> left</div>
                    <div>👀 CV Views: <?= $extra_stats['plan']['remaining_cv_views'] ?> left</div>
                    <div>🔍 Searches: <?= $extra_stats['plan']['remaining_searches'] ?> left</div>
                    <div>📦 Bulk: <?= $extra_stats['plan']['remaining_bulk'] ?> left</div>
                </div>
                <a href="#<?= site_url('employer/plans') ?>" class="mt-3 inline-block text-xs text-blue-600 hover:underline">Upgrade / Renew</a>
            </div>
        <?php else: ?>
            <!-- No Active Plan Card -->
            <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
                <div class="flex items-center justify-between mb-2">
                    <i class="fas fa-crown text-gray-400 text-lg"></i>
                    <span class="text-xs text-gray-500">Free Plan</span>
                </div>
                <h3 class="font-semibold text-gray-800 text-sm">No Active Subscription</h3>
                <p class="text-xs text-gray-500 mt-1">Upgrade to post more jobs and access premium features.</p>
                <a href="<?= site_url('employer/jobs/create') ?>" class="mt-3 inline-block text-xs text-blue-600 hover:underline">View Plans</a>
            </div>
        <?php endif; ?>

        <!-- Notifications Card -->
        <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
            <div class="flex items-center justify-between mb-2">
                <i class="fas fa-bell text-blue-500 text-lg"></i>
                <span class="text-xs text-gray-500">Unread</span>
            </div>
            <h3 class="font-semibold text-gray-800 text-2xl"><?= number_format($extra_stats['unread_notifications']) ?></h3>
            <p class="text-xs text-gray-500 mt-1">Notifications</p>
            <a href="#<?= site_url('employer/notifications') ?>" class="mt-3 inline-block text-xs text-blue-600 hover:underline">View all</a>
        </div>
    </div>

    <!-- Second Row of Stats (Shortlisted, Hired, Conversion Rate, Apps per Active Job) -->
    <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-4 gap-3 sm:gap-4 mb-6 sm:mb-8">
        <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
            <div class="flex items-center gap-2 mb-2">
                <i class="fas fa-user-check text-green-500"></i>
                <span class="text-xs text-gray-500">Shortlisted</span>
            </div>
            <h3 class="font-bold text-2xl text-gray-800"><?= number_format($extra_stats['shortlisted']) ?></h3>
        </div>
        <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
            <div class="flex items-center gap-2 mb-2">
                <i class="fas fa-user-graduate text-blue-500"></i>
                <span class="text-xs text-gray-500">Hired</span>
            </div>
            <h3 class="font-bold text-2xl text-gray-800"><?= number_format($extra_stats['hired']) ?></h3>
        </div>
        <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
            <div class="flex items-center gap-2 mb-2">
                <i class="fas fa-chart-line text-purple-500"></i>
                <span class="text-xs text-gray-500">Interview Conversion</span>
            </div>
            <h3 class="font-bold text-2xl text-gray-800"><?= $extra_stats['interview_conversion_rate'] ?>%</h3>
        </div>
        <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
            <div class="flex items-center gap-2 mb-2">
                <i class="fas fa-chart-simple text-orange-500"></i>
                <span class="text-xs text-gray-500">Apps per Active Job</span>
            </div>
            <h3 class="font-bold text-2xl text-gray-800"><?= $extra_stats['applications_per_job'] ?></h3>
        </div>
    </div>

    <!-- Quick Action Row -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
        <a href="<?= base_url('employer/jobs/create') ?>" class="bg-white p-4 rounded-xl shadow-sm hover:shadow-md transition-shadow text-center">
            <i class="fas fa-plus-circle text-blue-600 text-2xl mb-2"></i>
            <p class="text-sm font-medium">Post New Job</p>
        </a>
        <a href="<?= base_url('search/SearchCandidates') ?>" class="bg-white p-4 rounded-xl shadow-sm hover:shadow-md transition-shadow text-center">
            <i class="fas fa-search text-green-600 text-2xl mb-2"></i>
            <p class="text-sm font-medium">Candidate Search</p>
        </a>
        <a href="<?= base_url('employer/analytics') ?>" class="bg-white p-4 rounded-xl shadow-sm hover:shadow-md transition-shadow text-center">
            <i class="fas fa-chart-line text-purple-600 text-2xl mb-2"></i>
            <p class="text-sm font-medium">Analytics</p>
        </a>
        <a href="<?= base_url('settings') ?>" class="bg-white p-4 rounded-xl shadow-sm hover:shadow-md transition-shadow text-center">
            <i class="fas fa-cog text-orange-600 text-2xl mb-2"></i>
            <p class="text-sm font-medium">Settings</p>
        </a>
    </div>

    <!-- Recent Applications Table -->
    <div class="bg-white rounded-xl shadow-sm p-4 sm:p-6 mb-6 sm:mb-8">
        <div class="flex items-center justify-between mb-4 sm:mb-6">
            <h2 class="text-lg sm:text-xl font-semibold text-gray-800">Recent Applications</h2>
            <a href="<?= site_url('employer/applications') ?>" class="text-blue-600 hover:text-blue-700 text-sm flex items-center">
                View All <i class="fas fa-arrow-right ml-2"></i>
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full text-sm sm:text-base whitespace-nowrap">
                <thead>
                    <tr class="text-left text-gray-500 border-b">
                        <th class="pb-3 pr-4">Candidate</th>
                        <th class="pb-3 pr-4">Position</th>
                        <th class="pb-3 pr-4">Status</th>
                        <th class="pb-3 pr-4">Applied Date</th>
                        <th class="pb-3 pr-4">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(!empty($recent_applications)): ?>
                        <?php foreach($recent_applications as $application): ?>
                            <tr class="border-b hover:bg-gray-50">
                                <td class="py-4 pr-4">
									<a href="<?= site_url('employer/applications/view/'.$application['applied_id']) ?>" class="text-blue-600 hover:text-blue-700 font-medium">
										<?= htmlspecialchars($application['candidate_name']) ?>
									</a>
									<?php if (!empty($application['has_active_plan'])): ?>
										<span 
											class="inline-flex items-center justify-center w-5 h-5 rounded-full text-xs bg-gradient-to-br from-yellow-300 to-amber-500 text-white shadow-sm ml-1"
											title="Premium Subscriber"
										><i class="fas fa-crown"></i></span>
									<?php endif; ?>
								</td>
                                <td class="pr-4"><?= htmlspecialchars($application['job_title']) ?></td>
                                <?php $status = $application['ApplicationStage'] ?? 'Pending'; ?>
                                <td class="pr-4">
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium 
                                        <?= get_status_classes($status); ?>">
                                        <i class="<?= get_status_icon_class($status); ?> mr-1"></i>
                                        <?= htmlspecialchars($status, ENT_QUOTES, 'UTF-8') ?>
                                    </span>
                                </td>
                                <td class="pr-4"><?= date('M d, Y', strtotime($application['applied_date'])) ?></td>
                                <td class="pr-4 space-x-2">
                                    <a href="<?= site_url('employer/applications/view/'.$application['applied_id']) ?>" class="text-blue-600 hover:text-blue-700" title="View Application">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <?php 
                                      $can_schedule = in_array(
                                        $application['ApplicationStage'], 
                                        ['Shortlist','Under Review'], 
                                        true
                                      ); 
                                    ?>
                                    <?php if ($can_schedule): ?>
                                        <a href="<?= site_url('employer/interviews/schedule/'.$application['applied_id']) ?>" class="text-green-600 hover:text-green-700" title="Schedule Interview">
                                            <i class="fas fa-calendar-check"></i>
                                        </a>
                                    <?php else: ?>
                                        <span class="text-gray-400 cursor-not-allowed" title="Cannot schedule interview in ‘<?= htmlspecialchars($application['ApplicationStage']) ?>’ stage">
                                            <i class="fas fa-calendar-check"></i>
                                        </span>
                                    <?php endif; ?>
                                    <?php if($status === 'Rejected'): ?>
                                        <button onclick="updateStatus(<?= $application['applied_id'] ?>, <?= $application['job_id'] ?>, 'Under Review')" class="text-purple-600 hover:text-purple-700" title="Reconsider Application">
                                            <i class="fas fa-undo"></i>
                                        </button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="py-4 text-center text-gray-500">No recent applications found</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Company Profile Completeness -->
    <div class="bg-gradient-to-r from-blue-500 to-purple-600 text-white rounded-xl p-4 sm:p-6 mb-6 sm:mb-8 shadow-lg text-sm sm:text-base">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex-1">
                <div class="flex items-center mb-2">
                    <i class="fas fa-building mr-2 text-base sm:text-lg"></i>
                    <span class="font-semibold">Profile Completeness: <?= $profile_completeness['percentage'] ?>%</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2 mb-2">
                    <div class="bg-yellow-400 h-2 rounded-full" style="width: <?= $profile_completeness['percentage'] ?>%"></div>
                </div>
                <p class="opacity-90 leading-snug">
                    <?php if($profile_completeness['percentage'] < 100): ?>
                        Complete your profile to attract better candidates. Missing:
                        <?= implode(', ', array_slice($profile_completeness['missing_fields'], 0, 3)) ?>
                        <?= count($profile_completeness['missing_fields']) > 3 ? '...' : '' ?>
                    <?php else: ?>
                        Your profile is complete! 🎉
                    <?php endif; ?>
                </p>
            </div>
            <div class="shrink-0">
                <a href="<?= site_url('employer/profile') ?>" class="px-5 py-2 bg-white text-blue-600 rounded-full hover:bg-opacity-90 transition-all text-sm font-medium">
                    <?= $profile_completeness['percentage'] == 100 ? 'Update Profile' : 'Complete Profile' ?>
                </a>
            </div>
        </div>
    </div>

    <!-- Active Job Postings -->
    <div class="bg-white rounded-xl shadow-sm p-4 sm:p-6 mb-6 sm:mb-8">
        <div class="flex items-center justify-between mb-4 sm:mb-6">
            <h2 class="text-lg sm:text-xl font-semibold text-gray-800">Active Job Postings</h2>
            <a href="<?= site_url('employer/jobs') ?>" class="text-blue-600 hover:text-blue-700 text-sm flex items-center">
                Manage Jobs <i class="fas fa-arrow-right ml-2"></i>
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <?php if (!empty($active_jobs)): ?>
                <?php foreach ($active_jobs as $job): ?>
                    <?php
                        $status_config = [
                            1 => ['label' => 'Active', 'bg' => 'bg-green-100', 'text' => 'text-green-800'],
                            2 => ['label' => 'Pending', 'bg' => 'bg-blue-100', 'text' => 'text-blue-800'],
                            3 => ['label' => 'Closed', 'bg' => 'bg-gray-100', 'text' => 'text-gray-800']
                        ];
                        $status = $status_config[$job['status']] ?? ['label' => 'Draft', 'bg' => 'bg-gray-100', 'text' => 'text-gray-800'];
                    ?>
                    <div class="border rounded-xl p-4 sm:p-6 hover:shadow-md transition-shadow flex flex-col justify-between">
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <h3 class="font-semibold text-base sm:text-lg text-gray-900">
                                    <?= htmlspecialchars(ucfirst($job['job_title'])) ?>
                                </h3>
                                <div class="flex items-center text-sm text-gray-500 mt-2 flex-wrap gap-2">
                                    <span class="flex items-center">
                                        <i class="fas fa-map-marker-alt mr-1"></i>
                                        <?= htmlspecialchars($job['city_name'] ?? 'Remote') ?>
                                    </span>
                                    <span class="flex items-center">
                                        <i class="fas fa-clock mr-1"></i>
                                        <?= timeAgo(strtotime($job['created_at'])) ?>
                                    </span>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-xl font-bold text-blue-600">
                                    <?= number_format($job['applications']) ?>
                                </p>
                                <p class="text-sm text-gray-500">Applications</p>
                            </div>
                        </div>
                        <div class="flex items-center justify-between mt-2">
                            <span class="px-3 py-1 rounded-full text-sm font-medium <?= $status['bg'] ?> <?= $status['text'] ?>">
                                <?= $status['label'] ?>
                            </span>
                            <div class="flex gap-2">
                                <a href="<?= site_url('employer/jobs/view/'.$job['job_id']) ?>" class="px-4 py-2 bg-blue-100 text-blue-600 rounded-lg text-sm hover:bg-blue-200 transition">View</a>
                                <a href="<?= site_url('employer/jobs/edit/'.$job['job_id']) ?>" class="px-4 py-2 bg-gray-100 text-gray-600 rounded-lg text-sm hover:bg-gray-200 transition">Edit</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-span-full text-center py-4 text-gray-500 text-sm">
                    No active job postings found.
                    <a href="<?= site_url('employer/jobs/create') ?>" class="text-blue-600 hover:underline">Post a new job</a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Hiring Analytics Chart -->
    <div class="bg-white rounded-xl shadow-sm p-6 mb-8">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-semibold">Hiring Analytics</h2>
            <div class="flex space-x-2">
                <button onclick="showChart('line')" class="px-3 py-1 bg-blue-100 text-blue-600 rounded-lg">Applications</button>
                <button onclick="showChart('doughnut')" class="px-3 py-1 bg-purple-100 text-purple-600 rounded-lg">Status</button>
            </div>
        </div>
        <div class="h-64">
            <canvas id="analyticsChart"></canvas>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    window.baseUrl = '<?= base_url() ?>';

    const analyticsData = {
        applications: <?= json_encode($hiring_analytics['applications']) ?>,
        statusDistribution: <?= json_encode($hiring_analytics['status_distribution']) ?>
    };

    let myChart = null;

    function showChart(type) {
        if (myChart) myChart.destroy();
        const ctx = document.getElementById('analyticsChart').getContext('2d');

        if (type === 'line') {
            const labels = analyticsData.applications.map(item => item.date);
            const data = analyticsData.applications.map(item => item.count);

            myChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels,
                    datasets: [{
                        label: 'Applications',
                        data,
                        borderColor: '#3B82F6',
                        tension: 0.4,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false
                }
            });
        } else if (type === 'doughnut') {
            const labels = analyticsData.statusDistribution.map(item => item.ApplicationStage);
            const data = analyticsData.statusDistribution.map(item => item.count);
            const colors = ['#3B82F6', '#10B981', '#EF4444', '#F59E0B', '#8B5CF6'];

            myChart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels,
                    datasets: [{
                        data,
                        backgroundColor: colors,
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'right' }
                    }
                }
            });
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        showChart('line');
    });

    function updateStatus(applied_id, job_id, status) {
        let actionText = {
            'Rejected': 'reject this application',
            'Shortlist': 'shortlist this candidate'
        }[status] || `mark as "${status}"`;

        if (!confirm(`Are you sure you want to ${actionText}?`)) return;

        const buttons = document.querySelectorAll('button');
        buttons.forEach(btn => btn.disabled = true);

        const form = new URLSearchParams();
        form.append('applied_id', applied_id);
        form.append('job_id', job_id);
        form.append('status', status);

        showToast('Processing your request...', 'info');

        fetch(`${baseUrl}employer/update_application_status`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: form.toString()
        })
        .then(async res => {
            const data = await res.json();
            if (!res.ok) throw new Error(data.message || 'Request failed');
            return data;
        })
        .then(data => {
            showToast(data.message, 'success');
            setTimeout(() => window.location.reload(), 1500);
        })
        .catch(err => {
            showToast(err.message || 'Update failed', 'error');
            buttons.forEach(btn => btn.disabled = false);
        });
    }

    function showToast(message, type = 'info') {
        const toast = document.createElement('div');
        toast.className = `fixed bottom-4 right-4 z-50 px-6 py-3 rounded-lg text-white shadow-lg animate-slide-in ${
            type === 'success' ? 'bg-green-500' :
            type === 'error' ? 'bg-red-500' : 'bg-blue-500'
        }`;
        toast.innerHTML = `<div class="flex items-center"><i class="fas ${type === 'success' ? 'fa-check-circle' : type === 'error' ? 'fa-exclamation-triangle' : 'fa-info-circle'} mr-2"></i><span>${message}</span></div>`;
        document.body.appendChild(toast);
        setTimeout(() => {
            toast.classList.add('animate-fade-out');
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }
</script>