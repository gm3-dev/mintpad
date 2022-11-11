<?php

namespace App\Http\Controllers\Admin;

use App\Facades\Moneybird;
use App\Http\Controllers\Controller;
use App\Models\Import;
use App\Models\Transaction;
use Illuminate\Http\Request;

class ImportController extends Controller
{
    public function index()
    {
        $blockchains = ['Mainnets' => [], 'Testnets' => []];
        foreach (config('blockchains') as $blockchain) {
            if ($blockchain['chain'] == 'evm') {
                $array_key = $blockchain['testnet'] == true ? 'Testnets' : 'Mainnets';
                $blockchains[$array_key][$blockchain['id']] = $blockchain['full'].' ('.$blockchain['id'].')';
            }
        }
        $imports = Import::orderBy('created_at', 'DESC')->get();
        return view('admin.import.index')->with(compact('imports', 'blockchains'));
    }

    public function store(Request $request)
    {
        $data = $request->all();
        $file = $request->file('file');
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
                        'from' => $row[4],
                        'to' => $row[5],
                        'amount' => $row[7],
                        'fee' => $row[10],
                        'price' => $row[12],
                        'method' => $row[15],
                        'transaction_at' => $row[3],
                    ]);
                    $total_usd += $row[12] * $row[7];
                }
            }
            $import->total = $total_usd;
            $import->save();
        }

        return redirect()->back()->with('success', 'Import complete');
    }
}
