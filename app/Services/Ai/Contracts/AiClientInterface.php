<?php

namespace App\Services\Ai\Contracts;

interface AiClientInterface
{
    /**
     * Send a chat completion request.
     *
     * @param array<int, array{role: string, content: string}> $messages
     * @param array<string, mixed> $options
     * @return string The generated text response
     */
    public function chat(array $messages, array $options = []): string;

    /**
     * Check if the client is properly configured and available.
     */
    public function isAvailable(): bool;
}
