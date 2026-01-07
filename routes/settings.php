<?php

use App\Http\Controllers\Settings\PasswordController;
use App\Http\Controllers\Settings\ProfileController;
use App\Http\Controllers\Settings\GeneralController;
use App\Http\Controllers\Settings\TwoFactorAuthenticationController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::middleware('auth')->group(function () {
    Route::redirect('settings', '/settings/general');

    // General Settings (Principal)
    Route::get('settings/general', [GeneralController::class, 'edit'])->name('settings.general');
    Route::patch('settings/general', [GeneralController::class, 'update'])->name('settings.general.update');
    Route::post('settings/general', [GeneralController::class, 'update'])->name('settings.general.update.post'); // Para archivos
    Route::post('settings/general/remove-avatar', [GeneralController::class, 'removeAvatar'])->name('settings.general.remove-avatar');
    Route::delete('settings/general', [GeneralController::class, 'destroy'])->name('settings.general.destroy');

    // Profile
    Route::get('settings/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('settings/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('settings/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Password
    Route::get('settings/password', [PasswordController::class, 'edit'])->name('user-password.edit');
    Route::put('settings/password', [PasswordController::class, 'update'])
        ->middleware('throttle:6,1')
        ->name('settings.password.update');

    // Appearance
    Route::get('settings/appearance', function () {
        return Inertia::render('settings/Appearance');
    })->name('appearance.edit');

    // Two Factor
    Route::get('settings/two-factor', [TwoFactorAuthenticationController::class, 'show'])
        ->name('two-factor.show');
    
    // Custom 2FA Enable/Disable (for email-based 2FA)
    Route::post('settings/two-factor/enable', [\App\Http\Controllers\Settings\TwoFactorSettingsController::class, 'enable'])
        ->name('settings.two-factor.enable');
    Route::delete('settings/two-factor/disable', [\App\Http\Controllers\Settings\TwoFactorSettingsController::class, 'disable'])
        ->name('settings.two-factor.disable');

    // Subscription
    Route::get('settings/subscription', function () {
        return Inertia::render('settings/Subscription');
    })->name('settings.subscription');

    // Domain
    Route::get('settings/domain', function () {
        return Inertia::render('settings/Domain');
    })->name('settings.domain');

    // Integrations
    Route::get('settings/integrations', function () {
        return Inertia::render('settings/Integrations');
    })->name('settings.integrations');
});