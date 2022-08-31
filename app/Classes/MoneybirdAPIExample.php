<?php

namespace App\Classes;

use Illuminate\Support\Facades\Http;

class MoneybirdAPIExample
{
    public $client;
    public $administration;
    public $base_url;
    public $tax_rate_id; // 21.0
    public $ledger_account_id; // VoIP
    public $document_style_id; // Call4less
    public $workflow_id; // Call4less

    public function __construct($token)
    {
        $this->client = Http::withToken($token);
        $this->administration = $this->client->get('https://moneybird.com/api/v2/administrations.json');
        if (!$this->administration->successful()) {
            dd('Could not connect to Moneybird...');
        }
        $this->base_url = 'https://moneybird.com/api/v2/' . $this->administration[0]['id'];
        $this->tax_rate_id = $this->getTaxRateId();
        $this->ledger_account_id = $this->getLedgerAccountId();
        $this->document_style_id = $this->getDocumentStyleId();
        // $this->workflow_id = $this->getWorkflowId();

        if ($this->tax_rate_id == null) {
            dd("Taxrate '21.0' not found");
        }
        if ($this->ledger_account_id == null) {
            dd("Category 'VoIP' not found");
        }
        if ($this->document_style_id == null) {
            dd("Document style 'Call4less' not found");
        }
        // if ($this->workflow_id == null) {
        //     dd("Workflow 'Call4less' not found");
        // }
    }

    public function getCustomerById($id)
    {
        $contact = $this->client->get($this->base_url . '/contacts/customer_id/' . $id . '.json');
        if ($contact->collect()->count() == 0) {
            return false;
        }
        
        return $contact->object();
    }

    public function createSalesInvoice($contact, $details)
    {
        // Create details list
        $details_attributes = [];
        foreach ($details as $detail) {
            $details_attributes[] = [
                'amount' => $detail['quantity'],
                'price' => $detail['article-price-excl'],
                'description' => $detail['free-text-1'] . PHP_EOL . $detail['free-text-2'],
                'tax_rate_id' => $this->tax_rate_id,
                'ledger_account_id' => $this->ledger_account_id // Category
            ];
        }

        // Create invoice
        $invoice = $this->client->post($this->base_url . '/sales_invoices.json', ['sales_invoice' => [
            'contact_id' => $contact->id,
            'invoice_date' => date('Y-m-d'),
            'reference' => 'Maandelijks gebruik',
            'currency' => 'EUR',
            'prices_are_incl_tax' => false,
            'details_attributes' => $details_attributes,
            'document_style_id' => $this->document_style_id, // Huisstijl
            // 'workflow_id' => $this->workflow_id // Workflow
        ]]);

        if (! isset($invoice->object()->id)) {
            return false;
        }

        return $invoice->object()->id;
    }

    public function sendSalesInvoice($id)
    {
        $response = $this->client->patch($this->base_url . '/sales_invoices/' . $id . '/send_invoice.json', ['sales_invoice_sending' => [
            'delivery_method' => 'Manual',
            'invoice_date' => date('Y-m-d')
        ]]);
    }

    protected function getTaxRateId()
    {
        $tax_rates = $this->client->get($this->base_url . '/tax_rates.json');
        if ($tax_rates->collect()->count() == 0) {
            return null;
        }
        
        foreach ($tax_rates->object() as $tax_rate) {
            if ($tax_rate->percentage == '21.0') {
                return $tax_rate->id;
            }
        }
        return null;
    }

    protected function getLedgerAccountId()
    {
        $ledger_accounts = $this->client->get($this->base_url . '/ledger_accounts.json');
        if ($ledger_accounts->collect()->count() == 0) {
            return null;
        }
        
        foreach ($ledger_accounts->object() as $ledger_account) {
            if (strtolower($ledger_account->name) == 'voip') {
                return $ledger_account->id;
            }
        }
        return null;
    }

    protected function getDocumentStyleId()
    {
        $document_styles = $this->client->get($this->base_url . '/document_styles.json');
        if ($document_styles->collect()->count() == 0) {
            return null;
        }
        
        foreach ($document_styles->object() as $document_style) {
            if ($document_style->name == 'Call4less') {
                return $document_style->id;
            }
        }
        return null;
    }

    protected function getWorkflowId()
    {
        $workflows = $this->client->get($this->base_url . '/workflows.json');
        if ($workflows->collect()->count() == 0) {
            return null;
        }
        
        foreach ($workflows->object() as $workflow) {
            if ($workflow->name == 'Call4less') {
                return $workflow->id;
            }
        }
        return null;
    }
}
