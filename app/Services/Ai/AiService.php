<?php

namespace App\Services\Ai;

use App\Services\Ai\Clients\OpenAiClient;
use App\Services\Ai\Contracts\AiClientInterface;
use App\Services\Ai\Features\ProductDescriptionGenerator;
use App\Services\Ai\Search\WebSearchService;

class AiService
{
    protected static ?AiClientInterface $client = null;
    protected static ?WebSearchService $searchService = null;

    /**
     * Get or set the AI client.
     */
    public static function client(): AiClientInterface
    {
        if (static::$client === null) {
            static::$client = new OpenAiClient();
        }

        return static::$client;
    }

    /**
     * Swap client (useful for unit testing or switching providers).
     */
    public static function setClient(AiClientInterface $client): void
    {
        static::$client = $client;
    }

    /**
     * Get or set the search service.
     */
    public static function search(): WebSearchService
    {
        if (static::$searchService === null) {
            static::$searchService = new WebSearchService();
        }

        return static::$searchService;
    }

    /**
     * Generate an e-commerce product description using web search + AI.
     *
     * @param array<string, mixed> $productData
     * @param array<string, mixed> $options
     * @return array{description: string, search_results: array<int, array{title: string, snippet: string, url: string}>, search_count: int}
     */
    public static function generateProductDescription(array $productData, array $options = []): array
    {
        $generator = new ProductDescriptionGenerator(
            static::client(),
            static::search()
        );

        return $generator->generate($productData, $options);
    }

    /**
     * Perform a web search.
     *
     * @param string $query
     * @param int $limit
     * @return array<int, array{title: string, snippet: string, url: string}>
     */
    public static function searchWeb(string $query, int $limit = 5): array
    {
        return static::search()->search($query, $limit);
    }

    /**
     * Direct chat completion.
     *
     * @param array<int, array{role: string, content: string}> $messages
     * @param array<string, mixed> $options
     * @return string
     */
    public static function chat(array $messages, array $options = []): string
    {
        return static::client()->chat($messages, $options);
    }
}
