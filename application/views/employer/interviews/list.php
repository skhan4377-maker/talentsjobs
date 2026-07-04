<div class="container mx-auto">
  <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-lg p-6">

    <!-- Header & Search -->
    <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4 mb-6">    
      <div class="relative w-full md:w-64">
        <input
          type="text" placeholder="Search interviews…"
          oninput="filterTable(this.value)"
          class="w-full pl-10 pr-4 py-2 border border-gray-300 dark:border-gray-700 rounded-lg focus:ring-2 focus:ring-indigo-500 transition">
        <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
      </div>	  
      <!-- Schedule Interview Button -->
      <a href="<?= site_url('employer/applications') ?>" 
         class="inline-flex items-center px-4 py-2 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium shadow transition">
        <i class="fas fa-calendar-plus mr-2"></i> Schedule Interview
      </a>  
    </div>

    <!-- Interviews Table -->
    <div class="overflow-x-auto">
      <table id="interviewsTable" class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
        <thead class="bg-gray-50 dark:bg-gray-800">
            <th class="px-4 py-3 text-left text-sm font-medium text-gray-500 dark:text-gray-400 uppercase">Candidate</th>
            <th class="px-4 py-3 text-left text-sm font-medium text-gray-500 dark:text-gray-400 uppercase hidden md:table-cell">Job Title</th>
            <th class="px-4 py-3 text-left text-sm font-medium text-gray-500 dark:text-gray-400 uppercase">Date & Time</th>
            <th class="px-4 py-3 text-left text-sm font-medium text-gray-500 dark:text-gray-400 uppercase">Type</th>
            <th class="px-4 py-3 text-left text-sm font-medium text-gray-500 dark:text-gray-400 uppercase">Status</th>
            <th class="px-4 py-3 text-left text-sm font-medium text-gray-500 dark:text-gray-400 uppercase">Actions</th>
          </tr>
        </thead>
        <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">

        <?php foreach($interviews as $int): ?>

        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">

        <!-- Candidate -->
        <td class="px-4 py-4 whitespace-nowrap">
        <div class="flex items-center">

        <?php
        $firstName = trim($int['candidate_name'] ?? '');
        $lastName  = '';
        $fullName  = trim($firstName . ' ' . $lastName);

        if (!empty($int['logo']) && file_exists('uploads/candidate/profile/'.$int['logo'])) {

        $imgSrc = base_url('uploads/candidate/profile/'.$int['logo']);

        echo '<img src="'.$imgSrc.'" alt="'.htmlspecialchars($fullName).'" class="h-10 w-10 rounded-full object-cover shadow-sm">';

        } else {

        $initials = '';
        if (!empty($firstName)) $initials .= mb_substr($firstName,0,1);
        if (!empty($lastName))  $initials .= mb_substr($lastName,0,1);
        $initials = strtoupper($initials ?: 'NA');

        $bgColor = sprintf('#%06X', crc32($fullName) & 0xFFFFFF);

        $svg = "<svg xmlns='http://www.w3.org/2000/svg' width='40' height='40'>
        <rect width='100%' height='100%' rx='50%' fill='{$bgColor}'/>
        <text x='50%' y='50%' dy='.05em' text-anchor='middle' alignment-baseline='middle'
        font-family='Arial, Helvetica, sans-serif' font-size='16' fill='#ffffff' font-weight='700'>{$initials}</text>
        </svg>";

        $svgUrl = 'data:image/svg+xml;utf8,' . rawurlencode($svg);

        echo '<img src="'.$svgUrl.'" alt="'.htmlspecialchars($fullName).'" class="h-10 w-10 rounded-full shadow-sm">';
        }
        ?>

        <div class="ml-3">
          <div class="flex items-center gap-2 flex-wrap">
            <div class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate">
              <?= htmlspecialchars($int['candidate_name']) ?>
            </div>
            <?php if (!empty($int['has_active_plan'])): ?>
			  <span 
				class="inline-flex items-center justify-center w-5 h-5 rounded-full text-xs bg-gradient-to-br from-yellow-300 to-amber-500 text-white shadow-sm"
				title="Premium Subscriber"
			  ><i class="fas fa-crown"></i></span>
			<?php endif; ?>
          </div>
          <div class="text-xs text-gray-500 dark:text-gray-400">
            <?= date('M d', strtotime($int['interview_date'])) ?>
          </div>
        </div>

        </div>
        </td>

        <!-- Job Title -->
        <td class="px-4 py-4 text-sm text-gray-500 dark:text-gray-300 hidden md:table-cell truncate">
        <?= htmlspecialchars($int['job_title']) ?>
        </td>

        <!-- Date & Time -->
        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-200">
        <?= date('h:i A',strtotime($int['interview_date'].' '.$int['interview_time'])) ?>
        </td>

        <!-- Interview Type -->
        <td class="px-4 py-4 whitespace-nowrap">
        <span class="px-2 py-1 rounded-full text-xs bg-purple-100 text-purple-800 dark:bg-purple-800 dark:text-purple-200">
        <?= htmlspecialchars($int['interview_type']) ?>
        </span>
        </td>

        <!-- Status -->
        <td class="px-4 py-4 whitespace-nowrap">
        <?php $currentStatus = $int['status'] ?? 'Scheduled'; ?>
        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-semibold <?= get_status_classes($currentStatus) ?>">
        <i class="<?= get_status_icon_class($currentStatus) ?> text-[10px]"></i>
        <?= htmlspecialchars($currentStatus) ?>
        </span>
        </td>

        <!-- Actions -->
        <td class="px-4 py-4 whitespace-nowrap">
        <a href="<?= site_url('employer/interviews/schedule/'.$int['applied_id'].'?edit='.$int['interview_id']) ?>" 
        class="text-blue-600 hover:text-blue-800 transition">
        <i class="fas fa-pencil-alt"></i>
        </a>
        </td>

        </tr>

        <?php endforeach; ?>

        </tbody>

      </table>

      <?php if(empty($interviews)): ?>
        <div class="bg-white border border-gray-200 rounded-xl p-8 text-center max-w-2xl mx-auto my-6">
        
            <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-indigo-100 flex items-center justify-center">
                <i class="fas fa-calendar-alt text-indigo-600 text-2xl"></i>
            </div>
        
            <h3 class="text-xl font-bold text-gray-800 mb-2">
                No Interviews Scheduled Yet
            </h3>
        
            <p class="text-gray-600 mb-5">
                Start your hiring journey by posting a job. Once candidates apply,
                you can review applications and schedule interviews directly from your dashboard.
            </p>
        
            <div class="flex flex-wrap justify-center gap-2 mb-5 text-xs">
        
                <span class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full">
                    <i class="fas fa-briefcase mr-1"></i>
                    Post Job
                </span>
        
                <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full">
                    <i class="fas fa-users mr-1"></i>
                    Receive Applications
                </span>
        
                <span class="bg-purple-100 text-purple-700 px-3 py-1 rounded-full">
                    <i class="fas fa-calendar-check mr-1"></i>
                    Schedule Interviews
                </span>
        
            </div>
        
            <a href="<?= base_url('employer/jobs/create') ?>"
               class="inline-flex items-center px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-medium">
                <i class="fas fa-plus-circle mr-2"></i>
                Post Your First Job
            </a>
        
        </div>
        <?php endif; ?>
    </div>

    <!-- Pagination -->
    <?php if(!empty($links)): ?>
    <div class="mt-6 flex justify-center">
      <?= $links ?>
    </div>
    <?php endif; ?>
  </div>
</div>

<script>
// Optional: client-side table filtering
function filterTable(query) {
  const rows = document.querySelectorAll('#interviewsTable tbody tr');
  rows.forEach(row => {
    const content = row.innerText.toLowerCase();
    row.style.display = content.includes(query.toLowerCase()) ? '' : 'none';
  });
}
</script>