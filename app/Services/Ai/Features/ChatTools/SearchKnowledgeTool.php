<?php

namespace App\Services\Ai\Features\ChatTools;

use App\Models\ChatFaq;
use App\Services\Ai\AiService;

/**
 * Semantisch zoeken in de FAQ-kennisbank (embeddings).
 *
 * Puur betekenis-gebaseerd: geen keywords, geen regex. Als er geen
 * vectoren zijn of de API faalt, meldt de tool dat eerlijk zodat de
 * agent weet dat hij geen kennisbank heeft voor deze vraag.
 */
class SearchKnowledgeTool implements ChatTool
{
    protected const SIM_THRESHOLD = 0.25;

    /**
     * Grens voor EXACT ANTWOORD (letterlijk overnemen). Gekalibreerd met
     * echte metingen: echte matches 0.56–0.72 ("openingstijden" 0.70,
     * "Hoe laat zijn jullie open?" 0.72), een foutieve match (iPhone-vraag
     * vs laptop-prijs-FAQ) 0.49. Grens 0.60 scheidt die in de praktijk.
     */
    protected const SIM_EXACT = 0.60;

    protected const LIMIT = 3;

    public function definition(): array
    {
        return [
            'type' => 'function',
            'function' => [
                'name' => 'search_knowledge',
                'description' => 'Zoek in de Slimme-PC kennisbank (veelgestelde vragen over openingstijden, prijzen, contact, reparatie-aanmelden, reparatieduur, diensten). Gebruik dit bij elke inhoudelijke klantvraag. Geeft de best passende vragen + antwoorden terug, of meldt dat er niets relevants is.',
                'parameters' => [
                    'type' => 'object',
                    'properties' => [
                        'query' => [
                            'type' => 'string',
                            'description' => 'De klantvraag in eigen woorden (mag de volledige laatste klantzin zijn).',
                        ],
                    ],
                    'required' => ['query'],
                    'additionalProperties' => false,
                ],
            ],
        ];
    }

    public function execute(array $args): string
    {
        $query = trim((string) ($args['query'] ?? ''));
        if ($query === '') {
            return 'Geen zoekvraag meegegeven — geen kennisbank-resultaten.';
        }

        try {
            // Korte timeout: embeddings mogen het chat-budget nooit opeten.
            $queryVector = AiService::embed($query, ['timeout' => 8]);
        } catch (\Throwable $e) {
            report($e);

            return 'Kennisbank tijdelijk niet doorzoekbaar (technische fout). Beantwoord zonder kennisbank of bied een medewerker aan.';
        }

        if (! $queryVector) {
            return 'Kennisbank tijdelijk niet doorzoekbaar (geen vector). Beantwoord zonder kennisbank of bied een medewerker aan.';
        }

        $model = (string) config('services.embedding.model');
        $faqs = ChatFaq::active()
            ->where('embedding_model', $model)
            ->whereNotNull('embedding')
            ->get();

        if ($faqs->isEmpty()) {
            return 'Kennisbank is leeg (geen artikelen met vectoren). Beantwoord zonder kennisbank of bied een medewerker aan.';
        }

        $scored = [];
        foreach ($faqs as $faq) {
            $vector = is_array($faq->embedding) ? array_map('floatval', $faq->embedding) : [];
            if (count($vector) !== count($queryVector)) {
                continue;
            }
            $sim = self::cosine($queryVector, $vector);
            if ($sim >= self::SIM_THRESHOLD) {
                $scored[] = ['sim' => $sim, 'faq' => $faq];
            }
        }

        usort($scored, fn ($a, $b) => $b['sim'] <=> $a['sim']);
        $top = array_slice($scored, 0, self::LIMIT);

        if (! $top) {
            return 'Geen relevante kennisbank-artikelen gevonden voor deze vraag. Beantwoord uit eigen website-kennis of geef eerlijk aan dat je het niet weet en roep request_handoff aan.';
        }

        $lines = ['Relevante kennisbank-artikelen (vertaal naar de taal van de klant, feiten/cijfers/links NOOIT veranderen):'];
        if (($top[0]['sim'] ?? 0) >= self::SIM_EXACT) {
            // Structurele verankering: bij zeer sterke match is dit HET
            // antwoord — de agent neemt het letterlijk over (alleen vertaald).
            $lines[] = 'EXACT ANTWOORD — dit is HET antwoord op de klantvraag. Neem het letterlijk over, vertaal alleen de taal. Feiten, cijfers, namen en links NOOIT veranderen of aanvullen:';
        }
        foreach ($top as $i => $s) {
            $lines[] = ($i + 1).'. Vraag: '.$s['faq']->question."\nAntwoord: ".$s['faq']->answer;
        }

        return implode("\n", $lines);
    }

    /**
     * @param  array<int, float>  $a
     * @param  array<int, float>  $b
     */
    public static function cosine(array $a, array $b): float
    {
        $dot = 0.0;
        $normA = 0.0;
        $normB = 0.0;
        $n = min(count($a), count($b));
        for ($i = 0; $i < $n; $i++) {
            $dot += $a[$i] * $b[$i];
            $normA += $a[$i] * $a[$i];
            $normB += $b[$i] * $b[$i];
        }
        if ($normA <= 0.0 || $normB <= 0.0) {
            return 0.0;
        }

        return $dot / (sqrt($normA) * sqrt($normB));
    }
}
