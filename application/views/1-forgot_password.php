<?php $this->load->view('common/inc/top-header');?>
 <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #f4f4f4;
    }

    .forgot-email-container {
      max-width: 400px;
      margin: 0 auto;
      padding: 20px;
      background-color: #fff;
      border-radius: 5px;
      box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }
    h2 {
      text-align: center;
      margin-top: 0;
    }

    .form-group {
      margin-bottom: 20px;
    }

    .radio-group {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 10px;
    }

    .radio-button {
      display: flex;
      align-items: center;
      justify-content: center;
      height: 40px;
      background-color: #f1f1f1;
      border: none;
      border-radius: 5px;
      font-size: 16px;
      cursor: pointer;
      transition: background-color 0.3s, color 0.3s;
    }

    .radio-button input[type="radio"] {
      display: none;
    }

    .radio-button label {
      margin: 0;
      padding: 0;
      cursor: pointer;
    }

    .radio-button.checked {
      background-color: #4caf50;
      color: #fff;
      font-weight: bold;
    }

    .radio-button:hover {
      background-color: #e0e0e0;
    }

    .radio-button:hover.checked {
      background-color: #45a049;
    }

    input[type="text"], input[type="password"], input[type="email"] {
      width: 100%;
      padding: 10px;
      border: 1px solid #ccc;
      border-radius: 5px;
      font-size: 16px;
    }

    .error-message {
      color: red;
      font-size: 14px;
      margin-top: 5px;
    }

    button[type="submit"] {
      display: block;
      width: 100%;
      padding: 10px;
      border: none;
      background-color: #4caf50;
      color: #fff;
      font-size: 16px;
      border-radius: 5px;
      cursor: pointer;
    }

    button[type="submit"]:hover {
      background-color: #45a049;
    }
  </style>	
	
	<!-- Start Navigation -->
    <?php $this->load->view('common/inc/header');?>
	<!-- End Navigation -->
	<div class="clearfix"></div>
    <section class="tab-sec gray">
				<div class="forgot-email-container">
				    <div id="response_message"></div>
                   <div id="forgot_password_container">
                    <h2>Forgot Password</h2>
                   <form id="forgot_password_form">
                    <div class="form-group">
                        <div class="radio-group">
                            <label class="radio-button">
                                <input type="radio" name="user-type" value="candidate">
                                Candidate
                            </label>
                            <label class="radio-button">
                                <input type="radio" name="user-type" value="employer">
                                Employer
                            </label>
                        </div>
                        <div class="error-message user-type" style="display: none;"></div>
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email">
                        <div class="error-message email" style="display: none;"></div>
                    </div>
                    <button type="submit">Reset Password</button>
                </form>
                </div>
                
                <div id="reset_password_container" style="display: none;">
                    <h2>Reset Password</h2>
                    <form id="reset_password_form">
                        <div class="form-group">
                        <div class="radio-group">
                            <label class="radio-button" >
                                <input type="radio" name="user-type" value="candidate" id="user_type_candidate">
                                Candidate
                            </label>
                            <label class="radio-button">
                                <input type="radio" name="user-type" value="employer" id="user_type_employer">
                                Employer
                            </label>
                        </div>
                        <div class="error-message user-type" style="display: none;"></div>
                        </div>
                        <div class="form-group">
                            <label for="otp">OTP</label>
                            <input type="text" id="otp" name="otp">
                            <input type="hidden" id="user-id" name="user-id">
                            <div class="error-message otp" style="display: none;"></div>
                        </div>
                        <div class="form-group">
                            <label for="new_password">New Password</label>
                            <input type="password" id="new_password" name="new_password">
                            <div class="error-message new-password" style="display: none;"></div>
                        </div>
                        <div class="form-group">
                            <label for="confirm_password">Confirm Password</label>
                            <input type="password" id="confirm_password" name="confirm_password">
                            <div class="error-message confirm-password" style="display: none;"></div>
                        </div>
                        <button type="submit">Reset Password</button>
                    </form>
                </div>
                </div>
                
               
			</section>
	<!-- Tab section End -->

    <script>
    $(document).ready(function() {
      $('input[name="user-type"]').change(function() {
        $('.radio-button').removeClass('checked');
        if ($(this).is(':checked')) {
          $(this).closest('.radio-button').addClass('checked');
        }
      });  
      
    });
	
	$(document).ready(function() {
	    var selectedUserType = ""; // Variable to store the selected user type
        $('#forgot_password_form').submit(function(e) {
         e.preventDefault();
        selectedUserType = $('input[name="user-type"]:checked').val();
        var email = $('#email').val();
    
        // Clear previous response messages
        $('#response_message').html('');
        $('.error-message').html('').hide(); // Hide the error messages
        $('.error-message.user-type').hide();

        var isValid = true;

       // Validate user type selection
       if (!selectedUserType) {
            $('.error-message.user-type').html('Please select a user type.').show();
            return; // Abort submission if validation fails
        }
   
        // Validate email
        if (!email) {
            $('.error-message.email').text('Please enter your email.').show();
            isValid = false;
        } else if (!isEmailValid(email)) {
            $('.error-message.email').text('Please enter a valid email address.').show();
            isValid = false;
        }

    if (isValid) {
        // AJAX request
        $.ajax({
            url: '<?php echo base_url("forgot-password/send-reset-link"); ?>',
            type: 'POST',
            data: {
                'user-type': selectedUserType,
                'email': email
            },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    // Display success message
                    $('#response_message').html('<div class="success-message">' + response.message + '</div>');
                    // Reset form
                    $('#forgot_password_form')[0].reset();
                    // Hide the "Forgot Password" form
                    $('#forgot_password_container').hide();
                    // Show the reset password form
                    $('#reset_password_container').show();
                   
                    $('#user_type_' + selectedUserType).prop('checked', true); 
                    $('.radio-button input[name="user-type"][value="' + selectedUserType + '"]').closest('.radio-button').addClass('checked');
                     $('#user-id').val(email);       
                } else if (response.status === 'error') {
                    // Display error message(s)
                    if (response.errors) {
                        $.each(response.errors, function(key, value) {
                            $('#' + key).next('.error-message').html(value).show(); // Show the error message
                        });
                    } else {
                        $('#response_message').html('<div class="error-message">' + response.message + '</div>');
                    }
                }
            },
            error: function(xhr, textStatus, error) {
                console.log(xhr.responseText);
            }
            });
            }
        });

        $('#reset_password_form').submit(function(e) {
                e.preventDefault();

                var otp = $('#otp').val();
                var user_id = $('#user-id').val();
                var newPassword = $('#new_password').val();
                var confirmPassword = $('#confirm_password').val();

                // Clear previous response messages
                $('#response_message').html('');
                $('.error-message').html('').hide(); // Hide the error messages

                var isValid = true;

                // Validate OTP
                if (!otp) {
                    $('.error-message.otp').text('Please enter the OTP.').show();
                    isValid = false;
                }

                // Validate new password
                if (!newPassword) {
                    $('.error-message.new-password').text('Please enter the new password.').show();
                    isValid = false;
                }

                // Validate confirm password
                if (!confirmPassword) {
                    $('.error-message.confirm-password').text('Please confirm the new password.').show();
                    isValid = false;
                } else if (newPassword !== confirmPassword) {
                    $('.error-message.confirm-password').text('Passwords do not match.').show();
                    isValid = false;
                }

                if (isValid) {
                    // AJAX request
                    $.ajax({
                        url: '<?php echo base_url("forgot-password/reset-password"); ?>', // Replace with the actual URL for your back-end logic
                        type: 'POST',
                        data: {
                            'user-type': selectedUserType,
                            'user-id': user_id,
                            'otp': otp,
                            'new_password': newPassword
                        },
                        dataType: 'json',
                        success: function(response) {
                            if (response.status === 'success') {
                                // Display success message
                                $('#response_message').html('<div class="success-message">' + response.message + '</div>');
                                // Reset form
                                $('#reset_password_form')[0].reset();
                                 // Show the reset password form
                                $('#reset_password_container').hide();
                                $('#forgot_password_container').show();
                            } else if (response.status === 'error') {
                                // Display error message(s)
                                if (response.errors) {
                                    $.each(response.errors, function(key, value) {
                                        $('#' + key).next('.error-message').html(value).show(); // Show the error message
                                    });
                                } else {
                                    $('#response_message').html('<div class="error-message">' + response.message + '</div>');
                                }
                            }
                        },
                        error: function(xhr, textStatus, error) {
                            console.log(xhr.responseText);
                        }
                    });
                }
            });
            
    // Email validation function
    function isEmailValid(email) {
        var pattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return pattern.test(email);
        }
    });
  </script>
 <?php $this->load->view('common/inc/footer');?>	