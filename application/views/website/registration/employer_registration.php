<!-- Select2 CSS for searchable dropdown -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://www.google.com/recaptcha/api.js?render=<?= $recaptcha_site_key ?>"></script>
<style>
    /* --- RADIO CARD --- */
    .radio-card.checked { border-color: #3b82f6; background-color: #f0f9ff; }
    .radio-card .radio-check { transition: opacity 0.2s ease; }
    .radio-card.checked .radio-check { display: block !important; }
    @keyframes slide-in-left {
        0% { transform: translateX(-100px); opacity: 0 }
        100% { transform: translateX(0); opacity: 1 }
    }
    .animate-slide-in-left { animation: slide-in-left 0.6s cubic-bezier(0.22, 1, 0.36, 1) forwards; }

    /* ----------- NORMAL PLACEHOLDER INPUT (NO FLOATING) ----------- */
    .floating-input {
        width: 100%;
        padding: 12px 16px;
        border: 2px solid #e5e7eb;
        border-radius: 8px;
        font-size: 1rem;
        background: white;
        transition: border-color 0.2s ease;
    }
    .floating-input:focus { border-color: #3b82f6; outline: none; }
    .floating-label { display: none !important; }
    .floating-input::placeholder { color: #9ca3af !important; }

    /* --- SELECT DROPDOWN --- */
    .bg-select-arrow {
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e");
        background-position: right 0.5rem center;
        background-size: 1.5em 1.5em;
    }

    /* --- BUTTON LOADER --- */
    .loading-spinner { position: absolute; left: 50%; transform: translateX(-50%); }
    .submit-btn { position: relative; }
    .btn-text { transition: opacity 0.2s ease; }
    .btn-text.invisible { opacity: 0; }
    @keyframes spin { to { transform: rotate(360deg); } }
    .animate-spin { animation: spin 1s linear infinite; }

    /* Select2 height fix */
    .select2-container .select2-selection--single {
        height: 48px !important;
        border: 2px solid #e5e7eb !important;
        border-radius: 0.5rem !important;
        display: flex !important;
        align-items: center !important;
        padding: 0 12px !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: normal !important; color: #111827; padding-left: 0 !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 100% !important; right: 10px !important;
    }
    .select2-container { width: 100% !important; }
    </style>

<section class="min-h-screen bg-gradient-to-br from-indigo-50 to-blue-50 pt-20 pb-8 px-4 sm:px-6 lg:px-8">
   <div class="max-w-7xl mx-auto">
      <div class="flex flex-col lg:flex-row items-center gap-8 lg:gap-12">
         <!-- Left Section (unchanged) -->
         <div class="hidden lg:block w-full lg:w-1/2 animate-slide-in-left">
            <div class="space-y-6 bg-gradient-to-br from-blue-600 to-indigo-700 p-8 rounded-2xl shadow-2xl">
               <div class="flex items-center gap-3">
                  <div class="p-2 bg-white/10 rounded-full">
                     <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                     </svg>
                  </div>
                  <h1 class="text-4xl font-bold text-white leading-tight">
                     Build Your Dream Team<br>
                     <span class="text-blue-200">With Confidence</span>
                  </h1>
               </div>
               <p class="text-lg text-blue-100/90 mt-4">
                  Access a curated network of verified professionals across industries. AI-powered matching and advanced screening included.
               </p>
               <div class="relative mt-8 group">
                  <div class="absolute -inset-1 bg-gradient-to-r from-blue-400 to-purple-400 rounded-2xl blur opacity-30 group-hover:opacity-50 transition duration-1000"></div>
                  <img src="https://static.naukimg.com/s/5/117/i/registration_Page.81f6520ec94363234dd0.png" 
                     alt="Recruitment illustration" 
                     class="relative w-full max-w-lg transform transition duration-500 hover:scale-105">
               </div>
            </div>
         </div>
         <!-- Right Section - Glassmorphism Form -->
         <div class="w-full lg:w-1/2 bg-white/80 backdrop-blur-lg rounded-2xl shadow-2xl p-8 relative overflow-hidden">
            <div class="absolute -top-32 -right-32 w-64 h-64 bg-blue-500/10 rounded-full"></div>
            <div class="absolute -bottom-32 -left-32 w-64 h-64 bg-purple-500/10 rounded-full"></div>
            <div class="relative space-y-2 mb-8">
               <h2 class="text-3xl font-bold bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">
                  Employer Registration
               </h2>
               <p class="text-sm text-gray-600 font-medium">
                  Already have an account? 
                  <a href="<?= base_url('auth/login'); ?>" class="text-blue-600 font-semibold hover:text-purple-600 transition-colors">
                  Sign in here
                  </a>
               </p>
            </div>
            <form id="registrationForm" method="post" class="space-y-6 pb-20 md:pb-0 relative">
               <input type="hidden" name="recaptcha_token" id="recaptcha_token">
               <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>" id="csrf_token">
               <div id="validationErrors" class="text-red-500 text-sm space-y-2"></div>
               
               <!-- Account Type Selector -->
               <div class="space-y-4">
                  <label class="block text-sm font-medium text-gray-700">Account Type</label>
                  <div class="grid grid-cols-2 gap-2 sm:gap-3" id="radioGroup">
                     <!-- Direct Employer -->
                     <label class="radio-card relative p-3 sm:p-4 border-2 border-gray-200 rounded-xl cursor-pointer transition-all text-xs sm:text-sm">
                        <input type="radio" name="recuiter_type" value="Direct Employer" class="hidden" checked>
                        <div class="space-y-1">
                           <div class="font-semibold text-gray-900 text-sm sm:text-base">Direct Employer</div>
                           <p class="text-gray-500 leading-tight">Manage hiring</p>
                        </div>
                        <div class="absolute top-2 right-2 radio-check hidden">
                           <svg class="w-5 h-5 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                              <path d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"/>
                           </svg>
                        </div>
                     </label>
                     <!-- Recruitment Firm -->
                     <label class="radio-card relative p-3 sm:p-4 border-2 border-gray-200 rounded-xl cursor-pointer transition-all text-xs sm:text-sm">
                        <input type="radio" name="recuiter_type" value="Recruitment Firm" class="hidden">
                        <div class="space-y-1">
                           <div class="font-semibold text-gray-900 text-sm sm:text-base">Recruitment Firm</div>
                           <p class="text-gray-500 leading-tight">Manage clients</p>
                        </div>
                        <div class="absolute top-2 right-2 radio-check hidden">
                           <svg class="w-5 h-5 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                              <path d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"/>
                           </svg>
                        </div>
                     </label>
                  </div>
               </div>

               <!-- Form Fields -->
               <div class="space-y-5">
                  <!-- Full Name -->
                  <div class="relative">
                     <input type="text" id="name" name="name" 
                        class="floating-input peer w-full px-4 py-3 border-2 border-gray-200 rounded-lg
                        focus:border-blue-500 focus:ring-0 placeholder-transparent"
                        placeholder="John Doe">
                     <label class="floating-label absolute left-4 text-gray-500 pointer-events-none
                        transition-all duration-200 peer-placeholder-shown:text-base
                        peer-placeholder-shown:top-3.5 peer-focus:-top-2 peer-focus:text-sm
                        peer-focus:text-blue-600 -top-2 text-sm bg-white px-1">
                     Full Name
                     </label>
                  </div>
                  <!-- Email -->
                  <div class="relative">
                     <input type="email" id="email" name="email" 
                        class="floating-input peer w-full px-4 py-3 border-2 border-gray-200 rounded-lg
                        focus:border-blue-500 focus:ring-0 placeholder-transparent"
                        placeholder="john@company.com">
                     <label class="floating-label absolute left-4 text-gray-500 pointer-events-none
                        transition-all duration-200 peer-placeholder-shown:text-base
                        peer-placeholder-shown:top-3.5 peer-focus:-top-2 peer-focus:text-sm
                        peer-focus:text-blue-600 -top-2 text-sm bg-white px-1">
                     Email Address
                     </label>
                     <div class="absolute right-4 top-4 text-gray-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                           <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                     </div>
                  </div>

                  <!-- ===== COUNTRY + MOBILE (SEARCHABLE) ===== -->
                  <div class="space-y-4">
                     <!-- Country Dropdown -->
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
                     <!-- Mobile Number (local only) -->
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

                  <!-- Company Type & Company Name -->
                  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                     <div class="relative">
                        <select id="company_type" name="company_type" 
                           class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg appearance-none
                           focus:border-blue-500 focus:ring-0 bg-select-arrow bg-no-repeat bg-right-4">
                           <option value="">Company Type</option>
                           <option value="Foreign MNC">Foreign MNC</option>
                           <option value="Indian MNC">Indian MNC</option>
                           <option value="Corporate">Corporate</option>
                           <option value="Startup">Startup</option>
                           <option value="Govt/PSU">Govt/PSU</option>
                           <option value="Others">Others</option>
                        </select>
                        <div class="absolute right-4 top-4 text-gray-400 pointer-events-none">
                           <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                           </svg>
                        </div>
                     </div>
                     <div class="relative">
                        <input type="text" id="company_name" name="company_name" 
                           class="floating-input peer w-full px-4 py-3 border-2 border-gray-200 rounded-lg
                           focus:border-blue-500 focus:ring-0 placeholder-transparent"
                           placeholder="Acme Corp Pvt Ltd">
                        <label class="floating-label absolute left-4 text-gray-500 pointer-events-none
                           transition-all duration-200 peer-placeholder-shown:text-base
                           peer-placeholder-shown:top-3.5 peer-focus:-top-2 peer-focus:text-sm
                           peer-focus:text-blue-600 -top-2 text-sm bg-white px-1">
                        Company Name
                        </label>
                     </div>
                  </div>

                  <!-- Password -->
                  <div class="relative">
                     <input type="password" id="password" name="password" 
                        class="floating-input peer w-full px-4 py-3 border-2 border-gray-200 rounded-lg
                        focus:border-blue-500 focus:ring-0 placeholder-transparent pr-12"
                        placeholder="••••••••">
                     <label class="floating-label absolute left-4 text-gray-500 pointer-events-none
                        transition-all duration-200 peer-placeholder-shown:text-base
                        peer-placeholder-shown:top-3.5 peer-focus:-top-2 peer-focus:text-sm
                        peer-focus:text-blue-600 -top-2 text-sm bg-white px-1">
                     Password
                     </label>
                     <button type="button" class="absolute right-4 top-4 text-gray-400 hover:text-blue-500 password-toggle">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                           <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                           <path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                     </button>
                  </div>

                  <!-- Agree Terms -->
                  <div class="flex items-start mt-4">
                     <input type="checkbox" id="agreeToTerms" name="agreeToTerms" 
                        class="w-5 h-5 mt-1 border-2 border-gray-300 rounded-md focus:ring-blue-500 
                        text-blue-600 cursor-pointer transition-colors" checked>
                     <label for="agreeToTerms" class="ml-3 text-sm text-gray-600 leading-tight">
                     I agree to the <a href="#" class="text-blue-600 hover:text-purple-600 font-medium transition-colors">Terms of Service</a> 
                     and <a href="#" class="text-blue-600 hover:text-purple-600 font-medium transition-colors">Privacy Policy</a>
                     </label>
                  </div>
               </div>

               <!-- Desktop Submit Button -->
               <button type="submit" 
                  class="submit-btn hidden md:block w-full bg-gradient-to-r from-blue-600 to-purple-600 text-white py-4 px-6 rounded-xl text-sm font-semibold
                  hover:shadow-lg hover:scale-[1.02] transition-all duration-300 transform shadow-blue-500/20 disabled:opacity-75 disabled:cursor-not-allowed">
                  <span class="btn-text">Create Employer Account</span>
                  <div class="loading-spinner hidden">
                     <svg class="animate-spin h-5 w-5 text-white mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                     </svg>
                  </div>
               </button>
               <!-- Mobile Sticky Button -->
               <div class="md:hidden fixed bottom-0 left-0 right-0 bg-white border-t shadow-xl p-4 z-50 backdrop-blur-lg">
                  <button type="submit" 
                     class="submit-btn w-full bg-gradient-to-r from-blue-600 to-purple-600 text-white py-4 px-6 rounded-xl text-sm font-semibold
                     hover:shadow-lg transition-all duration-300 shadow-blue-500/20 disabled:opacity-75 disabled:cursor-not-allowed">
                     <span class="btn-text">Create Account</span>
                     <div class="loading-spinner hidden">
                        <svg class="animate-spin h-5 w-5 text-white mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                           <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                           <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                     </div>
                  </button>
               </div>
            </form>
         </div>
      </div>
   </div>
</section>

<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
function showToast(message, type = 'info') {
    const toast = document.createElement('div');
    toast.className = `fixed bottom-4 inset-x-4 z-[9999] mx-auto max-w-xs px-4 py-3 rounded-lg text-white text-center shadow-lg transition-transform transform ${
        type === 'success' ? 'bg-green-500' :
        type === 'error'   ? 'bg-red-500'   : 'bg-blue-500'
    }`;
    toast.textContent = message;
    document.body.appendChild(toast);
    setTimeout(() => {
        toast.style.transform = 'translateY(150%)';
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}

document.addEventListener('DOMContentLoaded', function () {

    /* ---------- COUNTRY SELECT2 with Search ---------- */
    $('#country_select').select2({
        placeholder: "Search your country...",
        allowClear: true,
        width: '100%'
    });

    // Update hidden dial code field on change
    $('#country_select').on('change', function () {
        var dialCode = $(this).find('option:selected').data('dialcode');
        $('#country_code_hidden').val(dialCode);
    });

    // Set initial value if any option selected
    var initialSelected = $('#country_select').find('option:selected');
    if (initialSelected.val() !== '') {
        $('#country_code_hidden').val(initialSelected.data('dialcode'));
    }

    /* ---------- RADIO CARD UI ---------- */
    const radioCards = document.querySelectorAll('#radioGroup .radio-card');
    radioCards.forEach(card => {
        const radio = card.querySelector('input[type="radio"]');
        if (radio.checked) {
            card.classList.add('checked');
            card.querySelector('.radio-check').style.display = 'block';
        }
        card.addEventListener('click', function () {
            radioCards.forEach(c => {
                c.classList.remove('checked');
                c.querySelector('.radio-check').style.display = 'none';
            });
            this.classList.add('checked');
            this.querySelector('.radio-check').style.display = 'block';
            this.querySelector('input[type="radio"]').checked = true;
        });
    });

    /* ---------- PASSWORD TOGGLE ---------- */
    document.querySelectorAll('.password-toggle').forEach(button => {
        button.addEventListener('click', function () {
            const input = this.closest('div').querySelector('input');
            const path  = this.querySelector('svg path');
            if (input.type === 'password') {
                input.type = 'text';
                path.setAttribute('d',
                    'M3 3l18 18M12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7'
                );
            } else {
                input.type = 'password';
                path.setAttribute('d',
                    'M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5'
                );
            }
        });
    });

    /* ---------- FORM VALIDATION ---------- */
    $("#registrationForm").validate({
        errorClass: "text-red-500 text-sm",
        rules: {
            recuiter_type: { required: true },
            name: { required: true, minlength: 3, lettersonly: true },
            email: { required: true, email: true, emailDomain: true },
            password: { required: true, minlength: 8, maxlength: 16 },
            country_id: "required",
            mobile: {
                required: true,
                digits: true,
                minlength: 6,
                maxlength: 15
            },
            country_code: "required",
            company_type: { required: true },
            company_name: { required: true, minlength: 2 },
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
            const $btns     = $('.submit-btn');
            const $texts    = $('.btn-text');
            const $spinners = $('.loading-spinner');
            $btns.prop('disabled', true);
            $texts.addClass('invisible');
            $spinners.removeClass('hidden');

            grecaptcha.ready(function () {
                grecaptcha.execute("<?= $recaptcha_site_key ?>", { action: 'register' })
                    .then(function (token) {
                        $('#recaptcha_token').val(token);
                        const formData = new FormData(form);
                        formData.append('recaptcha_token', token);

                        $.ajax({
                            url: "<?= base_url('common/process_registration') ?>",
                            type: "POST",
                            data: formData,
                            processData: false,
                            contentType: false,
                            dataType: "json",
                            complete: function () {
                                $btns.prop('disabled', false);
                                $texts.removeClass('invisible');
                                $spinners.addClass('hidden');
                            },
                            success: function (response) {
                                // ★ CSRF token update – chahe response success ho ya error ★
                                if (response.csrf_name && response.csrf_hash) {
                                    // Meta tags update
                                    $('meta[name="csrf-token"]').attr('content', response.csrf_hash);
                                    $('meta[name="csrf-name"]').attr('content', response.csrf_name);
                                    // Hidden input field update (form ke andar wala)
                                    $('input[name="' + response.csrf_name + '"]').val(response.csrf_hash);
                                }

                                if (response.status === 'success') {
                                    showToast(response.message || 'Registration successful', 'success');
                                    form.reset();
                                    setTimeout(() => { window.location.href = response.redirect; }, 1200);
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
        return /^[a-zA-Z\s]+$/.test(value);
    });
    $.validator.addMethod("emailDomain", function (value) {
        const domain = value.split("@")[1] || '';
        return /^[a-zA-Z0-9-]+(\.[a-zA-Z0-9-]+)+$/.test(domain);
    });
    $.validator.addMethod("digits", function(value, element) {
        return this.optional(element) || /^\d+$/.test(value);
    }, "Only digits allowed");
});
</script>
