<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Application Status Update</title>
  <style>
    @media only screen and (max-width: 600px) {
      .mobile-full {
        width: 100% !important;
      }
      .stack {
        display: block !important;
        width: 100% !important;
      }
    }
  </style>
</head>
<body style="margin:0; padding:0; background:#f3f4f6; font-family: Arial, Helvetica, sans-serif;">
  <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f3f4f6; padding:20px 0;">
    <tr>
      <td align="center">
        <!-- Main container with max-width for Gmail -->
        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:640px; background:#fff; border-radius:8px; box-shadow:0 1px 6px rgba(0,0,0,0.05);">
          
          <!-- Header -->
          <tr>
            <td align="center" style="background:#4f46e5; padding:20px; color:#fff;">
              <!-- Logo with explicit dimensions for Gmail -->
              <img src="<?= base_url('assets/frontend/logo.png') ?>" alt="Logo" width="180" height="36" style="display:block; margin:0 auto 8px; border:0; outline:none; text-decoration:none;">
              <h1 style="font-size:18px; margin:8px 0 4px; font-weight:bold;">Application Status Update</h1>
              <p style="font-size:13px; opacity:0.9; margin:0;">Your career journey is important to us</p>
            </td>
          </tr>

          <!-- Greeting + Status Badge -->
          <tr>
            <td style="padding:20px;">
              <p style="font-size:15px; margin:0 0 10px; line-height:1.5;">
                Hi <strong style="color:#4f46e5;"><?= htmlspecialchars($candidate_name) ?></strong>,
              </p>

              <p style="text-align:center; margin-bottom:20px;">
                <span style="display:inline-block; background:#10b981; color:#fff; padding:8px 20px; border-radius:20px; font-weight:600; font-size:14px;">
                  <?= htmlspecialchars($status) ?>
                </span>
              </p>

              <!-- Message from employer -->
              <table role="presentation" width="100%" cellpadding="12" cellspacing="0" style="background:#f9fafb; border-left:4px solid #4f46e5; border-radius:6px; font-size:13px; color:#4b5563; margin-bottom:20px;">
                <tr>
                  <td>
                    <?= nl2br(htmlspecialchars($message)) ?>
                  </td>
                </tr>
              </table>

              <!-- Application Details Card -->
              <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#fff; border-radius:8px; border:1px solid #e5e7eb; margin-bottom:20px; box-shadow:0 1px 3px rgba(0,0,0,0.05);">
                <tr>
                  <td style="padding:16px;">
                    <strong style="font-size:16px; display:block; padding-bottom:12px; border-bottom:1px solid #e5e7eb; color:#374151;">Application Details</strong>
                    
                    <!-- Job Title and Status side by side -->
                    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin:16px 0;">
                      <tr>
                        <td width="50%" class="stack" style="padding:0 8px 0 0;">
                          <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                            <tr>
                              <td width="36" align="center" style="background:#eef2ff; border-radius:6px; font-size:18px; padding:8px;">💼</td>
                              <td style="padding-left:12px;">
                                <strong style="font-size:12px; color:#6b7280; display:block;">Job Title</strong>
                                <span style="font-size:14px; color:#111827; font-weight:500;"><?= htmlspecialchars($job_title) ?></span>
                              </td>
                            </tr>
                          </table>
                        </td>
                        <td width="50%" class="stack" style="padding:0 0 0 8px;">
                          <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                            <tr>
                              <td width="36" align="center" style="background:#eef2ff; border-radius:6px; font-size:18px; padding:8px;">🛠</td>
                              <td style="padding-left:12px;">
                                <strong style="font-size:12px; color:#6b7280; display:block;">Current Stage</strong>
                                <span style="font-size:14px; color:#111827; font-weight:500;"><?= htmlspecialchars($status) ?></span>
                              </td>
                            </tr>
                          </table>
                        </td>
                      </tr>
                    </table>
                    
                    <!-- Date and Location side by side -->
                    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin:16px 0;">
                      <tr>
                        <td width="50%" class="stack" style="padding:0 8px 0 0;">
                          <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                            <tr>
                              <td width="36" align="center" style="background:#eef2ff; border-radius:6px; font-size:18px; padding:8px;">📅</td>
                              <td style="padding-left:12px;">
                                <strong style="font-size:12px; color:#6b7280; display:block;">Updated On</strong>
                                <span style="font-size:14px; color:#111827; font-weight:500;"><?= date('d M Y') ?></span>
                              </td>
                            </tr>
                          </table>
                        </td>
                        <td width="50%" class="stack" style="padding:0 0 0 8px;">
                          <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                            <tr>
                              <td width="36" align="center" style="background:#eef2ff; border-radius:6px; font-size:18px; padding:8px;">📍</td>
                              <td style="padding-left:12px;">
                                <strong style="font-size:12px; color:#6b7280; display:block;">Location</strong>
                                <span style="font-size:14px; color:#111827; font-weight:500;"><?= htmlspecialchars($job_location) ?></span>
                              </td>
                            </tr>
                          </table>
                        </td>
                      </tr>
                    </table>
                    
                    <!-- Company Information -->
                    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin-top:20px; padding-top:20px; border-top:1px solid #e5e7eb;">
                      <tr>
                        <td align="center">
                          <?php if (!empty($employer_logo) && file_exists(FCPATH . $employer_logo)): ?>
                            <img src="<?= base_url($employer_logo) ?>" alt="<?= htmlspecialchars($company_name) ?>" width="55" height="55" style="border-radius:8px; object-fit:cover; border:1px solid #e5e7eb; padding:4px; background:#f1f5f9; display:block; margin:auto;">
                          <?php else: 
                            $initials = strtoupper(substr(preg_replace("/[^A-Za-z]/", "", $company_name), 0, 2)) ?: "CO"; ?>
                            <div style="width:55px; height:55px; border-radius:8px; background:#2563eb; color:#fff; font-weight:600; font-size:20px; display:flex; align-items:center; justify-content:center; margin:auto;">
                              <?= $initials ?>
                            </div>
                          <?php endif; ?>
                          <div style="font-size:14px; font-weight:600; color:#1f2937; margin-top:8px;"><?= htmlspecialchars($company_name) ?></div>
                        </td>
                      </tr>
                    </table>
                  </td>
                </tr>
              </table>

              <!-- View Application Button -->
              <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin-top:10px;">
                <tr>
                  <td align="center">
                    <a href="<?= base_url('job/myapply?job-id=' . $job_id) ?>" style="background:#4f46e5; color:#fff; text-decoration:none; padding:12px 28px; border-radius:6px; font-weight:600; font-size:14px; display:inline-block; mso-padding-alt:0;">
                      <!--[if mso]>
                      <i style="letter-spacing: 25px; mso-font-width: -100%; mso-text-raise: 30pt;" hidden>&nbsp;</i>
                      <![endif]-->
                      <span style="mso-text-raise: 10pt;">View Your Application</span>
                      <!--[if mso]>
                      <i style="letter-spacing: 25px; mso-font-width: -100%;" hidden>&nbsp;</i>
                      <![endif]-->
                    </a>
                  </td>
                </tr>
              </table>

            </td>
          </tr>

          <!-- Footer -->
          <tr>
            <td align="center" style="background:#1f2937; color:#cbd5e1; padding:15px; font-size:12px;">
              <p style="margin:0 0 6px;">Need help? Contact us at <br>
                <strong style="color:#ffffff;"><?= htmlspecialchars(SITE_EMAIL) ?></strong>
              </p>
              <p style="margin:0;">
                &copy; <?= date('Y') ?> <?= htmlspecialchars(SITE_NAME) ?>. All rights reserved.<br>
                <a href="#" style="color:#60a5fa; text-decoration:none;">Update Preferences</a> |
                <a href="#" style="color:#60a5fa; text-decoration:none;">Unsubscribe</a>
              </p>
            </td>
          </tr>

        </table>
      </td>
    </tr>
  </table>
</body>
</html>