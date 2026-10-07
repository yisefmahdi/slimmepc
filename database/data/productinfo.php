<?php

// Defaults for the 'productinfo' CMS page (levering & garantie).
// Shown on the product page (trust row, warranty tab, "Snel in huis" card)
// and in the cart (checklist + trust bar) — single source of truth.
return [
    'info' => [
        'trust_items' => [
            ['icon' => 'badge-check', 'label' => '2 jaar garantie'],
            ['icon' => 'map-pin', 'label' => 'Afhalen Apeldoorn'],
            ['icon' => 'lock', 'label' => 'Veilig betalen'],
        ],
        'warranty_tab_title' => 'Levering & garantie',
        'warranty_items' => [
            ['label' => 'Gratis verzending vanaf €75'],
            ['label' => 'Afhalen bij Slimme-PC in Apeldoorn mogelijk'],
            ['label' => '2 jaar garantie'],
        ],
        'snel_title' => 'Snel in huis',
        'snel_subtitle' => 'Bestel vandaag en wij zorgen dat jouw laptop zo snel mogelijk onderweg is.',
        'snel_items' => [
            ['label' => 'Gratis verzending vanaf €75'],
            ['label' => 'Afhalen in Apeldoorn mogelijk'],
            ['label' => '2 jaar garantie'],
            ['label' => 'Veilig online betalen'],
        ],
        'cart_trust' => [
            ['title' => 'Gratis verzending', 'subtitle' => 'vanaf €75'],
            ['title' => 'Afhalen in Apeldoorn', 'subtitle' => 'Binnen 24 uur klaar'],
            ['title' => '2 jaar garantie', 'subtitle' => 'Op al onze producten'],
            ['title' => 'Veilig betalen', 'subtitle' => 'iDEAL, Bancontact, PayPal'],
        ],
        'webshop_trust' => [
            ['icon' => 'truck', 'title' => 'Gratis verzending', 'subtitle' => 'vanaf €75'],
            ['icon' => 'map-pin', 'title' => 'Afhalen in Apeldoorn', 'subtitle' => 'Binnen openingstijden'],
            ['icon' => 'shield-check', 'title' => 'Garantie', 'subtitle' => 'Op onze producten'],
            ['icon' => 'lock-keyhole', 'title' => 'Veilig betalen', 'subtitle' => 'Betrouwbare betaalmethodes'],
        ],
        'payment_badges' => [
            ['image' => null, 'label' => 'iDEAL'],
            ['image' => null, 'label' => 'Bancontact'],
            ['image' => null, 'label' => 'VISA'],
        ],
    ],
];
