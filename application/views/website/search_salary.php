<?php $this->load->view('particles/top-header');?>
<link type="text/css" rel="stylesheet" href="<?=base_url('assets/frontend/css/page-salary-search.css');?>">
<script  type="text/javascript" src="<?=base_url('assets/frontend/js/tax-salary.js');?>"></script>
<style>


.c-card__btn{
    padding:8px 8px;
    margin-right:10px
}
.c-card{
    width:100%;
    margin-top:10px;
    padding:12px
}
.l-card__btn-container{
    overflow:hidden;
    position:relative;
    padding:0 15px
}
.c-card__btn-group{
    flex-wrap:wrap;
    display:flex;
    align-items:center;
    width:100%;
    height:55px;
    overflow-x:auto;
    white-space:nowrap;
    flex-wrap:unset;
    padding:0 10px 0 0
}
.c-card__btn:last-of-type{
    margin-right:0
}

.c-card__btn-fade{
    height:43px;
    position:absolute;
    pointer-events:none;
    top:6px;
    z-index:1;
    align-items:center;
    justify-content:center
}
.c-card__fade-img-right{
    position:relative;
    right:-15px;
    width:8px;
    height:14px
}
.c-card__fade-img-left{
    position:relative;
    left:-15px;
    width:8px;
    height:14px
}
.l-card__holder{
    flex-wrap:wrap
}
.c-card--bottom{
    margin-top:0;
    padding:0 0 20px
}
.c-card--btn-label{
    margin-bottom:10px;
    display:flex;
    flex:auto;
    align-items:center;
    padding:15px 15px 0
}
.c-card--btn-group{
    flex-wrap:wrap;
    padding:0
}
.l-card__img-holder{
    margin-bottom:unset
}


 @media (max-width: 768px) {
.c-card__stats-chart{
    flex-direction:column
}
.l-card__stats--top-row{
    padding:15px 5px 10px
}
.l-card__stats--bottom-row{
    display:flex;
    flex-direction:column-reverse
}
.l-card__stats--right-column{
    display:flex;
    flex-direction:column;
    flex:1;
    padding:15px;
    margin-top:unset
}
}
  
</style>
<style>
   /* Container styles */
   .myproject-salary-container {
   max-width: 1100px;
   margin: 0 auto;
   padding: 20px;
   background-color: #fff;
   box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
   border-radius: 8px;
   display: flex;
   flex-direction: row; /* Arrange items horizontally */
   align-items: center; /* Center vertically */
   }
   /* Heading and Description styles */
   .myproject-left-section {
   flex-grow: 1; /* Allow section to grow and take available space */
   }
   /* Heading styles */
   .myproject-left-heading {
   font-size: 24px;
   margin-bottom: 10px;
   }
   /* Description styles */
   .myproject-description {
   font-size: 18px;
   margin-bottom: 20px;
   }
   /* Form styles */
   .myproject-form {
   text-align: left;
   }
   .myproject-label {
   display: block;
   font-weight: bold;
   text-align: left;
   }
   .myproject-input[type="text"] {
   padding: 15px; /* Increase text field height */
   width: 70%; /* Adjust text field width to 100% for responsiveness */
   border: 1px solid #ccc;
   border-radius: 50px; /* Increase border radius */
   font-size: 16px;
   }
   .myproject-button {
   margin-top: 10px; /* Add space between input field and button */
   padding: 15px 30px; /* Increase button size */
   background-color: #007BFF;
   color: #fff;
   border: none;
   border-radius: 50px; /* Increase border radius */
   cursor: pointer;
   font-size: 16px;
   }
   .myproject-button:hover {
   background-color: #0056b3;
   }
   /* Right side image (desktop only) */
   .myproject-right-image {
   margin-left: 20px; /* Add space between the form and image */
   display: block;
   }
   .myproject-right-image img {
   max-width: 225px; /* Set image width to 225px */
   height: auto;
   }
   .button-salary-container{
   max-width: 1100px;
   margin: 9px auto;
   padding: 0px;
   background-color: #fff;
   box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
   border-radius: 8px;
   display: flex;
   flex-direction: row; /* Arrange items horizontally */
   align-items: center; /* Center vertically */ 
   }
   
     /* Add this CSS inside your <style> block */
    @media (min-width: 769px) {
       .c-card__categoryList-container {
          display: flex;
          flex-wrap: wrap;
       }
       .c-card__categoryList--li {
          width: calc(50% - 20px); /* 50% width for two items per row with spacing */
          margin-right: 20px; /* Adjust spacing between items horizontally */
          margin-bottom: 5px; /* Adjust spacing between rows vertically */
       }
       .c-card__categoryList--li:nth-child(2n) {
          margin-right: 0; /* Remove right margin for every second item (to create two per row) */
       }
    }

   

   /* Media query for mobile responsiveness */
   @media (max-width: 768px) {
   .myproject-salary-container {
   flex-direction: column; /* Stack items vertically on smaller screens */
   text-align: center;
   }
   .myproject-right-image {
   margin-left: 0; /* Remove margin on smaller screens */
   margin-top: 20px; /* Add space between the image and form */
   display: none; /* Hide the image on mobile screens */
   }
   .myproject-label {
   margin-top: 10px; /* Add space between label and input field */
   }
   .myproject-input[type="text"] {
   width: 100%; /* Adjust text field width to 100% for responsiveness */
   }
   .myproject-button {
   margin-top: 10px; /* Add space between input field and button */
   float: right; /* Float button to the right */
   }
   
   .l-card__salary-holder{
       display:none;
   }
   .l-card__bottom-salary-holder {
        display: block;
        align-self: normal;
        text-align: left;
        padding: 0 15px 15px;
    }


    .button-salary-container{
       width: 100%;
     }
   
   
   }
</style>
<?php $this->load->view('particles/header');?>


<section class="tab-sec gray">
   <div class="myproject-salary-container">
      <div class="myproject-left-section">
         <div class="myproject-description">
            <h1 class="myproject-left-heading">
                <?php if($this->input->get('job')){ ?>
                    <?php echo $this->input->get('job');?> average
                <?php }?>
                Salary in India / International 2024
                </h1>
                
            <p>Find out what the average salary is for your position</p>
         </div>
         <center>
<script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-9268075008862469"
         crossorigin="anonymous"></script>
        <!-- main -->
        <ins class="adsbygoogle"
             style="display:block"
             data-ad-client="ca-pub-9268075008862469"
             data-ad-slot="6220165203"
             data-ad-format="auto"
             data-full-width-responsive="true"></ins>
        <script>
             (adsbygoogle = window.adsbygoogle || []).push({});
        </script>
        </center>
         <form class="myproject-form" id="myproject-salary-search-form" method="get">
            <label class="myproject-label" for="myproject-job-title">Type in a job title:</label>
            <input class="myproject-input" type="text" id="myproject-job-title" placeholder="Job title" name="job">
            <button class="myproject-button" type="submit">Find Salary</button>
         </form>
      </div>
      <!-- Right side image (desktop only) -->
      <div class="myproject-right-image">
         <img src="https://cdn-static.talent.com/img/tax-calculator/img_header_salary.png" alt="Image">
      </div>
   </div>
   <div class="button-salary-container">
      <div class="c-card c-card--btn-group">
         <div class="c-card--btn-label">Salary rate</div>
         <div class="l-card__btn-container">
            <div id="timeBasedGroup" class="c-card__btn-group" timebase="peryear">
               <div class="c-card__btn c-card__btn--selected" onclick="setBase('timeBasedGroup','peryear',this)">Annual</div>
               <div class="c-card__btn " onclick="setBase('timeBasedGroup','permonth',this)">Month</div>
               <div class="c-card__btn " onclick="setBase('timeBasedGroup','perbiweekly',this)">Biweekly</div>
               <div class="c-card__btn " onclick="setBase('timeBasedGroup','perweek',this)">Weekly</div>
               <div class="c-card__btn " onclick="setBase('timeBasedGroup','perday',this)">Day</div>
               <div class="c-card__btn " onclick="setBase('timeBasedGroup','perhour',this)">Hour</div>
            </div>
            <div class="c-card__btn-fade c-card__btn-fade--left">
               <img class="c-card__fade-img-left" src="https://cdn-static.talent.com/img/tax-calculator/btn_left-arrow.png" alt="">
            </div>
            <div class="c-card__btn-fade c-card__btn-fade--right" style="display: none;">
               <img class="c-card__fade-img-right" src="https://cdn-static.talent.com/img/tax-calculator/btn_right-arrow.png" alt="">
            </div>
         </div>
      </div>
   </div>
   
   <?php if ($this->input->get('job')):?>
    <div class="button-salary-container">
        <?php if($averageSalaryData['median_salary'] > 0){?>
        <div class="c-card--shadow">
            <div class="c-card c-card--top">
                <div class="c-card__title">
                    How much does a <?php echo $this->input->get('job'); ?> make in India?
                </div>
            </div>

           <?php
                // Calculate different salary intervals
                $annualSalary = $averageSalaryData['median_salary'] * 12;
                $monthlySalary = $annualSalary / 12;
                $biweeklySalary = $monthlySalary / 2;
                $weeklySalary = $biweeklySalary / 2.1667;
                $dailySalary = $weeklySalary / 5;
                $hourlySalary = $dailySalary / 7.50;
                
            ?>

            <div class="c-card c-card--bottom">
                <div class="c-card__stats-chart" id="statschart">
                    <div class="l-card__stats--top-row">
                        <div class="l-card__stats--top">
                            <div class="l-card__mainNumber">
                                <div class="c-card__stats-mainNumber timeBased"
                                         peryear="₹ <?=number_format($annualSalary)?>"
                                         permonth="₹ <?=number_format($monthlySalary)?>"
                                         perbiweekly="₹ <?=number_format($biweeklySalary)?>"
                                         perweek="₹ <?=number_format($weeklySalary)?>"
                                         perday="₹ <?=number_format($dailySalary)?>"
                                         perhour="₹ <?=number_format($hourlySalary)?>">
                                    ₹ <?=$annualSalary?>
                                </div>
                                <div class="c-card__stats-timeUnit timeBased"
                                     peryear="/ Annual" permonth="/ Month" perbiweekly="/ Biweekly" perweek="/ Weekly" perday="/ Day" perhour="/ Hour">/ Annual
                                </div>
                            </div>
                            <div class="c-card__stats-based">
                                Based on <?=$averageSalaryData['functional_area_count']?> salaries
                            </div>
                        </div>
                    </div>
                    <div class="l-card__stats--bottom-row">
                        <div class="l-card__stats--left-column">
                            <div class="c-card__stats-info">
                                The average <b><?php echo $this->input->get('job'); ?></b> salary in <b>India</b> is
                                <b>₹ <?=$averageSalaryData['median_salary']?></b> per year or <b>₹ <?=$averageSalaryData['min_salary']?></b> per hour. Entry-level positions start at <b>₹ 250,000</b>
                                per year, while most experienced workers make up to <b>₹ <?=$averageSalaryData['max_salary']?></b> per year.
                            </div>
                        </div>
                        
                        <div class="l-card__stats--right-column">
                            <div class="l-card__stats--midLabel">
                                <div class="c-card--stats-graph-text">Median</div>
                                <div class="c-card--stats-graph-text timeBased"
                                    peryear="₹ <?=number_format($annualSalary)?>"
                                    permonth="₹ <?=number_format($monthlySalary)?>"
                                    perbiweekly="₹ <?=number_format($biweeklySalary)?>"
                                    perweek="₹ <?=number_format($weeklySalary)?>"
                                    perday="₹ <?=number_format($dailySalary)?>"
                                    perhour="₹ <?=number_format($hourlySalary)?>">
                                    ₹  <?=number_format($annualSalary)?>
                                </div>
                            </div>
                            <div class="l-card__stats--img-container">
                                <img class="l-card__stats--img" src="https://cdn-static.talent.com/img/salary-graph-talent.png" alt="chart">
                            </div>

                            <div class="l-card__stats--bottom-labels">
                                <!-- Calculate low and high salaries -->
                                <?php
                                    // Calculate different salary intervals
                                    $lowAnnualSalary = $averageSalaryData['min_salary'] * 12;
                                    $lowMonthlySalary = $lowAnnualSalary / 12;
                                    $lowBiweeklySalary = $lowMonthlySalary / 2;
                                    $lowWeeklySalary = $lowBiweeklySalary / 2.1667;
                                    $lowDailySalary = $lowWeeklySalary / 5;
                                    $lowhourlySalary = $lowDailySalary / 7.50;
                                
                                ?>

                                <!-- Low -->
                                <div class="l-card__stats--lowLabel">
                                    <div class="c-card--stats-graph-text">Low</div>
                                    <div class="c-card--stats-graph-text timeBased"
                                        peryear="₹ <?=number_format($lowAnnualSalary)?>"
                                        permonth="₹ <?=number_format($lowMonthlySalary)?>"
                                        perbiweekly="₹ <?=number_format($lowBiweeklySalary)?>"
                                        perweek="₹ <?=number_format($lowWeeklySalary)?>"
                                        perday="₹ <?=number_format($lowDailySalary)?>"
                                        perhour="₹ <?=number_format($lowhourlySalary)?>">
                                        ₹ <?=$lowAnnualSalary?>
                                    </div>
                                </div>
                                <?php  
                                
                                    $highannualSalary = $averageSalaryData['max_salary'] * 12;
                                    $highMonthlySalary = $highannualSalary / 12;
                                    $highBiweeklySalary = $highMonthlySalary / 2;
                                    $highWeeklySalary = $highBiweeklySalary / 2.1667;
                                    $highDailySalary = $highWeeklySalary / 5;
                                    $highHourlySalary = $highDailySalary / 7.50;?>
                                <!-- High -->
                                <div class="l-card__stats--highLabel">
                                    <div class="c-card--stats-graph-text">High</div>
                                    <div class="c-card--stats-graph-text timeBased"
                                         peryear="₹ <?=number_format($highannualSalary)?>"
                                         permonth="₹ <?=number_format($highMonthlySalary)?>"
                                         perbiweekly="₹ <?=number_format($highBiweeklySalary)?>"
                                         perweek="₹ <?=number_format($highWeeklySalary)?>"
                                         perday="₹ <?=number_format($highDailySalary)?>"
                                         perhour="₹ <?=number_format($highHourlySalary)?>">
                                        ₹ <?=$highannualSalary?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        
                        
                    </div>
                </div>
            </div>
        </div>
        <?php }else{?>
         <div class="c-card">
            <div class="l-card__nojobsFound--container">
                <div class="c-card--nojobsFound--text">We don't have any data related to your search...</div>
                    <div class="c-card--nojobsFound--imgHolder">
                    <img class="c-card--nojobsFound--img" src="https://cdn-static.talent.com/img/tax-calculator/salary_no_results_elon.png" alt="elon_not_found">
                </div>
            </div>
        </div>
        <?php }?>
    </div>
    <?php endif; ?>

    
    <?php foreach ($industries as $row) : ?>
    <div class="button-salary-container">
        <div class="c-card--shadow">
            <div class="c-card c-card--top">
                <div class="l-card__salary-info-container">
                    <div class="l-card__img-holder">
                        <img class="c-card__category-img" src="https://cdn-static.talent.com/img/tax-calculator/accounting_administration_categ.png" alt="">
                    </div>
                    <div class="l-card__title-holder">
                        <div class="c-card__title" title="<?= $row['industry_name'] ?>"><?= $row['industry_name'] ?></div>
                    </div>
                    <div class="l-card__salary-holder">
                        <?php
                            // Calculate salary intervals
                            $maxSalary = $row['max_salary'] * 12;
                            $salaryPerMonth = $maxSalary / 12;
                            $salaryPerBiweekly = $salaryPerMonth / 2;
                            $salaryPerWeek = $salaryPerBiweekly / 2.1667;
                            $salaryPerDay = $salaryPerWeek / 5;
                            $salaryPerHour = $salaryPerDay / 7.50;
                        ?>
            
                        <div class="c-card__category-salary-value timeBased" 
                            peryear="₹ <?= number_format($maxSalary) ?>" 
                            permonth="₹ <?= number_format($salaryPerMonth) ?>" 
                            perbiweekly="₹ <?= number_format($salaryPerBiweekly) ?>" 
                            perweek="₹ <?= number_format($salaryPerWeek) ?>" 
                            perday="₹ <?= number_format($salaryPerDay) ?>" 
                            perhour="₹ <?= number_format($salaryPerHour) ?>">
                            ₹ <?= $maxSalary ?>
                        </div>

                        <div class="c-card__category-salary-based">Based on <?= $row['job_count'] ?> salaries</div>
                    </div>
                </div>
            </div>

            <div class="c-card c-card--bottom">
                <div class="l-card__container">
                    <div class="l-card__category-result-holder">
                        <div class="l-card__bottom-salary-holder">
                            <div class="c-card__category-salary-value timeBased" 
                                peryear="₹ <?= number_format($maxSalary) ?>" 
                                permonth="₹ <?= number_format($salaryPerMonth) ?>" 
                                perbiweekly="₹ <?= number_format($salaryPerBiweekly) ?>" 
                                perweek="₹ <?= number_format($salaryPerWeek) ?>" 
                                perday="₹ <?= number_format($salaryPerDay) ?>" 
                                perhour="₹ <?= number_format($salaryPerHour) ?>">
                                ₹ <?= $maxSalary ?>
                            </div>

                            <div class="c-card__category-salary-based">
                                Based on <?= $maxSalary ?> salaries
                            </div>
                        </div>

                        <div class="c-card__categoryList-container" id="jobList">
                            <?php $displayedFunctionalAreas = array(); ?>
                            <?php foreach ($row['functional_areas'] as $functional_area) : ?>
                                <?php if (!in_array($functional_area['functional_area'], $displayedFunctionalAreas)) {
                                    $displayedFunctionalAreas[] = $functional_area['functional_area'];
                                    
                                    $functionalMaxSalary = $functional_area['max_salary'] * 12;
                                    $functionalSalaryPerMonth = $functionalMaxSalary / 12;
                                    $functionalSalaryPerBiweekly = $functionalSalaryPerMonth / 2;
                                    $functionalSalaryPerWeek = $functionalSalaryPerBiweekly / 2.1667;
                                    $functionalSalaryPerDay = $functionalSalaryPerWeek / 5;
                                    $functionalSalaryPerHour = $functionalSalaryPerDay / 7.50;
                                    
                                    ?>

                                    <a class="c-card__categoryList--li" href="<?=base_url('search-salary?job=')?><?=urldecode(trim($functional_area['functional_area']));?>">
                                        <div class="c-card__categoryList-text" title="<?= $functional_area['functional_area'] ?>" key="accountant_categories"><?= $functional_area['functional_area'] ?></div>
                                        <div class="c-card__categoryList-value timeBased" 
                                            peryear="₹ <?= number_format($functionalMaxSalary) ?>" 
                                            permonth="₹ <?= number_format($functionalSalaryPerMonth) ?>" 
                                            perbiweekly="₹ <?= number_format($functionalSalaryPerBiweekly) ?>" 
                                            perweek="₹ <?= number_format($functionalSalaryPerWeek) ?>" 
                                            perday="₹ <?= number_format($functionalSalaryPerDay) ?>" 
                                            perhour="₹ <?= number_format($functionalSalaryPerHour) ?>">
                                            ₹ <?= $functionalMaxSalary ?>
                                        </div>
                                    </a>

                                <?php } ?>
                            <?php endforeach; ?>

                            <!-- Add more items here as needed -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>

   
</section>




<?php $this->load->view('particles/footer');?>
