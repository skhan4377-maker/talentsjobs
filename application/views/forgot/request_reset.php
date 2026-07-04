<script src="https://www.google.com/recaptcha/api.js?render=<?= $recaptcha_site_key ?>"></script>

<style>
/* Fixed floating label styles */
.floating-label-group {
  position: relative;
  margin-bottom: 1rem;
}
.floating-input {
  position: relative;
  z-index: 2;
  background: transparent;
  padding-top: 1.25rem;
  padding-bottom: 0.75rem;
}
.floating-label {
  position: absolute;
  left: 16px;
  top: 50%;
  transform: translateY(-50%);
  color: #9CA3AF;
  font-size: 16px;
  transition: all 0.2s ease;
  pointer-events: none;
  z-index: 1;
}
.floating-input:focus + .floating-label,
.floating-input:not(:placeholder-shown) + .floating-label {
  top: 8px;
  font-size: 12px;
  color: #3B82F6;
  background: white;
  padding: 0 4px;
  transform: translateY(0);
}
</style>

<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-indigo-50 to-blue-50 px-4 py-24">
  <div class="bg-white rounded-3xl shadow-2xl max-w-md w-full p-10 relative overflow-hidden">
    
    <!-- Decorative Circles -->
    <div class="absolute -top-20 -left-20 w-40 h-40 bg-blue-200/40 rounded-full"></div>
    <div class="absolute -bottom-24 -right-24 w-60 h-60 bg-purple-200/30 rounded-full"></div>

    <!-- Header -->
    <h2 class="text-3xl font-bold text-gray-800 text-center mb-6">Forgot Password</h2>
    <p class="text-center text-gray-500 mb-8 text-sm">Enter your email to receive a password reset link.</p>

    <!-- FORM START -->
    <form id="forgotForm" class="space-y-6 relative z-10">
      <input type="hidden" name="recaptcha_token" id="recaptcha_token">
      <!-- CSRF Token -->
      <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
      
      <!-- Role Selection -->
      <div class="space-y-2">
        <label class="text-sm text-gray-700 block">I am a:</label>
        <div class="flex gap-4">
          <label class="flex-1 cursor-pointer">
            <input type="radio" name="role" value="candidate" class="peer hidden" checked>
            <div class="p-3 text-center border border-gray-300 rounded-xl peer-checked:border-blue-500 peer-checked:bg-blue-50 font-medium transition-all duration-300">
              Candidate
            </div>
          </label>
          <label class="flex-1 cursor-pointer">
            <input type="radio" name="role" value="employer" class="peer hidden">
            <div class="p-3 text-center border border-gray-300 rounded-xl peer-checked:border-purple-500 peer-checked:bg-purple-50 font-medium transition-all duration-300">
              Employer
            </div>
          </label>
        </div>
      </div>

      <!-- Email Input - Fixed -->
      <div class="floating-label-group">
        <input type="email" name="email" required
               class="floating-input w-full px-4 border border-gray-300 rounded-xl focus:border-blue-500 focus:ring-1 focus:ring-blue-200 outline-none transition"
               placeholder=" " />
        <label class="floating-label">Email Address</label>
      </div>

    
      <!-- Error Message Display -->
      <div id="forgotErrors" class="text-sm"></div>

      <!-- Submit Button -->
      <button type="submit" id="forgotBtn"
              class="w-full py-3 bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-xl font-semibold shadow-lg hover:scale-105 hover:shadow-xl transition-transform duration-300 flex items-center justify-center gap-2">
        <span id="btnText">Send Link</span>
        <svg id="btnSpinner" class="w-5 h-5 animate-spin hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 12a8 8 0 018-8v0m0 16a8 8 0 01-8-8m16 0a8 8 0 01-8 8m0-16a8 8 0 018 8" />
        </svg>
      </button>
    </form>
    <!-- FORM END -->

    <!-- Back to Login -->
    <div class="mt-6 text-center text-sm text-gray-500">
      <a href="<?= base_url('auth/login') ?>" class="text-blue-600 hover:underline font-medium">Back to login</a>
    </div>
  </div>
</div>
<script>
const form       = $("#forgotForm");
const btn        = $("#forgotBtn");
const btnText    = $("#btnText");
const btnSpinner = $("#btnSpinner");
const errorBox   = $("#forgotErrors");

const cooldownKey      = "forgotCooldown";
const cooldownDuration = 60; // seconds

// CSRF
const csrfInput = $('input[name="<?php echo $this->security->get_csrf_token_name(); ?>"]');
const csrfName  = csrfInput.attr('name');

// --------------------
// COOLDOWN FUNCTIONS
// --------------------
function startCooldown(seconds) {
	const endTime = Date.now() + seconds * 1000;
	localStorage.setItem(cooldownKey, endTime);

	const interval = setInterval(() => {
		const remaining = Math.floor((endTime - Date.now()) / 1000);

		if (remaining > 0) {
			btnText.text(`Try again in ${remaining}s`);
			btn.prop("disabled", true);
		} else {
			clearInterval(interval);
			localStorage.removeItem(cooldownKey);
			btnText.text("Send Link");
			btn.prop("disabled", false);
		}
	}, 1000);
}

function checkCooldown() {
	const saved = localStorage.getItem(cooldownKey);
	if (saved && Date.now() < parseInt(saved)) {
		startCooldown((parseInt(saved) - Date.now()) / 1000);
		return true;
	}
	return false;
}

// --------------------
// FORM SUBMIT
// --------------------
form.on("submit", function (e) {
	e.preventDefault();
	if (checkCooldown()) return;

	const email = $('input[name="email"]').val();
	const role  = $('input[name="role"]:checked').val();

	if (!email) {
		errorBox.html('<div class="text-red-600">Please enter your email address</div>');
		return;
	}

	btn.prop("disabled", true);
	btnText.text("Sending...");
	btnSpinner.removeClass("hidden");
	errorBox.html("");

	// --------------------
	// reCAPTCHA v3
	// --------------------
	grecaptcha.ready(function () {
		grecaptcha.execute("<?= $recaptcha_site_key ?>", { action: 'forgot_password' })
		.then(function (token) {

			const formData = new FormData();
			formData.append('email', email);
			formData.append('role', role);
			formData.append('recaptcha_token', token);
			formData.append(csrfName, csrfInput.val());

			$.ajax({
				url: "<?= base_url('forgot-password/send-link') ?>",
				type: "POST",
				data: formData,
				processData: false,
				contentType: false,
				dataType: "json",

				success: function (res) {

					// refresh CSRF
					if (res.csrf_name && res.csrf_hash) {
						$('input[name="' + res.csrf_name + '"]').val(res.csrf_hash);
					}

					if (res.success) {
						errorBox.html('<div class="text-green-600">' + res.message + '</div>');
						form[0].reset();
						startCooldown(cooldownDuration);
					} else {
						errorBox.html('<div class="text-red-600">' + res.message + '</div>');
						btn.prop("disabled", false);
						btnText.text("Send Link");
					}

					btnSpinner.addClass("hidden");
				},

				error: function (xhr) {
					errorBox.html('<div class="text-red-600">Server error. Please try again.</div>');
					btn.prop("disabled", false);
					btnText.text("Send Link");
					btnSpinner.addClass("hidden");

					if (xhr.status === 403) {
						location.reload();
					}
				}
			});
		});
	});
});
</script>
