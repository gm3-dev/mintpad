<?php

namespace App\Http\Controllers;

use App\Models\Collection;

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
}
