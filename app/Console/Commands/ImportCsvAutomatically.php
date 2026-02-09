<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Supplier;
use App\Http\Controllers\TyreImportController;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log; // Import the Log facade

class ImportCsvAutomatically extends Command
{
    protected $signature = 'auto:install-tyres';
    protected $description = 'Automatically installs tyres data for all domains';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        // Retrieve the list of all sites from the sites configuration
        $sitesConfig = Config::get('sites.sites');

        // Loop through all the sites
        foreach ($sitesConfig as $siteIdentifier => $siteConfig) {
            // Set the site-specific database configuration
            $this->setSiteDatabaseConfig($siteIdentifier);
            $_SERVER['APP_FAKE_HOST'] = $siteIdentifier;
            // Fetch all suppliers (or use a condition if you want specific ones)
            $suppliers = Supplier::all();

            foreach ($suppliers as $supplier) {
                // Check if the supplier's status is 0 (inactive)
                if ($supplier->status === 0) {
                    // Skip if supplier is inactive
                    $message = "Skipping installation for supplier {$supplier->supplier_name} (inactive) on domain {$siteIdentifier}.";
                    $this->info($message);
                    Log::info($message); // Log the info
                    continue;
                }

                try {
                    // Resolve TyreImportController with FTPFetcher dependency from the container
                    $controller = app(TyreImportController::class); // This automatically injects the FTPFetcher dependency
                    // $controller->uninstall($supplier->id);
                    $controller->install($supplier->id); // Call the install method for each supplier
                    $message = "Install for supplier {$supplier->supplier_name} completed on domain {$siteIdentifier}.";
                    $this->info($message);
                    Log::info($message); // Log the info
                } catch (\Exception $e) {
                    $message = "Install failed for supplier {$supplier->supplier_name} on domain {$siteIdentifier}: " . $e->getMessage();
                    $this->error($message);
                    Log::error($message); // Log the error
                }
            }
        }
    }

    /**
     * Set the site-specific database configuration.
     *
     * @param string $siteIdentifier
     * @return void
     */
    protected function setSiteDatabaseConfig($siteIdentifier)
    {
        // Ensure the site identifier exists and its database config is loaded
        $siteConfigPath = base_path("website-configs/$siteIdentifier/database.php");

        if (file_exists($siteConfigPath)) {
            // Include the site-specific database configuration
            $domainDatabaseConfig = include($siteConfigPath);

            // Dynamically set the database connection
            Config::set('database.connections.mysql', $domainDatabaseConfig['connections']['mysql']);

            // Clear existing connection and reconnect to use new config
            \DB::purge('mysql');
            \DB::reconnect('mysql');
        }
    }
}
