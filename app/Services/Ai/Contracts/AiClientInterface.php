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
     * Chat completion met function-calling (agentic loop).
     *
     * @param array<int, array<string, mixed>> $messages OpenAI-style messages
     *   (system/user/assistant/tool — assistant mag 'tool_calls' dragen).
     * @param array<int, array<string, mixed>> $tools OpenAI-style tool definitions
     *   (['type' => 'function', 'function' => ['name' => ..., 'description' => ..., 'parameters' => ...]]).
     * @param array<string, mixed> $options Model-opties (model, temperature, max_tokens).
     * @return array{content: ?string, calls: array<int, array{id: string, name: string, arguments: array<string, mixed>}>, raw_calls: array<int, array<string, mixed>>}
     */
    public function chatWithTools(array $messages, array $tools, array $options = []): array;

    /**
     * Check if the client is properly configured and available.
     */
    public function isAvailable(): bool;
}
