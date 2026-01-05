<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Fortify\Http\Requests\TwoFactorLoginRequest;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class TwoFactorController extends Controller
{
    /**
     * Attempt to authenticate a new session using the two factor authentication code.
     *
     * @param  \Laravel\Fortify\Http\Requests\TwoFactorLoginRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(TwoFactorLoginRequest $request)
    {
        $user = $request->challengedUser();

        // Check Email Code
        $code = $request->input('code');
        $cachedCode = Cache::get('2fa_code_' . $user->id);

        if ($code && $cachedCode && $code == $cachedCode) {
            // Code is valid!
            /** @var \Illuminate\Auth\SessionGuard $guard */
            $guard = Auth::guard();
            $guard->login($user, $request->remember());
            $request->session()->regenerate();
            Cache::forget('2fa_code_' . $user->id); // Clear code

            return redirect()->intended('/dashboard');
        }

        // Email code validation failed
        throw ValidationException::withMessages([
            'code' => [__('The provided two factor authentication code was invalid.')],
        ]);
    }
}
