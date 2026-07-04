<div class="container mx-auto">
    <!-- header -->
    <div class="flex flex-wrap items-center justify-between gap-3 mb-6">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-slate-800 tracking-tight flex items-center gap-2">
                <i class="fas fa-database text-blue-600 text-2xl"></i>
                Talent<span class="text-blue-600">Pool</span> HR
            </h1>

            <!-- PLAN CREDITS SHOW HERE -->
            <div id="plan-credits" class="mt-2 flex flex-wrap items-center gap-3 text-xs"></div>

            <p class="text-sm sm:text-base text-slate-500 mt-0.5 flex items-center gap-1 flex-wrap">
                <i class="fas fa-briefcase text-xs"></i> Employer / advanced candidate search
            </p>
        </div>

        <div class="bg-white px-4 py-2 rounded-full shadow-sm border border-slate-200 text-sm flex items-center gap-2">
            <i class="fas fa-user-tie text-blue-500"></i>
            <span class="font-medium">HR workspace</span>
            <span class="bg-blue-100 text-blue-700 text-xs px-2 py-0.5 rounded-full">recruiter</span>
        </div>
    </div>

    <!-- FILTER TRIGGER BUTTON -->
    <div class="bg-white rounded-xl shadow-md border border-slate-200 p-4 sm:p-6 mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div class="flex flex-wrap items-center gap-2 text-slate-700">
                <i class="fas fa-sliders-h text-blue-500"></i>
                <h2 class="font-semibold text-base sm:text-lg">Find candidates</h2>
                <span id="total-matches" class="text-xs bg-slate-100 px-2 py-1 rounded-full text-slate-600">0 matches</span>
            </div>

            <div class="flex flex-wrap gap-2">
                <button id="exportBtn" onclick="downloadExcel()"
                    class="bg-green-600 hover:bg-green-700 text-white px-3 sm:px-5 py-2 rounded-lg text-xs sm:text-sm font-medium inline-flex items-center gap-1 sm:gap-2 shadow-sm transition">
                    <i class="fas fa-file-excel"></i>
                    <span class="hidden sm:inline">Export Excel</span>
                </button>

                <button onclick="openDrawer('filters')"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-3 sm:px-5 py-2 rounded-lg text-xs sm:text-sm font-medium inline-flex items-center gap-1 sm:gap-2 shadow-sm transition">
                    <i class="fas fa-search"></i>
                    <span class="hidden sm:inline">Advanced filters</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Active filters summary -->
    <div id="active-filters" class="flex flex-wrap items-center gap-2 mb-4 hidden">
        <span class="text-sm text-slate-600 mr-1">Active filters:</span>
        <div id="filter-chips" class="flex flex-wrap gap-2"></div>
        <button onclick="resetFilters()" class="text-xs text-red-600 hover:text-red-800 flex items-center gap-1">
            <i class="fas fa-times-circle"></i> Clear all
        </button>
    </div>

    <!-- results caption + download hint -->
    <div class="flex flex-wrap items-center justify-between gap-2 mb-4">
        <div class="flex items-center gap-2">
            <i class="fas fa-users text-slate-400"></i>
            <h3 class="font-semibold text-slate-800 text-lg">Suggested candidates</h3>
            <span id="profile-count" class="bg-slate-200 text-slate-700 text-xs px-2 py-1 rounded-full">0 profiles</span>
        </div>
       
    </div>

    <!-- CANDIDATE GRID -->
    <div id="candidate-grid" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5 sm:gap-6"></div>

    <!-- Load More button -->
    <div id="load-more-container" class="text-center mt-6 hidden">
        <button id="load-more"
            class="bg-white border border-slate-300 text-slate-700 px-6 py-2 rounded-lg text-sm font-medium hover:bg-slate-50 inline-flex items-center gap-2">
            <span class="button-text">Load more</span>
        </button>
        <div id="load-more-error" class="text-red-500 text-sm mt-2 hidden"></div>
    </div>  
</div>

<!-- RIGHT SIDE DRAWER (Tailwind only) -->
<div id="drawer" class="fixed top-0 right-0 h-full w-full sm:w-96 bg-white shadow-2xl z-50 transition-transform duration-300 ease-in-out transform translate-x-full flex flex-col">
    <div class="flex items-center justify-between p-5 border-b border-slate-200">
        <h3 id="drawerTitle" class="text-lg font-semibold text-slate-800">Advanced filters</h3>
        <button onclick="closeDrawer()" class="text-slate-400 hover:text-slate-600 text-2xl leading-5">&times;</button>
    </div>
    <div id="drawerContent" class="flex-1 overflow-y-auto p-5">
        <div id="filtersContent" class="space-y-4">
            <form id="filterForm" onsubmit="event.preventDefault(); applyFilters();">
                <!-- Keywords field -->
                <div>
                    <label class="text-sm font-medium text-slate-600 block mb-1">Keywords</label>
                    <div class="relative">
                        <input type="text" id="filter_keywords" placeholder="e.g. React, manager" 
                               value="<?= htmlspecialchars($filter_keywords ?? '') ?>"
                               class="w-full border border-slate-200 rounded-lg py-2.5 px-3 text-sm focus:ring-2 focus:ring-blue-200 outline-none pr-8"
                               oninput="updateFieldClear('keywords')">
                        <span class="absolute right-2 top-1/2 -translate-y-1/2 text-slate-400 cursor-pointer hidden" onclick="clearInput('keywords')" id="clear_keywords">&times;</span>
                    </div>
                </div>
                <!-- Experience -->
                <div>
                    <label class="text-sm font-medium text-slate-600 block mb-1">Experience</label>
                    <div class="relative">
                        <select id="filter_experience" class="w-full border border-slate-200 rounded-lg py-2.5 px-3 text-sm bg-white focus:ring-2 focus:ring-blue-200 outline-none pr-8"
                                onchange="updateFieldClear('experience')">
                            <option value="">Any</option>
                            <option value="0-2" <?= ($filter_experience ?? '') == '0-2' ? 'selected' : '' ?>>0-2 yrs</option>
                            <option value="3-5" <?= ($filter_experience ?? '') == '3-5' ? 'selected' : '' ?>>3-5 yrs</option>
                            <option value="6+" <?= ($filter_experience ?? '') == '6+' ? 'selected' : '' ?>>6+ yrs</option>
                        </select>
                        <span class="absolute right-2 top-1/2 -translate-y-1/2 text-slate-400 cursor-pointer hidden" onclick="clearInput('experience')" id="clear_experience">&times;</span>
                    </div>
                </div>
                <!-- Location -->
                <div>
                    <label class="text-sm font-medium text-slate-600 block mb-1">Location</label>
                    <div class="relative">
                        <input type="text" id="filter_location" placeholder="City or remote" 
                               value="<?= htmlspecialchars($filter_location ?? '') ?>"
                               class="w-full border border-slate-200 rounded-lg py-2.5 px-3 text-sm focus:ring-2 focus:ring-blue-200 outline-none pr-8"
                               oninput="updateFieldClear('location')">
                        <span class="absolute right-2 top-1/2 -translate-y-1/2 text-slate-400 cursor-pointer hidden" onclick="clearInput('location')" id="clear_location">&times;</span>
                    </div>
                </div>
                <!-- Skills -->
                <div>
                    <label class="text-sm font-medium text-slate-600 block mb-1">Skills (comma separated)</label>
                    <div class="relative">
                        <input type="text" id="filter_skills" placeholder="e.g. Python, Figma" 
                               value="<?= htmlspecialchars($filter_skills ?? '') ?>"
                               class="w-full border border-slate-200 rounded-lg py-2.5 px-3 text-sm focus:ring-2 focus:ring-blue-200 outline-none pr-8"
                               oninput="updateFieldClear('skills')">
                        <span class="absolute right-2 top-1/2 -translate-y-1/2 text-slate-400 cursor-pointer hidden" onclick="clearInput('skills')" id="clear_skills">&times;</span>
                    </div>
                </div>
                <!-- Job type -->
                <div>
                    <label class="text-sm font-medium text-slate-600 block mb-1">Job type</label>
                    <div class="relative">
                        <select id="filter_job_type" class="w-full border border-slate-200 rounded-lg py-2.5 px-3 text-sm bg-white focus:ring-2 focus:ring-blue-200 outline-none pr-8"
                                onchange="updateFieldClear('job_type')">
                            <option value="">Any</option>
                            <option value="Full-time" <?= ($filter_job_type ?? '') == 'Full-time' ? 'selected' : '' ?>>Full-time</option>
                            <option value="Part-time" <?= ($filter_job_type ?? '') == 'Part-time' ? 'selected' : '' ?>>Part-time</option>
                            <option value="Contract" <?= ($filter_job_type ?? '') == 'Contract' ? 'selected' : '' ?>>Contract</option>
                            <option value="Remote" <?= ($filter_job_type ?? '') == 'Remote' ? 'selected' : '' ?>>Remote only</option>
                        </select>
                        <span class="absolute right-2 top-1/2 -translate-y-1/2 text-slate-400 cursor-pointer hidden" onclick="clearInput('job_type')" id="clear_job_type">&times;</span>
                    </div>
                </div>
                <!-- Buttons -->
                <div class="flex justify-end gap-3 pt-3">
                    <button type="button" onclick="closeDrawer()" class="px-5 py-2 border border-slate-300 rounded-lg text-sm font-medium text-slate-700 hover:bg-slate-50">Cancel</button>
                    <button type="button" id="resetBtn" onclick="resetFilters()" class="px-5 py-2 border border-red-300 text-red-600 rounded-lg text-sm font-medium hover:bg-red-50 disabled:opacity-50 disabled:cursor-not-allowed">Reset</button>
                    <button type="submit" class="px-5 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700">Apply filters</button>
                </div>
            </form>
        </div>
        <div id="profileContent" class="hidden space-y-4"></div>
    </div>
</div>
<div id="drawerBackdrop" class="fixed inset-0 bg-black/40 backdrop-blur-sm z-40 hidden" onclick="closeDrawer()"></div>

<!-- Global loader (Tailwind only) -->
<div id="global-loader" class="fixed inset-0 bg-white/80 backdrop-blur-sm flex items-center justify-center z-50 hidden">
    <div class="w-10 h-10 border-4 border-blue-200 border-t-blue-600 rounded-full animate-spin"></div>
</div>
<script>
    // Global variables
    let currentPage = 1;
    let totalPages = 1;
    let isLoading = false;
    let filters = {};

    // On page load, read URL params and set filters
    document.addEventListener('DOMContentLoaded', function() {
        const urlParams = new URLSearchParams(window.location.search);
        filters = {
            keywords: urlParams.get('keywords') || '',
            experience: urlParams.get('experience') || '',
            location: urlParams.get('location') || '',
            skills: urlParams.get('skills') || '',
            job_type: urlParams.get('job_type') || ''
        };

        document.getElementById('filter_keywords').value = filters.keywords;
        document.getElementById('filter_experience').value = filters.experience;
        document.getElementById('filter_location').value = filters.location;
        document.getElementById('filter_skills').value = filters.skills;
        document.getElementById('filter_job_type').value = filters.job_type;

        // Show clear buttons if fields have values
        updateFieldClear('keywords');
        updateFieldClear('experience');
        updateFieldClear('location');
        updateFieldClear('skills');
        updateFieldClear('job_type');

        loadCandidates(1, true);
        updateFilterChips();
        updateResetButtonState();
    });

    // Update clear button visibility and reset button state for a specific field
    function updateFieldClear(field) {
        const input = document.getElementById('filter_' + field);
        const clearBtn = document.getElementById('clear_' + field);
        if (input && clearBtn) {
            const hasValue = (input.value && input.value.trim() !== '');
            clearBtn.style.display = hasValue ? 'block' : 'none';
        }
        updateResetButtonState();
    }

    function formatExperience(years, months) {
        years = parseInt(years) || 0;
        months = parseInt(months) || 0;
        if (years === 0 && months === 0) return 'Fresher';
        let parts = [];
        if (years > 0) parts.push(years + ' yr' + (years > 1 ? 's' : ''));
        if (months > 0) parts.push(months + ' mo' + (months > 1 ? 's' : ''));
        return parts.join(' ');
    }

    function clearInput(field) {
        const input = document.getElementById('filter_' + field);
        if (input) {
            input.value = '';
            if (input.tagName === 'SELECT') {
                input.selectedIndex = 0;
            }
            updateFieldClear(field); // hides clear button and updates reset button state
        }
    }

    function updateResetButtonState() {
        const resetBtn = document.getElementById('resetBtn');
        // Check if any filter input has a non-empty value
        const hasFilters = Object.values(filters).some(v => v !== '');
        resetBtn.disabled = !hasFilters;
    }

    function updateFilterChips() {
        const chipsContainer = document.getElementById('filter-chips');
        const activeFiltersDiv = document.getElementById('active-filters');
        chipsContainer.innerHTML = '';
        let hasFilters = false;

        function addChip(label, filterKey) {
            hasFilters = true;
            const chip = document.createElement('span');
            chip.className = 'inline-flex items-center gap-1 bg-blue-50 text-blue-700 text-xs px-3 py-1.5 rounded-full border border-blue-200';
            chip.innerHTML = `
                ${escapeHtml(label)}
                <button onclick="removeFilter('${filterKey}')" class="text-blue-400 hover:text-blue-700 focus:outline-none">
                    <i class="fas fa-times-circle text-xs"></i>
                </button>
            `;
            chipsContainer.appendChild(chip);
        }

        if (filters.keywords) addChip(`Keywords: ${filters.keywords}`, 'keywords');
        if (filters.experience) {
            let expLabel = '';
            switch (filters.experience) {
                case '0-2': expLabel = '0-2 yrs'; break;
                case '3-5': expLabel = '3-5 yrs'; break;
                case '6+': expLabel = '6+ yrs'; break;
            }
            addChip(`Exp: ${expLabel}`, 'experience');
        }
        if (filters.location) addChip(`Location: ${filters.location}`, 'location');
        if (filters.skills) addChip(`Skills: ${filters.skills}`, 'skills');
        if (filters.job_type) addChip(`Job type: ${filters.job_type}`, 'job_type');

        if (hasFilters) activeFiltersDiv.classList.remove('hidden');
        else activeFiltersDiv.classList.add('hidden');
    }

    function removeFilter(key) {
        filters[key] = '';
        document.getElementById('filter_' + key).value = '';
        updateFieldClear(key); // hides clear button and updates reset button state

        const url = new URL(window.location);
        url.searchParams.delete(key);
        window.history.pushState({}, '', url);

        loadCandidates(1, true);
        updateFilterChips();
        updateResetButtonState();
    }

    function resetFilters() {
        filters = { keywords: '', experience: '', location: '', skills: '', job_type: '' };
        document.getElementById('filter_keywords').value = '';
        document.getElementById('filter_experience').value = '';
        document.getElementById('filter_location').value = '';
        document.getElementById('filter_skills').value = '';
        document.getElementById('filter_job_type').value = '';

        // Hide all clear buttons and update reset button state
        updateFieldClear('keywords');
        updateFieldClear('experience');
        updateFieldClear('location');
        updateFieldClear('skills');
        updateFieldClear('job_type');

        const url = new URL(window.location);
        url.search = '';
        window.history.pushState({}, '', url);

        closeDrawer();
        loadCandidates(1, true);
        updateFilterChips();
        updateResetButtonState();
    }

    function applyFilters() {
        filters = {
            keywords: document.getElementById('filter_keywords').value,
            experience: document.getElementById('filter_experience').value,
            location: document.getElementById('filter_location').value,
            skills: document.getElementById('filter_skills').value,
            job_type: document.getElementById('filter_job_type').value
        };

        const url = new URL(window.location);
        url.searchParams.set('keywords', filters.keywords);
        url.searchParams.set('experience', filters.experience);
        url.searchParams.set('location', filters.location);
        url.searchParams.set('skills', filters.skills);
        url.searchParams.set('job_type', filters.job_type);
        Object.keys(filters).forEach(key => {
            if (!filters[key]) url.searchParams.delete(key);
        });
        window.history.pushState({}, '', url);

        closeDrawer();
        loadCandidates(1, true);
        updateFilterChips();
        updateResetButtonState();
    }

    function showGlobalLoader() { document.getElementById('global-loader').style.display = 'flex'; }
    function hideGlobalLoader() { document.getElementById('global-loader').style.display = 'none'; }

    function loadCandidates(page, reset = false) {
        if (isLoading) return;
        isLoading = true;
        showGlobalLoader();

        const loadMoreBtn = document.getElementById('load-more');
        const loadMoreError = document.getElementById('load-more-error');
        if (loadMoreError) loadMoreError.classList.add('hidden');

        if (!reset && loadMoreBtn && page > 1) {
            loadMoreBtn.querySelector('.button-text').textContent = 'Loading...';
            loadMoreBtn.disabled = true;
        }

        if (reset) showSkeletonCards(6);
        else appendSkeletonCards(3);

        let params = new URLSearchParams();
        params.set('page', page);
        if (filters.keywords) params.set('keywords', filters.keywords);
        if (filters.experience) params.set('experience', filters.experience);
        if (filters.location) params.set('location', filters.location);
        if (filters.skills) params.set('skills', filters.skills);
        if (filters.job_type) params.set('job_type', filters.job_type);

        fetch('<?=base_url('employer/EmployerTalentPool/ajax_get_candidates?')?>' + params.toString())
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                removeSkeletonCards();
            
                document.getElementById('candidate-grid').innerHTML = `
                <div class="col-span-full">
                    <div class="bg-white border border-slate-200 rounded-xl shadow-sm p-6 max-w-2xl mx-auto text-center">
            
                        <div class="w-14 h-14 mx-auto mb-3 rounded-full bg-blue-100 flex items-center justify-center">
                            <i class="fas fa-users text-blue-600 text-xl"></i>
                        </div>
            
                        <h3 class="text-xl font-bold text-slate-800 mb-2">
                            Activate Your Free Recruiter Plan
                        </h3>
            
                        <p class="text-sm text-slate-600 mb-4">
                            Unlock candidate search, profile access, resume viewing and free job posting.
                        </p>
            
                        <div class="flex flex-wrap justify-center gap-2 mb-5 text-xs">
                            <span class="bg-green-100 text-green-700 px-2 py-1 rounded-full">
                                <i class="fas fa-briefcase mr-1"></i> Job Posting
                            </span>
            
                            <span class="bg-blue-100 text-blue-700 px-2 py-1 rounded-full">
                                <i class="fas fa-search mr-1"></i> Candidate Search
                            </span>
            
                            <span class="bg-purple-100 text-purple-700 px-2 py-1 rounded-full">
                                <i class="fas fa-file-alt mr-1"></i> Resume Access
                            </span>
                        </div>
            
                        <a href="${BASE_URL}employer/jobs/create"
                           class="inline-flex items-center px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg">
                            <i class="fas fa-rocket mr-2"></i>
                            Activate Free Plan
                        </a>
            
                    </div>
                </div>`;
            
                document.getElementById('load-more-container').classList.add('hidden');
                return;
            }

                // Update plan credits
                if (data.remaining_credits) {
                    const c = data.remaining_credits;
                    document.getElementById('plan-credits').innerHTML = `
                        <span class="bg-slate-100 px-2 py-1 rounded"><i class="fas fa-briefcase mr-1"></i>Jobs: <b>${c.jobs_used} / ${c.jobs_limit}</b></span>
                        <span class="bg-slate-100 px-2 py-1 rounded"><i class="fas fa-user mr-1"></i>CV Views: <b>${c.cv_used} / ${c.cv_limit}</b></span>
                        <span class="bg-slate-100 px-2 py-1 rounded"><i class="fas fa-search mr-1"></i>Searches: <b>${c.search_used} / ${c.search_limit}</b></span>
                        <span class="bg-slate-100 px-2 py-1 rounded"><i class="fas fa-download mr-1"></i>Downloads: <b>${c.download_used} / ${c.download_limit}</b></span>`;
                }

                removeSkeletonCards();
                renderCandidates(data.candidates, !reset);

                totalPages = data.total_pages;
                currentPage = data.page;
                document.getElementById('total-matches').innerText = data.total + ' matches';
                document.getElementById('profile-count').innerText = data.total + ' profiles';

                if (data.candidates.length === 0) {
                    document.getElementById('load-more-container').classList.add('hidden');
                } else if (currentPage < totalPages) {
                    document.getElementById('load-more-container').classList.remove('hidden');
                } else {
                    document.getElementById('load-more-container').classList.add('hidden');
                }
            })
            .catch(err => {
                console.error(err);
                removeSkeletonCards();
                if (reset) {
                    document.getElementById('candidate-grid').innerHTML = `
                        <div class="col-span-full text-center py-10 text-red-500">
                            <i class="fas fa-exclamation-triangle text-4xl mb-3"></i>
                            <p>Failed to load candidates. <button onclick="loadCandidates(1, true)" class="text-blue-600 underline">Retry</button></p>
                        </div>`;
                } else if (loadMoreError) {
                    loadMoreError.innerHTML = 'Failed to load more. <button onclick="loadCandidates(currentPage+1, false)" class="text-blue-600 underline">Retry</button>';
                    loadMoreError.classList.remove('hidden');
                }
            })
            .finally(() => {
                isLoading = false;
                hideGlobalLoader();
                if (loadMoreBtn) {
                    loadMoreBtn.querySelector('.button-text').textContent = 'Load more';
                    loadMoreBtn.disabled = false;
                }
            });
    }

    function renderCandidates(candidates, append = false) {
        const SITE_URL = "<?= base_url() ?>";
        const grid = document.getElementById('candidate-grid');
        if (!append) grid.innerHTML = '';

        if (candidates.length === 0 && !append) {
            grid.innerHTML = '<div class="col-span-full text-center py-10 text-slate-500"><i class="fas fa-user-slash text-4xl mb-3"></i><p>No candidates match your criteria.</p></div>';
            return;
        }

        candidates.forEach(c => {
            const fullname = (c.name + ' ' + (c.last_name || '')).trim();
            const initials = (c.name ? c.name[0] : '') + (c.last_name ? c.last_name[0] : '');
            const location = c.city_name || 'NA';
            const work_status = c.work_status ? c.work_status.charAt(0).toUpperCase() + c.work_status.slice(1) : 'Not specified';
            const skills = c.skills || [];
            const skillsHtml = skills.slice(0, 4).map(s => `<span class="bg-slate-100 text-slate-700 text-xs px-2 py-1 rounded-full">${escapeHtml(s)}</span>`).join('');
            const moreSkills = skills.length > 4 ? `<span class="bg-slate-100 text-slate-700 text-xs px-2 py-1 rounded-full">+${skills.length-4} more</span>` : '';
            const verifiedBadge = (parseInt(c.is_verified) === 1)
				? '<span class="ml-1 inline-flex items-center text-green-600" title="Email verified"><i class="fas fa-check-circle text-xs"></i></span>'
				: '';
            // ✅ Only show badge when has_active_plan is exactly 1 (active plan)
			// New (use)
			const premiumBadge = (c.has_active_plan == 1) 
				? `<span class="inline-flex items-center justify-center w-5 h-5 rounded-full text-[10px] bg-gradient-to-br from-yellow-300 to-amber-500 text-yellow-900 shadow-sm" title="Premium Subscriber"><i class="fas fa-crown"></i></span>`
				: '';

            const avatar = c.profile_image
                ? `<img src="${SITE_URL}${c.profile_image}" class="w-11 h-11 rounded-full object-cover border-2 border-white shadow-sm" onerror="this.style.display='none'">`
                : `<div class="w-11 h-11 rounded-full bg-gradient-to-br from-blue-100 to-blue-200 flex items-center justify-center text-blue-700 font-bold text-base border-2 border-white shadow-sm">${initials}</div>`;

            const card = document.createElement('div');
            card.className = 'candidate-card bg-white rounded-xl border border-slate-200 p-5 shadow-sm transition-all duration-200 hover:shadow-md hover:-translate-y-0.5';
            card.innerHTML = `
                <div class="flex items-start gap-3">
                    ${avatar}
                    <div class="min-w-0 flex-1">
                        <div class="flex items-center gap-2 flex-wrap">
                            <h4 class="font-bold text-slate-800 truncate">${escapeHtml(fullname)} ${verifiedBadge}</h4>
                            ${premiumBadge}
                        </div>
                        <p class="text-sm text-slate-500 truncate">${escapeHtml(c.designations || 'No title')}</p>
                    </div>
                    <span class="bg-green-100 text-green-700 text-[10px] px-2 py-0.5 rounded-full font-medium whitespace-nowrap">${formatExperience(c.total_experience_years, c.total_experience_months)}</span>
                </div>
                <div class="mt-3 space-y-2">
                   <div class="flex items-center gap-2 text-xs text-slate-500 flex-wrap">    
						<span class="flex items-center gap-1">
							<i class="fas fa-map-marker-alt text-slate-400 w-3"></i> 
							${escapeHtml(location)}
						</span>
						<span class="flex items-center gap-1">
							<i class="fas fa-briefcase text-slate-400 w-3"></i> 
							${escapeHtml(work_status)}
						</span>
						<span class="flex items-center gap-1 text-blue-600">
							<i class="fas fa-clock text-slate-400 w-3"></i> 
							${formatLastLogin(c.last_login)}
						</span>
					</div>
                    <div class="flex flex-wrap gap-1.5">${skillsHtml} ${moreSkills}</div>
                </div>
                <div class="mt-4 flex items-center justify-between border-t border-slate-100 pt-3">
                    <button onclick='openDrawer("profile", ${c.candidate_id})' class="text-xs text-blue-600 hover:underline inline-flex items-center gap-1"><i class="fas fa-user-circle"></i> view full profile</button>
                    ${c.resume ? `<a href="${SITE_URL}${c.resume}" target="_blank" class="bg-blue-50 hover:bg-blue-100 text-blue-700 text-sm px-4 py-1.5 rounded-lg inline-flex items-center gap-2 font-medium transition"><i class="fas fa-download"></i> CV</a>` : `<button disabled class="bg-gray-100 text-gray-400 text-sm px-4 py-1.5 rounded-lg inline-flex items-center gap-2 font-medium cursor-not-allowed"><i class="fas fa-download"></i> No CV</button>`}
                </div>
            `;
            grid.appendChild(card);
        });
    }

    // Skeleton functions
    function showSkeletonCards(count) {
        const grid = document.getElementById('candidate-grid');
        grid.innerHTML = '';
        for (let i = 0; i < count; i++) grid.appendChild(createSkeletonCard());
    }
    function appendSkeletonCards(count) {
        const grid = document.getElementById('candidate-grid');
        for (let i = 0; i < count; i++) grid.appendChild(createSkeletonCard());
    }
    function removeSkeletonCards() {
        document.querySelectorAll('.skeleton-card').forEach(card => card.remove());
    }
    function createSkeletonCard() {
        const card = document.createElement('div');
        card.className = 'skeleton-card bg-white rounded-xl border border-slate-200 p-5 shadow-sm animate-pulse';
        card.innerHTML = `
            <div class="flex items-start gap-3">
                <div class="w-11 h-11 rounded-full bg-slate-200"></div>
                <div class="flex-1"><div class="h-4 bg-slate-200 rounded w-3/4 mb-2"></div><div class="h-3 bg-slate-200 rounded w-1/2"></div></div>
                <div class="w-12 h-5 bg-slate-200 rounded-full"></div>
            </div>
            <div class="mt-3 space-y-2"><div class="flex gap-2"><div class="h-3 bg-slate-200 rounded w-20"></div><div class="h-3 bg-slate-200 rounded w-16"></div></div><div class="flex flex-wrap gap-1"><div class="h-5 bg-slate-200 rounded-full w-16"></div><div class="h-5 bg-slate-200 rounded-full w-20"></div></div></div>
            <div class="mt-4 flex justify-between border-t border-slate-100 pt-3"><div class="h-3 bg-slate-200 rounded w-24"></div><div class="h-8 bg-slate-200 rounded w-16"></div></div>
        `;
        return card;
    }

    function escapeHtml(text) {
        if (!text) return '';
        const div = document.createElement('div');
        div.appendChild(document.createTextNode(text));
        return div.innerHTML;
    }

    function formatLastLogin(datetime) {
        if (!datetime) return 'Never logged in';
        const date = new Date(datetime);
        const now = new Date();
        const diffMs = now - date;
        const diffDays = Math.floor(diffMs / (1000 * 60 * 60 * 24));
        const diffHours = Math.floor(diffMs / (1000 * 60 * 60));
        const diffMinutes = Math.floor(diffMs / (1000 * 60));

        if (diffDays > 7) return date.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' });
        else if (diffDays >= 1) return `${diffDays} day${diffDays > 1 ? 's' : ''} ago`;
        else if (diffHours >= 1) return `${diffHours} hour${diffHours > 1 ? 's' : ''} ago`;
        else if (diffMinutes >= 1) return `${diffMinutes} minute${diffMinutes > 1 ? 's' : ''} ago`;
        else return 'Just now';
    }

    function openDrawer(mode, candidateId = null) {
		const drawer = document.getElementById('drawer');
		const backdrop = document.getElementById('drawerBackdrop');
		const titleEl = document.getElementById('drawerTitle');
		const filtersDiv = document.getElementById('filtersContent');
		const profileDiv = document.getElementById('profileContent');

		if (mode === 'filters') {
			titleEl.innerText = 'Advanced filters';
			filtersDiv.classList.remove('hidden');
			profileDiv.classList.add('hidden');
		} else if (mode === 'profile' && candidateId) {
			titleEl.innerText = 'Loading...';
			filtersDiv.classList.add('hidden');
			profileDiv.classList.remove('hidden');
			profileDiv.innerHTML = `<div class="text-center py-8"><i class="fas fa-spinner fa-pulse text-3xl text-blue-500"></i><p class="mt-2 text-sm text-slate-500">Loading profile...</p></div>`;

			fetch(BASE_URL + 'employer/EmployerTalentPool/get_candidate_details', {
				method: 'POST',
				headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
				body: 'candidate_id=' + candidateId + '&' + getCSRFName() + '=' + getCSRFToken()
			})
			.then(response => response.json())
			.then(data => {
				if (data.csrf_token) updateCSRFToken(data.csrf_token, data.csrf_name || getCSRFName());
				if (data.error) { profileDiv.innerHTML = `<p class="text-red-500">${escapeHtml(data.error)}</p>`; return; }

				// Candidate name with verification
				const fullProfileName = escapeHtml((data.name + ' ' + (data.last_name || '')).trim());

				// ✅ PREMIUM ICON – only if has_active_plan is truthy (1, true, "1")
				// Use loose equality (== 1) so that "1" string also matches
				const premiumIcon = (data.has_active_plan == 1)
					? `<span class="inline-flex items-center justify-center w-5 h-5 rounded-full text-[10px] bg-gradient-to-br from-yellow-300 to-amber-500 text-yellow-900 shadow-sm ml-1" title="Premium Subscriber"><i class="fas fa-crown"></i></span>`
					: '';

				titleEl.innerHTML = fullProfileName + premiumIcon;

				const initials = (data.name ? data.name[0] : '') + (data.last_name ? data.last_name[0] : '');
				const verifiedBadge = (parseInt(data.is_verified) === 1)
				? '<span class="ml-1 text-green-600" title="Email verified"><i class="fas fa-check-circle"></i></span>'
				: '';
				const lastLoginFormatted = formatLastLogin(data.last_login);
				const skillsHtml = (data.skills || []).map(s => `<span class="bg-slate-100 text-slate-700 text-xs px-2 py-1 rounded-full">${escapeHtml(s)}</span>`).join('');
				const employmentHtml = (data.employment_history || []).map(e => `<div class="border-l-2 border-blue-200 pl-3 py-1"><p class="text-sm font-medium">${escapeHtml(e.job_title)} at ${escapeHtml(e.employer_name)}</p><p class="text-xs text-slate-500">${e.start_date ? e.start_date.substring(0,4) : ''} - ${e.is_current ? 'Present' : (e.end_date ? e.end_date.substring(0,4) : '')} · ${e.job_type || ''}</p></div>`).join('');
				const educationHtml = (data.education_history || []).map(e => `<div class="border-l-2 border-purple-200 pl-3 py-1"><p class="text-sm font-medium">${escapeHtml(e.degreeName)} in ${escapeHtml(e.fieldOfStudy)}</p><p class="text-xs text-slate-500">${escapeHtml(e.institutionName)} · ${e.startYear} - ${e.endYear}</p></div>`).join('');
				const avatar = data.profile_image ? `<img src="${BASE_URL}${data.profile_image}" class="w-14 h-14 rounded-full object-cover border" onerror="this.style.display='none'">` : `<div class="w-14 h-14 rounded-full bg-blue-100 flex items-center justify-center text-blue-700 font-bold text-xl">${initials}</div>`;

				profileDiv.innerHTML = `
					<div class="space-y-4">
						<div class="flex items-center gap-3">${avatar}<div><p class="text-sm text-slate-500">${escapeHtml(data.designations || '')}</p><p class="text-xs text-slate-400">${formatExperience(data.total_experience_years, data.total_experience_months)} · ${escapeHtml(data.city_name || '')}</p></div></div>
						<div><span class="text-sm font-medium text-slate-600">Type</span><p class="text-sm">${escapeHtml(data.work_status || 'Not specified')}</p></div>
						<div><span class="text-sm font-medium text-slate-600">Skills</span><div class="flex flex-wrap gap-1 mt-1">${skillsHtml}</div></div>
						<div><span class="text-sm font-medium text-slate-600">Bio</span><p class="text-sm text-slate-700">${escapeHtml(data.about || 'No bio provided.')}</p></div>
						<div><span class="text-sm font-medium text-slate-600">Contact</span><p class="text-sm"><i class="fas fa-envelope text-slate-400 w-4"></i> ${escapeHtml(data.email)} ${verifiedBadge}<br><i class="fas fa-phone text-slate-400 w-4"></i> ${escapeHtml(data.mobile)}</p></div>
						<div><span class="text-sm font-medium text-slate-600">Last Login</span><p class="text-sm"><i class="fas fa-clock text-slate-400 w-4"></i> ${escapeHtml(lastLoginFormatted)}</p></div>
						<div><span class="text-sm font-medium text-slate-600">Employment History</span><div class="space-y-2 mt-1">${employmentHtml || '<p class="text-sm text-slate-500">No history</p>'}</div></div>
						<div><span class="text-sm font-medium text-slate-600">Education</span><div class="space-y-2 mt-1">${educationHtml || '<p class="text-sm text-slate-500">No education listed</p>'}</div></div>
						<div class="pt-3">${data.resume ? `<a href="${BASE_URL}${data.resume}" target="_blank" class="w-full bg-blue-50 hover:bg-blue-100 text-blue-700 text-sm px-4 py-2 rounded-lg inline-flex items-center justify-center gap-2"><i class="fas fa-download"></i> Download CV</a>` : `<button disabled class="w-full bg-gray-100 text-gray-400 text-sm px-4 py-2 rounded-lg inline-flex items-center justify-center gap-2 cursor-not-allowed"><i class="fas fa-download"></i> No CV</button>`}</div>
					</div>`;
			})
			.catch(err => { console.error(err); profileDiv.innerHTML = `<div class="text-center py-8 text-red-500"><i class="fas fa-exclamation-triangle text-4xl mb-3"></i><p>Failed to load profile.</p><button onclick="openDrawer('profile', ${candidateId})" class="mt-4 bg-blue-600 text-white px-4 py-2 rounded-lg text-sm">Retry</button></div>`; });
		}

		drawer.classList.remove('translate-x-full');
		drawer.classList.add('translate-x-0');
		backdrop.classList.remove('hidden');
	}

    function closeDrawer() {
        document.getElementById('drawer').classList.remove('translate-x-0');
        document.getElementById('drawer').classList.add('translate-x-full');
        document.getElementById('drawerBackdrop').classList.add('hidden');
    }

    document.addEventListener('keydown', function(e) { if (e.key === 'Escape') closeDrawer(); });

    document.getElementById('load-more').addEventListener('click', function() {
        if (isLoading || parseInt(currentPage) >= parseInt(totalPages)) return;
        loadCandidates(parseInt(currentPage) + 1, false);
    });

    function downloadExcel() {
        const btn = document.getElementById('exportBtn');
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Preparing Excel...';
        btn.classList.add('opacity-60','cursor-not-allowed');

        let params = new URLSearchParams();
        if (filters.keywords) params.set('keywords', filters.keywords);
        if (filters.experience) params.set('experience', filters.experience);
        if (filters.location) params.set('location', filters.location);
        if (filters.skills) params.set('skills', filters.skills);
        if (filters.job_type) params.set('job_type', filters.job_type);

        window.location.href = "<?= base_url('employer/EmployerTalentPool/export_excel?') ?>" + params.toString();

        setTimeout(() => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-file-excel mr-2"></i> Export Excel';
            btn.classList.remove('opacity-60','cursor-not-allowed');
        }, 10000);
    }
</script>