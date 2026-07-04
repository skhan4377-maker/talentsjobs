<div class="bg-white rounded-2xl shadow-sm p-6 md:p-8 mb-8">
  
    <!-- Filters Section -->
    <div class="mb-8 bg-gradient-to-r from-blue-50 to-indigo-50 p-5 md:p-6 rounded-2xl shadow-sm border border-blue-100">
        <div class="flex flex-col md:flex-row gap-4 md:gap-6">
            <!-- Industry Filter -->
            <div class="flex-1">
                <label for="industryFilter" class="block text-sm font-medium text-gray-700 mb-2">Filter by Industry</label>
                <select id="industryFilter" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white text-sm md:text-base">
                    <option value="">All Industries</option>
                    <?php foreach($industries as $industry): ?>
                        <option value="<?= $industry['industry_id'] ?>"><?= htmlspecialchars($industry['industry_name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <!-- Search Filter -->
            <div class="flex-1">
                <label for="searchInput" class="block text-sm font-medium text-gray-700 mb-2">Search Positions</label>
                <div class="relative">
                    <input type="text" id="searchInput" placeholder="Search by job title, company, or location..."
                        class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm md:text-base">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>
            
            <!-- Reset Filters Button -->
            <div class="md:flex items-end">
                <button id="resetFilters" class="w-full md:w-auto px-5 py-3 border border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 transition-colors text-sm font-medium">
                    Reset Filters
                </button>
            </div>
        </div>
    </div>

    <!-- Results Counter and Sort Options -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
        <div class="text-gray-600 text-sm">
            Showing <span id="resultsCount" class="font-medium text-gray-900">-</span> saved opportunities
        </div>
        <div class="flex items-center space-x-3">
            <span class="text-sm text-gray-600">Sort by:</span>
            <select id="sortOptions" class="text-sm border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-1 focus:ring-blue-500">
                <option value="newest">Most Recent</option>
                <option value="oldest">Oldest First</option>
                <option value="salary_high">Salary: High to Low</option>
                <option value="salary_low">Salary: Low to High</option>
            </select>
        </div>
    </div>

    <!-- Job List Container -->
    <div class="job-list-container"></div>

    <!-- Pagination (top and bottom) -->
    <div id="paginationContainer" class="my-6 md:my-8"></div>
    <div id="paginationContainerBottom" class="my-6 md:my-8"></div>
</div>

<script>
const jobListContainer = $('.job-list-container');
const paginationContainer = $('#paginationContainer');
let currentPage = 1;
let debounceTimer = null;
const DEBOUNCE_DELAY = 500;

/* ---------------- Fetch Data Using GET ---------------- */
function fetchData(page) {
    return new Promise(function(resolve, reject) {

        let params = {};

        const searchValue = $('#searchInput').val().trim();
        const industry = $('#industryFilter').val();
        const sort = $('#sortOptions').val();

        // Apply filters
        if (searchValue !== '') params.search = searchValue;
        if (industry !== '') params.industry = industry;
        if (sort !== '') params.sort = sort;

        params.page = page;

        const queryString = $.param(params);

        // Update URL without reload (optional)
        window.history.replaceState({}, '', '?' + queryString);

        $.ajax({
            url: "<?= base_url('candidate/Favourite/myFavouriteJobs') ?>?" + queryString,
            type: "GET",
            dataType: "json",

            success: function(data) {
                jobListContainer.html(data.job_html || '');
                paginationContainer.html(data.pagination_link || '');
                $('#paginationContainerBottom').html(data.pagination_link || '');

                // Update CSRF token from response
                if (data.csrf_token) {
                    updateCSRFToken(data.csrf_token, getCSRFName());
                }

                resolve();
            },

            error: function(jqXHR, textStatus, errorThrown) {
                reject('Error: ' + textStatus + ', ' + errorThrown);
            }
        });
    });
}

function filter_data(page) {
    fetchData(page).catch(console.error);
}

/* ---------------- Initial Load ---------------- */
filter_data(currentPage);

/* ---------------- Search (Debounce) ---------------- */
$('#searchInput').on('keyup', function () {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(() => {
        currentPage = 1;
        filter_data(currentPage);
    }, DEBOUNCE_DELAY);
});

/* ---------------- Industry Filter ---------------- */
$('#industryFilter').on('change', function() {
    currentPage = 1;
    filter_data(currentPage);
});

/* ---------------- Sort Filter ---------------- */
$('#sortOptions').on('change', function() {
    currentPage = 1;
    filter_data(currentPage);
});

/* ---------------- Reset Filters ---------------- */
$('#resetFilters').click(function () {
    $('#industryFilter').val('');
    $('#searchInput').val('');
    $('#sortOptions').val('newest');

    currentPage = 1;
    filter_data(currentPage);
});

/* ---------------- Pagination Click ---------------- */
$(document).on("click", "#paginationContainer a, #paginationContainerBottom a", function(event) {
    event.preventDefault();
    
    const href = $(this).attr("href") || '';
    const match = href.match(/[?&]page=(\d+)/);
    currentPage = match ? parseInt(match[1], 10) : 1;

    filter_data(currentPage);
});

/* ---------------- Toggle Favourite ---------------- */
$(document).on('click', '.like-icon', function(event){
    event.preventDefault();

    const $icon = $(this);
    const jobId = $icon.data('job-id');

    $.ajax({
        url: "<?= base_url('candidate/Applied/toggleFavoriteStatus') ?>",
        type: "POST",
        dataType: "json",
        data: {
            pid: jobId,
            [getCSRFName()]: getCSRFToken()
        },

        success: function(data) {
            // Update CSRF token
            if (data.csrf_token) {
                updateCSRFToken(data.csrf_token, getCSRFName());
            }

            // Reload the list to reflect new favourite state
            filter_data(currentPage);
        },

        error: function(xhr, status, error) {
            console.error('Error toggling favorite:', error);
        }
    });
});
</script>