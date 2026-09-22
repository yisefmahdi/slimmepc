<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class ImportUsersCommand extends Command
{
    protected $signature = 'users:import
        {file : Pad naar het JSON-bestand met gebruikers}
        {--delete : Verwijder het JSON-bestand na een succesvolle import}';

    protected $description = 'Importeer gebruikers uit een JSON-export (upsert op e-mail).';

    public function handle(): int
    {
        $path = (string) $this->argument('file');

        if (! is_file($path)) {
            $this->error('Bestand niet gevonden: ' . $path);

            return self::FAILURE;
        }

        $rows = json_decode((string) file_get_contents($path), true);
        if (! is_array($rows)) {
            $this->error('Ongeldige JSON.');

            return self::FAILURE;
        }

        $created = 0;
        $updated = 0;

        foreach ($rows as $row) {
            $email = strtolower(trim((string) ($row['email'] ?? '')));
            if ($email === '') {
                continue;
            }

            $data = [
                'name' => $row['name'] ?? $email,
                'phone' => $row['phone'] ?? null,
                'house_number' => $row['house_number'] ?? null,
                'street' => $row['street'] ?? null,
                'postcode' => $row['postcode'] ?? null,
                'city' => $row['city'] ?? null,
                'role' => in_array($row['role'] ?? 'user', ['admin', 'user', 'technician'], true) ? $row['role'] : 'user',
                'email_verified_at' => $row['email_verified_at'] ?? null,
            ];

            // Wachtwoord-hash 1-op-1 overnemen (zelfde wachtwoorden blijven werken).
            if (! empty($row['password']) && str_starts_with((string) $row['password'], '$2y$')) {
                $data['password'] = $row['password'];
            }

            // Klantnummer behouden als het nog vrij is, anders nieuw genereren (boot doet dat automatisch).
            if (! empty($row['klantnummer'])
                && ! User::where('klantnummer', $row['klantnummer'])->where('email', '!=', $email)->exists()
            ) {
                $data['klantnummer'] = $row['klantnummer'];
            }

            $exists = User::where('email', $email)->exists();
            $user = User::updateOrCreate(['email' => $email], $data);

            // Wachtwoord ontbrak in export → willekeurig (veilig).
            if (empty($row['password']) || ! str_starts_with((string) $row['password'], '$2y$')) {
                $user->update(['password' => Hash::make(bin2hex(random_bytes(16)))]);
            }

            $exists ? $updated++ : $created++;
        }

        $this->info("Klaar: {$created} aangemaakt, {$updated} bijgewerkt.");

        if ($this->option('delete')) {
            @unlink($path);
            $this->info('JSON-bestand verwijderd.');
        }

        return self::SUCCESS;
    }
}
