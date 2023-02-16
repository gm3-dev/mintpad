<?php

namespace App\Facades;

use App\Classes\EtherScanApi;
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
        return EtherScanApi::class;
    }
}
