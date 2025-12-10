<?php

use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminUserController;
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
        
        // Configuración del sistema (solo frontend por ahora)
        Route::get('/settings', function () {
            return Inertia::render('Admin/Settings');
        })->name('settings');
    });

