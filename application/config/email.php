<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$config['protocol']  = 'smtp';
$config['smtp_host'] = 'smtp.hostinger.com'; // SMTP सर्वर
$config['smtp_user'] = SITE_EMAIL; // SMTP यूज़रनेम
$config['smtp_pass'] = SECRET_SITE_PASSWORD; // SMTP पासवर्ड
$config['smtp_port'] = 587; // SMTP पोर्ट (465 या 587)
$config['smtp_crypto'] = 'tls'; // 'tls' या 'ssl'
$config['mailtype']  = 'html'; // ईमेल फ़ॉर्मेट ('text' या 'html')
$config['charset']   = 'utf-8';
$config['wordwrap']  = TRUE;
$config['newline']   = "\r\n"; 
