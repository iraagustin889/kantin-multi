<?php

namespace App\Modules\Reporting\Services;

use Illuminate\Support\ServiceProvider;

final class ReportingServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // binding interface->implementation modul Reporting, isi nanti kalau sudah ada
    }

    public function boot(): void
    {
        // route, event, atau bootstrap khusus modul Reporting, isi nanti
    }
}
