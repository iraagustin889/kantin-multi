<?php

use Illuminate\Support\Facades\Route;

/**
 * Konteks PENGELOLA KANTIN (internal, global — bukan per-canteen).
 * Prefix: admin, name: admin.*
 * Middleware auth+verified dari web.php; role:admin ditambahkan di modul otorisasi nanti.
 */
Route::get('/dashboard', fn () => view('admin.dashboard'))->name('dashboard');
