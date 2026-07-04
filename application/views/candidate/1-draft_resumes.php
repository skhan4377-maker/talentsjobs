<div class="container mx-auto px-4 py-8">
    
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php foreach($drafts as $draft): ?>
        <div class="bg-white rounded-xl shadow-lg hover:shadow-2xl transition-shadow duration-300">
            <div class="relative">
                <img src="<?= html_escape($draft['preview_image']) ?>" 
                     class="w-full h-48 object-cover rounded-t-xl">
                <span class="absolute top-2 right-2 bg-yellow-100 text-yellow-800 px-3 py-1 rounded-full text-sm">
                    Draft
                </span>
            </div>
            
            <div class="p-6">
                <h3 class="text-xl font-semibold text-gray-800 mb-2">
                    <?= html_escape($draft['name']) ?>
                </h3>
                
                <div class="flex items-center justify-between mb-4">
                    <span class="bg-gray-100 text-gray-600 px-3 py-1 rounded-full text-sm">
                        <?= $draft['template_type'] === 'paid' ? 'Premium' : 'Free' ?>
                    </span>
                    <?php if($draft['template_type'] === 'paid'): ?>
                    <span class="text-sm font-medium text-green-600">
                        ₹<?= number_format($draft['price'], 2) ?>
                    </span>
                    <?php endif; ?>
                </div>

                <div class="flex justify-between space-x-2">
                    <a href="<?= site_url("resume/{$draft['template_id']}/edit") ?>" 
                       class="flex-1 bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 text-center">
                        Continue Editing
                    </a>
                    <?php if($draft['template_type'] === 'paid'): ?>
                    <a href="<?= site_url("career-plans/{$draft['template_id']}") ?>" 
                       class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700">
                        Purchase Now
                    </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <?php if(empty($drafts)): ?>
    <div class="text-center py-12">
        <div class="mb-4 text-6xl text-gray-300">
            <i class="fas fa-file-alt"></i>
        </div>
        <h3 class="text-xl text-gray-500 mb-2">No draft resumes found</h3>
        <a href="<?= site_url('app/choose-template') ?>" 
           class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
            Create New Resume
        </a>
    </div>
    <?php endif; ?>
</div>