<?php

namespace App\Http\Controllers;

use App\Models\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Validation\Rules\File as ValidationFile;
use Image;

class EditorController extends Controller
{
    /**
     * Edit mint page
     *
     * @return \Illuminate\Http\Response
     */
    public function index($permalink)
    {
        $collection = Collection::where('permalink', $permalink)->first();

        if (!$collection) {
            abort(404);
        }

        $this->authorize('view', $collection);

        return view('editor.index')->with(compact('collection'));
    }

    /**
     * Upload resource
     *
     * @param Request $request
     * @param Collection $collection
     * @return Response
     */
    public function uploadResource(Request $request, Collection $collection)
    {
        $this->authorize('view', $collection);

        if ($request->ajax() && $request->hasFile('resource')) {
            $name = $request->get('name');
            $request->validate([
                'resource' => [
                    'required',
                    ValidationFile::types(['jpg', 'jpeg', 'png'])
                        ->max(config('resources.'.$name.'.max')),
                ],
            ]);

            $file = $request->file('resource');
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
            $width = config('resources.'.$name.'.width');
            if ($image->width() > $width || $image->height() > $width) {
                $image->resize($width, $width, function ($constraint) {
                    $constraint->aspectRatio();
                });
            }
            $image->save($public.$name.'.'.$file->getClientOriginalExtension());
        }

        $output = [
            'url' => '/resources/'.$collection->id.'/'.$name.'.'.$file->getClientOriginalExtension(),
        ];
        
        return response()->json($output, 200);
    }

    /**
     * Delete current logo
     *
     * @param Collection $collection
     * @return Response
     */
    public function deleteResource(Request $request, Collection $collection)
    {
        if ($request->ajax()) {
            $this->authorize('view', $collection);

            $name = $request->get('name');
            $resources = glob(public_path('resources/'.$collection->id.'/'.$name.'.*'));

            if (count($resources) > 0 && file_exists($resources[0])) {
                unlink($resources[0]);
            }

            return response()->json([], 200);
        }
    }
}
