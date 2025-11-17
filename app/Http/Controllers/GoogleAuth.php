<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuth extends Controller
{
    public function GoogleAuth()
    {
        try {
        $googleUser = Socialite::driver('google')->user();

        // Buscamos si existe
        $user = User::where('email', $googleUser->getEmail())->first();

        // Si no existe creamos
        if(!$user) {
            $user = User::create([
                'name' => $googleUser->getName(),
                'email' => $googleUser->getEmail(),
                'password' => bcrypt(uniqid()) // Contraseña aleatoria
            ]);
        }

        // Iniciamos sesión
        Auth::login($user);

        // Redirigimos al inicio
        return redirect()->route('index');

    } catch (\Throwable $th) {
        // Log del error para debugging
        \Log::error('Google login error: ' . $th->getMessage());
        return redirect('/login')->with('error', 'Error al iniciar sesión con Google');
    }
    }
}
