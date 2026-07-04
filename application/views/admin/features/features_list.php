
<div class="max-w-7xl mx-auto">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
        <h1 class="text-2xl font-bold text-gray-800"></h1>
        <button onclick="openModal('add')" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-lg text-sm font-medium transition flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Add New Feature
        </button>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Feature</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Service</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tag</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach ($features as $feature): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 whitespace-nowrap">
                            <div class="flex items-center">
                                <?php if($feature['feature_logo']): ?>
                                    <img src="<?= base_url($feature['feature_logo']) ?>" class="h-8 w-8 object-contain rounded bg-gray-100 p-1 mr-2">
                                <?php endif; ?>
                                <span class="text-sm font-medium text-gray-900"><?= $feature['feature_name'] ?></span>
                            </div>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600"><?= $feature['service_name'] ?></td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600"><?= $feature['feature_tag'] ?></td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs rounded-full <?= $feature['is_active'] ?>"><?= ucfirst($feature['is_active']) ?></span>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-center">
                            <button onclick='openModal("edit", <?= htmlspecialchars(json_encode($feature), ENT_QUOTES, 'UTF-8') ?>)' class="text-indigo-600 hover:text-indigo-900 mr-3 text-sm">Edit</button>
                            <button onclick="toggleStatus(<?= $feature['feature_id'] ?>, '<?= $feature['is_active'] ?>')" class="text-<?= ($feature['is_active']=='active') ? 'red' : 'green' ?>-600 hover:text-<?= ($feature['is_active']=='active') ? 'red' : 'green' ?>-900 text-sm">
                                <?= ($feature['is_active']=='active') ? 'Deactivate' : 'Activate' ?>
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if(empty($features)): ?>
                    <tr><td colspan="5" class="px-6 py-12 text-center text-gray-400">No features found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal -->
<div id="featureModal" class="fixed inset-0 z-50 overflow-y-auto hidden" aria-modal="true">
    <div class="flex items-end sm:items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75" onclick="closeModal()"></div>

        <div class="relative inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle w-full max-w-4xl mx-4 sm:mx-0">
            <form id="featureForm" enctype="multipart/form-data" x-data="featureFormData()" x-init="init()">
                <input type="hidden" name="feature_id" id="modal_feature_id">
                <input type="hidden" name="<?= $this->security->get_csrf_token_name() ?>" value="<?= $this->security->get_csrf_hash() ?>" id="csrf_token">

                <div class="bg-white px-4 sm:px-6 pt-4 sm:pt-6 pb-3 sm:pb-4">
				
				<!-- Loading Overlay -->
					<div id="modalLoading" class="hidden absolute inset-0 bg-white bg-opacity-75 flex items-center justify-center z-10 rounded-xl">
						<div class="flex flex-col items-center">
							<svg class="animate-spin h-10 w-10 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
								<circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
								<path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
							</svg>
							<span class="mt-2 text-sm text-gray-600">Loading feature data...</span>
						</div>
					</div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-5" id="modal_title">Add New Feature</h3>
                    
                    <div class="space-y-4 max-h-[70vh] overflow-y-auto pr-2">
                        <!-- Basic Section -->
                        <div class="grid grid-cols-1 gap-4">
                            <!-- Service -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Service <span class="text-red-500">*</span></label>
                                <select name="service_id" required class="w-full border border-gray-300 rounded-lg shadow-sm px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">Select Service</option>
                                    <?php foreach($services as $s): ?>
                                        <option value="<?= $s['service_id'] ?>"><?= $s['service_name'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <!-- Feature Name & Tag -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Feature Name *</label>
                                    <input type="text" name="feature_name" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Feature Tag *</label>
                                    <input type="text" name="feature_tag" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                </div>
                            </div>
                            <!-- Short Description -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Short Description *</label>
                                <textarea name="feature_short_description" rows="2" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"></textarea>
                            </div>
                            <!-- Full Description (TinyMCE) -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Full Description *</label>
                                <textarea name="feature_full_description" id="feature_full_description"></textarea>
                            </div>
                            <!-- Logo / Video Uploads -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Feature Logo</label>
                                    <input type="file" name="feature_logo" accept="image/*" class="w-full text-sm border border-gray-300 rounded-lg file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-blue-50 file:text-blue-700">
                                    <p class="text-xs text-gray-500 mt-1" id="current_logo_hint"></p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Benefit Logo</label>
                                    <input type="file" name="benefit_logo" accept="image/*" class="w-full text-sm border border-gray-300 rounded-lg file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-blue-50 file:text-blue-700">
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Video / GIF</label>
                                <input type="file" name="feature_video_gif" accept="video/*,image/gif" class="w-full text-sm border border-gray-300 rounded-lg file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-blue-50 file:text-blue-700">
                            </div>
                            <!-- Custom Label & Coupon -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Custom Label</label>
                                    <input type="text" name="feature_custom_label" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Coupon Discount</label>
                                    <input type="text" name="feature_coupon_discount" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                </div>
                            </div>
                            <!-- Status -->
							
							<div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div>
                                   <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                                    <select name="is_active" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <option value="draft">Draft</option>
                                    <option value="upcoming">Upcoming</option>
                                    <option value="active" selected>Active</option>
                                    <option value="inactive">Inactive</option>
                                    <option value="archived">Archived</option>
                                </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">redirect_url</label>
                                    <input type="url" name="redirect_url" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="https://example.com/custom-page">
                                </div>
                            </div>
							
                        </div>

                        <!-- Tags Section -->
                        <div class="border-t pt-3">
                            <h4 class="font-medium text-gray-800 mb-2">Tags</h4>
                            <template x-for="(tag, index) in tags" :key="index">
                                <div class="flex items-center gap-2 mb-2">
                                    <input type="text" :name="'tags[]'" x-model="tags[index]" class="flex-1 border border-gray-300 rounded-lg px-3 py-2" placeholder="Tag">
                                    <button type="button" @click="tags.splice(index,1)" class="text-red-500 text-xl">&times;</button>
                                </div>
                            </template>
                            <button type="button" @click="tags.push('')" class="text-sm text-blue-600 hover:underline">+ Add Tag</button>
                        </div>

                        <!-- Q&A Section -->
                        <div class="border-t pt-3">
                            <h4 class="font-medium text-gray-800 mb-2">Q&A</h4>
                            <template x-for="(qa, index) in qas" :key="index">
                                <div class="border border-gray-200 rounded p-3 mb-3 bg-gray-50">
                                    <div class="flex justify-end mb-1">
                                        <button type="button" @click="qas.splice(index,1)" class="text-red-500 text-sm">&times; Remove</button>
                                    </div>
                                    <input type="text" :name="'questions[]'" x-model="qa.question" class="w-full border border-gray-300 rounded-lg px-3 py-2 mb-2" placeholder="Question">
                                    <textarea :name="'answers[]'" x-model="qa.answer" rows="2" class="w-full border border-gray-300 rounded-lg px-3 py-2" placeholder="Answer"></textarea>
                                </div>
                            </template>
                            <button type="button" @click="qas.push({question:'', answer:''})" class="text-sm text-blue-600 hover:underline">+ Add Q&A</button>
                        </div>

                        <!-- Benefits Section -->
                        <div class="border-t pt-3">
                            <h4 class="font-medium text-gray-800 mb-2">Benefit Comparison</h4>
                            <!-- Headers -->
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 mb-3">
                                <input type="text" name="benefit_header_title" x-model="benefitHeader.title_label" placeholder="Title Label (e.g., Feature)" class="border border-gray-300 rounded-lg px-3 py-2">
                                <input type="text" name="benefit_col1_label" x-model="benefitHeader.col_1_label" placeholder="Column 1 Label" class="border border-gray-300 rounded-lg px-3 py-2">
                                <input type="text" name="benefit_col2_label" x-model="benefitHeader.col_2_label" placeholder="Column 2 Label" class="border border-gray-300 rounded-lg px-3 py-2">
                            </div>
                            <!-- Rows -->
                            <template x-for="(row, idx) in benefitRows" :key="idx">
                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 mb-2">
                                    <input type="text" :name="'benefit_titles[]'" x-model="row.benefit_title" placeholder="Benefit Title" class="border border-gray-300 rounded-lg px-3 py-2">
                                    <input type="text" :name="'col1_values[]'" x-model="row.col_1" placeholder="Column 1 Value" class="border border-gray-300 rounded-lg px-3 py-2">
                                    <input type="text" :name="'col2_values[]'" x-model="row.col_2" placeholder="Column 2 Value" class="border border-gray-300 rounded-lg px-3 py-2">
                                    <div class="sm:col-span-3 flex justify-end">
                                        <button type="button" @click="benefitRows.splice(idx,1)" class="text-red-500 text-sm">Remove</button>
                                    </div>
                                </div>
                            </template>
                            <button type="button" @click="benefitRows.push({benefit_title:'', col_1:'', col_2:''})" class="text-sm text-blue-600 hover:underline">+ Add Benefit Row</button>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-50 px-4 sm:px-6 py-3 sm:py-4 sm:flex sm:flex-row-reverse gap-3">
                    <button type="submit" class="w-full sm:w-auto px-5 py-2.5 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition" id="modal_submit_btn">Save</button>
                    <button type="button" onclick="closeModal()" class="mt-3 sm:mt-0 w-full sm:w-auto px-5 py-2.5 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// ================== INIT TINYMCE ==================
tinymce.init({
    selector: '#feature_full_description',
    height: 250,
    menubar: false,
    plugins: 'lists link',
    toolbar: 'undo redo | bold italic | bullist numlist | link',
    setup: function(editor) {
        editor.on('change', function() {
            editor.save();
        });
    }
});

// ================== ALPINE COMPONENT ==================
function featureFormData() {
    return {
        tags: [],
        qas: [],
        benefitHeader: { title_label: '', col_1_label: '', col_2_label: '' },
        benefitRows: [],
        init() {
            window.alpineFeatureComponent = this;
        }
    }
}

// ================== DOM ELEMENTS ==================
const modal = document.getElementById('featureModal');
const form = document.getElementById('featureForm');
const submitBtn = document.getElementById('modal_submit_btn');
const featureIdInput = document.getElementById('modal_feature_id');
const loadingOverlay = document.getElementById('modalLoading');

// Helper to show/hide loading
function showLoading(show) {
    if (loadingOverlay) {
        loadingOverlay.classList.toggle('hidden', !show);
    }
    submitBtn.disabled = show;
}

// ================== CSRF HELPER ==================
function appendCsrfToFormData(formData) {
    if (typeof getCSRFName === 'function' && typeof getCSRFToken === 'function') {
        formData.append(getCSRFName(), getCSRFToken());
    }
}

// ================== OPEN MODAL ==================
async function openModal(mode, feature = null) {
    // Reset basic form fields
    form.reset();
    featureIdInput.value = '';

    if (tinymce.get('feature_full_description')) {
        tinymce.get('feature_full_description').setContent('');
    }

    const hint = document.querySelector('#current_logo_hint');
    if (hint) hint.innerHTML = '';

    // Reset Alpine data
    if (window.alpineFeatureComponent) {
        window.alpineFeatureComponent.tags = [];
        window.alpineFeatureComponent.qas = [];
        window.alpineFeatureComponent.benefitHeader = { title_label: '', col_1_label: '', col_2_label: '' };
        window.alpineFeatureComponent.benefitRows = [];
    }

    if (mode === 'edit' && feature) {
        submitBtn.innerText = 'Update';
        featureIdInput.value = feature.feature_id;

        // Populate basic fields (immediate)
        document.querySelector('[name="service_id"]').value = feature.service_id;
        document.querySelector('[name="feature_name"]').value = feature.feature_name;
        document.querySelector('[name="feature_tag"]').value = feature.feature_tag;
        document.querySelector('[name="feature_short_description"]').value = feature.feature_short_description;
        tinymce.get('feature_full_description').setContent(feature.feature_full_description || '');
        document.querySelector('[name="feature_custom_label"]').value = feature.feature_custom_label || '';
        document.querySelector('[name="feature_coupon_discount"]').value = feature.feature_coupon_discount || '';
        document.querySelector('[name="is_active"]').value = feature.is_active;
		document.querySelector('[name="redirect_url"]').value = feature.redirect_url || '';
		
        if (feature.feature_logo) {
            hint.innerHTML = `Current: ${feature.feature_logo.split('/').pop()}`;
        }

        // Show loading overlay while fetching related data
        showLoading(true);
        modal.classList.remove('hidden'); // show modal immediately with loading

        try {
            const formData = new FormData();
            appendCsrfToFormData(formData);
            const response = await fetch(
                `<?= base_url('admin/features/Features/get_feature_related/') ?>${feature.feature_id}`,
                {
                    method: 'POST',
                    body: formData,
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                }
            );
            const data = await response.json();

            if (data.csrf_token && typeof updateCSRFToken === 'function') {
                updateCSRFToken(data.csrf_token.hash, data.csrf_token.name);
            }

            if (window.alpineFeatureComponent) {
                window.alpineFeatureComponent.tags = data.tags || [];
                window.alpineFeatureComponent.qas = data.qas || [];
                window.alpineFeatureComponent.benefitHeader = data.benefitHeader || { title_label: '', col_1_label: '', col_2_label: '' };
                window.alpineFeatureComponent.benefitRows = data.benefitRows || [];
            }
        } catch (error) {
            console.error('Fetch error:', error);
            alert('Could not load feature details');
        } finally {
            showLoading(false); // hide loading whether success or fail
        }
    } else {
        submitBtn.innerText = 'Save';
        document.querySelector('[name="is_active"]').value = 'active';
        modal.classList.remove('hidden');
    }
}

function closeModal() {
    modal.classList.add('hidden');
    showLoading(false); // ensure loading is hidden if modal closed during fetch
}

// ================== FORM SUBMIT ==================
form.addEventListener('submit', async function(e) {
    e.preventDefault();
    tinymce.triggerSave();

    const formData = new FormData(form);
    appendCsrfToFormData(formData);

    const url = featureIdInput.value
        ? '<?= base_url("admin/features/Features/update_feature") ?>'
        : '<?= base_url("admin/features/Features/add_feature") ?>';

    submitBtn.disabled = true;
    submitBtn.innerText = 'Saving...';

    try {
        const response = await fetch(url, {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
        const result = await response.json();

        if (result.csrf_token && typeof updateCSRFToken === 'function') {
            updateCSRFToken(result.csrf_token.hash, result.csrf_token.name);
        }

        if (result.success) {
            alert(result.message);
            location.reload();
        } else {
            alert(result.message || 'Error saving feature');
        }
    } catch (error) {
        console.error(error);
        alert('Network error. Please try again.');
    } finally {
        submitBtn.disabled = false;
        submitBtn.innerText = featureIdInput.value ? 'Update' : 'Save';
    }
});

// ================== TOGGLE STATUS ==================
async function toggleStatus(id, currentStatus) {
    const newStatus = currentStatus === 'active' ? 'inactive' : 'active';
    if (!confirm(`Change status to ${newStatus}?`)) return;

    const formData = new FormData();
    formData.append('id', id);
    formData.append('current_status', currentStatus);
    appendCsrfToFormData(formData);

    try {
        const response = await fetch('<?= base_url("admin/features/Features/toggle_status") ?>', {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
        const result = await response.json();
        if (result.csrf_token && typeof updateCSRFToken === 'function') {
            updateCSRFToken(result.csrf_token.hash, result.csrf_token.name);
        }
        if (result.success) {
            location.reload();
        } else {
            alert('Failed to update status');
        }
    } catch (error) {
        alert('Network error');
    }
}

// ================== ESCAPE KEY ==================
document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && !modal.classList.contains('hidden')) {
        closeModal();
    }
});
</script>
