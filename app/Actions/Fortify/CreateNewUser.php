<?php

namespace App\Actions\Fortify;

use App\Facades\Moneybird;
use App\Facades\Slack;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        $rules = [
            'accept_tos' => ['required', 'accepted'],
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
        if (request()->is_company == '1') {
            $rules['company_name'] = ['required', 'string', 'max:255'];
            $rules['vat_id'] = ['required', 'string', 'max:255'];
        }

        request()->validate($rules, [
            'accept_tos.required' => 'Accepting the Terms of Service is required',
            'accept_tos.accepted' => 'Accepting the Terms of Service is required'
        ]);

        $user = new User();
        $user->name = request()->name;
        $user->email = request()->email;
        $user->role = 'user';
        $user->status = 'active';
        $user->password = Hash::make(request()->password);
        $user->is_company = request()->is_company;
        $user->company_name = request()->company_name ?? null;
        $user->vat_id = request()->vat_id ?? null;
        $user->birthday = date('Y-m-d', strtotime(request()->birth_year.'-'.request()->birth_month.'-'.request()->birth_day));
        $user->country = request()->country ?? null;
        $user->city = request()->city ?? null;
        $user->state = request()->state ?? null;
        $user->postalcode = request()->postalcode ?? null;
        $user->address = request()->address ?? null;
        $user->reference = request()->reference ?? null;

        // Set Moneybird ID
        $moneybird_id = Moneybird::createContact($user);
        if ($moneybird_id !== false) {
            $user->moneybird_id = $moneybird_id;
        }

        // Check affiliate code
        if (request()->affiliate != '') {
            $affiliate = User::where('role', 'affiliate')->where('affiliate_code', request()->affiliate)->first();
            if ($affiliate) {
                $user->affiliate_id = $affiliate->id;
            }
        }

        $user->save();

        Slack::send('#registrations', '`'.$user->name . '` joined Mintpad!');

        return $user;
    }
}
