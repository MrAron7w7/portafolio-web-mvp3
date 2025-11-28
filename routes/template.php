<?php

use App\Http\Controllers\TemplateController;

Route::get('/plantillas', [TemplateController::class, 'Template'])
    ->name('template');

Route::post('/portfolio/crear', [TemplateController::class, 'createPortfolio'])
    ->name('portfolio.create');

Route::get('/portfolio/{portfolio}/editor', [TemplateController::class, 'EditorTemplate'])
    ->name('portfolio.editor');

Route::put('/portfolio/{portfolio}', [TemplateController::class, 'updatePortfolio'])
    ->name('portfolio.update');