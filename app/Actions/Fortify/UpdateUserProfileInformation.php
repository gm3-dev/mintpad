<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
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
        ];

        if (request()->new_password !== null) {
            $rules['new_password'] = ['required', 'confirmed', Rules\Password::defaults()];
        }

        request()->validate($rules);

        $user = User::find(Auth::user()->id);
        $user->name = request()->name;
        if (request()->get('new_password') !== null) {
            $user->password = Hash::make(request()->new_password);
        }
        $user->save();

        session()->flash('success', 'Profile saved');

        if (isset($input['email']) && $input['email'] !== $user->email && $user instanceof MustVerifyEmail) {
            $this->updateVerifiedUser($user, $input);
        }
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
