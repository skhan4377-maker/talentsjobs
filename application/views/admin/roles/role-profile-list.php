<div class="bg-white shadow rounded-xl p-6">
  <div class="flex justify-between items-center mb-4">
    <h2 class="text-lg font-semibold text-gray-800">All Role-Based Employer Profiles</h2>
    <a href="<?= base_url('admin/AdminRole/create_or_update_role_profile') ?>"
       class="inline-flex items-center px-4 py-2 text-sm text-white bg-blue-600 rounded hover:bg-blue-700">
      ➕ Create New Profile
    </a>
  </div>

  <div class="overflow-x-auto">
    <table class="min-w-full text-sm text-left border rounded-xl">
      <thead class="bg-gray-100 text-gray-700">
        <tr>
          <th class="px-4 py-3">#</th>
          <th class="px-4 py-3">Name</th>
          <th class="px-4 py-3">Email</th>
          <th class="px-4 py-3">Mobile</th>
          <th class="px-4 py-3">Role</th>
          <th class="px-4 py-3">Status</th>
          <th class="px-4 py-3">Actions</th>
        </tr>
      </thead>
      <tbody class="bg-white divide-y divide-gray-100 text-gray-800">
        <?php if (!empty($profiles)): ?>
          <?php foreach ($profiles as $i => $profile): ?>
            <tr>
              <td class="px-4 py-3"><?= $i + 1 ?></td>
              <td class="px-4 py-3"><?= $profile->name . ' ' . $profile->last_name ?></td>
              <td class="px-4 py-3"><?= $profile->email ?></td>
              <td class="px-4 py-3"><?= $profile->mobile ?></td>
              <td class="px-4 py-3 capitalize"><?= str_replace('_', ' ', $profile->role) ?></td>
              <td class="px-4 py-3">
				  <?php if ($profile->status === 'active'): ?>
					<a href="javascript:void(0);" onclick="toggleStatus(<?= $profile->employer_id ?>, 'inactive')"
					   class="inline-block px-2 py-1 text-xs bg-emerald-100 text-emerald-700 rounded-full hover:bg-emerald-200">
					  Active
					</a>
				  <?php else: ?>
					<a href="javascript:void(0);" onclick="toggleStatus(<?= $profile->employer_id ?>, 'active')"
					   class="inline-block px-2 py-1 text-xs bg-rose-100 text-rose-700 rounded-full hover:bg-rose-200">
					  Inactive
					</a>
				  <?php endif; ?>
				</td>

			  
			  
              <td class="px-4 py-3 space-x-2">
                <a href="<?= base_url('admin/AdminRole/create_or_update_role_profile/' . $profile->employer_id) ?>"
                   class="text-blue-600 hover:underline text-xs font-medium">Edit</a>               
              </td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr><td colspan="7" class="text-center py-4 text-gray-500">No profiles found.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
<script>
  function toggleStatus(id, newStatus) {
    const confirmationMessage = newStatus === 'active'
      ? 'Are you sure you want to activate this profile?'
      : 'Are you sure you want to deactivate this profile?';

    if (!confirm(confirmationMessage)) return;

    fetch('<?= base_url('admin/AdminRole/toggle_profile_status') ?>', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({ id: id, status: newStatus })
    })
    .then(res => res.json())
    .then(response => {
      if (response.status === 'success') {
        alert(response.message);
        location.reload();
      } else {
        alert(response.message || 'Status update failed.');
      }
    })
    .catch(() => alert('Something went wrong.'));
  }
</script>


