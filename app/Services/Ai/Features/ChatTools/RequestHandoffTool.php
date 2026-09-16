<?php

namespace App\Services\Ai\Features\ChatTools;

/**
 * Gestructureerde handoff: als de agent oordeelt dat een medewerker
 * nodig is, roept hij deze tool aan. De backend zet daarop
 * handoff_offer=true — de knop hangt NOOIT af van een tekst-marker.
 */
class RequestHandoffTool implements ChatTool
{
    public function definition(): array
    {
        return [
            'type' => 'function',
            'function' => [
                'name' => 'request_handoff',
                'description' => 'Bied een medewerker aan (toont de knop "Medewerker spreken" onder je antwoord). Roep dit ALLEEN aan als: (1) de klant expliciet om een medewerker vraagt, (2) je het antwoord ECHT niet weet (tools gaven niets bruikbaars), of (3) het probleem menselijke diagnose nodig heeft en je verdere vragen geen zin hebben. NOOIT bij begroetingen, bedankjes, of vragen die je met tools kon beantwoorden (openingstijden, contact, FAQ-antwoorden, gevonden producten, bekende pagina-links). Na het aanroepen schrijf je EEN afsluitende zin in de taal van de klant waarin je aanbiedt door te verbinden.',
                'parameters' => [
                    'type' => 'object',
                    'properties' => [
                        'reason' => [
                            'type' => 'string',
                            'description' => 'Korte interne reden (bv. "unknown_policy", "human_diagnosis", "explicit_request"). Alleen voor logging.',
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
        return 'Handoff genoteerd — de knop "Medewerker spreken" verschijnt onder je antwoord. '
            .'Schrijf nu je eindantwoord: eerst het inhoudelijke deel (als je dat hebt), dan EEN afsluitende zin '
            .'in de taal van de klant waarin je aanbiedt om door te verbinden met een medewerker. '
            .'Geen [handoff_offer]-marker nodig — die regelt het systeem.';
    }
}
