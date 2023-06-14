<?php

namespace App\Actions\Fortify;

use App\Facades\Slack;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
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
        ];

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
