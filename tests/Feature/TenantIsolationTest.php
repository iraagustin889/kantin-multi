<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Menu;
use App\Models\Tenant;
use App\Models\TenantOrder;
use App\Models\User;
use App\Models\UserTenantRole;
use App\Models\Withdrawal;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * Tahap 7 — item 1:
 * "Tulis test yang memastikan Tenant A tidak dapat membaca maupun mengubah
 * data Tenant B melalui index, detail, update, delete, dan route binding."
 *
 * CATATAN PENYESUAIAN (wajib dicek sebelum run):
 * - Nama route di $this->routeName() di bawah ini mengikuti pola resource
 *   nested standar: tenant.menus.index / .show / .update / .destroy, dst.
 *   Kalau route asli kamu beda (mis. prefix "kantin." atau nama resource
 *   lain), ubah method routeName() saja — struktur test tidak perlu diubah.
 * - Kolom/relasi user↔tenant memakai tabel pivot user_canteen_roles per
 *   modul; helper actingAsTenantOperator() di bawah menyesuaikan itu.
 *   Sesuaikan kalau struktur role project kamu beda.
 * - Test otomatis di-skip (bukan gagal) kalau route belum terdaftar, supaya
 *   file ini tetap bisa langsung ditempel sebelum semua route selesai.
 */
class TenantIsolationTest extends TestCase
{
    use RefreshDatabase;

    private Tenant $tenantA;
    private Tenant $tenantB;
    private User $userA;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tenantA = Tenant::factory()->create(['status' => 'active']);
        $this->tenantB = Tenant::factory()->create(['status' => 'active']);

        $this->userA = User::factory()->create();
        $this->attachUserToTenant($this->userA, $this->tenantA, 'tenant');
    }

    /** @return array<string, array{0: string, 1: class-string}> */
    public static function resourceProvider(): array
    {
        return [
            'menu' => ['menus', Menu::class],
            'order' => ['orders', TenantOrder::class],
            'withdrawal' => ['withdrawals', Withdrawal::class],
        ];
    }

    #[DataProvider('resourceProvider')]
    public function test_tenant_a_cannot_index_tenant_b_resource(string $routeSegment, string $modelClass): void
    {
        $routeName = "tenant.{$routeSegment}.index";
        if (! Route::has($routeName)) {
            $this->markTestSkipped("Route {$routeName} belum terdaftar — sesuaikan routeName di test ini.");
        }

        $modelClass::factory()->for($this->tenantB, 'tenant')->create();

        $response = $this->actingAs($this->userA)->get(route($routeName, $this->tenantA));

        $response->assertOk();
        $response->assertDontSeeText($this->tenantB->name ?? (string) $this->tenantB->id);
    }

    #[DataProvider('resourceProvider')]
    public function test_tenant_a_cannot_show_tenant_b_resource(string $routeSegment, string $modelClass): void
    {
        $routeName = "tenant.{$routeSegment}.show";
        if (! Route::has($routeName)) {
            $this->markTestSkipped("Route {$routeName} belum terdaftar — sesuaikan routeName di test ini.");
        }

        $resourceB = $modelClass::factory()->for($this->tenantB, 'tenant')->create();

        $response = $this->actingAs($this->userA)
            ->get(route($routeName, [$this->tenantA, $resourceB]));

        // Scoped route binding harus gagal menemukan resource B di bawah tenant A
        // → 404. Kalau proyekmu memakai 403 untuk kasus ini, ganti assertion di
        // bawah menjadi assertForbidden().
        $response->assertNotFound();
    }

    #[DataProvider('resourceProvider')]
    public function test_tenant_a_cannot_update_tenant_b_resource(string $routeSegment, string $modelClass): void
    {
        $routeName = "tenant.{$routeSegment}.update";
        if (! Route::has($routeName)) {
            $this->markTestSkipped("Route {$routeName} belum terdaftar — sesuaikan routeName di test ini.");
        }

        $resourceB = $modelClass::factory()->for($this->tenantB, 'tenant')->create();
        $originalAttributes = $resourceB->getAttributes();

        $response = $this->actingAs($this->userA)
            ->patch(route($routeName, [$this->tenantA, $resourceB]), $this->updatePayloadFor($modelClass));

        $this->assertContains($response->getStatusCode(), [403, 404]);
        // Yang paling penting bukan status code-nya saja, tapi data B benar-benar
        // tidak berubah.
        $this->assertEquals(
            $originalAttributes,
            $resourceB->fresh()?->getAttributes() ?? [],
        );
    }
    #[DataProvider('resourceProvider')]
    public function test_tenant_a_cannot_delete_tenant_b_resource(string $routeSegment, string $modelClass): void
    {
        $routeName = "tenant.{$routeSegment}.destroy";
        if (! Route::has($routeName)) {
            $this->markTestSkipped("Route {$routeName} belum terdaftar — sesuaikan routeName di test ini.");
        }

        $resourceB = $modelClass::factory()->for($this->tenantB, 'tenant')->create();

        $response = $this->actingAs($this->userA)
            ->delete(route($routeName, [$this->tenantA, $resourceB]));

        $this->assertContains($response->getStatusCode(), [403, 404]);
        $this->assertDatabaseHas($resourceB->getTable(), ['id' => $resourceB->id]);
    }

    /**
     * Kasus khusus: ID resource B dipasang manual di URL tenant A (bukan
     * dibuat lewat factory dengan tenant A), memastikan scoped implicit
     * binding benar-benar membaca parent route, bukan hanya ID resource.
     */
    public function test_swapping_menu_id_in_url_does_not_leak_data(): void
    {
        if (! Route::has('tenant.menus.show')) {
            $this->markTestSkipped('Route tenant.menus.show belum terdaftar.');
        }

        $menuB = Menu::factory()->for($this->tenantB, 'tenant')->create();

        $response = $this->actingAs($this->userA)->getJson(
            route('tenant.menus.show', [$this->tenantA, $menuB])
        );

        $response->assertNotFound();
        $response->assertJsonMissing(['id' => $menuB->id]);
    }

    private function updatePayloadFor(string $modelClass): array
    {
        return match ($modelClass) {
            Menu::class => ['name' => 'Hasil Tamper Tenant A', 'is_available' => false],
            TenantOrder::class => ['status' => 'cancelled'],
            Withdrawal::class => ['status' => 'approved'],
            default => [],
        };
    }

    private function attachUserToTenant(User $user, Tenant $tenant, string $role): void
    {
        UserTenantRole::create([
            'user_id' => $user->id,
            'tenant_id' => $tenant->id,
            'role' => $role,
        ]);
    }
}
