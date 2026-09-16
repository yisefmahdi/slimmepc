<?php

namespace App\Services\Ai\Features;

use App\Models\ChatConversation;
use App\Services\Ai\AiService;
use App\Services\Ai\Features\ChatTools\ChatTool;
use App\Services\Ai\Features\ChatTools\ProductDetailsTool;
use App\Services\Ai\Features\ChatTools\RequestHandoffTool;
use App\Services\Ai\Features\ChatTools\SearchKnowledgeTool;
use App\Services\Ai\Features\ChatTools\SearchProductsTool;
use App\Services\Ai\Features\ChatTools\SiteInfoTool;
use App\Services\Chat\ChatProductSearch;
use Illuminate\Support\Facades\Log;

/**
 * De Slimme-PC chat-agent.
 *
 * Eén LLM met gereedschap (function-calling) begrijpt de klantvraag,
 * roept zelf de juiste tools aan (kennisbank / website-gegevens /
 * producten) en formuleert het antwoord. Geen keywords: al het begrip
 * zit bij de agent. De request_handoff-tool is de enige bron voor
 * de medewerker-knop (gestructureerd, geen tekst-marker).
 *
 * Contract naar ChatController: generate() geeft altijd
 * ['text', 'handoff_offer', 'products'] terug.
 */
class ChatAgent
{
    protected const MAX_TOOL_ROUNDS = 3;

    /**
     * Tijd-budget per chatbericht (seconden). Altijd ruim onder de
     * PHP-limiet (60s): de gebruiker krijgt nooit een HTTP 500 door
     * een trage provider, maar een vriendelijke fallback + handoff.
     */
    protected const CALL_TIMEOUT = 8;

    protected const TIME_BUDGET = 30;

    /** @var array<string, ChatTool> */
    protected array $tools;

    protected bool $handoffRequested = false;

    public function __construct(?array $tools = null)
    {
        $this->tools = $tools ?? [
            'search_knowledge' => new SearchKnowledgeTool,
            'get_site_info' => new SiteInfoTool,
            'search_products' => new SearchProductsTool,
            'get_product_details' => new ProductDetailsTool,
            'request_handoff' => new RequestHandoffTool,
        ];
    }

    /**
     * @return array{text: string, handoff_offer: bool, products: array<int, array<string, mixed>>}
     */
    public function generate(ChatConversation $conversation, string $userText, bool $hasPhoto = false): array
    {
        $started = microtime(true);
        $history = $this->history($conversation);

        $messages = [
            ['role' => 'system', 'content' => $this->systemPrompt()],
            ...$history,
            ['role' => 'user', 'content' => $userText],
        ];

        if ($hasPhoto) {
            $messages[] = ['role' => 'system', 'content' => 'De klant stuurde een FOTO mee. Jij kunt hem NIET zien — beweer NOOIT van wel. Zeg kort dat je de foto niet kunt bekijken maar dat een medewerker hem wel ziet in het dashboard, en vraag waar het om gaat (apparaat + probleem). Antwoord in de taal van de klant. GEEN handoff tenzij de klant erom vraagt.'];
        }

        $toolDefs = array_map(fn (ChatTool $t) => $t->definition(), array_values($this->tools));

        try {
            $text = $this->runLoop($messages, $toolDefs, $started);
        } catch (\Throwable $e) {
            $elapsed = round(microtime(true) - $started, 1);
            Log::warning('[chat-agent] generate failed, trying fallback', [
                'conversation_id' => $conversation->id,
                'elapsed' => $elapsed,
                'error' => mb_substr($e->getMessage(), 0, 300),
            ]);
            // Budget op? Direct de vriendelijke fallback (geen nieuwe
            // LLM-call meer) — anders één normale call als terugval.
            if ($elapsed >= self::TIME_BUDGET) {
                return $this->providerFailure($conversation->id, $elapsed, 'budget-exhausted: '.$e->getMessage());
            }
            try {
                $text = $this->fallbackSinglePass($userText, $history, $hasPhoto);
            } catch (\Throwable $e2) {
                return $this->providerFailure($conversation->id, round(microtime(true) - $started, 1), $e2->getMessage());
            }
        }

        if ($text === null || trim($text) === '') {
            return $this->technicalError();
        }

        $result = $this->finalize($text);

        // Structurele anti-herhaling: is het antwoord (bijna) identiek
        // aan het vorige AI-antwoord terwijl de klant iets ANDERS vroeg?
        // Dan één herkansing met sturing — taal-onafhankelijk, geen lijsten.
        if ($this->isVerbatimRepeat($result['text'], $history, $userText)
            && (microtime(true) - $started) < self::TIME_BUDGET) {
            Log::info('[chat-agent] verbatim repeat detected, retrying', [
                'conversation_id' => $conversation->id,
            ]);
            $retryMessages = array_merge($messages, [
                ['role' => 'system', 'content' => 'Je vorige antwoord was een letterlijke herhaling van je eerdere antwoord, terwijl de klant iets ANDERS vroeg. Geef NU een nieuw, specifiek antwoord op de LAATSTE klantvraag alleen. Antwoord volledig in de taal van dat laatste bericht.'],
            ]);
            try {
                $this->handoffRequested = false;
                $retryText = $this->runLoop($retryMessages, $toolDefs, $started);
                if ($retryText !== null && trim($retryText) !== '') {
                    return $this->finalize($retryText);
                }
            } catch (\Throwable $e) {
                Log::warning('[chat-agent] repeat retry failed, keeping original', [
                    'conversation_id' => $conversation->id,
                    'error' => mb_substr($e->getMessage(), 0, 200),
                ]);
            }
        }

        return $result;
    }

    /**
     * Bijna-identiek aan het vorige AI-antwoord bij een ANDERE klantvraag?
     *
     * @param  array<int, array{role: string, content: string}>  $history
     */
    protected function isVerbatimRepeat(string $text, array $history, string $userText): bool
    {
        if (mb_strlen(trim($text)) < 40) {
            return false;
        }
        $lastAssistant = '';
        $lastUser = '';
        foreach (array_reverse($history) as $m) {
            if ($lastAssistant === '' && $m['role'] === 'assistant' && trim($m['content']) !== '') {
                $lastAssistant = $m['content'];
            } elseif ($lastAssistant !== '' && $m['role'] === 'user' && trim($m['content']) !== '') {
                $lastUser = $m['content'];
                break;
            }
        }
        if ($lastAssistant === '' || $lastUser === '') {
            return false;
        }
        // Zelfde klantvraag opnieuw? Dan is hetzelfde antwoord terecht.
        if (trim(mb_strtolower($lastUser)) === trim(mb_strtolower($userText))) {
            return false;
        }
        similar_text($text, $lastAssistant, $percent);

        return $percent >= 90;
    }

    /**
     * De agentic loop: LLM → tool-aanroepen uitvoeren → resultaten
     * terugvoeren → herhalen tot er een eindantwoord is.
     *
     * @param  array<int, array<string, mixed>>  $messages
     * @param  array<int, array<string, mixed>>  $toolDefs
     */
    protected function runLoop(array $messages, array $toolDefs, float $started): ?string
    {
        for ($round = 0; $round < self::MAX_TOOL_ROUNDS; $round++) {
            if (microtime(true) - $started >= self::TIME_BUDGET) {
                throw new RuntimeException('Chat-agent time budget exceeded at round '.($round + 1));
            }
            $res = AiService::chatWithTools($messages, $toolDefs, ['temperature' => 0.3, 'max_tokens' => 500, 'timeout' => self::CALL_TIMEOUT]);

            if (empty($res['calls'])) {
                return $res['content'];
            }

            $messages[] = [
                'role' => 'assistant',
                'content' => $res['content'],
                'tool_calls' => array_map(fn (array $c) => [
                    'id' => $c['id'],
                    'type' => 'function',
                    'function' => ['name' => $c['name'], 'arguments' => json_encode($c['arguments'] ?? [])],
                ], $res['calls']),
            ];

            foreach ($res['calls'] as $call) {
                $messages[] = [
                    'role' => 'tool',
                    'tool_call_id' => $call['id'],
                    'content' => $this->executeTool($call['name'], $call['arguments'] ?? []),
                ];
            }
        }

        // Ronden op: vraag een afsluitend antwoord zonder verdere tools.
        // Handoff loopt via de request_handoff-tool; is die al aangeroepen
        // dan staat de vlag — vraag hier ALLEEN om de eindtekst.
        $messages[] = ['role' => 'system', 'content' => 'Je hebt genoeg informatie verzameld (of er is niets meer te vinden). Geef NU het eindantwoord aan de klant op basis van alle tool-resultaten hierboven. Roep GEEN tools meer aan — ook request_handoff niet meer. Vind je echt geen antwoord, zeg dat eerlijk in één zin en vraag of je de klant met een medewerker mag doorverbinden (de knop wordt dan niet getoond, maar de vraag wel).'];

        return AiService::chat($messages, ['temperature' => 0.3, 'max_tokens' => 500, 'timeout' => self::CALL_TIMEOUT]);
    }

    protected function executeTool(string $name, array $args): string
    {
        $tool = $this->tools[$name] ?? null;
        if (! $tool) {
            return 'Onbekende tool "'.$name.'" — negeer dit en ga verder zonder.';
        }

        if ($name === 'request_handoff') {
            $this->handoffRequested = true;
        }

        try {
            $started = microtime(true);
            $out = $tool->execute($args);
            Log::info('[chat-agent] tool executed', [
                'tool' => $name,
                'args' => mb_substr(json_encode($args, JSON_UNESCAPED_UNICODE) ?: '', 0, 300),
                'ms' => (int) ((microtime(true) - $started) * 1000),
                'result_chars' => mb_strlen((string) $out),
            ]);
        } catch (\Throwable $e) {
            report($e);

            return 'Tool "'.$name.'" gaf een technische fout — ga verder met wat je wel hebt of bied een medewerker aan.';
        }

        return mb_substr(trim((string) $out), 0, 4000);
    }

    /**
     * Terugval voor providers zonder function-calling: één call met
     * semantische kennisbank + website-gegevens vooraf opgehaald.
     *
     * @param  array<int, array{role: string, content: string}>  $history
     */
    protected function fallbackSinglePass(string $userText, array $history, bool $hasPhoto): string
    {
        $knowledge = (new SearchKnowledgeTool)->execute(['query' => $userText]);
        $site = (new SiteInfoTool)->execute([]);

        $messages = [
            ['role' => 'system', 'content' => $this->systemPromptFallback($knowledge, $site)],
            ...$history,
            ['role' => 'user', 'content' => $userText],
        ];
        if ($hasPhoto) {
            $messages[] = ['role' => 'system', 'content' => 'De klant stuurde een FOTO mee die je NIET kunt zien — beweer NOOIT van wel.'];
        }

        return AiService::chat($messages, ['temperature' => 0.3, 'max_tokens' => 500, 'timeout' => self::CALL_TIMEOUT]);
    }

    /**
     * Eerste pad-segmenten van de eigen shop-routes (geen keywords,
     * maar de route-tabel van de app).
     *
     * @var array<int, string>
     */
    protected const SHOP_PATHS = [
        'tarieven', 'reparatie-aanmelden', 'afspraak', 'webshop', 'diensten',
        'contact', 'over-ons', 'checkout', 'cart', 'wishlist', 'mijn-bestellingen',
    ];

    /**
     * @return array{text: string, handoff_offer: bool, products: array<int, array<string, mixed>>}
     */
    protected function finalize(string $text): array
    {
        // Oude marker wegpoetsen (telt niet meer mee — zie request_handoff).
        $text = trim(str_replace('[handoff_offer]', '', $text));

        // Markdown-links omzetten naar platte tekst + losse URL, zodat de
        // widget-linkify ze klikbaar maakt ("[Afspraak](url)" → "Afspraak: url").
        $text = (string) preg_replace_callback(
            '/\[([^\]]{1,80})\]\((https?:\/\/[^\s)]+|[^\s)]+)\)/u',
            fn (array $m) => trim($m[1]).': '.$m[2],
            $text
        );

        // Product-kaarten: alleen expliciet gemarkeerde, bestaande ids.
        $ids = [];
        if (preg_match_all('/\[product:(\d+)\]/', $text, $m)) {
            $ids = array_map('intval', $m[1]);
        }
        $text = trim((string) preg_replace('/\[product:\d+\]/', '', $text));
        $text = trim((string) preg_replace('/\s{2,}/u', ' ', $text));
        $text = $this->normalizeShopLinks($text);

        $cards = $ids ? (new ChatProductSearch)->cardsForIds($ids) : [];

        // Gestructureerd: alleen een request_handoff-tool-aanroep toont de
        // knop. De oude [handoff_offer]-marker wordt nog weggepoetst als
        // een model hem toch schrijft, maar telt NIET meer mee.
        $handoff = $this->handoffRequested;

        return ['text' => $text, 'handoff_offer' => $handoff, 'products' => $cards];
    }

    /**
     * Herschrijft shop-links naar de eigen host: als het model een URL
     * met een vreemde host schrijft (geleerd domein i.p.v. tool-data),
     * wordt de host vervangen door de echte APP_URL-host. Externe links
     * (WhatsApp, Google Maps, ...) blijven onaangeroerd. Blote
     * host/path-links zonder scheme krijgen het scheme van de app.
     */
    protected function normalizeShopLinks(string $text): string
    {
        $base = rtrim((string) config('app.url'), '/');
        $baseHost = (string) parse_url($base, PHP_URL_HOST);
        if ($baseHost === '') {
            return $text;
        }
        $prefix = implode('|', self::SHOP_PATHS);

        // URLs met scheme: vreemde host + shop-pad → eigen host.
        $text = (string) preg_replace_callback(
            '#https?://([^\s)/]+)(/[^\s)]*)?#u',
            function (array $m) use ($base, $baseHost, $prefix): string {
                $path = rtrim($m[2] ?? '/', '.,;:!?');
                if ($path === '') {
                    $path = '/';
                }
                if (strcasecmp($m[1], $baseHost) !== 0 && preg_match('#^/('.$prefix.')(/|$)#', $path)) {
                    return $base.$path;
                }

                return $m[0];
            },
            $text
        );

        // Blote host/path zonder scheme (bv. 127.0.0.1:8000/afspraak).
        $text = (string) preg_replace_callback(
            '#(^|[\s(])([\w.-]+(?::\d+)?)(/('.$prefix.')(\\/[^\s)]*)?)#u',
            function (array $m) use ($base): string {
                return $m[1].$base.rtrim($m[3], '.,;:!?');
            },
            $text
        );

        return $text;
    }

    /**
     * Vriendelijke fallback bij provider-storing/timeout (nooit HTTP 500
     * voor de gebruiker). Timeouts worden wél gelogd met context.
     *
     * @return array{text: string, handoff_offer: bool, products: array<int, array<string, mixed>>}
     */
    protected function providerFailure(int $conversationId, float $elapsed, string $error): array
    {
        Log::warning('[chat-agent] provider failure, friendly fallback shown', [
            'conversation_id' => $conversationId,
            'elapsed' => $elapsed,
            'error' => mb_substr($error, 0, 300),
        ]);

        return [
            'text' => 'Ik kan je vraag momenteel niet goed verwerken. Wil je het opnieuw proberen of met een medewerker spreken?',
            'handoff_offer' => true,
            'products' => [],
        ];
    }

    /**
     * @return array{text: string, handoff_offer: bool, products: array<int, array<string, mixed>>}
     */
    protected function technicalError(): array
    {
        return [
            'text' => 'Sorry, er ging iets technisch mis. Probeer het zo opnieuw, klik op "Medewerker spreken" of bel ons op 055 203 21 45.',
            'handoff_offer' => true,
            'products' => [],
        ];
    }

    protected function systemPrompt(): string
    {
        return 'Je bent de vriendelijke chatassistent-agent van Slimme-PC in Apeldoorn (computerreparatie, verkoop en IT-service). '
            .'Je hebt GEREEDSCHAP (tools): search_knowledge (kennisbank), get_site_info (echte diensten/links/contact), '
            .'search_products (webshop), get_product_details (specs van een genoemd product), '
            .'request_handoff (toont de knop "Medewerker spreken" — de ENIGE manier om handoff aan te bieden; nooit een tekst-marker). '
            .'WERKWIJZE: begrijp de klantvraag uit het laatste bericht + gespreksgeschiedenis. '
            .'Is het een begroeting, bedankje of vage opmerking ("ik heb een vraag", "?", "dank je")? Antwoord DAN DIRECT zonder tools: '
            .'bij een begroeting groet je kort terug in DIEZELFDE taal (nooit "graag gedaan" bij een groet), '
            .'bij een bedankje antwoord je warm en kort, VOLLEDIG in de taal van het bedankje zelf: '
            .'Nederlands bedankje → bv. "Graag gedaan! We helpen je graag weer."; '
            .'Arabisch bedankje (شكرا…) → bv. "العفو! نحن بخدمتك دائماً."; '
            .'Engels bedankje → bv. "You\'re welcome! Happy to help anytime.". '
            .'NOOIT een Nederlandse "graag gedaan" op een Arabisch of Engels bedankje. '
            .'Bij een vage vraag nodig de klant uit zijn vraag te stellen. '
            .'Is het een inhoudelijke vraag? Roep EERST de passende tool(s) aan: inhoudelijke vragen ALTIJD via search_knowledge, '
            .'links/telefoonnummers/diensten ALTIJD via get_site_info, koop-/advies-/budgetvragen ALTIJD via search_products, '
            .'specs van een genoemd product via get_product_details. '
            .'REGELS: antwoord kort (max 3 zinnen!), zonder Markdown, zonder vriendelijke slotvragen — stop na het antwoord. '
            .'Schrijf NOOIT Markdown-links als [tekst](url): schrijf de link-titel en daarna de blote URL los (bv. "Afspraak maken: https://…"), zodat hij klikbaar wordt. '
            .'Herschrijf NOOIT het domein van een URL: kopieer elke URL VOLLEDIG EXACT zoals hij in de tool-data staat (inclusief http(s):// en domein) — verzin geen "slimme-pc.nl"-variant uit jezelf. '
            .'Eindig NOOIT met "hoe kan ik je helpen / meer informatie nodig / kan ik je ergens anders mee helpen / كيف يمكنني مساعدتك / كيف أساعدك / هل تحتاج شيئاً آخر" of varianten in welke taal dan ook. '
            .'Verzin NOOIT prijzen, voorraad, links of telefoonnummers — gebruik ALLEEN tool-resultaten en kopieer links letterlijk. '
            .'GRONDING (harde regel): spreekt een tool-resultaat (kennisbank, website-gegevens, product) een feit, duur, prijs of beleid uit, dan wint dat ALTIJD van je eigen vermoeden — verzin nooit een "gemiddelde" of schatting ernaast. '
            .'TOPIC-FIT: gebruik een kennisbank-artikel alleen als het over hetzelfde onderwerp gaat als de klantvraag (een laptop-artikel geldt niet voor een iPhone-vraag) — bij twijfel behandel je de vraag als onbekend (eerlijk zeggen + request_handoff). '
            .'Een EXACT ANTWOORD uit de tool neem je altijd letterlijk over (alleen de taal vertalen). '
            .'Staat een dienst, prijs of beleid NERGENS in de tool-data (bv. smartphone-reparatie), presenteer hem dan NOOIT als bestaand: zeg dat je het niet kunt bevestigen en roep request_handoff aan. '
            .'LINKS (harde regel): noem je een pagina (Tarieven, Reparatie aanmelden, Afspraak, Contact, een dienst of webshop-categorie), zet de URL er ALTIJD direct bij, exact uit de tool-data — nooit een pagina-naam zonder URL, nooit extra links die er niet toe doen. '
            .'CONTEXT (verwijswoorden): "het/dit/dat/die/hiervan/daarvan" slaat op het LAATST besproken onderwerp of product uit DIT gesprek (bv. "wanneer komt die binnen" na MacBook = die MacBook; "specs hiervan" na een product = dat product). '
            .'Zijn er meerdere kandidaten, gis NIET maar vraag door ("bedoel je X of Y?"). '
            .'Herhaal GEEN link of antwoord dat je eerder in DIT gesprek al gaf, tenzij de klant er expliciet om vraagt. '
            .'Vraagt de klant expliciet om een medewerker? Roep DAN DIRECT request_handoff aan (reden: explicit_request) en schrijf dat hij via de knop "Medewerker spreken" hieronder een ticket aanmaakt — maak zelf GEEN ticket aan. '
            .'Antwoord NOOIT met alleen een identiteits-zin ("Ik ben de Slimme-PC assistent.") — geef altijd een inhoudelijk antwoord, of een handoff via request_handoff. '
            .'Weet je het antwoord ECHT niet (tools gaven niets bruikbaars)? Zeg dat eerlijk in één zin en roep request_handoff aan (reden: unknown). '
            .'Heeft het probleem menselijke diagnose nodig en hebben je verhelderende vragen niets opgeleverd? Stel eerst 1-2 gerichte diagnosvragen (wat gebeurt er precies? gaat het apparaat nog aan? zie je een foutmelding?) — pas als je echt niet verder kunt, roep je request_handoff aan (reden: human_diagnosis). '
            .'HANDOFF-PRECISIE (geen overaanbod): roep request_handoff NOOIT aan bij begroetingen, bedankjes, openingstijden, contactgegevens, FAQ-antwoorden die je vond, gevonden producten of bekende pagina-links — daar volstaat het antwoord zelf, zonder slotvraag. '
            .'Gaat de vraag nergens over Slimme-PC, reparatie, verkoop, IT, prijzen of contact? '
            .'Zeg dan dat je alleen Slimme-PC-vragen beantwoordt en roep OOK request_handoff aan. '
            .'Bij producten: noem max 3 met naam + prijs en zet per product exact [product:{id}] erachter. '
            .'Gaat de vraag nergens over Slimme-PC, reparatie, verkoop, IT, prijzen of contact? '
            .'Zeg dan dat je alleen Slimme-PC-vragen beantwoordt en sluit OOK af met [handoff_offer]. '
            .'TAALREGEL (belangrijkste regel): detecteer de taal van het LAATSTE klantbericht en antwoord VOLLEDIG in die taal. '
            .'Is het laatste bericht Arabisch (ook een simpele groet als مرحبا of كيف بتقدر تساعدني), antwoord volledig in het Arabisch — NOOIT Nederlands. '
            .'Is het Engels, antwoord in het Engels. '
            .'Alleen als het laatste bericht Nederlands is, antwoord je in het Nederlands. '
            .'Een Arabische begroeting beantwoord je met een Arabische groet (bv. "أهلاً! كيف أساعدك؟"), geen Nederlandse.';
    }

    protected function systemPromptFallback(string $knowledge, string $site): string
    {
        return $this->systemPrompt()
            ."\n\nKennisbank-zoekresultaat voor deze vraag:\n".$knowledge
            ."\n\nWebsite-gegevens (echte pagina's en links — kopieer links exact):\n".$site;
    }

    /**
     * @return array<int, array{role: string, content: string}>
     */
    protected function history(ChatConversation $conversation): array
    {
        // reorder(): de messages()-relatie heeft orderBy('id') ASC ingebakken;
        // zonder reorder() wint ASC het van orderByDesc en krijgt de LLM
        // de geschiedenis ACHTERSTEVOREN (plus de verkeerde 12 bij lange threads).
        return $conversation->messages()->reorder()
            ->orderByDesc('id')
            ->limit(12)
            ->get()
            ->reverse()
            ->map(fn ($m) => [
                'role' => $m->sender === 'customer' ? 'user' : 'assistant',
                'content' => (string) ($m->body ?? ''),
            ])
            ->filter(fn ($m) => $m['content'] !== '')
            ->values()
            ->all();
    }
}
