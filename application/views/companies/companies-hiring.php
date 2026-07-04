<section class="bg-gradient-to-b from-gray-50 to-white pt-16 pb-12 px-4 sm:px-6 lg:px-8">

  <!-- Header -->
  <div class="max-w-7xl mx-auto mb-10">
    <div class="bg-purple-100 rounded-2xl p-10 text-center shadow-md hover:shadow-xl transition-all duration-300">
      <h2 class="text-3xl md:text-4xl font-extrabold text-purple-800 font-['Poppins'] tracking-tight animate-fade-in-down">
        Top Companies Hiring Now
      </h2>
    </div>
  </div>

  <!-- Ads (center aligned) -->
  <div class="mb-8 text-center">
    <?= $this->load->view('common/header_ads_tj', '', TRUE) ?>
  </div>

  <!-- Filter Form -->
  <div class="max-w-7xl mx-auto mb-10">
    <form id="search-filters" class="bg-white rounded-2xl shadow-md p-8 space-y-6">
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        
        <!-- Company Search -->
        <div>
          <label for="company-search" class="block text-sm font-medium text-gray-700 mb-2">Company Name</label>
          <input type="text" id="company-search" name="company" placeholder="Search companies..."
            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
        </div>

        <!-- Industry Filter -->
        <div>
          <label for="industry-filter" class="block text-sm font-medium text-gray-700 mb-2">Industry</label>
          <select id="industry-filter" name="industry"
            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
            <option value="">All Industries</option>
            <!-- Options via JS -->
          </select>
        </div>

        <!-- Location Filter -->
        <div>
          <label for="location-filter-jobs" class="block text-sm font-medium text-gray-700 mb-2">Location</label>
          <div class="relative">
            <input type="text" id="location-filter-jobs" name="location" placeholder="Enter location..."
              data-url="<?= base_url('website/companies/CompaniesController/search_cities') ?>"
              class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
              autocomplete="off">
            <button type="button" id="clear-city-jobs"
              class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 hidden">×</button>
          </div>
          <input type="hidden" id="city_id_jobs" name="city_id" data-filter="city_id" value="">
          <ul id="city_list_jobs"
            class="absolute z-50 w-full bg-white shadow-lg rounded-lg mt-1 max-h-60 overflow-y-auto border border-gray-200 hidden">
          </ul>
        </div>
      </div>

      <!-- Buttons -->
      <div class="flex flex-col sm:flex-row justify-end gap-4 pt-2">
        <button type="button" id="clear-filters"
          class="w-full sm:w-auto px-6 py-2.5 border border-gray-300 rounded-lg text-gray-700 bg-white hover:bg-gray-50 shadow-sm">
          Clear Filters
        </button>
        <button type="submit"
          class="w-full sm:w-auto px-6 py-2.5 bg-purple-600 text-white rounded-lg shadow-md hover:bg-purple-700 transition-all">
          Search Companies
        </button>
      </div>
    </form>
  </div>

  <!-- Companies Grid -->
  <div class="max-w-7xl mx-auto">
    <div id="company-container" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 transition-opacity duration-300">
      <!-- Dynamic content will be loaded here -->
    </div>

    <!-- Pagination -->
    <div id="pagination-container" class="mt-10 flex justify-center animate-fade-in-up">
      <!-- Pagination links will be injected here -->
    </div>
  </div>

</section>


<script>

document.addEventListener('DOMContentLoaded', function() {
  const companyContainer = document.getElementById('company-container');
  const paginationContainer = document.getElementById('pagination-container');
  let currentPage = 1;
 
  
 // Skeleton loader HTML generator
  function generateSkeletonItems(count) {
    let html = '';
    for (let i = 0; i < count; i++) {
      html += `
      <div class="company-list-item bg-white rounded-xl shadow-md p-6 animate-pulse">
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
  
  // Initial load
  loadCompanies(currentPage);

  // Pagination handler
  paginationContainer.addEventListener('click', function(e) {
    e.preventDefault();
    if (e.target.tagName === 'A') {
      const page = new URL(e.target.href).searchParams.get('page');
      currentPage = parseInt(page);
      loadCompanies(currentPage);
    }
  });

  function updateActivePage() {
    document.querySelectorAll('.pagination a').forEach(link => {
      link.classList.remove('bg-purple-600', 'text-white');
      if (link.textContent == currentPage) {
        link.classList.add('bg-purple-600', 'text-white');
      }
    });
  }

   // Modified loadCompanies function
	// Modified loadCompanies function with location ID fix
	async function loadCompanies(page) {
	  try {
		// Show loading state
		paginationContainer.classList.add('opacity-50', 'pointer-events-none');
		companyContainer.innerHTML = generateSkeletonItems(6);

		const company = document.getElementById('company-search').value;
		const industry = document.getElementById('industry-filter').value;
		const locationId = document.getElementById('location-filter-jobs').value; // Changed to use hidden field

		const url = new URL(`<?= base_url('website/companies/CompaniesController/fetch_data') ?>`);
		url.searchParams.append('page', page);
		if (company) url.searchParams.append('company', company);
		if (industry) url.searchParams.append('industry', industry);
		if (locationId) url.searchParams.append('location', locationId); // Now using the city ID

		const response = await fetch(url);
		const data = await response.json();

		companyContainer.innerHTML = data.company_data;
		paginationContainer.innerHTML = data.pagination_link;

		paginationContainer.classList.remove('opacity-50', 'pointer-events-none');
		
		document.querySelectorAll('.company-list-item').forEach((item, index) => {
		  item.style.animation = `fadeInUp 0.5s ease ${index * 0.1}s forwards`;
		});
		
		updateActivePage();
		
	  } catch (error) {
		console.error('Error loading companies:', error);
		companyContainer.innerHTML = `
		  <div class="col-span-full text-center py-12">
			<div class="text-2xl font-bold text-red-500 mb-2">
			  <i class="fas fa-exclamation-circle mr-2"></i>
			  Failed to load companies
			</div>
			<p class="text-gray-600">Please try again later</p>
		  </div>`;
		paginationContainer.classList.remove('opacity-50', 'pointer-events-none');
	  }
	}
		
	// Populate Industry Filter
	fetch(`<?= base_url('website/companies/CompaniesController/get_industries') ?>`)
	  .then(response => response.json())
	  .then(data => {
		const industrySelect = document.getElementById('industry-filter');
		data.forEach(industry => {
		  const option = document.createElement('option');
		  option.value = industry.industry_id;
		  option.textContent = industry.industry_name;
		  industrySelect.appendChild(option);
		});
	 });



	 // Form Submission Handler
	document.getElementById('search-filters').addEventListener('submit', function(e) {
	  e.preventDefault();
	  currentPage = 1;
	  loadCompanies(currentPage);
	});
	
	// Clear Filters Handler
	// Updated clear filters handler
	document.getElementById('clear-filters').addEventListener('click', function() {
	  document.getElementById('company-search').value = '';
	  document.getElementById('industry-filter').value = '';
	  document.getElementById('location-filter-jobs').value = '';
	  document.getElementById('city_id_jobs').value = ''; // Clear hidden field
	  currentPage = 1;
	  loadCompanies(currentPage);
	});

});

// Custom animations
const style = document.createElement('style');
style.textContent = `
  @keyframes fadeInUp {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
  }
  
  .company-list-item {
    opacity: 0;
  }
  
  .animate-fade-in-down {
    animation: fadeInDown 0.6s ease-out;
  }
`;
document.head.appendChild(style);
</script>

<style>
.company-list-item {
  @apply bg-white rounded-xl shadow-md hover:shadow-lg transition-all duration-300 p-6 flex items-start gap-4;
}

.company-logo {
  @apply w-20 h-20 object-cover rounded-lg flex-shrink-0 border-2 border-purple-100;
}

.company-name a {
  @apply text-lg font-semibold truncate hover:text-purple-600 transition-colors;
}

.review-rating {
  @apply flex items-center gap-2 text-sm font-medium text-gray-600;
}

.company-type {
  @apply inline-block px-3 py-1 text-sm font-medium text-purple-800 bg-purple-100 rounded-full;
}

.company-industry {
  @apply text-sm text-gray-600 inline-block mr-2 last:mr-0;
}

.pagination {
  @apply inline-flex space-x-2;
}

.pagination a {
  @apply px-4 py-2 rounded-md transition-all duration-200 hover:bg-purple-100;
}

.membership-tag {
  @apply inline-block ml-2 text-purple-600;
}
</style>




						<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-153460368-1"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'UA-153460368-1');
</script>
