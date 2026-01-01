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
        Route::get('/export', [AdminDashboardController::class, 'export'])->name('export');
        
        // Gestión de usuarios (CRUD)
        Route::resource('users', AdminUserController::class)->except(['create', 'store']);
        
        // Reportes
        Route::get('/reports', [AdminDashboardController::class, 'reports'])->name('reports');
        
        // Configuración del sistema (solo frontend por ahora)
        Route::get('/settings', function () {
            $sections = \App\Models\LandingPageSection::all()->keyBy('key');
            return Inertia::render('Admin/Settings', [
                'sections' => $sections
            ]);
        })->name('settings');

        // Landing Page CMS
        Route::get('/landing', [\App\Http\Controllers\Admin\AdminLandingPageController::class, 'index'])->name('landing.index');
        Route::post('/landing/{key}', [\App\Http\Controllers\Admin\AdminLandingPageController::class, 'update'])->name('landing.update');
    });

