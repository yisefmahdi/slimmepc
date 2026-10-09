<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    /**
     * Seed the application's admin account.
     *
     * The password can (and on production MUST) be supplied via the
     * SEED_ADMIN_PASSWORD env var — the checked-in default is only for local
     * dev and must be rotated immediately if ever used on a server
     * (audit/02-auth.md [AUTH-10]).
     */
    public function run(): void
    {
        $user = User::firstOrNew(['email' => 'slimmepc@admin.com']);
        $user->fill([
            'name' => 'Slimme-PC Beheerder',
            'phone' => null,
            'house_number' => null,
            'street' => null,
            'postcode' => null,
            'city' => null,
        ]);
        // Privileged fields are not mass assignable ([AUTH-01]).
        $user->forceFill([
            'is_blocked' => false,
            'role' => 'admin',
            'klantnummer' => 'ADMIN-0001',
            'email_verified_at' => now(),
            'password' => (string) (env('SEED_ADMIN_PASSWORD') ?: 'slimmepc@@#@10'),
        ])->save();
    }
}
