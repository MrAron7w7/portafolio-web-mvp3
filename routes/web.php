<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;

Route::get('/', function () {

    $user = Auth::user();

    if ($user) {
        return redirect()->route('dashboard.index');
    }

    return Inertia::render('Home/Home', [
        'canRegister' => Features::enabled(Features::registration()),
    ]);
})->name('home');

Route::middleware(['auth', 'verified'])
    ->prefix('dashboard')
    ->name('dashboard.')
    ->group(function () {
        
        Route::get('/', [\App\Http\Controllers\DashboardController::class, 'index'])->name('index');

        // Incluir otras rutas del dashboard
        require __DIR__.'/template.php';
        require __DIR__.'/editor.php';
    });

require __DIR__.'/settings.php';
require __DIR__.'/google_auth.php';
