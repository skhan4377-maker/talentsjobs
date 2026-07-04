<?php if ($this->session->flashdata('plan_activation_success')): ?>
<div id="successMessage"
     class="fixed top-5 right-5 z-50 bg-green-100 border border-green-400 text-green-700 px-6 py-4 rounded-lg shadow-lg flex items-center gap-3">
  <i class="fas fa-check-circle text-green-600"></i>
  <span>
    <strong>Success!</strong>
    <?= $this->session->flashdata('plan_activation_success') ?>
  </span>
  <button onclick="document.getElementById('successMessage').remove()"
          class="ml-2 text-green-700 hover:text-green-900">✖</button>
</div>
<script>
setTimeout(function(){
    let msg = document.getElementById('successMessage');
    if(msg) msg.remove();
},4000);
</script>
<?php endif; ?>

<?php if ($status == 'active' && !$hasActivePlan && !empty($freePlan)): ?>
<div id="planActivationModal"
     class="fixed inset-0 z-[1100] hidden items-center justify-center bg-black bg-opacity-50 px-4">
  <div class="bg-white rounded-2xl shadow-2xl max-w-lg w-full mx-auto p-6 sm:p-8 relative max-h-[90vh] overflow-y-auto">
    <button id="closeModal"
            type="button"
            class="absolute top-4 right-4 text-gray-500 hover:text-gray-700">✕</button>
    <div class="text-center mb-6">
      <div class="inline-flex items-center justify-center w-14 h-14 bg-blue-100 rounded-full mb-4">
        <i class="fas fa-gift text-blue-600 text-xl"></i>
      </div>
      <h2 class="text-2xl font-bold text-gray-800 mb-1">
        <span class="text-blue-600"><?= $freePlan['job_post_limit'] ?></span> Free Job Posts
      </h2>
      <p class="text-gray-600">Exclusive New Member Benefit</p>
    </div>
    <div class="bg-blue-50 rounded-lg p-4 mb-6 text-center text-sm text-gray-700">
      Full access to
      <strong><?= $freePlan['plan_name'] ?></strong>
      for
      <strong><?= $freePlan['plan_validity_days'] ?> days</strong>
    </div>
    <div class="grid grid-cols-2 gap-4 mb-6 text-sm">
      <div>
        <p class="font-semibold text-gray-900">CV Access</p>
        <p class="text-gray-600"><?= number_format($freePlan['cv_view_limit']) ?> views</p>
      </div>
      <div>
        <p class="font-semibold text-gray-900">Candidate Search</p>
        <p class="text-gray-600"><?= number_format($freePlan['search_limit']) ?> results</p>
      </div>
      <div>
        <p class="font-semibold text-gray-900">Validity</p>
        <p class="text-gray-600"><?= $freePlan['plan_validity_days'] ?> days</p>
      </div>
      <div>
        <p class="font-semibold text-gray-900">Total Value</p>
        <p class="text-gray-600">₹<?= number_format($freePlan['price'],2) ?></p>
      </div>
    </div>
    <form action="<?= base_url('employer/PostJobController/activateFreePlan') ?>" method="post">
      <input type="hidden"
             name="<?= $this->security->get_csrf_token_name(); ?>"
             value="<?= $this->security->get_csrf_hash(); ?>">
      <button type="submit"
              class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 rounded-lg">
        Activate Free Plan
      </button>
    </form>
  </div>
</div>
<script>
document.addEventListener('DOMContentLoaded',function(){
  const modal = document.getElementById('planActivationModal');
  const close = document.getElementById('closeModal');
  if(modal) modal.classList.remove('hidden'), modal.classList.add('flex');
  if(close) close.addEventListener('click',function(){
    modal.classList.add('hidden');
    modal.classList.remove('flex');
  });
});
</script>
<?php endif; ?>

<!-- Main container with extra bottom padding on mobile -->
<div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-4 md:p-6 mb-8 pb-20 md:pb-6">
  <!-- Progress Indicator -->
  <div class="mb-6 overflow-x-auto pb-3">
    <div class="flex justify-between items-center min-w-[300px]">
      <?php for($i=1; $i<=4; $i++): ?>
      <div class="flex flex-col items-center mx-2">
        <div class="w-7 h-7 mb-2 rounded-full bg-blue-500 text-white flex items-center justify-center text-sm font-medium transition-all ring-2 ring-blue-200"><?= $i ?></div>
        <span class="text-xs text-center text-gray-600 dark:text-gray-300 whitespace-nowrap"><?= ['Job Details', 'Salary & Qualifications', 'Application Info', 'Review'][$i-1] ?></span>
      </div>
      <?php endfor; ?>
    </div>
  </div>

  <!-- Form Container -->
  <form id="jobPostForm" class="space-y-4">
    <!-- CSRF Token -->
    <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" id="csrf_token">
    <input type="hidden" name="job_id" id="job_id" value="<?=$post_detail->job_id ?? '' ?>">

    <!-- Step 1: Job Details -->
    <div class="step transition-opacity duration-300 ease-in-out space-y-4" style="display: block;">
      <div class="job-profile-search-container">
        <label class="block mb-1 text-sm font-medium text-gray-700 dark:text-gray-200">Job Profile*</label>
        <div class="relative">
          <input type="text" id="job_profile_input" name="job_title"
                 class="w-full p-2.5 rounded-lg border border-gray-300 dark:bg-gray-700 dark:border-gray-600"
                 placeholder="Search job profiles..." autocomplete="off"
                 value="<?= set_value('job_title', $post_detail->job_title ?? '') ?>">
          <input type="hidden" id="job_profile_id">
          <ul id="job_profile_list" class="absolute z-50 w-full bg-white dark:bg-gray-800 shadow-lg rounded-b-lg mt-1 max-h-60 overflow-y-auto hidden"></ul>
        </div>
      </div>

      <div class="grid gap-4 md:grid-cols-2">
        <div>
          <label class="block mb-1 text-sm font-medium text-gray-700 dark:text-gray-200">Job Type*</label>
          <select name="job_type" required class="w-full p-2.5 rounded-lg border border-gray-300 dark:bg-gray-700 dark:border-gray-600">
            <option value="">Select</option>
            <?php foreach ([
              'full-time'=>'Full-time', 'part-time'=>'Part-time', 'contract'=>'Contract',
              'internship'=>'Internship', 'remote'=>'Remote', 'hybrid'=>'Hybrid',
              'freelance'=>'Freelance', 'volunteer'=>'Volunteer', 'government'=>'Government Jobs',
              'international'=>'International Jobs', 'entry-level'=>'Entry Level', 'other'=>'Other'
            ] as $val => $label): ?>
              <option value="<?= $val ?>" <?= (isset($post_detail->job_type) && $post_detail->job_type === $val) ? 'selected' : '' ?>><?= $label ?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="industry-search-container">
          <label class="block mb-1 text-sm font-medium text-gray-700 dark:text-gray-200">Industry*</label>
          <div class="relative">
            <input type="search" id="industry_input" name="industry_name"
                   class="w-full p-2.5 rounded-lg border border-gray-300 dark:bg-gray-700 dark:border-gray-600"
                   placeholder="Start typing to search industries..." autocomplete="off"
                   value="<?= set_value('industry_name', $post_detail->industry_name ?? '') ?>">
            <input type="hidden" name="industry_id" id="industry_id" value="<?= set_value('industry_id', $post_detail->industry_id ?? '') ?>">
            <ul id="industry_list" class="absolute z-50 w-full bg-white dark:bg-gray-800 shadow-lg rounded-b-lg mt-1 max-h-60 overflow-y-auto hidden"></ul>
          </div>
        </div>
      </div>

      <div class="w-full mt-4">
        <label class="block mb-1 text-sm font-medium text-gray-700 dark:text-gray-200">City*</label>
        <div class="relative overflow-visible">
          <input type="text" id="city_input"
                 class="w-full p-2.5 rounded-lg border border-gray-300 dark:bg-gray-700 dark:border-gray-600"
                 placeholder="Search cities (min 2 characters)" autocomplete="off">
          <input type="hidden" name="city_ids" id="city_ids"
                 value="<?= isset($post_detail->cities) ? implode(',', array_column($post_detail->cities, 'city_id')) : '' ?>">
          <ul id="city_list" class="absolute top-full left-0 z-50 w-full bg-white dark:bg-gray-800 shadow-lg rounded-b-lg mt-1 max-h-60 overflow-y-auto hidden"></ul>
        </div>
        <div class="selected-cities flex flex-wrap gap-2 mt-2">
          <?php if(!empty($post_detail->cities)): ?>
            <?php foreach($post_detail->cities as $city): ?>
              <div class="bg-blue-100 text-blue-800 px-2 py-1 rounded flex items-center">
                <?= htmlspecialchars($city['city_name']) ?>
                <button type="button" class="ml-2 remove-city" data-id="<?= $city['city_id'] ?>">×</button>
              </div>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>
      </div>
    </div>

    <!-- Step 2: Salary & Qualifications -->
    <div class="step transition-opacity duration-300 ease-in-out hidden space-y-4">
      <div class="grid gap-4 md:grid-cols-2">
        <div class="space-y-4">
          <div>
            <label class="block mb-1 text-sm font-medium text-gray-700 dark:text-gray-200">Salary Range (₹)*</label>
            <div class="grid grid-cols-2 gap-2">
              <div class="relative">
                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500">₹</span>
                <input type="text" name="min_salary"
                       class="w-full pl-8 pr-3 p-2.5 rounded-lg border border-gray-300 dark:bg-gray-700 dark:border-gray-600 salary-input"
                       placeholder="Min" required inputmode="numeric"
                       value="<?= set_value('min_salary', $post_detail->min_salary ?? '') ?>">
              </div>
              <div class="relative">
                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500">₹</span>
                <input type="text" name="max_salary"
                       class="w-full pl-8 pr-3 p-2.5 rounded-lg border border-gray-300 dark:bg-gray-700 dark:border-gray-600 salary-input"
                       placeholder="Max" required inputmode="numeric"
                       value="<?= set_value('max_salary', $post_detail->max_salary ?? '') ?>">
              </div>
            </div>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1" id="salaryExample"></p>
          </div>
        </div>
        <div>
          <label class="block mb-1 text-sm font-medium text-gray-700 dark:text-gray-200">Salary Type*</label>
          <select name="salary_type" required class="w-full p-2.5 rounded-lg border border-gray-300 dark:bg-gray-700 dark:border-gray-600">
            <?php foreach (['Monthly','Yearly','Hourly'] as $type): ?>
              <option value="<?= $type ?>" <?= (isset($post_detail->salary_type) && $post_detail->salary_type === $type) ? 'selected' : '' ?>><?= $type ?></option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>

      <div class="grid gap-4 md:grid-cols-2">
        <div>
          <label class="block mb-1 text-sm font-medium text-gray-700 dark:text-gray-200">Education*</label>
          <select name="education" required class="w-full p-2.5 rounded-lg border border-gray-300 dark:bg-gray-700 dark:border-gray-600">
            <?php foreach ([
              "Bachelor Degree", "Master Degree", "Combined Program",
              "Doctoral Research", "Diploma", "Other"
            ] as $edu): ?>
              <option value="<?= $edu ?>" data-category="<?= $edu ?>" <?= (isset($post_detail->education) && $post_detail->education === $edu) ? 'selected' : '' ?>><?= $edu ?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <?php $ranges = [
          ['Fresher', 0, 1], ['Entry Level', 1, 3], ['Mid Level', 3, 5],
          ['Senior Level', 5, 10], ['Expert Level', 10, 20], ['Veteran', 20, 50]
        ]; ?>
        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="block mb-1 text-sm font-medium text-gray-700 dark:text-gray-200">Min Experience*</label>
            <select id="min_exp" name="min_experience" required class="w-full p-2.5 rounded-lg border border-gray-300 dark:bg-gray-700 dark:border-gray-600">
              <?php foreach($ranges as $r): ?>
                <option value="<?= $r[1] ?>" data-max="<?= $r[2] ?>" <?= (isset($post_detail->min_experience) && $post_detail->min_experience == $r[1]) ? 'selected' : '' ?>><?= $r[0] ?> (<?= $r[1] ?>-<?= $r[2] ?> years)</option>
              <?php endforeach; ?>
            </select>
          </div>
          <div>
            <label class="block mb-1 text-sm font-medium text-gray-700 dark:text-gray-200">Max Experience*</label>
            <select id="max_exp" name="max_experience" required class="w-full p-2.5 rounded-lg border border-gray-300 dark:bg-gray-700 dark:border-gray-600">
              <?php foreach($ranges as $r): ?>
                <option value="<?= $r[2] ?>" data-min="<?= $r[1] ?>" <?= (isset($post_detail->max_experience) && $post_detail->max_experience == $r[2]) ? 'selected' : '' ?>><?= $r[0] ?> (<?= $r[1] ?>-<?= $r[2] ?> years)</option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>
      </div>

      <?php
        $skillNames = [];
        if (!empty($post_detail->skills) && is_array($post_detail->skills)) {
          foreach ($post_detail->skills as $skill) {
            if (is_array($skill)) $skillNames[] = $skill['name'] ?? $skill['skill'] ?? '';
            else $skillNames[] = (string)$skill;
          }
        }
      ?>
      <div>
        <label class="block mb-1 text-sm font-medium text-gray-700 dark:text-gray-200">Skills* (maximum 10)</label>
       <div class="skills-container flex flex-wrap items-center gap-2 p-2 min-h-[46px] rounded-lg border border-gray-300 dark:bg-gray-700 dark:border-gray-600 relative">
          <div class="tags flex flex-wrap gap-2">
            <?php foreach ($skillNames as $name): ?>
              <?php if ($name !== ''): ?>
                <div class="skill-tag flex items-center bg-blue-100 text-blue-600 px-2 py-1 rounded-full text-sm dark:bg-blue-600 dark:text-white hover:-translate-y-0.5 transition-transform">
                  <span class="truncate"><?= htmlspecialchars($name, ENT_QUOTES, 'UTF-8') ?></span>
                  <button type="button" class="remove-skill ml-1.5 hover:text-gray-800 dark:hover:text-gray-200" aria-label="Remove skill">&times;</button>
                </div>
              <?php endif; ?>
            <?php endforeach; ?>
          </div>
          <div class="inline-flex items-center gap-2 mt-2 w-full">
            <input type="text" id="skills-input" class="flex-1 p-1.5 text-sm bg-transparent border-0 focus:ring-0 dark:text-gray-200" placeholder="Type a skill (maximum 10)" aria-label="Add skills" maxlength="20">
            <div class="flex items-center gap-2">
              <span id="skill-counter" class="text-xs text-gray-500 dark:text-gray-300"><?= count($skillNames) ?>/10</span>
              <button type="button" id="add-skill-btn" class="px-3 py-1.5 text-sm bg-blue-100 hover:bg-blue-200 text-blue-600 rounded-md dark:bg-blue-600 dark:hover:bg-blue-700 dark:text-white">Add</button>
            </div>
          </div>
          <div id="skill-error" class="text-red-500 text-xs mt-1 hidden"></div>
          <input type="hidden" name="skills" id="hidden-skills" required value="<?= htmlspecialchars(implode(',', array_filter($skillNames)), ENT_QUOTES, 'UTF-8') ?>">
        </div>
      </div>

      <div>
        <label class="block mb-1 text-sm font-medium text-gray-700 dark:text-gray-200">Job Description*</label>
        <textarea name="job_description" id="job_description" rows="4" class="w-full p-2.5 rounded-lg border border-gray-300 dark:bg-gray-700 dark:border-gray-600"><?= set_value('job_description', $post_detail->job_description ?? '') ?></textarea>
      </div>
    </div>

    <!-- Step 3: Application Info -->
    <div class="step transition-opacity duration-300 ease-in-out hidden space-y-4">
      <div class="grid gap-4 md:grid-cols-2">
        <div>
          <label class="block mb-1 text-sm font-medium text-gray-700 dark:text-gray-200">Application Method*</label>
          <div class="space-y-2">
            <div class="flex items-center gap-2">
              <input type="checkbox" name="application_method" id="method-weblink" value="weblink" <?= (!empty($post_detail->apply_web_link)) ? 'checked' : '' ?>>
              <label for="method-weblink" class="text-sm">Via Web Link</label>
            </div>
          </div>
        </div>
        <div id="weblink-field" class="<?= (!empty($post_detail->apply_web_link)) ? '' : 'hidden' ?>">
          <label class="block mb-1 text-sm font-medium text-gray-700 dark:text-gray-200">Application Link</label>
          <input type="url" name="apply_web_link" class="w-full p-2.5 rounded-lg border border-gray-300 dark:bg-gray-700 dark:border-gray-600" placeholder="https://careers.example.com/apply" value="<?= set_value('apply_web_link', $post_detail->apply_web_link ?? '') ?>">
          <div class="mt-2 flex items-center gap-2">
            <input type="checkbox" name="enable_apply_link" id="enable-link" class="form-checkbox h-4 w-4 text-blue-600" <?= (isset($post_detail->enable_apply_link) && $post_detail->enable_apply_link === 'yes') ? 'checked' : '' ?>>
            <label for="enable-link" class="text-sm text-gray-600 dark:text-gray-300">Enable apply link</label>
          </div>
        </div>
      </div>

      <?php
        $deadline = isset($post_detail->deadline_date) ? strtotime($post_detail->deadline_date) : null;
        $isExpired = $deadline && $deadline < strtotime('today');
      ?>
      <?php if (!$isExpired): ?>
        <input type="date" name="deadline_date" min="<?= date('Y-m-d') ?>" max="<?= date('Y-m-d', strtotime('+90 days')) ?>" required
               class="w-full p-2.5 rounded-lg border border-gray-300 dark:bg-gray-700 dark:border-gray-600"
               value="<?= set_value('deadline_date', $deadline ? date('Y-m-d', $deadline) : '') ?>">
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Select a date from today to <?= date('F j, Y', strtotime('+90 days')) ?>.</p>
      <?php else: ?>
        <p class="text-sm text-red-600 dark:text-red-400">This job has already expired. Deadline updates are disabled.</p>
      <?php endif; ?>

      <div>
        <label class="block mb-1 text-sm font-medium text-gray-700 dark:text-gray-200">Positions Open*</label>
        <input type="number" name="positions_open" min="1"
               class="w-full p-2.5 rounded-lg border border-gray-300 dark:bg-gray-700 dark:border-gray-600"
               placeholder="Number of vacancies" value="<?= set_value('positions_open', $post_detail->positions_open ?? '') ?>" required>
      </div>
    </div>

    <!-- Step 4: Review -->
    <div class="step transition-opacity duration-300 ease-in-out hidden space-y-4">
      <h2 class="text-lg font-semibold mb-4 dark:text-white">Review Details</h2>
      <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 space-y-3">
        <div class="grid grid-cols-2 gap-4">
          <div class="p-3 rounded-lg bg-white dark:bg-gray-800 shadow-sm">
            <span class="text-sm text-gray-600 dark:text-gray-300">Job Title:</span>
            <p class="font-medium dark:text-white" id="review-job-title"></p>
          </div>
          <div class="p-3 rounded-lg bg-white dark:bg-gray-800 shadow-sm">
            <span class="text-sm text-gray-600 dark:text-gray-300">Job Type:</span>
            <p class="font-medium dark:text-white" id="review-job-type"></p>
          </div>
          <div class="p-3 rounded-lg bg-white dark:bg-gray-800 shadow-sm">
            <span class="text-sm text-gray-600 dark:text-gray-300">Industry:</span>
            <p class="font-medium dark:text-white" id="review-industry"></p>
          </div>
          <div class="p-3 rounded-lg bg-white dark:bg-gray-800 shadow-sm">
            <span class="text-sm text-gray-600 dark:text-gray-300">City:</span>
            <p class="font-medium dark:text-white" id="review-city"></p>
          </div>
          <div class="p-3 rounded-lg bg-white dark:bg-gray-800 shadow-sm">
            <span class="text-sm text-gray-600 dark:text-gray-300">Positions Open:</span>
            <p class="font-medium dark:text-white" id="review-positions"></p>
          </div>
          <div class="p-3 rounded-lg bg-white dark:bg-gray-800 shadow-sm">
            <span class="text-sm text-gray-600 dark:text-gray-300">Salary Type:</span>
            <p class="font-medium dark:text-white" id="review-salary-type"></p>
          </div>
          <div class="p-3 rounded-lg bg-white dark:bg-gray-800 shadow-sm">
            <span class="text-sm text-gray-600 dark:text-gray-300">Education:</span>
            <p class="font-medium dark:text-white" id="review-education"></p>
          </div>
          <div class="p-3 rounded-lg bg-white dark:bg-gray-800 shadow-sm">
            <span class="text-sm text-gray-600 dark:text-gray-300">Experience:</span>
            <p class="font-medium dark:text-white" id="review-experience"></p>
          </div>
          <div class="p-3 rounded-lg bg-white dark:bg-gray-800 shadow-sm">
            <span class="text-sm text-gray-600 dark:text-gray-300">Skills:</span>
            <p class="font-medium dark:text-white" id="review-skills"></p>
          </div>
          <div class="p-3 rounded-lg bg-white dark:bg-gray-800 shadow-sm">
            <span class="text-sm text-gray-600 dark:text-gray-300">Salary Range:</span>
            <p class="font-medium dark:text-white" id="review-salary"></p>
          </div>
          <div class="p-3 rounded-lg bg-white dark:bg-gray-800 shadow-sm">
            <span class="text-sm text-gray-600 dark:text-gray-300">Deadline:</span>
            <p class="font-medium dark:text-white" id="review-deadline"></p>
          </div>
          <div class="p-3 rounded-lg bg-white dark:bg-gray-800 shadow-sm">
            <span class="text-sm text-gray-600 dark:text-gray-300">Application Link:</span>
            <p class="font-medium dark:text-white" id="review-apply-link"></p>
          </div>
        </div>
        <div class="p-3 rounded-lg bg-white dark:bg-gray-800 shadow-sm">
          <span class="text-sm text-gray-600 dark:text-gray-300">Job Description:</span>
          <!-- Added break-words to prevent horizontal scroll -->
          <p class="mt-2 text-gray-700 dark:text-gray-300 whitespace-pre-line break-words" id="review-description"></p>
        </div>
      </div>
    </div>

    <!-- Navigation Buttons – Fixed on mobile, static on desktop -->
    <div class="flex justify-between gap-3 mt-6 px-4 md:px-0 fixed bottom-0 left-0 right-0 bg-white dark:bg-gray-800 p-4 shadow-lg z-10 md:static md:bg-transparent md:shadow-none md:p-0">
      <button type="button" class="prev-btn px-4 py-3 text-sm bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-200 w-1/2 transition-colors">Previous</button>
      <?php if ($status == 'active'): ?>
        <?php if (!$hasActivePlan): ?>
          <button type="button" id="activateButton" class="next-btn px-4 py-3 text-sm bg-blue-600 text-white rounded-lg w-1/2">Next</button>
        <?php else: ?>
          <button type="button" class="next-btn px-4 py-3 text-sm bg-blue-600 text-white rounded-lg w-1/2">Next</button>
        <?php endif; ?>
      <?php endif; ?>
      <button type="submit" class="submit-btn hidden px-4 py-3 text-sm bg-green-600 text-white rounded-lg hover:bg-green-700 dark:bg-green-700 w-full transition-colors">Post Job</button>
    </div>
  </form>

  <!-- Loader -->
  <div class="loader hidden fixed inset-0 bg-black/50 flex items-center justify-center">
    <div class="animate-spin rounded-full h-10 w-10 border-4 border-blue-500 border-t-transparent"></div>
  </div>
</div>


<!-- TinyMCE and other scripts remain unchanged -->
<script src="https://cdn.tiny.cloud/1/jxhyjhicc4somdh05bjumsfnalcuzz4uej01mbbbizec4fov/tinymce/5/tinymce.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Your existing JavaScript (unchanged) -->
<script>
class AutoCompleteWidget {
  constructor({
    inputSelector,
    hiddenSelector,
    listSelector,
    apiUrl,
    minChars = 1,
    multiSelect = false,
    maxSelections = null,
    maxResults = null,           // <-- new: maximum items to display
    onSelect = null
  }) {
    this.input = document.querySelector(inputSelector);
    this.hidden = document.querySelector(hiddenSelector);
    this.list = document.querySelector(listSelector);
    this.apiUrl = apiUrl;
    this.minChars = minChars;
    this.multiSelect = multiSelect;
    this.maxSelections = maxSelections;
    this.maxResults = maxResults; // store the limit
    this.onSelect = onSelect;

    this.cache = new Map();
    this.highlightIndex = -1;
    this.selections = new Map();

    this.init();
  }

  init() {
    this.debouncedFetch = this.debounce(term => this.fetchAndRender(term), 300);
    this.input.addEventListener('input', e => this.handleInput(e));
    //this.input.addEventListener('focus', e => this.handleInput(e));
    this.input.addEventListener('keydown', e => this.handleKeydown(e));
    this.list.addEventListener('click', e => this.handleClick(e));
    document.addEventListener('click', e => this.handleOutsideClick(e));
    window.addEventListener('resize', () => this.positionList());

    if (this.multiSelect) {
      this.selectedContainer = document.createElement('div');
      this.selectedContainer.className = 'selected-items flex flex-wrap gap-2 mt-2';
      this.input.insertAdjacentElement('afterend', this.selectedContainer);
      this.selectedContainer.addEventListener('click', e => this.handleRemove(e));
    }
	
  }
	
  handleInput(e) {
    const term = e.target.value.trim();
    if (term.length < this.minChars) {
      this.hideList();
      return;
    }
    if (this.cache.has(term)) {
      this.renderList(this.cache.get(term));
    } else {
      this.debouncedFetch(term);
    }
  }

  async fetchAndRender(term) {
    try {
      this.showLoading();
      const sep = this.apiUrl.includes('?') ? '&' : '?';
      const url = `${this.apiUrl}${sep}term=${encodeURIComponent(term)}`;
      const res = await fetch(url);
      const data = await res.json();
      this.cache.set(term, data);
      this.renderList(data);
    } catch (e) {
      this.showError();
    }
  }

  renderList(items) {
    // apply maxResults limit if specified
    const listItems = (this.maxResults && Array.isArray(items))
      ? items.slice(0, this.maxResults)
      : items;

    if (!listItems || !listItems.length) {
      this.list.innerHTML = '<li class="p-3 text-gray-500">No results</li>';
    } else {
      this.list.innerHTML = listItems.map((item, idx) => {
        const selected = this.selections.has(item.id) ? 'opacity-50' : '';
        return `<li data-id="${item.id}" data-label="${item.value || item.text}" class="p-3 hover:bg-blue-50 cursor-pointer truncate ${idx===this.highlightIndex?'bg-blue-100':''} ${selected}">${item.value||item.text}</li>`;
      }).join('');
    }
    this.highlightIndex = -1;
    this.showList();
    this.positionList();
  }

  handleKeydown(e) {
    const items = Array.from(this.list.querySelectorAll('li'));
    if (!items.length) return;

    if (e.key === 'ArrowDown') {
      e.preventDefault();
      this.highlightIndex = (this.highlightIndex + 1) % items.length;
    } else if (e.key === 'ArrowUp') {
      e.preventDefault();
      this.highlightIndex = (this.highlightIndex - 1 + items.length) % items.length;
    } else if (e.key === 'Enter') {
      e.preventDefault();
      if (this.highlightIndex > -1) items[this.highlightIndex].click();
      return;
    } else if (e.key === 'Escape') {
      this.hideList();
      return;
    } else {
      return;
    }

    items.forEach((li, idx) => li.classList.toggle('bg-blue-100', idx === this.highlightIndex));
    if (items[this.highlightIndex])
      items[this.highlightIndex].scrollIntoView({ block: 'nearest' });
  }

  handleClick(e) {
    const li = e.target.closest('li');
    if (!li) return;
    const id = li.dataset.id;
    const label = li.dataset.label;

    if (this.multiSelect) {
      if (this.selections.has(id)) return;
      if (this.maxSelections && this.selections.size >= this.maxSelections) {
        this.flashMessage(`Maximum ${this.maxSelections} items allowed`);
        return;
      }
      this.selections.set(id, label);
      this.renderSelections();
      this.updateHidden();
      this.input.value = '';
      this.hideList();
    } else {
      this.input.value = label;
      this.hidden.value = id;
      this.hideList();
      if (this.onSelect) this.onSelect({ id, label });
    }
  }

  renderSelections() {
    if (!this.multiSelect) return;
    this.selectedContainer.innerHTML = '';
    this.selections.forEach((label, id) => {
      const el = document.createElement('div');
      el.className = 'bg-blue-100 text-blue-800 px-2 py-1 rounded flex items-center';
      el.innerHTML = `${label} <button data-id="${id}" class="ml-2">×</button>`;
      this.selectedContainer.appendChild(el);
    });
  }

  handleRemove(e) {
    const btn = e.target.closest('button[data-id]');
    if (!btn) return;
    const id = btn.dataset.id;
    this.selections.delete(id);
    this.renderSelections();
    this.updateHidden();
  }

  updateHidden() {
    this.hidden.value = this.multiSelect
      ? Array.from(this.selections.keys()).join(',')
      : '';
  }

  showLoading() {
    this.list.innerHTML = '<li class="p-3 text-gray-500">Loading...</li>';
    this.showList();
  }

  showError() {
    this.list.innerHTML = '<li class="p-3 text-red-500">Error loading data</li>';
    this.showList();
  }

  positionList() {
    const rect = this.input.getBoundingClientRect();
    this.list.style.width = `${rect.width}px`;
    //this.list.style.top = `${rect.bottom + window.scrollY + 5}px`;
    //this.list.style.left = `${rect.left + window.scrollX}px`;
  }

  showList() {
    this.list.classList.remove('hidden');
  }

  hideList() {
    this.list.classList.add('hidden');
  }

  handleOutsideClick(e) {
    if (!this.input.contains(e.target) && !this.list.contains(e.target)) {
      this.hideList();
    }
  }

  debounce(fn, delay) {
    let timer;
    return (...args) => {
      clearTimeout(timer);
      timer = setTimeout(() => fn(...args), delay);
    };
  }
}

</script>
<script>

document.addEventListener('DOMContentLoaded', function () {
  // TinyMCE paste handler (keep as is)
  const textarea = document.getElementById('job_description');
  textarea.addEventListener('paste', function (e) {
    e.preventDefault();
    const text = (e.clipboardData || window.clipboardData).getData('text');
    const plainText = text.replace(/<\/?[^>]+(>|$)/g, "");
    const start = this.selectionStart;
    const end = this.selectionEnd;
    const original = this.value;
    this.value = original.substring(0, start) + plainText + original.substring(end);
    this.selectionStart = this.selectionEnd = start + plainText.length;
  });

  // CSRF helpers
  function getCsrf() { return document.querySelector('input[name="<?= $this->security->get_csrf_token_name(); ?>"]').value; }
  const csrfTokenName = "<?= $this->security->get_csrf_token_name(); ?>";
  function refreshCsrf(newToken) {
    if (!newToken) return;
    document.querySelectorAll('input[name="<?= $this->security->get_csrf_token_name(); ?>"]').forEach(el => { el.value = newToken; });
  }

  // TinyMCE init
  tinymce.init({
    selector: '#job_description',
    plugins: 'lists link',
    toolbar: 'bold italic underline | bullist numlist | link',
    height: 220,
    menubar: false,
    statusbar: false,
    branding: false,
    promotion: false
  });

  // Autocomplete widgets
  new AutoCompleteWidget({
    inputSelector: '#job_profile_input',
    hiddenSelector: '#job_profile_id',
    listSelector: '#job_profile_list',
    apiUrl: '<?= base_url("Common/get_search_data?type=job_profile") ?>',
    minChars: 1,
    multiSelect: false,
    maxResults: 5
  });
  new AutoCompleteWidget({
    inputSelector: '#industry_input',
    hiddenSelector: '#industry_id',
    listSelector: '#industry_list',
    apiUrl: '<?= base_url("Common/get_search_data?type=industry") ?>',
    minChars: 1,
    multiSelect: false,
    maxResults: 5
  });
  new AutoCompleteWidget({
    inputSelector: '#city_input',
    hiddenSelector: '#city_ids',
    listSelector: '#city_list',
    apiUrl: '<?= base_url("Common/get_cities") ?>',
    minChars: 2,
    multiSelect: true,
    maxSelections: 5,
    maxResults: 10
  });

  // Step navigation (unchanged, except we removed custom CSS)
  const steps = Array.from(document.querySelectorAll('.step'));
  const prevBtn = document.querySelector('.prev-btn');
  const nextBtn = document.querySelector('.next-btn');
  const submitBtn = document.querySelector('.submit-btn');
  const form = document.getElementById('jobPostForm');
  const loader = document.querySelector('.loader');
  const jobId = document.getElementById('job_id').value;
  let currentStep = 0;
  let isEditMode = !!jobId;

  function showStep(index) {
    steps.forEach((step, i) => {
      step.style.display = i === index ? 'block' : 'none';
      step.classList.toggle('active', i === index);
    });
    prevBtn.style.display = index === 0 ? 'none' : 'inline-block';
    nextBtn.style.display = index === steps.length - 1 ? 'none' : 'inline-block';
    submitBtn.style.display = index === steps.length - 1 ? 'inline-block' : 'none';
    updateProgress();
  }

  function updateProgress() {
    const indicators = document.querySelectorAll('.flex.flex-col.items-center.mx-2');
    indicators.forEach((indicator, idx) => {
      const circle = indicator.querySelector('div');
      circle.classList.toggle('bg-blue-600', idx <= currentStep);
      circle.classList.toggle('ring-blue-200', idx === currentStep);
      circle.classList.toggle('bg-blue-500', idx <= currentStep && idx !== currentStep);
    });
  }

  function validateSummernote() {
    const editor = tinymce.get('job_description');
    if(!editor) return false;
    const html = editor.getContent();
    const text = html.replace(/<[^>]+>/g,'').trim();
    if(!text) {
      const editorBox = document.querySelector('.tox-tinymce');
      if(editorBox) editorBox.classList.add('border-red-500');
      showToast('Please enter a job description','error');
      return false;
    }
    return true;
  }

  function validateStep(index) {
    const step = steps[index];
    if (!step) return false;
    let valid = true;
    step.querySelectorAll('input, select, textarea').forEach(el => el.classList.remove('border-red-500'));
    const editorBox = document.querySelector('.tox-tinymce');
    if(editorBox) editorBox.classList.remove('border-red-500');
    const error = msg => showToast(msg, 'error');

    if (index === 0) {
      if (!form.job_title.value.trim()) { form.job_title.classList.add('border-red-500'); valid = false; }
      if (!form.job_type.value) { form.job_type.classList.add('border-red-500'); valid = false; }
      if (!document.getElementById('industry_id').value) { document.getElementById('industry_input').classList.add('border-red-500'); valid = false; error('Please select an industry.'); }
      const cityIds = document.getElementById('city_ids').value;
      if (!cityIds || cityIds.trim() === '') { document.getElementById('city_input').classList.add('border-red-500'); valid = false; error('Please select at least one city.'); }
      return valid;
    }

    if (index === 1) {
      const minSal = parseInt(form.min_salary.value.replace(/\D/g, ''), 10);
      const maxSal = parseInt(form.max_salary.value.replace(/\D/g, ''), 10);
      if (isNaN(minSal) || isNaN(maxSal)) { document.querySelectorAll('.salary-input').forEach(el => el.classList.add('border-red-500')); valid = false; error('Please enter valid salary.'); }
      else if (minSal > maxSal) { form.min_salary.classList.add('border-red-500'); form.max_salary.classList.add('border-red-500'); valid = false; error('Min salary cannot exceed Max salary.'); }
      if (!form.salary_type.value) { form.salary_type.classList.add('border-red-500'); valid = false; }
      if (!form.education.value) { form.education.classList.add('border-red-500'); valid = false; }
      const minExp = parseInt(form.min_experience.value, 10);
      const maxExp = parseInt(form.max_experience.value, 10);
      if (minExp > maxExp) { form.min_experience.classList.add('border-red-500'); form.max_experience.classList.add('border-red-500'); valid = false; error('Min experience cannot exceed Max experience.'); }
      const skillsArr = (form.skills.value || '').split(',').filter(s => s.trim());
      if (skillsArr.length === 0) { document.getElementById('skills-input').classList.add('border-red-500'); valid = false; error('Please add at least one skill.'); }
      if (!validateSummernote()) valid = false;
      return valid;
    }

    if (index === 2) {
      const method = document.querySelector('input[name="application_method"]:checked')?.value || '';
      if (method === 'weblink' && document.getElementById('enable-link').checked) {
        const link = form.apply_web_link.value.trim();
        const urlRegex = /^(https?:\/\/)[\w.-]+(?:\.[\w\.-]+)+[\w\-\._~:\/?#[\]@!$&'()*+,;=.]+$/;
        if (!urlRegex.test(link)) { form.apply_web_link.classList.add('border-red-500'); valid = false; error('Please enter a valid URL.'); }
      }
      if (!form.deadline_date.value) { form.deadline_date.classList.add('border-red-500'); valid = false; error('Please select deadline.'); }
      else {
        const dl = new Date(form.deadline_date.value);
        const today = new Date(); today.setHours(0,0,0,0);
        if (dl < today) { form.deadline_date.classList.add('border-red-500'); valid = false; error('Deadline must be future date.'); }
      }
      if (!form.positions_open.value || parseInt(form.positions_open.value, 10) < 1) { form.positions_open.classList.add('border-red-500'); valid = false; error('Positions open must be at least 1.'); }
      return valid;
    }
    return true;
  }

  nextBtn.addEventListener('click', () => {
    if(nextBtn.id === 'activateButton'){
      const modal = document.getElementById('planActivationModal');
      if(modal) { modal.classList.remove('hidden'); modal.classList.add('flex'); }
      return;
    }
    if (!validateStep(currentStep)) return;
    currentStep++;
    if (currentStep === 3) updateReviewData();
    showStep(currentStep);
    window.scrollTo({ top:0, behavior:'smooth' });
  });

  prevBtn.addEventListener('click', () => {
    currentStep--;
    showStep(currentStep);
    window.scrollTo({ top:0, behavior:'smooth' });
  });

  function updateReviewData() {
		document.getElementById('review-job-title').textContent = form.job_title.value || 'Not provided';
		document.getElementById('review-job-type').textContent = form.job_type.selectedOptions[0]?.text || 'Not selected';
		document.getElementById('review-industry').textContent = document.getElementById('industry_input').value || 'Not selected';

		// Fixed city preview
		const cityTags = document.querySelectorAll('.selected-cities div, .selected-items div');
		const cities = [];
		cityTags.forEach(tag => {
			const text = tag.textContent.replace('×', '').trim();
			if (text) cities.push(text);
		});
		document.getElementById('review-city').textContent = cities.length ? cities.join(', ') : 'Not selected';

		document.getElementById('review-positions').textContent = form.positions_open.value || 'Not provided';
		document.getElementById('review-salary-type').textContent = form.salary_type.value || 'Not selected';
		document.getElementById('review-salary').textContent = `₹${form.min_salary.value || 0} - ₹${form.max_salary.value || 0} (${form.salary_type.value})`;
		document.getElementById('review-education').textContent = form.education.value || 'Not selected';
		document.getElementById('review-experience').textContent = `${form.min_experience.value} - ${form.max_experience.value} Years`;

		const skills = document.getElementById('hidden-skills').value.split(',').filter(s => s.trim());
		document.getElementById('review-skills').textContent = skills.length ? skills.join(', ') : 'Not provided';

		const editor = tinymce.get('job_description');
		if (editor) {
			const html = editor.getContent();
			const temp = document.createElement('div');
			temp.innerHTML = html;
			const text = temp.textContent || temp.innerText || '';
			document.getElementById('review-description').textContent = text || 'Not provided';
		}

		document.getElementById('review-deadline').textContent = form.deadline_date.value || 'Not selected';
		document.getElementById('review-apply-link').textContent = form.apply_web_link.value || 'Not provided';
	}
  form.addEventListener('submit', async (e) => {
    e.preventDefault();
    tinymce.triggerSave();
    if (!validateStep(currentStep)) return;
    loader.classList.remove('hidden');
    const formData = new FormData(form);
    if (!formData.has(csrfTokenName)) formData.append(csrfTokenName, getCsrf());
    const url = isEditMode ? '<?=base_url('employer/PostJobController/update_post_job')?>' : '<?=base_url('employer/PostJobController/insert_post_job')?>';
    try {
      const response = await fetch(url, { method: 'POST', body: formData, headers: { 'X-Requested-With': 'XMLHttpRequest' } });
      let result;
      try { result = await response.json(); } catch (err) { loader.classList.add('hidden'); showToast('Server response error', 'error'); return; }
      refreshCsrf(result.csrf_token);
      loader.classList.add('hidden');
      if (result.error_generate) { showProfileModal(result.error_generate); return; }
      if (result.success === 1) {
        document.body.classList.add('toast-success-shown');
        showToast(result.success_msg || (isEditMode ? 'Job updated successfully!' : 'Job posted successfully!'), 'success');
        if (!isEditMode) resetForm();
        return;
      }
      showToast(result.error_msg || 'Validation error', 'error');
      if (typeof result.validation_step === 'number') {
        currentStep = result.validation_step;
        showStep(currentStep);
        setTimeout(() => {
          const step = steps[currentStep];
          const firstInvalid = step.querySelector('.border-red-500, input:invalid, select:invalid, textarea:invalid');
          if (firstInvalid) { firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' }); firstInvalid.focus(); }
          if (currentStep === 1 && tinymce.get('job_description')) {
            const txt = tinymce.get('job_description').getContent({ format: 'text' }).trim();
            if (!txt) document.querySelector('.tox-tinymce').scrollIntoView({ behavior: 'smooth', block: 'center' });
          }
        }, 150);
      }
    } catch (error) {
      console.error('Submission error:', error);
      loader.classList.add('hidden');
      if (!document.body.classList.contains('toast-success-shown')) showToast('Network error! Please try again.', 'error');
    }
  });

  function resetForm() {
    form.reset();
    document.querySelectorAll('.selected-cities > div').forEach(el => el.remove());
    document.querySelectorAll('.skill-tag').forEach(el => el.remove());
    document.getElementById('hidden-skills').value = '';
    document.getElementById('skill-counter').textContent = '0/10';
    if (tinymce.get('job_description')) tinymce.get('job_description').setContent('');
    ['review-job-title','review-job-type','review-industry','review-city','review-positions','review-salary-type','review-education','review-experience','review-skills','review-salary','review-deadline','review-apply-link','review-description'].forEach(id => { const el = document.getElementById(id); if (el) el.textContent = ''; });
    currentStep = 0;
    showStep(currentStep);
    window.scrollTo({ top: 0, behavior: 'smooth' });
  }

  showStep(currentStep);

  document.querySelectorAll('input, select, textarea').forEach(el => {
    el.addEventListener('focus', () => { if (window.innerWidth < 640) setTimeout(() => el.scrollIntoView({ behavior: 'smooth', block: 'center' }), 300); });
  });

  document.getElementById('method-weblink').addEventListener('change', function() {
    document.getElementById('weblink-field').classList.toggle('hidden', !this.checked);
  });

  document.querySelector('input[name="apply_web_link"]').addEventListener('input', function () {
    if (this.value.trim() !== '') { document.getElementById('method-weblink').checked = true; document.getElementById('weblink-field').classList.remove('hidden'); }
  });

  document.querySelector('.selected-cities').addEventListener('click', function(e) {
    if (!e.target.classList.contains('remove-city')) return;
    const cityId = e.target.getAttribute('data-id');
    const jobId  = document.getElementById('job_id').value;
    if (!cityId || !jobId) return;
    const params = new URLSearchParams();
    params.append('job_id', jobId);
    params.append('city_id', cityId);
    params.append(csrfTokenName, getCsrf());
    fetch('<?= base_url('employer/PostJobController/remove_job_city') ?>', {
      method: 'POST',
      headers: { 'X-Requested-With': 'XMLHttpRequest', 'Content-Type': 'application/x-www-form-urlencoded' },
      body: params.toString()
    })
    .then(res => res.json())
    .then(data => {
      refreshCsrf(data.csrf_token);
      if (data.success) { e.target.closest('div').remove(); refreshCityIds(); showToast(data.success_msg,'success'); }
      else showToast(data.error_msg, 'error');
    })
    .catch(() => showToast('Network error', 'error'));
  });

  function refreshCityIds() {
    const ids = [];
    document.querySelectorAll('.remove-city').forEach(btn=> ids.push(btn.dataset.id));
    document.getElementById('city_ids').value = ids.join(',');
  }

  function showToast(message, type = 'info') {
    let icon = type === 'success' ? 'success' : type === 'error' ? 'error' : 'info';
    Swal.fire({ toast: true, position: 'top-end', icon: icon, title: message, showConfirmButton: false, timer: 2200, timerProgressBar: true, padding: '6px', customClass: { popup: 'swal2-toast' } });
  }

  function showProfileModal(html) {
    const existingModal = document.getElementById('profileCompleteModal');
    if (existingModal) existingModal.remove();
    const modal = document.createElement('div');
    modal.id = 'profileCompleteModal';
    modal.className = 'fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-[9999] p-4';
    modal.innerHTML = `
      <div class="bg-white w-full max-w-lg rounded-2xl shadow-lg relative overflow-hidden animate-fadeIn">
        <button type="button" class="absolute top-3 right-3 text-gray-500 hover:text-black text-2xl font-bold focus:outline-none" onclick="document.getElementById('profileCompleteModal').remove()">×</button>
        <div class="p-6 sm:p-8">${html}</div>
      </div>
    `;
    document.body.appendChild(modal);
    initCompanyLocationAutoComplete();
    modal.addEventListener('click', function (e) { if (e.target.id === 'profileCompleteModal') modal.remove(); });
  }

  function initCompanyLocationAutoComplete() {
    new AutoCompleteWidget({
      inputSelector: '#company_location',
      hiddenSelector: '#hiddenLocationId',
      listSelector: '#company_location_list',
      apiUrl: '<?= base_url("Common/get_cities") ?>',
      minChars: 2,
      multiSelect: false,
      maxResults: 10
    });
  }

  document.addEventListener('submit', async function (e) {
    if (e.target && e.target.id === 'completeProfileForm') {
      e.preventDefault();
      const profileForm = e.target;
      const profileSubmitBtn = profileForm.querySelector('#submitApplication');
      const spinner = profileForm.querySelector('.spinner');
      spinner.classList.remove('hidden');
      profileSubmitBtn.disabled = true;
      const profileFormData = new FormData(profileForm);
      const jobFormData = new FormData(form);
      for (let [key, value] of jobFormData.entries()) if (!profileFormData.has(key)) profileFormData.append(key, value);
      if (!profileFormData.has(csrfTokenName)) profileFormData.append(csrfTokenName, getCsrf());
      try {
        const url = isEditMode ? '<?=base_url('employer/PostJobController/update_post_job')?>' : '<?=base_url('employer/PostJobController/insert_post_job')?>';
        const response = await fetch(url, { method: 'POST', body: profileFormData, headers: { 'X-Requested-With': 'XMLHttpRequest' } });
        const result = await response.json();
        refreshCsrf(result.csrf_token);
        spinner.classList.add('hidden');
        profileSubmitBtn.disabled = false;
        if (result.success === 1) {
          showProfileMessage(result.success_msg || 'Profile updated and job posted successfully!', 'success');
          setTimeout(() => { document.getElementById('profileCompleteModal')?.remove(); if (!isEditMode) resetForm(); }, 1500);
        } else {
          showProfileMessage(result.error_msg || 'Something went wrong.', 'error');
        }
      } catch (err) {
        console.error('Profile submit error:', err);
        spinner.classList.add('hidden');
        profileSubmitBtn.disabled = false;
        showProfileMessage('Network error! Please try again.', 'error');
      }
    }
  });

  document.addEventListener('click', function (e) {
    if (e.target && e.target.id === 'skipProfileBtn') {
      const formData = new FormData(form);
      formData.append('skip_profile_check', '1');
      formData.append(csrfTokenName, getCsrf());
      fetch('<?=base_url('employer/PostJobController/insert_post_job')?>', {
        method: 'POST',
        body: formData,
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
      })
      .then(res => res.json())
      .then(result => {
        refreshCsrf(result.csrf_token);
        if (result.success === 1) { showToast(result.success_msg, 'success'); document.getElementById('profileCompleteModal')?.remove(); resetForm(); }
        else showToast(result.error_msg, 'error');
      })
      .catch(() => showToast('Network error!', 'error'));
    }
  });

  // Skills management (unchanged)
  const tagsDiv = document.querySelector('.skills-container .tags');
  const input = document.getElementById('skills-input');
  const addBtn = document.getElementById('add-skill-btn');
  const hiddenInput = document.getElementById('hidden-skills');
  const counter = document.getElementById('skill-counter');
  const errorDiv = document.getElementById('skill-error');
  const MAX_TAGS = 10, MAX_LENGTH = 20, SKILL_REGEX = /^[A-Za-z\s\-']+$/;
  let skills = hiddenInput.value ? hiddenInput.value.split(',').filter(s => s.trim()) : [];
  function sync() { counter.textContent = `${skills.length}/${MAX_TAGS}`; hiddenInput.value = skills.join(','); }
  function showError(msg) { errorDiv.textContent = msg; errorDiv.classList.remove('hidden'); setTimeout(() => errorDiv.classList.add('hidden'), 3000); }
  function addSkill(text) {
    text = text.trim();
    if (!text) return showError('Cannot be empty');
    if (text.length > MAX_LENGTH) return showError(`Max ${MAX_LENGTH} chars`);
    if (!SKILL_REGEX.test(text)) return showError('Invalid characters');
    if (skills.includes(text)) return showError('Already added');
    if (skills.length >= MAX_TAGS) return showError(`Max ${MAX_TAGS} skills`);
    skills.push(text);
    const tag = document.createElement('div');
    tag.className = 'skill-tag flex items-center bg-blue-100 text-blue-600 px-2 py-1 rounded-full text-sm dark:bg-blue-600 dark:text-white hover:-translate-y-0.5 transition-transform';
    tag.innerHTML = `<span class="truncate">${text}</span><button type="button" class="remove-skill ml-1.5 hover:text-gray-800 dark:hover:text-gray-200" aria-label="Remove skill">&times;</button>`;
    tagsDiv.append(tag);
    sync(); input.value = ''; input.focus();
  }
  tagsDiv.addEventListener('click', e => {
    if (!e.target.classList.contains('remove-skill')) return;
    const tagEl = e.target.closest('.skill-tag');
    const text = tagEl.querySelector('span').textContent;
    skills = skills.filter(s => s !== text);
    tagEl.remove();
    sync();
  });
  addBtn.addEventListener('click', () => addSkill(input.value));
  input.addEventListener('keydown', e => {
    if (e.key === 'Enter' || e.key === ',') { e.preventDefault(); addSkill(input.value); }
    if (e.key === 'Backspace' && !input.value) { if (skills.length) { skills.pop(); tagsDiv.lastElementChild.remove(); sync(); } }
  });
  input.addEventListener('paste', e => {
    e.preventDefault();
    const text = e.clipboardData.getData('text');
    text.split(/[\n,]+/).forEach(s => addSkill(s));
  });
  sync();
});
</script>