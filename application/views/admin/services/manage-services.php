<div class="max-w-7xl mx-auto">
        <!-- Header with Add Button -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
            <h1 class="text-2xl font-bold text-gray-800"></h1>
            <button onclick="openModal('add')" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-lg text-sm font-medium transition flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Add New Service
            </button>
        </div>

        <!-- Services Table -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Service</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Icon</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($services as $service): ?>
                        <tr class="hover:bg-gray-50 transition <?= $service['is_active'] == 0 ? 'bg-gray-50' : '' ?>">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900"><?= ucfirst($service['service_name']) ?></div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-600 max-w-xs truncate"><?= strip_tags($service['service_description']) ?></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <img src="<?= base_url($service['service_icon']) ?>" class="h-10 w-10 object-contain rounded bg-gray-100 p-1" alt="icon">
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full <?= $service['is_active'] ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' ?>">
                                    <?= $service['is_active'] ? 'Active' : 'Inactive' ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <button onclick='openModal("edit", <?= json_encode($service) ?>)' class="text-indigo-600 hover:text-indigo-900 mr-3 text-sm font-medium">Edit</button>
                                <button onclick="toggleStatus(<?= $service['service_id'] ?>, <?= $service['is_active'] ?>)" class="text-<?= $service['is_active'] ? 'red' : 'green' ?>-600 hover:text-<?= $service['is_active'] ? 'red' : 'green' ?>-900 text-sm font-medium">
                                    <?= $service['is_active'] ? 'Deactivate' : 'Activate' ?>
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($services)): ?>
                        <tr><td colspan="5" class="px-6 py-12 text-center text-gray-400">No services found. Click "Add New Service" to create one.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>


<!-- Modal Popup -->
<div id="serviceModal" class="fixed inset-0 z-50 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end sm:items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:p-0">
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="closeModal()"></div>

        <!-- Modal panel - mobile optimized -->
        <div class="relative inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle w-full max-w-md sm:max-w-2xl mx-4 sm:mx-0">
            <form id="serviceForm" enctype="multipart/form-data">
                <input type="hidden" name="service_id" id="modal_service_id">
                <!-- CSRF Hidden Input -->
                <input type="hidden" name="<?= $this->security->get_csrf_token_name() ?>" value="<?= $this->security->get_csrf_hash() ?>" id="csrf_token">

                <div class="bg-white px-4 sm:px-6 pt-4 sm:pt-6 pb-3 sm:pb-4">
                    <h3 class="text-xl font-semibold text-gray-900 mb-5 sm:mb-6" id="modal_title">Add New Service</h3>
                    
                    <div class="space-y-4 sm:space-y-5">
                        <!-- Service Name -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Service Name <span class="text-red-500">*</span></label>
                            <input type="text" name="service_name" id="service_name" required 
                                   class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 px-3 py-2"
                                   placeholder="e.g. Candidate Growth">
                        </div>

                        <!-- Service Icon -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Service Icon <span class="text-red-500">*</span></label>
                            <input type="file" name="service_icon" id="service_icon" accept="image/*" class="w-full text-sm border-gray-300 rounded-lg file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                            <p class="text-xs text-gray-500 mt-1" id="current_icon_hint"></p>
                        </div>

                        <!-- Description -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Description <span class="text-red-500">*</span></label>
                            <textarea name="service_description" id="service_description" rows="4" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500"></textarea>
                        </div>

                        <!-- Status -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                            <div class="flex items-center space-x-6">
                                <label class="inline-flex items-center">
                                    <input type="radio" name="is_active" value="1" class="form-radio h-4 w-4 text-blue-600" checked> <span class="ml-2">Active</span>
                                </label>
                                <label class="inline-flex items-center">
                                    <input type="radio" name="is_active" value="0" class="form-radio h-4 w-4 text-red-600"> <span class="ml-2">Inactive</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="bg-gray-50 px-4 sm:px-6 py-3 sm:py-4 sm:flex sm:flex-row-reverse gap-3">
                    <button type="submit" class="w-full sm:w-auto px-5 py-2.5 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition" id="modal_submit_btn">Save</button>
                    <button type="button" onclick="closeModal()" class="mt-3 sm:mt-0 w-full sm:w-auto px-5 py-2.5 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Initialize TinyMCE with responsive height
    tinymce.init({
        selector: '#service_description',
        height: window.innerWidth < 640 ? 150 : 200,
        menubar: false,
        plugins: 'lists link',
        toolbar: 'undo redo | bold italic | bullist numlist | link',
        setup: function(editor) {
            editor.on('change', function() {
                editor.save();
            });
        }
    });

    // Modal elements
    const modal = document.getElementById('serviceModal');
    const form = document.getElementById('serviceForm');
    const modalTitle = document.getElementById('modal_title');
    const submitBtn = document.getElementById('modal_submit_btn');
    const serviceIdInput = document.getElementById('modal_service_id');
    const serviceNameInput = document.getElementById('service_name');
    const currentIconHint = document.getElementById('current_icon_hint');
    const csrfInput = document.getElementById('csrf_token');

    // Helper to refresh CSRF token in hidden input
    function refreshCsrfInput() {
        if (typeof getCSRFName === 'function' && typeof getCSRFToken === 'function') {
            csrfInput.name = getCSRFName();
            csrfInput.value = getCSRFToken();
        }
    }
    refreshCsrfInput();

    function openModal(mode, service = null) {
        // Reset form
        form.reset();
        serviceIdInput.value = '';
        currentIconHint.innerHTML = '';
        serviceNameInput.value = '';
        
        // Reset TinyMCE content
        if (tinymce.get('service_description')) {
            tinymce.get('service_description').setContent('');
        } else {
            document.getElementById('service_description').value = '';
        }
        
        if (mode === 'edit' && service) {
            modalTitle.innerText = 'Edit Service';
            submitBtn.innerText = 'Update';
            serviceIdInput.value = service.service_id;
            
            serviceNameInput.value = service.service_name || '';
            
            const desc = service.service_description || '';
            setTimeout(() => {
                const editor = tinymce.get('service_description');
                if (editor) {
                    editor.setContent(desc);
                } else {
                    document.getElementById('service_description').value = desc;
                }
            }, 100);
            
            const statusRadio = document.querySelector(`input[name="is_active"][value="${service.is_active}"]`);
            if (statusRadio) statusRadio.checked = true;
            
            const iconName = service.service_icon ? service.service_icon.split('/').pop() : 'none';
            currentIconHint.innerHTML = `<span class="text-gray-500">Current: ${iconName}</span>`;
        } else {
            modalTitle.innerText = 'Add New Service';
            submitBtn.innerText = 'Save';
            document.querySelector('input[name="is_active"][value="1"]').checked = true;
        }
        
        refreshCsrfInput();
        modal.classList.remove('hidden');
    }

    function closeModal() {
        modal.classList.add('hidden');
    }

    // Form submission via AJAX
    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const formData = new FormData(form);
        const url = serviceIdInput.value 
            ? '<?= base_url("admin/services/Services/update_service") ?>'
            : '<?= base_url("admin/services/Services/add_service") ?>';
        
        submitBtn.disabled = true;
        submitBtn.innerText = 'Saving...';
        
        try {
            const response = await fetch(url, {
                method: 'POST',
                body: formData
            });
            const result = await response.json();
            
            if (result.csrf_token) {
                if (typeof updateCSRFToken === 'function') {
                    updateCSRFToken(result.csrf_token.hash, result.csrf_token.name);
                }
                refreshCsrfInput();
            }
            
            if (result.success) {
                alert(result.message);
                location.reload();
            } else {
                alert('Error: ' + (result.message || 'Unknown error'));
            }
        } catch (error) {
            alert('Network error. Please try again.');
        } finally {
            submitBtn.disabled = false;
            submitBtn.innerText = serviceIdInput.value ? 'Update' : 'Save';
        }
    });

    // Toggle status
    async function toggleStatus(id, currentStatus) {
        if (!confirm(`Are you sure you want to ${currentStatus ? 'deactivate' : 'activate'} this service?`)) return;
        
        const formData = new FormData();
        formData.append('id', id);
        if (typeof getCSRFName === 'function' && typeof getCSRFToken === 'function') {
            formData.append(getCSRFName(), getCSRFToken());
        }
        
        try {
            const response = await fetch('<?= base_url("admin/services/Services/toggle_service_status") ?>', {
                method: 'POST',
                body: formData
            });
            const result = await response.json();
            
            if (result.csrf_token) {
                if (typeof updateCSRFToken === 'function') {
                    updateCSRFToken(result.csrf_token.hash, result.csrf_token.name);
                }
                refreshCsrfInput();
            }
            
            if (result.success) {
                location.reload();
            } else {
                alert('Failed to update status.');
            }
        } catch (error) {
            alert('Network error.');
        }
    }

    // Close modal on Escape key
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && !modal.classList.contains('hidden')) {
            closeModal();
        }
    });
</script>