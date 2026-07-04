<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$config['permission_map'] = [

    // ================= ADMIN =================
    'admin/dashboard' => 'dashboard',

    // Employers
    'admin/employers/adminemployer' => ['*' => 'manage_employers'],

    // Candidates
    'admin/candidates/admincandidate' => ['*' => 'manage_candidates'],

    // Jobs
    'admin/jobs/adminjobs' => ['*' => 'manage_job_posts'],
    'admin/jobs/adminapplications' => ['*' => 'manage_job_applications'],

    // Blogs
    'admin/blogs/blogcontroller' => ['*' => 'manage_blogs'],

    // Cron
    'admin/cron/manage_cron' => ['*' => 'manage_email_crons'],

    // Support
    'admin/support' => ['*' => 'support_enquiries'],

    // Services
    'admin/manage-services/services' => ['*' => 'manage_services'],
    'admin/manage-services/features' => ['*' => 'manage_services'],
    'admin/manage-services/featuredurations' => ['*' => 'manage_services'],
    'admin/manage-services/resumetemplates' => ['*' => 'manage_resume_templates'],

    // Purchase & Refund
    'admin/manage-purchase/adminpurchaseplans' => ['*' => 'manage_purchase_plans'],
    'admin/manage-purchase/adminrefunds' => ['*' => 'manage_refunds'],

    // Roles
    'admin/adminrole' => ['*' => 'manage_roles'],


    // ================= EMPLOYER =================
    'employer/dashboard' => 'dashboard',
    'employer/profile'   => 'edit_profile',
    'employer/postjobcontroller' => ['*' => 'jobs'],
    'employer/applications'      => ['*' => 'applications'],
    'employer/interviews'        => ['*' => 'interviews'],
    'employer/employertalentpool'=> ['*' => 'viewed_resume'],
    'employer/employerplans'     => ['*' => 'my_plan'],
    'employer/package'           => ['*' => 'my_plan'],
    'employer/employer'          => ['*' => 'dashboard'],


    // ================= CANDIDATE =================
    'candidate/dashboard' => 'dashboard',
    'candidate/profile'   => 'edit_profile',
    'candidate/applied'   => ['*' => 'my_applications'],
    'candidate/favourite' => ['*' => 'saved_jobs'],      
    'candidate/candidate_plans' => [
		'index' => 'purchases',
		'invoice' => 'purchases',
		'request_refund' => 'purchases',
		'cancel_upcoming' => 'purchases',
	],
	'candidate/myresumes' => [
		'index' => 'draft_resumes',
		'downloaded_resumes' => 'downloads',
	],

    // ================= SHARED =================
    'setting/profilemanager' => ['*' => 'settings'],
	'notify/notification' => ['*' => 'dashboard'],
    'companies/companiescontroller' => ['*' => 'hiring_companies'],
	
];