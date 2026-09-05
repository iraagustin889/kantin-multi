<?php

use Illuminate\Support\Facades\Route;

/**
 * Konteks OPERATOR TENANT (internal, scoped ke satu tenant lewat {tenant:slug}).
 * Prefix: tenant/{tenant:slug}, name: tenant.*
 * Middleware auth+verified dari web.php; scopeBindings() membatasi child model ke tenant induk.
 */
Route::get('/dashboard', fn () => view('tenant.dashboard'))->name('dashboard');
