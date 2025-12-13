<?php

use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\AdminSettingController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
|
| Rutas exclusivas para administradores
| Middleware: auth + verified + role:admin
|
*/

Route::middleware(['auth', 'verified', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        
        // Dashboard de admin
        Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');
        
        // Gestión de usuarios (CRUD)
        Route::resource('users', AdminUserController::class)->except(['create', 'store']);
        
        // Reportes
        Route::get('/reports', [AdminDashboardController::class, 'reports'])->name('reports');
        
        // Configuración del Sistema
        Route::get('/settings', [AdminSettingController::class, 'index'])->name('settings.index');
        Route::post('/settings', [AdminSettingController::class, 'update'])->name('settings.update');
        Route::post('/settings/reset', [AdminSettingController::class, 'reset'])->name('settings.reset');
    });

