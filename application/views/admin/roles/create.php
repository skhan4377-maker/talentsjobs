<?php 
$is_edit = isset($role);
$form_action = $is_edit 
    ? base_url('admin/AdminRole/update/'.$role->id) 
    : base_url('admin/AdminRole/store');
?>

<div class="max-w-xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        
        <!-- Header -->
        <div class="px-6 py-4 border-b bg-gradient-to-r from-blue-50 to-indigo-50">
            <h2 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
                <i class="fas fa-user-tag text-blue-600"></i>
                <?= $is_edit ? 'Edit Role' : 'Create New Role' ?>
            </h2>
            <p class="text-sm text-gray-500 mt-1">
                <?= $is_edit ? 'Update role details below' : 'Add a new role to manage user permissions' ?>
            </p>
        </div>

        <!-- Form -->
        <form method="post" action="<?= $form_action ?>" class="p-6 space-y-5">
            
            <!-- CSRF Token -->
            <input type="hidden" 
                   name="<?= $this->security->get_csrf_token_name(); ?>" 
                   value="<?= $this->security->get_csrf_hash(); ?>">

            <!-- Role Name -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Role Name <span class="text-red-500">*</span>
                </label>

                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                        <i class="fas fa-id-badge"></i>
                    </span>

                    <input type="text" 
                           name="role_name" 
                           required
                           value="<?= $is_edit ? htmlspecialchars($role->role_name) : '' ?>"
                           class="w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                           placeholder="e.g. HR Manager, Moderator">
                </div>

                <p class="text-xs text-gray-500 mt-1">
                    Role name should be unique and easy to understand.
                </p>
            </div>

            <!-- Buttons -->
            <div class="flex items-center justify-between pt-4 border-t">
                <a href="<?= base_url('admin/AdminRole') ?>" 
                   class="text-sm text-gray-600 hover:text-gray-800">
                    ← Cancel
                </a>

                <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-lg shadow-sm hover:shadow transition flex items-center gap-2">
                    <i class="fas <?= $is_edit ? 'fa-save' : 'fa-plus-circle' ?>"></i>
                    <?= $is_edit ? 'Update Role' : 'Create Role' ?>
                </button>
            </div>

        </form>
    </div>
</div>