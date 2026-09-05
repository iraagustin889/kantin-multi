<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

final class AdminServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // binding interface->implementation modul Admin, isi nanti kalau sudah ada
    }

    public function boot(): void
    {
        // route, event, atau bootstrap khusus modul Admin, isi nanti
    }
}
