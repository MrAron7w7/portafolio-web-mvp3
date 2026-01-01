<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PortfolioPdfController;
use App\Http\Controllers\TemplateController;
use Inertia\Inertia;
use Laravel\Fortify\Features;

use App\Http\Controllers\WelcomeController;

Route::get('/', [WelcomeController::class, 'index'])->name('home');

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
        Route::post('/comunidad/{id}/calificar', [\App\Http\Controllers\CommunityController::class, 'rate'])->name('community.rate');
        Route::post('/comunidad/{postId}/comentar', [\App\Http\Controllers\CommunityCommentController::class, 'store'])->name('community.comment.store');
        Route::post('/comunidad/comentarios/{comment}', [\App\Http\Controllers\CommunityCommentController::class, 'update'])->name('community.comment.update');
        Route::delete('/comunidad/comentarios/{comment}', [\App\Http\Controllers\CommunityCommentController::class, 'destroy'])->name('community.comment.destroy');

        // Herramientas (Tools)
        Route::get('/herramientas', [\App\Http\Controllers\ToolsController::class, 'index'])->name('tools');
        Route::post('/herramientas/analyze-skills', [\App\Http\Controllers\ToolsController::class, 'analyzeSkillsMatch'])->name('tools.analyze-skills');
        Route::post('/herramientas/interview-questions', [\App\Http\Controllers\ToolsController::class, 'generateInterviewQuestions'])->name('tools.interview-questions');
        Route::post('/herramientas/ats-scan', [\App\Http\Controllers\ToolsController::class, 'scanATS'])->name('tools.ats-scan');
        Route::post('/herramientas/interview-interaction', [\App\Http\Controllers\ToolsController::class, 'interviewInteraction'])->name('tools.interview-interaction');

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

// Ruta pública para EDITAR portafolio por slug (requiere permiso view_edit)
Route::get('/p/{slug}/edit', [TemplateController::class, 'editPublicBySlug'])
    ->name('portfolio.public.edit');

// Ruta pública para GUARDAR cambios del editor público
Route::put('/p/{slug}', [TemplateController::class, 'updatePublicBySlug'])
    ->name('portfolio.public.update');

// ==========================================
// RUTAS PÚBLICAS DE ENLACES COMPARTIDOS
// ==========================================
use App\Http\Controllers\PortfolioSharingController;

// Ver portafolio vía token compartido (acceso público o restringido según config)
Route::get('/share/{token}', [PortfolioSharingController::class, 'viewShared'])
    ->name('portfolio.share.view');

// Actualizar portafolio vía token (solo si tiene permiso view_edit)
Route::put('/share/{token}', [PortfolioSharingController::class, 'updateShared'])
    ->name('portfolio.share.update-shared');

// ==========================================
// RUTA PÚBLICA DE COMUNIDAD
// ==========================================
Route::get('/comunidad', [\App\Http\Controllers\CommunityController::class, 'index'])
    ->name('community.index');

require __DIR__.'/settings.php';
require __DIR__.'/google_auth.php';
require __DIR__.'/admin.php';
