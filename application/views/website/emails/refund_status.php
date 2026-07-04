<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Refund Status Update - <?= SITE_NAME ?></title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #e0e0e0; border-radius: 10px; }
        .header { text-align: center; margin-bottom: 30px; padding-bottom: 20px; border-bottom: 1px solid #e0e0e0; }
        .header h1 { color: #2563eb; margin: 0; }
        .status-box { padding: 20px; border-radius: 8px; margin-bottom: 30px; }
        .approved { background-color: #d1fae5; color: #065f46; }
        .rejected { background-color: #fee2e2; color: #991b1b; }
        .details { background-color: #f9fafb; padding: 20px; border-radius: 8px; margin-bottom: 30px; }
        .footer { text-align: center; color: #6b7280; font-size: 14px; padding-top: 30px; border-top: 1px solid #e5e7eb; }
        .amount { font-size: 24px; font-weight: bold; margin: 10px 0; }
        .btn { display: inline-block; padding: 10px 20px; background-color: #2563eb; color: white; text-decoration: none; border-radius: 5px; margin-top: 10px; }
        .notes { background-color: #f3f4f6; padding: 15px; border-left: 4px solid #6b7280; margin: 20px 0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><?= SITE_NAME ?></h1>
            <p>Refund Status Update</p>
        </div>
        
        <div class="status-box <?= $status ?>">
            <h2><?= $status == 'approved' ? 'Refund Approved!' : 'Refund Request Update' ?></h2>
        </div>
        
        <div class="details">
            <p>Dear <?= $user['name'] ?>,</p>
            
            <?php if ($status == 'approved'): ?>
            <p>We are pleased to inform you that your refund request has been approved.</p>
            <p>The amount of <span class="amount">₹<?= number_format($amount, 2) ?></span> will be credited to your original payment method within 5-7 business days.</p>
            <?php else: ?>
            <p>We regret to inform you that your refund request for <span class="amount">₹<?= number_format($amount, 2) ?></span> has been reviewed and rejected.</p>
            <?php endif; ?>
            
            <?php if (!empty($notes)): ?>
            <div class="notes">
                <p><strong>Admin Notes:</strong> <?= $notes ?></p>
            </div>
            <?php endif; ?>
            
            <table style="width: 100%; border-collapse: collapse; margin-top: 20px;">
                <tr>
                    <td style="padding: 8px 0; border-bottom: 1px solid #e5e7eb;"><strong>Status:</strong></td>
                    <td style="padding: 8px 0; border-bottom: 1px solid #e5e7eb; text-transform: capitalize;">
                        <?= $status ?>
                    </td>
                </tr>
                <tr>
                    <td style="padding: 8px 0; border-bottom: 1px solid #e5e7eb;"><strong>Amount:</strong></td>
                    <td style="padding: 8px 0; border-bottom: 1px solid #e5e7eb;">₹<?= number_format($amount, 2) ?></td>
                </tr>
                <tr>
                    <td style="padding: 8px 0;"><strong>Date:</strong></td>
                    <td style="padding: 8px 0;"><?= date('d M Y, h:i A') ?></td>
                </tr>
            </table>
            
            <p style="margin-top: 20px;">
                If you have any questions about this refund, please contact our support team.
            </p>
        </div>
        
        <div class="footer">
            <p>Thank you for choosing <?= SITE_NAME ?>.</p>
            <p>© <?= date('Y') ?> <?= SITE_NAME ?>. All rights reserved.</p>
            <p>
                <a href="<?= base_url() ?>" style="color: #2563eb; text-decoration: none;">Visit our website</a> | 
                <a href="<?= base_url('contact') ?>" style="color: #2563eb; text-decoration: none;">Contact Support</a>
            </p>
        </div>
    </div>
</body>
</html>