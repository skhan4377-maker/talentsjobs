<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Email Verification</title>
  <style>
        @media only screen and (max-width: 600px) {
            .container {
                width: 100% !important;
            }
        }

        body {
            font-family: Arial, sans-serif;
            background-color: #f1f1f1;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 600px;
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
        }

        .verification-link {
            display: inline-block;
            background-color: #4caf50;
            color: #ffffff;
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 4px;
            margin-top: 20px;
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
      <h1>Email Verification</h1>
      <?php if ($user_type === 'employer'): ?>
        <p>Dear <?= $user_name ?>,</p>
      <?php elseif ($user_type === 'candidate'): ?>
        <p>Dear <?= $user_name ?>,</p>
      <?php endif; ?>
      <!-- Rest of the template -->
    <p>Thank you for signing up! To complete your registration, please click the verification link below:</p>
    <div style="text-align: center;">
       <a class="verification-link" href="<?= base_url('verification/verify_email/'.$user_type.'/'.$token) ?>">Verify Email</a>
    </div>
    <p>If you did not register with us, please ignore this email.</p>
    <p>Best regards,<br>[<?=SITE_NAME;?>]</p>
    <div class="footer">
      This email was sent from <?=SITE_EMAIL?>. Please do not reply to this email.
    </div>
  </div>
</body>
</html>
