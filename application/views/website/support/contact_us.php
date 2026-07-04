<!-- Add this in the <head> section (same as login) -->
<script src="https://www.google.com/recaptcha/api.js?render=<?= $recaptcha_site_key ?>"></script>

<style>
  .recaptcha-error {
    color: #dc2626;
    font-size: 0.875rem;
    margin-top: 0.5rem;
  }
</style>

<section class="bg-gray-50 pt-24 pb-16">
  <div class="max-w-6xl mx-auto px-4">
    <h1 class="text-3xl font-bold text-gray-800 mb-2 text-center">Contact Us</h1>
    <p class="text-lg text-gray-600 mb-10 text-center">If you have any questions or inquiries, feel free to contact us!</p>

    <!-- Flex Container for Equal Height Columns -->
    <div class="md:flex md:gap-8">
      
      <!-- Contact Form Column -->
      <div class="bg-white shadow rounded-xl p-6 mb-8 md:mb-0 w-full h-full">
        <form id="contactForm" enctype="multipart/form-data" class="space-y-4">
          <!-- CSRF Token -->
          <input type="hidden"
                 name="<?php echo $this->security->get_csrf_token_name(); ?>"
                 value="<?php echo $this->security->get_csrf_hash(); ?>"
                 id="csrf_token">

          <!-- reCAPTCHA v3 hidden input (token generated on submit) -->
          <input type="hidden" name="recaptcha_response" id="recaptcha_response">

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
            <input type="text" name="name" required class="w-full border border-gray-300 rounded-lg px-4 py-3">
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
            <input type="email" name="email" required class="w-full border border-gray-300 rounded-lg px-4 py-3">
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">You are a</label>
            <select name="role" required class="w-full border border-gray-300 rounded-lg px-4 py-3">
              <option value="">Select your role</option>
              <option value="candidate">Candidate</option>
              <option value="employer">Employer</option>
            </select>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Subject</label>
            <input type="text" name="subject" required class="w-full border border-gray-300 rounded-lg px-4 py-3">
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Message</label>
            <textarea name="message" rows="5" required class="w-full border border-gray-300 rounded-lg px-4 py-3"></textarea>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Attachment</label>
            <input type="file" name="attachment" accept="image/*,application/pdf" class="w-full border border-gray-300 rounded-lg px-4 py-2">
          </div>

          <!-- Error message container for reCAPTCHA -->
          <div id="recaptcha-error" class="recaptcha-error hidden"></div>

          <div id="formResponse" class="text-sm text-center text-green-600 font-medium hidden"></div>

          <div>
            <button type="submit" id="submitBtn" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-3 rounded-lg w-full flex items-center justify-center gap-2">
              <span id="btnText">Send Message</span>
              <svg id="btnSpinner" class="w-5 h-5 animate-spin hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 12a8 8 0 018-8v0m0 16a8 8 0 01-8-8m16 0a8 8 0 01-8 8m0-16a8 8 0 018 8" />
              </svg>
            </button>
          </div>
        </form>
      </div>

      <!-- Contact Info Column (unchanged) -->
      <div class="bg-white shadow rounded-xl p-6 space-y-6 w-full h-full">
        <div>
          <h2 class="text-xl font-semibold text-gray-800 mb-2">Get in Touch</h2>
          <p class="text-gray-600">Reach out to us using the following information:</p>
        </div>
        <div>
          <h3 class="text-sm font-semibold text-gray-500 uppercase mb-1">Email</h3>
          <p class="text-gray-800">info@talentsjobs.in</p>
        </div>
        <div>
          <h3 class="text-sm font-semibold text-gray-500 uppercase mb-1">Phone</h3>
          <p class="text-gray-800">+91-89594-97264</p>
        </div>
        <div>
          <h3 class="text-sm font-semibold text-gray-500 uppercase mb-1">Office Address</h3>
          <p class="text-gray-800">
            Talents Jobs<br>
            City Centre Noida Dadri Gautambuddha Nagar,<br>
            Noida, Uttar Pradesh - 203207
          </p>
        </div>
        <div>
          <h3 class="text-sm font-semibold text-gray-500 uppercase mb-1">Working Hours</h3>
          <p class="text-gray-800">Mon - Fri: 9:30 AM - 6:30 PM</p>
        </div>
      </div>

    </div>
  </div>
</section>

<script>
document.getElementById('contactForm').addEventListener('submit', function(e) {
  e.preventDefault();

  const form = document.getElementById('contactForm');
  const submitBtn = document.getElementById('submitBtn');
  const btnText = document.getElementById('btnText');
  const btnSpinner = document.getElementById('btnSpinner');
  const responseBox = document.getElementById('formResponse');
  const recaptchaError = document.getElementById('recaptcha-error');
  const hiddenRecaptcha = document.getElementById('recaptcha_response');

  // Show loading state
  submitBtn.disabled = true;
  btnText.textContent = 'Sending...';
  btnSpinner.classList.remove('hidden');
  recaptchaError.classList.add('hidden');

  // Execute reCAPTCHA v3 and submit on success
  grecaptcha.ready(function() {
    grecaptcha.execute('<?= $recaptcha_site_key ?>', { action: 'contact' })
      .then(function(token) {
        // Store token in hidden field
        hiddenRecaptcha.value = token;

        // Prepare form data (CSRF and files automatically included)
        const formData = new FormData(form);

        // Send request
        fetch("<?= base_url('website/support/submit_contact_form') ?>", {
          method: "POST",
          body: formData
        })
        .then(res => res.json())
        .then(data => {
          // Reset UI
          submitBtn.disabled = false;
          btnText.textContent = 'Send Message';
          btnSpinner.classList.add('hidden');

          // Show response message
          responseBox.classList.remove('hidden');
          responseBox.classList.toggle('text-red-600', data.status !== 'success');
          responseBox.classList.toggle('text-green-600', data.status === 'success');
          responseBox.innerText = data.message;

          // Update CSRF token if returned
          if (data.csrf_token) {
            document.getElementById('csrf_token').value = data.csrf_token;
          }

          // Reset form on success (optional)
          if (data.status === 'success') {
            form.reset();
            // Clear hidden reCAPTCHA value for next submission
            hiddenRecaptcha.value = '';
          }
        })
        .catch(() => {
          submitBtn.disabled = false;
          btnText.textContent = 'Send Message';
          btnSpinner.classList.add('hidden');

          responseBox.classList.remove('hidden');
          responseBox.classList.add('text-red-600');
          responseBox.innerText = "Something went wrong. Please try again.";
          hiddenRecaptcha.value = '';
        });
      });
  });
});
</script>