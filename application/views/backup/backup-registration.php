<!doctype html>
<html lang="en">
<head>
	<!-- Basic Page Needs ================================================== -->
	<title> Registration | Talents Jobs</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
	<!-- CSS================================================== -->
    <link rel="shortcut icon" type="image/png" href="<?=base_url('assets/resources/');?>assets/img/favicon.ico" />
	<link rel="stylesheet" href="<?=base_url('assets/resources/');?>assets/plugins/css/plugins.css">
    <link href="<?=base_url('assets/resources/');?>assets/css/style.css" rel="stylesheet">
	<link type="text/css" rel="stylesheet" id="jssDefault" href="<?=base_url('assets/resources/');?>assets/css/colors/green-style.css">
	<link rel="stylesheet" type="text/css" href="<?=base_url('assets/resources/assets/multistep/');?>multi-form.css?v2">
	
	<link href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.css" rel="stylesheet" type="text/css"/>
	<link rel="stylesheet" href="https://bootstrap-tagsinput.github.io/bootstrap-tagsinput/dist/bootstrap-tagsinput.css">

<style>
.inner-header-title {
   padding-top: 0em;
   height: 0px;
}

@media only screen and (max-width: 768px) {
.form-control{
    width:290px;
    margin-left:-35px;
}
.form-group {
    margin-bottom: 2px;
}
form {
    background-color: #ffffff;
}


.theme-cl{
    margin-left:-35px;
}
label{
    margin-left:-35px;  
}
.tabr{
        width:130px;
        font-size:14px;
}
.select2-container {
    width: 132%!important;
    vertical-align: inherit;
    margin-left:-35px; 
}
.inner-header-title {
display: none;
}
                    
}
                    
.btn {
font-size: 11px;
border-radius: 2px;
padding: 10px 15px;
}
.btn-group .btn+.btn, .btn-group .btn+.btn-group, .btn-group .btn-group+.btn, .btn-group .btn-group+.btn-group {
margin-left: 3px;
margin-bottom:2px;
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
			<!-- Title Header Start -->
			<section class="inner-header-title" style="background-image:url(<?//=base_url('assets/resources/');?>assets/img/bn2.jpg);">
				<!--<div class="container">
					<h1>Create And Account </h1>
				</div>-->
			</section>
			
			<div class="clearfix"></div>
			<!-- Title Header End -->
			<!-- Tab Section Start -->
			<section class="tab-sec gray">
				<div class="container">
    	    	<b>
    	    	    <h4>Register for free to Apply for jobs </h4>
    	            <h4>2 Lakh + recuriters are looking or candidate on Talents Jobs </h4>
    	        </b>
			    <?php $this->load->view('common/inc/header_ads_tj2');?>
					
				    <div class="col-lg-8 col-md-8 col-sm-12 col-lg-offset-2 col-md-offset-2">
						
								<ul class="nav modern-tabs nav-tabs theme-bg" id="simple-design-tab">
									<li class="active tabr"><a  href="#candidate" id="regist_type" onsubmit="return false;" data-type="candidate">I am Canidate </a></li>
									<li class="tabr"><a href="#employer" id="regist_type" data-type="employer">Employer/Post Free Job</a></li>
								</ul>
									    					
								<script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-9268075008862469"
                                     crossorigin="anonymous"></script>
                                <!-- ads5 -->
                                <ins class="adsbygoogle"
                                     style="display:inline-block;width:820px;height:100px"
                                     data-ad-client="ca-pub-9268075008862469"
                                     data-ad-slot="3997766125"></ins>
                                <script>
                                     (adsbygoogle = window.adsbygoogle || []).push({});
                                </script>
                                
								<div class="tab-content" style="width:100%;">
									<form id="myForm" class="form-group"  method="post" autocomplete="off" onsubmit="return false" style="margin-top:-55px;">
									        <div class="form-group">
    											<label>Candidate Name</label>
    											<div class="input-with-icon">
    												<input type="text"  class="form-control"  placeholder="Full Name" name="name">
    												<input type="hidden"  name="id" value="0">
    												<i class="theme-cl ti-user"></i>
    											</div>
    									    </div>
									        <div class="form-group">
    											<label>Email</label>
    											    <div class="input-with-icon">
    												<input type="text" class="form-control"  placeholder="Email Id" name="email" >
    												<i class="theme-cl ti-email"></i>
    											</div>
    										</div>
								        	<div class="form-group">
    											<label>Password</label>
    										    <div class="input-with-icon">
    												<input type="password" class="form-control"  placeholder="Password" name="candidate_password">
    												<i class="theme-cl ti-lock"></i>
    											</div>
    										</div>
									        
									        <div class="form-group">
												<label>Profile / Designation</label>
												<div class="input-with-icon ">
													<input type="text" class="form-control jobs_profile" placeholder="Designation / Current Profile" name="designations"	/>
													<i class="theme-cl ti-user"></i>
												</div>
											</div>
										
											<!--<div class="form-group">
												<label>Functional Area </label>
												<select name="functional_areas" id="" class="form-control">
													<option value="">Functional</option>
													<?php //if(!empty($functional_area)){ foreach($functional_area as $key => $data){?>
														<option value="<?//=$data['id']?>"><?//=$data['functional_area']?></option>
													<?php //}}?>
												</select>
											</div>-->	
										
											<div class="form-group">
												<label>Mobile</label>
											    <div class="input-with-icon">
												    <input type="text" class="form-control"  placeholder="Mobile Number" name="candidate_mobile">
												    <i class="theme-cl ti-mobile"></i>
										    	</div>
											</div>
										
											<div class="form-group">
												<label>Location <span id="current_spinner"></span></label>
												   <div class="input-with-icon">
                                                	<input type="text" autocomplete="off" placeholder="Location"  id="current_location" name="location_current" class="form-control" >
                                                    <i class="theme-cl ti-map"></i>
                                                 </div>
                                                <input type="hidden" id="current_id" name="current_location">
											</div>
											
											<div class="form-group">
												<label>Work Status</label>
												<select id="work_status" class="form-control" name="work_status">
													<option value="">Select one</option>
													<option value="fresher">Fresher</option>
													<option value="experience">Experience</option>
												</select>
											</div>
										  
										    <div class="form-groups">
											    <button type="submit" class="btn btn-primary theme-bg full-width">Register</button>
									    	</div>
									</form>
								</div>
								 
								<?php //}else if($this->input->get('type')=='employer'){ ?>	
								<div class="tab-content"  style="margin-top:-100px;">
									<div id="employer" class="tab-pane fade">
									<form id="emyForm" class="form-group"  method="post" action="employers" autocomplete="off" onsubmit="return false" style="display:none; margin-top:-30px;">
										<div class="form-group">
											<label>Employee Name</label>
											<div class="input-with-icon">
												<input type="text"  class="form-control" value="" placeholder="Employee Name." name="employee_name">
												<input type="hidden"  name="id" value="">
												<i class="theme-cl ti-user"></i>
											</div>
										</div>
								
										<div class="form-group">
											<label>Company Name</label>
											<div class="input-with-icon">
												<input type="text" class="form-control" value="" placeholder="Company Name" name="employee_company_name">
												<i class="theme-cl ti-home"></i>
											</div>
										</div>
										
										<div class="form-group">
											<label>Email</label>
											<div class="input-with-icon">
												<input type="text" class="form-control" value="" placeholder="Your Work Emial Id." name="work_email" >
												<i class="theme-cl ti-email"></i>
											</div>
										</div>
										
										<div class="form-group">
											<label>Password</label>
											<div class="input-with-icon">
												<input type="password" class="form-control" value="" placeholder="Password." name="employee_password">
												<i class="theme-cl ti-lock"></i>
											</div>
										</div>
										
										<div class="form-group">
											<label>Company Contact</label>
											<div class="input-with-icon">
												<input type="text" class="form-control" value="" placeholder="Company Contact Number" name="company_contact">
												<i class="theme-cl ti-mobile"></i>
											</div>
										</div>
										
										
										<div class="form-group">
											<label>Location <span id="preferred_spinner"></span></label>
											<!--<select name="company_location" id="" class="form-control location">
												<option value="">Enter Curren Location</option>
											</select>-->
                                            <div class="ui-widget">
                                            	<input type="text" autocomplete="off" placeholder="Location"  id="preferred_loaction" class="form-control input-lg" >
                                            </div>
                                            <input type="hidden" id="preferred_id" name="company_location">
										</div>
										
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
										
									
										<div class="form-group">
											<label>Company Logo </label>
											<input type="file" class="form-control" name="company_logo">
										</div>	
									
									
										<div class="form-group">
										    <div class="input-with-icon">
										        <label style="font-size:10px;"><i class="theme-cl ti-email"></i>  SHOULD CANDIDATE MAIL YOUR RESUME?</label>
										        <input type="checkbox" id="email_status" value="0" name="email_status" data-toggle="switchbutton"  data-onstyle="danger">
										    </div>
										</div>
										
										<div class="form-group">
											<div class="input-with-icon">
											    <label style="font-size:10px;"><i class="theme-cl ti-headphone-alt"></i>  SHOULD CANDIDATE CAN CALL YOU IN YOUR ALTERNATIVE NUMBER?</label>
											    <input type="checkbox"id="call_status" name="call_status" value="0" data-toggle="switchbutton"  data-onstyle="info">
										    </div>
										</div>
										
										<div class="form-group" id="company_alternate_contact" style="display:none;">
											<label>Alternate Number</label>
											 <div class="input-with-icon"  >
												<input type="text" class="form-control" value="" placeholder="Company Alternate Contact Number" name="company_alternate_contact" id="" >
												<i class="theme-cl ti-mobile"></i>
											</div>
										</div>
										
										<!---<div class="register-account text-center">
											By hitting the <span class="theme-cl">"Register"</span> button, you agree to the <a class="theme-cl" href="#">Terms conditions</a> and <a class="theme-cl" href="#">Privacy Policy</a>
										</div>--->
										
										<div class="form-groups">
											<button type="submit" class="btn btn-primary theme-bg full-width">Register</button>
										</div>
									</form>
			
									</div>
								</div>
								
                                <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-9268075008862469"
                                     crossorigin="anonymous"></script>
                                <!-- ads5 -->
                                <ins class="adsbygoogle"
                                     style="display:inline-block;width:600px;height:100px"
                                     data-ad-client="ca-pub-9268075008862469"
                                     data-ad-slot="3997766125"></ins>
                                <script>
                                     (adsbygoogle = window.adsbygoogle || []).push({});
                                </script>
                                
                                <!--<a target="_blank" href="https://bit.ly/Win10LakhPrize-TalentsJobs"><img src="assets/resources/assets/newton_school/black_modern_personal_linkedIn.png" 
                                style="width: 100%;padding: 7px; border: 1px solid #cfcbcb;margin-bottom: 8px;border-radius: 6px;" ></a>-->
			
						
					</div>
				</div>
			</section>
			<!-- Tab section End -->
									
			<?php $this->load->view('common/inc/footer');?>
			<!-- Scripts ================================================== -->
			<script src="<?=base_url('assets/resources/');?>assets/js/jQuery.style.switcher.js"></script>
			<script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.17.0/dist/jquery.validate.min.js"></script>
			<!--<script type="text/javascript" src="<?=base_url('assets/resources/assets/multistep/');?>multi-form.js?v2"></script>-->
			<script src="https://cdn.jsdelivr.net/jquery.validation/1.15.0/additional-methods.min.js"></script>	
			<script src="https://cdnjs.cloudflare.com/ajax/libs/filesize/3.5.11/filesize.min.js"></script>
			<!--<script src="https://bootstrap-tagsinput.github.io/bootstrap-tagsinput/dist/bootstrap-tagsinput.min.js"></script>-->
			<script src="<?=base_url('assets/resources/');?>assets/js/login.js"></script>
			<script src="<?=base_url('assets/resources/');?>assets/js/employer_reg.js"></script>
			<!--<script src='https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.5/jquery-ui.min.js' type='text/javascript'></script>-->
			<!--<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/js/bootstrap-datepicker.js"></script>-->
			<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>	
            <script src="<?=base_url('assets/resources/');?>assets/js/autocomplete.js"></script>

<?php if($_GET['sr']=='em') { ?>
<script type="text/javascript">
$(document).ready(function() {
$('#logins').modal('show');
});
</script>
<?php }?>	
	
<style>
.switch.btn {
min-height: calc(2.5em + 0.75rem + 2px);
}
</style>


<script>
$( function() {
var stateObject = [<?php $get_jobs_profile = get_jobs_profile(); echo $get_jobs_profile['job_profile'];?>];
$(".jobs_profile").autocomplete({
source: stateObject
}); 
});
</script>
<script type="text/javascript">
$(document).ready(function() {
	$('#styleOptions').styleSwitcher();
});
</script>
<script>
function openRightMenu() {
	document.getElementById("rightMenu").style.display = "block";
}
function closeRightMenu() {
	document.getElementById("rightMenu").style.display = "none";
}
</script>

<script type="text/javascript">
$(document).ready(function(){
		    
    //$("span").removeAttr("style");
		      
    $('#myForm').validate({
    errorElement: "span",
    //ignore: ":hidden:not(textarea)",     
    rules: {
        name:{
		required:true,
		minlength:4,
		maxlength:16,
	},
	email:{
	    required: true,
		email: true
	},
	candidate_password:{
		required:true,
		minlength:8,
		maxlength:16,
	},
	candidate_mobile: {
		required:true,
		minlength:10,
		maxlength:10,
	    digits:true
	},
	//functional_area:"required",
	location_current:"required",
	designations:"required",
	work_status:"required"
    },
    messages: {
					
	name:{
    	required: 	"Full Name is required",
    	minlength: 	"Username should be minimum 4 characters",
    	maxlength: 	"Username should be maximum 16 characters",
	},
	email: {
		required: 	"Email is required",
		email: 		"Please enter a valid e-mail",
	},
	candidate_password:{
		required: 	"Password is required",
		minlength: 	"Password should be minimum 8 characters",
		maxlength: 	"Password should be maximum 16 characters",
	},
					
	candidate_mobile:{
	    required: 	"Mobile number is requied",
		minlength: 	"Please enter 10 digit mobile number",
		maxlength: 	"Please enter 10 digit mobile number",
		digits: 	"Only numbers are allowed in this field"
	},
	//functional_area:"Select functional area",
	location_current: "Select current location",
	designations: "Enter designation"
	
	},
	submitHandler: function(form) {
	var formData = new FormData($(form)[0]);
	    $.ajax({
        url:"registration",
        method:"POST",
        data: formData,
        contentType:false,
        processData:false,
        dataType:"json",
        beforeSend:function(){ 
            $('#submit').text('Please wait').css('color','green');
            $('#submit').attr('disabled',true);
        },
        success:function(data){
            if(data.success == 0) {
            	alert(data.error_msg);
            }else{
                alert(data.success_msg['success_msg']);
            	if(data.success_msg['log_type']=='candidate'){
            	  window.location.href = "candidate-dashboard";
            	   //window.location.href = "<?=base_url()?>recommended-jobs";	
            	}else if(data.success_msg['log_type']=='employer'){
            		window.location.href = "employer-dashboard";
            		//window.location.href = "post-new-job";
            	}
            }
            					
        },error: function(jqXHR, exception) {
            if (jqXHR.status === 0) {
            	alert(jqXHR.status +'Not connect.\n Verify Network.');
            }else if (jqXHR.status == 404) {
            	alert('Requested page not found. [404]');
            }else if (jqXHR.status == 500) {
            	alert('Internal Server Error [500].');
            }else if (exception === 'parsererror') {
            	alert('Requested JSON parse failed.');
            }else if (exception === 'timeout') {
            	alert('Time out error.');
            }else if (exception === 'abort') {
            	alert('Ajax request aborted.');
            }else {
            	alert('Uncaught Error.\n' + jqXHR.responseText);
            }
            } 
        });
		}
	});
			 
    $(document).on('click','#regist_type',function(){
		if($(this).data('type')=='candidate'){
			$('#emyForm').css('display','none');
			$('#myForm').css('display','');
		}else if($(this).data('type')=='employer'){
			$('#myForm').css('display','none');
			$('#emyForm').css('display','');
		}
	});
			
	/*$(document).on('change','#employee_type',function(){
		if($('#employee_type').val()=='Freelancer'){
		    $('#freelancer_rate').css('display','');
		}else{
		    $('#freelancer_rate').css('display','none');	
		}
	});*/
						
});
</script>
  </div>
</div>
	
</body>

</html>