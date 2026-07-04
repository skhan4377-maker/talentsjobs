
  <!-- Include Alpine.js for handling the mobile toggle 
 <script src="https://unpkg.com/alpinejs@3.12.0/dist/cdn.min.js"></script>-->

  <style>
    @keyframes marquee {
      0% { transform: translateX(100%); }
      100% { transform: translateX(-100%); }
    }
    .animate-marquee {
      animation: marquee 20s linear infinite;
    }
  </style>

  <section class="bg-gray-50 py-8">  
    <!-- Loading Spinner -->
    <div id="loadingSpinner" class="hidden fixed inset-0 bg-black bg-opacity-30 z-50">
      <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2">
        <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-blue-500"></div>
      </div>
    </div>
	
    <div class="container mx-auto px-4 max-w-6xl">	
		<!-- Header Section -->
		<div class="flex justify-between items-center mb-8">
		  <a href="" class="flex items-center text-blue-600 hover:text-blue-800 transition-colors group">
			<svg class="w-5 h-5 mr-2 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
			  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
			</svg>
			<span class="font-medium">View All Services</span>
		  </a>
		</div>
	
      <!-- Responsive Grid with re-ordered content for mobile -->
      <div class="md:grid md:grid-cols-3 md:gap-8">
       
	   <!-- Pricing Section: appears first on mobile, right column on desktop -->
        <div class="order-1 md:order-2 md:col-span-1" id="plan-durations-container"></div>

        <!-- Main Content: appears second on mobile, left columns on desktop -->
        <div class="order-2 md:order-1 md:col-span-2">
          <!-- Header Section -->
          <div class="mb-8">
            <h1 class="text-3xl font-bold mb-2 flex items-center gap-2">
              <?=@$feature['feature_name']?>
              <span class="bg-orange-500 text-white text-sm px-3 py-1 rounded-full"><?=ucfirst(@$feature['feature_tag'])?></span>
            </h1>
            <p class="text-gray-600 mb-4">
             <?=@$feature['feature_short_description']?>
            </p>
            <div class="text-sm text-green-600 font-medium">150+ bought recently</div>
          </div>

          <!-- Image Section -->
          <div class="mb-8">
            <img src="<?=base_url(@$feature['feature_video_gif'])?>" alt="Featured Profile" class="mx-auto max-w-full h-auto" />
          </div>

          <!-- Features Grid -->
          <div class="grid md:grid-cols-3 gap-4 mb-8">
			   <?php if (!empty($profile_features)): ?>
			   <?php foreach ($profile_features as $feature_profile): ?>
				<div class="p-6 bg-white rounded-lg shadow-sm">
				  <h3 class="font-bold mb-2"><?php echo htmlspecialchars($feature_profile['tag_title']); // Profile Boost or similar field ?></h3>
				  <p class="text-gray-600 text-sm"><?php echo htmlspecialchars($feature_profile['tag_description']); // Highlight in Search ?></p>
				</div>
				<?php endforeach; ?>
				<?php else: ?>
				   <p>No features available.</p>
				<?php endif; ?>			
          </div>

          <!-- Company Logos Marquee -->
          <div class="mb-8">
            <h4 class="text-gray-700 font-medium mb-4">Top Companies hiring with us</h4>
            <div class="overflow-hidden whitespace-nowrap">
              <div class="animate-marquee inline-block">
                <img src="https://media.foundit.in/trex/public/default/images/career-services/pdp/companies/infosys.png" class="h-12 inline-block mx-8" alt="Company" />
                <img src="https://media.foundit.in/trex/public/default/images/career-services/pdp/companies/xoriant.png" class="h-12 inline-block mx-8" alt="Company" />
                <img src="https://media.foundit.in/trex/public/default/images/career-services/pdp/companies/tech-mahindra.png" class="h-12 inline-block mx-8" alt="Company" />
                <img src="https://media.foundit.in/trex/public/default/images/career-services/pdp/companies/genpact.png" class="h-12 inline-block mx-8" alt="Company" />
              </div>
            </div>
          </div>
			
		<div class="bg-white rounded-lg shadow-sm overflow-hidden mb-8">
		  <div class="px-6 py-4">
			<h4 class="text-xl font-bold text-gray-900"><?= htmlspecialchars(@$feature['benefit_heading']) ?></h4>
		  </div>
		  <div class="p-6 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
			<!-- Span all columns on desktop -->
			<div class="flex items-center justify-center col-span-1 sm:col-span-2 md:col-span-3">
			  <img src="<?= base_url(@$feature['benefit_logo']) ?>" alt="Benefit Logo" class="w-full h-auto object-contain">
			</div>
			<!-- Add additional benefit cards as needed -->
		  </div>
		</div>
			
		<div class="bg-white rounded-lg shadow-sm overflow-hidden mb-8">
		  <div class="px-6 py-4">
			<?php
			$heading_title = '';

			// Build "Col1 vs Col2" for each header
			if (!empty($advantage_headings_columns)) {
				$vs_labels = [];
				foreach ($advantage_headings_columns as $col) {
					$col1 = $col['col_1_label'] ?? '';
					$col2 = $col['col_2_label'] ?? '';
					if ($col1 && $col2) {
						$vs_labels[] = "$col1 vs $col2";
					}
				}
				$heading_title = !empty($vs_labels) ? implode(', ', $vs_labels) : '';
			}
			?>

			<?php if ($heading_title): ?>
				<h2 class="text-xl font-bold"><?= htmlspecialchars($heading_title) ?></h2>
			<?php endif; ?>

		  </div>
			<table class="w-full">
				<thead class="bg-gray-50">
					<tr>
						<?php foreach ($advantage_headings_columns as $col): ?>
							<th class="px-6 py-4 text-left"><?= htmlspecialchars($col['title_label']) ?></th>
							<th class="px-6 py-4 text-left"><?= htmlspecialchars($col['col_1_label']) ?></th>
							<th class="px-6 py-4 text-left"><?= htmlspecialchars($col['col_2_label']) ?></th>
						<?php endforeach; ?>
					</tr>
				</thead>
				<tbody class="divide-y">
					<?php if (!empty($advantage_headings)): ?>
						<?php foreach ($advantage_headings as $adv): ?>
							<tr>
								<td class="px-6 py-4"><?= htmlspecialchars($adv['benefit_title'] ?? '') ?></td>
								<td class="px-6 py-4"><?= htmlspecialchars($adv['col_1'] ?? '') ?></td>
								<td class="px-6 py-4"><?= htmlspecialchars($adv['col_2'] ?? '') ?></td>
							</tr>
						<?php endforeach; ?>
					<?php else: ?>
						<tr>
							<td colspan="2" class="px-6 py-4">No features available.</td>
						</tr>
					<?php endif; ?>
				</tbody>
			</table>
		</div>


          <!-- FAQ Section -->
         <div class="mb-8">
		  <h2 class="text-2xl font-bold mb-4">Frequently Asked Questions</h2>
		  <div class="space-y-4">
			<?php if (!empty($faqs)): ?>
			  <?php foreach ($faqs as $faq): ?>
				<div class="border rounded-lg">
				  <div class="p-4 flex justify-between items-center cursor-pointer" onclick="toggleFAQ(this)">
					<span><?= htmlspecialchars($faq['question']) ?></span>
					<span class="text-xl faq-toggle">+</span>
				  </div>
				  <div class="hidden p-4 pt-0">
					<p><?= htmlspecialchars($faq['answer']) ?></p>
				  </div>
				</div>
			  <?php endforeach; ?>
			<?php else: ?>
			  <p>No FAQs available for this feature.</p>
			<?php endif; ?>
		  </div>
		</div>

        </div>
      
	  </div>
    </div>
  </section>

   
  
 <script>
   // Toggle FAQ answer visibility using the clicked element's next sibling
    function toggleFAQ(element) {
      const answer = element.nextElementSibling;
      answer.classList.toggle('hidden');
      const toggleIndicator = element.querySelector('.faq-toggle');
      if (toggleIndicator) {
        toggleIndicator.textContent = answer.classList.contains('hidden') ? '+' : '-';
      }
    }
	 // Toast notification function
    function showToast(message, type = 'info') {
      const toast = document.createElement('div');
      toast.className = `fixed bottom-4 right-4 z-[9999] px-6 py-3 rounded-lg text-white shadow-lg transition-transform transform translate-x-0 ${
        type === 'success' ? 'bg-green-500' : 
        type === 'error' ? 'bg-red-500' : 'bg-blue-500'
      }`;
      toast.textContent = message;
      document.body.appendChild(toast);

      setTimeout(() => {
        toast.style.transform = 'translateX(150%)';
        setTimeout(() => toast.remove(), 300);
      }, 3000);
    }  

	
  function fetchPlanDurationsWithParams(featureSlug, expIndex, planIndex) {
    // Build URL with query parameters
    let url = '<?= base_url('website/services/CareerServices/get_plan_durations'); ?>/' + featureSlug;
    let params = [];
    if (expIndex !== null && expIndex !== undefined) {
      params.push('exp=' + expIndex);
    }
    if (planIndex !== null && planIndex !== undefined) {
      params.push('plan=' + planIndex);
    }
    if (params.length) {
      url += '?' + params.join('&');
    }
    
    $.ajax({
      url: url,
      type: 'GET',
      dataType: 'json',
      beforeSend: function() {
        $('#loadingSpinner').show();
      },
      success: function(response) {
        if (response.error) {
          $('#plan-durations-container').html('<div>' + response.error + '</div>');
        } else {
          $('#plan-durations-container').html(response.html);
        }
      },
      error: function() {
        $('#plan-durations-container').html('<div>Error loading plan durations.</div>');
      },
      complete: function() {
        $('#loadingSpinner').hide();
      }
    });
  }

  function handleAddToCart(featureId, durationId, redirectAfter = false) {
		$.ajax({
			url: '<?= base_url('website/services/CartController/add_to_cart'); ?>',
			type: 'POST',
			dataType: 'json',
			data: {
				feature_id: featureId,
				duration_id: durationId
			},
			success: function(response) {
				if (response.status === 'success') {
					updateCartCount();
					addCartUI();
					showToast(response.message, 'success');

					if (redirectAfter) {
						setTimeout(() => {
							window.location.href = '<?= base_url('cart'); ?>';
						}, 1200); // slight delay to allow user to see the toast
					}
				} else {
					showToast(response.message, 'error');

					if (redirectAfter) {
						setTimeout(() => {
							window.location.href = '<?= base_url('cart'); ?>';
						}, 1200);
					}
				}
			},
			error: function() {
				showToast('Error adding the plan to the cart.', 'error');
			}
		});
	}

	
	// Add Cart Toggle Button to the DOM with the new design
	function addCartUI() {
		const cartToggle = `
			<div class="fixed right-4 md:right-6 top-4 md:top-20 z-[1000] cart-toggle" style="display: none;"> <!-- Initially hidden -->
				<div class="relative w-14 h-14 md:w-20 md:h-20 bg-green-600 flex items-center justify-center rounded-md md:rounded-lg shadow-md hover:shadow-lg transition-all duration-200">
					<a href="<?= base_url('cart'); ?>" class="flex items-center justify-center w-full h-full">
						<!-- Cart Icon -->
						<svg class="w-7 h-7 md:w-10 md:h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
								  d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
						</svg>
						
						<!-- Count Badge -->
						<span class="absolute -top-1 -right-1 md:-top-2 md:-right-2 bg-red-500 text-white rounded-full w-5 h-5 md:w-6 md:h-6 
								  flex items-center justify-center text-xs md:text-sm font-bold ring-2 ring-white service-cart-count">
							0
						</span>
					</a>
				</div>
			</div>
		`;

		// Append the new cart toggle button to the body
		$('body').append(cartToggle);
	}

	// Function to update cart count in the cart toggle
	function updateCartCount() {
		$.ajax({
			url: '<?= base_url('website/services/CartController/get_cart_count'); ?>',
			type: 'GET',
			dataType: 'json',  
			success: function(response) {
				const count = response.count;
				
				if (count > 0) {
					// Show the cart toggle when there's something in the cart
					$('.cart-toggle').show();
					$('.service-cart-count').text(count);
				} else {
					// Hide the cart toggle when the cart is empty
					$('.cart-toggle').hide();
				}
			},
			error: function(xhr, status, error) {
				console.error('Failed to fetch cart count:', error);
			}
		});
	}

	
  // On Document Ready
  $(document).ready(function () {  
	  
		addCartUI();
		updateCartCount();
		
		var currentURL = window.location.href;
		var urlParts = currentURL.split('/');
		var featureSlug = urlParts[urlParts.length - 1];
		// Initial load without extra params (defaults will be applied)
		fetchPlanDurationsWithParams(featureSlug);
		
		// When an experience-level button is clicked
	  $(document).on('click', '.exp-toggle', function() {
		// Get selected experience index from data attribute
		var expIndex = $(this).data('exp-index');
		// When experience changes, we can default the plan index to 0 (or keep previous if desired)
		var planIndex = 0;
		// Get featureSlug from URL or a global variable
		var currentURL = window.location.href;
		var urlParts = currentURL.split('/');
		var featureSlug = urlParts[urlParts.length - 1];

		// Re-fetch with the selected experience level
		fetchPlanDurationsWithParams(featureSlug, expIndex, planIndex);
	  });

	  // When a plan-duration button is clicked
	  $(document).on('click', '.plan-toggle', function() {
		var planIndex = $(this).data('plan-index');
		// For plan click, keep the current experience selection.
		// You could store the current expIndex in a global variable if needed.
		// For example, suppose you have a hidden field or global variable named currentExpIndex:
		var expIndex = $('.exp-toggle.border-b-2').data('exp-index'); // assumes active button has border classes

		var currentURL = window.location.href;
		var urlParts = currentURL.split('/');
		var featureSlug = urlParts[urlParts.length - 1];

		fetchPlanDurationsWithParams(featureSlug, expIndex, planIndex);
	  });

	  // Price Breakup toggle remains the same
	  $(document).on('click', '#viewPriceBreakupButton', function() {
		$('#priceBreakup').toggleClass('hidden');
	  });
	  
		$(document).on('click', '.add-to-cart-btn', function () {
			const featureId = $(this).data('feature-id');
			const durationId = $(this).data('duration-id');
			handleAddToCart(featureId, durationId, false); // No redirect
		});
		
		
		$(document).on('click', '.buy-now-btn', function () {
			const featureId = $(this).data('feature-id');
			const durationId = $(this).data('duration-id');
			handleAddToCart(featureId, durationId, true); // Redirect after add
		});
  });
  
</script>

  <script>
    document.addEventListener("DOMContentLoaded", function () {
      // Experience buttons toggle functionality
      const expButtons = document.querySelectorAll('.exp-toggle');
  
      expButtons.forEach(button => {
        button.addEventListener('click', function () {
          // Remove active styling from all buttons and apply inactive bottom border
          expButtons.forEach(btn => {
            btn.classList.remove('border-blue-500', 'font-medium');
            // Ensure each button has a bottom border; inactive state: border transparent & text-gray-500
            btn.classList.add('border-b-2', 'border-transparent', 'text-gray-500');
          });
          // Active state for clicked button: remove transparent border and gray text, add blue border & font-medium
          this.classList.remove('border-transparent', 'text-gray-500');
          this.classList.add('border-blue-500', 'font-medium');
  
          // Update pricing data based on experience selection
          if (this.id === 'exp8plus') {
            // Data for 8+ years of experience (example values)
            document.getElementById('strikePrice').innerText = '₹6,000';
            document.getElementById('activePrice').innerText = '₹5,800';
            document.getElementById('monthlyCost').innerText = '₹966/month';
          } else {
            // Data for 0-8 years of experience (default values)
            document.getElementById('strikePrice').innerText = '₹5,498';
            document.getElementById('activePrice').innerText = '₹5,427';
            document.getElementById('monthlyCost').innerText = '₹904/month';
          }
        });
      });
  
      // Toggle Price Breakup Details
      const viewPriceBreakupButton = document.getElementById("viewPriceBreakupButton");
      const priceBreakup = document.getElementById("priceBreakup");
      if (viewPriceBreakupButton) {
        viewPriceBreakupButton.addEventListener("click", function () {
          priceBreakup.classList.toggle("hidden");
        });
      }
    });
  </script>
