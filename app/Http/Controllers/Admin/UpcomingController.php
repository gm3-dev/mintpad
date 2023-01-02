<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Storage;

class UpcomingController extends Controller
{
    public function index()
    {
        $phases = collect(json_decode(Storage::disk('local')->get('data/mint-phases.json')))->sortByDesc('when');
        $collections = $phases->map(function ($phase) {
            $collection = Collection::where('id', $phase->id)->first();
            $collection->mint_at = Date::createFromTimeString($phase->when)->format('Y M d H:i');
            $collection->mint_price = $phase->price;
            return $collection;
        });

        $last_phase_update = false;
        if (Storage::exists('data/mint-phases.json')) {
            $last_phase_update = Date::createFromTimestampUTC(Storage::lastModified('data/mint-phases.json'))->format('Y-m-d H:i:s');
        }

        return view('admin.upcoming.index')->with(compact('collections', 'last_phase_update'));
    }
}
