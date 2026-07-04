<!doctype html>
<html lang="en">

<head>
	<!-- Basic Page Needs
	================================================== -->
	<title>Company Profile | Talents Jobs</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

	<!-- CSS
	================================================== -->
    <link rel="shortcut icon" type="image/png" href="<?=base_url('assets/resources/');?>assets/img/favicon.ico" />
	<link rel="stylesheet" href="<?=base_url('assets/resources/');?>assets/plugins/css/plugins.css">
    <link href="<?=base_url('assets/resources/');?>assets/css/style.css" rel="stylesheet">
	<link href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.5/themes/base/jquery-ui.css" rel="stylesheet" type="text/css"/>
	<link href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.css" rel="stylesheet" type="text/css"/>
	
	
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
						<?php $this->load->view('common/inc/left_menu');?>
						
						<!-- Content Wrap -->
						<div class="col-lg-9 col-md-8">
							<div class="dashboard-body">
								<div class="dashboard-caption">
									<div class="dashboard-caption-header">
										<h4><i class="ti-id-badge"></i>Company Profile</h4>
									</div>
									<div class="dashboard-caption-wrap">
										<form id="emyForm" class="form-group"  method="post" action="employers" autocomplete="off" onsubmit="return false" >
										<!-- row -->
										<input type="hidden" name="old_banner_image" id="old_image" value="<?=$employer_details['company_logo']?>" />
										
										<div class="row mrg-top-20">
											<div class="col-lg-4 col-md-4 col-sm-12">
												<div class="form-group">
													<label>Employee Name* </label>
													<input type="text"  class="form-control"  placeholder="Employee Name..." name="employee_name" value="<?=$employer_details['employee_name']?>">
													<input type="hidden"  name="id" value="<?=$employer_details['id']?>">
												</div>	
											</div>
											<div class="col-lg-4 col-md-4 col-sm-12">
												<div class="form-group">
													<label>Company Name</label>
													<input type="text" class="form-control" value="<?=$employer_details['employee_company_name']?>" placeholder="Company Name" name="employee_company_name">
												</div>	
											</div>
											<div class="col-lg-4 col-md-4 col-sm-12">
												<div class="form-group">
													<label>Email</label>
													<input type="text" class="form-control" value="<?=$employer_details['work_email']?>" placeholder="Your Work Email Id" name="work_email">
												</div>	
											</div>
											<div class="col-lg-4 col-md-4 col-sm-12">
												<div class="form-group">
													<label>Company Contact</label>
													<input type="text" class="form-control" value="<?=$employer_details['company_contact']?>" placeholder="Company Contact Number" name="company_contact">
												</div>	
											</div>
											
											<div class="col-lg-4 col-md-4 col-sm-12">
												<div class="form-group">
													<label>Location* <span id="preferred_spinner"></span></label>
													<!--<select name="company_location" id="" class="form-control location">
														<option value="">Enter Curren Location</option>
														<option value="<?//=$employer_details['city_id']?>" <?php //if($employer_details['city_id']==$employer_details['city_id']) {echo "selected";}?>><?//=$employer_details['city_name']?></option>
													</select>-->
													<div class="ui-widget">
                                                    	<input type="text" name="preferred_loaction" value="<?=$employer_details['city_name']?>" autocomplete="off" placeholder="Location"  id="preferred_loaction" class="form-control input-lg" >
                                                    </div>
                                                    <input type="hidden" id="preferred_id" name="company_location" value="<?=$employer_details['company_location']?>">
													
												</div>	
											</div>
											
											<div class="col-lg-4 col-md-4 col-sm-12">
												<div class="form-group">
													<label>Recuiter Type</label>
													<select class="form-control" name="recuiter_type" id="recuiter_type">
														<option value="" disabled>Select Type</option>
														<option value="direct employer" <?php if($employer_details['recuiter_type']=='direct employer') {echo "selected";}?>>DIRECT EMPLOYER</option>
														<option value="recriument firm" <?php if($employer_details['recuiter_type']=='recriument firm') {echo "selected";}?>>RECRIUMENT FIRM</option>
													</select>
												</div>	
											</div>
											
											<div class="col-lg-12 col-md-12 col-sm-12">
												<div class="form-group">
													<label>Company Logo </label>
													<input type="file" class="form-control" name="company_logo">
												</div>	
											</div>
											
											<div class="col-lg-6 col-md-6 col-sm-12" style="margin-top:20px;">
												<div class="form-group">
													<label>SHOULD CANDIDATE MAIL YOUR RESUME?</label>
													<div class="btn-group pull-right" data-toggle="buttons">
														<label class="btn form-check-label <?php if($employer_details['email_status']=='1') {echo "active";}?>">
															<input  type="radio" id="email_status" name="email_status" value="1" <?php if($employer_details['email_status']=='1') {echo "checked";}?>> YES
														</label> 
														<label class="btn form-check-label <?php if($employer_details['email_status']=='0') {echo "active";}?> ">
															<input  type="radio" id="email_status" name="email_status" value="0" <?php if($employer_details['email_status']=='0') {echo "checked";}?>> NO	
														</label>
													</div>
												</div>	
											</div>
											
											<div class="col-lg-6 col-md-6 col-sm-12" style="margin-top:20px;">
												<div class="form-group">
													<label>SHOULD CANDIDATE CAN CALL YOU IN YOUR ALTERNATIVE NUMBER?</label>
													<div class="btn-group pull-right" data-toggle="buttons">
														<label class="btn form-check-label <?php if($employer_details['call_status']=='1') {echo "active";}?>">
															<input  type="radio" id="call_status" name="call_status" value="1" <?php if($employer_details['call_status']=='1') {echo "checked";}?>> YES
														</label> 
														<label class="btn form-check-label <?php if($employer_details['call_status']=='0') {echo "active";}?>">
															<input  type="radio" id="call_status" name="call_status" value="0" <?php if($employer_details['call_status']=='0') {echo "checked";}?>> NO	
														</label>
													</div>
												</div>	
												
												<div class="form-group pull-left" id="company_alternate_contact" style="display:none;">
													<label>Alternate Number</label>
													<input type="text" class="form-control"  value="<?=$employer_details['company_alternate_contact']?>" placeholder="Company Alternate Contact Number" name="company_alternate_contact" id="" >
												</div>	
											</div>
											
											<div class="col-lg-12 col-md-12 col-sm-12"   style="margin-top:20px;">
												<div class="form-group">
													<label>About Info</label>
													<textarea class="form-control textarea" placeholder="About Company"><?=$employer_details['company_info']?></textarea>
												</div>	
											</div>
										</div>
										<button type="submit" class="btn-savepreview" id="submit_btn"><i class="ti-angle-double-right"></i>Update Changes</button>
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
			<!-- ============================ Call To Action ================================== -->
			<!-- End Signin Window -->
		<!---<script type="text/javascript" src="<?=base_url('assets/resources/');?>assets/plugins/js/sweetalert.html"></script>--->
			<script src="<?=base_url('assets/resources/');?>assets/js/dashboard-custom.js"></script>
			<script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.17.0/dist/jquery.validate.min.js"></script>
			<script type="text/javascript" src="<?=base_url('assets/resources/assets/multistep/');?>multi-form.js?v2"></script>
			<script src="https://cdn.jsdelivr.net/jquery.validation/1.15.0/additional-methods.min.js"></script>	
			<script src="https://cdnjs.cloudflare.com/ajax/libs/filesize/3.5.11/filesize.min.js"></script>
			<script src="<?=base_url('assets/resources/');?>assets/js/employer_reg.js"></script>
			
			<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>	
			<script src="<?=base_url('assets/resources/');?>assets/js/autocomplete.js"></script>
		
			
			<script>
				//$('#birthday').dateDropper();
			</script>
		</div>
	</body>
</html>