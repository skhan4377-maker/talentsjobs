<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Payment Invoice</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-5">
    <div class="max-w-2xl w-full mx-auto bg-white rounded-xl shadow-lg overflow-hidden">
        <!-- Invoice Header -->
        <?php if ($this->session->userdata('payment_success')): ?>
        <div class="flex justify-between items-center p-6 border-b-2 border-gray-200">
            <img src="<?=base_url('assets/frontend/header/logo.png');?>" alt="Company Logo" class="h-12">
            <h1 class="text-3xl font-bold text-gray-800">Payment Invoice</h1>
        </div>
        <?php endif; ?>

        <!-- Status Section -->
        <div class="<?= $this->session->userdata('payment_success') ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-700' ?> p-4 text-center rounded-lg mx-6 mt-6">
            <h2 class="text-xl font-semibold mb-2">
                <?= $this->session->userdata('payment_success') ? 'Payment Successful' : 'Payment Failed' ?>
            </h2>
            <p class="text-sm">
                <?= $this->session->userdata('payment_success') ? 'Thank you for your purchase! Your selected plans are now active.' : 'We could not verify your payment. Please try again or contact support.' ?>
            </p>
        </div>

        <?php if ($this->session->userdata('payment_success')): ?>
        <!-- Invoice Details -->
        <div class="flex flex-col md:flex-row justify-between p-6 space-y-4 md:space-y-0">
            <div class="space-y-2">
                <p class="text-sm text-gray-600">
                    <span class="font-semibold">Invoice Date:</span> <?= $invoiceDate ?>
                </p>
                <p class="text-sm text-gray-600">
                    <span class="font-semibold">Invoice Number:</span> #<?= $invoiceNumber ?>
                </p>
            </div>
        </div>

        <!-- Plans Table -->
        <div class="overflow-x-auto px-6">
            <table class="w-full table-auto border-collapse text-xs md:text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="p-3 border border-gray-200 font-semibold">Plan</th>
                        <th class="p-3 border border-gray-200 font-semibold">Experience Level</th>
                        <th class="p-3 border border-gray-200 font-semibold">Duration</th>
                        <th class="p-3 border border-gray-200 font-semibold">End Date</th>
                        <th class="p-3 border border-gray-200 font-semibold">MRP</th>
                        <th class="p-3 border border-gray-200 font-semibold">Discount (%)</th>
                        <th class="p-3 border border-gray-200 font-semibold">Tax</th>
                        <th class="p-3 border border-gray-200 font-semibold">Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($purchasedPlans as $plan): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="p-3 border border-gray-200 text-center"><?= $plan['feature_name'] ?></td>
                        <td class="p-3 border border-gray-200 text-center"><?= !empty($plan['plan_level']) ? $plan['plan_level'] : $plan['experience_level'] ?>
</td>
                        <td class="p-3 border border-gray-200 text-center"><?= $plan['plan_duration'] ?></td>
                        <td class="p-3 border border-gray-200 text-center"><?= date('d-m-Y', strtotime($plan['end_date'])) ?></td>
                        <td class="p-3 border border-gray-200 text-center">₹<?= number_format($plan['plan_mrp'], 0) ?></td>
                        <td class="p-3 border border-gray-200 text-center"><?= number_format($plan['plan_discount'], 0) ?>%</td>
                        <td class="p-3 border border-gray-200 text-center"><?= number_format($plan['plan_taxes'] * 100) ?>%</td>
                        <td class="p-3 border border-gray-200 text-center font-semibold">₹<?= number_format($plan['plan_total'], 0) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Summary Section -->
        <div class="bg-gray-50 rounded-lg p-6 m-6 space-y-4">
            <div class="flex justify-between items-center">
                <span class="text-sm text-gray-600">MRP Total:</span>
                <span class="text-sm font-semibold">₹<?= number_format($totalMrp, 0) ?></span>
            </div>
            
            <div class="flex justify-between items-center">
                <span class="text-sm text-gray-600">Discount Total:</span>
                <span class="text-sm font-semibold text-red-600">-₹<?= number_format($totalDiscount, 0) ?></span>
            </div>

            <div class="flex justify-between items-center">
                <span class="text-sm text-gray-600">Tax Total (<?= number_format($taxPercentage * 100, 0) ?>%):</span>
                <span class="text-sm font-semibold">₹<?= number_format($totalTax, 0) ?></span>
            </div>

            <div class="border-t border-gray-300 pt-4 flex justify-between items-center">
                <span class="text-lg font-bold text-gray-800">Grand Total:</span>
                <span class="text-lg font-bold text-gray-800">₹<?= number_format($grandTotal, 0) ?></span>
            </div>
        </div>
        <?php endif; ?>

        <!-- Home Link -->
        <div style="margin-top:20px; text-align:center;">
            <a href="<?=base_url()?>" 
               style="display:inline-block; padding:12px 20px; background:#4f46e5; color:#fff; text-decoration:none; font-size:14px; border-radius:6px;">
                ⬅ Back to Home
            </a>
        </div>
    </div>
</body>
</html>
