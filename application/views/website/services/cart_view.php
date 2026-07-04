
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<style>
@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}
</style>
  <section class="bg-gray-50 py-12">
  
	<!-- Global Page Loader -->
	<div id="globalLoader" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(255,255,255,0.8); z-index:9999; display:flex; align-items:center; justify-content:center;">
	  <div style="text-align:center;">
		<div class="loader" style="border: 5px solid #ccc; border-top: 5px solid #4f46e5; border-radius: 50%; width: 40px; height: 40px; animation: spin 1s linear infinite;"></div>
		<p style="margin-top: 10px; color:#4f46e5; font-weight:500;">Please wait...</p>
	  </div>
	</div>


     <!-- Main Container -->
     <div class="cart-page-cart-items"></div>
   </section>


<?php if(!$this->session->userdata('logged_in') || $this->session->userdata('role') !== 'candidate'){ ?>
<!-- Login Modal (Centered) -->
<div class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 hidden z-50" id="loginModal">
  <div class="bg-white rounded-2xl w-full max-w-md p-6 animate-slide-up">
    <!-- Modal Content -->
    <div class="space-y-6">
      <!-- Header -->
      <div class="flex justify-between items-center">
        <h3 class="text-2xl font-bold text-gray-900">Welcome Back</h3>
        <button id="closeLoginModal" class="text-gray-500 hover:text-gray-700">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
          </svg>
        </button>
      </div>

      <!-- Login Form -->
      <form class="space-y-5" id="loginForm">
        <!-- Email Input -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Email or Mobile</label>
          <input 
            type="text" 
            class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
            placeholder="Enter email or mobile"
            id="login_id" name="login_id"
          >
          <input type="hidden" name="role" value="candidate">
        </div>

        <!-- Password Input -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Password</label>
          <input 
            type="password" 
            class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
            placeholder="Enter password"
            id="login_password" name="login_password"
          >
        </div>

        <!-- Submit Button -->
        <button 
          type="submit"
          class="w-full bg-blue-600 hover:bg-blue-700 text-white py-3 px-4 rounded-xl font-medium transition-colors"
        >
          Sign In
        </button>

        <!-- Signup Link -->
        <p class="text-center text-sm text-gray-600">
          New user? 
          <a href="#" class="text-blue-600 font-medium hover:underline">Create account</a>
        </p>
      </form>
    </div>
  </div>
</div>
<?php } ?>

<script>
$(document).ready(function() {
	$('#globalLoader').fadeOut(); // Ensure loader is hidden
    loadCartItems(); 
    
	function loadCartItems() {
		$.ajax({
			url: '<?php echo site_url("website/services/CartController/load_cart_items"); ?>', 
			method: 'GET',
			dataType: 'json', // Expect JSON response if redirect
			success: function(response) {
				// Check if the response has a redirect_url property
				if(response.redirect_url) {
					window.location.href = response.redirect_url;
				} else if(response.status === 'success') {
					// Use the HTML property from the response
					$('.cart-page-cart-items').html(response.html);
				}
			},
			error: function() {
				console.error("Failed to load cart items.");
			}
		});
	}

    $(document).on('click', '.cart-page-remove-item', function(e) {
        e.preventDefault();
        // Get the item ID from the data-id attribute
        var itemId = $(this).data('id');		
        // Get feature_id and duration_id from clicked button
        var featureId = $(this).data('feature-id');
        var durationId = $(this).data('duration-id');
        // Show confirmation prompt
        var confirmRemove = confirm("Are you sure you want to remove this item from the cart?");
        // If user confirms, proceed to remove the item
        if (confirmRemove) {
            $.ajax({
                url: '<?php echo site_url("website/services/CartController/remove_cart_item"); ?>',
                method: 'POST',
                data: { id: itemId, feature_id :featureId, duration_id:durationId }, // Send the item ID to the server
                dataType: 'json', // Ensure the response is parsed as JSON
                success: function(response) {
                    // Check if the response is successful
                    if (response.success) {
                        alert("Item removed from cart.");
                        loadCartItems(); // Reload cart items after removing
                        loadCartSummary();
                    } else {
                        alert("Failed to remove item. Please try again.");
                    }
                },
                error: function() {
                    console.error("Error in removing the item.");
                }
            });
        } else {
            alert("Item removal cancelled.");
        }
    });
   
     // Define the onchange function
    function handleDurationChange() {
        // Get the selected duration and feature ID from the element's attributes
        var durationId = $(this).val();
        var featureId = $(this).data('feature-id');

        // AJAX request to add the plan to the cart
        $.ajax({
            url: '<?= base_url('website/services/CartController/add_to_cart'); ?>',
            type: 'POST',
            dataType: 'json',
            data: {
                feature_id: featureId,
                duration_id: durationId
            },
            success: function(response) {
                if(response.status === 'success'){
                    // Refresh the cart display if the add-to-cart was successful
                    loadCartItems();
                    loadCartSummary();
                }
            },
            error: function() {
                // Handle the error (e.g., show an error message in the console)
                console.error("Error adding the plan to the cart.");
            }
        });
    }

    // Bind the function to the change event of the select element with ID "cart-plan-duration"
    $(document).on('change', '#cart-plan-duration', handleDurationChange);
    
  <?php if(!$this->session->userdata('logged_in') || $this->session->userdata('role') !== 'candidate'){ ?>
	
    var modal = $('#loginModal');
    var closeBtn = $('#closeLoginModal');

    // Open Login Modal on Button Click
    $(document).on('click', '#openLoginModal', function() {
        modal.removeClass('hidden').addClass('flex');

        // Get feature_id and duration_id from clicked button
        var featureId = $(this).data('feature-id');
        var durationId = $(this).data('duration-id');

        // Store in modal for use after login
        modal.attr('data-feature-id', featureId);
        modal.attr('data-duration-id', durationId);
    });

    // Close Modal on Close Button Click
    closeBtn.on('click', function() {
        modal.addClass('hidden');
    });

    // Close Modal on Outside Click
    modal.on('click', function(e) {
        if (e.target.id === 'loginModal') {
            modal.addClass('hidden');
        }
    });

    // Close Modal on ESC Key Press
    $(document).on('keydown', function(e) {
        if (e.key === 'Escape' && !modal.hasClass('hidden')) {
            modal.addClass('hidden');
        }
    });

    // Handle Login Form Submission
    $(document).on('submit', '#loginForm', function(e) {
        e.preventDefault();

        $.ajax({
            url: '<?php echo site_url("credential"); ?>',
            method: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    alert(response.success_message);
                    modal.addClass('hidden');  // Close login popup
                      
                    var featureId = modal.attr('data-feature-id');
                    var durationId = modal.attr('data-duration-id');

                    // Call addToCart function after login success
                    addToCart(featureId, durationId);
                } else {
                    alert("Login failed: " + response.error_message);
                }
            },
            error: function() {
                console.error("Error during login.");
                alert("An unexpected error occurred. Please try again.");
            }
        });
    });

    // Function to Add Item to Cart After Login
    function addToCart(featureId, durationId) {
        $.ajax({
            url: '<?php echo site_url("website/services/CartController/add_to_cart"); ?>',
            method: 'POST',
            data: { feature_id: featureId, duration_id: durationId },
            dataType: 'json',
            success: function(response) {
                // Reload page after item is added to cart
                window.location.reload();
            },
            error: function() {
                console.error("Error adding item to cart.");
                alert("An unexpected error occurred while adding the item.");
            }
        });
    }

	<?php } ?>


   <?php if ($this->session->userdata('logged_in') === TRUE && $this->session->userdata('role') === 'candidate') { ?>
		$(document).on('click', '#place-order-btn', function(e) {
		  e.preventDefault();

		  $('#globalLoader').fadeIn(); // Show loader

		  $.ajax({
			url: '<?php echo site_url("website/services/PaymentController/checkout"); ?>',
			method: 'POST',
			dataType: 'json',
			success: function(response) {
			  if (response.error) {
				alert("Error: " + response.error);
				$('#globalLoader').fadeOut(); // Hide loader on error
				return;
			  }

			  var options = {
				"key": response.razorpay_key,
				"amount": response.amount,
				"currency": "INR",
				"name": '<?php echo SITE_NAME; ?>',
				"description": "Payment for order",
				"image": "<?= base_url('assets/frontend/logo.png'); ?>",
				"order_id": response.order_id,
				"handler": function(paymentResponse) {
				  $.ajax({
					url: '<?php echo site_url("website/services/PaymentController/handlePaymentResponse"); ?>',
					method: 'POST',
					dataType: 'json',
					data: {
					  razorpay_payment_id: paymentResponse.razorpay_payment_id,
					  razorpay_order_id: paymentResponse.razorpay_order_id,
					  razorpay_signature: paymentResponse.razorpay_signature,
					  amount: options.amount,
					  feature_ids: response.feature_ids,
					  duration_ids: response.duration_ids
					},
					success: function(res) {
					  $('#globalLoader').fadeOut(); // Hide loader
					  if (res.status === 'success') {
						alert(res.message);
						window.location.href = res.redirect_url;
					  } else {
						alert(res.message || "Payment verification failed.");
					  }
					},
					error: function() {
					  $('#globalLoader').fadeOut(); // Hide loader
					  alert("Payment verification request failed. Please contact support.");
					}
				  });
				},
				"theme": {
				  "color": "#F37254"
				},
				"modal": {
				  "ondismiss": function() {
					$('#globalLoader').fadeOut(); // Hide loader if Razorpay is closed
				  }
				}
			  };

			  var rzp = new Razorpay(options);
			  rzp.open();
			},
			error: function() {
			  $('#globalLoader').fadeOut(); // Hide loader
			  alert("Failed to initiate checkout. Please try again.");
			}
		  });
		});

	<?php } ?>
});  
</script>

