<?php

namespace App\Providers;

use App\Classes\SlackAPI;
use Illuminate\Support\ServiceProvider;

class SlackServiceProvider extends ServiceProvider
{
    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(SlackAPI::class, function ($app) {
            return new SlackAPI(config('services.slack.token'));
        });
    }
}
