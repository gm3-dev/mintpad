<?php

namespace App\Providers;

use App\Classes\EtherScanAPI;
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
        $this->app->singleton(EtherScanAPI::class, function ($app) {
            return new EtherScanAPI(config('etherscan'));
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
