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

        $image = $collection->getMeta('seo.image');
        $image_info = parse_url($image);
        $seo = [
            'title' => $collection->getMeta('seo.title', $collection->name),
            'description' => $collection->getMeta('seo.description', $collection->description),
            'image' => !empty($image) && file_exists(public_path($image_info['path'])) ? url($image) : false
        ];

        return view('mint.index')->with(compact('collection', 'seo'));
    }

    public function embed(Request $request, $address)
    {
        $collection = Collection::where('address', $address)->first();
        if (!$collection) {
            abort(404);
        }

        return view('mint.embed')->with(compact('collection'));
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

            $collection->buttons = $collection->getMeta('buttons') ?? [];
            $collection->theme = [
                'mint' => $collection->getMeta('theme.mint'),
                'embed' => $collection->getMeta('theme.embed')
            ];
            $collection->settings = [
                'embed' => $collection->getMeta('settings.embed')
            ];
            $collection->phases = [
                1 => ['name' => $collection->getMeta('phases.1.name')],
                2 => ['name' => $collection->getMeta('phases.2.name')],
                3 => ['name' => $collection->getMeta('phases.3.name')]
            ];
            $collection->logo = $logo;
            $collection->background = $background;
            $collection->thumb = $thumb;
            $collection->embed_url = route('mint.embed', $collection->address);

            return response()->json($collection, 200);
        }
    }
}
