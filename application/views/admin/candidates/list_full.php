<!-- Global Loader -->
<div id="globalLoader" class="fixed inset-0 z-[9999] bg-black bg-opacity-50 hidden flex items-center justify-center">
  <div class="flex items-center gap-3 bg-white px-6 py-4 rounded-md shadow text-gray-800 text-sm font-medium">
    <span class="loader-spinner w-5 h-5 border-2 border-blue-600 border-t-transparent rounded-full"></span>
    Processing...
  </div>
</div>

<!-- Main Container -->
<div class="bg-white rounded-xl shadow-md">
  <!-- Filter Form (unchanged) -->
  <form method="GET" action="<?= base_url('admin/candidates/AdminCandidate/candidates') ?>" id="filter-form" class="mb-5 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
    <input type="text" name="name" placeholder="Name" value="<?= html_escape($filters['name'] ?? '') ?>" class="border px-3 py-2 rounded-md w-full">
    <input type="text" name="email" placeholder="Email" value="<?= html_escape($filters['email'] ?? '') ?>" class="border px-3 py-2 rounded-md w-full">
    <input type="text" name="mobile" placeholder="Mobile" value="<?= html_escape($filters['mobile'] ?? '') ?>" class="border px-3 py-2 rounded-md w-full">

    <select name="status" class="border px-3 py-2 rounded-md w-full">
      <option value="">All Status</option>
      <option value="active" <?= ($filters['status'] ?? '') == 'active' ? 'selected' : '' ?>>Active</option>
      <option value="inactive" <?= ($filters['status'] ?? '') == 'inactive' ? 'selected' : '' ?>>Inactive</option>
      <option value="under_review" <?= ($filters['status'] ?? '') == 'under_review' ? 'selected' : '' ?>>Under Review</option>
    </select>

    <input type="text" name="date_range" id="date_range" placeholder="Date range (From - To)" value="<?= html_escape($filters['date_range'] ?? '') ?>" class="border px-3 py-2 rounded-md w-full" autocomplete="off">

    <select name="is_verified" class="border px-3 py-2 rounded-md w-full">
      <option value="">All Email Status</option>
      <option value="1" <?= ($filters['is_verified'] ?? '') === '1' ? 'selected' : '' ?>>Verified</option>
      <option value="0" <?= ($filters['is_verified'] ?? '') === '0' ? 'selected' : '' ?>>Not Verified</option>
    </select>

    <select name="industry_id" class="border px-3 py-2 rounded-md w-full">
      <option value="">All Industries</option>
      <?php foreach ($industries as $ind): ?>
        <option value="<?= $ind['industry_id'] ?>" <?= ($filters['industry_id'] ?? '') == $ind['industry_id'] ? 'selected' : '' ?>>
          <?= html_escape($ind['industry_name']) ?>
        </option>
      <?php endforeach; ?>
    </select>

    <div class="flex gap-2 col-span-full sm:col-span-1">
      <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-semibold">
        Search
      </button>
      <a href="<?= base_url('admin/candidates/AdminCandidate/candidates') ?>" class="w-full bg-gray-200 hover:bg-gray-300 text-gray-800 px-4 py-2 rounded-md text-sm text-center">
        Reset
      </a>
    </div>
  </form>

  <!-- Count display -->
  <?php if (isset($total_candidates)): ?>
    <div class="mb-4 text-sm text-gray-600">
      Showing <strong><?= $total_candidates ?></strong> candidate(s)
      <?php if (!empty(array_filter($filters))): ?>
        <span class="text-blue-600">(filtered)</span>
      <?php endif; ?>
    </div>
  <?php endif; ?>

  <!-- Candidate List Table -->
  <div class="overflow-x-auto">
    <table class="min-w-full text-sm text-left border rounded-lg overflow-hidden">
      <thead class="bg-gray-100 text-gray-700 font-semibold">
        <tr>
          <th class="px-4 py-3">Name</th>
          <th class="px-4 py-3">Email</th>
          <th class="px-4 py-3">Mobile</th>
          <th class="px-4 py-3">Experience</th>
          <th class="px-4 py-3">Location</th>
          <th class="px-4 py-3">Status</th>
          <th class="px-4 py-3">Created</th>
          <th class="px-4 py-3 text-center">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($candidates)): ?>
          <tr><td colspan="8" class="text-center py-6 text-gray-500">No candidates found.</td></tr>
        <?php else: ?>
          <?php foreach ($candidates as $c): ?>
            <tr class="border-b">
              <td class="px-4 py-3"><?= html_escape($c['name']) ?></td>
              <td class="px-4 py-3"><?= $c['email'] ?></td>
              <td class="px-4 py-3"><?= html_escape($c['mobile']) ?></td>
              <td class="px-4 py-3"><?= $c['experience'] ?></td>
              <td class="px-4 py-3"><?= html_escape($c['location']) ?></td>
              <td class="px-4 py-3"><?= $c['status'] ?></td>
              <td class="px-4 py-3"><?= $c['created'] ?></td>
              <td class="px-4 py-3 text-center"><?= $c['actions'] ?></td>
            </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

  <!-- Tailwind Pagination Links (replaces $links) -->
  <div class="mt-6">
    <?= $links ?>
  </div>
</div>

<!-- Candidate Modal -->
<div id="candidateModal" class="fixed inset-0 z-[9999] hidden bg-black/60 backdrop-blur-sm flex justify-center items-start p-2 overflow-y-auto">
  <div class="absolute inset-0" onclick="$('#candidateModal').addClass('hidden')"></div>
  <div class="relative w-full max-w-3xl max-h-[95vh] overflow-y-auto bg-white rounded-xl shadow-2xl animate-scale-in p-4">
    <div class="bg-gradient-to-r from-blue-600 to-cyan-600 p-5 pt-10 pb-12 relative rounded-t-xl">
      <div class="absolute top-3 right-3">
        <button onclick="$('#candidateModal').addClass('hidden')" class="text-white hover:text-cyan-200 text-lg w-9 h-9 rounded-full flex items-center justify-center hover:bg-white hover:bg-opacity-10 transition">
          <i class="fas fa-times"></i>
        </button>
      </div>
      <div class="text-center">
        <h2 class="text-xl font-semibold text-white">Candidate Profile</h2>
        <p class="text-cyan-100 text-sm mt-1">Complete applicant information</p>
      </div>
    </div>
    <div class="relative flex justify-center -mt-14">
      <div class="w-28 h-28 rounded-full border-4 border-white shadow-md overflow-hidden">
        <img id="candidateImage" src="<?= base_url('assets/images/no-image.png') ?>" class="w-full h-full object-cover">
      </div>
    </div>
    <div class="p-4 text-sm text-gray-800" id="candidateModalContent">
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
  // Date range picker
  $('input[name="date_range"]').daterangepicker({
    autoUpdateInput: false,
    locale: { cancelLabel: 'Clear' }
  });
  $('input[name="date_range"]').on('apply.daterangepicker', function(ev, picker) {
    $(this).val(picker.startDate.format('YYYY-MM-DD') + ' - ' + picker.endDate.format('YYYY-MM-DD'));
  });
  $('input[name="date_range"]').on('cancel.daterangepicker', function(ev, picker) {
    $(this).val('');
  });

  // View candidate modal – NOW WITH TAILWIND VERIFIED BADGE
  $(document).on('click', '.view-candidate', function(e) {
    e.preventDefault();
    const id = $(this).data('id');
    $('#candidateModal').removeClass('hidden');
    $('#candidateModalContent').html('<div class="text-center py-4">Loading...</div>');
    $('#candidateImage').attr('src', '<?= base_url("uploads/noimage.png") ?>');
    
    toggleLoader(true);

    $.get('<?= base_url("admin/candidates/AdminCandidate/get_candidate_json/") ?>' + id, function(response) {
      const res = JSON.parse(response);
      if (res.status === 'success') {
        const c = res.data;
        const profileImage = c.logo ? '<?= base_url() ?>' + c.logo : '<?= base_url("uploads/noimage.png") ?>';
        $('#candidateImage').attr('src', profileImage);

        // Verified badge using Tailwind classes (FIXED)
        const verifiedBadge = c.is_verified == 1
          ? '<span class="bg-green-500 text-white text-xs font-medium px-3 py-1.5 rounded-full"><i class="fas fa-check-circle mr-1"></i> Verified</span>'
          : '<span class="bg-gray-500 text-white text-xs font-medium px-3 py-1.5 rounded-full"><i class="fas fa-times-circle mr-1"></i> Not Verified</span>';

        const html = `<div class="flex flex-wrap justify-center gap-3 mb-4">
          <span class="${c.status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'} text-xs font-medium px-3 py-1.5 rounded-full">
            <i class="fas fa-circle text-[8px] mr-1.5 align-middle"></i> ${c.status}
          </span>
          <span class="bg-blue-100 text-blue-800 text-xs font-medium px-3 py-1.5 rounded-full">
            <i class="fas fa-briefcase text-[8px] mr-1.5 align-middle"></i> ${c.total_experience_years}y ${c.total_experience_months}m
          </span>
          <span class="bg-purple-100 text-purple-800 text-xs font-medium px-3 py-1.5 rounded-full">
            <i class="fas fa-user-tag text-[8px] mr-1.5 align-middle"></i> ${c.designations ?? 'N/A'}
          </span>
          ${verifiedBadge}
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
          <p><span class="text-gray-500">Skills:</span><br><strong>${c.skills.length ? c.skills.join(', ') : 'N/A'}</strong></p>
          <p><span class="text-gray-500">Preferred Locations:</span><br><strong>${c.preferred_locations.length ? c.preferred_locations.join(', ') : 'N/A'}</strong></p>
          <p><span class="text-gray-500">About:</span><br><strong>${c.about ?? 'N/A'}</strong></p>
          <p><span class="text-gray-500">Objective:</span><br><strong>${c.objective ?? 'N/A'}</strong></p>
          <p><span class="text-gray-500">Created At:</span> <strong>${c.created_at}</strong></p>
          <p><span class="text-gray-500">Last Updated:</span> <strong>${c.updated_at ?? 'N/A'}</strong></p>
        </div>`;
        $('#candidateModalContent').html(html);
      } else {
        $('#candidateModalContent').html('<div class="text-red-500 text-center">Candidate not found.</div>');
      }
    }).fail(function() {
      $('#candidateModalContent').html('<div class="text-red-500 text-center">Failed to load candidate data.</div>');
    }).always(function() {
      toggleLoader(false);
    });
  });
});
</script>

<!-- Date Range Picker CSS/JS -->
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/moment/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>