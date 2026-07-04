<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class SecurityHeaders {

    public function setHeaders() 
    {
        header("X-Frame-Options: DENY");
        header("X-XSS-Protection: 1; mode=block");
        header("X-Content-Type-Options: nosniff");
        header("Referrer-Policy: no-referrer-when-downgrade");
        header("Permissions-Policy: geolocation=(), camera=(), microphone=()");

        $csp = "Content-Security-Policy: "
            . "default-src 'self' https: data: blob:; "
            . "img-src 'self' https: data: blob:; "
            . "script-src 'self' https: 'unsafe-inline' 'unsafe-eval'; "
            . "style-src 'self' https: 'unsafe-inline'; "
            . "font-src 'self' https: data:; "
            . "connect-src 'self' https:; "
            . "frame-ancestors 'none'; "
            . "upgrade-insecure-requests;";

        header($csp);
    }
}
