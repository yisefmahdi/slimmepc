<?php

namespace Tests\Unit;

use App\Models\ChatFaq;
use App\Services\Ai\AiService;
use App\Services\Ai\Contracts\EmbeddingClientInterface;
use App\Services\Ai\Features\ChatTools\SearchKnowledgeTool;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ChatEmbeddingTest extends TestCase
{
    use RefreshDatabase;

    protected function mockEmbedding(array $map, ?float $default = 0.1): void
    {
        $mock = new class($map, $default) implements EmbeddingClientInterface
        {
            public function __construct(private array $map, private ?float $default) {}

            public function embed(string $text, array $options = []): array
            {
                foreach ($this->map as $needle => $vector) {
                    if (str_contains(mb_strtolower($text), mb_strtolower((string) $needle))) {
                        return $vector;
                    }
                }

                return array_fill(0, 3, $this->default ?? 0.1);
            }

            public function embedMany(array $texts): array
            {
                return array_map(fn ($t) => $this->embed($t), $texts);
            }

            public function isAvailable(): bool
            {
                return true;
            }
        };

        AiService::setEmbeddingClient($mock);
    }

    private function seedFaqs(): void
    {
        ChatFaq::create([
            'question' => 'Hoe kan ik contact opnemen?',
            'answer' => 'Bel 055 203 21 45.',
            'is_active' => true,
            'embedding' => [1.0, 0.0, 0.0],
            'embedding_model' => 'test-model',
        ]);
        ChatFaq::create([
            'question' => 'Hoe lang duurt een reparatie?',
            'answer' => 'Meestal dezelfde dag.',
            'is_active' => true,
            'embedding' => [0.0, 1.0, 0.0],
            'embedding_model' => 'test-model',
        ]);
    }

    public function test_cosine_similarity_math(): void
    {
        $this->assertEquals(1.0, round(SearchKnowledgeTool::cosine([1, 0, 0], [1, 0, 0]), 4));
        $this->assertEquals(0.0, round(SearchKnowledgeTool::cosine([1, 0, 0], [0, 1, 0]), 4));
        $this->assertEquals(0.0, SearchKnowledgeTool::cosine([0, 0, 0], [1, 0, 0]));
    }

    public function test_semantic_search_picks_closest_faq(): void
    {
        config(['services.embedding.model' => 'test-model']);
        $this->seedFaqs();
        $this->mockEmbedding(['contact' => [1.0, 0.0, 0.0], 'reparatie' => [0.0, 1.0, 0.0]]);

        $out = (new SearchKnowledgeTool)->execute(['query' => 'hoe kom ik met jullie in contact']);

        $this->assertStringContainsString('055 203 21 45', $out);
        $this->assertStringNotContainsString('dezelfde dag', $out);
    }

    public function test_strong_match_is_marked_exact(): void
    {
        config(['services.embedding.model' => 'test-model']);
        ChatFaq::create([
            'question' => 'Hoe lang duurt een reparatie?',
            'answer' => 'Meestal dezelfde dag.',
            'is_active' => true,
            'embedding' => [1.0, 0.0, 0.0],
            'embedding_model' => 'test-model',
        ]);
        $this->mockEmbedding(['reparatie' => [1.0, 0.0, 0.0]]);

        $out = (new SearchKnowledgeTool)->execute(['query' => 'reparatie duur?']);

        expect($out)->toContain('EXACT ANTWOORD')
            ->and($out)->toContain('dezelfde dag');
    }

    public function test_semantic_search_reports_honestly_when_nothing_matches(): void
    {
        config(['services.embedding.model' => 'test-model']);
        $this->seedFaqs();
        // Vector loodrecht op beide FAQs → sim 0 < threshold.
        $this->mockEmbedding(['piano' => [0.0, 0.0, 1.0]], 0.0);

        $out = (new SearchKnowledgeTool)->execute(['query' => 'piano stemmen aan huis']);

        $this->assertStringContainsString('Geen relevante kennisbank', $out);
    }

    public function test_semantic_search_reports_technical_failure(): void
    {
        config(['services.embedding.model' => 'test-model']);
        $this->seedFaqs();

        $failing = new class implements EmbeddingClientInterface
        {
            public function embed(string $text, array $options = []): array
            {
                throw new \RuntimeException('API down');
            }

            public function embedMany(array $texts): array
            {
                throw new \RuntimeException('API down');
            }

            public function isAvailable(): bool
            {
                return true;
            }
        };
        AiService::setEmbeddingClient($failing);

        $out = (new SearchKnowledgeTool)->execute(['query' => 'wat is jullie telefoonnummer']);

        $this->assertStringContainsString('niet doorzoekbaar', $out);
    }

    public function test_empty_knowledge_base_is_reported(): void
    {
        config(['services.embedding.model' => 'test-model']);
        $this->mockEmbedding([]);

        $out = (new SearchKnowledgeTool)->execute(['query' => 'wanneer zijn jullie open']);

        $this->assertStringContainsString('leeg', $out);
    }
}
