<section class="min-h-screen bg-gradient-to-br from-blue-50 to-purple-50 pt-20 pb-12 px-4 sm:px-6 lg:px-8">
  <div class="max-w-3xl mx-auto">
    <div class="flex flex-col items-center justify-center">

      <!-- Admin Login Card -->
      <div class="w-full max-w-md bg-white rounded-2xl shadow-lg p-8">
        <div class="space-y-6 text-center">
          <h2 class="text-3xl font-bold text-gray-900">Admin Login</h2>
          <p class="text-gray-600 text-sm">Only authorized admin users can access this portal.</p>
        </div>

        <!-- Login Form -->
        <form class="space-y-6 mt-8" id="adminLoginForm">
		
			<!-- CSRF Hidden Field -->
			<input type="hidden"
			   name="<?= $this->security->get_csrf_token_name(); ?>"
			   value="<?= $this->security->get_csrf_hash(); ?>"
			   id="csrf_token">
		   
          <div id="adminLoginValidationErrors" class="hidden"></div>

          <!-- Email -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
            <input type="email" name="username"
              class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-blue-200 focus:border-blue-500"
              placeholder="admin@domain.com" required>
          </div>

          <!-- Password -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
            <input type="password" name="password"
              class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-blue-200 focus:border-blue-500"
              placeholder="••••••••" required>
          </div>

          <!-- Submit -->
          <button type="submit"
            class="w-full bg-blue-600 text-white py-3 px-4 rounded-lg font-semibold hover:bg-blue-700 flex justify-center items-center gap-2"
            id="adminLoginButton">
            <span>Login</span>
            <svg id="adminLoginSpinner" class="hidden w-5 h-5 animate-spin" xmlns="http://www.w3.org/2000/svg"
              fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor"
                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
              </path>
            </svg>
          </button>
        </form>
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
<!-- AJAX Script -->
<script>
document.addEventListener('DOMContentLoaded', function () {

  $("#adminLoginForm").submit(function (e) {
    e.preventDefault();

    const username = $('input[name="username"]').val();
    const password = $('input[name="password"]').val();

    // CSRF Token
    const csrf_token_name = $("#csrf_token").attr("name");
    const csrf_token_value = $("#csrf_token").val();

    const btn = $('#adminLoginButton');
    const spinner = $('#adminLoginSpinner');

    btn.prop('disabled', true);
    spinner.removeClass('hidden');

    $.ajax({
      type: "POST",
      url: "<?= site_url('admin/login') ?>",
      data: {
        username: username,
        password: password,
        [csrf_token_name]: csrf_token_value
      },
      dataType: "json",

      success: function (response) {

        // Update CSRF Token After Response
        if (response.csrf_token_name && response.csrf_hash) {
          $("#csrf_token").attr("name", response.csrf_token_name);
          $("#csrf_token").val(response.csrf_hash);
        }

        if (response.success) {
          window.location.href = response.redirect_url;
        } else {
          $('#adminLoginValidationErrors')
            .removeClass('hidden')
            .html(`<div class="text-red-600 text-sm">${response.message}</div>`);
        }
      },

      error: function (xhr) {
        console.error(xhr.responseText);
      },

      complete: function () {
        btn.prop('disabled', false);
        spinner.addClass('hidden');
      }
    });
  });

});
</script>