<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * STATUS MAP – single source of truth
 */
$statusMap = [
    'pending' => [
        'label' => 'Pending Admin Review',
        'class' => 'bg-yellow-100 text-yellow-800',
        'icon'  => 'clock'
    ],
    'approved' => [
        'label' => 'Approved (Queued)',
        'class' => 'bg-blue-100 text-blue-800',
        'icon'  => 'check-circle'
    ],
    'processing' => [
        'label' => 'Processing (Razorpay)',
        'class' => 'bg-purple-100 text-purple-800',
        'icon'  => 'spinner'
    ],
    'processed' => [
        'label' => 'Refunded',
        'class' => 'bg-green-100 text-green-800',
        'icon'  => 'money-bill-wave'
    ],
    'failed' => [
        'label' => 'Refund Failed',
        'class' => 'bg-red-100 text-red-800',
        'icon'  => 'times-circle'
    ],
    'rejected' => [
        'label' => 'Rejected',
        'class' => 'bg-red-100 text-red-800',
        'icon'  => 'ban'
    ]
];

$currentStatus = $statusMap[$refund['status']] ?? [
    'label' => ucfirst($refund['status']),
    'class' => 'bg-gray-100 text-gray-800',
    'icon'  => 'question-circle'
];
?>

<div class="bg-white rounded-lg shadow">

    <!-- Flash messages -->
    <?php if ($this->session->flashdata('success')): ?>
        <div class="bg-green-100 text-green-700 p-4 rounded m-4">
            <?= $this->session->flashdata('success') ?>
        </div>
    <?php endif; ?>

    <?php if ($this->session->flashdata('error')): ?>
        <div class="bg-red-100 text-red-700 p-4 rounded m-4">
            <?= $this->session->flashdata('error') ?>
        </div>
    <?php endif; ?>

    <!-- Header -->
    <div class="p-6 border-b flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold">Refund Request Details</h1>
            <p class="text-sm text-gray-500">Refund ID: #<?= $refund['id'] ?></p>
        </div>
        <a href="<?= base_url('admin/refunds') ?>" class="px-4 py-2 bg-gray-100 rounded hover:bg-gray-200">
            ← Back
        </a>
    </div>

    <div class="p-6 grid grid-cols-1 lg:grid-cols-2 gap-6">

        <!-- LEFT COLUMN -->
        <div class="space-y-6">

            <!-- Refund Info -->
            <div class="border rounded-lg">
                <div class="bg-gray-50 px-4 py-3 font-semibold">Refund Information</div>
                <div class="p-4 grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <div class="text-gray-500">Status</div>
                        <span class="inline-flex items-center px-2 py-1 rounded text-xs <?= $currentStatus['class'] ?>">
                            <i class="fas fa-<?= $currentStatus['icon'] ?> mr-1"></i>
                            <?= $currentStatus['label'] ?>
                        </span>
                    </div>

                    <div>
                        <div class="text-gray-500">Amount</div>
                        <div class="font-semibold">₹<?= number_format($refund['amount'], 2) ?></div>
                    </div>

                    <div>
                        <div class="text-gray-500">Order ID</div>
                        <div class="font-mono text-xs"><?= $refund['order_id'] ?></div>
                    </div>

                    <div>
                        <div class="text-gray-500">Requested At</div>
                        <div><?= date('d M Y, h:i A', strtotime($refund['requested_at'])) ?></div>
                    </div>

                    <?php if ($refund['processed_at']): ?>
                        <div>
                            <div class="text-gray-500">Finalized At</div>
                            <div><?= date('d M Y, h:i A', strtotime($refund['processed_at'])) ?></div>
                        </div>
                    <?php endif; ?>
                </div>

                <?php if (!empty($refund['razorpay_refund_id'])): ?>
                    <div class="border-t px-4 py-3">
                        <div class="text-gray-500 text-xs">Razorpay Refund ID</div>
                        <div class="font-mono text-sm"><?= $refund['razorpay_refund_id'] ?></div>
                    </div>
                <?php endif; ?>
            </div>

            <!-- User Info -->
            <div class="border rounded-lg">
                <div class="bg-gray-50 px-4 py-3 font-semibold">User Information</div>
                <div class="p-4 text-sm space-y-2">
                    <div><strong>Name:</strong> <?= $user['name'] ?? 'N/A' ?></div>
                    <div><strong>Email:</strong> <?= $user['email'] ?? 'N/A' ?></div>
                    <div><strong>Mobile:</strong> <?= $user['mobile'] ?? 'N/A' ?></div>
                </div>
            </div>

            <!-- Reason -->
            <div class="border rounded-lg">
                <div class="bg-gray-50 px-4 py-3 font-semibold">Refund Reason</div>
                <div class="p-4 text-sm text-gray-700">
                    <?= nl2br(htmlspecialchars($refund['reason'])) ?>
                </div>
            </div>

        </div>

        <!-- RIGHT COLUMN -->
        <div class="space-y-6">

            <!-- Payment -->
            <div class="border rounded-lg">
                <div class="bg-gray-50 px-4 py-3 font-semibold">Payment Information</div>
                <div class="p-4 text-sm grid grid-cols-2 gap-4">
                    <div>
                        <div class="text-gray-500">Payment ID</div>
                        <div class="font-mono text-xs"><?= $payment['payment_id'] ?? 'N/A' ?></div>
                    </div>
                    <div>
                        <div class="text-gray-500">Payment Status</div>
                        <div><?= ucfirst($payment['status'] ?? 'unknown') ?></div>
                    </div>
                    <div>
                        <div class="text-gray-500">Amount</div>
                        <div>₹<?= number_format($payment['amount'] ?? 0, 2) ?></div>
                    </div>
                    <div>
                        <div class="text-gray-500">Refund Status</div>
                        <div><?= ucfirst($payment['refund_status'] ?? 'none') ?></div>
                    </div>
                </div>
            </div>

            <!-- Plan -->
            <div class="border rounded-lg">
                <div class="bg-gray-50 px-4 py-3 font-semibold">Plan Information</div>
                <div class="p-4 text-sm grid grid-cols-2 gap-4">
                    <div>
                        <div class="text-gray-500">Plan Status</div>
                        <div><?= ucfirst($plan['status'] ?? 'N/A') ?></div>
                    </div>
                    <div>
                        <div class="text-gray-500">Feature</div>
                        <div><?= $plan['feature_name'] ?? 'N/A' ?></div>
                    </div>
                </div>
            </div>

            <!-- Admin Actions -->
            <div class="border rounded-lg">
                <div class="bg-gray-50 px-4 py-3 font-semibold">Admin Actions</div>
                <div class="p-4">

                    <?php if ($refund['status'] === 'pending'): ?>
                        <form method="post" action="<?= base_url('admin/refunds/process_refund') ?>">
                            <input type="hidden" name="<?= $this->security->get_csrf_token_name() ?>"
                                   value="<?= $this->security->get_csrf_hash() ?>">
                            <input type="hidden" name="refund_id" value="<?= $refund['id'] ?>">

                            <div class="space-y-3">
                                <label>
                                    <input type="radio" name="action" value="approve" checked>
                                    Approve (refund will be processed automatically)
                                </label><br>
                                <label>
                                    <input type="radio" name="action" value="reject">
                                    Reject request
                                </label>

                                <textarea name="admin_notes" rows="3"
                                          class="w-full border rounded p-2 text-sm"
                                          placeholder="Admin notes (optional)"></textarea>

                                <button type="submit"
                                        onclick="return confirm('Approve this refund? It will be sent to Razorpay automatically.')"
                                        class="w-full bg-green-600 text-white py-2 rounded hover:bg-green-700">
                                    Submit Decision
                                </button>
                            </div>
                        </form>
                    <?php else: ?>
                        <div class="text-sm text-gray-600">
                            No further action possible.
                        </div>
                    <?php endif; ?>

                </div>
            </div>

            <!-- Timeline -->
            <div class="border rounded-lg">
                <div class="bg-gray-50 px-4 py-3 font-semibold">Refund Timeline</div>
                <div class="p-4 text-sm space-y-2">
                    <div>✔ Request submitted</div>

                    <?php if (in_array($refund['status'], ['approved','processing','processed','failed'])): ?>
                        <div>✔ Approved by admin</div>
                    <?php endif; ?>

                    <?php if (in_array($refund['status'], ['processing','processed','failed'])): ?>
                        <div>✔ Sent to Razorpay</div>
                    <?php endif; ?>

                    <?php if ($refund['status'] === 'processed'): ?>
                        <div class="text-green-700 font-semibold">✔ Money refunded</div>
                    <?php endif; ?>

                    <?php if ($refund['status'] === 'failed'): ?>
                        <div class="text-red-700 font-semibold">✖ Refund failed</div>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </div>
</div>
