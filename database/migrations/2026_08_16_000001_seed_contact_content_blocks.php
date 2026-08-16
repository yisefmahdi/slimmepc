<?php

use App\Models\ContentBlock;
use App\Support\Cms;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Seed default content for the Contact page.
     *
     * Uses firstOrCreate so admin edits are NEVER overwritten when this
     * migration runs automatically on deploy (migrate --force).
     */
    public function up(): void
    {
        $content = require database_path('data/contact.php');

        foreach ($content as $section => $blocks) {
            $sort = 0;

            foreach ($blocks as $key => $value) {
                $isJson = is_array($value);

                ContentBlock::firstOrCreate(
                    ['page' => 'contact', 'section' => $section, 'block_key' => $key],
                    [
                        'type' => $isJson ? 'json' : 'text',
                        'value' => $isJson ? null : $value,
                        'json_value' => $isJson ? $value : null,
                        'sort_order' => $sort++,
                    ]
                );
            }
        }

        Cms::bust();
    }

    public function down(): void
    {
        ContentBlock::where('page', 'contact')->delete();

        Cms::bust();
    }
};