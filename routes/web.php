<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;
use App\Http\Controllers\PortfolioPdfController;

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
        
        Route::get('/', function () {
            return Inertia::render('Dashboard/Dashboard');
        })->name('index'); // Cambiado a 'index'

        // Incluir otras rutas del dashboard
        require __DIR__.'/template.php';
        require __DIR__.'/editor.php';
    });

Route::get('/test-pdf/{id}', [PortfolioPdfController::class, 'download']);

require __DIR__.'/settings.php';
require __DIR__.'/google_auth.php';
