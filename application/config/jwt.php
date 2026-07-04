<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$config['jwt_key']       = getenv('JWT_KEY') ?: 'replace_this_with_strong_secret';
$config['jwt_algorithm'] = 'HS256';
$config['jwt_exp'] = 86400; // 24 hours in seconds
$config['refresh_exp'] = 604800; // 7 days in seconds