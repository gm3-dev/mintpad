<?php

namespace App\Http\Controllers;

use App\Models\Collection;
use Illuminate\Http\Request;

class MintController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function mint($permalink)
    {
        $collection = Collection::where('permalink', $permalink)->first();

        if (!$collection) {
            abort(404);
        }

        return view('mint.index')->with(compact('collection'));
    }

    /**
     * Fetch collection data
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function fetch(Request $request, $collection_id)
    {
        if ($request->ajax()) {
            $collection = Collection::find($collection_id)->withoutRelations();

            if (!$collection) {
                return response()->json('Collection not found', 204);
            }

            $logos = glob(public_path('resources/'.$collection->id.'/logo.*'));
            $logo = count($logos) > 0 ? '/resources/'.$collection->id.'/'.pathinfo($logos[0], PATHINFO_BASENAME).'?v='.filemtime($logos[0]) : false;
            $thumbs = glob(public_path('resources/'.$collection->id.'/thumb.*'));
            $thumb = count($thumbs) > 0 ? '/resources/'.$collection->id.'/'.pathinfo($thumbs[0], PATHINFO_BASENAME).'?v='.filemtime($thumbs[0]) : false;
            $backgrounds = glob(public_path('resources/'.$collection->id.'/background.*'));
            $background = count($backgrounds) > 0 ? '/resources/'.$collection->id.'/'.pathinfo($backgrounds[0], PATHINFO_BASENAME).'?v='.filemtime($backgrounds[0]) : false;

            $collection->token = config('blockchains.'.$collection->chain_id.'.token');
            $collection->buttons = $collection->getMeta('buttons');
            $collection->roadmap = $collection->getMeta('roadmap');
            $collection->team = $collection->getMeta('team');
            $collection->about = $collection->getMeta('about');
            $collection->theme = $collection->getMeta('theme');
            $collection->claim_phase_name_1 = $collection->getMeta('claim_phase_name_1');
            $collection->claim_phase_name_2 = $collection->getMeta('claim_phase_name_2');
            $collection->claim_phase_name_3 = $collection->getMeta('claim_phase_name_3');
            $collection->logo = $logo;
            $collection->background = $background;
            $collection->thumb = $thumb;

            return response()->json($collection, 200);
        }
    }
}
