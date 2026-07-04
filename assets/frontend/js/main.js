// main.js – All frontend logic

(function() {
    'use strict';

    // ============================================
    // Dropdown Toggles
    // ============================================
    function setupDropdown(buttonId, dropdownId) {
        const button = document.getElementById(buttonId);
        const dropdown = document.getElementById(dropdownId);
        if (!button || !dropdown) return;

        button.addEventListener('click', (e) => {
            e.stopPropagation();
            dropdown.classList.toggle('hidden');
            document.querySelectorAll('.dropdown').forEach(d => {
                if (d.id !== dropdownId) d.classList.add('hidden');
            });
        });

        document.addEventListener('click', () => dropdown.classList.add('hidden'));
        window.addEventListener('scroll', () => dropdown.classList.add('hidden'));
    }

    document.addEventListener('DOMContentLoaded', function() {
        setupDropdown('desktopNotificationBtn', 'desktopNotificationDropdown');
        setupDropdown('desktopProfileBtn', 'desktopProfileDropdown');
        setupDropdown('notificationBtn', 'notificationDropdown');
        setupDropdown('profileBtn', 'profileDropdown');
    });

    // ============================================
    // Notification System (only if logged in)
    // ============================================
    if (window.TalentsJobsConfig.isLoggedIn) {
        document.addEventListener('DOMContentLoaded', function() {
            updateNotifications();
            setInterval(updateNotifications, 300000); // every 5 minutes

            function updateNotifications() {
                fetch(window.TalentsJobsConfig.urls.notifications)
                    .then(response => response.json())
                    .then(data => {
                        // Update badges
                        document.querySelectorAll('.notification-badge').forEach(badge => {
                            if (data.unread_count > 0) {
                                badge.classList.remove('hidden');
                                badge.textContent = data.unread_count > 9 ? '9+' : data.unread_count;
                            } else {
                                badge.classList.add('hidden');
                            }
                        });

                        // Build notifications HTML
                        const notificationsHTML = data.notifications.map(notification => `
                            <div class="flex items-start p-2 hover:bg-gray-50 rounded-lg cursor-pointer notification-item ${notification.is_read == 0 ? 'bg-blue-50' : ''}" 
                                 data-id="${notification.id}" data-link="${notification.link || '#'}">
                                <div class="bg-blue-100 p-2 rounded-lg mr-3">
                                    ${getNotificationIcon(notification.type)}
                                </div>
                                <div>
                                    <p class="text-sm ${notification.is_read == 0 ? 'font-medium' : 'font-normal'}">${notification.message}</p>
                                    <p class="text-xs text-gray-500 mt-1">${timeAgo(notification.created_at)}</p>
                                </div>
                            </div>
                        `).join('');

                        // Update containers
                        document.querySelectorAll('.notification-container').forEach(container => {
                            container.innerHTML = notificationsHTML;
                        });

                        // Update count
                        document.querySelectorAll('.notification-title-count').forEach(element => {
                            element.textContent = data.notifications.length;
                        });

                        // Attach click handlers
                        document.querySelectorAll('.notification-item').forEach(item => {
                            item.addEventListener('click', () => {
                                const link = item.dataset.link;
                                markNotificationRead(item.dataset.id, link);
                            });
                        });
                    })
                    .catch(error => console.error('Error fetching notifications:', error));
            }

            function markNotificationRead(notificationId, link) {
                const formData = new URLSearchParams();
                formData.append('notification_id', notificationId);
                formData.append(getCSRFName(), getCSRFToken());

                fetch(window.TalentsJobsConfig.urls.markRead, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: formData.toString()
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        updateNotifications();
                        if (link && link !== '#') {
                            window.location.href = new URL(link, window.TalentsJobsConfig.baseUrl).href;
                        }
                    }
                })
                .catch(error => console.error('Error marking notification as read:', error));
            }

            function getNotificationIcon(type) {
                const icons = {
                    job: 'fas fa-briefcase text-blue-600',
                    message: 'fas fa-envelope text-green-600',
                    alert: 'fas fa-exclamation-circle text-red-600'
                };
                return `<i class="${icons[type] || 'fas fa-bell'}"></i>`;
            }

            function timeAgo(dateString) {
                const date = new Date(dateString);
                const seconds = Math.floor((new Date() - date) / 1000);
                const intervals = {
                    year: 31536000,
                    month: 2592000,
                    week: 604800,
                    day: 86400,
                    hour: 3600,
                    minute: 60
                };
                for (let [unit, sec] of Object.entries(intervals)) {
                    const interval = Math.floor(seconds / sec);
                    if (interval >= 1) return interval + ' ' + unit + (interval > 1 ? 's' : '') + ' ago';
                }
                return 'Just now';
            }
        });
    }

    // ============================================
    // Search Form Handler
    // ============================================
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.querySelector('[action*="browse-jobs"]');
        if (!form) return;

        form.addEventListener('submit', function(e) {
            e.preventDefault();

            const jobTitleInput = document.querySelector('#job_profile_input');
            const cityInput = document.querySelector('#city_input');
            const experienceSelect = document.querySelector('[name="experience"]');

            function slugify(text) {
                return text
                    .toString()
                    .toLowerCase()
                    .trim()
                    .replace(/[^a-z0-9]+/g, '-')
                    .replace(/-+/g, '-')
                    .replace(/^-+|-+$/g, '');
            }

            let jobSlugPart = '';
            let jobTyped = jobTitleInput?.value.trim() || '';
            if (jobTyped) {
                const jobsArray = jobTyped.split(',').map(j => slugify(j.trim())).filter(Boolean);
                jobSlugPart = jobsArray.join('-') + '-';
            }

            let citySlug = 'india';
            let cityTyped = cityInput?.value.trim() || '';
            if (cityTyped) {
                const cityArray = cityTyped.split(',').map(c => slugify(c.trim())).filter(Boolean);
                if (cityArray.length) citySlug = cityArray.join('-');
            }

            let expSlugPart = '';
            let expTyped = experienceSelect?.value.trim() || '';
            if (expTyped) expSlugPart = '-' + slugify(expTyped);

            const queryParams = new URLSearchParams({
                key_word: jobTyped,
                locations: cityTyped,
                experience: expTyped
            }).toString();

            const baseUrl = window.TalentsJobsConfig.urls.browseJobs;
            window.location.href = `${baseUrl}${jobSlugPart}jobs-in-${citySlug}${expSlugPart}?${queryParams}`;
        });
    });

    // ============================================
    // AutoComplete Widget Initializations
    // ============================================
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof AutoCompleteWidget === 'undefined') {
            console.error('AutoCompleteWidget not loaded');
            return;
        }

        // City input (modal)
        const cityInput = document.querySelector('#city_input');
        if (cityInput) {
            new AutoCompleteWidget({
                inputSelector: '#city_input',
                hiddenSelector: '#city_id',
                listSelector: '#city_list',
                apiUrl: window.TalentsJobsConfig.urls.getJobCities,
                minChars: 1,
                multiSelect: true,
                maxResults: 5
            });
        }

        // Job profile input (modal)
        const jobProfileInput = document.querySelector('#job_profile_input');
        if (jobProfileInput) {
            new AutoCompleteWidget({
                inputSelector: '#job_profile_input',
                hiddenSelector: '#job_profile_id',
                listSelector: '#job_profile_list',
                apiUrl: window.TalentsJobsConfig.urls.getSearchData + '?type=job_profile',
                minChars: 1,
                multiSelect: true,
                maxResults: 5
            });
        }

        // Job title (homepage)
        const jobTitleHome = document.querySelector('#job_title_home');
        if (jobTitleHome) {
            new AutoCompleteWidget({
                inputSelector: '#job_title_home',
                hiddenSelector: '#job_profile_id_home',
                listSelector: '#job_profile_list_home',
                apiUrl: window.TalentsJobsConfig.urls.getSearchData + '?type=job_profile',
                minChars: 1,
                multiSelect: true,
                maxResults: 5
            });
        }

        // City input (homepage)
        const cityInputHome = document.querySelector('#city_input_home');
        if (cityInputHome) {
            new AutoCompleteWidget({
                inputSelector: '#city_input_home',
                hiddenSelector: '#city_id_home',
                listSelector: '#city_list_home',
                apiUrl: window.TalentsJobsConfig.urls.getJobCities + '?type=location',
                minChars: 1,
                multiSelect: true,
                maxResults: 5
            });
        }

        // Location filter (jobs page)
        const locationInputJobs = document.querySelector('#location-filter-jobs');
        if (locationInputJobs) {
            new AutoCompleteWidget({
                inputSelector: '#location-filter-jobs',
                hiddenSelector: '#city_id_jobs',
                listSelector: '#city_list_jobs',
                apiUrl: locationInputJobs.getAttribute('data-url') || window.TalentsJobsConfig.urls.getJobCities,
                minChars: 1,
                multiSelect: false,
                maxResults: 5
            });

            locationInputJobs.addEventListener('focus', () => {
                locationInputJobs.dispatchEvent(new Event('input'));
            });
        }

        // Candidate registration page
        const jobTitleRegister = document.querySelector('#designationName');
        if (jobTitleRegister) {
            new AutoCompleteWidget({
                inputSelector: '#designationName',
                hiddenSelector: '#job_profile_id_register',
                listSelector: '#job_profile_list_register',
                apiUrl: window.TalentsJobsConfig.urls.getSearchData + '?type=job_profile',
                minChars: 1,
                multiSelect: false,
                maxResults: 5,
                onSelect: function() {
                    $('#designationName').valid(); // jQuery validation
                }
            });

            $('#designationName').on('blur input change', function() {
                $(this).valid();
            });
        }
    });

  // ============================================
// Dropdown List Positioning & Clear Buttons
// ============================================
document.addEventListener('DOMContentLoaded', function() {
    // ========== Helper functions for list positioning ==========
    function positionList(list, input) {
        if (!list || !input) return;
        const rect = input.getBoundingClientRect();
        list.style.top = rect.bottom + 'px';
        list.style.left = rect.left + 'px';
        list.style.width = rect.width + 'px';
    }

    function showListIfFocused(list, input) {
        if (!list || !input) return;
        if (document.activeElement === input && list.children.length > 0) {
            // Only position fixed lists (modal) – absolute lists use CSS
            if (list.classList.contains('fixed')) {
                positionList(list, input);
            }
            list.classList.remove('hidden');
        } else {
            list.classList.add('hidden');
        }
    }

    function setupListObserver(list, input) {
        if (!list || !input) return;
        const observer = new MutationObserver(() => {
            showListIfFocused(list, input);
        });
        observer.observe(list, { childList: true, subtree: false });
    }

    // ========== Clear button visibility ==========
    function updateClearButtonVisibility(input, button) {
        if (!input || !button) return;
        if (input.value.trim().length > 0) {
            button.classList.remove('hidden');
        } else {
            button.classList.add('hidden');
        }
    }

    // ========== Attach behaviors to all input/list pairs ==========
    const pairs = [
        { input: document.getElementById('job_profile_input'), list: document.getElementById('job_profile_list') },
        { input: document.getElementById('city_input'), list: document.getElementById('city_list') },
        { input: document.getElementById('job_title_home'), list: document.getElementById('job_profile_list_home') },
        { input: document.getElementById('city_input_home'), list: document.getElementById('city_list_home') },
        { input: document.getElementById('location-filter-jobs'), list: document.getElementById('city_list_jobs') },
        { input: document.getElementById('designationName'), list: document.getElementById('job_profile_list_register') }
    ];

    function attachListBehavior(input, list) {
        if (!input || !list) return;
        input.addEventListener('focus', () => showListIfFocused(list, input));
        input.addEventListener('blur', () => {
            setTimeout(() => {
                if (!list.matches(':hover')) {
                    list.classList.add('hidden');
                }
            }, 200);
        });
        setupListObserver(list, input);
    }

    pairs.forEach(pair => {
        if (pair.input && pair.list) {
            attachListBehavior(pair.input, pair.list);
        }
    });

    // Hide lists on scroll/resize
    window.addEventListener('scroll', () => {
        document.querySelectorAll('[id$="_list"], [id$="_list_home"], [id$="_list_jobs"], [id$="_list_register"]').forEach(list => {
            list.classList.add('hidden');
        });
    });
    window.addEventListener('resize', () => {
        document.querySelectorAll('[id$="_list"], [id$="_list_home"], [id$="_list_jobs"], [id$="_list_register"]').forEach(list => {
            list.classList.add('hidden');
        });
    });

    // ========== Clear buttons: visibility on input and click ==========
    document.querySelectorAll('.clear-input').forEach(button => {
        const inputId = button.dataset.input;
        const listId = button.dataset.list;
        const hiddenId = button.dataset.hidden;

        const input = document.getElementById(inputId);
        const list = document.getElementById(listId);
        const hidden = document.getElementById(hiddenId);

        if (!input) return;

        // Initial visibility check
        updateClearButtonVisibility(input, button);

        // Update visibility on input event
        input.addEventListener('input', () => {
            updateClearButtonVisibility(input, button);
        });

        // Clear button click handler
        button.addEventListener('click', function(e) {
            e.preventDefault();

            if (input) {
                input.value = '';
                input.dispatchEvent(new Event('input', { bubbles: true }));
            }
            if (hidden) hidden.value = '';
            if (list) {
                list.innerHTML = '';
                list.classList.add('hidden');
            }
            // Button will be hidden by the input event handler above
        });
    });
});
})();