<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class SetSiteDatabase
{
    public function handle(Request $request, Closure $next)
    {
        // Get the current domain to load site-specific config
        $siteIdentifier = $request->getHost();
        $siteConfigPath = base_path("website-configs/$siteIdentifier/database.php");

        // Check if the site-specific database config file exists
        if (file_exists($siteConfigPath)) {
            // Log::info("Loaded database configurations from site-specific config for $siteIdentifier");

            // Dynamically update the database configuration
            $this->setDatabaseConfigurations($siteConfigPath);
        } else {
            // Log::warning("No site-specific database config found for $siteIdentifier");
        }

        return $next($request);
    }

    protected function setDatabaseConfigurations($siteConfigPath)
    {
        // Load the site-specific database config
        $domainDatabaseConfig = include($siteConfigPath);

        // Log::info("Loaded database configuration for $siteConfigPath", $domainDatabaseConfig);

        // Set the database configuration dynamically based on the configuration file
        Config::set('database.connections.mysql', $domainDatabaseConfig['connections']['mysql']);

        // Force Laravel to reload the database configuration
        DB::purge('mysql');
        DB::reconnect('mysql');

        // Log::info("Dynamic database configuration set for MySQL connection");
    }
}
