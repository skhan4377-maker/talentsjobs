<!-- Desktop Header -->
<div class="hidden lg:flex justify-between items-center mb-8 p-4 bg-white rounded-xl shadow-sm">
    <div class="flex flex-col sm:flex-row sm:items-center gap-3">
        <!-- Title -->
        <h1 class="text-lg sm:text-xl lg:text-2xl font-bold text-gray-800 tracking-tight">
            <?php echo html_escape(@$title); ?>
        </h1>

        <!-- Status Container -->
        <div class="flex flex-col sm:flex-row sm:items-center gap-2">
            <!-- Under-Review Message -->
            <?php if (@$status === 'under_review'): ?>
                <div class="flex items-center space-x-2 px-3 py-1.5 bg-amber-50 rounded-lg">
                    <svg class="w-4 h-4 text-amber-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    <span class="text-xs sm:text-sm text-amber-700 font-medium">
                        Account under review. Approval pending.
                    </span>
                </div>
            <?php endif; ?>

            <!-- Inactive Message -->
            <?php if (@$status === 'inactive'): ?>
                <div class="flex items-center space-x-2 px-3 py-1.5 bg-red-50 rounded-lg">
                    <svg class="w-4 h-4 text-red-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.366-.446.957-.716 1.571-.716s1.205.27 1.571.716l6.141 7.5c.764.934.106 2.401-1.071 2.401H2.187c-1.177 0-1.835-1.467-1.071-2.401l6.141-7.5zM11 14a1 1 0 10-2 0 1 1 0 002 0z" clip-rule="evenodd"/>
                    </svg>
                    <span class="text-xs sm:text-sm text-red-700 font-medium">
                        Account inactive. Contact support to reactivate.
                    </span>
                </div>
            <?php endif; ?>

            <!-- Rejected Message -->
            <?php if (@$status === 'rejected'): ?>
                <div class="flex items-center space-x-2 px-3 py-1.5 bg-red-100 rounded-lg">
                    <svg class="w-4 h-4 text-red-700 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm-2.83-5.17a1 1 0 011.42 0L10 13.59l1.41-1.41a1 1 0 111.42 1.42L11.41 15l1.41 1.41a1 1 0 01-1.42 1.42L10 16.41l-1.41 1.41a1 1 0 01-1.42-1.42L8.59 15l-1.42-1.41a1 1 0 010-1.42z" clip-rule="evenodd"/>
                    </svg>
                    <span class="text-xs sm:text-sm text-red-800 font-medium">
                        Account rejected post verification.
                    </span>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="flex items-center gap-6">
        <!-- Desktop Notifications -->
        <div class="relative">
            <button id="desktopNotificationBtn" class="p-2 text-gray-600 rounded-full relative">
                <i class="fas fa-bell text-xl"></i>
                <span class="notification-badge absolute top-0 right-0 w-5 h-5 bg-red-500 rounded-full text-white text-xs flex items-center justify-center hidden count-badge">
                    0
                </span>
            </button>
            <div id="desktopNotificationDropdown" class="hidden absolute right-0 mt-2 w-80 bg-white rounded-xl shadow-2xl border z-50">
                <div class="p-4">
                    <h3 class="font-semibold mb-3">Notifications (<span class="notification-title-count">0</span>)</h3>
                    <div class="space-y-3 max-h-60 overflow-y-auto notification-container">
                        <!-- Notifications will be dynamically inserted here -->
                    </div>
                </div>
            </div>
        </div>

        <!-- Desktop Profile Dropdown -->
        <div class="relative">
            <button id="desktopProfileBtn" class="flex items-center gap-3">
                <!-- Avatar with premium crown overlay -->
                <div class="relative">
                    <img src="<?= $this->session->userdata('logo') ? $this->session->userdata('logo') : base_url('assets/frontend/default-avatar.png'); ?>" 
                         class="w-10 h-10 rounded-full" 
                         alt="Profile">
                    <?php 
                        $role = $this->session->userdata('role');
                        $hasPlan = $this->session->userdata('has_active_plan');
                        if ($role === 'candidate' && $hasPlan): 
                    ?>
                        <span class="absolute -bottom-1 -right-1 inline-flex items-center justify-center w-5 h-5 rounded-full text-[10px] bg-gradient-to-br from-yellow-300 to-amber-500 text-white shadow-sm" title="Premium Subscriber">
                            <i class="fas fa-crown"></i>
                        </span>
                    <?php endif; ?>
                </div>
                <span class="text-gray-700">
                    <?= ucfirst($this->session->userdata('name')); ?>
                </span>
                <i class="fas fa-chevron-down text-sm text-gray-600"></i>
            </button>

            <div class="relative">
                <div id="desktopProfileDropdown" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-2xl border z-50">
                    <div class="py-2">
                        <!-- Profile Link -->
                        <?php if ($role === 'candidate'): ?>
                            <a href="<?= base_url('candidate/profile') ?>" class="flex items-center px-4 py-2 text-gray-700 hover:bg-gray-100">
                                <i class="fas fa-user mr-2"></i> Profile
                            </a>
                        <?php elseif ($role === 'employer'): ?>
                            <a href="<?= base_url('my-profile') ?>" class="flex items-center px-4 py-2 text-gray-700 hover:bg-gray-100">
                                <i class="fas fa-user mr-2"></i> Profile
                            </a>
                        <?php else: ?>
                            <a href="<?= base_url('my-profile') ?>" class="flex items-center px-4 py-2 text-gray-700 hover:bg-gray-100">
                                <i class="fas fa-user mr-2"></i> Profile
                            </a>
                        <?php endif; ?>

                        <!-- Premium Badge removed (now on avatar) -->

                        <!-- My Plans Link -->
                        <?php if (can('purchases')): ?>
                            <a href="<?= site_url('my-purchases') ?>" class="flex items-center px-4 py-2 text-gray-700 hover:bg-gray-100">
                                <i class="fas fa-shopping-bag mr-2"></i> My Plans
                            </a>
                        <?php endif; ?>

                        <!-- Employer My Plan Link (only for employer) -->
                        <?php if ($role === 'employer' && can('my_plan')): ?>
                            <a href="<?= base_url('employer/employer-plans') ?>" class="flex items-center px-4 py-2 text-gray-700 hover:bg-gray-100">
                                <i class="fas fa-shopping-bag mr-2"></i> My Plan
                            </a>
                        <?php endif; ?>

                        <!-- Settings Link -->
                        <?php if (can('settings')): ?>
                            <a href="<?= base_url('settings') ?>" class="flex items-center px-4 py-2 text-gray-700 hover:bg-gray-100">
                                <i class="fas fa-cog mr-2"></i> Settings
                            </a>
                        <?php endif; ?>

                        <hr class="my-2">
                        <a href="<?= base_url('auth/logout') ?>" class="flex items-center px-4 py-2 text-red-600 hover:bg-gray-100">
                            <i class="fas fa-sign-out-alt mr-2"></i> Logout
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Mobile Header -->
<div class="lg:hidden flex justify-between items-center mb-6 p-4 bg-white rounded-xl shadow-sm">
    <!-- Left Content -->
    <div class="flex flex-col sm:flex-row sm:items-center gap-3">
        <!-- Title -->
        <h1 class="text-lg sm:text-xl lg:text-2xl font-bold text-gray-800 tracking-tight">
            <?php echo html_escape($title); ?>
        </h1>

        <!-- Status Container -->
        <div class="flex flex-col sm:flex-row sm:items-center gap-2">
            <!-- Under-Review Message -->
            <?php if (@$status === 'under_review'): ?>
                <div class="flex items-center space-x-2 px-3 py-1.5 bg-amber-50 rounded-lg">
                    <svg class="w-4 h-4 text-amber-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    <span class="text-xs sm:text-sm text-amber-700 font-medium">
                        Account under review. Approval pending.
                    </span>
                </div>
            <?php endif; ?>

            <!-- Inactive Message -->
            <?php if (@$status === 'inactive'): ?>
                <div class="flex items-center space-x-2 px-3 py-1.5 bg-red-50 rounded-lg">
                    <svg class="w-4 h-4 text-red-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.366-.446.957-.716 1.571-.716s1.205.27 1.571.716l6.141 7.5c.764.934.106 2.401-1.071 2.401H2.187c-1.177 0-1.835-1.467-1.071-2.401l6.141-7.5zM11 14a1 1 0 10-2 0 1 1 0 002 0z" clip-rule="evenodd"/>
                    </svg>
                    <span class="text-xs sm:text-sm text-red-700 font-medium">
                        Account inactive. Contact support to reactivate.
                    </span>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="flex items-center gap-4">
        <!-- Mobile notificationBtn -->
        <div class="relative">
            <button id="notificationBtn" class="p-2 text-gray-600 rounded-full relative">
                <i class="fas fa-bell text-xl"></i>
                <span class="notification-badge absolute top-0 right-0 w-5 h-5 bg-red-500 rounded-full text-white text-xs flex items-center justify-center hidden count-badge">
                    0
                </span>
            </button>            
            <div id="notificationDropdown" class="hidden absolute right-0 mt-2 w-72 bg-white rounded-xl shadow-2xl border z-50">
                <div class="p-4">
                    <h3 class="font-semibold mb-3">Notifications (<span class="notification-title-count">0</span>)</h3>
                    <div class="space-y-3 max-h-60 overflow-y-auto notification-container">
                        <!-- Notifications will be dynamically inserted here -->
                    </div>
                </div>
            </div>
        </div>

        <div class="relative">
            <button id="profileBtn" class="flex items-center gap-2">
                <!-- Avatar with premium crown overlay -->
                <div class="relative">
                    <img src="<?= $this->session->userdata('logo') ? $this->session->userdata('logo') : base_url('assets/frontend/default-avatar.png'); ?>"
                         class="w-10 h-10 rounded-full" 
                         alt="Profile">
                    <?php if ($role === 'candidate' && $hasPlan): ?>
                        <span class="absolute -bottom-1 -right-1 inline-flex items-center justify-center w-5 h-5 rounded-full text-[10px] bg-gradient-to-br from-yellow-300 to-amber-500 text-white shadow-sm" title="Premium Subscriber">
                            <i class="fas fa-crown"></i>
                        </span>
                    <?php endif; ?>
                </div>
                <i class="fas fa-chevron-down text-sm text-gray-600"></i>
            </button>
            <?php $role = $this->session->userdata('role'); ?>
            <!-- Profile Dropdown -->
            <div id="profileDropdown" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-2xl border z-50">
                <div class="py-2">
                    <!-- Profile Link -->
                    <?php if (can('edit_profile')): ?>
                        <?php if ($role === 'candidate'): ?>
                            <a href="<?= base_url('candidate/profile') ?>" class="flex items-center px-4 py-2 text-gray-700 hover:bg-gray-100">
                                <i class="fas fa-user mr-2"></i> Profile
                            </a>
                        <?php else: ?>
                            <a href="<?= base_url('my-profile') ?>" class="flex items-center px-4 py-2 text-gray-700 hover:bg-gray-100">
                                <i class="fas fa-user mr-2"></i> Profile
                            </a>
                        <?php endif; ?>
                    <?php endif; ?>

                    <!-- Premium badge removed -->

                    <!-- My Plans Link -->
                    <?php if (can('purchases')): ?>
                        <a href="<?= site_url('my-purchases') ?>" class="flex items-center px-4 py-2 text-gray-700 hover:bg-gray-100">
                            <i class="fas fa-shopping-bag mr-2"></i> My Plans
                        </a>
                    <?php endif; ?>

                    <!-- Employer My Plan Link (only for employer) -->
                    <?php if ($role === 'employer' && can('my_plan')): ?>
                        <a href="<?= base_url('employer/employer-plans') ?>" class="flex items-center px-4 py-2 text-gray-700 hover:bg-gray-100">
                            <i class="fas fa-shopping-bag mr-2"></i> My Plan
                        </a>
                    <?php endif; ?>

                    <!-- Settings Link -->
                    <?php if (can('settings')): ?>
                        <a href="<?= base_url('settings') ?>" class="flex items-center px-4 py-2 text-gray-700 hover:bg-gray-100">
                            <i class="fas fa-cog mr-2"></i> Settings
                        </a>
                    <?php endif; ?>

                    <hr class="my-2">
                    <a href="<?= base_url('auth/logout') ?>" class="flex items-center px-4 py-2 text-red-600 hover:bg-gray-100">
                        <i class="fas fa-sign-out-alt mr-2"></i> Logout
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>