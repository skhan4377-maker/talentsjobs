<script src="https://www.google.com/recaptcha/api.js?render=<?= $recaptcha_site_key ?>"></script>

<!-- Main Content -->
<section class="min-h-screen bg-gradient-to-br from-blue-50 to-purple-50 pt-20 pb-12 px-4 sm:px-6 lg:px-8">
  <div class="max-w-7xl mx-auto">
    <div class="flex flex-col md:flex-row gap-12 items-center justify-center">
      
      <!-- Left Section - Illustration -->
      <div class="hidden md:block md:w-1/2 lg:w-2/5 space-y-8 text-center md:text-left">
        <div class="space-y-4 md:mt-8">
          <h1 class="text-2xl md:text-4xl font-bold text-gray-900 leading-tight">
            Welcome to <br class="hidden md:block">
            <span class="text-blue-600">Talents Recruitment Solutions</span>
          </h1>
          <p class="text-gray-600 md:text-lg">
            Connect with your next career opportunity or find the perfect candidate
          </p>
        </div>
        <img 
          src="https://static.naukimg.com/s/5/117/i/registration_Page.81f6520ec94363234dd0.png" 
          alt="Career illustration"
          class="max-w-full h-auto md:max-w-md mx-auto hidden md:block">
      </div>
		
      <!-- Right Section - Login Form -->
      <div class="w-full md:w-1/2 lg:w-96 bg-white rounded-2xl shadow-lg p-8">
        <div class="space-y-6">
          <div class="text-center">
            <h2 class="text-3xl font-bold text-gray-900 mb-2">Welcome Back</h2>
            <p class="text-gray-600">
              Don't have an account? 
              <a href="<?=base_url('registration/candidate');?>" class="text-blue-600 font-semibold hover:text-blue-800">
                Create Account
              </a>
            </p>
          </div>

          <!-- Role Selection -->        
          <div class="flex gap-4 justify-center" id="roleContainer">
            <label class="flex-1 cursor-pointer">
              <input type="radio" name="role" value="candidate" class="peer hidden" checked>
              <div class="p-3 text-center rounded-lg border-2 peer-checked:border-blue-500 peer-checked:bg-blue-50 transition-all">
                <span class="font-medium text-gray-700">Candidate</span>
              </div>
            </label>
            
            <label class="flex-1 cursor-pointer">
              <input type="radio" name="role" value="employer" class="peer hidden">
              <div class="p-3 text-center rounded-lg border-2 peer-checked:border-blue-500 peer-checked:bg-blue-50 transition-all">
                <span class="font-medium text-gray-700">Employer</span>
              </div>
            </label>
          </div>

          <!-- Login Form -->
          <form class="space-y-6" id="UloginForm">
		 
            <div id="loginValidationErrors"></div>
			<?php if ($this->input->get('account') === 'deleted'): ?>
			  <div class="w-full px-4 py-3 bg-red-50 border border-red-200 text-red-700 text-sm rounded-lg">
				Your account has been deleted by the administrator. Please contact support.
			  </div>
			<?php endif; ?>	

            <!-- Email Input -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
              <input 
                type="email" 
                name="login_id"
                class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none transition"
                placeholder="Enter your email"
                required
              >
            </div>

            <!-- Password Input -->
            <div class="relative">
			  <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>

			  <input 
				type="password" 
				name="login_password"
				class="w-full px-4 py-3 pr-12 rounded-lg border border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none transition password-input"
				placeholder="••••••••"
				required
			  >

			  <button type="button"
				class="absolute right-4 top-[42px] text-gray-400 hover:text-blue-500 password-toggle">
				<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                           <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                           <path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
			  </button>
			</div>


            <!-- reCAPTCHA -->
            <input type="hidden" name="recaptcha_token" id="recaptcha_token">


            <!-- Forgot Password -->
            <div class="flex items-center justify-end">
              <a href="<?=base_url('forgot-password')?>" class="text-sm text-blue-600 hover:text-blue-800">
                Forgot Password?
              </a>
            </div>

            <!-- Submit Button -->
            <button 
              type="submit"
              class="w-full bg-blue-600 text-white py-3 px-4 rounded-lg font-semibold hover:bg-blue-700 transition-colors flex items-center justify-center gap-2"
              id="loginButton"
            >
              <span>Sign In</span>
              <svg id="loadingSpinner" class="hidden w-5 h-5 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
            </button>
          </form>
        </div>
      </div>
    </div>
  </div>
</section>
<script>
window.addEventListener("pageshow", function (event) {
    if (event.persisted || performance.getEntriesByType("navigation")[0]?.type === "back_forward") {
        window.location.reload();
    }
});
</script>
<script>
document.addEventListener('DOMContentLoaded', function () {

    // ---------------------------------
    // Role Radio Button Styling
    // ---------------------------------
    const roleRadios = document.querySelectorAll('#roleContainer input[type="radio"]');
    roleRadios.forEach(radio => {
        radio.addEventListener('change', () => {
            document.querySelectorAll('#roleContainer div')
                .forEach(div => div.classList.remove('border-blue-500', 'bg-blue-50'));

            if (radio.checked) {
                radio.closest('label')
                    .querySelector('div')
                    .classList.add('border-blue-500', 'bg-blue-50');
            }
        });
    });

    // ---------------------------------
    // Password Show / Hide
    // ---------------------------------
    document.querySelectorAll('.password-toggle').forEach(button => {
        button.addEventListener('click', function () {
            const input = this.closest('div').querySelector('input');
            const svgPath = this.querySelector('svg path');

            if (input.type === 'password') {
                input.type = 'text';
                svgPath.setAttribute(
                    'd',
                    'M3 3l18 18M12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7'
                );
            } else {
                input.type = 'password';
                svgPath.setAttribute(
                    'd',
                    'M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5'
                );
            }
        });
    });

    // ---------------------------------
    // jQuery Validate + AJAX Login
    // ---------------------------------
    $("#UloginForm").validate({
        rules: {
            login_id: { required: true, email: true },
            login_password: "required",
            role: "required"
        },
        messages: {
            login_id: {
                required: "Please enter your email",
                email: "Please enter a valid email address"
            },
            login_password: "Please enter your password",
            role: "Please select a role"
        },
        errorClass: "text-red-600 text-sm",
        errorElement: "div",

        submitHandler: function (form) {
            const btn     = document.getElementById('loginButton');
            const spinner = document.getElementById('loadingSpinner');
        
            btn.disabled = true;
            spinner.classList.remove('hidden');
        
            // ---------------------------------
            // reCAPTCHA v3 TOKEN GENERATE
            // ---------------------------------
            grecaptcha.ready(function () {
                grecaptcha.execute("<?= $recaptcha_site_key ?>", { action: 'login' })
                    .then(function (token) {
                        $('#recaptcha_token').val(token);
        
                        // Build data object using global CSRF functions
                        const formData = {
                            login_id: $('input[name="login_id"]').val(),
                            login_password: $('input[name="login_password"]').val(),
                            role: $('input[name="role"]:checked').val(),
                            recaptcha_token: token
                        };
                        // Add CSRF token using global helpers
                        formData[getCSRFName()] = getCSRFToken();
        
                        // ---------------------------------
                        // AJAX REQUEST
                        // ---------------------------------
                        $.ajax({
                            type: "POST",
                            url: "<?= base_url('credential') ?>",
                            data: formData,
                            dataType: "json",
        
                            success: function (response) {
                                // If server returns a new CSRF token, update meta tags
                                if (response.csrf_token && response.csrf_name) {
                                    updateCSRFToken(response.csrf_token, response.csrf_name);
                                } else if (response.csrf_token) {
                                    // fallback: only token provided, keep same name
                                    updateCSRFToken(response.csrf_token, getCSRFName());
                                }
        
                                if (response.success === 1) {
                                    form.reset();
                                    setTimeout(() => {
                                        window.location.href = response.redirect;
                                    }, 200);
                                } else {
                                    $('#loginValidationErrors').html(
                                        `<span class="text-red-600 text-sm">${response.error_message}</span>`
                                    );
                                }
                            },
        
                            error: function (xhr) {
                                console.error("Login error:", xhr.responseText);
                            },
        
                            complete: function () {
                                btn.disabled = false;
                                spinner.classList.add('hidden');
                            }
                        });
                    });
            });
        
            return false;
        }
    });

});
</script>

