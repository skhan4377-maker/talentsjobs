<?php
defined('BASEPATH') OR exit('No direct script access allowed');

function can($permission)
{
    $CI =& get_instance();

    if (!$CI->session->userdata('logged_in')) return false;

    // 👑 SUPER ADMIN FULL ACCESS
    if ($CI->session->userdata('role') === 'super_admin') return true;

    $perms = $CI->session->userdata('permissions') ?? [];

    return in_array($permission, $perms, true);
}
