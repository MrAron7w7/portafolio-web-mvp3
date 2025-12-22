<?php

use App\Http\Controllers\TemplateController;
use App\Http\Controllers\IaAutoCompleteController;
use App\Http\Controllers\PortfolioSharingController;
use Illuminate\Support\Facades\Route;

Route::get('/plantillas', [TemplateController::class, 'Template'])
    ->name('template');

Route::post('/portfolio/crear', [TemplateController::class, 'createPortfolio'])
    ->name('portfolio.create');

Route::get('/portfolio/{portfolio}/editor', [TemplateController::class, 'EditorTemplate'])
    ->name('portfolio.editor');

Route::put('/portfolio/{portfolio}', [TemplateController::class, 'updatePortfolio'])
    ->name('portfolio.update');

Route::patch('/portfolio/{portfolio}/title', [TemplateController::class, 'updateTitle'])
    ->name('portfolio.update-title');

Route::get('/portfolio/{portfolio}/view', [TemplateController::class, 'viewPortfolio'])
    ->name('portfolio.view');

Route::delete('/portfolio/{portfolio}', [TemplateController::class, 'deletePortfolio'])
    ->name('portfolio.delete');

Route::post('/ia/auto-complete', [IaAutoCompleteController::class, 'generateSummary'])
    ->name('ia.auto-complete');

// ========================================
// RUTAS DEL SISTEMA DE COMPARTIR
// ========================================

// Generar enlace compartido
Route::post('/portfolio/{portfolio}/share/generate', [PortfolioSharingController::class, 'generateLink'])
    ->name('portfolio.share.generate');

// Desactivar enlace (volver a privado)
Route::post('/portfolio/{portfolio}/share/disable', [PortfolioSharingController::class, 'disableLink'])
    ->name('portfolio.share.disable');

// Obtener estado actual de compartir
Route::get('/portfolio/{portfolio}/share/status', [PortfolioSharingController::class, 'getStatus'])
    ->name('portfolio.share.status');

// Actualizar configuración completa de compartir
Route::put('/portfolio/{portfolio}/share', [PortfolioSharingController::class, 'updateSharingConfig'])
    ->name('portfolio.share.update');

// Cambiar permiso del enlace (view ↔ view_edit)
Route::patch('/portfolio/{portfolio}/share/permission', [PortfolioSharingController::class, 'updatePermission'])
    ->name('portfolio.share.permission');

// Cambiar tipo de audiencia
Route::patch('/portfolio/{portfolio}/share/audience', [PortfolioSharingController::class, 'updateAudienceType'])
    ->name('portfolio.share.audience');

// Gestión de correos autorizados
Route::post('/portfolio/{portfolio}/share/emails', [PortfolioSharingController::class, 'addAuthorizedEmail'])
    ->name('portfolio.share.emails.add');

Route::delete('/portfolio/{portfolio}/share/emails/{email}', [PortfolioSharingController::class, 'removeAuthorizedEmail'])
    ->name('portfolio.share.emails.remove');