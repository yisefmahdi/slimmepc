<?php

namespace Database\Seeders;

use App\Models\ContentBlock;
use App\Support\Cms;
use Illuminate\Database\Seeder;

/**
 * Seeds ONLY the CMS defaults for page = "afspraak".
 *
 * Safe to run on production at any time:
 *  - firstOrCreate never overwrites existing blocks (admin edits are kept)
 *  - no other page is touched
 */
class AfspraakContentSeeder extends Seeder
{
    public function run(): void
    {
        $afspraak = require database_path('data/afspraak.php');

        $seeded = 0;

        foreach ($afspraak as $section => $blocks) {
            $sort = 0;

            foreach ($blocks as $key => $value) {
                $isJson = is_array($value);

                $block = ContentBlock::firstOrCreate(
                    ['page' => 'afspraak', 'section' => $section, 'block_key' => $key],
                    [
                        'type' => $isJson ? 'json' : 'text',
                        'value' => $isJson ? null : $value,
                        'json_value' => $isJson ? $value : null,
                        'sort_order' => $sort++,
                    ]
                );

                if ($block->wasRecentlyCreated) {
                    $seeded++;
                }
            }
        }

        Cms::bust();

        $this->command?->info("Afspraak content: {$seeded} new block(s) created, existing left untouched.");
    }
}
