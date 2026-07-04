<div class="max-w-3xl mx-auto">
    <!-- Profile Card -->
    <div class="bg-white rounded-2xl shadow-lg p-6 mb-6 border border-gray-100">
        <!-- Header -->
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Profile Settings</h2>
                <p class="text-gray-500">Manage your account visibility and preferences</p>
            </div>
            <div class="bg-blue-100 text-blue-800 px-4 py-2 rounded-full text-sm font-medium 
						whitespace-nowrap flex-shrink-0">
				<?= ucfirst($userType) ?> Account
			</div>

        </div>

        <!-- Status Selection -->
        <div class="flex flex-col sm:flex-row items-center justify-between p-4 bg-gray-50 rounded-xl mb-6">
            <div class="space-y-1 mb-4 sm:mb-0">
                <h4 class="font-semibold text-gray-900">Profile Visibility</h4>
                <p class="text-sm text-gray-500">
                    <?php 
                        if ($profileStatus == 'active') {
                            echo 'Visible to ' . ($userType === 'employer' ? 'candidate' : 'employer');
                        } elseif ($profileStatus == 'under_review') {
                            echo 'Profile under review';
                        } elseif ($profileStatus == 'rejected') {
                            echo 'Profile rejected - activation not allowed';
                        } else {
                            echo 'Profile hidden';
                        }
                    ?>
                </p>
            </div>
            <div class="flex items-center gap-4">
                <label class="inline-flex items-center">
                    <input type="radio" name="profile_status" value="active" class="form-radio"
                        <?= $profileStatus == 'active' ? 'checked' : '' ?>
                        <?= in_array($profileStatus, ['under_review', 'rejected']) ? 'disabled' : '' ?>>
                    <span class="ml-2">Active</span>
                </label>
                <label class="inline-flex items-center">
                    <input type="radio" name="profile_status" value="inactive" class="form-radio"
                        <?= ($profileStatus == 'inactive' || $profileStatus == 'under_review' || $profileStatus == 'rejected') ? 'checked' : '' ?>
                        <?= $profileStatus == 'rejected' ? 'disabled' : '' ?>>
                    <span class="ml-2">Inactive</span>
                </label>
            </div>
        </div>

        <!-- Status Indicator -->
        <div class="p-4 rounded-xl mb-6 <?= 
            $profileStatus == 'active' ? 'bg-green-50 border border-green-200' : 
            ($profileStatus == 'under_review' ? 'bg-amber-50 border border-amber-200' : 
            ($profileStatus == 'rejected' ? 'bg-red-50 border border-red-200' : 'bg-gray-100 border border-gray-200'))
        ?>">
            <div class="flex items-center gap-3">
                <div class="flex-shrink-0">
                    <?php 
                        if ($profileStatus == 'active'): ?>
                            <span class="text-green-600">✅</span>
                    <?php elseif ($profileStatus == 'under_review'): ?>
                            <span class="text-amber-600">⏳</span>
                    <?php elseif ($profileStatus == 'rejected'): ?>
                            <span class="text-red-600">⛔</span>
                    <?php else: ?>
                            <span class="text-gray-600">⏸️</span>
                    <?php endif; ?>
                </div>
                <div>
                    <p class="text-sm <?= 
                        $profileStatus == 'active' ? 'text-green-800' : 
                        ($profileStatus == 'under_review' ? 'text-amber-800' : 
                        ($profileStatus == 'rejected' ? 'text-red-800' : 'text-gray-800'))
                    ?>">
                        <?php 
                            if ($profileStatus == 'active') {
                                echo 'Profile active and visible - You appear in search results';
                            } elseif ($profileStatus == 'under_review') {
                                echo 'Profile under review - Awaiting approval';
                            } elseif ($profileStatus == 'rejected') {
                                echo 'Profile rejected - You cannot activate your profile';
                            } else {
                                echo 'Profile paused - Not visible to others';
                            }
                        ?>
                    </p>
                </div>
            </div>
        </div>

        <!-- Danger Zone -->
        <div class="border border-red-100 rounded-xl bg-red-50 p-6">
            <div class="flex items-center justify-between gap-4">
                <div class="flex-1">
                    <h4 class="font-semibold text-red-800 mb-1">Delete Account</h4>
                    <p class="text-sm text-red-700">Permanently remove your account and all associated data</p>
                </div>
                <button onclick="deleteProfile('<?= htmlspecialchars($userType) ?>')" 
                        class="flex items-center gap-2 px-4 py-2.5 text-red-700 hover:bg-red-100 rounded-lg transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                    Delete
                </button>
            </div>
        </div>
    </div>
</div>

<input type="hidden" id="csrf_name" value="<?= $this->security->get_csrf_token_name(); ?>">
<input type="hidden" id="csrf_hash" value="<?= $this->security->get_csrf_hash(); ?>">


<script>
// Function to update CSRF token
function updateCsrfToken(response) {
    if (response.csrf_name && response.csrf_hash) {
        $('#csrf_name').val(response.csrf_name);
        $('#csrf_hash').val(response.csrf_hash);
    }
}
$(document).ready(function() {
    $('input[name="profile_status"]').change(function() {
        var status = $(this).val();
        var csrfName = $('#csrf_name').val();
        var csrfHash = $('#csrf_hash').val();

        if (confirm("Are you sure you want to change your profile status to " + status.replace('_', ' ') + "?")) {
            $.ajax({
                url: '<?= base_url("setting/ProfileManager/toggleProfileStatus"); ?>',
                type: 'POST',
                data: { 
                    status: status,
                    [csrfName]: csrfHash
                },
                dataType: 'json',
                success: function(response) {
                    // Update CSRF token
                    updateCsrfToken(response);

                    if (response.success) {
                        showToast('<?= $userType; ?> profile status toggled to ' + response.status + '.', 'success');
                        if (response.logout) {
                            window.location.href = '<?= base_url("auth/login"); ?>';
                        } else {
                            location.reload();
                        }
                    } else {
                        showToast(response.message, 'error');
                    }
                },
                error: function(xhr, status, error) {
                    showToast('An error occurred. Please try again.', 'error');
                    console.error('AJAX Error:', error);
                }
            });
        } else {
            location.reload();
        }
    });
});

function deleteProfile(userType) {
    var csrfName = $('#csrf_name').val();
    var csrfHash = $('#csrf_hash').val();

    if (confirm("Are you sure you want to delete your " + userType + " profile? This action cannot be undone.")) {
        $.ajax({
            url: '<?= base_url("setting/ProfileManager/deleteProfile"); ?>',
            type: 'POST',
            data: { 
                [csrfName]: csrfHash
            },
            dataType: 'json',
            success: function(response) {
                // Update CSRF token
                updateCsrfToken(response);

                if (response.success) {
                    showToast(userType + " profile deleted.", 'success');
                    window.location.href = '<?= base_url("auth/login"); ?>';
                } else {
                    showToast('Failed to delete profile.', 'error');
                }
            },
            error: function(xhr, status, error) {
                showToast('An error occurred. Please try again.', 'error');
                console.error('AJAX Error:', error);
            }
        });
    }
}

function showToast(message, type = 'info') {
    const toast = document.createElement('div');
    toast.className = `fixed bottom-4 right-4 z-[9999] px-6 py-3 rounded-lg text-white shadow-lg transition-transform transform translate-x-0 ${
        type === 'success' ? 'bg-green-500' :
        type === 'error' ? 'bg-red-500' : 'bg-blue-500'
    }`;
    toast.textContent = message;
    document.body.appendChild(toast);

    setTimeout(() => {
        toast.style.transform = 'translateX(150%)';
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}
</script>
