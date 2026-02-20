<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Auth;
use App\Providers\CustomUserProvider;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use App\Models\VrmVehicleDetail;
use Illuminate\Support\Facades\Session;
use App\Models\CalendarSetting;
use Illuminate\Pagination\Paginator;
use App\Models\GarageDetails;
use App\Services\BondService;
use App\Services\EdenService;
use App\Services\BookingNotificationService;

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
        // Custom password reset URL
        ResetPassword::createUrlUsing(function ($user, string $token) {
            return url(route('customer.password.reset', [
                'token' => $token,
                'email' => $user->getEmailForPasswordReset(),
            ], false));
        });

        // Handle DB config based on request host
        $this->setSiteDatabaseConfig(request()->getHost());

        if ($this->app->runningInConsole()) {
            $siteIdentifier = $this->getSiteIdentifierFromConfig();
            $this->setSiteDatabaseConfig($siteIdentifier);
        }
        Paginator::useBootstrap();
        View::composer('*', function ($view) {
            $cart = session('cart', []);
            $totalQuantity = array_sum(array_column($cart, 'quantity'));
            $totalPrice = array_reduce($cart, fn($sum, $item) => $sum + ($item['price'] * $item['quantity']), 0);

            $view->with('cart', $cart);
            $view->with('cartTotalQuantity', $totalQuantity);
            $view->with('cartTotalPrice', number_format($totalPrice, 2));

            $garage = GarageDetails::first();
            $view->with('garage', $garage);
            $garageId = Session::get('selected_garage_id');
            if (!$garageId) {
                return redirect()->route('grages')
                    ->with('error', 'Please select a garage first.');
            }
            $calendarSettings = CalendarSetting::where('garage_id', $garageId)->first();
            $view->with('globalCalendarSettings', $calendarSettings);
        });
        View::composer('core.navbar', function ($view) {
            $service = app(BookingNotificationService::class);
            $newBookings = $service->getLatestBookings();
            $newBookingsCount = $newBookings->count();
            $view->with('newBookings', $newBookings);
            $view->with('newBookingsCount', $newBookingsCount);
        });
    }


    /**
     * Get the site identifier from the config file.
     *
     * @return string
     */
    protected function getSiteIdentifierFromConfig()
    {
        $sitesConfig = Config::get('sites.sites');
        return array_key_first($sitesConfig);
    }

    /**
     * Set the site-specific database configuration.
     *
     * @param string $siteIdentifier
     * @return void
     */
    protected function setSiteDatabaseConfig($siteIdentifier)
    {
        if ($this->app->runningInConsole()) {
            \Log::warning('Skipping dynamic database configuration for console command.');
            return;
        }
        $siteConfigPath = base_path("website-configs/$siteIdentifier/database.php");
        if (!file_exists($siteConfigPath)) {
            if ($this->app->runningInConsole()) {
                throw new \Exception("Database configuration not found for site: $siteIdentifier");
            } else {
                abort(410, 'Database configuration not found.');
            }
        }
        $domainDatabaseConfig = include($siteConfigPath);
        Config::set('database.connections.mysql', $domainDatabaseConfig['connections']['mysql']);
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
            $supplierDetails = [
                'trading_point' => env('BOND_TRADING_POINT', ''),
                'supplier_email' => env('BOND_SUPPLIER_EMAIL', ''),
                'api_code' => env('BOND_API_CODE', ''),
                'api_mode' => env('BOND_API_MODE', 'test'),
            ];

            return new BondService($supplierDetails);
        });

        $this->app->bind(EdenService::class, function ($app) {
            $supplierDetails = [
                'trading_point' => env('BOND_TRADING_POINT', ''),
                'supplier_email' => env('BOND_SUPPLIER_EMAIL', ''),
                'api_code' => env('BOND_API_CODE', ''),
                'api_mode' => env('BOND_API_MODE', 'test'),
            ];

            return new EdenService($supplierDetails);
        });
    }

}
