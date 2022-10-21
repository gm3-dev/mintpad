<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
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
            if (! Storage::exists('traits/'.Auth::user()->id)) {
                Storage::makeDirectory('traits/'.Auth::user()->id, 0775, true);
            }

            if ($request->has('layers')) {
                Storage::disk('local')->put('traits/'.Auth::user()->id.'/traits.json', $request->get('layers'));

                return response()->json(['user_id' => Auth::user()->id], 200);
            }

            return response()->json('error', 409);
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
            if (! Storage::exists('traits')) {
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

            return response()->json('error', 409);
        }
    }

    /**
     * Download collection
     *
     * @return Response
     */
    public function download()
    {
        $file = storage_path(). '/app/archives/'.Auth::user()->id.'/collection.zip';
        if (file_exists($file)) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/zip');
            header('Content-Disposition: attachment; filename="collection.zip"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($file));
            
            $chunkSize = 8 * (1024 * 1024); //8MB (highest possible fread length)
            $handle = fopen($file, 'rb');
            while (!feof($handle)) {
                $buffer = fread($handle, $chunkSize);
                echo $buffer;
                ob_flush();
                flush();
            }
            fclose($handle);
            exit;

            // header('Content-Description: File Transfer');
            // header('Content-Type: application/zip');
            // header('Content-Disposition: attachment; filename="collection.zip"');
            // header('Expires: 0');
            // header('Cache-Control: must-revalidate');
            // header('Pragma: public');
            // header('Content-Length: ' . filesize($file));
            // readfile($file);
            // exit;
        }

        abort(404);

        // Laravel method does not work in Chrome
        // return response()->download($file, 'collection.zip', ['Content-Type' => 'application/zip']);
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

    public function status(Request $request)
    {
        if ($request->ajax()) {
            $file = storage_path('app/collections/'.Auth::user()->id.'/status.json');
            if (file_exists($file)) {
                $status = json_decode(file_get_contents($file), true);
            } else {
                $status = ['state' => 'missing'];
            }

            return response()->json($status);
        }
    }
}
