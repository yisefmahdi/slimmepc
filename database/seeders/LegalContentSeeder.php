<?php

namespace Database\Seeders;

use App\Models\ContentBlock;
use App\Support\Cms;
use Illuminate\Database\Seeder;

class LegalContentSeeder extends Seeder
{
    /**
     * Juridische pagina's (privacy + voorwaarden) vullen met de inhoud
     * uit het oude project. Koppen krijgen een "## "-prefix, de
     * legal-page partial rendert die als <h2>.
     */
    public function run(): void
    {
        $file = database_path('seeders/data/legal-old.json');

        if (!is_file($file)) {
            $this->command->warn('legal-old.json niet gevonden — juridische pagina\'s overgeslagen.');
            return;
        }

        $data = json_decode((string) file_get_contents($file), true);

        $pages = [
            'privacy' => [
                'badge' => 'Privacy',
                'title_line1' => 'Privacy',
                'title_line2' => 'verklaring',
                'description' => 'Hoe Slimme-PC omgaat met jouw persoonsgegevens.',
                'rows' => $data['privacy'] ?? [],
            ],
            'voorwaarden' => [
                'badge' => 'Voorwaarden',
                'title_line1' => 'Algemene',
                'title_line2' => 'voorwaarden',
                'description' => 'De voorwaarden waaronder Slimme-PC haar diensten levert.',
                'rows' => $data['voorwaarden'] ?? [],
            ],
        ];

        foreach ($pages as $page => $cfg) {
            $parts = [];

            foreach ($cfg['rows'] as $row) {
                $title = trim((string) ($row['title'] ?? ''));
                $body = trim((string) ($row['content'] ?? ''));
                if ($title === '' && $body === '') continue;
                if ($title !== '') $parts[] = '## ' . $title;
                if ($body !== '') $parts[] = $body;
            }

            $blocks = [
                'badge' => $cfg['badge'],
                'title_line1' => $cfg['title_line1'],
                'title_line2' => $cfg['title_line2'],
                'description' => $cfg['description'],
                'content' => implode("\n\n", $parts),
                'updated_label' => 'Laatst bijgewerkt: ' . now()->format('d-m-Y'),
            ];

            $order = 0;
            foreach ($blocks as $key => $value) {
                ContentBlock::updateOrCreate(
                    ['page' => $page, 'section' => 'inhoud', 'block_key' => $key],
                    ['type' => $key === 'content' || $key === 'description' ? 'textarea' : 'text', 'value' => $value, 'json_value' => null, 'sort_order' => $order++]
                );
            }
        }

        Cms::bust();

        $this->command->info('Juridische pagina\'s (privacy + voorwaarden) gevuld.');
    }
}
