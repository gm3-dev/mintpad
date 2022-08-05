<?php

namespace App\Http\Controllers;

use App\Models\Collection;
use Illuminate\Http\Request;

class MintController extends Controller
{
    public $tokens;

    public function __construct()
    {
        $this->tokens = config('tokens');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function mint(Collection $collection)
    {
        return view('mint.index')->with(compact('collection'));
    }

    /**
     * Fetch collection data
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function fetch(Collection $collection)
    {
        $collection->token = $this->tokens[$collection->blockchain];

        return response()->json($collection, 200);
    }
}
