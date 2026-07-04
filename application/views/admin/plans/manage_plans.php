<div class="max-w-7xl mx-auto">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
        <h1 class="text-2xl font-bold text-gray-800"></h1>
        <button onclick="openPlanModal('add')" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-lg text-sm font-medium transition flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Add New Plan
        </button>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Feature</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Plan Level</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Duration</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">MRP</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Discount</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Taxes</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Monthly</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach ($plans as $plan): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900"><?= $plan['feature_name'] ?> (<?= $plan['feature_tag'] ?>)</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600"><?= $plan['plan_level'] ?></td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600"><?= $plan['duration'] ?></td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600">₹<?= number_format($plan['plan_mrp'], 2) ?></td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600"><?= $plan['plan_discount'] ?>%</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600"><?= $plan['plan_taxes'] ?>%</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600 font-semibold">₹<?= number_format($plan['plan_total'], 2) ?></td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600">₹<?= number_format($plan['monthly_cost'], 2) ?></td>
                        <td class="px-4 py-3 whitespace-nowrap text-center">
                            <button onclick='openPlanModal("edit", <?= htmlspecialchars(json_encode($plan), ENT_QUOTES, 'UTF-8') ?>)' class="text-indigo-600 hover:text-indigo-900 mr-3 text-sm">Edit</button>
                            <button onclick="deletePlan(<?= $plan['duration_id'] ?>)" class="text-red-600 hover:text-red-900 text-sm">Delete</button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if(empty($plans)): ?>
                    <tr><td colspan="9" class="px-6 py-12 text-center text-gray-400">No plans found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal for Add/Edit Plan -->
<div id="planModal" class="fixed inset-0 z-50 overflow-y-auto hidden" aria-modal="true">
    <div class="flex items-end sm:items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75" onclick="closePlanModal()"></div>
        <div class="relative inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle w-full max-w-2xl mx-4 sm:mx-0">
            <form id="planForm" enctype="multipart/form-data">
                <input type="hidden" name="plan_id" id="modal_plan_id">
             

                <div class="bg-white px-4 sm:px-6 pt-4 sm:pt-6 pb-3 sm:pb-4">
                    <h3 class="text-xl font-semibold text-gray-900 mb-5" id="modal_title">Add New Plan</h3>
                    
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
                            <!-- Feature -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Feature <span class="text-red-500">*</span></label>
                                <select name="feature_id" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500">
                                    <option value="">Select Feature</option>
                                    <?php foreach($features as $f): ?>
                                        <option value="<?= $f['feature_id'] ?>"><?= $f['feature_name'] ?> (<?= $f['feature_tag'] ?>)</option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <!-- Plan Level & Duration -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Plan Level *</label>
                                    <select name="plan_level" required class="w-full border border-gray-300 rounded-lg px-3 py-2">
                                        <option value="All Level">All Level</option>
                                        <option value="Entry To Mid Level">Entry To Mid Level</option>
                                        <option value="Senior To Executive Level">Senior To Executive Level</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Duration *</label>
                                    <select name="duration" required class="w-full border border-gray-300 rounded-lg px-3 py-2">
                                        <option value="1 Month">1 Month</option>
                                        <option value="2 Months">2 Months</option>
                                        <option value="3 Months">3 Months</option>
                                        <option value="6 Months">6 Months</option>
                                        <option value="Annual">Annual</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Experience Range -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Experience Range (optional)</label>
                                <input type="text" name="experience_range" class="w-full border border-gray-300 rounded-lg px-3 py-2" placeholder="e.g., 0-2 years">
                            </div>

                            <!-- Pricing fields -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">MRP (₹) *</label>
                                    <input type="number" step="0.01" name="plan_mrp" required class="w-full border border-gray-300 rounded-lg px-3 py-2" id="plan_mrp">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Discount (%) *</label>
                                    <input type="number" step="0.01" name="plan_discount" required class="w-full border border-gray-300 rounded-lg px-3 py-2" id="plan_discount">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Taxes (%) *</label>
                                    <input type="number" step="0.01" name="plan_taxes" required class="w-full border border-gray-300 rounded-lg px-3 py-2" id="plan_taxes">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Total Amount (₹) *</label>
                                    <input type="number" step="0.01" name="plan_total" required class="w-full border border-gray-300 rounded-lg px-3 py-2 bg-gray-50" id="plan_total" readonly>
                                </div>
                                <div class="sm:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Monthly Cost (₹) *</label>
                                    <input type="number" step="0.01" name="monthly_cost" required class="w-full border border-gray-300 rounded-lg px-3 py-2" id="monthly_cost">
                                    <p class="text-xs text-gray-500 mt-1">For multi-month plans, this is total divided by months (auto-calculated).</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-50 px-4 sm:px-6 py-3 sm:py-4 sm:flex sm:flex-row-reverse gap-3">
                    <button type="submit" class="w-full sm:w-auto px-5 py-2.5 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition" id="modal_submit_btn">Save</button>
                    <button type="button" onclick="closePlanModal()" class="mt-3 sm:mt-0 w-full sm:w-auto px-5 py-2.5 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Auto-calculate total and monthly cost
const mrpInput = document.getElementById('plan_mrp');
const discountInput = document.getElementById('plan_discount');
const taxesInput = document.getElementById('plan_taxes');
const totalInput = document.getElementById('plan_total');
const monthlyInput = document.getElementById('monthly_cost');
const durationSelect = document.querySelector('[name="duration"]');

function calculateTotal() {
    let mrp = parseFloat(mrpInput.value) || 0;
    let discount = parseFloat(discountInput.value) || 0;
    let taxes = parseFloat(taxesInput.value) || 0;
    
    let discounted = mrp - (mrp * discount / 100);
    let total = discounted + (discounted * taxes / 100);
    totalInput.value = total.toFixed(2);
    
    // Monthly cost calculation
    let duration = durationSelect.value;
    let months = 1;
    switch(duration) {
        case '1 Month': months = 1; break;
        case '2 Months': months = 2; break;
        case '3 Months': months = 3; break;
        case '6 Months': months = 6; break;
        case 'Annual': months = 12; break;
    }
    let monthly = total / months;
    monthlyInput.value = monthly.toFixed(2);
}

mrpInput.addEventListener('input', calculateTotal);
discountInput.addEventListener('input', calculateTotal);
taxesInput.addEventListener('input', calculateTotal);
durationSelect.addEventListener('change', calculateTotal);

// ================== MODAL LOGIC ==================
const modal = document.getElementById('planModal');
const form = document.getElementById('planForm');
const submitBtn = document.getElementById('modal_submit_btn');
const planIdInput = document.getElementById('modal_plan_id');
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

async function openPlanModal(mode, plan = null) {
    form.reset();
    planIdInput.value = '';
    calculateTotal(); // reset totals

    if (mode === 'edit' && plan) {
        document.getElementById('modal_title').innerText = 'Edit Plan';
        submitBtn.innerText = 'Update';
        planIdInput.value = plan.duration_id;
        
        document.querySelector('[name="feature_id"]').value = plan.feature_id;
        document.querySelector('[name="plan_level"]').value = plan.plan_level;
        document.querySelector('[name="duration"]').value = plan.duration;
        document.querySelector('[name="experience_range"]').value = plan.experience_range || '';
        document.querySelector('[name="plan_mrp"]').value = plan.plan_mrp;
        document.querySelector('[name="plan_discount"]').value = plan.plan_discount;
        document.querySelector('[name="plan_taxes"]').value = plan.plan_taxes;
        document.querySelector('[name="plan_total"]').value = plan.plan_total;
        document.querySelector('[name="monthly_cost"]').value = plan.monthly_cost;
    } else {
        document.getElementById('modal_title').innerText = 'Add New Plan';
        submitBtn.innerText = 'Save';
    }
    modal.classList.remove('hidden');
}

function closePlanModal() {
    modal.classList.add('hidden');
}

form.addEventListener('submit', async function(e) {
    e.preventDefault();
    showLoading(true);
    
    const formData = new FormData(form);
    appendCsrf(formData);
    
    const url = planIdInput.value ? '<?= base_url("admin/features/Plans/update_plan") ?>' : '<?= base_url("admin/Plans/add_plan") ?>';
    
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

async function deletePlan(id) {
    if (!confirm('Are you sure you want to delete this plan?')) return;
    const formData = new FormData();
    formData.append('id', id);
    appendCsrf(formData);
    try {
        const response = await fetch('<?= base_url("admin/features/Plans/delete_plan") ?>', {
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
    if (e.key === 'Escape' && !modal.classList.contains('hidden')) closePlanModal();
});
</script>