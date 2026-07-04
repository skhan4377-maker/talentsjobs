<div class="bg-gradient-to-br from-indigo-50 via-white to-purple-50 min-h-screen pt-24 pb-10">
    <div class="container mx-auto px-4 max-w-5xl">

        <!-- Title -->
        <h1 class="text-3xl md:text-4xl font-bold text-gray-800 mb-2">
            <?= htmlspecialchars($bio->name ?? $bio->title ?? 'Untitled') ?>
        </h1>

        <!-- Slug -->
        <?php if (!empty($bio->slug)): ?>
            <p class="text-indigo-500 font-mono mb-2">
                @<?= htmlspecialchars($bio->slug) ?>
            </p>
        <?php endif; ?>

        <!-- Date -->
        <?php if (!empty($bio->created_at)): ?>
            <p class="text-sm text-gray-400 mb-6">
                <?= date('M d, Y', strtotime($bio->created_at)) ?>
            </p>
        <?php endif; ?>

        <!-- Content -->
        <div class="bg-white p-6 md:p-8 rounded-xl shadow text-gray-700 leading-relaxed text-base md:text-lg">
            <?= !empty($bio->content) ? nl2br($bio->content) : 'No content available.' ?>
        </div>

        <!-- Back -->
        <div class="mt-6">
            <a href="<?= site_url('bio') ?>" class="text-indigo-600 hover:underline">
                ← Back to list
            </a>
        </div>

    </div>
</div>