<?php

namespace App\Console\Commands;

use App\Jobs\GenerateMintPhaseData as JobGenerateMintPhaseData;
use App\Models\Collection;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class GenerateMintPhaseData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:mint-phases';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate mint phase data';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        if (! Storage::exists('data')) {
            Storage::makeDirectory('data');
        }

        $collections = Collection::orderBy('id', 'DESC')->get();
        $output = [];
        foreach ($collections as $collection) {
            $output[] = ['id' => $collection->id, 'address' => $collection->address, 'chain' => $collection->chain_name, 'type' => $collection->type];
        }

        Storage::disk('local')->put('data/collections.json', json_encode($output));

        JobGenerateMintPhaseData::dispatch()->onQueue('default');

        return;
    }
}
