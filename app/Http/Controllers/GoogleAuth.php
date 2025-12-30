<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Log;

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
                // Separar nombre y apellido
                $fullName = $googleUser->getName();
                $parts = explode(' ', $fullName, 2);
                $firstName = $parts[0] ?? explode('@', $googleUser->getEmail())[0]; // Fallback to email prefix
                $lastName = $parts[1] ?? '';

                $user = User::create([
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'email' => $googleUser->getEmail(),
                    'password' => bcrypt(uniqid()), // Contraseña aleatoria
                    'avatar_url' => $googleUser->getAvatar(),
                    'email_verified_at' => now(), // Auto-verificar correos de Google
                ]);
                
                // Asignar rol por defecto si usas Spatie Permissions
                // $user->assignRole('user'); 
            } else {
                // Si existe, actualizamos avatar si no tiene
                if (empty($user->avatar_url)) {
                    $user->update(['avatar_url' => $googleUser->getAvatar()]);
                }
            }

            // Iniciamos sesión
            Auth::login($user);

            // Redirigimos al dashboard
            return redirect()->route('dashboard.index');

        } catch (\Throwable $th) {
            // Log del error para debugging
            Log::error('Google login error: ' . $th->getMessage());
            return redirect('/login')->with('error', 'Error al iniciar sesión con Google');
        }
    }
}
