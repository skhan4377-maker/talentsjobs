<!DOCTYPE html>
<html>
<head>
  <title>Password Reset OTP</title>
 <style>
     /* Container styles */
.container {
  max-width: 100%;
  margin: 0 auto;
  padding: 20px;
  font-family: Arial, sans-serif;
  background-color: #f2f2f2;
}

/* Header styles */
.header {
  text-align: center;
  margin-bottom: 20px;
}

.logo {
  max-width: 150px;
}

/* Content styles */
.content {
  background-color: #fff;
  padding: 20px;
  border-radius: 5px;
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
}

h2 {
  margin-top: 0;
  font-size: 24px;
  text-align: center;
}

.otp {
  font-size: 18px;
  font-weight: bold;
  padding: 10px;
  background-color: #f2f2f2;
  border-radius: 5px;
  text-align: center;
}

/* Footer styles */
.footer {
  text-align: center;
  margin-top: 20px;
  font-size: 14px;
  color: #888;
}
  </style>
</head>
<body>
  <div class="container">
    <div class="header">
      <img src="<?=base_url('assets/frontend/header/logo.png')?>" alt="Company Logo" class="logo">
    </div>
    <div class="content">
      <h2>Hello <?=strtoupper($name)?>,</h2>
      <p>Please use the following One-Time Password (OTP) to reset your password:</p>
      <p class="otp"> <?=$otp?></p>
      <p>This OTP will expire in 10 minutes. If you didn't request a password reset, you can safely ignore this email.</p>
    </div>
    <div class="footer">
      <p>&copy; <?php echo date('Y'); ?> <?php echo SITE_NAME;?>. All rights reserved.</p>
    </div>
  </div>
</body>
</html>
