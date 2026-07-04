
<style>
.scrollbar-hide::-webkit-scrollbar {
  display: none;
}

.scrollbar-hide {
  -ms-overflow-style: none;
  scrollbar-width: none;
}

@media (max-width: 1024px) {
  .lg\:grid-cols-2 {
    grid-template-columns: 1fr;
  }
}
</style>

<section class="relative py-12 px-4 sm:px-6 lg:px-8 bg-gradient-to-b from-gray-50 to-white">
  <div class="max-w-7xl mx-auto">
    <!-- Header Section -->
    <div class="flex justify-between items-center mb-8">
	  <a href="<?php echo base_url('career-services'); ?>" class="flex items-center text-blue-600 hover:text-blue-800 transition-colors group">
		<svg class="w-5 h-5 mr-2 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
		  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
		</svg>
		<span class="font-medium">View All Services</span>
	  </a>
	</div>


    <!-- Boost Categories Section -->
    <div class="mb-12 lg:mb-16" id="category-section">
     
    </div>

    <!-- Service Cards Grid -->
    <div class="grid md:grid-cols-2 gap-6 lg:gap-8" id="service-cards-section"></div>
	
  </div>
</section>

<script>
$(document).ready(function() {
    // Get slug from the current URL
    const urlParts = window.location.pathname.split('/');
    const slug = urlParts[urlParts.length - 1]; // Assuming the slug is the last part of the URL

    // Making an AJAX request with the slug
    $.ajax({
        url: '<?= site_url('website/services/CareerServices/fetch_service_with_features/'); ?>' + slug, // Include slug in the URL
        method: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                // Append the categories HTML to a designated element
                
                  // Append the categories HTML
                $('#category-section').html(response.categories); 
                // Append the service cards HTML
                $('#service-cards-section').html(response.serviceCards); // Assuming you have a div with this ID for service cards
            
            } else {
                alert(response.message); // Show an alert if no services are found
            }
        },
        error: function(xhr, status, error) {
            console.error('AJAX Error:', error);
        }
    });
});
</script>
