<?php

namespace App\Http\Controllers;

use App\Facades\Moneybird;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class UserController extends Controller
{
    /**
     * Profile page
     *
     * @return Response
     */
    public function profile()
    {
        $user = Auth::user();
        $countries = collect(config('countries'))->map(function ($country) {
            return $country['full'];
        });
        $user->birth_day = $user->birthday->day ?? date('d');
        $user->birth_month = $user->birthday->month ?? date('n');
        $user->birth_year = $user->birthday->year ?? date('Y');

        return view('users.profile')->with(compact('user', 'countries'));
    }

    /**
     * List all user sales invoices
     *
     * @return Response
     */
    public function invoices()
    {
        $user = Auth::user();
        if ($invoices = Moneybird::getSalesInvoicesFromContact($user->moneybird_id)) {
            $invoices = $invoices->collect();
        } else {
            $invoices = collect();
        }

        return view('users.invoices')->with(compact('invoices'));
    }

    /**
     * Download sales invoice
     *
     * @param string $invoice_id
     * @return Response
     */
    public function download($invoice_id)
    {
        $invoice = Moneybird::downloadSalesInvoice($invoice_id);
        if ($invoice !== false) {
            $headers = [
                'Content-Type: application/pdf',
            ];
  
            return response()->streamDownload(function () use ($invoice) {
                echo $invoice;
            }, 'mintpad-'.$invoice_id.'.pdf', $headers);
        }
    }
}
