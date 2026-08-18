<?php

/*
|--------------------------------------------------------------------------
| Default content for the Over ons page (shared by the seeder + migration)
|--------------------------------------------------------------------------
| Mirrors D:\sm 2026\slimmepc2026nieuwe\about.html
*/

return [
    'hero' => [
        'badge' => 'Over Slimme-PC',
        'title_line1' => 'Wij repareren niet alleen apparaten.',
        'title_line2' => 'Wij lossen problemen op.',
        'description' => 'Slimme-PC is dé specialist in reparatie, upgrade en onderhoud van computers, laptops, consoles en meer. Met passie voor technologie en oog voor kwaliteit helpen wij jou verder.',
        'hero_image' => 'assets/img/landing/81kelZG3xvereuCNPzQ9aMcoshigTEsgLLcVE7et.jpg',
        'hero_image_alt' => 'Professionele reparatiewerkplaats',
        'trust_points' => [
            ['icon' => 'circle-check', 'label' => 'Eerlijk advies'],
            ['icon' => 'circle-check', 'label' => 'Transparant'],
            ['icon' => 'circle-check', 'label' => 'Kwaliteit'],
            ['icon' => 'circle-check', 'label' => 'Snelle service'],
        ],
        'rating_value' => '4.9',
        'rating_scale' => 'uit 5',
        'rating_count' => '120+ reviews',
        'rating_url' => 'https://www.google.com/maps/search/?api=1&query=Slimme-PC+Apeldoorn',
    ],

    'meet' => [
        'badge' => 'Meet Mo',
        'title_prefix' => 'Mijn naam is',
        'title_highlight' => 'Mohammed.',
        'description' => 'In 2018 ben ik Slimme-PC gestart met één doel: eerlijke, transparante en kwalitatieve reparaties aanbieden in Apeldoorn.',
        'image' => 'assets/img/landing/qmkFcXn7ryxcedJaDw97I9jtpAjRKXOZ6dqnwLCh.png',
        'image_alt' => 'Mohammed, eigenaar van Slimme-PC',
        'points' => [
            ['icon' => 'circle-check', 'label' => 'Wij repareren op componentniveau, niet alleen vervangen.'],
            ['icon' => 'circle-check', 'label' => 'Je krijgt altijd duidelijkheid vooraf, geen verrassingen achteraf.'],
            ['icon' => 'circle-check', 'label' => 'Jouw gegevens en privacy behandelen wij met de grootste zorg.'],
            ['icon' => 'circle-check', 'label' => 'Kwaliteit is geen keuze, het is onze standaard.'],
        ],
        'sign_name' => 'Mo Al Hendi',
        'sign_role' => 'Oprichter Slimme-PC',
    ],

    'why' => [
        'badge' => 'Waarom klanten terugkomen',
        'items' => [
            ['icon' => 'shield-check', 'title' => 'Transparant & eerlijk', 'description' => 'Je weet altijd wat er aan de hand is en wat het gaat kosten. Geen kleine lettertjes.'],
            ['icon' => 'lock-keyhole', 'title' => 'Jouw data is veilig', 'description' => 'Wij behandelen jouw gegevens met de grootste zorg en respect voor privacy.'],
            ['icon' => 'badge-check', 'title' => 'Kwaliteit boven alles', 'description' => 'Wij gebruiken hoogwaardige onderdelen en professionele apparatuur.'],
            ['icon' => 'shield', 'title' => 'Garantie op reparaties', 'description' => 'Standaard garantie op alle reparaties, zodat jij met een gerust hart verder kunt.'],
        ],
    ],

    'werkplaats' => [
        'badge' => 'Binnen in onze werkplaats',
        'items' => [
            ['image' => 'about-werkplaats-1.jpg', 'icon' => 'microscope', 'title' => 'Precisie microscoop'],
            ['image' => 'about-werkplaats-2.jpg', 'icon' => 'wrench', 'title' => 'Rework station'],
            ['image' => 'about-werkplaats-3.jpg', 'icon' => 'activity', 'title' => 'Meetapparatuur'],
            ['image' => 'about-werkplaats-4.jpg', 'icon' => 'boxes', 'title' => 'Onderdelen & voorraad'],
            ['image' => 'about-werkplaats-5.webp', 'icon' => 'monitor-cog', 'title' => 'Werkplek'],
        ],
    ],

    'reis' => [
        'badge' => 'Onze reis',
        'items' => [
            ['icon' => 'clipboard-list', 'year' => '2018', 'title' => 'Start Slimme-PC in Apeldoorn'],
            ['icon' => 'wrench', 'year' => '2020', 'title' => 'Uitbreiding naar moederbordreparaties'],
            ['icon' => 'monitor-smartphone', 'year' => '2022', 'title' => 'Specialisatie in Apple & Mac'],
            ['icon' => 'package', 'year' => '2024', 'title' => 'Nieuwe werkplaats & meer apparatuur'],
            ['icon' => 'star', 'year' => '2026', 'title' => 'Focus op kwaliteit, service & groei'],
        ],
    ],

    'reviews' => [
        'badge' => 'Wat klanten zeggen',
        'items' => [
            ['stars' => '5', 'name' => 'Mark, Apeldoorn', 'quote' => 'Topservice! Mijn laptop mocht dan niet meer gemaakt kunnen worden, maar Mo heeft het wel voor elkaar gekregen. Duidelijke communicatie en eerlijk advies.'],
            ['stars' => '5', 'name' => 'Kevin, Apeldoorn', 'quote' => 'PlayStation 5 was heet en viel steeds uit. Binnen twee dagen gerepareerd en weer als nieuw. Eerlijk, snel en professioneel.'],
            ['stars' => '5', 'name' => 'Saskia, Apeldoorn', 'quote' => 'Gegevensrecovery van mijn harde schijf was een succes. Zeer blij dat mijn bestanden veilig zijn hersteld. Aanrader!'],
        ],
    ],

    'trust' => [
        'items' => [
            ['icon' => 'stethoscope', 'title' => 'Duidelijke diagnose', 'subtitle' => 'Je weet waar je aan toe bent.'],
            ['icon' => 'receipt-text', 'title' => 'Geen verborgen kosten', 'subtitle' => 'Transparant en duidelijk.'],
            ['icon' => 'wrench', 'title' => 'Snelle service', 'subtitle' => 'Meestal binnen 1–3 werkdagen.'],
            ['icon' => 'message-circle-heart', 'title' => 'Persoonlijk contact', 'subtitle' => 'We denken met je mee.'],
        ],
    ],
];
