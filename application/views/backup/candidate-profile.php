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
      <title>Candidate Profile | Talents Jobs</title>
      <meta charset="utf-8">
      <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
      <!-- CSS ================================================== -->
      <link rel="shortcut icon" type="image/png" href="<?=base_url('assets/resources/');?>assets/img/logo.png" />
      <link rel="stylesheet" href="<?=base_url('assets/resources/');?>assets/plugins/css/plugins.css">
      <link href="<?=base_url('assets/resources/');?>assets/css/style.css" rel="stylesheet">
      <!---<link href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.5/themes/base/jquery-ui.css" rel="stylesheet" type="text/css"/>-->
      <link href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.css" rel="stylesheet" type="text/css"/>
      <link rel="stylesheet" href="https://bootstrap-tagsinput.github.io/bootstrap-tagsinput/dist/bootstrap-tagsinput.css">
      <?php 
         if(!empty($candidate_info)){
         		$id = $candidate_info['candidate_id'];
         		$email = $candidate_info['email'];
         		$resume = $candidate_info['resume'];
         		$name = $candidate_info['name'];
         		$candidate_mobile = $candidate_info['candidate_mobile'];
         		$gender = $candidate_info['gender'];
         		$industrys = $candidate_info['industry'];
         		$industry_name = $candidate_info['industry_name'];
         		$functional_area_id = $candidate_info['functional_area_id'];
         		$functional_area_name = $candidate_info['functional_area_name'];
         		
         		$work_status = $candidate_info['work_status'];
         		$current_location = $candidate_info['current_location'];
         		$city_name = $candidate_info['city_name'];
         		
         		$qualification = $candidate_info['qualification'];
         		$passing_year = $candidate_info['passing_year'];
         		
         		$course_type = $candidate_info['course_type'];
         		$key_skill = @$candidate_info['key_skill'];
         		$designations = $candidate_info['designations'];
         		$company_name = $candidate_info['company_name'];
         		$current_ctc = $candidate_info['current_ctc'];
         		$notice_period = $candidate_info['notice_period'];
         		$expected_salary = $candidate_info['expected_salary'];
         		$student_about = $candidate_info['student_about'];
         		$language = $candidate_info['language'];
         		$candidate_availablity_status = $candidate_info['candidate_availablity_status'];
         }
         ?>
      <style>
         .mand{color:red;}
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
               <?php $this->load->view('common/inc/left_menu'); if(!empty($candidate_session=$this->session->userdata('candidate_session')))?>
               <!-- Sidebar Wrap -->
               <!-- Content Wrap -->
               <div class="col-lg-9 col-md-8">
                  <div class="dashboard-body">
                     <div class="dashboard-caption">
                        <div class="dashboard-caption-header">
                           <h4><i class="ti-id-badge"></i>My Profile</h4>
                        </div>
                        <div class="dashboard-caption-wrap">
                           <form id="myForm" class="form-group" action="registration"  method="post" autocomplete="off" onsubmit="return false">
                              <!-- row -->
                              <input type="hidden"  name="id" value="<?=$id?>">
                              <input type="hidden"  name="old_banner_image" value="<?=$resume?>">
                              <input type="hidden" name="candidate_password" value="talents@jobs!@41211212">
                              <div class="row mrg-top-20">
                                 <?php $this->load->view('common/inc/header_ads_tj');?>
                                 <div class="col-lg-12 col-md-6 col-sm-12">
                                    <div class="alert alert-warning" role="alert">
                                       Personal Details
                                    </div>
                                 </div>
                                 <div class="col-lg-4 col-md-6 col-sm-12">
                                    <div class="form-group">
                                       <label>Full Name <span class="mand">*</span></label>
                                       <input type="text" class="form-control" name="name" placeholder="Enter Name" value="<?=$name?>">
                                    </div>
                                 </div>
                                 <div class="col-lg-4 col-md-6 col-sm-12">
                                    <div class="form-group">
                                       <label>Email <span class="mand">*</span></label>
                                       <input type="email" name="email" class="form-control" placeholder="designing@themezhub.com" value="<?=$email?>">
                                    </div>
                                 </div>
                                 <div class="col-lg-4 col-md-6 col-sm-12">
                                    <div class="form-group">
                                       <label>Phone <span class="mand">*</span></label>
                                       <input type="text" class="form-control" name="candidate_mobile" placeholder="91 256 254 4578" value="<?=$candidate_mobile?>">
                                    </div>
                                 </div>
                                 <!---<div class="col-lg-4 col-md-6 col-sm-12">
                                    <div class="form-group">
                                    	<label>Dob</label>
                                    	<input type="text" id="datepicker1" name="dob"  placeholder="Date of Birth..."  class="form-control" value="<?//=date('m-d-Y',strtotime($dob))?>" >
                                    </div>	
                                    </div>-->
                                 <div class="col-lg-4 col-md-6 col-sm-12">
                                    <div class="form-group">
                                       <label>Work Status</label>
                                       <select id="work_status" class="form-control" name="work_status">
                                          <option value="">Select one</option>
                                          <option value="fresher" <?=($work_status == 'fresher') ? 'selected' : ''?>>Fresher</option>
                                          <option value="experience" <?=($work_status == 'experience') ? 'selected' : ''?>>Experience</option>
                                       </select>
                                    </div>
                                 </div>
                                 <div class="col-lg-4 col-md-6 col-sm-12">
                                    <div class="form-group">
                                       <label>Gender <span class="mand">*</span></label>
                                       <select class="form-control" name="gender">
                                          <option value="">Gender</option>
                                          <option value="male" <?=($gender == 'male') ? 'selected' : ''?>>Male</option>
                                          <option value="female" <?=($gender == 'female') ? 'selected' : ''?>>Female</option>
                                          <option value="other" <?=($gender == 'other') ? 'selected' : ''?>>Other</option>
                                       </select>
                                    </div>
                                 </div>
                                 <div class="col-lg-4 col-md-6 col-sm-12">
                                    <div class="form-group">
                                       <label>Current Location <span class="mand">*</span> <span id="current_spinner"></span></label>
                                       <div class="ui-widget">
                                          <input type="text" value="<?=$city_name?>" autocomplete="off" placeholder="Type City"  id="current_location" name="location_current" class="form-control input-lg" >
                                       </div>
                                       <input type="hidden" value="<?=$current_location?>" id="current_id" name="current_location">
                                    </div>
                                 </div>
                                 <!--<div class="col-lg-4 col-md-6 col-sm-12">
                                    <div class="form-group">
                                    				<label>Preferred Location <span id="preferred_spinner"></span></label>
                                    							<div class="ui-widget">
                                                                            	<input type="text" value="<?//=$candidate_session['city_name']?>" autocomplete="off" placeholder="Type City..."  id="preferred_loaction" name="preferred_loaction" class="form-control input-lg" >
                                                                            </div>
                                                                            <input type="hidden" value="<?php //echo $candidate_session['preferred_location'];?>" id="preferred_id" name="preferred_location">
                                    			</div>
                                    </div>-->
                              </div>
                              <div class="row mrg-top-20">
                                 <div class="col-lg-12 col-md-6 col-sm-12">
                                    <div class="alert alert-info" role="alert">
                                       Educational Details
                                    </div>
                                 </div>
                                 <div class="col-lg-4 col-md-6 col-sm-12">
                                    <div class="form-group">
                                       <label>Industry <span class="mand">*</span></label>
                                       <select name="industry" id="industry" class="form-control">
                                          <option value="">Industry</option>
                                          <?php if(!empty($industrys)){?>
                                          <option value="<?=$industrys?>" selected><?=$industry_name?></option>
                                          <?php } ?>
                                          <?php if(!empty($industry)){ foreach($industry as $key => $data){?>
                                          <option value="<?=$data['id']?>"><?=$data['industry_name']?></option>
                                          <?php }}?>
                                       </select>
                                    </div>
                                 </div>
                                 <div class="col-lg-4 col-md-6 col-sm-12">
                                    <div class="form-group">
                                       <label>Functional <span class="mand">*</span></label>
                                       <select id="functional_area" name="functional_area" class="form-control">
                                          <option value="">Functional Area</option>
                                          <?php if(!empty($functional_area_id)){?>
                                          <option  value="<?=$functional_area_id?>"selected><?=$functional_area_name?></option>
                                          <?php }?>
                                          <?php if(!empty($functional_area)){ foreach($functional_area as $key => $data){?>
                                          <option value="<?=$data['id']?>"><?=$data['functional_area']?></option>
                                          <?php }}?>
                                       </select>
                                    </div>
                                 </div>
                                 <div class="col-lg-4 col-md-6 col-sm-12">
                                    <div class="form-group">
                                       <label>Qualification <span class="mand">*</span></label>
                                       <select id="qualification" name="qualification" class="form-control">
                                          <option value="">Qualification </option>
                                          <option value="Bachelor of Architecture (B.Arch)" <?=($qualification == 'Bachelor of Architecture (B.Arch)') ? 'selected' : ''?>>Bachelor of Architecture (B.Arch)</option>
                                          <option value="Bachelor of Arts (B.A.)" <?=($qualification == 'Bachelor of Arts (B.A)') ? 'selected' : ''?>>Bachelor of Arts (B.A)</option>
                                          <option value="Bachelor of Arts Bachelor of Education (B.A.B.Ed)" <?=($qualification == 'Bachelor of Arts Bachelor of Education (B.A.B.Ed)') ? 'selected' : ''?>>Bachelor of Arts Bachelor of Education (B.A.B.Ed)</option>
                                          <option value="Bachelor of Ayurvedic Medicine and surgery (B.A.M.S)" <?=($qualification == 'Bachelor of Ayurvedic Medicine and surgery (B.A.M.S)') ? 'selected' : ''?>>Bachelor of Ayurvedic Medicine and surgery (B.A.M.S)</option>
                                          <option value="Bachelor of Business Administration (B.B.A)" <?=($qualification == 'Bachelor of Business Administration (B.B.A)') ? 'selected' : ''?>>Bachelor of Business Administration (B.B.A)</option>
                                          <option value="Bachelor of Business Management (B.B.M)" <?=($qualification == 'Bachelor of Business Management (B.B.M)') ? 'selected' : ''?>>Bachelor of Business Management (B.B.M)</option>
                                          <option value="Bachelor of Business Studies (B.B.S)" <?=($qualification == 'Bachelor of Business Studies (B.B.S)') ? 'selected' : ''?>>Bachelor of Business Studies (B.B.S)</option>
                                          <option value="Bachelor of Commerce (B.Com)" <?=($qualification == 'Bachelor of Commerce (B.Com)') ? 'selected' : ''?>>Bachelor of Commerce (B.Com)</option>
                                          <option value="Bachelor of Communication Journalism (B.C.J)" <?=($qualification == 'Bachelor of Communication Journalism (B.C.J)') ? 'selected' : ''?>>Bachelor of Communication Journalism (B.C.J)</option>
                                          <option value="Bachelor of Computer Application (B.C.A)" <?=($qualification == 'Bachelor of Computer Application (B.C.A)') ? 'selected' : ''?>>Bachelor of Computer Application (B.C.A)</option>
                                          <option value="Bachelor of Computer Science (B.C.S)" <?=($qualification == 'Bachelor of Computer Science (B.C.S)') ? 'selected' : ''?>>Bachelor of Computer Science (B.C.S)</option>
                                          <option value="Bachelor of Design (B.Des)" <?=($qualification == 'Bachelor of Design (B.Des)') ? 'selected' : ''?>>Bachelor of Design (B.Des)</option>
                                          <option value="Bachelor of Education (B.Ed)" <?=($qualification == 'Bachelor of Education (B.Ed)') ? 'selected' : ''?>>Bachelor of Education (B.Ed)</option>
                                          <option value="Bachelor of Engineering (B.E)" <?=($qualification == 'Bachelor of Engineering (B.E)') ? 'selected' : ''?>>Bachelor of Engineering (B.E)</option>
                                          <option value="Bachelor of Fine Arts (B.F.A)" <?=($qualification == 'Bachelor of Fine Arts (B.F.A)') ? 'selected' : ''?>>Bachelor of Fine Arts (B.F.A)</option>
                                          <option value="Bachelor of Hotel Management (B.H.M)" <?=($qualification == 'Bachelor of Hotel Management (B.H.M)') ? 'selected' : ''?>>Bachelor of Hotel Management (B.H.M)</option>
                                          <option value="Bachelor of Legislative Law (L.L.B)" <?=($qualification == 'Bachelor of Legislative Law (L.L.B)') ? 'selected' : ''?>>Bachelor of Legislative Law (L.L.B)</option>
                                          <option value="Bachelor of Medicine Bachelor of Surgery (M.B.B.S)" <?=($qualification == 'Bachelor of Medicine Bachelor of Surgery (M.B.B.S)') ? 'selected' : ''?>>Bachelor of Medicine Bachelor of Surgery (M.B.B.S)</option>
                                          <option value="Bachelor of Multimedia Communication (B.M.C)"<?=($qualification == 'Bachelor of Multimedia Communication (B.M.C)') ? 'selected' : ''?>>Bachelor of Multimedia Communication (B.M.C)</option>
                                          <option value="Bachelor of Pharmacy (B.Pharma)" <?=($qualification == 'Bachelor of Pharmacy (B.Pharma)') ? 'selected' : ''?>>Bachelor of Pharmacy (B.Pharma)</option>
                                          <option value="Bachelor of Physical Education (B.P.E)" <?=($qualification == 'Bachelor of Physical Education (B.P.E)') ? 'selected' : ''?>>Bachelor of Physical Education (B.P.E)</option>
                                          <option value="Bachelor of Science (B.Sc)" <?=($qualification == 'Bachelor of Science (B.Sc)') ? 'selected' : ''?>>Bachelor of Science (B.Sc)</option>
                                          <option value="Bachelor of Technology (B.Tech)" <?=($qualification == 'Bachelor of Technology (B.Tech)') ? 'selected' : ''?>>Bachelor of Technology (B.Tech)</option>
                                          <option value="Integrated B.A &amp; B.Ed" <?=($qualification == 'Integrated B.A &amp; B.Ed') ? 'selected' : ''?>>Integrated B.A &amp; B.Ed</option>
                                          <option value="Integrated B.E/B. Tech. &amp; M.B.A." <?=($qualification == 'Integrated B.E/B. Tech. &amp; M.B.A') ? 'selected' : ''?>>Integrated B.E/B. Tech. &amp; M.B.A</option>
                                          <option value="Integrated M.A" <?=($qualification == 'Integrated M.A') ? 'selected' : ''?>>Integrated M.A</option>
                                          <option value="Integrated M.B.A." <?=($qualification == 'Integrated M.B.A') ? 'selected' : ''?>>Integrated M.B.A</option>
                                          <option value="Integrated M.Sc." <?=($qualification == 'Integrated M.Sc') ? 'selected' : ''?>>Integrated M.Sc</option>
                                          <option value="M.C.A" <?=($qualification == 'M.C.A') ? 'selected' : ''?>>M.C.A</option>
                                          <option value="MBA" <?=($qualification == 'MBA') ? 'selected' : ''?>>MBA</option>
                                          <option value="PGDM" <?=($qualification == 'PGDM') ? 'selected' : ''?>>PGDM</option>
                                          <option value="other" <?=($qualification == 'Other') ? 'selected' : ''?>>Other</option>
                                       </select>
                                    </div>
                                 </div>
                                 <div class="col-lg-12 col-md-12 col-sm-12"></div>
                                 <div class="col-lg-4 col-md-6 col-sm-12">
                                    <div class="form-group">
                                       <label>Year of Graduation</label>
                                       <select id="passing_year" name="passing_year" class="form-control">
                                          <option value="">Passing Year</option>
                                          <?php if(!empty($passing_year)){?>
                                          <option value="<?=$passing_year?>" selected><?=$passing_year?></option>
                                          <?php }?>
                                          <?php $i=0; for($i="1945";$i<=date('Y'); $i++){?>
                                          <option value="<?=$i;?>"><?=$i;?></option>
                                          <?php }?>
                                       </select>
                                    </div>
                                 </div>
                                 <div class="col-lg-4 col-md-6 col-sm-12">
                                    <div class="form-group">
                                       <label>Course Type <span class="mand">*</span></label>
                                       <select id="" name="course_type" class="form-control">
                                          <option value="">Course Type</option>
                                          <option value="full time" <?=($course_type == 'full time') ? 'selected' : ''?>>Full Time</option>
                                          <option value="part time" <?=($course_type == 'part time') ? 'selected' : ''?>>Part Time</option>
                                          <option value="correspondence" <?=($course_type == 'correspondence') ? 'selected' : ''?>>Correspondence</option>
                                          <option value="distance learning program" <?=($course_type == 'distance learning program') ? 'selected' : ''?>>Distance Learning Program</option>
                                          <option value="executive program" <?=($course_type == 'executive program') ? 'selected' : ''?>>Executive Program</option>
                                          <option value="certification" <?=($course_type == 'certification') ? 'selected' : ''?>>Certification</option>
                                       </select>
                                    </div>
                                 </div>
                                 
                                <!-- <div class="col-lg-4 col-md-6 col-sm-12">
                                    <div class="form-group">
                                       <label>Key Skill</label>
                                       <select multiple data-role="tagsinput" id="skill" name="skill[]" placeholder="Key Skill" >
                                          <?php //if(!empty($get_skill)) {foreach($get_skill as $row): ?>
                                          <option selected value="<?//=$row['skill_name'];?>"><?//=$row['skill_name'];?></option>
                                          <?php //endforeach; }?>
                                       </select>
                                       <?php //if(!empty($get_skill)) {foreach($get_skill as $row): ?>
                                       <input type="hidden" name="skill_id[]" value="<?//=$row['skill_id'] ?>">
                                       <?php //endforeach; }?>
                                    </div>
                                 </div>-->
                                 
                                 <div class="col-lg-4 col-md-6 col-sm-12">
                                    <div class="form-group">
                                       <label>Language</label>
                                       <select multiple data-role="tagsinput" id="language" name="language[]" placeholder="Enter Language"  >
                                          <option selected value="<?=$language?>"><?=$language?></option>
                                       </select>
                                    </div>
                                 </div>
                                 <!--<div class="col-lg-4 col-md-6 col-sm-12">
                                    <div class="form-group">
                                    	<label>College/University <span class="mand">*</span></label>
                                    	<input type="text" class="form-control" placeholder="University / Institute" name="college_university" value="<?=$college_university?>">
                                    </div>	
                                    </div>-->
                                 <!--<div class="col-lg-12 col-md-6 col-sm-12"></div>
                                    <div class="col-lg-4 col-md-6 col-sm-12">
                                    	<div class="form-group">
                                    		<label>Degree <span class="mand">*</span></label>
                                    		<select id="degree_type" name="degree_type" class="form-control">
                                    			<option value="">Degree Name</option>
                                    			<option value="BA" <?=($degree_type == 'BA') ? 'selected' : ''?>>BA</option>
                                    			<option value="BSC" <?=($degree_type == 'BSC') ? 'selected' : ''?>>BSC</option>
                                    			<option value="BCOM" <?=($degree_type == 'BCOM') ? 'selected' : ''?>>BCOM</option>
                                    			<option value="BCA" <?=($degree_type == 'BCA') ? 'selected' : ''?>>BCA</option>
                                    			<option value="BBA" <?=($degree_type == 'BBA') ? 'selected' : ''?>>BBA</option>
                                    			<option value="MA" <?=($degree_type == 'MA') ? 'selected' : ''?>>MA</option>
                                    			<option value="Msc/MS/MTECH" <?=($degree_type == 'Msc/MS/MTECH') ? 'selected' : ''?>>Msc/ MS/ MTECH</option>
                                    			<option value="MCA" <?=($degree_type == 'MCA') ? 'selected' : ''?>>MCA</option>
                                    			<option value="INTEGRATED MASTERS PROGRAM" <?=($degree_type == 'INTEGRATED MASTERS PROGRAM') ? 'selected' : ''?>>INTEGRATED MASTERS PROGRAM</option>
                                    			<option value="MBBS" <?=($degree_type == 'MBBS') ? 'selected' : ''?>>MBBS</option>
                                    			<option value="LLB/LLM" <?=($degree_type == 'LLB/LLM') ? 'selected' : ''?>>LLB/LLM</option>
                                    			<option value="ICWA" <?=($degree_type == 'ICWA') ? 'selected' : ''?>>ICWA</option>
                                    			<option value="CS" <?=($degree_type == 'CS') ? 'selected' : ''?>>CS</option>
                                    			<option value="CA" <?=($degree_type == 'CA') ? 'selected' : ''?>>CA</option>
                                    			<option value="MBA/PGDM" <?=($degree_type == 'MBA/PGDM') ? 'selected' : ''?>>MBA/PGDM</option>
                                    			<option value="BARCH" <?=($degree_type == 'BARCH') ? 'selected' : ''?>>BARCH</option>
                                    			<option value="PhD" <?=($degree_type == 'PhD') ? 'selected' : ''?>>PhD</option>
                                    			<option value="OTHERS" <?=($degree_type == 'OTHERS') ? 'selected' : ''?>>OTHERS</option>
                                    		</select>
                                    	</div>	
                                    </div>
                                    
                                    <div class="col-lg-4 col-md-6 col-sm-12">
                                    	<div class="form-group">
                                    		<label>Passing Month</label>
                                    		<select id="passing_month" name="passing_month" class="form-control">
                                    			<option value="" >Passing Month</option>
                                    			<option value="Jan" <?=($passing_month == 'Jan') ? 'selected' : ''?>>Jan</option>
                                    			<option value="Feb" <?=($passing_month == 'Feb') ? 'selected' : ''?>>Feb</option>
                                    			<option value="March" <?=($passing_month == 'March') ? 'selected' : ''?>>March</option>
                                    			<option value="Apr" <?=($passing_month == 'Apr') ? 'selected' : ''?>>Apr</option>
                                    			<option value="May" <?=($passing_month == 'May') ? 'selected' : ''?>>May</option>
                                    			<option value="June" <?=($passing_month == 'June') ? 'selected' : ''?>>June</option>
                                    			<option value="July" <?=($passing_month == 'July') ? 'selected' : ''?>>July</option>
                                    			<option value="Aug" <?=($passing_month == 'Aug') ? 'selected' : ''?>>Aug</option>
                                    			<option value="Sep" <?=($passing_month == 'Sep') ? 'selected' : ''?>>Sep</option>
                                    			<option value="Oct" <?=($passing_month == 'Oct') ? 'selected' : ''?>>Oct</option>
                                    			<option value="Nov" <?=($passing_month == 'Nov') ? 'selected' : ''?>>Nov</option>
                                    			<option value="Dec" <?=($passing_month == 'Dec') ? 'selected' : ''?>>Dec</option>
                                    		</select>
                                    	</div>	
                                    </div>-->
                              </div>
                              <div class="row mrg-top-20">
                                 <div class="col-lg-12 col-md-6 col-sm-12">
                                    <div class="alert alert-success" role="alert">
                                       Professional Details
                                    </div>
                                 </div>
                                 <div class="col-lg-4 col-md-6 col-sm-12">
                                    <div class="form-group">
                                       <label>Profile / Designation <span class="mand">*</span></label>
                                       <input type="text" class="form-control"
                                          placeholder="Designation / Current Profile" 
                                          name="designations" value="<?=$designations?>"/>
                                    </div>
                                 </div>
                                 <div class="col-lg-4 col-md-6 col-sm-12 professional">
                                    <div class="form-group">
                                       <label>Organization/Company</label>
                                       <input type="text" class="form-control" 
                                          placeholder="Organization / Company" 
                                          name="company_name" 
                                          id="company_name" value="<?=$company_name?>" />
                                    </div>
                                 </div>
                                 <div class="col-lg-4 col-md-6 col-sm-12 professional">
                                    <div class="form-group">
                                       <label>Current CTC</label>
                                       <input type="text" class="form-control"
                                          placeholder="Current CTC" 
                                          name="current_ctc" id="current_ctc" value="<?=$current_ctc?>" >
                                    </div>
                                 </div>
                                 <div class="col-lg-4 col-md-6 col-sm-12 professional">
                                    <div class="form-group">
                                       <label>Notice Period</label>
                                       <select class="form-control" name="notice_period" id="notice_period">
                                          <option value="">Notice Period</option>
                                          <option value="7" <?=($notice_period == '7') ? 'selected' : ''?>>7 Day</option>
                                          <option value="15" <?=($notice_period == '15') ? 'selected' : ''?>>15 Day</option>
                                          <option value="30" <?=($notice_period == '30') ? 'selected' : ''?>>30 Day</option>
                                       </select>
                                    </div>
                                 </div>
                                 <!--<div class="col-lg-4 col-md-6 col-sm-12">
                                    <div class="form-group">
                                    	<label>Expected Salary</label>
                                    	<input type="text" class="form-control" placeholder="Expected Salary" name="expected_salary" value="<?=$expected_salary?>">
                                    </div>	
                                    </div>-->
                                 <!---<div class="col-lg-4 col-md-6 col-sm-12">
                                    <div class="form-group">
                                    	<label>Resume Title</label>
                                    	<input type="text" class="form-control" placeholder="Resume Title" name="resume_title" value="<?=$resume_title?>">
                                    </div>	
                                    </div>--->
                                 <!--<div class="col-lg-4 col-md-6 col-sm-12">
                                    <div class="form-group">
                                    	<label>Employee Type</label>
                                    	<select class="form-control" name="employee_type" id="employee_type">
                                    		<option value="">Employee Type</option>
                                    		<option value="Freelancer" <?=($employee_type == 'Freelancer') ? 'selected' : ''?>>Freelancer</option>
                                    		<option value="Permanent" <?=($employee_type == 'Permanent') ? 'selected' : ''?>>Permanent</option>
                                    		<option value="Temporary/Contract" <?=($employee_type == 'Temporary/Contract') ? 'selected' : ''?>>Temporary/Contract</option>
                                    		<option value="Full Time" <?=($employee_type == 'Full Time') ? 'selected' : ''?>>Full Time</option>
                                    		<option value="Part Time" <?=($employee_type == 'Part Time') ? 'selected' : ''?>>Part Time</option>
                                    		<option value="Work From Home" <?=($employee_type == 'Work From Home') ? 'selected' : ''?>>Work From Home</option>
                                    		<option value="Remote Work" <?=($employee_type == 'Remote Work') ? 'selected' : ''?>>Remote Work</option>
                                    		
                                    	</select>
                                    </div>
                                    </div>
                                    <div class="col-lg-4 col-md-6 col-sm-12">
                                    <div class="form-group"  id="freelancer_rate">
                                    	<label>Freelancer Rate</label>
                                    	<select class="form-control" name="freelancer_rate" >
                                    		<?php //if(!empty($freelancer_rate)){?>
                                    			<option value="<?//=$freelancer_rate?>" selected >$<?//=$freelancer_rate?> /hr</option>
                                    		<?php //}?>
                                    		<option value="">Job Type</option>
                                    		<?php //$i=0; for($i="1";$i<="350"; $i++){?>
                                    			<option value="<?//=$i;?>">$<?//=$i;?> /hr</option>
                                    		<?php //}?>
                                    	</select>
                                    </div>
                                    </div>
                                    
                                    <div class="col-lg-4 col-md-6 col-sm-12">
                                    <div class="form-group"  id="freelancer_rate">
                                    	<label>Availablity Status </label>
                                    	<select class="form-control" name="candidate_availablity_status" >
                                    		<option value="" disabled></option>
                                    		<option value="Available" <?//=($candidate_availablity_status == 'Available') ? 'selected' : ''?> >Available</option>
                                    		<option value="At Work" <?//=($candidate_availablity_status == 'At Work') ? 'selected' : ''?>>At Work</option>
                                    		<option value="Busy" <?//=($candidate_availablity_status == 'Busy') ? 'selected' : ''?>>Busy</option>
                                    		<option value="Not Available" <?//=($candidate_availablity_status == 'Not Available') ? 'selected' : ''?>>Not Available</option>
                                    	</select>
                                    </div>
                                    </div>-->
                                 <div class="col-lg-4 col-md-6 col-sm-12">
                                    <div class="form-group">
                                       <label>Upload Resume</label>
                                       <input type="file" class="form-control" placeholder="" name="resume" >
                                    </div>
                                 </div>
                                 <!--<div class="col-lg-12 col-md-12 col-sm-12">
                                    <div class="form-group">
                                    	<label>Student About</label>
                                    	<textarea class="form-control textarea" name="student_about" placeholder="Student Company"><?//=$student_about?></textarea>
                                    </div>	
                                    </div>-->
                                 <div class="row mrg-top-30">
                                    <div class="col-md-12 col-sm-12">
                                       <div class="form-group text-center">
                                          <button type="submit" class="btn-savepreview" id="submit_btn"><i class="ti-angle-double-right"></i>Save</button>
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
      <head>
      <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-9268075008862469"
         crossorigin="anonymous"></script>
      <ins class="adsbygoogle"
         style="display:block"
         data-ad-format="autorelaxed"
         data-ad-client="ca-pub-9268075008862469"
         data-ad-slot="9174453996"></ins>
      <script>
         (adsbygoogle = window.adsbygoogle || []).push({});
      </script>
      </head>
      <!-- ============================ Footer Start ================================== -->
      <?php $this->load->view('common/inc/footer');?>
      <!-- ============================ Footer End ================================== -->
      <!-- End Signin Window -->
      <script src="<?=base_url('assets/resources/');?>assets/js/dashboard-custom.js"></script>
      <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.17.0/dist/jquery.validate.min.js"></script>
      <script src="https://cdn.jsdelivr.net/jquery.validation/1.15.0/additional-methods.min.js"></script>	
      <script src="https://cdnjs.cloudflare.com/ajax/libs/filesize/3.5.11/filesize.min.js"></script>
      <!--<script src='https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.5/jquery-ui.min.js' type='text/javascript'></script>-->
      <script src="https://bootstrap-tagsinput.github.io/bootstrap-tagsinput/dist/bootstrap-tagsinput.min.js"></script>
      <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/js/bootstrap-datepicker.js"></script>
      <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
      <script src="<?=base_url('assets/resources/');?>assets/js/autocomplete.js"></script>
      <script>
         $("#datepicker1" ).datepicker({
         	dateFormat: 'yy-mm-dd',
         	showAnim: "drop",
         	changeMonth: true,
         	changeYear: true,
         	showWeek: true,
         	yearRange: '1945:2022',
         	//minDate: 0
         });
         //$('#birthday').dateDropper();
         if('<?=$work_status?>'=="fresher"){
         	$('.professional').css('display','none');
         }	
         				
         $(document).on('change','#work_status',function(){
         	if($(this).val()=='fresher'){
         		$('.professional').css('display','none');
         }else if($(this).val()=='experience'){
         	$('.professional').css('display','');
         	}
         });
         			
         $('#skill').tagsinput({
         	maxTags: 5,
             maxChars: 20,
         	allowDuplicates: false,
         });
         $('#language').tagsinput({
         	maxTags: 3,
         	maxChars: 30,
         	allowDuplicates: false,
         });
      </script>
      <script type="text/javascript">
         $('#myForm').validate({
         /*$.validator.addMethod('filesize', function (value, element, param){
           return this.optional(element) || (element.files[0].size <= param)
          }, function(size){
         return "file size must be " + filesize(size,{exponent:2,round:1});
         });*/
         
         errorElement: "span",
         rules: {
         	name:{
         		required:true,
         		minlength:4,
         		maxlength:50,
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
         	work_status:"required",
         	gender:"required",
         	location_current:"required",
         	industry:"required",
         	functional_area:"required",
         	qualification:"required",
         	passing_year :"required",
         	course_type:"required",
         	designations:"required",
         	//company_name:"required",
         	current_ctc:{
         		//required :true,
         	    number: true
         	},
         	//notice_period:"required",
         	expected_salary:{
         		required :true,
         		number: true
         	},
         	//resume: {
         		//required: true,
         		//extension: "docx|rtf|doc|pdf",
         		//filesize: 1000 * 1024,  
         	//}
         },
         messages: {
         	name:{
         		required: 	"Full Name is required",
         		minlength: 	"should be minimum 4 characters",
         		maxlength: 	"should be maximum 50 characters",
         	},
         	email: {
         		required: 	"Email is required",
         		email: 		"Please enter a valid e-mail",
         	},
         	candidate_password:{
         		required: 	"Password is required",
         		minlength: 	"should be minimum 8 characters",
         		maxlength: 	"should be maximum 16 characters",
         	},
         	candidate_mobile:{
         		required: 	"Mobile number is requied",
         	    minlength: 	"Please enter 10 digit mobile number",
         		maxlength: 	"Please enter 10 digit mobile number",
         		digits: 	"Only numbers are allowed in this field"
         	},
         	gender: "Select gender",
         	location_current: "Select current location",
         	industry: "Select Iidustry",
         	functional_area: "Select functional area",
         	qualification: "Select qualification",
         	course_type: "Select course type",
         	passing_year: "Select passing year",
         	designations: "Enter designation",
         	//company_name: "Enter company name",
         	current_ctc: {
         		//required : "Enter current ctc",
         		number: "Only numbers are allowed in this field"
         	},
         	//notice_period: "Select notice",
         	expected_salary:{
         		required : "Enter expected salary",
         		number: "Only numbers are allowed in this field"
         	},
         	//resume: {
         		//required: "Please upload resume",
         		//extension: "Only pdf, doc, docx, rtf files are allowed",
         		//filesize: "file size must be less than 2MB.",
         	//}
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
           alert(data.success_msg['success_msg']);
           history.go(-1);
           window.open('<?=base_url('recommended-jobs')?>', '_blank');
         }
         },
         beforeSend: function() {
          $("#submit_btn").text('saving..').addClass('btn-secondary disabled');
         },
         complete: function() {
         $("#submit_btn").text('Update Changes').removeClass('btn-secondary disabled');
         }
         });
         }
         });	
      </script>
      </div>
   </body>
</html>