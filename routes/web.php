<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

// Route pelanggan: anonim, TANPA middleware auth/verified
Route::prefix('kantin/{canteen:slug}')
    ->name('customer.')
    ->group(base_path('routes/customer.php'));

Route::middleware(['auth', 'verified'])->group(function (): void {

    Route::view('dashboard', 'dashboard')->name('dashboard');

    Route::prefix('tenant/{tenant:slug}')
        ->scopeBindings()
        ->name('tenant.')
        ->group(base_path('routes/tenant.php'));

    Route::prefix('admin')
        ->name('admin.')
        ->group(base_path('routes/admin.php'));

});

require __DIR__.'/settings.php';
