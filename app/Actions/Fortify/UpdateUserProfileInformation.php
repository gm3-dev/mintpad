<?php

namespace App\Actions\Fortify;

use App\Facades\Moneybird;
use App\Models\User;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Laravel\Fortify\Contracts\UpdatesUserProfileInformation;

class UpdateUserProfileInformation implements UpdatesUserProfileInformation
{
    /**
     * Validate and update the given user's profile information.
     *
     * @param  array<string, string>  $input
     */
    public function update(User $user, array $input): void
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'country' => ['required', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:255'],
            'state' => ['required', 'string', 'max:255'],
            'postalcode' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string', 'max:255'],
        ];

        // Validate company info
        if (request()->has('is_company')) {
            $rules['company_name'] = ['required', 'string', 'max:255'];
            $rules['vat_id'] = ['required', 'string', 'max:255'];
        }

        request()->validate($rules);

        $user = User::find(Auth::user()->id);
        $user->name = request()->name;
        $user->is_company = request()->has('is_company');
        $user->company_name = request()->company_name ?? null;
        $user->vat_id = request()->vat_id ?? null;
        $user->birthday = date('Y-m-d', strtotime(request()->birth_year.'-'.request()->birth_month.'-'.request()->birth_day));
        $user->country = request()->country ?? null;
        $user->city = request()->city ?? null;
        $user->state = request()->state ?? null;
        $user->postalcode = request()->postalcode ?? null;
        $user->address = request()->address ?? null;
        $user->save();

        Moneybird::updateContact($user);

        session()->flash('success', 'Profile saved');

        if (isset($input['email']) && $input['email'] !== $user->email && $user instanceof MustVerifyEmail) {
            $this->updateVerifiedUser($user, $input);
        }

        // Validator::make($input, [
        //     'name' => ['required', 'string', 'max:255'],

        //     'email' => [
        //         'required',
        //         'string',
        //         'email',
        //         'max:255',
        //         Rule::unique('users')->ignore($user->id),
        //     ],
        // ])->validateWithBag('updateProfileInformation');

        // if ($input['email'] !== $user->email &&
        //     $user instanceof MustVerifyEmail) {
        //     $this->updateVerifiedUser($user, $input);
        // } else {
        //     $user->forceFill([
        //         'name' => $input['name'],
        //         'email' => $input['email'],
        //     ])->save();
        // }
    }

    /**
     * Update the given verified user's profile information.
     *
     * @param  array<string, string>  $input
     */
    protected function updateVerifiedUser(User $user, array $input): void
    {
        $user->forceFill([
            'name' => $input['name'],
            'email' => $input['email'],
            'email_verified_at' => null,
        ])->save();

        $user->sendEmailVerificationNotification();
    }
}
