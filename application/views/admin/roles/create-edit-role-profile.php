<?php
$profile = isset($profile) ? $profile : null;
$actionUrl = $profile ? base_url('admin/AdminRole/create_or_update_role_profile/' . $profile->employer_id) : base_url('admin/AdminRole/create_or_update_role_profile');
?>

<div class="bg-gradient-to-br from-white via-gray-50 to-white min-h-screen px-4 sm:px-6 lg:px-8 py-6">
  <div class="mx-auto">
    <div class="bg-white/80 backdrop-blur-lg border border-gray-200 p-8 rounded-3xl shadow-xl space-y-8">

      <form id="roleProfileForm" class="space-y-8" autocomplete="off" action="<?= $actionUrl ?>" method="post">

        <h2 class="text-xl font-semibold text-gray-800">
          <?= $profile ? 'Edit' : 'Create' ?> Role-Based Employer Profile
        </h2>

        <!-- First Name -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">First Name <span class="text-red-500">*</span></label>
          <input type="text" name="name" value="<?= $profile->name ?? '' ?>" required
            class="w-full text-sm bg-white border border-gray-300 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500" />
        </div>

        <!-- Last Name -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Last Name</label>
          <input type="text" name="last_name" value="<?= $profile->last_name ?? '' ?>"
            class="w-full text-sm bg-white border border-gray-300 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500" />
        </div>

        <!-- Email -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Email <span class="text-red-500">*</span></label>
          <input type="email" name="email" value="<?= $profile->email ?? '' ?>" required
            class="w-full text-sm bg-white border border-gray-300 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500" />
        </div>

        <!-- Mobile -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Mobile <span class="text-red-500">*</span></label>
          <input type="text" name="mobile" value="<?= $profile->mobile ?? '' ?>" required
            class="w-full text-sm bg-white border border-gray-300 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500" />
        </div>

        <!-- Password -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">
            Password <?= $profile ? '<small class="text-gray-500">(Leave blank to keep current)</small>' : '<span class="text-red-500">*</span>' ?>
          </label>
          <div class="flex gap-2">
            <input type="password" id="password" name="password"
              <?= $profile ? '' : 'required' ?>
              class="w-full text-sm bg-white border border-gray-300 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500" />
            <button type="button" onclick="generatePassword()"
              class="px-4 py-2 text-sm bg-gray-100 border border-gray-300 rounded-xl hover:bg-gray-200 transition-all text-gray-700">
              🔐 Generate
            </button>
          </div>
        </div>

        <!-- Role -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Select Role <span class="text-red-500">*</span></label>
          <select name="role" required
            class="w-full text-sm bg-white border border-gray-300 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500">
            <option value="">-- Select Role --</option>
            <?php foreach (get_role_options() as $rName): ?>
              <option value="<?= $rName ?>" <?= isset($profile) && $profile->role === $rName ? 'selected' : '' ?>>
                <?= ucwords(str_replace('_', ' ', $rName)) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <!-- Submit Button -->
        <div class="flex justify-end">
          <button type="submit"
            class="inline-flex items-center gap-2 px-6 py-3 text-sm font-semibold text-white bg-blue-600 rounded-xl hover:bg-blue-700 shadow-md transition-all">
            <?= $profile ? '✏️ Update Profile' : '➕ Create Profile' ?>
          </button>
        </div>

      </form>
    </div>
  </div>
</div>

<script>
function generatePassword(length = 10) {
  const chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789@#$!&";
  let password = "";
  for (let i = 0; i < length; i++) {
    password += chars.charAt(Math.floor(Math.random() * chars.length));
  }
  const input = document.getElementById('password');
  input.value = password;
  input.type = 'text';
  setTimeout(() => input.type = 'password', 8000);
}

document.getElementById('roleProfileForm').addEventListener('submit', function (e) {
  e.preventDefault();
  const form = this;
  const formData = new FormData(form);

  fetch(form.action, {
    method: 'POST',
    body: formData
  })
    .then(res => res.json())
    .then(response => {
      const alertBox = document.createElement('div');
      alertBox.className = 'text-sm rounded px-4 py-3 mt-4';

      if (response.status === 'success') {
        alertBox.classList.add('bg-emerald-100', 'border-l-4', 'border-emerald-400', 'text-emerald-800');
        alertBox.innerText = response.message;
        form.reset();
      } else {
        alertBox.classList.add('bg-rose-100', 'border-l-4', 'border-rose-400', 'text-rose-800');
        alertBox.innerText = response.message;
      }

      form.prepend(alertBox);
      setTimeout(() => alertBox.remove(), 5000);
    })
    .catch(() => {
      alert("Something went wrong. Please try again.");
    });
});
</script>
