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

        'tarieven' => [
            'label' => 'Tarieven',
            'sections' => [
                'hero' => [
                    'label' => 'Hero (Tarieven zonder verrassingen)',
                    'blocks' => [
                        'badge' => ['label' => 'Badge (boven de titel)', 'type' => 'text'],
                        'title_line1' => ['label' => 'Titel regel 1', 'type' => 'text'],
                        'title_line2' => ['label' => 'Titel regel 2 (blauw)', 'type' => 'text'],
                        'description' => ['label' => 'Beschrijving', 'type' => 'textarea'],
                        'button1_text' => ['label' => 'Knop 1 tekst', 'type' => 'text'],
                        'button1_url' => ['label' => 'Knop 1 link (bijv. #tarieven)', 'type' => 'text'],
                        'button2_text' => ['label' => 'Knop 2 tekst', 'type' => 'text'],
                        'button2_url' => ['label' => 'Knop 2 link (bijv. /reparatie-aanmelden)', 'type' => 'text'],
                        'hero_image' => ['label' => 'Afbeelding (hero)', 'type' => 'image'],
                        'hero_image_alt' => ['label' => 'Alt tekst afbeelding', 'type' => 'text'],
                        'trust_points' => [
                            'label' => 'Vertrouwenspunten',
                            'type' => 'json',
                            'columns' => 2,
                            'fields' => [
                                ['key' => 'icon', 'label' => 'Pictogram', 'type' => 'icon'],
                                ['key' => 'label', 'label' => 'Label', 'type' => 'text'],
                            ],
                        ],
                    ],
                ],

                'pricing' => [
                    'label' => 'Tarieven & Prijzen (apparaten)',
                    'blocks' => [
                        'heading' => ['label' => 'Titel', 'type' => 'text'],
                        'description' => ['label' => 'Beschrijving', 'type' => 'textarea'],
                        'categories' => [
                            'label' => 'Categorieën (services met prijzen)',
                            'type' => 'json',
                            'columns' => 2,
                            'fields' => [
                                ['key' => 'icon', 'label' => 'Pictogram (tab)', 'type' => 'icon'],
                                ['key' => 'label', 'label' => 'Tab-label (bijv. Laptop & PC)', 'type' => 'text'],
                                ['key' => 'title', 'label' => 'Titel (paneel)', 'type' => 'text'],
                                ['key' => 'description', 'label' => 'Beschrijving', 'type' => 'textarea'],
                                ['key' => 'image', 'label' => 'Afbeelding (paneel)', 'type' => 'image'],
                                ['key' => 'notice', 'label' => 'Let op-tekst', 'type' => 'textarea'],
                                ['key' => 'prices', 'label' => 'Prijzen (onderstaande regels)',
                                 'type' => 'nested',
                                 'fields' => [
                                     ['key' => 'icon', 'label' => 'Pictogram', 'type' => 'icon'],
                                     ['key' => 'title', 'label' => 'Titel', 'type' => 'text'],
                                     ['key' => 'prefix', 'label' => 'Voorvoegsel (bijv. vanaf)', 'type' => 'text'],
                                     ['key' => 'price', 'label' => 'Prijs (bijv. €35)', 'type' => 'text'],
                                 ],
                                ],
                            ],
                        ],
                    ],
                ],

                'extra' => [
                    'label' => 'Algemene tarieven, Zakelijke IT-service & Trust',
                    'blocks' => [
                        'accordions' => [
                            'label' => 'Accordions (Algemene + Zakelijke tarieven)',
                            'type' => 'json',
                            'columns' => 2,
                            'fields' => [
                                ['key' => 'icon', 'label' => 'Pictogram', 'type' => 'icon'],
                                ['key' => 'title', 'label' => 'Titel', 'type' => 'text'],
                                ['key' => 'accent', 'label' => 'Accentkleur (blue of green)', 'type' => 'text'],
                                ['key' => 'description', 'label' => 'Ondertitel', 'type' => 'text'],
                                ['key' => 'prices', 'label' => 'Prijzen',
                                 'type' => 'nested',
                                 'fields' => [
                                     ['key' => 'title', 'label' => 'Titel', 'type' => 'text'],
                                     ['key' => 'description', 'label' => 'Omschrijving', 'type' => 'text'],
                                     ['key' => 'price', 'label' => 'Prijs (bijv. €35)', 'type' => 'text'],
                                 ],
                                ],
                            ],
                        ],
                        'trust_cards' => [
                            'label' => 'Trustkaarten',
                            'type' => 'json',
                            'columns' => 2,
                            'fields' => [
                                ['key' => 'icon', 'label' => 'Pictogram', 'type' => 'icon'],
                                ['key' => 'title', 'label' => 'Titel', 'type' => 'text'],
                                ['key' => 'description', 'label' => 'Beschrijving', 'type' => 'textarea'],
                            ],
                        ],
                    ],
                ],
            ],
        ],

        'contact' => [
            'label' => 'Contact',
            'sections' => [
                'hero' => [
                    'label' => 'Hero (Contact met Slimme-PC)',
                    'blocks' => [
                        'badge' => ['label' => 'Badge (boven de titel)', 'type' => 'text'],
                        'title_line1' => ['label' => 'Titel regel 1', 'type' => 'text'],
                        'title_line2' => ['label' => 'Titel regel 2 (blauw)', 'type' => 'text'],
                        'description' => ['label' => 'Beschrijving', 'type' => 'textarea'],
                        'button1_text' => ['label' => 'Knop 1 tekst (bijv. Bericht versturen)', 'type' => 'text'],
                        'button2_text' => ['label' => 'Knop 2 tekst (bijv. WhatsApp ons)', 'type' => 'text'],
                        'whatsapp_number' => ['label' => 'WhatsApp nummer (zonder +, bijv. 31617100945)', 'type' => 'text'],
                        'hero_image' => ['label' => 'Afbeelding (hero)', 'type' => 'image'],
                        'hero_image_alt' => ['label' => 'Alt tekst afbeelding', 'type' => 'text'],
                        'trust_points' => [
                            'label' => 'Vertrouwenspunten',
                            'type' => 'json',
                            'columns' => 1,
                            'fields' => [
                                ['key' => 'icon', 'label' => 'Pictogram', 'type' => 'icon'],
                                ['key' => 'label', 'label' => 'Label', 'type' => 'text'],
                            ],
                        ],
                    ],
                ],

                'gegevens' => [
                    'label' => 'Contactgegevens, Service & support & Openingstijden',
                    'blocks' => [
                        'card1_title' => ['label' => 'Kaart 1 titel (Contactgegevens)', 'type' => 'text'],
                        'card1_icon' => ['label' => 'Kaart 1 pictogram', 'type' => 'icon'],
                        'company_name' => ['label' => 'Bedrijfsnaam', 'type' => 'text'],
                        'address' => ['label' => 'Adres (elke regel apart)', 'type' => 'textarea'],
                        'kvk' => ['label' => 'KvK-nummer', 'type' => 'text'],
                        'btw' => ['label' => 'BTW-nummer', 'type' => 'text'],
                        'route_label' => ['label' => 'Link tekst (bijv. Route bekijken)', 'type' => 'text'],
                        'route_url' => ['label' => 'Link (bijv. #locatie)', 'type' => 'text'],

                        'card2_title' => ['label' => 'Kaart 2 titel (Service & support)', 'type' => 'text'],
                        'card2_icon' => ['label' => 'Kaart 2 pictogram', 'type' => 'icon'],
                        'contact_methods' => [
                            'label' => 'Contactmethoden (telefoon/e-mail/WhatsApp)',
                            'type' => 'json',
                            'columns' => 1,
                            'fields' => [
                                ['key' => 'icon', 'label' => 'Pictogram', 'type' => 'icon'],
                                ['key' => 'label', 'label' => 'Label (bijv. Telefoon)', 'type' => 'text'],
                                ['key' => 'value', 'label' => 'Waarde (bijv. 055 203 21 45)', 'type' => 'text'],
                                ['key' => 'url', 'label' => 'Link (bijv. tel:+31552032145)', 'type' => 'text'],
                            ],
                        ],

                        'card3_title' => ['label' => 'Kaart 3 titel (Openingstijden)', 'type' => 'text'],
                        'card3_icon' => ['label' => 'Kaart 3 pictogram', 'type' => 'icon'],
                        'opening_hours' => [
                            'label' => 'Openingstijden (dagen)',
                            'type' => 'json',
                            'columns' => 1,
                            'fields' => [
                                ['key' => 'day', 'label' => 'Dag (bijv. Maandag – vrijdag)', 'type' => 'text'],
                                ['key' => 'note', 'label' => 'Opmerking (bijv. Reguliere openingstijden)', 'type' => 'text'],
                                ['key' => 'time', 'label' => 'Tijd (bijv. 09:00 – 17:00)', 'type' => 'text'],
                                ['key' => 'closed', 'label' => 'Gesloten (grijze weergave)', 'type' => 'boolean'],
                            ],
                        ],
                    ],
                ],

                'formulier' => [
                    'label' => 'Contactformulier (introductie + voordelen)',
                    'blocks' => [
                        'badge' => ['label' => 'Badge (boven de titel)', 'type' => 'text'],
                        'title_line1' => ['label' => 'Titel regel 1', 'type' => 'text'],
                        'title_line2' => ['label' => 'Titel regel 2 (blauw)', 'type' => 'text'],
                        'description' => ['label' => 'Beschrijving', 'type' => 'textarea'],
                        'benefits' => [
                            'label' => 'Voordelen',
                            'type' => 'json',
                            'columns' => 1,
                            'fields' => [
                                ['key' => 'label', 'label' => 'Label', 'type' => 'text'],
                            ],
                        ],
                    ],
                ],

                'locatie' => [
                    'label' => 'Locatie (kaart + bereikbaarheid)',
                    'blocks' => [
                        'badge' => ['label' => 'Badge (boven de titel)', 'type' => 'text'],
                        'title_line1' => ['label' => 'Titel regel 1', 'type' => 'text'],
                        'title_line2' => ['label' => 'Titel regel 2 (blauw)', 'type' => 'text'],
                        'description' => ['label' => 'Beschrijving', 'type' => 'textarea'],
                        'map_src' => ['label' => 'Kaart URL (Google Maps embed)', 'type' => 'textarea'],
                        'route_label' => ['label' => 'Knop tekst (bijv. Route plannen)', 'type' => 'text'],
                        'route_url' => ['label' => 'Knop link (Google Maps)', 'type' => 'textarea'],
                        'location_items' => [
                            'label' => 'Locatiepunten',
                            'type' => 'json',
                            'columns' => 1,
                            'fields' => [
                                ['key' => 'icon', 'label' => 'Pictogram', 'type' => 'icon'],
                                ['key' => 'title', 'label' => 'Titel (optioneel)', 'type' => 'text'],
                                ['key' => 'text', 'label' => 'Tekst (elke regel apart)', 'type' => 'textarea'],
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

