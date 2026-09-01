<?php

namespace App\Services\Ai\Contracts;

interface SearchDriverInterface
{
    /**
     * Search the web for a given query.
     *
     * @param string $query The search query
     * @param int $limit Maximum number of results
     * @return array<int, array{title: string, snippet: string, url: string}>
     */
    public function search(string $query, int $limit = 5): array;
}
