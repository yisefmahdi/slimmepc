<?php

namespace App\Services\Ai\Clients;

use App\Services\Ai\Contracts\EmbeddingClientInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class OpenAiEmbeddingClient implements EmbeddingClientInterface
{
    protected ?string $apiKey;
    protected string $apiUrl;
    protected string $model;
    protected int $dimensions;
    protected int $timeout;

    public function __construct(
        ?string $apiKey = null,
        ?string $apiUrl = null,
        ?string $model = null,
        ?int $dimensions = null,
        ?int $timeout = null
    ) {
        $this->apiKey = $apiKey ?? config('services.embedding.api_key');
        $this->apiUrl = $apiUrl ?? config('services.embedding.api_url', 'https://api.openai.com/v1/embeddings');
        $this->model = $model ?? config('services.embedding.model', 'text-embedding-3-small');
        $this->dimensions = $dimensions ?? (int) config('services.embedding.dimensions', 1536);
        $this->timeout = $timeout ?? (int) config('services.embedding.timeout', 30);
    }

    public function isAvailable(): bool
    {
        return !empty($this->apiKey);
    }

    public function embed(string $text): array
    {
        $vectors = $this->embedMany([$text]);

        return $vectors[0];
    }

    public function embedMany(array $texts): array
    {
        if (!$this->isAvailable()) {
            throw new RuntimeException('Embedding API-sleutel ontbreekt. Voeg OPENAI_API_KEY toe aan je .env bestand.');
        }

        $inputs = array_map(fn ($t) => mb_substr(trim((string) $t), 0, 8000), array_values($texts));

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer '.trim($this->apiKey),
                'Content-Type' => 'application/json',
            ])
            ->timeout($this->timeout)
            ->post($this->apiUrl, [
                'model' => $this->model,
                'input' => $inputs,
            ]);

            if ($response->successful()) {
                $data = $response->json('data', []);
                $vectors = [];
                foreach ($data as $row) {
                    $vec = array_map('floatval', (array) ($row['embedding'] ?? []));
                    if (count($vec) === $this->dimensions) {
                        $vectors[] = $vec;
                    }
                }
                if (count($vectors) === count($inputs)) {
                    return $vectors;
                }
            }

            $errorMessage = $response->json('error.message') ?? $response->body();

            Log::error('Embedding API Error', [
                'status' => $response->status(),
                'error' => $errorMessage,
            ]);

            if ($response->status() === 401) {
                throw new RuntimeException('Ongeldige Embedding API-sleutel. Controleer je .env.');
            }

            throw new RuntimeException("Embedding fout: {$errorMessage}");
        } catch (\Throwable $e) {
            if ($e instanceof RuntimeException) {
                throw $e;
            }

            Log::error('Embedding Exception: '.$e->getMessage());
            throw new RuntimeException('Fout bij verbinden met Embedding API: '.$e->getMessage());
        }
    }
}
