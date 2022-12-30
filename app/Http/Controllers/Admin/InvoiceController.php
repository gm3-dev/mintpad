<?php

namespace App\Http\Controllers\Admin;

use App\Facades\Moneybird;
use App\Http\Controllers\Controller;
use App\Jobs\GenerateInvoices;
use App\Models\Collection;
use App\Models\Import;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->all();

        GenerateInvoices::dispatch($data['month'], $data['year'])->onQueue('default');

        return redirect()->back()->with('success', 'Job added to queue');
    }
}
