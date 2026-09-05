<?php

namespace App\Modules\Ordering\Services;

use Illuminate\Support\ServiceProvider;

final class OrderingServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // binding interface->implementation modul Ordering, isi nanti kalau sudah ada
    }

    public function boot(): void
    {
        // route, event, atau bootstrap khusus modul Ordering, isi nanti
    }
}
