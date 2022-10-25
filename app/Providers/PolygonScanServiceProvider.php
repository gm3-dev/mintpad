<?php

namespace App\Providers;

use App\Classes\PolygonScanApi;
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
        $this->app->singleton(PolygonScanApi::class, function ($app) {
            return new PolygonScanApi(config('polygon'));
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
