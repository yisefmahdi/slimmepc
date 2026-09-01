<?php

namespace App\Services\Ai\Prompts;

class ProductPromptBuilder
{
    /**
     * Build the system prompt.
     */
    public static function buildSystemPrompt(): string
    {
        return <<<PROMPT
Je bent een ervaren e-commerce copywriter en SEO-specialist voor "Slimme-PC" (een toonaangevende computer- en elektronicawinkel in Nederland).
Je taak is om een aantrekkelijke, professionele en conversiegerichte productbeschrijving te schrijven in vlekkeloos Nederlands.

Richtlijnen voor de opmaak en inhoud:
1. Schrijf in heldere, schone HTML-tags (gebruik uitsluitend <p>, <h3>, <ul>, <li>, <strong>). Gebruik GEEN Markdown zoals ** of #, en GEEN <html> of <body> tags.
2. Structuur van de beschrijving:
   - Een pakkende introductiealinea (<p>): Wat is dit product, voor wie is het ideaal (gaming, werk, studie, dagelijks gebruik), en wat is de grootste kracht?
   - <h3>Belangrijkste kenmerken & specificaties</h3> gevolgd door een <ul> met <li>-items met <strong>vetgedrukte labels</strong> (zoals <strong>Processor:</strong>, <strong>Opslag:</strong>, <strong>Scherm:</strong>).
   - Een korte alinea over het gebruiksgemak en betrouwbaarheid.
   - <h3>Waarom kopen bij Slimme-PC?</h3> met <ul> en <li> (bijv. Volledig getest & gecontroleerd, snelle levering, deskundige ondersteuning en garantie).
   - Een enthousiaste afsluitende zin die de klant uitnodigt tot aankoop.
3. Toon: Professioneel, behulpzaam, commercieel maar betrouwbaar en zonder overdreven claims.
PROMPT;
    }

    /**
     * Build the user prompt combining product data and retrieved web search snippets.
     *
     * @param array<string, mixed> $productData
     * @param array<int, array{title: string, snippet: string, url: string}> $searchSnippets
     * @param string|null $additionalInstructions
     * @return string
     */
    public static function buildUserPrompt(
        array $productData,
        array $searchSnippets = [],
        ?string $additionalInstructions = null
    ): string {
        $title = $productData['title'] ?? 'Onbekend product';
        $brand = $productData['brand'] ?? '';
        $sku = $productData['sku'] ?? '';
        $category = $productData['category_name'] ?? ($productData['category'] ?? '');
        $price = isset($productData['price']) && is_numeric($productData['price'])
            ? '€' . number_format((float) $productData['price'], 2)
            : '';

        $features = $productData['features'] ?? [];
        if (is_string($features)) {
            $features = array_filter(array_map('trim', explode("\n", $features)));
        }

        $prompt = "Schrijf een professionele productbeschrijving voor het volgende product:\n\n";
        $prompt .= "- Productnaam: {$title}\n";
        if ($brand) {
            $prompt .= "- Merk: {$brand}\n";
        }
        if ($sku) {
            $prompt .= "- Model/SKU: {$sku}\n";
        }
        if ($category) {
            $prompt .= "- Categorie: {$category}\n";
        }
        if ($price) {
            $prompt .= "- Prijs: {$price}\n";
        }

        if (!empty($features) && is_array($features)) {
            $prompt .= "- Ingevoerde specificaties/eigenschappen:\n";
            foreach ($features as $f) {
                if (trim($f) !== '') {
                    $prompt .= "  * " . trim($f) . "\n";
                }
            }
        }

        if (!empty($searchSnippets)) {
            $prompt .= "\n- Gevonden informatie en technische specificaties van het internet:\n";
            foreach (array_slice($searchSnippets, 0, 5) as $s) {
                $snippet = trim($s['snippet'] ?? '');
                if ($snippet !== '') {
                    $prompt .= "  * {$snippet}\n";
                }
            }
        }

        if (!empty($additionalInstructions)) {
            $prompt .= "\n- Extra instructies van de beheerder:\n{$additionalInstructions}\n";
        }

        $prompt .= "\nGenereer nu de complete HTML-beschrijving volgens de gevraagde structuur.";

        return $prompt;
    }
}
