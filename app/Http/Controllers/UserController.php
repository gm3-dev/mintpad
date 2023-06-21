<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

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

        return Inertia::render('Users/Profile', compact('user'));
    }

    /**
     * Manage 2FA settings
     *
     * @return Response
     */
    public function twoFactorAuthSettings()
    {
        $user = Auth::user();
        if (session('status') == 'two-factor-authentication-enabled') {
            $user->twoFactorQrCodeSvg = $user->twoFactorQrCodeSvg();
            $user->recoveryCodes = $user->recoveryCodes();
        }
        return Inertia::render('Users/TwoFactorAuthentication', compact('user'));
    }
}
