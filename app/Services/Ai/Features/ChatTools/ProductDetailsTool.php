<?php

namespace App\Services\Ai\Features\ChatTools;

use App\Models\Product;

/**
 * Volledige details van één eerder genoemd product
 * (voor specificatie-vragen als "wat zijn de specs hiervan?").
 */
class ProductDetailsTool implements ChatTool
{
    public function definition(): array
    {
        return [
            'type' => 'function',
            'function' => [
                'name' => 'get_product_details',
                'description' => 'Volledige details (prijs, voorraad, specificaties, link) van één product dat al in dit gesprek is genoemd. Gebruik dit bij specificatie-vragen over "dit/deze" product ("wat zijn de specs hiervan?"). Werkwijze: haal eerst via search_products het product op naam op als je het id niet hebt — beweer NOOIT dat het "niet meer beschikbaar" is zonder te zoeken.',
                'parameters' => [
                    'type' => 'object',
                    'properties' => [
                        'product_id' => [
                            'type' => ['integer', 'null'],
                            'description' => 'De numerieke id uit een eerdere [product:{id}] vermelding in dit gesprek.',
                        ],
                        'slug' => [
                            'type' => ['string', 'null'],
                            'description' => 'De product-slug (laatste deel van een /webshop/.../... link uit dit gesprek), als je het id niet hebt.',
                        ],
                        'url' => [
                            'type' => ['string', 'null'],
                            'description' => 'De volledige product-URL uit dit gesprek — de slug wordt er automatisch uitgehaald.',
                        ],
                    ],
                    'required' => [],
                    'additionalProperties' => false,
                ],
            ],
        ];
    }

    public function execute(array $args): string
    {
        $id = isset($args['product_id']) && is_numeric($args['product_id']) ? (int) $args['product_id'] : 0;
        $slug = isset($args['slug']) && is_string($args['slug']) ? trim($args['slug'], " \t\n\r\0\x0B/") : '';
        if ($slug === '' && isset($args['url']) && is_string($args['url'])) {
            $slug = $this->slugFromUrl($args['url']);
        }

        $product = null;
        if ($id > 0) {
            $product = Product::with('category')->find($id);
        }
        if (! $product && $slug !== '') {
            $product = Product::with('category')->where('slug', $slug)->first();
        }
        if (! $product) {
            return 'Product niet gevonden met deze verwijzing. Zoek het eerst opnieuw via search_products op naam en probeer dan opnieuw — beweer nooit zomaar dat het weg is.';
        }
        if (! $product->status) {
            return 'Product "'.$product->title.'" bestaat maar is niet meer actief. Zeg dit eerlijk en stel een alternatief voor via search_products of bied een medewerker aan.';
        }

        $lines = [
            'Details van "'.($product->brand ? $product->brand.' ' : '').$product->title.'" (gebruik DIT bij specificatie-vragen, met link '
                .route('webshop.product', [$product->category?->slug ?? 'laptops', $product->slug]).'):',
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
     * Haalt de product-slug uit een canonische /webshop/{cat}/{slug} URL.
     */
    protected function slugFromUrl(string $url): string
    {
        $path = parse_url(trim($url), PHP_URL_PATH) ?: trim($url);
        $segments = array_values(array_filter(explode('/', (string) $path)));

        return (string) end($segments);
    }
}
