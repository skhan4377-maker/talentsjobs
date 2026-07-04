<style>
  .pagination li a { @apply px-4 py-2 rounded-md text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700; }
  .pagination .active span { @apply bg-gradient-to-br from-blue-500 to-purple-600 text-white; }
  .pagination .disabled span { @apply text-gray-400 dark:text-gray-600 cursor-not-allowed; }
</style>

<div class="container mx-auto">
     <!-- Header: Filter & Action -->
    <div class="flex flex-col gap-4 mb-4 text-sm">
    
      <!-- Filters Section -->
     <form method="get" class="grid grid-cols-1 md:grid-cols-2 gap-3 w-full">

			<!-- Status Filter -->
			<?php $current = $this->input->get('status') ?? 'all'; ?>
			<select name="status"
				class="w-full border border-gray-300 rounded-md py-2 px-3">

				<option value="all" <?= $current === 'all' ? 'selected' : '' ?>>All Status</option>
				<option value="draft" <?= $current === 'draft' ? 'selected' : '' ?>>Draft</option>
				<option value="active" <?= $current === 'active' ? 'selected' : '' ?>>Active</option>
				<option value="on-hold" <?= $current === 'on-hold' ? 'selected' : '' ?>>On Hold</option>
				<option value="under-review" <?= $current === 'under-review' ? 'selected' : '' ?>>Under Review</option>
				<option value="rejected" <?= $current === 'rejected' ? 'selected' : '' ?>>Rejected</option>
				<option value="suspended" <?= $current === 'suspended' ? 'selected' : '' ?>>Suspended</option>

			</select>

			<!-- Job Title Search -->
			<input type="text"
				   name="q"
				   value="<?= htmlspecialchars($this->input->get('q')) ?>"
				   placeholder="Search job title..."
				   class="border border-gray-300 rounded-md px-3 py-2 w-full">

			<!-- Buttons -->
			<div class="flex gap-2 col-span-full">

				<button type="submit"
					class="bg-blue-600 text-white px-4 py-2 rounded-md">
					Filter
				</button>

				<a href="<?= base_url('employer/jobs') ?>"
					class="px-4 py-2 bg-gray-200 rounded-md">
					Reset
				</a>

			</div>

		</form>
      <!-- Post Job Button (New Row) -->
      <div class="flex justify-start md:justify-end">
        <a href="<?= base_url('employer/jobs/create') ?>"
           class="inline-flex items-center whitespace-nowrap bg-indigo-600 hover:bg-indigo-700 text-white font-medium px-4 py-2 rounded-md shadow-sm transition">
          <i class="fas fa-plus mr-1 text-xs"></i> Post New Job
        </a>
      </div>
    </div>

    <!-- Job Listings Container -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm overflow-hidden">
    
      <!-- Header -->
      <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700 flex flex-col sm:flex-row justify-between items-start sm:items-center text-sm">
        <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100">
          <?= number_format($filtered_count) ?> Jobs
          <span class="text-xs text-gray-500 dark:text-gray-400 ml-2">
            (Filtered from <?= number_format($total_count) ?> total)
          </span>
        </h2>
      </div>
    
     <!-- Desktop Table -->
    <div class="hidden sm:block overflow-x-auto">
      <table class="min-w-full table-auto text-[15px] text-left text-gray-700 dark:text-gray-200">
        <thead class="bg-gray-50 dark:bg-gray-700 text-xs uppercase text-gray-600 dark:text-gray-300">
          <tr>
            <th class="px-4 py-3 whitespace-nowrap">Title</th>
            <th class="px-4 py-3 whitespace-nowrap">Posted</th>
            <th class="px-4 py-3 whitespace-nowrap">Applications</th>
            <th class="px-4 py-3 whitespace-nowrap">Views</th>
            <th class="px-4 py-3">Locations</th>
            <th class="px-4 py-3 whitespace-nowrap">Type</th>
            <th class="px-4 py-3 whitespace-nowrap">Salary</th>
            <th class="px-4 py-3 whitespace-nowrap">Days Left</th>
            <th class="px-4 py-3 whitespace-nowrap">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
          <?php if (empty($post_job_list)): ?>
            <tr>
             <td colspan="9" class="py-12">
                <div class="max-w-2xl mx-auto text-center">
            
                    <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-indigo-100 flex items-center justify-center">
                        <i class="fas fa-briefcase text-indigo-600 text-2xl"></i>
                    </div>
            
                    <h3 class="text-xl font-bold text-gray-800 mb-2">
                        Post Your First Job
                    </h3>
            
                    <p class="text-gray-600 text-sm mb-5 max-w-lg mx-auto">
                        Create a job posting to start receiving applications from candidates. Manage applications, shortlist talent and schedule interviews all from one place.
                    </p>
            
                    <div class="flex flex-wrap justify-center gap-2 mb-5">
            
                        <span class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-xs">
                            <i class="fas fa-briefcase mr-1"></i>
                            Post Job
                        </span>
            
                        <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs">
                            <i class="fas fa-users mr-1"></i>
                            Get Applications
                        </span>
            
                        <span class="bg-purple-100 text-purple-700 px-3 py-1 rounded-full text-xs">
                            <i class="fas fa-calendar-check mr-1"></i>
                            Schedule Interviews
                        </span>
            
                    </div>
            
                    <a href="<?= base_url('employer/jobs/create') ?>"
                       class="inline-flex items-center gap-2 px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-medium">
                        <i class="fas fa-plus-circle"></i>
                        Post New Job
                    </a>
            
                </div>
            </td>
            </tr>
          <?php else: ?>
            <?php foreach ($post_job_list as $job): 
              $deadlineTs = strtotime($job['deadline_date'] ?? '+0 days');
              $daysRemaining = max(0, ceil(($deadlineTs - time())/86400));
              $isExpired = $daysRemaining === 0;
              $rawStatus = $isExpired ? 'Expired' : ($job['status'] ?? 'Draft');
              $slug = strtolower(str_replace(' ', '-', $rawStatus));
              $statusMap = [
                'draft' => ['label'=>'Draft','bg'=>'bg-gray-100','text'=>'text-gray-800'],
                'active' => ['label'=>'Active','bg'=>'bg-green-100','text'=>'text-green-800'],
                'on-hold' => ['label'=>'On Hold','bg'=>'bg-yellow-100','text'=>'text-yellow-800'],
                'under-review'=> ['label'=>'Under Review','bg'=>'bg-blue-100','text'=>'text-blue-800'],
                'rejected' => ['label'=>'Rejected','bg'=>'bg-red-100','text'=>'text-red-800'],
                'suspended' => ['label'=>'Suspended','bg'=>'bg-purple-100','text'=>'text-purple-800'],
                'expired' => ['label'=>'Expired','bg'=>'bg-red-200','text'=>'text-red-900']
              ];
              $st = $statusMap[$slug] ?? $statusMap['draft'];
            ?>
            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition">
              <td class="px-4 py-3">
                <div class="flex items-center gap-2">
                  <span class="truncate font-semibold"><?= htmlspecialchars($job['job_title']) ?></span>
                  <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] border <?= $st['bg'] ?> <?= $st['text'] ?> shrink-0">
                    <span class="h-1.5 w-1.5 rounded-full mr-1 bg-current"></span><?= $st['label'] ?>
                  </span>
                </div>
              </td>
              <td class="px-4 py-3 whitespace-nowrap"><?= timeAgo($job['created_at']) ?></td>
              <td class="px-4 py-3 whitespace-nowrap"><?= $job['applications_count'] ?? 0 ?></td>
              <td class="px-4 py-3 whitespace-nowrap"><?= number_format($job['view_count'] ?? 0) ?></td>
              <td class="px-4 py-3">
				<?php if(!empty($job['cities'])): 
					$totalCities = count($job['cities']);
					$firstCity = $job['cities'][0]['city_name'];
					$remainingCities = array_slice($job['cities'], 1);
					$tooltipText = implode(', ', array_map(fn($c) => $c['city_name'], $remainingCities));
				?>
					<span class="inline-block bg-indigo-100 dark:bg-gray-700 text-indigo-800 dark:text-indigo-300 px-2 py-0.5 rounded-full text-[11px] mb-1">
						<?= htmlspecialchars($firstCity) ?>
					</span>
					<?php if($totalCities > 1): ?>
						<span class="inline-block bg-gray-200 dark:bg-gray-800 text-gray-800 dark:text-gray-200 px-2 py-0.5 rounded-full text-[11px] mb-1 cursor-pointer" 
							  title="<?= htmlspecialchars($tooltipText) ?>">
							+<?= $totalCities - 1 ?> more
						</span>
					<?php endif; ?>
				<?php else: ?>
					<span class="text-gray-400 text-xs">—</span>
				<?php endif; ?>
			</td>
              <td class="px-4 py-3 whitespace-nowrap"><?= htmlspecialchars(ucfirst($job['job_type'])) ?></td>
              <td class="px-4 py-3 whitespace-nowrap">₹<?= number_format($job['min_salary']) ?> - ₹<?= number_format($job['max_salary']) ?></td>
              <td class="px-4 py-3 whitespace-nowrap"><?= $daysRemaining ?> days</td>
              <td class="px-4 py-3 whitespace-nowrap">
                <div x-data="{ open: false }" class="relative">
                  <button @click="open = !open" @click.away="open = false"
                          class="inline-flex items-center px-2 py-1 text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-md border border-gray-300 dark:border-gray-600 text-sm">
                    Actions <i class="fas fa-caret-down ml-1"></i>
                  </button>
                  <div x-show="open" x-transition
                       class="absolute right-0 mt-2 w-40 z-20 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-md shadow-lg text-sm">
                    <a href="<?= base_url('employer/jobs/edit/'.$job['job_id']) ?>"
                       class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-200">
                      <i class="fas fa-pencil-alt mr-1 text-xs"></i> Edit Job
                    </a>
                    <button onclick="showStatusModal('<?= $job['job_id'] ?>','<?= $slug ?>')"
                            class="w-full text-left px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-200">
                      <i class="fas fa-sync-alt mr-1 text-xs"></i> Update Status
                    </button>
                    <a href="<?= base_url('employer/jobs/applications/'.$job['job_id']) ?>"
                       class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-200">
                      <i class="fas fa-users mr-1 text-xs"></i> View Applicants
                    </a>
                  </div>
                </div>
              </td>
            </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>

        <!-- Mobile Cards -->
    <div class="sm:hidden px-4 py-3 space-y-4 overflow-y-auto max-h-[75vh] pb-[5rem]">
      <?php if (empty($post_job_list)): ?>
        <div class="text-center py-10 text-sm text-gray-500 dark:text-gray-400">
          <i class="fas fa-file-search text-indigo-500 text-4xl mb-2 block animate-pulse"></i>
          No Active Job Postings
        </div>
      <?php else: ?>
        <?php foreach ($post_job_list as $job): 
          $deadlineTs = strtotime($job['deadline_date'] ?? '+0 days');
          $daysRemaining = max(0, ceil(($deadlineTs - time())/86400));
          $isExpired = $daysRemaining === 0;
          $rawStatus = $isExpired ? 'Expired' : ($job['status'] ?? 'Draft');
          $slug = strtolower(str_replace(' ', '-', $rawStatus));
          $st = $statusMap[$slug] ?? $statusMap['draft'];
        ?>
        <div class="bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg p-4 shadow-sm mb-6">
          
          <!-- Job Title -->
          <div class="flex items-center gap-2 mb-2">
            <span class="text-base font-semibold truncate"><?= htmlspecialchars($job['job_title']) ?></span>
            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] border <?= $st['bg'] ?> <?= $st['text'] ?> shrink-0">
              <span class="h-1.5 w-1.5 rounded-full mr-1 bg-current"></span><?= $st['label'] ?>
            </span>
          </div>
    
          <!-- Grid Info -->
          <div class="grid grid-cols-2 gap-2 text-sm text-gray-700 dark:text-gray-200 mb-2">
            <div><strong>Posted:</strong> <?= date('M d, Y', strtotime($job['created_at'])) ?></div>
            <div><strong>Applications:</strong> <?= $job['applications_count'] ?? 0 ?></div>
            <div><strong>Views:</strong> <?= number_format($job['view_count'] ?? 0) ?></div>
            <div><strong>Type:</strong> <?= htmlspecialchars(ucfirst($job['job_type'])) ?></div>
            <div><strong>Days Left:</strong> <?= $daysRemaining ?> days</div>
            <div class="col-span-2"><strong>Salary:</strong> ₹<?= number_format($job['min_salary']) ?> - ₹<?= number_format($job['max_salary']) ?></div>
            <div class="col-span-2">
              <strong>Locations:</strong>
              <?php if(!empty($job['cities'])): foreach($job['cities'] as $c): ?>
                <span class="inline-block bg-indigo-100 text-indigo-800 px-2 py-0.5 rounded-full text-[11px] mb-1 mr-1">
                  <?= htmlspecialchars($c['city_name']) ?>
                </span>
              <?php endforeach; else: ?>
                <span class="text-gray-400 text-xs">—</span>
              <?php endif; ?>
            </div>
          </div>
    
          <!-- Actions -->
          <div class="grid grid-cols-3 gap-2 text-xs text-gray-700 dark:text-gray-200 mt-3">
            <a href="<?= base_url('employer/jobs/edit/'.$job['job_id']) ?>"
               class="flex items-center justify-center gap-1 px-2 py-1 bg-gray-100 dark:bg-gray-600 rounded hover:bg-gray-200 dark:hover:bg-gray-500">
              <i class="fas fa-pencil-alt text-xs"></i> Edit
            </a>
            <a href="<?= base_url('employer/jobs/applications/'.$job['job_id']) ?>"
               class="flex items-center justify-center gap-1 px-2 py-1 bg-gray-100 dark:bg-gray-600 rounded hover:bg-gray-200 dark:hover:bg-gray-500">
              <i class="fas fa-users text-xs"></i> Applicants
            </a>
            <button onclick="showStatusModal('<?= $job['job_id'] ?>','<?= $slug ?>')"
               class="flex items-center justify-center gap-1 px-2 py-1 bg-gray-100 dark:bg-gray-600 rounded hover:bg-gray-200 dark:hover:bg-gray-500">
              <i class="fas fa-sync-alt text-xs"></i> Status
            </button>
          </div>
        </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>

     <?php if(!empty($links)): ?>
      <div class="sm:hidden px-4 py-[1.75rem] border-t border-gray-200 dark:border-gray-700 text-sm 
                  sticky bottom-0 bg-white dark:bg-gray-800 z-10">
        <nav class="flex justify-center"> <?= $links ?> </nav>
      </div>
    <?php endif; ?>

    </div>


</div>


<?php
	// Define status config keyed by lowercase slugs:
	$statusConfig = [
		'draft'    => ['label' => 'Draft',    'class' => 'bg-gray-400 text-white'],
		'active'   => ['label' => 'Active',   'class' => 'bg-green-500 text-white'],
		'on-hold'  => ['label' => 'On Hold',  'class' => 'bg-blue-400 text-white']
						
	];
?>
					
<!-- Status Modal -->
<div id="statusModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 backdrop-blur-sm hidden transition-opacity">
    <div class="bg-white dark:bg-gray-800 rounded-2xl w-full max-w-md p-6 transform transition-all scale-95 opacity-0
                mx-4 shadow-xl">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-xl font-bold text-gray-800 dark:text-gray-100">Update Job Status</h3>
            <button onclick="closeStatusModal()" class="text-gray-500 hover:text-gray-700 dark:text-gray-400">
                <i class="fas fa-times text-lg"></i>
            </button>
        </div>
        
        <select id="statusSelect" 
                class="w-full px-4 py-3 border-2 rounded-xl bg-white dark:bg-gray-700 dark:border-gray-600 
                       dark:text-gray-200 focus:ring-4 focus:ring-blue-100 dark:focus:ring-blue-900/50 
                       transition-all appearance-none cursor-pointer">
            <?php foreach($statusConfig as $key => $status): ?>
                <option value="<?= $key ?>"><?= $status['label'] ?></option>
            <?php endforeach; ?>
        </select>
        
        <div class="flex justify-end gap-3 mt-8">
            <button onclick="closeStatusModal()" 
                    class="px-6 py-2.5 text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-xl">
                Cancel
            </button>
            <button onclick="confirmStatusUpdate()" 
                    class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl shadow-md">
                Update Status
            </button>
        </div>
    </div>
</div>

<script>
 // CSRF Token Management – uses global functions from custom.js
function getCsrfData() {
    return {
        name: getCSRFName(),
        hash: getCSRFToken()
    };
}

function handleAjaxSuccess(response) {
    // Update CSRF token if provided
    if (response.csrf_token) {
        updateCSRFToken(response.csrf_token, response.csrf_name || getCSRFName());
    }
    
    if (response.success) {
        if (response.success_msg) {
            showToast('✅ ' + response.success_msg, 'success');
        }
        // Reload page on success
        setTimeout(() => {
            location.reload();
        }, 1000);
    } else {
        showToast('❌ ' + response.error_msg, 'error');
    }
}

// Status configuration
const statusConfig = <?= json_encode($statusConfig) ?>;

let currentJobId = null;

// Status filter change handler
document.getElementById('statusFilter').addEventListener('change', function () {
    const status = this.value;
    const url = new URL(window.location.href);
    url.searchParams.set('page', 1);
    url.searchParams.set('status', status);
    window.location.href = url.toString();
});

// Show status modal
function showStatusModal(jobId, currentStatus) {
    currentJobId = jobId;
    const select = document.getElementById('statusSelect');
    select.innerHTML = ''; // Clear first
    
    // Fill options
    for (const [key, cfg] of Object.entries(statusConfig)) {
        const opt = document.createElement('option');
        opt.value = key;
        opt.textContent = cfg.label;
        if (key === currentStatus) opt.selected = true;
        select.append(opt);
    }
    
    // Then show modal
    const modal = document.getElementById('statusModal');
    modal.classList.remove('hidden');
    setTimeout(() => {
        modal.classList.add('opacity-100');
        modal.querySelector('div').classList.replace('scale-95','scale-100');
        modal.querySelector('div').classList.replace('opacity-0','opacity-100');
    }, 10);
}

// Close status modal
function closeStatusModal() {
    const modal = document.getElementById('statusModal');
    modal.querySelector('div').classList.replace('scale-100','scale-95');
    modal.querySelector('div').classList.replace('opacity-100','opacity-0');
    modal.classList.remove('opacity-100');
    setTimeout(() => modal.classList.add('hidden'), 300);
    currentJobId = null;
}

// Confirm status update using global CSRF functions
function confirmStatusUpdate() {
    const status = document.getElementById('statusSelect').value;
    if (!currentJobId || !status) return closeStatusModal();

    const csrf = getCsrfData();
    const params = new URLSearchParams({ 
        id: currentJobId, 
        status,
        [csrf.name]: csrf.hash
    });

    fetch('<?= base_url("employer/jobs/update-status") ?>', {
        method: 'POST',
        headers: {'Content-Type':'application/x-www-form-urlencoded'},
        body: params.toString()
    })
    .then(r => r.json())
    .then(json => {
        handleAjaxSuccess(json);
    })
    .catch((error) => {
        console.error('Error:', error);
        showToast('❌ Network error occurred', 'error');
    })
    .finally(closeStatusModal);
}

// Modal close handlers
document.getElementById('statusModal').addEventListener('click', e => {
    if (e.target.id === 'statusModal') closeStatusModal();
});

// Toast notification function
function showToast(message, type = 'info') {
    const toast = document.createElement('div');
    toast.className = `fixed bottom-4 right-4 z-[9999] px-6 py-3 rounded-lg text-white shadow-lg transition-transform transform translate-x-0 ${
        type === 'success' ? 'bg-green-500' : 
        type === 'error' ? 'bg-red-500' : 'bg-blue-500'
    }`;
    toast.textContent = message;
    document.body.appendChild(toast);

    setTimeout(() => {
        toast.style.transform = 'translateX(150%)';
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}
</script>