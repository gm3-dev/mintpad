<?php

namespace App\Console\Commands;

use App\Facades\Moneybird;
use App\Jobs\GenerateInvoices as JobsGenerateInvoices;
use App\Models\User;
use Illuminate\Console\Command;

class GenerateInvoices extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'invoices:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate user invoices';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // Set previous month
        $month = date('n') - 1;
        $year = date('Y') - ($month === 0 ? 1 : 0);
        if ($month === 0) {
            $month = 12;
        }

        JobsGenerateInvoices::dispatch($month, $year)->onQueue('low');

        return;
    }
}
