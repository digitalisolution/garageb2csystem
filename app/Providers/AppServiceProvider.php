<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Auth;
use App\Providers\CustomUserProvider;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use App\Models\VrmVehicleDetail;
use App\Models\GeneralSettings;
use Illuminate\Pagination\Paginator;
use App\Models\GarageDetails;
use App\Services\BondService;
use App\Services\EdenService;

use Illuminate\Auth\Notifications\ResetPassword;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        ResetPassword::createUrlUsing(function ($user, string $token) {
            return url(route('customer.password.reset', [
                'token' => $token,
                'email' => $user->getEmailForPasswordReset(),
            ], false));
        });
        // Handle HTTP requests for dynamic DB config
        $this->setSiteDatabaseConfig(request()->getHost());

        // Apply the configuration before running any command
        if ($this->app->runningInConsole()) {
            // Get site identifier based on your logic or configuration
            $siteIdentifier = $this->getSiteIdentifierFromConfig(); // Dynamically get the site identifier
            $this->setSiteDatabaseConfig($siteIdentifier);
        }
        Paginator::useBootstrap();
        // Share cart details and vehicle information with all views
        view()->composer('*', function ($view) {
            $cart = session('cart', []);
            $totalQuantity = array_sum(array_column($cart, 'quantity'));
            $totalPrice = array_reduce($cart, fn($sum, $item) => $sum + ($item['price'] * $item['quantity']), 0);

            $view->with('cart', $cart);
            $view->with('cartTotalQuantity', $totalQuantity);
            $view->with('cartTotalPrice', number_format($totalPrice, 2));

            // Share garage and vehicle details
            $garage = GarageDetails::first();
            $view->with('garage', $garage);

            // $generalSettings = GeneralSettings::all();
            // View::share('generalSettings', $generalSettings);

            $vehicleDetails = VrmVehicleDetail::all();
            View::share('vehicleDetails', $vehicleDetails);
        });
    }

    /**
     * Get the site identifier from the config file.
     *
     * @return string
     */
    protected function getSiteIdentifierFromConfig()
    {
        // Retrieve the site configuration (only return the domain name)
        $sitesConfig = Config::get('sites.sites');

        // For the purpose of console commands, you can either return a specific domain or loop through all
        // Here, we just return the first domain
        return array_key_first($sitesConfig); // Or use other logic to select the domain dynamically
    }

    /**
     * Set the site-specific database configuration.
     *
     * @param string $siteIdentifier
     * @return void
     */
    protected function setSiteDatabaseConfig($siteIdentifier)
    {
        // Skip dynamic database configuration for console commands
        if ($this->app->runningInConsole()) {
            // Optionally, log a warning or set a default database connection
            \Log::warning('Skipping dynamic database configuration for console command.');
            return;
        }

        // Ensure the site identifier exists and its database config is loaded
        $siteConfigPath = base_path("website-configs/$siteIdentifier/database.php");
        if (!file_exists($siteConfigPath)) {
            // Log the error and throw a custom exception for CLI
            // \Log::error("Database configuration file not found for site: $siteIdentifier");

            // Handle HTTP vs CLI differently
            if ($this->app->runningInConsole()) {
                throw new \Exception("Database configuration not found for site: $siteIdentifier");
            } else {
                abort(410, 'Database configuration not found.');
            }
        }

        // Include the site-specific database configuration
        $domainDatabaseConfig = include($siteConfigPath);

        // Dynamically set the database connection
        Config::set('database.connections.mysql', $domainDatabaseConfig['connections']['mysql']);

        // Clear existing connection and reconnect to use new config
        DB::purge('mysql');
        DB::reconnect('mysql');
    }

    /**
     * Register any application services.
     *
     * @return void
     */


    public function register()
    {
        $this->app->bind(BondService::class, function ($app) {
            // Provide the required $supplierDetails array
            $supplierDetails = [
                'trading_point' => env('BOND_TRADING_POINT', ''),
                'supplier_email' => env('BOND_SUPPLIER_EMAIL', ''),
                'api_code' => env('BOND_API_CODE', ''),
                'api_mode' => env('BOND_API_MODE', 'test'), // Default to 'test' mode
            ];

            return new BondService($supplierDetails);
        });

        $this->app->bind(EdenService::class, function ($app) {
            // Provide the required $supplierDetails array
            $supplierDetails = [
                'trading_point' => env('BOND_TRADING_POINT', ''),
                'supplier_email' => env('BOND_SUPPLIER_EMAIL', ''),
                'api_code' => env('BOND_API_CODE', ''),
                'api_mode' => env('BOND_API_MODE', 'test'), // Default to 'test' mode
            ];

            return new EdenService($supplierDetails);
        });
    }

}
