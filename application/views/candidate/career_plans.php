<div class="min-h-screen bg-gray-50 p-4 md:p-8">
    <div class="max-w-6xl mx-auto">
       
        <?php if(empty($plans)): ?>
            <div class="bg-white rounded-lg shadow p-6 text-center">
                <div class="text-gray-500 mb-4">
                    <svg class="w-16 h-16 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <p class="text-gray-600 mb-2">No purchased plans found</p>
                <a href="<?php echo site_url('career-services'); ?>" class="text-blue-600 hover:underline">Browse Career Services</a>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php foreach ($plans as $plan): ?>
                    <div class="bg-white rounded-xl shadow-lg hover:shadow-xl transition-shadow duration-300">
                        <div class="p-6">
                            <div class="flex items-center justify-between mb-4">
                                <span class="text-sm font-semibold text-blue-600">
                                    <?php echo $plan['invoice_number']; ?>
                                </span>
                                <span class="px-3 py-1 text-sm rounded-full <?php echo ($plan['payment_status'] == 'success') ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' ?>">
                                    <?php echo ucfirst($plan['payment_status']); ?>
                                </span>
                            </div>
                            
                            <div class="space-y-4">
                                <div class="flex items-center text-gray-600">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                    <?php echo date('d M Y', strtotime($plan['purchase_date'])); ?>
                                </div>
                                
                                <div class="flex items-center text-gray-600">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                    </svg>
                                    ₹<?php echo number_format($plan['plan_total'], 2); ?>
                                </div>
                                
                                <div class="pt-4 border-t border-gray-200">
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-500">Plan Duration</span>
                                        <span class="text-gray-700 font-medium">
                                            <?php echo $plan['duration_id']; ?> Month(s)
                                        </span>
                                    </div>
                                    <div class="flex justify-between text-sm mt-2">
                                        <span class="text-gray-500">Valid Until</span>
                                        <span class="text-gray-700 font-medium">
                                            <?php echo date('d M Y', strtotime($plan['end_date'])); ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-gray-50 px-6 py-4 rounded-b-xl">
                            <div class="flex items-center justify-between">
                                <a href="<?php echo site_url('career-plans/invoice/'.$plan['purchase_id']); ?>" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                    View Invoice
                                </a>
                                <?php if($plan['payment_status'] == 'success'): ?>
                                    <span class="text-green-500">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>