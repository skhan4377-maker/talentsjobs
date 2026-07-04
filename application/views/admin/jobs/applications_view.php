<!-- Loader -->
<div id="globalLoader" class="fixed inset-0 z-[9999] bg-black bg-opacity-50 hidden flex items-center justify-center">
  <div class="flex items-center gap-3 bg-white px-6 py-4 rounded-md shadow text-gray-800 text-sm font-medium">
    <span class="loader-spinner w-5 h-5 border-2 border-blue-600 border-t-transparent rounded-full"></span>
    Processing...
  </div>
</div>

<div class="bg-white p-4 rounded shadow">
  <!-- Filters -->
  <form method="GET" action="<?= base_url('admin/jobs/AdminApplications/applications') ?>" id="application-filter-form" class="flex flex-col sm:flex-row flex-wrap gap-3 mb-4">
    <div class="flex flex-col sm:flex-row flex-wrap gap-3 w-full">
      <input type="text" name="candidate_name" placeholder="Candidate Name" value="<?= html_escape($filters['candidate_name'] ?? '') ?>" class="border px-3 py-2 rounded w-full sm:w-48 flex-grow">
      <input type="text" name="job_title" placeholder="Job Title" value="<?= html_escape($filters['job_title'] ?? '') ?>" class="border px-3 py-2 rounded w-full sm:w-48 flex-grow">
      
      <select name="status" class="border px-3 py-2 rounded w-full sm:w-52 flex-grow">
        <option value="">All Status</option>
        <option value="pending" <?= ($filters['status'] ?? '') == 'pending' ? 'selected' : '' ?>>Pending</option>
        <option value="shortlist" <?= ($filters['status'] ?? '') == 'shortlist' ? 'selected' : '' ?>>Shortlist</option>
        <option value="rejected" <?= ($filters['status'] ?? '') == 'rejected' ? 'selected' : '' ?>>Rejected</option>
        <option value="under review" <?= ($filters['status'] ?? '') == 'under review' ? 'selected' : '' ?>>Under Review</option>
        <option value="interview scheduled" <?= ($filters['status'] ?? '') == 'interview scheduled' ? 'selected' : '' ?>>Interview Scheduled</option>
        <option value="offer extended" <?= ($filters['status'] ?? '') == 'offer extended' ? 'selected' : '' ?>>Offer Extended</option>
        <option value="hired" <?= ($filters['status'] ?? '') == 'hired' ? 'selected' : '' ?>>Hired</option>
        <option value="withdraw" <?= ($filters['status'] ?? '') == 'withdraw' ? 'selected' : '' ?>>Withdraw</option>
      </select>
      
      <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
        <input type="date" name="applied_from" value="<?= html_escape($filters['applied_from'] ?? '') ?>" class="border px-3 py-2 rounded w-full sm:w-44">
        <input type="date" name="applied_to" value="<?= html_escape($filters['applied_to'] ?? '') ?>" class="border px-3 py-2 rounded w-full sm:w-44">
      </div>
      
      <div class="flex gap-3 w-full sm:w-auto">
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded flex-1 sm:flex-none">Search</button>
        <a href="<?= base_url('admin/jobs/AdminApplications/applications') ?>" class="bg-gray-200 text-gray-700 px-4 py-2 rounded flex-1 sm:flex-none text-center">Reset</a>
      </div>
    </div>
  </form>

  <div class="bg-white p-3 sm:p-4 rounded shadow overflow-x-auto">
    <!-- Applications Table -->
    <table class="w-full text-sm">
      <thead>
        <tr class="bg-gray-100">
          <th class="px-3 py-2 text-left">Candidate</th>
          <th class="px-3 py-2 text-left">Job</th>
          <th class="px-3 py-2 text-left">Employer</th>
          <th class="px-3 py-2 text-left">Status</th>
          <th class="px-3 py-2 text-left">Applied On</th>
          <th class="px-3 py-2 text-center">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($applications)): ?>
          <tr><td colspan="6" class="text-center py-6 text-gray-500">No applications found.</td></tr>
        <?php else: ?>
          <?php foreach ($applications as $app): ?>
            <tr class="border-b">
              <td class="px-3 py-2"><?= html_escape($app['candidate_name']) ?></td>
              <td class="px-3 py-2"><?= html_escape($app['job_title']) ?></td>
              <td class="px-3 py-2"><?= $app['company_name'] ?></td>
              <td class="px-3 py-2"><?= $app['ApplicationStage'] ?></td>
              <td class="px-3 py-2"><?= $app['created_at'] ?></td>
              <td class="px-3 py-2 text-center"><?= $app['actions'] ?></td>
            </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

  <!-- Record count summary -->
  <div class="flex flex-col sm:flex-row justify-between items-center mt-4 mb-2 text-sm text-gray-600">
    <div>
      Showing <?= ($current_offset + 1) ?> to <?= min($current_offset + $per_page, $total_rows) ?> of <?= $total_rows ?> entries
    </div>
    <div>
      <?= $total_rows ?> record(s) found
    </div>
  </div>

  <!-- Pagination Links -->
  <div class="mt-2">
    <?= $links ?>
  </div>
</div>

<!-- Candidate Modal (unchanged) -->
<div id="candidateModal" class="fixed inset-0 z-[9999] hidden bg-black/60 backdrop-blur-sm flex justify-center items-start p-2 sm:p-4 overflow-y-auto">
  <div class="absolute inset-0" onclick="$('#candidateModal').addClass('hidden')"></div>
  <div class="relative w-full max-w-full sm:max-w-3xl max-h-[95vh] overflow-y-auto bg-white rounded-xl shadow-2xl animate-scale-in p-3 sm:p-4">
    <!-- Header -->
    <div class="bg-gradient-to-r from-blue-600 to-cyan-600 p-4 sm:p-5 pt-8 sm:pt-10 pb-10 sm:pb-12 relative rounded-t-xl">
      <div class="absolute top-2 right-2 sm:top-3 sm:right-3">
        <button onclick="$('#candidateModal').addClass('hidden')" class="text-white hover:text-cyan-200 text-lg w-8 h-8 sm:w-9 sm:h-9 rounded-full flex items-center justify-center hover:bg-white hover:bg-opacity-10 transition">
          <i class="fas fa-times"></i>
        </button>
      </div>
      <div class="text-center">
        <h2 class="text-lg sm:text-xl font-semibold text-white">Candidate Profile</h2>
        <p class="text-cyan-100 text-xs sm:text-sm mt-1">Complete applicant information</p>
      </div>
    </div>

    <!-- Profile Image -->
    <div class="relative flex justify-center -mt-10 sm:-mt-14">
      <div class="w-20 h-20 sm:w-28 sm:h-28 rounded-full border-4 border-white shadow-md overflow-hidden">
        <img id="candidateImage" src="<?= base_url('assets/images/no-image.png') ?>" class="w-full h-full object-cover">
      </div>
    </div>

    <!-- Content -->
    <div class="p-2 sm:p-4 text-sm text-gray-800" id="candidateModalContent">
      Loading...
    </div>
  </div>
</div>

<script>
function toggleLoader(show) {
  if (show) $('#globalLoader').fadeIn(150);
  else $('#globalLoader').fadeOut(150);
}

$(document).ready(function () {
  // View Application Modal (same as before)
  $(document).on('click', '.view-application-btn', function (e) {
    e.preventDefault();
    const id = $(this).data('id');

    $('#candidateModal').removeClass('hidden');
    $('#candidateModalContent').html('<div class="text-center py-4">Loading...</div>');
    $('#candidateImage').attr('src', '<?= base_url("uploads/noimage.png") ?>');

    $.get('<?= base_url("admin/jobs/AdminApplications/view/") ?>' + id, function (response) {
      const res = JSON.parse(response);
      if (res.status === 'success') {
        const c = res.data;
        const profileImage = c.logo ? '<?= base_url() ?>' + c.logo : '<?= base_url("uploads/noimage.png") ?>';
        $('#candidateImage').attr('src', profileImage);

        const html = `
          <div class="flex flex-wrap justify-center gap-3 mb-4">
            <span class="${c.status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'} text-xs font-medium px-3 py-1.5 rounded-full">
              <i class="fas fa-circle text-[8px] mr-1.5 align-middle"></i> ${c.status}
            </span>
            <span class="bg-blue-100 text-blue-800 text-xs font-medium px-3 py-1.5 rounded-full">
              <i class="fas fa-briefcase text-[8px] mr-1.5 align-middle"></i> ${c.total_experience_years ?? 0}y ${c.total_experience_months ?? 0}m
            </span>
            <span class="bg-purple-100 text-purple-800 text-xs font-medium px-3 py-1.5 rounded-full">
              <i class="fas fa-user-tag text-[8px] mr-1.5 align-middle"></i> ${c.designations ?? 'N/A'}
            </span>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
            <p><span class="text-gray-500">Full Name:</span><br><strong>${c.name} ${c.last_name ?? ''}</strong></p>
            <p><span class="text-gray-500">Email:</span><br><strong>${c.email}</strong></p>
            <p><span class="text-gray-500">Mobile:</span><br><strong>${c.mobile}</strong></p>
            <p><span class="text-gray-500">Industry:</span><br><strong>${c.industry_name ?? '-'}</strong></p>
            <p><span class="text-gray-500">Functional Role:</span><br><strong>${c.functional_area ?? '-'}</strong></p>
            <p><span class="text-gray-500">City:</span><br><strong>${c.city_name ?? '-'}</strong></p>
            <p><span class="text-gray-500">Country:</span><br><strong>${c.country_name ?? '-'}</strong></p>
            <p><span class="text-gray-500">Current CTC:</span><br><strong>₹${c.current_ctc ?? 'N/A'}</strong></p>
            <p><span class="text-gray-500">Notice Period:</span><br><strong>${c.notice_period ?? 'N/A'}</strong></p>
            <p><span class="text-gray-500">Resume Headline:</span><br><strong>${c.resume_headline ?? 'N/A'}</strong></p>
            <p><span class="text-gray-500">LinkedIn:</span><br><a href="${c.linkedinProfile ?? '#'}" target="_blank" class="text-blue-600 hover:underline">${c.linkedinProfile ?? 'N/A'}</a></p>
            <p><span class="text-gray-500">Portfolio:</span><br><a href="${c.portfolioUrl ?? '#'}" target="_blank" class="text-blue-600 hover:underline">${c.portfolioUrl ?? 'N/A'}</a></p>
            <p><span class="text-gray-500">Resume:</span><br>${c.resume ? `<a href="<?= base_url() ?>${c.resume}" class="text-blue-600 underline" target="_blank">Download Resume</a>` : 'Not uploaded'}</p>
            <p><span class="text-gray-500">Last Login:</span><br><strong>${c.last_login ?? 'N/A'}</strong></p>
          </div>

          <div class="mt-5 text-sm border-t pt-4 space-y-3">
            <p><span class="text-gray-500">Skills:</span><br><strong>${c.skills?.length ? c.skills.join(', ') : 'N/A'}</strong></p>
            <p><span class="text-gray-500">Preferred Locations:</span><br><strong>${c.preferred_locations?.length ? c.preferred_locations.join(', ') : 'N/A'}</strong></p>
            <p><span class="text-gray-500">About:</span><br><strong>${c.about ?? 'N/A'}</strong></p>
            <p><span class="text-gray-500">Objective:</span><br><strong>${c.objective ?? 'N/A'}</strong></p>
            <p><span class="text-gray-500">Created At:</span> <strong>${c.created_at}</strong></p>
            <p><span class="text-gray-500">Last Updated:</span> <strong>${c.updated_at ?? 'N/A'}</strong></p>
          </div>
        `;

        let timelineHtml = '';
        if (Array.isArray(c.logs) && c.logs.length) {
          timelineHtml += `
            <div class="bg-gray-50 rounded-xl p-4 mt-6">
              <h3 class="text-sm font-semibold mb-3 flex items-center">
                <i class="fas fa-history text-blue-600 mr-2"></i> Application Timeline
              </h3>
              <div class="overflow-x-auto">
                <div class="flex items-center space-x-6 min-w-max pl-1">
          `;
          const redStages = ['Withdraw', 'Rejected', 'Canceled'];
          c.logs.forEach((log, index) => {
            const isRed = redStages.includes(log.stage);
            const dotColor = isRed ? 'bg-red-600' : 'bg-blue-600';
            const textColor = isRed ? 'text-red-600' : 'text-gray-700';
            timelineHtml += `
              <div class="text-center relative">
                <div class="w-4 h-4 rounded-full ${dotColor} mx-auto mb-1"></div>
                <div class="text-xs font-medium ${textColor} whitespace-nowrap">${log.stage}</div>
                <div class="text-[10px] text-gray-400">${log.performed_by}</div>
                <div class="text-[10px] text-gray-400">${log.created_at}</div>
              </div>
            `;
            if (index !== c.logs.length - 1) timelineHtml += `<div class="w-10 h-0.5 bg-blue-300"></div>`;
          });
          timelineHtml += `</div></div></div>`;
        }

        $('#candidateModalContent').html(html + timelineHtml);
      } else {
        $('#candidateModalContent').html('<div class="text-red-500 text-center">Candidate not found.</div>');
      }
    }).fail(function () {
      $('#candidateModalContent').html('<div class="text-red-500 text-center">Failed to load candidate data.</div>');
    });
  });
});
</script>