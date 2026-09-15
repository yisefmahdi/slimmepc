<?php

namespace App\Services\Ai\Contracts;

interface EmbeddingClientInterface
{
    /**
     * Embed one text into a float vector.
     *
     * @return array<int, float>
     */
    public function embed(string $text): array;

    /**
     * Embed multiple texts (order preserved).
     *
     * @param array<int, string> $texts
     * @return array<int, array<int, float>>
     */
    public function embedMany(array $texts): array;

    /**
     * Check if the client is properly configured and available.
     */
    public function isAvailable(): bool;
}
