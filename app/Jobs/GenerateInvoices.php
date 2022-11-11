<?php

namespace App\Jobs;

use App\Facades\Moneybird;
use App\Models\Collection;
use App\Models\Import;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GenerateInvoices implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $month;
    public $year;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($month, $year)
    {
        $this->month = $month;
        $this->year = $year;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $import_at = date('Y-m-d', strtotime('01-'.$this->month.'-'.$this->year));
        $imports = Import::where('import_at', $import_at)->get();

        $invoices = [];
        foreach ($imports as $import) {
            $transactions = $import->transactions;

            foreach ($transactions as $transaction) {
                if (! isset($invoices[$transaction->from])) {
                    $invoices[$transaction->from] = [
                        'address' => $transaction->from,
                        'amount' => 0
                    ];
                }

                $usd = $transaction->price * $transaction->amount;
                $invoices[$transaction->from]['amount'] += $usd;
            }
        }

        if (Moneybird::administrationIsValid()) {
            $vat = 371047370954115028; // 21% BTW

            foreach ($invoices as $address => $invoice) {
                $collection = Collection::where('address', $address)->first();

                if ($collection) {
                    $user = $collection->user;

                    $details = collect();
                    $details->push(['amount' => 1, 'price' => $invoice['amount'], 'description' => $collection->name .':<br>address '.$collection->address, 'tax_rate_id' => $vat]);
                
                    $invoice = Moneybird::createSalesInvoice($user, $details);
                    if ($invoice['id']) {
                        dump($invoice);
                        Moneybird::sendSalesInvoice($invoice['id']);
                        Moneybird::createSalesInvoicePayment($invoice);
                    }
                }
            }
        }
    }
}
