<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WelcomeController;
use App\Http\Controllers\PortfolioPdfController;
use App\Http\Controllers\TemplateController;
use App\Http\Controllers\CommunityController;
use App\Http\Controllers\CommunityCommentController;
use App\Http\Controllers\ToolsController;
use App\Http\Controllers\PortfolioSharingController;
use App\Http\Controllers\DashboardController;
use Inertia\Inertia;

// Ruta de diagnóstico para NODE (Solo temporal/debug)
Route::post('/two-factor-challenge', [App\Http\Controllers\TwoFactorController::class, 'store'])
    ->middleware(['guest:'.config('fortify.guard')])
    ->name('two-factor-email.verify');
    
Route::get('/debug-node', function () {
    $nodePath = exec('which node');
    $npmPath = exec('which npm');
    $version = exec('node -v');
    
    return [
        'node_path' => $nodePath ?: 'No encontrado (Prueba buscando en /usr/local/bin/node o ~/.nvm)',
        'npm_path' => $npmPath ?: 'No encontrado',
        'node_version' => $version ?: 'No se pudo ejecutar',
        'can_run_process' => function_exists('proc_open') ? 'Sí' : 'No (Browsershot fallará - proc_open deshabilitado en php.ini)'
    ];
});

Route::get('/', [WelcomeController::class, 'index'])->name('home');

Route::middleware(['auth', 'verified'])
    ->prefix('dashboard')
    ->name('dashboard.')
    ->group(function () {
        
        Route::get('/', [DashboardController::class, 'index'])->name('index');

        // Ayuda
        Route::get('/ayuda', function () {
            return Inertia::render('Dashboard/Help');
        })->name('help');
        
        // Comunidad (Mini-Foro)
        Route::get('/comunidad', [CommunityController::class, 'dashboardIndex'])->name('community');
        Route::post('/comunidad', [CommunityController::class, 'store'])->name('community.store');
        Route::get('/comunidad/{id}', [CommunityController::class, 'show'])->name('community.show');
        Route::delete('/comunidad/{id}', [CommunityController::class, 'destroy'])->name('community.destroy');
        Route::post('/comunidad/{id}/calificar', [CommunityController::class, 'rate'])->name('community.rate');
        Route::post('/comunidad/{postId}/comentar', [CommunityCommentController::class, 'store'])->name('community.comment.store');
        Route::post('/comunidad/comentarios/{comment}', [CommunityCommentController::class, 'update'])->name('community.comment.update');
        Route::delete('/comunidad/comentarios/{comment}', [CommunityCommentController::class, 'destroy'])->name('community.comment.destroy');

        // Herramientas (Tools)
        Route::get('/herramientas', [ToolsController::class, 'index'])->name('tools');
        Route::post('/herramientas/analyze-skills', [ToolsController::class, 'analyzeSkillsMatch'])->name('tools.analyze-skills');
        Route::post('/herramientas/interview-questions', [ToolsController::class, 'generateInterviewQuestions'])->name('tools.interview-questions');
        Route::post('/herramientas/ats-scan', [ToolsController::class, 'scanATS'])->name('tools.ats-scan');
        Route::post('/herramientas/interview-interaction', [ToolsController::class, 'interviewInteraction'])->name('tools.interview-interaction');

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

// RUTA PÚBLICA DE COMUNIDAD
Route::get('/comunidad', [CommunityController::class, 'index'])
    ->name('community.index');

// RUTAS PÚBLICAS DE ENLACES COMPARTIDOS
Route::get('/share/{token}', [PortfolioSharingController::class, 'viewShared'])
    ->name('portfolio.share.view');

Route::put('/share/{token}', [PortfolioSharingController::class, 'updateShared'])
    ->name('portfolio.share.update-shared');


require __DIR__.'/settings.php';
require __DIR__.'/google_auth.php';
require __DIR__.'/admin.php';
