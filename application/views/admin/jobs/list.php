<!-- Global Loader -->
<div id="globalLoader" class="fixed inset-0 z-[9999] bg-black/50 hidden flex items-center justify-center">
    <div class="flex items-center gap-2 bg-white px-4 py-3 rounded shadow text-gray-800 text-xs">
        <div class="loader-spinner w-4 h-4 border-2 border-blue-600 border-t-transparent rounded-full animate-spin"></div>
        Processing...
    </div>
</div>

<!-- Compact Filters -->
<div class="bg-white p-3 mb-3 rounded border shadow-sm">
    <form method="GET" action="<?= base_url('admin/jobs/AdminJobs/jobs') ?>" id="filter-form" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-2">
        <input type="text" name="job_title" placeholder="Job Title" value="<?= html_escape($filters['job_title'] ?? '') ?>" class="text-xs px-3 py-1.5 border rounded w-full">
        <select name="status" class="text-xs px-3 py-1.5 border rounded w-full">
            <option value="">All Status</option>
            <?php foreach ($statuses as $status): ?>
                <option value="<?= $status ?>" <?= ($filters['status'] ?? '') === $status ? 'selected' : '' ?>><?= ucfirst($status) ?></option>
            <?php endforeach; ?>
        </select>
        <select name="is_paid" class="text-xs px-3 py-1.5 border rounded w-full">
            <option value="">Paid/Free</option>
            <option value="1" <?= ($filters['is_paid'] ?? '') == '1' ? 'selected' : '' ?>>Paid</option>
            <option value="0" <?= ($filters['is_paid'] ?? '') === '0' ? 'selected' : '' ?>>Free</option>
        </select>
        <input type="date" name="created_from" value="<?= html_escape($filters['created_from'] ?? '') ?>" class="text-xs px-3 py-1.5 border rounded w-full">
        <input type="date" name="created_to" value="<?= html_escape($filters['created_to'] ?? '') ?>" class="text-xs px-3 py-1.5 border rounded w-full">
        <div class="flex gap-1.5 sm:col-span-2 md:col-span-3 lg:col-span-1">
            <button type="submit" class="text-xs bg-blue-600 text-white px-3 py-1.5 rounded hover:bg-blue-700 flex-1">
                <i class="fas fa-search mr-1"></i> Search
            </button>
            <a href="<?= base_url('admin/jobs/AdminJobs/jobs') ?>" class="text-xs bg-gray-200 text-gray-700 px-3 py-1.5 rounded hover:bg-gray-300 text-center">
                <i class="fas fa-redo"></i>
            </a>
        </div>
    </form>
</div>

<!-- Job List Table -->
<div class="overflow-x-auto bg-white rounded border shadow-sm">
    <table class="min-w-full text-xs">
        <thead class="text-xs bg-gray-50 text-gray-500">
            <tr>
                <th class="py-2 px-3 text-left">Job Title</th>
                <th class="py-2 px-3 text-left">Company</th>
                <th class="py-2 px-3 text-left">Exp</th>
                <th class="py-2 px-3 text-left">Salary</th>
                <th class="py-2 px-3 text-left">Status</th>
                <th class="py-2 px-3 text-center">Applied</th>
                <th class="py-2 px-3 text-left">Posted</th>
                <th class="py-2 px-3 text-center">Actions</th>
            </tr>
        </thead>
        <tbody class="text-gray-700">
            <?php if (empty($jobs)): ?>
                <tr><td colspan="8" class="text-center py-6 text-gray-500">No jobs found.</td></tr>
            <?php else: ?>
                <?php foreach ($jobs as $job): ?>
                    <tr class="border-b">
                        <td class="py-2 px-3"><div class="font-medium text-gray-800 truncate max-w-[150px]" title="<?= html_escape($job['job_title']) ?>"><?= html_escape($job['job_title']) ?></div></td>
                        <td class="py-2 px-3"><?= $job['company_name'] ?></td>
                        <td class="py-2 px-3"><?= $job['experience'] ?></td>
                        <td class="py-2 px-3"><?= $job['salary'] ?></td>
                        <td class="py-2 px-3"><?= $job['status'] ?></td>
                        <td class="py-2 px-3 text-center"><span class="inline-flex items-center justify-center w-6 h-6 bg-blue-100 text-blue-700 rounded-full text-xs"><?= $job['applied_count'] ?></span></td>
                        <td class="py-2 px-3"><div class="text-xs text-gray-500"><?= $job['created_at'] ?></div></td>
                        <td class="py-2 px-3 text-center"><?= $job['actions'] ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>


<!-- Record count summary -->
<div class="flex justify-between items-center mt-4 mb-2 text-sm text-gray-600">
    <div>
        Showing <?= ($current_offset + 1) ?> to <?= min($current_offset + $per_page, $total_rows) ?> of <?= $total_rows ?> entries
    </div>
    <div>
        <?= $total_rows ?> record(s) found
    </div>
</div>

<!-- Pagination Links -->
<div class="mt-4">
    <?= $links ?>
</div>

<!-- Status Modal -->
<div id="jobStatusModal" class="fixed inset-0 z-50 hidden bg-black/50 flex items-center justify-center p-4">
    <div class="bg-white w-full max-w-xs md:max-w-sm p-4 md:p-5 rounded-lg shadow-lg">
        <h2 class="text-base font-semibold mb-3 text-gray-800">Change Status</h2>
        <form id="statusChangeForm">
            <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
            <input type="hidden" name="job_id" id="modal-job-id">
            <div class="mb-3">
                <label class="block mb-1.5 text-sm font-medium text-gray-700">Select Status</label>
                <select name="status" required class="w-full border text-sm px-3 py-2 rounded" id="modal-status">
                    <option value="">Choose status</option>
                    <option value="active">Active</option>
                    <option value="under-review">Under Review</option>
                    <option value="rejected">Rejected</option>
                    <option value="suspended">Suspended</option>
                </select>
            </div>
            <div class="mb-4" id="modal-reason-wrap" style="display:none;">
                <label class="block mb-1.5 text-sm font-medium text-gray-700">Reason (Optional)</label>
                <textarea name="reason" rows="2" class="w-full border text-sm px-3 py-2 rounded"></textarea>
            </div>
            <div class="flex justify-end space-x-2">
                <button type="button" class="close-modal text-sm px-3 py-1.5 bg-gray-200 rounded">Cancel</button>
                <button type="submit" class="text-sm px-3 py-1.5 bg-blue-600 text-white rounded">Update</button>
            </div>
        </form>
    </div>
</div>

<!-- View Job Modal -->
<div id="jobViewModal" class="fixed inset-0 z-[60] hidden bg-black/50">
    <div class="flex items-center justify-center min-h-screen p-2 md:p-4">
        <div class="bg-white w-full max-w-md md:max-w-2xl max-h-[90vh] rounded-lg shadow-xl flex flex-col">
            <div class="flex items-center justify-between p-4 border-b">
                <h3 class="text-sm md:text-base font-semibold text-gray-800">Job Details</h3>
                <button type="button" class="close-view-modal text-gray-500 hover:text-gray-700"><i class="fas fa-times text-lg"></i></button>
            </div>
            <div id="job-details-content" class="flex-1 overflow-y-auto p-4"><div class="text-center text-gray-400 py-8">Loading job details...</div></div>
            <div class="border-t p-3 text-right"><button type="button" class="close-view-modal text-xs md:text-sm px-4 py-2 bg-gray-200 rounded">Close</button></div>
        </div>
    </div>
</div>

<script>
$(document).ready(function () {
    // View Job Modal
    $(document).on('click', '.view-job-btn', function () {
        const jobId = $(this).data('id');
        $('#job-details-content').html('<div class="text-center py-8"><div class="spinner-border text-blue-600 mx-auto"></div><p class="text-gray-500 mt-2">Loading...</p></div>');
        $('#jobViewModal').removeClass('hidden').fadeIn();
        $('body').css('overflow', 'hidden');
        $.get('<?= site_url("admin/jobs/AdminJobs/ajax_view_job/") ?>' + jobId, function (html) {
            $('#job-details-content').html(html);
        }).fail(function() {
            $('#job-details-content').html('<div class="text-red-500 text-center py-8">Failed to load job details</div>');
        });
    });

    // Close View Modal
    $(document).on('click', '.close-view-modal', function () {
        $('#jobViewModal').fadeOut(function() { $(this).addClass('hidden'); $('body').css('overflow', 'auto'); });
    });
    $('#jobViewModal').on('click', function(e) { if ($(e.target).is('#jobViewModal')) $(this).fadeOut(function() { $(this).addClass('hidden'); $('body').css('overflow', 'auto'); }); });

    // Status Modal
    $(document).on('click', '.open-status-modal', function () {
        $('#modal-job-id').val($(this).data('id'));
        $('#modal-status').val('');
        $('textarea[name="reason"]').val('');
        $('#modal-reason-wrap').hide();
        $('#jobStatusModal').removeClass('hidden').fadeIn();
    });
    $(document).on('click', '.close-modal', function () { $('#jobStatusModal').fadeOut(function() { $(this).addClass('hidden'); }); });
    $('#modal-status').on('change', function () { $('#modal-reason-wrap').toggle($(this).val() === 'rejected'); });

    // Submit Status Change
    $('#statusChangeForm').on('submit', function (e) {
        e.preventDefault();
        var btn = $(this).find('button[type="submit"]');
        var orig = btn.text();
        btn.prop('disabled', true).text('Updating...');
        $.ajax({
            url: '<?= base_url("admin/jobs/AdminJobs/ajax_update_status") ?>',
            type: "POST",
            data: $(this).serialize(),
            success: function (res) {
                try { var resp = typeof res === 'string' ? JSON.parse(res) : res;
                    if (resp.csrf_token) $('input[name="<?= $this->security->get_csrf_token_name(); ?>"]').val(resp.csrf_token);
                    if (resp.status === 'success') {
                        $('#jobStatusModal').fadeOut(function() { $(this).addClass('hidden'); });
                        location.reload();
                    } else showToast(resp.message || 'Failed', 'error');
                } catch(e) { showToast('Error processing response', 'error'); }
            },
            error: function() { showToast('Network error, please try again', 'error'); },
            complete: function() { btn.prop('disabled', false).text(orig); }
        });
    });

    // Delete Job
    $(document).on('click', '.delete-job-btn', function (e) {
        e.preventDefault();
        if (confirm('Are you sure you want to delete this job? This action cannot be undone.')) {
            var jobId = $(this).data('id');
            $.ajax({
                url: '<?= base_url("admin/jobs/AdminJobs/soft_delete_job") ?>',
                type: "POST",
                data: { job_id: jobId, '<?= $this->security->get_csrf_token_name(); ?>': $('input[name="<?= $this->security->get_csrf_token_name(); ?>"]').val() },
                beforeSend: function() { $('#globalLoader').fadeIn(); },
                success: function (res) {
                    var result = typeof res === 'string' ? JSON.parse(res) : res;
                    if (result.csrf_token) $('input[name="<?= $this->security->get_csrf_token_name(); ?>"]').val(result.csrf_token);
                    if (result.status === 'success') { showToast('Job deleted successfully', 'success'); location.reload(); }
                    else showToast(result.message || 'Failed to delete job', 'error');
                },
                complete: function() { $('#globalLoader').fadeOut(); }
            });
        }
    });

    function showToast(message, type) {
        var toast = $('<div class="fixed bottom-4 right-4 px-4 py-2 rounded shadow-lg text-white text-sm z-[9999] animate-slide-up">' + message + '</div>');
        toast.addClass(type === 'success' ? 'bg-green-600' : (type === 'error' ? 'bg-red-600' : 'bg-blue-600'));
        $('body').append(toast);
        setTimeout(function() { toast.remove(); }, 3000);
    }
});

// CSS for animations
var style = document.createElement('style');
style.textContent = `
    @keyframes slide-up { from { transform: translateY(100%); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
    .animate-slide-up { animation: slide-up 0.3s ease-out; }
    .spinner-border { width: 1.5rem; height: 1.5rem; border: 2px solid currentColor; border-right-color: transparent; border-radius: 50%; animation: spinner-border .75s linear infinite; display: inline-block; }
    @keyframes spinner-border { to { transform: rotate(360deg); } }
    .loader-spinner { animation: spin 0.6s linear infinite; }
    @keyframes spin { to { transform: rotate(360deg); } }
`;
document.head.appendChild(style);
</script>