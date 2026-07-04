<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Invoice #<?= htmlspecialchars($data['invoiceNumber']) ?></title>
</head>
<body style="margin:0; padding:20px; background-color:#f4f7fa; font-family:'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; color:#333; line-height:1.6;">
  <div style="max-width:600px; margin:auto; background:#fff; border-radius:10px; overflow:hidden; box-shadow:0 4px 8px rgba(0,0,0,0.1);">
    
    <!-- Header -->
    <div style="background:#4f46e5; color:#fff; text-align:center; padding:25px;">
      <h1 style="margin:0; font-size:24px; font-weight:600;">Invoice #<?= htmlspecialchars($data['invoiceNumber']) ?></h1>
      <p style="margin:5px 0 0; font-size:14px; opacity:0.9;">Issued: <?= htmlspecialchars($data['invoiceDate']) ?></p>
    </div>

    <!-- Body -->
    <div style="padding:25px;">
      
      <!-- Table -->
      <table width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse; margin-bottom:25px; font-size:14px;">
        <thead>
          <tr style="background:#f8fafc;">
            <th align="left" style="padding:12px 15px; text-transform:uppercase; font-size:12px; color:#555;">Plan</th>
            <th align="left" style="padding:12px 15px; text-transform:uppercase; font-size:12px; color:#555;">Level</th>
            <th align="right" style="padding:12px 15px; text-transform:uppercase; font-size:12px; color:#555;">Price</th>
            <th align="right" style="padding:12px 15px; text-transform:uppercase; font-size:12px; color:#555;">Total</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($data['purchasedPlans'] as $plan): ?>
          <tr style="border-bottom:1px solid #e2e8f0;">
            <td style="padding:12px 15px;"><?= htmlspecialchars($plan['feature_name']) ?></td>
            <td style="padding:12px 15px;"><?= !empty($plan['plan_level']) ? $plan['plan_level'] : $plan['experience_level'] ?></td>
            <td align="right" style="padding:12px 15px;">
              ₹<?= number_format($plan['plan_mrp'], 0) ?>
              <div style="font-size:12px; color:#64748b;"><?= number_format($plan['plan_discount'], 0) ?>% off</div>
            </td>
            <td align="right" style="padding:12px 15px; font-weight:600; color:#4f46e5;">₹<?= number_format($plan['plan_total'], 0) ?></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>

      <!-- Summary (Email-safe table layout) -->
      <table width="100%" cellpadding="0" cellspacing="0" style="background:#f8fafc; border-radius:8px; font-size:14px; margin-bottom:20px;">
        <tr>
          <td style="padding:12px 15px; color:#555;">Subtotal</td>
          <td align="right" style="padding:12px 15px; font-weight:600;">₹<?= number_format($data['totalMrp'], 0) ?></td>
        </tr>
        <tr>
          <td style="padding:12px 15px; color:#10b981;">Discount</td>
          <td align="right" style="padding:12px 15px; color:#10b981; font-weight:600;">- ₹<?= number_format($data['totalDiscount'], 0) ?></td>
        </tr>
        <tr>
          <td style="padding:12px 15px; color:#555;">Tax (<?= number_format($data['taxPercentage'] * 100, 0) ?>%)</td>
          <td align="right" style="padding:12px 15px; font-weight:600;">₹<?= number_format($data['totalTax'], 0) ?></td>
        </tr>
        <tr>
          <td style="padding:15px 15px; border-top:2px solid #e2e8f0; color:#333;">Grand Total</td>
          <td align="right" style="padding:15px 15px; border-top:2px solid #e2e8f0; font-weight:700; color:#4f46e5;">₹<?= number_format($data['grandTotal'], 0) ?></td>
        </tr>
      </table>
    </div>

    <!-- Footer -->
    <div style="padding:25px; border-top:1px solid #e2e8f0; font-size:12px; color:#555;">
      <h4 style="margin:0 0 10px; font-weight:600; color:#333;">Important Notes:</h4>
      <ul style="padding-left:20px; margin:0;">
        <li style="margin-bottom:8px;">This is a computer generated invoice.</li>
        <li style="margin-bottom:8px;">Valid for tax purposes under GST.</li>
        <li style="margin-bottom:8px;">Contact <a href="mailto:info@talentsjobs.in" style="color:#4f46e5; text-decoration:none;">info@talentsjobs.in</a> for queries.</li>
      </ul>
    </div>
  </div>
</body>
</html>
