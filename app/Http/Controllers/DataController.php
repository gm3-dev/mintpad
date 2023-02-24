<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DataController extends Controller
{
    public function blockchains()
    {
        $blockchains = get_blockchains();
        return response()->json($blockchains);
    }
}
