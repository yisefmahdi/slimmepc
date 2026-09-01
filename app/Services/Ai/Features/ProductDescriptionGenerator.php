<?php

namespace App\Services\Ai\Features;

use App\Services\Ai\Contracts\AiClientInterface;
use App\Services\Ai\Prompts\ProductPromptBuilder;
use App\Services\Ai\Search\WebSearchService;

class ProductDescriptionGenerator
{
    protected AiClientInterface $aiClient;
    protected WebSearchService $searchService;

    public function __construct(
        AiClientInterface $aiClient,
        ?WebSearchService $searchService = null
    ) {
        $this->aiClient = $aiClient;
        $this->searchService = $searchService ?? new WebSearchService();
    }

    /**
     * Generate a rich e-commerce product description.
     *
     * @param array<string, mixed> $productData
     * @param array<string, mixed> $options
     * @return array{description: string, search_results: array<int, array{title: string, snippet: string, url: string}>, search_count: int}
     */
    public function generate(array $productData, array $options = []): array
    {
        $enableSearch = $options['enable_search'] ?? true;
        $additionalInstructions = $options['additional_instructions'] ?? null;
        $searchResults = [];

        // 1. Search the web for specs if enabled
        if ($enableSearch) {
            $searchResults = $this->searchService->searchProduct($productData, 4);
        }

        // 2. Build Prompts
        $systemPrompt = ProductPromptBuilder::buildSystemPrompt();
        $userPrompt = ProductPromptBuilder::buildUserPrompt(
            $productData,
            $searchResults,
            $additionalInstructions
        );

        $messages = [
            ['role' => 'system', 'content' => $systemPrompt],
            ['role' => 'user', 'content' => $userPrompt],
        ];

        // 3. Call AI Client
        $rawOutput = $this->aiClient->chat($messages, [
            'model'       => $options['model'] ?? null,
            'temperature' => 0.7,
            'max_tokens'  => $options['max_tokens'] ?? 1000,
        ]);

        // 4. Clean output: strip markdown code blocks if the model wrapped the HTML in ```html ... ```
        $cleaned = $this->cleanHtmlOutput($rawOutput);

        return [
            'description'    => $cleaned,
            'search_results' => $searchResults,
            'search_count'   => count($searchResults),
        ];
    }

    /**
     * Clean markdown code fences if returned by the LLM.
     */
    protected function cleanHtmlOutput(string $content): string
    {
        $trimmed = trim($content);

        // Remove ```html and ``` fences
        if (preg_match('/^```(?:html)?\s*([\s\S]*?)\s*```$/i', $trimmed, $matches)) {
            $trimmed = trim($matches[1]);
        }

        // Collapse extra consecutive blank lines between HTML tags so rich text editors don't create huge gaps
        $trimmed = preg_replace('/>\s*\n\s*</', ">\n<", $trimmed);

        return $trimmed;
    }
}
