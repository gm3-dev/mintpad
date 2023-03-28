<?php

namespace App\Facades;

use App\Classes\EtherScanAPI;
use Illuminate\Support\Facades\Facade;

class EtherScan extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return EtherScanAPI::class;
    }
}
