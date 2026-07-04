<div class="bg-white rounded-xl shadow border border-gray-100 p-4 mb-6">

  <!-- Back Button -->
  <div class="mb-4">
    <button id="backButton" 
            class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-lg font-medium flex items-center space-x-2">
      <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
      </svg>
      <span>Back</span>
    </button>
  </div>

  <!-- Job List Wrapper -->
  <div class="space-y-4 resultJobList"></div>

  <!-- Pagination -->
  <div id="paginationContainer" class="mt-5 flex justify-center text-sm text-gray-600"></div>

</div>
<script>
const jobListContainer = $('.resultJobList');
const paginationContainer = $('#paginationContainer');
let currentPage = 1;

// Loading spinner HTML
const loadingHTML = `<div class="flex justify-center items-center py-10">
    <i class="fas fa-spinner fa-pulse text-blue-500 text-3xl"></i>
    <span class="ml-3 text-gray-500 text-sm">Loading...</span>
</div>`;

// Get job-id from URL query string
const urlParams = new URLSearchParams(window.location.search);
const jobId = urlParams.get('job-id');

function fetchData(page) {
    let ajaxUrl = "<?= base_url('candidate/Applied/myApplyJobs') ?>?page=" + page;
    if (jobId) ajaxUrl += "&job-id=" + encodeURIComponent(jobId);
    return $.ajax({ url: ajaxUrl, method: "GET", dataType: "json" });
}

function filter_data(page) {
    // Show loading spinner
    jobListContainer.html(loadingHTML);
    paginationContainer.empty(); // clear old pagination

    fetchData(page).done(function (data) {
        jobListContainer.html(data.job_html);
        paginationContainer.html(data.pagination_link);
    }).fail(function() {
        jobListContainer.html('<div class="text-center text-red-500 py-6">Failed to load data. Please try again.</div>');
    });
}

// Initial load
filter_data(currentPage);

// Pagination click (with loading indication)
$(document).on("click", ".pagination a", function (e) {
    e.preventDefault();
    let href = $(this).attr("href");
    let page = 1;
    if (href) {
        let params = new URLSearchParams(href.split("?")[1]);
        page = params.get("page") || 1;
    }
    filter_data(page);
});

// ==================== CONTACT TOGGLE ====================
$(document).on('click', '.employer-details-toggle', function () {
    const $btn = $(this);
    const $content = $btn.next('.accordion-content');
    $content.slideToggle(200); // smooth animation
    $btn.find('.toggle-text').text($content.is(':visible') ? 'Hide Contact' : 'View Contact');
    $btn.find('.toggle-arrow').toggleClass('rotate-180');
});

// ==================== TIMELINE TOGGLE ====================
$(document).on('click', '.timeline-toggle', function() {
    const $btn = $(this);
    const $content = $btn.next('.timeline-content');
    $content.slideToggle(200);
    $btn.find('.toggle-arrow').toggleClass('rotate-180');
});

// Back button
$('#backButton').on('click', function() { window.history.back(); });
</script>
