<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DataController extends Controller
{
    public function blockchains()
    {
        $blockchains = [];
        foreach (config('blockchains') as $blockchain) {
            if (config('app.env') != 'production') {
                $blockchains[$blockchain['id']] = $blockchain;
            } elseif ($blockchain['testnet'] == false) {
                $blockchains[$blockchain['id']] = $blockchain;
            }
        }
        return response()->json($blockchains);
    }
}
