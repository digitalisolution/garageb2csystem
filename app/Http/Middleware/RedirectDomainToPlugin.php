<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;

class RedirectDomainToPlugin
{
   public function handle(Request $request, Closure $next)
    {
        // Only apply on root path
        if (!$request->is('/')) {
            return $next($request);
        }

        // Get current domain
        $host = $request->getHost();
        $domain = Str::replaceFirst('www.', '', $host);

        // List of base domains that should be redirected
        $pluginDomains = Config::get('sites.plugin_domains', []);

        foreach ($pluginDomains as $pluginDomain) {
            if ($domain === $pluginDomain || Str::endsWith($domain, '.' . $pluginDomain)) {
                return redirect()->route('plugin.search.form');
            }
        }

        return $next($request);
    }
}