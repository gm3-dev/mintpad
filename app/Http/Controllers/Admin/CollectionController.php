<?php

namespace App\Http\Controllers\Admin;

use App\Facades\PolygonScan;
use App\Facades\Coinbase;
use App\Http\Controllers\Controller;
use App\Models\Collection;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class CollectionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $collections = Collection::select(['id', 'name', 'chain_id', 'address', 'permalink', 'type'])->orderBy('id', 'DESC')->get();
        return Inertia::render('Admin/Collections/Index', compact('collections'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $users = User::select('id', 'name')->orderBy('id')->pluck('name', 'id');
        return Inertia::render('Admin/Collections/Create', compact('users'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if ($request->ajax()) {
            $request->validate([
                'user_id' => ['required'],
                'type' => ['required'],
                'name' => ['required'],
                'symbol' => ['required'],
                'chain_id' => ['required'],
                'address' => ['required', 'max:255', 'unique:collections,permalink'],
                'permalink' => ['required', 'max:255', 'unique:collections,permalink'],
            ]);

            $collection = new Collection();
            $collection->user_id = $request->user_id;
            $collection->type = $request->type;
            $collection->name = $request->name;
            $collection->description = $request->description;
            $collection->symbol = $request->symbol;
            $collection->royalties = $request->royalties;
            $collection->chain_id = $request->chain_id;
            $collection->address = $request->address;
            $collection->permalink = $request->permalink;
            $collection->save();
    
            return Redirect::route('admin.collections.index');
        }
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

            return Redirect()->back();
        }
    }
}
