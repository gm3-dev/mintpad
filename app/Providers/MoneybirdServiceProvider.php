<?php

namespace App\Providers;

use App\Classes\MoneybirdAPI;
use Illuminate\Support\ServiceProvider;

class MoneybirdServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(MoneybirdAPI::class, function ($app) {
            return new MoneybirdAPI(config('moneybird.token'));
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
