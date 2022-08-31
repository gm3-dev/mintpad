<?php

namespace App\Jobs;

use App\Facades\Moneybird;
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

    public $user;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if (Moneybird::administrationIsValid()) {
            $details = collect();
            $details->push(['amount' => 1, 'price' => 300.00, 'description' => 'Product description 1', 'tax_rate_id' => 364534241984250909]);
            $details->push(['amount' => 2, 'price' => 200.00, 'description' => 'Product description 2', 'tax_rate_id' => 364534241995785247]);
        
            $invoice = Moneybird::createSalesInvoice($this->user, $details);
            if ($invoice['id']) {
                Moneybird::sendSalesInvoice($invoice['id']);
                Moneybird::createSalesInvoicePayment($invoice);
            }
        }
    }
}
