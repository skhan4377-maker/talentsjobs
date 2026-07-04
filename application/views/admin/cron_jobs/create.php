<?php
  $is_edit = isset($job);
  $action_url = $is_edit
    ? base_url('admin/cron/Manage_cron/edit/' . $job->id)   // ← YAHAN CHANGE KIYA
    : base_url('admin/cron/Manage_cron/create');
  
  // Get aggregated stats for edit mode
  if ($is_edit && isset($job_stats)) {
    $total_sent = isset($job_stats->overall->total_emails_sent) ? $job_stats->overall->total_emails_sent : 0;
    $total_executions = isset($job_stats->overall->total_executions) ? $job_stats->overall->total_executions : 0;
    $last_run = isset($job->last_run) ? $job->last_run : null;
  }
?>

<div class="bg-white rounded-lg shadow-sm">
    <div class="px-6 py-4 border-b border-gray-200">
        <h2 class="text-xl font-semibold text-gray-800">
            <?= $is_edit ? 'Edit Email Automation' : 'Create New Email Automation' ?>
        </h2>
        <p class="text-gray-600 text-sm mt-1">
            <?= $is_edit ? 'Update your email automation settings' : 'Set up a new automated email campaign' ?>
        </p>
    </div>

    <div class="p-6">
        <div id="responseMessage" class="hidden px-4 py-3 rounded-lg mb-6 text-sm"></div>

        <form id="cronJobForm" class="space-y-6">
           
            <!-- Job Information -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Job Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
						Job Name <span class="text-red-500">*</span>
					</label>
                    <!-- Add field validation classes -->
					<input type="text" name="name" id="name" required
						   value="<?= $is_edit ? htmlspecialchars($job->name) : '' ?>"
						   placeholder="e.g., Welcome Email Campaign"
						   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2
						   focus:ring-blue-500 focus:border-blue-500 transition-all validation-field">
                </div>
				
				<!-- Cron Model -->
					<div>
						<label for="cron_model" class="block text-sm font-medium text-gray-700 mb-2">
							Cron Model *
						</label>

						<input 
							type="text" 
							name="cron_model" 
							id="cron_model" 
							required
							value="<?= ($is_edit ? $job->cron_model : '') ?>"
							placeholder="Enter model name like: CronManager_model"
							class="w-full px-4 py-2 border border-gray-300 rounded-lg 
								   focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
						/>

						<p class="text-xs text-gray-500 mt-1">
							Enter the PHP model class that will handle this cron job
						</p>
					</div>


                <!-- Context -->
				 <div>
					<label for="context" class="block text-sm font-medium text-gray-700 mb-2">
						Cron Method Name *
					</label>
					<input type="text" name="context" id="context" required
						   value="<?= $is_edit ? htmlspecialchars($job->context) : '' ?>"
						   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
						   placeholder="Enter method name (e.g., plan_automation)">
					<p class="mt-1 text-sm text-gray-500">
						Enter the method name that exists in your cron model
					</p>	
				</div>
				<div>
					<label for="email_service" class="block text-sm font-medium text-gray-700 mb-2">
							Email Service *
						</label>
						<select name="email_service" id="email_service" required
								class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
							<option value="mailercloud" <?= ($is_edit && ($job->email_service ?? '') === 'mailercloud') ? 'selected' : '' ?>>Mailercloud</option>
							<option value="smtp" <?= ($is_edit && ($job->email_service ?? '') === 'smtp') ? 'selected' : '' ?>>SMTP</option>
						</select>
				</div>
            </div>

            <!-- Description -->
            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                    Description
                </label>
                <textarea name="description" id="description" rows="3"
                          placeholder="Describe what this automated email does..."
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"><?= $is_edit ? htmlspecialchars($job->description) : '' ?></textarea>
            </div>

            <!-- Schedule Settings -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Schedule Type -->
                <div>
                    <label for="schedule_type" class="block text-sm font-medium text-gray-700 mb-2">
                        Schedule Type *
                    </label>
                    <select name="schedule_type" id="schedule_type" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                        <option value="daily" <?= $is_edit && $job->schedule_type == 'daily' ? 'selected' : '' ?>>Daily</option>
                        <option value="weekly" <?= $is_edit && $job->schedule_type == 'weekly' ? 'selected' : '' ?>>Weekly</option>
                        <option value="monthly" <?= $is_edit && $job->schedule_type == 'monthly' ? 'selected' : '' ?>>Monthly</option>
                        <option value="custom" <?= $is_edit && $job->schedule_type == 'custom' ? 'selected' : '' ?>>Custom</option>
                    </select>
                </div>

                <!-- Custom Schedule (Conditional) -->
                <div id="custom_schedule_field" style="display: none;">
                    <label for="custom_schedule" class="block text-sm font-medium text-gray-700 mb-2">
                        Custom Schedule
                    </label>
                    <input type="datetime-local" name="custom_schedule" id="custom_schedule"
                           value="<?= $is_edit && $job->custom_schedule ? date('Y-m-d\TH:i', strtotime($job->custom_schedule)) : '' ?>"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                </div>
			
			
				<div id="weekly_days_field" style="display:none;">
					<label class="block text-sm font-medium text-gray-700 mb-2">Select Days (Weekly)</label>
					<div class="grid grid-cols-3 gap-2">
						<?php 
						$days = ['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'];
						foreach($days as $day): ?>
							<label class="inline-flex items-center">
								<input type="checkbox" name="week_days[]" value="<?= $day ?>" 
									<?= ($is_edit && in_array($day, explode(',', $job->week_days ?? ''))) ? 'checked' : '' ?>
									class="form-checkbox">
								<span class="ml-2"><?= $day ?></span>
							</label>
						<?php endforeach; ?>
					</div>
				</div>	
				
				<div id="month_day_field" style="display:none;">
				<label class="block text-sm font-medium text-gray-700 mb-2">Select Date (Monthly)</label>
				<select name="month_day" class="w-full px-4 py-2 border rounded-lg">
					<option value="">-- Select Date --</option>
					<?php for($i=1;$i<=28;$i++): ?>
						<option value="<?= $i ?>" <?= ($is_edit && $job->month_day==$i) ? 'selected' : '' ?>><?= $i ?></option>
					<?php endfor; ?>
				</select>
			</div>

                <!-- Emails Per Run -->
                <div>
                    <label for="emails_per_run" class="block text-sm font-medium text-gray-700 mb-2">
                        Emails Per Run *
                    </label>
                    <input type="number" name="emails_per_run" id="emails_per_run" required min="1" 
                           value="<?= $is_edit ? $job->emails_per_run : '10' ?>"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                    <p class="text-xs text-gray-500 mt-1">Maximum number of emails to send per execution</p>
                </div>

            </div>

            <!-- ============================ -->
            <!-- OPERATING HOURS SECTION -->
            <!-- ============================ -->
            <div class="border-t border-gray-200 pt-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Operating Hours</h3>
                <p class="text-sm text-gray-600 mb-6">Set the time window when this automation should run each day</p>
                
                <!-- Operating Hours Inputs -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Start Time -->
                    <div>
                        <label for="start_time" class="block text-sm font-medium text-gray-700 mb-2">
                            Start Time *
                        </label>
                        <input type="time" name="start_time" id="start_time" required
                               value="<?= $is_edit ? $job->start_time : '09:00' ?>"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                        <p class="text-xs text-gray-500 mt-1">When to start sending emails each day</p>
                    </div>

                    <!-- End Time -->
                    <div>
                        <label for="end_time" class="block text-sm font-medium text-gray-700 mb-2">
                            End Time *
                        </label>
                        <input type="time" name="end_time" id="end_time" required
                               value="<?= $is_edit ? $job->end_time : '18:00' ?>"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                        <p class="text-xs text-gray-500 mt-1">When to stop sending emails each day</p>
                    </div>

                    <!-- Timezone -->
                    <div>
                        <label for="timezone" class="block text-sm font-medium text-gray-700 mb-2">
                            Timezone *
                        </label>
                        <select name="timezone" id="timezone" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                            <?php
                            $timezones = [
                                'Asia/Kolkata' => 'IST (India)',
                                'UTC' => 'UTC',
                                'America/New_York' => 'EST (New York)',
                                'America/Los_Angeles' => 'PST (Los Angeles)',
                                'Europe/London' => 'GMT (London)',
                                'Europe/Paris' => 'CET (Paris)',
                                'Asia/Tokyo' => 'JST (Tokyo)',
                                'Australia/Sydney' => 'AEST (Sydney)'
                            ];
                            foreach ($timezones as $value => $label) {
                                $selected = ($is_edit && $job->timezone === $value) ? 'selected' : '';
                                echo "<option value=\"$value\" $selected>$label</option>";
                            }
                            ?>
                        </select>
                    </div>
                </div>

                <!-- Operating Hours Display -->
                <div class="bg-blue-50 rounded-lg p-4 mt-4">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span class="text-sm font-medium text-blue-800">Operating Hours: </span>
                        <span class="text-sm text-blue-600 ml-2" id="operatingHoursDisplay">
                            <?= $is_edit ? 
                                "{$job->start_time} to {$job->end_time} ({$job->timezone})" : 
                                "09:00 to 18:00 (Asia/Kolkata)" 
                            ?>
                        </span>
                    </div>
                    <p class="text-xs text-blue-600 mt-1">
                        Emails will only be sent during these hours. Outside this window, the job will pause automatically.
                    </p>
                </div>
            </div>

            <?php if ($is_edit && isset($job_stats)): ?>
            <!-- Performance Stats -->
            <div class="bg-gray-50 rounded-lg p-4">
                <h3 class="text-sm font-medium text-gray-700 mb-3">Performance Statistics</h3>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-4 text-sm">
                    <div>
                        <span class="text-gray-600">Total Sent:</span>
                        <span class="font-medium ml-2"><?= number_format($total_sent) ?></span>
                    </div>
                    <div>
                        <span class="text-gray-600">Last Run:</span>
                        <span class="font-medium ml-2"><?= $last_run ? date('M j, g:i A', strtotime($last_run)) : 'Never' ?></span>
                    </div>
                    <div>
                        <span class="text-gray-600">Executions:</span>
                        <span class="font-medium ml-2"><?= number_format($total_executions) ?></span>
                    </div>
                </div>
                
                <?php if (isset($job_stats->success_rate) || isset($job_stats->open_rate)): ?>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm mt-4 pt-4 border-t border-gray-200">
                    <?php if (isset($job_stats->success_rate)): ?>
                    <div>
                        <span class="text-gray-600">Success Rate:</span>
                        <span class="font-medium ml-2 <?= $job_stats->success_rate >= 90 ? 'text-green-600' : ($job_stats->success_rate >= 70 ? 'text-yellow-600' : 'text-red-600') ?>">
                            <?= $job_stats->success_rate ?>%
                        </span>
                    </div>
                    <?php endif; ?>
                    
                    <?php if (isset($job_stats->open_rate)): ?>
                    <div>
                        <span class="text-gray-600">Open Rate:</span>
                        <span class="font-medium ml-2 <?= $job_stats->open_rate >= 30 ? 'text-green-600' : ($job_stats->open_rate >= 15 ? 'text-yellow-600' : 'text-red-600') ?>">
                            <?= $job_stats->open_rate ?>%
                        </span>
                    </div>
                    <?php endif; ?>
                    
                    <?php if (isset($job_stats->click_rate)): ?>
                    <div>
                        <span class="text-gray-600">Click Rate:</span>
                        <span class="font-medium ml-2 <?= $job_stats->click_rate >= 10 ? 'text-green-600' : ($job_stats->click_rate >= 5 ? 'text-yellow-600' : 'text-red-600') ?>">
                            <?= $job_stats->click_rate ?>%
                        </span>
                    </div>
                    <?php endif; ?>
                    
                    <?php if (isset($job_stats->error_rate)): ?>
                    <div>
                        <span class="text-gray-600">Error Rate:</span>
                        <span class="font-medium ml-2 <?= $job_stats->error_rate <= 5 ? 'text-green-600' : ($job_stats->error_rate <= 15 ? 'text-yellow-600' : 'text-red-600') ?>">
                            <?= $job_stats->error_rate ?>%
                        </span>
                    </div>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </div>
            <?php endif; ?>

            <!-- Activation -->
            <div class="flex items-center space-x-3 p-4 bg-blue-50 rounded-lg">
                <input type="checkbox" name="is_active" id="is_active" value="1"
                       <?= $is_edit && $job->is_active ? 'checked' : '' ?>
                       class="h-5 w-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                <div>
                    <label for="is_active" class="text-sm font-medium text-gray-700">
                        Activate this automation
                    </label>
                    <p class="text-xs text-gray-500 mt-1">
                        When active, this job will run automatically according to its schedule
                    </p>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex justify-between items-center pt-4 border-t border-gray-200">
                <a href="<?= base_url('admin/cron/Manage_cron') ?>" 
                   class="px-4 py-2 text-gray-600 hover:text-gray-800 transition-colors flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Back to List
                </a>
                <button type="submit"
                        class="bg-blue-600 text-white px-6 py-2 rounded-lg shadow hover:bg-blue-700 transition-all flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="<?= $is_edit ? 'M5 13l4 4L19 7' : 'M12 4v16m8-8H4' ?>"></path>
                    </svg>
                    <?= $is_edit ? 'Update Automation' : 'Create Automation' ?>
                </button>
            </div>
        </form>
    
	</div>
</div>

<script>
$(document).ready(function() {

    /* =========================================
       ✅ TOGGLE SCHEDULE FIELDS
    ========================================= */
    $('#schedule_type').on('change', function() {
        const val = $(this).val();
        if(val === 'custom') {
            $('#custom_schedule_field').show();
            $('#weekly_days_field, #month_day_field').hide();
        } else if(val === 'weekly') {
            $('#weekly_days_field').show();
            $('#custom_schedule_field, #month_day_field').hide();
        } else if(val === 'monthly') {
            $('#month_day_field').show();
            $('#custom_schedule_field, #weekly_days_field').hide();
        } else {
            $('#custom_schedule_field, #weekly_days_field, #month_day_field').hide();
        }
    }).trigger('change');


    /* =========================================
       ✅ OPERATING HOURS DISPLAY
    ========================================= */
    function updateOperatingHoursDisplay() {
        const startTime = $('#start_time').val();
        const endTime = $('#end_time').val();
        const timezone = $('#timezone option:selected').text();
        $('#operatingHoursDisplay').text(`${startTime} to ${endTime} (${timezone})`);
    }

    updateOperatingHoursDisplay();
    $('#start_time, #end_time, #timezone').on('change', updateOperatingHoursDisplay);


    /* =========================================
       ✅ TIME RANGE VALIDATION
    ========================================= */
    function validateTimeRange() {
        const startTime = $('#start_time').val();
        const endTime = $('#end_time').val();

        if (startTime && endTime && startTime >= endTime) {
            $('#responseMessage')
                .removeClass()
                .addClass('bg-red-100 text-red-800 px-4 py-3 rounded-lg mb-6 text-sm')
                .html('❌ End time must be after start time')
                .show();
            return false;
        }
        return true;
    }

    $('#start_time, #end_time').on('change', function() {
        validateTimeRange();
    });


    /* =========================================
       ✅ FORM VALIDATION FUNCTIONS
    ========================================= */
    function validateForm() {
        const name = $('#name').val().trim();
        const cronModel = $('#cron_model').val();
        const context = $('#context').val();
        const scheduleType = $('#schedule_type').val();
        const emailsPerRun = $('#emails_per_run').val();
        //const queuePerRun = $('#queue_per_run').val();          // ✅ नया फ़ील्ड
        const timezone = $('#timezone').val();
        const startTime = $('#start_time').val();
        const endTime = $('#end_time').val();
        
        let errors = [];

        // Basic required field validation
        if (!name) errors.push('Job Name is required');
        if (!cronModel) errors.push('Cron Model is required');
        if (!context) errors.push('Email Type is required');
        if (!emailsPerRun || emailsPerRun < 1) errors.push('Valid Emails Per Run is required (minimum 1)');
        //if (!queuePerRun || queuePerRun < 1) errors.push('Valid Queue Per Run is required (minimum 1)');
        if (!timezone) errors.push('Timezone is required');
        
        // Schedule-specific validation
        if (scheduleType === 'weekly') {
            const weekDays = $('input[name="week_days[]"]:checked').length;
            if (weekDays === 0) errors.push('Please select at least one day for weekly schedule');
        }
        
        if (scheduleType === 'monthly') {
            const monthDay = $('select[name="month_day"]').val();
            if (!monthDay) errors.push('Please select a date for monthly schedule');
        }
        
        if (scheduleType === 'custom') {
            const customSchedule = $('#custom_schedule').val();
            if (!customSchedule) {
                errors.push('Custom schedule date is required');
            } else if (new Date(customSchedule) <= new Date()) {
                errors.push('Custom schedule must be a future date');
            }
        } else {
            // For non-custom schedules, validate operating hours
            if (!startTime || !endTime) {
                errors.push('Start Time and End Time are required');
            } else if (startTime >= endTime) {
                errors.push('End Time must be after Start Time');
            }
        }

        return errors;
    }

    /* =========================================
       ✅ REAL-TIME FIELD VALIDATION
    ========================================= */
    function validateField(field) {
        const value = field.val().trim();
        
        if (field.attr('required') && !value) {
            field.addClass('border-red-500')
                 .removeClass('border-green-500 border-gray-300');
            return false;
        } else if (value) {
            field.addClass('border-green-500')
                 .removeClass('border-red-500 border-gray-300');
            return true;
        } else {
            field.removeClass('border-red-500 border-green-500')
                 .addClass('border-gray-300');
            return true;
        }
    }

    // Apply validation on blur for required fields
    $('#name, #cron_model, #context, #emails_per_run').on('blur', function() {
        validateField($(this));
    });


    /* =========================================
       ✅ FORM SUBMISSION WITH VALIDATION
    ========================================= */
    $('#cronJobForm').on('submit', function(e) {
        e.preventDefault();

        // Clear previous messages
        $('#responseMessage').hide().empty();
        
        // Validate form
        const validationErrors = validateForm();
        if (validationErrors.length > 0) {
            $('#responseMessage')
                .removeClass()
                .addClass('bg-red-100 text-red-800 px-4 py-3 rounded-lg mb-6 text-sm')
                .html('❌ ' + validationErrors.join('<br>'))
                .show();
            
            // Scroll to error message
            $('html, body').animate({
                scrollTop: $('#responseMessage').offset().top - 100
            }, 300);
            return false;
        }

        // Validate time range
        if (!validateTimeRange()) return false;

        const submitBtn = $(this).find('button[type="submit"]');
        const originalText = submitBtn.html();

        submitBtn.prop('disabled', true).html('⏳ Processing...');

        // Prepare form data
        let formData = new FormData(this);
        
        // Clean up schedule data based on type
        const scheduleType = $('#schedule_type').val();
        if (scheduleType !== 'weekly') {
            formData.delete('week_days[]');
        }
        if (scheduleType !== 'monthly') {
            formData.delete('month_day');
        }
        if (scheduleType !== 'custom') {
            formData.delete('custom_schedule');
        }

        // ✅ USE GLOBAL CSRF HELPERS
        formData.append(getCSRFName(), getCSRFToken());

        $.ajax({
            url: "<?= $action_url ?>",
            type: "POST",
            data: formData,
            contentType: false,
            processData: false,
            dataType: 'json',

            success: function(res) {
                // ✅ UPDATE CSRF USING GLOBAL FUNCTION
                if (res.csrf_token) {
                    updateCSRFToken(res.csrf_token.hash, res.csrf_token.name);
                }

                const msgBox = $('#responseMessage');

                if (res.status === 'success') {
                    msgBox
                        .removeClass()
                        .addClass('bg-green-100 text-green-800 px-4 py-3 rounded-lg mb-6 text-sm')
                        .html(`✅ ${res.message}`)
                        .show();

                    // Clear form for create mode
                    if (!<?= json_encode($is_edit) ?>) {
                        setTimeout(() => {
                            $('#cronJobForm')[0].reset();
                            updateOperatingHoursDisplay();
                            // Reset border colors
                            $('.validation-field').removeClass('border-red-500 border-green-500')
                                                 .addClass('border-gray-300');
                        }, 500);
                    }

                    // Redirect if provided
                    if (res.redirect) {
                        setTimeout(() => {
                            window.location.href = res.redirect;
                        }, 1500);
                    }

                } else {
                    msgBox
                        .removeClass()
                        .addClass('bg-red-100 text-red-800 px-4 py-3 rounded-lg mb-6 text-sm')
                        .html(`❌ ${res.message}`)
                        .show();
                    
                    // Scroll to error message
                    $('html, body').animate({
                        scrollTop: msgBox.offset().top - 100
                    }, 300);
                }
            },

            error: function(xhr, status, error) {
                let errorMsg = 'Server error occurred. Please try again.';
                
                // Try to parse JSON error response
                if (xhr.responseText) {
                    try {
                        const errorResponse = JSON.parse(xhr.responseText);
                        if (errorResponse.message) {
                            errorMsg = errorResponse.message;
                        }
                    } catch (e) {
                        console.error("Could not parse error response:", e);
                    }
                }
                
                $('#responseMessage')
                    .removeClass()
                    .addClass('bg-red-100 text-red-800 px-4 py-3 rounded-lg mb-6 text-sm')
                    .html(`❌ ${errorMsg}`)
                    .show();
            },

            complete: function() {
                submitBtn.prop('disabled', false).html(originalText);
            }
        });
    });


    /* =========================================
       ✅ ADDITIONAL UX ENHANCEMENTS
    ========================================= */
    
    // Auto-hide success message after some time
    $(document).on('click', function() {
        const msgBox = $('#responseMessage');
        if (msgBox.hasClass('bg-green-100')) {
            setTimeout(() => {
                msgBox.fadeOut(500);
            }, 5000);
        }
    });

    // Show custom schedule field with current datetime + 1 hour as default
    $('#schedule_type').on('change', function() {
        if ($(this).val() === 'custom') {
            // Set default to 1 hour from now
            const now = new Date();
            now.setHours(now.getHours() + 1);
            const formattedDateTime = now.toISOString().slice(0, 16);
            $('#custom_schedule').val(formattedDateTime);
        }
    });

    // Validate custom schedule on change
    $('#custom_schedule').on('change', function() {
        const customDate = new Date($(this).val());
        const now = new Date();
        
        if (customDate <= now) {
            $(this).addClass('border-red-500')
                   .removeClass('border-green-500 border-gray-300');
            $('#responseMessage')
                .removeClass()
                .addClass('bg-red-100 text-red-800 px-4 py-3 rounded-lg mb-6 text-sm')
                .html('❌ Custom schedule must be a future date')
                .show();
        } else {
            $(this).addClass('border-green-500')
                   .removeClass('border-red-500 border-gray-300');
            $('#responseMessage').hide();
        }
    });

    // Prevent form submission on Enter key except in textareas
    $('#cronJobForm').on('keydown', function(e) {
        if (e.key === 'Enter' && e.target.tagName !== 'TEXTAREA') {
            e.preventDefault();
        }
    });

});
</script>