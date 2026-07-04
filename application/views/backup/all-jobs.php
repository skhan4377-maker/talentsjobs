<!doctype html>
<html lang="en">

<head>
	<!-- Basic Page Needs
	================================================== -->
	<title>All Job | Talents Jobs</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

	<!-- CSS
	================================================== -->
		<link rel="shortcut icon" type="image/png" href="<?=base_url('assets/resources/');?>assets/img/logo.png" />
	<link rel="stylesheet" href="<?=base_url('assets/resources/');?>assets/plugins/css/plugins.css">
    <link href="<?=base_url('assets/resources/');?>assets/css/style.css" rel="stylesheet">
	
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
										<h4><i class="ti-briefcase"></i>All Jobs</h4>
									</div>
									
									<div class="dashboard-caption-wrap">
									
										<!-- row -->
										<!---<div class="row">
											<div class="col-lg-4 col-md-6 col-sm-12">
												<div class="form-group">
													<input type="text" class="form-control" placeholder="Search Name">
												</div>
											</div>
											
											<div class="col-lg-4 col-md-6 col-sm-12">
												<div class="form-group">
													<select id="jb-filter" class="form-control">
														<option>Choose Job Status</option>
														<option>All Jobs</option>
														<option>Approved Jobs</option>
														<option>Expire Jobs</option>
														<option>Pending Payment</option>
														<option>Rejected Jobs</option>
														<option>Draft Jobs</option>
													</select>
												</div>
											</div>
											
											<div class="col-lg-4 col-md-6 col-sm-12">
												<div class="form-group">
													<select id="jb-filter-date" class="form-control">
														<option>Default Shorting</option>
														<option>Title</option>
														<option>Date</option>
														<option>Modifications</option>
													</select>
												</div>
											</div>
										</div>--->
										<!-- row -->
										
										<ul class="list">
										
										<?php if(!empty($post_job_list)){ foreach($post_job_list as $key=>$row):
										
											if($row['job_type']=='Full-Time'){
												$jobType = 'full-time';
											}else if($row['job_type']=='Contract'){
												$jobType = 'internship';
											}else if($row['job_type']=='Part-Time'){
												$jobType = 'part-time';
											}else if($row['job_type']=='Temporary'){
												$jobType = 'internship';
											}else if($row['job_type']=='Freelancer'){
												$jobType = 'freelancer';
											}else if($row['job_type']=='Online Work'){
												$jobType = 'full-time';
											}else if($row['job_type']=='Work From Home'){
												$jobType = 'part-time';
											}else if($row['job_type']=='Private'){
												$jobType = 'full-time';
											}else if($row['job_type']=='Fresher'){
												$jobType = 'internship';
											}else if($row['job_type']=='Walik In'){
												$jobType = 'full-time';
											}
											
											if(!empty($row['job_tag'])){
												$job_tag = $row['job_tag'];
											}else{
												$job_tag='';
											}
										?>
										
											<li class="manage-list-row clearfix">
												<div class="job-info premium-job">
													<div class="job-img">
														<?php if(!empty($row['company_logo'])){?>
															<img src="<?=base_url('uploads/employer/company_logo/');?><?=$row['company_logo']?>" style="heigth:120px;width:120px;" class="attachment-thumbnail" alt="<?=$row['employee_company_name']?>">
														<?php }else{?>
															<img src="<?=base_url('assets/resources/');?>assets/img/com-1.jpg" class="attachment-thumbnail" alt="Academy Pro Theme">
														<?php }?>
													</div>
													<div class="job-details">
														<h3 class="job-name"><?=ucfirst($row['job_title'])?> <span class="cl-danger"><?=ucfirst($job_tag)?></span></h3>
														<small class="job-company"><i class="ti-home"></i><a href="#"><?=ucfirst($row['functional_area'])?></a></small>
														<small class="job-sallery"><i class="ti-credit-card"></i><?=$row['salary_from'].'-'.$row['salary_to']?></small>
														<small class="job-update"><i class="ti-time"></i>Expired: <?=date('m/d/y',strtotime($row['dead_line_date']))?></small>
														<span class="j-type <?=$jobType?>"><?=$row['job_type']?></span>
													</div>
												</div>
												<div class="job-buttons">
													<a href="edit-post-job?post=<?=$row['post_id']?>" class="btn btn-gary manage-btn" data-toggle="tooltip" data-placement="top" title="" data-original-title="Edit"><i class="ti-pencil-alt"></i></a> 
													<a href="#" class="btn btn-cancel manage-btn" data-toggle="tooltip" data-placement="top" title="" data-original-title="Remove"><i class="ti-close"></i></a>
												</div>
											</li>
											<?php endforeach; }?>
										</ul>
										
										<ul class="pagination">
											<?php echo $links; ?>
										</ul>
										
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
			<script type="text/javascript" src="<?//=base_url('assets/resources/');?>assets/plugins/js/sweetalert.html"></script>
			
			<script>
				//$('#company-dob').dateDropper();
			</script>
		</div>
	</body>

</html>