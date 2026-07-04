<!DOCTYPE html>
<html>
<?php 
    if($response == 1){
        $label = '<label style="color:green;">'.$message.'</label>';
    }else if($response == 2){
        $label = '<label style="color:orange;">'.$message.'</label>';
    }else{
        $label = '<label style="color:red;">Invalid verification token</label>'; 
        $message = "Invalid verification token";
    }
?>
<head>
    <title>Thank You</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
        }
        
        .container {
            max-width: 500px;
            margin: 30px auto;
            background-color: #ffffff;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }
        
        h1 {
            color: #333333;
            text-align: center;
            margin-top: 0;
        }
        
        p {
            color: #666666;
            line-height: 1.5;
            margin-bottom: 20px;
        }
        
        .thank-you-message {
            text-align: center;
            margin-top: 30px;
        }
        
        .logo {
            display: block;
            margin: 0 auto;
            width: 150px;
        }
        
        .footer {
            margin-top: 30px;
            text-align: center;
            color: #999999;
            font-size: 12px;
        }
    </style>
</head>

<body>
  <div class="container">
    <img class="logo" src="<?=base_url('assets/frontend/header/logo.png')?>" alt="Company Logo">
    <?php if ($response == 0) { ?>
        <h1>Verification Error</h1>
        <div class="error-message">
            <p>Apologies, but we encountered an error while verifying your email address.</p>
            <p><?=$label?>. Please ensure you have entered the correct verification token.</p>
            <a href="javascript:history.go(-1)" class="back-link">Go Back</a>
        </div>
    <?php } else { ?>
        <h1>Thank You</h1>
        <div class="thank-you-message">
            <p>Thank you for successfully verifying your email address.</p>
            <p><?=$label?>. Your participation is greatly appreciated.</p>
            <p class="center-text"><a href="javascript:history.go(-1)" class="back-link">Back to Homepage</a></p>
        <?php } ?>
        <div class="footer">
            <p>&copy; <?=date('Y')?> Your Company. All rights reserved.</p>
        </div>
    </div>
    </div>
</body>


</html>
