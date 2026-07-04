<!doctype html>
<html lang="en">
 <!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-153460368-1"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'UA-153460368-1');
</script>
<head>
	<!-- Basic Page Needs
	================================================== -->
	<title>Employee Dashboard | Talents Jobs</title>
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
										<h4><i class="ti-settings"></i>Employer Dashboard</h4>
									</div>
									<div class="dashboard-caption-wrap">
									
										<!-- Overview -->
										<div class="row">
											<div class="col-lg-3 col-md-6 col-sm-12">
												<div class="dashboard-stat widget-1">
													<div class="dashboard-stat-content"><h4><?=$applied_job_count['applied_total'];?></h4> <span>Response</span></div>
													<div class="dashboard-stat-icon"><i class="ti-location-pin"></i></div>
												</div>	
											</div>
											
											<div class="col-lg-3 col-md-6 col-sm-12">
												<div class="dashboard-stat widget-2">
													<div class="dashboard-stat-content"><h4><?=$applied_job_count['postjob'];?> </h4> <span>Add Post Job </span></div>
													<div class="dashboard-stat-icon"><i class="ti-bell"></i></div>
												</div>	
											</div>
											
											<div class="col-lg-3 col-md-6 col-sm-12">
												<div class="dashboard-stat widget-3">
													<div class="dashboard-stat-content"><h4>712</h4> <span>Total Views</span></div>
													<div class="dashboard-stat-icon"><i class="ti-pie-chart"></i></div>
												</div>	
											</div>
											
											<div class="col-lg-3 col-md-6 col-sm-12">
												<div class="dashboard-stat widget-4">
													<div class="dashboard-stat-content"><h4><?=$applied_job_count['shortlist'];?></h4> <span>Shortlisted</span></div>
													<div class="dashboard-stat-icon"><i class="ti-bookmark"></i></div>
												</div>	
											</div>
										</div>
										
										<!-- Notifications -->
										<div class="row">
											<div class="col-lg-6 col-md-12">
												<div class="dashboard-gravity-list">
													<h4>Recent Activities</h4>
													<ul>
													    <?php if(!empty($applied_job)){ foreach($applied_job as $key=>$row):?>
														<li>
															(<?=$row['count_applied']?>) Job's Applied Profile for <strong><a href="applied-list/single/<?=$row['posId']?>"><?=ucfirst($row['job_title'])?></a></strong> <?=ucfirst($row['name'])?> <br> (<?=$row['designations']?>)!
														</li>
													    <?php endforeach; }?>
													    <li>
															<a href="applied-list/group/gp2" style="color:green;"><i class="fa fa-eye"></i> Click here for more</a>
														</li>
													</ul>
												</div>
											</div>
											
											<div class="col-lg-6 col-md-12">
												<div class="dashboard-gravity-list invoices with-icons">
													<h4>Jobs</h4>
													<ul>
														<li><i class="dash-icon-box ti-files"></i>
															<strong>Starter Plan</strong>
															<ul>
																<li class="unpaid">Pending</li>
																<li>Order: #20551</li>
																<li>Date: 01/08/2019</li>
															</ul>
															<div class="buttons-to-right">
																<a href="#" class="button gray">Soon</a>
															</div>
														</li>
													</ul>
												</div>
											</div>	
										</div>
										
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
			
			
			<script>
				$('#company-dob').dateDropper();
			</script>
		</div>
	</body>

<!-- Mirrored from codeminifier.com/job-stock-5.4.1/job-stock/candidate-dashboard.html by HTTrack Website Copier/3.x [XR&CO'2014], Mon, 14 Jun 2021 08:33:08 GMT -->
</html>