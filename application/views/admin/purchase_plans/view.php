<div class="bg-white rounded-lg shadow">
    <div class="p-6 border-b">
        <div class="flex justify-between items-center">
            <h2 class="text-2xl font-bold text-gray-800">Purchase Plan Details</h2>
            <a href="<?= base_url('admin/purchase-plans') ?>" 
               class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                <i class="fas fa-arrow-left mr-2"></i> Back to List
            </a>
        </div>
    </div>

    <div class="p-6">
        <?php if (!empty($plan)): ?>
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            
            <!-- Plan Information -->
            <div class="space-y-6">
                <div class="bg-blue-50 p-4 rounded-lg">
                    <h3 class="text-lg font-medium text-blue-700 mb-4">Plan Information</h3>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-600">Plan ID</p>
                            <p class="font-medium">#<?= $plan['id'] ?></p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Status</p>
                            <span class="px-2 py-1 text-xs rounded-full <?= 
                                $plan['status'] == 'active' ? 'bg-green-100 text-green-800' : 
                                ($plan['status'] == 'expired' ? 'bg-yellow-100 text-yellow-800' : 
                                ($plan['status'] == 'refunded' ? 'bg-purple-100 text-purple-800' : 
                                ($plan['status'] == 'cancelled' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800'))) ?>">
                                <?= ucfirst($plan['status'] ?? 'Unknown') ?>
                            </span>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Feature</p>
                            <p class="font-medium"><?= $plan['feature_name'] ?? 'N/A' ?></p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Plan Level</p>
                            <p class="font-medium"><?= $plan['plan_level'] ?? 'N/A' ?></p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Duration</p>
                            <p class="font-medium"><?= $plan['duration'] ?? 'N/A' ?></p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Experience Range</p>
                            <p class="font-medium"><?= $plan['experience_range'] ?? 'N/A' ?></p>
                        </div>
                    </div>
                </div>

                <!-- User Information -->
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h3 class="text-lg font-medium text-gray-700 mb-4">User Information</h3>
                    <div class="space-y-3">
                        <div>
                            <p class="text-sm text-gray-600">Name</p>
                            <p class="font-medium"><?= $user['name'] ?? 'N/A' ?></p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Email</p>
                            <p class="font-medium"><?= $user['email'] ?? 'N/A' ?></p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Mobile</p>
                            <p class="font-medium"><?= $user['mobile'] ?? 'N/A' ?></p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">User Type</p>
                            <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">
                                Candidate
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment & Timeline -->
            <div class="space-y-6">
                <!-- Payment Information -->
                <div class="bg-green-50 p-4 rounded-lg">
                    <h3 class="text-lg font-medium text-green-700 mb-4">Payment Information</h3>
                    
                    <?php if (!empty($payment)): ?>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-600">Payment ID</p>
                            <p class="font-medium"><?= $payment['payment_id'] ?? 'N/A' ?></p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Order ID</p>
                            <p class="font-medium"><?= $payment['order_id'] ?? 'N/A' ?></p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Amount</p>
                            <p class="font-medium text-lg">₹<?= $payment['amount'] ?? 0 ?></p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Payment Status</p>
                            <span class="px-2 py-1 text-xs rounded-full <?= 
                                $payment['status'] == 'paid' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' ?>">
                                <?= ucfirst($payment['status'] ?? 'Unknown') ?>
                            </span>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Payment Method</p>
                            <p class="font-medium"><?= ucfirst($payment['method'] ?? 'N/A') ?></p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Payment Date</p>
                            <p class="font-medium"><?= !empty($payment['created_at']) ? date('d M Y, h:i A', strtotime($payment['created_at'])) : 'N/A' ?></p>
                        </div>
                    </div>
                    
                    <!-- Refund Status if any -->
                    <?php if (isset($payment['refund_status']) && $payment['refund_status'] !== 'none'): ?>
                    <div class="mt-4 pt-4 border-t border-green-200">
                        <h4 class="text-md font-medium text-gray-700 mb-2">Refund Information</h4>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm text-gray-600">Refund Status</p>
                                <span class="px-2 py-1 text-xs rounded-full <?= 
                                    $payment['refund_status'] == 'processed' ? 'bg-purple-100 text-purple-800' : 'bg-yellow-100 text-yellow-800' ?>">
                                    <?= ucfirst($payment['refund_status'] ?? 'none') ?>
                                </span>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Refund Amount</p>
                                <p class="font-medium">₹<?= $payment['refund_amount'] ?? 0 ?></p>
                            </div>
                            <?php if (!empty($payment['refund_date'])): ?>
                            <div>
                                <p class="text-sm text-gray-600">Refund Date</p>
                                <p class="font-medium"><?= date('d M Y, h:i A', strtotime($payment['refund_date'])) ?></p>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <?php else: ?>
                    <div class="p-3 bg-yellow-50 rounded border border-yellow-200">
                        <p class="text-yellow-700">
                            <i class="fas fa-exclamation-triangle mr-2"></i>
                            No payment information found for this plan.
                        </p>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Plan Timeline -->
                <div class="bg-yellow-50 p-4 rounded-lg">
                    <h3 class="text-lg font-medium text-yellow-700 mb-4">Plan Timeline</h3>
                    <div class="space-y-4">
                        <div class="flex items-center">
                            <div class="w-2 h-2 bg-green-500 rounded-full mr-3"></div>
                            <div class="flex-1">
                                <p class="text-sm font-medium">Start Date</p>
                                <p class="text-sm text-gray-600"><?= !empty($plan['start_date']) ? date('d M Y, h:i A', strtotime($plan['start_date'])) : 'N/A' ?></p>
                            </div>
                        </div>
                        <div class="flex items-center">
                            <div class="w-2 h-2 bg-red-500 rounded-full mr-3"></div>
                            <div class="flex-1">
                                <p class="text-sm font-medium">End Date</p>
                                <p class="text-sm text-gray-600"><?= !empty($plan['end_date']) ? date('d M Y, h:i A', strtotime($plan['end_date'])) : 'N/A' ?></p>
                            </div>
                        </div>
                        <div class="flex items-center">
                            <div class="w-2 h-2 bg-blue-500 rounded-full mr-3"></div>
                            <div class="flex-1">
                                <p class="text-sm font-medium">Purchase Date</p>
                                <p class="text-sm text-gray-600"><?= !empty($plan['created_at']) ? date('d M Y, h:i A', strtotime($plan['created_at'])) : 'N/A' ?></p>
                            </div>
                        </div>
                        <div class="flex items-center">
                            <div class="w-2 h-2 bg-gray-500 rounded-full mr-3"></div>
                            <div class="flex-1">
                                <p class="text-sm font-medium">Last Updated</p>
                                <p class="text-sm text-gray-600"><?= !empty($plan['updated_at']) ? date('d M Y, h:i A', strtotime($plan['updated_at'])) : 'N/A' ?></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h3 class="text-lg font-medium text-gray-700 mb-4">Actions</h3>
                    <div class="flex flex-wrap gap-3">
                        <?php if ($plan['status'] == 'active'): ?>
                        <button onclick="updatePlanStatus(<?= $plan['id'] ?>, 'expired')" 
                                class="px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700">
                            <i class="fas fa-clock mr-2"></i> Mark as Expired
                        </button>
                        <button onclick="updatePlanStatus(<?= $plan['id'] ?>, 'cancelled')" 
                                class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                            <i class="fas fa-times mr-2"></i> Cancel Plan
                        </button>
                        <?php elseif ($plan['status'] == 'expired' || $plan['status'] == 'cancelled'): ?>
                        <button onclick="updatePlanStatus(<?= $plan['id'] ?>, 'active')" 
                                class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                            <i class="fas fa-check mr-2"></i> Reactivate Plan
                        </button>
                        <?php endif; ?>
                        
                        <?php if (empty($refund_request) && $plan['status'] == 'active' && !empty($payment['order_id'])): ?>
                        <a href="<?= base_url('admin/refunds') ?>?search=<?= $payment['order_id'] ?>" 
                           class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700">
                            <i class="fas fa-undo-alt mr-2"></i> Process Refund
                        </a>
                        <?php endif; ?>
                        
                        <?php if (!empty($refund_request)): ?>
                        <a href="<?= base_url('admin/refunds/view/' . $refund_request['id']) ?>" 
                           class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                            <i class="fas fa-external-link-alt mr-2"></i> View Refund
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Refund Request Section -->
        <?php if (!empty($refund_request)): ?>
        <div class="mt-6 bg-red-50 p-4 rounded-lg border border-red-200">
            <h3 class="text-lg font-medium text-red-700 mb-4">Refund Request</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <p class="text-sm text-gray-600">Request ID</p>
                    <p class="font-medium">#<?= $refund_request['id'] ?></p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Status</p>
                    <span class="px-2 py-1 text-xs rounded-full <?= 
                        $refund_request['status'] == 'pending' ? 'bg-yellow-100 text-yellow-800' : 
                        ($refund_request['status'] == 'approved' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800') ?>">
                        <?= ucfirst($refund_request['status']) ?>
                    </span>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Requested Amount</p>
                    <p class="font-medium">₹<?= $refund_request['amount'] ?></p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Requested Date</p>
                    <p class="font-medium"><?= date('d M Y, h:i A', strtotime($refund_request['requested_at'])) ?></p>
                </div>
                <div class="md:col-span-2">
                    <p class="text-sm text-gray-600">Reason</p>
                    <p class="font-medium"><?= $refund_request['reason'] ?></p>
                </div>
                <?php if (!empty($refund_request['admin_notes'])): ?>
                <div class="md:col-span-2">
                    <p class="text-sm text-gray-600">Admin Notes</p>
                    <p class="font-medium"><?= $refund_request['admin_notes'] ?></p>
                </div>
                <?php endif; ?>
            </div>
            <div class="mt-4">
                <a href="<?= base_url('admin/refunds/view/' . $refund_request['id']) ?>" 
                   class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                    <i class="fas fa-external-link-alt mr-2"></i> View Refund Details
                </a>
            </div>
        </div>
        <?php endif; ?>

        <?php else: ?>
        <div class="text-center py-12">
            <i class="fas fa-exclamation-triangle text-4xl text-yellow-400 mb-4"></i>
            <p class="text-gray-600">Purchase plan not found</p>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Status Update Modal -->
<div id="statusModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Update Plan Status</h3>
            <form id="statusForm">
				<input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>"
			   value="<?= $this->security->get_csrf_hash(); ?>">
                <input type="hidden" id="planId" name="plan_id">
                <input type="hidden" id="newStatus" name="status">
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Admin Notes (Optional)</label>
                    <textarea id="adminNotes" name="notes" rows="3" 
                              class="w-full rounded-lg border-gray-300" 
                              placeholder="Add notes for this status change..."></textarea>
                </div>
                
                <div class="flex justify-end space-x-2">
                    <button type="button" onclick="closeStatusModal()" 
                            class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        Update Status
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function updatePlanStatus(planId, status) {
    document.getElementById('planId').value = planId;
    document.getElementById('newStatus').value = status;
    document.getElementById('statusModal').classList.remove('hidden');
}

function closeStatusModal() {
    document.getElementById('statusModal').classList.add('hidden');
    document.getElementById('statusForm').reset();
}

document.getElementById('statusForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch('<?= base_url("admin/purchase-plans/update-status") ?>', {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Status updated successfully');
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while updating status');
    });
});
</script>