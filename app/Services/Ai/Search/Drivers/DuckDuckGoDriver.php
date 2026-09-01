<?php

namespace App\Services\Ai\Search\Drivers;

use App\Services\Ai\Contracts\SearchDriverInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DuckDuckGoDriver implements SearchDriverInterface
{
    protected int $timeout;

    public function __construct(int $timeout = 4)
    {
        $this->timeout = $timeout;
    }

    /**
     * @param string $query
     * @param int $limit
     * @return array<int, array{title: string, snippet: string, url: string}>
     */
    public function search(string $query, int $limit = 5): array
    {
        $cleanQuery = trim($query);
        if ($cleanQuery === '') {
            return [];
        }

        // 1. Try DuckDuckGo HTML search first
        $results = $this->searchDdgHtml($cleanQuery, $limit);
        if (!empty($results)) {
            return $results;
        }

        // 2. Fallback: DuckDuckGo Instant Answers API + Wikipedia API
        return $this->searchFallback($cleanQuery, $limit);
    }

    /**
     * Search via DuckDuckGo HTML.
     *
     * @param string $query
     * @param int $limit
     * @return array<int, array{title: string, snippet: string, url: string}>
     */
    protected function searchDdgHtml(string $query, int $limit): array
    {
        try {
            $response = Http::withHeaders([
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36',
                'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
                'Accept-Language' => 'nl-NL,nl;q=0.9,en-US;q=0.8,en;q=0.7',
            ])
            ->timeout($this->timeout)
            ->get('https://html.duckduckgo.com/html/', [
                'q' => $query,
            ]);

            if (!$response->successful()) {
                return [];
            }

            $body = $response->body();
            $results = [];

            // Extract snippets
            if (preg_match_all('/<a[^>]*class="result__snippet[^>]*>(.*?)<\/a>/si', $body, $snippetMatches)) {
                preg_match_all('/<a[^>]*class="result__url[^>]*>(.*?)<\/a>/si', $body, $urlMatches);

                foreach ($snippetMatches[1] as $idx => $snippetHtml) {
                    if (count($results) >= $limit) {
                        break;
                    }

                    $snippet = trim(html_entity_decode(strip_tags($snippetHtml)));
                    $url = isset($urlMatches[1][$idx]) ? trim(strip_tags($urlMatches[1][$idx])) : '';

                    if ($snippet !== '') {
                        $results[] = [
                            'title'   => 'Specificaties ' . ($idx + 1),
                            'snippet' => $snippet,
                            'url'     => $url ? 'https://' . ltrim($url, '/') : '',
                        ];
                    }
                }
            }

            return $results;
        } catch (\Throwable $e) {
            Log::warning('DuckDuckGo HTML search error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Fallback search combining DDG Instant Answers and Wikipedia.
     *
     * @param string $query
     * @param int $limit
     * @return array<int, array{title: string, snippet: string, url: string}>
     */
    protected function searchFallback(string $query, int $limit): array
    {
        $results = [];

        // Try DDG Instant Answers
        try {
            $response = Http::withHeaders([
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)',
            ])
            ->timeout($this->timeout)
            ->get('https://api.duckduckgo.com/', [
                'q' => $query,
                'format' => 'json',
                'no_html' => 1,
                'skip_disambig' => 1,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $abstract = trim($data['AbstractText'] ?? '');
                $heading = trim($data['Heading'] ?? '');
                $url = trim($data['AbstractURL'] ?? '');

                if ($abstract !== '') {
                    $results[] = [
                        'title'   => $heading ?: $query,
                        'snippet' => $abstract,
                        'url'     => $url,
                    ];
                }
            }
        } catch (\Throwable $e) {
            // Silently continue to next fallback
        }

        // Try Wikipedia API (search for product terms)
        if (count($results) < $limit) {
            try {
                $wikiRes = Http::withHeaders([
                    'User-Agent' => 'SlimmePC-ProductBot/1.0 (info@slimme-pc.nl)',
                ])
                ->timeout($this->timeout)
                ->get('https://en.wikipedia.org/w/api.php', [
                    'action'   => 'query',
                    'list'     => 'search',
                    'srsearch' => $query,
                    'format'   => 'json',
                    'srlimit'  => $limit - count($results),
                ]);

                if ($wikiRes->successful()) {
                    $items = $wikiRes->json('query.search') ?? [];
                    foreach ($items as $item) {
                        $snippet = trim(html_entity_decode(strip_tags($item['snippet'] ?? '')));
                        if ($snippet !== '') {
                            $results[] = [
                                'title'   => $item['title'] ?? '',
                                'snippet' => $snippet,
                                'url'     => 'https://en.wikipedia.org/wiki/' . urlencode($item['title'] ?? ''),
                            ];
                        }
                    }
                }
            } catch (\Throwable $e) {
                // Ignore
            }
        }

        return array_slice($results, 0, $limit);
    }
}
