<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;

class ValidatePluginClient
{
    public function handle(Request $request, Closure $next)
    {
        // Only apply this logic to the /contact route
        if ($request->is('contact')) {
            $host = $request->getHost();
            $domain = Str::replaceFirst('www.', '', $host);

            // Load plugin domains from config/sites.php
            $pluginDomains = Config::get('sites.plugin_domains', []);

            foreach ($pluginDomains as $pluginDomain) {
                if ($domain === $pluginDomain || Str::endsWith($domain, '.' . $pluginDomain)) {
                    // Abort with 404 if the current domain is a plugin domain
                    abort(404);
                }
            }
        }

        return $next($request);
    }
}
