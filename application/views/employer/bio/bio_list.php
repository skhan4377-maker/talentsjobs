<!-- Global Loader (only if not already in master) -->
<div id="globalLoader" class="fixed inset-0 z-[9999] bg-black bg-opacity-50 hidden flex items-center justify-center">
  <div class="bg-white px-6 py-4 rounded shadow text-gray-800 text-sm font-medium flex items-center gap-3">
    <span class="loader-spinner w-5 h-5 border-2 border-blue-600 border-t-transparent rounded-full animate-spin"></span>
    Processing...
  </div>
</div>

<style>
    .job-card {
        border-left: 5px solid #3b82f6;
        transition: all 0.2s;
    }
    .job-card:hover {
        background-color: #f9fafb;
        transform: translateX(5px);
    }
    /* DataTables override for better spacing */
    .dataTables_wrapper .dataTables_length, 
    .dataTables_wrapper .dataTables_filter {
        margin-bottom: 1rem;
    }
    .dataTables_wrapper .dataTables_paginate {
        margin-top: 1rem;
    }
</style>

<section class="bg-gray-50 pt-20">
    <div class="container mx-auto">
        <div class="max-w-6xl mx-auto">
            <div class="bg-white rounded-xl shadow-md p-6">
                <div class="flex justify-between items-center mb-6 flex-wrap gap-4">
                    <h1 class="text-2xl font-bold text-gray-800">📢 Latest Job Openings</h1>
                    <button class="bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded-lg transition" onclick="openCreateModal()">+ Post New Job</button>
                </div>

                <div id="alert-area"></div>
                
                <!-- Jobs DataTable -->
                <div class="overflow-x-auto">
                    <table id="jobsTable" class="display" style="width:100%">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Slug / URL</th>
                                <th>Posted Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Populated via AJAX -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Modal for Create / Edit -->
<div id="jobModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modalTitle" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
        <div class="relative bg-white rounded-lg shadow-xl max-w-3xl w-full mx-auto">
            <div class="flex justify-between items-center border-b p-4">
                <h5 id="modalTitle" class="text-xl font-semibold text-gray-900">Add New Job</h5>
                <button type="button" class="text-gray-400 hover:text-gray-600" onclick="closeModal()">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            <div class="p-6">
                <form id="jobForm">
                    <input type="hidden" name="id" id="jobId">
                    
                    <!-- Job Title -->
                    <div class="mb-4">
                        <label class="block text-gray-700 font-medium mb-2">Job Title</label>
                        <input type="text" name="title" id="title" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                    </div>

                    <!-- External Apply URL -->
                    <div class="mb-4">
                        <label class="block text-gray-700 font-medium mb-2">External Apply URL (Optional)</label>
                        <input type="url" name="external_url" id="external_url" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="https://example.com/apply">
                        <p class="text-sm text-gray-500 mt-1">Link to external application page or company website.</p>
                    </div>

                    <!-- TinyMCE Rich Text Editor -->
                    <div class="mb-4">
                        <label class="block text-gray-700 font-medium mb-2">Full Description (Rich Text)</label>
                        <textarea name="content" id="editor" rows="10" class="w-full border border-gray-300 rounded-lg px-3 py-2"></textarea>
                    </div>
                </form>
            </div>
            <div class="flex justify-end gap-3 border-t p-4">
                <button type="button" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold py-2 px-4 rounded-lg transition" onclick="closeModal()">Cancel</button>
                <button type="button" id="saveBtn" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg transition">Save Job</button>
            </div>
        </div>
    </div>
</div>

<script>
let editorInstance = null;
let currentAction = 'create';
let isSaving = false;

$(document).ready(function() {
    // Initialize TinyMCE (already loaded in master)
    if (typeof tinymce !== 'undefined') {
        tinymce.init({
            selector: '#editor',
            height: 400,
            menubar: false,
            plugins: 'advlist autolink lists link image charmap print preview anchor searchreplace visualblocks code fullscreen insertdatetime media table paste code help wordcount',
            toolbar: 'undo redo | formatselect | bold italic backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat | help',
            setup: function(editor) {
                editorInstance = editor;
            }
        });
    }

    loadJobsDataTable();
});

function loadJobsDataTable() {
    $('#globalLoader').removeClass('hidden');

    $.ajax({
        url: '<?= base_url("bio/manage/get_jobs_ajax") ?>',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            $('#globalLoader').addClass('hidden');
            
            if (response.status === 'success') {
                const jobs = response.jobs;
                const tableData = jobs.map(job => [
                    escapeHtml(job.title),
                    `<span class="text-xs text-gray-500">/bio/${escapeHtml(job.slug)}</span>`,
                    formatDate(job.created_at),
                    `<div class="flex space-x-2">
                        ${job.external_url ? `<a href="${escapeHtml(job.external_url)}" target="_blank" rel="noopener noreferrer" class="bg-green-500 hover:bg-green-600 text-white text-xs font-medium py-1 px-2 rounded">Apply</a>` : ''}
                        <a href="<?= base_url('bio/') ?>${job.slug}" target="_blank" class="bg-indigo-500 hover:bg-indigo-600 text-white text-xs font-medium py-1 px-2 rounded">View</a>
                        <button class="edit-btn bg-blue-500 hover:bg-blue-600 text-white text-xs font-medium py-1 px-2 rounded" data-id="${job.id}" data-title="${escapeHtml(job.title).replace(/"/g, '&quot;')}" data-content="${escapeHtml(job.content).replace(/"/g, '&quot;')}" data-external-url="${job.external_url ? escapeHtml(job.external_url).replace(/"/g, '&quot;') : ''}">Edit</button>
                        <button class="delete-btn bg-red-500 hover:bg-red-600 text-white text-xs font-medium py-1 px-2 rounded" data-id="${job.id}">Delete</button>
                    </div>`
                ]);

                // Destroy existing DataTable if any
                if ($.fn.DataTable.isDataTable('#jobsTable')) {
                    $('#jobsTable').DataTable().clear().destroy();
                }
                
                $('#jobsTable tbody').html('');
                $('#jobsTable').DataTable({
                    data: tableData,
                    columns: [
                        { title: "Title" },
                        { title: "Slug / URL" },
                        { title: "Posted Date" },
                        { title: "Actions", orderable: false }
                    ],
                    pageLength: 10,
                    lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
                    order: [[2, 'desc']]
                });
            } else {
                showAlert('danger', 'No jobs found or failed to load.');
                if ($.fn.DataTable.isDataTable('#jobsTable')) {
                    $('#jobsTable').DataTable().clear().destroy();
                }
                $('#jobsTable tbody').html('<tr><td colspan="4" class="text-center">No jobs available</td></tr>');
            }
        },
        error: function() {
            $('#globalLoader').addClass('hidden');
            showAlert('danger', 'Failed to load jobs. Please refresh.');
        }
    });
}

// Event delegation for dynamic buttons
$(document).on('click', '.edit-btn', function() {
    const id = $(this).data('id');
    const title = $(this).data('title');
    const content = $(this).data('content');
    const externalUrl = $(this).data('external-url');
    openEditModal(id, title, content, externalUrl);
});

$(document).on('click', '.delete-btn', function() {
    const id = $(this).data('id');
    deleteJob(id);
});

function openCreateModal() {
    currentAction = 'create';
    $('#modalTitle').text('Add New Job');
    $('#jobId').val('');
    $('#title').val('');
    $('#external_url').val('');
    if (editorInstance) editorInstance.setContent('');
    $('#jobModal').removeClass('hidden');
}

function openEditModal(id, title, content, externalUrl) {
    currentAction = 'edit';
    $('#modalTitle').text('Edit Job');
    $('#jobId').val(id);
    $('#title').val(title);
    $('#external_url').val(externalUrl || '');
    if (editorInstance) editorInstance.setContent(content);
    $('#jobModal').removeClass('hidden');
}

function closeModal() {
    $('#jobModal').addClass('hidden');
}

$('#saveBtn').click(function() {
    if (isSaving) return;
    
    const id = $('#jobId').val();
    const title = $('#title').val().trim();
    const externalUrl = $('#external_url').val().trim();
    const content = editorInstance ? editorInstance.getContent() : '';

    if (!title) {
        showAlert('danger', 'Title is required.');
        return;
    }
    if (!content) {
        showAlert('danger', 'Content cannot be empty.');
        return;
    }

    const $btn = $(this);
    const originalText = $btn.text();
    $btn.prop('disabled', true).text('Saving...');
    isSaving = true;
    $('#globalLoader').removeClass('hidden');

    const url = (currentAction === 'create')
        ? '<?= base_url("bio/manage/store_ajax") ?>'
        : '<?= base_url("bio/manage/update_ajax") ?>';

    const data = { 
        title: title, 
        content: content,
        external_url: externalUrl
    };
    if (currentAction === 'edit') data.id = id;

    // Use CSRF helpers from master template
    data[getCSRFName()] = getCSRFToken();

    $.ajax({
        url: url,
        type: 'POST',
        data: data,
        dataType: 'json',
        success: function(res) {
            if (res.status === 'success') {
                closeModal();
                showAlert('success', res.message);
                loadJobsDataTable();
                if (res.csrf_token) {
                    updateCSRFToken(res.csrf_token, res.csrf_name || getCSRFName());
                }
            } else {
                showAlert('danger', res.message);
            }
        },
        error: function() {
            showAlert('danger', 'An error occurred.');
        },
        complete: function() {
            $btn.prop('disabled', false).text(originalText);
            isSaving = false;
            $('#globalLoader').addClass('hidden');
        }
    });
});

function deleteJob(id) {
    if (!confirm('Are you sure you want to delete this job?')) return;

    $('#globalLoader').removeClass('hidden');
    const data = { id: id };
    data[getCSRFName()] = getCSRFToken();

    $.ajax({
        url: '<?= base_url("bio/manage/delete_ajax") ?>',
        type: 'POST',
        data: data,
        dataType: 'json',
        success: function(res) {
            if (res.status === 'success') {
                showAlert('success', res.message);
                loadJobsDataTable();
                if (res.csrf_token) {
                    updateCSRFToken(res.csrf_token, res.csrf_name || getCSRFName());
                }
            } else {
                showAlert('danger', res.message);
            }
        },
        error: function() {
            showAlert('danger', 'Delete failed.');
        },
        complete: function() {
            $('#globalLoader').addClass('hidden');
        }
    });
}

function showAlert(type, message) {
    const bg = (type === 'success') ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800';
    const alertHtml = `
        <div class="${bg} p-4 rounded-lg mb-4 flex justify-between items-center">
            <span>${escapeHtml(message)}</span>
            <button class="text-gray-500 hover:text-gray-700" onclick="this.parentElement.remove()">✕</button>
        </div>
    `;
    $('#alert-area').html(alertHtml);
    setTimeout(() => { $('#alert-area').empty(); }, 5000);
}

function formatDate(dateString) {
    if (!dateString) return '';
    const d = new Date(dateString.replace(/-/g, '/'));
    return d.toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' });
}

function escapeHtml(str) {
    if (!str) return '';
    return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}
</script>