<?php

namespace Database\Seeders;

use App\Models\ShippingRate;
use Illuminate\Database\Seeder;

class ShippingRateSeeder extends Seeder
{
    public function run(): void
    {
        ShippingRate::updateOrCreate(['slug' => 'delivery'], [
            'name' => 'Standaard verzending',
            'price' => 6.95,
            'free_above' => 75.00,
            'is_active' => true,
            'sort_order' => 0,
        ]);

        ShippingRate::updateOrCreate(['slug' => 'pickup'], [
            'name' => 'Afhalen in Apeldoorn',
            'price' => 0.00,
            'free_above' => null,
            'is_active' => true,
            'sort_order' => 1,
        ]);
    }
}
