<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class UpcomingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $phases = collect(json_decode(Storage::disk('local')->get('data/mint-phases.json')))->sortByDesc('when');
        $collections = $phases->map(function ($phase) {
            $collection = Collection::where('id', $phase->id)->first();
            $collection->mint_at = Date::createFromTimeString($phase->when)->format('Y M d H:i');
            $collection->mint_price = $phase->price;
            return $collection;
        });

        $last_update = false;
        if (Storage::exists('data/mint-phases.json')) {
            $last_update = Date::createFromTimestampUTC(Storage::lastModified('data/mint-phases.json'))->format('Y-m-d H:i:s');
        }

        return Inertia::render('Admin/Upcoming/Index', compact('collections', 'last_update'));
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
