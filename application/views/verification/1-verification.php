<!DOCTYPE html>
<html>
<head>
    <?php 
        if($response == 1){
            $label = $message;
            $label_response ='Thank you for verifying your email address.';
            $backgroundColor = '#dff0d8';
        }else if($response ==2){
            $label = $message;
            $label_response ='';
             $backgroundColor = 'orange';
        }else{
            $label = $message; 
            $label_response ='';
             $backgroundColor = 'red';
        }
    
    ?>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 500px;
            margin: 0 auto;
            text-align: center;
        }
        .success-message {
            background-color: <?=$backgroundColor?>;
            border: 1px solid #d0e9c6;
            color: #3c763d;
            padding: 10px;
            margin-top: 20px;
        }
    </style>
    
</head>
<body>
    <div class="container">
        <h1>Email Verification</h1>
        <p><?=$label?></p>
        <div class="success-message">
            <p><?=$label_response?></p>
        </div>
    </div>
</body>
</html>