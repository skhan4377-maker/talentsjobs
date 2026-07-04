<div class="bg-white rounded-lg shadow">
    <div class="p-4 md:p-6 border-b">

        <!-- ================= STATS ================= -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3 md:gap-4 mb-6">
            <div class="bg-blue-50 p-3 md:p-4 rounded-lg border border-blue-100">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-xs md:text-sm text-blue-600 font-medium">Total</div>
                        <div class="text-lg md:text-2xl font-bold text-gray-800">
                            <?= $stats['total_requests'] ?? 0 ?>
                        </div>
                    </div>
                    <div class="w-8 h-8 md:w-10 md:h-10 bg-blue-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-exchange-alt text-blue-600"></i>
                    </div>
                </div>
            </div>

            <div class="bg-yellow-50 p-3 md:p-4 rounded-lg border border-yellow-100">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-xs md:text-sm text-yellow-600 font-medium">Pending</div>
                        <div class="text-lg md:text-2xl font-bold text-gray-800">
                            <?= $stats['pending'] ?? 0 ?>
                        </div>
                    </div>
                    <div class="w-8 h-8 md:w-10 md:h-10 bg-yellow-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-clock text-yellow-600"></i>
                    </div>
                </div>
            </div>

            <div class="bg-green-50 p-3 md:p-4 rounded-lg border border-green-100">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-xs md:text-sm text-green-600 font-medium">Approved</div>
                        <div class="text-lg md:text-2xl font-bold text-gray-800">
                            <?= $stats['approved'] ?? 0 ?>
                        </div>
                    </div>
                    <div class="w-8 h-8 md:w-10 md:h-10 bg-green-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-check text-green-600"></i>
                    </div>
                </div>
            </div>

            <div class="bg-red-50 p-3 md:p-4 rounded-lg border border-red-100">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-xs md:text-sm text-red-600 font-medium">Rejected</div>
                        <div class="text-lg md:text-2xl font-bold text-gray-800">
                            <?= $stats['rejected'] ?? 0 ?>
                        </div>
                    </div>
                    <div class="w-8 h-8 md:w-10 md:h-10 bg-red-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-times text-red-600"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- ================= FILTERS ================= -->
        <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
            <form method="GET">
                <div class="grid grid-cols-1 md:grid-cols-5 gap-3">
                    <div>
                        <label class="text-sm font-medium">Status</label>
                        <select name="status" class="w-full rounded-lg border-gray-300">
                            <option value="">All</option>
                            <?php
                            $statuses = [
                                'pending' => 'Pending',
                                'approved' => 'Approved (Admin)',
                                'processing' => 'Processing',
                                'processed' => 'Refunded',
                                'failed' => 'Failed',
                                'rejected' => 'Rejected'
                            ];
                            foreach ($statuses as $k => $v):
                            ?>
                                <option value="<?= $k ?>" <?= ($_GET['status'] ?? '') === $k ? 'selected' : '' ?>>
                                    <?= $v ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div>
                        <label class="text-sm font-medium">From</label>
                        <input type="date" name="date_from" value="<?= $_GET['date_from'] ?? '' ?>"
                               class="w-full rounded-lg border-gray-300">
                    </div>

                    <div>
                        <label class="text-sm font-medium">To</label>
                        <input type="date" name="date_to" value="<?= $_GET['date_to'] ?? '' ?>"
                               class="w-full rounded-lg border-gray-300">
                    </div>

                    <div>
                        <label class="text-sm font-medium">Search</label>
                        <input type="text" name="search" value="<?= $_GET['search'] ?? '' ?>"
                               placeholder="Name / Email / Order"
                               class="w-full rounded-lg border-gray-300">
                    </div>

                    <div class="flex items-end gap-2">
                        <button class="flex-1 bg-blue-600 text-white px-3 py-2 rounded-lg">
                            <i class="fas fa-filter mr-1"></i> Filter
                        </button>
                        <a href="<?= base_url('admin/refunds') ?>"
                           class="px-3 py-2 bg-gray-200 rounded-lg">
                            <i class="fas fa-times"></i>
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- ================= TABLE ================= -->
    <div class="p-4 md:p-6">
        <form method="POST" action="<?= base_url('admin/refunds/bulk_action') ?>" id="bulkForm">
            <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>"
                   value="<?= $this->security->get_csrf_hash(); ?>">

            <div class="flex justify-between mb-4">
                <div>
                    <input type="checkbox" id="selectAll">
                    <label for="selectAll" class="text-sm ml-1">Select All (Pending only)</label>
                </div>

                <div class="flex gap-2">
                    <select name="bulk_action" class="rounded-lg border-gray-300 text-sm">
                        <option value="">Bulk Action</option>
                        <option value="reject_selected">Reject Selected</option>
                    </select>
                    <button class="bg-blue-600 text-white px-3 py-2 rounded-lg text-sm">
                        Apply
                    </button>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-3 py-3"></th>
                            <th class="px-3 py-3">ID</th>
                            <th class="px-3 py-3">User</th>
                            <th class="px-3 py-3">Order</th>
                            <th class="px-3 py-3">Amount</th>
                            <th class="px-3 py-3">Status</th>
                            <th class="px-3 py-3">Requested</th>
                            <th class="px-3 py-3">Action</th>
                        </tr>
                    </thead>

                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php if ($refunds): foreach ($refunds as $r): ?>

                        <?php
                        $badge = [
                            'pending' => 'bg-yellow-100 text-yellow-800',
                            'approved' => 'bg-green-100 text-green-800',
                            'processing' => 'bg-blue-100 text-blue-800',
                            'processed' => 'bg-purple-100 text-purple-800',
                            'failed' => 'bg-red-100 text-red-800',
                            'rejected' => 'bg-red-100 text-red-800'
                        ][$r['status']] ?? 'bg-gray-100';
                        ?>

                        <tr>
                            <td class="px-3 py-3">
                                <?php if ($r['status'] === 'pending'): ?>
                                    <input type="checkbox" name="refund_ids[]" value="<?= $r['id'] ?>">
                                <?php endif; ?>
                            </td>

                            <td class="px-3 py-3">#<?= $r['id'] ?></td>

                            <td class="px-3 py-3">
                                <div class="text-sm font-medium"><?= $r['name'] ?></div>
                                <div class="text-xs text-gray-500"><?= $r['email'] ?></div>
                            </td>

                            <td class="px-3 py-3 text-sm"><?= $r['order_id'] ?></td>

                            <td class="px-3 py-3 font-bold">₹<?= $r['amount'] ?></td>

                            <td class="px-3 py-3">
                                <span class="px-2 py-1 rounded-full text-xs <?= $badge ?>">
                                    <?= ucfirst($r['status']) ?>
                                </span>
                            </td>

                            <td class="px-3 py-3 text-sm">
                                <?= date('d M Y, h:i A', strtotime($r['requested_at'])) ?>
                            </td>

                            <td class="px-3 py-3">
                                <a href="<?= base_url('admin/refunds/view/'.$r['id']) ?>"
                                   class="text-blue-600 text-sm">
                                    <i class="fas fa-eye mr-1"></i> View
                                </a>
                            </td>
                        </tr>

                        <?php endforeach; else: ?>
                        <tr>
                            <td colspan="8" class="text-center py-12 text-gray-500">
                                No refund requests found
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </form>
    </div>
</div>

<script>
document.getElementById('selectAll')?.addEventListener('change', function () {
    document.querySelectorAll('input[name="refund_ids[]"]').forEach(cb => {
        cb.checked = this.checked;
    });
});
</script>
