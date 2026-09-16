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
        return ! empty($this->apiKey);
    }

    /**
     * @param  array<int, array{role: string, content: string}>  $messages
     * @param  array<string, mixed>  $options
     */
    public function chat(array $messages, array $options = []): string
    {
        if (! $this->isAvailable()) {
            throw new RuntimeException('OpenAI API-sleutel ontbreekt. Voeg OPENAI_API_KEY toe aan je .env bestand.');
        }

        $data = $this->post([
            'model' => $options['model'] ?? $this->defaultModel,
            'messages' => $messages,
            'temperature' => $options['temperature'] ?? 0.7,
            'max_tokens' => $options['max_tokens'] ?? 1000,
        ], (int) ($options['timeout'] ?? $this->timeout));

        $content = $data['choices'][0]['message']['content'] ?? null;
        if ($content !== null) {
            return trim((string) $content);
        }

        throw new RuntimeException('OpenAI gaf geen tekst terug.');
    }

    /**
     * @param  array<int, array<string, mixed>>  $messages
     * @param  array<int, array<string, mixed>>  $tools
     * @param  array<string, mixed>  $options
     * @return array{content: ?string, calls: array<int, array{id: string, name: string, arguments: array<string, mixed>}>, raw_calls: array<int, array<string, mixed>>}
     */
    public function chatWithTools(array $messages, array $tools, array $options = []): array
    {
        if (! $this->isAvailable()) {
            throw new RuntimeException('OpenAI API-sleutel ontbreekt. Voeg OPENAI_API_KEY toe aan je .env bestand.');
        }

        $payload = [
            'model' => $options['model'] ?? $this->defaultModel,
            'messages' => $messages,
            'temperature' => $options['temperature'] ?? 0.3,
            'max_tokens' => $options['max_tokens'] ?? 500,
        ];
        if ($tools) {
            $payload['tools'] = array_values($tools);
            $payload['tool_choice'] = $options['tool_choice'] ?? 'auto';
        }

        $data = $this->post($payload, (int) ($options['timeout'] ?? $this->timeout));

        $message = $data['choices'][0]['message'] ?? [];
        $content = isset($message['content']) && $message['content'] !== null
            ? trim((string) $message['content'])
            : null;

        $calls = [];
        $raw = [];
        foreach ((array) ($message['tool_calls'] ?? []) as $tc) {
            if (! is_array($tc) || ($tc['type'] ?? 'function') !== 'function') {
                continue;
            }
            $raw[] = $tc;
            $args = [];
            $rawArgs = $tc['function']['arguments'] ?? '';
            if (is_string($rawArgs) && $rawArgs !== '') {
                $decoded = json_decode($rawArgs, true);
                if (is_array($decoded)) {
                    $args = $decoded;
                }
            } elseif (is_array($rawArgs)) {
                $args = $rawArgs;
            }
            $calls[] = [
                'id' => (string) ($tc['id'] ?? ''),
                'name' => (string) ($tc['function']['name'] ?? ''),
                'arguments' => $args,
            ];
        }

        return ['content' => $content === '' ? null : $content, 'calls' => $calls, 'raw_calls' => $raw];
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    protected function post(array $payload, ?int $timeout = null): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer '.trim((string) $this->apiKey),
                'Content-Type' => 'application/json',
            ])
                ->timeout($timeout ?? $this->timeout)
                ->post($this->apiUrl, $payload);

            if ($response->successful()) {
                $data = $response->json();
                if (is_array($data)) {
                    return $data;
                }
            }

            $errorCode = $response->json('error.code');
            $errorMessage = $response->json('error.message') ?? $response->body();

            Log::error('OpenAI API Error', [
                'status' => $response->status(),
                'code' => $errorCode,
                'error' => $errorMessage,
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

            Log::error('OpenAI Exception: '.$e->getMessage());
            throw new RuntimeException('Fout bij verbinden met OpenAI: '.$e->getMessage());
        }
    }
}
