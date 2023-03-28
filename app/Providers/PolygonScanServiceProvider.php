<?php

namespace App\Providers;

use App\Classes\PolygonScanAPI;
use Illuminate\Support\ServiceProvider;

class PolygonScanServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(PolygonScanAPI::class, function ($app) {
            return new PolygonScanAPI(config('polygonscan'));
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
