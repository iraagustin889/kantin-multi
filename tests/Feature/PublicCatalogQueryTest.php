<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Canteen;
use App\Models\Menu;
use App\Models\Tenant;
use App\Modules\Catalog\Services\PublicCatalogQuery;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Tahap 7 — item 2:
 * "Tambahkan test yang memastikan query katalog publik tidak membocorkan
 * data antarkantin."
 *
 * Ini melengkapi verifikasi manual via tinker di Tahap 6 dengan test yang
 * dijalankan otomatis tiap kali test suite jalan (regression-proof).
 */
class PublicCatalogQueryTest extends TestCase
{
    use RefreshDatabase;

    public function test_catalog_only_returns_menus_from_the_requested_canteen(): void
    {
        $canteenA = Canteen::factory()->create(['status' => 'active']);
        $canteenB = Canteen::factory()->create(['status' => 'active']);

        $tenantInA1 = Tenant::factory()->for($canteenA)->create(['status' => 'active']);
        $tenantInA2 = Tenant::factory()->for($canteenA)->create(['status' => 'active']);
        $tenantInB = Tenant::factory()->for($canteenB)->create(['status' => 'active']);

        $menuA1 = Menu::factory()->for($tenantInA1, 'tenant')->create(['is_available' => true]);
        $menuA2 = Menu::factory()->for($tenantInA2, 'tenant')->create(['is_available' => true]);
        $menuB = Menu::factory()->for($tenantInB, 'tenant')->create(['is_available' => true]);

        $result = app(PublicCatalogQuery::class)->forCanteen($canteenA);

        $ids = $result->pluck('id')->all();

        $this->assertContains($menuA1->id, $ids);
        $this->assertContains($menuA2->id, $ids);
        $this->assertNotContains($menuB->id, $ids, 'Menu dari canteen lain bocor ke katalog publik.');
    }

    public function test_catalog_excludes_unavailable_menus(): void
    {
        $canteen = Canteen::factory()->create(['status' => 'active']);
        $tenant = Tenant::factory()->for($canteen)->create(['status' => 'active']);

        $availableMenu = Menu::factory()->for($tenant, 'tenant')->create(['is_available' => true]);
        $unavailableMenu = Menu::factory()->for($tenant, 'tenant')->create(['is_available' => false]);

        $ids = app(PublicCatalogQuery::class)->forCanteen($canteen)->pluck('id')->all();

        $this->assertContains($availableMenu->id, $ids);
        $this->assertNotContains($unavailableMenu->id, $ids);
    }

    public function test_catalog_excludes_menus_from_inactive_tenants(): void
    {
        $canteen = Canteen::factory()->create(['status' => 'active']);

        $activeTenant = Tenant::factory()->for($canteen)->create(['status' => 'active']);
        $suspendedTenant = Tenant::factory()->for($canteen)->create(['status' => 'suspended']);

        $activeMenu = Menu::factory()->for($activeTenant, 'tenant')->create(['is_available' => true]);
        $suspendedMenu = Menu::factory()->for($suspendedTenant, 'tenant')->create(['is_available' => true]);

        $ids = app(PublicCatalogQuery::class)->forCanteen($canteen)->pluck('id')->all();

        $this->assertContains($activeMenu->id, $ids);
        $this->assertNotContains($suspendedMenu->id, $ids, 'Menu dari tenant nonaktif ikut tampil di katalog publik.');
    }
}
