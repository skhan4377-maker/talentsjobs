<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * SECURITY HEADERS HOOK
 */
$hook['post_controller_constructor'][] = array(
    'class'    => 'SecurityHeaders',
    'function' => 'setHeaders',
    'filename' => 'SecurityHeaders.php',
    'filepath' => 'hooks'
);
