<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Interview Invitation - Talents Job</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body style="margin:0; padding:0; background:#f4f6f8; font-family:'Segoe UI', sans-serif;">

  <table width="100%" cellpadding="0" cellspacing="0" border="0" style="padding: 12px;">
    <tr>
      <td align="center">

        <!-- Container -->
        <div style="max-width:600px; width:100%; background:#fff; border-radius:12px; box-shadow:0 4px 15px rgba(0,0,0,0.06); overflow:hidden;">

          <!-- Header -->
          <div style="background:#4f46e5; padding:14px 20px; text-align:center;">
            <img src="<?= base_url('assets/frontend/logo.png') ?>" alt="Talents Job" style="height:40px;">
          </div>

          <!-- Body -->
          <div style="padding:20px 24px;">
            <h2 style="color:#1f2937; font-size:20px; margin-bottom:12px;">👋 Hello <?= htmlspecialchars($candidate_name) ?>,</h2>

            <p style="font-size:15px; color:#374151; margin:10px 0;">
              <?= $is_edit ? 'Your interview has been updated' : 'You have been invited to an interview' ?> with
              <strong><?= htmlspecialchars($company_name) ?></strong> for the role of
              <strong><?= htmlspecialchars($job_title) ?></strong>.
            </p>

            <!-- Interview Details Box -->         
			<div style="margin:18px 0; padding:16px; background:#f9fafb; border:1px solid #e5e7eb; border-radius:10px;">
			  
			  <p style="font-size:14px; color:#111827; margin:6px 0;">
				<span>📅 <strong>Date:</strong> <?= $interview_date ?></span>
			  </p>

			  <p style="font-size:14px; color:#111827; margin:6px 0;">
				<span>⏰ <strong>Time:</strong> <?= $interview_time ?></span>
			  </p>

			  <p style="font-size:14px; color:#111827; margin:6px 0;">
				<span>💼 <strong>Mode:</strong> <?= htmlspecialchars($interview_type) ?></span>
			  </p>

			  <?php if (!empty($interview_link)): ?>
			  <p style="font-size:14px; color:#111827; margin:10px 0;">
				🔗 <strong>Interview Link:</strong><br>
				<a href="<?= htmlspecialchars($interview_link) ?>" target="_blank"
				   style="color:#4f46e5; text-decoration:underline; word-break:break-word;">
				  <?= htmlspecialchars($interview_link) ?>
				</a>
			  </p>
			  <?php endif; ?>

			  <?php if (!empty($interview_notes)): ?>
			  <div style="margin-top:16px; padding:12px 14px; background:#fffbe6; border:1px solid #fde68a; border-radius:8px;">
				<p style="font-size:14px; color:#92400e; margin:0;">
				  📝 <strong>Notes:</strong><br>
				  <?= nl2br(htmlspecialchars($interview_notes)) ?>
				</p>
			  </div>
			  <?php endif; ?>

			</div>


            <p style="font-size:14px; color:#4b5563; margin-top:20px;">
              We look forward to speaking with you.<br><br>
              Best regards,<br>
              <strong><?= htmlspecialchars($company_name) ?> Team</strong>
            </p>
          </div>

          <!-- Footer -->
          <div style="background:#f1f5f9; text-align:center; padding:10px; font-size:12px; color:#6b7280;">
            © <?= date('Y') ?> Talents Job. All rights reserved.
          </div>

        </div>
        <!-- /Container -->

      </td>
    </tr>
  </table>

</body>
</html>
