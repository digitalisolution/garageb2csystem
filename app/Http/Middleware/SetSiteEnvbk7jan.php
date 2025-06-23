<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class SetSiteEnv
{
    public function handle(Request $request, Closure $next)
    {
        // Log::info("Executing SetSiteEnv middleware");

        // Get the current domain to load site-specific config
        $siteIdentifier = $request->getHost(); // Example: garage-automation.com

        // Set the path for domain-specific config files
        $siteConfigPath = base_path("website-configs/$siteIdentifier");

        // Load site-specific config files if they exist
        if (is_dir($siteConfigPath)) {
            // Load config.php
            $configFilePath = "$siteConfigPath/config.php";
            if (file_exists($configFilePath)) {
                $config = include($configFilePath);
                foreach ($config as $key => $value) {
                    Config::set($key, $value);
                }
                // Log::info("Loaded site-specific config for $siteIdentifier");
            } else {
                Log::warning("Config file not found for $siteIdentifier: $configFilePath");
            }

            // Load database.php
            $dbConfigFilePath = "$siteConfigPath/database.php";
            if (file_exists($dbConfigFilePath)) {
                $databaseConfig = include($dbConfigFilePath);
                Config::set('database.connections.mysql', $databaseConfig['connections']['mysql']);
                // Log::info("Loaded site-specific database config for $siteIdentifier");
            } else {
                Log::warning("Database config file not found for $siteIdentifier: $dbConfigFilePath");
            }

            // Load session.php
            $sessionConfigFilePath = "$siteConfigPath/session.php";
            if (file_exists($sessionConfigFilePath)) {
                $sessionConfig = include($sessionConfigFilePath);
                Config::set('session', $sessionConfig);
                // Log::info("Loaded site-specific session config for $siteIdentifier");
            } else {
                Log::warning("Session config file not found for $siteIdentifier: $sessionConfigFilePath");
            }
        } else {
            Log::warning("Site config directory not found: $siteConfigPath");
        }

        // Set global configuration settings if necessary (for things like asset URLs)
        // Example: Assets for the site
        $assetsPath = public_path('assets/' . $siteIdentifier);
        if (is_dir($assetsPath)) {
            Config::set('app.asset_url', url('assets/' . $siteIdentifier));
        }
        // Log::info('Authenticated User in Global Middleware:', ['user' => auth()->user()]);
        // dd(auth()->user());
        return $next($request);
    }
}
