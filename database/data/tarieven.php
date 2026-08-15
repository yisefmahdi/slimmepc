<?php

/*
|--------------------------------------------------------------------------
| Default content for the Tarieven page (shared by the seeder + migration)
|--------------------------------------------------------------------------
| Mirrors D:\sm 2026\slimmepc2026nieuwe\tariven.html
*/

return [
    'hero' => [
        'badge' => 'Transparant & eerlijk',
        'title_line1' => 'Tarieven zonder',
        'title_line2' => 'verrassingen',
        'description' => 'Bekijk de meest voorkomende kosten voor reparatie, onderhoud en IT-service.',
        'button1_text' => 'Bekijk tarieven',
        'button1_url' => '#tarieven',
        'button2_text' => 'Reparatie aanmelden',
        'button2_url' => '/reparatie-aanmelden',
        'hero_image' => 'assets/img/landing/586bcf7a-9154-4fb5-af89-87899a0bca24.png',
        'hero_image_alt' => 'Tarieven voor computerreparatie',
        'trust_points' => [
            ['icon' => 'check', 'label' => 'Diagnose vanaf €35'],
            ['icon' => 'check', 'label' => 'Vooraf akkoord'],
            ['icon' => 'check', 'label' => 'Prijzen inclusief btw'],
        ],
    ],

    'pricing' => [
        'heading' => 'Kies je apparaat of dienst',
        'description' => 'Selecteer een categorie om alleen de bijbehorende tarieven te bekijken.',
        'categories' => [
            [
                'icon' => 'laptop',
                'label' => 'Laptop & PC',
                'title' => 'Laptop reparatie',
                'description' => 'Snelle en professionele reparatie voor alle laptops en desktopcomputers.',
                'image' => '53f89edd-3207-4891-b580-7246605e1858.png',
                'notice' => 'De exacte prijs hangt af van het model en de benodigde onderdelen.',
                'prices' => [
                    ['icon' => 'search', 'title' => 'Diagnose', 'prefix' => '', 'price' => '€35'],
                    ['icon' => 'settings', 'title' => 'Onderhoud & reiniging', 'prefix' => 'vanaf', 'price' => '€65'],
                    ['icon' => 'panels-top-left', 'title' => 'Windows installatie', 'prefix' => 'vanaf', 'price' => '€79'],
                    ['icon' => 'hard-drive', 'title' => 'SSD upgrade', 'prefix' => 'vanaf', 'price' => '€49'],
                    ['icon' => 'monitor', 'title' => 'Scherm vervangen', 'prefix' => '', 'price' => 'Offerte'],
                ],
            ],
            [
                'icon' => 'apple',
                'label' => 'MacBook & iMac',
                'title' => 'Apple reparatie',
                'description' => 'Professionele reparatie voor MacBook Air, MacBook Pro en iMac.',
                'image' => '363f8f55-fba7-4f23-88db-8c8e728d522e.png',
                'notice' => 'De prijs hangt af van het model, bouwjaar en het benodigde onderdeel.',
                'prices' => [
                    ['icon' => 'search', 'title' => 'Diagnose', 'prefix' => '', 'price' => '€35'],
                    ['icon' => 'monitor', 'title' => 'Scherm vervangen', 'prefix' => '', 'price' => 'Offerte'],
                    ['icon' => 'battery-charging', 'title' => 'Batterij vervangen', 'prefix' => '', 'price' => 'Offerte'],
                    ['icon' => 'keyboard', 'title' => 'Toetsenbord vervangen', 'prefix' => '', 'price' => 'Offerte'],
                    ['icon' => 'microchip', 'title' => 'Logicboard reparatie', 'prefix' => '', 'price' => 'Offerte'],
                ],
            ],
            [
                'icon' => 'gamepad-2',
                'label' => 'Consoles',
                'title' => 'Console reparatie',
                'description' => 'Onderhoud en reparatie voor PlayStation- en Xbox-consoles.',
                'image' => 'Xbox-Series-X-and-Playstation-5-ps5.webp',
                'notice' => 'De prijs hangt af van het consolemodel en de aard van het defect.',
                'prices' => [
                    ['icon' => 'search', 'title' => 'Diagnose', 'prefix' => '', 'price' => '€35'],
                    ['icon' => 'fan', 'title' => 'PS5 onderhoud', 'prefix' => '', 'price' => '€65'],
                    ['icon' => 'cable', 'title' => 'HDMI reparatie', 'prefix' => '', 'price' => 'Offerte'],
                    ['icon' => 'disc-3', 'title' => 'Disc drive reparatie', 'prefix' => '', 'price' => 'Offerte'],
                    ['icon' => 'gamepad-2', 'title' => 'Controller reparatie', 'prefix' => '', 'price' => 'Offerte'],
                ],
            ],
            [
                'icon' => 'database',
                'label' => 'Data recovery',
                'title' => 'Dataherstel',
                'description' => 'Herstel van gegevens van HDD, SSD en andere opslagmedia.',
                'image' => 'e6cc3cb7-5aea-460d-a1a9-884318edc64a.png',
                'notice' => 'Data recovery is maatwerk. De prijs hangt af van het opslagmedium en de beschadiging.',
                'prices' => [
                    ['icon' => 'search', 'title' => 'Onderzoek', 'prefix' => '', 'price' => '€35'],
                    ['icon' => 'file-search', 'title' => 'Softwarematig herstel', 'prefix' => 'vanaf', 'price' => '€99'],
                    ['icon' => 'hard-drive-download', 'title' => 'Data recovery HDD / SSD', 'prefix' => 'vanaf', 'price' => '€200'],
                    ['icon' => 'panels-top-left', 'title' => 'Data recovery + Windows', 'prefix' => 'vanaf', 'price' => '€300'],
                    ['icon' => 'shield-check', 'title' => 'Externe specialist', 'prefix' => '', 'price' => 'Offerte'],
                ],
            ],
            [
                'icon' => 'ellipsis',
                'label' => 'Overig',
                'title' => 'Software, netwerk & meer',
                'description' => 'Ondersteuning voor Windows, wifi, printers, tablets en andere IT-problemen.',
                'image' => 'chatgpt-image-25-jul-2026.png',
                'notice' => 'Staat jouw probleem er niet bij? Meld je apparaat aan en beschrijf de situatie.',
                'prices' => [
                    ['icon' => 'panels-top-left', 'title' => 'Windows installatie', 'prefix' => 'vanaf', 'price' => '€79'],
                    ['icon' => 'shield-check', 'title' => 'Virus verwijderen', 'prefix' => 'vanaf', 'price' => '€69'],
                    ['icon' => 'wifi', 'title' => 'Wifi probleem oplossen', 'prefix' => '', 'price' => 'Offerte'],
                    ['icon' => 'printer', 'title' => 'Printer installeren', 'prefix' => '', 'price' => 'Offerte'],
                    ['icon' => 'tablet-smartphone', 'title' => 'iPad / tablet reparatie', 'prefix' => '', 'price' => 'Offerte'],
                ],
            ],
        ],
    ],

    'extra' => [
        'accordions' => [
            [
                'icon' => 'file-euro',
                'title' => 'Algemene tarieven',
                'accent' => 'blue',
                'description' => 'Voorrijkosten, arbeid, diagnose en hulp op afstand.',
                'prices' => [
                    ['title' => 'Standaard diagnose', 'description' => 'Onderzoek naar hardware- of softwareproblemen', 'price' => '€35'],
                    ['title' => 'Diagnose gaming-pc', 'description' => 'Uitgebreide hardware- en prestatietest', 'price' => '€75'],
                    ['title' => 'Voorrijkosten', 'description' => 'Binnen Apeldoorn', 'price' => 'vanaf €15'],
                    ['title' => 'Arbeid', 'description' => 'Berekening per 15 minuten', 'price' => 'vanaf €13,50'],
                    ['title' => 'Hulp op afstand', 'description' => 'Remote ondersteuning', 'price' => 'vanaf €18,50'],
                    ['title' => 'Jaarabonnement', 'description' => 'Kan op elk moment worden afgesloten', 'price' => '€30 per jaar'],
                ],
            ],
            [
                'icon' => 'building-2',
                'title' => 'Zakelijke IT-service',
                'accent' => 'green',
                'description' => 'Remote support, werkplekbeheer en periodiek onderhoud.',
                'prices' => [
                    ['title' => 'Remote support', 'description' => 'Zakelijke ondersteuning op afstand', 'price' => 'vanaf €65 p/u'],
                    ['title' => 'Service op locatie', 'description' => 'Ondersteuning op bedrijfslocatie', 'price' => 'vanaf €85 p/u'],
                    ['title' => 'Werkplek installatie', 'description' => 'Computer, accounts en software', 'price' => 'vanaf €95'],
                    ['title' => 'Netwerkbeheer', 'description' => 'Onderhoud en ondersteuning', 'price' => 'Offerte'],
                    ['title' => 'Periodiek onderhoud', 'description' => 'Vaste onderhoudsafspraken', 'price' => 'Maatwerk'],
                ],
            ],
        ],
        'trust_cards' => [
            ['icon' => 'shield-check', 'title' => 'Transparante prijzen', 'description' => 'Geen verborgen kosten, je weet vooraf waar je aan toe bent.'],
            ['icon' => 'circle-check-big', 'title' => 'Vooraf akkoord', 'description' => 'Je ontvangt eerst toestemming bij extra kosten.'],
            ['icon' => 'badge-check', 'title' => 'Garantie op reparatie', 'description' => 'Garantie op werkzaamheden en geselecteerde onderdelen.'],
            ['icon' => 'clock-3', 'title' => 'Snelle service', 'description' => 'Wij staan voor je klaar en helpen je snel verder.'],
        ],
    ],
];