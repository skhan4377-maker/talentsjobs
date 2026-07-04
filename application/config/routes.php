<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	https://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/

$route['default_controller'] = 'Home';

// Admin Purchase Plans
$route['admin/purchase-plans'] =
    'admin/manage-purchase/AdminPurchasePlans';

$route['admin/purchase-plans/view/(:num)'] =
    'admin/manage-purchase/AdminPurchasePlans/view/$1';

$route['admin/purchase-plans/update-status']
    = 'admin/manage-purchase/AdminPurchasePlans/update_status';


$route['admin/purchase-plans/export'] =
    'admin/manage-purchase/AdminPurchasePlans/export';

$route['admin/purchase-plans/analytics'] =
    'admin/manage-purchase/AdminPurchasePlans/analytics';

// Admin Refunds
$route['admin/refunds'] = 'admin/manage-purchase/AdminRefunds';
$route['admin/refunds/view/(:num)'] = 'admin/manage-purchase/AdminRefunds/view/$1';
$route['admin/refunds/process_refund'] = 'admin/manage-purchase/AdminRefunds/process_refund';
$route['admin/refunds/bulk_action'] = 'admin/manage-purchase/AdminRefunds/bulk_action';

// Manage Page (CRUD UI)
$route['bio/manage'] = 'admin/bio/manage';
// AJAX APIs
$route['bio/manage/get_jobs_ajax'] = 'admin/bio/get_jobs_ajax';
$route['bio/manage/store_ajax'] = 'admin/bio/store_ajax';
$route['bio/manage/update_ajax'] = 'admin/bio/update_ajax';
$route['bio/manage/delete_ajax'] = 'admin/bio/delete_ajax';

// ========================= Website - Registration =========================
$route['registration/candidate'] = 'website/registrations/RegistrationController/index/$1';
$route['recruit/client-registration-form'] = 'website/registrations/RegistrationController/employerRegistration';

// ========================= Website - Jobs =========================
$route['browse-jobs'] = 'website/jobs/Jobs/index';
$route['browse-jobs/(:any)'] = 'website/jobs/Jobs/index/$1';
$route['fetch_data'] = 'website/jobs/Jobs/fetch_data';


// ========================= Website - Support =========================
$route['help-center'] = 'website/support/help_center';
$route['contact-us'] = 'website/support/contact_us';
$route['privacy-policy'] = 'website/support/privacy_policy';
$route['terms-of-service'] = 'website/support/terms_of_service';
$route['about-us'] = 'website/support/about_us';


// ========================= Website - Salary & Companies =========================
$route['search-salary'] = 'website/salary/SalarySearch';
$route['companies/hiring'] = 'website/companies/CompaniesController/companies_hiring';
$route['companies-detail'] = 'website/companies/CompaniesController/companies_detail';

// ========================= Website - Blog =========================
$route['blogs'] = 'website/blog/Blogs';
$route['blog-detail/(:any)'] = 'website/blog/Blogs/blog_detail/$1';

// ========================= Common API Calls =========================
$route['get-job-title'] = 'common/get_job_title';
$route['get-city'] = 'common/get_city';
$route['get-company'] = 'common/get_company';

// ========================= Auth =========================
$route['credential']        = 'auth/login/credential';        // Handles AJAX login credential validation
$route['auth/login']        = 'auth/login/index';             // General login page (candidate/employer)
$route['auth/verify']       = 'auth/Verifications/verifyEmail';
$route['logout']            = 'auth/login/logout';            // Logout route

// ========================= Admin Login =========================
$route['admin/login']       = 'auth/login/login';             // Admin login view + credential (POST via same method)


// Forgot Password
$route['forgot-password'] = 'auth/forgot/index';
$route['forgot-password/send-link'] = 'auth/forgot/send_link';
$route['reset-password'] = 'auth/forgot/reset';
$route['reset-password/save'] = 'auth/forgot/save_password';

// ========================= Candidate Panel =========================
$route['candidate/dashboard'] = 'candidate/Dashboard/index';
$route['candidate/profile'] = 'candidate/Profile/index';
$route['job/myapply'] = 'candidate/Applied/index';
$route['job/favourite'] = 'candidate/Favourite/index';
$route['candidate/interviews/view/(:any)'] = 'candidate/Applied/view/$1';

//$route['career-plans'] = 'candidate/CareerPlans';

$route['my-downloads'] = 'candidate/MyResumes/downloaded_resumes';
$route['my-resumes'] = 'candidate/MyResumes';

$route['my-purchases'] = 'candidate/Candidate_plans';
$route['candidate/invoice/(:any)'] = 'candidate/Candidate_plans/invoice/$1';
$route['candidate/request_refund']  = 'candidate/candidate_plans/request_refund';
$route['candidate/cancel_upcoming'] = 'candidate/candidate_plans/cancel_upcoming';


// ========================= Employer Panel =========================
$route['employer/dashboard'] = 'employer/Dashboard/index';
$route['my-profile'] = 'employer/Profile/index';
$route['employer/jobs'] = 'employer/PostJobController/index';
$route['employer/jobs/view/(:any)'] = 'employer/PostJobController/index/$1';
$route['employer/jobs/create'] = 'employer/PostJobController/addNewJob';
$route['employer/jobs/edit/(:num)'] = 'employer/PostJobController/addNewJob/$1';
$route['employer/jobs/update-status'] = 'employer/PostJobController/jobStatus';
$route['employer/update_application_status'] = 'employer/applications/update_application_status';
$route['employer/jobs/applications'] = 'employer/applications/get_job_applications';
$route['employer/jobs/applications/(:any)'] = 'employer/applications/get_job_applications/$1';
$route['updatePosttrack'] = 'employer/employer/updatePosttrack';
$route['shortlisted-candidates'] = 'employer/employer/shortlisted_candidates';
$route['data-access'] = 'employer/employer/data_access';
//$route['package'] = 'employer/Package';

$route['employer/employer-plans'] = 'employer/EmployerPlans';
$route['employer/employer-plans/fetch-plan-details/([a-zA-Z]+)'] = 'employer/EmployerPlans/fetchPlanDetails/$1';

$route['search/SearchCandidates'] = 'employer/EmployerTalentPool';
$route['employer/export-excel'] = 'employer/EmployerTalentPool/export_excel';

// ========================= Shared Settings =========================
$route['settings'] = 'setting/ProfileManager/index';

// ========================= Pricing & Payment =========================
//$route['pricing/plan'] = 'pricing/pricing/plans';
//$route['pricing/fetch_pricing_plans'] = 'pricing/pricing/fetch_pricing_plans';
//$route['pricing/add_to_cart'] = 'pricing/pricing/add_to_cart';
//$route['pricing/remove_from_cart'] = 'pricing/pricing/remove_from_cart';
//$route['pricing/fetch_cart_contents'] = 'pricing/pricing/fetch_cart_contents';

//$route['payment/checkout'] = 'checkout/checkout/checkout';

// ========================= Resume Builder =========================
$route['candidate/resume'] = 'uploads/candidate/resume';
//$route['career-services'] = 'website/services/CareerServices/services_index';
//$route['career-services/(:any)'] = 'website/services/CareerServices/view/$1';

//$route['cart'] = 'website/services/CartController/view_cart';
//$route['checkout'] = 'website/services/PaymentController/checkout';
//$route['payment-success'] = 'website/services/PaymentController/payment_success';
//$route['payment-status'] = 'website/services/PaymentController/payment_status';

//email open tracking
$route['track/click'] = 'cron/Track/click';
$route['track/open'] = 'cron/Track/open';

$route['track/campaign_open/(:any)'] = 'cron/Track/campaign_open/$1';
$route['track/campaign_click/(:any)'] = 'cron/Track/campaign_click/$1';

$route['track/push_click/(:any)'] = 'cron/Track/push_click/$1';

$route['track/plan_reminder'] = 'cron/Track/plan_reminder';

$route['track/plan_automation_open/(:any)']  = 'cron/Track/plan_automation_open/$1';
$route['track/plan_automation_click/(:any)'] = 'cron/Track/plan_automation_click/$1';


/*$route['app/build-cv'] = 'website/services/ResumeServiceController/index';
$route['app/choose-template'] = 'website/services/ResumeServiceController/choose_template';
$route['app/create-resume'] = 'website/services/ResumeServiceController/create_resume';
$route['resume/(:any)/edit'] = 'website/services/ResumeBuilder/edit_resume/$1';*/


// Resume Templates API
$route['api/resume-templates']['GET'] = 'api/resume-builder/resume_templates/index';
//$route['api/resume-templates/industries']['GET'] = 'api/resume-builder/resume_templates/industries';
$route['api/resume-templates/(:num)']['GET'] = 'api/resume-builder/resume_templates/view/$1';

// ============================
// ⚙️ Template Tracking API Routes
// ============================
$route['api/resume-templates/track-usage']['POST'] = 'api/resume-builder/resume_templates/track_usage';


// Services API
$route['career-services'] = 'api/resume-builder/ServicesFeatures/features_get';
$route['career-services/(:any)'] = 'api/resume-builder/ServicesFeatures/feature_get/$1';
//Resume Templates page
$route['resume-templates'] = 'website/ResumeTemplates/index';

// Features API
//$route['api/features']['GET'] = 'api/resume-builder/ServicesFeatures/features'; // expects ?service_id={id}
//$route['api/features/(:num)']['GET'] = 'api/resume-builder/ServicesFeatures/feature/$1';

// --- Cart API ---
$route['cart']  = 'api/resume-builder/CartController/index';   // get user cart
$route['cart/count'] = 'api/resume-builder/CartController/count';

$route['cart/add'] = 'api/resume-builder/CartController/add';     // add or update cart
$route['cart/update-plan'] = 'api/resume-builder/CartController/update_plan';     // add or update cart
$route['cart/remove']= 'api/resume-builder/CartController/remove'; // remove cart item

$route['payment/create-order'] = 'api/resume-builder/payment/create_order';
$route['payment/verify-payment'] = 'api/resume-builder/payment/verify_payment';
$route['payment/failed'] = 'api/resume-builder/payment/failed';


//$route['api/invoice/invoice_data']['get'] = 'api/resume-builder/InvoiceController/invoice_data';
//Purchase history
//$route['api/invoice/purchase_history']['get'] = 'api/resume-builder/InvoiceController/purchase_history';

// USER PLAN
$route['api/userplan/my_plan']['GET'] = 'api/resume-builder/UserPlan/my_plan';
$route['api/userplan/track-download']['POST'] = 'api/resume-builder/UserPlan/track_download';

// ✅ Resume Draft API routes
// Fetch a specific resume draft
$route['api/resume-draft/(:num)']['get']  = 'api/resume-builder/Resume_draft/view/$1';

// Save/update a specific resume draft
$route['api/save-draft/(:num)'] = 'api/resume-builder/Resume_draft/save/$1';

$route['api/dashboard/stats/(:num)'] = 'api/resume-builder/Dashboard_api/stats/$1';

// GET: Fetch paginated or searched city list
$route['api/cities']['get'] = 'api/resume-builder/Cities/index';

$route['api/auth/login_token']['GET'] = 'api/resume-builder/UserAuth/login_token';
//$route['api/auth/refresh']['POST'] = 'api/resume-builder/UserAuth/refresh';
$route['api/auth/logout']['POST'] = 'api/resume-builder/UserAuth/logout';

//$route['api/auth/login']['POST'] = 'api/resume-builder/UserAuth/login';
//$route['api/auth/register']['POST'] = 'api/resume-builder/UserAuth/register';

// LOGOUT ROUTE

// Forgot Password Routes
//$route['api/forgotpassword/send-otp'] = 'api/resume-builder/ForgotPassword/send_otp';
//$route['api/forgotpassword/verify-otp'] = 'api/resume-builder/ForgotPassword/verify_otp';
//$route['api/forgotpassword/reset-password'] = 'api/resume-builder/ForgotPassword/reset_password';
//$route['api/forgotpassword/resend-otp'] = 'api/resume-builder/ForgotPassword/resend_otp';
//$route['api/forgotpassword/validate-token'] = 'api/resume-builder/ForgotPassword/validate_token';

//$route['api/job-recommendations'] = 'api/resume-builder/JobRecommendation/getJobRecommendations';

// ========================= Contact API =========================
//$route['api/contact/submit']['POST'] = 'api/resume-builder/Contact/submit';
//$route['api/contact/info'] = 'api/resume-builder/Contact/info';  // Add this line

$route['api/track-redirect']['post'] = 'api/resume-builder/redirect/track';


//cron job
$route['unsubscribe/(:any)'] = 'Unsubscribe/job_alerts/$1';
$route['unsubscribe/email_unsubscribe/(:any)'] = 'Unsubscribe/email_unsubscribe/$1';

$route['mailclouder/webhook'] = 'Mailclouder_webhook/index';
$route['razorpay/webhook/refund'] = 'RazorpayWebhook/refund';


$route['bio'] = 'website/jobs/Jobs/bio';
$route['bio/(:any)'] = 'website/jobs/Jobs/bio_detail/$1';
$route['bio-fetch-more'] = 'website/jobs/Jobs/bio_fetch_more';

// ========================= Fallback =========================
$route['404_override'] = 'errors/show_404';

// ========================= Website - Jobs =========================
$route['(.+)-(\d+)'] = 'website/jobs/Jobs/job_details/$1-$2';

$route['translate_uri_dashes'] = FALSE;
