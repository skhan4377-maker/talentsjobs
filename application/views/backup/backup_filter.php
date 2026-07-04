

<div class="show-hide-sidebar hidden-xs hidden-sm">
<!-- Search Job -->
<div class="sidebar-widgets">
<div class="ur-detail-wrap">
<div class="ur-detail-wrap-header">
<b><h4>Search Job Here</h4></b>
</div>
<div class="ur-detail-wrap-body">
<form action="browse-jobs">
<div class="form-group">
<label>Keyword <span id="spinner"></span></label>
<input type="text" class="form-control job_search" autocomplete="off" id="job_search" placeholder="Search Job Title or Keyword">
<!--<input type="hidden" id="key_word" name="key_word">-->
</div>

<div class="form-group">
<label>Experince</label>
<select class="form-control experince" name="experince" id="experince">
<option value="">Choose Experince</option>
<?php $data = job_by_experience(false); foreach($data['data'] as $data=>$key): ?>
<option value="<?=$key['min_exp']?>"><?=$key['min_exp']?> - <?=$key['max_exp']?> Year (<?=$key['count_min_exp']?>)</option>
<?php endforeach;?>
</select>
</div>


<div class="form-group">
<label>Location <span id="preferred_spinner"></span></label>
<input type="text" autocomplete="off" placeholder="Search by Location" id="preferred_loaction" class="form-control input-lg preferred_loaction" >	
<!--<input type="hidden" id="preferred_id" name="preferred_location" class="preferred_location" value="<?//=@$_GET['preferred_location']?>">-->
</div>
													
<div class="form-group">
<label>Category</label>
<select id="choose-category" class="form-control industry" name="industry">
<option value="" >Choose Category</option>
<?php $data = job_by_category(8); foreach($data['data'] as $data=>$key): ?>
<option  value="<?=$key['industry']?>" <?//=(@$_GET['industry']==$key['industry']) ? 'selected' : $key['industry'];?> ><?=ucfirst($key['industry_name'])?></option>
<?php endforeach;?>
</select>
</div>

<div class="form-group">
<label>Company</label>
<select id="choose-company" class="form-control job_company" name="job_company">
<option value="">Select</option>
<?php $data = job_by_company(30); foreach($data['data'] as $data=>$key): ?>
<?php //if($key['count_company'] >=50){?>
<option value="<?=$key['reff_id']?>" <?//=(@$_GET['job_company']==$key['reff_id']) ? 'selected' : $key['reff_id'];?>><?=mb_strimwidth(ucfirst($key['employee_company_name']), 0, 30, "...");?></option>
<?php //}?>
<?php endforeach;?>
</select>
</div>
<!--<button type="submit" class="btn btn-primary full-width">Search Jobs</button>-->
</form>
</div>
</div>
</div>
<!-- /Search Job -->

<!-- -->
<div class="sidebar-widgets">
<div class="ur-detail-wrap">
<div class="ur-detail-wrap-body">
<div class="switchbox-outer">
<ul class="advance-list switchbox">
    
<li>
<span class="custom-checkbox"><h4>What are you looking for?</h4></span>
<label class="switch">
<input type="checkbox" class="job_level-btn" <?=($this->input->get('job_type')=='Internships-Graduate-Programs') ? 'checked' : ''?> value="Internships-Graduate-Programs">
<span class="slider round"></span>
<span class="title">Internships</span>
</label>
</li>

<li>
<label class="switch">
<input type="checkbox" class="job_level-btn" <?=($this->input->get('job_type')=='Part-Time-Jobs') ? 'checked' : ''?> value="Part-Time-Jobs">
<span class="slider round"></span>
<span class="title">Part Time</span>
</label>
</li>

<li>
<label class="switch">
<input type="checkbox" class="job_level-btn" <?=($this->input->get('job_type')=='Jobs-For-Fresher') ? 'checked' : ''?> value="Jobs-For-Fresher">
<span class="slider round"></span>
<span class="title">Fresher jobs</span>
</label>
</li>

<li>
<label class="switch">
<input type="checkbox" class="job_level-btn" <?=($this->input->get('job_type')=='Internships-Graduate-Programs') ? 'checked' : ''?> value="Part-Time-Jobs">
<span class="slider round"></span>
<span class="title">Remote Jobs</span>
</label>
</li>

    
    
<li><span class="custom-checkbox"><h4>Type of Opportunities?</h4></span>
<label class="switch">
<input type="checkbox" class="job_level-btn" <?=($this->input->get('job_type')=='Work-From-Home-Jobs') ? 'checked' : ''?> value="Work-From-Home-Jobs">
<span class="slider round"></span>
<span class="title">Work From Home </span>
</label>
</li>

<li>
<label class="switch">
<input type="checkbox" class="job_level-btn" <?=($this->input->get('job_type')=='Full-Time') ? 'checked' : ''?> value="Full-Time">
<span class="slider round"></span>
<span class="title">In Office </span>
</label>
</li>

<li>
<label class="switch">
<input type="checkbox" class="job_level-btn" <?=($this->input->get('job_type')=='Hybrid-Jobs') ? 'checked' : ''?> value="Hybrid-Jobs">
<span class="slider round"></span>
<span class="title">Search Job Near me  </span>
</label>
</li>



<div class="filter-block">
<h4 class="d-inline-block">Salary</h4>
<div class="range-slider-one salary-range">
<div class="salary-range-slider ui-slider ui-corner-all ui-slider-horizontal ui-widget ui-widget-content">
<div class="ui-slider-range ui-corner-all ui-widget-header" style="left: 0%; width: 100%;"></div>
<span tabindex="0" class="ui-slider-handle ui-corner-all ui-state-default" style="left: 0%;"></span>
<span tabindex="0" class="ui-slider-handle ui-corner-all ui-state-default" style="left: 100%;"></span>
</div>
<div class="input-outer">
<div class="amount-outer">
<span class="amount salary-amount">
<div class="amdiv">
<span class="min">0</span>INR
</div>
<div class="todiv">to</div>
<div class="amdiv">
<span class="max">100000</span> INR
</div>
</span>
</div>
</div>
</div>
</div>

</ul>
</div>
</div>
</div>
</div>
<!-- -->

<!-- Job Role -->
<div class="sidebar-widgets">
<div class="ur-detail-wrap colps-wrap">
<div class="ur-detail-wrap-header">
<h4 class="colps-head collapsed" data-toggle="collapse" href="#jb-everyone" role="button" aria-expanded="false" aria-controls="jb-types">Job Role </h4>
</div>
<div class="collapse" id="jb-everyone">
<div class="ur-detail-wrap-body">
<ul class="advance-list">
<?php $data = job_by_category(8); foreach($data['data'] as $data=>$key): ?>
<li>
<span class="custom-checkbox">
<input type="checkbox" class="common_selector industry" id="aw<?=$key['industry']?>" name="industry" value="<?=$key['industry']?>" >
<label for="aw<?=$key['industry']?>"></label>
</span>
<?=ucfirst($key['industry_name'])?>
<span class="pull-right"><?=$key['count_industry']?></span>
</li>
<?php endforeach;?>
</ul>
</div>
</div>
</div>
</div>
<!-- /Job Role -->
								

								
<!-- Job Type -->
<div class="sidebar-widgets">
<div class="ur-detail-wrap colps-wrap">
<div class="ur-detail-wrap-header">
<h4 class="colps-head collapsed" data-toggle="collapse" href="#jb-types" role="button" aria-expanded="false" aria-controls="jb-types">Job Type </h4>
</div>
<div class="collapse" id="jb-types">
<div class="ur-detail-wrap-body">
<ul class="advance-list">
<?php $data = job_by_everyone(10,null); foreach($data['data'] as $data=>$key):?>
<li>
<span class="custom-checkbox">
<input type="checkbox" id="<?=ucfirst($key['job_type'])?>" class="common_selector job_type" name="job_type" value="<?=ucfirst($key['job_type'])?>">
<label for="<?=ucfirst($key['job_type'])?>"></label>
</span>
<?=ucfirst($key['job_type'])?>
<span class="pull-right"><?=$key['count_job_type']?></span>
</li>
<?php endforeach;?>
</ul>
</div>
</div>
</div>
</div>
<!-- /Job Type -->
								
<!-- Qualification -->
<div class="sidebar-widgets">
<div class="ur-detail-wrap colps-wrap">
<div class="ur-detail-wrap-header">
<h4 class="colps-head collapsed" data-toggle="collapse" href="#jb-location" role="button" aria-expanded="false" aria-controls="jb-location">Qualification</h4>
</div>
<div class="collapse" id="jb-location">
<div class="ur-detail-wrap-body">
<ul class="advance-list">
<?php $data = job_by_qualification(8); foreach($data['data'] as $data=>$key): ?>
<li>
<span class="custom-checkbox">
<input type="checkbox" id="<?=$key['qualification']?>" class="common_selector qualification" name="qualification" value="<?=$key['qualification']?>">
<label for="<?=$key['qualification']?>"></label>
</span>
<?=$key['qualification']?>
<span class="pull-right"><?=$key['count_qualification']?></span>
</li>
<?php endforeach;?>
</ul>
</div>
</div>
</div>
</div>
<!-- /Qualification -->
								
<!-- salary -->
<div class="sidebar-widgets">
<div class="ur-detail-wrap colps-wrap">
<div class="ur-detail-wrap-header">
<h4 class="colps-head collapsed" data-toggle="collapse" href="#jb-salary" role="button" aria-expanded="false" aria-controls="jb-salary">Salary</h4>
</div>
<div class="collapse" id="jb-salary">
<div class="ur-detail-wrap-body">
<ul class="advance-list">
<li>
<span class="custom-checkbox">
<input type="checkbox"  name="salary" class="common_selector salary" value="5000">
<label for="1"></label>
</span>
More than ₹5000
<span class="pull-right"></span>
</li>
<li>
<span class="custom-checkbox">
<input type="checkbox" name="salary" class="common_selector salary" value="10000">
<label for="1"></label>
</span>
More than ₹10000
<span class="pull-right"></span>
</li>
<li>
<span class="custom-checkbox">
<input type="checkbox" name="salary" class="common_selector salary" value="20000">
<label for="1"></label>
</span>
More than ₹20000
<span class="pull-right"></span>
</li>
<li>
<span class="custom-checkbox">
<input type="checkbox" name="salary" class="common_selector salary" value="30000">
<label for="1"></label>
</span>
More than ₹30000
<span class="pull-right"></span>
</li>
<li>
<span class="custom-checkbox">
<input type="checkbox" name="salary" class="common_selector salary" value="40000">
<label for="1"></label>
</span>
More than ₹40000
<span class="pull-right"></span>
</li>
<li>
<span class="custom-checkbox">
<input type="checkbox" name="salary"  class="common_selector salary" value="50000">
<label for="1"></label>
</span>
More than ₹50000
<span class="pull-right"></span>
</li>
</ul>
</div>
</div>
</div>
</div>
<!-- /salary -->

<!-- Location 
<div class="sidebar-widgets">
<div class="ur-detail-wrap colps-wrap">
<div class="ur-detail-wrap-header">
<h4 class="colps-head collapsed" data-toggle="collapse" href="#jb-Location" role="button" aria-expanded="false" aria-controls="jb-Country">Location</h4>
</div>
<div class="collapse" id="jb-Location">
<div class="ur-detail-wrap-body">
<ul class="advance-list">
<?php //$data = job_by_location(25); foreach($data['data'] as $data=>$key): ?>
<?php //if($key['count_current_location'] >= 50) {?>
<li>
<span class="custom-checkbox">
<input type="checkbox" id="<?//=$key['current_location']?>" class="common_selector preferred_location" name="preferred_location" value="<?//=$key['current_location']?>" >
<label for="<?//=$key['current_location']?>"></label>
</span>
<?//=$key['city_name']?>
<span class="pull-right"><?//=$key['count_current_location']?></span>
</li>
<?php //}?>
<?php //endforeach;?>
</ul>
</div>
</div>
</div>
</div>
                					
<!-- /Location -->
                					
                					
<!-- Country -->
<div class="sidebar-widgets">
<div class="ur-detail-wrap colps-wrap">
<div class="ur-detail-wrap-header">
<h4 class="colps-head collapsed" data-toggle="collapse" href="#jb-Country" role="button" aria-expanded="false" aria-controls="jb-Country">Country</h4>
</div>
<div class="collapse" id="jb-Country">
<div class="ur-detail-wrap-body">
<ul class="advance-list">
<?php $data = job_by_country(15); foreach($data['data'] as $data=>$key): ?>
<?php //if($key['count_country'] >= 25) {?>
<li>
<span class="custom-checkbox">
<input type="checkbox" id="<?=$key['tcid']?>" class="common_selector country" name="country" value="<?=$key['tcid']?>" >
<label for="<?=$key['tcid']?>"></label>
</span>
<?=$key['name']?>
<span class="pull-right"><?=$key['count_country']?></span>
</li>
<?php //}?>
<?php endforeach;?>
</ul>
</div>
</div>
</div>
</div>
<!-- /Country -->
</div>


<!-- /Mobile Filter -->	
<div id="filter-sidebar" class="filter-sidebar">
<strong>Welcome!</strong>
<div class="d-flex mt-10">
    <a href="<?=base_url()?>/register?sr=em" style="display: flex;align-items: center;line-height: 0.428571;" class="btn btns-outline-secondary mr-15">Sign in</a>
    <a href="<?=base_url()?>/register" style="display: flex;align-items: center;line-height: 0.428571;" class="btn btns-outline-secondary">Register</a>
</div>

<a href="javascript:void(0)" class="closebtn" onclick="closeNav()"><i class="ti-close"></i></a>

<div class="show-hide-sidebar">
    
<div class="sidebar-widgets">
<div class="ur-detail-wrap">
<div class="ur-detail-wrap-header">
<h4>Search Job Here  &nbsp; <a href="javascript:void(0)" class="btn btn-info" id="apply" style="line-height: 0.428571;background: #1db9aa00;border-color: #23c0e9;color: #23c0e9;"><i class="ti-filter mrg-r-5"></i> Apply</a></h4>
</div>
<div class="ur-detail-wrap-body">
<form action="browse-jobs">	
<div class="form-group">
<label>Keyword <span id="m_spinner"></span></label>
<input type="text" autocomplete="off" class="form-control m_job_search" id="m_job_search" name="key_word"  placeholder="Job Title or Keyword">
<!--<input type="hidden" id="m_key_word">-->
</div>

<div class="form-group">
<label>Experince</label>
<select class="form-control mexperince" name="experince">
<option value="" >Choose Experince</option>
<?php $data = job_by_experience(false); foreach($data['data'] as $data=>$key): ?>
<option value="<?=$key['min_exp']?>" <?//=(@$_GET['experince']==$key['min_exp']) ? 'selected' : $key['min_exp'];?> ><?=$key['min_exp']?> - <?=$key['max_exp']?> Year (<?=$key['count_min_exp']?>)</option>
<?php endforeach;?>
</select>
</div>

<div class="form-group">
<label>Location <span id="current_spinner"></span></label>
<input type="text" autocomplete="off" placeholder="Job Location" name="preferred_location"  id="current_location" class="form-control" >
<!--<input type="hidden" id="current_id"  name="preferred_location"">-->
</div>
									
<div class="form-group">
<label>Category</label>
<select id="choose-category" class="form-control mindustry" name="industry">
<option value="">Select</option>
<?php $data = job_by_category(8); foreach($data['data'] as $data=>$key): ?>
<option value="<?=$key['industry']?>" <?//=(@$_GET['industry']==$key['industry']) ? 'selected' : $key['industry'];?>><?=ucfirst($key['industry_name'])?></option>
<?php endforeach;?>
</select>
</div>

<div class="form-group">
<label>Company</label>
<select id="choose-company" class="form-control mcompany" name="job_company">
<option value="">Select</option>
<?php $data = job_by_company(30); foreach($data['data'] as $data=>$key): ?>
<?php //if($key['count_company'] >=50){?>
<option value="<?=$key['reff_id']?>" <?//=(@$_GET['job_company']==$key['reff_id']) ? 'selected' : $key['reff_id'];?>><?=mb_strimwidth(ucfirst($key['employee_company_name']), 0, 30, "...");?></option>
<?php //}?>
<?php endforeach;?>
</select>
</div>

</form>	
</div>
</div>
</div>
<!--Search Job -->

<!--
<div class="sidebar-widgets">
<div class="ur-detail-wrap">
<div class="ur-detail-wrap-header">
<h4>Experince</h4>
</div>
<div class="ur-detail-wrap-body">
<ul class="advance-list">
<?php //$data = job_by_experience(false); foreach($data['data'] as $data=>$key): ?>
<li>
<span class="custom-checkbox">
<input type="checkbox" id="uy<?//=$key['min_exp']?>" class="common_selector experince" name="experince" value="<?//=$key['min_exp']?>">
<label for="uy<?//=$key['min_exp']?>"></label>
</span>
<?//=$key['min_exp']?> - <?//=$key['max_exp']?> Year
<span class="pull-right"><?//=$key['count_min_exp']?></span>
</li>
<?php //endforeach;?>
</ul>
</div>
</div>
</div>
<!-- /Experince -->	

<div class="sidebar-widgets">
<div class="ur-detail-wrap">
<div class="ur-detail-wrap-body">
<div class="switchbox-outer">
<ul class="advance-list switchbox">
    
<li>
<span class="custom-checkbox"><h4>What are you looking for?</h4></span>
<label class="switch">
<input type="checkbox" class="job_level-btn" <?=($this->input->get('job_type')=='Internships-Graduate-Programs') ? 'checked' : ''?> value="Internships-Graduate-Programs">
<span class="slider round"></span>
<span class="title">Internships</span>
</label>
</li>

<li>
<label class="switch">
<input type="checkbox" class="job_level-btn" <?=($this->input->get('job_type')=='Part-Time-Jobs') ? 'checked' : ''?> value="Part-Time-Jobs">
<span class="slider round"></span>
<span class="title">Part Time</span>
</label>
</li>

<li>
<label class="switch">
<input type="checkbox" class="job_level-btn" <?=($this->input->get('job_type')=='Jobs-For-Fresher') ? 'checked' : ''?> value="Jobs-For-Fresher">
<span class="slider round"></span>
<span class="title">Fresher jobs</span>
</label>
</li>

<li>
<label class="switch">
<input type="checkbox" class="job_level-btn" <?=($this->input->get('job_type')=='Internships-Graduate-Programs') ? 'checked' : ''?> value="Part-Time-Jobs">
<span class="slider round"></span>
<span class="title">Remote Jobs</span>
</label>
</li>

    
    
<li><span class="custom-checkbox"><h4>Type of Opportunities?</h4></span>
<label class="switch">
<input type="checkbox" class="job_level-btn" <?=($this->input->get('job_type')=='Work-From-Home-Jobs') ? 'checked' : ''?> value="Work-From-Home-Jobs">
<span class="slider round"></span>
<span class="title">Work From Home </span>
</label>
</li>

<li>
<label class="switch">
<input type="checkbox" class="job_level-btn" <?=($this->input->get('job_type')=='Full-Time') ? 'checked' : ''?> value="Full-Time">
<span class="slider round"></span>
<span class="title">In Office </span>
</label>
</li>

<li>
<label class="switch">
<input type="checkbox" class="job_level-btn" <?=($this->input->get('job_type')=='Hybrid-Jobs') ? 'checked' : ''?> value="Hybrid-Jobs">
<span class="slider round"></span>
<span class="title">Search Job Near me </span>
</label>
</li>



<div class="filter-block">
<h4 class="d-inline-block">Salary</h4>
<div class="range-slider-one salary-range">
<div class="salary-range-slider ui-slider ui-corner-all ui-slider-horizontal ui-widget ui-widget-content">
<div class="ui-slider-range ui-corner-all ui-widget-header" style="left: 0%; width: 100%;"></div>
<span tabindex="0" class="ui-slider-handle ui-corner-all ui-state-default" style="left: 0%;"></span>
<span tabindex="0" class="ui-slider-handle ui-corner-all ui-state-default" style="left: 100%;"></span>
</div>
<div class="input-outer">
<div class="amount-outer">
<span class="amount salary-amount">
<div class="amdiv">
<span class="min">0</span>INR
</div>
<div class="todiv">to</div>
<div class="amdiv">
<span class="max">100000</span> INR
</div>
</span>
</div>
</div>
</div>
</div>

</ul>
</div>
</div>
</div>
</div>


<div class="sidebar-widgets">
<div class="ur-detail-wrap colps-wrap">
<div class="ur-detail-wrap-header">
<h4 class="colps-head collapsed" data-toggle="collapse" href="#jb-category2" role="button" aria-expanded="false" aria-controls="jb-types">Job Role </h4>
</div>
<div class="collapse" id="jb-category2">
<div class="ur-detail-wrap-body">
<ul class="advance-list">
<?php $data = job_by_category(8); foreach($data['data'] as $data=>$key): ?>
<li>
<span class="custom-checkbox">
<input type="checkbox" class="common_selector industry" id="aw<?=$key['industry']?>" name="industry" value="<?=$key['industry']?>" <?//=($this->input->get('industry')==$key['industry']) ? 'checked' : ''?>>
<label for="aw<?=$key['industry']?>"></label>
</span>
<?=ucfirst($key['industry_name'])?>
<span class="pull-right"><?=$key['count_industry']?></span>
</li>
<?php endforeach;?>
</ul>
</div>
</div>
</div>
</div>

				

				
 
<div class="sidebar-widgets">
<div class="ur-detail-wrap colps-wrap">
<div class="ur-detail-wrap-header">
<h4 class="colps-head collapsed" data-toggle="collapse" href="#jb-types2" role="button" aria-expanded="false" aria-controls="jb-types">Job Type </h4>
</div>
<div class="collapse" id="jb-types2">
<div class="ur-detail-wrap-body">
<ul class="advance-list">
<?php $data = job_by_everyone(10,null); foreach($data['data'] as $data=>$key):?>
<li>
<span class="custom-checkbox">
<input type="checkbox" id="<?=ucfirst($key['job_type'])?>" class="common_selector job_type" name="job_type" value="<?=$key['job_type']?>">
<label for="<?=ucfirst($key['job_type'])?>"></label>
</span>
<?=ucfirst($key['job_type'])?>
<span class="pull-right"><?=$key['count_job_type']?></span>
</li>
<?php endforeach;?>
</ul>
</div>
</div>
</div>
</div>

				

<div class="sidebar-widgets">
<div class="ur-detail-wrap colps-wrap">
<div class="ur-detail-wrap-header">
<h4 class="colps-head collapsed" data-toggle="collapse" href="#jb-location2" role="button" aria-expanded="false" aria-controls="jb-location">Qualification</h4>
</div>
<div class="collapse" id="jb-location2">
<div class="ur-detail-wrap-body">
<ul class="advance-list">
<?php $data = job_by_qualification(8); foreach($data['data'] as $data=>$key): ?>
<li>
<span class="custom-checkbox">
<input type="checkbox" id="<?=$key['qualification']?>" class="common_selector qualification" name="qualification" value="<?=$key['qualification']?>">
<label for="<?=$key['qualification']?>"></label>
</span>
<?=$key['qualification']?>
<span class="pull-right"><?=$key['count_qualification']?></span>
</li>
<?php endforeach;?>
</ul>
</div>
</div>
</div>
</div>

				

<div class="sidebar-widgets">
<div class="ur-detail-wrap colps-wrap">
<div class="ur-detail-wrap-header">
<h4 class="colps-head collapsed" data-toggle="collapse" href="#jb-salary2" role="button" aria-expanded="false" aria-controls="jb-salary2">Salary</h4>
</div>
<div class="collapse" id="jb-salary2">
<div class="ur-detail-wrap-body">
<ul class="advance-list">
<li>
<span class="custom-checkbox">
<input type="checkbox"  name="salary" class="common_selector salary" value="5000">
<label for="1"></label>
</span>
More than ₹5000
<span class="pull-right"></span>
</li>
<li>
<span class="custom-checkbox">
<input type="checkbox" name="salary" class="common_selector salary" value="10000">
<label for="1"></label>
</span>
More than ₹10000
<span class="pull-right"></span>
</li>
<li>
<span class="custom-checkbox">
<input type="checkbox" name="salary" class="common_selector salary" value="20000">
<label for="1"></label>
</span>
More than ₹20000
<span class="pull-right"></span>
</li>
<li>
<span class="custom-checkbox">
<input type="checkbox" name="salary" class="common_selector salary" value="30000">
<label for="1"></label>
</span>
More than ₹30000
<span class="pull-right"></span>
</li>
<li>
<span class="custom-checkbox">
<input type="checkbox" name="salary" class="common_selector salary" value="40000">
<label for="1"></label>
</span>
More than ₹40000
<span class="pull-right"></span>
</li>
<li>
<span class="custom-checkbox">
<input type="checkbox" name="salary"  class="common_selector salary" value="50000">
<label for="1"></label>
</span>
More than ₹50000
<span class="pull-right"></span>
</li>
</ul>
</div>
</div>
</div>
</div>



<!--<div class="sidebar-widgets">
<div class="ur-detail-wrap colps-wrap">
<div class="ur-detail-wrap-header">
<h4 class="colps-head collapsed" data-toggle="collapse" href="#jb-Country2" role="button" aria-expanded="false" aria-controls="jb-Country2">Country</h4>
</div>
<div class="collapse" id="jb-Country2">
<div class="ur-detail-wrap-body">
<ul class="advance-list">
<?php //$data = job_by_country(15); foreach($data['data'] as $data=>$key): ?>
<?php //if($key['count_country'] >= 25) {?>
<li>
<span class="custom-checkbox">
<input type="checkbox" id="<?//=$key['tcid']?>" class="common_selector country" name="country" value="<?//=$key['tcid']?>" >
<label for="<?//=$key['tcid']?>"></label>
</span>
<?//=$key['name']?>
<span class="pull-right"><?//=$key['count_country']?></span>
</li>
<?php //}?>
<?php //endforeach;?>
</ul>
</div>
</div>
</div>
</div>-->

</div>
</div>
			