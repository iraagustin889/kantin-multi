<?php

namespace App\Modules\Kitchen\Services;

use Illuminate\Support\ServiceProvider;

final class KitchenServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // binding interface->implementation modul Kitchen, isi nanti kalau sudah ada
    }

    public function boot(): void
    {
        // route, event, atau bootstrap khusus modul Kitchen, isi nanti
    }
}
