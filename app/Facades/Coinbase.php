<?php

namespace App\Facades;

use App\Classes\CoinbaseAPI;
use Illuminate\Support\Facades\Facade;

class Coinbase extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return CoinbaseAPI::class;
    }
}
