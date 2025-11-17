<?php

use App\Http\Controllers\TemplateController;

Route::get('/plantillas', [TemplateController::class, 'Template'])
    ->name('template');

Route::get('/informcion', [TemplateController::class, 'EditorTemplate'])
    ->name('informcion');