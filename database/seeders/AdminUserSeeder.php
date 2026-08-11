<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    /**
     * Seed the application's admin account.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'slimmepc@admin.com'],
            [
                'name' => 'Slimme-PC Beheerder',
                'phone' => null,
                'is_blocked' => false,
                'house_number' => null,
                'street' => null,
                'postcode' => null,
                'city' => null,
                'role' => 'admin',
                'klantnummer' => 'ADMIN-0001',
                'email_verified_at' => now(),
                'password' => 'slimmepc@@#@10',
            ]
        );
    }
}
