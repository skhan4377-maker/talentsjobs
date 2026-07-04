<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>New Employer Registration</title>
</head>
<body style="margin:0; padding:0; font-family: Arial, sans-serif; line-height: 1.4; color: #333; background: #f5f5f5;">
    <div style="max-width: 600px; margin: 0 auto; background: white;">
        <!-- Header -->
        <div style="background: #2563eb; color: white; padding: 12px 20px; border-bottom: 3px solid #1d4ed8;">
            <h1 style="margin:0; font-size: 18px; font-weight: bold;">New Employer Registration</h1>
            <p style="margin:4px 0 0 0; font-size: 12px; opacity: 0.9;"><?= $site_name ?></p>
        </div>
        
        <!-- Content -->
        <div style="padding: 16px 20px;">
            <h2 style="margin:0 0 12px 0; font-size: 16px; color: #1f2937;">New employer registered on platform</h2>
            
            <!-- Details Table -->
            <table style="width:100%; border-collapse: collapse; background: #f8fafc; border: 1px solid #e2e8f0; font-size: 13px;">
                <tr>
                    <td colspan="2" style="padding:8px 12px; background: #1e40af; color: white; font-weight: bold; font-size: 14px;">
                        Employer Details
                    </td>
                </tr>
                <tr>
                    <td style="padding:6px 12px; border-bottom:1px solid #e2e8f0; width:35%; font-weight:bold;">Name:</td>
                    <td style="padding:6px 12px; border-bottom:1px solid #e2e8f0;"><?= htmlspecialchars($employer_name) ?></td>
                </tr>
                <tr>
                    <td style="padding:6px 12px; border-bottom:1px solid #e2e8f0; font-weight:bold;">Email:</td>
                    <td style="padding:6px 12px; border-bottom:1px solid #e2e8f0;"><?= htmlspecialchars($employer_email) ?></td>
                </tr>
                <tr>
                    <td style="padding:6px 12px; border-bottom:1px solid #e2e8f0; font-weight:bold;">Mobile:</td>
                    <td style="padding:6px 12px; border-bottom:1px solid #e2e8f0;"><?= htmlspecialchars($employer_mobile) ?></td>
                </tr>
                <tr>
                    <td style="padding:6px 12px; border-bottom:1px solid #e2e8f0; font-weight:bold;">Company Name:</td>
                    <td style="padding:6px 12px; border-bottom:1px solid #e2e8f0;"><?= htmlspecialchars($company_name) ?></td>
                </tr>
                <tr>
                    <td style="padding:6px 12px; border-bottom:1px solid #e2e8f0; font-weight:bold;">Recruiter Type:</td>
                    <td style="padding:6px 12px; border-bottom:1px solid #e2e8f0;"><?= htmlspecialchars($recruiter_type) ?></td>
                </tr>
                <tr>
                    <td style="padding:6px 12px; border-bottom:1px solid #e2e8f0; font-weight:bold;">Company Type:</td>
                    <td style="padding:6px 12px; border-bottom:1px solid #e2e8f0;"><?= htmlspecialchars($company_type) ?></td>
                </tr>
                <tr>
                    <td style="padding:6px 12px; font-weight:bold;">Registration Date:</td>
                    <td style="padding:6px 12px;"><?= $registration_date ?></td>
                </tr>
            </table>

            <!-- Action Button -->
            <div style="text-align: center; margin: 16px 0 8px 0;">
                <a href="<?= $admin_dashboard_url ?>" style="display: inline-block; padding: 8px 16px; background: #2563eb; color: white; text-decoration: none; border-radius: 4px; font-size: 13px; font-weight: bold;">
                    View in Admin Dashboard
                </a>
            </div>
        </div>
        
        <!-- Footer -->
        <div style="padding: 12px 20px; text-align: center; font-size: 11px; color: #6b7280; border-top: 1px solid #e5e7eb; background: #f9fafb;">
            <p style="margin:0;">This is an automated notification from <?= $site_name ?></p>
            <p style="margin:4px 0 0 0;">© <?= date('Y') ?> <?= $site_name ?>. All rights reserved.</p>
        </div>
    </div>
</body>
</html>