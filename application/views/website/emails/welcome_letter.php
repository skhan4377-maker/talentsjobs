<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="x-apple-disable-message-reformatting">
    <style>
        @media only screen and (max-width: 600px) {
            .container {
                width: 95% !important;
                padding: 20px 15px !important;
            }
            h1 {
                font-size: 28px !important;
            }
            .cta-button {
                padding: 12px 20px !important;
            }
        }

        body {
            margin: 0;
            padding: 20px 0;
            background-color: #f6f9fc;
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 40px;
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.08);
        }

        .logo {
            display: block;
            max-width: 120px;
            height: auto;
            margin: 0 auto 30px;
        }

        h1 {
            color: #1a237e;
            font-size: 32px;
            margin: 0 0 25px;
            text-align: center;
            font-weight: 700;
            letter-spacing: -0.5px;
        }

        .content {
            line-height: 1.6;
            color: #4a5568;
            font-size: 16px;
        }

        .cta-button {
            display: inline-block;
            margin: 25px 0;
            padding: 15px 30px;
            background: #3f51b5;
            color: #ffffff !important;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(63,81,181,0.25);
        }

        .cta-button:hover {
            background: #303f9f !important;
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(63,81,181,0.3);
        }

        .highlight {
            color: #3f51b5;
            font-weight: 600;
        }

        .signature {
            margin-top: 40px;
            padding-top: 30px;
            border-top: 1px solid #e2e8f0;
            color: #718096;
            font-size: 14px;
        }

        .footer {
            text-align: center;
            margin-top: 35px;
            color: #a0aec0;
            font-size: 13px;
        }
    </style>
</head>
<body>
    <div class="container">
        <img class="logo" src="<?=base_url('assets/frontend/logo.png')?>" alt="<?php echo SITE_NAME;?>">
        
        <h1>Welcome to <?php echo SITE_NAME;?> 👋</h1>

        <div class="content">
            <p>Hi <span class="highlight"><?=ucfirst($name)?></span>,</p>

            <p>We're excited to have you with us at <strong><?php echo SITE_NAME;?></strong>! Let’s get started on your journey to new opportunities.</p>

            <?php if($type == 'employer'): ?>
                <p>Here’s what you can do right away:</p>
                <ul>
                    <li>Post jobs for free</li>
                    <li>Search and connect with top talent</li>
                    <li>Track your performance in real time</li>
                </ul>
            <?php else: ?>
                <p>Discover job openings, apply instantly, and build your professional profile with ease.</p>
            <?php endif; ?>

            <p>To unlock full access, please verify your account:</p>
            
            <center>
                <a href="<?= $verification_link ?>" class="cta-button">
                    <?= ($type == 'candidate' ? 'Verify My Profile' : 'Activate Employer Account') ?>
                </a>
            </center>

            <p>Need help? Our support team is always ready to assist you at 
                <a href="mailto:<?php echo SITE_EMAIL;?>" style="color: #3f51b5; text-decoration: underline;"><?php echo SITE_EMAIL;?></a>.
            </p>
        </div>

        <div class="signature">
            Warm regards,<br>
            <strong>The <?php echo SITE_NAME;?> Team</strong><br>
            <span style="color: #718096; font-size: 14px;">Empowering Careers, Connecting Talent</span>
        </div>

        <div class="footer">
            © <?= date('Y') ?> <?php echo SITE_NAME;?>. All rights reserved.<br>
            <a href="#" style="color: #718096; text-decoration: none;">Privacy Policy</a> | 
            <a href="#" style="color: #718096; text-decoration: none;">Terms of Service</a>
        </div>
    </div>
</body>
</html>
