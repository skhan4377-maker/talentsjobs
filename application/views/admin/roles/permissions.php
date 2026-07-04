<div class="max-w-6xl">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">

        <!-- Header -->
        <div class="px-6 py-4 border-b bg-gradient-to-r from-indigo-50 to-blue-50">
            <h2 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
                <i class="fas fa-key text-indigo-600"></i>
                Assign Permissions
            </h2>
            <p class="text-sm text-gray-600 mt-1">
                Role:
                <span class="font-semibold text-indigo-700">
                    <?= ucwords(str_replace('_',' ',$role->role_name)) ?>
                </span>

                <?php if($role->role_name === 'super_admin'): ?>
                    <span class="ml-2 px-2 py-1 text-xs bg-red-100 text-red-600 rounded-full">
                        Protected Role
                    </span>
                <?php endif; ?>
            </p>
        </div>

        <?php if($role->role_name === 'super_admin'): ?>
            <!-- Super Admin Notice -->
            <div class="p-6">
                <div class="bg-yellow-50 border border-yellow-200 text-yellow-700 px-4 py-3 rounded-lg">
                    <i class="fas fa-lock mr-2"></i>
                    Super Admin has full system access. Permissions cannot be modified.
                </div>
            </div>
        <?php else: ?>

        <!-- Form -->
        <form method="post" action="<?= base_url('admin/AdminRole/save_permissions') ?>" class="p-6">
    
            <!-- CSRF Token -->
            <input type="hidden" 
                   name="<?= $this->security->get_csrf_token_name(); ?>" 
                   value="<?= $this->security->get_csrf_hash(); ?>">

            <input type="hidden" name="role_id" value="<?= $role->id ?>">

            <!-- Select All -->
            <div class="flex items-center justify-between mb-4">
                <label class="flex items-center gap-2 cursor-pointer select-none">
                    <input type="checkbox" id="selectAll"
                           class="w-4 h-4 text-indigo-600 rounded">
                    <span class="text-sm font-medium text-gray-700">Select All Permissions</span>
                </label>

                <span class="text-xs text-gray-500">
                    Total Permissions: <?= count($permissions) ?>
                </span>
            </div>

            <!-- Permissions Grid -->
            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
                <?php foreach($permissions as $perm): ?>
                <label class="permission-card group relative flex items-start gap-3 p-4 border border-gray-200 rounded-lg cursor-pointer hover:border-indigo-300 hover:bg-indigo-50 transition">
                    
                    <input type="checkbox"
                           name="permissions[]"
                           value="<?= $perm->id ?>"
                           <?= in_array($perm->id,$assigned) ? 'checked' : '' ?>
                           class="perm-checkbox mt-1 w-4 h-4 text-indigo-600 rounded">

                    <div>
                        <p class="text-sm font-semibold text-gray-800 group-hover:text-indigo-700">
                            <?= htmlspecialchars($perm->perm_name) ?>
                        </p>
                        <p class="text-xs text-gray-500">
                            Key: <?= htmlspecialchars($perm->perm_key) ?>
                        </p>
                    </div>

                    <i class="fas fa-shield-alt absolute top-3 right-3 text-gray-200 group-hover:text-indigo-200"></i>
                </label>
                <?php endforeach; ?>
            </div>

            <!-- Buttons -->
            <div class="flex items-center justify-between mt-8 pt-6 border-t">
                <a href="<?= base_url('admin/AdminRole') ?>"
                   class="text-sm text-gray-600 hover:text-gray-800">
                   ← Back to Roles
                </a>

                <button type="submit"
                        class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2.5 rounded-lg shadow-sm hover:shadow transition flex items-center gap-2">
                    <i class="fas fa-save"></i>
                    Save Permissions
                </button>
            </div>

        </form>
        <?php endif; ?>

    </div>
</div>

<!-- Select All Script -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    const selectAll = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('.perm-checkbox');

    if(selectAll){
        selectAll.addEventListener('change', function () {
            checkboxes.forEach(cb => cb.checked = selectAll.checked);
        });

        checkboxes.forEach(cb => {
            cb.addEventListener('change', function () {
                const allChecked = [...checkboxes].every(c => c.checked);
                selectAll.checked = allChecked;
            });
        });
    }
});
</script>