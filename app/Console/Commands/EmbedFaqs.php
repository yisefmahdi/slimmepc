<?php

namespace App\Console\Commands;

use App\Services\Ai\Features\FaqEmbedder;
use Illuminate\Console\Command;

class EmbedFaqs extends Command
{
    protected $signature = 'ai:embed-faqs {--fresh : Embed alle actieve FAQs opnieuw, ook als er al een vector is}';

    protected $description = 'Genereer embeddings voor de chat-kennisbank (backfill)';

    public function handle(FaqEmbedder $embedder): int
    {
        $stats = $embedder->backfill((bool) $this->option('fresh'));

        $this->info("Embedded: {$stats['embedded']}, overgeslagen: {$stats['skipped']}, mislukt: {$stats['failed']}");

        return $stats['failed'] > 0 && $stats['embedded'] === 0 ? self::FAILURE : self::SUCCESS;
    }
}
