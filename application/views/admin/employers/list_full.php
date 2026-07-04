<!-- Global Loader -->
<div id="globalLoader" class="fixed inset-0 z-[9999] bg-black bg-opacity-50 hidden flex items-center justify-center">
  <div class="bg-white px-6 py-4 rounded shadow text-gray-800 text-sm font-medium flex items-center gap-3">
    <span class="loader-spinner w-5 h-5 border-2 border-blue-600 border-t-transparent rounded-full animate-spin"></span>
    Processing...
  </div>
</div>

<!-- Filters Section - Improved Responsive Design -->
<div class="bg-white p-4 mb-4 rounded shadow-sm">
  <form id="employer-filter-form" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 xl:grid-cols-6 gap-3">
    <!-- Company Name -->
    <div>
      <label class="block text-xs text-gray-500 mb-1">Company Name</label>
      <input type="text" name="company_name" placeholder="Company Name" value="<?= html_escape($filters['company_name']) ?>" class="w-full px-3 py-2 border rounded text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
    </div>

    <!-- Email -->
    <div>
      <label class="block text-xs text-gray-500 mb-1">Email</label>
      <input type="text" name="email" placeholder="Email" value="<?= html_escape($filters['email']) ?>" class="w-full px-3 py-2 border rounded text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
    </div>

    <!-- Mobile -->
    <div>
      <label class="block text-xs text-gray-500 mb-1">Mobile</label>
      <input type="text" name="mobile" placeholder="Mobile" value="<?= html_escape($filters['mobile']) ?>" class="w-full px-3 py-2 border rounded text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
    </div>

    <!-- Status -->
    <div>
      <label class="block text-xs text-gray-500 mb-1">Status</label>
      <select name="status" class="w-full px-3 py-2 border rounded text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
        <option value="">All Status</option>
        <option value="active" <?= $filters['status'] === 'active' ? 'selected' : '' ?>>Active</option>
        <option value="inactive" <?= $filters['status'] === 'inactive' ? 'selected' : '' ?>>Inactive</option>
        <option value="under_review" <?= $filters['status'] === 'under_review' ? 'selected' : '' ?>>Under Review</option>
        <option value="rejected" <?= $filters['status'] === 'rejected' ? 'selected' : '' ?>>Rejected</option>
      </select>
    </div>

    <!-- Date Range -->
    <div class="sm:col-span-2 lg:col-span-1">
      <label class="block text-xs text-gray-500 mb-1">From Date</label>
      <input type="date" name="from_date" value="<?= html_escape($filters['from_date']) ?>" class="w-full px-3 py-2 border rounded text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
    </div>

    <div class="sm:col-span-2 lg:col-span-1">
      <label class="block text-xs text-gray-500 mb-1">To Date</label>
      <input type="date" name="to_date" value="<?= html_escape($filters['to_date']) ?>" class="w-full px-3 py-2 border rounded text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
    </div>

    <!-- Action Buttons -->
    <div class="sm:col-span-2 lg:col-span-2 flex gap-2 items-end">
      <button type="button" id="search-btn" class="flex-1 bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 text-sm font-medium transition-colors">
        <i class="fas fa-search mr-2"></i>Search
      </button>
      <button type="button" id="reset-btn" class="flex-1 bg-gray-300 text-gray-800 px-4 py-2 rounded hover:bg-gray-400 text-sm font-medium transition-colors">
        <i class="fas fa-redo mr-2"></i>Reset
      </button>
    </div>
  </form>
</div>

<!-- AJAX Target -->
<div id="employer-section" class="w-full">
  <!-- Card Container -->
  <div class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden">
    
    <!-- Responsive Scroll Wrapper -->
    <div class="overflow-x-auto">
      <table id="employersTable" class="min-w-full divide-y divide-gray-200 text-sm">
        <thead class="bg-gray-50 text-gray-600 text-xs uppercase">
          <tr>
            <th class="px-3 py-3 w-10 text-center">
              <input type="checkbox" id="checkAllEmployers" class="form-checkbox text-blue-600 rounded focus:ring-blue-500">
            </th>
            <th class="px-3 py-3 text-left whitespace-nowrap">Company</th>
            <th class="px-3 py-3 text-left hidden sm:table-cell">Name</th>
            <th class="px-3 py-3 text-left">Email</th>
            <th class="px-3 py-3 text-center hidden md:table-cell">Jobs</th>
            <th class="px-3 py-3 text-left">Status</th>
            <th class="px-3 py-3 text-left whitespace-nowrap hidden lg:table-cell">Date</th>
            <th class="px-3 py-3 text-center">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 bg-white text-[13px]">
          <!-- ✅ Dynamic Rows Will Be Injected Here Via JS -->
        </tbody>
      </table>
    </div>

    <!-- Footer with Bulk Action Buttons -->
    <div class="p-4 bg-gray-50 border-t border-gray-200 flex flex-col sm:flex-row gap-3 sm:gap-0 sm:items-center">
      <!-- Selected Count -->
      <div id="selectedCountLabel" class="text-sm text-gray-500 hidden">
        <span id="selectedCount">0</span> selected
      </div>

      <!-- Buttons aligned to the right -->
      <div class="flex gap-2 sm:ml-auto">
        <button id="sendEmailToSelected"
          class="hidden px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-md shadow-sm transition-all duration-150">
          <i class="fas fa-envelope mr-1"></i> <span class="hidden sm:inline">Send Email</span>
        </button>

        <button id="deleteSelectedEmployers"
          class="hidden px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-md shadow-sm transition-all duration-150">
          <i class="fas fa-trash mr-1"></i> <span class="hidden sm:inline">Delete Selected</span>
        </button>
      </div>
    </div>
  </div>
</div>

<!-- PREMIUM EMPLOYER MODAL -->
<div id="employerModal" class="fixed inset-0 z-[999] hidden bg-black/60 backdrop-blur-sm flex justify-center items-start p-1 overflow-y-auto">
  <div class="absolute inset-0" onclick="closeEmployerModal()"></div>
  <div class="relative w-full max-w-3xl max-h-[100vh] overflow-y-auto bg-white rounded-xl shadow-2xl animate-scale-in p-4 m-2">
    <!-- Header -->
    <div class="bg-gradient-to-r from-indigo-600 to-purple-700 p-5 pt-10 pb-12 relative rounded-t-md">
      <div class="absolute top-3 right-3">
        <button onclick="closeEmployerModal()" class="text-white hover:text-indigo-200 text-lg w-9 h-9 rounded-full flex items-center justify-center hover:bg-white hover:bg-opacity-10 transition-colors">
          <i class="fas fa-times"></i>
        </button>
      </div>
      <div class="text-center">
        <h2 class="text-xl font-semibold text-white">Employer Profile</h2>
        <p class="text-indigo-200 text-sm mt-0.5">Detailed company information</p>
      </div>
    </div>

    <!-- Content -->
    <div class="pb-6 -mt-10">
      <div class="relative flex justify-center mb-4">
        <div class="w-28 h-28 rounded-full border-4 border-white shadow-md overflow-hidden">
          <img id="employerLogo" src="<?= base_url('assets/images/no-image.png') ?>" class="w-full h-full object-cover">
        </div>
        <div id="verifiedBadge" class="absolute bottom-2 right-[calc(50%-44px)] w-7 h-7 rounded-full bg-green-500 text-white flex items-center justify-center shadow border-2 border-white">
          <i class="fas fa-check text-xs"></i>
        </div>
      </div>

      <div id="employerModalContent" class="bg-white rounded-md p-4 mt-4 shadow-sm border border-gray-100 text-sm">
        <!-- Filled by JS -->
      </div>
    </div>

    <!-- Footer Actions -->
    <div class="px-4 pb-4">
      <div class="flex flex-wrap justify-center gap-2">
        <button id="approveEmployer" class="bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white font-medium py-2 px-3 rounded-md shadow hover:shadow-md text-sm flex items-center">
          <i class="fas fa-check mr-2"></i> <span class="hidden sm:inline">Approve</span>
        </button>
        
        <button id="suggestEmployer" class="bg-gradient-to-r from-sky-500 to-blue-600 hover:from-sky-600 hover:to-blue-700 text-white font-medium py-2 px-3 rounded-md shadow hover:shadow-md text-sm flex items-center">
          <i class="fas fa-info-circle mr-2"></i> <span class="hidden sm:inline">Suggest</span>
        </button>

        <button id="rejectEmployer" class="bg-gradient-to-r from-yellow-500 to-amber-600 hover:from-yellow-600 hover:to-amber-700 text-white font-medium py-2 px-3 rounded-md shadow hover:shadow-md text-sm flex items-center">
          <i class="fas fa-times mr-2"></i> <span class="hidden sm:inline">Reject</span>
        </button>
        
        <button onclick="closeEmployerModal()" class="bg-gradient-to-r from-gray-500 to-gray-700 hover:from-gray-600 hover:to-gray-800 text-white font-medium py-2 px-3 rounded-md shadow hover:shadow-md text-sm flex items-center">
          <i class="fas fa-times-circle mr-2"></i> <span class="hidden sm:inline">Close</span>
        </button>
      </div>
    </div>
  </div>
</div>

<!-- Rejection Reason Modal -->
<div id="rejectionModal" class="fixed inset-0 bg-black bg-opacity-50 z-[1000] hidden flex items-center justify-center p-4">
  <div class="bg-white rounded-lg p-6 w-full max-w-md shadow-lg">
    <h3 class="text-lg font-semibold mb-4">Reject Employer</h3>
    <textarea id="rejectionReason" rows="4" class="w-full p-3 border rounded text-sm focus:ring-2 focus:ring-blue-500" placeholder="Enter reason for rejection..."></textarea>
    <div class="mt-4 flex justify-end gap-2">
      <button onclick="$('#rejectionModal').addClass('hidden')" class="bg-gray-300 hover:bg-gray-400 px-4 py-2 rounded text-sm">Cancel</button>
      <button id="confirmRejectEmployer" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded text-sm">Reject</button>
    </div>
  </div>
</div>

<!-- Admin Suggestion Modal -->
<div id="suggestionModal" class="fixed inset-0 bg-black bg-opacity-50 z-[1000] hidden flex items-center justify-center p-4">
  <div class="bg-white rounded-lg p-6 w-full max-w-md shadow-lg">
    <h3 class="text-lg font-semibold mb-4">Review Suggestion</h3>
    <textarea id="adminSuggestion" rows="4" class="w-full p-3 border rounded text-sm focus:ring-2 focus:ring-blue-500" placeholder="Enter review feedback/suggestion..."></textarea>
    <div class="mt-4 flex justify-end gap-2">
      <button onclick="$('#suggestionModal').addClass('hidden')" class="bg-gray-300 hover:bg-gray-400 px-4 py-2 rounded text-sm">Cancel</button>
      <button id="confirmSuggestion" class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded text-sm">Send</button>
    </div>
  </div>
</div>

<!-- Bulk Email Modal -->
<div id="bulkEmailModal" class="hidden fixed inset-0 z-50 bg-black bg-opacity-30 flex items-center justify-center p-4">
  <div class="bg-white w-full max-w-lg p-6 rounded shadow-lg max-h-[90vh] overflow-y-auto">
    <h2 class="text-xl font-semibold mb-4">Send Bulk Email</h2>

    <div class="mb-4">
      <label class="font-medium text-sm text-gray-700 mb-1 block">Selected Emails:</label>
      <div id="selectedEmails" class="text-sm bg-gray-100 p-2 rounded h-20 overflow-y-auto text-gray-800 border border-gray-300"></div>
    </div>
    
    <div class="mb-4">
      <label class="font-medium text-sm text-gray-700 mb-1 block">Email Context:</label>
      <select id="bulkEmailContext" class="w-full border px-3 py-2 rounded text-sm focus:ring-2 focus:ring-blue-500">
        <option value="">-- Select Email Context --</option>
        <option value="profile_reminder">Profile Reminder</option>            
        <option value="job_post">Job Post</option>    
      </select>
    </div>
    
    <div class="mb-4">
      <label class="font-medium text-sm text-gray-700 mb-1 block">Subject:</label>
      <input type="text" id="bulkEmailSubject" class="w-full border px-3 py-2 rounded text-sm focus:ring-2 focus:ring-blue-500" placeholder="Enter subject" />
    </div>

    <div class="mb-4">
      <label class="font-medium text-sm text-gray-700 mb-1 block">Message:</label>
      <textarea id="bulkEmailMessage" name="bulkEmailMessage" class="w-full border px-3 py-2 rounded text-sm focus:ring-2 focus:ring-blue-500" rows="4" placeholder="Enter message..."></textarea>
    </div>

    <div class="flex justify-end gap-2 mt-4">
      <button onclick="$('#bulkEmailModal').addClass('hidden')" class="px-4 py-2 text-gray-600 border border-gray-300 rounded text-sm">Cancel</button>
      <button id="confirmSendBulkEmail" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm">Send</button>
    </div>
  </div>
</div>


<script src="https://cdn.tiny.cloud/1/jxhyjhicc4somdh05bjumsfnalcuzz4uej01mbbbizec4fov/tinymce/5/tinymce.min.js"></script>

<script>
// Global CSRF Helper Functions
function getCsrfToken() {
    return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
}

function getCsrfName() {
    return document.querySelector('meta[name="csrf-name"]')?.getAttribute('content') || '';
}

function getCsrfData() {
    const data = {};
    data[getCsrfName()] = getCsrfToken();
    return data;
}

function updateCsrfToken(newToken) {
    const meta = document.querySelector('meta[name="csrf-token"]');
    if (meta) {
        meta.setAttribute('content', newToken);
    }
}

// AJAX Setup with CSRF
$.ajaxSetup({
    beforeSend: function(xhr, settings) {
        if (settings.type === 'POST' || settings.type === 'PUT' || settings.type === 'DELETE') {
            const csrfData = getCsrfData();
            if (settings.data instanceof FormData) {
                for (const [key, value] of Object.entries(csrfData)) {
                    settings.data.append(key, value);
                }
            } else if (typeof settings.data === 'string') {
                settings.data += '&' + $.param(csrfData);
            } else {
                settings.data = {...settings.data, ...csrfData};
            }
        }
    }
});

// Initialize TinyMCE
tinymce.init({
    selector: 'textarea[name="bulkEmailMessage"]',
    plugins: 'code fullscreen',
    toolbar: 'undo redo | bold italic | alignleft aligncenter alignright | code fullscreen',
    height: 300,
    menubar: false,
    statusbar: false
});

// Utility Functions
function toggleLoader(show) {
    if (show) $('#globalLoader').fadeIn(150);
    else $('#globalLoader').fadeOut(150);
}

function showToast(message, type = 'info') {
    const toast = document.createElement('div');
    toast.className = `fixed bottom-4 right-4 z-[9999] px-6 py-3 rounded-lg text-white shadow-lg transition-all duration-300 ${
        type === 'success' ? 'bg-green-500' : 
        type === 'error' ? 'bg-red-500' : 'bg-blue-500'
    }`;
    toast.textContent = message;
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transform = 'translateY(100%)';
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}

let selectedEmployerId = null;

// Main Document Ready
$(document).ready(function () {
    // DataTable Initialization
    const table = $('#employersTable').DataTable({
        processing: true,
        serverSide: true,
        searching: false,
        ordering: true,
        order: [[6, 'desc']], // created_at
        responsive: true,
        language: {
            processing: '<div class="spinner-border text-blue-600" role="status"></div> Processing...'
        },

        ajax: {
            url: '<?= base_url("admin/employers/AdminEmployer/datatables") ?>',
            type: 'GET',
            data: function (d) {
                const filters = $('#employer-filter-form').serializeArray();
                filters.forEach(f => d[f.name] = f.value);
                d.search = { value: $('#globalSearchInput').val() || '' };
            },
            beforeSend: function () { toggleLoader(true); },
            complete: function () { toggleLoader(false); }
        },

        columns: [
            { data: 'checkbox', orderable: false, searchable: false, className: "text-center" },
            { data: 'company_name', responsivePriority: 1 },
            { data: 'name', className: "hidden sm:table-cell", responsivePriority: 3 },
            { data: 'email', responsivePriority: 2 },
            { data: 'job_count', orderable: false, className: "text-center hidden md:table-cell", responsivePriority: 5 },
            { data: 'status', responsivePriority: 4 },
            { data: 'created_at', className: "hidden lg:table-cell", responsivePriority: 6 },
            { data: 'actions', orderable: false, searchable: false, className: "text-center", responsivePriority: 1 }
        ],

        drawCallback: function () {
            $('#checkAllEmployers').prop('checked', false);
            toggleBulkActions();
        }
    });

    // Search and Reset Functions
    $('#search-btn').on('click', function () {
        table.ajax.reload();
    });

    $('#reset-btn').on('click', function () {
        $('#employer-filter-form')[0].reset();
        table.ajax.reload();
    });

    // Checkbox Management
    $('#checkAllEmployers').on('change', function () {
        const isChecked = $(this).is(':checked');
        $('.employer-checkbox').prop('checked', isChecked).trigger('change');
    });

    $(document).on('change', '.employer-checkbox', function () {
        toggleBulkActions();
    });

    function toggleBulkActions() {
        const selected = $('.employer-checkbox:checked').length;
        $('#selectedCount').text(selected);
        $('#selectedCountLabel').toggle(selected > 0);
        $('#deleteSelectedEmployers').toggle(selected > 0);
        $('#sendEmailToSelected').toggle(selected > 0);
    }

    // Bulk Delete Action
    $('#deleteSelectedEmployers').on('click', function () {
        const selectedIds = $('.employer-checkbox:checked').map(function () {
            return this.value;
        }).get();

        if (selectedIds.length === 0) return;

        if (!confirm(`Are you sure you want to delete ${selectedIds.length} employer(s)?`)) return;

        $.ajax({
            url: '<?= base_url("admin/employers/AdminEmployer/bulk_soft_delete") ?>',
            type: 'POST',
            data: { ids: selectedIds },
            dataType: 'json',
            success: function (res) {
                if (res.csrf_token) {
                    updateCsrfToken(res.csrf_token);
                }

                if (res.status === 'success') {
                    showToast(res.message, 'success');
                    table.ajax.reload();
                } else {
                    showToast(res.message, 'error');
                }
            },
            error: function () {
                showToast('Something went wrong.', 'error');
            }
        });
    });

    // View Employer Details
    $(document).on('click', '.view-employer', function (e) {
        e.preventDefault();
        const id = $(this).data('id');
        $.get('<?= base_url("admin/employers/AdminEmployer/get_employer_json/") ?>' + id, function (res) {
            if (res.status === 'success') openEmployerModal(res);
            else showToast('Failed to load employer data.', 'error');
        }, 'json');
    });

    // Approve Employer
    $('#approveEmployer').on('click', function () {
        if (!selectedEmployerId) return;

        const $btn = $(this);
        const originalHtml = $btn.html();

        $btn.prop('disabled', true).html(`
            <svg class="animate-spin h-4 w-4 mr-2 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
            </svg>
            Approving...
        `);

        $.ajax({
            url: '<?= base_url("admin/employers/AdminEmployer/update_employer") ?>',
            type: 'POST',
            data: {
                employer_id: selectedEmployerId,
                status: 'active'
            },
            dataType: 'json',
            success: function (res) {
                if (res.csrf_token) {
                    updateCsrfToken(res.csrf_token);
                }

                if (res.status === 'success') {
                    showToast('Employer approved.', 'success');
                    closeEmployerModal();
                    table.ajax.reload();
                } else {
                    showToast(res.message || 'Approval failed.', 'error');
                }
                $btn.prop('disabled', false).html(originalHtml);
            },
            error: function () {
                showToast('Request failed.', 'error');
                $btn.prop('disabled', false).html(originalHtml);
            }
        });
    });

    // Reject Employer
    $('#rejectEmployer').on('click', function () {
        if (!selectedEmployerId) return;
        $.get('<?= base_url("admin/employers/AdminEmployer/get_employer_json/") ?>' + selectedEmployerId, function (res) {
            if (res.status === 'success') {
                const current = res.data.rejection_reason?.trim();
                const defaultReason = "After review, we found some inconsistencies in your company details. Please revise and reapply.";
                $('#rejectionReason').val(current || defaultReason);
                $('#rejectionModal').removeClass('hidden');
            }
        }, 'json');
    });

    $('#confirmRejectEmployer').on('click', function () {
        const reason = $('#rejectionReason').val().trim();
        if (!reason) return alert('Please enter a rejection reason.');

        const $btn = $(this);
        const originalHtml = $btn.html();

        $btn.prop('disabled', true).html(`
            <svg class="animate-spin h-4 w-4 mr-2 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
            </svg>
            Rejecting...
        `);

        $.post('<?= base_url("admin/employers/AdminEmployer/update_employer") ?>', {
            employer_id: selectedEmployerId,
            status: 'rejected',
            rejection_reason: reason
        }, function (res) {
            if (res.status === 'success') {
                $('#rejectionModal').addClass('hidden');
                showToast('Employer rejected.', 'success');
                closeEmployerModal();
                table.ajax.reload();
            } else {
                showToast('Rejection failed.', 'error');
            }
            $btn.prop('disabled', false).html(originalHtml);
        }, 'json').fail(function () {
            showToast('Request failed.', 'error');
            $btn.prop('disabled', false).html(originalHtml);
        });
    });

    // Suggest Employer
    $('#suggestEmployer').on('click', function () {
        if (!selectedEmployerId) return;
        $.get('<?= base_url("admin/employers/AdminEmployer/get_employer_json/") ?>' + selectedEmployerId, function (res) {
            if (res.status === 'success') {
                const current = res.data.admin_suggestion?.trim();
                const defaultSuggestion = "Please update your company profile with accurate details to proceed with verification.";
                $('#adminSuggestion').val(current || defaultSuggestion);
                $('#suggestionModal').removeClass('hidden');
            }
        }, 'json');
    });

    $('#confirmSuggestion').on('click', function () {
        const suggestion = $('#adminSuggestion').val().trim();
        if (!suggestion) return alert('Please enter a suggestion.');

        const $btn = $(this);
        const originalHtml = $btn.html();

        $btn.prop('disabled', true).html(`
            <svg class="animate-spin h-4 w-4 mr-2 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
            </svg>
            Sending...
        `);

        $.post('<?= base_url("admin/employers/AdminEmployer/update_employer") ?>', {
            employer_id: selectedEmployerId,
            status: 'under_review',
            admin_suggestion: suggestion
        }, function (res) {
            if (res.status === 'success') {
                $('#suggestionModal').addClass('hidden');
                showToast('Suggestion sent successfully.', 'success');
                closeEmployerModal();
                table.ajax.reload();
            } else {
                showToast('Suggestion failed.', 'error');
            }
            $btn.prop('disabled', false).html(originalHtml);
        }, 'json').fail(function () {
            showToast('Request failed.', 'error');
            $btn.prop('disabled', false).html(originalHtml);
        });
    });

    // Bulk Email Functions
    $('#sendEmailToSelected').on('click', function () {
        const selectedEmails = $('.employer-checkbox:checked').map(function () {
            return $(this).data('email');
        }).get();

        if (selectedEmails.length === 0) {
            alert('Please select at least one employer.');
            return;
        }

        $('#selectedEmails').html(selectedEmails.map(email => `<div class="truncate">${email}</div>`).join(''));
        $('#bulkEmailSubject').val('');
        tinymce.get('bulkEmailMessage')?.setContent('');
        $('#bulkEmailContext').val('');
        $('#bulkEmailModal').removeClass('hidden');
    });

    $('#confirmSendBulkEmail').on('click', function () {
        const $btn = $(this);
        const originalText = $btn.html();

        const selectedIds = $('.employer-checkbox:checked').map(function () {
            return this.value;
        }).get();

        const subject = $('#bulkEmailSubject').val().trim();
        const message = tinymce.get('bulkEmailMessage')?.getContent().trim();
        const context = $('#bulkEmailContext').val().trim();
        const messageText = $('<div>').html(message).text().trim();

        if (!subject || !messageText || !context) {
            alert('Please enter subject, message, and select an email context.');
            return;
        }

        $btn.prop('disabled', true).html('<span class="animate-spin mr-2">&#9696;</span> Sending...');

        $.ajax({
            url: '<?= base_url("admin/employers/AdminEmployer/send_bulk_email") ?>',
            type: 'POST',
            data: {
                ids: selectedIds,
                subject: subject,
                message: message,
                context: context
            },
            dataType: 'json',
            success: function (res) {
                if (res.status === 'success') {
                    showToast(res.message || 'Email sent successfully.', 'success');
                    $('#bulkEmailModal').addClass('hidden');
                    
                    // Reset selections
                    $('.employer-checkbox').prop('checked', false);
                    $('#deleteSelectedEmployers').addClass('hidden');
                    $('#sendEmailToSelected').addClass('hidden');
                    $('#selectedCount').text('0');
                    $('#selectedCountLabel').addClass('hidden');
                } else {
                    showToast(res.message || 'Failed to send email.', 'error');
                }
            },
            error: function () {
                showToast('Something went wrong.', 'error');
            },
            complete: function () {
                $btn.prop('disabled', false).html(originalText);
            }
        });
    });

    // Email Context Templates
    $('#bulkEmailContext').on('change', function () {
        const context = $(this).val();
        let subject = '';
        let message = '';

        if (context === 'profile_reminder') {
            subject = 'Talents Jobs - Complete Your Employer Profile to Get Started';
            message = `
                <p>Thank you for signing up on <strong>Talents Jobs</strong>.</p>
                <p>To verify and activate your employer account, please complete your company profile. This helps us ensure a trusted and professional platform for job seekers.</p>
                <p><strong>Kindly include the following details:</strong></p>
                <p>
                ✅ Company logo and a short description<br>
                ✅ Website and contact information<br>
                ✅ Business registration or valid identity proof
                </p>
                <p>Once your profile is complete and verified, you'll be able to post jobs and start receiving applications from qualified candidates.</p>`;
        } else if (context === 'job_post') {
            subject = 'Talents Jobs - Post Unlimited Jobs for Free';
            message = `
                <p>We're happy to offer you <strong>free unlimited job postings</strong> on Talents Jobs.</p>
                <p>Whether you're hiring for internships or senior roles, you can post as many jobs as you need – no fees, no subscriptions.</p>
                <p>
                ✅ Unlimited job postings<br>
                ✅ No subscription required<br>
                ✅ Connect with thousands of active job seekers
                </p>
                <p>Start posting today and hire the right talent faster.</p>`;
        }

        $('#bulkEmailSubject').val(subject);
        tinymce.get('bulkEmailMessage')?.setContent(message);
    });
});

// Modal Functions
function openEmployerModal(res) {
    const e = res.data;
    const email_logs = res.email_logs || [];
    
    selectedEmployerId = e.employer_id;
    document.body.classList.add('overflow-hidden');
    document.getElementById('employerLogo').src = e.logo || '<?= base_url("uploads/employer/noimage.png") ?>';

    const verifiedBadge = document.getElementById('verifiedBadge');
    verifiedBadge.innerHTML = e.is_verified == '1'
        ? '<i class="fas fa-check text-xs"></i>' : '<i class="fas fa-times text-xs"></i>';
    verifiedBadge.className = e.is_verified == '1'
        ? 'absolute bottom-2 right-[calc(50%-44px)] w-7 h-7 rounded-full bg-green-500 text-white flex items-center justify-center shadow border-2 border-white'
        : 'absolute bottom-2 right-[calc(50%-44px)] w-7 h-7 rounded-full bg-red-500 text-white flex items-center justify-center shadow border-2 border-white';

    const verificationLabel = e.is_verified == '1'
        ? '<span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded-full ml-2">Verified</span>'
        : '<span class="bg-red-100 text-red-800 text-xs font-medium px-2.5 py-0.5 rounded-full ml-2">Unverified</span>';

    let html = `
        <div class="flex flex-wrap justify-center gap-3 mb-4">
            <span class="${e.status === 'active' ? 'bg-green-100 text-green-800' : e.status === 'rejected' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800'} text-xs font-medium px-3 py-1.5 rounded-full">
                <i class="fas fa-circle text-[8px] mr-1.5 align-middle"></i> ${e.status}
            </span>
            <span class="bg-blue-100 text-blue-800 text-xs font-medium px-3 py-1.5 rounded-full">
                <i class="fas fa-star text-[8px] mr-1.5 align-middle"></i> ${e.membership_type}
            </span>
            <span class="bg-purple-100 text-purple-800 text-xs font-medium px-3 py-1.5 rounded-full">
                <i class="fas fa-users text-[8px] mr-1.5 align-middle"></i> ${e.company_size || '-'}
            </span>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
            <div class="space-y-3">
                <p><span class="text-gray-500">Company Name</span><br><strong>${e.company_name}</strong></p>
                <p><span class="text-gray-500">Name</span><br><strong>${e.name || '-'}</strong></p>
                <p><span class="text-gray-500">Last Name</span><br><strong>${e.last_name || '-'}</strong></p>
                <p><span class="text-gray-500">Designation</span><br><strong>${e.employee_designation || '-'}</strong></p>
                <p><span class="text-gray-500">Email</span><br><strong>${e.email}</strong> ${verificationLabel}</p>
                <p><span class="text-gray-500">Mobile</span><br><strong>${e.mobile || '-'}</strong></p>
                <p><span class="text-gray-500">Alternate Contact</span><br><strong>${e.alternate_contact || '-'}</strong></p>
                <p><span class="text-gray-500">Gender</span><br><strong>${e.gender || '-'}</strong></p>
                <p><span class="text-gray-500">Role</span><br><strong>${e.role}</strong></p>
            </div>
            <div class="space-y-3">
                <p><span class="text-gray-500">Industry</span><br><strong>${e.industry_name || '-'}</strong></p>
                <p><span class="text-gray-500">Expertise</span><br><strong>${e.expertise_specialization || '-'}</strong></p>
                <p><span class="text-gray-500">Company Location</span><br><strong>${e.city_name || '-'}</strong></p>
                <p><span class="text-gray-500">Company Website</span><br><a href="${e.company_website || '#'}" target="_blank" class="text-indigo-600 hover:underline">${e.company_website || '-'}</a></p>
                <p><span class="text-gray-500">Company Type</span><br><strong>${e.company_type || '-'}</strong></p>
                <p><span class="text-gray-500">Company Founded</span><br><strong>${e.company_founded || '-'}</strong></p>
                <p><span class="text-gray-500">Membership Expiry</span><br><strong>${e.membership_expiry || '-'}</strong></p>
                <p><span class="text-gray-500">Agree to Terms</span><br><strong>${e.agree_to_terms || '-'}</strong></p>
                <p><span class="text-gray-500">Recruiter Type</span><br><strong>${e.recuiter_type || '-'}</strong></p>
            </div>
        </div>
        <div class="mt-5 text-sm border-t pt-4 space-y-2">
            <p><span class="text-gray-500">Company Address</span><br><strong>${e.company_address || '-'}</strong></p>
            <p><span class="text-gray-500">About Company</span><br><strong>${e.about_company || '-'}</strong></p>
            <p><span class="text-gray-500">Created At</span> — <strong>${e.created_at}</strong></p>
            <p><span class="text-gray-500">Last Login</span> — <strong>${e.last_login || '-'}</strong></p>
            <p><span class="text-gray-500">Total Jobs Posted</span> — <strong>${e.total_jobs || 0}</strong></p>
            <p><span class="text-gray-500">Last Job Posted At</span> — <strong>${e.last_job_posted_at || '-'}</strong></p>
            <p><span class="text-gray-500">Last Reminder Email Sent</span> — <strong>${e.profile_reminder_sent_at || '-'}</strong></p>
        </div>
    `;
    
    // Email Logs
    if (email_logs.length > 0) {
        html += `
            <div class="mt-6 text-sm border-t pt-4">
                <h3 class="text-md font-semibold mb-2">Email Logs</h3>
                <ul class="space-y-2 max-h-52 overflow-y-auto pr-2">
        `;
        email_logs.forEach(log => {
            const context = (log.email_context || '').split('_').join(' ');
            const sentAt = log.created_at || '-';
            const status = log.status || '-';
            const openedAt = log.email_opened_at ? `Opened at: ${log.email_opened_at}` : 'Not opened';
            const clickedAt = log.profile_clicked_at ? `Profile clicked at: ${log.profile_clicked_at}` : 'Not clicked';

            html += `
                <li class="border p-2 rounded bg-blue-50">
                    <div class="flex justify-between text-xs text-gray-500">
                        <span>${context}</span>
                        <span>${sentAt}</span>
                    </div>
                    <p class="mt-1 text-gray-800">Status: ${status}</p>
                    <p class="text-xs text-gray-600 mt-1">${openedAt}</p>
                    <p class="text-xs text-gray-600">${clickedAt}</p>
                </li>
            `;
        });
        html += `</ul></div>`;
    } else {
        html += `
            <div class="mt-6 text-sm border-t pt-4 text-gray-500">
                <h3 class="text-md font-semibold mb-2">Email Logs</h3>
                <p>No emails sent to this employer yet.</p>
            </div>
        `;
    }
    
    // Rejection Reason
    if (e.status === 'rejected' && e.rejection_reason) {
        html += `
            <div class="mt-5 text-sm border-t pt-4 text-red-600">
                <p><span class="font-semibold">Rejection Reason:</span><br>${e.rejection_reason}</p>
            </div>
        `;
    }
    
    // Admin Suggestion
    if (e.admin_suggestion) {
        html += `
            <div class="mt-5 text-sm border-t pt-4 text-yellow-600">
                <p><span class="font-semibold">Admin Suggestion:</span><br>${e.admin_suggestion}</p>
            </div>
        `;
    }
   
    // Show/Hide Action Buttons Based on Employer Status
    document.getElementById('approveEmployer').classList.add('hidden');
    document.getElementById('rejectEmployer').classList.add('hidden');
    document.getElementById('suggestEmployer').classList.add('hidden');

    if (e.status === 'under_review') {
        document.getElementById('approveEmployer').classList.remove('hidden');
        document.getElementById('rejectEmployer').classList.remove('hidden');
        document.getElementById('suggestEmployer').classList.remove('hidden');
    } else if (e.status === 'active') {
        document.getElementById('rejectEmployer').classList.remove('hidden');
    } else if (e.status === 'rejected') {
        document.getElementById('approveEmployer').classList.remove('hidden');
        document.getElementById('suggestEmployer').classList.remove('hidden');
    }

    document.getElementById('employerModalContent').innerHTML = html;
    document.getElementById('employerModal').classList.remove('hidden');
}

function closeEmployerModal() {
    document.body.classList.remove('overflow-hidden');
    document.getElementById('employerModal').classList.add('hidden');
    selectedEmployerId = null;
}
</script>