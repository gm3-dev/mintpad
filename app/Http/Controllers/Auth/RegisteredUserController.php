<?php

namespace App\Http\Controllers\Auth;

use App\Facades\Moneybird;
use App\Facades\Slack;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $countries = config('countries');
        return view('auth.register')->with(compact('countries'));
    }

    /**
     * Handle an incoming registration request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        $rules = [
            'accept_tos' => ['required'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'country' => ['required', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:255'],
            'state' => ['required', 'string', 'max:255'],
            'postalcode' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string', 'max:255'],
        ];

        // Validate company info
        if ($request->has('is_company')) {
            $rules['company_name'] = ['required', 'string', 'max:255'];
            $rules['vat_id'] = ['required', 'string', 'max:255'];
        }

        $request->validate($rules, [
            'accept_tos.required' => 'Accepting the Terms of Service is required'
        ]);

        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->role = 'user';
        $user->password = Hash::make($request->password);
        $user->is_company = $request->has('is_company');
        $user->company_name = $request->company_name ?? null;
        $user->vat_id = $request->vat_id ?? null;
        $user->country = $request->country ?? null;
        $user->city = $request->city ?? null;
        $user->state = $request->state ?? null;
        $user->postalcode = $request->postalcode ?? null;
        $user->address = $request->address ?? null;
        $user->address2 = $request->address2 ?? null;

        // Set Moneybird ID
        $moneybird_id = Moneybird::createContact($user);
        if ($moneybird_id !== false) {
            $user->moneybird_id = $moneybird_id;
        }

        $user->save();

        Auth::login($user);

        Slack::send('#registrations', $user->name . ' joined Mintpad!');

        return redirect(RouteServiceProvider::HOME);
    }
}
