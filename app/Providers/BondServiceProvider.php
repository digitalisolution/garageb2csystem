<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
// use App\Services\BondService;

class BondServiceProvider extends ServiceProvider
{
    public function register()
    {
        // $this->app->singleton(BondService::class, function ($app) {
        //     $supplierDetails = config('services.bond'); // Load configuration
        //     return new BondService($supplierDetails);
        // });
    }

    public function boot()
    {
        // Boot logic, if any
    }
}
