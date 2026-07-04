<style>
    .job-card {
        border-left: 5px solid #3b82f6;
        transition: all 0.2s;
    }
    .job-card:hover {
        background-color: #f9fafb;
        transform: translateX(5px);
    }
    .pagination {
        display: flex;
        list-style: none;
        gap: 0.25rem;
    }
    .pagination .page-item .page-link {
        padding: 0.5rem 0.75rem;
        border: 1px solid #ddd;
        color: #3b82f6;
        text-decoration: none;
        border-radius: 0.25rem;
    }
    .pagination .page-item.active .page-link {
        background-color: #3b82f6;
        border-color: #3b82f6;
        color: white;
    }
    .pagination .page-item.disabled .page-link {
        color: #6c757d;
        pointer-events: none;
        background-color: #fff;
    }
</style>

<div class="container mx-auto">
    <div class="max-w-6xl mx-auto">
        <div class="bg-white rounded-xl shadow-md p-6">
            <div class="flex justify-between items-center mb-6 flex-wrap gap-4">
                <h1 class="text-2xl font-bold text-gray-800">📢 Latest Job Openings</h1>
                <button class="bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded-lg transition" onclick="openCreateModal()">+ Post New Job</button>
            </div>

            <div id="alert-area"></div>

            <!-- Per Page Selector (reloads page) -->
            <div class="flex justify-between items-center mb-4 flex-wrap gap-2">
                <div class="flex items-center gap-2">
                    <span class="text-sm text-gray-600">Show</span>
                    <select id="perPageSelect" class="border border-gray-300 rounded px-2 py-1 text-sm">
                        <option value="10" <?= $per_page == 10 ? 'selected' : '' ?>>10</option>
                        <option value="25" <?= $per_page == 25 ? 'selected' : '' ?>>25</option>
                        <option value="50" <?= $per_page == 50 ? 'selected' : '' ?>>50</option>
                        <option value="100" <?= $per_page == 100 ? 'selected' : '' ?>>100</option>
                    </select>
                    <span class="text-sm text-gray-600">entries</span>
                </div>
                <div class="text-sm text-gray-500">
                    Showing <?= (($current_page-1)*$per_page)+1 ?> to <?= min($current_page*$per_page, $total) ?> of <?= $total ?> entries
                </div>
            </div>

            <!-- Jobs Table (PHP loop) -->
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white border border-gray-200">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Title</th>
                            <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Slug / URL</th>
                            <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Posted Date</th>
                            <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($jobs)): ?>
                            <?php foreach ($jobs as $job): ?>
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="px-4 py-2"><?= htmlspecialchars($job->title) ?></td>
                                    <td class="px-4 py-2"><span class="text-xs text-gray-500">/bio/<?= htmlspecialchars($job->slug) ?></span></td>
                                    <td class="px-4 py-2"><?= date('d M Y', strtotime($job->created_at)) ?></td>
                                    <td class="px-4 py-2">
                                        <div class="flex space-x-2">
                                            <?php if ($job->external_url): ?>
                                                <a href="<?= htmlspecialchars($job->external_url) ?>" target="_blank" rel="noopener noreferrer" class="bg-green-500 hover:bg-green-600 text-white text-xs font-medium py-1 px-2 rounded">Apply</a>
                                            <?php endif; ?>
                                            <a href="<?= base_url('bio/'.$job->slug) ?>" target="_blank" class="bg-indigo-500 hover:bg-indigo-600 text-white text-xs font-medium py-1 px-2 rounded">View</a>
                                            <button class="edit-btn bg-blue-500 hover:bg-blue-600 text-white text-xs font-medium py-1 px-2 rounded" data-id="<?= $job->id ?>" data-title="<?= htmlspecialchars($job->title, ENT_QUOTES) ?>" data-content="<?= htmlspecialchars($job->content, ENT_QUOTES) ?>" data-external-url="<?= htmlspecialchars($job->external_url, ENT_QUOTES) ?>">Edit</button>
                                            <button class="delete-btn bg-red-500 hover:bg-red-600 text-white text-xs font-medium py-1 px-2 rounded" data-id="<?= $job->id ?>">Delete</button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="text-center py-4">No jobs available.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination Links (server-side generated) -->
            <div class="flex justify-center mt-6">
                <?= $pagination_links ?>
            </div>
        </div>
    </div>
</div>

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
                    <div class="mb-4">
                        <label class="block text-gray-700 font-medium mb-2">Job Title</label>
                        <input type="text" name="title" id="title" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 font-medium mb-2">External Apply URL (Optional)</label>
                        <input type="url" name="external_url" id="external_url" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="https://example.com/apply">
                        <p class="text-sm text-gray-500 mt-1">Link to external application page or company website.</p>
                    </div>
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
// No CSRF redefinitions – using functions from master header (getCSRFToken, getCSRFName, updateCSRFToken)
let editorInstance = null;
let currentAction = 'create';
let isSaving = false;

$(document).ready(function() {
    // Initialize TinyMCE (assuming it's already loaded from master)
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

    // Per page change - reload page with new per_page value
    $('#perPageSelect').on('change', function() {
        let perPage = $(this).val();
        let url = new URL(window.location.href);
        url.searchParams.set('per_page', perPage);
        url.searchParams.set('page', 1); // reset to first page
        window.location.href = url.toString();
    });
});

// Edit button handler
$(document).on('click', '.edit-btn', function() {
    const id = $(this).data('id');
    const title = $(this).data('title');
    const content = $(this).data('content');
    const externalUrl = $(this).data('external-url');
    openEditModal(id, title, content, externalUrl);
});

// Delete button handler
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

    const url = (currentAction === 'create')
        ? '<?= base_url("bio/manage/store_ajax") ?>'
        : '<?= base_url("bio/manage/update_ajax") ?>';

    const data = { 
        title: title, 
        content: content,
        external_url: externalUrl
    };
    if (currentAction === 'edit') data.id = id;

    // Use CSRF functions from master header
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
                // Update CSRF token if returned
                if (res.csrf_token) {
                    updateCSRFToken(res.csrf_token, res.csrf_name || getCSRFName());
                }
                // Reload the page to reflect changes (server-side pagination)
                setTimeout(() => { window.location.reload(); }, 1000);
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
        }
    });
});

function deleteJob(id) {
    if (!confirm('Are you sure you want to delete this job?')) return;

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
                if (res.csrf_token) {
                    updateCSRFToken(res.csrf_token, res.csrf_name || getCSRFName());
                }
                setTimeout(() => { window.location.reload(); }, 1000);
            } else {
                showAlert('danger', res.message);
            }
        },
        error: function() {
            showAlert('danger', 'Delete failed.');
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