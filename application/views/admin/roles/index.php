<div class="max-w-6xl">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">

        <!-- Header -->
        <div class="px-6 py-4 border-b bg-gradient-to-r from-blue-50 to-indigo-50 flex items-center justify-between">
            <div>
                <h2 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
                    <i class="fas fa-user-shield text-blue-600"></i>
                    Manage Roles
                </h2>
                <p class="text-sm text-gray-500 mt-1">View and manage system roles and their permissions</p>
            </div>

            <a href="<?= base_url('admin/AdminRole/create') ?>"
               class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg shadow-sm hover:shadow flex items-center gap-2 transition">
                <i class="fas fa-plus-circle"></i>
                Create Role
            </a>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 text-gray-600 uppercase text-xs tracking-wider">
                        <th class="px-6 py-3 text-left">Role</th>
                        <th class="px-6 py-3 text-center">Permissions</th>
                        <th class="px-6 py-3 text-center">Actions</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100">

                    <?php if(!empty($roles)): ?>
                        <?php foreach($roles as $role): ?>
                        <tr class="hover:bg-gray-50 transition">
                            
                            <!-- Role Name -->
                            <td class="px-6 py-4 font-medium text-gray-800 flex items-center gap-3">
                                <span class="w-9 h-9 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center">
                                    <i class="fas fa-user-tag text-sm"></i>
                                </span>
                                <?= ucwords(str_replace('_',' ',$role->role_name)) ?>

                                <?php if($role->role_name == 'super_admin'): ?>
                                    <span class="ml-2 px-2 py-1 text-xs bg-red-100 text-red-600 rounded-full">
                                        Protected
                                    </span>
                                <?php endif; ?>
                            </td>

                            <!-- Permission Column -->
                            <td class="px-6 py-4 text-center">
                                <?php if($role->role_name == 'super_admin'): ?>
                                    <span class="inline-flex items-center gap-2 px-4 py-2 bg-green-50 text-green-600 rounded-lg text-xs font-semibold cursor-not-allowed">
                                        <i class="fas fa-shield-alt"></i>
                                        Full Access
                                    </span>
                                <?php else: ?>
                                    <a href="<?= base_url('admin/AdminRole/permissions/'.$role->id) ?>"
                                       class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-50 text-indigo-600 rounded-lg hover:bg-indigo-100 transition text-xs font-semibold">
                                        <i class="fas fa-key"></i>
                                        Manage Permissions
                                    </a>
                                <?php endif; ?>
                            </td>

                            <!-- Actions -->
                            <td class="px-6 py-4 text-center">
                                <div class="flex items-center justify-center gap-2">

                                    <!-- Edit -->
                                    <a href="<?= base_url('admin/AdminRole/edit/'.$role->id) ?>"
                                       class="w-9 h-9 rounded-lg bg-yellow-50 text-yellow-600 flex items-center justify-center hover:bg-yellow-100 transition"
                                       title="Edit Role">
                                        <i class="fas fa-edit text-sm"></i>
                                    </a>

                                    <!-- Delete -->
                                    <?php if($role->role_name != 'super_admin'): ?>
                                    <form method="post" action="<?= base_url('admin/AdminRole/delete/'.$role->id) ?>" onsubmit="return confirmDelete()" class="inline">
                                        
                                        <!-- CSRF -->
                                        <input type="hidden" 
                                               name="<?= $this->security->get_csrf_token_name(); ?>" 
                                               value="<?= $this->security->get_csrf_hash(); ?>">

                                        <button type="submit"
                                                class="w-9 h-9 rounded-lg bg-red-50 text-red-600 flex items-center justify-center hover:bg-red-100 transition"
                                                title="Delete Role">
                                            <i class="fas fa-trash-alt text-sm"></i>
                                        </button>
                                    </form>
                                    <?php endif; ?>

                                </div>
                            </td>

                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="3" class="text-center py-10 text-gray-500">
                                <i class="fas fa-info-circle mr-2"></i>
                                No roles found. Click “Create Role” to add one.
                            </td>
                        </tr>
                    <?php endif; ?>

                </tbody>
            </table>
        </div>

    </div>
</div>

<!-- Delete Confirm -->
<script>
function confirmDelete() {
    return confirm("⚠️ This role will be permanently deleted.\n\nDo you want to continue?");
}
</script>