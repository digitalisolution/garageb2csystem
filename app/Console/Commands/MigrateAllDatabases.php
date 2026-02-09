<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class MigrateAllDatabases extends Command
{
    protected $signature = 'migrate:all-sites {--reset : Reset migrations instead of running them} {--site= : Specific site domain to migrate}';
    protected $description = 'Run or reset migrations for all site databases or a specific site dynamically.';
    // php artisan migrate:all-sites --reset --site=example.com
    // php artisan migrate:all-sites --reset
    // php artisan migrate:all-sites 
    // php artisan migrate:all-sites --site="www.example.com"      migration for specifi website or domain 


    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        // Get the reset flag and site option from the command
        $isReset = $this->option('reset');
        $specificSite = $this->option('site');
    
        // Get all site configurations
        $sitesConfigPath = base_path('website-configs/');
        $siteIdentifiers = File::directories($sitesConfigPath);
    
        if (empty($siteIdentifiers)) {
            $this->error("No site configurations found!");
            return;
        }
    
        foreach ($siteIdentifiers as $sitePath) {
            $siteIdentifier = basename($sitePath);
    
            // Skip if specific site is provided and doesn't match current site
            if ($specificSite && $specificSite !== $siteIdentifier) {
                continue;
            }
    
            $databaseConfigPath = "$sitePath/database.php";
    
            if (!file_exists($databaseConfigPath)) {
                $this->error("Skipping $siteIdentifier: No database config file found.");
                Log::warning("Skipping $siteIdentifier: No database config file found.");
                continue;
            }
    
            try {
                // Load site-specific database configuration
                $this->setSiteDatabaseConfig($siteIdentifier, $databaseConfigPath);
    
                // Perform migration or reset based on the flag
                if ($isReset) {
                    $this->info("Resetting migrations for: $siteIdentifier");
                    Artisan::call('migrate:reset', ['--database' => 'mysql', '--force' => true]);
                } else {
                    $this->info("Migrating database for: $siteIdentifier");
                    Artisan::call('migrate', ['--database' => 'mysql', '--force' => true]);
                }
    
                $this->info(Artisan::output());
                Log::info(($isReset ? "Reset" : "Migration") . " successful for site: $siteIdentifier");
    
            } catch (\Exception $e) {
                $this->error(($isReset ? "Reset" : "Migration") . " failed for $siteIdentifier: " . $e->getMessage());
                Log::error(($isReset ? "Reset" : "Migration") . " failed for $siteIdentifier: " . $e->getMessage());
    
                if ($specificSite) {
                    return; // Exit if specific site migration fails
                }
            }
        }
    
        // Check if no matching site was found when specific site was requested
        if ($specificSite && !in_array($specificSite, array_map('basename', $siteIdentifiers))) {
            $this->error("Site '$specificSite' not found in configurations!");
            return;
        }
    
        $this->info("All " . ($isReset ? "resets" : "migrations") . " completed.");
    }

    /**
     * Set the site-specific database configuration.
     *
     * @param string $siteIdentifier
     * @param string $databaseConfigPath
     * @return void
     */
    protected function setSiteDatabaseConfig($siteIdentifier, $databaseConfigPath)
    {
        // Include the site's database config
        $databaseConfig = include($databaseConfigPath);

        if (!isset($databaseConfig['connections']['mysql'])) {
            throw new \Exception("Invalid database config for $siteIdentifier.");
        }

        // Set the dynamic database connection
        Config::set('database.connections.mysql', $databaseConfig['connections']['mysql']);

        // Clear and reconnect
        DB::purge('mysql');
        DB::reconnect('mysql');

        Log::info("Database config set for $siteIdentifier.");
    }
}
