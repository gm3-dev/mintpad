<?php

namespace App\Http\Controllers\Admin;

use App\Facades\PolygonScan;
use App\Facades\Coinbase;
use App\Http\Controllers\Controller;
use App\Models\Collection;
use Illuminate\Http\Request;

class CollectionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // dump(Coinbase::getProfile());
        // Coinbase::getProducts();
        // Coinbase::getProductByid('SOL-EUR');
        // Coinbase::postOrder('SOL-EUR', '0.1');
        // dump(Coinbase::getAccountById('0937b056-2f39-478e-8c3a-0621c8a24906'));
        // Coinbase::getCoinbaseAccounts();
        // dump(Coinbase::getAccountByToken('SOL'));
        // dump(Coinbase::getAccountByToken('SOL'));
        // Coinbase::postConversion();

        // $transfers = Coinbase::getTransfers();
        // $transfer = Coinbase::getTransferById('ad4bed40-45d7-408a-bede-c65d6c8d8ff1');
        // dump($transfers[0]);
        // dump($transfer);

        // PolygonScan::getBalance('0x892a99573583c6490526739ba38baefae10a84d4');
        // PolygonScan::getInternalTransactions('0x892a99573583c6490526739bA38BaeFae10a84D4');
        // PolygonScan::getNormalTransactions('0x892a99573583c6490526739bA38BaeFae10a84D4');

        $collections = Collection::orderBy('id', 'DESC')->get();
        return view('admin.collections.index')->with(compact('collections'));
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
    public function destroy(Request $request, Collection $collection)
    {
        if ($request->ajax()) {
            $collection->delete();

            return response()->json(['succes']);
        }
    }
}
