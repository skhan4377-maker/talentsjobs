<style>
@keyframes slide-in {
    from { transform: translateX(100%); }
    to { transform: translateX(0); }
}
@keyframes fade-out {
    from { opacity: 1; }
    to { opacity: 0; }
}
.animate-slide-in { animation: slide-in 0.3s ease-out; }
.animate-fade-out { animation: fade-out 0.3s ease-in forwards; }
</style>

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
          $fullName = trim(($app['name'] ?? '') . ' ' . ($app['last_name'] ?? ''));
          $email = $app['email'] ?? '';
          
          $words = preg_split('/\s+/', $fullName, -1, PREG_SPLIT_NO_EMPTY);
          if (count($words) >= 2) {
              $initials = strtoupper(mb_substr($words[0],0,1) . mb_substr($words[1],0,1));
          } else {
              $initials = strtoupper(mb_substr(preg_replace('/[^A-Za-z0-9]/u','',$fullName),0,2));
          }
          if(empty($initials)) $initials='NA';
          
          $bgColor = sprintf('#%06X', crc32($fullName) & 0xFFFFFF);
          $svg = "<svg xmlns='http://www.w3.org/2000/svg' width='40' height='40'>
            <rect width='100%' height='100%' rx='8' fill='{$bgColor}'/>
            <text x='50%' y='50%' dy='.05em' text-anchor='middle' alignment-baseline='middle'
              font-family='Arial' font-size='16' fill='#fff' font-weight='700'>{$initials}</text>
          </svg>";
          $svgUrl = 'data:image/svg+xml;utf8,' . rawurlencode($svg);
          
          $currentStage = $app['ApplicationStage'] ?? 'Applied';
          $status = $currentStage;
        ?>
        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
          <!-- Candidate -->
          <td class="px-6 py-4">
            <div class="flex items-center gap-3">
              <img src="<?= $svgUrl ?>" class="w-10 h-10 rounded-lg ring-2 ring-white dark:ring-gray-800">
              <div class="min-w-0">
                <div class="flex items-center gap-2 flex-wrap">
                  <a href="<?= base_url('employer/applications/view/'.$app['applied_id']) ?>"
                     class="block font-semibold text-gray-900 dark:text-gray-100 hover:text-purple-600 truncate">
                    <?= htmlspecialchars($fullName) ?>
                  </a>
                  <?php if (!empty($app['has_active_plan'])): ?>
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300">
                      <i class="fas fa-crown text-xs mr-1"></i> Premium
                    </span>
                  <?php endif; ?>
                </div>
                <p class="text-xs text-gray-500 truncate mt-0.5">
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
            <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full <?= get_status_classes($status) ?> text-xs font-semibold shadow-sm">
              <i class="<?= get_status_icon_class($status) ?>"></i>
              <?= htmlspecialchars($status) ?>
            </span>
           </td>
          
          <!-- Applied -->
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
                 class="inline-flex items-center gap-2 px-3 py-1.5 bg-gradient-to-r from-blue-50 to-blue-100 text-blue-700 rounded-lg text-xs font-medium border border-blue-200">
                <i class="fas fa-file-pdf text-red-500"></i>
                <span>View Resume</span>
              </a>
            <?php else: ?>
              <span class="text-xs text-gray-400 italic">No resume</span>
            <?php endif; ?>
           </td>
          
          <!-- Actions (DYNAMIC) -->
          <td class="px-6 py-4">
            <div class="relative inline-block action-container" 
                 data-applied-id="<?= $app['applied_id'] ?>" 
                 data-job-id="<?= $app['job_id'] ?>">
              <button class="toggle-actions-btn inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-purple-600 to-blue-600 text-white rounded-lg text-xs font-semibold hover:opacity-90 transition">
                Actions <i class="fas fa-chevron-down text-[10px]"></i>
              </button>
              <div class="action-dropdown absolute right-0 mt-2 w-56 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl shadow-xl z-50 hidden">
                <div class="action-options py-1">
                  <div class="px-4 py-2 text-gray-500 text-sm">Loading…</div>
                </div>
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
        $status = $currentStage;
        
        $fullName = trim(($app['name'] ?? '') . ' ' . ($app['last_name'] ?? ''));
        $email = $app['email'] ?? '';
        
        $words = preg_split('/\s+/', $fullName, -1, PREG_SPLIT_NO_EMPTY);
        if (count($words) >= 2) {
            $initials = strtoupper(mb_substr($words[0],0,1) . mb_substr($words[1],0,1));
        } else {
            $initials = strtoupper(mb_substr(preg_replace('/[^A-Za-z0-9]/u','',$fullName),0,2));
        }
        if(empty($initials)) $initials='NA';
        
        $bgColor = sprintf('#%06X', crc32($fullName) & 0xFFFFFF);
        $svg = "<svg xmlns='http://www.w3.org/2000/svg' width='40' height='40'>
          <rect width='100%' height='100%' rx='8' fill='{$bgColor}'/>
          <text x='50%' y='50%' dy='.05em' text-anchor='middle' alignment-baseline='middle'
            font-family='Arial' font-size='16' fill='#fff' font-weight='700'>{$initials}</text>
        </svg>";
        $svgUrl = 'data:image/svg+xml;utf8,' . rawurlencode($svg);
      ?>
      <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4 relative">
        <!-- Candidate Header -->
        <div class="flex items-start justify-between mb-3">
          <div class="flex items-center gap-3">
            <img src="<?= $svgUrl ?>" class="w-12 h-12 rounded-xl ring-2 ring-white dark:ring-gray-800">
            <div>
              <div class="flex items-center gap-2 flex-wrap">
                <a href="<?= base_url('employer/applications/view/'.$app['applied_id']) ?>"
                   class="block font-semibold text-gray-900 dark:text-gray-100 hover:text-purple-600">
                  <?= htmlspecialchars($fullName) ?>
                </a>
                <?php if (!empty($app['has_active_plan'])): ?>
                  <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300">
                    <i class="fas fa-crown text-xs mr-1"></i> Premium
                  </span>
                <?php endif; ?>
              </div>
              <p class="text-xs text-gray-500 mt-0.5">
                <i class="fas fa-envelope mr-1 text-[10px]"></i>
                <?= htmlspecialchars($email) ?>
              </p>
            </div>
          </div>
          <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full <?= get_status_classes($status) ?> text-xs font-semibold">
            <i class="<?= get_status_icon_class($status) ?>"></i>
            <?= htmlspecialchars($status) ?>
          </span>
        </div>
        
        <!-- Details -->
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
        
        <!-- Resume + Actions -->
        <div class="flex items-center justify-between pt-3 border-t border-gray-100 dark:border-gray-700">
          <?php if(!empty($app['resume'])): ?>
            <a href="<?= base_url($app['resume'])?>" target="_blank"
               class="inline-flex items-center gap-2 px-3 py-1.5 bg-gradient-to-r from-blue-50 to-blue-100 text-blue-700 rounded-lg text-xs font-medium">
              <i class="fas fa-file-pdf text-red-500"></i>
              <span>Resume</span>
            </a>
          <?php else: ?>
            <span class="text-xs text-gray-400 italic">No resume</span>
          <?php endif; ?>
          
          <!-- Actions (DYNAMIC) -->
          <div class="relative inline-block action-container" 
               data-applied-id="<?= $app['applied_id'] ?>" 
               data-job-id="<?= $app['job_id'] ?>">
            <button class="toggle-actions-btn inline-flex items-center gap-2 px-3 py-1.5 bg-gradient-to-r from-purple-600 to-blue-600 text-white rounded-lg text-xs font-semibold hover:opacity-90 transition">
              <i class="fas fa-ellipsis-h"></i>
              <span>Actions</span>
            </button>
            <div class="action-dropdown absolute right-0 bottom-full mb-2 w-64 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl shadow-xl z-50 hidden">
              <div class="action-options py-1">
                <div class="px-4 py-2 text-gray-500 text-sm">Loading…</div>
              </div>
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
// ============================================================
// TOAST & UPDATE STATUS (kept unchanged)
// ============================================================
function showToast(title, message, type = 'success') {
  const toast = document.getElementById('toast');
  const toastIcon = document.getElementById('toast-icon');
  const toastIconType = document.getElementById('toast-icon-type');
  const toastTitle = document.getElementById('toast-title');
  const toastMessage = document.getElementById('toast-message');
  
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
    btn.innerHTML = `<i class="fas fa-spinner fa-spin"></i>`;
    btn.disabled = true;
  }

  const csrfName = getCSRFName();
  const csrfToken = getCSRFToken();

  const form = new URLSearchParams();
  form.append('applied_id', applied_id);
  form.append('job_id', job_id);
  form.append('status', status);
  form.append(csrfName, csrfToken);

  fetch('<?= base_url('employer/update_application_status') ?>', {
    method: 'POST',
    headers: { 
      'Content-Type': 'application/x-www-form-urlencoded',
      'X-CSRF-TOKEN': csrfToken
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
      updateCSRFToken(data.csrf_hash, data.csrf_name);
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

// ============================================================
// DYNAMIC DROPDOWN FOR LIST VIEW (replaces Alpine)
// ============================================================
document.addEventListener('DOMContentLoaded', function() {
    // Toggle dropdown on button click
    document.querySelectorAll('.toggle-actions-btn').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.stopPropagation();
            const container = this.closest('.action-container');
            const dropdown = container.querySelector('.action-dropdown');
            const isOpen = !dropdown.classList.contains('hidden');

            // Close all other dropdowns
            document.querySelectorAll('.action-dropdown').forEach(d => d.classList.add('hidden'));

            if (!isOpen) {
                dropdown.classList.remove('hidden');
                const optionsDiv = dropdown.querySelector('.action-options');
                // Load only if not loaded yet
                if (!optionsDiv.dataset.loaded) {
                    loadActionsForRow(container, optionsDiv);
                }
            }
        });
    });

    // Close dropdown when clicking outside
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.action-container')) {
            document.querySelectorAll('.action-dropdown').forEach(d => d.classList.add('hidden'));
        }
    });
});

function loadActionsForRow(container, optionsDiv) {
    const appliedId = container.dataset.appliedId;
    const jobId = container.dataset.jobId;

    optionsDiv.innerHTML = '<div class="px-4 py-2 text-gray-500 text-sm">Loading…</div>';

    fetch(`${window.baseUrl}employer/applications/get_next_statuses/${appliedId}`)
        .then(response => response.json())
        .then(data => {
            const statuses = data.statuses;
            optionsDiv.innerHTML = '';

            if (Object.keys(statuses).length === 0) {
                optionsDiv.innerHTML = '<div class="px-4 py-2 text-gray-500 text-sm">No further actions</div>';
                optionsDiv.dataset.loaded = 'true';
                return;
            }

            for (const [statusValue, statusLabel] of Object.entries(statuses)) {
                const item = document.createElement('div');
                item.className = 'border-b border-gray-100 dark:border-gray-700 last:border-0';

                // Interview actions (Scheduled / Rescheduled) → redirect
                if (statusValue === 'Scheduled' || statusValue === 'Rescheduled') {
                    const link = document.createElement('a');
                    link.href = `${window.baseUrl}employer/applications/redirect_to_interview/${appliedId}`;
                    link.className = 'block px-4 py-3 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 flex items-center';
                    link.innerHTML = `<i class="fas fa-calendar-alt mr-2 text-purple-600"></i> ${statusLabel}`;
                    item.appendChild(link);
                } else {
                    const btn = document.createElement('button');
                    btn.className = 'w-full text-left px-4 py-3 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 flex items-center';
                    const iconClass = getStatusIconClass(statusValue);
                    btn.innerHTML = `<i class="${iconClass} mr-2 ${statusValue === 'Rejected' ? 'text-red-600' : 'text-green-600'}"></i> ${statusLabel}`;
                    btn.addEventListener('click', function(e) {
                        e.preventDefault();
                        updateStatus(appliedId, jobId, statusValue);
                    });
                    item.appendChild(btn);
                }
                optionsDiv.appendChild(item);
            }

            optionsDiv.dataset.loaded = 'true';
        })
        .catch(error => {
            optionsDiv.innerHTML = '<div class="px-4 py-2 text-red-500 text-sm">Error loading options</div>';
            console.error('Error fetching statuses:', error);
        });
}

function getStatusIconClass(status) {
    const map = {
        'Applied': 'far fa-file',
        'Viewed': 'far fa-eye',
        'Under Review': 'fas fa-search',
        'Shortlist': 'fas fa-user-check',
        'Interview Scheduled': 'fas fa-video',
        'Scheduled': 'fas fa-calendar-check',
        'Rescheduled': 'fas fa-calendar-alt',
        'Offer Extended': 'fas fa-hand-holding-usd',
        'Hired': 'fas fa-trophy',
        'Completed': 'fas fa-check-circle',
        'Missed': 'fas fa-user-times',
        'Rejected': 'fas fa-ban',
        'Withdraw': 'fas fa-sign-out-alt',
        'Canceled': 'fas fa-times-circle'
    };
    return map[status] || 'fas fa-question-circle';
}

// ============================================================
// GLOBAL BASE URL (set in layout)
// ============================================================
window.baseUrl = '<?= base_url() ?>';
</script>