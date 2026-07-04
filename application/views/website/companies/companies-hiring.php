<!-- application/views/website/companies/companies-hiring-tailwind.php -->
<div class="bg-gray-50 min-h-screen pt-20 pb-8">
    <div class="container mx-auto px-4">
        <!-- Header -->
       <!-- Header - now fully responsive -->
        <div class="mb-8">
            <h1 class="text-2xl sm:text-3xl md:text-4xl font-bold text-purple-800 text-left">
                Top Companies Hiring Now
            </h1>
        </div>

        <!-- Desktop & Mobile Layout -->
        <div class="lg:grid lg:grid-cols-12 lg:gap-8">

            <!-- ========== DESKTOP FILTER SIDEBAR (sticky) ========== -->
            <div class="hidden lg:block lg:col-span-3">
                <div class="sticky top-24 z-10 bg-white rounded-xl shadow-sm p-6">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                        <i class="fas fa-filter text-purple-600"></i> Filter Companies
                    </h2>
                    <div class="space-y-4">
                        <!-- Industry Filter -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Industry</label>
                            <select id="industry-filter-desktop" class="w-full border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500 px-4 py-3">
                                <option value="">All Industries</option>
                                <?php foreach ($industries as $ind): ?>
                                    <option value="<?= $ind['industry_id'] ?>">
                                        <?= htmlspecialchars($ind['industry_name']) ?> (<?= $ind['company_count'] ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <!-- Location Filter -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Location</label>
                            <input type="text" id="location-filter-desktop" 
                                   class="w-full border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500 px-4 py-3"
                                   placeholder="Search city..." autocomplete="off">
                            <input type="hidden" id="location-id-desktop">
                            <ul id="location-suggestions-desktop" class="hidden absolute z-10 w-full bg-white shadow-lg rounded-b-lg mt-1 max-h-60 overflow-y-auto border border-gray-200"></ul>
                        </div>
                        <!-- Company Name -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Company Name</label>
                            <input type="text" id="company-search-desktop" 
                                   class="w-full border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500 px-4 py-3"
                                   placeholder="Enter company name...">
                        </div>
                        <!-- Buttons -->
                        <div class="flex gap-2 pt-2">
                            <button id="apply-filters-desktop" class="flex-1 bg-purple-600 hover:bg-purple-700 text-white font-medium py-1.5 px-3 rounded-lg transition">
                                Apply Filters
                            </button>
                            <button id="clear-filters-desktop" class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-800 font-medium py-1.5 px-3 rounded-lg transition">
                                Clear
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ========== MAIN CONTENT ========== -->
            <div class="lg:col-span-9">
                <!-- Loading Spinner -->
                <div id="loading-spinner" class="hidden flex justify-center items-center py-12">
                    <div class="animate-spin rounded-full h-12 w-12 border-4 border-purple-600 border-t-transparent"></div>
                </div>

                <!-- Companies Grid - now always 2 columns on desktop -->
                <div id="companies-grid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-6">
                    <!-- Filled via AJAX -->
                </div>

                <!-- Pagination -->
                <div id="pagination" class="mt-8"></div>
            </div>
        </div>

        <!-- ========== MOBILE FILTER DRAWER ========== -->
        <div class="lg:hidden">
            <!-- Floating filter button -->
            <button id="filter-drawer-btn" class="fixed bottom-6 right-6 z-20 bg-purple-600 text-white p-4 rounded-full shadow-lg hover:bg-purple-700 transition-all">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path>
                </svg>
            </button>

            <!-- Drawer overlay -->
            <div id="filter-drawer-overlay" class="fixed inset-0 bg-black bg-opacity-50 z-30 hidden transition-opacity"></div>

            <!-- Drawer panel -->
            <div id="filter-drawer" class="fixed bottom-0 left-0 right-0 bg-white rounded-t-2xl shadow-xl z-40 transform transition-transform duration-300 ease-in-out translate-y-full">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-xl font-semibold text-gray-800">Filter Companies</h3>
                        <button id="close-drawer" class="text-gray-500 hover:text-gray-700">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <!-- Mobile filter form -->
                    <div class="space-y-4">
                        <!-- Industry Filter -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Industry</label>
                            <select id="industry-filter-mobile" class="w-full border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500 px-4 py-3">
                                <option value="">All Industries</option>
                                <?php foreach ($industries as $ind): ?>
                                    <option value="<?= $ind['industry_id'] ?>">
                                        <?= htmlspecialchars($ind['industry_name']) ?> (<?= $ind['company_count'] ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <!-- Location Filter -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Location</label>
                            <input type="text" id="location-filter-mobile" 
                                   class="w-full border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500 px-4 py-3"
                                   placeholder="Search city..." autocomplete="off">
                            <input type="hidden" id="location-id-mobile">
                            <ul id="location-suggestions-mobile" class="hidden absolute z-10 w-full bg-white shadow-lg rounded-b-lg mt-1 max-h-60 overflow-y-auto border border-gray-200"></ul>
                        </div>
                        <!-- Company Name -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Company Name</label>
                            <input type="text" id="company-search-mobile" 
                                   class="w-full border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500 px-4 py-3"
                                   placeholder="Enter company name...">
                        </div>
                        <!-- Buttons -->
                        <div class="flex gap-2 pt-2">
                            <button id="apply-filters-mobile" class="flex-1 bg-purple-600 hover:bg-purple-700 text-white font-medium py-1.5 px-3 rounded-lg transition">
                                Apply Filters
                            </button>
                            <button id="clear-filters-mobile" class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-800 font-medium py-1.5 px-3 rounded-lg transition">
                                Clear
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // ========== Mobile drawer controls ==========
    const drawerBtn = document.getElementById('filter-drawer-btn');
    const drawerOverlay = document.getElementById('filter-drawer-overlay');
    const drawer = document.getElementById('filter-drawer');
    const closeDrawerBtn = document.getElementById('close-drawer');

    function openDrawer() {
        drawer.classList.remove('translate-y-full');
        drawerOverlay.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    }

    function closeDrawer() {
        drawer.classList.add('translate-y-full');
        drawerOverlay.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }

    if (drawerBtn) {
        drawerBtn.addEventListener('click', openDrawer);
        closeDrawerBtn.addEventListener('click', closeDrawer);
        drawerOverlay.addEventListener('click', closeDrawer);
    }

    // ========== Helper to get active filter values ==========
    function getActiveFilterValues() {
        const isDesktop = window.matchMedia('(min-width: 1024px)').matches;
        const suffix = isDesktop ? 'desktop' : 'mobile';
        return {
            industry: document.getElementById(`industry-filter-${suffix}`)?.value || '',
            location: document.getElementById(`location-id-${suffix}`)?.value || '',
            company: document.getElementById(`company-search-${suffix}`)?.value || ''
        };
    }

    // ========== Skeleton loader ==========
    function generateSkeletonItems(count = 6) {
        let html = '';
        for (let i = 0; i < count; i++) {
            html += `
            <div class="bg-white rounded-xl shadow-md p-6 animate-pulse">
                <div class="flex items-start gap-4">
                    <div class="w-20 h-20 bg-gray-200 rounded-lg border-2 border-gray-100"></div>
                    <div class="flex-1 space-y-3">
                        <div class="h-6 bg-gray-200 rounded w-3/4"></div>
                        <div class="h-4 bg-gray-200 rounded w-1/2"></div>
                        <div class="space-y-2 pt-2">
                            <div class="h-4 bg-gray-200 rounded w-full"></div>
                            <div class="h-4 bg-gray-200 rounded w-2/3"></div>
                            <div class="h-4 bg-gray-200 rounded w-1/2"></div>
                        </div>
                    </div>
                </div>
            </div>`;
        }
        return html;
    }

    // ========== AJAX load companies ==========
    const grid = document.getElementById('companies-grid');
    const paginationDiv = document.getElementById('pagination');
    const spinner = document.getElementById('loading-spinner');

    async function loadCompanies(page = 1) {
        spinner.classList.remove('hidden');
        grid.innerHTML = generateSkeletonItems(6);

        const { industry, location, company } = getActiveFilterValues();

        const params = new URLSearchParams({
            page: page,
            industry: industry,
            location: location,
            company: company
        });

        try {
            const response = await fetch('<?= base_url("website/companies/CompaniesController/fetch_data") ?>?' + params);
            const data = await response.json();

            spinner.classList.add('hidden');
            grid.innerHTML = data.company_data;

            // Add fade-in animation to each company card
            grid.querySelectorAll('.company-list-item').forEach((item, index) => {
                item.style.animation = `fadeInUp 0.5s ease ${index * 0.1}s forwards`;
            });

            paginationDiv.innerHTML = data.pagination_link;

            // Re-attach pagination click handlers
            paginationDiv.querySelectorAll('a').forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    const url = new URL(this.href);
                    const page = url.searchParams.get('page') || 1;
                    loadCompanies(page);
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                });
            });
        } catch (error) {
            spinner.classList.add('hidden');
            grid.innerHTML = '<div class="col-span-full text-center py-12 text-red-500">Error loading companies.</div>';
        }
    }

    // ========== Apply filters (both forms) ==========
    function attachFilterHandlers(suffix) {
        const applyBtn = document.getElementById(`apply-filters-${suffix}`);
        const clearBtn = document.getElementById(`clear-filters-${suffix}`);

        if (applyBtn) {
            applyBtn.addEventListener('click', () => {
                loadCompanies(1);
                if (suffix === 'mobile') closeDrawer();
            });
        }

        if (clearBtn) {
            clearBtn.addEventListener('click', () => {
                // Clear all filter fields
                document.querySelectorAll('[id^="industry-filter-"], [id^="location-filter-"], [id^="company-search-"], [id^="location-id-"]').forEach(el => {
                    if (el.tagName === 'SELECT' || el.type === 'text') el.value = '';
                    else if (el.type === 'hidden') el.value = '';
                });
                loadCompanies(1);
                if (suffix === 'mobile') closeDrawer();
            });
        }
    }

    attachFilterHandlers('desktop');
    attachFilterHandlers('mobile');

    // ========== Location autocomplete ==========
    function initAutocomplete(inputId, hiddenId, suggestionsId) {
        const input = document.getElementById(inputId);
        const hidden = document.getElementById(hiddenId);
        const suggestions = document.getElementById(suggestionsId);
        if (!input) return;

        // Your existing AutoCompleteWidget
        new AutoCompleteWidget({
            inputSelector: `#${inputId}`,
            hiddenSelector: `#${hiddenId}`,
            listSelector: `#${suggestionsId}`,
            apiUrl: '<?= base_url("website/companies/CompaniesController/search_cities") ?>',
            minChars: 2,
            multiSelect: false,
            maxResults: 5,
            onSelect: function(item) {
                input.value = item.label;
                hidden.value = item.id;
            }
        });
    }

    // Initialize desktop autocomplete
    initAutocomplete('location-filter-desktop', 'location-id-desktop', 'location-suggestions-desktop');

    // Initialize mobile autocomplete when drawer opens
    if (drawerBtn) {
        drawerBtn.addEventListener('click', () => {
            setTimeout(() => {
                initAutocomplete('location-filter-mobile', 'location-id-mobile', 'location-suggestions-mobile');
            }, 300);
        });
    }

    // ========== Initial load ==========
    loadCompanies();

    // ========== Pagination delegation ==========
    paginationDiv.addEventListener('click', function(e) {
        if (e.target.tagName === 'A') {
            e.preventDefault();
            const url = new URL(e.target.href);
            const page = url.searchParams.get('page') || 1;
            loadCompanies(page);
        }
    });
});

// ========== fadeInUp animation ==========
const style = document.createElement('style');
style.textContent = `
@keyframes fadeInUp {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}
.company-list-item {
    opacity: 0;
}
`;
document.head.appendChild(style);
</script>