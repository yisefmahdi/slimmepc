<?php

/*
|--------------------------------------------------------------------------
| CMS Schema
|--------------------------------------------------------------------------
| Drives the admin editor forms (which sections/blocks are editable).
|
| pages.<page>.sections.<section>.blocks.<key>:
|   label   -> human readable label (admin)
|   type    -> text | textarea | image | json
|   hint    -> optional helper text
|   fields  -> for type=json: list of item fields [key, label, type]
|
| design: settings stored as JSON in content_meta (meta_key = 'design').
*/

return [

    'cache_version_key' => 'cache_version',

    'pages' => [
        'home' => [
            'label' => 'Home',
            'sections' => [
                'header' => [
                    'label' => 'Header (logo & titel)',
                    'blocks' => [
                        'logo_image' => ['label' => 'Logo afbeelding', 'type' => 'image'],
                        'logo_text' => ['label' => 'Logo tekst (bijv. SLIMME-PC)', 'type' => 'text'],
                        'tagline' => ['label' => 'Tagline (bijv. Reparatie · Verkoop · IT-service)', 'type' => 'text'],
                    ],
                ],

                'hero' => [
                    'label' => 'Hero (IT-service & computerreparatie)',
                    'blocks' => [
                        'badge' => ['label' => 'Badge (boven de titel)', 'type' => 'text'],
                        'title_line1' => ['label' => 'Titel regel 1', 'type' => 'text'],
                        'title_line2' => ['label' => 'Titel regel 2', 'type' => 'text'],
                        'title_gradient' => ['label' => 'Titel regel 3 (gradient)', 'type' => 'text'],
                        'description' => ['label' => 'Beschrijving', 'type' => 'textarea'],
                        'hero_image' => ['label' => 'Afbeelding (desktop)', 'type' => 'image'],
                        'hero_image_mobile' => ['label' => 'Afbeelding (mobiel)', 'type' => 'image'],
                    ],
                ],

                'why' => [
                    'label' => 'Waarom voor ons kiezen',
                    'blocks' => [
                        'badge' => ['label' => 'Badge (boven de titel)', 'type' => 'text'],
                        'title_prefix' => ['label' => 'Titel (voor highlight)', 'type' => 'text'],
                        'title_highlight' => ['label' => 'Titel (gradient deel)', 'type' => 'text'],
                        'description' => ['label' => 'Beschrijving', 'type' => 'textarea'],
                        'hub_icon' => ['label' => 'Hub pictogram (lucide naam, bijv. laptop-minimal-check)', 'type' => 'text'],
                        'hub_title' => ['label' => 'Hub titel (midden)', 'type' => 'text'],
                        'hub_subtitle' => ['label' => 'Hub ondertitel (midden)', 'type' => 'text'],
                        'benefits' => [
                            'label' => 'Voordelen (6 kaarten rond de hub)',
                            'type' => 'json',
                            'fields' => [
                                ['key' => 'icon', 'label' => 'Pictogram (lucide naam)', 'type' => 'text'],
                                ['key' => 'title', 'label' => 'Titel', 'type' => 'text'],
                                ['key' => 'description', 'label' => 'Beschrijving', 'type' => 'textarea'],
                            ],
                        ],
                        'stats' => [
                            'label' => 'Statistieken (onderaan)',
                            'type' => 'json',
                            'columns' => 2,
                            'fields' => [
                                ['key' => 'icon', 'label' => 'Pictogram (lucide naam)', 'type' => 'text'],
                                ['key' => 'value', 'label' => 'Waarde (bijv. 1200+)', 'type' => 'text'],
                                ['key' => 'label', 'label' => 'Label', 'type' => 'text'],
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],

    /*
    | Design / site settings — stored as JSON in content_meta (meta_key 'design')
    */
    'design' => [
        'site' => [
            'label' => 'Site',
            'fields' => [
                ['key' => 'meta_title', 'label' => 'Pagina titel (SEO)', 'type' => 'text'],
                ['key' => 'meta_description', 'label' => 'Meta beschrijving (SEO)', 'type' => 'textarea'],
            ],
        ],
        'colors' => [
            'label' => 'Kleuren',
            'fields' => [
                ['key' => 'brand_primary', 'label' => 'Primaire kleur', 'type' => 'color'],
                ['key' => 'brand_primary_dark', 'label' => 'Primaire kleur (donker)', 'type' => 'color'],
                ['key' => 'brand_accent', 'label' => 'Accentkleur (groen/lime)', 'type' => 'color'],
                ['key' => 'brand_heading', 'label' => 'Koptekst kleur', 'type' => 'color'],
                ['key' => 'gradient_from', 'label' => 'Gradient start', 'type' => 'color'],
                ['key' => 'gradient_to', 'label' => 'Gradient einde', 'type' => 'color'],
            ],
        ],
        'typography' => [
            'label' => 'Lettertype',
            'fields' => [
                [
                    'key' => 'font_family',
                    'label' => 'Lettertype',
                    'type' => 'select',
                    'options' => [
                        'Figtree' => 'Figtree',
                        'Inter' => 'Inter',
                        'Poppins' => 'Poppins',
                        'Roboto' => 'Roboto',
                        'Merriweather' => 'Merriweather',
                    ],
                ],
            ],
        ],
    ],
];
