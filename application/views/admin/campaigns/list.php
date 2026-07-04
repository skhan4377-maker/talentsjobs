<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="container mx-auto">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4 gap-2">
        <h1 class="text-2xl font-bold text-gray-800"></h1>
        <button id="addCampaignBtn" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg shadow flex items-center gap-2">
            <i class="fas fa-plus"></i> Add New Campaign
        </button>
    </div>

    <!-- Filter Card -->
    <div class="bg-white rounded-lg shadow p-4 mb-6">
        <form id="filterForm" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Search Title</label>
                <input type="text" name="search" placeholder="Title..." class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-3 py-2 border text-sm">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Status</label>
                <select name="status" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-3 py-2 border text-sm bg-white">
                    <option value="">All</option>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Start Date From</label>
                <input type="date" name="start_from" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-3 py-2 border text-sm">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Start Date To</label>
                <input type="date" name="start_to" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-3 py-2 border text-sm">
            </div>
            <div class="flex items-end gap-2 lg:col-span-4">
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2 px-4 rounded-lg text-sm">Filter</button>
                <button type="button" id="resetFilterBtn" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-medium py-2 px-4 rounded-lg text-sm">Reset</button>
            </div>
        </form>
    </div>

    <!-- Table Card -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date Range</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Total Leads</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Pending</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Sent</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Failed</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody id="campaignTableBody" class="bg-white divide-y divide-gray-200">
                    <!-- AJAX loaded rows -->
                </tbody>
            </table>
        </div>
        <!-- Pagination -->
        <div id="paginationContainer" class="px-4 py-3 border-t flex justify-between items-center text-sm text-gray-600">
            <!-- AJAX rendered -->
        </div>
    </div>
</div>

<!-- Campaign Add/Edit Modal -->
<div id="campaignModal" class="fixed inset-0 z-40 hidden items-start sm:items-center justify-center overflow-y-auto py-4 sm:py-0" style="background-color: rgba(0,0,0,0.5);">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-2xl mx-2 sm:mx-4 max-h-[95vh] sm:max-h-[90vh] overflow-y-auto relative my-auto">
        <div class="flex justify-between items-center p-4 sm:p-6 border-b sticky top-0 bg-white z-10">
            <h3 id="modalTitle" class="text-lg sm:text-xl font-semibold text-gray-800">Add Campaign</h3>
            <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600 text-2xl leading-none p-1">&times;</button>
        </div>

        <form id="campaignForm" class="p-4 sm:p-6 space-y-4">
            <input type="hidden" name="id" id="campaignId">

            <div>
                <label class="block text-sm font-medium text-gray-700">Title <span class="text-red-500">*</span></label>
                <input type="text" name="title" id="title" required maxlength="255"
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-3 py-2 border">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Description</label>
                <textarea name="description" id="description" rows="3"
                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-3 py-2 border"></textarea>
            </div>
			
			<!-- Email Service -->
			<div>
				<label class="block text-sm font-medium text-gray-700">Email Service</label>
				<select name="email_service" id="email_service"
						class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-3 py-2 border bg-white">
					<option value="mailercloud">Mailercloud</option>
					<option value="smtp">SMTP</option>
				</select>
			</div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Start Date <span class="text-red-500">*</span></label>
                    <input type="datetime-local" name="start_date" id="start_date" required
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-3 py-2 border">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">End Date <span class="text-red-500">*</span></label>
                    <input type="datetime-local" name="end_date" id="end_date" required
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-3 py-2 border">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Status <span class="text-red-500">*</span></label>
                <select name="status" id="status" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-3 py-2 border bg-white">
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>

            <div class="flex justify-end gap-3 pt-4 border-t sticky bottom-0 bg-white py-3">
                <button type="button" onclick="closeModal()"
                        class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-medium py-2 px-4 rounded-lg">Cancel</button>
                <button type="submit" id="saveBtn"
                        class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg">Save</button>
            </div>
        </form>
    </div>
</div>

<!-- Upload Leads Modal -->
<div id="uploadLeadsModal" class="fixed inset-0 z-50 hidden items-center justify-center" style="background-color: rgba(0,0,0,0.5);">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-lg mx-4 p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold">Upload Leads</h3>
            <button onclick="closeUploadModal()" class="text-gray-400 hover:text-gray-600 text-2xl">&times;</button>
        </div>
        <form id="uploadForm" enctype="multipart/form-data">
            <input type="hidden" name="campaign_id" id="uploadCampaignId">
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">CSV File (email, name, designation)</label>
                <input type="file" name="leads_csv" accept=".csv" required
                       class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                <p class="text-xs text-gray-500 mt-1">First row must contain headers: email, name, designation</p>
            </div>
            <div class="flex justify-end gap-3">
                <button type="button" onclick="closeUploadModal()" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-medium py-2 px-4 rounded-lg">Cancel</button>
                <button type="submit" id="uploadSubmitBtn" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg">Upload</button>
            </div>
        </form>
    </div>
</div>

<!-- Loading Overlay -->
<div id="loadingOverlay" class="fixed inset-0 z-[100] hidden items-center justify-center bg-black bg-opacity-50">
    <div class="bg-white p-6 rounded-lg shadow-lg flex items-center space-x-3">
        <svg class="animate-spin h-8 w-8 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
        </svg>
        <span class="text-gray-700 font-medium">Processing...</span>
    </div>
</div>

<script>
    // ==============================
    // CSRF Token Handling
    // Uses global functions defined in master layout:
    // getCSRFName(), getCSRFToken(), updateCSRFToken()
    // ==============================

    // Attach CSRF token to every POST request automatically
    $.ajaxPrefilter(function(options, originalOptions, jqXHR) {
        if (options.type.toLowerCase() === 'post') {
            // If data is FormData, append token directly
            if (originalOptions.data instanceof FormData) {
                originalOptions.data.append(getCSRFName(), getCSRFToken());
            } else {
                // Otherwise, add to query string or existing data
                options.data = options.data || '';
                options.data += (options.data ? '&' : '') +
                    encodeURIComponent(getCSRFName()) + '=' + encodeURIComponent(getCSRFToken());
            }
        }
    });

    // After every successful AJAX call, update meta tags if response contains new token
    $(document).ajaxSuccess(function(event, xhr, settings, data) {
        if (data && typeof data === 'object' && data.csrf_token) {
            updateCSRFToken(data.csrf_token, data.csrf_name);
        }
    });

    // Loading Spinner
    $(document).ajaxStart(function() {
        $('#loadingOverlay').removeClass('hidden').addClass('flex');
    }).ajaxStop(function() {
        $('#loadingOverlay').addClass('hidden').removeClass('flex');
    });

    // ==============================
    // TinyMCE
    // ==============================
    let editorReady = false;
    let pendingContent = null;

    function initCampaignEditor() {
        if (tinymce.get('description')) {
            tinymce.get('description').remove();
        }

        tinymce.init({
            selector: '#description',
            height: 300,

            // Prevent URL conversion
            relative_urls: false,
            remove_script_host: false,
            convert_urls: false,

            menubar: false,
            branding: false,
            promotion: false,
            statusbar: false,

            plugins: 'link lists code textcolor fontfamily emoticons',

            toolbar: 'undo redo | fontselect fontsizeselect forecolor backcolor | bold italic underline | bullist numlist | link emoticons code',

            font_family_formats:
                'Arial=arial,helvetica,sans-serif;' +
                'Georgia=georgia,serif;' +
                'Tahoma=tahoma,geneva,sans-serif;' +
                'Times New Roman=times new roman,times;' +
                'Verdana=verdana,geneva;' +
                'Courier New=courier new,courier;',

            font_size_formats: '8pt 10pt 12pt 14pt 18pt 24pt 36pt',

            link_default_target: '_blank',

            setup: function(editor) {
                editor.on('init', function() {
                    editorReady = true;

                    if (pendingContent !== null) {
                        editor.setContent(pendingContent);
                        pendingContent = null;
                    }
                });
            }
        });
    }

    function destroyCampaignEditor() {
        if (tinymce.get('description')) {
            tinymce.get('description').remove();
        }
        editorReady = false;
        pendingContent = null;
    }

    function setEditorContent(html) {
        const editor = tinymce.get('description');
        if (editor && editorReady) {
            editor.setContent(html || '');
        } else {
            pendingContent = html || '';
        }
    }

    function getEditorContent() {
        const editor = tinymce.get('description');
        return editor ? editor.getContent() : $('#description').val();
    }

    // ==============================
    // Modal controls
    // ==============================
    function openModal() {
        document.getElementById('campaignModal').classList.remove('hidden');
        document.getElementById('campaignModal').classList.add('flex');
        initCampaignEditor();
    }

    function closeModal() {
        destroyCampaignEditor();
        document.getElementById('campaignModal').classList.add('hidden');
        document.getElementById('campaignModal').classList.remove('flex');
    }

    function openUploadModal(campaignId) {
        document.getElementById('uploadCampaignId').value = campaignId;
        document.getElementById('uploadLeadsModal').classList.remove('hidden');
        document.getElementById('uploadLeadsModal').classList.add('flex');
        $('#uploadForm')[0].reset();
        $('#uploadForm').data('submitting', false);
        $('#uploadSubmitBtn').prop('disabled', false).text('Upload');
    }

    function closeUploadModal() {
        document.getElementById('uploadLeadsModal').classList.add('hidden');
        document.getElementById('uploadLeadsModal').classList.remove('flex');
        $('#uploadForm')[0].reset();
        $('#uploadForm').data('submitting', false);
        $('#uploadSubmitBtn').prop('disabled', false).text('Upload');
    }

    function toDatetimeLocal(datetimeStr) {
        if (!datetimeStr) return '';
        return datetimeStr.replace(' ', 'T').substring(0, 16);
    }

    // ==============================
    // Load Campaigns (with filters, stats, pagination)
    // ==============================
    let currentPage = 1;
    let currentFilters = {};

    function loadCampaigns(page = 1, filters = {}) {
        currentPage = page;
        currentFilters = filters;
        let queryParams = $.param(Object.assign({}, filters, { page: page }));
        $.ajax({
            url: "<?php echo base_url('admin/manage-campaigns/campaigns/ajax_list'); ?>?" + queryParams,
            type: "GET",
            dataType: "json",
            success: function(response) {
                let html = '';
                if (response.data && response.data.length > 0) {
                    $.each(response.data, function(i, row) {
                        html += '<tr class="hover:bg-gray-50">';
                        html += '<td class="px-4 py-3 text-sm text-gray-700">' + row.no + '</td>';
                        html += '<td class="px-4 py-3 text-sm font-medium text-gray-900">' + row.title + '</td>';
                        html += '<td class="px-4 py-3 text-sm text-gray-600">' + row.start_date + ' - ' + row.end_date + '</td>';
                        html += '<td class="px-4 py-3">' + row.status_badge + '</td>';
                        html += '<td class="px-4 py-3 text-sm text-center font-semibold text-gray-800">' + row.total_leads + '</td>';
                        html += '<td class="px-4 py-3 text-sm text-center text-yellow-600">' + row.pending + '</td>';
                        html += '<td class="px-4 py-3 text-sm text-center text-green-600">' + row.sent + '</td>';
                        html += '<td class="px-4 py-3 text-sm text-center text-red-600">' + row.failed + '</td>';
                        html += '<td class="px-4 py-3 text-sm">' + row.actions + '</td>';
                        html += '</tr>';
                    });
                } else {
                    html = '<tr><td colspan="9" class="px-4 py-6 text-center text-sm text-gray-500">No campaigns found</td></tr>';
                }
                $('#campaignTableBody').html(html);

                // Pagination
                let pagHtml = '';
                if (response.pagination) {
                    const pg = response.pagination;
                    pagHtml += '<span>Showing page ' + pg.current_page + ' of ' + pg.total_pages + ' (' + pg.total_rows + ' total)</span>';
                    pagHtml += '<div class="flex gap-1">';
                    if (pg.current_page > 1) {
                        pagHtml += '<button class="page-btn px-3 py-1 rounded border text-blue-600 hover:bg-blue-50" data-page="' + (pg.current_page - 1) + '">Prev</button>';
                    }
                    if (pg.current_page < pg.total_pages) {
                        pagHtml += '<button class="page-btn px-3 py-1 rounded border text-blue-600 hover:bg-blue-50" data-page="' + (pg.current_page + 1) + '">Next</button>';
                    }
                    pagHtml += '</div>';
                }
                $('#paginationContainer').html(pagHtml);
            },
            error: function() {
                alert('Failed to load campaigns');
            }
        });
    }

    // ==============================
    // Document Ready
    // ==============================
    $(document).ready(function() {
        // Initial load
        loadCampaigns(1);

        // Filter form submit
        $('#filterForm').submit(function(e) {
            e.preventDefault();
            let filters = {
                search: $('[name="search"]').val(),
                status: $('[name="status"]').val(),
                start_from: $('[name="start_from"]').val(),
                start_to: $('[name="start_to"]').val()
            };
            loadCampaigns(1, filters);
        });

        // Reset filter
        $('#resetFilterBtn').click(function() {
            $('#filterForm')[0].reset();
            loadCampaigns(1, {});
        });

        // Pagination click
        $(document).on('click', '.page-btn', function() {
            let page = $(this).data('page');
            if (page) loadCampaigns(page, currentFilters);
        });

        // Add Campaign Button
        $('#addCampaignBtn').click(function() {
            $('#campaignForm')[0].reset();
            $('#campaignId').val('');
            $('#modalTitle').text('Add Campaign');
            $('#saveBtn').text('Save');
            openModal();
            setTimeout(() => setEditorContent(''), 100);
        });

        // Edit Campaign
        $(document).on('click', '.edit-btn', function() {
            var id = $(this).data('id');
            $.ajax({
                url: "<?php echo base_url('admin/manage-campaigns/campaigns/ajax_get/'); ?>" + id,
                type: "GET",
                dataType: "json",
                success: function(response) {
                    if(response.success) {
                        var c = response.data;
                        $('#campaignId').val(c.id);
                        $('#title').val(c.title);
                        $('#start_date').val(toDatetimeLocal(c.start_date));
                        $('#end_date').val(toDatetimeLocal(c.end_date));
                        $('#email_service').val(c.email_service || 'mailercloud');
                        $('#status').val(c.status);
                        $('#modalTitle').text('Edit Campaign');
                        $('#saveBtn').text('Update');
                        openModal();
                        function setWhenReady() {
                            if (editorReady) {
                                setEditorContent(c.description || '');
                            } else {
                                setTimeout(setWhenReady, 50);
                            }
                        }
                        setWhenReady();
                    } else {
                        alert(response.message);
                    }
                }
            });
        });

        // Save Campaign (Add/Update)
        $('#campaignForm').submit(function(e) {
            e.preventDefault();
            var id = $('#campaignId').val();
            var url = id ? "<?php echo base_url('admin/manage-campaigns/campaigns/ajax_update'); ?>" :
                          "<?php echo base_url('admin/manage-campaigns/campaigns/ajax_add'); ?>";

            var formData = $(this).serializeArray();
            for (var i = 0; i < formData.length; i++) {
                if (formData[i].name === 'description') {
                    formData[i].value = getEditorContent();
                    break;
                }
            }

            $.ajax({
                url: url,
                type: "POST",
                data: $.param(formData),
                dataType: "json",
                success: function(response) {
                    if(response.success) {
                        closeModal();
                        loadCampaigns(currentPage, currentFilters);
                        alert(response.message);
                    } else {
                        alert(response.message);
                    }
                },
                error: function() {
                    alert('Request failed');
                }
            });
        });

        // Delete Campaign
        $(document).on('click', '.delete-btn', function() {
            if(confirm('Are you sure you want to delete this campaign?')) {
                var id = $(this).data('id');
                $.ajax({
                    url: "<?php echo base_url('admin/manage-campaigns/campaigns/ajax_delete/'); ?>" + id,
                    type: "POST",
                    dataType: "json",
                    success: function(response) {
                        if(response.success) {
                            loadCampaigns(currentPage, currentFilters);
                            alert(response.message);
                        } else {
                            alert(response.message);
                        }
                    }
                });
            }
        });

        // Upload Leads Modal
        $(document).on('click', '.upload-btn', function() {
            var campaignId = $(this).data('id');
            openUploadModal(campaignId);
        });

        // Upload Form Submit (double-submit fix)
        $('#uploadForm').off('submit').on('submit', function(e) {
            e.preventDefault();
            if ($(this).data('submitting')) return;
            $(this).data('submitting', true);

            var $submitBtn = $('#uploadSubmitBtn');
            $submitBtn.prop('disabled', true).text('Uploading...');

            var formData = new FormData(this);

            $.ajax({
                url: "<?php echo base_url('admin/manage-campaigns/campaigns/ajax_upload_leads'); ?>",
                type: "POST",
                data: formData,
                contentType: false,
                processData: false,
                dataType: "json",
                success: function(response) {
                    if(response.success) {
                        alert(response.message);
                        closeUploadModal();
                        loadCampaigns(currentPage, currentFilters);
                    } else {
                        alert(response.message);
                    }
                },
                error: function() {
                    alert('Upload failed');
                },
                complete: function() {
                    $('#uploadForm').data('submitting', false);
                    $submitBtn.prop('disabled', false).text('Upload');
                }
            });
        });

        // Close modals on outside click
        document.getElementById('campaignModal').addEventListener('click', function(e) {
            if (e.target === this) closeModal();
        });
        document.getElementById('uploadLeadsModal').addEventListener('click', function(e) {
            if (e.target === this) closeUploadModal();
        });
    });
</script>