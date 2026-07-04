<!-- Loader -->
<div id="globalLoader" class="fixed inset-0 z-[9999] bg-black bg-opacity-50 hidden flex items-center justify-center">
  <div class="flex items-center gap-3 bg-white px-6 py-4 rounded-md shadow text-gray-800 text-sm font-medium">
    <span class="loader-spinner w-5 h-5 border-2 border-blue-600 border-t-transparent rounded-full"></span>
    Processing...
  </div>
</div>

<!-- Filters -->
<div class="bg-white p-4 mb-4 rounded shadow-sm">
  <!-- Filters -->
<form id="job-filter-form" class="bg-white p-4 mb-4 rounded shadow-sm">
  <div class="flex flex-wrap gap-3">
    <!-- Job Title -->
    <input type="text" name="job_title" placeholder="Job Title"
      value="<?= html_escape($filters['job_title']) ?>"
      class="px-3 py-2 border rounded w-48">

    <!-- Status -->
    <select name="status" class="px-3 py-2 border rounded w-40">
      <option value="">All Status</option>
      <?php foreach ($statuses as $status): ?>
        <option value="<?= $status['status'] ?>" <?= $filters['status'] === $status['status'] ? 'selected' : '' ?>>
          <?= ucfirst($status['status']) ?>
        </option>
      <?php endforeach; ?>
    </select>

    <!-- Industry -->
    <select name="industry_id" class="px-3 py-2 border rounded w-44">
      <option value="">All Industries</option>
      <?php foreach ($industries as $industry): ?>
        <option value="<?= $industry['id'] ?>" <?= $filters['industry_id'] == $industry['id'] ? 'selected' : '' ?>>
          <?= $industry['name'] ?>
        </option>
      <?php endforeach; ?>
    </select>



    <!-- Job Type -->
    <select name="job_type" class="px-3 py-2 border rounded w-40">
      <option value="">All Job Types</option>
      <option value="Full-time" <?= $filters['job_type'] == 'Full-time' ? 'selected' : '' ?>>Full-time</option>
      <option value="Part-time" <?= $filters['job_type'] == 'Part-time' ? 'selected' : '' ?>>Part-time</option>
      <option value="Freelance" <?= $filters['job_type'] == 'Freelance' ? 'selected' : '' ?>>Freelance</option>
      <option value="Internship" <?= $filters['job_type'] == 'Internship' ? 'selected' : '' ?>>Internship</option>
    </select>

    <!-- Is Paid -->
    <select name="is_paid" class="px-3 py-2 border rounded w-32">
      <option value="">Paid/Free</option>
      <option value="1" <?= $filters['is_paid'] == '1' ? 'selected' : '' ?>>Paid</option>
      <option value="0" <?= $filters['is_paid'] === '0' ? 'selected' : '' ?>>Free</option>
    </select>

    <!-- Created Date From -->
    <input type="date" name="created_from" value="<?= html_escape($filters['created_from']) ?>"
      class="px-3 py-2 border rounded w-44" placeholder="From Date">

    <!-- Created Date To -->
    <input type="date" name="created_to" value="<?= html_escape($filters['created_to']) ?>"
      class="px-3 py-2 border rounded w-44" placeholder="To Date">

    <!-- Action Buttons -->
    <div class="flex gap-2 items-center">
      <button id="search-job-btn" type="button" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
        Search
      </button>

      <button id="reset-job-btn" type="button" class="bg-gray-200 text-gray-700 px-4 py-2 rounded hover:bg-gray-300">
        Reset
      </button>
    </div>
  </div>
</form>

</div>

<!-- AJAX Content Target -->
<div id="employer-jobs-section">
 <h2 class="text-xl font-semibold text-gray-800 mb-2 flex items-center gap-4">
  Employer: <?= html_escape($employer['company_name']) ?>

  <?php
    $status = $employer['status'];
    $is_deleted = $employer['is_deleted'] ?? 0;

    if ($is_deleted == 1) {
        echo '<span class="px-2 py-1 text-xs rounded-full bg-red-200 text-red-800 font-medium">Deleted</span>';
    } else {
        switch ($status) {
            case 'active':
                echo '<span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-700 font-medium">Active</span>';
                break;
            case 'inactive':
                echo '<span class="px-2 py-1 text-xs rounded-full bg-gray-200 text-gray-700 font-medium">Inactive</span>';
                break;
            case 'under_review':
                echo '<span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800 font-medium">Under Review</span>';
                break;
            case 'rejected':
                echo '<span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-700 font-medium">Rejected</span>';
                break;
            default:
                echo '<span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-600 font-medium">' . ucfirst($status) . '</span>';
                break;
        }
    }
  ?>
</h2>

<div class="overflow-x-auto">
 <table id="employer-jobs-table" class="min-w-full text-sm text-left text-gray-700">
  <thead class="text-xs uppercase text-gray-500 border-b">
    <tr>
      <th>Job Title</th>
      <th>Experience</th>
      <th>Salary</th>
      <th>Status</th>
      <th class="text-center">Applied</th>
      <th>Created At</th>
      <th>Actions</th>
    </tr>
  </thead>
</table>
</div>
</div>


<!-- Modal -->
<div id="jobStatusModal" class="fixed inset-0 z-50 hidden bg-black bg-opacity-50 flex items-center justify-center p-4" >
  <div class="bg-white w-full max-w-md md:w-96 p-6 rounded-lg shadow-lg relative">
    <h2 class="text-lg font-semibold mb-4 text-gray-800">Change Job Status</h2>

    <form id="statusChangeForm">
		<input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>"
           value="<?= $this->security->get_csrf_hash(); ?>">
      <input type="hidden" name="job_id" id="modal-job-id">

      <!-- Status Select -->
      <div class="mb-4">
        <label class="block mb-1 font-medium text-gray-700">Select Status</label>
        <select name="status" required class="w-full border border-gray-300 px-3 py-2 rounded focus:outline-none focus:ring-2 focus:ring-blue-500" id="modal-status">
          <option value="">Choose status</option>
          <option value="active">Active</option>
          <option value="under-review">Under Review</option>
          <option value="rejected">Rejected</option>
          <option value="suspended">Suspended</option>
        </select>
      </div>

      <!-- Reason -->
      <div class="mb-4" id="modal-reason-wrap" style="display:none;">
        <label class="block mb-1 font-medium text-gray-700">Reason (Optional)</label>
        <textarea name="reason" class="w-full border border-gray-300 px-3 py-2 rounded focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Enter reason..."></textarea>
      </div>

      <!-- Actions -->
      <div class="flex justify-end space-x-2">
        <button type="button" class="close-modal px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded">Cancel</button>
        <button type="submit" class="px-4 py-2 bg-blue-600 text-white hover:bg-blue-700 rounded">Update</button>
      </div>
    </form>
  </div>
</div>

<!-- View Job Modal -->
<div id="jobViewModal" class="fixed inset-0 z-50 hidden bg-black bg-opacity-50 flex items-center justify-center p-4">
  <div class="bg-white w-full max-w-2xl p-6 rounded-lg shadow-lg relative">
    <h2 class="text-lg font-semibold mb-4 text-gray-800">Job Details</h2>

    <div id="job-details-content" class="text-sm text-gray-700 space-y-2">
      <div class="text-center text-gray-400">Loading...</div>
    </div>

    <div class="mt-4 text-right">
      <button type="button" class="close-view-modal px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded">Close</button>
    </div>
  </div>
</div>

<script>
function toggleLoader(show) {
  $('#globalLoader').fadeToggle(show);
}

let jobTable;

function initJobDataTable() {
  jobTable = $('#employer-jobs-table').DataTable({
    processing: true,
    serverSide: true,
    searching: false,
    ordering: true, // ✅ Enable sorting
   ajax: {
	  url: '<?= site_url("admin/employers/AdminEmployer/employer_jobs_ajax/$employer_id") ?>',
	  type: 'GET', // ✅ GET request
	  data: function (d) {
		const filters = $('#job-filter-form').serializeArray();
		filters.forEach(f => d[f.name] = f.value);
	  },
	  beforeSend: function () { toggleLoader(true); },
	  complete: function () { toggleLoader(false); }
	},
    columns: [
      { data: 'job_title', orderable: true },
      { data: 'experience', orderable: false }, // computed field
      { data: 'salary', orderable: false },     // computed field
      { data: 'status', orderable: true },
      { data: 'applied_count', className: "text-center", orderable: true },
      { data: 'created_at', orderable: true },
      { data: 'actions', orderable: false, searchable: false }
    ],
    order: [[5, 'desc']] // Default sort: created_at desc
  });
}


$(document).ready(function () {
  initJobDataTable();

  $('#search-job-btn').on('click', function () {
    jobTable.ajax.reload();
  });
  
  $('#reset-job-btn').on('click', function () {
	  // Reset all inputs/selects
	  $('#job-filter-form')[0].reset();
	  // Reload DataTable
	  jobTable.ajax.reload();
	});

});

// View Job Modal
$(document).on('click', '.view-job-btn', function () {
  const jobId = $(this).data('id');
  $('#jobViewModal').removeClass('hidden');
  $('#job-details-content').html('<div class="text-center text-gray-400">Loading...</div>');
  $.get('<?= base_url("admin/employers/AdminEmployer/ajax_view_job/") ?>' + jobId, function (res) {
    $('#job-details-content').html(res);
  }).fail(function () {
    $('#job-details-content').html('<div class="text-red-500 text-center">Failed to load job details.</div>');
  });
});

$(document).on('click', '.close-view-modal', function () {
  $('#jobViewModal').addClass('hidden');
});

// Status Modal
$(document).on('click', '.open-status-modal', function () {
  $('#modal-job-id').val($(this).data('id'));
  $('#modal-status').val('');
  $('textarea[name="reason"]').val('');
  $('#modal-reason-wrap').hide();
  $('#jobStatusModal').removeClass('hidden').fadeIn();
});

$(document).on('click', '.close-modal', function () {
  $('#jobStatusModal').fadeOut(function () {
    $(this).addClass('hidden');
  });
});

$(document).on('change', '#modal-status', function () {
  $('#modal-reason-wrap').toggle(this.value === 'rejected');
});

$('#statusChangeForm').on('submit', function (e) {
    e.preventDefault();

    let csrfName = '<?= $this->security->get_csrf_token_name(); ?>';
    let csrfHash = $('input[name="<?= $this->security->get_csrf_token_name(); ?>"]').val();

    let formData = $(this).serializeArray();
    formData.push({ name: csrfName, value: csrfHash });

    $.ajax({
        url: '<?= base_url("admin/jobs/AdminJobs/ajax_update_status") ?>',
        type: "POST",
        data: formData,
        beforeSend: function () {
            $('#statusChangeForm button[type="submit"]').text('Updating...');
        },
        success: function (res) {
            let response = JSON.parse(res);

            // Update CSRF token
            if (response.csrf_token) {
                $('input[name="' + csrfName + '"]').val(response.csrf_token);
            }

            if (response.status === 'success') {
                $('#jobStatusModal').fadeOut();
                jobTable.ajax.reload();
            } else {
                alert(response.message);
            }

            $('#statusChangeForm button[type="submit"]').text('Update');
        },
        error: function () {
            $('#statusChangeForm button[type="submit"]').text('Update');
            alert("Failed to update status.");
        }
    });
});


$(document).on('click', '.delete-job-btn', function (e) {
    e.preventDefault();

    const jobId = $(this).data('id');

    if (!confirm("Are you sure you want to delete this job?")) return;

    let csrfName = '<?= $this->security->get_csrf_token_name(); ?>';
    let csrfHash = $('input[name="<?= $this->security->get_csrf_token_name(); ?>"]').val();

    $.ajax({
        url: '<?= base_url("admin/jobs/AdminJobs/soft_delete_job") ?>',
        type: "POST",
        data: {
            job_id: jobId,
            [csrfName]: csrfHash
        },
        success: function (res) {
            const result = JSON.parse(res);

            // Update CSRF Token
            if (result.csrf_token) {
                $('input[name="' + csrfName + '"]').val(result.csrf_token);
            }

            if (result.status === 'success') {
                alert("Job deleted successfully.");
                jobTable.ajax.reload();
            } else {
                alert(result.message || "Failed to delete job.");
            }
        },
        error: function () {
            alert("Server error, please try again.");
        }
    });
});

</script>
