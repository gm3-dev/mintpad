<?php

namespace App\Facades;

use App\Classes\MoneybirdAPI;
use Illuminate\Support\Facades\Facade;

class Moneybird extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return MoneybirdAPI::class;
    }
}
