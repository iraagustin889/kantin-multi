<?php

use App\Modules\Admin\Services\AdminServiceProvider;
use App\Modules\Catalog\Services\CatalogServiceProvider;
use App\Modules\Kitchen\Services\KitchenServiceProvider;
use App\Modules\Ordering\Services\OrderingServiceProvider;
use App\Modules\Payments\Services\PaymentsServiceProvider;
use App\Modules\Reporting\Services\ReportingServiceProvider;
use App\Providers\AppServiceProvider;
use App\Providers\FortifyServiceProvider;

return [
    AppServiceProvider::class,
    FortifyServiceProvider::class,
    AdminServiceProvider::class,
    CatalogServiceProvider::class,
    OrderingServiceProvider::class,
    PaymentsServiceProvider::class,
    KitchenServiceProvider::class,
    ReportingServiceProvider::class,
];
