<?php

namespace App\Http\Controllers;

use App\Models\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Validation\Rules\File as ValidationFile;
use Image;

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
            $logo = count($logos) > 0 ? '/resources/'.$collection->id.'/'.pathinfo($logos[0], PATHINFO_BASENAME) : false;
            $thumbs = glob(public_path('resources/'.$collection->id.'/thumb.*'));
            $thumb = count($thumbs) > 0 ? '/resources/'.$collection->id.'/'.pathinfo($thumbs[0], PATHINFO_BASENAME) : false;

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
            $collection->thumb = $thumb;

            return response()->json($collection, 200);
        }
    }

    /**
     * Edit mint page
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Collection $collection)
    {
        $this->authorize('view', $collection);

        if (!$collection) {
            abort(404);
        }

        return view('mint.edit')->with(compact('collection'));
    }

    /**
     * Delete current logo
     *
     * @param Collection $collection
     * @return Response
     */
    public function deleteLogo(Request $request, Collection $collection)
    {
        if ($request->ajax()) {
            $this->authorize('view', $collection);

            $logos = glob(public_path('resources/'.$collection->id.'/logo.*'));
            $logo = count($logos) > 0 ? '/resources/'.$collection->id.'/'.pathinfo($logos[0], PATHINFO_BASENAME) : false;

            if ($logo && file_exists($logos[0])) {
                unlink($logos[0]);
            }

            return response()->json([], 200);
        }
    }

    /**
     * Upload logo
     *
     * @param Request $request
     * @param Collection $collection
     * @return Response
     */
    public function uploadLogo(Request $request, Collection $collection)
    {
        $this->authorize('view', $collection);

        if ($request->ajax() && $request->hasFile('logo')) {
            $request->validate([
                'logo' => [
                    'required',
                    ValidationFile::types(['jpg', 'jpeg', 'png'])
                        ->max(5 * 1024),
                ],
            ]);

            $file = $request->file('logo');
            $public = public_path('resources/'.$collection->id.'/');

            if (! File::exists($public)) {
                File::makeDirectory($public, 0775, true);
            }

            $filename = $file->getClientOriginalName();
            $file->storeAs(
                $public,
                strtolower($filename)
            );

            $image = Image::make($file->path());
            if ($image->width() > 400 || $image->height() > 400) {
                $image->resize(400, 400, function ($constraint) {
                    $constraint->aspectRatio();
                });
            }
            $image->save($public.'logo.'.$file->getClientOriginalExtension());
        }

        $output = [
            'url' => '/resources/'.$collection->id.'/logo.'.$file->getClientOriginalExtension(),
        ];
        
        return response()->json($output, 200);
    }
}
