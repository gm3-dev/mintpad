<?php

namespace App\Classes;

use Illuminate\Support\Facades\Http;

class MoneybirdAPI
{
    public $client;
    public $administration;
    public $base_url;

    public function __construct($token)
    {
        $this->client = Http::withToken($token);
        $this->administration = $this->client->get('https://moneybird.com/api/v2/administrations.json');
        $this->base_url = 'https://moneybird.com/api/v2/' . $this->administration[0]['id'];
    }

    /**
     * Create contact
     *
     * @param User $user
     * @return mixed
     */
    public function createContact($user)
    {
        $contact = $this->client->post($this->base_url . '/contacts.json', $this->getContactData($user));
        
        if ($contact->getStatusCode() == 201) {
            return $contact['id'];
        } else {
            return false;
        }
    }

    /**
     * Update contact
     *
     * @param User $user
     * @return boolean
     */
    public function updateContact($user)
    {
        if (is_null($user->moneybird_id)) {
            return false;
        }

        $contact = $this->client->put($this->base_url . '/contacts/'.$user->moneybird_id.'.json', $this->getContactData($user));
        
        if ($contact->getStatusCode() == 200) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Check if the administration is valid
     *
     * @return boolean
     */
    public function administrationIsValid()
    {
        return $this->administration->successful();
    }

    /**
     * Create sales invoice
     *
     * @param User $user
     * @param array $details
     * @return mixed
     */
    public function createSalesInvoice($user, $details)
    {
        if (is_null($user->moneybird_id)) {
            return false;
        }

        // Create details list
        $details_attributes = [];
        foreach ($details as $detail) {
            $details_attributes[] = [
                'amount' => $detail['amount'],
                'price' => $detail['price'],
                'description' => $detail['description'],
                'tax_rate_id' => $detail['tax_rate_id'],
                // 'ledger_account_id' => $this->ledger_account_id // Category
            ];
        }

        // Create invoice
        $invoice = $this->client->post($this->base_url . '/sales_invoices.json', ['sales_invoice' => [
            'contact_id' => $user->moneybird_id,
            'invoice_date' => date('Y-m-d'),
            'reference' => 'Mintpad montly usage',
            'currency' => 'EUR',
            'prices_are_incl_tax' => false,
            'details_attributes' => $details_attributes,
            // 'document_style_id' => $this->document_style_id, // Huisstijl
            // 'workflow_id' => $this->workflow_id // Workflow
        ]]);

        if ($invoice->getStatusCode() == 201) {
            return $invoice;
        } else {
            return false;
        }
    }

    /**
     * Mark invoice as send
     *
     * @param string $invoice_id
     * @return boolean
     */
    public function sendSalesInvoice($invoice_id)
    {
        $invoice = $this->client->patch($this->base_url . '/sales_invoices/' . $invoice_id . '/send_invoice.json', ['sales_invoice_sending' => [
            'delivery_method' => 'Manual',
            'invoice_date' => date('Y-m-d')
        ]]);

        if ($invoice->getStatusCode() == 200) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Create payment for given invoice
     *
     * @param array $invoice
     * @return boolean
     */
    public function createSalesInvoicePayment($invoice)
    {
        $invoice = $this->client->post($this->base_url . '/sales_invoices/' . $invoice['id'] . '/payments.json', ['payment' => [
            'price' => $invoice['total_price_incl_tax'],
            'payment_date' => date('Y-m-d')
        ]]);

        if ($invoice->getStatusCode() == 201) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Get all sales invoices from contact
     *
     * @param string $contact_id
     * @return mixed
     */
    public function getSalesInvoicesFromContact($contact_id)
    {
        $invoices = $this->client->get($this->base_url . '/sales_invoices.json?filter=contact_id:'.$contact_id.',state:paid|open');
        if ($invoices->getStatusCode() == 200) {
            return $invoices;
        } else {
            return false;
        }
    }

    /**
     * Download sales invoice
     *
     * @param string $invoice_id
     * @return mixed
     */
    public function downloadSalesInvoice($invoice_id)
    {
        $invoice = $this->client->get($this->base_url . '/sales_invoices/'.$invoice_id.'/download_pdf.json');
        if ($invoice->getStatusCode() == 200) {
            return $invoice->getBody()->getContents();
        } else {
            return false;
        }
    }

    /**
     * Get all sales invoice tax rates
     *
     * @return array
     */
    public function getTaxRateIds()
    {
        $tax_rates = $this->client->get($this->base_url . '/tax_rates.json?filter=tax_rate_type:sales_invoice');
        if ($tax_rates->collect()->count() == 0) {
            return null;
        }
 
        $output = collect();
        foreach ($tax_rates->object() as $tax_rate) {
            $output->put($tax_rate->percentage, $tax_rate->id);
        }
        return $output;
    }

    /**
     * Create contact array
     *
     * @param User $user
     * @return array
     */
    protected function getContactData($user)
    {
        return ['contact' => [
            'firstname' => $user->name,
            'company_name' => $user->company,
            'address1' => $user->address,
            'address2' => $user->address2,
            'zipcode' => $user->postalcode,
            'city' => $user->city,
            'country' => $user->country,
            'tax_number' => $user->vat_id,
            'send_invoices_to_attention' => $user->name,
            'send_invoices_to_email' => $user->email,
            'send_estimates_to_attention' => $user->name,
            'send_estimates_to_email' => $user->email,
        ]];
    }
}
