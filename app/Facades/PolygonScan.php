<?php

namespace App\Facades;

use App\Classes\PolygonScanApi;
use Illuminate\Support\Facades\Facade;

class PolygonScan extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return PolygonScanApi::class;
    }
}
