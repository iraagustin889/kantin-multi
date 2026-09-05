<?php

namespace App\Modules\Payments\Services;

use Illuminate\Support\ServiceProvider;

final class PaymentsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // binding interface->implementation modul Payment, isi nanti kalau sudah ada
    }

    public function boot(): void
    {
        // route, event, atau bootstrap khusus modul Payment, isi nanti
    }
}
