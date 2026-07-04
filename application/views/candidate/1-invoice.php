<div class="min-h-screen bg-gray-50 p-4 sm:p-6">
    <div class="max-w-4xl mx-auto bg-white rounded-lg shadow-lg p-4 sm:p-8">
        <!-- Invoice Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start gap-4 mb-6 sm:mb-8">
            <div class="order-2 sm:order-1">
                <h1 class="text-xl sm:text-2xl font-bold text-gray-800">Invoice #<?= $invoice['invoice_number'] ?></h1>
                <p class="text-sm sm:text-base text-gray-600 mt-1">Issued: <?= date('d M Y', strtotime($invoice['payment_date'])) ?></p>
            </div>
            <div class="order-1 sm:order-2 w-full sm:w-auto text-right">
                <span class="inline-block px-3 py-1 text-xs sm:text-sm rounded-full bg-green-100 text-green-800">
                    <?= ucfirst($invoice['status']) ?>
                </span>
            </div>
        </div>

        <!-- Payment Details -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6 mb-6 sm:mb-8">
            <div class="space-y-1 sm:space-y-2">
                <h3 class="text-xs sm:text-sm font-semibold text-gray-600 uppercase">Billed To</h3>
                <p class="text-sm sm:text-base text-gray-800"><?= $this->session->userdata('name') ?></p>
                <p class="text-xs sm:text-sm text-gray-600 break-all"><?= $this->session->userdata('email') ?></p>
            </div>
            <div class="space-y-1 sm:space-y-2">
                <h3 class="text-xs sm:text-sm font-semibold text-gray-600 uppercase">Payment Details</h3>
                <p class="text-sm sm:text-base text-gray-800">Order ID: <?= $invoice['order_id'] ?></p>
                <p class="text-xs sm:text-sm text-gray-600 break-all">UPI ID: <?= $invoice['upi_id'] ?></p>
            </div>
        </div>

        <!-- Plan Details Table -->
        <div class="overflow-x-auto mb-6 sm:mb-8">
            <table class="w-full min-w-[300px]">
                <thead class="hidden sm:table-header-group">
                    <tr class="bg-gray-50">
                        <th class="text-left py-3 px-4 text-sm font-semibold text-gray-600">Plan Details</th>
                        <th class="text-right py-3 px-4 text-sm font-semibold text-gray-600">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="border-b flex flex-col sm:table-row">
                        <td class="py-3 px-4 sm:py-4">
                            <div class="sm:hidden text-xs font-semibold text-gray-600 mb-1">Plan Details</div>
                            <div class="space-y-1">
                                <p class="text-sm sm:text-base font-medium text-gray-800">Career Service Plan</p>
                                <p class="text-xs sm:text-sm text-gray-600">Duration: <?= $invoice['duration_id'] ?> months</p>
                                <p class="text-xs sm:text-sm text-gray-600">Valid until: <?= date('d M Y', strtotime($invoice['end_date'])) ?></p>
                            </div>
                        </td>
                        <td class="py-3 px-4 sm:py-4 sm:text-right">
                            <div class="sm:hidden text-xs font-semibold text-gray-600 mb-1">Amount</div>
                            <p class="text-sm sm:text-base text-gray-800">₹<?= number_format($invoice['plan_total'], 2) ?></p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Validity Status -->
        <div class="bg-blue-50 p-4 sm:p-6 rounded-lg mb-6 sm:mb-8">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <div class="space-y-1">
                    <p class="text-xs sm:text-sm font-semibold text-blue-800">Plan Validity</p>
                    <p class="text-xs sm:text-sm text-blue-700"><?= $validity_days ?> days remaining</p>
                </div>
                <a href="#<?//= site_url('career-plans/activate/'.$invoice['purchase_id']) ?>" 
                   class="w-full sm:w-auto text-center px-4 py-2 text-sm bg-blue-600 hover:bg-blue-700 text-white rounded transition-colors">
                    Activate Plan
                </a>
            </div>
        </div>

        <!-- Footer -->
        <div class="border-t pt-6 sm:pt-8 text-center">
            <p class="text-xs sm:text-sm text-gray-600">Thank you for choosing our services</p>
            <p class="text-xs sm:text-sm text-gray-600 mt-1">For any queries, contact info@talentsjobs.in</p>
        </div>
    </div>
</div>