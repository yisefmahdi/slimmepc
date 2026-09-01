<?php

namespace App\Services\Ai\Search;

use App\Services\Ai\Contracts\SearchDriverInterface;
use App\Services\Ai\Search\Drivers\DuckDuckGoDriver;

class WebSearchService
{
    protected SearchDriverInterface $driver;

    public function __construct(?SearchDriverInterface $driver = null)
    {
        $this->driver = $driver ?? new DuckDuckGoDriver();
    }

    /**
     * Search the web for a product to find hardware specifications and reviews.
     *
     * @param array<string, mixed> $productData
     * @param int $limit
     * @return array<int, array{title: string, snippet: string, url: string}>
     */
    public function searchProduct(array $productData, int $limit = 5): array
    {
        $title = trim($productData['title'] ?? '');
        $brand = trim($productData['brand'] ?? '');
        $sku = trim($productData['sku'] ?? '');

        // Build clean search query
        $terms = array_filter([$brand, $title, $sku]);
        if (empty($terms)) {
            return [];
        }

        $query = implode(' ', $terms) . ' specificaties specs';
        return $this->driver->search($query, $limit);
    }

    /**
     * Generic web search.
     *
     * @param string $query
     * @param int $limit
     * @return array<int, array{title: string, snippet: string, url: string}>
     */
    public function search(string $query, int $limit = 5): array
    {
        return $this->driver->search($query, $limit);
    }
}
