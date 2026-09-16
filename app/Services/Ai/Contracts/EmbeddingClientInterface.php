<?php

namespace App\Services\Ai\Contracts;

interface EmbeddingClientInterface
{
    /**
     * Embed one text into a float vector.
     *
     * @param  array<string, mixed>  $options  Provider-opties (o.a. 'timeout' in seconden).
     * @return array<int, float>
     */
    public function embed(string $text, array $options = []): array;

    /**
     * Embed multiple texts (order preserved).
     *
     * @param  array<int, string>  $texts
     * @return array<int, array<int, float>>
     */
    public function embedMany(array $texts): array;

    /**
     * Check if the client is properly configured and available.
     */
    public function isAvailable(): bool;
}
