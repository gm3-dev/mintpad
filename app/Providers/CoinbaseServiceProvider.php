<?php

namespace App\Providers;

use App\Classes\CoinbaseAPI;
use Illuminate\Support\ServiceProvider;

class CoinbaseServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(CoinbaseAPI::class, function ($app) {
            return new CoinbaseAPI(config('coinbase'));
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
