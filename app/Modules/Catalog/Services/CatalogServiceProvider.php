<?php

namespace App\Modules\Catalog\Services;

use Illuminate\Support\ServiceProvider;

final class CatalogServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // binding interface->implementation modul Catalog, isi nanti kalau sudah ada
    }

    public function boot(): void
    {
        // route, event, atau bootstrap khusus modul Catalog, isi nanti
    }
}
