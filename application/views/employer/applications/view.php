<style>
@keyframes slide-in {
    from { transform: translateX(100%); }
    to { transform: translateX(0); }
}

@keyframes fade-out {
    from { opacity: 1; }
    to { opacity: 0; }
}

.animate-slide-in {
    animation: slide-in 0.3s ease-out;
}

.animate-fade-out {
    animation: fade-out 0.3s ease-in forwards;
}
</style>

<div class="container mx-auto px-2 py-8">
    <div class="bg-white rounded-xl shadow-lg p-6 lg:p-8">
        <!-- Header Section -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
            <div class="flex-1">
                <!-- Candidate Name + Avatar -->
                <div class="flex items-center gap-4 mb-4">
                    <?php
                        $firstName = trim($application['name'] ?? '');
                        $lastName  = trim($application['last_name'] ?? '');
                        $fullName  = trim($firstName . ' ' . $lastName);

                        if (!empty($application['logo'])) {
                            $imgSrc = base_url($application['logo']);
                            echo '<img src="' . $imgSrc . '" alt="' . htmlspecialchars($fullName) . '" class="h-16 w-16 rounded-full object-cover">';
                        } else {
                            $initials = '';
                            if (!empty($firstName)) $initials .= mb_substr($firstName, 0, 1);
                            if (!empty($lastName))  $initials .= mb_substr($lastName, 0, 1);
                            $initials = strtoupper($initials);
                            if (empty($initials)) $initials = 'NA';

                            $bgColor = sprintf('#%06X', crc32($fullName) & 0xFFFFFF);

                            $svg = "<svg xmlns='http://www.w3.org/2000/svg' width='64' height='64'>
                                <rect width='100%' height='100%' rx='50%' fill='{$bgColor}'/>
                                <text x='50%' y='50%' dy='.05em' text-anchor='middle' alignment-baseline='middle'
                                      font-family='Arial, Helvetica, sans-serif' font-size='24' fill='#ffffff' font-weight='700'>{$initials}</text>
                            </svg>";
                            $svgUrl = 'data:image/svg+xml;utf8,' . rawurlencode($svg);

                            echo '<img src="' . $svgUrl . '" alt="' . htmlspecialchars($fullName) . '" class="h-16 w-16 rounded-full">';
                        }
                    ?>

                    <div>
                        <div class="flex items-center gap-2 flex-wrap">
                            <h1 class="text-2xl lg:text-3xl font-bold text-gray-800">
                                <?= htmlspecialchars($fullName) ?>
                            </h1>
                            <!-- ✅ PREMIUM ICON (icon only, no text) -->
                            <?php if (!empty($application['active_features'])): ?>
                                <span 
                                    class="inline-flex items-center justify-center w-5 h-5 rounded-full text-xs bg-gradient-to-br from-yellow-300 to-amber-500 text-white shadow-sm"
                                    title="Premium Subscriber"
                                ><i class="fas fa-crown"></i></span>
                            <?php endif; ?>
                        </div>
                        <span class="text-lg text-gray-500"><?= htmlspecialchars($application['designations'] ?? 'Not Specified') ?></span>
                    </div>
                </div>

                <!-- Status Badges -->
                <div class="flex flex-wrap gap-2 mt-3">
                    <span class="px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                        <?= htmlspecialchars($application['work_status'] ?? 'Availability Not Specified') ?>
                    </span>
                    <span class="px-3 py-1 rounded-full text-sm font-medium <?= $application['status'] === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' ?>">
                        Profile Status: <?= ucfirst($application['status'] ?? 'N/A') ?>
                    </span>
                </div>
            </div>

            <!-- Application Stage Badge - Right Side -->
            <?php $stage = $application['ApplicationStage'] ?? 'Applied'; ?>
            <div class="sm:self-start mt-4 sm:mt-0">
                <span class="px-4 py-2 rounded-full text-sm font-medium <?php echo get_status_classes($stage); ?>">
                    <i class="<?php echo get_status_icon_class($stage); ?> text-xs mr-2"></i>
                    <?php echo htmlspecialchars($stage, ENT_QUOTES, 'UTF-8'); ?>
                </span>
            </div>
        </div>

        <!-- Main Content Grid (unchanged) -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left Column - Professional Details -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Contact Information -->
                <div class="bg-gray-50 rounded-xl p-6">
                    <h3 class="text-xl font-semibold mb-4 flex items-center">
                        <i class="fas fa-address-card text-blue-600 mr-2"></i>
                        Contact Information
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-gray-600">
                        <p>
                            <i class="fas fa-envelope mr-2"></i>
                            <button 
                                type="button" 
                                class="reveal-btn text-blue-600 underline hover:text-blue-800"
                                data-type="email"
                                data-value="<?= htmlspecialchars($application['email'] ?? 'N/A') ?>"
                                data-status="<?= $status ?>"
                                data-verified="<?= $is_verified ?>"
                                data-profile="<?= $profile_complete ?>"
                            >
                                View Email
                            </button>
                            <span class="hidden reveal-result ml-2 text-sm text-gray-700"></span>
                        </p>
                        <p>
                            <i class="fas fa-phone mr-2"></i>
                            <button 
                                type="button" 
                                class="reveal-btn text-blue-600 underline hover:text-blue-800"
                                data-type="phone"
                                data-value="<?= htmlspecialchars($application['mobile'] ?? 'N/A') ?>"
                                data-status="<?= $status ?>"
                                data-verified="<?= $is_verified ?>"
                                data-profile="<?= $profile_complete ?>"
                            >
                                View Phone Number
                            </button>
                            <span class="hidden reveal-result ml-2 text-sm text-gray-700"></span>
                        </p>
                        <p>
                            <i class="fas fa-map-marker-alt mr-2"></i>
                            <?= htmlspecialchars($application['address'] ?? 'Address Not Provided') ?>
                        </p>
                        <p>
                            <i class="fas fa-globe-americas mr-2"></i>
                            <?= htmlspecialchars($application['city_name'] ?? 'N/A') ?>, <?= htmlspecialchars($application['country'] ?? 'N/A') ?>
                        </p>
                    </div>
                </div>

                <!-- Professional Summary -->
                <div class="bg-gray-50 rounded-xl p-6">
                    <h3 class="text-xl font-semibold mb-4 flex items-center">
                        <i class="fas fa-user-tie text-blue-600 mr-2"></i>
                        Professional Summary
                    </h3>
                    <div class="space-y-4">
                        <?php if(!empty($application['resume_headline'])): ?>
                        <p class="text-gray-600 font-medium">
                            <?= htmlspecialchars($application['resume_headline']) ?>
                        </p>
                        <?php endif; ?>
                        <?php if(!empty($application['about'])): ?>
                        <div class="prose max-w-none text-gray-600">
                            <?= nl2br(htmlspecialchars($application['about'])) ?>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Core Professional Details -->
                <div class="bg-gray-50 rounded-xl p-6">
                    <h3 class="text-xl font-semibold mb-4 flex items-center">
                        <i class="fas fa-briefcase text-blue-600 mr-2"></i>
                        Professional Details
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-gray-600">
                        <div class="space-y-2">
                            <p><i class="fas fa-industry mr-2"></i>Industry: <?= htmlspecialchars($application['industry_name'] ?? 'N/A') ?></p>
                            <p><i class="fas fa-tasks mr-2"></i>Functional Area: <?= htmlspecialchars($application['functional_area'] ?? 'N/A') ?></p>
                        </div>
                        <div class="space-y-2">
                            <p><i class="fas fa-hourglass-half mr-2"></i>Notice Period: <?= htmlspecialchars($application['notice_period'] ?? 'Immediate') ?></p>
                            <p>
                                <i class="fas fa-money-bill-wave mr-2"></i>
                                Current CTC: 
                                <?= isset($application['current_ctc']) && is_numeric($application['current_ctc']) 
                                    ? '₹' . number_format($application['current_ctc']) 
                                    : 'Not Disclosed' ?>
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Skills & Expertise -->
                <div class="bg-gray-50 rounded-xl p-6">
                    <h3 class="text-xl font-semibold mb-4 flex items-center">
                        <i class="fas fa-tools text-blue-600 mr-2"></i>
                        Skills & Expertise
                    </h3>
                    <div class="flex flex-wrap gap-2">
                        <?php if(!empty($application['skills'])): ?>
                            <?php foreach($application['skills'] as $skill): ?>
                                <?php if(trim($skill)): ?>
                                    <span class="px-3 py-1 bg-white rounded-full text-sm border border-gray-200">
                                        <?= htmlspecialchars($skill) ?>
                                    </span>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="text-gray-500">No skills listed</p>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Employment History -->
                <div class="bg-gray-50 rounded-xl p-6">
                    <h3 class="text-xl font-semibold mb-4 flex items-center">
                        <i class="fas fa-briefcase text-blue-600 mr-2"></i>
                        Work Experience
                    </h3>
                    <div class="space-y-3 text-gray-700 text-sm">
                        <?php if (!empty($application['employment_details'])): ?>
                            <?php foreach($application['employment_details'] as $job): ?>
                                <?php
                                    $from = !empty($job['start_date']) ? date('M Y', strtotime($job['start_date'])) : '';
                                    $to = (!empty($job['is_current']) && $job['is_current']) ? 'Present' :
                                          ((!empty($job['end_date']) && $job['end_date'] !== '0000-00-00') ? date('M Y', strtotime($job['end_date'])) : '');
                                ?>
                                <div>
                                    <div class="font-semibold"><?= htmlspecialchars($job['job_title']) ?></div>
                                    <div><?= htmlspecialchars($job['employer_name']) ?> (<?= $from ?> - <?= $to ?>)</div>
                                    <div class="text-xs"><?= htmlspecialchars($job['work_location']) ?></div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p>No employment history available.</p>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Education History -->
                <div class="bg-gray-50 rounded-xl p-6">
                    <h3 class="text-xl font-semibold mb-4 flex items-center">
                        <i class="fas fa-graduation-cap text-blue-600 mr-2"></i>
                        Education
                    </h3>
                    <div class="space-y-3 text-gray-700 text-sm">
                        <?php if (!empty($application['education'])): ?>
                            <?php foreach($application['education'] as $edu): ?>
                                <?php
                                    $start = $edu['startYear'] ?? '';
                                    $end   = $edu['endYear'] ?? '';
                                    $range = ($start || $end) ? " ($start - $end)" : '';
                                ?>
                                <div>
                                    <div class="font-semibold"><?= htmlspecialchars($edu['degreeName']) ?></div>
                                    <div><?= htmlspecialchars($edu['institutionName']) . $range ?></div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p>No education records found.</p>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Preferred Locations -->
                <div class="bg-gray-50 rounded-xl p-6">
                    <h3 class="text-xl font-semibold mb-4 flex items-center">
                        <i class="fas fa-map-marked-alt text-blue-600 mr-2"></i>
                        Preferred Locations
                    </h3>
                    <div class="flex flex-wrap gap-2">
                        <?php if (!empty($application['preferred_locations'])): ?>
                            <?php foreach($application['preferred_locations'] as $city): ?>
                                <span class="bg-white border border-gray-200 px-3 py-1 rounded-full text-sm"><?= htmlspecialchars($city) ?></span>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="text-gray-500">No preferred locations specified.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Right Column - Actions & Timeline -->
            <div class="space-y-6">
                <!-- Account Status -->
                <div class="bg-gray-50 rounded-lg p-4 space-y-3">
                    <h3 class="text-xl font-semibold mb-2 flex items-center">
                        <i class="fas fa-user-shield text-blue-600 mr-2"></i>
                        Account Verification
                    </h3>
                    <div class="grid grid-cols-1 gap-3">
                        <div class="flex items-center">
                            <?php if($application['is_verified']): ?>
                                <i class="fas fa-check-circle text-green-600 mr-3"></i>
                                <span class="text-gray-600">Email Verified</span>
                            <?php else: ?>
                                <i class="fas fa-exclamation-triangle text-yellow-600 mr-3"></i>
                                <span class="text-gray-600">Email Not Verified</span>
                            <?php endif; ?>
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-clock text-blue-600 mr-3"></i>
                            <span class="text-gray-600">
                                Last Active: <?= $application['last_login'] ? time_elapsed_string($application['last_login']) : 'Never' ?>
                            </span>
                        </div>
                    </div>
                </div>

                <?php 
                    $currentStage = $application['ApplicationStage'] ?? 'Applied';
                    $transitions  = get_application_statuses($currentStage);
                ?>

               <!-- ========== Manage Application - DYNAMIC TOGGLE ========== -->
				<div class="bg-gray-50 rounded-xl p-6 space-y-4">
					<h3 class="text-xl font-semibold mb-2 flex items-center">
						<i class="fas fa-tasks text-blue-600 mr-2"></i>
						Manage Application
					</h3>

					<div class="relative" id="manageAppContainer" 
						 data-applied-id="<?= $application['applied_id'] ?>" 
						 data-job-id="<?= $application['job_id'] ?>">
						
						<?php
							$currentStage = $application['ApplicationStage'] ?? 'Applied';
							$transitions  = get_application_statuses($currentStage);
						?>
						<?php if(!empty($transitions)): ?>
							<button id="manageAppBtn"
									class="w-full bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition flex items-center justify-between"
									onclick="toggleDropdown()">
								<span><i class="fas fa-cog mr-2"></i> Take Action</span>
								<i class="fas fa-chevron-down text-xs"></i>
							</button>
							<div id="actionDropdown"
								 class="absolute z-20 mt-2 w-full bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-lg hidden">
								<!-- Will be filled by JavaScript -->
								<div id="dropdownOptions" class="py-1">
									<div class="px-4 py-2 text-gray-500 text-sm">Loading options…</div>
								</div>
							</div>
						<?php else: ?>
							<button disabled
									class="w-full bg-gray-300 dark:bg-gray-700 text-gray-500 dark:text-gray-400 px-6 py-3 rounded-lg flex items-center justify-center cursor-not-allowed">
								<i class="fas fa-check-circle mr-2"></i> No Further Actions
							</button>
						<?php endif; ?>
					</div>
				</div>
                <div class="bg-gray-50 rounded-xl p-6">
                    <h3 class="text-xl font-semibold mb-6 flex items-center">
                        <i class="fas fa-history text-blue-600 mr-2"></i>
                        Application Timeline
                    </h3>
                    <div class="relative max-h-[500px] overflow-y-auto pr-2">
                        <div class="space-y-6 border-l-2 border-blue-200 ml-5 pl-6">
                            <?php foreach ($logs as $log): ?>
                                <div class="relative flex items-center gap-4">
                                    <div class="absolute -left-[32px] flex items-center justify-center">
                                        <div class="w-4 h-4 bg-blue-600 border-2 border-white rounded-full shadow-md"></div>
                                    </div>
                                    <div class="bg-white rounded-lg shadow-sm p-4 w-full hover:shadow-md transition">
                                        <div class="flex justify-between items-center">
                                            <div class="text-sm font-medium text-gray-800">
                                                <?= ucwords($log['stage']) ?>
                                                <span class="text-xs text-gray-500 ml-2">
                                                    (<?= ucfirst($log['performed_by']) ?>)
                                                </span>
                                            </div>
                                            <div class="text-xs text-gray-400">
                                                <?= date('M d, Y h:i A', strtotime($log['created_at'])) ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <!-- Attachments -->
                <div class="bg-gray-50 rounded-xl p-6 space-y-4">
                    <h3 class="text-xl font-semibold flex items-center">
                        <i class="fas fa-paperclip text-blue-600 mr-2"></i>
                        Attachments
                    </h3>
                    
                    <?php if(!empty($application['resume'])): ?>
                    <a href="<?= base_url($application['resume']) ?>" 
                       class="w-full flex items-center justify-center bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors"
                       download>
                        <i class="fas fa-file-download mr-2"></i>
                        Download Resume
                    </a>
                    <?php endif; ?>

                    <?php if(!empty($application['portfolioUrl'])): ?>
                    <a href="<?= htmlspecialchars($application['portfolioUrl']) ?>" 
                       target="_blank" 
                       rel="noopener noreferrer"
                       class="w-full flex items-center justify-center bg-purple-600 text-white px-6 py-3 rounded-lg hover:bg-purple-700 transition-colors">
                        <i class="fas fa-briefcase mr-2"></i>
                        View Portfolio
                    </a>
                    <?php endif; ?>

                    <?php if(!empty($application['linkedinProfile'])): ?>
                    <a href="<?= htmlspecialchars($application['linkedinProfile']) ?>" 
                       target="_blank" 
                       rel="noopener noreferrer"
                       class="w-full flex items-center justify-center bg-[#0077b5] text-white px-6 py-3 rounded-lg hover:bg-[#005582] transition-colors">
                        <i class="fab fa-linkedin mr-2"></i>
                        LinkedIn Profile
                    </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
// =====================================================================
// 1. CSRF HELPERS (assumed to be globally available from master template)
//    If not, define them here or use your existing ones.
// =====================================================================

// =====================================================================
// 2. REVEAL BUTTONS (original functionality)
// =====================================================================
document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll(".reveal-btn").forEach(function (btn) {
        btn.addEventListener("click", function () {
            let status = btn.getAttribute("data-status");
            let verified = btn.getAttribute("data-verified");
            let profile = btn.getAttribute("data-profile");
            let value = btn.getAttribute("data-value");
            let type = btn.getAttribute("data-type");
            let resultSpan = btn.nextElementSibling;

            let message = "";

            if (status !== "active") {
                message = `${type === "email" ? "Email" : "Phone"} hidden — Account inactive`;
            } else if (verified == "0") {
                message = `${type === "email" ? "Email" : "Phone"} hidden — Your email account not verified <a href="<?= base_url('employer/profile') ?>" class="ml-2 text-blue-600 underline">Verify Now</a>`;
            } else if (profile == "0") {
                message = `${type === "email" ? "Email" : "Phone"} hidden — complete your profile <a href="<?= base_url('employer/profile') ?>" class="ml-2 text-blue-600 underline">Complete Now</a>`;
            } else {
                message = value;
            }

            resultSpan.innerHTML = message;
            resultSpan.classList.remove("hidden");
            btn.classList.add("hidden");
        });
    });
});

// =====================================================================
// 3. TOAST NOTIFICATIONS
// =====================================================================
function showToast(message, type = 'info') {
    const toast = document.createElement('div');
    toast.className = `fixed bottom-4 right-4 z-50 px-6 py-3 rounded-lg text-white shadow-lg animate-slide-in ${
        type === 'success' ? 'bg-green-500' :
        type === 'error' ? 'bg-red-500' :
        'bg-blue-500'
    }`;
    toast.innerHTML = `
        <div class="flex items-center">
            <i class="fas ${type === 'success' ? 'fa-check-circle' : type === 'error' ? 'fa-exclamation-triangle' : 'fa-info-circle'} mr-2"></i>
            <span>${message}</span>
        </div>
    `;
    document.body.appendChild(toast);
    setTimeout(() => {
        toast.classList.add('animate-fade-out');
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}

// =====================================================================
// 4. STATUS UPDATE (enhanced with optional container refresh)
// =====================================================================
function updateStatus(applied_id, job_id, status, container) {
    let actionText = '';
    switch(status) {
        case 'Rejected': actionText = 'reject this application'; break;
        case 'Shortlist': actionText = 'shortlist this candidate'; break;
        default: actionText = `mark as "${status}"`; break;
    }

    if (!confirm(`Are you sure you want to ${actionText}?`)) {
        return;
    }

    const buttons = document.querySelectorAll('button');
    buttons.forEach(btn => btn.disabled = true);

    // CSRF tokens from global helpers
    const csrfName = getCSRFName();
    const csrfToken = getCSRFToken();

    const form = new URLSearchParams();
    form.append('applied_id', applied_id);
    form.append('job_id', job_id);
    form.append('status', status);
    form.append(csrfName, csrfToken);

    showToast('Processing your request...', 'info');

    fetch(`${window.baseUrl}employer/update_application_status`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: form.toString()
    })
    .then(async response => {
        const data = await response.json();
        if (data.csrf_hash) {
            updateCSRFToken(data.csrf_hash, data.csrf_name);
        }
        if (!response.ok) throw new Error(data.message || 'Request failed');
        return data;
    })
    .then(data => {
        if (data.success) {
            showToast(data.message, 'success');

            // 1. Update the status badge in the page (the big pill at top)
            const badge = document.querySelector('.status-badge') || 
                          document.querySelector('.px-4.py-2.rounded-full.text-sm.font-medium');
            if (badge) {
                // Update icon
                const icon = badge.querySelector('i');
                if (icon) {
                    icon.className = getStatusIconClass(status) + ' text-xs mr-2';
                }
                // Update CSS classes
                let cls = badge.className.split(' ').filter(c => !c.startsWith('bg-') && !c.startsWith('text-'));
                const newClasses = getStatusClass(status);
                badge.className = cls.join(' ') + ' ' + newClasses;
                // Update text (keep the icon if present, else set text)
                const textNode = badge.childNodes[badge.childNodes.length - 1];
                if (textNode) textNode.textContent = ' ' + status;
                else badge.textContent = status;
            }

            // 2. If container is provided, refresh the dropdown (reset cache and close)
            if (container) {
                const dropdown = document.getElementById('actionDropdown');
                if (dropdown) dropdown.classList.add('hidden');
                window.dropdownLoaded = false; // reset cache
            }

            // 3. Re-enable buttons
            buttons.forEach(btn => btn.disabled = false);

            // 4. If server returns a redirect (for interview), follow it
            if (data.redirect) {
                window.location.href = data.redirect;
            }

        } else {
            showToast(data.message || 'Update failed', 'error');
            buttons.forEach(btn => btn.disabled = false);
        }
    })
    .catch(error => {
        showToast(error.message || 'Update failed', 'error');
        buttons.forEach(btn => btn.disabled = false);
    });
}

// =====================================================================
// 5. STATUS HELPER FUNCTIONS (mirror PHP helpers)
// =====================================================================
function getStatusIconClass(status) {
    const map = {
        'Applied': 'far fa-file',
        'Viewed': 'far fa-eye',
        'Under Review': 'fas fa-search',
        'Shortlist': 'fas fa-user-check',
        'Interview Scheduled': 'fas fa-video',
        'Scheduled': 'fas fa-calendar-check',
        'Rescheduled': 'fas fa-calendar-alt',
        'Offer Extended': 'fas fa-hand-holding-usd',
        'Hired': 'fas fa-trophy',
        'Completed': 'fas fa-check-circle',
        'Missed': 'fas fa-user-times',
        'Rejected': 'fas fa-ban',
        'Withdraw': 'fas fa-sign-out-alt',
        'Canceled': 'fas fa-times-circle'
    };
    return map[status] || 'fas fa-question-circle';
}

function getStatusClass(status) {
    const map = {
        'Applied': 'bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-200',
        'Viewed': 'bg-blue-50 text-blue-700 dark:bg-blue-800 dark:text-blue-200',
        'Under Review': 'bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-200',
        'Shortlist': 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-200',
        'Interview Scheduled': 'bg-purple-100 text-purple-800 dark:bg-purple-800 dark:text-purple-200',
        'Scheduled': 'bg-indigo-100 text-indigo-800 dark:bg-indigo-800 dark:text-indigo-200',
        'Rescheduled': 'bg-orange-100 text-orange-800 dark:bg-orange-800 dark:text-orange-200',
        'Offer Extended': 'bg-pink-100 text-pink-800 dark:bg-pink-800 dark:text-pink-200',
        'Hired': 'bg-green-500 text-white dark:bg-green-700',
        'Completed': 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-200',
        'Missed': 'bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-200',
        'Rejected': 'bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-200',
        'Withdraw': 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
        'Canceled': 'bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-200'
    };
    return map[status] || 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300';
}

// =====================================================================
// 6. TOGGLE DROPDOWN LOGIC (dynamic loading without page reload)
// =====================================================================
window.dropdownLoaded = false;

function toggleDropdown() {
    const dropdown = document.getElementById('actionDropdown');
    if (!dropdown) return;
    const isHidden = dropdown.classList.contains('hidden');
    if (isHidden) {
        dropdown.classList.remove('hidden');
        if (!window.dropdownLoaded) {
            loadDropdownOptions();
        }
    } else {
        dropdown.classList.add('hidden');
    }
}

// Close dropdown when clicking outside
document.addEventListener('click', function(e) {
    const container = document.getElementById('manageAppContainer');
    if (container && !container.contains(e.target)) {
        const dropdown = document.getElementById('actionDropdown');
        if (dropdown) dropdown.classList.add('hidden');
    }
});

function loadDropdownOptions() {
    const container = document.getElementById('manageAppContainer');
    const appliedId = container.dataset.appliedId;
    const optionsDiv = document.getElementById('dropdownOptions');
    if (!optionsDiv) return;

    // Show loading
    optionsDiv.innerHTML = '<div class="px-4 py-2 text-gray-500 text-sm">Loading options…</div>';

    fetch(`${window.baseUrl}employer/applications/get_next_statuses/${appliedId}`)
        .then(response => response.json())
        .then(data => {
            const statuses = data.statuses;
            optionsDiv.innerHTML = '';

            if (Object.keys(statuses).length === 0) {
                optionsDiv.innerHTML = '<div class="px-4 py-2 text-gray-500 text-sm">No further actions</div>';
                window.dropdownLoaded = true;
                return;
            }

            // Build buttons for each status
            for (const [statusValue, statusLabel] of Object.entries(statuses)) {
                const item = document.createElement('div');
                item.className = 'border-b border-gray-100 dark:border-gray-700 last:border-0';

                // Special handling for interview statuses: redirect
                if (statusValue === 'Scheduled' || statusValue === 'Rescheduled' || statusValue === 'Interview Scheduled') {
                    const link = document.createElement('a');
                    link.href = `${window.baseUrl}employer/applications/redirect_to_interview/${appliedId}`;
                    link.className = 'block px-4 py-3 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center';
                    link.innerHTML = `<i class="fas fa-calendar-alt mr-2 text-purple-600"></i> ${statusLabel}`;
                    item.appendChild(link);
                } else {
                    const btn = document.createElement('button');
                    btn.className = 'w-full text-left px-4 py-3 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center';
                    const iconClass = getStatusIconClass(statusValue);
                    btn.innerHTML = `<i class="${iconClass} mr-2 ${statusValue === 'Rejected' ? 'text-red-600' : 'text-green-600'}"></i> ${statusLabel}`;
                    btn.dataset.status = statusValue;
                    btn.addEventListener('click', function(e) {
                        e.preventDefault();
                        const container = document.getElementById('manageAppContainer');
                        const appliedId = container.dataset.appliedId;
                        const jobId = container.dataset.jobId;
                        // Call updateStatus with container for refresh
                        updateStatus(appliedId, jobId, statusValue, container);
                    });
                    item.appendChild(btn);
                }
                optionsDiv.appendChild(item);
            }

            window.dropdownLoaded = true;
        })
        .catch(error => {
            optionsDiv.innerHTML = '<div class="px-4 py-2 text-red-500 text-sm">Error loading options</div>';
            console.error('Error fetching statuses:', error);
        });
}

// =====================================================================
// 7. GLOBAL BASE URL (already set in your view, but ensure it exists)
// =====================================================================
window.baseUrl = '<?= base_url() ?>';
</script>