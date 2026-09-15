<?php

namespace Tests\Unit;

use App\Models\ChatFaq;
use App\Services\Ai\AiService;
use App\Services\Ai\Contracts\EmbeddingClientInterface;
use App\Services\Ai\Features\ChatAnswerGenerator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ChatEmbeddingTest extends TestCase
{
    use RefreshDatabase;

    protected function mockEmbedding(array $map, ?float $default = 0.1): void
    {
        $mock = new class($map, $default) implements EmbeddingClientInterface {
            public function __construct(private array $map, private ?float $default) {}

            public function embed(string $text): array
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
            'keywords' => 'contact,telefoon',
            'is_active' => true,
            'embedding' => [1.0, 0.0, 0.0],
            'embedding_model' => 'test-model',
        ]);
        ChatFaq::create([
            'question' => 'Hoe lang duurt een reparatie?',
            'answer' => 'Meestal dezelfde dag.',
            'keywords' => 'duur,wachten',
            'is_active' => true,
            'embedding' => [0.0, 1.0, 0.0],
            'embedding_model' => 'test-model',
        ]);
    }

    public function test_cosine_similarity_math(): void
    {
        $gen = new ChatAnswerGenerator();

        $this->assertEquals(1.0, round($gen->cosine([1, 0, 0], [1, 0, 0]), 4));
        $this->assertEquals(0.0, round($gen->cosine([1, 0, 0], [0, 1, 0]), 4));
        $this->assertEquals(0.0, $gen->cosine([0, 0, 0], [1, 0, 0]));
    }

    public function test_arabic_normalization_strips_plurals_and_prefixes(): void
    {
        $this->assertEquals('لابتوب', \App\Services\Chat\ArabicText::normalize('لابتوبات'));
        $this->assertEquals('موظف', \App\Services\Chat\ArabicText::normalize('موظفين'));
        $this->assertEquals('تواصل', \App\Services\Chat\ArabicText::normalize('التواصل'));
        $this->assertEquals('ا', \App\Services\Chat\ArabicText::normalize('أ'));
        $this->assertContains('laptop', \App\Services\Chat\ArabicText::toDutchTerms(['لابتوب']));
    }

    public function test_embedding_retrieval_picks_closest_faq(): void
    {
        config(['services.embedding.model' => 'test-model']);
        $this->seedFaqs();
        $this->mockEmbedding(['contact' => [1.0, 0.0, 0.0], 'reparatie' => [0.0, 1.0, 0.0]]);

        $gen = new ChatAnswerGenerator();
        $ref = new \ReflectionMethod($gen, 'findFaqs');
        $ref->setAccessible(true);

        $found = $ref->invoke($gen, 'hoe kom ik met jullie in contact');
        $this->assertNotEmpty($found);
        $this->assertStringContainsString('contact', mb_strtolower($found[0]['question']));
    }

    public function test_keyword_fallback_when_embedding_fails(): void
    {
        config(['services.embedding.model' => 'test-model']);
        $this->seedFaqs();

        $failing = new class implements EmbeddingClientInterface {
            public function embed(string $text): array
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

        $gen = new ChatAnswerGenerator();
        $ref = new \ReflectionMethod($gen, 'findFaqs');
        $ref->setAccessible(true);

        $found = $ref->invoke($gen, 'wat is jullie telefoonnummer voor contact');
        $this->assertNotEmpty($found);
        $this->assertStringContainsString('contact', mb_strtolower($found[0]['question']));
    }

    public function test_no_vectors_means_keyword_fallback(): void
    {
        config(['services.embedding.model' => 'test-model']);
        // Geen embeddings opgeslagen → findFaqsByEmbedding geeft null → fallback.
        ChatFaq::create([
            'question' => 'Wat zijn jullie openingstijden?',
            'answer' => 'Maandag tot vrijdag.',
            'is_active' => true,
        ]);
        $this->mockEmbedding([]);

        $gen = new ChatAnswerGenerator();
        $ref = new \ReflectionMethod($gen, 'findFaqs');
        $ref->setAccessible(true);

        $found = $ref->invoke($gen, 'wanneer zijn jullie open');
        $this->assertNotEmpty($found);
        $this->assertStringContainsString('openingstijden', mb_strtolower($found[0]['question']));
    }
}
