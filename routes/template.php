<?php

use App\Http\Controllers\TemplateController;
use Illuminate\Support\Facades\Route;

Route::get('/plantillas', [TemplateController::class, 'Template'])
    ->name('template');

Route::post('/portfolio/crear', [TemplateController::class, 'createPortfolio'])
    ->name('portfolio.create');

Route::get('/portfolio/{portfolio}/editor', [TemplateController::class, 'EditorTemplate'])
    ->name('portfolio.editor');

Route::put('/portfolio/{portfolio}', [TemplateController::class, 'updatePortfolio'])
    ->name('portfolio.update');

Route::get('/portfolio/{portfolio}/view', [TemplateController::class, 'viewPortfolio'])
    ->name('portfolio.view');

Route::delete('/portfolio/{portfolio}', [TemplateController::class, 'deletePortfolio'])
    ->name('portfolio.delete');