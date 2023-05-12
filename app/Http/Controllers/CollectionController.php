<?php

namespace App\Http\Controllers;

use App\Facades\Slack;
use App\Models\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Str;
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
        $collections = Collection::select(['id', 'name', 'chain_id', 'type', 'address'])->where('user_id', Auth::user()->id)->orderBy('id', 'DESC')->get();
        return Inertia::render('Collections/Index', [
            'collections' => $collections
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return Inertia::render('Collections/Create', []);
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

        $request->validate([
            'name' => 'required',
            'type' => 'required',
            'royalties' => 'required|numeric|between:0,100',
            'symbol' => 'required',
            'chain_id' => 'required|numeric',
            'address' => 'required'
        ]);
        $counter = 0;
        do {
            $permalink = Str::slug($request->get('name')) . ($counter > 0 ? '-' . $counter : '');
            $permalink_check = Collection::where('permalink', $permalink)->first();
            $counter++;
        } while ($permalink_check);

        $collection->name  = $request->get('name');
        $collection->type  = $request->get('type');
        $collection->permalink  = $permalink;
        $collection->symbol  = $request->get('symbol');
        $collection->royalties  = $request->get('royalties');
        $collection->chain_id  = $request->get('chain_id');
        $collection->address  = $request->get('address');
        $collection->save();

        $public = public_path('resources/'.$collection->id.'/');
        if (! File::exists($public)) {
            File::makeDirectory($public, 0775, true);
        }

        Slack::send('#collections', '`'.$collection->name . '` added to Mintpad!');

        return Redirect::route('collections.edit', $collection->id);
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

        // SEO
        $image = $collection->getMeta('seo.image', false);
        $image_info = parse_url($image);
        $collection->seo = [
            'title' => $collection->getMeta('seo.title', ''),
            'description' => $collection->getMeta('seo.description', ''),
            'image' => !empty($image) && file_exists(public_path($image_info['path'])) ? $image : false
        ];
        $collection->mint_url = config('app.mint_url');
        $collection->mint_editor_url = config('app.url').'/mint-editor';
        $collection->embed_editor_url = config('app.url').'/embed-editor';

        return Inertia::render('Collections/Edit', [
            'collection' => $collection
        ]);
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

        $data = $request->all();

        if (isset($data['buttons'])) {
            $collection->setMeta('buttons', $data['buttons'] ?? []);
        }
        if (isset($data['theme']['mint'])) {
            $collection->setMeta('theme.mint', $data['theme']['mint'] ?? []);
        }
        if (isset($data['theme']['embed'])) {
            $collection->setMeta('theme.embed', $data['theme']['embed'] ?? []);
        }
        if (isset($data['settings']['embed'])) {
            $collection->setMeta('settings.embed', $data['settings']['embed'] ?? []);
        }

        $collection->save();

        return response()->json($collection, 200);
    }

    /**
     * Update metadata settings
     *
     * @param Request $request
     * @param Collection $collection
     * @return Response
     */
    public function updateMetadata(Request $request, Collection $collection)
    {
        if ($request->ajax()) {
            $this->authorize('update', $collection);

            $data = $request->all();

            $collection->name  = $data['name'] ?? '';
            $collection->save();

            return Redirect::back();
        }
    }

    /**
     * Update mint settings
     *
     * @param Request $request
     * @param Collection $collection
     * @return Response
     */
    public function updateMint(Request $request, Collection $collection)
    {
        if ($request->ajax()) {
            $this->authorize('update', $collection);

            $request->validate([
                'permalink' => ['required', 'max:255', 'unique:collections,permalink,'.$collection->id],
                'seo.title' => ['max:60'],
                'seo.description' => ['max:155']
            ]);

            $data = $request->all();

            $collection->permalink = $data['permalink'] ?? '';
            $collection->setMeta('seo.title', $data['seo']['title'] ?? '');
            $collection->setMeta('seo.description', $data['seo']['description'] ?? '');
            $collection->setMeta('seo.image', $data['image'] ?? '');
            $collection->save();

            return Redirect::back();
        }
    }

    /**
     * Get whitelist data
     *
     * @param Request $request
     * @param Collection $collection
     * @return Response
     */
    public function whitelist(Request $request, Collection $collection)
    {
        if ($request->hasFile('file')) {
            $file = $request->file('file');

            $addresses = [];
            $file_handle = fopen($file, 'r');
            while (!feof($file_handle)) {
                $row = fgetcsv($file_handle, 0, ',');
                if (isset($row[0]) && strpos($row[0], '0x') === 0) {
                    $addresses[] = [
                        'address' => $row[0],
                        'maxClaimable' => 1
                    ];
                }
            }
            fclose($file_handle);

            return response()->json($addresses);
        }
    }

    /**
     * Download first NFT as thumb
     *
     * @param Request $request
     * @param Collection $collection
     * @return Response
     */
    public function downloadThumb(Request $request, Collection $collection)
    {
        if ($request->ajax()) {
            $this->authorize('view', $collection);

            $public = public_path('resources/'.$collection->id.'/');
            if (! File::exists($public)) {
                File::makeDirectory($public, 0775, true);
            }

            $url = $request->get('url');
            $destination = public_path('resources/'.$collection->id.'/thumb.'.pathinfo($url, PATHINFO_EXTENSION));

            if (! File::exists($destination)) {
                file_put_contents($destination, file_get_contents($url));
            }

            return response()->json(['url' => $url], 200);
        }
    }
}
