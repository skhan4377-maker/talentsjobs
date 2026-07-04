<!doctype html>
<html lang="en">

<head>
	<!-- Basic Page Needs ================================================== -->
	<title>offline Post Job | Talents Jobs</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

	<!-- CSS================================================== -->
	<link rel="shortcut icon" type="image/png" href="<?=base_url('assets/resources/');?>assets/img/favicon.ico" />
	<link rel="stylesheet" href="<?=base_url('assets/resources/');?>assets/plugins/css/plugins.css">
    <link href="<?=base_url('assets/resources/');?>assets/css/style.css" rel="stylesheet">
	<link rel="stylesheet" href="https://bootstrap-tagsinput.github.io/bootstrap-tagsinput/dist/bootstrap-tagsinput.css">
	<link href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.css" rel="stylesheet" type="text/css"/>
	<link rel="stylesheet" href="<?=base_url('assets/resources/assets/');?>plugins/summernote/summernote-bs4.css">
    
    
<style>
@media only screen and (max-width: 600px) {
    #sidebars{
        display:none;
    }               
}
</style> 
<style>
.bootstrap-tagsinput {
    background-color: #fbfdff;
    border: 1px solid #dde6ef
    box-shadow: inset 0 1px 1px rgb(0 0 0 / 8%);
}
</style>


</head>

	<body>
		<div class="Loader"></div>
		<div class="wrapper">  
			
			<!-- Start Navigation -->
			<?php $this->load->view('common/inc/header');?>
			<!-- End Navigation -->
			<div class="clearfix"></div>
			
			<!-- General Detail Start -->
			<section class="dashboard-wrap">
				<div class="container-fluid">
					<div class="row">
					
						<!-- Sidebar Wrap -->
						<span id="sidebars"><?php $this->load->view('common/inc/left_menu');?></span>
						
						<!-- Content Wrap -->
						<div class="col-lg-9 col-md-8 col-sm-12">
							<div class="dashboard-body">
								<div class="dashboard-caption">
									
									<div class="dashboard-caption-header">
										<h4><i class="ti-ruler-pencil"></i>Post Free Job & Fast Applications From our Large Seekers Database </h4>
									</div>
									
									<div class="dashboard-caption-wrap">
											<form id="save_post_job" action="offline-post-save" class="post-form"  method="post" autocomplete="off" onsubmit="return false">
											<!-- row -->
											<div class="row">
												<div class="col-md-4 col-sm-12">
													<div class="form-group">
														<label>Job Title*</label>
														<input type="text" class="form-control jobs_profile" name="job_title" id="" placeholder="Job Title" value="">
													</div>	
												</div>
												
												<div class="col-lg-4 col-md-6 col-sm-12">
													<div class="form-group">
														<label>Job Category*</label>
														<select name="industry" id="industry" class="form-control">
															<option value="">Industry</option>
															<?php if(!empty($industry)){ foreach($industry as $key => $data){?>
																<option value="<?=$data['id']?>"><?=$data['industry_name']?></option>
															<?php }}?>
														</select>
													</div>	
												</div>
												
												<div class="col-lg-4 col-md-6 col-sm-12">
													<div class="form-group">
														<label>Functional Area *</label>
														<select name="functional_area" id="functional_area" class="form-control">
															<option value="">Functional</option>
															<?php if(!empty($functional)){ foreach($functional as $key => $data){?>
																<option value="<?=$data['id']?>"><?=$data['functional_area']?></option>
															<?php }}?>
														</select>
													
													</div>	
												</div>
											</div>
											
											<!-- row -->
											<div class="row">
											
												<div class="col-lg-4 col-md-6 col-sm-12">
													<div class="form-group">
														<label>Experience*</label>
														<select id="min_exp" name="min_exp" class="form-control">
															<option value="">Minimum Experience </option>
															<option value="0">0 Year</option>
															<option value="1">1 Year</option>
															<option value="2">2 Year</option>
															<option value="3">3 Year</option>
															<option value="4">4 Year</option>
															<option value="5">5 Year</option>
															<option value="6">6 Year</option>
															<option value="7">7 Year</option>
															<option value="8">8 Year</option>
															<option value="9">9 Year</option>
															<option value="10">10 Year</option>
															<option value="11">11 Year</option>
															<option value="12">12 Year</option>
															<option value="13">13 Year</option>
															<option value="14">14 Year</option>
															<option value="15">15 Year</option>
															<option value="16">16 Year</option>
															<option value="17">17 Year</option>
															<option value="18">18 Year</option>
															<option value="19">19 Year</option>
															<option value="20">20 Year</option>
															<option value="21">21 Year</option>
															<option value="22">22 Year</option>
															<option value="23">23 Year</option>
															<option value="24">24 Year</option>
															<option value="25">25 Year</option>
															<option value="26">26 Year</option>
															<option value="27">27 Year</option>
															<option value="28">28 Year</option>
															<option value="29">29 Year</option>
															<option value="30">30 Year</option>
														</select>
													</div>	
												</div>
												
												<div class="col-lg-4 col-md-6 col-sm-12">
													<div class="form-group">
														<label>Experience*</label>
														<select id="max_exp" name="max_exp" class="form-control">
															<option value="">Maximum Experience</option>
															<option value="1">1 Year</option>
															<option value="2">2 Year</option>
															<option value="3">3 Year</option>
															<option value="4">4 Year</option>
															<option value="5">5 Year</option>
															<option value="6">6 Year</option>
															<option value="7">7 Year</option>
															<option value="8">8 Year</option>
															<option value="9">9 Year</option>
															<option value="10">10 Year</option>
															<option value="11">11 Year</option>
															<option value="12">12 Year</option>
															<option value="13">13 Year</option>
															<option value="14">14 Year</option>
															<option value="15">15 Year</option>
															<option value="16">16 Year</option>
															<option value="17">17 Year</option>
															<option value="18">18 Year</option>
															<option value="19">19 Year</option>
															<option value="20">20 Year</option>
															<option value="21">21 Year</option>
															<option value="22">22 Year</option>
															<option value="23">23 Year</option>
															<option value="24">24 Year</option>
															<option value="25">25 Year</option>
															<option value="26">26 Year</option>
															<option value="27">27 Year</option>
															<option value="28">28 Year</option>
															<option value="29">29 Year</option>
															<option value="30">30 Year</option>
														</select>
													</div>	
												</div>
											
												<div class="col-lg-4 col-md-6 col-sm-12">
													<div class="form-group">
														<label>Job Type*</label>
														<select id="jb-type" name="job_type" class="form-control">
															<option value="">Job Type</option>
															<?php $jobType = jobType(); foreach($jobType as $jobtyp){?>
															    <option value="<?=$jobtyp?>"><?=$jobtyp?></option>
															<?php }?>
														</select>
													</div>	
												</div>
												
											</div>
											
											<!-- row -->
											<div class="row">
												
												<div class="col-lg-4 col-md-6 col-sm-12">
													<div class="form-group">
														<label>Salary Type</label>
														<select id="jb-type" name="salary_type" class="form-control">
															<option value="">Salary Type</option>
															<option value="Per Annual">Per Annual</option>
															<option value="Per Month">Per Month</option>
														</select>
													</div>	
												</div>
												
												
												<div class="col-lg-4 col-md-6 col-sm-12">
													<div class="form-group">
														<label>Min Salary</label>
														<input type="text" class="form-control" value="" name="salary_from" placeholder="Min Salary">
													</div>	
												</div>
												
												<div class="col-lg-4 col-md-6 col-sm-12">
													<div class="form-group">
														<label>Max Salary</label>
														<input type="text" class="form-control" value="" name="salary_to" placeholder="Max Salary">
													</div>	
												</div>
												
												
												<div class="col-md-4 col-sm-12">
													<label>Required Skills</label>
													<div class="form-group">
														<select multiple data-role="tagsinput" id="skill" name="skill[]">
														</select>
														<?php if(!empty($get_skill)) {foreach($get_skill as $row): ?>
															<input type="hidden" name="skill_id[]" value="<?=$row['skill_id'] ?>" >
														<?php endforeach; }?>
													</div>	
												</div>
												<!---<div class="col-lg-4 col-md-6 col-sm-12">
													<div class="form-group">
														<label>Salary Postfix</label>
														<input type="text" class="form-control" value="" name="post_fix_salary" placeholder="EX. Month 20K - 30K">
													</div>	
												</div>--->
												
												<div class="col-md-4 col-sm-12">
													<div class="form-group">
														<label>Select Country</label>
														<select name="country_name" id="country_name" class="form-control">
														<option value="">Country</option>
														</select>
													</div>	
												</div>
												<div class="col-md-4 col-sm-12">
													<div class="form-group">
													<label>City <span id="current_spinner"></span></label>
													 <div class="ui-widget">
                                                    	<input type="text" autocomplete="off" placeholder="Job Location"  id="current_location" name="location_current" class="form-control input-lg" >
                                                     </div>
                                                     <input type="hidden" id="current_id" name="current_location">
													</div>	
												</div>
											</div>
											
											
											<!-- row -->
											<div class="row">
												<div class="col-md-4 col-sm-12">
													<div class="form-group">
														<label>Deadline Submission</label>
														<input type="text" style="position: inherit; z-index: 10000;" id="datepicker1" value="" name="dead_line_date" class="form-control" >
													</div>	
												</div>
											</div>
											
											<!-- row -->
											<div class="row">
												<div class="col-md-12 col-sm-12">
													<div class="form-group">
														<label>Job Description</label>
														<!---<textarea class="form-control  height-120" name="" placeholder="Job Description"></textarea>--->
														<textarea name="job_description" id="job_description" class="form-control summernote" rows="2" placeholder="Job Description"></textarea>
													</div>	
												</div>
											</div>
											
											<!-- row -->
											<div class="row mrg-top-30">
												<div class="col-md-12 col-sm-12">
													<div class="form-group">
														<h4>Employer Section</h4>
													</div>	
												</div>
											</div>
												
											<div class="row">
												<div class="col-md-4 col-sm-12">
													<div class="form-group">
														<label>Employee Name</label>
														<div class="input-with-icon">
															<input type="text"  class="form-control" value="" placeholder="Employee Name" name="employee_name">
															<input type="hidden"  name="id" value="">
															<i class="theme-cl ti-user"></i>
														</div>
													</div>
												</div>
										
												<div class="col-md-4 col-sm-12">
													<div class="form-group">
														<label>Company Name</label>
														<div class="input-with-icon">
															<input type="text" class="form-control" value="" placeholder="Company Name" name="employee_company_name">
															<i class="theme-cl ti-home"></i>
														</div>
													</div>
												</div>
												
												<div class="col-md-4 col-sm-12">
												<div class="form-group">
													<label>Email</label>
													<div class="input-with-icon">
														<input type="text" class="form-control" value="" placeholder="Your Work Email Id." name="work_email">
														<i class="theme-cl ti-email"></i>
													</div>
												</div>
												</div>
												
												<div class="col-md-4 col-sm-12">
												<div class="form-group">
													<label>Password</label>
													<div class="input-with-icon">
														<input type="text" class="form-control" value="" placeholder="Password." name="employee_password">
														<i class="theme-cl ti-lock"></i>
													</div>
												</div>
												</div>
												
												<div class="col-md-4 col-sm-12">
												<div class="form-group">
													<label>Company Contact</label>
													<div class="input-with-icon">
														<input type="text" class="form-control" value="" placeholder="Company Contact Number" name="company_contact">
														<i class="theme-cl ti-mobile"></i>
													</div>
												</div>
												</div>
												
												<div class="col-md-4 col-sm-12">
												<div class="form-group">
													<label>Location <span id="preferred_spinner"></span></label>
													<div class="input-with-icon">
													<div class="ui-widget">
                                                    	<input type="text" autocomplete="off" placeholder="Type City..."  id="preferred_loaction" name="location_company" class="form-control input-lg" >
                                                    </div>
                                                     <input type="hidden" id="preferred_id" name="company_location">
														<i class="theme-cl ti-location-arrow"></i>
													</div>
												</div>
												</div>
												
												<div class="col-md-4 col-sm-12">
												<div class="form-group">
													<label>Recuiter Type</label>
													<div class="input-with-icon">
														<select class="form-control" name="recuiter_type" id="recuiter_type">
															<option value="">Select Type</option>
															<option value="direct employer">DIRECT EMPLOYER</option>
															<option value="recriument firm">RECRIUMENT FIRM</option>
														</select>
														<i class="theme-cl ti-clip"></i>
													</div>
												</div>
												</div>
												
												<div class="col-md-4 col-sm-12">
												    <div class="form-group">
													<label>Company Logo </label>
													<div class="input-with-icon">
													    <input type="file" class="form-control" name="company_logo">
														<i class="theme-cl ti-camera"></i>
													</div>
													</div>
												</div>
												
												<div class="col-md-12 col-sm-12"></div>	
												
												<div class="col-md-4 col-sm-12">
    												<div class="form-group">
    												    <label style="font-size:9px;"><i class="theme-cl ti-headphone-alt"></i> SHOULD CANDIDATE CAN CALL YOU IN YOUR ALTERNATIVE NUMBER?</label>
                										<input type="checkbox"id="call_status" name="call_status"
                										value="0" data-toggle="switchbutton"  data-onstyle="info">
                										
                									</div>
                									
                									<div class="form-group">
    													<input type="checkbox" id="email_status" value="0" name="email_status" data-toggle="switchbutton"  data-onstyle="danger">
    													<label style="font-size:9px;"><i class="theme-cl ti-email"></i> SHOULD CANDIDATE MAIL YOUR RESUME?</label>
    												</div>
												</div>
												
												<div class="col-md-4 col-sm-12">
    												<div class="form-group" id="company_alternate_contact" style="display:none;">
    													<label>Alternate Number</label>
    													 <div class="input-with-icon"  >
    														<input type="text" class="form-control"  placeholder="Company Alternate Contact Number" name="company_alternate_contact" id="" >
    														<i class="theme-cl ti-mobile"></i>
    													</div>
    												</div>
												</div>
												
											</div>
											
											<!-- row -->
											<div class="row mrg-top-30">
												<div class="col-md-12 col-sm-12">
													<div class="form-group text-center">
														<button type="submit" class="btn-savepreview" id="publish"><i class="ti-angle-double-right"></i>Submit</button>
													</div>	
												</div>
											</div>
										</form>
									</div>
									
								</div>
							</div>
						</div>
					
					</div>
				</div>
			</section>
			<!-- General Detail End -->
			
<?php $this->load->view('common/inc/footer');?> 
<!-- End Signin Window -->
<script src="<?=base_url('assets/resources/assets/');?>/plugins/summernote/summernote-bs4.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.17.0/dist/jquery.validate.min.js"></script>
<script type="text/javascript" src="<?=base_url('assets/resources/assets/multistep/');?>multi-form.js?v2"></script>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.15.0/additional-methods.min.js"></script>	
<script src="https://cdnjs.cloudflare.com/ajax/libs/filesize/3.5.11/filesize.min.js"></script>
<!--<script src="<?=base_url('assets/resources/');?>assets/js/dashboard-custom.js"></script>-->
<script src="https://bootstrap-tagsinput.github.io/bootstrap-tagsinput/dist/bootstrap-tagsinput.min.js"></script>
<!---<script src='https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.5/jquery-ui.min.js' type='text/javascript'></script>--->
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/js/bootstrap-datepicker.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
<script src="<?=base_url('assets/resources/');?>assets/js/login.js"></script>
<script src="<?=base_url('assets/resources/');?>assets/js/autocomplete.js"></script>


<script>
$(function () {
$('.summernote').summernote({
    height: 200
});
//$('div.note-group-select-from-files').remove();
});
</script>
  
<script>
$( function() {
var stateObject = [<?php $get_jobs_profile = get_jobs_profile(); echo $get_jobs_profile['job_profile'];?>];
$(".jobs_profile").autocomplete({
source: stateObject
}); 
});
</script>

<script>
$('#skill').tagsinput({
maxTags: 5,
maxChars: 20,
allowDuplicates: false,
});
				
$("#datepicker1" ).datepicker({
dateFormat: 'yy-mm-dd',
showAnim: "drop",
changeMonth: false,
changeYear: false,
showWeek: true,
yearRange: '1945:2023',
minDate: 0
});
				
				
	$.validator.addMethod('filesize', function (value, element, param){
		return this.optional(element) || (element.files[0].size <= param)
	},function(size){
		return "file size must be " + filesize(size,{exponent:2,round:1});
	});

	$('#save_post_job').validate({
		errorClass: "error fail-alert",
		//validClass: "valid success-alert",

		//errorElement: "span",
		ignore: ":hidden:not(textarea)",     
		rules: {
		job_title:{
			required:true,
			minlength:4,
			maxlength:50,
		},
		industry:"required",
		functional_area:"required",
		min_exp:"required",
		max_exp:"required",
		job_type:"required",
		salary_type:"required",
		salary_from: {
			required:true,
			minlength:3,
			maxlength:7,
			digits:true
		},
		salary_to:{
			required:true,
			minlength:3,
			maxlength:7,
			digits:true
		},
		//skill:"required",
		country_name:"required",
		location_current:"required",
		dead_line_date: {
			required:true,
			date : true,
		},
		job_description:{
			required:true,
			//minlength: 100,
			//maxlength: 30,
		},
		company_logo:{
			extension: "jpg|jpeg|png",
			filesize: 200000,
		},
		employee_name:{
			required:true,
			minlength:4,
			maxlength:16,
		},
		work_email:{
			required: true,
			email: true
		},
		employee_password:{
			required:true,
			minlength:8,
		    maxlength:16,
		},
		company_contact: {
			required:true,
			minlength:10,
			maxlength:10,
			digits:true
		},
		company_alternate_contact: {
			required:true,
			minlength:10,
			maxlength:10,
			digits:true
		},
		employee_company_name:"required",
		location_company:"required",
		recuiter_type:"required",
		call_status:"required",
		email_status:"required"
		},
		messages: {
			company_logo:{
				extension:"Please upload .jpg or .png or .jpeg file of notice.",
				filesize:" file size must be less than 200 KB.",
			},
			employee_name:{
				required: 	"Full Name is required",
				minlength: 	"Username should be minimum 4 characters",
				maxlength: 	"Username should be maximum 16 characters",
			},
			work_email: {
				required: "Email is required",
				email: "Please enter a valid e-mail",
			},
			employee_password:{
				required: 	"Password is required",
				minlength: 	"Password should be minimum 8 characters",
				maxlength: 	"Password should be maximum 16 characters",
			},
			company_contact:{
				required: 	"Mobile number is requied",
				minlength: 	"Please enter 10 digit mobile number",
				maxlength: 	"Please enter 10 digit mobile number",
				digits: 	"Only numbers are allowed in this field"
			},
			company_alternate_contact:{
				required: 	"Mobile number is requied",
				minlength: 	"Please enter 10 digit mobile number",
				maxlength: 	"Please enter 10 digit mobile number",
				digits: 	"Only numbers are allowed in this field"
				}
			},
			submitHandler: function(form) {
			var formData = new FormData($(form)[0]);
			$.ajax({
			type     : "POST",
			cache    : false,
			contentType: false,
			processData: false,
			url      : form.action,
			dataType : 'json',
			data     : formData,
			success  : function(data) {
    			if(data.success == 0) {
    				alert(data.error_msg);
    			}else{
    				$('#save_post_job')[0].reset();
    				alert(data.success_msg['success_msg']);
    				window.location.href = "post-all-job";
    				//location.reload();
    			}
			},
			beforeSend: function() {
				$('#publish').attr('disabled','true');
				$("#publish").text('Please wait..');
			},
			complete: function() {
				$('#publish').prop("disabled", false);
				$("#publish").text('Publish & Preview');
			}
		});
		}
	});	
	
</script>
</div>
</body>
</html>