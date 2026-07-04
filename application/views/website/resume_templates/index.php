<?php
// Ensure data exists
if (empty($resumeTemplates)) {
    echo '<div class="text-center py-20"><p class="text-gray-500">No templates available right now.</p></div>';
    return;
}

$baseResumeUrl = 'https://resume.talentsjobs.in/resume-builder';
$apiBase       = base_url('api/resume-templates');
?>

<section class="bg-gradient-to-b from-white to-gray-50 py-12 md:py-16 lg:py-20">
    <div class="container mx-auto px-4 sm:px-6 max-w-7xl">
        <!-- Page Header -->
        <div class="text-center mb-10 md:mb-14">
            <span class="inline-flex items-center gap-2 bg-blue-50 text-blue-700 text-sm font-semibold px-4 py-1.5 rounded-full mb-4">
                <i class="fas fa-magic text-xs"></i> All Templates
            </span>
            <h1 class="text-3xl md:text-4xl lg:text-5xl font-bold text-gray-900 mb-3">
                Professional Resume Templates
                <span class="text-blue-600">That Get You Hired</span>
            </h1>
            <p class="text-gray-600 max-w-2xl mx-auto text-sm md:text-base">
                Choose from 50+ ATS‑friendly, recruiter‑approved designs. Click any template to start building your resume instantly.
            </p>
        </div>

        <!-- Templates Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            <?php foreach ($resumeTemplates as $template):
                $templateId   = $template['template_id'];
                $isPremium    = ($template['type'] === 'premium');
                $downloadCount = number_format($template['downloads'] ?? 0);
                $previewUrl   = $template['thumbnail_url'] ?? 'https://placehold.co/400x500/e2e8f0/1e293b?text=' . urlencode($template['name']);
                $templateUrl  = $baseResumeUrl . '/' . $templateId;
                if (!empty($resumeToken)) {
                    $templateUrl .= '?token=' . urlencode($resumeToken);
                }
            ?>
            <div class="group relative bg-white rounded-2xl shadow-md hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 overflow-hidden flex flex-col h-full border border-gray-100">
                <!-- Popular badge -->
                <?php if (!empty($template['popular'])): ?>
                    <div class="absolute top-3 left-3 z-10 bg-gradient-to-r from-amber-400 to-orange-500 text-white text-xs font-bold px-2.5 py-1 rounded-full shadow-md flex items-center gap-1">
                        <i class="fas fa-fire text-xs"></i> Popular
                    </div>
                <?php endif; ?>

                <!-- Type badge -->
                <div class="absolute top-3 right-3 z-10">
                    <?php if ($isPremium): ?>
                        <span class="bg-gradient-to-r from-indigo-500 to-purple-600 text-white text-xs font-bold px-3 py-1 rounded-full shadow-md flex items-center gap-1">
                            <i class="fas fa-crown text-[10px]"></i> Premium
                        </span>
                    <?php else: ?>
                        <span class="bg-emerald-500 text-white text-xs font-bold px-3 py-1 rounded-full shadow-md flex items-center gap-1">
                            <i class="fas fa-gem text-[10px]"></i> Free
                        </span>
                    <?php endif; ?>
                </div>

                <!-- Thumbnail -->
                <div class="relative overflow-hidden bg-gray-100 aspect-[4/5]">
                    <img src="<?= htmlspecialchars($previewUrl) ?>"
                         alt="<?= htmlspecialchars($template['name']) ?> Resume Template"
                         class="w-full h-full object-cover object-top transition-transform duration-500 group-hover:scale-105"
                         loading="lazy">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-end justify-center pb-4">
                        <a href="<?= $templateUrl ?>"
                           target="_blank"
                           class="track-download bg-white text-gray-900 hover:bg-blue-50 font-medium px-4 py-2 rounded-lg shadow-lg transform translate-y-2 group-hover:translate-y-0 transition-all duration-300 flex items-center gap-2 text-sm"
                           data-template-id="<?= $templateId ?>">
                            <i class="fas fa-pen-fancy"></i> Use Template
                        </a>
                    </div>
                </div>

                <!-- Card body -->
                <div class="p-4 flex flex-col flex-grow">
                    <div class="flex justify-between items-start mb-2">
                        <h3 class="font-bold text-gray-800 text-base truncate pr-2"><?= htmlspecialchars($template['name']) ?></h3>
                        <?php if (!$isPremium): ?>
                            <span class="inline-flex items-center gap-1 bg-green-50 text-green-700 text-[10px] font-semibold px-2 py-0.5 rounded-full">
                                <i class="fas fa-check-circle text-[8px]"></i> No CC Required
                            </span>
                        <?php endif; ?>
                    </div>
                    <div class="flex items-center gap-3 text-xs text-gray-500 mb-3">
                        <div class="flex items-center gap-1"><i class="fas fa-download text-gray-400"></i> <?= $downloadCount ?> downloads</div>
                        <div class="flex items-center gap-1"><i class="fas fa-clock text-gray-400"></i> 5‑min setup</div>
                    </div>
                    <div class="flex flex-wrap gap-1.5 mb-4">
                        <span class="text-[10px] bg-gray-100 text-gray-600 px-2 py-0.5 rounded-full">ATS Friendly</span>
                        <span class="text-[10px] bg-gray-100 text-gray-600 px-2 py-0.5 rounded-full">PDF Export</span>
                        <span class="text-[10px] bg-gray-100 text-gray-600 px-2 py-0.5 rounded-full">HR Approved</span>
                    </div>
                    <a href="<?= $templateUrl ?>"
                       target="_blank"
                       class="track-download mt-auto inline-flex items-center justify-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold py-2.5 px-4 rounded-xl transition-all duration-200 shadow-sm hover:shadow group/btn"
                       data-template-id="<?= $templateId ?>">
                        <span>Build Resume Now</span>
                        <i class="fas fa-arrow-right text-xs transition-transform group-hover/btn:translate-x-1"></i>
                    </a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Bottom Trust Banner -->
        <div class="mt-12 md:mt-16 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-2xl p-6 md:p-8 flex flex-col md:flex-row items-center justify-between gap-5 shadow-sm border border-blue-100">
            <div class="flex items-center gap-4">
                <div class="bg-white p-3 rounded-full shadow-md"><i class="fas fa-file-pdf text-2xl text-red-500"></i></div>
                <div>
                    <h4 class="font-bold text-gray-800 text-lg">Professional PDF Downloads</h4>
                    <p class="text-gray-600 text-sm">One‑click download, print‑ready & recruiter‑friendly format</p>
                </div>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <div class="flex -space-x-2">
                    <img src="https://randomuser.me/api/portraits/women/68.jpg" class="w-8 h-8 rounded-full border-2 border-white" alt="User">
                    <img src="https://randomuser.me/api/portraits/men/32.jpg" class="w-8 h-8 rounded-full border-2 border-white" alt="User">
                    <img src="https://randomuser.me/api/portraits/women/45.jpg" class="w-8 h-8 rounded-full border-2 border-white" alt="User">
                    <div class="w-8 h-8 rounded-full bg-blue-200 text-blue-800 flex items-center justify-center text-xs font-bold border-2 border-white">1k+</div>
                </div>
                <span class="text-sm text-gray-600">Trusted by 10,000+ job seekers</span>
            </div>
        </div>
    </div>
</section>
