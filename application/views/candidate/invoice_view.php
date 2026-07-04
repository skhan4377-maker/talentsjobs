<style>
    @media print {
        body * {
            visibility: hidden;
        }
        .invoice-container, .invoice-container * {
            visibility: visible;
        }
        .invoice-container {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            margin: 0;
            padding: 0;
        }
        .no-print {
            display: none;
        }
    }
</style>

<div class="invoice-container max-w-4xl mx-auto bg-white rounded-lg shadow-lg overflow-hidden">
    <!-- Header -->
    <div class="bg-blue-800 text-white px-6 py-4 flex flex-wrap justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold">INVOICE</h1>
            <p class="text-blue-200 text-sm">Tax Invoice / Bill of Supply</p>
        </div>
        <div class="text-right">
            <div class="text-sm">Invoice No.</div>
            <div class="text-xl font-mono font-bold"><?= htmlspecialchars($invoice['invoice_no']) ?></div>
        </div>
    </div>

    <div class="p-6">
        <!-- Company & Customer Info -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <div>
                <h3 class="text-lg font-semibold border-b pb-2 mb-3">From</h3>
                <div class="text-gray-700">
                    <p class="font-bold"><?= htmlspecialchars($company['name']) ?></p>
                    <p><?= nl2br(htmlspecialchars($company['address'])) ?></p>
                    <p>Email: <?= htmlspecialchars($company['email']) ?></p>
                    <p>Phone: <?= htmlspecialchars($company['phone']) ?></p>
                    <p>GSTIN: <?= htmlspecialchars($company['gstin']) ?></p>
                </div>
            </div>
            <div>
                <h3 class="text-lg font-semibold border-b pb-2 mb-3">Bill To</h3>
                <div class="text-gray-700">
                    <p class="font-bold"><?= htmlspecialchars($invoice['first_name'] . ' ' . $invoice['last_name']) ?></p>
                    <p>Email: <?= htmlspecialchars($invoice['email']) ?></p>
                    <p>Mobile: <?= htmlspecialchars($invoice['mobile']) ?></p>
                </div>
            </div>
        </div>

        <!-- Invoice Details Table -->
        <div class="overflow-x-auto mb-6">
            <table class="min-w-full border-collapse border border-gray-200">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="border border-gray-200 px-4 py-2 text-left text-sm font-semibold">Description</th>
                        <th class="border border-gray-200 px-4 py-2 text-left text-sm font-semibold">Duration</th>
                        <th class="border border-gray-200 px-4 py-2 text-right text-sm font-semibold">Amount (₹)</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="border border-gray-200 px-4 py-2"><?= htmlspecialchars($invoice['feature_name']) ?> Plan</td>
                        <td class="border border-gray-200 px-4 py-2"><?= htmlspecialchars($invoice['duration']) ?></td>
                        <td class="border border-gray-200 px-4 py-2 text-right"><?= number_format($invoice['plan_total'], 2) ?></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Totals -->
        <!-- Totals -->
		<div class="flex justify-end mb-6">
			<div class="w-64">
				<?php 
				$mrp = floatval($invoice['plan_mrp']);
				$discount_percent = floatval($invoice['plan_discount']);
				$tax_percent = floatval($invoice['plan_taxes']);
				
				// Discount amount = MRP * (discount % / 100)
				$discount_amount = $mrp * ($discount_percent / 100);
				// Taxable amount = MRP - discount
				$subtotal = $mrp - $discount_amount;
				// Tax amount = taxable amount * (tax % / 100)
				$tax_amount = $subtotal * ($tax_percent / 100);
				// Total paid (should equal plan_total)
				$total_paid = floatval($invoice['paid_amount']);
				?>
				<div class="flex justify-between py-1">
					<span>MRP:</span>
					<span>₹<?= number_format($mrp, 2) ?></span>
				</div>
				<?php if ($discount_percent > 0): ?>
				<div class="flex justify-between py-1 text-green-600">
					<span>Discount (<?= number_format($discount_percent, 0) ?>%):</span>
					<span>- ₹<?= number_format($discount_amount, 2) ?></span>
				</div>
				<div class="flex justify-between py-1 border-t border-gray-200 pt-1 mt-1">
					<span>Subtotal (after discount):</span>
					<span>₹<?= number_format($subtotal, 2) ?></span>
				</div>
				<?php endif; ?>
				<?php if ($tax_percent > 0): ?>
				<div class="flex justify-between py-1 text-gray-600">
					<span>Tax (<?= number_format($tax_percent, 0) ?>%):</span>
					<span>+ ₹<?= number_format($tax_amount, 2) ?></span>
				</div>
				<?php endif; ?>
				<div class="flex justify-between py-2 font-bold text-lg border-t mt-2 pt-2">
					<span>Total Paid:</span>
					<span>₹<?= number_format($total_paid, 2) ?></span>
				</div>
			</div>
		</div>

        <!-- Payment & Plan Info -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-sm text-gray-600 border-t pt-6">
            <div>
                <p><span class="font-semibold">Payment Status:</span> 
                    <span class="inline-block px-2 py-1 rounded-full text-xs font-semibold <?= $invoice['payment_status'] == 'success' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' ?>">
                        <?= ucfirst($invoice['payment_status']) ?>
                    </span>
                </p>
                <p><span class="font-semibold">Transaction ID:</span> <?= htmlspecialchars($invoice['transaction_id'] ?: 'N/A') ?></p>
                <p><span class="font-semibold">Payment Method:</span> <?= ucfirst(str_replace('_', ' ', $invoice['payment_method'])) ?></p>
                <p><span class="font-semibold">Payment Date:</span> <?= date('d M Y, h:i A', strtotime($invoice['payment_date'])) ?></p>
            </div>
            <div>
                <p><span class="font-semibold">Plan Validity:</span> <?= date('d M Y', strtotime($invoice['start_date'])) ?> – <?= date('d M Y', strtotime($invoice['end_date'])) ?></p>
                <p><span class="font-semibold">Plan Status:</span> 
                    <span class="capitalize <?= $invoice['plan_status'] == 'active' ? 'text-green-600' : 'text-red-600' ?>">
                        <?= $invoice['plan_status'] ?>
                    </span>
                </p>
            </div>
        </div>

        <!-- Footer Note -->
        <div class="mt-8 text-xs text-gray-500 border-t pt-4 text-center">
            <p>This is a computer generated invoice and does not require a physical signature.</p>
            <p>For any queries, contact <?= htmlspecialchars($company['email']) ?> or call <?= htmlspecialchars($company['phone']) ?>.</p>
        </div>

        <!-- Print Button -->
        <div class="mt-6 text-center no-print">
            <button onclick="window.print()" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded inline-flex items-center">
                <i class="fas fa-print mr-2"></i> Print Invoice
            </button>
            <a href="<?= site_url('candidate/plans') ?>" class="ml-2 bg-gray-500 hover:bg-gray-600 text-white font-semibold py-2 px-4 rounded inline-flex items-center">
                <i class="fas fa-arrow-left mr-2"></i> Back to Plans
            </a>
        </div>
    </div>
</div>