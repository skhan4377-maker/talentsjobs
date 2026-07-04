<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<!-- Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-153460368-1"></script>
<script>
       window.dataLayer = window.dataLayer || [];
       function gtag(){dataLayer.push(arguments);}
       gtag('js', new Date());
       gtag('config', 'UA-153460368-1');
    </script>
<style>
       .floating-label {
       @apply absolute left-4 top-3 text-gray-500 pointer-events-none transition-all duration-200 peer-placeholder-shown:text-base peer-placeholder-shown:top-3.5 peer-focus:-top-2 peer-focus:text-sm peer-focus:text-blue-600 -top-2 text-sm bg-white px-1;
       }
       .radio-card.checked {
       @apply border-blue-500 bg-blue-50/50;
       }
       .radio-card .radio-check {
       @apply transition-opacity duration-200;
       }
       @keyframes check-appear {
       0% { transform: scale(0); opacity: 0 }
       80% { transform: scale(1.2); }
       100% { transform: scale(1); opacity: 1 }
       }
       .loading-spinner {
       transition: opacity 0.2s ease-in-out;
       }
       .button-text {
       transition: opacity 0.2s ease-in-out;
       }
       
       /* Select2 height fix */
        .select2-container .select2-selection--single {
            height: 52px !important;
            border: 2px solid #e5e7eb !important;
            border-radius: 0.5rem !important;
            display: flex !important;
            align-items: center !important;
            padding: 0 12px !important;
        }
        
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: normal !important;
            color: #111827;
            padding-left: 0 !important;
        }
        
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 100% !important;
            right: 10px !important;
        }
        
        .select2-container {
            width: 100% !important;
        }
    </style>

<section class="min-h-screen bg-gradient-to-br from-indigo-50 to-blue-50 pt-20 pb-10 px-4 sm:px-6 lg:px-8">
   <div class="max-w-7xl mx-auto">
      <div class="flex flex-col lg:flex-row items-center gap-8 lg:gap-12">
         <!-- Left Section (unchanged) -->
         <div class="hidden lg:block w-full lg:w-1/2 animate-slide-in-left">
            <div class="space-y-6 bg-gradient-to-br from-blue-600 to-indigo-700 p-8 rounded-2xl shadow-2xl">
               <div class="flex items-center gap-3">
                  <div class="p-2 bg-white/10 rounded-full">
                     <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/>
                        <path d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/>
                     </svg>
                  </div>
                  <h1 class="text-4xl font-bold text-white leading-tight">
                     Begin Your Career Journey<br>
                     <span class="text-blue-200">With Confidence</span>
                  </h1>
               </div>
               <p class="text-lg text-blue-100/90">
                  Discover opportunities with top employers. Build a profile that reflects your strengths and ambitions.
               </p>
               <div class="relative mt-8 group">
                  <div class="absolute -inset-1 bg-gradient-to-r from-blue-400 to-purple-400 rounded-2xl blur opacity-30 group-hover:opacity-50 transition duration-1000"></div>
                  <img src="https://static.naukimg.com/s/5/117/i/registration_Page.81f6520ec94363234dd0.png" 
                     alt="Illustration of candidate" 
                     class="relative w-full max-w-lg transform transition duration-500 hover:scale-105">
               </div>
            </div>
         </div>
         <!-- Right Section (form) -->
         <div class="w-full lg:w-1/2 bg-white/80 backdrop-blur-lg rounded-2xl shadow-2xl p-8 relative overflow-hidden">
            <div class="absolute -top-32 -right-32 w-64 h-64 bg-blue-500/10 rounded-full"></div>
            <div class="absolute -bottom-32 -left-32 w-64 h-64 bg-purple-500/10 rounded-full"></div>
            <div class="relative space-y-2 mb-8">
               <h2 class="text-3xl font-bold bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">
                  Candidate Registration
               </h2>
               <?= $this->load->view('common/header_ads_tj8', '', TRUE) ?>
               <p class="text-sm text-gray-600 font-medium">
                  Already have an account? 
                  <a href="<?= base_url('auth/login'); ?>" class="text-blue-600 font-semibold hover:text-purple-600 transition">
                  Log in here
                  </a>
               </p>
            </div>
            <form id="registrationForm" method="post" class="space-y-6 pb-24 md:pb-0 relative">
               <input type="hidden" name="recaptcha_token" id="recaptcha_token">
               <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" id="csrf_token">
               <div id="validationErrors" class="text-red-500 text-sm space-y-2"></div>
               <input type="radio" name="user_type" value="Candidate" class="hidden" checked>

               <!-- Input Fields -->
               <div class="space-y-5">
                  <!-- Full Name -->
                  <div class="space-y-1">
                     <label for="name" class="block text-sm font-medium text-gray-700">Full Name</label>
                     <input type="text" id="name" name="name" 
                        class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:border-blue-500 focus:ring-0"
                        placeholder="John Doe">
                  </div>

                  <!-- Current Designation -->
                  <div class="space-y-1 relative">
                     <label for="designationName" class="block text-sm font-medium text-gray-700">Current Designation</label>
                     <input type="text" id="designationName" name="designation"
                        class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:border-blue-500 focus:ring-0"
                        placeholder="Software Engineer" autocomplete="off">
                     <input type="hidden" id="job_profile_id_register" name="designation_id">
                     <ul id="job_profile_list_register" class="absolute z-50 w-full bg-white shadow-lg rounded-b-lg mt-1 max-h-60 overflow-y-auto hidden"></ul>
                  </div>

                  <!-- Email -->
                  <div class="space-y-1 relative">
                     <label for="email" class="block text-sm font-medium text-gray-700">Email Address</label>
                     <input type="email" id="email" name="email"
                        class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:border-blue-500 focus:ring-0"
                        placeholder="john@example.com">
                     <div class="absolute right-4 top-1/2  text-gray-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                           <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                     </div>
                  </div>

                  <!-- ===== COUNTRY + MOBILE (Searchable) ===== -->
                  <div class="space-y-4">
                     <!-- Country Dropdown (Searchable) -->
                     <div class="space-y-1">
                        <label for="country_select" class="block text-sm font-medium text-gray-700">Country</label>
                        <select id="country_select" name="country_id"
                                class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:border-blue-500 focus:ring-0"
                                required>
                           <option value="">Select your country</option>
                           <?php foreach ($countries as $country): ?>
                              <option value="<?= $country['country_id'] ?>"
                                      data-dialcode="<?= $country['dial_code'] ?>"
                                      data-phonecode="<?= $country['phonecode'] ?>">
                                 <?= $country['country_name'] ?> (+<?= $country['phonecode'] ?>)
                              </option>
                           <?php endforeach; ?>
                        </select>
                     </div>

                     <!-- Mobile Number -->
                     <div class="space-y-1">
                        <label for="mobile" class="block text-sm font-medium text-gray-700">Mobile Number</label>
                        <input type="tel" id="mobile" name="mobile"
                               inputmode="numeric"
                               maxlength="15"
                               class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:border-blue-500 focus:ring-0"
                               placeholder="Enter Mobile number"
                               required>
                     </div>

                     <!-- Hidden field to store dial code (+91, +1 etc.) -->
                     <input type="hidden" name="country_code" id="country_code_hidden">
                  </div>
                  <!-- ===== END MOBILE BLOCK ===== -->

                  <!-- Password -->
                  <div class="relative">
                     <input type="password" id="password" name="password"
                        class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:border-blue-500 focus:ring-0 pr-12"
                        placeholder="••••••••">
                     <button type="button"
                        class="password-toggle absolute right-3 top-1/2 -translate-y-1/2 flex items-center justify-center w-6 h-6 text-gray-400 hover:text-blue-500">
                        <svg id="eyeIcon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                           <path id="eyePath" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M15 12a3 3 0 11-6 0 3 3 0 016 0z 
                                 M2.458 12C3.732 7.943 7.523 5 12 5 
                                 c4.478 0 8.268 2.943 9.542 7 
                                 -1.274 4.057-5.064 7-9.542 7 
                                 -4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                     </button>
                  </div>

                  <!-- Agree Terms -->
                  <div class="flex items-start mt-4">
                     <input type="checkbox" id="agreeToTerms" name="agreeToTerms"
                        class="w-5 h-5 mt-1 border-2 border-gray-300 rounded-md focus:ring-blue-500 text-blue-600 cursor-pointer transition-colors" checked>
                     <label for="agreeToTerms" class="ml-3 text-sm text-gray-600 leading-tight">
                     I agree to the 
                     <a href="#" class="text-blue-600 hover:text-purple-600 font-medium">Terms of Service</a> and 
                     <a href="#" class="text-blue-600 hover:text-purple-600 font-medium">Privacy Policy</a>
                     </label>
                  </div>
               </div>

               <!-- Desktop Submit Button -->
               <button type="submit" class="hidden md:block w-full bg-gradient-to-r from-blue-600 to-purple-600 text-white py-4 px-6 rounded-xl text-sm font-semibold hover:shadow-lg hover:scale-[1.02] transition-all duration-300 transform shadow-blue-500/20 relative">
                  <span class="button-text">Create Candidate Account</span>
                  <div class="loading-spinner hidden absolute inset-0 flex items-center justify-center">
                     <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                     </svg>
                  </div>
               </button>

               <!-- Mobile Submit Button -->
               <div class="md:hidden fixed bottom-0 left-0 right-0 bg-white border-t shadow-xl p-4 z-50 backdrop-blur-lg">
                  <button type="submit" class="w-full bg-gradient-to-r from-blue-600 to-purple-600 text-white py-4 px-6 rounded-xl text-sm font-semibold hover:shadow-lg transition-all duration-300 shadow-blue-500/20 relative">
                     <span class="button-text">Create Account</span>
                     <div class="loading-spinner hidden absolute inset-0 flex items-center justify-center">
                        <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                           <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                           <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>
                     </div>
                  </button>
               </div>
            </form>
         </div>
      </div>
   </div>
</section>

<!-- Recaptcha -->
<script src="https://www.google.com/recaptcha/api.js?render=<?= $recaptcha_site_key ?>"></script>
<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
function showToast(message, type = 'info') {
    const toast = document.createElement('div');
    toast.className = `fixed bottom-4 inset-x-4 z-[9999] mx-auto max-w-xs px-4 py-3 rounded-lg text-white text-center shadow-lg ${
        type === 'success' ? 'bg-green-500' :
        type === 'error'   ? 'bg-red-500'   : 'bg-blue-500'
    }`;
    toast.textContent = message;
    document.body.appendChild(toast);
    setTimeout(() => toast.remove(), 3000);
}

$(document).ready(function () {

    /* ---------- COUNTRY SELECT2 with Search ---------- */
    $('#country_select').select2({
        placeholder: "Search your country...",
        allowClear: true,
        width: '100%'
    });

    // Update hidden field with dial code (e.g., +91) when selection changes
    $('#country_select').on('change', function () {
        var selected = $(this).find('option:selected');
        $('#country_code_hidden').val(selected.data('dialcode'));
    });

    // If any pre-selected value exists, set hidden field on load
    var initialSelected = $('#country_select').find('option:selected');
    if (initialSelected.val() !== '') {
        $('#country_code_hidden').val(initialSelected.data('dialcode'));
    }

    /* ---------- PASSWORD TOGGLE ---------- */
    document.querySelector(".password-toggle").addEventListener("click", function () {
        const input = document.getElementById("password");
        const eyePath = document.getElementById("eyePath");
        if (input.type === "password") {
            input.type = "text";
            eyePath.setAttribute("d",
                "M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7"
            );
        } else {
            input.type = "password";
            eyePath.setAttribute("d",
                "M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5"
            );
        }
    });

    /* ---------- FORM VALIDATION ---------- */
    $("#registrationForm").validate({
        errorClass: "text-red-500 text-sm",
        rules: {
            name: { required: true, minlength: 3, lettersonly: true },
            designation: { required: true, minlength: 3 },
            email: { required: true, email: true, emailDomain: true },
            password: { required: true, minlength: 8, maxlength: 16 },
            country_id: "required",
            mobile: {
                required: true,
                digits: true,
                minlength: 6,
                maxlength: 15
            },
            country_code: "required",   // validated later by server regex
            agreeToTerms: "required"
        },
        messages: {
            country_id: "Please select your country",
            mobile: {
                digits: "Only numbers allowed",
                minlength: "At least 6 digits",
                maxlength: "Maximum 15 digits"
            }
        },
        submitHandler: function (form) {
            const buttons     = $(form).find('button[type="submit"]');
            const spinners    = $(form).find('.loading-spinner');
            const buttonTexts = $(form).find('.button-text');

            buttons.prop('disabled', true);
            buttonTexts.addClass('invisible');
            spinners.removeClass('hidden');

            grecaptcha.ready(function () {
                grecaptcha.execute("<?= $recaptcha_site_key ?>", { action: 'candidate_register' })
                    .then(function (token) {
                        $('#recaptcha_token').val(token);

                        const formData = new FormData(form);
                        formData.append('recaptcha_token', token);

                        $.ajax({
                            url: "<?= base_url('common/common_register') ?>",
                            type: "POST",
                            data: formData,
                            processData: false,
                            contentType: false,
                            dataType: "json",
                            complete: function () {
                                buttons.prop('disabled', false);
                                buttonTexts.removeClass('invisible');
                                spinners.addClass('hidden');
                            },
                            success: function (response) {
                                if (response.csrf_name && response.csrf_hash) {
                                    $('input[name="' + response.csrf_name + '"]').val(response.csrf_hash);
                                }
                                if (response.status === 'success') {
                                    showToast(response.message || 'Registration successful', 'success');
                                    form.reset();
                                    setTimeout(() => {
                                        window.location.href = response.redirect;
                                    }, 1200);
                                } else {
                                    showToast(response.error_msg || 'Something went wrong', 'error');
                                }
                            },
                            error: function () {
                                showToast('Server error. Please try again.', 'error');
                            }
                        });
                    });
            });

            return false;
        }
    });

    /* ---------- CUSTOM VALIDATORS ---------- */
    $.validator.addMethod("phone", function (value) {
        return /^(\+?\d{1,4})?[ -]?\d{6,14}$/.test(value);
    });
    $.validator.addMethod("lettersonly", function (value) {
        return /^[A-Za-z\s]+$/.test(value);
    });
    $.validator.addMethod("emailDomain", function (value) {
        const parts = value.split("@");
        return parts.length === 2 && /^[a-zA-Z0-9-]+(\.[a-zA-Z0-9-]+)+$/.test(parts[1]);
    });
    $.validator.addMethod("digits", function(value, element) {
        return this.optional(element) || /^\d+$/.test(value);
    }, "Only digits allowed");
});
</script>
