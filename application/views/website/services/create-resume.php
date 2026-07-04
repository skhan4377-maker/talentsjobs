
<section class="bg-gradient-to-b from-blue-50 to-white pt-20 pb-24 md:pb-16">

  <!-- Container: horizontally centered with reduced top margin -->
  <div class="w-full max-w-4xl bg-white shadow-xl rounded-lg p-8 mx-auto mt-4">
    <!-- Step Indicator (hidden on mobile screens) -->
    <div class="mb-8 hidden sm:block">
      <div class="flex items-center justify-center">
        <!-- Step 1 -->
        <div class="flex flex-col items-center">
          <div id="stepIndicator1" class="flex items-center justify-center w-8 h-8 rounded-full bg-blue-600 text-white font-semibold">1</div>
        </div>
        <span class="mt-1 text-xs font-medium text-gray-700"> Choose Template</span>
        <div class="w-16 h-1 mx-2 bg-blue-200" id="line1"></div>
        <!-- Step 2 -->
        <div class="flex flex-col items-center">
          <div id="stepIndicator2" class="flex items-center justify-center w-8 h-8 rounded-full bg-gray-300 text-gray-600 font-semibold">2</div>          
        </div>
        <span class="mt-1 text-xs font-medium text-gray-700"> Enter Your Details</span>
        <div class="w-16 h-1 mx-2 bg-gray-300" id="line2"></div>
        <!-- Step 3 -->
        <div class="flex flex-col items-center">
          <div id="stepIndicator3" class="flex items-center justify-center w-8 h-8 rounded-full bg-gray-300 text-gray-600 font-semibold">3</div>         
        </div>
        <span class="mt-1 text-xs font-medium text-gray-700"> Download Resume</span>
      </div>
    </div>

    <!-- Step 1: Choose Template -->
    <div id="step1" class="step">
      <h2 class="text-2xl font-bold text-center mb-6">Choose a Resume Template</h2>     
      <!-- Templates will be loaded dynamically into this container -->
      <div id="templates-container"></div>
      <!-- Note: No Next button here; the "Use this template" button triggers the next step -->
    </div>

   <!-- Step 2: Enter Your Details -->
	<div id="step2" class="step hidden">
	  <h2 class="text-2xl font-bold text-center mb-6">Add your name</h2>
	  <p class="mt-1 text-xs text-gray-500 text-center">
		You made a great template selection! Now let’s add your name to it.
	  </p>
	  <!-- Error message container for Step 2 -->
	  <div id="error-message-step2" class="text-red-600 text-sm mb-4"></div>
	  <form id="detailsForm" class="space-y-4 mt-4">
		<div>
		  <label for="firstName" class="block text-gray-700 mb-1">First Name</label>
		  <input type="text" id="firstName" name="firstName" class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-600" placeholder="John" />
		</div>
		<div>
		  <label for="lastName" class="block text-gray-700 mb-1">Last Name</label>
		  <input type="text" id="lastName" name="lastName" class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-600" placeholder="Doe" />
		</div>
		<!-- Additional fields can be added here -->
	  </form>
	  <div class="flex justify-between mt-6">
		<button id="backToStep1" class="px-6 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400 transition">
		  Previous
		</button>
		<button id="toStep3" class="px-6 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition">
		  Next
		</button>
	  </div>
	</div>


    <!-- Step 3: Download Resume / Supply Contact Information -->
       <!-- Step 3: Download Resume / Supply Contact Information -->
    <div id="step3" class="step hidden">
      <h2 class="text-2xl font-bold text-center mb-6">Supply contact information</h2>
      <p class="mt-1 text-xs text-gray-500 text-center">
        It’s important to let employers know how to contact you. Enter your email address below.
      </p>
      <div class="mb-4 mt-4">
        <label for="email" class="block text-gray-700 mb-1">Confirm Email Address</label>
        <input type="email" id="email" name="email" class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-600" placeholder="confirm@example.com" />
      </div>
      
      <!-- Password Field -->
      <div class="mb-4">
        <label for="password" class="block text-gray-700 mb-1">Password</label>
        <div class="relative">
          <input type="password" id="password" name="password" class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-600" placeholder="Enter password" />
          <button type="button" class="absolute inset-y-0 right-0 flex items-center px-3 text-gray-600" onclick="togglePassword('password')">
            👁
          </button>
        </div>
      </div>
      
      <!-- Confirm Password Field -->
      <!--<div class="mb-4">
        <label for="confirm_password" class="block text-gray-700 mb-1">Confirm Password</label>
        <div class="relative">
          <input type="password" id="confirm_password" name="confirm_password" class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-600" placeholder="Confirm password" />
          <button type="button" class="absolute inset-y-0 right-0 flex items-center px-3 text-gray-600" onclick="togglePassword('confirm_password')">
            👁
          </button>
        </div>
      </div>-->
      
      <!-- Error message container -->
      <div id="error-message" class="text-red-600 text-sm mb-4"></div>
      
      <div class="flex flex-col sm:flex-row justify-between mt-6 space-y-4 sm:space-y-0 sm:space-x-4">
        <button id="backToStep2" class="px-6 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400 transition">
          Previous
        </button>
        <button id="downloadResume" class="px-6 py-2 bg-green-600 text-white rounded hover:bg-green-700 transition">
          Continue
        </button>
      </div>
    </div>
        
  </div>
</section>

    <script>
    function togglePassword(fieldId) {
      var field = document.getElementById(fieldId);
      field.type = field.type === "password" ? "text" : "password";
    }
    </script>

  <script>
   // Function to set the template_id cookie and then redirect
	function storeTemplateCookie(templateId, redirectUrl) {
		// Set the cookie 'template_id' with the value of templateId (URL-encoded) for the entire domain.
		document.cookie = "template_id=" + encodeURIComponent(templateId) + "; path=/";		
	}
	
	function getCookie(name) {
      const cookieArr = document.cookie.split(";");
      for (let i = 0; i < cookieArr.length; i++) {
        const cookiePair = cookieArr[i].split("=");
        if (name === cookiePair[0].trim()) {
          return decodeURIComponent(cookiePair[1]);
        }
      }
      return null;
    }


    // Define custom step names and mapping to element IDs
    const stepNames = ["templates", "introduction", "contact-info"];
    const stepIdMapping = {
      "templates": "step1",
      "introduction": "step2",
      "contact-info": "step3"
    };

    // Update the step indicator appearance based on the current step name
    function updateStepIndicator(currentStep) {
      stepNames.forEach((name, index) => {
        const indicator = document.getElementById('stepIndicator' + (index + 1));
        const line = document.getElementById('line' + (index + 1));
        if (name === currentStep) {
          indicator.classList.replace('bg-gray-300', 'bg-blue-600');
          indicator.classList.replace('text-gray-600', 'text-white');
          if (line) {
            line.classList.replace('bg-gray-300', 'bg-blue-600');
            line.classList.replace('bg-blue-200', 'bg-blue-600');
          }
        } else {
          indicator.classList.replace('bg-blue-600', 'bg-gray-300');
          indicator.classList.replace('text-white', 'text-gray-600');
          if (line) {
            line.classList.replace('bg-blue-600', 'bg-gray-300');
          }
        }
      });
    }

    // Push the current step name to the URL using the History API
    function pushStepToUrl(stepName) {
      history.pushState({step: stepName}, '', '?step=' + stepName);
    }

    // Fetch templates via AJAX and render them into the container
    function fetchTemplates() {
      $.ajax({
        url: "<?= site_url('website/services/ResumeServiceController/load_templates'); ?>",
        type: "GET",
        dataType: "json",
        success: function(response) {
          $('#templates-container').html(response.html);
        },
        error: function() {
          alert('Failed to fetch templates');
        }
      });
    }
    
    $(document).ready(function() {
      // Fetch and render templates for Step 1
      fetchTemplates();
      
      // Initialize current step based on URL parameter
      const urlParams = new URLSearchParams(window.location.search);
      const currentStep = urlParams.get('step') || "templates";
      $('.step').addClass('hidden');
      $('#' + stepIdMapping[currentStep]).removeClass('hidden');
      updateStepIndicator(currentStep);
      history.replaceState({step: currentStep}, '', '?step=' + currentStep);
      
      // Delegated event binding for dynamically loaded "Use this template" buttons
      $(document).on('click', '.use-template-btn', function() {
        $('#step1').addClass('hidden');
        $('#step2').removeClass('hidden');
        updateStepIndicator("introduction");
        pushStepToUrl("introduction");
      });
      
      // Static event bindings for step navigation
      $('#backToStep1').on('click', function () {
        $('#step2').addClass('hidden');
        $('#step1').removeClass('hidden');
        updateStepIndicator("templates");
        pushStepToUrl("templates");
      });
      
      $('#toStep3').on('click', function () {
		  // Clear any previous error message for Step 2
		  $('#error-message-step2').text('');
		  
		  // Validate first and last name fields
		  var firstName = $('#firstName').val().trim();
		  var lastName = $('#lastName').val().trim();
		  
		  if(firstName === "" || lastName === ""){
			$('#error-message-step2').text("Please fill in both your first and last name.");
			return false; // Prevent transition if validation fails
		  }
		  
		  // If validation passes, move to Step 3
		  $('#step2').addClass('hidden');
		  $('#step3').removeClass('hidden');
		  updateStepIndicator("contact-info");
		  pushStepToUrl("contact-info");
		});

      
      $('#backToStep2').on('click', function () {
        $('#step3').addClass('hidden');
        $('#step2').removeClass('hidden');
        updateStepIndicator("introduction");
        pushStepToUrl("introduction");
      });
      
      
      $('#downloadResume').on('click', function () {
        // Clear any previous error message
        $('#error-message').text('');
        
        // Gather data from Step 2 and Step 3
        const firstName = $('#firstName').val().trim();
        const lastName = $('#lastName').val().trim();
        const email = $('#email').val().trim();
        // Get password values (MISSING IN ORIGINAL CODE)
        const password = $('#password').val().trim();
        //const confirm_password = $('#confirm_password').val().trim();
    
        // Validate required fields
        if (!firstName || !lastName || !email || !password) {
            $('#error-message').addClass('text-red-600').text('Please fill in all the required fields.');
            return false;
        }
        
        // Validate email format using a regex
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email)) {
            $('#error-message').addClass('text-red-600').text('Please enter a valid email address.');
            return false;
        }
    
        // Show a modern processing spinner on the "Continue" button
        const $btn = $(this);
        $btn.prop('disabled', true);
        $btn.html(
            '<svg class="animate-spin h-5 w-5 text-white mr-2 inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">' +
            '<circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>' +
            '<path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>' +
            '</svg>Processing...'
        );
    
        // Get template_id from cookies
        const templateId = getCookie('template_id');
    
        // Send data to the server via AJAX
        $.ajax({
            url: "<?= site_url('Common/register_resume'); ?>",
            type: "POST",
            dataType: "json",
            data: {
                firstName: firstName,
                lastName: lastName,
                email: email,
                password: password,
                //confirm_password: confirm_password,
                templateId: templateId // Send template_id with the request
            },
            success: function(response) {
                if (response.status === 'success') {
                    // Reset form fields
                    $('#detailsForm').trigger('reset'); // Step 2 form reset
                    $('#email, #password').val(''); // Step 3 fields clear
                      // Remove template_id cookie
                    document.cookie = "template_id=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
        
                    $('#error-message').removeClass('text-red-600').addClass('text-green-600').html(response.message);
                    
                    // Redirect using the URL returned from the server
                    setTimeout(function() { 
                        window.location.href = response.redirectUrl; 
                    }, 1000);
                    
                } else {
                    $('#error-message').removeClass('text-green-600').addClass('text-red-600').html(response.message);
                }
                $btn.prop('disabled', false);
                $btn.text('Continue');
            },
            error: function() {
                $('#error-message').removeClass('text-green-600').addClass('text-red-600').text('An error occurred while submitting your details.');
                $btn.prop('disabled', false);
                $btn.text('Continue');
            }
        });
    });

      
      // Handle browser navigation via popstate
      window.addEventListener('popstate', function(event) {
        let currentStep = "templates";
        if (event.state && event.state.step) {
          currentStep = event.state.step;
        } else {
          const urlParams = new URLSearchParams(window.location.search);
          currentStep = urlParams.get('step') || "templates";
        }
        $('.step').addClass('hidden');
        $('#' + stepIdMapping[currentStep]).removeClass('hidden');
        updateStepIndicator(currentStep);
      });
    });
  </script>

