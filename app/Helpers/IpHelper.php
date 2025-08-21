<?php

namespace App\Helpers;

use Illuminate\Http\Request;

class IpHelper
{
    public static function getClientIp(Request $request): ?string
    {
        // Cloudflare (real client IP)
        if ($request->headers->has('CF-Connecting-IP')) {
            $ip = $request->headers->get('CF-Connecting-IP');
            if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
                return $ip;
            }
        }

        // X-Forwarded-For (may contain multiple IPs)
        if ($request->headers->has('X-Forwarded-For')) {
            $ips = explode(',', $request->headers->get('X-Forwarded-For'));
            foreach ($ips as $ip) {
                $ip = trim($ip);
                if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
                    return $ip;
                }
            }
        }

        // Default fallback
        $ip = $request->ip();
        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            return $ip;
        }

        return null;
    }
}
