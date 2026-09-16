<?php

namespace App\Services\Ai\Features\ChatTools;

use App\Services\Chat\ChatCmsContext;

/**
 * Echte website-gegevens: diensten, contact, pagina-links, webshop-categorieën.
 * De enige bron van waarheid voor links en telefoonnummers.
 */
class SiteInfoTool implements ChatTool
{
    public function __construct(protected ?ChatCmsContext $cms = null)
    {
        $this->cms ??= new ChatCmsContext;
    }

    public function definition(): array
    {
        return [
            'type' => 'function',
            'function' => [
                'name' => 'get_site_info',
                'description' => 'Echte Slimme-PC website-gegevens: diensten met links, contact (telefoon/e-mail/adres), pagina-links (reparatie aanmelden, afspraak aan huis, tarieven) en webshop-categorieën. Roep dit aan als je een link, telefoonnummer, adres of dienstenoverzicht nodig hebt. Kopieer links letterlijk uit het resultaat.',
                'parameters' => [
                    'type' => 'object',
                    'properties' => new \stdClass,
                    'additionalProperties' => false,
                ],
            ],
        ];
    }

    public function execute(array $args): string
    {
        try {
            $ctx = $this->cms->build();
        } catch (\Throwable $e) {
            report($e);

            return 'Website-gegevens tijdelijk niet beschikbaar. Bel 055 203 21 45 of mail info@slimme-pc.nl.';
        }

        return $ctx !== '' ? $ctx : 'Geen website-gegevens beschikbaar.';
    }
}
