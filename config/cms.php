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
                        'hub_icon' => ['label' => 'Hub pictogram', 'type' => 'icon'],
                        'hub_title' => ['label' => 'Hub titel (midden)', 'type' => 'text'],
                        'hub_subtitle' => ['label' => 'Hub ondertitel (midden)', 'type' => 'text'],
                        'benefits' => [
                            'label' => 'Voordelen (6 kaarten rond de hub)',
                            'type' => 'json',
                            'fields' => [
                                ['key' => 'icon', 'label' => 'Pictogram', 'type' => 'icon'],
                                ['key' => 'title', 'label' => 'Titel', 'type' => 'text'],
                                ['key' => 'description', 'label' => 'Beschrijving', 'type' => 'textarea'],
                            ],
                        ],
                        'stats' => [
                            'label' => 'Statistieken (onderaan)',
                            'type' => 'json',
                            'columns' => 2,
                            'fields' => [
                                ['key' => 'icon', 'label' => 'Pictogram', 'type' => 'icon'],
                                ['key' => 'value', 'label' => 'Waarde (bijv. 1200+)', 'type' => 'text'],
                                ['key' => 'label', 'label' => 'Label', 'type' => 'text'],
                            ],
                        ],
                    ],
                ],

                'services' => [
                    'label' => 'Services (Onze diensten)',
                    'blocks' => [
                        'badge' => ['label' => 'Badge (boven de titel)', 'type' => 'text'],
                        'title_prefix' => ['label' => 'Titel (voor highlight)', 'type' => 'text'],
                        'title_highlight' => ['label' => 'Titel (gradient deel)', 'type' => 'text'],
                        'title_suffix' => ['label' => 'Titel (na highlight)', 'type' => 'text'],
                        'description' => ['label' => 'Beschrijving', 'type' => 'textarea'],
                        'services' => [
                            'label' => 'Diensten (8 kaarten)',
                            'type' => 'json',
                            'columns' => 2,
                            'fixed' => true,
                            'fields' => [
                                ['key' => 'image', 'label' => 'Afbeelding (kaart op de homepage)', 'type' => 'image'],
                                ['key' => 'title', 'label' => 'Titel', 'type' => 'text'],
['key' => 'icon', 'label' => 'Pictogram', 'type' => 'icon'],
                                ['key' => 'title', 'label' => 'Titel', 'type' => 'text'],
                                ['key' => 'description', 'label' => 'Beschrijving', 'type' => 'textarea'],
                                ['key' => 'link', 'label' => 'Link (bijv. /pc.html)', 'type' => 'text', 'hidden' => true],
                                ['key' => 'hidden', 'label' => 'Verberg van de homepage', 'type' => 'boolean'],
                            ],
                        ],
                    ],
                ],

                'footer' => [
                    'label' => 'Footer (onderkant van de website)',
                    'blocks' => [
                        'brand_about' => ['label' => 'Bedrijfstekst (onder het logo)', 'type' => 'textarea'],
                        'social' => [
                            'label' => 'Social media links',
                            'type' => 'json',
                            'columns' => 2,
                            'fields' => [
                                ['key' => 'icon', 'label' => 'Pictogram', 'type' => 'icon'],
                                ['key' => 'url', 'label' => 'Link (bijv. https://facebook.com/...)', 'type' => 'text'],
                            ],
                        ],
                        'contact' => [
                            'label' => 'Contactgegevens',
                            'type' => 'json',
                            'columns' => 2,
                            'fields' => [
                                ['key' => 'icon', 'label' => 'Pictogram', 'type' => 'icon'],
                                ['key' => 'label', 'label' => 'Label', 'type' => 'text'],
                                ['key' => 'value', 'label' => 'Waarde (regel per regel)', 'type' => 'textarea'],
                            ],
                        ],
                        'trust' => [
                            'label' => 'Trustbadges (onder het contact)',
                            'type' => 'json',
                            'columns' => 2,
                            'fields' => [
                                ['key' => 'icon', 'label' => 'Pictogram', 'type' => 'icon'],
                                ['key' => 'title', 'label' => 'Titel', 'type' => 'text'],
                                ['key' => 'subtitle', 'label' => 'Ondertitel', 'type' => 'text'],
                            ],
                        ],
                        'copyright' => ['label' => 'Copyright tekst (onderste balk)', 'type' => 'text'],
                        'payments' => [
                            'label' => 'Betaalmethoden (onderste balk)',
                            'type' => 'json',
                            'columns' => 2,
                            'fields' => [
                                ['key' => 'label', 'label' => 'Label (bijv. iDEAL)', 'type' => 'text'],
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],

    /*
    | Design / site settings — stored as JSON in content_meta (meta_key 'design')
    | Brand colors and the font are FIXED in code (resources/views/landing/layouts/app.blade.php
    | + resources/css/landing.css) and are intentionally NOT editable here.
    */
    'design' => [
        'site' => [
            'label' => 'Site',
            'fields' => [
                ['key' => 'meta_title', 'label' => 'Pagina titel (SEO)', 'type' => 'text'],
                ['key' => 'meta_description', 'label' => 'Meta beschrijving (SEO)', 'type' => 'textarea'],
            ],
        ],
    ],
];

