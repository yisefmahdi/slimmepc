<?php

namespace Tests\Unit;

use App\Models\Category;
use App\Models\ChatConversation;
use App\Models\ChatFaq;
use App\Models\Product;
use App\Services\Ai\AiService;
use App\Services\Ai\Contracts\AiClientInterface;
use App\Services\Ai\Contracts\EmbeddingClientInterface;
use App\Services\Ai\Features\ChatAgent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * De agent beslist zelf (geen keywords): scripted LLM + echte tools.
 */
class ChatAgentTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Scripted LLM: geeft per chatWithTools-call het volgende
     * script-item terug (content of tool-aanroep).
     */
    protected function scriptClient(array $script, ?array &$seen = null): AiClientInterface
    {
        return new class($script, $seen) implements AiClientInterface
        {
            public function __construct(private array $script, public ?array &$seen) {}

            public function chat(array $messages, array $options = []): string
            {
                $this->seen[] = ['chat', end($messages)];
                $next = array_shift($this->script);

                return is_string($next) ? $next : ($next['content'] ?? '');
            }

            public function chatWithTools(array $messages, array $tools, array $options = []): array
            {
                $this->seen[] = ['tools', end($messages)];
                $next = array_shift($this->script);
                if (is_string($next)) {
                    return ['content' => $next, 'calls' => [], 'raw_calls' => []];
                }

                return [
                    'content' => $next['content'] ?? null,
                    'calls' => $next['calls'] ?? [],
                    'raw_calls' => [],
                ];
            }

            public function isAvailable(): bool
            {
                return true;
            }
        };
    }

    protected function mockEmbedding(array $map): void
    {
        $mock = new class($map) implements EmbeddingClientInterface
        {
            public function __construct(private array $map) {}

            public function embed(string $text, array $options = []): array
            {
                foreach ($this->map as $needle => $vector) {
                    if (str_contains(mb_strtolower($text), mb_strtolower((string) $needle))) {
                        return $vector;
                    }
                }

                return [0.0, 0.0, 1.0];
            }

            public function embedMany(array $texts): array
            {
                return array_map(fn ($t) => $this->embed($t), $texts);
            }

            public function isAvailable(): bool
            {
                return true;
            }
        };

        AiService::setEmbeddingClient($mock);
    }

    private function thread(): ChatConversation
    {
        return ChatConversation::create([
            'guest_token' => Str::random(64),
            'name' => 'Test Klant',
            'email' => 'klant@test.nl',
            'status' => 'open',
            'last_activity_at' => now(),
        ]);
    }

    public function test_thanks_gets_direct_answer_without_tools(): void
    {
        $seen = [];
        AiService::setClient($this->scriptClient(['Graag gedaan! Laat het weten als je nog iets nodig hebt.'], $seen));

        $result = (new ChatAgent)->generate($this->thread(), 'dank u wel');

        expect($result['text'])->toContain('Graag gedaan')
            ->and($result['handoff_offer'])->toBeFalse()
            ->and($result['products'])->toBeEmpty()
            ->and($result['text'])->not->toContain('http');
        // Eén LLM-call, zonder tool-aanroepen.
        expect($seen)->toHaveCount(1);
    }

    public function test_agent_calls_knowledge_tool_for_content_question(): void
    {
        config(['services.embedding.model' => 'test-model']);
        ChatFaq::create([
            'question' => 'Hoe kan ik contact opnemen?',
            'answer' => 'Bel 055 203 21 45.',
            'is_active' => true,
            'embedding' => [1.0, 0.0, 0.0],
            'embedding_model' => 'test-model',
        ]);
        $this->mockEmbedding(['contact' => [1.0, 0.0, 0.0], 'telefoonnummer' => [1.0, 0.0, 0.0]]);

        $seen = [];
        AiService::setClient($this->scriptClient([
            ['content' => null, 'calls' => [
                ['id' => 'call_1', 'name' => 'search_knowledge', 'arguments' => ['query' => 'wat is jullie telefoonnummer']],
            ]],
            'Je kunt ons bellen op 055 203 21 45.',
        ], $seen));

        $result = (new ChatAgent)->generate($this->thread(), 'wat is jullie telefoonnummer');

        expect($result['text'])->toContain('055 203 21 45')
            ->and($result['handoff_offer'])->toBeFalse();
        expect($seen)->toHaveCount(2);
        // De tool-uitvoer ging echt door de echte kennisbank.
        expect($seen[1][1]['content'] ?? '')->toContain('055 203 21 45');
    }

    public function test_agent_marks_handoff_via_tool_when_tools_find_nothing(): void
    {
        config(['services.embedding.model' => 'test-model']);
        $this->mockEmbedding([]);

        $seen = [];
        AiService::setClient($this->scriptClient([
            ['content' => null, 'calls' => [
                ['id' => 'call_1', 'name' => 'search_knowledge', 'arguments' => ['query' => 'kunnen jullie mijn dak repareren']],
            ]],
            ['content' => null, 'calls' => [
                ['id' => 'call_2', 'name' => 'request_handoff', 'arguments' => ['reason' => 'unknown']],
            ]],
            'Dat weet ik eerlijk gezegd niet. Wil je dat ik je doorverbind met een medewerker?',
        ], $seen));

        $result = (new ChatAgent)->generate($this->thread(), 'kunnen jullie mijn dak repareren');

        expect($result['handoff_offer'])->toBeTrue()
            ->and($result['text'])->toContain('doorverbind');
    }

    public function test_agent_resolves_product_cards_from_markers(): void
    {
        $cat = Category::create(['name' => 'Laptops', 'status' => true, 'sort_order' => 0]);
        $p = Product::create([
            'category_id' => $cat->id, 'title' => 'HP 15s Agent Test', 'slug' => 'hp-15s-agent-'.Str::random(6),
            'brand' => 'HP', 'price' => 599, 'stock_status' => 'in_stock', 'status' => true,
            'description' => 'Betrouwbare HP laptop.',
        ]);

        $seen = [];
        AiService::setClient($this->scriptClient([
            ['content' => null, 'calls' => [
                ['id' => 'call_1', 'name' => 'search_products', 'arguments' => ['query' => 'laptop']],
            ]],
            'Deze HP 15s Agent Test voor € 599,00 past goed bij je. [product:'.$p->id.']',
        ], $seen));

        $result = (new ChatAgent)->generate($this->thread(), 'ik zoek een laptop');

        expect($result['products'])->toHaveCount(1)
            ->and($result['products'][0]['id'])->toBe($p->id)
            ->and($result['products'][0]['url'])->toContain('/webshop/')
            ->and($result['text'])->not->toContain('[product:');
    }

    public function test_short_thanks_shows_no_handoff_button(): void
    {
        $seen = [];
        AiService::setClient($this->scriptClient(['Graag gedaan!'], $seen));

        $result = (new ChatAgent)->generate($this->thread(), 'dank u wel');

        expect($result['text'])->toBe('Graag gedaan!')
            ->and($result['handoff_offer'])->toBeFalse()
            ->and($result['products'])->toBeEmpty();
    }

    public function test_identity_fragment_without_marker_shows_no_handoff(): void
    {
        // Marker-only: zonder marker van de agent géén knop,
        // ook niet bij een leeg identiteits-fragment.
        $seen = [];
        AiService::setClient($this->scriptClient(['Ik ben de Slimme-PC assistent.'], $seen));

        $result = (new ChatAgent)->generate($this->thread(), 'ik heb een vraag');

        expect($result['handoff_offer'])->toBeFalse();
    }

    public function test_markdown_links_become_plain_text_with_bare_url(): void
    {
        $seen = [];
        AiService::setClient($this->scriptClient(
            ['Je kunt een afspraak maken via [Afspraak maken](https://slimmepc.test/afspraak).'],
            $seen
        ));

        $result = (new ChatAgent)->generate($this->thread(), 'ik wil een afspraak maken');

        // Markdown wordt platte tekst + URL, en de host wordt
        // genormaliseerd naar de eigen APP_URL-host.
        expect($result['text'])->not->toContain('[Afspraak maken]')
            ->and($result['text'])->toContain(rtrim((string) config('app.url'), '/').'/afspraak');
    }

    public function test_stray_marker_alone_never_shows_handoff_button(): void
    {
        // Gestructureerd: alleen de request_handoff-tool toont de knop.
        // Een losse marker in tekst telt niet meer mee.
        $seen = [];
        AiService::setClient($this->scriptClient(
            ['Wij bieden data recovery aan en kunnen je bestanden redden. Wil je dat ik je doorverbind met een medewerker voor verdere hulp? [handoff_offer]'],
            $seen
        ));

        $result = (new ChatAgent)->generate($this->thread(), 'mijn laptop start niet meer op, ik wil mijn bestanden redden');

        expect($result['handoff_offer'])->toBeFalse()
            ->and($result['text'])->toContain('doorverbind')
            ->and($result['text'])->not->toContain('[handoff_offer]');
    }

    public function test_system_prompt_uses_handoff_tool_with_precision_rule(): void
    {
        $ref = new \ReflectionMethod(ChatAgent::class, 'systemPrompt');
        $ref->setAccessible(true);
        $prompt = $ref->invoke(new ChatAgent);

        expect($prompt)->toContain('request_handoff')
            ->and($prompt)->toContain('HANDOFF-PRECISIE');
    }

    public function test_shop_links_are_rewritten_to_app_host(): void
    {
        config(['app.url' => 'http://127.0.0.1:8000']);
        $seen = [];
        AiService::setClient($this->scriptClient(
            ['Bekijk onze Tarieven-pagina: https://slimme-pc.nl/tarieven en meld je aan via 127.0.0.1:8000/reparatie-aanmelden. WhatsApp: https://wa.me/31617100945.'],
            $seen
        ));

        $result = (new ChatAgent)->generate($this->thread(), 'waar vind ik tarieven?');

        expect($result['text'])->toContain('http://127.0.0.1:8000/tarieven')
            ->and($result['text'])->toContain('http://127.0.0.1:8000/reparatie-aanmelden')
            ->and($result['text'])->not->toContain('slimme-pc.nl')
            ->and($result['text'])->toContain('https://wa.me/31617100945');
    }

    public function test_verbatim_repeat_triggers_one_retry(): void
    {
        $c = $this->thread();
        $c->messages()->create(['sender' => 'customer', 'body' => 'mijn laptop start niet op', 'source' => 'widget']);
        $c->messages()->create(['sender' => 'ai', 'body' => 'Het lijkt erop dat je laptop niet opstart. Dit kan verschillende oorzaken hebben, zoals een probleem met de voeding of de hardware in je computer die stuk is gegaan.', 'source' => 'ai']);

        $seen = [];
        AiService::setClient($this->scriptClient([
            'Het lijkt erop dat je laptop niet opstart. Dit kan verschillende oorzaken hebben, zoals een probleem met de voeding of de hardware in je computer die stuk is gegaan.',
            'Een reparatie duurt meestal dezelfde dag.',
        ], $seen));

        $result = (new ChatAgent)->generate($c, 'hoe lang duurt een reparatie?');

        // Twee LLM-calls (origineel + herkansing), nieuw antwoord gebruikt.
        expect($seen)->toHaveCount(2)
            ->and($result['text'])->toContain('dezelfde dag');
    }

    public function test_provider_failure_returns_friendly_fallback_with_handoff(): void
    {
        $failing = new class implements AiClientInterface
        {
            public function chat(array $messages, array $options = []): string
            {
                throw new \RuntimeException('cURL error 28: SSL connection timeout');
            }

            public function chatWithTools(array $messages, array $tools, array $options = []): array
            {
                throw new \RuntimeException('cURL error 28: SSL connection timeout');
            }

            public function isAvailable(): bool
            {
                return true;
            }
        };
        AiService::setClient($failing);

        $result = (new ChatAgent)->generate($this->thread(), 'wat zijn jullie openingstijden?');

        expect($result['text'])->toContain('momenteel niet goed verwerken')
            ->and($result['handoff_offer'])->toBeTrue()
            ->and($result['products'])->toBeEmpty();
    }

    public function test_system_prompt_has_multilingual_thanks_examples(): void
    {
        $ref = new \ReflectionMethod(ChatAgent::class, 'systemPrompt');
        $ref->setAccessible(true);
        $prompt = $ref->invoke(new ChatAgent);

        expect($prompt)->toContain('نحن بخدمتك')
            ->and($prompt)->toContain('Graag gedaan! We helpen je graag weer.');
    }

    public function test_default_agent_has_handoff_tool(): void
    {
        $ref = new \ReflectionProperty(ChatAgent::class, 'tools');
        $ref->setAccessible(true);
        $tools = $ref->getValue(new ChatAgent);

        expect($tools)->toHaveKey('request_handoff');
    }

    public function test_handover_request_yields_offer_flag_not_ticket(): void
    {
        $seen = [];
        AiService::setClient($this->scriptClient(
            [
                ['content' => null, 'calls' => [
                    ['id' => 'call_1', 'name' => 'request_handoff', 'arguments' => ['reason' => 'explicit_request']],
                ]],
                'Klik hieronder op "Medewerker spreken" — er wordt dan een ticket voor je aangemaakt.',
            ],
            $seen
        ));

        $c = $this->thread();
        $result = (new ChatAgent)->generate($c, 'ik wil met een medewerker spreken');

        // Tekst-aanvraag = aanbod via tool, géén statuswijziging (doet pas de knop).
        expect($result['handoff_offer'])->toBeTrue()
            ->and($c->fresh()->status)->toBe('open');
    }
}
