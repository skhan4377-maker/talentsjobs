<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Job Application</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            background: #f5f5f5;
            font-family: 'Segoe UI', Arial, sans-serif;
            line-height: 1.4;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            background: #ffffff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 6px rgba(0,0,0,0.06);
        }
        .header {
            background: #4f46e5;
            text-align: center;
            padding: 20px;
        }
        .logo {
            height: 40px;
        }
        .content {
            padding: 25px 20px;
        }
        .status-badge {
            width: 60px;
            height: 60px;
            background: #10b981;
            border-radius: 50%;
            line-height: 60px;
            text-align: center;
            color: white;
            font-size: 26px;
            margin: -30px auto 20px;
        }
        .job-title {
            font-size: 20px;
            color: #1e293b;
            text-align: center;
            margin-bottom: 20px;
        }
        .meta-table {
            width: 100%;
            margin-bottom: 25px;
        }
        .meta-table td {
            width: 50%;
            padding: 8px;
        }
        .meta-cell {
            background: #f8fafc;
            border-radius: 6px;
            padding: 12px;
            text-align: center;
            min-height: 90px;
        }
        .meta-label {
            font-size: 13px;
            color: #64748b;
            margin-bottom: 4px;
        }
        .meta-value {
            font-size: 14px;
            font-weight: 600;
            color: #1e293b;
        }
        .cta-button {
			display: block;
			width: 100%;
			box-sizing: border-box; /* ✅ Fix for mobile overflow */
			background: #4f46e5;
			color: white;
			padding: 13px;
			border-radius: 6px;
			text-align: center;
			font-weight: 500;
			font-size: 14px;
			text-decoration: none;
			margin: 25px 0 0;
		}


        .alert {
            background: #fff1f2;
            border-radius: 6px;
            padding: 16px 20px;
            display: flex;
            gap: 12px;
            margin-top: 25px;
        }
        .alert-icon {
            font-size: 18px;
            color: #dc2626;
        }
        .alert-text {
            font-size: 13px;
            color: #334155;
        }
        .footer {
            background: #f8fafc;
            text-align: center;
            font-size: 12px;
            color: #64748b;
            padding: 18px;
        }
    </style>
</head>
<body>
    <div class="container">

        <!-- Header -->
        <div class="header">
            <img src="<?=base_url('assets/frontend/logo.png')?>" alt="<?=SITE_NAME?>" class="logo">
        </div>

        <!-- Content -->
        <div class="content">

             <!-- Success Icon -->
			<div style="width:40px; height:40px; background:#10b981; border-radius:50%; margin:0 auto 20px; text-align:center; line-height:40px; color:#fff; font-size:20px;">
				!
			</div>


            <!-- Title -->
            <h1 class="job-title">New Application for <?= ucfirst($job_title) ?></h1>

            <!-- Application Details -->
            <table class="meta-table" cellpadding="0" cellspacing="0">
                <tr>
                    <td>
                        <div class="meta-cell">
                            <div class="meta-label">Candidate</div>
                            <div class="meta-value"><?= $candidate_name ?></div>
                        </div>
                    </td>
                    <td>
                        <div class="meta-cell">
                            <div class="meta-label">Position</div>
                            <div class="meta-value"><?= $job_title ?></div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div class="meta-cell">
                            <div class="meta-label">Applied On</div>
                            <div class="meta-value"><?= date('d M Y') ?></div>
                        </div>
                    </td>
                    <td>
                        <div class="meta-cell">
                            <div class="meta-label">App ID</div>
                            <div class="meta-value">#<?= rand(1000, 9999) ?></div>
                        </div>
                    </td>
                </tr>
            </table>

            <!-- CTA -->
            <a href="<?= $review_application_link ?>" class="cta-button">Review Application</a>

            <!-- Alert -->
            <div class="alert">
                <div class="alert-icon">⚠️</div>
                <div class="alert-text">
                    <strong>Security Notice:</strong><br>
                    Beware of fake job postings. <?= SITE_NAME ?> never asks for payments or sensitive information.
                </div>
            </div>

        </div>

        <!-- Footer -->
        <div class="footer">
            © <?= date('Y') ?> <?= SITE_NAME ?>. All rights reserved.
        </div>

    </div>
</body>
</html>
