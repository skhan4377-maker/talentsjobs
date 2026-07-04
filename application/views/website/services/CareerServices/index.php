<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 pt-20 pb-10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header Section -->
         <div class="text-center mb-12">
            <h1 class="text-4xl font-extrabold text-gray-900 sm:text-5xl tracking-tight">
                Career Services & Features
            </h1>
            <p class="mt-4 text-xl text-gray-600 max-w-3xl mx-auto">
                Explore all our professional features designed to accelerate your career growth
            </p>
        </div>

        <?php if (empty($data)): ?>
            <div class="text-center py-20">
                <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-gray-200 mb-6">
                    <svg class="w-10 h-10 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <p class="text-gray-500 text-lg">No features available right now.</p>
                <p class="text-gray-400">Please check back later for new career tools.</p>
            </div>
        <?php else: ?>
            <!-- Group features by service -->
            <?php
                $grouped = [];
                foreach ($data as $feature) {
                    $service_id = $feature['service_id'];
                    $service_name = $feature['service_name'] ?? 'Uncategorized';
                    if (!isset($grouped[$service_id])) {
                        $grouped[$service_id] = [
                            'service_name' => $service_name,
                            'features' => []
                        ];
                    }
                    $grouped[$service_id]['features'][] = $feature;
                }
            ?>

            <!-- Render each service section with its features -->
            <?php foreach ($grouped as $service_id => $group): ?>
                <div class="mb-16 last:mb-0">
                    <!-- Service Header -->
                    <div class="flex items-center justify-between mb-6 border-b border-gray-200 pb-3">
                        <h2 class="text-2xl font-bold text-gray-800">
                            <?= htmlspecialchars($group['service_name']) ?>
                        </h2>
                        <!--<span class="text-sm text-gray-500 bg-gray-200 px-3 py-1 rounded-full">
                            <?//= count($group['features']) ?> feature(s)
                        </span>-->
                    </div>

                    <!-- Features Grid -->
                    <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                        <?php foreach ($group['features'] as $feature): ?>
                            <?php
                                $featureUrl = base_url('career-services/' . ($feature['slug'] ?? $feature['feature_id']));
                                $logo = $feature['feature_logo'] ?? base_url('icons/default.png');
                                $plan = $feature['plan'] ?? null;
                            ?>
                            <div class="bg-white rounded-2xl shadow-md hover:shadow-xl transition-all duration-300 flex flex-col overflow-hidden border border-gray-100 hover:border-blue-200 group">
                                <div class="p-5 flex-1">
                                    <!-- Feature Logo & Badge -->
                                    <div class="flex items-center justify-between mb-4">
                                        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-blue-50 to-blue-100 flex items-center justify-center">
                                            <img src="<?= htmlspecialchars($logo) ?>" 
                                                 alt="<?= htmlspecialchars($feature['feature_name']) ?>"
                                                 class="w-7 h-7 object-contain">
                                        </div>
                                        <?php if ($plan && !empty($plan['discount'])): ?>
                                            <span class="text-xs font-semibold bg-green-100 text-green-700 px-2 py-1 rounded-full">
                                                <?= (int)$plan['discount'] ?>% OFF
                                            </span>
                                        <?php endif; ?>
                                    </div>

                                    <!-- Feature Name -->
                                    <h3 class="text-lg font-semibold text-gray-900 mb-2 line-clamp-1 group-hover:text-blue-600 transition">
                                        <?= htmlspecialchars($feature['feature_name'] ?? '') ?>
                                    </h3>

                                    <!-- Short Description -->
                                    <p class="text-gray-500 text-sm mb-4 line-clamp-2">
                                        <?= htmlspecialchars($feature['feature_short_description'] ?? '') ?>
                                    </p>

                                    <!-- Pricing / Plan Info -->
                                    <?php if ($plan): ?>
                                        <div class="mt-2 mb-3">
                                            <div class="flex items-baseline gap-1">
                                                <span class="text-2xl font-bold text-gray-900">
                                                    ₹<?= number_format($plan['final_price'] ?? 0) ?>
                                                </span>
                                                <span class="text-sm text-gray-500">
                                                    for <?= htmlspecialchars($plan['duration'] ?? '') ?>
                                                </span>
                                            </div>
                                            <?php if (isset($plan['mrp']) && $plan['mrp'] > ($plan['final_price'] ?? 0)): ?>
                                                <div class="text-xs text-gray-400 line-through">
                                                    MRP: ₹<?= number_format($plan['mrp']) ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    <?php else: ?>
                                        <div class="text-sm text-gray-400 mt-2 mb-3">
                                            Pricing available on request
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <!-- Action Button -->
                                <div class="px-5 pb-5 pt-1">
                                    <a href="<?= htmlspecialchars($featureUrl) ?>"
                                       class="w-full inline-flex justify-center items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-xl transition-colors duration-200">
                                        View Details
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>

            <!-- Optional: Show total count -->
            <!--<div class="text-center text-gray-400 text-sm pt-8 border-t border-gray-200 mt-6">
                Showing <?//= count($data) ?> feature(s) across <?//= count($grouped) ?> service(s)
            </div>-->
        <?php endif; ?>
    </div>
</div>