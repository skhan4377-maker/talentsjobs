<div class="text-center py-20">
  <div class="inline-flex items-center justify-center w-20 h-20 bg-red-100 text-red-600 rounded-full mb-4">
    <i class="fas fa-lock text-3xl"></i>
  </div>
  <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-100 mb-2">
    Access Restricted
  </h2>
  <?php
    $status_messages = [
      'under_review' => 'Your profile is currently under review. You’ll be able to access this feature once approved.',
      'rejected'     => 'Your account has been rejected. Access to this section is disabled.',
      'inactive'     => 'Your account is inactive. Please reactivate your profile to continue.',
      'default'      => 'Access to this section is currently restricted for your account.'
    ];

    $message = $status_messages[$status] ?? $status_messages['default'];
  ?>
  <p class="text-gray-600 dark:text-gray-400 text-sm">
    <?= $message ?>
  </p>
</div>

