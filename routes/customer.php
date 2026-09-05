<?php

use Illuminate\Support\Facades\Route;

/**
 * Konteks PELANGGAN (publik, anonim — tanpa auth/verified).
 * Prefix: kantin/{canteen:slug}, name: customer.*
 * JANGAN pakai <x-layouts::app> di sini — guest akan crash karena $auth->user()->name null.
 */
Route::get('/', fn () => view('customer.home'))->name('home');
