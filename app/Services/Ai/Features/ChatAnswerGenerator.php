<?php

namespace App\Services\Ai\Features;

use App\Models\ChatConversation;
use App\Models\ChatFaq;
use App\Services\Ai\AiService;
use App\Services\Chat\ChatCmsContext;
use App\Services\Chat\ChatProductSearch;

/**
 * Genereert een AI-antwoord voor de website-chat.
 *
 * Gronding in volgorde: producten (bij koopintentie) → CMS → FAQ's.
 * Geeft via [handoff_offer] aan als de AI het echt niet weet.
 */
class ChatAnswerGenerator
{
    public function __construct(
        protected ?ChatProductSearch $products = null,
        protected ?ChatCmsContext $cms = null
    ) {
        $this->products ??= new ChatProductSearch();
        $this->cms ??= new ChatCmsContext();
    }

    /**
     * @return array{text: string, handoff_offer: bool, products: array<int, array<string, mixed>>}
     */
    public function generate(ChatConversation $conversation, string $userText, bool $hasPhoto = false): array
    {
        $faqs = $this->findFaqs($userText);
        $history = $this->history($conversation);

        // Beslissende FAQ-match → EXACT antwoord (vertaald, feiten ongewijzigd).
        $exactAnswer = null;
        if (isset($faqs[0])) {
            $top = $faqs[0];
            if (($top['sim'] ?? 0) >= 0.55 || ($top['score'] ?? 0) >= 3) {
                $exactAnswer = $top['answer'];
            }
        }

        $productResult = $this->wantsProducts($userText)
            ? $this->products->search($userText, 6)
            : ['budget' => null, 'products' => []];

        // Budget uit context ("rond 1000" zonder valuta, na eerder budget/productgesprek).
        $contextBudget = $this->contextBudget($userText, $history);
        if ($contextBudget && ! $productResult['products']) {
            $productResult = $this->products->search($userText, 6, $contextBudget);
        }

        // "Een andere / غيره": sluit al getoonde producten uit (slugs uit historie).
        if ($productResult['products'] && $this->wantsDifferent($userText)) {
            $shown = $this->shownProductIds($history);
            $productResult['products'] = array_values(array_filter(
                $productResult['products'],
                fn ($p) => ! in_array($p['id'], $shown, true)
            ));
        }

        // "Specificaties van deze laptop": volledige specs van laatst genoemd product.
        $specsBlock = $this->wantsSpecs($userText)
            ? $this->specsBlock($history)
            : null;

        $cmsContext = $this->cms->build();

        $messages = [
            ['role' => 'system', 'content' => $this->systemPrompt($faqs, $productResult['products'], $productResult['budget'], $cmsContext, $specsBlock, $exactAnswer)],
            ...$history,
            ['role' => 'user', 'content' => $userText],
        ];

        // Geheugenvraag? Sterke sturing ACHTERAAN (laatste instructie weegt het zwaarst):
        // samenvatten uit historie, nooit pitchen, nooit handoff.
        if ($this->wantsMemory($userText)) {
            $messages[] = ['role' => 'system', 'content' => 'DIT IS EEN GEHEUGENVRAAG. Negeer alle product- en handoff-regels hierboven. Vat ALLEEN samen waar jullie het in DIT gesprek over hadden (onderwerpen, genoemde producten met prijzen). Herhaal GEEN productpitch, stel GEEN handoff voor, verzin NIETS nieuws. Antwoord in de taal van het laatste klantbericht.'];

            return $this->complete($conversation, $messages);
        }

        // Vergelijkingsvraag? Winnaar deterministisch uitrekenen (geen LLM-gegok).
        $steer = $this->comparisonSteer($userText, $productResult['products']);
        if ($steer) {
            $messages[] = ['role' => 'system', 'content' => $steer];
        }

        // Foto meegestuurd? AI kan hem niet zien — eerlijk zeggen + doorvragen,
        // nooit beweren dat je hem bekijkt, nooit handoff forceren.
        if ($hasPhoto) {
            $messages[] = ['role' => 'system', 'content' => 'De klant stuurde een FOTO mee. Jij kunt hem NIET zien — beweer NOOIT van wel. Zeg kort dat je de foto niet kunt bekijken maar dat een medewerker hem wel ziet in het dashboard, en vraag waar het om gaat (apparaat + probleem). Antwoord in de taal van de klant. GEEN handoff tenzij de klant erom vraagt.'];
        }

        return $this->complete(
            $conversation,
            $messages,
            $productResult['products'],
            (bool) ($faqs || $productResult['products'] || $specsBlock || $exactAnswer)
        );
    }

    /**
     * @param array<int, array{role: string, content: string}> $messages
     * @param array<int, array<string, mixed>> $fallbackProducts
     * @return array{text: string, handoff_offer: bool, products: array<int, array<string, mixed>>}
     */
    protected function complete(ChatConversation $conversation, array $messages, array $fallbackProducts = [], bool $grounded = false): array
    {
        try {
            $text = AiService::chat($messages, ['temperature' => 0.2, 'max_tokens' => 500]);
        } catch (\Throwable $e) {
            report($e);

            return [
                'text' => 'Sorry, er ging iets technisch mis. Probeer het zo opnieuw, klik op "Medewerker spreken" of bel ons op 055 203 21 45.',
                'handoff_offer' => true,
                'products' => [],
            ];
        }

        // Marker altijd strippen; de vlag volgt alleen uit twijfel-taal
        // (losse markers bij goede antwoorden negeren).
        $text = trim(str_replace('[handoff_offer]', '', $text));

        // Alleen bestaande product-ids uit de payload mogen als kaart renderen.
        // Zonder markers: toon de top-3 zoekresultaten alsnog als kaarten.
        $ids = [];
        if (preg_match_all('/\[product:(\d+)\]/', $text, $m)) {
            $ids = array_map('intval', $m[1]);
        }
        $text = trim((string) preg_replace('/\[product:\d+\]/', '', $text));
        $cards = $ids
            ? $this->products->cardsForIds($ids)
            : array_slice($fallbackProducts, 0, 3);

        // Marker altijd strippen; de vlag volgt alleen uit twijfel-taal
        // (losse markers bij goede antwoorden negeren).
        $text = trim(str_replace('[handoff_offer]', '', $text));

        // Met gronding (faq/product/specs/exact) hoort geen handoff-aanbod:
        // knip aanbod-/vleierij-zinnen van de STAART (feiten blijven staan).
        if ($grounded) {
            $text = $this->stripTailOffers($text);
            $text = trim((string) preg_replace('/\s{2,}/u', ' ', $text));
        }

        $handoff = $this->looksClueless($text);

        return ['text' => $text, 'handoff_offer' => $handoff, 'products' => $cards];
    }

    /**
     * Deterministische sturing voor goedkoopste/duerste-vragen.
     *
     * @param array<int, array<string, mixed>> $products
     */
    protected function comparisonSteer(string $userText, array $products): ?string
    {
        if (! $products) {
            return null;
        }
        $t = mb_strtolower($userText);
        $cheapest = (bool) preg_match('/goedkoopste|cheapest|ارخص|الأرخص|اقل سعر|أقل سعر/u', $t);
        $expensive = (bool) preg_match('/duurste|most expensive|اغلى|الأغلى|اعلى سعر|أعلى سعر/u', $t);
        if (! $cheapest && ! $expensive) {
            return null;
        }

        $sorted = $products;
        usort($sorted, fn ($a, $b) => $a['price'] <=> $b['price']);
        $winner = $cheapest ? $sorted[0] : end($sorted);
        $label = $cheapest ? 'goedkoopste' : 'duurste';

        return 'VERGELIJKING: de '.$label.' uit de lijst hierboven is "'.($winner['brand'] ? $winner['brand'].' ' : '').$winner['title']
            .'" (€'.number_format((float) $winner['price'], 2, ',', '.').'). '
            .'Begin je antwoord EXACT met deze zin: "De '.$label.' is de '.($winner['brand'] ? $winner['brand'].' ' : '').$winner['title']
            .' voor €'.number_format((float) $winner['price'], 2, ',', '.').'." '
            .'Beantwoord daarna de specificatie-vraag met zijn specs uit de lijst. Noem daarna eventueel 1 alternatief. '
            .'Antwoord in de taal van de klant. GEEN handoff.';
    }

    /**
     * Hybride FAQ-zoekopdracht: embeddings + keywords UNIE (geen van beide
     * mag de ander blokkeren) — embeddings eerst op score.
     *
     * @return array<int, array{question: string, answer: string, sim?: float, score?: int}>
     */
    protected function findFaqs(string $userText): array
    {
        $embedded = $this->findFaqsByEmbedding($userText) ?? [];
        $keyworded = $this->findFaqsByKeywords($userText);

        $merged = [];
        foreach (array_merge($embedded, $keyworded) as $item) {
            $key = mb_strtolower($item['question']);
            if (! isset($merged[$key])) {
                $merged[$key] = $item;
            } else {
                $merged[$key]['sim'] = max($merged[$key]['sim'] ?? 0, $item['sim'] ?? 0);
                $merged[$key]['score'] = max($merged[$key]['score'] ?? 0, $item['score'] ?? 0);
            }
        }

        uasort($merged, function ($a, $b) {
            $sa = ($a['sim'] ?? 0) * 10 + ($a['score'] ?? 0);
            $sb = ($b['sim'] ?? 0) * 10 + ($b['score'] ?? 0);

            return $sb <=> $sa;
        });

        return array_slice(array_values($merged), 0, 3);
    }

    /**
     * @return array<int, array{question: string, answer: string}>|null null = niet beschikbaar, fallback gebruiken
     */
    protected function findFaqsByEmbedding(string $userText): ?array
    {
        try {
            $queryVector = AiService::embed($userText);
        } catch (\Throwable $e) {
            return null;
        }

        if (! $queryVector) {
            return null;
        }

        $model = (string) config('services.embedding.model');
        $faqs = ChatFaq::active()
            ->where('embedding_model', $model)
            ->whereNotNull('embedding')
            ->get();

        if ($faqs->isEmpty()) {
            return null;
        }

        $queryWords = $this->queryWords($userText);

        $scored = [];
        foreach ($faqs as $faq) {
            $vector = is_array($faq->embedding) ? array_map('floatval', $faq->embedding) : [];
            if (count($vector) !== count($queryVector)) {
                continue;
            }
            $sim = $this->cosine($queryVector, $vector);
            // Exacte keyword-hit krijgt een bonus (bv. telefoonnummers).
            if ($queryWords && $this->keywordHit($queryWords, $faq)) {
                $sim += 0.15;
            }
            $scored[] = ['sim' => $sim, 'faq' => $faq];
        }

        usort($scored, fn ($a, $b) => $b['sim'] <=> $a['sim']);

        $top = array_filter(
            array_slice($scored, 0, 3),
            fn ($s) => $s['sim'] >= 0.25
        );

        if (! $top) {
            return [];
        }

        return array_map(
            fn ($s) => ['question' => $s['faq']->question, 'answer' => $s['faq']->answer, 'sim' => round($s['sim'], 3)],
            array_values($top)
        );
    }

    /**
     * Terugval: genormaliseerde keyword-scoring (geen API nodig).
     *
     * @return array<int, array{question: string, answer: string}>
     */
    protected function findFaqsByKeywords(string $userText): array
    {
        $words = $this->queryWords($userText);

        if (! $words) {
            return [];
        }

        $candidates = ChatFaq::active()->orderBy('sort_order')->orderBy('id')->get();

        $scored = [];
        foreach ($candidates as $faq) {
            $haystack = $this->normalizeWord($faq->question.' '.$faq->answer.' '.($faq->keywords ?? ''));
            $score = 0;
            foreach ($words as $w) {
                if (str_contains($haystack, $w)) {
                    $score += mb_strlen($w) >= 5 ? 2 : 1;
                }
            }
            if ($score > 0) {
                $scored[] = ['score' => $score, 'faq' => $faq];
            }
        }

        usort($scored, fn ($a, $b) => $b['score'] <=> $a['score']
            ?: $a['faq']->sort_order <=> $b['faq']->sort_order);

        return array_map(
            fn ($s) => ['question' => $s['faq']->question, 'answer' => $s['faq']->answer, 'score' => $s['score']],
            array_slice($scored, 0, 3)
        );
    }

    /**
     * @return array<int, string>
     */
    protected function queryWords(string $userText): array
    {
        return collect(preg_split('/\s+/u', mb_strtolower($userText)))
            ->map(fn ($w) => $this->normalizeWord($w))
            ->filter(fn ($w) => mb_strlen($w) >= 3)
            ->unique()
            ->take(8)
            ->values()
            ->all();
    }

    protected function keywordHit(array $words, ChatFaq $faq): bool
    {
        $haystack = $this->normalizeWord($faq->question.' '.($faq->keywords ?? ''));
        foreach ($words as $w) {
            if (str_contains($haystack, $w)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param array<int, float> $a
     * @param array<int, float> $b
     */
    public function cosine(array $a, array $b): float
    {
        $dot = 0.0;
        $normA = 0.0;
        $normB = 0.0;
        $n = min(count($a), count($b));
        for ($i = 0; $i < $n; $i++) {
            $dot += $a[$i] * $b[$i];
            $normA += $a[$i] * $a[$i];
            $normB += $b[$i] * $b[$i];
        }
        if ($normA <= 0.0 || $normB <= 0.0) {
            return 0.0;
        }

        return $dot / (sqrt($normA) * sqrt($normB));
    }

    /**
     * Arabische + basis-normalisatie (gedeeld met product-zoekopdracht).
     */
    protected function normalizeWord(string $word): string
    {
        return \App\Services\Chat\ArabicText::normalize($word);
    }

    /**
    /**
     * @param array<int, array{question: string, answer: string}> $faqs
     * @param array<int, array<string, mixed>> $products
     */
    protected function systemPrompt(array $faqs, array $products, ?int $budget, string $cmsContext, ?string $specsBlock, ?string $exactAnswer): string
    {
        $prompt = 'Je bent de vriendelijke chatassistent van Slimme-PC in Apeldoorn (computerreparatie, verkoop en IT-service). '
            .'Antwoord kort (max 3 zinnen!), zonder Markdown, zonder vriendelijke slotvragen. '
            .'Eindig NOOIT met zinnen als "hoe kan ik je helpen", "meer informatie nodig", "kan ik je ergens anders mee helpen" of varianten in welke taal dan ook — stop gewoon na het antwoord. '
            .'Verzin NOOIT prijzen, voorraad, links of telefoonnummers: gebruik alleen de gegevens hieronder. '
            .'Links die je noemt moeten letterlijk uit de gegevens hieronder komen (kopieer ze exact). '
            .'Als de klant om een medewerker vraagt, bevestig dat je dit doorzet. '
            .'Als je het antwoord ECHT niet weet (staat nergens hieronder), zeg dat eerlijk in één zin, '
            .'vraag "Wil je dat ik je doorverbind met een medewerker?" en zet AAN HET EINDE exact dit merkteken: [handoff_offer]. '
            .'Stel de medewerker-vraag NOOIT na een goed inhoudelijk antwoord — alleen bij echte twijfel. '
            .'Gaat de vraag nergens over Slimme-PC, reparatie, verkoop, IT, prijzen of contact (onzin of een ander onderwerp)? '
            .'Zeg dan dat je alleen Slimme-PC-vragen beantwoordt en sluit OOK af met [handoff_offer]. '
            .'Als er hieronder producten staan EN de klant vraagt naar een product, advies of aanbod, '
            .'noem er 1–3 met naam, prijs en link — zeg dan NOOIT dat je het niet weet. '
            .'Staan er hieronder producten, FAQ\'s of details? Dan is de "alleen Slimme-PC-vragen"-zin VERBODEN — '
            .'er IS gronding, dus altijd inhoudelijk antwoorden. '
            .'Bij geheugen-, specificatie- of bedankvragen: GEEN productpitch herhalen, gewoon antwoorden. '
            .'Vraagt de klant wat je kunt, waar je mee helpt of wat Slimme-PC doet? '
            .'Som dan kort op uit Kennisbank/Website-gegevens en vraag waarmee je kunt helpen — dit is GEEN reden voor [handoff_offer]. '
            .'Is het bericht alleen een begroeting (hallo/merhaba/مرحبا/hi/hey, eventueel met "kun je me helpen")? '
            .'Groet kort terug in die taal + vraag waarmee je kunt helpen — NOOIT "alleen Slimme-PC-vragen", NOOIT handoff. '
            .'TAALREGEL (belangrijkste regel): detecteer de taal van het LAATSTE klantbericht en antwoord VOLLEDIG in die taal. '
            .'Is het laatste bericht Arabisch, antwoord volledig in het Arabisch. Is het Engels, antwoord in het Engels. '
            .'Alleen als het laatste bericht Nederlands is, antwoord je in het Nederlands. '
            .'Eerdere berichten in een andere taal negeer je voor de taalkeuze.';

        if ($faqs) {
            $prompt .= "\n\nKennisbank (gebruik deze antwoorden als ze passen):";
            foreach ($faqs as $i => $faq) {
                $prompt .= "\n".($i + 1).'. Vraag: '.$faq['question']."\nAntwoord: ".$faq['answer'];
            }
        }

        if ($exactAnswer) {
            $prompt .= "\n\nEXACT ANTWOORD — dit is HET antwoord op de klantvraag. "
                ."Vertaal het naar de taal van de klant, maar verander feiten, cijfers, namen en links NOOIT:\n"
                .$exactAnswer;
        }

        if ($cmsContext !== '') {
            $prompt .= "\n\nWebsite-gegevens (echte pagina's en links — kopieer links exact):\n".$cmsContext;
        }

        if ($products) {
            $prompt .= "\n\nBeschikbare producten (noem max 3 bij naam + prijs, zet per product exact [product:{id}] erachter):";
            foreach ($products as $p) {
                $prompt .= "\n- [product:{$p['id']}] {$p['brand']} {$p['title']} — €".number_format($p['price'], 2, ',', '.')
                    .' ('.($p['in_stock'] ? 'op voorraad' : 'NIET op voorraad').($p['delivery'] ? ', '.$p['delivery'] : '').')'
                    .(! empty($p['specs']) ? ' — specs: '.$p['specs'] : '')
                    .' — '.$p['url'];
            }
            if ($budget) {
                $prompt .= "\nBudget van de klant: rond €{$budget} — sorteer op dichtstbijzijnde prijs.";
            }
            $prompt .= "\nVergelijkingsvragen (goedkoopste/duurste/beste specificaties) beantwoord je UITSLUITEND met deze lijst: reken zelf (laagste/hogste prijs) en noem de winnaar met naam, prijs en link.";
        }

        if ($specsBlock) {
            $prompt .= "\n\n".$specsBlock;
        }

        return $prompt;
    }

    /**
     * Korte bevestiging door de AI zelf, in de taal van de klant.
     * De taal volgt uit het geciteerde klantbericht (geen detectiecode).
     * Valt terug op Nederlands bij een LLM-fout.
     */
    public function confirmation(ChatConversation $conversation, string $kind): string
    {
        $history = $this->history($conversation);
        $lastCustomer = '';
        foreach (array_reverse($history) as $m) {
            if ($m['role'] === 'user' && $m['content'] !== '') {
                $lastCustomer = mb_substr($m['content'], 0, 500);
                break;
            }
        }
        $instruction = $kind === 'thanks'
            ? 'Schrijf een kort bedankje (max 2 zinnen): het ticket is aangemaakt en we reageren per e-mail.'
            : 'Schrijf een korte ticketbevestiging (max 2 zinnen): het ticket is aangemaakt en een medewerker neemt het over, even geduld.';
        $userContent = $instruction;
        if ($lastCustomer !== '') {
            $userContent .= ' BELANGRIJKSTE REGEL: schrijf VOLLEDIG in DEZELFDE TAAL als dit klantbericht (letterlijk overnemen van de taal, geen uitzonderingen): "'.$lastCustomer.'"';
        }
        $messages = [
            ['role' => 'system', 'content' => 'Je bent de chatassistent van Slimme-PC. Kort, zonder Markdown, geen slotvragen, geen handoff-aanbod.'],
            ...$history,
            ['role' => 'user', 'content' => $userContent],
        ];

        try {
            return AiService::chat($messages, ['temperature' => 0.3, 'max_tokens' => 150]);
        } catch (\Throwable $e) {
            report($e);

            return $kind === 'thanks'
                ? 'Bedankt! Je ticket is aangemaakt — we reageren per e-mail.'
                : 'Ticket aangemaakt! Een medewerker neemt dit gesprek over — even geduld alsjeblieft.';
        }
    }

    /**
     * @return array<int, array{role: string, content: string}>
     */
    protected function history(ChatConversation $conversation): array
    {
        return $conversation->messages()
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

    /**
     * Koopintentie? Budget alleen is al genoeg (bv. "rond 600 euro").
     */
    protected function wantsProducts(string $userText): bool
    {
        if (preg_match('/(\d{2,5})\s*(€|euro|يورو)/iu', $userText)) {
            return true;
        }

        return $this->hasProductIntent($userText);
    }

    /**
     * Koopintentie-heuristiek (NL + AR triggers).
     */
    protected function hasProductIntent(string $userText): bool
    {
        $t = mb_strtolower($userText);

        return (bool) preg_match('/laptop|computer|pc\b|gaming|tablet|ipad|telefoon|iphone|smartphone|console|playstation|xbox|kopen|koop|prijs|prijzen|product|producten|euro|€|voorraad|op voorraad|aanbod|ssd|ram|geheugen|processor|scherm|beeldscherm|merk|model|aanbieding|korting|لابتوب|لالبتوب|منتجات|منتج|كمبيوتر|جوال|ايفون|هاتف|سعر|اسعار|شراء|اشتري|متوفر|متوفرة|العاب|بلاي|اكس بوكس|تابلت|ايباد|شاشة|رام|ذاكرة|معالج|خصم|عرض|بكم/u', $t);
    }

    /**
     * Geheugenvraag ("waar hadden we het over", "عن شو حكينا", samenvatten)?
     */
    protected function wantsMemory(string $userText): bool
    {
        return (bool) preg_match('/waar hadden|waar ging het over|wat hebben we besproken|vat samen|samenvatting|eerder gezegd|عن شو حكينا|عن ايش حكينا|شو حكينا|لخص|ملخص|what did we (talk|discuss)|summar/ui', $userText);
    }

    /**
     * Budget uit gesprekscontext: kaal getal (3-4 cijfers, bv. "rond 1000")
     * terwijl eerder al over budget/producten is gesproken, óf als het
     * bericht zelf al productintentie heeft ("منتجات بسعر 500").
     *
     * @param array<int, array{role: string, content: string}> $history
     */
    protected function contextBudget(string $userText, array $history): ?int
    {
        if (preg_match('/(\d{2,5})\s*(€|euro|يورو)/iu', $userText)) {
            return null;
        }
        if (! preg_match('/\b(\d{3,4})\b/u', $userText, $m)) {
            return null;
        }
        if ($this->hasProductIntent($userText)) {
            return (int) $m[1];
        }
        foreach (array_reverse($history) as $msg) {
            if (preg_match('/(\d{2,5})\s*(€|euro|يورو)/iu', $msg['content'] ?? '')
                || str_contains($msg['content'] ?? '', '/webshop/')) {
                return (int) $m[1];
            }
        }

        return null;
    }

    /**
     * "Een andere / غيره": klant wil iets anders dan getoond.
     */
    protected function wantsDifferent(string $userText): bool
    {
        return (bool) preg_match('/\b(ander|andere|alternatief|other|another|else)\b|غير|آخر|اخر|ثاني|ثانية|تاني|بديل|غيره/u', mb_strtolower($userText));
    }

    /**
     * Specificatie-vraag over "dit/deze" product ("مواصفات هذه اللابتوب").
     */
    protected function wantsSpecs(string $userText): bool
    {
        return (bool) preg_match('/specificaties|specs|eigenschappen|kenmerken|مواصفات|مميزات|تفاصيل/u', mb_strtolower($userText));
    }

    /**
     * Product-ids die al in de thread staan (via /webshop/-slugs in historie).
     *
     * @param array<int, array{role: string, content: string}> $history
     * @return array<int, int>
     */
    protected function shownProductIds(array $history): array
    {
        $slugs = [];
        foreach ($history as $m) {
            if (preg_match_all('#/webshop/[\w-]+/([\w-]+)#u', $m['content'], $mm)) {
                array_push($slugs, ...$mm[1]);
            }
        }
        if (! $slugs) {
            return [];
        }

        return \App\Models\Product::whereIn('slug', array_unique($slugs))->pluck('id')->map('intval')->all();
    }

    /**
     * Volledige specs van het laatst genoemde product (voor specificatie-vragen).
     *
     * @param array<int, array{role: string, content: string}> $history
     */
    protected function specsBlock(array $history): ?string
    {
        $ids = $this->shownProductIds($history);
        if (! $ids) {
            return null;
        }
        $product = \App\Models\Product::with('category')->find(end($ids));
        if (! $product) {
            return null;
        }

        $lines = [
            'Details van "'.$product->brand.' '.$product->title.'" (gebruik DIT bij specificatie-vragen, met link '.
                route('webshop.product', [$product->category?->slug ?? 'laptops', $product->slug]).'):',
            '- Prijs: €'.number_format((float) $product->discounted_price, 2, ',', '.')
                .' ('.(($product->stock_status ?? 'in_stock') === 'in_stock' ? 'op voorraad' : 'NIET op voorraad').')',
        ];
        foreach ((array) ($product->features ?? []) as $f) {
            $t = is_array($f) ? ($f['title'] ?? '') : '';
            $v = is_array($f) ? ($f['value'] ?? '') : (string) $f;
            if ($t !== '' || $v !== '') {
                $lines[] = '- '.trim($t.': '.$v, ': ');
            }
        }
        foreach (array_slice((array) ($product->highlights ?? []), 0, 4) as $h) {
            if (is_array($h) && ! empty($h['title'])) {
                $lines[] = '- '.trim($h['title'].': '.($h['subtitle'] ?? ''), ': ');
            }
        }

        return implode("\n", $lines);
    }

    /**
     * Knip aanbod-/vleierij-zinnen van de STAART (max 3, min 1 zin blijft).
     * - Medewerker-zinnen gaan er ALTIJD uit bij gronding (ook met cijfers).
     * - Overige vleierij alleen zonder feiten (cijfers/links/winkel/contact).
     */
    protected function stripTailOffers(string $text): string
    {
        for ($i = 0; $i < 3; $i++) {
            if (! preg_match('/^(.*[.؟?!])\s*([^.؟?!]+[.؟?!]?)$/us', $text, $m)) {
                break;
            }
            $tail = trim($m[2]);
            if ($tail === '' || trim($m[1]) === '') {
                break;
            }
            $isOffer = (bool) preg_match('/موظف|medewerker|employee|doorverbind|handover|أوصلك|إحالة|تحويل|أحولك/iu', $tail);
            $isNag = (bool) preg_match('/feel free|let me know|laat het me weten|neem contact|aanvullende|extra hulp|مساعدة إضافية|مزيد من المساعدة|further assistance|additional help|happy to help|يسعدني|كيف يمكنني مساعدتك|hoe kan ik[^.?!]*helpen|kan ik je[^.?!]*helpen|meer informatie nodig|هل تحتاج|فلا تتردد|لا تتردد|أخبرني|أعلمني/iu', $tail);
            $hasFacts = (bool) preg_match('/\d{3,}|https?:\/\/|webshop|winkel|tariev|prijz|contact|تواصل|اتصل|رقم|موقع|reparatie|aanmelden|055|@|afspraak|موعد|حجز|أسعار|سعر|€/iu', $tail);
            if ($isOffer || ($isNag && ! $hasFacts)) {
                $text = trim($m[1]);
                continue;
            }
            break;
        }

        return $text;
    }

    /**
     * Knip generieke slotzinnen ("laat het me weten", "كيف يمكنني مساعدتك"...)
     * van het EINDE — alleen bij gronding, max 2 zinnen, minstens 1 zin blijft.
     */
    /**
     * Valt terug op handoff als het antwoord klinkt als "weet ik niet"
     * (NL/AR/EN — inclusief beleefde deflecties).
     */
    protected function looksClueless(string $text): bool
    {
        $t = mb_strtolower($text);

        return (bool) preg_match('/weet (het )?niet|geen idee|kan (je )?niet helpen|geen antwoord|medewerker|doorverbind|alleen .*vragen over|buiten mijn|niet verder helpen|لا أعرف|لا اعرف|لست متأكد|لا أستطيع|لا استطيع|لا يمكنني|موظف|عذر|آسف.*فقط|i don.t know|not sure|can.t help/u', $t);
    }
}
