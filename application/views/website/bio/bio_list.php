<div class="bg-gradient-to-br from-indigo-50 via-white to-purple-50 min-h-screen pt-24 pb-8">
    <div class="container mx-auto px-4 max-w-4xl">

        <!-- Heading -->
        <div class="text-center mb-8">
            <h1 class="text-4xl md:text-5xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-indigo-700 to-purple-700">
                <?= htmlspecialchars($title ?? 'Bio Directory') ?>
            </h1>
            <p class="text-gray-600 mt-2 text-lg">
                Find all Hiring Links👇 Here
            </p>
            <div class="mt-4 h-1 w-20 bg-indigo-400 rounded-full mx-auto"></div>
        </div>

        <!-- Bio Cards -->
        <div id="bio-container" class="grid grid-cols-1 gap-5">
            <?php foreach ($bios as $bio): ?>
                <?php $this->load->view('website/bio/bio_item', ['bio' => $bio]); ?>
            <?php endforeach; ?>
        </div>

        <!-- Loader -->
        <div id="loading-indicator" class="text-center py-8 hidden">
            <div class="inline-block animate-spin rounded-full h-8 w-8 border-4 border-indigo-500 border-t-transparent"></div>
            <p class="text-gray-500 mt-2">Loading more bios...</p>
        </div>

        <!-- No More -->
        <div id="no-more-message" class="text-center py-8 text-gray-400 hidden">
            ✨ No more bios to load ✨
        </div>

    </div>
</div>

<script>
let currentPage = <?= json_encode($page ?? 2) ?>;
const perPage = <?= json_encode($per_page ?? 10) ?>;
const totalRows = <?= json_encode($total_rows ?? 0) ?>;

let hasMore = (currentPage - 1) * perPage < totalRows;
let isLoading = false;

function loadMoreBios() {
    if (!hasMore || isLoading) return;

    isLoading = true;
    document.getElementById('loading-indicator').classList.remove('hidden');

    fetch(`<?= site_url('bio-fetch-more') ?>?page=${currentPage}&per_page=${perPage}`)
        .then(res => res.json())
        .then(data => {
            if (data.html) {
                document.getElementById('bio-container')
                    .insertAdjacentHTML('beforeend', data.html);
            }

            hasMore = data.has_more;
            currentPage = data.page;

            if (!hasMore) {
                document.getElementById('no-more-message').classList.remove('hidden');
            }
        })
        .catch(err => console.error(err))
        .finally(() => {
            isLoading = false;
            document.getElementById('loading-indicator').classList.add('hidden');
        });
}

let ticking = false;
window.addEventListener('scroll', () => {
    if (!ticking) {
        window.requestAnimationFrame(() => {
            if ((window.innerHeight + window.scrollY) >= document.body.offsetHeight - 400) {
                loadMoreBios();
            }
            ticking = false;
        });
        ticking = true;
    }
});
</script>