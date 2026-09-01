<?php

namespace App\Services\Ai\Clients;

use App\Services\Ai\Contracts\AiClientInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class OpenAiClient implements AiClientInterface
{
    protected ?string $apiKey;
    protected string $apiUrl;
    protected string $defaultModel;
    protected int $timeout;

    public function __construct(
        ?string $apiKey = null,
        ?string $apiUrl = null,
        ?string $defaultModel = null,
        ?int $timeout = null
    ) {
        $this->apiKey = $apiKey ?? config('services.openai.api_key');
        $this->apiUrl = $apiUrl ?? config('services.openai.api_url', 'https://api.openai.com/v1/chat/completions');
        $this->defaultModel = $defaultModel ?? config('services.openai.model', 'gpt-4o-mini');
        $this->timeout = $timeout ?? (int) config('services.openai.timeout', 30);
    }

    public function isAvailable(): bool
    {
        return !empty($this->apiKey);
    }

    /**
     * @param array<int, array{role: string, content: string}> $messages
     * @param array<string, mixed> $options
     * @return string
     */
    public function chat(array $messages, array $options = []): string
    {
        if (!$this->isAvailable()) {
            throw new RuntimeException('OpenAI API-sleutel ontbreekt. Voeg OPENAI_API_KEY toe aan je .env bestand.');
        }

        $model = $options['model'] ?? $this->defaultModel;
        $temperature = $options['temperature'] ?? 0.7;
        $maxTokens = $options['max_tokens'] ?? 1000;

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . trim($this->apiKey),
                'Content-Type'  => 'application/json',
            ])
            ->timeout($this->timeout)
            ->post($this->apiUrl, [
                'model'       => $model,
                'messages'    => $messages,
                'temperature' => $temperature,
                'max_tokens'  => $maxTokens,
            ]);

            if ($response->successful()) {
                $content = $response->json('choices.0.message.content');
                if ($content !== null) {
                    return trim($content);
                }
            }

            $errorCode = $response->json('error.code');
            $errorMessage = $response->json('error.message') ?? $response->body();

            Log::error('OpenAI API Error', [
                'status' => $response->status(),
                'code'   => $errorCode,
                'error'  => $errorMessage,
            ]);

            // Friendly error for insufficient quota / expired credits
            if ($errorCode === 'credit_balance_exhausted' || $errorCode === 'insufficient_quota' || str_contains($errorMessage, 'quota') || str_contains($errorMessage, 'credits')) {
                throw new RuntimeException('OpenAI saldo is ontoereikend (credit balance exhausted). Waardeer je OpenAI account op via platform.openai.com.');
            }

            if ($response->status() === 401) {
                throw new RuntimeException('Ongeldige OpenAI API-sleutel. Controleer je OPENAI_API_KEY in .env.');
            }

            throw new RuntimeException("OpenAI fout: {$errorMessage}");
        } catch (\Throwable $e) {
            if ($e instanceof RuntimeException) {
                throw $e;
            }

            Log::error('OpenAI Exception: ' . $e->getMessage());
            throw new RuntimeException('Fout bij verbinden met OpenAI: ' . $e->getMessage());
        }
    }
}
