<?php

namespace App\Http\Controllers;

use App\Models\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CollectionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $collections = Collection::where('user_id', Auth::user()->id)->get();
        return view('collections.index')->with(compact('collections'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('collections.manage');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $collection = new Collection();
        $collection->user_id = Auth::user()->id;

        $this->save($request, $collection);

        return redirect()->route('collections.index')->with('status', 'Collection added!');
    }

    /**
     * upload NFT's for the specified resource
     *
     * @param  int  $id
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function upload(Request $request, Collection $collection)
    {
        $this->authorize('view', $collection);

        $files = $request->file('files');

        if ($request->hasFile('files')) {
            foreach ($files as $file_key => $file) {
                $path = $request->get('paths')[$file_key];
                $file->storeAs(
                    'collections/' . $collection->id . '/',
                    $path
                );
            }
        }

        return response()->json($request->get('paths'), 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Collection $collection)
    {
        $this->authorize('view', $collection);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Collection $collection)
    {
        $this->authorize('view', $collection);

        return view('collections.manage')->with(compact('collection'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Collection $collection)
    {
        $this->authorize('update', $collection);

        if ($collection->deployed) {
            return redirect()->back()->with('error', 'This smart contract is already deployed to the blockchain and cannot be changed anymore!');
        }
        
        $files = $request->file('image_collection');
        $files2 = $request->get('image_collection');

        // dump($files);
        // dump($files2);
        // dump($_FILES);

        // if ($request->hasFile('files')) {
        //     foreach ($files as $file_key => $file) {
        //         $path = $request->get('paths')[$file_key];
        //         $file->storeAs(
        //             'collections/' . $collection->id . '/',
        //             $path
        //         );
        //     }
        // }

        $this->save($request, $collection);

        return redirect()->route('collections.index')->with('status', 'Collection updated!');
    }

    /**
     * Set deployed status
     *
     * @param Request $request
     * @param Collection $collection
     * @return Response
     */
    public function deployed(Request $request, Collection $collection)
    {
        $this->authorize('update', $collection);

        $collection->deployed = true;
        $collection->address = $request->get('address');
        $collection->save();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function deploy(Collection $collection)
    {
        $this->authorize('view', $collection);

        return view('collections.deploy')->with(compact('collection'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Collection $collection)
    {
        $this->authorize('delete', $collection);
    }

    /**
     * Save collection
     *
     * @param Request $request
     * @param Collection $collection
     * @return void
     */
    protected function save(Request $request, Collection $collection)
    {
        $request->validate([
            'name' => 'required',
            'mint_cost' => 'required|numeric|between:0,99999',
            'royalties' => 'required|numeric|between:0,100',
            'contract_name' => 'required|alpha',
            'symbol' => 'required',
            'base_name' => 'required',
            'collection_size' => 'required|numeric',
        ]);

        $collection->name  = $request->get('name');
        $collection->contract_name  = $request->get('contract_name');
        $collection->symbol  = $request->get('symbol');
        $collection->whitelist  = $request->get('whitelist') ?? false;
        $collection->collection_size  = $request->get('collection_size');
        $collection->base_name  = $request->get('base_name');
        $collection->mint_cost  = $request->get('mint_cost');
        $collection->royalties  = $request->get('royalties');
        $collection->save();
    }
}
