<!-- TinyMCE for html_layout -->
<script src="https://cdn.tiny.cloud/1/jxhyjhicc4somdh05bjumsfnalcuzz4uej01mbbbizec4fov/tinymce/5/tinymce.min.js"></script>

<div class="bg-gradient-to-br from-gray-50 to-white p-4 md:p-6 rounded-2xl shadow-lg">
  <div class="max-w-6xl mx-auto space-y-8">

    <!-- Edit Template Form -->
    <div id="resume_template_form_wrapper" class="bg-white p-4 md:p-6 rounded-xl shadow-sm border border-gray-200">
      <div class="flex flex-col sm:flex-row sm:items-center justify-between border-b pb-4 mb-6 gap-4">
        <div class="flex-1">
          <h3 class="text-xl md:text-2xl font-bold text-gray-800">
            Edit Template: <?= htmlspecialchars($template['name']) ?>
          </h3>
          <p class="text-sm text-gray-600 mt-1">Update your resume template details</p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
          <button type="button" onclick="previewTemplate()" 
                  class="inline-flex items-center px-3 py-2 md:px-4 md:py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 transition">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
            </svg>
            <span class="hidden sm:inline">Preview</span>
          </button>
          <button type="button" onclick="resetForm()" 
                  class="inline-flex items-center px-3 py-2 md:px-4 md:py-2 border border-red-300 text-sm font-medium rounded-md text-red-700 bg-white hover:bg-red-50 transition">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
            </svg>
            <span class="hidden sm:inline">Reset</span>
          </button>
        </div>
      </div>

      <form id="form_resume_template" enctype="multipart/form-data" method="post" class="space-y-6">
        <!-- Hidden ID -->
        <input type="hidden" name="template_id" value="<?= $template['template_id'] ?>">

        <!-- Basic Information Section -->
        <div class="bg-blue-50 p-3 md:p-4 rounded-lg">
          <h4 class="text-base md:text-lg font-semibold text-blue-800 mb-4">Basic Information</h4>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">           

            <!-- Template Name -->
            <div class="col-span-1">
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Template Name <span class="text-red-500">*</span>
              </label>
              <input type="text" name="name" list="template_name_suggestions"
                     class="w-full border-gray-300 rounded-lg shadow-sm px-3 py-2 text-sm md:text-base focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                     placeholder="Enter template name" 
                     value="<?= htmlspecialchars($template['name']) ?>" required>
              <datalist id="template_name_suggestions">
                <option value="Classic">
                <option value="Professional">
                <option value="Modern">
                <option value="Creative">
                <option value="Executive">
                <option value="Minimalist">
                <option value="Corporate">
                <option value="ATS Friendly">
                <option value="Two Column">
                <option value="Simple">
              </datalist>
            </div>

            <!-- Layout Type -->
            <div class="col-span-1">
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Layout <span class="text-red-500">*</span>
              </label>
              <select name="layout_type" required 
                      class="w-full border-gray-300 rounded-lg shadow-sm px-3 py-2 text-sm md:text-base focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                <option value="">Select Layout</option>			
                <option value="Picture" <?= $template['layout_type'] == 'Picture' ? 'selected' : '' ?>>Picture</option>
                <option value="Word" <?= $template['layout_type'] == 'Word' ? 'selected' : '' ?>>Word</option>
                <option value="Simple" <?= $template['layout_type'] == 'Simple' ? 'selected' : '' ?>>Simple</option>
                <option value="ATS" <?= $template['layout_type'] == 'ATS' ? 'selected' : '' ?>>ATS</option>
                <option value="Two-column" <?= $template['layout_type'] == 'Two-column' ? 'selected' : '' ?>>Two-column</option>
                <option value="Google Docs" <?= $template['layout_type'] == 'Google Docs' ? 'selected' : '' ?>>Google Docs</option>
              </select>
            </div>

            <!-- Template Description -->
            <div class="col-span-1">
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Description
              </label>
              <textarea name="description" rows="2"
                        class="w-full border-gray-300 rounded-lg shadow-sm px-3 py-2 text-sm md:text-base focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                        placeholder="Brief description of the template"><?= htmlspecialchars($template['description'] ?? '') ?></textarea>
            </div>

            <!-- Template Version -->
            <div class="col-span-1">
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Template Version
              </label>
              <input type="number" name="template_version" value="<?= $template['template_version'] ?? 1 ?>" min="1"
                     class="w-full border-gray-300 rounded-lg shadow-sm px-3 py-2 text-sm md:text-base focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
            </div>

          </div>
        </div>

        <!-- Template Details Section -->
        <div class="bg-green-50 p-3 md:p-4 rounded-lg">
          <h4 class="text-base md:text-lg font-semibold text-green-800 mb-4">Template Details</h4>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
            <!-- Industry -->
            <div class="col-span-1">
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Industry <span class="text-red-500">*</span>
              </label>
              <select name="industry_id" 
                      class="w-full border-gray-300 rounded-lg shadow-sm px-3 py-2 text-sm md:text-base focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition" required>
                <option value="">Select Industry</option>
                <?php foreach ($industries as $ind): ?>
                <option value="<?= $ind['industry_id'] ?>" <?= $template['industry_id'] == $ind['industry_id'] ? 'selected' : '' ?>>
                  <?= $ind['industry_name'] ?>
                </option>
                <?php endforeach; ?>
              </select>
            </div>

            <!-- Experience Level -->
            <div class="col-span-1">
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Experience Level <span class="text-red-500">*</span>
              </label>
              <select name="experience_level" 
                      class="w-full border-gray-300 rounded-lg shadow-sm px-3 py-2 text-sm md:text-base focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition" required>
                <option value="both" <?= $template['experience_level'] == 'both' ? 'selected' : '' ?>>Both Fresher & Experienced</option>
                <option value="fresher" <?= $template['experience_level'] == 'fresher' ? 'selected' : '' ?>>Fresher Only</option>
                <option value="experienced" <?= $template['experience_level'] == 'experienced' ? 'selected' : '' ?>>Experienced Only</option>
              </select>
            </div>

            <!-- Template Type -->
            <div class="col-span-1">
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Template Type
              </label>
              <select name="template_type" 
                      class="w-full border-gray-300 rounded-lg shadow-sm px-3 py-2 text-sm md:text-base focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                <option value="free" <?= $template['template_type'] == 'free' ? 'selected' : '' ?>>Free</option>
                <option value="paid" <?= $template['template_type'] == 'paid' ? 'selected' : '' ?>>Paid</option>
              </select>
            </div>

            <!-- Preview Image -->
            <div class="col-span-1 md:col-span-2">
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Preview Image
              </label>
              <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4">
                <input type="file" name="preview_image" 
                       class="flex-1 border-gray-300 rounded-lg shadow-sm px-3 py-2 text-sm md:text-base focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition file:mr-4 file:py-1.5 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
                       accept="image/jpeg,image/jpg,image/png,image/gif,image/webp">
                <div class="text-xs text-gray-500">
                  <p>Supported formats: JPEG, PNG, GIF, WebP</p>
                  <p>Max size: 5MB</p>
                </div>
              </div>
              
              <?php if (!empty($template['preview_image'])): ?>
                <div class="mt-3">
                  <p class="text-sm text-gray-600 mb-1">Current preview:</p>
                  <img src="<?= base_url($template['preview_image']) ?>" 
                       class="w-32 h-32 rounded border object-cover shadow-sm">
                  <p class="text-xs text-gray-500 mt-1">Leave empty to keep existing image</p>
                </div>
              <?php endif; ?>
            </div>
          </div>
        </div>

        <!-- Advanced Configuration Section -->
        <div class="bg-purple-50 p-3 md:p-4 rounded-lg">
          <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-4 gap-2">
            <h4 class="text-base md:text-lg font-semibold text-purple-800">Advanced Configuration</h4>
            <button type="button" onclick="toggleAdvancedConfig()" 
                    class="text-sm text-purple-600 hover:text-purple-800 font-medium inline-flex items-center">
              <svg id="advanced_toggle_icon" class="w-4 h-4 mr-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
              </svg>
              <span>Toggle Advanced Options</span>
            </button>
          </div>
          
          <div id="advanced_config_section" class="<?= empty($template['layout_config']) && empty($template['schema_json']) && empty($template['zones_supported']) ? 'hidden' : '' ?> space-y-4 transition-all duration-300">
            <!-- Layout Config JSON -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Layout Configuration (JSON)
                <span class="text-xs text-gray-500 font-normal ml-2">Optional: Advanced layout settings</span>
              </label>
              <textarea name="layout_config" rows="5"
                        class="w-full border-gray-300 rounded-lg shadow-sm px-3 py-2 text-sm font-mono focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition"><?= 
                !empty($template['layout_config']) ? htmlspecialchars(json_encode(json_decode($template['layout_config']), JSON_PRETTY_PRINT)) : 
                '{
  "columns": 2,
  "gap": "24px",
  "widths": {
    "primary": "35%",
    "secondary": "65%"
  },
  "zones": {
    "primary": [
      "summary",
      "skills",
      "languages"
    ],
    "secondary": [
      "experience",
      "education",
      "projects",
      "certifications"
    ]
  }
}' 
              ?></textarea>
            </div>

            <!-- Schema JSON -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Schema JSON
                <span class="text-xs text-gray-500 font-normal ml-2">Optional: Structured data for SEO</span>
              </label>
              <textarea name="schema_json" rows="5"
                        class="w-full border-gray-300 rounded-lg shadow-sm px-3 py-2 text-sm font-mono focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition"><?= 
                !empty($template['schema_json']) ? htmlspecialchars(json_encode(json_decode($template['schema_json']), JSON_PRETTY_PRINT)) : 
                '{
  "@context": "https://schema.org",
  "@type": "ResumeTemplate",
  "name": "Modern Professional",
  "industry": "IT",
  "experienceLevel": "experienced",
  "templateCategory": "Two-column",
  "required": [
    "name",
    "email"
  ],
  "sections": {
    "header": "object",
    "summary": "string",
    "experience": "array",
    "education": "array",
    "projects": "array",
    "skills": "array",
    "languages": "array",
    "certifications": "array",
    "achievements": "array",
    "hobbies": "array"
  }
}' 
              ?></textarea>
            </div>

            <!-- Zones Supported -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Zones/Regions Supported (JSON)
                <span class="text-xs text-gray-500 font-normal ml-2">Optional: Define template zones</span>
              </label>
              <textarea name="zones_supported" rows="5"
                        class="w-full border-gray-300 rounded-lg shadow-sm px-3 py-2 text-sm font-mono focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition"><?= 
                !empty($template['zones_supported']) ? htmlspecialchars(json_encode(json_decode($template['zones_supported']), JSON_PRETTY_PRINT)) : 
                '[
  "header",
  "summary",
  "experience",
  "education",
  "projects",
  "skills",
  "languages",
  "certifications",
  "achievements",
  "hobbies"
]' 
              ?></textarea>
            </div>
          </div>
        </div>

        <!-- HTML Layout Section -->
        <div class="bg-indigo-50 p-3 md:p-4 rounded-lg">
          <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-4 gap-2">
            <h4 class="text-base md:text-lg font-semibold text-indigo-800">
              HTML Layout <span class="text-red-500">*</span>
            </h4>
            <button type="button" onclick="previewTemplate()" 
                    class="text-sm text-indigo-600 hover:text-indigo-800 font-medium inline-flex items-center px-3 py-1.5 bg-indigo-100 rounded-md">
              <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
              </svg>
              Preview Template
            </button>
          </div>
          
          <textarea id="html_layout" name="html_layout" rows="8"
                    class="w-full border-gray-300 rounded-lg shadow-sm px-3 py-2 text-sm md:text-base focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition resize-y"
                    placeholder="Enter your HTML template code here..."><?= htmlspecialchars($template['html_layout'] ?? '') ?></textarea>
          <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mt-2 text-xs text-gray-500">
            <p>Use placeholders like {{name}}, {{email}}, {{phone}} for dynamic data</p>
            <p>Shortcut: Ctrl+S to save</p>
          </div>
        </div>

        <!-- Template Settings Section -->
        <div class="bg-yellow-50 p-3 md:p-4 rounded-lg">
          <h4 class="text-base md:text-lg font-semibold text-yellow-800 mb-4">Template Settings</h4>
          <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
            <label class="inline-flex items-center p-2 bg-white rounded border hover:bg-gray-50 cursor-pointer">
              <input type="checkbox" name="is_premium" value="1" <?= !empty($template['is_premium']) ? 'checked' : '' ?>
                     class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
              <span class="ml-2 text-sm font-medium text-gray-700">Premium Template</span>
            </label>
            <label class="inline-flex items-center p-2 bg-white rounded border hover:bg-gray-50 cursor-pointer">
              <input type="checkbox" name="is_active" value="1" <?= empty($template['is_active']) ? '' : 'checked' ?>
                     class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
              <span class="ml-2 text-sm font-medium text-gray-700">Active Template</span>
            </label>
          </div>
        </div>

        <!-- Hidden Fields -->
        <input type="hidden" name="created_by" value="<?= isset($_SESSION['user_id']) ? $_SESSION['user_id'] : '' ?>">

        <!-- Submit Button -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center pt-4 border-t gap-4">
          <div class="text-sm text-gray-500">
            <p>All fields marked with <span class="text-red-500">*</span> are required</p>
            <p class="text-xs mt-1">JSON fields are optional for advanced configuration</p>
          </div>
          <div class="flex flex-wrap gap-3">
            <button type="submit" id="submit_template" 
                    class="inline-flex items-center px-6 py-2.5 bg-gradient-to-r from-blue-600 to-blue-700 text-white font-semibold rounded-lg shadow-md hover:from-blue-700 hover:to-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition transform hover:-translate-y-0.5">
              <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
              </svg>
              Update Template
            </button>
          </div>
        </div>
        
        <!-- Form Response -->
        <div id="formResponse" class="mt-4 p-3 rounded-lg hidden"></div>
      </form>
    </div>
  </div>
</div>

<script>
// ===============================
// EDIT TEMPLATE UI MANAGEMENT
// ===============================
class EditTemplateManager {
    static showNotification(message, type = 'success') {
        const formResponse = document.getElementById('formResponse');
        if (!formResponse) return;

        formResponse.innerHTML = '';
        formResponse.className = 'mt-4 p-4 rounded-lg border transition-all duration-300';
        
        if (!message) {
            formResponse.classList.add('hidden');
            return;
        }

        const typeClasses = {
            success: 'bg-green-50 text-green-800 border-green-200',
            error: 'bg-red-50 text-red-800 border-red-200',
            info: 'bg-blue-50 text-blue-800 border-blue-200',
            warning: 'bg-yellow-50 text-yellow-800 border-yellow-200'
        };

        formResponse.className = `mt-4 p-4 rounded-lg border transition-all duration-300 ${typeClasses[type] || typeClasses.info}`;
        formResponse.innerHTML = `
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    ${this.getNotificationIcon(type)}
                </div>
                <div class="ml-3 flex-1">
                    <p class="text-sm font-medium">${message}</p>
                </div>
                <button type="button" class="ml-auto -mx-1.5 -my-1.5 rounded-lg p-1.5 inline-flex h-8 w-8 hover:bg-white hover:bg-opacity-50 transition" onclick="EditTemplateManager.hideNotification()">
                    <span class="sr-only">Dismiss</span>
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                    </svg>
                </button>
            </div>
        `;
        formResponse.classList.remove('hidden');

        if (type === 'success') {
            setTimeout(() => this.hideNotification(), 5000);
        }
    }

    static getNotificationIcon(type) {
        const icons = {
            success: `<svg class="w-5 h-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
            </svg>`,
            error: `<svg class="w-5 h-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
            </svg>`,
            info: `<svg class="w-5 h-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
            </svg>`,
            warning: `<svg class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
            </svg>`
        };
        return icons[type] || icons.info;
    }

    static hideNotification() {
        const formResponse = document.getElementById('formResponse');
        if (formResponse) formResponse.classList.add('hidden');
    }

    static toggleLoading(show) {
        const submitBtn = document.getElementById('submit_template');
        if (!submitBtn) return;

        if (show) {
            submitBtn.disabled = true;
            submitBtn.innerHTML = `
                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Updating...
            `;
        } else {
            submitBtn.disabled = false;
            submitBtn.innerHTML = `
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Update Template
            `;
        }
    }

    static validateForm() {       
        const templateName = document.querySelector('input[name="name"]');
        const layoutType = document.querySelector('select[name="layout_type"]');
        const industry = document.querySelector('select[name="industry_id"]');
        const experienceLevel = document.querySelector('select[name="experience_level"]');
        const htmlLayout = document.getElementById('html_layout');

        if (!templateName.value.trim()) {
            this.showNotification('Please enter template name', 'error');
            templateName.focus();
            return false;
        }

        if (!layoutType.value) {
            this.showNotification('Please select layout type', 'error');
            layoutType.focus();
            return false;
        }

        if (!industry.value) {
            this.showNotification('Please select industry', 'error');
            industry.focus();
            return false;
        }

        if (!experienceLevel.value) {
            this.showNotification('Please select experience level', 'error');
            experienceLevel.focus();
            return false;
        }

        if (!htmlLayout.value.trim()) {
            this.showNotification('Please enter HTML layout', 'error');
            htmlLayout.focus();
            return false;
        }

        return true;
    }
}

// ===============================
// HELPER FUNCTIONS
// ===============================

function toggleAdvancedConfig() {
    const section = document.getElementById('advanced_config_section');
    const icon = document.getElementById('advanced_toggle_icon');
    
    if (section) {
        section.classList.toggle('hidden');
        if (icon) {
            icon.style.transform = section.classList.contains('hidden') ? 'rotate(0deg)' : 'rotate(180deg)';
        }
    }
}

function previewTemplate() {
    let htmlContent = '';
    if (tinymce.get('html_layout')) {
        htmlContent = tinymce.get('html_layout').getContent();
    } else {
        htmlContent = document.getElementById('html_layout').value;
    }
    
    if (htmlContent.trim()) {
        const previewWindow = window.open('', '_blank');
        previewWindow.document.write(`
            <!DOCTYPE html>
            <html>
            <head>
                <title>Template Preview - <?= htmlspecialchars($template['name']) ?></title>
                <style>
                    body { font-family: Arial, sans-serif; padding: 20px; margin: 0; background: #f5f5f5; }
                    .template-preview { max-width: 800px; margin: 0 auto; background: white; padding: 40px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); border-radius: 8px; }
                    .placeholder { background-color: #ffeb3b; padding: 2px 4px; border-radius: 3px; border: 1px dashed #ff9800; font-weight: bold; }
                </style>
            </head>
            <body>
                <div class="template-preview">
                    ${htmlContent.replace(/\{\{(\w+)\}\}/g, '<span class="placeholder">[$1]</span>')}
                </div>
            </body>
            </html>
        `);
        previewWindow.document.close();
    } else {
        EditTemplateManager.showNotification('Please enter HTML content to preview', 'warning');
    }
}

function resetForm() {
    if (confirm('Are you sure you want to reset all changes?')) {
        location.reload();
    }
}

// ===============================
// DOCUMENT READY
// ===============================
$(document).ready(function () {
    // Initialize TinyMCE
    tinymce.init({
        selector: '#html_layout',
        menubar: false,
        statusbar: false,
        plugins: 'code',
        toolbar: 'code',
        height: 400,
        setup: function(editor) {
            editor.on('change', function() {
                editor.save();
            });
        }
    });

    // Form submission with CSRF using global functions
    $('#form_resume_template').on('submit', function (e) {
        e.preventDefault();
        
        if (!EditTemplateManager.validateForm()) return;

        if (tinymce.get('html_layout')) tinymce.triggerSave();

        EditTemplateManager.toggleLoading(true);
        EditTemplateManager.showNotification('Updating template...', 'info');

        const formData = new FormData(this);
        // Manually append CSRF token using global functions
        const csrfName = getCSRFName();
        const csrfToken = getCSRFToken();
        formData.append(csrfName, csrfToken);

        // Validate JSON fields
        try {
            const layoutConfig = $('textarea[name="layout_config"]').val();
            const schemaJson = $('textarea[name="schema_json"]').val();
            const zonesSupported = $('textarea[name="zones_supported"]').val();
            if (layoutConfig) JSON.parse(layoutConfig);
            if (schemaJson) JSON.parse(schemaJson);
            if (zonesSupported) JSON.parse(zonesSupported);
        } catch (error) {
            EditTemplateManager.toggleLoading(false);
            EditTemplateManager.showNotification('Invalid JSON in advanced configuration fields', 'error');
            return;
        }

        $.ajax({
            url: '<?= base_url("admin/features/ResumeTemplates/update") ?>',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            dataType: 'json',
            headers: {
                'X-CSRF-TOKEN': getCSRFToken()  // Also send as header
            },
            success: function (response) {
                // Update CSRF token from response using global function
                if (response.csrf_token && response.csrf_name) {
                    updateCSRFToken(response.csrf_token, response.csrf_name);
                } else if (response.csrf_token) {
                    // Fallback if only token is provided
                    updateCSRFToken(response.csrf_token, getCSRFName());
                }
                
                EditTemplateManager.toggleLoading(false);

                if (response.success) {
                    EditTemplateManager.showNotification(response.message || 'Template updated successfully!', 'success');
                    const newName = $('input[name="name"]').val();
                    document.title = document.title.replace(/Edit Template.*/, `Edit Template: ${newName}`);
                    const heading = document.querySelector('h3');
                    if (heading) heading.innerHTML = `Edit Template: ${newName}`;
                } else {
                    EditTemplateManager.showNotification(response.message || 'Failed to update template. Please try again.', 'error');
                }
            },
            error: function (xhr, status, error) {
                EditTemplateManager.toggleLoading(false);
                let errorMessage = 'Server error occurred. Please try again.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                } else if (xhr.status === 413) {
                    errorMessage = 'File size too large. Please choose a smaller image.';
                } else if (xhr.status === 415) {
                    errorMessage = 'Unsupported file type. Please use JPEG, PNG, GIF, or WebP images.';
                }
                EditTemplateManager.showNotification(errorMessage, 'error');
                console.error('AJAX Error:', error, xhr.responseText);
            }
        });
    });

    // File input validation & preview
    $('input[type="file"]').on('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            if (file.size > 5 * 1024 * 1024) {
                EditTemplateManager.showNotification('File size must be less than 5MB', 'error');
                $(this).val('');
                return;
            }
            const validTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
            if (!validTypes.includes(file.type)) {
                EditTemplateManager.showNotification('Please select a valid image file (JPEG, PNG, GIF, WebP)', 'error');
                $(this).val('');
                return;
            }

            const reader = new FileReader();
            reader.onload = function(e) {
                const preview = `
                    <div id="imagePreviewModal" class="fixed inset-0 bg-black bg-opacity-60 z-50 flex items-center justify-center p-3">
                        <div class="bg-white rounded-xl w-full max-w-3xl max-h-[95vh] flex flex-col overflow-hidden">
                            <div class="flex items-center justify-between px-4 py-3 border-b shrink-0">
                                <h3 class="text-base font-semibold text-gray-800">Image Preview</h3>
                                <button onclick="$('#imagePreviewModal').remove()" class="text-gray-500 hover:text-gray-800">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </div>
                            <div class="flex-1 overflow-auto p-4 bg-gray-50">
                                <img src="${e.target.result}" alt="Preview" class="w-full h-auto max-h-full object-contain rounded-lg shadow-md block mx-auto">
                            </div>
                            <div class="px-4 py-3 border-t flex justify-end shrink-0 bg-white">
                                <button onclick="$('#imagePreviewModal').remove()" class="px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700">Close</button>
                            </div>
                        </div>
                    </div>
                `;
                $('body').append(preview);
            };
            reader.readAsDataURL(file);
            EditTemplateManager.showNotification('New image selected successfully', 'success');
        }
    });

    // Keyboard shortcuts
    $(document).on('keydown', function(e) {
        if ((e.ctrlKey || e.metaKey) && e.key === 's') {
            e.preventDefault();
            $('#form_resume_template').submit();
        }
        if ((e.ctrlKey || e.metaKey) && e.key === 'p') {
            e.preventDefault();
            previewTemplate();
        }
    });
});

// Responsive TinyMCE adjustment
$(window).on('resize', function() {
    if (window.innerWidth < 768 && tinymce.get('html_layout')) {
        tinymce.get('html_layout').setContent(tinymce.get('html_layout').getContent());
    }
});

// Prevent Enter key from submitting form inside textareas
$('textarea').on('keydown', function(e) {
    if (e.key === 'Enter' && e.ctrlKey) {
        $(this).val($(this).val() + '\n');
    } else if (e.key === 'Enter' && !e.ctrlKey) {
        e.preventDefault();
    }
});
</script>

<style>
/* Responsive adjustments */
@media (max-width: 640px) {
    .grid-cols-1 > div {
        width: 100%;
    }
    
    textarea {
        font-size: 14px;
    }
    
    input, select {
        font-size: 16px !important;
    }
    
    .template-preview {
        padding: 20px !important;
    }
}

/* Placeholder styles for preview */
.placeholder {
    background-color: #ffeb3b;
    padding: 2px 4px;
    border-radius: 3px;
    border: 1px dashed #ff9800;
    font-weight: bold;
}

/* Smooth transitions */
.transition {
    transition: all 0.2s ease-in-out;
}

/* Custom scrollbar for textareas */
textarea::-webkit-scrollbar {
    width: 8px;
}

textarea::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 4px;
}

textarea::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 4px;
}

textarea::-webkit-scrollbar-thumb:hover {
    background: #555;
}

/* File input styling */
input[type="file"]::-webkit-file-upload-button {
    cursor: pointer;
}

/* Focus styles */
:focus {
    outline: 2px solid transparent;
    outline-offset: 2px;
}

/* Animation for form response */
@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

#formResponse:not(.hidden) {
    animation: slideIn 0.3s ease-out;
}

/* Mobile optimizations */
@media (max-width: 768px) {
    .tinymce-container {
        max-height: 300px;
        overflow-y: auto;
    }
    
    .mce-toolbar-grp {
        flex-wrap: wrap;
        max-height: 120px;
        overflow-y: auto;
    }
}

/* JSON field styling */
textarea[name="layout_config"],
textarea[name="schema_json"],
textarea[name="zones_supported"] {
    font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', monospace;
    font-size: 12px;
    line-height: 1.4;
}

/* Current preview image */
.current-preview img {
    border: 2px solid #e5e7eb;
    transition: border-color 0.2s;
}

.current-preview img:hover {
    border-color: #3b82f6;
}
</style>
