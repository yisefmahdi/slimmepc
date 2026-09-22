<?php

namespace Database\Seeders;

use App\Models\TechnicianSetting;
use Illuminate\Database\Seeder;

class TechnicianRatesSeeder extends Seeder
{
    /**
     * Standaard monteur-tarieven (alleen vullen als leeg).
     * Overschrijft nooit handmatig ingestelde waarden.
     */
    public function run(): void
    {
        $defaults = [
            'hour_price' => 80, // €80/uur = €20/kwartier (zoals oud project)
            'travel_cost' => 5,
            'member_discount_type' => 'none',
            'member_discount_value' => 0,
            'member_free_travel' => '0',
        ];

        foreach ($defaults as $key => $value) {
            if (TechnicianSetting::where('key', $key)->doesntExist()) {
                TechnicianSetting::create(['key' => $key, 'value' => $value]);
            }
        }

        $this->command->info('Monteur-tarieven gecontroleerd.');
    }
}
