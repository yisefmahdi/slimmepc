<?php

namespace App\Services\Ai\Features;

use App\Models\ChatFaq;
use App\Services\Ai\AiService;
use Illuminate\Support\Facades\Log;

/**
 * Genereert + ververst FAQ-embeddings (question + answer).
 */
class FaqEmbedder
{
    /**
     * Embed één FAQ (overschrijft bestaande vector + model).
     */
    public function embedFaq(ChatFaq $faq): bool
    {
        try {
            $vector = AiService::embed($faq->embeddableText());
            $faq->updateQuietly([
                'embedding' => $vector,
                'embedding_model' => (string) config('services.embedding.model'),
            ]);

            return true;
        } catch (\Throwable $e) {
            Log::warning('[faq-embed] failed for FAQ #'.$faq->id.': '.$e->getMessage());

            return false;
        }
    }

    /**
     * Backfill: embed alle actieve FAQ's zonder (passende) vector.
     *
     * @return array{embedded: int, skipped: int, failed: int}
     */
    public function backfill(bool $fresh = false): array
    {
        $model = (string) config('services.embedding.model');
        $stats = ['embedded' => 0, 'skipped' => 0, 'failed' => 0];

        foreach (ChatFaq::orderBy('id')->get() as $faq) {
            $stale = $faq->embedding_model !== $model || ! is_array($faq->embedding) || ! $faq->embedding;
            if (! $fresh && ! $stale) {
                $stats['skipped']++;
                continue;
            }
            if ($this->embedFaq($faq)) {
                $stats['embedded']++;
            } else {
                $stats['failed']++;
            }
        }

        return $stats;
    }
}
