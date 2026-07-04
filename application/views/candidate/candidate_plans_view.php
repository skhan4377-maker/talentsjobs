<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="container mx-auto">
    <div class="max-w-7xl mx-auto">
        <?php if (empty($plans)): ?>
            <!-- Empty State -->
            <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-xl p-12 text-center border border-gray-100 dark:border-gray-700">
                <div class="w-28 h-28 bg-gradient-to-br from-blue-100 to-blue-200 dark:from-blue-900/30 dark:to-blue-800/30 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-shopping-cart text-5xl text-blue-600 dark:text-blue-400"></i>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">No Plans Yet</h3>
                <p class="text-gray-600 dark:text-gray-400 mb-8 max-w-md mx-auto">You haven't purchased any resume builder plans. Start building your professional resume today!</p>
                <a href="<?=base_url('career-services')?>" class="inline-flex items-center px-8 py-3.5 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-semibold rounded-xl transition-all duration-300 shadow-lg shadow-blue-500/25">
                    <i class="fas fa-rocket mr-2"></i> Explore Plans
                </a>
            </div>
        <?php else: ?>
            <!-- Cards Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php foreach ($plans as $plan): ?>
                    <?php 
                        // Status colours
                        $statusStyles = [
                            'Active' => 'bg-emerald-100 text-emerald-700 border-emerald-200',
                            'Expiring Soon' => 'bg-amber-100 text-amber-700 border-amber-200',
                            'Upcoming' => 'bg-blue-100 text-blue-700 border-blue-200',
                            'Expired' => 'bg-red-100 text-red-700 border-red-200',
                            'Cancelled' => 'bg-gray-100 text-gray-700 border-gray-200',
                        ];
                        $badgeClass = $statusStyles[$plan['status_display']] ?? 'bg-gray-100 text-gray-700';
                    ?>
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-md hover:shadow-xl transition-shadow duration-300 border border-gray-100 dark:border-gray-700 overflow-hidden flex flex-col">
                        <!-- Header -->
                        <div class="p-6 border-b border-gray-100 dark:border-gray-700 bg-gradient-to-r from-gray-50 to-white dark:from-gray-900 dark:to-gray-800">
                            <div class="flex items-start justify-between">
                                <div>
                                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-1"><?= htmlspecialchars($plan['feature_name']) ?></h3>
                                    <p class="text-sm text-gray-500 dark:text-gray-400"><?= htmlspecialchars($plan['duration'] ?? 'Custom Duration') ?></p>
                                </div>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold border <?= $badgeClass ?>">
                                    <?= $plan['status_display'] ?>
                                </span>
                            </div>
                        </div>

                        <!-- Card Body -->
                        <div class="p-6 flex-1">
                            <!-- Dates & Amount -->
                            <div class="grid grid-cols-2 gap-4 mb-5">
                                <div>
                                    <span class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider">Start Date</span>
                                    <p class="text-sm font-medium text-gray-900 dark:text-white"><?= $plan['start_date_formatted'] ?></p>
                                </div>
                                <div>
                                    <span class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider">End Date</span>
                                    <p class="text-sm font-medium text-gray-900 dark:text-white"><?= $plan['end_date_formatted'] ?></p>
                                </div>
                                <div>
                                    <span class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider">Amount</span>
                                    <p class="text-sm font-bold text-gray-900 dark:text-white">₹<?= number_format($plan['paid_amount'] ?? $plan['plan_price'], 2) ?></p>
                                </div>
                                <div>
                                    <span class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider">Invoice</span>
                                    <p class="text-sm">
                                        <?php if (!empty($plan['invoice_no'])): ?>
                                            <a href="<?= $plan['invoice_link'] ?>" class="text-blue-600 hover:underline"><?= $plan['invoice_no'] ?></a>
                                        <?php else: ?>
                                            <span class="text-gray-400">N/A</span>
                                        <?php endif; ?>
                                    </p>
                                </div>
                            </div>

                            <!-- Countdown Timer & Urgent Actions (only for Expiring Soon) -->
                            <?php if ($plan['status_display'] == 'Expiring Soon' && !empty($plan['expiry_timestamp'])): ?>
                                <div class="mt-2 p-3 bg-amber-50 border border-amber-200 rounded-xl">
                                    <p class="text-xs font-semibold text-amber-800 uppercase tracking-wider mb-2">
                                        <i class="fas fa-hourglass-half mr-1"></i> Expires In
                                    </p>
                                    <div class="flex items-center justify-center space-x-2 text-center"
                                         id="timer-<?= $plan['order_id'] ?>"
                                         data-expiry="<?= $plan['expiry_timestamp'] ?>">
                                        <div class="bg-white rounded-lg px-2 py-1 shadow-sm">
                                            <span class="block text-lg font-bold text-amber-700" id="days-<?= $plan['order_id'] ?>">00</span>
                                            <span class="text-xs text-gray-500">Days</span>
                                        </div>
                                        <span class="text-amber-700 text-xl">:</span>
                                        <div class="bg-white rounded-lg px-2 py-1 shadow-sm">
                                            <span class="block text-lg font-bold text-amber-700" id="hours-<?= $plan['order_id'] ?>">00</span>
                                            <span class="text-xs text-gray-500">Hrs</span>
                                        </div>
                                        <span class="text-amber-700 text-xl">:</span>
                                        <div class="bg-white rounded-lg px-2 py-1 shadow-sm">
                                            <span class="block text-lg font-bold text-amber-700" id="mins-<?= $plan['order_id'] ?>">00</span>
                                            <span class="text-xs text-gray-500">Min</span>
                                        </div>
                                        <span class="text-amber-700 text-xl">:</span>
                                        <div class="bg-white rounded-lg px-2 py-1 shadow-sm">
                                            <span class="block text-lg font-bold text-amber-700" id="secs-<?= $plan['order_id'] ?>">00</span>
                                            <span class="text-xs text-gray-500">Sec</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex flex-wrap gap-2 mt-3">
                                    <a href="<?= base_url('career-services') ?>"
                                       class="flex-1 inline-flex items-center justify-center px-4 py-2 bg-gradient-to-r from-orange-500 to-red-500 text-white text-sm font-medium rounded-lg hover:from-orange-600 hover:to-red-600 transition-all shadow-sm">
                                        <i class="fas fa-sync-alt mr-1.5"></i> Renew Now
                                    </a>
                                    <a href="<?= base_url('resume-templates') ?>"
                                       class="flex-1 inline-flex items-center justify-center px-4 py-2 bg-gradient-to-r from-blue-600 to-blue-700 text-white text-sm font-medium rounded-lg hover:from-blue-700 hover:to-blue-800 transition-all shadow-sm">
                                        <i class="fas fa-file-alt mr-1.5"></i> Continue
                                    </a>
                                </div>
                            <?php endif; ?>

                            <!-- Included Benefits (Tags) -->
                            <?php if (!empty($plan['tags'])): ?>
                                <div class="mb-5 mt-4">
                                    <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">What's Included</h4>
                                    <div class="flex flex-wrap gap-2">
                                        <?php foreach ($plan['tags'] as $tag): ?>
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-indigo-50 text-indigo-700 border border-indigo-100 dark:bg-indigo-900/20 dark:text-indigo-300 dark:border-indigo-800">
                                                <i class="fas fa-check-circle mr-1.5 text-indigo-500"></i> <?= htmlspecialchars($tag['tag_title']) ?>
                                            </span>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <!-- Payment Status -->
                            <div class="flex items-center space-x-2">
                                <span class="text-xs text-gray-500 dark:text-gray-400">Payment:</span>
                                <span class="capitalize text-sm font-medium <?= $plan['payment_status'] == 'success' || $plan['payment_status'] == 'paid' ? 'text-green-600' : 'text-red-600' ?>">
                                    <?= $plan['payment_status'] ?? 'N/A' ?>
                                </span>
                            </div>
                        </div>

                        <!-- Card Footer / Actions -->
                        <div class="px-6 py-4 bg-gray-50 dark:bg-gray-900/50 border-t border-gray-100 dark:border-gray-700">
                            <?php if ($plan['status_display'] == 'Active' || $plan['status_display'] == 'Expiring Soon'): ?>
                                <div class="flex flex-wrap gap-2">
                                    <a href="<?= base_url('resume-templates') ?>" class="flex-1 inline-flex items-center justify-center px-4 py-2 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white text-sm font-medium rounded-lg transition-all shadow-sm">
                                        <i class="fas fa-file-alt mr-1.5"></i> Continue
                                    </a>
                                    <?php if ($plan['refund_status'] == 'none'): ?>
                                        <?php
                                            $refund_window = strtotime($plan['payment_date']) + (24 * 60 * 60);
                                            if (time() < $refund_window):
                                        ?>
                                            <button onclick="requestRefund('<?= $plan['order_id'] ?>')" class="flex-1 inline-flex items-center justify-center px-4 py-2 bg-red-500 hover:bg-red-600 text-white text-sm font-medium rounded-lg transition-all shadow-sm">
                                                <i class="fas fa-undo-alt mr-1.5"></i> Refund
                                            </button>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <button onclick="showRefundStatus(<?= htmlspecialchars(json_encode([
                                            'order_id' => $plan['order_id'],
                                            'refund_status' => $plan['refund_status'],
                                            'refund_req_status' => $plan['refund_req_status'] ?? 'pending',
                                            'refund_req_at' => $plan['refund_req_at'] ?? '',
                                            'refund_req_processed_at' => $plan['refund_req_processed_at'] ?? '',
                                            'gateway_status' => $plan['gateway_status'] ?? 'pending',
                                            'admin_notes' => $plan['admin_notes'] ?? ''
                                        ]), ENT_QUOTES) ?>)" class="flex-1 inline-flex items-center justify-center px-4 py-2 bg-amber-100 text-amber-800 text-sm font-medium rounded-lg hover:bg-amber-200 transition-all">
                                            <i class="fas fa-info-circle mr-1.5"></i> Refund Status
                                        </button>
                                    <?php endif; ?>
                                </div>
                            <?php elseif ($plan['status_display'] == 'Upcoming'): ?>
                                <?php if ($plan['refund_status'] == 'none'): ?>
                                    <button onclick="cancelUpcoming('<?= $plan['order_id'] ?>')" class="w-full inline-flex items-center justify-center px-4 py-2 bg-red-500 hover:bg-red-600 text-white text-sm font-medium rounded-lg transition-all shadow-sm">
                                        <i class="fas fa-ban mr-1.5"></i> Cancel Upcoming
                                    </button>
                                <?php else: ?>
                                    <button onclick="showRefundStatus(<?= htmlspecialchars(json_encode([
                                        'order_id' => $plan['order_id'],
                                        'refund_status' => $plan['refund_status'],
                                        'refund_req_status' => $plan['refund_req_status'] ?? 'pending',
                                        'refund_req_at' => $plan['refund_req_at'] ?? '',
                                        'refund_req_processed_at' => $plan['refund_req_processed_at'] ?? '',
                                        'gateway_status' => $plan['gateway_status'] ?? 'pending',
                                        'admin_notes' => $plan['admin_notes'] ?? ''
                                    ]), ENT_QUOTES) ?>)" class="w-full inline-flex items-center justify-center px-4 py-2 bg-amber-100 text-amber-800 text-sm font-medium rounded-lg hover:bg-amber-200 transition-all">
                                        <i class="fas fa-info-circle mr-1.5"></i> Refund Status
                                    </button>
                                <?php endif; ?>
                            <?php else: ?>
                                <?php if ($plan['refund_status'] != 'none'): ?>
                                    <button onclick="showRefundStatus(<?= htmlspecialchars(json_encode([
                                        'order_id' => $plan['order_id'],
                                        'refund_status' => $plan['refund_status'],
                                        'refund_req_status' => $plan['refund_req_status'] ?? 'pending',
                                        'refund_req_at' => $plan['refund_req_at'] ?? '',
                                        'refund_req_processed_at' => $plan['refund_req_processed_at'] ?? '',
                                        'gateway_status' => $plan['gateway_status'] ?? 'pending',
                                        'admin_notes' => $plan['admin_notes'] ?? ''
                                    ]), ENT_QUOTES) ?>)" class="w-full inline-flex items-center justify-center px-4 py-2 bg-amber-100 text-amber-800 text-sm font-medium rounded-lg hover:bg-amber-200 transition-all">
                                        <i class="fas fa-info-circle mr-1.5"></i> Refund Status
                                    </button>
                                <?php else: ?>
                                    <a href="<?= base_url('my-purchases') ?>" class="w-full inline-flex items-center justify-center px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition-all">
                                        View Details
                                    </a>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Cancel & Refund Modal -->
<div id="refundModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:999;">
  <div style="background:white; margin:10% auto; padding:20px; width:90%; max-width:400px; border-radius:8px;">
    <h3 class="text-lg font-semibold mb-3">Refund Request</h3>
    <textarea id="refundReason" rows="3" class="w-full border rounded p-2" placeholder="Why are you cancelling? (optional)"></textarea>
    <div class="flex justify-end space-x-2 mt-3">
      <button onclick="closeRefundModal()" class="px-4 py-2 bg-gray-200 rounded">Cancel</button>
      <button onclick="submitRefund()" class="px-4 py-2 bg-red-500 text-white rounded">Submit</button>
    </div>
  </div>
</div>

<!-- Refund Status Modal -->
<div id="refundStatusModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:999;">
  <div style="background:white; margin:10% auto; padding:20px; width:90%; max-width:500px; border-radius:8px;">
    <h3 class="text-lg font-semibold mb-3">Refund Request Status</h3>
    <div id="refundStatusContent" class="text-sm space-y-2"></div>
    <div class="flex justify-end mt-4">
      <button onclick="document.getElementById('refundStatusModal').style.display='none'" class="px-4 py-2 bg-gray-200 rounded">Close</button>
    </div>
  </div>
</div>

<script>
// ----- Countdown Timer Logic -----
function startCountdown(timerElement) {
    const expiryMs = parseInt(timerElement.getAttribute('data-expiry'));
    if (isNaN(expiryMs)) return;

    function updateTimer() {
        const now = Date.now();
        const diff = expiryMs - now;

        if (diff <= 0) {
            timerElement.innerHTML = '<span class="text-red-500 font-semibold text-sm">Plan Expired</span>';
            // Optional: reload page after expiry
            // location.reload();
            return;
        }

        const days = Math.floor(diff / (1000 * 60 * 60 * 24));
        const hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((diff % (1000 * 60)) / 1000);

        const pad = (n) => String(n).padStart(2, '0');
        const orderId = timerElement.id.split('-')[1];

        const daysEl = document.getElementById('days-' + orderId);
        const hoursEl = document.getElementById('hours-' + orderId);
        const minsEl = document.getElementById('mins-' + orderId);
        const secsEl = document.getElementById('secs-' + orderId);

        if (daysEl) daysEl.textContent = pad(days);
        if (hoursEl) hoursEl.textContent = pad(hours);
        if (minsEl) minsEl.textContent = pad(minutes);
        if (secsEl) secsEl.textContent = pad(seconds);
    }

    updateTimer();
    setInterval(updateTimer, 1000);
}

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-expiry]').forEach(el => startCountdown(el));
});

// ----- Refund & Cancel Functions (unchanged) -----
function requestRefund(orderId) {
    if (!orderId || orderId === 'null' || orderId === '') {
        alert('Unable to process: Order ID is missing.');
        return;
    }
    const modal = document.getElementById('refundModal');
    modal.setAttribute('data-order-id', orderId);
    modal.style.display = 'block';
}

function closeRefundModal() {
    const modal = document.getElementById('refundModal');
    modal.style.display = 'none';
    modal.removeAttribute('data-order-id');
    document.getElementById('refundReason').value = '';
}

function submitRefund() {
    const modal = document.getElementById('refundModal');
    const orderId = modal.getAttribute('data-order-id');
    const reason = document.getElementById('refundReason').value.trim() || 'User requested cancellation';

    if (!orderId) {
        alert('Error: Order ID not found. Please try again.');
        closeRefundModal();
        return;
    }

    const csrfName = getCSRFName();
    const csrfToken = getCSRFToken();
    const params = new URLSearchParams();
    params.append('order_id', orderId);
    params.append('reason', reason);
    params.append(csrfName, csrfToken);

    fetch('<?= base_url("candidate/request_refund") ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: params.toString()
    })
    .then(response => response.json())
    .then(data => {
        alert(data.message);
        if (data.status) location.reload();
    })
    .catch(err => alert('Error: ' + err));

    closeRefundModal();
}

function cancelUpcoming(orderId) {
    if (!orderId || orderId === 'null' || orderId === '') {
        alert('Order ID missing. Please refresh the page.');
        return;
    }
    if (!confirm('Cancel this upcoming plan? A refund request will be created.')) return;

    const csrfName = getCSRFName();
    const csrfToken = getCSRFToken();
    const params = new URLSearchParams();
    params.append('order_id', orderId);
    params.append(csrfName, csrfToken);

    fetch('<?= base_url("candidate/cancel_upcoming") ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: params.toString()
    })
    .then(response => response.json())
    .then(data => {
        alert(data.message);
        if (data.status) location.reload();
    })
    .catch(err => alert('Error: ' + err));
}

function showRefundStatus(data) {
    const modal = document.getElementById('refundStatusModal');
    const content = document.getElementById('refundStatusContent');
    
    let statusText = '';
    if (data.refund_status === 'requested' || data.refund_req_status === 'pending') {
        statusText = '<span class="text-yellow-600 font-semibold">⏳ Refund Requested</span>';
    } else if (data.refund_status === 'approved' || data.refund_req_status === 'approved') {
        statusText = '<span class="text-green-600 font-semibold">✔️ Approved by Admin</span>';
    } else if (data.refund_status === 'processed' || data.gateway_status === 'success') {
        statusText = '<span class="text-green-700 font-semibold">💰 Refund Processed</span>';
    } else if (data.refund_status === 'rejected' || data.refund_req_status === 'rejected') {
        statusText = '<span class="text-red-600 font-semibold">❌ Refund Rejected</span>';
    } else {
        statusText = 'Status: ' + (data.refund_status ?? 'Unknown');
    }
    
    let html = `
        <p><strong>Order ID:</strong> ${data.order_id}</p>
        <p><strong>Current Status:</strong> ${statusText}</p>
        <p><strong>Requested On:</strong> ${data.refund_req_at ? data.refund_req_at : 'N/A'}</p>
        <p><strong>Processed On:</strong> ${data.refund_req_processed_at ?? 'N/A'}</p>
        <p><strong>Gateway Status:</strong> ${data.gateway_status ?? 'N/A'}</p>
        <div class="bg-yellow-50 border border-yellow-300 text-yellow-800 text-sm p-3 rounded-md mt-4">
          <i class="fas fa-info-circle mr-1"></i> 
          <strong>Note:</strong> Your plan has been <strong>cancelled</strong> as per the refund request. Once the refund is processed, the amount will be credited to your original payment method.
        </div>
    `;
    content.innerHTML = html;
    modal.style.display = 'block';
}
</script>