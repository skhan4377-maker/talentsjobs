<div class="max-w-7xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="px-6 py-5 border-b border-gray-200">
            <h3 class="text-xl font-semibold text-gray-800 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                </svg>
                Active Plan Details
            </h3>
        </div>
        
        <div class="p-6">
            <div class="active-plan-details" id="activePlanDetails">
                <div class="flex justify-center items-center h-32">
                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500"></div>
                </div>
            </div>
        </div>
    </div>
</div>


<script>
function fetchPlanDetails(tabType) {
    $.ajax({
        type: 'POST',
        url: '<?= base_url('employer/employer-plans/fetch-plan-details/') ?>' + tabType,
        data: {
            [getCSRFName()]: getCSRFToken()   // send CSRF token with request
        },
        dataType: 'json',
        beforeSend: function() {
            $('#activePlanDetails').html(`
                <div class="flex justify-center items-center h-32">
                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500"></div>
                </div>
            `);
        },
        success: function(response) {
            // Update CSRF token from server response
            if (response.csrf_token && response.csrf_name) {
                updateCSRFToken(response.csrf_token, response.csrf_name);
            }
            $('#activePlanDetails').html(response.activePlan);
        },
        error: function(err) {
            console.log("Error:", err);
            $('#activePlanDetails').html('<p class="text-red-500">Failed to load plan details.</p>');
        }
    });
}

$(document).ready(function() {
    fetchPlanDetails('active');
});
</script>