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

<section class="bg-gray-50 py-16 px-4 sm:px-6 lg:px-8">
   <div class="mx-auto p-6">
    
	<!-- Header Section -->
    <div class="grid lg:grid-cols-2 gap-12 mb-16">
      <div class="space-y-6">
        <h2 class="text-4xl font-bold text-gray-900 tracking-tight">
          Explore Our Expert Career Services
        </h2>
        <p class="text-lg text-gray-600">
          Stand out from other jobseekers! Enhance your profile with industry-standard guidance. 
          Choose from our tailored career services to revolutionise your job search.
        </p>
        <div class="flex items-center gap-2 text-emerald-700 font-medium">
          <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
          </svg>
          <span>Trusted by <span class="text-2xl font-bold">75M</span> users across Asia & the Gulf</span>
        </div>
      </div>

      <!-- Service Categories -->	  
      <?php if (!empty($services)) : ?>
		<div class="grid grid-cols-2 gap-4">
			<?php foreach ($services as $service) : ?>
				<a href="<?php echo base_url('career-services/' . $service['service_name']); ?>" class="group p-6 bg-white rounded-xl shadow-sm hover:shadow-md transition-shadow">
					<div class="flex flex-col items-center">
						<img src="<?php echo base_url($service['service_icon']); ?>" alt="<?php echo ucfirst($service['service_name']); ?>" class="h-16 w-16 mb-4">
						<p class="text-lg font-semibold text-gray-900 group-hover:text-blue-600">
							<?php echo ucfirst($service['service_name']); ?>
						</p>
					</div>
				</a>
			<?php endforeach; ?>
		</div>
	  <?php endif; ?>	  
    </div>

    <div class="relative group" id="service-container">
        <!-- Dynamic content will be loaded here -->
    </div>
</section>
<script>
  
    function fetchService() {
        $.ajax({
            url: '<?php echo site_url("website/services/CareerServices/fetch_service"); ?>',
            method: 'GET',
            success: function(html) {
                $('#service-container').html(html);             
            },
            error: function(xhr, status, error) {
                console.error("AJAX Error: ", error);
            }
        });
    }
	
    $(document).ready(function() {       
        fetchService();
    });
</script>
