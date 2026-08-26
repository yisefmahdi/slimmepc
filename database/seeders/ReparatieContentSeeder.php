<?php

namespace Database\Seeders;

use App\Models\ContentBlock;
use App\Support\Cms;
use Illuminate\Database\Seeder;

/**
 * Seeds ONLY the CMS defaults for page = "reparatie".
 *
 * Safe to run on production at any time:
 *  - firstOrCreate never overwrites existing blocks (admin edits are kept)
 *  - no other page is touched
 */
class ReparatieContentSeeder extends Seeder
{
    public function run(): void
    {
        $reparatie = require database_path('data/reparatie.php');

        $seeded = 0;

        foreach ($reparatie as $section => $blocks) {
            $sort = 0;

            foreach ($blocks as $key => $value) {
                $isJson = is_array($value);

                $block = ContentBlock::firstOrCreate(
                    ['page' => 'reparatie', 'section' => $section, 'block_key' => $key],
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

        $this->command?->info("Reparatie content: {$seeded} new block(s) created, existing left untouched.");
    }
}
