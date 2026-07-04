<div class="container mx-auto px-4 py-6 md:py-8">
    <!-- Header -->
	 <div class="mb-6 md:mb-8 flex justify-end">
		<span class="text-xs md:text-sm bg-blue-100 text-blue-800 px-3 py-1.5 rounded-full">
			<i class="fas fa-download mr-1"></i><?= count($downloads) ?> Templates
		</span>
	</div>
	
    <!-- Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-6">
        <?php foreach($downloads as $item): ?>
        <div class="bg-white rounded-xl shadow-md hover:shadow-lg transition-shadow duration-300 text-sm">
            <!-- Image -->
            <div class="relative">
                <img src="<?= html_escape($item['preview_image']) ?>" 
                     alt="<?= html_escape($item['name']) ?>" 
                     class="w-full h-40 md:h-48 object-cover rounded-t-xl">
                <span class="absolute top-2 right-2 <?= $item['template_type'] === 'paid' ? 'bg-purple-100 text-purple-800' : 'bg-green-100 text-green-800' ?> text-xs px-3 py-1 rounded-full">
                    <?= ucfirst($item['template_type']) ?> Template
                </span>
            </div>

            <!-- Content -->
            <div class="p-4 md:p-6">
                <h3 class="text-base md:text-xl font-semibold text-gray-800 mb-2">
                    <?= html_escape($item['name']) ?>
                </h3>

                <!-- Category & Count -->
                <div class="flex flex-wrap items-center mb-4 gap-2">
                    <span class="bg-gray-100 text-gray-600 px-2.5 py-1 rounded-full text-xs md:text-sm">
                        <i class="fas fa-tag mr-1"></i><?= html_escape($item['category']) ?>
                    </span>
                    <span class="text-xs md:text-sm text-gray-500">
                        <i class="fas fa-download mr-1"></i>3 times
                    </span>
                </div>

                <!-- Timeline -->
                <div class="border-t pt-3 md:pt-4">
                    <div class="flex justify-between text-xs md:text-sm">
                        <div>
                            <p class="text-gray-500">First Downloaded</p>
                            <p class="text-gray-700"><?= date('d M Y', strtotime($item['downloaded_at'])) ?></p>
                        </div>
                        <div class="text-right">
                            <p class="text-gray-500">Last Accessed</p>
                            <p class="text-gray-700">2 days ago</p>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="mt-4 flex justify-between items-center">
                    <a href="https://talentsjobs.in/development/resume/<?= $item['template_id'] ?>/edit" 
                       class="bg-blue-600 text-white px-3 py-1.5 md:px-4 md:py-2 rounded-md hover:bg-blue-700 transition-colors text-xs md:text-sm">
                        <i class="fas fa-edit mr-1"></i>Edit Design
                    </a>
                    <button class="text-gray-400 hover:text-blue-600">
                        <i class="fas fa-ellipsis-v"></i>
                    </button>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- Empty State -->
    <?php if(empty($downloads)): ?>
    <div class="text-center py-12">
        <div class="mb-4 text-5xl md:text-6xl text-gray-300">
            <i class="fas fa-file-download"></i>
        </div>
        <h3 class="text-lg md:text-xl text-gray-500 mb-2">No downloads yet</h3>
        <p class="text-gray-400 mb-4 text-sm">Your downloaded resumes will appear here</p>
        <a href="<?= site_url('app/choose-template') ?>" class="bg-blue-600 text-white px-5 py-2 rounded-lg hover:bg-blue-700 text-sm">
            Browse Templates
        </a>
    </div>
    <?php endif; ?>
</div>
