<div class="container mx-auto px-4 py-6 md:px-6 md:py-8">
	  <div class="mb-8 flex justify-between items-center">
	  <!-- Right-aligned content -->
	  <div class="ml-auto flex items-center space-x-4">
		<!-- Total Purchases Button with Icon -->
		<button class="flex items-center bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
		  <i class="fas fa-shopping-cart mr-2"></i> <!-- Shopping cart icon -->
		  Total Purchases: <?= count($purchases) ?>
		</button>
	  </div>
	</div>


    <!-- Purchases Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php foreach($purchases as $item): ?>
        <div class="bg-white rounded-xl shadow-lg hover:shadow-2xl transition-shadow duration-300">
            <!-- Image Section -->
            <div class="relative">
                <img src="<?= html_escape($item['preview_image']) ?>" 
                     alt="<?= html_escape($item['template_name']) ?>"
                     class="w-full h-48 object-cover rounded-t-xl">
                <span class="absolute top-2 right-2 bg-green-100 text-green-800 text-sm px-3 py-1 rounded-full">
                    <i class="fas fa-check-circle mr-1"></i><?= $item['payment_status'] ?>
                </span>
            </div>

            <!-- Content Section -->
            <div class="p-4 md:p-6">
                <h3 class="text-xl font-semibold text-gray-800 mb-2">
                    <?= html_escape($item['template_name']) ?>
                </h3>
                
                <!-- Details Grid -->
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div class="flex items-center">
                        <i class="fas fa-calendar-day text-blue-600 mr-2"></i>
                        <div>
                            <p class="text-xs text-gray-500">Purchased</p>
                            <p class="text-sm"><?= date('d M Y', strtotime($item['purchase_date'])) ?></p>
                        </div>
                    </div>
                    <div class="flex items-center">
                        <i class="fas fa-clock text-purple-600 mr-2"></i>
                        <div>
                            <p class="text-xs text-gray-500">Valid Till</p>
                            <p class="text-sm"><?= date('d M Y', strtotime($item['end_date'])) ?></p>
                        </div>
                    </div>
                </div>

                <!-- Price Section -->
                <div class="flex justify-between items-center bg-gray-50 p-3 rounded-lg">
                    <div>
                        <span class="text-sm text-gray-600">Amount Paid:</span>
                        <span class="text-lg font-bold text-green-600 ml-2">
                            ₹<?= number_format($item['amount_paid'], 2) ?>
                        </span>
                    </div>
                    <a href="https://talentsjobs.in/development/resume/<?= $item['template_id'] ?>/edit" 
					   class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors text-sm whitespace-nowrap">
						<i class="fas fa-edit mr-1"></i>Edit Resume
					</a>

                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- Empty State -->
    <?php if(empty($purchases)): ?>
    <div class="text-center py-12">
        <div class="mb-4 text-6xl text-gray-300">
            <i class="fas fa-file-invoice-dollar"></i>
        </div>
        <h3 class="text-xl text-gray-500 mb-2">No purchases found</h3>
        <p class="text-gray-400 mb-4">Your purchased resume templates will appear here</p>
        <a href="<?= site_url('career-plans') ?>" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
            Browse Plans
        </a>
    </div>
    <?php endif; ?>
</div>
