<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Application Confirmation</title>
</head>
<body style="margin:0;padding:0;background:#f3f4f6;font-family:'Segoe UI',Arial,sans-serif;">

<!-- Wrapper -->
<table width="100%" cellpadding="0" cellspacing="0" border="0" style="background:#f3f4f6;padding:10px 0;">
  <tr>
    <td align="center">

      <!-- Main Container -->
      <table cellpadding="0" cellspacing="0" border="0" width="100%" style="max-width:600px;width:100%!important;background:#ffffff;border:1px solid #e5e7eb;border-radius:10px;overflow:hidden;table-layout:fixed;">
        
        <!-- Header -->
        <tr>
          <td style="background:#4f46e5;padding:20px;text-align:center;">
            <img src="<?=base_url('assets/frontend/logo.png')?>" alt="<?=SITE_NAME?>" style="height:40px;max-width:160px;">
          </td>
        </tr>

        <!-- Success -->
        <tr>
          <td align="center" style="padding:30px 16px 16px;">
            <div style="width:56px;height:56px;background:#10b981;border-radius:50%;text-align:center;line-height:56px;font-size:28px;color:#fff;">✓</div>
            <h1 style="font-size:20px;color:#1e293b;margin:16px 0 8px;">Application Submitted Successfully!</h1>
            <p style="color:#64748b;font-size:14px;margin:0;">Thank you for applying. Here are your application details:</p>
          </td>
        </tr>

        <!-- Details -->
        <tr>
          <td style="padding:20px;">
            <table width="100%" cellpadding="0" cellspacing="0" border="0" style="table-layout:fixed;font-size:14px;color:#1e293b;">
              <tr>
                <td width="35%" style="padding:8px 0;color:#64748b;">Position:</td>
                <td style="padding:8px 0;font-weight:600;word-break:break-word;"><?=ucfirst($job_title)?></td>
              </tr>
              <tr>
                <td width="35%" style="padding:8px 0;color:#64748b;">Company:</td>
                <td style="padding:8px 0;font-weight:600;word-break:break-word;"><?=htmlspecialchars($company_name)?></td>
              </tr>
              <tr>
                <td width="35%" style="padding:8px 0;color:#64748b;">Applied On:</td>
                <td style="padding:8px 0;font-weight:600;"><?=date('d M Y')?></td>
              </tr>
            </table>

            <div style="text-align:center;margin-top:20px;">
              <a href="<?= base_url('job/myapply?job-id='.$job_id) ?>" style="display:inline-block;padding:12px 24px;background:#4f46e5;color:#fff;text-decoration:none;border-radius:6px;font-weight:600;font-size:14px;">Track Application</a>
            </div>
          </td>
        </tr>

        <!-- Recommended Jobs -->
     
		<tr>
		  <td style="padding:20px;border-top:1px solid #e5e7eb;">
			<h2 style="font-size:15px;color:#1e293b;margin-bottom:16px;">Recommended Opportunities</h2>

			<?php foreach($matched_job as $row): 
			  $jobSlug = $row['slug'];
			  $cities = !empty($row['job_locations']) ? explode(', ', $row['job_locations']) : [];
			  $logoPath = !empty($row['logo']) ? base_url($row['logo']) : '';
			  $initial = strtoupper(substr(trim($row['company_name']), 0, 1));
			?>
			<table width="100%" cellpadding="0" cellspacing="0" border="0" style="border:1px solid #e2e8f0;border-radius:8px;margin-bottom:14px;table-layout:fixed;">
			  <tr>
				<td width="60" style="padding:12px;vertical-align:top;text-align:center;">
				  <?php if($logoPath): ?>
					<img src="<?=$logoPath?>" alt="<?=htmlspecialchars($row['company_name'])?>" style="width:52px;height:52px;object-fit:contain;border-radius:6px;border:1px solid #e5e7eb;">
				  <?php else: ?>
					<div style="width:52px;height:52px;background:#2563eb;border-radius:6px;line-height:52px;color:#fff;font-size:18px;font-weight:700;"><?=$initial?></div>
				  <?php endif; ?>
				</td>
				<td style="padding:12px;vertical-align:top;">
				  <div style="color:#4f46e5;font-weight:600;font-size:14px;word-break:break-word;line-height:1.4;">
					<?=ucfirst($row['job_title'])?>
				  </div>
				  <div style="font-size:13px;color:#475569;line-height:1.3;margin-top:2px;">
					🏢 <?=htmlspecialchars($row['company_name'])?>
				  </div>
				  <div style="font-size:13px;color:#475569;line-height:1.3;">
					📍 <?=!empty($cities) ? implode(', ', array_map('ucfirst', array_map('trim', $cities))) : 'Multiple Locations'?>
				  </div>
				  <div style="font-size:13px;color:#475569;line-height:1.3;">
					💰 ₹<?=$row['min_salary']?> - ₹<?=$row['max_salary']?>
				  </div>
				  <a href="<?=site_url($jobSlug)?>" style="display:inline-block;margin-top:8px;padding:8px 14px;background:#10b981;color:#fff;text-decoration:none;border-radius:5px;font-size:13px;">Quick Apply</a>
				</td>
			  </tr>
			</table>
			<?php endforeach; ?>
		  </td>
		</tr>

        <!-- Security -->
        <tr>
          <td style="background:#fff7ed;border-top:1px solid #fde68a;padding:14px 20px;">
            <strong style="color:#b45309;font-size:13px;">⚠️ Security Notice:</strong>
            <div style="color:#64748b;font-size:13px;">Beware of fake job offers. <?=SITE_NAME?> never asks for payments or sensitive info.</div>
          </td>
        </tr>

        <!-- Footer -->
        <tr>
          <td style="background:#f8fafc;padding:16px;text-align:center;color:#94a3b8;font-size:12px;">
            © <?=date('Y')?> <?=SITE_NAME?>. All rights reserved.<br>
            <span style="display:block;margin-top:4px;">Made with ❤️ in India</span>
          </td>
        </tr>
      </table>
    </td>
  </tr>
</table>
</body>
</html>
