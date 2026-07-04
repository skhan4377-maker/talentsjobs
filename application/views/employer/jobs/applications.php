<?php
// Updated status transitions
$statusTransitions = [
    'Applied' => [
        'Viewed'     => 'Mark as Viewed',
        'Under Review' => 'Mark as Under Review',
        'Shortlist'    => 'Shortlist Candidate',
        'Rejected'     => 'Reject Application'
    ],
    'Viewed' => [
        'Under Review' => 'Mark as Under Review',
        'Shortlist'    => 'Shortlist Candidate',
        'Rejected'     => 'Reject Application'
    ],
    'Under Review' => [
        'Shortlist'           => 'Shortlist Candidate',
        'Interview Scheduled' => 'Schedule Interview',
        'Rejected'            => 'Reject Application'
    ],
    'Shortlist' => [
        'Interview Scheduled' => 'Schedule Interview',
        'Rejected'            => 'Reject Application'
    ],
    'Interview Scheduled' => [
        'Scheduled'   => 'Confirm Interview Schedule',
        'Rescheduled' => 'Reschedule Interview',
        'Rejected'    => 'Reject Application'
    ],
    'Scheduled' => [
        'Completed' => 'Mark as Completed',
        'Rejected'  => 'Reject Application'
    ],
    'Rescheduled' => [
        'Scheduled' => 'Confirm Reschedule',
        'Rejected'  => 'Reject Application'
    ],
    'Offer Extended' => [
        'Hired'    => 'Mark as Hired',
        'Rejected' => 'Reject Application'
    ],
    'Hired' => [],
    'Withdraw' => [],
    'Completed' => [],
    'Canceled' => []
];
?>

<div class="container mx-auto">
  <!-- Header -->
  <div class="flex flex-col md:flex-row items-start md:items-center justify-between mb-6 text-sm">
    <div class="mb-3 md:mb-0">
      <p class="mt-1 text-gray-600 dark:text-gray-400 text-sm">
        <?= count($applications) ?> candidates found • <span class="text-purple-600 dark:text-purple-400">Manage all job applications</span>
      </p>
    </div>
    <div class="flex items-center space-x-3">
      <a href="<?= base_url('employer/jobs') ?>" class="inline-flex items-center gap-2 px-4 py-2.5 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm hover:shadow transition text-sm font-medium">
        <i class="fas fa-arrow-left text-gray-600 dark:text-gray-400"></i>
        <span class="text-gray-800 dark:text-gray-200">Back to Jobs</span>
      </a>
      <button class="inline-flex items-center gap-2 px-4 py-2.5 bg-purple-600 hover:bg-purple-700 text-white rounded-lg shadow-sm transition text-sm font-medium">
        <i class="fas fa-filter"></i>
        <span>Filter</span>
      </button>
    </div>
  </div>

  <?php if(empty($applications)): ?>
    <div class="text-center py-16 md:py-20 text-sm bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 mt-4">
      <div class="inline-flex items-center justify-center w-24 h-24 bg-gradient-to-br from-blue-50 to-purple-50 dark:from-blue-900/20 dark:to-purple-900/20 rounded-full mb-4">
        <i class="fas fa-file-alt text-4xl text-blue-600 dark:text-blue-400"></i>
      </div>
      <h3 class="text-xl font-semibold text-gray-700 dark:text-gray-300 mb-2">No applications received</h3>
      <p class="text-gray-500 dark:text-gray-400 max-w-md mx-auto mb-6">
        You haven't received any applications yet. Share your job posting to attract candidates.
      </p>
      <a href="<?= base_url('employer/jobs/create') ?>" class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white rounded-lg shadow-sm transition text-sm font-medium">
        <i class="fas fa-plus"></i>
        <span>Create New Job</span>
      </a>
    </div>
  <?php else: ?>
  
  <!-- Stats Cards -->
  <div class="grid grid-cols-2 md:grid-cols-4 gap-3 md:gap-4 mb-6">
    <div class="bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/30 dark:to-blue-800/30 p-4 rounded-xl border border-blue-200 dark:border-blue-800">
      <div class="flex items-center justify-between">
        <div>
          <p class="text-xs font-medium text-blue-700 dark:text-blue-300">Total</p>
          <p class="text-2xl font-bold text-blue-900 dark:text-blue-100"><?= count($applications) ?></p>
        </div>
        <i class="fas fa-users text-blue-500 dark:text-blue-400 text-xl"></i>
      </div>
    </div>
    <div class="bg-gradient-to-br from-purple-50 to-purple-100 dark:from-purple-900/30 dark:to-purple-800/30 p-4 rounded-xl border border-purple-200 dark:border-purple-800">
      <div class="flex items-center justify-between">
        <div>
          <p class="text-xs font-medium text-purple-700 dark:text-purple-300">Shortlisted</p>
          <p class="text-2xl font-bold text-purple-900 dark:text-purple-100">
            <?= count(array_filter($applications, fn($app) => ($app['ApplicationStage'] ?? '') === 'Shortlist')) ?>
          </p>
        </div>
        <i class="fas fa-star text-purple-500 dark:text-purple-400 text-xl"></i>
      </div>
    </div>
    <div class="bg-gradient-to-br from-green-50 to-green-100 dark:from-green-900/30 dark:to-green-800/30 p-4 rounded-xl border border-green-200 dark:border-green-800">
      <div class="flex items-center justify-between">
        <div>
          <p class="text-xs font-medium text-green-700 dark:text-green-300">Interview</p>
          <p class="text-2xl font-bold text-green-900 dark:text-green-100">
            <?= count(array_filter($applications, fn($app) => in_array($app['ApplicationStage'] ?? '', ['Interview Scheduled', 'Scheduled', 'Rescheduled']))) ?>
          </p>
        </div>
        <i class="fas fa-calendar-check text-green-500 dark:text-green-400 text-xl"></i>
      </div>
    </div>
    <div class="bg-gradient-to-br from-red-50 to-red-100 dark:from-red-900/30 dark:to-red-800/30 p-4 rounded-xl border border-red-200 dark:border-red-800">
      <div class="flex items-center justify-between">
        <div>
          <p class="text-xs font-medium text-red-700 dark:text-red-300">Rejected</p>
          <p class="text-2xl font-bold text-red-900 dark:text-red-100">
            <?= count(array_filter($applications, fn($app) => ($app['ApplicationStage'] ?? '') === 'Rejected')) ?>
          </p>
        </div>
        <i class="fas fa-times-circle text-red-500 dark:text-red-400 text-xl"></i>
      </div>
    </div>
  </div>

  <!-- Desktop Table -->
  <div class="hidden md:block overflow-x-auto overflow-y-visible rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800">


    <table class="w-full text-sm border-collapse">
      <thead class="bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-800 dark:to-gray-900 text-gray-700 dark:text-gray-200 text-xs uppercase">
        <tr>
          <th class="px-6 py-4 text-left font-semibold text-gray-600 dark:text-gray-300">Candidate</th>
          <th class="px-6 py-4 text-left font-semibold text-gray-600 dark:text-gray-300">Location</th>
          <th class="px-6 py-4 text-left font-semibold text-gray-600 dark:text-gray-300">Status</th>
          <th class="px-6 py-4 text-left font-semibold text-gray-600 dark:text-gray-300">Applied</th>
          <th class="px-6 py-4 text-left font-semibold text-gray-600 dark:text-gray-300">Resume</th>
          <th class="px-6 py-4 text-left font-semibold text-gray-600 dark:text-gray-300">Actions</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
        <?php foreach($applications as $app): ?>
        
          <?php
            // Prepare full name
            $fullName = trim(($app['name'] ?? '') . ' ' . ($app['last_name'] ?? ''));
            $email = $app['email'] ?? '';

            // Get initials
            $words = preg_split('/\s+/', $fullName, -1, PREG_SPLIT_NO_EMPTY);
            if (count($words) >= 2) {
              $initials = strtoupper(mb_substr($words[0], 0, 1) . mb_substr($words[1], 0, 1));
            } else {
              $initials = strtoupper(mb_substr(preg_replace('/[^A-Za-z0-9]/u', '', $fullName), 0, 2));
            }
            if (empty($initials)) $initials = 'NA';

            // Generate background color
            $bgColor = sprintf('#%06X', crc32($fullName) & 0xFFFFFF);

            // SVG avatar
            $svg = "<svg xmlns='http://www.w3.org/2000/svg' width='40' height='40'>
              <rect width='100%' height='100%' rx='8' fill='{$bgColor}'/>
              <text x='50%' y='50%' dy='.05em' text-anchor='middle' alignment-baseline='middle'
                    font-family='Arial, Helvetica, sans-serif' font-size='16' fill='#ffffff' font-weight='700'>{$initials}</text>
            </svg>";
            $svgUrl = 'data:image/svg+xml;utf8,' . rawurlencode($svg);
            
            $currentStage = $app['ApplicationStage'] ?? 'Applied';        
            $transitions = $statusTransitions[$currentStage] ?? [];
            $final = empty($transitions);
            $status = $app['ApplicationStage'] ?? 'Applied';
          ?>
          
          <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
            <!-- Candidate -->
            <td class="px-6 py-4">
              <div class="flex items-center gap-3">
                <img src="<?= $svgUrl ?>" alt="<?= htmlspecialchars($fullName) ?>" class="w-10 h-10 rounded-lg flex-shrink-0 ring-2 ring-white dark:ring-gray-800">
                <div class="min-w-0">
                  <a href="<?= base_url('employer/applications/view/' . $app['applied_id']) ?>" 
                     class="block font-semibold text-gray-900 dark:text-gray-100 hover:text-purple-600 dark:hover:text-purple-400 truncate transition-colors">
                    <?= htmlspecialchars($fullName) ?>
                  </a>
                  <p class="text-xs text-gray-500 dark:text-gray-400 truncate mt-0.5">
                    <i class="fas fa-envelope text-[10px] mr-1"></i>
                    <?= htmlspecialchars($email) ?>
                  </p>
                </div>
              </div>
            </td>
            
            <!-- Location -->
            <td class="px-6 py-4">
              <div class="flex items-center text-gray-600 dark:text-gray-300">
                <i class="fas fa-map-marker-alt text-purple-500 mr-2 text-xs"></i>
                <span><?= !empty($app['city_name']) ? htmlspecialchars($app['city_name']) : 'Remote' ?></span>
              </div>
            </td>
            
            <!-- Status -->
            <td class="px-6 py-4">
              <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full <?php echo get_status_classes($status); ?> text-xs font-semibold shadow-sm">
                <i class="<?php echo get_status_icon_class($status); ?>"></i>
                <?= htmlspecialchars($status, ENT_QUOTES, 'UTF-8'); ?>
              </span>
            </td>
            
            <!-- Applied Date -->
            <td class="px-6 py-4">
              <div class="flex items-center text-xs text-gray-600 dark:text-gray-300">
                <i class="fas fa-clock mr-2 text-purple-500"></i>
                <span><?= timeAgo($app['applied_date']) ?></span>
              </div>
            </td>
            
            <!-- Resume -->
            <td class="px-6 py-4">
              <?php if(!empty($app['resume'])): ?>
                <a href="<?= base_url($app['resume'])?>" target="_blank"
                   class="inline-flex items-center gap-2 px-3 py-1.5 bg-gradient-to-r from-blue-50 to-blue-100 dark:from-blue-900/30 dark:to-blue-800/30 
                          text-blue-700 dark:text-blue-300 rounded-lg hover:from-blue-100 hover:to-blue-200 dark:hover:from-blue-800/50 dark:hover:to-blue-700/50 
                          transition-all text-xs font-medium border border-blue-200 dark:border-blue-800">
                  <i class="fas fa-file-pdf text-red-500"></i>
                  <span>View Resume</span>
                </a>
              <?php else: ?>
                <span class="text-xs text-gray-400 dark:text-gray-500 italic">No resume</span>
              <?php endif; ?>
            </td>
            
			<td class="px-6 py-4">
			  <div class="relative inline-block" x-data="{ open: false }">

				<button @click.stop="open = !open"
				  class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-purple-600 to-blue-600 
						 text-white rounded-lg text-xs font-semibold">
				  Actions
				  <i class="fas fa-chevron-down text-[10px]" 
					 :class="{ 'rotate-180': open }"></i>
				</button>

				<div x-show="open"
					 @click.outside="open = false"
					 x-transition
					 x-cloak
					 class="absolute right-0 mt-2 w-56 bg-white dark:bg-gray-900 
							border border-gray-200 rounded-xl shadow-xl z-50 text-sm">

				  <?php foreach($transitions as $val => $lbl): ?>
					<button type="button"
							onclick="updateStatus(<?= $app['applied_id'] ?>, <?= $app['job_id'] ?>, '<?= $val ?>')"
							class="w-full flex items-center gap-3 px-4 py-3 hover:bg-gray-50 
								   <?= $val === 'Rejected' ? 'text-red-600' : 'text-gray-700' ?>">
					  <i class="fas fa-arrow-right text-purple-500"></i>
					  <span><?= $lbl ?></span>
					</button>
				  <?php endforeach; ?>
				</div>

			  </div>
			</td>

		  </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  
  <!-- Mobile Cards -->
  <div class="md:hidden space-y-3">
    <?php foreach($applications as $app): ?>
      <?php 
        $currentStage = $app['ApplicationStage'] ?? 'Applied';        
        $transitions = $statusTransitions[$currentStage] ?? [];
        $final = empty($transitions);
        $fullName = trim(($app['name'] ?? '') . ' ' . ($app['last_name'] ?? ''));
        $email = $app['email'] ?? '';

        // Avatar logic
        $words = preg_split('/\s+/', $fullName, -1, PREG_SPLIT_NO_EMPTY);
        if (count($words) >= 2) {
          $initials = strtoupper(mb_substr($words[0], 0, 1) . mb_substr($words[1], 0, 1));
        } else {
          $initials = strtoupper(mb_substr(preg_replace('/[^A-Za-z0-9]/u', '', $fullName), 0, 2));
        }
        if (empty($initials)) $initials = 'NA';

        $bgColor = sprintf('#%06X', crc32($fullName) & 0xFFFFFF);

        $svg = "<svg xmlns='http://www.w3.org/2000/svg' width='40' height='40'>
          <rect width='100%' height='100%' rx='8' fill='{$bgColor}'/>
          <text x='50%' y='50%' dy='.05em' text-anchor='middle' alignment-baseline='middle'
                font-family='Arial, Helvetica, sans-serif' font-size='16' fill='#ffffff' font-weight='700'>{$initials}</text>
        </svg>";
        $svgUrl = 'data:image/svg+xml;utf8,' . rawurlencode($svg);
      ?>
      
      <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4 relative">
        <!-- Candidate Header -->
        <div class="flex items-start justify-between mb-3">
          <div class="flex items-center gap-3">
            <img src="<?= $svgUrl ?>" alt="<?= htmlspecialchars($fullName) ?>" 
                 class="w-12 h-12 rounded-xl ring-2 ring-white dark:ring-gray-800">
            <div>
              <a href="<?= base_url('employer/applications/view/' . $app['applied_id']) ?>" 
                 class="block font-semibold text-gray-900 dark:text-gray-100 hover:text-purple-600 dark:hover:text-purple-400">
                <?= htmlspecialchars($fullName) ?>
              </a>
              <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                <i class="fas fa-envelope mr-1 text-[10px]"></i>
                <?= htmlspecialchars($email) ?>
              </p>
            </div>
          </div>
          <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full <?= get_status_classes($app['ApplicationStage']) ?> text-xs font-semibold">
            <i class="<?= get_status_icon_class($app['ApplicationStage']) ?>"></i>
            <?= htmlspecialchars($app['ApplicationStage']) ?>
          </span>
        </div>

        <!-- Details Row -->
        <div class="grid grid-cols-2 gap-3 mb-4">
          <div class="flex items-center text-xs text-gray-600 dark:text-gray-300">
            <i class="fas fa-map-marker-alt text-purple-500 mr-2"></i>
            <span class="truncate"><?= !empty($app['city_name']) ? htmlspecialchars($app['city_name']) : 'Remote' ?></span>
          </div>
          <div class="flex items-center text-xs text-gray-600 dark:text-gray-300">
            <i class="fas fa-clock text-purple-500 mr-2"></i>
            <span><?= timeAgo($app['applied_date']) ?></span>
          </div>
        </div>

        <!-- Resume & Actions -->
        <div class="flex items-center justify-between pt-3 border-t border-gray-100 dark:border-gray-700">
          <?php if(!empty($app['resume'])): ?>
            <a href="<?= base_url($app['resume'])?>" target="_blank" 
               class="inline-flex items-center gap-2 px-3 py-1.5 bg-gradient-to-r from-blue-50 to-blue-100 dark:from-blue-900/30 dark:to-blue-800/30 
                      text-blue-700 dark:text-blue-300 rounded-lg text-xs font-medium">
              <i class="fas fa-file-pdf text-red-500"></i>
              <span>Resume</span>
            </a>
          <?php else: ?>
            <span class="text-xs text-gray-400 dark:text-gray-500 italic">No resume</span>
          <?php endif; ?>

          <!-- Mobile Actions Dropdown with Fixed Positioning -->
          <div class="relative inline-block" x-data="{ open: false }">
  
			  <button @click.stop="open = !open"
				class="inline-flex items-center gap-2 px-3 py-1.5 bg-gradient-to-r from-purple-600 to-blue-600 
					   text-white rounded-lg text-xs font-semibold shadow-sm">
				<i class="fas fa-ellipsis-h"></i>
				<span>Actions</span>
			  </button>

			  <div x-show="open"
				   @click.outside="open = false"
				   x-transition
				   x-cloak
				   class="absolute right-0 bottom-full mb-2 w-64 bg-white dark:bg-gray-900 
						  border border-gray-200 dark:border-gray-700 
						  rounded-xl shadow-xl z-50 text-sm">

				<?php foreach($transitions as $val => $lbl): ?>
				  <button type="button"
						  onclick="updateStatus(<?= $app['applied_id'] ?>, <?= $app['job_id'] ?>, '<?= $val ?>')"
						  class="w-full flex items-center gap-3 px-4 py-3 hover:bg-gray-50 
								 <?= $val === 'Rejected' ? 'text-red-600' : 'text-gray-700' ?>">
					<i class="fas fa-arrow-right text-purple-500"></i>
					<span><?= $lbl ?></span>
				  </button>
				<?php endforeach; ?>

			  </div>
			</div>

        </div>
      </div>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>

  <!-- Pagination -->
  <?php if(!empty($links)): ?>
    <div class="mt-8 mb-4">
      <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4">
        <div class="flex flex-col md:flex-row items-center justify-between gap-4">
          <p class="text-sm text-gray-600 dark:text-gray-400">
            Showing <?= count($applications) ?> of <?= $totalApplications ?? count($applications) ?> applications
          </p>
          <div class="flex items-center space-x-2">
            <?= $links ?>
          </div>
        </div>
      </div>
    </div>
  <?php endif; ?>
</div>

<!-- Toast Notification -->
<div id="toast" class="fixed bottom-4 right-4 z-[100] hidden">
  <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl border border-gray-200 dark:border-gray-700 
              transform transition-all duration-300 translate-x-full">
    <div class="p-4">
      <div class="flex items-center gap-3">
        <div id="toast-icon" class="w-10 h-10 rounded-lg flex items-center justify-center">
          <i id="toast-icon-type" class="fas text-white"></i>
        </div>
        <div>
          <h4 id="toast-title" class="font-semibold text-gray-900 dark:text-gray-100"></h4>
          <p id="toast-message" class="text-sm text-gray-600 dark:text-gray-400 mt-0.5"></p>
        </div>
        <button onclick="hideToast()" class="ml-4 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
          <i class="fas fa-times"></i>
        </button>
      </div>
    </div>
  </div>
</div>

<script>
function getCsrf() {
  return {
    name: document.querySelector('meta[name="csrf-name"]').getAttribute('content'),
    token: document.querySelector('meta[name="csrf-token"]').getAttribute('content')
  };
}

function refreshCsrf(newToken) {
  if (!newToken) return;
  document.querySelector('meta[name="csrf-token"]').setAttribute('content', newToken);
}

function showToast(title, message, type = 'success') {
  const toast = document.getElementById('toast');
  const toastIcon = document.getElementById('toast-icon');
  const toastIconType = document.getElementById('toast-icon-type');
  const toastTitle = document.getElementById('toast-title');
  const toastMessage = document.getElementById('toast-message');
  
  // Set colors based on type
  const colors = {
    success: 'bg-green-500',
    error: 'bg-red-500',
    warning: 'bg-yellow-500',
    info: 'bg-blue-500'
  };
  
  const icons = {
    success: 'fa-check-circle',
    error: 'fa-times-circle',
    warning: 'fa-exclamation-triangle',
    info: 'fa-info-circle'
  };
  
  toastIcon.className = `w-10 h-10 rounded-lg flex items-center justify-center ${colors[type]}`;
  toastIconType.className = `fas ${icons[type]} text-white`;
  toastTitle.textContent = title;
  toastMessage.textContent = message;
  
  toast.classList.remove('hidden');
  setTimeout(() => {
    toast.querySelector('.transform').classList.remove('translate-x-full');
  }, 10);
  
  // Auto hide after 5 seconds
  setTimeout(hideToast, 5000);
}

function hideToast() {
  const toast = document.getElementById('toast');
  toast.querySelector('.transform').classList.add('translate-x-full');
  setTimeout(() => toast.classList.add('hidden'), 300);
}

function updateStatus(applied_id, job_id, status) {
  if (!status.trim()) {
    showToast('Error', 'Please select a valid status', 'error');
    return;
  }

  const btn = event?.target?.closest("button");
  if (btn) {
    const originalHTML = btn.innerHTML;
    btn.innerHTML = `<i class="fas fa-spinner fa-spin"></i>`;
    btn.disabled = true;
  }

  const csrf = getCsrf();
  
  const form = new URLSearchParams();
  form.append('applied_id', applied_id);
  form.append('job_id', job_id);
  form.append('status', status);
  form.append(csrf.name, csrf.token);

  fetch('<?= base_url('employer/update_application_status') ?>', {
    method: 'POST',
    headers: { 
      'Content-Type': 'application/x-www-form-urlencoded',
      'X-CSRF-TOKEN': csrf.token
    },
    body: form.toString()
  })
  .then(res => res.json())
  .then(data => {
    if (btn) {
      btn.innerHTML = `<i class="fas fa-check"></i>`;
      setTimeout(() => {
        if (btn) {
          btn.innerHTML = btn.dataset.originalText || 'Done';
          btn.disabled = false;
        }
      }, 1000);
    }

    const message = data.message || (data.success ? 'Status updated successfully' : 'Update failed');
    const type = data.success ? 'success' : 'error';
    showToast(data.success ? 'Success' : 'Error', message, type);

    if (data.csrf_hash) {
      refreshCsrf(data.csrf_hash);
    }

    if (data.redirect) {
      setTimeout(() => window.location.href = data.redirect, 1200);
    } else if (data.success) {
      setTimeout(() => location.reload(), 1200);
    }
  })
  .catch(() => {
    if (btn) {
      btn.innerHTML = `<i class="fas fa-times"></i>`;
      setTimeout(() => {
        if (btn) {
          btn.innerHTML = btn.dataset.originalText || 'Retry';
          btn.disabled = false;
        }
      }, 1000);
    }
    showToast('Error', 'Network error occurred', 'error');
  });
}

</script>