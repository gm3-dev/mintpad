<?php

namespace App\Http\Controllers\Admin;

use App\Facades\Moneybird;
use App\Http\Controllers\Controller;
use App\Models\Import;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ImportController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $imports = Import::orderBy('created_at', 'DESC')->get();
        return Inertia::render('Admin/Import/Index', compact('imports'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->all();
        $file = $request->file;
        if ($file) {
            // $filename = $file->getClientOriginalName();
            // $extension = $file->getClientOriginalExtension();
            // $tempPath = $file->getRealPath();
            // $fileSize = $file->getSize();

            $file->move(storage_path('app/imports'), 'import.csv');
            $filepath = storage_path('app/imports/import.csv');

            $file = fopen($filepath, "r");
            $rows = array();
            $i = 0;

            while (($filedata = fgetcsv($file, 1000, ",")) !== false) {
                $num = count($filedata);

                // Skip first row which contain titles
                if ($i == 0) {
                    $i++;
                    continue;
                }
                for ($c = 0; $c < $num; $c++) {
                    $rows[$i][] = $filedata[$c];
                }
                $i++;
            }
            fclose($file);

            $total_usd = 0;
            $import_at = date('Y-m-d', strtotime('01-'.$data['month'].'-'.$data['year']));
            $import = Import::firstOrCreate([], ['chain_id' => $data['chain_id'], 'import_at' => $import_at]);
            foreach ($rows as $row) {
                if (!$import->transactions()->where('block', $row[1])->exists()) {
                    Transaction::create([
                        'block' => $row[1],
                        'import_id' => $import->id,
                        'from' => $row[7],
                        'to' => $row[8],
                        'amount' => $row[10],
                        'price' => $row[13],
                        'method' => $row[16],
                        'transaction_at' => $row[3],
                    ]);
                    $total_usd += $row[13] * $row[10];
                }
            }
            $import->total = $total_usd;
            $import->save();
        }

        return redirect()->back()->with('success', 'Import complete');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
