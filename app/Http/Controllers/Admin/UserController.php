<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function index()
    {
        $users = User::orderBy('id', 'DESC')->get();
        return view('admin.users.index')->with(compact('users'));
    }

    public function edit(User $user)
    {
        $countries = collect(config('countries'))->map(function ($country) {
            return $country['full'];
        });
        $user->birth_day = $user->birthday->day ?? date('d');
        $user->birth_month = $user->birthday->month ?? date('n');
        $user->birth_year = $user->birthday->year ?? date('Y');
        $roles = config('roles');
        return view('admin.users.edit')->with(compact('user', 'countries', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        if ($request->role == 'affiliate' && $user->affiliate_code == null) {
            do {
                $affiliate_code = Str::random(15);
                $affiliate_code_check = User::where('affiliate_code', $affiliate_code)->first();
            } while ($affiliate_code_check);
            $user->affiliate_code = $affiliate_code;
        }
        $user->role = $request->role;
        $user->status = $request->status;
        $user->save();

        return redirect()->back()->with('success', 'User saved');
    }
}
