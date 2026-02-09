<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

class ThrottleOtpRequests
{
    public function handle(Request $request, Closure $next)
    {
        $emailKey = 'otp_send_email|' . strtolower($request->input('email'));
        $ipKey = 'otp_send_ip|' . $request->ip();

        // Check email limit
        if (RateLimiter::tooManyAttempts($emailKey, 3)) {
            return response()->json(['message' => 'Too many OTP requests for this email. Please try again later.'], 429);
        }

        // Check IP limit
        if (RateLimiter::tooManyAttempts($ipKey, 5)) {
            return response()->json(['message' => 'Too many OTP requests from this IP. Please try again later.'], 429);
        }

        // Record attempts for both keys
        RateLimiter::hit($emailKey, 86400); // 24 hours in seconds
        RateLimiter::hit($ipKey, 86400);

        return $next($request);
    }
}
