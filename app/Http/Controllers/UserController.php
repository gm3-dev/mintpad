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
        $countries = config('countries');

        return view('users.profile')->with(compact('user', 'countries'));
    }

    /**
     * Update profile information
     *
     * @param Request $request
     * @return Response
     */
    public function update(Request $request)
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
        ];

        // Validate company info
        if ($request->has('is_company')) {
            $rules['company_name'] = ['required', 'string', 'max:255'];
            $rules['vat_id'] = ['required', 'string', 'max:255'];
            $rules['country'] = ['required', 'string', 'max:255'];
            $rules['city'] = ['required', 'string', 'max:255'];
            $rules['state'] = ['required', 'string', 'max:255'];
            $rules['postalcode'] = ['required', 'string', 'max:255'];
            $rules['address'] = ['required', 'string', 'max:255'];
        }

        $request->validate($rules);

        $user = User::find(Auth::user()->id);
        $user->name = $request->name;
        $user->is_company = $request->has('is_company');
        $user->company_name = $request->company_name ?? null;
        $user->vat_id = $request->vat_id ?? null;
        $user->country = $request->country ?? null;
        $user->city = $request->city ?? null;
        $user->state = $request->state ?? null;
        $user->postalcode = $request->postalcode ?? null;
        $user->address = $request->address ?? null;
        $user->address2 = $request->address2 ?? null;
        $user->save();

        Moneybird::updateContact($user);

        return redirect()->back()->with('success', 'Profile saved');
    }

    /**
     * List all user sales invoices
     *
     * @return Response
     */
    public function invoices()
    {
        $user = User::find(1);
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
