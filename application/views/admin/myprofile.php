
 <?php $this->load->view('admin/inc/top-header');?>
 <?php $this->load->view('admin/inc/header');?>

 <?php $this->load->view('admin/inc/left-sidebar');?>
 

    <div class="page-wrapper">

        <div class="content container-fluid">
        <div class="row">
            <div class="col-md-8 offset-md-2">
            
                <div class="page-header">
                    <div class="row">
                        <div class="col-sm-12">
                            <h3 class="page-title">Company Settings <span id="messageContainer"></span></h3> 
                        </div>
                    </div>
                </div>
            
             <form id="adminForm">
                <div class="row">
            <div class="col-sm-6">
            <div class="input-block mb-3">
            <label class="col-form-label">Company Name <span class="text-danger">*</span></label>
             <input class="form-control" type="text" name="company_name" value="<?=$adminDetails['employee_company_name']?>">
            </div>
            </div>
            <div class="col-sm-6">
            <div class="input-block mb-3">
            <label class="col-form-label">Contact Person</label>
            <input class="form-control " name="contact_person" value="<?=$adminDetails['employee_name']?>" type="text">
            </div>
            </div>
            </div>
            <div class="row">
            <div class="col-sm-12">
            <div class="input-block mb-3">
            <label class="col-form-label">Address</label>
            <input class="form-control " name="company_address" value="<?=$adminDetails['company_address']?>" type="text">
            </div>
            </div>
            <div class="col-sm-6 col-md-6 col-lg-3">
            <div class="input-block mb-3">
            <label class="col-form-label">Country</label>
            <select class="form-control select">
            <option value="101">India</option>
            </select>
            </div>
            </div>
            <div class="col-sm-6 col-md-6 col-lg-3">
            <div class="input-block mb-3">
            <label class="col-form-label">City</label>
            <input class="form-control" name="city_name" value="<?=$adminDetails['company_location']?>" type="text">
            </div>
            </div>
            
             <div class="col-sm-6 col-md-6 col-lg-6">
                <div class="input-block mb-3">
                    <label class="col-form-label">Email</label>
                    <input class="form-control" name="work_email" value="<?=$adminDetails['work_email']?>" type="email">
                </div>
            </div>
            </div>
            <div class="row">
          
           
            </div>
            <div class="row">
            <div class="col-sm-6">
            <div class="input-block mb-3">
            <label class="col-form-label">Mobile Number</label>
            <input class="form-control" name="company_contact" value="<?=$adminDetails['company_contact']?>" type="text">
            </div>
            </div>
            <div class="col-sm-6">
            <div class="input-block mb-3">
            <label class="col-form-label">Company Founded</label>
            <input class="form-control" name="company_founded" value="<?=$adminDetails['company_founded']?>" type="text">
            </div>
            </div>
            </div>
            <div class="row">
            <div class="col-sm-6">
            <div class="input-block mb-3">
            <label class="col-form-label">Website Url</label>
            <input class="form-control" name="company_website" value="<?=$adminDetails['company_website']?>" type="text">
            </div>
            </div>
            </div>
             <div class="submit-section">
                <button class="btn btn-primary submit-btn" type="button" id="saveButton">Save</button>
            </div>
            </form>
            </div>
        </div>
        </div>

    </div>


    <?php $this->load->view('admin/inc/footer');?>
    <script>
        $(document).ready(function() {
            $("#saveButton").click(function() {
                // Serialize the form data
                var formData = $("#adminForm").serialize();
    
                // Make an AJAX POST request to update the admin data
                $.ajax({
                    type: "POST",
                    url: "<?php echo base_url('admin/setting/updateAdminData'); ?>",
                    data: formData,
                    success: function(response) {
                        // Parse the JSON response
                        var responseData = JSON.parse(response);
                        
                        // Display the message on the page with the appropriate color
                        var messageContainer = $("#messageContainer");
                        messageContainer.html(responseData.message);
                        
                        // Set the text color based on success or error
                        if (responseData.success) {
                            messageContainer.css("color", "green");
                        } else {
                            messageContainer.css("color", "red");
                        }
                    }
                });
            });
        });
    </script>
