<?php

namespace Tests\Feature;

use App\Models\Canteen;
use App\Models\Menu;
use App\Models\ModifierGroup;
use App\Models\Tenant;
use Database\Seeders\DemoCanteenSeeder;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

class TenantIsolationConstraintTest extends TestCase
{
    use RefreshDatabase;

    private Canteen $canteen;
    private Tenant $tenantA;
    private Tenant $tenantB;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(DemoCanteenSeeder::class);

        $this->canteen = Canteen::where('code', 'DEMO01')->firstOrFail();
        $this->tenantA = Tenant::where('canteen_id', $this->canteen->id)->where('code', 'TNT01')->firstOrFail();
        $this->tenantB = Tenant::where('canteen_id', $this->canteen->id)->where('code', 'TNT02')->firstOrFail();
    }

    public function test_modifier_option_tidak_boleh_menunjuk_modifier_group_tenant_lain(): void
    {
        $groupB = ModifierGroup::where('tenant_id', $this->tenantB->id)->firstOrFail();

        $this->expectException(QueryException::class);

        DB::table('modifier_options')->insert([
            'tenant_id' => $this->tenantA->id, // tenant A
            'group_id' => $groupB->id,          // tapi group milik tenant B
            'name' => 'Pedas Nakal',
            'price_delta' => 0,
            'stock_qty' => 10,
            'is_available' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function test_menu_tidak_boleh_menunjuk_menu_category_tenant_lain(): void
    {
        $categoryB = DB::table('menu_categories')->where('tenant_id', $this->tenantB->id)->first();

        $this->expectException(QueryException::class);

        DB::table('menus')->insert([
            'tenant_id' => $this->tenantA->id,   // tenant A
            'category_id' => $categoryB->id,      // kategori tenant B
            'name' => 'Menu Nyasar',
            'base_price' => 10000,
            'stock_qty' => 5,
            'is_available' => true,
            'prep_minutes' => 10,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function test_order_items_tidak_boleh_menunjuk_menu_tenant_lain(): void
    {
        $menuB = Menu::where('tenant_id', $this->tenantB->id)->firstOrFail();

        $orderId = DB::table('orders')->insertGetId([
            'public_id' => (string) Str::uuid(),
            'order_number' => 'ORD-TEST-001',
            'canteen_id' => $this->canteen->id,
            'checkout_key' => 'CHK-TEST-001',
            'tracking_token_hash' => random_bytes(32),
            'status' => 'pending',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $commissionA = DB::table('commission_schemes')->where('tenant_id', $this->tenantA->id)->first();

        $tenantOrderAId = DB::table('tenant_orders')->insertGetId([
            'order_id' => $orderId,
            'tenant_id' => $this->tenantA->id,
            'commission_id' => $commissionA->id,
            'commission_rate_snapshot' => $commissionA->commission_rate,
            'subtotal_amount' => 0,
            'commission_amount' => 0,
            'net_amount' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->expectException(QueryException::class);

        DB::table('order_items')->insert([
            'tenant_id' => $this->tenantA->id,     // tenant A
            'tenant_order_id' => $tenantOrderAId,
            'menu_id' => $menuB->id,                // menu tenant B
            'name_snapshot' => 'Menu Nyasar',
            'unit_price_snapshot' => 15000,
            'quantity' => 1,
            'line_total' => 15000,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function test_code_tenant_duplikat_dalam_canteen_yang_sama_ditolak(): void
    {
        $this->expectException(QueryException::class);

        DB::table('tenants')->insert([
            'canteen_id' => $this->canteen->id,
            'code' => 'TNT01', // sudah dipakai tenant A
            'slug' => 'slug-lain-lain',
            'display_name' => 'Tenant Duplikat',
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function test_idempotency_key_duplikat_pada_menu_stock_movements_ditolak(): void
    {
        $menuA = Menu::where('tenant_id', $this->tenantA->id)->firstOrFail();

        DB::table('menu_stock_movements')->insert([
            'tenant_id' => $this->tenantA->id,
            'menu_id' => $menuA->id,
            'idempotency_key' => 'STOCK-DUP-001',
            'type' => 'sale',
            'quantity_delta' => -1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->expectException(QueryException::class);

        DB::table('menu_stock_movements')->insert([
            'tenant_id' => $this->tenantA->id,
            'menu_id' => $menuA->id,
            'idempotency_key' => 'STOCK-DUP-001', // key sama
            'type' => 'sale',
            'quantity_delta' => -1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}