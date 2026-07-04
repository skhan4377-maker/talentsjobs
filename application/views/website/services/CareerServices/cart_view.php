<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!-- Razorpay SDK -->
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<style>
    .animate-spin {
        animation: spin 1s linear infinite;
    }
    @keyframes spin {
        to { transform: rotate(360deg); }
    }
</style>

<div class="min-h-screen bg-gray-50 pt-20 pb-10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <h1 class="text-3xl font-extrabold text-gray-900 mb-8">🛒 Your Cart</h1>

        <!-- Empty Cart -->
        <div id="empty-cart" class="<?= empty($cart_items) ? '' : 'hidden' ?> text-center py-20">
            <div class="text-6xl mb-4">🛒</div>
            <h2 class="text-2xl font-semibold text-gray-700 mb-2">Your cart is empty</h2>
            <p class="text-gray-500 mb-6">Looks like you haven't added anything yet.</p>
            <a href="<?= base_url('career-services') ?>"
               class="inline-block bg-purple-600 text-white px-6 py-3 rounded-xl font-semibold hover:bg-purple-700 transition">
                Browse Career Services
            </a>
        </div>

        <!-- Cart Content -->
        <div id="cart-content" class="<?= empty($cart_items) ? 'hidden' : '' ?> flex flex-col lg:flex-row gap-8">
            <!-- ================= CART ITEMS (Desktop + Mobile) ================= -->
            <div class="lg:w-2/3">
                <!-- Desktop Table -->
                <div class="hidden md:block bg-white rounded-2xl shadow-md overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Item</th>
                                    <th class="px-6 py-4 text-center text-xs font-semibold text-gray-500 uppercase">Duration</th>
                                    <th class="px-6 py-4 text-right text-xs font-semibold text-gray-500 uppercase">Price Details</th>
                                    <th class="px-6 py-4 text-right text-xs font-semibold text-gray-500 uppercase">Total</th>
                                    <th class="px-4 py-4"></th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-100">
                                <?php foreach ($cart_items as $item): ?>
                                    <?php
                                        $feature_id       = $item['feature_id'] ?? ($item['options']['feature_id'] ?? null);
                                        $available_plans  = $plans_by_feature[$feature_id] ?? [];
                                        $selected_plan_id = $item['plan_id'] ?? $item['id'] ?? 0;
                                        $finalPrice   = $item['final_price'] ?? $item['price'] ?? 0;
                                        $planMrp      = $item['plan_mrp'] ?? $finalPrice;
                                        $discountAmt  = $item['discount_amount'] ?? 0;
                                        $taxAmt       = $item['tax_amount'] ?? 0;
                                        $discountPct  = $item['plan_discount'] ?? 0;
                                        $taxPct       = $item['plan_taxes'] ?? 0;
                                        $imgSrc = !empty($item['feature_logo']) ? $item['feature_logo'] : 'assets/default-product.png';
                                    ?>
                                    <tr data-feature-id="<?= $feature_id ?>">
                                        <td class="px-6 py-5">
                                            <div class="flex items-start gap-4">
                                                <div class="w-16 h-16 rounded-2xl border border-gray-200 bg-white p-2 flex items-center justify-center shrink-0">
                                                    <img src="<?=$imgSrc?>" class="max-w-full max-h-full object-contain">
                                                </div>
                                                <div>
                                                    <h3 class="font-semibold text-gray-900 text-sm"><?= $item['feature_name'] ?? $item['name'] ?></h3>
                                                    <p class="text-xs text-gray-500 mt-1"><?= $item['plan_label'] ?? $item['plan_level'] ?? '' ?></p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-5 text-center">
                                            <select class="plan-duration border border-gray-300 rounded-lg px-3 py-2 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-purple-500"
                                                    data-feature-id="<?= $feature_id ?>">
                                                <?php foreach ($available_plans as $p): ?>
                                                    <option value="<?= $p['duration_id'] ?>"
                                                            data-price="<?= $p['plan_total'] ?>"
                                                            <?= $selected_plan_id == $p['duration_id'] ? 'selected' : '' ?>>
                                                        <?= $p['duration'] ?> - ₹<?= number_format($p['plan_total'], 2) ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </td>
                                        <td class="px-6 py-5 text-right">
                                            <div class="space-y-1 inline-block text-right">
                                                <div class="text-xs text-gray-400 line-through">₹<?= number_format($planMrp, 2) ?></div>
                                                <?php if ($discountPct > 0): ?>
                                                    <div class="text-xs font-semibold text-green-600"><?= (float)$discountPct ?>% OFF (-₹<?= number_format($discountAmt, 2) ?>)</div>
                                                <?php endif; ?>
                                                <?php if ($taxPct > 0): ?>
                                                    <div class="text-xs text-gray-500">+<?= (float)$taxPct ?>% Tax (₹<?= number_format($taxAmt, 2) ?>)</div>
                                                <?php endif; ?>
                                                <!--<div class="text-lg font-bold text-purple-700">₹<?//= number_format($finalPrice, 2) ?></div>-->
                                            </div>
                                        </td>
                                        <td class="px-6 py-5 text-right">
                                            <div class="font-bold text-lg text-purple-700">₹<?= number_format($finalPrice, 2) ?></div>
                                        </td>
                                        <td class="px-4 py-5 text-center">
                                            <button class="remove-item group inline-flex items-center justify-center w-10 h-10 rounded-xl border border-red-100 bg-red-50 hover:bg-red-100 hover:border-red-200 transition-all duration-200"
                                                    data-plan-id="<?= $selected_plan_id ?>" title="Remove Item">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-red-500 group-hover:text-red-700 transition" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3m-7 0h8" />
                                                </svg>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- Mobile Cards -->
                <div class="md:hidden space-y-4">
                    <?php foreach ($cart_items as $item): ?>
                        <?php
                            $feature_id       = $item['feature_id'] ?? ($item['options']['feature_id'] ?? null);
                            $available_plans  = $plans_by_feature[$feature_id] ?? [];
                            $selected_plan_id = $item['plan_id'] ?? $item['id'] ?? 0;
                            $finalPrice   = $item['final_price'] ?? $item['price'] ?? 0;
                            $planMrp      = $item['plan_mrp'] ?? $finalPrice;
                            $discountAmt  = $item['discount_amount'] ?? 0;
                            $taxAmt       = $item['tax_amount'] ?? 0;
                            $discountPct  = $item['plan_discount'] ?? 0;
                            $taxPct       = $item['plan_taxes'] ?? 0;
                            $imgSrc = !empty($item['feature_logo']) ? $item['feature_logo'] : 'assets/default-product.png';
                        ?>
                        <div class="bg-white rounded-2xl shadow p-4 border border-gray-100">
                            <div class="flex items-start gap-3">
                                <div class="w-20 h-20 rounded-2xl border border-gray-200 bg-white p-2 flex items-center justify-center shrink-0">
                                    <img src="<?=$imgSrc?>" class="max-w-full max-h-full object-contain">
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h3 class="font-semibold text-gray-900 text-sm"><?= $item['feature_name'] ?? $item['name'] ?></h3>
                                    <p class="text-xs text-gray-500 mt-1"><?= $item['plan_label'] ?? $item['plan_level'] ?? '' ?></p>
                                </div>
                                <button class="remove-item group flex items-center justify-center w-10 h-10 rounded-xl bg-red-50 border border-red-100 hover:bg-red-100 hover:border-red-200 transition"
                                        data-plan-id="<?= $selected_plan_id ?>" title="Remove Item">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-red-500 group-hover:text-red-700 transition" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3m-7 0h8" />
                                    </svg>
                                </button>
                            </div>
                            <div class="mt-4">
                                <label class="text-xs text-gray-400 block mb-1">Duration</label>
                                <select class="plan-duration border border-gray-300 rounded-lg px-3 py-2 w-full text-sm bg-white"
                                        data-feature-id="<?= $feature_id ?>">
                                    <?php foreach ($available_plans as $p): ?>
                                        <option value="<?= $p['duration_id'] ?>"
                                                data-price="<?= $p['plan_total'] ?>"
                                                <?= $selected_plan_id == $p['duration_id'] ? 'selected' : '' ?>>
                                            <?= $p['duration'] ?> - ₹<?= number_format($p['plan_total'], 2) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="mt-4 border-t pt-4">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-sm text-gray-500">MRP</span>
                                    <span class="text-sm text-gray-400 line-through">₹<?= number_format($planMrp, 2) ?></span>
                                </div>
                                <?php if ($discountPct > 0): ?>
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="text-sm text-green-600">Discount (<?= (float)$discountPct ?>%)</span>
                                        <span class="text-sm font-medium text-green-600">-₹<?= number_format($discountAmt, 2) ?></span>
                                    </div>
                                <?php endif; ?>
                                <?php if ($taxPct > 0): ?>
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="text-sm text-gray-500">Tax (<?= (float)$taxPct ?>%)</span>
                                        <span class="text-sm text-gray-700">₹<?= number_format($taxAmt, 2) ?></span>
                                    </div>
                                <?php endif; ?>
                                <div class="flex items-center justify-between border-t pt-3 mt-3">
                                    <div>
                                        <div class="text-xs text-gray-400">Final Payable</div>
                                        <div class="text-xs text-gray-400">Inclusive of taxes</div>
                                    </div>
                                    <div class="text-2xl font-extrabold text-purple-700">₹<?= number_format($finalPrice, 2) ?></div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- ================= PRICE SUMMARY ================= -->
            <div class="lg:w-1/3">
                <div class="bg-white rounded-2xl shadow-xl p-6 sticky top-24 border border-gray-100">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">Price Summary</h2>
                    <?php
                        $summary  = $cart_summary ?? [];
                        $mrp      = $summary['total_mrp'] ?? 0;
                        $discount = $summary['total_discount'] ?? 0;
                        $taxes    = $summary['total_taxes'] ?? 0;
                        $grand    = $summary['grand_total'] ?? 0;
                        $discountPercent = ($mrp > 0) ? round(($discount / $mrp) * 100, 1) : 0;
                    ?>
                    <div class="space-y-4">
                        <div class="flex justify-between text-sm text-gray-600">
                            <span>Total MRP</span><span>₹<?= number_format($mrp, 2) ?></span>
                        </div>
                        <?php if ($discount > 0): ?>
                            <div class="flex justify-between text-sm text-green-600 font-semibold">
                                <span>Discount <span class="text-xs">(<?= $discountPercent ?>% Saved)</span></span>
                                <span>-₹<?= number_format($discount, 2) ?></span>
                            </div>
                        <?php endif; ?>
                        <?php if ($taxes > 0): ?>
                            <div class="flex justify-between text-sm text-gray-600">
                                <span>Tax Included</span><span>₹<?= number_format($taxes, 2) ?></span>
                            </div>
                        <?php endif; ?>
                        <div class="border-t pt-5">
                            <div class="flex items-center justify-between">
                                <div>
                                    <div class="text-lg font-bold text-gray-900">Total Amount</div>
                                    <div class="text-xs text-gray-500 mt-1">Inclusive of all taxes</div>
                                </div>
                                <div class="text-3xl font-extrabold text-purple-700">₹<?= number_format($grand, 2) ?></div>
                            </div>
                        </div>
                    </div>

                    <button id="checkout-btn"
                            class="w-full mt-8 bg-purple-600 hover:bg-purple-700 text-white py-3 rounded-xl font-semibold transition disabled:opacity-50"
                            <?= ($this->session->userdata('logged_in')) ? '' : 'disabled title="Please login to checkout"' ?>>
                        Proceed to Checkout
                    </button>

                    <?php if (!$this->session->userdata('logged_in')): ?>
                        <p class="text-xs text-gray-400 text-center mt-3">
                            <a href="<?= base_url('auth/login') ?>" class="text-blue-600 underline">Login</a> or
                            <a href="<?= base_url('registration/candidate') ?>" class="text-blue-600 underline">Register</a> to checkout.
                        </p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Payment Processing Overlay (initially hidden) -->
<div id="payment-overlay" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center hidden">
    <div class="bg-white rounded-2xl shadow-xl p-8 text-center max-w-sm w-full mx-4">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-purple-700 mx-auto"></div>
        <p class="mt-4 text-gray-700 font-semibold">Payment is being processed...</p>
        <p class="text-sm text-gray-500 mt-1">Please do not close or refresh this page</p>
    </div>
</div>

<script>
// ================== GLOBAL DATA ==================
const cartItems = <?= json_encode($cart_items) ?>;
const cartSummary = <?= json_encode($cart_summary ?? []) ?>;

const paymentOverlay = document.getElementById('payment-overlay');
const checkoutBtn = document.getElementById('checkout-btn');
let isPaymentProcessing = false;
let beforeUnloadHandler = null;

// ---------- HELPER: SHOW / HIDE PROCESSING OVERLAY ----------
function showPaymentProcessing(state) {
    isPaymentProcessing = state;
    if (state) {
        // Reset overlay to spinner
        paymentOverlay.innerHTML = `
            <div class="bg-white rounded-2xl shadow-xl p-8 text-center max-w-sm w-full mx-4">
                <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-purple-700 mx-auto"></div>
                <p class="mt-4 text-gray-700 font-semibold">Payment is being processed...</p>
                <p class="text-sm text-gray-500 mt-1">Please do not close or refresh this page</p>
            </div>`;
        paymentOverlay.classList.remove('hidden');
        checkoutBtn.disabled = true;

        // Attach beforeunload only while processing
        beforeUnloadHandler = function(e) {
            e.preventDefault();
            e.returnValue = 'Payment is still processing. Are you sure you want to leave?';
            return e.returnValue;
        };
        window.addEventListener('beforeunload', beforeUnloadHandler);
    } else {
        paymentOverlay.classList.add('hidden');
        checkoutBtn.disabled = false;
        if (beforeUnloadHandler) {
            window.removeEventListener('beforeunload', beforeUnloadHandler);
            beforeUnloadHandler = null;
        }
    }
}

// ---------- SUCCESS OVERLAY (no prompt) ----------
function showPaymentSuccessAndRedirect(redirectUrl) {
    // Stop processing state – removes beforeunload listener
    isPaymentProcessing = false;
    if (beforeUnloadHandler) {
        window.removeEventListener('beforeunload', beforeUnloadHandler);
        beforeUnloadHandler = null;
    }

    // Transform overlay into success message
    paymentOverlay.innerHTML = `
        <div class="bg-white rounded-2xl shadow-xl p-8 text-center max-w-sm w-full mx-4">
            <div class="text-4xl mb-2">✅</div>
            <p class="text-xl font-bold text-gray-800">Payment Successful!</p>
            <p class="text-sm text-gray-500 mt-2">Redirecting to your dashboard...</p>
        </div>`;
    paymentOverlay.classList.remove('hidden');
    checkoutBtn.disabled = true; // keep disabled

    // Redirect after a short pause so user sees the message
    setTimeout(() => {
        window.location.href = redirectUrl;
    }, 1500);
}

// ---------- CHECKOUT BUTTON ----------
checkoutBtn.addEventListener('click', async function () {
    if (this.disabled || isPaymentProcessing) return;

    const btn = this;
    showPaymentProcessing(true);
    btn.textContent = 'Processing...';

    try {
        // 1. Create Razorpay order
        const orderRes = await fetch('<?= base_url("payment/create-order") ?>', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({
                [getCSRFName()]: getCSRFToken()
            })
        });
        const orderData = await orderRes.json();
        if (!orderData.success) throw new Error(orderData.error || 'Order creation failed');

        if (orderData.csrf_token) updateCSRFToken(orderData.csrf_token, orderData.csrf_name);

        // 2. Open Razorpay
        const options = {
            key: orderData.key,
            amount: orderData.amount,
            currency: orderData.currency,
            name: "Talents Jobs",
            description: cartItems.length === 1
                ? `${cartItems[0].feature_name || 'Item'} Subscription`
                : `${cartItems.length} Services Subscription`,
            image: "<?= base_url('assets/frontend/logo.png') ?>",
            order_id: orderData.order_id,
            handler: async function (response) {
                // Keep overlay while verifying
                try {
                    const verifyRes = await fetch('<?= base_url("payment/verify-payment") ?>', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                        body: new URLSearchParams({
                            razorpay_order_id: response.razorpay_order_id,
                            razorpay_payment_id: response.razorpay_payment_id,
                            razorpay_signature: response.razorpay_signature,
                            cart_items: JSON.stringify(cartItems),
                            [getCSRFName()]: getCSRFToken()
                        })
                    });
                    const result = await verifyRes.json();
                    if (result.success) {
                        // ✅ Success – show success overlay & redirect with NO prompt
                        showPaymentSuccessAndRedirect('<?= base_url("candidate/dashboard") ?>');
                    } else {
                        throw new Error(result.error || 'Verification failed');
                    }
                } catch (err) {
                    console.error('Verification error:', err);
                    alert('Payment verification failed: ' + err.message);
                    showPaymentProcessing(false);
                    btn.textContent = 'Proceed to Checkout';
                }
            },
            modal: {
                ondismiss: function () {
                    showPaymentProcessing(false);
                    btn.textContent = 'Proceed to Checkout';
                }
            },
            theme: { color: '#2563eb' },
            retry: { enabled: true, max_count: 3 },
            notes: {
                user_id: '<?= $this->session->userdata('user_id') ?>',
                item_count: cartItems.length
            }
        };

        const rzp = new Razorpay(options);
        rzp.open();

    } catch (err) {
        console.error('Checkout error:', err);
        alert('Failed to start payment. ' + (err.message || ''));
        showPaymentProcessing(false);
        btn.textContent = 'Proceed to Checkout';
    }
});

// ---------- CART UPDATE / REMOVE (unchanged) ----------
const cartUpdatePlanAPI = '<?= base_url("cart/update-plan") ?>';
const cartRemoveAPI     = '<?= base_url("cart/remove") ?>';

document.addEventListener('change', e => {
    if (e.target.matches('.plan-duration')) {
        const select = e.target;
        const featureId = select.dataset.featureId;
        const selectedOption = select.options[select.selectedIndex];
        const planId = selectedOption.value;
        const fd = new FormData();
        fd.append('plan_id', planId);
        fd.append('feature_id', featureId);
        fd.append(getCSRFName(), getCSRFToken());

        fetch(cartUpdatePlanAPI, { method: 'POST', body: fd })
            .then(res => res.json())
            .then(json => {
                if (json.csrf_token) updateCSRFToken(json.csrf_token, json.csrf_name);
                if (json.status) {
                    window.location.reload();
                } else {
                    alert(json.message || 'Failed to update plan');
                }
            })
            .catch(err => {
                console.error(err);
                alert('Network error');
            });
    }
});

document.addEventListener('click', function (e) {
    const removeBtn = e.target.closest('.remove-item');
    if (!removeBtn) return;
    e.preventDefault();
    if (!confirm('Remove this item from cart?')) return;

    const planId = removeBtn.dataset.planId;
    if (!planId) { alert('Invalid item'); return; }

    const fd = new FormData();
    fd.append('plan_id', planId);
    fd.append(getCSRFName(), getCSRFToken());

    removeBtn.disabled = true;

    fetch(cartRemoveAPI, { method: 'POST', body: fd })
        .then(res => res.json())
        .then(json => {
            if (json.csrf_token) updateCSRFToken(json.csrf_token, json.csrf_name);
            if (json.status) {
                window.location.reload();
            } else {
                removeBtn.disabled = false;
                alert(json.message || 'Failed to remove item');
            }
        })
        .catch(err => {
            console.error(err);
            removeBtn.disabled = false;
            alert('Network error');
        });
});
</script>