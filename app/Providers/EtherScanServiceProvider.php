<?php

namespace App\Providers;

use App\Classes\EtherScanApi;
use Illuminate\Support\ServiceProvider;

class EtherScanServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(EtherScanApi::class, function ($app) {
            return new EtherScanApi(config('etherscan'));
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
