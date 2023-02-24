<?php

namespace App\Http\Controllers;

use App\Facades\Slack;
use App\Models\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Str;

class CollectionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $collections = Collection::where('user_id', Auth::user()->id)->orderBy('id', 'DESC')->get();
        return view('collections.index')->with(compact('collections'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $blockchains = get_blockchain_list();
        return view('collections.create')->with(compact('blockchains'));
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

        $public = public_path('resources/'.$collection->id.'/');
        if (! File::exists($public)) {
            File::makeDirectory($public, 0775, true);
        }

        Slack::send('#collections', '`'.$collection->name . '` added to Mintpad!');

        return response()->json($collection, 200);
    }

    /**
     * Manage NFT collection
     *
     * @param  int  $id
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function collection(Request $request, Collection $collection)
    {
        $this->authorize('view', $collection);

        $images = File::glob(storage_path('app/collections/'.$collection->id.'/').'*.{png,gif,jpg,jpeg}', GLOB_BRACE);
        return view('collections.collection')->with(compact('collection', 'images'));
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

        return view('collections.edit')->with(compact('collection'));
    }

    /**
     * Fetch collection data
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function fetch(Collection $collection)
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

        return response()->json($collection, 200);
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

            file_put_contents($destination, file_get_contents($url));

            return response()->json(['url' => $url], 200);
        }
    }

    /**
     * Update claim phases
     *
     * @param Request $request
     * @param Collection $collection
     * @return Response
     */
    public function updateClaimPhases(Request $request, Collection $collection)
    {
        if ($request->ajax()) {
            $this->authorize('update', $collection);

            //

            return response()->json($collection, 200);
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
                'title' => ['max:60'],
                'description' => ['max:155']
            ]);

            $data = $request->all();

            $collection->permalink = $data['permalink'] ?? '';
            $collection->setMeta('seo.title', $data['title'] ?? '');
            $collection->setMeta('seo.description', $data['description'] ?? '');
            $collection->setMeta('seo.image', $data['image'] ?? '');
            $collection->save();

            return response()->json($collection, 200);
        }
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
            $collection->description  = $data['description'] ?? '';
            $collection->save();

            return response()->json($collection, 200);
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

            $file_handle = fopen($file, 'r');
            while (!feof($file_handle)) {
                $row = fgetcsv($file_handle, 0, ',');
                if (isset($row[0]) && strpos($row[0], '0x') === 0) {
                    $line_of_text[] = ['address' => $row[0]];
                }
            }
            fclose($file_handle);

            return response()->json($line_of_text);
        }
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
            'royalties' => 'required|numeric|between:0,100',
            'description' => 'required',
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
        $collection->permalink  = $permalink;
        $collection->description  = $request->get('description');
        $collection->symbol  = $request->get('symbol');
        $collection->royalties  = $request->get('royalties');
        $collection->chain_id  = $request->get('chain_id');
        $collection->address  = $request->get('address');
        $collection->save();
    }
}
