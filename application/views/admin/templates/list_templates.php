<div class="bg-gradient-to-br from-gray-50 to-white p-4 sm:p-6 rounded-2xl shadow-lg">
  <div class="max-w-6xl mx-auto space-y-6">

   <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6 p-4 bg-white rounded-xl border border-gray-200 shadow-sm">
      <div>
        <h1 class="text-2xl font-bold text-gray-800">Resume Templates</h1>
        <p class="text-sm text-gray-600 mt-1">Manage your resume template library</p>
      </div>
      <a href="<?= base_url('admin/features/ResumeTemplates/add') ?>" 
         class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-blue-600 to-blue-700 text-white text-sm font-medium rounded-lg hover:from-blue-700 hover:to-blue-800 transition shadow-md">
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
        </svg>
        Add New Template
      </a>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
      <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
        <div class="flex items-center">
          <div class="p-2 bg-blue-100 rounded-lg">
            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
          </div>
          <div class="ml-4">
            <p class="text-sm font-medium text-gray-600">Total Templates</p>
            <p class="text-2xl font-bold text-gray-900"><?= count($templates) ?></p>
          </div>
        </div>
      </div>
      
      <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
        <div class="flex items-center">
          <div class="p-2 bg-green-100 rounded-lg">
            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
          </div>
          <div class="ml-4">
            <p class="text-sm font-medium text-gray-600">Active</p>
            <p class="text-2xl font-bold text-gray-900"><?= count(array_filter($templates, fn($t) => $t['is_active'])) ?></p>
          </div>
        </div>
      </div>
      
      <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
        <div class="flex items-center">
          <div class="p-2 bg-purple-100 rounded-lg">
            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
            </svg>
          </div>
          <div class="ml-4">
            <p class="text-sm font-medium text-gray-600">Premium</p>
            <p class="text-2xl font-bold text-gray-900"><?= count(array_filter($templates, fn($t) => $t['is_premium'])) ?></p>
          </div>
        </div>
      </div>
      
      <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
        <div class="flex items-center">
          <div class="p-2 bg-orange-100 rounded-lg">
            <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
            </svg>
          </div>
          <div class="ml-4">
            <p class="text-sm font-medium text-gray-600">Free</p>
            <p class="text-2xl font-bold text-gray-900"><?= count(array_filter($templates, fn($t) => $t['template_type'] === 'free')) ?></p>
          </div>
        </div>
      </div>
    </div>

    <!-- Table Section -->
    <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
      <!-- Table Container with Responsive Scroll -->
      <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
          <thead class="bg-gray-50 border-b">
            <tr>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Preview</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Template</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden lg:table-cell">Layout Type</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden md:table-cell">Industry</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden sm:table-cell">Type</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden xl:table-cell">Premium</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden md:table-cell">Status</th>
              <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200">
            <?php if (!empty($templates)) : ?>
              <?php foreach ($templates as $index => $row) : ?>
                <tr class="hover:bg-gray-50 transition-colors">
                  <!-- Serial Number -->
                  <td class="px-4 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                    <?= $index + 1 ?>
                  </td>
                  
                  <!-- Preview Image -->
                  <td class="px-4 py-4 whitespace-nowrap">
                    <?php if ($row['preview_image']) : ?>
                      <img src="<?= base_url($row['preview_image']) ?>" 
                           alt="Preview" 
                           class="h-10 w-10 rounded-lg border object-cover shadow-sm">
                    <?php else : ?>
                      <div class="h-10 w-10 rounded-lg border bg-gray-100 flex items-center justify-center">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                      </div>
                    <?php endif; ?>
                  </td>
                  
                  <!-- Template Name & Mobile Details -->
                  <td class="px-4 py-4">
                    <div class="flex flex-col">
                      <span class="text-sm font-medium text-gray-900"><?= htmlspecialchars($row['name']) ?></span>
                      <div class="flex flex-wrap gap-2 mt-1 lg:hidden">
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                          <?= htmlspecialchars($row['layout_type']) ?>
                        </span>
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                          <?= ucfirst($row['template_type']) ?>
                        </span>
                        <?php if ($row['is_premium']) : ?>
                          <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                            Premium
                          </span>
                        <?php endif; ?>
                      </div>
                    </div>
                  </td>
                  
                  <!-- Category (Hidden on mobile) -->
                  <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900 hidden lg:table-cell">
                    <?= htmlspecialchars($row['layout_type']) ?>
                  </td>
                  
                  <!-- Industry (Hidden on mobile) -->
                  <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500 hidden md:table-cell">
                    <?= htmlspecialchars($row['industry_name'] ?? '-') ?>
                  </td>
                  
                  <!-- Type (Hidden on mobile) -->
                  <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500 hidden sm:table-cell">
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium <?= $row['template_type'] === 'paid' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' ?>">
                      <?= ucfirst($row['template_type']) ?>
                    </span>
                  </td>
                  
                  <!-- Premium (Hidden on mobile) -->
                  <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500 hidden xl:table-cell">
                    <?php if ($row['is_premium']) : ?>
                      <span class="inline-flex items-center">
                        <svg class="w-4 h-4 text-green-500 mr-1" fill="currentColor" viewBox="0 0 20 20">
                          <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        Yes
                      </span>
                    <?php else : ?>
                      <span class="text-gray-400">No</span>
                    <?php endif; ?>
                  </td>
                  
                  <!-- Status (Hidden on mobile) -->
                  <td class="px-4 py-4 whitespace-nowrap text-sm hidden md:table-cell">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= $row['is_active'] ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' ?>">
                      <?= $row['is_active'] ? 'Active' : 'Inactive' ?>
                    </span>
                  </td>
                  
                  <!-- Actions -->
                  <td class="px-4 py-4 whitespace-nowrap text-right text-sm font-medium">
                    <div class="flex justify-end items-center space-x-2">
                      <!-- Edit Button -->
                      <a target="_blank" href="<?= base_url('admin/features/ResumeTemplates/edit/' . $row['template_id']) ?>" 
                         class="inline-flex items-center p-2 text-blue-600 hover:text-blue-900 hover:bg-blue-50 rounded-lg transition"
                         title="Edit Template">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        <span class="sr-only">Edit</span>
                      </a>
                      
                      <!-- Preview Button -->
                      <a target="_blank" href="<?= 'http://resume.talentsjobs.in/resume-preview/' . $row['template_id']; ?>" 
                         
                         class="inline-flex items-center p-2 text-indigo-600 hover:text-indigo-900 hover:bg-indigo-50 rounded-lg transition"
                         title="Preview Template">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                        <span class="sr-only">Preview</span>
                      </a>
                      
                      <!-- Delete Button -->
                      <a href="<?= base_url('admin/features/ResumeTemplates/delete/' . $row['template_id']) ?>" 
                         class="inline-flex items-center p-2 text-red-600 hover:text-red-900 hover:bg-red-50 rounded-lg transition"
                         onclick="return confirm('Are you sure you want to delete this template?')"
                         title="Delete Template">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                        <span class="sr-only">Delete</span>
                      </a>
                    </div>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php else : ?>
              <tr>
                <td colspan="9" class="px-4 py-8 text-center">
                  <div class="flex flex-col items-center justify-center text-gray-500">
                    <svg class="w-16 h-16 mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <p class="text-lg font-medium mb-2">No templates found</p>
                    <p class="text-sm mb-4">Get started by creating your first resume template</p>
                    <a href="<?= base_url('admin/features/ResumeTemplates/add') ?>" 
                       class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition">
                      <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                      </svg>
                      Add New Template
                    </a>
                  </div>
                </td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Mobile Card View (Alternative for very small screens) -->
    <div class="lg:hidden space-y-4">
      <?php if (!empty($templates)) : ?>
        <?php foreach ($templates as $index => $row) : ?>
          <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-4">
            <div class="flex items-start justify-between mb-3">
              <div class="flex items-center space-x-3">
                <?php if ($row['preview_image']) : ?>
                  <img src="<?= base_url($row['preview_image']) ?>" 
                       alt="Preview" 
                       class="h-12 w-12 rounded-lg border object-cover">
                <?php else : ?>
                  <div class="h-12 w-12 rounded-lg border bg-gray-100 flex items-center justify-center">
                    <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                  </div>
                <?php endif; ?>
                <div>
                  <h3 class="text-sm font-semibold text-gray-900"><?= htmlspecialchars($row['name']) ?></h3>
                  <p class="text-xs text-gray-500"><?= htmlspecialchars($row['category']) ?></p>
                </div>
              </div>
              <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium <?= $row['is_active'] ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' ?>">
                <?= $row['is_active'] ? 'Active' : 'Inactive' ?>
              </span>
            </div>
            
            <div class="grid grid-cols-2 gap-4 mb-3 text-xs">
              <div>
                <span class="text-gray-500">Industry:</span>
                <span class="font-medium ml-1"><?= htmlspecialchars($row['industry_name'] ?? '-') ?></span>
              </div>
              <div>
                <span class="text-gray-500">Type:</span>
                <span class="font-medium ml-1"><?= ucfirst($row['template_type']) ?></span>
              </div>
              <div>
                <span class="text-gray-500">Premium:</span>
                <span class="font-medium ml-1"><?= $row['is_premium'] ? 'Yes' : 'No' ?></span>
              </div>
            </div>
            
            <div class="flex justify-between items-center pt-3 border-t">
              <span class="text-xs text-gray-500">Template #<?= $index + 1 ?></span>
              <div class="flex space-x-2">
                <a target="_blank" href="<?= base_url('admin/features/ResumeTemplates/edit/' . $row['template_id']) ?>" 
                   class="inline-flex items-center px-3 py-1 text-xs bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                  Edit
                </a>
                <a href="<?= 'http://resume.talentsjobs.in/resume-preview/' . $row['template_id']; ?>" 
                   target="_blank" 
                   class="inline-flex items-center px-3 py-1 text-xs bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
                  Preview
                </a>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>

  </div>
</div>

<style>
@media (max-width: 1024px) {
  .table-container {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
  }
  
  table {
    min-width: 800px;
  }
}

/* Smooth scrolling for mobile */
.table-container {
  scrollbar-width: thin;
  scrollbar-color: #cbd5e0 #f7fafc;
}

.table-container::-webkit-scrollbar {
  height: 8px;
}

.table-container::-webkit-scrollbar-track {
  background: #f7fafc;
  border-radius: 4px;
}

.table-container::-webkit-scrollbar-thumb {
  background: #cbd5e0;
  border-radius: 4px;
}

.table-container::-webkit-scrollbar-thumb:hover {
  background: #a0aec0;
}
</style>