<div class="container mx-auto px-4 py-8">
  <div class="max-w-3xl mx-auto bg-white dark:bg-gray-900 rounded-2xl shadow-lg p-6 md:p-8">
    
    <div class="flex flex-col md:flex-row items-start md:items-center justify-between mb-6 md:mb-8">
      <div class="flex flex-col md:flex-row md:items-center gap-2 md:gap-4">
        <div>
          <h1 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-gray-100">Schedule Interview</h1>
          <p class="mt-1 text-gray-600 dark:text-gray-400">For <?= htmlspecialchars($application['name']) ?></p>
        </div>

        <?php if (isset($interview)): ?>
          <?php
            $currentStage = $interview['ApplicationStage'] ?? $application['ApplicationStage'] ?? '';
            $labelClass = 'bg-gray-100 text-gray-800';
            if ($currentStage === 'Scheduled') {
                $labelClass = 'bg-blue-100 text-blue-800';
            } elseif ($currentStage === 'Rescheduled') {
                $labelClass = 'bg-yellow-100 text-yellow-800';
            } elseif ($currentStage === 'Completed') {
                $labelClass = 'bg-green-100 text-green-800';
            } elseif ($currentStage === 'Hired') {
                $labelClass = 'bg-purple-100 text-purple-800';
            } elseif ($currentStage === 'Rejected') {
                $labelClass = 'bg-red-100 text-red-800';
            }
          ?>
          <span class="inline-block text-sm font-medium px-3 py-1 rounded-full <?= $labelClass ?>">
            <?= $currentStage ?>
          </span>
        <?php endif; ?>
      </div>

      <a href="<?= site_url('employer/applications/view/'.$application['applied_id']) ?>"
         class="inline-flex items-center gap-2 text-sm text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-white transition">
        <i class="fas fa-arrow-left"></i><span>Back</span>
      </a>
    </div>

    <!-- Form -->
    <form id="scheduleForm" action="<?= isset($interview)
        ? site_url('employer/interviews/update/'.$interview['interview_id'])
        : site_url('employer/interviews/schedule/'.$application['applied_id']) ?>"
    method="post" class="space-y-6">

      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

        <!-- Interview Date -->
        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-400 mb-1">Interview Date</label>
          <input type="date" name="interview_date" required
            value="<?= isset($interview) ? htmlspecialchars($interview['interview_date']) : '' ?>"
            min="<?= date('Y-m-d', strtotime('+1 day')) ?>"
            max="<?= date('Y-m-d', strtotime('+60 days')) ?>"
            class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 transition">
        </div>

        <!-- Time -->
        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-400 mb-1">Time</label>
          <input type="time" name="interview_time" id="interviewTime" required
            min="10:00" max="18:00"
            value="<?= isset($interview) ? htmlspecialchars(date('H:i', strtotime($interview['interview_time']))) : '' ?>"
            class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 transition">
        </div>

        <!-- Type -->
        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-400 mb-1">Type</label>
          <select name="interview_type" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500">
            <?php foreach(['Video Call','In-person','Phone'] as $type): ?>
            <option value="<?= $type ?>" <?= isset($interview) && $interview['interview_type']===$type ? 'selected':'' ?>><?= $type ?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <?php if(isset($interview)): ?>
		<?php
		  $currentStage = $interview['ApplicationStage'] ?? $application['ApplicationStage'] ?? 'Scheduled';
		  $nextStages = get_application_statuses($currentStage);
		  $isFinalStage = empty($nextStages);
		?>
		<!-- Interview Stage -->
		<div>
		  <label class="block text-sm font-medium text-gray-700 dark:text-gray-400 mb-1">Interview Stage</label>
		  <select name="ApplicationStage" <?= $isFinalStage ? 'disabled':'' ?> class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500">
			<!-- Current stage (always shown, disabled in some cases?) -->
			<option value="<?= $currentStage ?>" selected><?= get_status_label($currentStage) ?></option>
			<?php foreach($nextStages as $stage): ?>
			<option value="<?= $stage ?>"><?= get_status_label($stage) ?></option>
			<?php endforeach; ?>
		  </select>
		</div>
		<?php endif; ?>

      </div>

      <!-- Interview Link -->
      <div>
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-400 mb-1">Interview Link</label>
        <input type="url" name="interview_link"
          value="<?= isset($interview) ? htmlspecialchars($interview['interview_link'] ?? ''):'' ?>"
          placeholder="https://meet.google.com/... or https://zoom.us/..."
          class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500">
      </div>

      <!-- Notes -->
      <div>
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-400 mb-1">Notes / Agenda</label>
        <textarea name="notes" rows="4" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500"
          placeholder="Add agenda points..."><?= isset($interview) ? htmlspecialchars($interview['notes']) : '' ?></textarea>
      </div>

      <!-- Submit -->
      <button type="submit"
        <?= (isset($interview) && $isFinalStage) || ($status!=='active') ? 'disabled':'' ?>
        class="w-full flex items-center justify-center gap-2 bg-gradient-to-r from-indigo-500 to-purple-500 text-white font-semibold px-6 py-3 rounded-2xl shadow-lg hover:scale-105 transition disabled:opacity-50">
        <i class="fas fa-calendar-check"></i>
        <span class="button-label"><?= isset($interview) ? 'Update' : 'Schedule' ?> Interview</span>
      </button>

    </form>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
  const form = document.getElementById('scheduleForm');

  // Helper: Show toast
  function showToast(message, type = 'info') {
    const toast = document.createElement('div');
    toast.className = `fixed bottom-4 right-4 z-50 px-6 py-3 rounded-lg text-white shadow-lg animate-slide-in ${
      type === 'success' ? 'bg-green-500' :
      type === 'error'   ? 'bg-red-500'   : 'bg-blue-500'
    }`;
    toast.innerHTML = `
      <div class="flex items-center">
        <i class="fas ${
          type === 'success' ? 'fa-check-circle' :
          type === 'error'   ? 'fa-exclamation-triangle' : 'fa-info-circle'
        } mr-2"></i>
        <span>${message}</span>
      </div>`;
    document.body.appendChild(toast);
    setTimeout(() => {
      toast.classList.add('animate-fade-out');
      setTimeout(() => toast.remove(), 300);
    }, 3000);
  }

  // CSRF helpers (global from master)
  const getCsrfToken = () => getCSRFToken ? getCSRFToken() : document.querySelector('meta[name="csrf-token"]')?.content;
  const getCsrfName = () => getCSRFName ? getCSRFName() : document.querySelector('meta[name="csrf-name"]')?.content;
  const updateCsrfToken = (token, name) => {
    if (typeof updateCSRFToken === 'function') {
      updateCSRFToken(token, name);
    } else {
      const metaToken = document.querySelector('meta[name="csrf-token"]');
      const metaName = document.querySelector('meta[name="csrf-name"]');
      if (metaToken) metaToken.setAttribute('content', token);
      if (metaName) metaName.setAttribute('content', name);
    }
  };

  form.addEventListener('submit', async (e) => {
    e.preventDefault();

    const submitBtn = form.querySelector('button[type="submit"]');
    const btnLabel = submitBtn.querySelector('.button-label');
    const originalText = btnLabel.textContent;

    // Disable and show spinner
    submitBtn.disabled = true;
    btnLabel.textContent = 'Please wait...';

    try {
      const formData = new FormData(form);
      const csrfName = getCsrfName();
      const csrfToken = getCsrfToken();
      formData.append(csrfName, csrfToken);

      const response = await fetch(form.action, {
        method: 'POST',
        body: formData,
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
      });

      const json = await response.json();

      // Update CSRF token if provided
      if (json.csrf) {
        updateCsrfToken(json.csrf, csrfName);
      }

      // Clear previous errors
      form.querySelectorAll('.text-red-500').forEach(el => el.remove());
      form.querySelectorAll('.border-red-500').forEach(el => el.classList.remove('border-red-500'));

      if (json.success) {
        showToast(json.message, 'success');
        // Optionally redirect or reload after delay
        setTimeout(() => {
          if (json.redirect) window.location.href = json.redirect;
          else location.reload();
        }, 1000);
      } else {
        showToast(json.error || 'Operation failed', 'error');
        // Display field errors
        if (json.errors) {
          Object.entries(json.errors).forEach(([field, msg]) => {
            const input = form.querySelector(`[name="${field}"]`);
            if (input) {
              input.classList.add('border-red-500');
              input.insertAdjacentHTML('afterend', `<p class="text-red-500 text-sm mt-1">${msg}</p>`);
            }
          });
        }
      }
    } catch (err) {
      showToast('Network error', 'error');
    } finally {
      // Re-enable button and restore text
      submitBtn.disabled = false;
      btnLabel.textContent = originalText;
    }
  });

  // Time validation with toast
  const timeInput = document.getElementById('interviewTime');
  if (timeInput) {
    timeInput.addEventListener('change', function () {
      const time = this.value;
      if (time) {
        const [hour, minute] = time.split(':').map(Number);
        if (hour < 10 || (hour === 18 && minute > 0) || hour > 18) {
          showToast('Only schedule interviews between 10:00 AM and 6:00 PM.', 'error');
          this.classList.add('border-red-500');
        } else {
          this.classList.remove('border-red-500');
        }
      }
    });
  }
});
</script>