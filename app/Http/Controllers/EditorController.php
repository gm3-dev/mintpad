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
    public function mint($permalink)
    {
        $collection = Collection::where('permalink', $permalink)->first();

        if (!$collection) {
            abort(404);
        }

        $this->authorize('view', $collection);
        $contract_url = config('blockchains.'.$collection->chain_id.'.explorer').$collection->address;

        return view('editor.mint')->with(compact('collection', 'contract_url'));
    }

    public function embed($permalink)
    {
        $collection = Collection::where('permalink', $permalink)->first();

        if (!$collection) {
            abort(404);
        }

        $this->authorize('view', $collection);
        $contract_url = config('blockchains.'.$collection->chain_id.'.explorer').$collection->address;

        return view('editor.embed')->with(compact('collection', 'contract_url'));
    }
}
