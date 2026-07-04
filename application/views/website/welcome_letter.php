<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        @media only screen and (max-width: 600px) {
            .container {
                width: 100%;
                padding: 10px;
            }
        }
        
        body {
            background-color: #f7f7f7;
            font-family: Arial, sans-serif;
            color: #333;
        }
        
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 30px;
            background-color: #ffffff;
            border: 1px solid #dddddd;
            border-radius: 4px;
        }
        
        h1 {
            color: #333;
            font-size: 24px;
            margin-top: 0;
        }
        
        p {
            font-size: 16px;
            line-height: 1.5;
            margin-bottom: 20px;
        }
        
        .signature {
            margin-top: 40px;
            font-style: italic;
            color: #777777;
        }
        
        .logo {
            max-width: 100px;
            height: auto;
            margin-bottom: 20px;
        }
    </style>
</head>
<?=$site_url = SITE_URL;?>
<body>
    <div class="container">
        <img class="logo" src="https://talentsjobs.in/assets/resources/assets/img/logo.png" alt="<?php echo SITE_NAME;?>">
        <h1>Welcome to Our Community!</h1>
        <p>Dear <?=ucfirst($name)?>,</p>
        <p>Thank you for joining our community. We are excited to have you on board! Here at [<?php echo SITE_NAME;?>], we strive to create a welcoming and inclusive environment for all our members.</p>
        <p><?php echo SITE_NAME;?>, a platform bringing colleges, students and companies together, your account has been created. Now it will be easierthan ever to apply to relevant jobs.</p>
        <?=($type=='employer' ? '<p>You Can Post unlimited Free Job Posting & Free Candidate Search & Data Access.</p>' : '')?>
        <p>Login & verify your account at below link: <a <?=($type=='candidate' ? $site_url.'/registration/candidate' : $site_url.'/registration/employer')?> ><?=($type=='candidate' ? $site_url.'/registration/candidate' : $site_url.'/registration/employer')?></a></p>
        <p>If you have any questions or need assistance, please don't hesitate to reach out to our team. We're here to help! <a href="mailto:<?php echo SITE_EMAIL;?>"><?php echo SITE_EMAIL;?></a></p>
        <p>Once again, welcome to [<?php echo SITE_NAME;?>]! We look forward to seeing you thrive and contribute to our vibrant community.</p>
        <p class="signature">Best regards,<br>
        <?php echo SITE_NAME;?><br>
        Best of luck</p>
    </div>
</body>
</html>