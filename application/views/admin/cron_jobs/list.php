<div class="bg-white rounded-lg shadow-sm">
    <?php $dateRange = $this->input->get('date_range'); ?>

    <!-- Header -->
    <div class="px-4 sm:px-6 py-4 border-b border-gray-200">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center space-y-3 sm:space-y-0">
            <div>
                <h1 class="text-xl sm:text-2xl font-bold text-gray-800">Automation Jobs</h1>
                <p class="text-sm sm:text-base text-gray-600 mt-1">Manage automated batch jobs and schedules</p>
            </div>
            <a href="<?= base_url('admin/cron/Manage_cron/create') ?>" 
               class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-all flex items-center text-sm sm:text-base">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                New Job
            </a>
        </div>
    </div>

    <!-- Date Range Filter -->
    <div class="px-4 sm:px-6 py-4 border-b bg-gray-50">
        <form method="get" class="flex flex-wrap items-end gap-3">
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Date Range</label>
                <input type="text" id="dateRange" name="date_range"
                       value="<?= htmlspecialchars($dateRange ?? '') ?>"
                       placeholder="Select date range" readonly
                       class="border border-gray-300 rounded-md px-3 py-2 text-sm w-64 bg-white cursor-pointer focus:ring-2 focus:ring-blue-500">
            </div>
            <button class="bg-blue-600 text-white px-4 py-2 rounded-md text-sm hover:bg-blue-700">Apply</button>
            <?php if ($dateRange): ?>
                <a href="<?= base_url('admin/cron/Manage_cron') ?>" class="text-sm text-gray-600 underline">Reset</a>
            <?php endif; ?>
        </form>
        <?php if (!empty($range_display)): ?>
            <div class="mt-2 text-xs text-gray-600 flex items-center">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                Showing data for: <span class="font-medium ml-1"><?= htmlspecialchars($range_display) ?></span>
            </div>
        <?php endif; ?>
    </div>

    <!-- Stats Cards (Generic) -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 sm:gap-6 p-4 sm:p-6 bg-gray-50 border-b">
        <?php
        $statCards = [
            ['color' => 'blue', 'icon' => 'M13 10V3L4 14h7v7l9-11h-7z', 'label' => 'Total Jobs', 'value' => $stats['total_jobs']],
            ['color' => 'green', 'icon' => 'M5 13l4 4L19 7', 'label' => 'Active Jobs', 'value' => $stats['active_jobs']],
            ['color' => 'purple', 'icon' => 'M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z', 'label' => ($dateRange ? 'Items in Range' : 'Total Items'), 'value' => number_format($stats['total_processed'])],
            ['color' => 'orange', 'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z', 'label' => ($dateRange ? 'Successful Items' : "Today's Items"), 'value' => number_format($stats['today_processed'])],
        ];
        foreach ($statCards as $card): ?>
            <div class="bg-white rounded-lg p-3 sm:p-4 shadow border">
                <div class="flex items-center">
                    <div class="p-2 sm:p-3 rounded-full bg-<?= $card['color'] ?>-100 text-<?= $card['color'] ?>-600">
                        <svg class="w-4 h-4 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="<?= $card['icon'] ?>"></path>
                        </svg>
                    </div>
                    <div class="ml-2 sm:ml-4">
                        <p class="text-xs sm:text-sm font-medium text-gray-600"><?= $card['label'] ?></p>
                        <p class="text-lg sm:text-2xl font-semibold text-gray-900"><?= $card['value'] ?></p>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Jobs Table -->
    <div class="p-4 sm:p-6 pb-20 sm:pb-6">
        <?php if (empty($cron_jobs)): ?>
            <div class="text-center py-12">
                <svg class="w-16 h-16 text-gray-400 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <h3 class="mt-4 text-lg font-medium text-gray-900">No jobs found</h3>
                <p class="mt-1 text-sm text-gray-500">Get started by creating a new automation job.</p>
                <div class="mt-6">
                    <a href="<?= base_url('admin/cron/Manage_cron/create') ?>" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                        Create New Job
                    </a>
                </div>
            </div>
        <?php else: ?>
            <div class="overflow-x-auto border border-gray-200 rounded-lg">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap min-w-[180px]">Job Details</th>
                            <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap min-w-[130px]">Schedule</th>
                            <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap min-w-[130px]">Performance</th>
                            <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap min-w-[200px]">Last Run Message</th>
                            <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap min-w-[120px]">Status</th>
                            <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap min-w-[120px]">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($cron_jobs as $job): ?>
                        <tr class="hover:bg-gray-50 transition-colors">
                            <!-- Job Details -->
                            <td class="px-3 py-3">
                                <div class="text-sm font-medium text-gray-900 truncate max-w-[150px] sm:max-w-none"><?= htmlspecialchars($job->name ?? 'Untitled') ?></div>
                                <div class="text-xs text-gray-500 truncate max-w-[150px] sm:max-w-none"><?= htmlspecialchars($job->description ?? 'No description') ?></div>
                                <div class="text-xs text-gray-400 mt-1">Context: <span class="font-medium"><?= htmlspecialchars($job->context ?? 'N/A') ?></span></div>
                                <div class="text-xs text-gray-400">Model: <span class="font-medium"><?= htmlspecialchars($job->cron_model ?? 'N/A') ?></span></div>
                            </td>

                            <!-- Schedule -->
                            <td class="px-3 py-3 whitespace-nowrap">
                                <div class="text-sm text-gray-900 capitalize"><?= htmlspecialchars($job->schedule_type ?? 'daily') ?></div>
                                <?php if ($job->schedule_type == 'custom' && !empty($job->custom_schedule)): ?>
                                    <div class="text-xs text-gray-500"><?= htmlspecialchars($job->custom_schedule) ?></div>
                                <?php endif; ?>
                                <div class="text-xs text-gray-500">Limit: <?= number_format($job->emails_per_run ?? 0) ?>/run</div>
                                <?php if (!empty($job->start_time) && !empty($job->end_time)): ?>
                                    <div class="text-xs text-gray-500">
                                        Time: <?= date('g:i A', strtotime($job->start_time)) ?> - <?= date('g:i A', strtotime($job->end_time)) ?>
                                    </div>
                                <?php endif; ?>
                            </td>

                            <!-- Performance (Processed / Failed) -->
                            <td class="px-3 py-3 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    <?php if ($dateRange && isset($job->total_processed_in_range)): ?>
                                        Processed: <?= number_format($job->total_processed_in_range) ?>
                                    <?php else: ?>
                                        Processed: <?= number_format($job->total_processed_overall ?? 0) ?>
                                    <?php endif; ?>
                                </div>
                                <div class="text-xs text-gray-500">
                                    Executions: <?= count($job->recent_executions ?? []) ?>
                                </div>
                                <?php if (empty($dateRange) && !empty($job->today_stats)): ?>
                                    <div class="text-xs text-gray-500">Today: <?= number_format($job->today_stats->processed_today ?? 0) ?> items</div>
                                <?php endif; ?>
                                <div class="text-xs text-gray-500">
                                    Last: <?= !empty($job->last_run) ? date('M j, g:i A', strtotime($job->last_run)) : 'Never' ?>
                                </div>
                            </td>

							<td class="px-3 py-3 whitespace-normal">
								<?php
									$message = $job->last_message ?? '';

									// Date aur message ko split karo
									if (strpos($message, ' – ') !== false) {
										list($run_time, $status_msg) = explode(' – ', $message, 2);
									} else {
										$run_time = '';
										$status_msg = $message;
									}

									$colorClass =
										strpos($status_msg, '✅') !== false ? 'text-green-700' :
										(strpos($status_msg, '❌') !== false ? 'text-red-700' :
										(strpos($status_msg, '⚠️') !== false ? 'text-yellow-700' : 'text-gray-600'));
								?>

								<div class="<?= $colorClass ?> font-medium text-xs"
									 title="<?= htmlspecialchars($job->last_message_full ?? $message) ?>">
									<?= htmlspecialchars($status_msg) ?>
								</div>

								<?php if (!empty($run_time)): ?>
									<div class="text-[11px] text-gray-500 mt-1">
										<?= htmlspecialchars($run_time) ?>
									</div>
								<?php endif; ?>
							</td>

                            <!-- Status -->
                            <td class="px-3 py-3 whitespace-nowrap">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium <?= ($job->is_active ?? 0) ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' ?>">
                                    <?= ($job->is_active ?? 0) ? 'Active' : 'Inactive' ?>
                                </span>
                                <?php if ($job->is_running ?? 0): ?>
                                    <span class="ml-1 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">Running</span>
                                    <div class="text-xs text-gray-500 mt-1">Since: <?= !empty($job->running_since) ? date('g:i A', strtotime($job->running_since)) : 'N/A' ?></div>
                                <?php endif; ?>
                            </td>

                            <!-- Actions -->
                            <td class="px-3 py-3 whitespace-nowrap">
                                <div class="flex flex-wrap gap-1">
                                    <a href="<?= base_url('admin/cron/Manage_cron/edit/' . $job->id) ?>" class="text-blue-600 hover:text-blue-900 p-1.5 rounded hover:bg-blue-50" title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </a>

                                    <!-- Toggle Status -->
                                    <a href="<?= base_url('admin/cron/Manage_cron/toggle_status/' . $job->id) ?>" class="text-gray-600 hover:text-gray-900 p-1.5 rounded hover:bg-gray-100" title="<?= ($job->is_active ?? 0) ? 'Deactivate' : 'Activate' ?>" onclick="return confirm('<?= ($job->is_active ?? 0) ? 'Deactivate' : 'Activate' ?> this job?')">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <?php if ($job->is_active ?? 0): ?>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path>
                                            <?php else: ?>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                            <?php endif; ?>
                                        </svg>
                                    </a>

                                    <!-- Delete -->
                                    <a href="<?= base_url('admin/cron/Manage_cron/delete/' . $job->id) ?>" class="text-red-600 hover:text-red-900 p-1.5 rounded hover:bg-red-50" title="Delete" onclick="return confirm('Are you sure you want to delete this cron job?')">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    flatpickr("#dateRange", {
        mode: "range",
        dateFormat: "Y-m-d",
        altInput: true,
        altFormat: "F j, Y",
        maxDate: "today",
        defaultDate: <?= json_encode($dateRange ?? null) ?>,
        locale: { firstDayOfWeek: 1 }
    });

    document.querySelectorAll('.logs-menu-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.stopPropagation();
            const dropdown = this.nextElementSibling;
            document.querySelectorAll('.logs-dropdown').forEach(d => d.classList.add('hidden'));
            dropdown.classList.toggle('hidden');
        });
    });

    document.addEventListener('click', function(e) {
        if (!e.target.closest('.logs-menu-btn')) {
            document.querySelectorAll('.logs-dropdown').forEach(d => d.classList.add('hidden'));
        }
    });

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            document.querySelectorAll('.logs-dropdown').forEach(d => d.classList.add('hidden'));
        }
    });
});
</script>