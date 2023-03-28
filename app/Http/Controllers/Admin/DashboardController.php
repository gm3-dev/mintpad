<?php

namespace App\Http\Controllers\Admin;

use App\Facades\EtherScan;
use App\Facades\PolygonScan;
use App\Http\Controllers\Controller;
use App\Models\Collection;
use App\Models\Import;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::all()->count();
        $imports = Import::all();
        $collections = Collection::count();
        $chains = Collection::select('chain_id', DB::raw('COUNT(*) as count'))->groupBy('chain_id')->pluck('count', 'chain_id');
        $wallets = $this->getWalletBalances();

        return Inertia::render('Admin/Dashboard/Index', compact('collections', 'chains', 'imports', 'users', 'wallets'));
    }

    public function getWalletBalances()
    {
        return [
            1 => EtherScan::getBalance(config('wallet.address')),
            137 => PolygonScan::getBalance(config('wallet.address'))
        ];
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
