<?php

namespace App\Http\Controllers;

use App\Models\Collection;
use Inertia\Inertia;

class EditorController extends Controller
{
    public function embed($permalink)
    {
        $collection = Collection::where('permalink', $permalink)->first();

        if (!$collection) {
            abort(404);
        }

        $this->authorize('view', $collection);

        return Inertia::render('Embed/Index', compact('collection'));
    }
}
