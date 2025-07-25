<?php


if (!function_exists('otp_verification_enabled')) {
    function otp_verification_enabled(): bool {
        return config('otp-email-verification.enabled', true);
    }
}
if (!function_exists('otp_view_path')) {
    function otp_view_path(string $view): string {
        return "otp-email-verification::{$view}";
    }
}
if (!function_exists('otp_session_key')) {
    function otp_session_key(string $email): string {
        return 'otp_' . md5($email);
    }
}
