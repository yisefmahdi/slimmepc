<?php

namespace Database\Seeders;

use App\Models\MembershipSetting;
use Illuminate\Database\Seeder;

class MembershipPriceSeeder extends Seeder
{
    /**
     * Standaard lidmaatschapsprijs (alleen als er nog geen is ingesteld).
     * Overschrijft nooit een handmatig ingestelde prijs.
     */
    public function run(): void
    {
        if (MembershipSetting::count() === 0) {
            MembershipSetting::create(['subscription_price' => 23.00]);
            $this->command->info('Standaard lidmaatschapsprijs (€23,00) ingesteld.');
        } else {
            $this->command->info('Lidmaatschapsprijs bestaat al — niets gedaan.');
        }
    }
}
