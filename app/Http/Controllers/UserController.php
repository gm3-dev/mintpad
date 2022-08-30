<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class UserController extends Controller
{
    public function profile()
    {
        $user = Auth::user();
        $countries = config('countries');

        return view('users.profile')->with(compact('user', 'countries'));
    }

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

        return redirect()->back()->with('success', 'Profile saved');
    }
}
