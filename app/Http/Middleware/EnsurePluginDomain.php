<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;

class EnsurePluginDomain
{
    public function handle(Request $request, Closure $next)
    {
        // Get current domain
        $host = Str::replaceFirst('www.', '', $request->getHost());

        // Get list of allowed domains
        $allowedDomains = Config::get('sites.plugin_domains', []);

        foreach ($allowedDomains as $domain) {
            if (
                $host === $domain ||
                Str::endsWith($host, '.' . $domain)
            ) {
                return $next($request);
            }
        }

        // Block access
        abort(404, 'Access denied');
    }
}