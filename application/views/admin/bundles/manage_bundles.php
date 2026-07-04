<div class="max-w-7xl mx-auto">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
        <h1 class="text-2xl font-bold text-gray-800"></h1>
        <button onclick="openBundleModal('add')" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-lg text-sm font-medium transition flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Add New Bundle
        </button>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Bundle Name</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Slug</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Features</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach ($bundles as $bundle): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900"><?= $bundle['bundle_name'] ?></td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600"><?= $bundle['bundle_slug'] ?></td>
                        <td class="px-4 py-3 text-sm text-gray-600 max-w-md truncate"><?= $bundle['feature_names'] ?: 'No features' ?></td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs rounded-full <?= $bundle['is_active'] == 1 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' ?>">
                                <?= $bundle['is_active'] == 1 ? 'Active' : 'Inactive' ?>
                            </span>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-center">
                            <button onclick='openBundleModal("edit", <?= htmlspecialchars(json_encode($bundle), ENT_QUOTES, 'UTF-8') ?>)' class="text-indigo-600 hover:text-indigo-900 mr-3 text-sm">Edit</button>
                            <button onclick="deleteBundle(<?= $bundle['bundle_id'] ?>)" class="text-red-600 hover:text-red-900 text-sm">Delete</button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if(empty($bundles)): ?>
                    <tr><td colspan="5" class="px-6 py-12 text-center text-gray-400">No bundles found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal for Add/Edit Bundle -->
<div id="bundleModal" class="fixed inset-0 z-50 overflow-y-auto hidden" aria-modal="true">
    <div class="flex items-end sm:items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75" onclick="closeBundleModal()"></div>
        <div class="relative inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle w-full max-w-2xl mx-4 sm:mx-0">
            <form id="bundleForm" enctype="multipart/form-data">
                <input type="hidden" name="bundle_id" id="modal_bundle_id">
                <!-- No static CSRF input – appended via JS -->

                <div class="bg-white px-4 sm:px-6 pt-4 sm:pt-6 pb-3 sm:pb-4">
                    <h3 class="text-xl font-semibold text-gray-900 mb-5" id="modal_title">Add New Bundle</h3>
                    
                    <!-- Loading Overlay -->
                    <div id="modalLoading" class="hidden absolute inset-0 bg-white bg-opacity-75 flex items-center justify-center z-10 rounded-xl">
                        <div class="flex flex-col items-center">
                            <svg class="animate-spin h-10 w-10 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span class="mt-2 text-sm text-gray-600">Processing...</span>
                        </div>
                    </div>

                    <div class="space-y-4 max-h-[70vh] overflow-y-auto pr-2">
                        <div class="grid grid-cols-1 gap-4">
                            <!-- Bundle Name -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Bundle Name <span class="text-red-500">*</span></label>
                                <input type="text" name="bundle_name" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500">
                            </div>

                            <!-- Description -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                                <textarea name="bundle_description" rows="3" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500"></textarea>
                            </div>

                            <!-- Select Features (multi-select) -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Select Features <span class="text-red-500">*</span></label>
                                <select name="features[]" multiple required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500" size="6">
                                    <?php foreach($features as $f): ?>
                                        <option value="<?= $f['feature_id'] ?>"><?= $f['feature_name'] ?> (<?= $f['feature_tag'] ?>)</option>
                                    <?php endforeach; ?>
                                </select>
                                <p class="text-xs text-gray-500 mt-1">Hold Ctrl (Windows) or Cmd (Mac) to select multiple features.</p>
                            </div>

                            <!-- Status -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                                <select name="is_active" class="w-full border border-gray-300 rounded-lg px-3 py-2">
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-50 px-4 sm:px-6 py-3 sm:py-4 sm:flex sm:flex-row-reverse gap-3">
                    <button type="submit" class="w-full sm:w-auto px-5 py-2.5 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition" id="modal_submit_btn">Save</button>
                    <button type="button" onclick="closeBundleModal()" class="mt-3 sm:mt-0 w-full sm:w-auto px-5 py-2.5 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// ================== DOM ELEMENTS ==================
const modal = document.getElementById('bundleModal');
const form = document.getElementById('bundleForm');
const submitBtn = document.getElementById('modal_submit_btn');
const bundleIdInput = document.getElementById('modal_bundle_id');
const loadingOverlay = document.getElementById('modalLoading');

function showLoading(show) {
    if (loadingOverlay) loadingOverlay.classList.toggle('hidden', !show);
    submitBtn.disabled = show;
}

// CSRF helpers are already defined globally in master.php:
// getCSRFName(), getCSRFToken(), updateCSRFToken(token, name)
function appendCsrf(formData) {
    formData.append(getCSRFName(), getCSRFToken());
}

async function openBundleModal(mode, bundle = null) {
    form.reset();
    bundleIdInput.value = '';

    if (mode === 'edit' && bundle) {
        document.getElementById('modal_title').innerText = 'Edit Bundle';
        submitBtn.innerText = 'Update';
        bundleIdInput.value = bundle.bundle_id;
        
        document.querySelector('[name="bundle_name"]').value = bundle.bundle_name;
        document.querySelector('[name="bundle_description"]').value = bundle.bundle_description || '';
        document.querySelector('[name="is_active"]').value = bundle.is_active;
        
        // Fetch full bundle with features (since list view only has names)
        try {
            showLoading(true);
            const formData = new FormData();
            appendCsrf(formData);
            const response = await fetch(`<?= base_url("admin/features/Bundles/get_bundle/") ?>${bundle.bundle_id}`, {
                method: 'POST',
                body: formData,
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            const result = await response.json();
            if (result.csrf_token) updateCSRFToken(result.csrf_token.hash, result.csrf_token.name);
            if (result.success && result.data) {
                const features = result.data.features || [];
                // Select options in multi-select
                const select = document.querySelector('[name="features[]"]');
                for (let i = 0; i < select.options.length; i++) {
                    select.options[i].selected = features.includes(parseInt(select.options[i].value));
                }
            }
        } catch (err) {
            console.error(err);
            alert('Could not load bundle features');
        } finally {
            showLoading(false);
        }
    } else {
        document.getElementById('modal_title').innerText = 'Add New Bundle';
        submitBtn.innerText = 'Save';
        // Clear multi-select
        const select = document.querySelector('[name="features[]"]');
        for (let i = 0; i < select.options.length; i++) {
            select.options[i].selected = false;
        }
    }
    modal.classList.remove('hidden');
}

function closeBundleModal() {
    modal.classList.add('hidden');
}

form.addEventListener('submit', async function(e) {
    e.preventDefault();
    showLoading(true);
    
    const formData = new FormData(form);
    appendCsrf(formData);
    
    const url = bundleIdInput.value ? '<?= base_url("admin/features/Bundles/update_bundle") ?>' : '<?= base_url("admin/features/Bundles/add_bundle") ?>';
    
    try {
        const response = await fetch(url, {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
        const result = await response.json();
        if (result.csrf_token) {
            updateCSRFToken(result.csrf_token.hash, result.csrf_token.name);
        }
        if (result.success) {
            alert(result.message);
            location.reload();
        } else {
            alert(result.message || 'Error');
        }
    } catch (err) {
        alert('Network error');
    } finally {
        showLoading(false);
    }
});

async function deleteBundle(id) {
    if (!confirm('Are you sure you want to delete this bundle? This will remove all feature associations.')) return;
    const formData = new FormData();
    formData.append('id', id);
    appendCsrf(formData);
    try {
        const response = await fetch('<?= base_url("admin/features/Bundles/delete_bundle") ?>', {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
        const result = await response.json();
        if (result.csrf_token) {
            updateCSRFToken(result.csrf_token.hash, result.csrf_token.name);
        }
        if (result.success) {
            alert(result.message);
            location.reload();
        } else {
            alert(result.message);
        }
    } catch(err) {
        alert('Network error');
    }
}

// Escape key to close modal
document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && !modal.classList.contains('hidden')) closeBundleModal();
});
</script>