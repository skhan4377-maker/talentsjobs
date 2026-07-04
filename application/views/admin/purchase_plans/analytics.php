<div class="bg-white rounded-lg shadow">
    <div class="p-6 border-b">
        <div class="flex justify-between items-center">
            <h2 class="text-2xl font-bold text-gray-800">Purchase Analytics</h2>
            <a href="<?= base_url('admin/purchase-plans') ?>" 
               class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                <i class="fas fa-arrow-left mr-2"></i> Back to Plans
            </a>
        </div>
    </div>

    <div class="p-6">
        <!-- Revenue Statistics -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-blue-50 p-6 rounded-lg border border-blue-100">
                <div class="flex items-center">
                    <div class="p-3 bg-blue-100 rounded-lg mr-4">
                        <i class="fas fa-wallet text-blue-600 text-2xl"></i>
                    </div>
                    <div>
                        <p class="text-sm text-blue-600">Total Revenue</p>
                        <p class="text-2xl font-bold text-gray-800">
                            ₹<?= number_format($revenue_stats['total_revenue'] ?? 0, 2) ?>
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="bg-green-50 p-6 rounded-lg border border-green-100">
                <div class="flex items-center">
                    <div class="p-3 bg-green-100 rounded-lg mr-4">
                        <i class="fas fa-shopping-bag text-green-600 text-2xl"></i>
                    </div>
                    <div>
                        <p class="text-sm text-green-600">Total Purchases</p>
                        <p class="text-2xl font-bold text-gray-800">
                            <?= $revenue_stats['total_purchases'] ?? 0 ?>
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="bg-purple-50 p-6 rounded-lg border border-purple-100">
                <div class="flex items-center">
                    <div class="p-3 bg-purple-100 rounded-lg mr-4">
                        <i class="fas fa-chart-line text-purple-600 text-2xl"></i>
                    </div>
                    <div>
                        <p class="text-sm text-purple-600">Avg. Purchase Value</p>
                        <p class="text-2xl font-bold text-gray-800">
                            ₹<?= number_format(
                                ($revenue_stats['total_revenue'] ?? 0) / 
                                (($revenue_stats['total_purchases'] ?? 0) ?: 1), 2
                            ) ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Revenue Chart Section -->
        <div class="mb-8">
            <div class="bg-white p-6 rounded-lg border border-gray-200">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-lg font-medium text-gray-800">Revenue Trends (Last 6 Months)</h3>
                    <div class="text-sm text-gray-500">
                        <i class="fas fa-calendar-alt mr-2"></i>
                        <?= date('M Y', strtotime('-5 months')) ?> - <?= date('M Y') ?>
                    </div>
                </div>
                
                <div class="h-64">
                    <?php if (!empty($revenue_stats['months']) && !empty($revenue_stats['revenue'])): ?>
                    <canvas id="revenueChart"></canvas>
                    <?php else: ?>
                    <div class="flex items-center justify-center h-full">
                        <p class="text-gray-500">No revenue data available</p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Feature Popularity -->
        <div class="mb-8">
            <div class="bg-white p-6 rounded-lg border border-gray-200">
                <h3 class="text-lg font-medium text-gray-800 mb-6">Feature Popularity</h3>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Feature
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Total Purchases
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Total Revenue
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Avg. Revenue
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Popularity
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php if (!empty($feature_stats)): ?>
                            <?php 
                            // Calculate total purchases for percentage
                            $total_purchases = array_sum(array_column($feature_stats, 'purchase_count'));
                            ?>
                            <?php foreach ($feature_stats as $feature): ?>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">
                                        <?= $feature['feature_name'] ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900"><?= $feature['purchase_count'] ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">₹<?= number_format($feature['total_revenue'] ?? 0, 2) ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        ₹<?= number_format(
                                            ($feature['total_revenue'] ?? 0) / 
                                            (($feature['purchase_count'] ?? 0) ?: 1), 2
                                        ) ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="w-full bg-gray-200 rounded-full h-2.5">
                                        <?php
                                        $percentage = $total_purchases > 0 ? 
                                            ($feature['purchase_count'] / $total_purchases) * 100 : 0;
                                        ?>
                                        <div class="bg-blue-600 h-2.5 rounded-full" 
                                             style="width: <?= $percentage ?>%"></div>
                                    </div>
                                    <div class="text-xs text-gray-500 mt-1"><?= round($percentage, 1) ?>%</div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php else: ?>
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center">
                                    <i class="fas fa-chart-pie text-4xl text-gray-300 mb-4"></i>
                                    <p class="text-gray-500">No feature data available</p>
                                </td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Monthly Trends -->
        <div class="mb-8">
            <div class="bg-white p-6 rounded-lg border border-gray-200">
                <h3 class="text-lg font-medium text-gray-800 mb-6">Monthly Performance</h3>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Month
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Total Plans
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Revenue
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Avg. per Plan
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Growth
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php if (!empty($monthly_trends)): ?>
                            <?php 
                            $prev_revenue = null;
                            foreach ($monthly_trends as $index => $month): 
                            ?>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">
                                        <?= date('M Y', strtotime($month['month'] . '-01')) ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900"><?= $month['total_plans'] ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">
                                        ₹<?= number_format($month['total_revenue'] ?? 0, 2) ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        ₹<?= number_format(
                                            ($month['total_revenue'] ?? 0) / 
                                            (($month['total_plans'] ?? 0) ?: 1), 2
                                        ) ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php if ($prev_revenue !== null && $prev_revenue > 0): 
                                        $growth = (($month['total_revenue'] - $prev_revenue) / $prev_revenue) * 100;
                                    ?>
                                        <span class="text-sm <?= $growth >= 0 ? 'text-green-600' : 'text-red-600' ?>">
                                            <i class="fas <?= $growth >= 0 ? 'fa-arrow-up' : 'fa-arrow-down' ?> mr-1"></i>
                                            <?= round(abs($growth), 1) ?>%
                                        </span>
                                    <?php else: ?>
                                        <span class="text-sm text-gray-500">-</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php 
                                $prev_revenue = $month['total_revenue'];
                            endforeach; 
                            ?>
                            <?php else: ?>
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center">
                                    <i class="fas fa-chart-line text-4xl text-gray-300 mb-4"></i>
                                    <p class="text-gray-500">No monthly data available</p>
                                </td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- User Type Statistics -->
        <div class="mb-8">
            <div class="bg-white p-6 rounded-lg border border-gray-200">
                <h3 class="text-lg font-medium text-gray-800 mb-6">User Type Distribution</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="h-64">
                        <canvas id="userTypeChart"></canvas>
                    </div>
                    <div>
                        <div class="space-y-4">
                            <?php if (!empty($user_type_stats)): ?>
                            <?php foreach ($user_type_stats as $user_type): ?>
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <div class="flex items-center">
                                    <div class="w-3 h-3 rounded-full 
                                        <?= $user_type['role'] == 'candidate' ? 'bg-blue-500' : 'bg-green-500' ?> 
                                        mr-3"></div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">
                                            <?= ucfirst($user_type['role']) ?>
                                        </p>
                                        <p class="text-xs text-gray-500">
                                            <?= $user_type['total_plans'] ?> plans
                                        </p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-medium text-gray-900">
                                        ₹<?= number_format($user_type['total_revenue'] ?? 0, 2) ?>
                                    </p>
                                    <p class="text-xs text-gray-500">
                                        <?= 
                                            $user_type['total_plans'] > 0 ? 
                                            '₹' . number_format($user_type['total_revenue'] / $user_type['total_plans'], 2) . ' avg.' : 
                                            'N/A'
                                        ?>
                                    </p>
                                </div>
                            </div>
                            <?php endforeach; ?>
                            <?php else: ?>
                            <div class="text-center py-8">
                                <i class="fas fa-users text-4xl text-gray-300 mb-4"></i>
                                <p class="text-gray-500">No user type data available</p>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Export Options -->
        <div class="bg-gray-50 p-6 rounded-lg border border-gray-200">
            <h3 class="text-lg font-medium text-gray-800 mb-4">Export Analytics</h3>
            <div class="flex space-x-4">
                <a href="<?= base_url('admin/purchase-plans/export') ?>" 
                   class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    <i class="fas fa-file-excel mr-2"></i> Export as CSV
                </a>
                <button onclick="printAnalytics()" 
                        class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">
                    <i class="fas fa-print mr-2"></i> Print Report
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Include Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
// Revenue Chart
<?php if (!empty($revenue_stats['months']) && !empty($revenue_stats['revenue'])): ?>
const revenueCtx = document.getElementById('revenueChart').getContext('2d');
const revenueChart = new Chart(revenueCtx, {
    type: 'line',
    data: {
        labels: <?= json_encode($revenue_stats['months']) ?>,
        datasets: [{
            label: 'Revenue (₹)',
            data: <?= json_encode($revenue_stats['revenue']) ?>,
            borderColor: '#3b82f6',
            backgroundColor: 'rgba(59, 130, 246, 0.1)',
            borderWidth: 2,
            fill: true,
            tension: 0.4
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        return 'Revenue: ₹' + context.parsed.y.toLocaleString();
                    }
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return '₹' + value.toLocaleString();
                    }
                }
            }
        }
    }
});
<?php endif; ?>

// User Type Chart
<?php if (!empty($user_type_stats)): ?>
const userTypeCtx = document.getElementById('userTypeChart').getContext('2d');
const userTypeChart = new Chart(userTypeCtx, {
    type: 'doughnut',
    data: {
        labels: <?= json_encode(array_column($user_type_stats, 'role')) ?>,
        datasets: [{
            data: <?= json_encode(array_column($user_type_stats, 'total_plans')) ?>,
            backgroundColor: [
                '#3b82f6', // Blue for candidate
                '#10b981', // Green for employer
            ],
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'right',
                labels: {
                    padding: 20
                }
            }
        }
    }
});
<?php endif; ?>

function printAnalytics() {
    window.print();
}

// Add print styles
const style = document.createElement('style');
style.innerHTML = `
    @media print {
        .bg-white { background: white !important; }
        .shadow, .border { border: 1px solid #ddd !important; box-shadow: none !important; }
        .rounded-lg { border-radius: 0 !important; }
        .h-64 { height: auto !important; }
        canvas { max-height: 300px; }
        .flex { display: block !important; }
        .grid { display: block !important; }
        .hidden { display: none !important; }
        a { text-decoration: none !important; color: black !important; }
        button { display: none !important; }
        .p-6 { padding: 15px !important; }
        .mb-8 { margin-bottom: 20px !important; }
    }
`;
document.head.appendChild(style);
</script>