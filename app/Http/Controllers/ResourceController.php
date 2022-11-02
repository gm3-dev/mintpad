<?php

namespace App\Http\Controllers;

use App\Models\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Validation\Rules\File as ValidationFile;
use Image;

class ResourceController extends Controller
{
    /**
     * Upload resource
     *
     * @param Request $request
     * @param Collection $collection
     * @return Response
     */
    public function upload(Request $request, Collection $collection)
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
            'url' => '/resources/'.$collection->id.'/'.$name.'.'.$file->getClientOriginalExtension().'?v='.filemtime($public.$name.'.'.$file->getClientOriginalExtension())
        ];
        
        return response()->json($output, 200);
    }

    /**
     * Delete current logo
     *
     * @param Collection $collection
     * @return Response
     */
    public function delete(Request $request, Collection $collection)
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
