<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PortfolioPdfController;
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
        
        // Comunidad (Mini-Foro)
        Route::get('/comunidad', [\App\Http\Controllers\CommunityController::class, 'dashboardIndex'])->name('community');
        Route::post('/comunidad', [\App\Http\Controllers\CommunityController::class, 'store'])->name('community.store');

        // Incluir otras rutas del dashboard
        require __DIR__.'/template.php';
        require __DIR__.'/editor.php';
    });

// ruta pública pero protegida por firma (signed)
Route::get('/print/portfolio/{id}', [PortfolioPdfController::class, 'renderForPdf'])
    ->name('portfolio.print.view')
    ->middleware('signed'); 

// ruta de descarga
Route::get('/portfolio/{id}/download-pdf', [PortfolioPdfController::class, 'download'])
    ->name('portfolio.download.pdf');

// ==========================================
// RUTA PÚBLICA DE COMUNIDAD
// ==========================================
Route::get('/comunidad', [\App\Http\Controllers\CommunityController::class, 'index'])
    ->name('community.index');

require __DIR__.'/settings.php';
require __DIR__.'/google_auth.php';
require __DIR__.'/admin.php';
