<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;
use App\Http\Controllers\PortfolioPdfController;
use App\Http\Controllers\TemplateController;

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


// ruta para que descargue el pdf
Route::get('/portfolio/{id}/download-pdf', [PortfolioPdfController::class, 'download'])
    ->name('portfolio.download.pdf');


// guardado
Route::put('/portfolio/{id}', [TemplateController::class, 'updatePortfolio'])
    ->name('portfolio.update');

require __DIR__.'/settings.php';
require __DIR__.'/google_auth.php';
