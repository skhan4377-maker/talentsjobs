<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="bg-white rounded-lg shadow">
    <div class="p-6 border-b">
        <div class="flex justify-between items-center">
            <h2 class="text-2xl font-bold text-gray-800">Purchase Plans</h2>
            <div class="flex space-x-2">
                <a href="<?= base_url('admin/purchase-plans/analytics') ?>"
                   class="px-4 py-2 bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200">
                    <i class="fas fa-chart-bar mr-2"></i> Analytics
                </a>
                <a href="<?= base_url('admin/purchase-plans/export') . '?' . http_build_query($_GET) ?>"
                   class="px-4 py-2 bg-green-100 text-green-700 rounded-lg hover:bg-green-200">
                    <i class="fas fa-download mr-2"></i> Export CSV
                </a>
            </div>
        </div>

        <!-- ===================== STATS ===================== -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mt-6">
            <div class="bg-blue-50 p-4 rounded-lg">
                <div class="text-sm text-blue-600">Total Purchases</div>
                <div class="text-2xl font-bold"><?= $stats['total_purchases'] ?? 0 ?></div>
            </div>
            <div class="bg-green-50 p-4 rounded-lg">
                <div class="text-sm text-green-600">Active Plans</div>
                <div class="text-2xl font-bold"><?= $stats['active_plans'] ?? 0 ?></div>
            </div>
            <div class="bg-yellow-50 p-4 rounded-lg">
                <div class="text-sm text-yellow-600">Expired Plans</div>
                <div class="text-2xl font-bold"><?= $stats['expired_plans'] ?? 0 ?></div>
            </div>
            <div class="bg-purple-50 p-4 rounded-lg">
                <div class="text-sm text-purple-600">
                    Total Revenue <span class="text-xs text-gray-500">(excl. refunds)</span>
                </div>
                <div class="text-2xl font-bold">
                    ₹<?= number_format($stats['total_revenue'] ?? 0, 2) ?>
                </div>
            </div>
        </div>

        <!-- ===================== FILTERS ===================== -->
        <div class="mt-6 bg-gray-50 p-4 rounded-lg">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Plan Status</label>
                    <select name="status" class="w-full rounded-lg border-gray-300">
                        <option value="">All</option>
                        <?php foreach (['active','expired','cancelled','refunded','upcoming'] as $st): ?>
                            <option value="<?= $st ?>" <?= ($_GET['status'] ?? '') === $st ? 'selected' : '' ?>>
                                <?= ucfirst($st) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Refund Status</label>
                    <select name="refund_status" class="w-full rounded-lg border-gray-300">
                        <option value="">All</option>
                        <?php foreach (['requested','approved','processing','processed','rejected'] as $rs): ?>
                            <option value="<?= $rs ?>" <?= ($_GET['refund_status'] ?? '') === $rs ? 'selected' : '' ?>>
                                <?= ucfirst($rs) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">From</label>
                    <input type="date" name="date_from" value="<?= $_GET['date_from'] ?? '' ?>"
                           class="w-full rounded-lg border-gray-300">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">To</label>
                    <input type="date" name="date_to" value="<?= $_GET['date_to'] ?? '' ?>"
                           class="w-full rounded-lg border-gray-300">
                </div>

                <div class="md:col-span-5">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                    <input type="text" name="search" value="<?= $_GET['search'] ?? '' ?>"
                           placeholder="Name, email, order id, payment id"
                           class="w-full rounded-lg border-gray-300">
                </div>

                <div class="md:col-span-5 flex gap-2">
                    <button class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                        <i class="fas fa-filter mr-1"></i> Filter
                    </button>
                    <a href="<?= base_url('admin/purchase-plans') ?>"
                       class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                        <i class="fas fa-times"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- ===================== TABLE ===================== -->
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Plan</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">User</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Feature</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Duration</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Purchased</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Action</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
            <?php if (!empty($plans)): foreach ($plans as $plan): ?>
                <tr>
                    <td class="px-6 py-4">
                        <div class="font-medium">#<?= $plan['id'] ?></div>
                        <div class="text-xs text-gray-500"><?= $plan['order_id'] ?? '—' ?></div>
                    </td>

                    <td class="px-6 py-4">
                        <div class="font-medium"><?= $plan['first_name'] ?? 'N/A' ?></div>
                        <div class="text-xs text-gray-500"><?= $plan['email'] ?? '' ?></div>
                    </td>

                    <td class="px-6 py-4">
                        <div class="font-medium"><?= $plan['feature_name'] ?></div>
                        <div class="text-xs text-gray-500"><?= $plan['plan_level'] ?></div>
                    </td>

                    <td class="px-6 py-4 font-medium">
                        ₹<?= $plan['plan_total'] ?>
                    </td>

                    <td class="px-6 py-4 text-sm">
                        <?= date('d M Y', strtotime($plan['start_date'])) ?>
                        →
                        <?= date('d M Y', strtotime($plan['end_date'])) ?>
                    </td>

                    <td class="px-6 py-4 space-y-1">
                        <span class="px-2 py-1 text-xs rounded bg-gray-100">
                            <?= ucfirst($plan['status']) ?>
                        </span>

                        <?php if (!empty($plan['refund_status']) && $plan['refund_status'] !== 'none'): ?>
                            <span class="block px-2 py-1 text-xs rounded bg-orange-100 text-orange-800">
                                Refund <?= ucfirst($plan['refund_status']) ?>
                            </span>
                        <?php endif; ?>
                    </td>

                    <td class="px-6 py-4 text-sm text-gray-500">
                        <?= date('d M Y, h:i A', strtotime($plan['created_at'])) ?>
                    </td>

                    <td class="px-6 py-4">
                        <a href="<?= base_url('admin/purchase-plans/view/'.$plan['id']) ?>"
                           class="text-blue-600 hover:underline">
                            View
                        </a>
                    </td>
                </tr>
            <?php endforeach; else: ?>
                <tr>
                    <td colspan="8" class="text-center py-10 text-gray-500">
                        No purchase plans found
                    </td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
