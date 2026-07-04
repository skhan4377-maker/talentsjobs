<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Check Your Email</title>
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

        .highlight {
            color: #4caf50;
            font-weight: bold;
        }

        .note {
            color: #999999;
            font-size: 14px;
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
    <h1>Check Your Email</h1>
    <p><?php echo $message; ?></p>
    <p>If you haven't received the email in your inbox, please also check your <span class="highlight">spam/junk folder</span> as it might have been filtered incorrectly.</p>
    <p class="note">Note: It may take a few moments for the email to arrive. If you haven't received it within 15 minutes, please consider retrying or contact our support team for assistance.</p>
    <div style="text-align: center;">
        <a class="verification-link" href="javascript:history.back()">Return to Homepage</a>
    </div>
    <div class="footer">
        <p>&copy; <?= date('Y') ?> <?php echo SITE_NAME; ?>. All rights reserved.</p>
    </div>
</div>

</body>

</html>
