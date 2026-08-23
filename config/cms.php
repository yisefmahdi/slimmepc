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

$serviceSectionDef = [
    'hero' => [
        'label' => 'Hero',
        'blocks' => [
            'badge' => ['label' => 'Badge (boven titel)', 'type' => 'text'],
            'title1' => ['label' => 'Titel regel 1', 'type' => 'text'],
            'title2' => ['label' => 'Titel regel 2', 'type' => 'text'],
            'title3' => ['label' => 'Titel regel 3 (gradient)', 'type' => 'text'],
            'description' => ['label' => 'Beschrijving', 'type' => 'textarea'],
            'image' => ['label' => 'Achtergrondafbeelding (hero)', 'type' => 'image'],
            'usp' => [
                'label' => 'USP-rij (4 items in hero)',
                'type' => 'json',
                'columns' => 2,
                'fields' => [
                    ['key' => 'icon', 'label' => 'Pictogram', 'type' => 'icon'],
                    ['key' => 'title', 'label' => 'Titel', 'type' => 'text'],
                    ['key' => 'subtitle', 'label' => 'Ondertitel', 'type' => 'text'],
                ],
            ],
        ],
    ],
    'problems' => [
                'label' => 'Wat is er mis?',
        'blocks' => [
            'title' => ['label' => 'Titel (voor highlight)', 'type' => 'text'],
            'title_highlight' => ['label' => 'Titel (gekleurde highlight)', 'type' => 'text'],
            'subtitle' => ['label' => 'Ondertitel', 'type' => 'text'],
            'items' => [
                'label' => 'Problemen (kaarten)',
                'type' => 'json',
                'columns' => 2,
                'fields' => [
                    ['key' => 'icon', 'label' => 'Pictogram', 'type' => 'icon'],
                    ['key' => 'title', 'label' => 'Titel', 'type' => 'text'],
                ],
            ],
        ],
    ],
    'speciality' => [
                'label' => 'Onze specialiteit',
        'blocks' => [
            'badge' => ['label' => 'Badge', 'type' => 'text'],
            'title1' => ['label' => 'Titel regel 1', 'type' => 'text'],
            'title2' => ['label' => 'Titel regel 2 (gradient)', 'type' => 'text'],
            'description' => ['label' => 'Beschrijving', 'type' => 'textarea'],
            'list' => [
                'label' => 'Specialiteiten (lijst)',
                'type' => 'json',
                'columns' => 1,
                'fields' => [
                    ['key' => 'icon', 'label' => 'Pictogram', 'type' => 'icon'],
                    ['key' => 'title', 'label' => 'Titel', 'type' => 'text'],
                ],
            ],
            'video' => ['label' => 'Video (mp4)', 'type' => 'video'],
            'video_poster' => ['label' => 'Video poster (afbeelding)', 'type' => 'image'],
        ],
    ],
    'equipment' => [
        'label' => 'Professionele uitrusting',
        'blocks' => [
            'items' => [
                'label' => 'Uitrusting (4 items)',
                'type' => 'json',
                'columns' => 2,
                'fields' => [
                    ['key' => 'icon', 'label' => 'Pictogram', 'type' => 'icon'],
                    ['key' => 'title', 'label' => 'Titel', 'type' => 'text'],
                    ['key' => 'subtitle', 'label' => 'Ondertitel', 'type' => 'text'],
                ],
            ],
        ],
    ],
    'example' => [
        'label' => 'Een reparatie van dichtbij',
        'blocks' => [
            'title' => ['label' => 'Titel', 'type' => 'text'],
            'subtitle' => ['label' => 'Ondertitel', 'type' => 'text'],
            'before_image' => ['label' => 'Voor afbeelding', 'type' => 'image'],
            'before_label' => ['label' => 'Voor label', 'type' => 'text'],
            'before_text' => ['label' => 'Voor tekst', 'type' => 'textarea'],
            'diagnose_image' => ['label' => 'Diagnose afbeelding', 'type' => 'image'],
            'diagnose_label' => ['label' => 'Diagnose label', 'type' => 'text'],
            'diagnose_text' => ['label' => 'Diagnose tekst', 'type' => 'textarea'],
            'after_image' => ['label' => 'Na afbeelding', 'type' => 'image'],
            'after_label' => ['label' => 'Na label', 'type' => 'text'],
            'after_text' => ['label' => 'Na tekst', 'type' => 'textarea'],
            'tested_title' => ['label' => 'Getest titel', 'type' => 'text'],
            'tested_text' => ['label' => 'Getest tekst', 'type' => 'textarea'],
        ],
    ],
    'other' => [
        'label' => 'Andere reparaties',
        'blocks' => [
            'title' => ['label' => 'Titel (voor highlight)', 'type' => 'text'],
            'title_highlight' => ['label' => 'Titel (gekleurde highlight)', 'type' => 'text'],
            'items' => [
                'label' => 'Andere reparaties (kaarten)',
                'type' => 'json',
                'columns' => 2,
                'fields' => [
                    ['key' => 'image', 'label' => 'Afbeelding', 'type' => 'image'],
                    ['key' => 'title', 'label' => 'Titel', 'type' => 'text'],
                    ['key' => 'subtitle', 'label' => 'Ondertitel', 'type' => 'text'],
                ],
            ],
        ],
    ],
    'faq' => [
        'label' => 'FAQ + CTA',
        'blocks' => [
            'title' => ['label' => 'FAQ titel', 'type' => 'text'],
            'items' => [
                'label' => 'Veelgestelde vragen',
                'type' => 'json',
                'columns' => 1,
                'fields' => [
                    ['key' => 'question', 'label' => 'Vraag', 'type' => 'text'],
                    ['key' => 'answer', 'label' => 'Antwoord', 'type' => 'textarea'],
                ],
            ],
            'more_url' => ['label' => 'Meer FAQ link', 'type' => 'text', 'hidden' => true],
            'cta_title' => ['label' => 'CTA titel regel 1', 'type' => 'text'],
            'cta_title2' => ['label' => 'CTA titel regel 2 (gradient)', 'type' => 'text'],
            'cta_subtitle' => ['label' => 'CTA ondertitel', 'type' => 'textarea'],
            'cta_phone' => ['label' => 'CTA telefoon', 'type' => 'text'],
            'cta_bg' => ['label' => 'CTA afbeelding (karakter)', 'type' => 'image'],
        ],
    ],
    'bottom' => [
        'label' => 'Onderaan USP-balk',
        'blocks' => [
            'items' => [
                'label' => 'USP items (onderaan)',
                'type' => 'json',
                'columns' => 2,
                'fields' => [
                    ['key' => 'icon', 'label' => 'Pictogram', 'type' => 'icon'],
                    ['key' => 'title', 'label' => 'Titel', 'type' => 'text'],
                    ['key' => 'subtitle', 'label' => 'Ondertitel', 'type' => 'text'],
                ],
            ],
        ],
    ],
];

$consoleSectionDef = [
    'hero' => [
        'label' => 'Hero',
        'blocks' => [
            'badge' => ['label' => 'Badge (boven titel)', 'type' => 'text'],
            'title1' => ['label' => 'Titel regel 1', 'type' => 'text'],
            'title2' => ['label' => 'Titel regel 2', 'type' => 'text'],
            'title3' => ['label' => 'Titel regel 3 (gradient)', 'type' => 'text'],
            'description' => ['label' => 'Beschrijving', 'type' => 'textarea'],
            'problem_list' => [
                'label' => 'Probleem-lijst (hero)',
                'type' => 'json',
                'columns' => 1,
                'fields' => [
                    ['key' => 'title', 'label' => 'Probleem', 'type' => 'text'],
                ],
            ],
            'image' => ['label' => 'Console afbeelding (hero rechts)', 'type' => 'image'],
        ],
    ],
    'consoles' => [
        'label' => 'Welke console heb je?',
        'blocks' => [
            'title' => ['label' => 'Titel', 'type' => 'text'],
            'subtitle' => ['label' => 'Ondertitel', 'type' => 'text'],
            'items' => [
                'label' => 'Consoles (3 kaarten)',
                'type' => 'json',
                'columns' => 3,
                'fields' => [
                    ['key' => 'name', 'label' => 'Naam', 'type' => 'text'],
                    ['key' => 'image', 'label' => 'Afbeelding', 'type' => 'image'],
                ],
            ],
        ],
    ],
    'problems' => [
        'label' => 'Wat is er mis?',
        'blocks' => [
            'title' => ['label' => 'Titel', 'type' => 'text'],
            'subtitle' => ['label' => 'Ondertitel', 'type' => 'text'],
            'items' => [
                'label' => 'Problemen (kaarten)',
                'type' => 'json',
                'columns' => 2,
                'fields' => [
                    ['key' => 'emoji', 'label' => 'Emoji', 'type' => 'text'],
                    ['key' => 'title', 'label' => 'Titel', 'type' => 'text'],
                    ['key' => 'subtitle', 'label' => 'Ondertitel', 'type' => 'text'],
                ],
            ],
        ],
    ],
    'werkwijze' => [
        'label' => 'Onze werkwijze',
        'blocks' => [
            'title' => ['label' => 'Titel', 'type' => 'text'],
            'steps' => [
                'label' => 'Stappen',
                'type' => 'json',
                'columns' => 1,
                'fields' => [
                    ['key' => 'number', 'label' => 'Nummer', 'type' => 'text'],
                    ['key' => 'title', 'label' => 'Titel', 'type' => 'text'],
                    ['key' => 'description', 'label' => 'Beschrijving', 'type' => 'textarea'],
                ],
            ],
        ],
    ],
    'motorkap' => [
        'label' => 'Onder de motorkap',
        'blocks' => [
            'title' => ['label' => 'Titel', 'type' => 'text'],
            'description' => ['label' => 'Beschrijving', 'type' => 'textarea'],
            'checklist' => [
                'label' => 'Checklist',
                'type' => 'json',
                'columns' => 1,
                'fields' => [
                    ['key' => 'title', 'label' => 'Tekst', 'type' => 'text'],
                ],
            ],
            'image' => ['label' => 'Afbeelding (binnenkant)', 'type' => 'image'],
            'spots' => [
                'label' => 'Onderdelen',
                'type' => 'json',
                'columns' => 1,
                'fields' => [
                    ['key' => 'title', 'label' => 'Titel', 'type' => 'text'],
                    ['key' => 'description', 'label' => 'Beschrijving', 'type' => 'text'],
                ],
            ],
        ],
    ],
    'voorana' => [
        'label' => 'Voor & na',
        'blocks' => [
            'title' => ['label' => 'Titel', 'type' => 'text'],
            'before_image' => ['label' => 'Voor afbeelding', 'type' => 'image'],
            'before_label' => ['label' => 'Voor label', 'type' => 'text'],
            'after_image' => ['label' => 'Na afbeelding', 'type' => 'image'],
            'after_label' => ['label' => 'Na label', 'type' => 'text'],
            'checklist' => [
                'label' => 'Checklist',
                'type' => 'json',
                'columns' => 1,
                'fields' => [
                    ['key' => 'title', 'label' => 'Tekst', 'type' => 'text'],
                ],
            ],
            'hdmi_title' => ['label' => 'HDMI titel', 'type' => 'text'],
            'hdmi_steps' => [
                'label' => 'HDMI stappen',
                'type' => 'json',
                'columns' => 1,
                'fields' => [
                    ['key' => 'number', 'label' => 'Nummer', 'type' => 'text'],
                    ['key' => 'title', 'label' => 'Titel', 'type' => 'text'],
                ],
            ],
        ],
    ],
    'services' => [
        'label' => 'Onze console services',
        'blocks' => [
            'title' => ['label' => 'Titel', 'type' => 'text'],
            'items' => [
                'label' => 'Services (kaarten)',
                'type' => 'json',
                'columns' => 2,
                'fields' => [
                    ['key' => 'emoji', 'label' => 'Emoji', 'type' => 'text'],
                    ['key' => 'title', 'label' => 'Titel', 'type' => 'text'],
                    ['key' => 'description', 'label' => 'Beschrijving', 'type' => 'textarea'],
                    ['key' => 'price', 'label' => 'Prijs', 'type' => 'text'],
                ],
            ],
        ],
    ],
    'garantie' => [
        'label' => 'Garantie',
        'blocks' => [
            'title' => ['label' => 'Titel', 'type' => 'text'],
            'description' => ['label' => 'Beschrijving', 'type' => 'textarea'],
            'points' => [
                'label' => 'Punten',
                'type' => 'json',
                'columns' => 1,
                'fields' => [
                    ['key' => 'title', 'label' => 'Tekst', 'type' => 'text'],
                ],
            ],
        ],
    ],
];

$macSectionDef = [
    'hero' => [
        'label' => 'Hero',
        'blocks' => [
            'badge' => ['label' => 'Badge (boven titel)', 'type' => 'text'],
            'title1' => ['label' => 'Titel regel 1', 'type' => 'text'],
            'title2' => ['label' => 'Titel regel 2 (normaal)', 'type' => 'text'],
            'title3' => ['label' => 'Titel regel 2 (blauw)', 'type' => 'text'],
            'description' => ['label' => 'Beschrijving', 'type' => 'textarea'],
            'trust' => [
                'label' => 'Vertrouwenspunten (hero)',
                'type' => 'json',
                'columns' => 2,
                'fields' => [
                    ['key' => 'title', 'label' => 'Tekst', 'type' => 'text'],
                ],
            ],
            'image' => ['label' => 'Mac afbeelding (hero rechts)', 'type' => 'image'],
        ],
    ],
    'devices' => [
        'label' => 'Welke Mac heb je?',
        'blocks' => [
            'title' => ['label' => 'Titel', 'type' => 'text'],
            'subtitle' => ['label' => 'Ondertitel', 'type' => 'text'],
            'items' => [
                'label' => 'Mac modellen (3 kaarten)',
                'type' => 'json',
                'columns' => 3,
                'fields' => [
                    ['key' => 'name', 'label' => 'Naam', 'type' => 'text'],
                    ['key' => 'image', 'label' => 'Afbeelding', 'type' => 'image'],
                    ['key' => 'sub1', 'label' => 'Regel 1', 'type' => 'text'],
                    ['key' => 'sub2', 'label' => 'Regel 2', 'type' => 'text'],
                ],
            ],
        ],
    ],
    'problems' => [
        'label' => 'Problemen + component reparatie',
        'blocks' => [
            'title' => ['label' => 'Titel', 'type' => 'text'],
            'subtitle' => ['label' => 'Ondertitel', 'type' => 'text'],
            'items' => [
                'label' => 'Problemen (8 kaarten)',
                'type' => 'json',
                'columns' => 4,
                'fields' => [
                    ['key' => 'emoji', 'label' => 'Icoon (emoji)', 'type' => 'text'],
                    ['key' => 'title', 'label' => 'Titel', 'type' => 'text'],
                    ['key' => 'subtitle', 'label' => 'Ondertitel', 'type' => 'text'],
                ],
            ],
            'component_title' => ['label' => 'Component titel', 'type' => 'text'],
            'component_text' => ['label' => 'Component tekst', 'type' => 'textarea'],
            'component_items' => [
                'label' => 'Component punten',
                'type' => 'json',
                'columns' => 1,
                'fields' => [
                    ['key' => 'title', 'label' => 'Tekst', 'type' => 'text'],
                ],
            ],
        ],
    ],
    'process' => [
        'label' => 'Werkwijze',
        'blocks' => [
            'title' => ['label' => 'Titel', 'type' => 'text'],
            'subtitle' => ['label' => 'Ondertitel', 'type' => 'text'],
            'items' => [
                'label' => 'Stappen (4)',
                'type' => 'json',
                'columns' => 4,
                'fields' => [
                    ['key' => 'emoji', 'label' => 'Icoon (emoji)', 'type' => 'text'],
                    ['key' => 'title', 'label' => 'Titel', 'type' => 'text'],
                    ['key' => 'description', 'label' => 'Beschrijving', 'type' => 'textarea'],
                ],
            ],
        ],
    ],
    'water' => [
        'label' => 'Vloeistofschade',
        'blocks' => [
            'title' => ['label' => 'Titel', 'type' => 'text'],
            'text' => ['label' => 'Tekst', 'type' => 'textarea'],
            'image' => ['label' => 'Afbeelding', 'type' => 'image'],
        ],
    ],
    'battery' => [
        'label' => 'Batterij problemen',
        'blocks' => [
            'title' => ['label' => 'Titel', 'type' => 'text'],
            'items' => [
                'label' => 'Punten',
                'type' => 'json',
                'columns' => 1,
                'fields' => [
                    ['key' => 'title', 'label' => 'Tekst', 'type' => 'text'],
                ],
            ],
            'image' => ['label' => 'Afbeelding', 'type' => 'image'],
        ],
    ],
    'imac' => [
        'label' => 'iMac',
        'blocks' => [
            'title' => ['label' => 'Titel', 'type' => 'text'],
            'text' => ['label' => 'Tekst', 'type' => 'textarea'],
            'image' => ['label' => 'Afbeelding', 'type' => 'image'],
            'items' => [
                'label' => 'Punten',
                'type' => 'json',
                'columns' => 2,
                'fields' => [
                    ['key' => 'title', 'label' => 'Tekst', 'type' => 'text'],
                ],
            ],
        ],
    ],
    'why' => [
        'label' => 'Waarom Slimme-PC',
        'blocks' => [
            'badge' => ['label' => 'Badge (boven titel)', 'type' => 'text'],
            'title' => ['label' => 'Titel', 'type' => 'text'],
            'text' => ['label' => 'Beschrijving', 'type' => 'textarea'],
            'items' => [
                'label' => 'Punten (6)',
                'type' => 'json',
                'columns' => 2,
                'fields' => [
                    ['key' => 'title', 'label' => 'Tekst', 'type' => 'text'],
                ],
            ],
        ],
    ],
    'recent' => [
        'label' => 'Recente Mac reparaties',
        'blocks' => [
            'title' => ['label' => 'Titel', 'type' => 'text'],
            'items' => [
                'label' => 'Reparaties (3 kaarten)',
                'type' => 'json',
                'columns' => 3,
                'fields' => [
                    ['key' => 'image', 'label' => 'Afbeelding', 'type' => 'image'],
                    ['key' => 'title', 'label' => 'Titel', 'type' => 'text'],
                    ['key' => 'text', 'label' => 'Beschrijving', 'type' => 'text'],
                ],
            ],
        ],
    ],
    'faq' => [
        'label' => 'FAQ',
        'blocks' => [
            'title' => ['label' => 'Titel', 'type' => 'text'],
            'subtitle' => ['label' => 'Ondertitel', 'type' => 'text'],
            'items' => [
                'label' => 'Vragen',
                'type' => 'json',
                'columns' => 1,
                'fields' => [
                    ['key' => 'question', 'label' => 'Vraag', 'type' => 'text'],
                    ['key' => 'answer', 'label' => 'Antwoord', 'type' => 'textarea'],
                ],
            ],
        ],
    ],
    'cta' => [
        'label' => 'Afsluitende CTA',
        'blocks' => [
            'title' => ['label' => 'Titel', 'type' => 'text'],
            'subtitle' => ['label' => 'Ondertitel', 'type' => 'text'],
            'text' => ['label' => 'Tekst', 'type' => 'text'],
        ],
    ],
];

$ipadSectionDef = [
    'hero' => [
        'label' => 'Hero',
        'blocks' => [
            'badge' => ['label' => 'Badge (boven titel)', 'type' => 'text'],
            'title1' => ['label' => 'Titel regel 1 (Tablet kapot?)', 'type' => 'text'],
            'title2' => ['label' => 'Titel regel 2 (Wij maken ’m)', 'type' => 'text'],
            'title3' => ['label' => 'Titel regel 3 (weer compleet.)', 'type' => 'text'],
            'description' => ['label' => 'Beschrijving', 'type' => 'textarea'],
            'trust' => [
                'label' => 'Vertrouwenspunten (4)',
                'type' => 'json',
                'columns' => 2,
                'fields' => [
                    ['key' => 'title', 'label' => 'Tekst', 'type' => 'text'],
                ],
            ],
            'image' => ['label' => 'Tablet afbeelding (hero rechts)', 'type' => 'image'],
        ],
    ],
    'problems' => [
        'label' => 'Wat is er kapot?',
        'blocks' => [
            'title' => ['label' => 'Titel', 'type' => 'text'],
            'subtitle' => ['label' => 'Ondertitel', 'type' => 'text'],
            'left_items' => [
                'label' => 'Linker problemen (4)',
                'type' => 'json',
                'columns' => 2,
                'fields' => [
                    ['key' => 'emoji', 'label' => 'Icoon', 'type' => 'text'],
                    ['key' => 'title', 'label' => 'Titel', 'type' => 'text'],
                    ['key' => 'subtitle', 'label' => 'Ondertitel', 'type' => 'text'],
                ],
            ],
            'center_image' => ['label' => 'Middelste tablet afbeelding', 'type' => 'image'],
            'right_items' => [
                'label' => 'Rechter problemen (4)',
                'type' => 'json',
                'columns' => 2,
                'fields' => [
                    ['key' => 'emoji', 'label' => 'Icoon', 'type' => 'text'],
                    ['key' => 'title', 'label' => 'Titel', 'type' => 'text'],
                    ['key' => 'subtitle', 'label' => 'Ondertitel', 'type' => 'text'],
                ],
            ],
            'cta_title' => ['label' => 'CTA titel (Tablet laadt niet?)', 'type' => 'text'],
            'cta_text' => ['label' => 'CTA tekst', 'type' => 'textarea'],
        ],
    ],
    'screen' => [
        'label' => 'Schermreparatie',
        'blocks' => [
            'title' => ['label' => 'Titel', 'type' => 'text'],
            'subtitle' => ['label' => 'Ondertitel', 'type' => 'text'],
            'before_image' => ['label' => 'Voor afbeelding', 'type' => 'image'],
            'before_label' => ['label' => 'Voor label', 'type' => 'text'],
            'after_image' => ['label' => 'Na afbeelding', 'type' => 'image'],
            'after_label' => ['label' => 'Na label', 'type' => 'text'],
            'benefits' => [
                'label' => 'Voordelen (4)',
                'type' => 'json',
                'columns' => 1,
                'fields' => [
                    ['key' => 'title', 'label' => 'Tekst', 'type' => 'text'],
                ],
            ],
        ],
    ],
    'brands' => [
        'label' => 'Merken',
        'blocks' => [
            'title' => ['label' => 'Titel', 'type' => 'text'],
            'items' => [
                'label' => 'Merken (6)',
                'type' => 'json',
                'columns' => 3,
                'fields' => [
                    ['key' => 'emoji', 'label' => 'Icoon', 'type' => 'text'],
                    ['key' => 'title', 'label' => 'Naam', 'type' => 'text'],
                    ['key' => 'subtitle', 'label' => 'Ondertitel', 'type' => 'text'],
                ],
            ],
        ],
    ],
    'steps' => [
        'label' => 'Reparatie stappen',
        'blocks' => [
            'title' => ['label' => 'Titel', 'type' => 'text'],
            'steps' => [
                'label' => 'Stappen (3)',
                'type' => 'json',
                'columns' => 3,
                'fields' => [
                    ['key' => 'number', 'label' => 'Nummer', 'type' => 'text'],
                    ['key' => 'title', 'label' => 'Titel', 'type' => 'text'],
                    ['key' => 'description', 'label' => 'Beschrijving', 'type' => 'textarea'],
                ],
            ],
            'benefits' => [
                'label' => 'Voordelen (4)',
                'type' => 'json',
                'columns' => 1,
                'fields' => [
                    ['key' => 'title', 'label' => 'Tekst', 'type' => 'text'],
                ],
            ],
        ],
    ],
    'repair' => [
        'label' => 'Repareren of vervangen',
        'blocks' => [
            'repair_title' => ['label' => 'Repareren titel', 'type' => 'text'],
            'repair_subtitle' => ['label' => 'Repareren ondertitel', 'type' => 'text'],
            'repair_items' => [
                'label' => 'Repareren punten (5)',
                'type' => 'json',
                'columns' => 1,
                'fields' => [
                    ['key' => 'title', 'label' => 'Tekst', 'type' => 'text'],
                ],
            ],
            'repair_image' => ['label' => 'Repareren afbeelding', 'type' => 'image'],
            'replace_title' => ['label' => 'Vervangen titel', 'type' => 'text'],
            'replace_items' => [
                'label' => 'Vervangen punten (4)',
                'type' => 'json',
                'columns' => 1,
                'fields' => [
                    ['key' => 'title', 'label' => 'Tekst', 'type' => 'text'],
                ],
            ],
            'replace_image' => ['label' => 'Vervangen afbeelding', 'type' => 'image'],
            'advice_title' => ['label' => 'Advies titel', 'type' => 'text'],
            'advice_text' => ['label' => 'Advies tekst', 'type' => 'textarea'],
        ],
    ],
    'numbers' => [
        'label' => 'Cijfers',
        'blocks' => [
            'title' => ['label' => 'Titel', 'type' => 'text'],
            'items' => [
                'label' => 'Cijfers (4)',
                'type' => 'json',
                'columns' => 4,
                'fields' => [
                    ['key' => 'emoji', 'label' => 'Icoon', 'type' => 'text'],
                    ['key' => 'value', 'label' => 'Waarde', 'type' => 'text'],
                    ['key' => 'label', 'label' => 'Label', 'type' => 'text'],
                ],
            ],
        ],
    ],
    'faq' => [
        'label' => 'FAQ',
        'blocks' => [
            'title' => ['label' => 'Titel', 'type' => 'text'],
            'items' => [
                'label' => 'Vragen (4)',
                'type' => 'json',
                'columns' => 1,
                'fields' => [
                    ['key' => 'question', 'label' => 'Vraag', 'type' => 'text'],
                    ['key' => 'answer', 'label' => 'Antwoord', 'type' => 'textarea'],
                ],
            ],
        ],
    ],
    'cta' => [
        'label' => 'Afsluitende CTA',
        'blocks' => [
            'title' => ['label' => 'Titel', 'type' => 'text'],
            'subtitle' => ['label' => 'Ondertitel', 'type' => 'text'],
            'address_title' => ['label' => 'Adres titel', 'type' => 'text'],
            'address_text' => ['label' => 'Adres', 'type' => 'textarea'],
            'image' => ['label' => 'CTA afbeelding', 'type' => 'image'],
        ],
    ],
];

$softwareSectionDef = [
    'hero' => [
        'label' => 'Hero',
        'blocks' => [
            'badge' => ['label' => 'Badge (boven titel)', 'type' => 'text'],
            'title1' => ['label' => 'Titel regel 1 (Software-probleem?)', 'type' => 'text'],
            'title2' => ['label' => 'Titel regel 2 (Wij)', 'type' => 'text'],
            'title3' => ['label' => 'Titel regel 3 (lossen)', 'type' => 'text'],
            'title4' => ['label' => 'Titel regel 4 (het op.)', 'type' => 'text'],
            'description' => ['label' => 'Beschrijving', 'type' => 'textarea'],
            'bullets' => [
                'label' => 'Punten (4)',
                'type' => 'json',
                'columns' => 2,
                'fields' => [
                    ['key' => 'title', 'label' => 'Tekst', 'type' => 'text'],
                ],
            ],
            'image' => ['label' => 'Hero afbeelding (rechts)', 'type' => 'image'],
        ],
    ],
    'selector' => [
        'label' => 'Waar kunnen we je mee helpen?',
        'blocks' => [
            'title' => ['label' => 'Titel', 'type' => 'text'],
            'subtitle' => ['label' => 'Ondertitel', 'type' => 'text'],
            'tabs' => [
                'label' => 'Tabs (9)',
                'type' => 'json',
                'columns' => 3,
                'fields' => [
                    ['key' => 'emoji', 'label' => 'Icoon', 'type' => 'text'],
                    ['key' => 'title', 'label' => 'Titel', 'type' => 'text'],
                ],
            ],
            'selected_title' => ['label' => 'Geselecteerde titel', 'type' => 'text'],
            'selected_image' => ['label' => 'Geselecteerde afbeelding', 'type' => 'image'],
            'selected_image_text' => ['label' => 'Afbeelding tekst', 'type' => 'text'],
            'selected_problems' => [
                'label' => 'Problemen (8)',
                'type' => 'json',
                'columns' => 2,
                'fields' => [
                    ['key' => 'title', 'label' => 'Tekst', 'type' => 'text'],
                ],
            ],
        ],
    ],
    'services' => [
        'label' => 'Dit kunnen we voor je doen',
        'blocks' => [
            'title' => ['label' => 'Titel', 'type' => 'text'],
            'items' => [
                'label' => 'Services (6)',
                'type' => 'json',
                'columns' => 3,
                'fields' => [
                    ['key' => 'image', 'label' => 'Afbeelding', 'type' => 'image'],
                    ['key' => 'title', 'label' => 'Titel', 'type' => 'text'],
                    ['key' => 'points', 'label' => 'Punten (4, komma gescheiden)', 'type' => 'text'],
                ],
            ],
        ],
    ],
    'steps' => [
        'label' => 'Zo gaan we te werk',
        'blocks' => [
            'title' => ['label' => 'Titel', 'type' => 'text'],
            'steps' => [
                'label' => 'Stappen (5)',
                'type' => 'json',
                'columns' => 2,
                'fields' => [
                    ['key' => 'emoji', 'label' => 'Icoon', 'type' => 'text'],
                    ['key' => 'title', 'label' => 'Titel', 'type' => 'text'],
                    ['key' => 'description', 'label' => 'Beschrijving', 'type' => 'textarea'],
                ],
            ],
        ],
    ],
    'trust' => [
        'label' => 'Trust bar (blauw)',
        'blocks' => [
            'items' => [
                'label' => 'Items (4)',
                'type' => 'json',
                'columns' => 2,
                'fields' => [
                    ['key' => 'emoji', 'label' => 'Icoon', 'type' => 'text'],
                    ['key' => 'title', 'label' => 'Titel', 'type' => 'text'],
                    ['key' => 'subtitle', 'label' => 'Ondertitel', 'type' => 'text'],
                ],
            ],
        ],
    ],
    'final' => [
        'label' => 'Contact & Review',
        'blocks' => [
            'remote_title' => ['label' => 'Remote titel', 'type' => 'text'],
            'remote_text' => ['label' => 'Remote tekst', 'type' => 'textarea'],
            'contact_title' => ['label' => 'Contact titel', 'type' => 'text'],
            'contact_subtitle' => ['label' => 'Contact ondertitel', 'type' => 'text'],
            'contact_phone' => ['label' => 'Telefoon', 'type' => 'text'],
            'contact_address' => ['label' => 'Adres', 'type' => 'textarea'],
            'contact_image' => ['label' => 'Winkel afbeelding', 'type' => 'image'],
            'review_text' => ['label' => 'Review tekst', 'type' => 'textarea'],
            'review_author' => ['label' => 'Review auteur', 'type' => 'text'],
        ],
    ],
];

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
                            'label' => 'Diensten (kaarten)',
                            'type' => 'json',
                            'columns' => 2,
                            'fields' => [
                                ['key' => 'image', 'label' => 'Afbeelding (kaart op de homepage)', 'type' => 'image'],
                                ['key' => 'title', 'label' => 'Titel', 'type' => 'text'],
                                ['key' => 'icon', 'label' => 'Pictogram', 'type' => 'icon'],
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

        'overons' => [
            'label' => 'Over ons',
            'sections' => [
                'hero' => [
                    'label' => 'Hero (Over Slimme-PC)',
                    'blocks' => [
                        'badge' => ['label' => 'Badge (boven de titel)', 'type' => 'text'],
                        'title_line1' => ['label' => 'Titel regel 1', 'type' => 'text'],
                        'title_line2' => ['label' => 'Titel regel 2 (blauw)', 'type' => 'text'],
                        'description' => ['label' => 'Beschrijving', 'type' => 'textarea'],
                        'hero_image' => ['label' => 'Achtergrondafbeelding (hero)', 'type' => 'image'],
                        'hero_image_alt' => ['label' => 'Alt tekst afbeelding', 'type' => 'text'],
                        'trust_points' => [
                            'label' => 'Vertrouwenspunten (bijv. Eerlijk advies)',
                            'type' => 'json',
                            'columns' => 2,
                            'fields' => [
                                ['key' => 'icon', 'label' => 'Pictogram', 'type' => 'icon'],
                                ['key' => 'label', 'label' => 'Label', 'type' => 'text'],
                            ],
                        ],
                        'rating_value' => ['label' => 'Google beoordeling (bijv. 4.9)', 'type' => 'text'],
                        'rating_scale' => ['label' => 'Schaal (bijv. uit 5)', 'type' => 'text'],
                        'rating_count' => ['label' => 'Aantal reviews (bijv. 120+ reviews)', 'type' => 'text'],
                        'rating_url' => ['label' => 'Link naar Google Maps profiel', 'type' => 'text'],
                    ],
                ],

                'meet' => [
                    'label' => 'Meet Mo (eigenaar)',
                    'blocks' => [
                        'badge' => ['label' => 'Badge (boven de titel)', 'type' => 'text'],
                        'title_prefix' => ['label' => 'Titel (voor highlight)', 'type' => 'text'],
                        'title_highlight' => ['label' => 'Titel (blauw deel)', 'type' => 'text'],
                        'description' => ['label' => 'Beschrijving', 'type' => 'textarea'],
                        'image' => ['label' => 'Foto van Mo', 'type' => 'image'],
                        'image_alt' => ['label' => 'Alt tekst foto', 'type' => 'text'],
                        'points' => [
                            'label' => 'Punten (waar we voor staan)',
                            'type' => 'json',
                            'columns' => 1,
                            'fields' => [
                                ['key' => 'icon', 'label' => 'Pictogram', 'type' => 'icon'],
                                ['key' => 'label', 'label' => 'Label', 'type' => 'text'],
                            ],
                        ],
                        'sign_name' => ['label' => 'Handtekening naam (bijv. Mo Al Hendi)', 'type' => 'text'],
                        'sign_role' => ['label' => 'Handtekening rol (bijv. Oprichter Slimme-PC)', 'type' => 'text'],
                    ],
                ],

                'why' => [
                    'label' => 'Waarom klanten terugkomen',
                    'blocks' => [
                        'badge' => ['label' => 'Badge (boven de titel)', 'type' => 'text'],
                        'items' => [
                            'label' => 'Kaarten (4)',
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

                'werkplaats' => [
                    'label' => 'Binnen in onze werkplaats',
                    'blocks' => [
                        'badge' => ['label' => 'Badge (boven de titel)', 'type' => 'text'],
                        'items' => [
                            'label' => 'Foto\'s (sla horizontaal)',
                            'type' => 'json',
                            'columns' => 2,
                            'fields' => [
                                ['key' => 'image', 'label' => 'Afbeelding', 'type' => 'image'],
                                ['key' => 'title', 'label' => 'Titel', 'type' => 'text'],
                                ['key' => 'icon', 'label' => 'Pictogram', 'type' => 'icon'],
                            ],
                        ],
                    ],
                ],

                'reis' => [
                    'label' => 'Onze reis (tijdlijn)',
                    'blocks' => [
                        'badge' => ['label' => 'Badge (boven de titel)', 'type' => 'text'],
                        'items' => [
                            'label' => 'Mijlpalen',
                            'type' => 'json',
                            'columns' => 2,
                            'fields' => [
                                ['key' => 'icon', 'label' => 'Pictogram', 'type' => 'icon'],
                                ['key' => 'year', 'label' => 'Jaartal (bijv. 2018)', 'type' => 'text'],
                                ['key' => 'title', 'label' => 'Titel', 'type' => 'text'],
                            ],
                        ],
                    ],
                ],

                'reviews' => [
                    'label' => 'Wat klanten zeggen',
                    'blocks' => [
                        'badge' => ['label' => 'Badge (boven de titel)', 'type' => 'text'],
                        'items' => [
                            'label' => 'Reviews',
                            'type' => 'json',
                            'columns' => 1,
                            'fields' => [
                                ['key' => 'stars', 'label' => 'Aantal sterren (1 t/m 5)', 'type' => 'text'],
                                ['key' => 'name', 'label' => 'Naam (bijv. Mark, Apeldoorn)', 'type' => 'text'],
                                ['key' => 'quote', 'label' => 'Tekst', 'type' => 'textarea'],
                            ],
                        ],
                    ],
                ],

                'trust' => [
                    'label' => 'Trust (onderaan de pagina)',
                    'blocks' => [
                        'items' => [
                            'label' => 'Vertrouwenspunten',
                            'type' => 'json',
                            'columns' => 2,
                            'fields' => [
                                ['key' => 'icon', 'label' => 'Pictogram', 'type' => 'icon'],
                                ['key' => 'title', 'label' => 'Titel', 'type' => 'text'],
                                ['key' => 'subtitle', 'label' => 'Ondertitel', 'type' => 'text'],
                            ],
                        ],
                    ],
                ],
            ],
        ],
        'laptopreparatie' => ['label' => 'Laptop Reparatie', 'sections' => $serviceSectionDef],
        'pcreparatie' => ['label' => 'PC Reparatie', 'sections' => $serviceSectionDef],
        'mac' => ['label' => 'Mac Reparatie', 'sections' => $macSectionDef],
        'datarecovery' => ['label' => 'Data Recovery', 'sections' => $serviceSectionDef],
        'ipad' => ['label' => 'iPad Reparatie', 'sections' => $ipadSectionDef],
        'moederbord' => ['label' => 'Moederbord Reparatie', 'sections' => $serviceSectionDef],
        'software' => ['label' => 'Software & Windows', 'sections' => $softwareSectionDef],
        'netwerk' => ['label' => 'Netwerkoplossingen', 'sections' => $serviceSectionDef],
        'console' => ['label' => 'Playstation / Xbox', 'sections' => $consoleSectionDef],
    ],

    /*
    | Design / site settings — stored as JSON in content_meta (meta_key 'design')
    | Brand colors and the font are FIXED in code (resources/views/landing/layouts/app.blade.php
    | + resources/css/landing.css) and are intentionally NOT editable here.
    */
    'service_slugs' => [
        'laptop-reparatie' => 'laptopreparatie',
        'pc-reparatie' => 'pcreparatie',
        'mac-reparatie' => 'mac',
        'macbook-reparatie' => 'mac',
        'data-recovery' => 'datarecovery',
        'ipad-reparatie' => 'ipad',
        'moederbord-reparatie' => 'moederbord',
        'software-windows' => 'software',
        'netwerkoplossingen' => 'netwerk',
        'console-reparatie' => 'console',
    ],
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

