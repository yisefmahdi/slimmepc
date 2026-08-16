<?php

/*
|--------------------------------------------------------------------------
| Default content for the Contact page (shared by the seeder + migration)
|--------------------------------------------------------------------------
| Mirrors D:\sm 2026\slimmepc2026nieuwe\contact.html
*/

return [
    'hero' => [
        'badge' => 'Contact met Slimme-PC',
        'title_line1' => 'We helpen je',
        'title_line2' => 'graag verder.',
        'description' => 'Heb je een vraag, wil je een reparatie bespreken of zakelijk met ons samenwerken? Neem gerust contact met ons op.',
        'button1_text' => 'Bericht versturen',
        'button1_url' => '#contactformulier',
        'button2_text' => 'WhatsApp ons',
        'whatsapp_number' => '31617100945',
        'hero_image' => 'assets/img/landing/6F69A001-617B-44CE-B7E9-75C6165A3A4F_1_105_c.jpeg',
        'hero_image_alt' => 'Contact met Slimme-PC',
        'trust_points' => [
            ['icon' => 'check', 'label' => 'Reactie meestal binnen één werkdag'],
            ['icon' => 'check', 'label' => 'Persoonlijk contact vanuit Apeldoorn'],
        ],
    ],

    'gegevens' => [
        'card1_title' => 'Contactgegevens',
        'card1_icon' => 'map-pin',
        'company_name' => 'Slimme-PC',
        'address' => "Laan van de Mensenrechten 400\n7331 VX Apeldoorn",
        'kvk' => '86906720',
        'btw' => 'NL864142560B01',
        'route_label' => 'Route bekijken',
        'route_url' => '#locatie',

        'card2_title' => 'Service & support',
        'card2_icon' => 'headphones',
        'contact_methods' => [
            ['icon' => 'phone', 'label' => 'Telefoon', 'value' => '055 203 21 45', 'url' => 'tel:+31552032145'],
            ['icon' => 'mail', 'label' => 'E-mailadres', 'value' => 'info@slimme-pc.nl', 'url' => 'mailto:info@slimme-pc.nl'],
            ['icon' => 'message-circle', 'label' => 'WhatsApp', 'value' => 'Stuur ons een bericht', 'url' => 'https://wa.me/31617100945'],
        ],

        'card3_title' => 'Openingstijden',
        'card3_icon' => 'clock',
        'opening_hours' => [
            ['day' => 'Maandag – vrijdag', 'note' => 'Reguliere openingstijden', 'time' => '09:00 – 17:00', 'closed' => false],
            ['day' => 'Zaterdag', 'note' => 'Alleen op afspraak', 'time' => '10:00 – 14:00', 'closed' => false],
            ['day' => 'Zondag', 'note' => 'Wij zijn gesloten', 'time' => 'Gesloten', 'closed' => true],
        ],
    ],

    'formulier' => [
        'badge' => 'Contactformulier',
        'title_line1' => 'Waar kunnen we',
        'title_line2' => 'je mee helpen?',
        'description' => 'Laat je gegevens en bericht achter. Wij nemen zo snel mogelijk contact met je op.',
        'benefits' => [
            ['label' => 'Geen verplichtingen'],
            ['label' => 'Duidelijk en persoonlijk antwoord'],
            ['label' => 'Bestand of foto meesturen mogelijk'],
        ],
    ],

    'locatie' => [
        'badge' => 'Onze locatie',
        'title_line1' => 'Je bent welkom',
        'title_line2' => 'in Apeldoorn.',
        'description' => 'Je kunt je apparaat tijdens onze openingstijden langsbrengen. Voor sommige werkzaamheden adviseren wij vooraf contact op te nemen.',
        'map_src' => 'https://www.google.com/maps?q=Laan%20van%20de%20Mensenrechten%20400%20Apeldoorn&output=embed',
        'route_label' => 'Route plannen',
        'route_url' => 'https://www.google.com/maps/search/?api=1&query=Laan+van+de+Mensenrechten+400+Apeldoorn',
        'location_items' => [
            ['icon' => 'map-pin', 'title' => 'Slimme-PC Apeldoorn', 'text' => "Laan van de Mensenrechten 400\n7331 VX Apeldoorn"],
            ['icon' => 'square-parking', 'title' => '', 'text' => 'Parkeergelegenheid in de omgeving'],
            ['icon' => 'bus', 'title' => '', 'text' => 'Goed bereikbaar met het openbaar vervoer'],
        ],
    ],
];