/**
 * custom.js
 * All JavaScript functionality for TalentsJobs portal
 * Includes: mobile menu, dropdowns, notifications, swiper sliders, resume ad handling
 */

// ===================== GLOBAL FUNCTIONS =====================

// Hide resume ad and store timestamp
function hideAd() {
    const ad = document.getElementById('resumeAd');
    if (ad) {
        ad.style.opacity = '0';
        ad.style.transform = 'translateY(-10px)';
        setTimeout(() => {
            ad.style.display = 'none';
        }, 300);
        localStorage.setItem('resumeAdHidden', Date.now());
    }
}

// Return icon HTML based on notification type
function getNotificationIcon(type) {
    const icons = {
        job: 'fas fa-briefcase text-blue-600',
        message: 'fas fa-envelope text-green-600',
        alert: 'fas fa-exclamation-circle text-red-600'
    };
    return `<i class="${icons[type] || 'fas fa-bell'}"></i>`;
}

// Convert timestamp to "time ago" string
function timeAgo(dateString) {
    const date = new Date(dateString);
    const seconds = Math.floor((new Date() - date) / 1000);
    const intervals = {
        year: 31536000, month: 2592000, week: 604800,
        day: 86400, hour: 3600, minute: 60
    };
    for (let [unit, secondsInUnit] of Object.entries(intervals)) {
        const interval = Math.floor(seconds / secondsInUnit);
        if (interval >= 1) return interval + ' ' + unit + (interval === 1 ? '' : 's') + ' ago';
    }
    return 'Just now';
}

// ===================== DOM CONTENT LOADED =====================
document.addEventListener('DOMContentLoaded', function () {

    // ---------- RESUME AD VISIBILITY (from master.php) ----------
    const lastHidden = localStorage.getItem('resumeAdHidden');
    if (lastHidden) {
        const daysSinceHidden = (Date.now() - parseInt(lastHidden)) / (1000 * 60 * 60 * 24);
        if (daysSinceHidden < 2) {
            const ad = document.getElementById('resumeAd');
            if (ad) ad.style.display = 'none';
        }
    }

    // ---------- RESUME CTA LINK (updated to preserve token) ----------
    const resumeCta = document.getElementById('resumeCta');
    if (resumeCta) {
        const currentPath = window.location.pathname
            .replace(/^\/|\/$/g, '')
            .replace(/\//g, '_');
        const utmCampaign = currentPath || 'homepage';
        const utmContent = 'candidate_loggedin_resume_ad';

        // Build final URL preserving any existing query parameters (like token)
        let url = new URL(resumeCta.href);
        url.searchParams.set('utm_source', 'talentsjobs');
        url.searchParams.set('utm_medium', 'referral');
        url.searchParams.set('utm_campaign', utmCampaign);
        url.searchParams.set('utm_content', utmContent);
        
        resumeCta.href = url.toString();
    }

    // ---------- MOBILE MENU TOGGLE (from footer.php) ----------
    const menuBtn = document.getElementById('mobileMenuButton');
    const mobileMenu = document.getElementById('mobileMenu');
    const menuPanel = mobileMenu?.querySelector('.absolute.right-0');
    const closeBtn = document.getElementById('closeMobileMenu');
    const overlay = document.getElementById('mobileMenuOverlay');

    function openMenu() {
        if (!mobileMenu || !menuPanel) return;
        mobileMenu.classList.remove('hidden');
        setTimeout(() => menuPanel.classList.remove('translate-x-full'), 10);
    }

    function closeMenu() {
        if (!mobileMenu || !menuPanel) return;
        menuPanel.classList.add('translate-x-full');
        setTimeout(() => mobileMenu.classList.add('hidden'), 300);
    }

    menuBtn?.addEventListener('click', openMenu);
    closeBtn?.addEventListener('click', closeMenu);
    overlay?.addEventListener('click', closeMenu);
    document.addEventListener('keydown', function (e) {
        if (e.key === "Escape") closeMenu();
    });

    // ---------- DROPDOWN MENUS (from footer.php) ----------
    function setupDropdown(buttonId, dropdownId) {
        const btn = document.getElementById(buttonId);
        const dropdown = document.getElementById(dropdownId);
        if (!btn || !dropdown) return;

        btn.addEventListener('click', function (e) {
            e.stopPropagation();
            dropdown.classList.toggle('hidden');
            // close other dropdowns
            document.querySelectorAll('.dropdown').forEach(d => {
                if (d.id !== dropdownId) d.classList.add('hidden');
            });
        });
    }

    setupDropdown('desktopNotificationBtn', 'desktopNotificationDropdown');
    setupDropdown('desktopProfileBtn', 'desktopProfileDropdown');
    setupDropdown('notificationBtn', 'notificationDropdown');
    setupDropdown('profileBtn', 'profileDropdown');

    // Close dropdowns when clicking outside or scrolling
    document.addEventListener('click', () => {
        document.querySelectorAll('.dropdown').forEach(d => d.classList.add('hidden'));
    });
    window.addEventListener('scroll', () => {
        document.querySelectorAll('.dropdown').forEach(d => d.classList.add('hidden'));
    });

    // ---------- NOTIFICATION SYSTEM (from footer.php) ----------
    function updateNotifications() {
        fetch(BASE_URL + 'notify/notification/get_notifications')
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

                // Build notifications list
                const html = data.notifications.map(notification => `
                    <div class="flex items-start p-2 hover:bg-gray-50 rounded-lg cursor-pointer notification-item ${notification.is_read == 0 ? 'bg-blue-50' : ''}" 
                         data-id="${notification.id}" data-link="${notification.link || '#'}">
                        <div class="bg-blue-100 p-2 rounded-lg mr-3">
                            ${getNotificationIcon(notification.type)}
                        </div>
                        <div>
                            <p class="text-sm ${notification.is_read == 0 ? 'font-medium' : 'font-normal'}">
                                ${notification.message}
                            </p>
                            <p class="text-xs text-gray-500 mt-1">${timeAgo(notification.created_at)}</p>
                        </div>
                    </div>
                `).join('');

                document.querySelectorAll('.notification-container').forEach(c => c.innerHTML = html);
                document.querySelectorAll('.notification-title-count').forEach(e => e.textContent = data.notifications.length);

                // Attach click handlers to each notification
                document.querySelectorAll('.notification-item').forEach(item => {
                    item.addEventListener('click', () => {
                        const link = item.dataset.link;
                        markNotificationRead(item.dataset.id, link);
                    });
                });
            });
    }

    // ✅ FIXED: Use helper functions to get CSRF token
    function markNotificationRead(id, link) {
        const formData = new FormData();
        formData.append('notification_id', id);
        formData.append(getCSRFName(), getCSRFToken());   // Use the helper functions

        fetch(BASE_URL + 'notify/notification/mark_read_ajax', {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                updateNotifications();
                if (link && link !== '#') window.location.href = new URL(BASE_URL + link).href;
            } else {
                console.error('Failed to mark notification as read');
            }
        })
        .catch(error => console.error('Error:', error));
    }

    // Initial load and periodic refresh
    updateNotifications();
    setInterval(updateNotifications, 300000); // every 5 minutes

    // ---------- SWIPER SLIDERS (from footer.php) ----------
    if (document.querySelector('.blog-slider')) {
        new Swiper('.blog-slider', {
            slidesPerView: 1,
            spaceBetween: 20,
            pagination: { el: '.swiper-pagination', clickable: true },
            breakpoints: {
                640: { slidesPerView: 2 },
                1024: { slidesPerView: 3 }
            }
        });
    }

    if (document.querySelector('.companies-slider')) {
        new Swiper('.companies-slider', {
            slidesPerView: 'auto',
            spaceBetween: 20,
            freeMode: true
        });
    }

}); // end DOMContentLoaded