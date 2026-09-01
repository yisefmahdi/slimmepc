<?php

namespace Tests\Unit;

use App\Services\Ai\AiService;
use App\Services\Ai\Contracts\AiClientInterface;
use App\Services\Ai\Contracts\SearchDriverInterface;
use App\Services\Ai\Features\ProductDescriptionGenerator;
use App\Services\Ai\Prompts\ProductPromptBuilder;
use App\Services\Ai\Search\WebSearchService;
use Tests\TestCase;

class AiServiceTest extends TestCase
{
    public function test_prompt_builder_contains_dutch_instructions_and_product_details(): void
    {
        $systemPrompt = ProductPromptBuilder::buildSystemPrompt();
        $this->assertStringContainsString('Slimme-PC', $systemPrompt);
        $this->assertStringContainsString('HTML', $systemPrompt);

        $userPrompt = ProductPromptBuilder::buildUserPrompt([
            'title' => 'HP Victus 15',
            'brand' => 'HP',
            'sku' => 'VIC-15-2026',
            'price' => 799.99,
            'features' => ['16GB RAM', '512GB SSD', 'RTX 4060'],
        ], [
            ['title' => 'HP Victus Specs', 'snippet' => 'Intel Core i7 13th Gen with RTX 4060 GPU', 'url' => 'https://example.com'],
        ]);

        $this->assertStringContainsString('HP Victus 15', $userPrompt);
        $this->assertStringContainsString('16GB RAM', $userPrompt);
        $this->assertStringContainsString('Intel Core i7', $userPrompt);
    }

    public function test_product_description_generator_with_mocked_client(): void
    {
        $mockAiClient = new class implements AiClientInterface {
            public function chat(array $messages, array $options = []): string
            {
                return "```html\n<p>De HP Victus 15 is een krachtige gaming laptop.</p>\n<h3>Belangrijkste kenmerken</h3>\n<ul><li><strong>GPU:</strong> RTX 4060</li></ul>\n```";
            }

            public function isAvailable(): bool
            {
                return true;
            }
        };

        $mockSearchDriver = new class implements SearchDriverInterface {
            public function search(string $query, int $limit = 5): array
            {
                return [
                    ['title' => 'Victus Review', 'snippet' => 'Snelle gaming laptop met top scherm', 'url' => 'https://test.nl'],
                ];
            }
        };

        $searchService = new WebSearchService($mockSearchDriver);
        $generator = new ProductDescriptionGenerator($mockAiClient, $searchService);

        $result = $generator->generate([
            'title' => 'HP Victus 15',
            'brand' => 'HP',
        ]);

        // Code fences should be stripped
        $this->assertStringNotContainsString('```html', $result['description']);
        $this->assertStringContainsString('<p>De HP Victus 15 is een krachtige gaming laptop.</p>', $result['description']);
        $this->assertEquals(1, $result['search_count']);
        $this->assertEquals('Victus Review', $result['search_results'][0]['title']);
    }

    public function test_ai_service_facade_swap_and_chat(): void
    {
        $mockAiClient = new class implements AiClientInterface {
            public function chat(array $messages, array $options = []): string
            {
                return 'Mocked AI response';
            }

            public function isAvailable(): bool
            {
                return true;
            }
        };

        AiService::setClient($mockAiClient);

        $response = AiService::chat([
            ['role' => 'user', 'content' => 'Hallo'],
        ]);

        $this->assertEquals('Mocked AI response', $response);
    }
}
