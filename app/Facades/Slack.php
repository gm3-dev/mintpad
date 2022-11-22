<?php

namespace App\Facades;

use App\Classes\SlackAPI;
use Illuminate\Support\Facades\Facade;

class Slack extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return SlackAPI::class;
    }
}
