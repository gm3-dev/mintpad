<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class GeneratorController extends Controller
{
    /**
     * Show the generate page
     *
     * @return Response
     */
    public function index()
    {
        return view('generator.index');
    }

    /**
     * Create traits.json
     *
     * @param Request $request
     * @return Response
     */
    public function create(Request $request)
    {
        if ($request->ajax()) {
            if (! Storage::exists('traits')) {
                Storage::makeDirectory('traits', 0775, true);
            }

            if ($request->has('layers')) {
                Storage::disk('local')->put('traits/'.Auth::user()->id.'/traits.json', $request->get('layers'));

                return response()->json('success', 200);
            }

            return response()->json('success', 409);
        }
    }

    /**
     * upload NFT's for the specified resource
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function upload(Request $request)
    {
        if ($request->ajax()) {
            if (! Storage::exists('users')) {
                Storage::makeDirectory('traits', 0775, true);
            }

            if ($request->hasFile('files')) {
                $files = $request->file('files');
                $path = 'traits/' . Auth::user()->id;

                foreach ($files as $file_key => $file) {
                    $dir_path = $_FILES['files']['full_path'][$file_key];
                    if ($full_path = $this->getFullPath($dir_path)) {
                        $filename = $file->getClientOriginalName();
                        $file->storeAs($path.'/'.$full_path, $filename);
                    }
                }

                return response()->json('success', 200);
            }

            return response()->json('success', 409);
        }
    }

    public function getFullPath($path)
    {
        $structure = explode('/', $path);
        if (count($structure) == 3) {
            array_shift($structure);
            return $structure[0];
        } else {
            return false;
        }
    }
}
