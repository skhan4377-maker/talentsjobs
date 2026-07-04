<script src="https://www.google.com/recaptcha/api.js?render=<?= $recaptcha_site_key ?>"></script>

<style>
/* Floating blob animation */
@keyframes blob {
  0%, 100% { transform: translate(0px, 0px) scale(1); }
  33% { transform: translate(10px, -10px) scale(1.05); }
  66% { transform: translate(-10px, 10px) scale(0.95); }
}
.animate-blob { animation: blob 7s infinite; }
.animation-delay-2000 { animation-delay: 2s; }


/* Fixed floating label styles */
.floating-label-group {
  position: relative;
  margin-bottom: 1rem;
}
.floating-input {
  position: relative;
  z-index: 2;
  background: transparent;
}
.floating-label {
  position: absolute;
  left: 12px;
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
  top: 0;
  font-size: 12px;
  color: #3B82F6;
  background: white;
  padding: 0 4px;
  transform: translateY(0);
}
</style>

<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-50 to-purple-50 px-4 py-12">
  <div class="relative bg-white rounded-3xl shadow-2xl max-w-md w-full overflow-hidden">
    
    <!-- Decorative Gradient Circles -->
    <div class="absolute -top-20 -right-20 w-60 h-60 bg-blue-400/20 rounded-full animate-blob"></div>
    <div class="absolute -bottom-24 -left-16 w-72 h-72 bg-purple-400/20 rounded-full animate-blob animation-delay-2000"></div>

    <div class="p-8 relative z-10">
      <h2 class="text-3xl font-bold text-center bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent mb-8">
        Set New Password
      </h2>
      
      <form method="post" action="<?= base_url('reset-password/save') ?>" id="resetPasswordForm" class="space-y-6">
        <!-- CSRF Token -->
		<input type="hidden" name="recaptcha_token" id="recaptcha_token">
        <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
        
        <input type="hidden" name="token" value="<?= $token ?>">
        <input type="hidden" name="role" value="<?= $role ?>">

        <!-- New Password -->
        <div class="floating-label-group">
          <input type="password" name="password" required
                 class="floating-input w-full px-4 py-3 border border-gray-300 rounded-xl focus:border-blue-500 focus:ring-1 focus:ring-blue-400 outline-none pr-12"
                 placeholder=" " />
          <label class="floating-label">New Password</label>
          <?= form_error('password', '<div class="text-red-600 text-sm mt-1">', '</div>'); ?>
          <button type="button" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-blue-500 password-toggle z-10">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
            </svg>
          </button>
        </div>

        <!-- Confirm Password -->
        <div class="floating-label-group">
          <input type="password" name="confirm_password" required
                 class="floating-input w-full px-4 py-3 border border-gray-300 rounded-xl focus:border-blue-500 focus:ring-1 focus:ring-blue-400 outline-none pr-12"
                 placeholder=" " />
          <label class="floating-label">Confirm Password</label>
          <?= form_error('confirm_password', '<div class="text-red-600 text-sm mt-1">', '</div>'); ?>
          <button type="button" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-blue-500 password-toggle z-10">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
            </svg>
          </button>
        </div>

        <!-- Submit -->
        <button type="submit" id="submitBtn" class="w-full py-3 bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-xl font-semibold hover:shadow-lg hover:scale-[1.02] transition-all duration-300 flex items-center justify-center gap-2">
          <span id="btnText">Update Password</span>
          <svg id="btnSpinner" class="w-5 h-5 animate-spin hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 12a8 8 0 018-8v0m0 16a8 8 0 01-8-8m16 0a8 8 0 01-8 8m0-16a8 8 0 018 8" />
          </svg>
        </button>
      </form>
    </div>
  </div>
</div>

<script>
// ------------------------------------
// PASSWORD TOGGLE
// ------------------------------------
document.querySelectorAll('.password-toggle').forEach(button => {
  button.addEventListener('click', function () {
    const input = this.closest('.floating-label-group').querySelector('input');
    const path  = this.querySelector('svg path');

    if (input.type === 'password') {
      input.type = 'text';
      path.setAttribute(
        'd',
        'M3 3l18 18M12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.478 0-8.268-2.943-9.542-7C3.732 7.943 7.522 5 12 5z'
      );
    } else {
      input.type = 'password';
      path.setAttribute(
        'd',
        'M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z'
      );
    }
  });
});

// ------------------------------------
// FORM SUBMIT WITH reCAPTCHA v3
// ------------------------------------
document.getElementById('resetPasswordForm').addEventListener('submit', function (e) {
  e.preventDefault();

  const form      = this;
  const submitBtn = document.getElementById('submitBtn');
  const btnText   = document.getElementById('btnText');
  const spinner   = document.getElementById('btnSpinner');

  submitBtn.disabled = true;
  btnText.textContent = 'Updating...';
  spinner.classList.remove('hidden');

  // reCAPTCHA v3 execution
  grecaptcha.ready(function () {
    grecaptcha.execute("<?= $recaptcha_site_key ?>", {
      action: 'reset_password'
    }).then(function (token) {

      document.getElementById('recaptcha_token').value = token;

      // normal POST submit
      form.submit();
    });
  });
});

// ------------------------------------
// LABEL CLICK → INPUT FOCUS
// ------------------------------------
document.querySelectorAll('.floating-label').forEach(label => {
  label.addEventListener('click', function () {
    const input = this.previousElementSibling;
    if (input) input.focus();
  });
});
</script>
