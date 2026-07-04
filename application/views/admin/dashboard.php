
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />

<div class="min-h-screen">
    <!-- Header with Date Range -->
    <div class="mb-4 bg-white p-3 rounded-lg shadow-sm border border-gray-200">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
            <h2 class="text-base md:text-lg font-semibold text-gray-800">📊 Dashboard Analytics</h2>
            <div class="w-full sm:w-auto">
                <input type="text" id="dateRange" class="border border-gray-300 rounded-lg px-3 py-1.5 text-sm w-full sm:w-64 cursor-pointer bg-white shadow-sm" readonly placeholder="Select date range">
            </div>
        </div>
    </div>

    <!-- ========== METRICS GRID (compact, fully responsive) ========== -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 gap-3 mb-6" id="dashboardMetrics">
        <!-- Employers -->
        <div class="bg-white p-3 rounded-xl shadow-sm border border-gray-200 transition hover:shadow-md">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-xs text-gray-500 uppercase tracking-wide">Employers</p>
                    <p id="metricEmployers" class="text-xl font-bold text-gray-800 mt-1">--</p>
                </div>
                <div class="bg-blue-50 p-2 rounded-lg"><i class="fas fa-building text-blue-600 text-sm"></i></div>
            </div>
            <div class="mt-1 text-xs"><span id="growthEmployers" class="hidden text-green-600 font-medium"></span><span id="declineEmployers" class="hidden text-red-600 font-medium"></span></div>
        </div>
        <!-- Candidates -->
        <div class="bg-white p-3 rounded-xl shadow-sm border border-gray-200">
            <div class="flex justify-between items-start">
                <div><p class="text-xs text-gray-500 uppercase">Candidates</p><p id="metricCandidates" class="text-xl font-bold mt-1">--</p></div>
                <div class="bg-green-50 p-2 rounded-lg"><i class="fas fa-users text-green-600 text-sm"></i></div>
            </div>
            <div class="mt-1 text-xs"><span id="growthCandidates" class="hidden text-green-600"></span><span id="declineCandidates" class="hidden text-red-600"></span></div>
        </div>
        <!-- Active Jobs -->
        <div class="bg-white p-3 rounded-xl shadow-sm border border-gray-200">
            <div class="flex justify-between items-start">
                <div><p class="text-xs text-gray-500 uppercase">Active Jobs</p><p id="metricJobs" class="text-xl font-bold mt-1">--</p></div>
                <div class="bg-purple-50 p-2 rounded-lg"><i class="fas fa-briefcase text-purple-600 text-sm"></i></div>
            </div>
            <div class="mt-1 text-xs"><span id="growthJobs" class="hidden text-green-600"></span><span id="declineJobs" class="hidden text-red-600"></span></div>
        </div>
        <!-- Applications -->
        <div class="bg-white p-3 rounded-xl shadow-sm border border-gray-200">
            <div class="flex justify-between items-start">
                <div><p class="text-xs text-gray-500 uppercase">Applications</p><p id="metricApplications" class="text-xl font-bold mt-1">--</p></div>
                <div class="bg-indigo-50 p-2 rounded-lg"><i class="fas fa-file-alt text-indigo-600 text-sm"></i></div>
            </div>
            <div class="mt-1 text-xs"><span id="growthApplications" class="hidden text-green-600"></span><span id="declineApplications" class="hidden text-red-600"></span></div>
        </div>
        <!-- Posted Jobs (selected range) -->
        <div class="bg-white p-3 rounded-xl shadow-sm border border-gray-200">
            <div class="flex justify-between items-start">
                <div><p class="text-xs text-gray-500 uppercase">📅 Posted Jobs</p><p id="todayPostedJobs" class="text-xl font-bold mt-1">--</p></div>
                <div class="bg-orange-50 p-2 rounded-lg"><i class="fas fa-plus-circle text-orange-600 text-sm"></i></div>
            </div>
        </div>
        <!-- New Applications (selected range) -->
        <div class="bg-white p-3 rounded-xl shadow-sm border border-gray-200">
            <div class="flex justify-between items-start">
                <div><p class="text-xs text-gray-500 uppercase">📬 New Apps</p><p id="todayApplications" class="text-xl font-bold mt-1">--</p></div>
                <div class="bg-pink-50 p-2 rounded-lg"><i class="fas fa-paper-plane text-pink-600 text-sm"></i></div>
            </div>
        </div>
        <!-- On-Hold & Draft (combined card) -->
        <div class="bg-white p-3 rounded-xl shadow-sm border border-gray-200 col-span-2 sm:col-span-1">
            <div class="space-y-2">
                <div class="flex justify-between items-center">
				<div class="flex items-center gap-2">
					<div class="w-2 h-2 bg-yellow-500 rounded-full"></div>
					<span class="text-xs text-gray-600">On Hold</span>
				</div>
				<span id="metricOnHoldJobs" class="font-bold text-yellow-700">--</span></div>
                <div class="flex justify-between items-center"><div class="flex items-center gap-2"><div class="w-2 h-2 bg-gray-500 rounded-full"></div><span class="text-xs text-gray-600">Draft</span></div><span id="metricDraftJobs" class="font-bold text-gray-700">--</span></div>
            </div>
        </div>
		
        <!-- Support queries -->
        <div class="bg-white p-3 rounded-xl shadow-sm border border-gray-200">
            <div class="flex justify-between items-start">
                <div><p class="text-xs text-gray-500 uppercase">Support</p><p id="metricSupportTotal" class="text-xl font-bold mt-1">--</p><p id="metricSupportToday" class="text-xs text-green-600 mt-0.5">+-- today</p></div>
                <div class="bg-red-50 p-2 rounded-lg"><i class="fas fa-headset text-red-600 text-sm"></i></div>
            </div>
        </div>
        <!-- Blog stats compact -->
        <div class="bg-white p-3 rounded-xl shadow-sm border border-gray-200">
            <div class="space-y-1.5">
                <div class="flex justify-between"><span class="text-xs text-gray-600">Blogs</span><span id="metricTotalBlogs" class="font-bold text-gray-800">--</span></div>
                <div class="flex justify-between"><span class="text-xs text-green-600">Live</span><span id="metricPublishedBlogs" class="font-bold text-green-600">--</span></div>
                <div class="flex justify-between"><span class="text-xs text-yellow-600">Draft</span><span id="metricDraftBlogs" class="font-bold text-yellow-600">--</span></div>
            </div>
        </div>
        <!-- Revenue -->
        <div class="bg-white p-3 rounded-xl shadow-sm border border-gray-200">
            <div class="flex justify-between items-start">
                <div><p class="text-xs text-gray-500 uppercase">Revenue</p><p id="metricPaymentRevenue" class="text-xl font-bold text-green-600 mt-1">--</p></div>
                <div class="bg-green-50 p-2 rounded-lg"><i class="fas fa-rupee-sign text-green-600 text-sm"></i></div>
            </div>
        </div>
    </div>

    <!-- ========== CHARTS SECTION ========== -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 mb-6">
        <!-- Registration Trends -->
        <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-200">
            <div class="flex flex-wrap justify-between items-center mb-3 gap-2">
                <h3 class="text-sm font-semibold text-gray-800">📈 Registration Trends</h3>
                <div class="flex gap-1">
                    <button data-chart-type="line" class="chart-type-btn px-3 py-1 rounded-md text-xs font-medium bg-blue-600 text-white shadow-sm">Line</button>
                    <button data-chart-type="bar" class="chart-type-btn px-3 py-1 rounded-md text-xs font-medium bg-gray-200 text-gray-700">Bar</button>
                </div>
            </div>
            <div class="h-64 relative"><canvas id="registrationChart"></canvas></div>
        </div>
        <!-- Application Stage Chart -->
        <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-200">
            <h3 class="text-sm font-semibold text-gray-800 mb-3">📋 Application Stage Distribution</h3>
            <div class="h-64 relative"><canvas id="applicationChart"></canvas></div>
        </div>
    </div>

    <!-- ========== TABLES SECTION (Pending Approvals + Recent Registrations) ========== -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 mb-6">
        <!-- Pending Approvals Card -->
        <div id="pendingApprovalsBox" class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
            <div class="flex justify-between items-center mb-3 flex-wrap gap-2">
                <h3 class="text-sm font-semibold text-gray-800">⏳ Pending Employer Approvals</h3>
                <span class="bg-red-100 text-red-700 text-xs font-semibold px-2.5 py-0.5 rounded-full" id="pendingCount">0</span>
            </div>
            <div id="pendingEmployersList" class="space-y-2 max-h-72 overflow-y-auto pr-1">
                <div class="text-center py-6 text-gray-400 text-sm"><i class="fas fa-spinner fa-spin mr-1"></i> Loading...</div>
            </div>
        </div>

        <!-- Recent Registrations Table -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
            <div class="flex justify-between items-center mb-3 flex-wrap gap-2">
                <h3 class="text-sm font-semibold text-gray-800">👤 Recent Candidate Registrations</h3>
                <a href="#" class="text-xs text-blue-600 hover:underline font-medium">View all →</a>
            </div>
            <div class="overflow-x-auto -mx-1">
                <table class="min-w-full text-xs">
                    <thead class="bg-gray-50 border-b"><tr><th class="text-left py-2 px-2 font-medium text-gray-600">Name</th><th class="text-left py-2 px-2 font-medium text-gray-600">Email</th><th class="text-left py-2 px-2 font-medium text-gray-600">Date</th></tr></thead>
                    <tbody id="recentCandidates" class="divide-y divide-gray-100"><tr><td colspan="3" class="text-center py-5 text-gray-400"><i class="fas fa-spinner fa-spin"></i> Loading...</td></tr></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    // ----------------------------- GLOBALS -----------------------------
    let registrationChart = null;
    let applicationChart = null;
    let currentChartType = 'line';

    // Helper: format numbers K/M
    function formatNumber(num) {
        if (num === null || num === undefined) return '0';
        if (num >= 1e6) return (num / 1e6).toFixed(1) + 'M';
        if (num >= 1e3) return (num / 1e3).toFixed(1) + 'K';
        return num.toString();
    }

    function formatDateShort(dateStr) {
        if (!dateStr) return '';
        const d = new Date(dateStr);
        const today = new Date();
        const yesterday = new Date(today); yesterday.setDate(today.getDate()-1);
        if (d.toDateString() === today.toDateString()) return 'Today';
        if (d.toDateString() === yesterday.toDateString()) return 'Yesterday';
        return d.toLocaleDateString('en-IN', { day: 'numeric', month: 'short' });
    }

    function capitalize(str) { return str.charAt(0).toUpperCase() + str.slice(1); }

    function updateMetricWithGrowth(total, current, previous, metricKey) {
        const metricId = `#metric${capitalize(metricKey)}`;
        const growthId = `#growth${capitalize(metricKey)}`;
        const declineId = `#decline${capitalize(metricKey)}`;
        $(metricId).text(formatNumber(total));
        $(growthId).addClass('hidden').html('');
        $(declineId).addClass('hidden').html('');
        if (previous === 0 && current === 0) return;
        const diff = current - previous;
        const percent = previous === 0 ? 100 : Math.round((diff / previous) * 100);
        if (diff > 0) {
            $(growthId).removeClass('hidden').html(`<i class="fas fa-arrow-up text-xs mr-0.5"></i> +${percent}%`);
        } else if (diff < 0) {
            $(declineId).removeClass('hidden').html(`<i class="fas fa-arrow-down text-xs mr-0.5"></i> ${percent}%`);
        }
    }

    // ----------------------------- DATE RANGE -----------------------------
    function initializeDateRangePicker() {
        $('#dateRange').daterangepicker({
            opens: 'left',
            startDate: moment().subtract(29, 'days'),
            endDate: moment(),
            ranges: {
                'Today': [moment(), moment()],
                'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                'This Month': [moment().startOf('month'), moment().endOf('month')],
                'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
            },
            locale: { format: 'MMM D, YYYY' }
        }, function(start, end) {
            $('#dateRange').val(start.format('MMM D, YYYY') + ' - ' + end.format('MMM D, YYYY'));
            loadDashboardStats();
            loadChartData();
        });
        $('#dateRange').val(moment().subtract(29, 'days').format('MMM D, YYYY') + ' - ' + moment().format('MMM D, YYYY'));
    }

    function getDateRangeParams() {
        const range = $('#dateRange').val();
        if (range) {
            const parts = range.split(' - ');
            if (parts.length === 2) {
                const start = moment(parts[0], 'MMM D, YYYY').format('YYYY-MM-DD');
                const end = moment(parts[1], 'MMM D, YYYY').format('YYYY-MM-DD');
                return `?start_date=${start}&end_date=${end}`;
            }
        }
        return '';
    }

    // ----------------------------- CHARTS -----------------------------
    function initRegistrationChart(data) {
        const ctx = document.getElementById('registrationChart').getContext('2d');
        if (registrationChart) registrationChart.destroy();
        registrationChart = new Chart(ctx, {
            type: currentChartType,
            data: {
                labels: data.labels,
                datasets: [
                    { label: 'Candidates', data: data.candidates, borderColor: '#10B981', backgroundColor: currentChartType === 'bar' ? '#10B981' : 'transparent', borderWidth: 1.5, fill: true, tension: 0.3 },
                    { label: 'Employers', data: data.employers, borderColor: '#3B82F6', backgroundColor: currentChartType === 'bar' ? '#3B82F6' : 'transparent', borderWidth: 1.5, fill: true, tension: 0.3 }
                ]
            },
            options: { responsive: true, maintainAspectRatio: true, plugins: { legend: { position: 'top', labels: { boxWidth: 10, font: { size: 10 } } } }, scales: { y: { beginAtZero: true, ticks: { font: { size: 9 } } }, x: { ticks: { font: { size: 9 }, maxRotation: 35 } } } }
        });
    }

    function initApplicationChart(data) {
        const ctx = document.getElementById('applicationChart').getContext('2d');
        if (applicationChart) applicationChart.destroy();
        applicationChart = new Chart(ctx, {
            type: 'doughnut',
            data: { labels: data.labels, datasets: [{ data: data.data, backgroundColor: ['#3B82F6', '#F59E0B', '#10B981', '#EF4444', '#8B5CF6'], borderWidth: 0 }] },
            options: { responsive: true, maintainAspectRatio: true, plugins: { legend: { position: 'bottom', labels: { boxWidth: 8, font: { size: 9 } } } }, cutout: '55%' }
        });
    }

    function loadChartData() {
        const params = getDateRangeParams();
        // For demo/real integration: expects JSON endpoint
        $.getJSON(`/admin/dashboard/ajax_chart_data${params}`).done(res => {
            if(res.status === 'success') { initRegistrationChart(res.data.registration); initApplicationChart(res.data.application); }
            else fallbackChartData();
        }).fail(() => fallbackChartData());
    }

    function fallbackChartData() {
        // mock data for offline / demo
        const labels = ['Mar 1', 'Mar 5', 'Mar 10', 'Mar 15', 'Mar 20', 'Mar 25', 'Mar 30'];
        initRegistrationChart({ labels: labels, candidates: [4,7,12,9,15,20,18], employers: [2,3,5,7,9,12,10] });
        initApplicationChart({ labels: ['Applied', 'Under Review', 'Hired', 'Rejected'], data: [124, 45, 32, 28] });
    }

    // ----------------------------- MAIN STATS LOADER -----------------------------
    function loadDashboardStats() {
        const params = getDateRangeParams();
        $.getJSON(`/admin/dashboard/ajax_dashboard_stats${params}`).done(res => {
            if(res.status === 'success' && res.data) {
                const d = res.data;
                updateMetricWithGrowth(d.employers.total, d.employers.current, d.employers.previous, 'employers');
                updateMetricWithGrowth(d.candidates.total, d.candidates.current, d.candidates.previous, 'candidates');
                updateMetricWithGrowth(d.active_jobs.total, d.active_jobs.current, d.active_jobs.previous, 'jobs');
                updateMetricWithGrowth(d.applications.total, d.applications.current, d.applications.previous, 'applications');
                $('#metricOnHoldJobs').text(formatNumber(d.on_hold_jobs));
                $('#metricDraftJobs').text(formatNumber(d.draft_jobs));
                $('#metricSupportTotal').text(formatNumber(d.support_contacts.total));
                $('#metricSupportToday').text('+' + formatNumber(d.support_contacts.today));
                $('#todayPostedJobs').text(formatNumber(d.todayStats.posted_jobs));
                $('#todayApplications').text(formatNumber(d.todayStats.applications));
                $('#metricTotalBlogs').text(formatNumber(d.blog_stats.total));
                $('#metricPublishedBlogs').text(formatNumber(d.blog_stats.published));
                $('#metricDraftBlogs').text(formatNumber(d.blog_stats.draft));
                $('#metricPaymentRevenue').text('₹' + formatNumber(d.additional_stats.payment_revenue));
            } else fallbackStats();
        }).fail(() => fallbackStats());
    }

    function fallbackStats() {
        // demo data when backend not ready
        updateMetricWithGrowth(1240, 45, 38, 'employers');
        updateMetricWithGrowth(3870, 112, 98, 'candidates');
        updateMetricWithGrowth(187, 24, 30, 'jobs');
        updateMetricWithGrowth(4590, 210, 195, 'applications');
        $('#metricOnHoldJobs').text('12'); $('#metricDraftJobs').text('8');
        $('#metricSupportTotal').text('47'); $('#metricSupportToday').text('+5');
        $('#todayPostedJobs').text('34'); $('#todayApplications').text('87');
        $('#metricTotalBlogs').text('42'); $('#metricPublishedBlogs').text('28'); $('#metricDraftBlogs').text('14');
        $('#metricPaymentRevenue').text('₹1,28,400');
    }

    // ----------------------------- PENDING EMPLOYERS -----------------------------
    function loadPendingEmployers() {
        $.getJSON("/admin/dashboard/ajax_pending_employers").done(res => {
            if(res.status === 'success' && res.data.length) {
                let html = '';
                res.data.forEach(emp => {
                    html += `<div class="flex items-center justify-between p-2 bg-gray-50 rounded-lg text-xs">
                        <div class="flex-1 min-w-0"><p class="font-semibold text-gray-800 truncate">${escapeHtml(emp.company_name)}</p>
                        <div class="flex flex-wrap items-center gap-x-2 text-gray-500"><span><i class="fas fa-user mr-0.5"></i>${escapeHtml(emp.name)}</span><span class="hidden sm:inline">•</span><span>${formatDateShort(emp.created_at)}</span></div></div>
                        <div class="flex gap-1 ml-2"><button onclick="approveEmployer(${emp.employer_id})" class="bg-green-600 hover:bg-green-700 text-white px-2 py-1 rounded text-xs"><i class="fas fa-check"></i></button>
                        <button onclick="rejectEmployer(${emp.employer_id})" class="bg-red-600 hover:bg-red-700 text-white px-2 py-1 rounded text-xs"><i class="fas fa-times"></i></button></div>
                    </div>`;
                });
                $('#pendingEmployersList').html(html);
                $('#pendingCount').text(res.data.length);
            } else { noPending(); }
        }).fail(() => noPending());
    }
    function noPending() { $('#pendingEmployersList').html(`<div class="text-center py-6 text-gray-400"><i class="fas fa-check-circle text-xl mb-1"></i><p class="text-sm">No pending approvals</p></div>`); $('#pendingCount').text('0'); }
    function approveEmployer(id) { if(confirm('Approve this employer?')) $.post("/admin/dashboard/approve_employer", { employer_id: id }).done(() => { loadPendingEmployers(); loadDashboardStats(); showNotif('Employer approved','success'); }).fail(() => showNotif('Error','error')); }
    function rejectEmployer(id) { let reason = prompt('Rejection reason:'); if(reason && reason.trim()) $.post("/admin/dashboard/reject_employer", { employer_id: id, reason: reason }).done(() => { loadPendingEmployers(); showNotif('Employer rejected','success'); }).fail(() => showNotif('Error','error')); }
    function loadRecentCandidates() {
        $.getJSON("/admin/dashboard/ajax_recent_candidates").done(res => {
            if(res.status === 'success' && res.data.length) {
                let rows = '';
                res.data.forEach(user => {
                    rows += `<tr class="hover:bg-gray-50"><td class="py-2 px-2"><div class="flex items-center gap-2"><div class="w-6 h-6 bg-blue-100 rounded-full flex items-center justify-center"><i class="fas fa-user text-blue-600 text-xs"></i></div><span class="font-medium text-gray-700 truncate">${escapeHtml(user.name)} ${escapeHtml(user.last_name||'')}</span></div></td>
                    <td class="py-2 px-2 text-gray-500 truncate">${escapeHtml(user.email)}</td><td class="py-2 px-2 text-gray-400 text-xs whitespace-nowrap">${formatDateShort(user.created_at)}</td></tr>`;
                });
                $('#recentCandidates').html(rows);
            } else { $('#recentCandidates').html('<tr><td colspan="3" class="text-center py-5 text-gray-400">No recent registrations</td></tr>'); }
        }).fail(() => $('#recentCandidates').html('<tr><td colspan="3" class="text-center py-5 text-gray-400">Unable to load</td></tr>'));
    }

    // helper
    function escapeHtml(str) { if(!str) return ''; return str.replace(/[&<>]/g, function(m){ if(m === '&') return '&amp;'; if(m === '<') return '&lt;'; if(m === '>') return '&gt;'; return m;}); }
    function showNotif(msg, type) { let bg = type === 'success' ? 'bg-green-500' : 'bg-red-500'; let $div = $(`<div class="fixed bottom-4 right-4 ${bg} text-white px-4 py-2 rounded-lg shadow-lg text-sm z-50 flex items-center gap-2"><i class="fas ${type==='success'?'fa-check-circle':'fa-exclamation-circle'}"></i><span>${msg}</span></div>`); $('body').append($div); setTimeout(() => $div.fadeOut(300, function(){$(this).remove();}), 2500); }

    // chart toggle
    $('.chart-type-btn').on('click', function() {
        currentChartType = $(this).data('chart-type');
        $('.chart-type-btn').removeClass('bg-blue-600 text-white').addClass('bg-gray-200 text-gray-700');
        $(this).addClass('bg-blue-600 text-white').removeClass('bg-gray-200 text-gray-700');
        loadChartData();
    });

    // initializers
    $(function() {
        initializeDateRangePicker();
        loadDashboardStats();
        loadChartData();
        loadPendingEmployers();
        loadRecentCandidates();
        setInterval(() => { loadDashboardStats(); }, 120000);
    });
</script>
