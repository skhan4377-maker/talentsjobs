<div class="container mx-auto">
  <div class="bg-white dark:bg-gray-900 rounded-xl shadow-sm p-4 md:p-6 mb-4 md:mb-0 text-sm">
    <!-- Search & Header -->
    <div class="mb-6">
      <form method="get" action="<?= site_url('employer/applications') ?>" class="w-full">
        <div class="flex flex-col md:flex-row flex-wrap gap-3">
          
          <!-- Search Input -->
          <div class="relative flex-1 min-w-0">
            <div class="relative">
              <input
                type="text" 
                name="search" 
                placeholder="Search by name, email..."
                value="<?= htmlspecialchars($this->input->get('search')) ?>"
                class="w-full pl-10 pr-4 py-2.5 md:py-1.5 border border-gray-300 dark:border-gray-700 rounded-lg md:rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm bg-white dark:bg-gray-800"
              >
              <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 text-sm"></i>
            </div>
          </div>
    
          <!-- Date Range - Mobile Accordion -->
          <div class="w-full md:hidden">
            <details class="group">
              <summary class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-800 rounded-lg cursor-pointer">
                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Date Filter</span>
                <i class="fas fa-chevron-down text-gray-500 group-open:rotate-180 transition-transform"></i>
              </summary>
              <div class="mt-2 space-y-2 p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                <div>
                  <label class="block text-xs text-gray-600 dark:text-gray-400 mb-1">From</label>
                  <input type="date" name="start_date"
                         value="<?= htmlspecialchars($this->input->get('start_date')) ?>"
                         class="w-full border border-gray-300 dark:border-gray-700 rounded-md px-3 py-2 text-sm bg-white dark:bg-gray-900">
                </div>
                <div>
                  <label class="block text-xs text-gray-600 dark:text-gray-400 mb-1">To</label>
                  <input type="date" name="end_date"
                         value="<?= htmlspecialchars($this->input->get('end_date')) ?>"
                         class="w-full border border-gray-300 dark:border-gray-700 rounded-md px-3 py-2 text-sm bg-white dark:bg-gray-900">
                </div>
              </div>
            </details>
          </div>
    
          <!-- Desktop Date Range -->
          <div class="hidden md:flex gap-2">
            <div class="w-40">
              <input type="date" name="start_date"
                     value="<?= htmlspecialchars($this->input->get('start_date')) ?>"
                     class="w-full border border-gray-300 dark:border-gray-700 rounded-md px-3 py-1.5 text-sm"
                     placeholder="Start date">
            </div>
            <div class="w-40">
              <input type="date" name="end_date"
                     value="<?= htmlspecialchars($this->input->get('end_date')) ?>"
                     class="w-full border border-gray-300 dark:border-gray-700 rounded-md px-3 py-1.5 text-sm"
                     placeholder="End date">
            </div>
          </div>
    
          <!-- Status Filter -->
          <div class="w-full md:w-auto">
            <select name="status"
                    class="w-full border border-gray-300 dark:border-gray-700 rounded-lg md:rounded-md px-4 py-2.5 md:py-1.5 text-sm bg-white dark:bg-gray-800">
              <option value="">All Statuses</option>
              <?php
                $statuses = [
                  'Applied', 'Viewed', 'Under Review', 'Shortlist',
                  'Interview Scheduled', 'Scheduled', 'Rescheduled',
                  'Offer Extended', 'Hired', 'Completed',
                  'Withdraw', 'Rejected', 'Canceled'
                ];
                foreach ($statuses as $s):
              ?>
                <option value="<?= $s ?>" <?= ($this->input->get('status') == $s) ? 'selected' : '' ?>>
                  <?= $s ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>
    
          <!-- Action Buttons -->
          <div class="flex flex-row gap-2 w-full md:w-auto">
            <button type="submit"
                    class="flex-1 md:flex-none bg-blue-600 text-white px-5 py-2.5 md:py-1.5 rounded-lg md:rounded-md text-sm hover:bg-blue-700 font-medium">
              <i class="fas fa-filter md:hidden mr-2"></i>
              <span>Filter</span>
            </button>
            <a href="<?= site_url('employer/applications') ?>"
               class="flex-1 md:flex-none px-5 py-2.5 md:py-1.5 bg-gray-100 hover:bg-gray-200 dark:bg-gray-800 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-200 rounded-lg md:rounded-md text-sm font-medium text-center">
              <i class="fas fa-redo md:hidden mr-2"></i>
              <span>Reset</span>
            </a>
          </div>
        </div>
      </form>
    </div>

    <!-- Results Summary -->
    <?php if (!empty($applications)): ?>
      <div class="mb-4 p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-100 dark:border-blue-800">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
          <div class="text-sm text-gray-700 dark:text-gray-300">
            <span class="font-medium"><?= count($applications) ?></span> applications found
            <?php if ($total_filtered < $total_all): ?>
              <span class="text-gray-500 dark:text-gray-400 text-xs">(filtered from <?= $total_all ?> total)</span>
            <?php endif; ?>
          </div>
          <div class="flex items-center gap-3 text-xs">
            <div class="flex items-center gap-1">
              <span class="w-2 h-2 bg-blue-500 rounded-full"></span>
              <span class="text-gray-600 dark:text-gray-400">Applied</span>
            </div>
            <div class="flex items-center gap-1">
              <span class="w-2 h-2 bg-green-500 rounded-full"></span>
              <span class="text-gray-600 dark:text-gray-400">Shortlisted</span>
            </div>
            <div class="flex items-center gap-1">
              <span class="w-2 h-2 bg-purple-500 rounded-full"></span>
              <span class="text-gray-600 dark:text-gray-400">Interview</span>
            </div>
          </div>
        </div>
      </div>
    <?php endif; ?>

    <!-- Desktop Table (hidden on mobile) -->
    <div class="hidden md:block overflow-x-auto">
      <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
        <thead class="bg-gray-50 dark:bg-gray-800 text-xs">
          <tr>
            <th class="px-4 py-3 text-left font-medium text-gray-600 dark:text-gray-400 uppercase tracking-wider">Candidate</th>
            <th class="px-4 py-3 text-left font-medium text-gray-600 dark:text-gray-400 uppercase tracking-wider">Job Title</th>
            <th class="px-4 py-3 text-left font-medium text-gray-600 dark:text-gray-400 uppercase tracking-wider">Status</th>
            <th class="px-4 py-3 text-left font-medium text-gray-600 dark:text-gray-400 uppercase tracking-wider">Applied</th>
            <th class="px-4 py-3 text-left font-medium text-gray-600 dark:text-gray-400 uppercase tracking-wider">Actions</th>
          </tr>
        </thead>
        <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
          <?php foreach($applications as $app): ?>
          <?php $status = $app['ApplicationStage'] ?? 'Applied'; ?>
          <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition">
            <!-- Candidate -->
            <td class="px-4 py-3">
              <div class="flex items-center gap-3">
                <?php
                  $firstName = trim($app['name'] ?? '');
                  $lastName  = trim($app['last_name'] ?? '');
                  $fullName  = trim($firstName . ' ' . $lastName);
                  
                  if (!empty($app['logo'])) {
                    $imgSrc = base_url($app['logo']);
                    echo '<img src="' . $imgSrc . '" alt="' . htmlspecialchars($fullName) . '" class="h-9 w-9 rounded-full object-cover ring-2 ring-white dark:ring-gray-800">';
                  } else {
                    $initials = '';
                    if (!empty($firstName)) $initials .= mb_substr($firstName, 0, 1);
                    if (!empty($lastName))  $initials .= mb_substr($lastName, 0, 1);
                    $initials = strtoupper($initials);
                    if (empty($initials)) $initials = 'NA';
                    
                    $bgColor = sprintf('#%06X', crc32($fullName) & 0xFFFFFF);
                    
                    $svg = "<svg xmlns='http://www.w3.org/2000/svg' width='36' height='36'>
                      <rect width='100%' height='100%' rx='50%' fill='{$bgColor}'/>
                      <text x='50%' y='50%' dy='.05em' text-anchor='middle' alignment-baseline='middle'
                            font-family='Arial, Helvetica, sans-serif' font-size='14' fill='#ffffff' font-weight='700'>{$initials}</text>
                    </svg>";
                    $svgUrl = 'data:image/svg+xml;utf8,' . rawurlencode($svg);
                    
                    echo '<img src="' . $svgUrl . '" alt="' . htmlspecialchars($fullName) . '" class="h-9 w-9 rounded-full ring-2 ring-white dark:ring-gray-800">';
                  }
                ?>
                <div class="min-w-0">
                  <div class="flex items-center gap-2">
                    <a href="<?= site_url('employer/applications/view/' . $app['applied_id']) ?>" 
                       class="block font-medium text-gray-900 dark:text-gray-100 hover:text-blue-600 dark:hover:text-blue-400 truncate transition">
                      <?= htmlspecialchars($fullName) ?>
                    </a>
                    <!-- ✅ SINGLE PREMIUM ICON (only if user has any active plan) -->
                    <?php if (!empty($app['active_features'])): ?>
                      <span 
                        class="inline-flex items-center justify-center w-5 h-5 rounded-full text-xs bg-gradient-to-br from-yellow-300 to-amber-500 text-white shadow-sm"
                        title="Premium Subscriber"
                      ><i class="fas fa-crown"></i></span>
                    <?php endif; ?>
                  </div>
                  <p class="text-xs text-gray-500 dark:text-gray-400 truncate mt-0.5">
                    <i class="fas fa-envelope mr-1 text-[10px]"></i>
                    <?= htmlspecialchars($app['email'] ?? '') ?>
                  </p>
                </div>
              </div>
             </td>

            <!-- Job Title -->
            <td class="px-4 py-3 text-gray-700 dark:text-gray-300 truncate max-w-[180px]">
              <?= htmlspecialchars($app['job_title'] ?? 'N/A') ?>
             </td>

            <!-- Status -->
            <td class="px-4 py-3">
              <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold <?= get_status_classes($status); ?>">
                <i class="<?= get_status_icon_class($status); ?> text-xs"></i>
                <?= htmlspecialchars($status, ENT_QUOTES, 'UTF-8'); ?>
              </span>
             </td>

            <!-- Applied Date -->
            <td class="px-4 py-3 text-gray-600 dark:text-gray-400 text-xs">
              <?= date('M d, Y', strtotime($app['created_at'])) ?>
             </td>

            <!-- Actions -->
            <td class="px-4 py-3">
              <div class="flex items-center gap-3">
                <a href="<?= site_url('employer/applications/view/'.$app['applied_id']) ?>" 
                   class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 hover:bg-blue-100 dark:hover:bg-blue-800/50 rounded-md text-xs font-medium transition"
                   title="View Details">
                  <i class="fas fa-eye text-xs"></i>
                  <span>View</span>
                </a>
                
                <?php $canSchedule = in_array($status, ['Shortlist', 'Under Review']); ?>
                <?php if ($employer_status === 'active' && $canSchedule): ?>
                  <a href="<?= site_url('employer/interviews/schedule/' . $app['applied_id']) ?>"
                     class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-green-50 dark:bg-green-900/30 text-green-600 dark:text-green-400 hover:bg-green-100 dark:hover:bg-green-800/50 rounded-md text-xs font-medium transition"
                     title="Schedule Interview">
                    <i class="fas fa-calendar-check text-xs"></i>
                    <span>Schedule</span>
                  </a>
                <?php elseif ($employer_status !== 'active'): ?>
                  <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-gray-100 dark:bg-gray-800 text-gray-400 dark:text-gray-500 rounded-md text-xs cursor-not-allowed"
                        title="Employer account is inactive">
                    <i class="fas fa-calendar-check text-xs"></i>
                    <span>Schedule</span>
                  </span>
                <?php else: ?>
                  <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-gray-100 dark:bg-gray-800 text-gray-400 dark:text-gray-500 rounded-md text-xs cursor-not-allowed"
                        title="Cannot schedule in '<?= htmlspecialchars($status) ?>' stage">
                    <i class="fas fa-calendar-check text-xs"></i>
                    <span>Schedule</span>
                  </span>
                <?php endif; ?>

                <?php if ($status === 'Hired'): ?>
                  <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 rounded-md text-xs"
                        title="Candidate Hired">
                    <i class="fas fa-check-double text-xs"></i>
                    <span>Hired</span>
                  </span>
                <?php endif; ?>
              </div>
             </td>
           </tr>
          <?php endforeach; ?>
        </tbody>
       </table>
    </div>

    <!-- Mobile Cards (visible only on mobile) -->
    <div class="md:hidden space-y-3">
      <?php foreach($applications as $app): ?>
        <?php 
          $status = $app['ApplicationStage'] ?? 'Applied';
          $firstName = trim($app['name'] ?? '');
          $lastName  = trim($app['last_name'] ?? '');
          $fullName  = trim($firstName . ' ' . $lastName);
          $canSchedule = in_array($status, ['Shortlist', 'Under Review']);
        ?>
        
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4 shadow-sm">
          <!-- Card Header -->
          <div class="flex items-start justify-between mb-3">
            <div class="flex items-center gap-3">
              <?php
                if (!empty($app['logo'])) {
                  $imgSrc = base_url($app['logo']);
                  echo '<img src="' . $imgSrc . '" alt="' . htmlspecialchars($fullName) . '" class="h-12 w-12 rounded-xl object-cover ring-2 ring-white dark:ring-gray-800">';
                } else {
                  $initials = '';
                  if (!empty($firstName)) $initials .= mb_substr($firstName, 0, 1);
                  if (!empty($lastName))  $initials .= mb_substr($lastName, 0, 1);
                  $initials = strtoupper($initials);
                  if (empty($initials)) $initials = 'NA';
                  
                  $bgColor = sprintf('#%06X', crc32($fullName) & 0xFFFFFF);
                  
                  $svg = "<svg xmlns='http://www.w3.org/2000/svg' width='48' height='48'>
                    <rect width='100%' height='100%' rx='12' fill='{$bgColor}'/>
                    <text x='50%' y='50%' dy='.05em' text-anchor='middle' alignment-baseline='middle'
                          font-family='Arial, Helvetica, sans-serif' font-size='18' fill='#ffffff' font-weight='700'>{$initials}</text>
                  </svg>";
                  $svgUrl = 'data:image/svg+xml;utf8,' . rawurlencode($svg);
                  
                  echo '<img src="' . $svgUrl . '" alt="' . htmlspecialchars($fullName) . '" class="h-12 w-12 rounded-xl ring-2 ring-white dark:ring-gray-800">';
                }
              ?>
              <div class="min-w-0">
                <div class="flex items-center gap-2">
                  <a href="<?= site_url('employer/applications/view/' . $app['applied_id']) ?>" 
                     class="block font-semibold text-gray-900 dark:text-gray-100 hover:text-blue-600 dark:hover:text-blue-400 truncate">
                    <?= htmlspecialchars($fullName) ?>
                  </a>
                  <!-- ✅ SINGLE PREMIUM ICON (mobile) -->
                  <?php if (!empty($app['active_features'])): ?>
                    <span 
                      class="inline-flex items-center justify-center w-5 h-5 rounded-full text-xs bg-gradient-to-br from-yellow-300 to-amber-500 text-white shadow-sm"
                      title="Premium Subscriber"
                    ><i class="fas fa-crown"></i></span>
                  <?php endif; ?>
                </div>
                <p class="text-xs text-gray-500 dark:text-gray-400 truncate mt-0.5">
                  <i class="fas fa-envelope mr-1 text-[10px]"></i>
                  <?= htmlspecialchars($app['email'] ?? '') ?>
                </p>
              </div>
            </div>
            <span class="px-2.5 py-1 rounded-full text-[11px] font-semibold <?= get_status_classes($status); ?>">
              <i class="<?= get_status_icon_class($status); ?> text-xs"></i>
            </span>
          </div>

          <!-- Job Title & Status -->
          <div class="mb-3">
            <p class="text-sm text-gray-700 dark:text-gray-300 font-medium truncate">
              <?= htmlspecialchars($app['job_title'] ?? 'N/A') ?>
            </p>
            <div class="flex items-center justify-between mt-2">
              <span class="text-xs text-gray-500 dark:text-gray-400">
                <i class="fas fa-calendar-day mr-1"></i>
                <?= date('M d, Y', strtotime($app['created_at'])) ?>
              </span>
              <span class="text-xs font-medium px-2 py-0.5 rounded-full <?= get_status_classes($status); ?>">
                <?= htmlspecialchars($status) ?>
              </span>
            </div>
          </div>

          <!-- Action Buttons -->
          <div class="flex items-center gap-2 pt-3 border-t border-gray-100 dark:border-gray-700">
            <a href="<?= site_url('employer/applications/view/'.$app['applied_id']) ?>" 
               class="flex-1 inline-flex items-center justify-center gap-2 px-3 py-2.5 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition">
              <i class="fas fa-eye text-sm"></i>
              <span>View Details</span>
            </a>
            
            <?php if ($employer_status === 'active' && $canSchedule): ?>
              <a href="<?= site_url('employer/interviews/schedule/' . $app['applied_id']) ?>"
                 class="flex-1 inline-flex items-center justify-center gap-2 px-3 py-2.5 bg-green-600 text-white rounded-lg text-sm font-medium hover:bg-green-700 transition">
                <i class="fas fa-calendar-check text-sm"></i>
                <span>Schedule</span>
              </a>
            <?php elseif ($employer_status !== 'active'): ?>
              <button class="flex-1 inline-flex items-center justify-center gap-2 px-3 py-2.5 bg-gray-300 dark:bg-gray-700 text-gray-500 dark:text-gray-400 rounded-lg text-sm font-medium cursor-not-allowed"
                      disabled
                      title="Employer account is inactive">
                <i class="fas fa-calendar-check text-sm"></i>
                <span>Schedule</span>
              </button>
            <?php else: ?>
              <button class="flex-1 inline-flex items-center justify-center gap-2 px-3 py-2.5 bg-gray-300 dark:bg-gray-700 text-gray-500 dark:text-gray-400 rounded-lg text-sm font-medium cursor-not-allowed"
                      disabled
                      title="Cannot schedule in '<?= htmlspecialchars($status) ?>' stage">
                <i class="fas fa-calendar-check text-sm"></i>
                <span>Schedule</span>
              </button>
            <?php endif; ?>
          </div>

          <!-- Quick Actions Row -->
          <?php if ($status === 'Hired'): ?>
            <div class="mt-3 p-2 bg-green-50 dark:bg-green-900/20 rounded-lg border border-green-100 dark:border-green-800">
              <div class="flex items-center justify-center gap-2 text-green-700 dark:text-green-400 text-xs font-medium">
                <i class="fas fa-check-double"></i>
                <span>Candidate has been hired for this position</span>
              </div>
            </div>
          <?php endif; ?>
        </div>
      <?php endforeach; ?>
    </div>

    <?php if (empty($applications)): ?>
    
        <?php if ($total_filtered < $total_all): ?>
    
            <div class="text-center py-12 md:py-16">
                <div class="inline-flex items-center justify-center w-20 h-20 bg-gradient-to-br from-blue-50 to-purple-50 rounded-full mb-4">
                    <i class="fas fa-filter text-3xl text-blue-600"></i>
                </div>
    
                <h3 class="text-lg font-semibold text-gray-700 mb-2">
                    No Matching Applications
                </h3>
    
                <p class="text-gray-500 max-w-md mx-auto text-sm mb-6">
                    Try adjusting your filters to see more results.
                </p>
    
                <a href="<?= site_url('employer/applications') ?>"
                   class="inline-flex items-center gap-2 px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium transition">
                    <i class="fas fa-redo"></i>
                    <span>Clear Filters</span>
                </a>
            </div>
    
        <?php else: ?>
    
            <div class="max-w-2xl mx-auto text-center py-12">
    
                <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-blue-100 flex items-center justify-center">
                    <i class="fas fa-user-friends text-blue-600 text-2xl"></i>
                </div>
    
                <h3 class="text-xl font-bold text-gray-800 mb-2">
                    No Applications Yet
                </h3>
    
                <p class="text-gray-600 text-sm mb-5 max-w-lg mx-auto">
                    Start by posting a job. Once candidates apply, you'll be able to review applications, shortlist talent and schedule interviews from here.
                </p>
    
                <div class="flex flex-wrap justify-center gap-2 mb-6">
    
                    <span class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-xs">
                        <i class="fas fa-briefcase mr-1"></i>
                        Post Job
                    </span>
    
                    <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs">
                        <i class="fas fa-users mr-1"></i>
                        Receive Applications
                    </span>
    
                    <span class="bg-purple-100 text-purple-700 px-3 py-1 rounded-full text-xs">
                        <i class="fas fa-calendar-check mr-1"></i>
                        Schedule Interviews
                    </span>
    
                </div>
    
                <a href="<?= site_url('employer/jobs/create') ?>"
                   class="inline-flex items-center gap-2 px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium">
                    <i class="fas fa-plus-circle"></i>
                    Post Your First Job
                </a>
    
            </div>
    
        <?php endif; ?>
    
    <?php endif; ?>

    <!-- Pagination -->
    <?php if (!empty($links)): ?>
      <div class="mt-8 pt-4 border-t border-gray-200 dark:border-gray-700">
        <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
          <div class="text-sm text-gray-600 dark:text-gray-400">
            Showing <?= count($applications) ?> of <?= $total_filtered ?? $total_all ?> applications
          </div>
          <div class="flex flex-wrap justify-center gap-2">
            <?= $links ?>
          </div>
        </div>
      </div>
    <?php endif; ?>
  </div>
</div>

<script>
// Mobile filter accordion toggle
document.addEventListener('DOMContentLoaded', function() {
  const details = document.querySelectorAll('details');
  details.forEach(targetDetail => {
    targetDetail.addEventListener('click', () => {
      details.forEach(detail => {
        if (detail !== targetDetail) {
          detail.removeAttribute('open');
        }
      });
    });
  });
  
  const today = new Date().toISOString().split('T')[0];
  document.querySelectorAll('input[type="date"]').forEach(input => {
    input.setAttribute('max', today);
  });
});
</script>