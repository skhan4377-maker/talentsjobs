<!-- application/views/candidate/my_resumes.php -->

<div class="container mx-auto px-4 py-4">

    <!-- HEADER -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">My Resumes</h1>
            <p class="text-gray-500 text-sm mt-1">Manage and track all your professional resumes</p>
        </div>

        <?php if ($has_active_plan): ?>
        <a href="<?= base_url('resume-templates') ?>"
           class="inline-flex items-center gap-2 bg-gradient-to-r from-blue-600 to-indigo-600 hover:opacity-90 text-white px-5 py-3 rounded-2xl shadow-lg text-sm font-semibold transition-all duration-300">
            <i class="fas fa-plus"></i>
            Create Resume
        </a>
        <?php endif; ?>
    </div>

    <!-- STATS -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <!-- TOTAL -->
        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Total Resumes</p>
                    <h2 class="text-2xl font-bold text-gray-800 mt-2"><?= $total ?></h2>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-blue-100 text-blue-600 flex items-center justify-center">
                    <i class="fas fa-file-alt text-lg"></i>
                </div>
            </div>
        </div>

        <!-- COMPLETED -->
        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Completed</p>
                    <h2 class="text-2xl font-bold text-green-600 mt-2"><?= $completed ?></h2>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-green-100 text-green-600 flex items-center justify-center">
                    <i class="fas fa-check-circle text-lg"></i>
                </div>
            </div>
        </div>

        <!-- DRAFT -->
        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Drafts</p>
                    <h2 class="text-2xl font-bold text-yellow-500 mt-2"><?= $draft ?></h2>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-yellow-100 text-yellow-600 flex items-center justify-center">
                    <i class="fas fa-edit text-lg"></i>
                </div>
            </div>
        </div>

        <!-- DOWNLOAD -->
        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Downloads</p>
                    <h2 class="text-2xl font-bold text-indigo-600 mt-2"><?= $total_downloads ?></h2>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-indigo-100 text-indigo-600 flex items-center justify-center">
                    <i class="fas fa-download text-lg"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- RESUMES -->
    <?php if (!empty($resumes)): ?>
    <div class="space-y-6">
        <?php foreach ($resumes as $r): ?>
            <?php
                $imgSrc = $r['preview_image_url'] ?? '';
                if (!empty($imgSrc)) {
                    if (strpos($imgSrc, 'data:') === 0 || strpos($imgSrc, 'http') === 0) {
                        $imgUrl = $imgSrc;
                    } else {
                        $imgUrl = base_url($imgSrc);
                    }
                } else {
                    $imgUrl = '';
                }

                if ($r['completion'] == 100) {
                    $badge = 'bg-green-100 text-green-700';
                    $statusText = 'Completed';
                } elseif ($r['completion'] >= 50) {
                    $badge = 'bg-blue-100 text-blue-700';
                    $statusText = 'In Progress';
                } else {
                    $badge = 'bg-yellow-100 text-yellow-700';
                    $statusText = 'Draft';
                }

                $token = $this->session->userdata('login_token');
            ?>

            <!-- CARD -->
            <div class="group bg-white rounded-[28px] overflow-hidden border border-gray-100 shadow-sm hover:shadow-xl transition-all duration-300 flex flex-col lg:flex-row">

                <!-- PREVIEW (fixed width) -->
                <div class="relative bg-gradient-to-br from-gray-50 to-gray-100 p-4 w-full lg:w-[260px] lg:min-w-[260px] lg:max-w-[260px] shrink-0">
                    <!-- STATUS -->
                    <div class="absolute top-4 right-4 z-20">
                        <span class="px-3 py-1 rounded-full text-xs font-semibold <?= $badge ?>">
                            <?= $statusText ?>
                        </span>
                    </div>

                    <!-- PREVIEW BOX -->
                    <div class="relative group/preview bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden w-full h-[320px] lg:h-[360px] flex items-center justify-center">
                        <?php if (!empty($imgUrl)): ?>
                            <img src="<?= $imgUrl ?>" alt="Resume Preview" class="w-full h-full object-contain bg-white" loading="lazy">
                            <!-- HOVER OVERLAY -->
                            <div class="absolute inset-0 bg-black/40 opacity-0 group-hover/preview:opacity-100 transition-all duration-300 flex items-center justify-center">
                                <a href="<?= 'https://resume.talentsjobs.in/resume-builder/'.$r['template_id']
                                    . '?token=' . urlencode($token)
                                    . '&draft=' . $r['draft_id'] ?>"
                                   class="w-14 h-14 rounded-full bg-white text-blue-600 flex items-center justify-center shadow-2xl hover:scale-110 transition-all duration-300">
                                    <i class="fas fa-pen text-xl"></i>
                                </a>
                            </div>
                        <?php else: ?>
                            <div class="flex flex-col items-center justify-center text-gray-400">
                                <i class="fas fa-file-alt text-4xl mb-2"></i>
                                <span class="text-sm font-medium">No Preview Available</span>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- CONTENT -->
                <div class="flex-1 min-w-0 p-5 flex flex-col justify-between">
                    <div>
                        <!-- RESUME TITLE (dynamic from template name, fallback to Draft ID) -->
                        <h2 class="text-xl lg:text-2xl font-bold text-gray-800 truncate">
                            <?= !empty($r['template_name']) ? htmlspecialchars($r['template_name']) : 'Resume #' . $r['draft_id'] ?>
                        </h2>
                        <!-- CREATED DATE -->
                        <?php if (!empty($r['created_at'])): ?>
                        <p class="text-gray-500 mt-1.5 text-sm">
                            Created: <?= date('d M, Y', strtotime($r['created_at'])) ?>
                        </p>
                        <?php endif; ?>

                        <!-- COMPLETION -->
                        <div class="mt-4">
                            <div class="flex items-center justify-between mb-1.5">
                                <span class="text-sm text-gray-500">Completion</span>
                                <span class="text-sm font-bold text-blue-600"><?= $r['completion'] ?>%</span>
                            </div>
                            <div class="w-full h-2.5 bg-gray-200 rounded-full overflow-hidden">
                                <div class="h-full rounded-full bg-gradient-to-r from-blue-500 to-indigo-600"
                                     style="width: <?= $r['completion'] ?>%"></div>
                            </div>
                        </div>

                        <!-- DOWNLOADS -->
                        <div class="flex items-center gap-2 text-sm text-gray-400 mt-4">
                            <i class="fas fa-download"></i>
                            <span><?= $r['download_count'] ?> Downloads</span>
                        </div>
                    </div>

                    <!-- ACTIONS – only one Edit button, no Delete -->
                    <div class="mt-6">
                        <a href="<?= 'https://resume.talentsjobs.in/resume-builder/'.$r['template_id']
                            . '?token=' . urlencode($token)
                            . '&draft=' . $r['draft_id'] ?>"
                           class="flex items-center justify-center gap-2 bg-gradient-to-r from-blue-600 to-indigo-600 hover:opacity-90 text-white py-2.5 rounded-2xl text-sm font-semibold transition-all duration-300 w-full">
                            <i class="fas fa-pen"></i> Edit Resume
                        </a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <?php else: ?>
    <!-- EMPTY STATE -->
    <div class="bg-white rounded-[32px] border border-gray-100 shadow-sm p-10 text-center max-w-2xl mx-auto">
        <div class="w-24 h-24 mx-auto rounded-full bg-gradient-to-br from-blue-100 to-indigo-100 text-blue-600 flex items-center justify-center mb-5">
            <i class="fas fa-file-alt text-4xl"></i>
        </div>
        <h2 class="text-2xl font-bold text-gray-800 mb-3">No Resume Created Yet</h2>
        <p class="text-gray-500 max-w-md mx-auto mb-6 leading-relaxed">
            Build a professional ATS-friendly resume in minutes and apply for your dream jobs confidently.
        </p>
        <?php if ($has_active_plan): ?>
        <a href="<?= base_url('resume-templates') ?>"
           class="inline-flex items-center gap-3 bg-gradient-to-r from-blue-600 to-indigo-600 hover:opacity-90 text-white px-6 py-3.5 rounded-2xl font-semibold shadow-lg transition-all duration-300">
            <i class="fas fa-plus-circle"></i> Create Your First Resume
        </a>
        <?php endif; ?>
    </div>
    <?php endif; ?>

</div>