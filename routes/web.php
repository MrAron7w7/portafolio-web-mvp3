<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PortfolioPdfController;
use App\Http\Controllers\TemplateController;
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
        // Comunidad (Mini-Foro)
        Route::get('/comunidad', [\App\Http\Controllers\CommunityController::class, 'dashboardIndex'])->name('community');
        Route::post('/comunidad', [\App\Http\Controllers\CommunityController::class, 'store'])->name('community.store');
        Route::get('/comunidad/{id}', [\App\Http\Controllers\CommunityController::class, 'show'])->name('community.show');
        Route::delete('/comunidad/{id}', [\App\Http\Controllers\CommunityController::class, 'destroy'])->name('community.destroy');
        Route::post('/comunidad/{postId}/comentar', [\App\Http\Controllers\CommunityCommentController::class, 'store'])->name('community.comment.store');
        Route::put('/comunidad/comentarios/{comment}', [\App\Http\Controllers\CommunityCommentController::class, 'update'])->name('community.comment.update');
        Route::delete('/comunidad/comentarios/{comment}', [\App\Http\Controllers\CommunityCommentController::class, 'destroy'])->name('community.comment.destroy');

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

// Ruta pública para ver portafolio por slug
Route::get('/p/{slug}', [TemplateController::class, 'viewPublicBySlug'])
    ->name('portfolio.public.view');

// ==========================================
// RUTA PÚBLICA DE COMUNIDAD
// ==========================================
Route::get('/comunidad', [\App\Http\Controllers\CommunityController::class, 'index'])
    ->name('community.index');

require __DIR__.'/settings.php';
require __DIR__.'/google_auth.php';
require __DIR__.'/admin.php';
