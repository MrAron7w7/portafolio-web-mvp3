<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class TwoFactorSettingsController extends Controller
{
    /**
     * Enable two-factor authentication for the user.
     */
    public function enable(Request $request)
    {
        $request->validate([
            'password' => ['required', 'string'],
        ]);

        if (!Hash::check($request->password, $request->user()->password)) {
            throw ValidationException::withMessages([
                'password' => ['La contraseña es incorrecta.'],
            ]);
        }

        $user = $request->user();
        
        // Set a placeholder secret (we don't actually use TOTP, but Fortify checks this field)
        $user->two_factor_secret = encrypt('email-based-2fa');
        $user->two_factor_confirmed_at = now();
        $user->save();

        return back()->with('status', '2fa-enabled');
    }

    /**
     * Disable two-factor authentication for the user.
     */
    public function disable(Request $request)
    {
        $request->validate([
            'password' => ['required', 'string'],
        ]);

        if (!Hash::check($request->password, $request->user()->password)) {
            throw ValidationException::withMessages([
                'password' => ['La contraseña es incorrecta.'],
            ]);
        }

        $user = $request->user();
        
        $user->two_factor_secret = null;
        $user->two_factor_confirmed_at = null;
        $user->two_factor_recovery_codes = null;
        $user->save();

        return back()->with('status', '2fa-disabled');
    }
}
