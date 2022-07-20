<?php

namespace App\Http\Controllers;

use App\Models\Collection;
use Collator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use Image;

class CollectionController extends Controller
{
    public $blockchains = [
        'ethereum' => 'Ethereum (ETH)',
        'polygon' => 'Polygon (MATIC)',
        'fantom' => 'Fantom (FTM)',
        'avalanche' => 'Avalanche (AVAX)',
    ];
    public $tokens = [
        'ethereum' => 'ETH',
        'polygon' => 'MATIC',
        'fantom' => 'FTM',
        'avalanche' => 'AVAX',
    ];

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
        $blockchains = $this->blockchains;
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

        return response()->json($collection, 200);

        // return redirect()->route('collections.index')->with('status', 'Collection added!');
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
     * upload NFT's for the specified resource
     *
     * @param  int  $id
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function upload(Request $request, Collection $collection)
    {
        $this->authorize('view', $collection);

        if (! Storage::exists('collections')) {
            Storage::makeDirectory('collections', 0775, true);
        }

        $output = ['counter' => 0, 'images' => []];
        if ($request->hasFile('files')) {
            $files = $request->file('files');
            $path = 'collections/' . $collection->id;

            if (! Storage::exists($path . '/thumbs')) {
                Storage::makeDirectory($path . '/thumbs', 0775, true);
            }

            foreach ($files as $file_key => $file) {
                $filename = $file->getClientOriginalName();
                // $path = $request->get('paths')[$file_key];
                $file->storeAs(
                    $path,
                    strtolower($filename)
                );
                if ($file->extension() != 'json') {
                    $image = Image::make($file->path());
                    $image->resize(100, 100, function ($constraint) {
                        $constraint->aspectRatio();
                    })->save(storage_path('app/' . $path . '/thumbs/' . strtolower($filename)));
                    $output['images'][] = url(route('collections.image', [$collection->id, strtolower($filename)]));
                }
                $output['counter']++;
            }
        }
        $output['files'] = count($files);

        return response()->json($output, 200);
    }

    public function image(Request $request, Collection $collection, $filename)
    {
        $this->authorize('view', $collection);

        $path = storage_path('app/collections/'.$collection->id.'/thumbs/' . $filename);
        if (!File::exists($path)) {
            abort(404);
        }

        $file = File::get($path);
        $type = File::mimeType($path);

        $response = Response::make($file, 200);
        $response->header("Content-Type", $type);

        return $response;
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

        $blockchains = $this->blockchains;
        return view('collections.edit')->with(compact('collection', 'blockchains'));
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

        $collection->token = $this->tokens[$collection->blockchain];

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
     * Show the claim page
     *
     * @param Collection $collection
     * @return Response
     */
    public function claim(Collection $collection)
    {
        $this->authorize('view', $collection);

        return view('collections.claim')->with(compact('collection'));
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
            'blockchain' => 'required',
            'address' => 'required'
        ]);

        $collection->name  = $request->get('name');
        $collection->description  = $request->get('description');
        $collection->symbol  = $request->get('symbol');
        $collection->royalties  = $request->get('royalties');
        $collection->blockchain  = $request->get('blockchain');
        $collection->address  = $request->get('address');
        $collection->deployed = true;
        $collection->save();
    }
}
