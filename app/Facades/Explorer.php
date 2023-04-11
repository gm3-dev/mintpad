<?php

namespace App\Facades;

use App\Classes\ExplorerAPI;
use Illuminate\Support\Facades\Facade;

class Explorer extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return ExplorerAPI::class;
    }
}
