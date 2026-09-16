<?php

namespace App\Services\Ai\Features\ChatTools;

use App\Services\Chat\ChatProductSearch;

/**
 * Zoekt beschikbare webshop-producten. De agent roept dit aan bij
 * koopintentie, budget- of adviesvragen — nooit andersom.
 */
class SearchProductsTool implements ChatTool
{
    public function __construct(protected ?ChatProductSearch $search = null)
    {
        $this->search ??= new ChatProductSearch;
    }

    public function definition(): array
    {
        return [
            'type' => 'function',
            'function' => [
                'name' => 'search_products',
                'description' => 'Zoek in de Slimme-PC webshop naar echte beschikbare producten (naam, prijs, voorraad, link, specs, categorie). Gebruik dit als de klant iets wil kopen, om advies vraagt, een budget noemt of naar producten/prijzen/aanbod vraagt. STAPPEN: (1) haal eerst via get_site_info de echte webshop-categorieën + slugs op, (2) kies de slug die past bij de vraag en geef hem mee als category (laptop-vraag → laptop-categorie, nooit andersom), (3) formuleer de query kort in het Nederlands (vertaal zelf: bv. gaming-laptop in het Arabisch → "gaming laptop"). FIT-REGEL met voorbeelden (jij past dit toe op de specs in het resultaat): "gaming laptop" = ALLEEN een laptop MET aparte videokaart in de specs — een laptop met alleen processor-graphics en een desktop zijn GEEN gaming laptop; "SSD" = ALLEEN items uit de onderdelen-categorie. Noem in je antwoord ALLEEN passende producten (max 3 met naam + prijs en exact [product:{id}] erachter); past niets, zeg dat eerlijk in plaats van iets onpassends te noemen.',
                'parameters' => [
                    'type' => 'object',
                    'properties' => [
                        'query' => [
                            'type' => 'string',
                            'description' => 'Korte Nederlandse zoekopdracht, 1–4 inhoudelijke woorden (bv. "gaming laptop", "SSD", "laptop student"). Vertaal zelf uit andere talen.',
                        ],
                        'category' => [
                            'type' => ['string', 'null'],
                            'description' => 'Exacte categorie-slug uit get_site_info (bv. "labtop", "gaming-pc", "onderdelen"). Filtert hard: alleen producten uit deze categorie komen terug. ALTIJD meegeven bij productvragen.',
                        ],
                        'budget' => [
                            'type' => ['integer', 'null'],
                            'description' => 'Budget in euro\'s als de klant een bedrag noemt (bv. 600 — jij haalt dit zelf uit het gesprek). Anders null.',
                        ],
                    ],
                    'required' => ['query'],
                    'additionalProperties' => false,
                ],
            ],
        ];
    }

    public function execute(array $args): string
    {
        $query = trim((string) ($args['query'] ?? ''));
        $budget = isset($args['budget']) && is_numeric($args['budget']) ? (int) $args['budget'] : null;
        $category = isset($args['category']) && is_string($args['category']) ? trim($args['category']) : null;

        if ($query === '' && ! $budget && ! $category) {
            return 'Geen zoekopdracht meegegeven — geen producten gevonden.';
        }

        try {
            $result = $this->search->search($query !== '' ? $query : (string) $budget, 6, $budget, $category ?: null);
        } catch (\Throwable $e) {
            report($e);

            return 'Productzoekopdracht mislukt (technische fout). Bied een medewerker aan.';
        }

        $products = $result['products'] ?? [];
        if (! $products) {
            return 'Geen passende producten gevonden in de webshop (alle zoeklagen geprobeerd). Zeg eerlijk dat je niets passends vindt — beweer NOOIT dat er "geen laptops" zijn als je alleen op woorden zocht — en bied een medewerker aan of verwijs naar de webshop-categorieën via get_site_info.';
        }

        $lines = ['Beschikbare producten — let op de CATEGORIE per product, noem ALLEEN passende (max 3 bij naam + prijs, zet per product exact [product:{id}] erachter):'];
        foreach ($products as $p) {
            $name = trim(($p['brand'] ?? '').' '.($p['title'] ?? ''));
            if ($p['brand'] && str_starts_with(mb_strtolower((string) $p['title']), mb_strtolower((string) $p['brand']))) {
                $name = (string) $p['title'];
            }
            $lines[] = '- [product:'.$p['id'].'] '.$name
                .' — €'.number_format((float) $p['price'], 2, ',', '.')
                .' ('.($p['in_stock'] ? 'op voorraad' : 'NIET op voorraad').($p['delivery'] ? ', '.$p['delivery'] : '').')'
                .' [categorie: '.($p['category'] ?? 'onbekend').']'
                .(! empty($p['specs']) ? ' — specs: '.$p['specs'] : '')
                .' — '.$p['url'];
        }
        if (! empty($result['budget'])) {
            $lines[] = 'Budget van de klant: rond €'.$result['budget'].' — sorteer op dichtstbijzijnde prijs.';
        }

        return implode("\n", $lines);
    }
}
