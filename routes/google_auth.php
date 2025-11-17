<?php

use App\Http\Controllers\GoogleAuth;
use Illuminate\Support\Facades\Route;
use Laravel\Socialite\Facades\Socialite;

Route::get('/login/google', function () {
    return Socialite::driver('google')->redirect();
})->name('google.login');

Route::get('/login/google/callback', [GoogleAuth::class, 'GoogleAuth'])->name('google.callback');