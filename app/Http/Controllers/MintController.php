<?php

namespace App\Http\Controllers;

use App\Models\Collection;
use Illuminate\Http\Request;

class MintController extends Controller
{
    public function mint(Collection $collection)
    {
        // for ($i = 0; $i < 347; $i++) {
        //     $address = '0x'.sprintf('%040d', $i);
        //     echo $address.'<br>';
        // }
        // dd();

        return view('mint.index')->with(compact('collection'));
    }
}
