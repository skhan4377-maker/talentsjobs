
 <?php $this->load->view('admin/inc/top-header');?>
 <?php $this->load->view('admin/inc/header');?>
 
<style>
    #message {
        padding: 10px;
        margin-top: 10px;
        border: 1px solid #ccc;
        background-color: #f2f2f2;
        color: #333;
        font-size: 14px;
    }
    
    /* Style for success message */
    #message.success {
        background-color: #dff0d8;
        border-color: #d6e9c6;
        color: #3c763d;
    }
    
    /* Style for error message */
    #message.error {
        background-color: #f2dede;
        border-color: #ebccd1;
        color: #a94442;
    }
</style>
 <?php $this->load->view('admin/inc/left-sidebar');?>


<div class="page-wrapper">

    <div class="content container-fluid">
    
    <div class="page-header">
    <div class="row align-items-center">
    <div class="col">
    <h3 class="page-title">Setting</h3>
    <ul class="breadcrumb">
    <li class="breadcrumb-item"><a href="admin-dashboard.html">Dashboard</a></li>
    <li class="breadcrumb-item active">Setting </li> 
    </ul>
    </div>
    <div class="col-auto float-end ms-auto">
    <a href="#" class="btn add-btn" data-bs-toggle="modal" data-bs-target="#add_holiday"><i class="fa-solid fa-plus"></i> Add Setting</a>
    </div>
    </div>
    </div>
    
    <div class="row">
    <div class="col-md-12">
    <div class="table-responsive">
    
    <table class="table table-striped custom-table mb-0">
    <thead>
    <tr>
    <th>Global Variable</th>
    <th>Value </th>
    <th>Limit</th>
    <th>Status</th>
    <th class="text-end">Action</th>
    </tr>
    </thead>
    <tbody>
        <?php foreach($settings as $row):?>
            <tr class="holiday-upcoming">
                <td><?=$row['option_name']?></td>
                <td><?=$row['option_value']?></td>
                <td><?=$row['send_limit']?></td>
                <td><?=$row['status']?></td>
                <td class="text-end">
                <div class="dropdown dropdown-action">
                <a href="#" class="action-icon dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false"><i class="material-icons">more_vert</i></a>
                <div class="dropdown-menu dropdown-menu-right">
                <a class="dropdown-item edit-button"
                 id="edit-button"
                   href="#"
                   data-id="<?=$row['id']?>"
                   data-variable-name="<?=$row['option_name']?>"
                   data-value="<?=$row['option_value']?>"
                   data-sending-limit="<?=$row['send_limit']?>"
                   data-cron-job-status="<?=$row['status']?>"
                   data-bs-toggle="modal"
                   data-bs-target="#edit_holiday">
                   <i class="fa-solid fa-pencil m-r-5"></i> Edit
                </a>

                </div>
                </div>
                </td>
            </tr>
        <?php endforeach;?>
    </tbody>
    </table>
    
    
    </div>
    </div>
    </div>
    </div>
    
    <div class="modal custom-modal fade" id="add_holiday" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
    <div class="modal-header">
    <h5 class="modal-title">Add Setting</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
    <span aria-hidden="true">&times;</span>
    </button>
    
    </div>
    <div class="modal-body">
    <form id="settings-form">
        <div class="input-block mb-3">
            <div id="message"></div>
        </div>
        
         <div class="input-block mb-3">
            <div id="message"></div>
            <label class="col-form-label">Variable Name<span class="text-danger">*</span></label>
            <input class="form-control" value="" type="text" name="variable_name">
        </div>
        
        <div class="input-block mb-3">
            <label class="col-form-label">Value <span class="text-danger">*</span></label>
            <input class="form-control" value="" type="text" name="value">
        </div>
        <div class="input-block mb-3">
            <label class="col-form-label">Sending Limit <span class="text-danger">*</span></label>
            <input class="form-control" value="" type="text" name="sending_limit">
        </div>
        <div class="input-block mb-3">
            <label class="col-form-label">Cron Job On / Off <span class="text-danger">*</span></label><br>
            <label class="switch">
                <input type="checkbox"  id="cronJobCheckbox" name="cron_job_status">
                <span></span>
            </label>
        </div>
    
    
        <div class="submit-section">
            <button class="btn btn-primary submit-btn" type="submit">Save</button>
        </div>
    </form>
    
    </div>
    </div>
    </div>
    </div>
    
    <div class="modal custom-modal fade" id="edit_holiday" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
    <div class="modal-header">
    <h5 class="modal-title">Edit Setting</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
    <span aria-hidden="true">&times;</span>
    </button>
    </div>
    <div class="modal-body">
    <form id="settings-form">
        <div class="input-block mb-3">
            <div id="message"></div>
        </div>
        
         <div class="input-block mb-3">
            <div id="message"></div>
            <label class="col-form-label">Variable Name<span class="text-danger">*</span></label>
            <input class="form-control" value="" type="text" name="variable_name">
        </div>
        
        <div class="input-block mb-3">
            <label class="col-form-label">Value <span class="text-danger">*</span></label>
            <input class="form-control" value="" type="text" name="value">
        </div>
        <div class="input-block mb-3">
            <label class="col-form-label">Sending Limit <span class="text-danger">*</span></label>
            <input class="form-control"  type="text" name="sending_limit">
        </div>
        <div class="input-block mb-3">
            <label class="col-form-label">Cron Job On / Off <span class="text-danger">*</span></label><br>
            <label class="switch">
                <input type="checkbox" checked="checked" id="cronJobCheckbox" name="cron_job_status">
                <span></span>
            </label>
        </div>
    
    
        <div class="submit-section">
            <button class="btn btn-primary submit-btn" type="submit" id="myButton">Save</button>
        </div>
    </form>
    </div>
    </div>
    </div>
    </div>

</div>



<?php $this->load->view('admin/inc/footer');?>

<script>
    // JavaScript code to handle checkbox value change
    var checkbox = document.getElementById("cronJobCheckbox");
    checkbox.addEventListener("change", function() {
        if (checkbox.checked) {
            checkbox.value = "on";
        } else {
            checkbox.value = "off";
        }
    });
</script>
<script>
    $(document).ready(function () {
        // Handle form submission
        $("#settings-form").submit(function (event) {
            event.preventDefault(); // Prevent the form from submitting normally

            // Serialize form data
            var formData = $(this).serialize();

            // Get the base URL from a JavaScript variable
            var baseUrl = "<?php echo base_url(); ?>"; // Use the base_url() function provided by CodeIgniter

            // Define the backend URL relative to the base URL
            var backendUrl = baseUrl + "admin/setting/saveSettings"; // Adjust the path as needed

            // Send AJAX request to save settings
            $.ajax({
                type: "POST",
                url: backendUrl,
                data: formData,
                dataType: "json",
                success: function (response) {
                    // Handle success response from the server
                   // Inside the success block of your AJAX request
                    if (response.success) {
                        // Display a success message
                        $("#message").html("Settings saved successfully").addClass("success");
                    } else {
                        // Display an error message
                        $("#message").html("Failed to save settings: " + response.message).addClass("error");
                    }

                },
                error: function (error) {
                    // Handle error response from the server
                    $("#message").html("Error saving settings: " + error);
                }
            });
        });
    });
</script>
<script>
   // JavaScript Event Listener for Edit Button
    document.querySelectorAll('.edit-button').forEach(function (button) {
        button.addEventListener('click', function (event) {
            event.preventDefault(); // Prevent the default form submission
    
            // Access the parameters from the data- attributes
            var id = this.getAttribute('data-id');
            var variableName = this.getAttribute('data-variable-name');
            var value = this.getAttribute('data-value');
            var sendingLimit = this.getAttribute('data-sending-limit');
            var cronJobStatus = this.getAttribute('data-cron-job-status');
    
            // Set modal input fields with the parameters
            document.querySelector('#edit_holiday input[name="variable_name"]').value = variableName;
            document.querySelector('#edit_holiday input[name="value"]').value = value;
            document.querySelector('#edit_holiday input[name="sending_limit"]').value = sendingLimit;
            document.querySelector('#edit_holiday input[name="cron_job_status"]').checked = cronJobStatus === 'on';
    
            // Store the 'id' in a data attribute of the "Save" button
            document.querySelector('#myButton').setAttribute('data-id', id);
    
            // Show the modal
            $('#edit_holiday').modal('show');
        });
    });

    // Add a click event listener to the "Save" button outside the forEach loop
    document.querySelector('#myButton').addEventListener('click', function (event) {
        event.preventDefault();
        
        // Get the 'id' from the data attribute of the "Save" button
        var id = this.getAttribute('data-id');
        
        // Call updateSetting() function and pass 'id' as a parameter
        updateSetting(id);
    });



    // Modify your updateSetting() function to accept the 'id' parameter
   function updateSetting(id) {
    // Disable the "Save" button to prevent multiple clicks
    $('#myButton').prop('disabled', true);

    // Show a loading indicator (optional)
    $('#myButton').html('Saving...');

    // Collect data from the modal input fields and include 'id' in the data object
    var variableName = document.querySelector('#edit_holiday input[name="variable_name"]').value;
    var value = document.querySelector('#edit_holiday input[name="value"]').value;
    var sendingLimit = document.querySelector('#edit_holiday input[name="sending_limit"]').value;
    var cronJobStatus = document.querySelector('#edit_holiday #cronJobCheckbox').checked ? 'on' : 'off';

    // Create an object to hold the data including 'id'
    var data = {
        id: id,
        variable_name: variableName,
        value: value,
        sending_limit: sendingLimit,
        cron_job_status: cronJobStatus
    };

    // Send an AJAX request to the server
    $.ajax({
        type: 'POST',
        url: '<?=base_url('admin/setting/updateSetting')?>', // Replace with your controller URL
        data: data,
        dataType: 'json',
        success: function(response) {
            // Re-enable the "Save" button and reset its label
            $('#myButton').prop('disabled', false);
            $('#myButton').html('Save');

            // Handle the server response here
            if (response.status === 'success') {
                // Setting updated successfully, you can handle this case here
                alert('Setting updated successfully.');
                $('#edit_holiday').modal('hide'); // Close the modal if needed
            } else {
                // Error while updating, you can handle this case here
                alert('Error updating setting: ' + response.message);
            }
        },
        error: function() {
            // Re-enable the "Save" button and reset its label
            $('#myButton').prop('disabled', false);
            $('#myButton').html('Save');

            // Handle AJAX error here
            alert('AJAX request failed.');
        }
    });
}

</script>




