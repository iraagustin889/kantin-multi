<?php

namespace Database\Seeders;

use App\Models\Canteen;
use App\Models\CommissionScheme;
use App\Models\DiningTable;
use App\Models\Menu;
use App\Models\MenuCategory;
use App\Models\ModifierGroup;
use App\Models\ModifierOption;
use App\Models\Tenant;
use App\Models\TenantBalance;
use App\Models\User;
use App\Models\UserTenantRole;
use Illuminate\Database\Seeder;

class DemoCanteenSeeder extends Seeder
{
    public function run(): void
    {
        // 1 kantin
        $canteen = Canteen::updateOrCreate(
            ['code' => 'DEMO01'],
            [
                'slug' => 'kantin-demo',
                'name' => 'Kantin Demo',
                'tax_rate' => 0.10,
                'service_fee_rate' => 0.02,
                'status' => 'active',
            ]
        );

        // meja, tidak terikat tenant tertentu
        DiningTable::updateOrCreate(
            ['canteen_id' => $canteen->id, 'code' => 'T01'],
            ['label' => 'Meja 1', 'zone' => 'Indoor', 'status' => 'active']
        );
        DiningTable::updateOrCreate(
            ['canteen_id' => $canteen->id, 'code' => 'T02'],
            ['label' => 'Meja 2', 'zone' => 'Outdoor', 'status' => 'active']
        );

        // 2 tenant
        $tenantsData = [
            ['code' => 'TNT01', 'slug' => 'nasi-goreng-abadi', 'display_name' => 'Nasi Goreng Abadi'],
            ['code' => 'TNT02', 'slug' => 'es-teh-segar', 'display_name' => 'Es Teh Segar'],
        ];

        foreach ($tenantsData as $index => $data) {
            $tenant = Tenant::updateOrCreate(
                ['canteen_id' => $canteen->id, 'code' => $data['code']],
                [
                    'slug' => $data['slug'],
                    'display_name' => $data['display_name'],
                    'status' => 'active',
                ]
            );

            // saldo tenant (manual, bukan factory)
            TenantBalance::updateOrCreate(
                ['tenant_id' => $tenant->id],
                ['available_amount' => 0, 'held_amount' => 0]
            );

            // komisi berlaku
            CommissionScheme::updateOrCreate(
                ['tenant_id' => $tenant->id, 'valid_from' => now()->subDays(30)],
                ['commission_rate' => 0.15, 'valid_to' => null]
            );

            // kategori + menu
            $category = MenuCategory::updateOrCreate(
                ['tenant_id' => $tenant->id, 'name' => 'Makanan Utama'],
                ['sort_order' => 0, 'is_active' => true]
            );

            $menu = Menu::updateOrCreate(
                ['tenant_id' => $tenant->id, 'name' => $data['display_name'].' Spesial'],
                [
                    'category_id' => $category->id,
                    'base_price' => 15000,
                    'stock_qty' => 50,
                    'is_available' => true,
                    'prep_minutes' => 10,
                ]
            );

            // modifier group + option
            $group = ModifierGroup::updateOrCreate(
                ['tenant_id' => $tenant->id, 'name' => 'Level Pedas'],
                ['min_select' => 0, 'max_select' => 1, 'is_active' => true]
            );

            ModifierOption::updateOrCreate(
                ['tenant_id' => $tenant->id, 'group_id' => $group->id, 'name' => 'Pedas Sedang'],
                ['price_delta' => 0, 'stock_qty' => 50, 'is_available' => true]
            );

            // role user untuk tenant ini
            $user = User::updateOrCreate(
                ['email' => "owner-tenant{$index}@demo.test"],
                [
                    'name' => "Owner {$data['display_name']}",
                    'password' => bcrypt('password'),
                    'email_verified_at' => now(),
                ]
            );

            UserTenantRole::updateOrCreate(
                ['user_id' => $user->id, 'tenant_id' => $tenant->id, 'role' => 'owner']
            );
        }
    }
}