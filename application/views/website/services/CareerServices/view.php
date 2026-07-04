<div class="min-h-screen bg-gray-50 pt-20 pb-10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Back Link -->
        <a href="<?= base_url('career-services') ?>" class="inline-flex items-center text-blue-600 hover:text-blue-700 mb-6">
            <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            All Features
        </a>

        <!-- Feature Header (full width) -->
        <div class="bg-white rounded-2xl shadow-md p-6 md:p-8 mb-8">
            <div class="flex flex-col md:flex-row items-start md:items-center gap-6">
                <?php if (!empty($data['feature_logo'])): ?>
                    <div class="w-24 h-24 rounded-xl bg-gradient-to-br from-blue-50 to-blue-100 flex items-center justify-center flex-shrink-0">
                        <img src="<?= htmlspecialchars($data['feature_logo']) ?>" alt="<?= htmlspecialchars($data['feature_name']) ?>" class="w-14 h-14 object-contain">
                    </div>
                <?php endif; ?>
                <div class="flex-1">
                    <h1 class="text-3xl md:text-4xl font-extrabold text-gray-900 mb-2">
                        <?= htmlspecialchars($data['feature_name'] ?? '') ?>
                    </h1>
                    <?php if (!empty($data['feature_tag'])): ?>
                        <span class="inline-block px-3 py-1 bg-blue-100 text-blue-800 text-sm rounded-full mb-3">
                            <?= htmlspecialchars($data['feature_tag']) ?>
                        </span>
                    <?php endif; ?>
                    <p class="text-lg text-gray-600">
                        <?= nl2br(htmlspecialchars($data['feature_short_description'] ?? '')) ?>
                    </p>
                </div>
            </div>
        </div>

        <!-- Two-column layout: Pricing widget (right/top) + Main content (left) -->
        <div class="flex flex-col md:flex-row-reverse gap-8">
            <!-- Right Column (Pricing Widget) - sticky on desktop, appears first on mobile -->
            <div class="md:w-1/3 flex-shrink-0">
                <div class="sticky top-24">
                    <?php if (!empty($data['plans'])): ?>
                        <?php
                            // Sort plans by duration order
                            $duration_order = ['1 Month', '2 Months', '3 Months', '6 Months', 'Annual'];
                            usort($data['plans'], function($a, $b) use ($duration_order) {
                                $pos_a = array_search($a['duration'], $duration_order);
                                $pos_b = array_search($b['duration'], $duration_order);
                                if ($pos_a === false) $pos_a = 999;
                                if ($pos_b === false) $pos_b = 999;
                                return $pos_a - $pos_b;
                            });
                            $first_plan = $data['plans'][0];
                        ?>
                        <div class="bg-white rounded-2xl shadow-xl overflow-hidden transition-all duration-300 hover:shadow-2xl border border-gray-100">
                            <!-- Header -->
                            <div class="bg-gradient-to-r from-purple-600 to-indigo-600 px-4 sm:px-6 py-4 sm:py-5 text-white">
                                <div class="flex items-center justify-between flex-wrap gap-2">
                                    <div>
                                        <h3 class="font-bold text-lg sm:text-xl"><?= htmlspecialchars($data['feature_name'] ?? 'Plan') ?></h3>
                                        <span class="inline-block mt-1 text-xs bg-white/20 px-2 py-0.5 rounded-full">Choose your duration</span>
                                    </div>
                                    <?php if (!empty($data['feature_coupon_discount'])): ?>
                                        <div class="bg-yellow-400 text-gray-900 text-xs font-bold px-2 py-1 rounded-full shadow-sm whitespace-nowrap">
                                            SAVE<?= (int)$data['feature_coupon_discount'] ?>%
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="p-4 sm:p-6">
                                <!-- Duration Buttons -->
                                <div class="mb-8">
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Select Duration</label>
                                    <div class="flex flex-wrap gap-2" id="duration-buttons">
                                        <?php foreach ($data['plans'] as $index => $plan): ?>
                                            <button type="button"
                                                    class="duration-btn relative px-3 sm:px-4 py-1.5 sm:py-2 rounded-full text-xs sm:text-sm font-medium transition-all whitespace-nowrap <?= $index === 0 ? 'bg-purple-600 text-white shadow-md' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' ?>"
                                                    data-plan-id="<?= $plan['plan_id'] ?? '' ?>"
                                                    data-plan-index="<?= $index ?>"
                                                    data-duration="<?= htmlspecialchars($plan['duration']) ?>"
                                                    data-mrp="<?= $plan['mrp'] ?? 0 ?>"
                                                    data-discount="<?= $plan['discount'] ?? 0 ?>"
                                                    data-final-price="<?= $plan['final_price'] ?? 0 ?>"
                                                    data-plan-total="<?= $plan['plan_total'] ?? 0 ?>"
                                                    data-plan-discount-amount="<?= $plan['plan_discount_amount'] ?? 0 ?>"
                                                    data-coupon-discount="<?= $plan['coupon_discount'] ?? 0 ?>"
                                                    data-taxes-amount="<?= htmlspecialchars($plan['plan_taxes_amount'] ?? '₹0') ?>"
                                                    data-plan-level="<?= htmlspecialchars($plan['plan_level'] ?? '') ?>"
                                                    data-savings="<?= ($plan['mrp'] ?? 0) - ($plan['plan_total'] ?? 0) ?>">
                                                <?= htmlspecialchars($plan['duration']) ?>
                                                <?php if ($plan['duration'] === 'Annual'): ?>
                                                    <span class="absolute -top-2 -right-2 bg-green-500 text-white text-[9px] sm:text-[10px] font-bold px-1.5 py-0.5 rounded-full whitespace-nowrap">Save 20%</span>
                                                <?php endif; ?>
                                            </button>
                                        <?php endforeach; ?>
                                    </div>
                                </div>

                                <!-- Price Display -->
                                <div class="mb-6" id="price-display">
                                    <div class="text-center relative">
                                        <div class="mt-2">
                                            <div class="flex flex-wrap items-baseline justify-center gap-1 sm:gap-2">
                                                <span class="text-3xl sm:text-4xl font-extrabold text-gray-900" id="display-total">₹<?= number_format($first_plan['plan_total'] ?? 0) ?></span>
                                                <span class="text-gray-500 line-through text-base sm:text-lg" id="display-mrp">₹<?= number_format($first_plan['mrp'] ?? 0) ?></span>
                                                <span class="bg-green-100 text-green-700 text-xs font-bold px-2 py-1 rounded-full" id="display-discount-badge"><?= ($first_plan['discount'] ?? 0) ?>% OFF</span>
                                            </div>
                                            <div class="text-gray-500 text-xs sm:text-sm mt-1" id="display-per-month">
                                                ₹<?= number_format($first_plan['final_price'] ?? 0) ?> / <?= htmlspecialchars($first_plan['duration'] ?? '') ?>
                                            </div>
                                            <div class="text-green-600 text-xs sm:text-sm font-medium mt-2 flex items-center justify-center gap-1" id="display-savings">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-trending-up w-4 h-4">
                                                    <path d="M16 7h6v6"></path>
                                                    <path d="m22 7-8.5 8.5-5-5L2 17"></path>
                                                </svg>
                                                You save ₹<?= number_format(($first_plan['mrp'] ?? 0) - ($first_plan['plan_total'] ?? 0)) ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Action Buttons -->
                                <div class="space-y-3 mt-6">
                                    <button id="buy-now-btn" type="button" class="w-full bg-purple-600 text-white py-3 rounded-xl font-semibold hover:bg-purple-700 transition shadow-sm flex items-center justify-center gap-2 text-sm sm:text-base">
                                        Buy Now →
                                    </button>
                                    <button id="add-to-cart-btn" class="w-full bg-white border-2 border-purple-600 text-purple-600 py-3 rounded-xl font-semibold hover:bg-purple-50 transition">
                                        🛒 Add to Cart
                                    </button>
                                </div>

                                <!-- Price Breakup Toggle -->
                                <div class="mt-4 border-t pt-4">
                                    <button id="toggle-breakup" class="text-xs text-gray-500 hover:text-gray-700 flex items-center gap-1 mx-auto">
                                        Show price breakup
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-down w-3 h-3 transition-transform duration-200">
                                            <path d="m6 9 6 6 6-6"></path>
                                        </svg>
                                    </button>
                                    <div id="breakup-details" class="hidden mt-3 text-xs sm:text-sm text-gray-700 bg-gray-50 rounded-lg p-3 sm:p-4 space-y-2 text-left">
                                        <div class="flex justify-between">
                                            <span>Total MRP</span>
                                            <span id="breakup-mrp">₹<?= number_format($first_plan['mrp'] ?? 0) ?></span>
                                        </div>
                                        <div class="flex justify-between text-green-600" id="breakup-discount-row">
                                            <span>Discount (<span id="breakup-discount-percent"><?= ($first_plan['discount'] ?? 0) ?></span>%)</span>
                                            <span>-₹<span id="breakup-discount-amount"><?= number_format($first_plan['plan_discount_amount'] ?? 0) ?></span></span>
                                        </div>
                                        <?php if (!empty($data['feature_coupon_discount'])): ?>
                                            <div class="flex justify-between text-green-600" id="breakup-coupon-row">
                                                <span>Coupon Discount (SAVE<?= (int)$data['feature_coupon_discount'] ?>)</span>
                                                <span>-₹<span id="breakup-coupon-amount"><?= number_format($first_plan['coupon_discount'] ?? 0) ?></span></span>
                                            </div>
                                        <?php endif; ?>
                                        <div class="flex justify-between">
                                            <span>Taxes &amp; Fees</span>
                                            <span id="breakup-taxes"><?= htmlspecialchars($first_plan['plan_taxes_amount'] ?? '₹0') ?></span>
                                        </div>
                                        <div class="flex justify-between font-bold border-t pt-2 mt-2">
                                            <span>You Pay</span>
                                            <span id="breakup-total">₹<?= number_format($first_plan['plan_total'] ?? 0) ?></span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Security Badges -->
                                <div class="flex flex-col items-center gap-2 mt-6 text-xs text-gray-500">
                                    <div class="flex items-center gap-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-shield w-4 h-4 text-green-600">
                                            <path d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z"></path>
                                        </svg>
                                        <span>100% Safe &amp; Secure Payments</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-credit-card w-4 h-4 text-blue-600">
                                            <rect width="20" height="14" x="2" y="5" rx="2"></rect>
                                            <line x1="2" x2="22" y1="10" y2="10"></line>
                                        </svg>
                                        <span>All major cards &amp; UPI accepted</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Left Column (Main Content) -->
            <div class="md:w-2/3">
                <!-- Full Description -->
                <?php if (!empty($data['feature_full_description'])): ?>
                    <div class="bg-white rounded-2xl shadow-md p-6 md:p-8 mb-8">
                        <h2 class="text-2xl font-bold text-gray-800 mb-4">Overview</h2>
                        <div class="prose max-w-none text-gray-600">
                            <?= nl2br(htmlspecialchars($data['feature_full_description'])) ?>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Tags Section -->
                <?php if (!empty($data['tags'])): ?>
                    <div class="bg-white rounded-2xl shadow-md p-6 md:p-8 mb-8">
                        <h2 class="text-2xl font-bold text-gray-800 mb-4">Key Features</h2>
                        <div class="grid gap-4 md:grid-cols-2">
                            <?php foreach ($data['tags'] as $tag): ?>
                                <div class="border-l-4 border-blue-500 pl-4 py-2">
                                    <h3 class="font-semibold text-gray-900"><?= htmlspecialchars($tag['tag_title'] ?? '') ?></h3>
                                    <?php if (!empty($tag['tag_description'])): ?>
                                        <p class="text-sm text-gray-500 mt-1"><?= htmlspecialchars($tag['tag_description']) ?></p>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Benefit Comparison -->
                <?php if (!empty($data['benefit_headers']) && !empty($data['benefit_comparisons'])): ?>
                    <div class="bg-white rounded-2xl shadow-md p-6 md:p-8 mb-8">
                        <h2 class="text-2xl font-bold text-gray-800 mb-4">Compare Benefits</h2>
                        <?php $headers = $data['benefit_headers']; ?>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead>
                                    <tr>
                                        <th class="px-4 py-3 bg-gray-50 text-left text-sm font-medium text-gray-500"><?= htmlspecialchars($headers['title_label'] ?? '') ?></th>
                                        <th class="px-4 py-3 bg-gray-50 text-left text-sm font-medium text-gray-500"><?= htmlspecialchars($headers['col_1_label'] ?? '') ?></th>
                                        <th class="px-4 py-3 bg-gray-50 text-left text-sm font-medium text-gray-500"><?= htmlspecialchars($headers['col_2_label'] ?? '') ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($data['benefit_comparisons'] as $benefit): ?>
                                        <tr class="border-t">
                                            <td class="px-4 py-3 text-sm text-gray-800"><?= htmlspecialchars($benefit['benefit_title'] ?? '') ?></td>
                                            <td class="px-4 py-3 text-sm text-gray-600"><?= htmlspecialchars($benefit['col_1'] ?? '') ?></td>
                                            <td class="px-4 py-3 text-sm text-gray-600"><?= htmlspecialchars($benefit['col_2'] ?? '') ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Q&A Section -->
                <?php if (!empty($data['qas'])): ?>
                    <div class="bg-white rounded-2xl shadow-md p-6 md:p-8 mb-8">
                        <h2 class="text-2xl font-bold text-gray-800 mb-4">Frequently Asked Questions</h2>
                        <div class="space-y-4">
                            <?php foreach ($data['qas'] as $qa): ?>
                                <details class="border border-gray-200 rounded-lg p-4">
                                    <summary class="font-semibold text-gray-900 cursor-pointer"><?= htmlspecialchars($qa['question'] ?? '') ?></summary>
                                    <div class="mt-2 text-gray-600 pl-4">
                                        <?= nl2br(htmlspecialchars($qa['answer'] ?? '')) ?>
                                    </div>
                                </details>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- ✅ UPDATED JAVASCRIPT – uses duration_id for cart actions -->
<script>
document.addEventListener('DOMContentLoaded', function() {

    const plans = <?= json_encode($data['plans']) ?>; // Must contain 'duration_id' field
    const featureId = <?= $data['feature_id'] ?? 0 ?>;
    const couponDiscount = <?= json_encode($data['feature_coupon_discount'] ?? 0) ?>;

    // currentPlan stores the full plan object (including duration_id)
    let currentPlan = plans.length ? plans[0] : null;

    const durationBtns = document.querySelectorAll('.duration-btn');
    const addToCartBtn = document.getElementById('add-to-cart-btn');
    const buyNowBtn = document.getElementById('buy-now-btn');

    const displayTotal = document.getElementById('display-total');
    const displayMrp = document.getElementById('display-mrp');
    const displayDiscountBadge = document.getElementById('display-discount-badge');
    const displayPerMonth = document.getElementById('display-per-month');
    const displaySavings = document.getElementById('display-savings');

    const breakupMrp = document.getElementById('breakup-mrp');
    const breakupDiscountPercent = document.getElementById('breakup-discount-percent');
    const breakupDiscountAmount = document.getElementById('breakup-discount-amount');
    const breakupCouponAmount = document.getElementById('breakup-coupon-amount');
    const breakupTaxes = document.getElementById('breakup-taxes');
    const breakupTotal = document.getElementById('breakup-total');

    const toggleBreakupBtn = document.getElementById('toggle-breakup');
    const breakupDetails = document.getElementById('breakup-details');

    function format(num) {
        return Number(num).toLocaleString('en-IN');
    }

    // ✅ Update UI and currentPlan object using the plans array index
    function updatePlan(planIndex) {
        const plan = plans[planIndex];
        if (!plan) return;

        currentPlan = plan; // plan includes duration_id, plan_total, etc.

        displayTotal.innerText = '₹' + format(plan.plan_total);
        displayMrp.innerText = '₹' + format(plan.mrp);
        displayDiscountBadge.innerText = (plan.discount || 0) + '% OFF';
        displayPerMonth.innerText = '₹' + format(plan.final_price) + ' / ' + plan.duration;

        const savings = plan.mrp - plan.plan_total;
        displaySavings.innerHTML = `
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24">
                <path d="M16 7h6v6"></path>
                <path d="m22 7-8.5 8.5-5-5L2 17"></path>
            </svg>
            You save ₹${format(savings)}
        `;

        // Breakup
        if (breakupMrp) breakupMrp.innerText = '₹' + format(plan.mrp);
        if (breakupDiscountPercent) breakupDiscountPercent.innerText = plan.discount || 0;
        if (breakupDiscountAmount) breakupDiscountAmount.innerText = format(plan.plan_discount_amount);
        if (breakupCouponAmount) breakupCouponAmount.innerText = format(plan.coupon_discount || 0);
        if (breakupTaxes) breakupTaxes.innerText = plan.plan_taxes_amount || '₹0';
        if (breakupTotal) breakupTotal.innerText = '₹' + format(plan.plan_total);
    }

    // Button click: update active style + load plan
    durationBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            durationBtns.forEach(b => {
                b.classList.remove('bg-purple-600','text-white','shadow-md');
                b.classList.add('bg-gray-100','text-gray-700');
            });
            this.classList.add('bg-purple-600','text-white','shadow-md');

            const index = parseInt(this.dataset.planIndex);
            updatePlan(index);
        });
    });

    // Toggle price breakup
    if (toggleBreakupBtn && breakupDetails) {
        toggleBreakupBtn.addEventListener('click', function(e) {
            e.preventDefault();
            const icon = this.querySelector('svg');
            breakupDetails.classList.toggle('hidden');
            if (icon) icon.classList.toggle('rotate-180');
            this.innerHTML = this.innerHTML.includes('Show')
                ? this.innerHTML.replace('Show', 'Hide')
                : this.innerHTML.replace('Hide', 'Show');
        });
    }

    // ✅ ADD TO CART — sends duration_id
    addToCartBtn.addEventListener('click', function() {
        if (!currentPlan) {
            toastr.error('Please select a plan first');
            return;
        }

        const formData = new FormData();
        // ⚠️ key change: use duration_id (not plan_id/PK)
        formData.append('plan_id', currentPlan.duration_id);
        formData.append('feature_id', featureId);
        formData.append('price', currentPlan.plan_total);
        formData.append('quantity', 1);
        formData.append(getCSRFName(), getCSRFToken());

        fetch('<?= base_url("cart/add") ?>', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(res => {
            if (res.csrf_token && res.csrf_name) {
                updateCSRFToken(res.csrf_token, res.csrf_name);
            }
            if (res.status) {
                toastr.success('Added to cart');
                updateCartCount();
            } else {
                toastr.error(res.message || 'Failed');
            }
        })
        .catch(err => {
            console.error(err);
            toastr.error('Network error');
        });
    });

    buyNowBtn.addEventListener('click', function(e) {
		e.preventDefault();

		if (!currentPlan) {
			toastr.error('Please select a plan duration first');
			return;
		}

		const planId = currentPlan.duration_id || currentPlan.plan_id || currentPlan.id;
		if (!planId) {
			console.error('No plan identifier found', currentPlan);
			toastr.error('Plan ID missing. Please refresh.');
			return;
		}

		// Build FormData exactly like Add to Cart
		const fd = new FormData();
		fd.append('plan_id', planId);
		fd.append('feature_id', featureId);
		fd.append('price', currentPlan.plan_total);
		fd.append('quantity', 1);
		fd.append(getCSRFName(), getCSRFToken());

		// Add to cart via AJAX
		fetch('<?= base_url("cart/add") ?>', {
			method: 'POST',
			body: fd
		})
		.then(res => res.json())
		.then(data => {
			if (data.csrf_token) updateCSRFToken(data.csrf_token, data.csrf_name);
			if (data.status) {
				// Successfully added – now go to cart page
				window.location.href = '<?= base_url("cart") ?>';
			} else {
				toastr.error(data.message || 'Failed to add to cart');
			}
		})
		.catch(err => {
			console.error(err);
			toastr.error('Network error');
		});
	});

    // Initialize with first plan
    if (plans.length > 0) {
        updatePlan(0);
    }
});
</script>

<style>
.rotate-180 { transform: rotate(180deg); }
.transition-transform { transition: transform 0.2s ease; }
</style>