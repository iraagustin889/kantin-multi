<?php

namespace Database\Factories;

use App\Models\Canteen;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class OrderFactory extends Factory
{
    public function definition(): array
    {
        return [
            'public_id' => Str::uuid(),
            'order_number' => strtoupper(Str::random(10)),
            'canteen_id' => Canteen::factory(),
            'checkout_key' => Str::uuid(),
            'tracking_token_hash' => hash('sha256', Str::random(32), true),
            'status' => 'pending',
            'subtotal_amount' => 0,
            'tax_amount' => 0,
            'service_fee_amount' => 0,
            'grand_total_amount' => 0,
            'placed_at' => now(),
        ];
    }
}