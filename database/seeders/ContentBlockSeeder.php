<?php

namespace Database\Seeders;

use App\Models\ContentBlock;
use App\Models\ContentMeta;
use App\Support\Cms;
use Illuminate\Database\Seeder;

class ContentBlockSeeder extends Seeder
{
    /**
     * Default content — mirrored from step-1/home.html (Dutch).
     */
    public function run(): void
    {
        $content = [
            'header' => [
                'logo_text' => 'SLIMME-PC',
                'tagline' => 'Reparatie · Verkoop · IT-service',
                'logo_image' => 'assets/img/landing/logo.webp',
                'nav_links' => [
                    ['label' => 'Home', 'url' => '/', 'icon' => 'house', 'active' => true],
                    ['label' => 'Over ons', 'url' => '/over-ons', 'icon' => 'users', 'active' => false],
                    ['label' => 'Lid worden', 'url' => '/lid-worden', 'icon' => 'user-plus', 'active' => false],
                    ['label' => 'Tarieven', 'url' => '/tarieven', 'icon' => 'tag', 'active' => false],
                    ['label' => 'Contact', 'url' => '/contact', 'icon' => 'mail', 'active' => false],
                ],
                'webshop_dropdown' => [
                    ['label' => 'Laptops', 'url' => '/webshop/laptops', 'icon' => 'laptop', 'subtitle' => 'Voor werk en thuis'],
                    ['label' => 'Computers', 'url' => '/webshop/computers', 'icon' => 'monitor', 'subtitle' => 'Desktop en maatwerk'],
                    ['label' => 'Onderdelen', 'url' => '/webshop/onderdelen', 'icon' => 'cpu', 'subtitle' => 'SSD, RAM en laders'],
                    ['label' => 'Alles bekijken', 'url' => '/webshop', 'icon' => 'arrow-right', 'subtitle' => 'Volledig assortiment'],
                ],
                'services_dropdown' => [
                    ['label' => 'Laptop & PC reparatie', 'url' => '/diensten/laptop-reparatie', 'icon' => 'laptop', 'subtitle' => 'Diagnose en reparatie'],
                    ['label' => 'Moederbord reparatie', 'url' => '/diensten/moederbord', 'icon' => 'microchip', 'subtitle' => 'Component-level service'],
                    ['label' => 'Data recovery', 'url' => '/diensten/data-recovery', 'icon' => 'database-backup', 'subtitle' => 'Bestanden herstellen'],
                    ['label' => 'Onderhoud & upgrades', 'url' => '/diensten/onderhoud', 'icon' => 'fan', 'subtitle' => 'Reinigen, SSD en RAM'],
                ],
                'search_placeholder' => 'Bijvoorbeeld: laptop reparatie',
                'wishlist_count' => '0',
                'cart_count' => '2',
                'account_label' => 'Account',
            ],

            'hero' => [
                'badge' => 'IT-service & computerreparatie in Apeldoorn',
                'title_line1' => 'Computerproblemen?',
                'title_line2' => 'Slimme-PC helpt je',
                'title_gradient' => 'snel verder.',
                'description' => 'Van diagnose tot reparatie, testen en levering. Eerlijk, professioneel en persoonlijk vanuit onze werkplaats in Apeldoorn.',
                'buttons' => [
                    ['label' => 'Reparatie aanmelden', 'url' => '/reparatie-aanmelden', 'icon' => 'wrench', 'variant' => 'primary'],
                    ['label' => 'Afspraak maken', 'url' => '/afspraak', 'icon' => 'calendar-check', 'variant' => 'outline'],
                ],
                'trust' => [
                    ['icon' => 'shield-check', 'title' => 'Eerlijke prijs', 'subtitle' => 'Geen verrassingen'],
                    ['icon' => 'zap', 'title' => 'Snelle diagnose', 'subtitle' => 'Vaak dezelfde dag'],
                    ['icon' => 'user-round', 'title' => 'Persoonlijk', 'subtitle' => 'Advies op maat'],
                ],
                'hero_image' => 'assets/img/landing/53f89edd-3207-4891-b580-7246605e1858.png',
                'hero_image_mobile' => 'assets/img/landing/b4c74892-bfeb-4b3f-8968-32762aa3af6a.png',
                'hero_image_alt' => 'Laptop reparatie bij Slimme-PC',
            ],

            'process' => [
                'steps' => [
                    ['number' => '1', 'icon' => 'activity', 'title' => 'Diagnose', 'description' => 'We analyseren het probleem.'],
                    ['number' => '2', 'icon' => 'wrench', 'title' => 'Reparatie', 'description' => 'We voeren de reparatie uit.'],
                    ['number' => '3', 'icon' => 'clipboard-check', 'title' => 'Testen', 'description' => 'We testen alles grondig.'],
                    ['number' => '4', 'icon' => 'package-check', 'title' => 'Levering', 'description' => 'Klaar om op te halen.'],
                ],
            ],

            'why' => [
                'badge' => 'Waarom voor ons kiezen?',
                'title_prefix' => 'Waarom kiezen klanten voor',
                'title_highlight' => 'Slimme-PC?',
                'description' => 'Wij combineren vakmanschap, snelle service en eerlijke prijzen om jou de beste reparatie-ervaring te bieden.',
                'hub_icon' => 'laptop-minimal-check',
                'hub_title' => 'SLIMME-PC',
                'hub_subtitle' => 'IT-service & Reparatie',
                'benefits' => [
                    ['icon' => 'zap', 'title' => 'Snelle service', 'description' => 'Veel reparaties zijn snel klaar. Vaak zelfs dezelfde dag.'],
                    ['icon' => 'shield-check', 'title' => 'Garantie op reparatie', 'description' => 'Garantie op onze reparaties, zodat je verzekerd bent van kwaliteit.'],
                    ['icon' => 'user-round-check', 'title' => 'Persoonlijk advies', 'description' => 'Eerlijk en helder advies, afgestemd op jouw apparaat en situatie.'],
                    ['icon' => 'badge-euro', 'title' => 'Eerlijke prijzen', 'description' => 'Transparante prijzen zonder onverwachte kosten achteraf.'],
                    ['icon' => 'wrench', 'title' => 'Ervaren monteurs', 'description' => 'Ervaring met complexe hardware-, software- en moederbordreparaties.'],
                    ['icon' => 'star', 'title' => 'Google Reviews', 'description' => 'Klanten waarderen onze service, duidelijkheid en persoonlijke aanpak.'],
                ],
                'stats' => [
                    ['icon' => 'users-round', 'value' => '1200+', 'label' => 'Tevreden klanten'],
                    ['icon' => 'laptop', 'value' => '2500+', 'label' => 'Apparaten gerepareerd'],
                    ['icon' => 'calendar-check-2', 'value' => '15+', 'label' => 'Jaar ervaring'],
                    ['icon' => 'map-pin', 'value' => 'Apeldoorn', 'label' => 'Lokaal & betrokken'],
                ],
            ],

            'services' => [
                'badge' => 'Onze diensten',
                'title_prefix' => 'Kies de',
                'title_highlight' => 'service',
                'title_suffix' => 'die bij jou past',
                'description' => 'Van hardware reparaties tot data recovery, upgrades en software. Klik op een service en ontdek hoe wij je kunnen helpen.',
                'services' => [
                    ['icon' => 'laptop', 'title' => 'Laptop Reparatie', 'description' => 'Scherm, toetsenbord, batterij, scharnieren en andere laptopproblemen.', 'link' => '/diensten/laptop-reparatie', 'image' => '53f89edd-3207-4891-b580-7246605e1858.png', 'hidden' => false],
                    ['icon' => 'monitor', 'title' => 'PC Reparatie', 'description' => 'Desktop traag, start niet of hardwareproblemen? Wij lossen het snel op.', 'link' => '/pc.html', 'image' => 'aad70dda-b34c-4737-881f-eddab9c5b46c.png', 'hidden' => false],
                    ['icon' => 'apple', 'title' => 'MacBook Reparatie', 'description' => 'Professionele reparatie voor MacBook Air, MacBook Pro en andere Apple-apparaten.', 'link' => '/diensten/macbook-reparatie', 'image' => '363f8f55-fba7-4f23-88db-8c8e728d522e.png', 'hidden' => false],
                    ['icon' => 'database', 'title' => 'Data Recovery', 'description' => 'Wij herstellen gegevens van beschadigde of niet meer werkende opslagapparaten.', 'link' => '/datarecovery.html', 'image' => 'e6cc3cb7-5aea-460d-a1a9-884318edc64a.png', 'hidden' => false],
                    ['icon' => 'arrow-up', 'title' => 'Ipad', 'description' => 'Wij herstellen gegevens van beschadigde of niet meer werkende opslagapparaten.', 'link' => '/smart-apparaten.html', 'image' => 'ipad.png', 'hidden' => false],
                    ['icon' => 'cpu', 'title' => 'Moederbord Reparatie', 'description' => 'Component-level reparatie bij complexe storingen, geen beeld en stroomproblemen.', 'link' => '/matherbord-reparatie.html', 'image' => '85cea032-2e38-4f3d-8071-f8677565c0a3.png', 'hidden' => false],
                    ['icon' => 'panels-top-left', 'title' => 'Software & Windows', 'description' => 'Installatie, updates, drivers, optimalisatie en het verwijderen van virussen.', 'link' => '/software.html', 'image' => '45e1353e-fb4a-4fec-9e45-1e00df7b86ec.png', 'hidden' => false],
                    ['icon' => 'wifi', 'title' => 'Netwerkoplossingen', 'description' => 'Installatie en onderhoud van stabiele, veilige en efficiënte netwerken.', 'link' => '/diensten/netwerkoplossingen', 'image' => 'cd15d7f8-c7f8-4d86-aa46-b51b04415092.png', 'hidden' => false],
                    ['icon' => 'gamepad-2', 'title' => 'Console reparatie · Apeldoorn', 'description' => 'PlayStation, Xbox en Nintendo reparatie: HDMI, ventilator, laadpoort en stroomproblemen lossen wij vakkundig op.', 'link' => '/diensten/console-reparatie', 'image' => 'Xbox-Series-X-and-Playstation-5-ps5.webp', 'hidden' => false],
                ],
            ],

            'shop' => [
                'badge' => 'Slimme-PC Webshop',
                'title_prefix' => 'Populaire',
                'title_highlight' => 'producten',
                'description' => 'Betrouwbare hardware, geselecteerd door onze specialisten. Kwaliteit, performance en de beste service.',
                'benefits' => [
                    ['icon' => 'shield-check', 'title' => 'Kwaliteit gegarandeerd', 'subtitle' => 'Alle producten zijn gecontroleerd'],
                    ['icon' => 'truck', 'title' => 'Snelle levering', 'subtitle' => 'Snel en betrouwbaar geleverd'],
                    ['icon' => 'headphones', 'title' => 'Persoonlijk advies', 'subtitle' => 'Wij helpen je de juiste keuze maken'],
                ],
                'cta_label' => 'Bekijk All!',
                'cta_url' => '/webshop',
                'note_title' => 'Meer dan 500',
                'note_subtitle' => 'producten online!',
                'products' => [
                    ['badge' => 'Populair', 'badge_color' => 'blue', 'image' => 'IEuzcakRreQh6cjtEhkoRNYd41py1KaCEU5fEMve.jpg', 'title' => 'HP All in One', 'specs' => '24" FHD | i5 | 16GB | 512GB SSD', 'price' => '€599,00', 'link' => '/product/asus-vivobook-15', 'in_stock' => true],
                    ['badge' => 'Aanbevolen', 'badge_color' => 'green', 'image' => 'VmKcOTw9ialCZa7W9u8Iu6kxLMBNDHD8jowg79L8.webp', 'title' => 'HP Laptop', 'specs' => 'Ryzen 5 | 16GB | RTX 4060 | 1TB SSD', 'price' => '€1.199,00', 'link' => '/product/gaming-pc-pro', 'in_stock' => true],
                    ['badge' => '', 'badge_color' => 'blue', 'image' => 'yEbdqw2tXz7FOrbsClAcNUomCd3HyEYCsn1lzshq.webp', 'title' => 'Gaming PC', 'specs' => '24" | Full HD | 75Hz | IPS', 'price' => '€139,00', 'link' => '/product/samsung-monitor', 'in_stock' => true],
                    ['badge' => 'Bestseller', 'badge_color' => 'orange', 'image' => '550x399.jpg', 'title' => 'Scherm', 'specs' => '27 inch', 'price' => '€89,95', 'link' => '/product/samsung-980-pro', 'in_stock' => true],
                    ['badge' => 'Bestseller', 'badge_color' => 'orange', 'image' => '550x399.jpg', 'title' => 'Scherm', 'specs' => '27 inch', 'price' => '€89,95', 'link' => '/product/samsung-980-pro', 'in_stock' => true],
                ],
                'trust' => [
                    ['icon' => 'shield-check', 'title' => 'Veilig winkelen', 'subtitle' => 'Veilige betaling'],
                    ['icon' => 'rotate-ccw', 'title' => '14 dagen bedenktijd', 'subtitle' => 'Niet tevreden? Geld terug'],
                    ['icon' => 'award', 'title' => 'Garantie', 'subtitle' => 'Op geselecteerde producten'],
                    ['icon' => 'truck', 'title' => 'Snelle levering', 'subtitle' => 'Binnen Nederland'],
                ],
            ],

            'footer' => [
                'brand_about' => 'De betrouwbare partner voor al je computerproblemen en IT-oplossingen. Kwaliteit, snelheid en persoonlijke service staan bij ons voorop.',
                'social' => [
                    ['icon' => 'facebook', 'url' => '#'],
                    ['icon' => 'instagram', 'url' => '#'],
                    ['icon' => 'message-circle', 'url' => '#'],
                    ['icon' => 'youtube', 'url' => '#'],
                ],
                'contact' => [
                    ['icon' => 'map-pin', 'label' => 'Adres', 'value' => "Kanaalweg 33\n3526 KL Utrecht"],
                    ['icon' => 'phone', 'label' => 'Telefoon', 'value' => '030 123 45 67'],
                    ['icon' => 'mail', 'label' => 'E-mail', 'value' => 'info@slimme-pc.nl'],
                    ['icon' => 'clock', 'label' => 'Openingstijden', 'value' => "Ma - Vr: 09:00 - 18:00\nZa: 10:00 - 16:00\nZo: Gesloten"],
                ],
                'trust' => [
                    ['icon' => 'shield-check', 'title' => 'Veilig betalen', 'subtitle' => 'Betrouwbare betaalmethodes'],
                    ['icon' => 'lock', 'title' => 'Privacy beschermd', 'subtitle' => 'Jouw gegevens zijn veilig'],
                    ['icon' => 'truck', 'title' => 'Snelle levering', 'subtitle' => 'Snel binnen Nederland'],
                    ['icon' => 'award', 'title' => 'Garantie', 'subtitle' => 'Op geselecteerde producten'],
                ],
                'copyright' => '© 2026 Slimme-PC. Alle rechten voorbehouden.',
                'payments' => [
                    ['label' => 'iDEAL'],
                    ['label' => 'VISA'],
                    ['label' => 'Mastercard'],
                    ['label' => 'PayPal'],
                ],
            ],

            'floating' => [
                'chat_tooltip' => 'Chat met Slimme-PC',
                'chat_url' => '#',
                'whatsapp_tooltip' => 'Stuur ons een WhatsApp',
                'whatsapp_url' => '#',
            ],
        ];

        foreach ($content as $section => $blocks) {
            $sort = 0;

            foreach ($blocks as $key => $value) {
                $isJson = is_array($value);

                ContentBlock::updateOrCreate(
                    ['page' => 'home', 'section' => $section, 'block_key' => $key],
                    [
                        'type' => $isJson ? 'json' : 'text',
                        'value' => $isJson ? null : $value,
                        'json_value' => $isJson ? $value : null,
                        'sort_order' => $sort++,
                    ]
                );
            }
        }

        $tarieven = require database_path('data/tarieven.php');

        foreach ($tarieven as $section => $blocks) {
            $sort = 0;

            foreach ($blocks as $key => $value) {
                $isJson = is_array($value);

                ContentBlock::firstOrCreate(
                    ['page' => 'tarieven', 'section' => $section, 'block_key' => $key],
                    [
                        'type' => $isJson ? 'json' : 'text',
                        'value' => $isJson ? null : $value,
                        'json_value' => $isJson ? $value : null,
                        'sort_order' => $sort++,
                    ]
                );
            }
        }

        $contact = require database_path('data/contact.php');

        foreach ($contact as $section => $blocks) {
            $sort = 0;

            foreach ($blocks as $key => $value) {
                $isJson = is_array($value);

                ContentBlock::firstOrCreate(
                    ['page' => 'contact', 'section' => $section, 'block_key' => $key],
                    [
                        'type' => $isJson ? 'json' : 'text',
                        'value' => $isJson ? null : $value,
                        'json_value' => $isJson ? $value : null,
                        'sort_order' => $sort++,
                    ]
                );
            }
        }

        $overons = require database_path('data/overons.php');

        foreach ($overons as $section => $blocks) {
            $sort = 0;

            foreach ($blocks as $key => $value) {
                $isJson = is_array($value);

                ContentBlock::firstOrCreate(
                    ['page' => 'overons', 'section' => $section, 'block_key' => $key],
                    [
                        'type' => $isJson ? 'json' : 'text',
                        'value' => $isJson ? null : $value,
                        'json_value' => $isJson ? $value : null,
                        'sort_order' => $sort++,
                    ]
                );
            }
        }

        // Service detail pages (CMS-editable). First-or-create so admin edits are preserved on reseed.
        $laptop = [
            'hero' => [
                'badge' => 'Laptop reparatie in Apeldoorn',
                'title1' => 'Laptop kapot?',
                'title2' => 'Slimme-PC',
                'title3' => 'maakt hem weer als nieuw',
                'description' => 'Van scherm tot moederbord: wij repareren je laptop snel en vakkundig in onze werkplaats in Apeldoorn. Eerlijk advies, transparante prijzen en garantie.',
                'image' => 'assets/img/landing/53f89edd-3207-4891-b580-7246605e1858.png',
                'usp' => [
                    ['icon' => 'zap', 'title' => 'Snelle diagnose', 'subtitle' => 'Vaak dezelfde dag'],
                    ['icon' => 'shield-check', 'title' => 'Garantie', 'subtitle' => 'Op de reparatie'],
                    ['icon' => 'badge-euro', 'title' => 'Eerlijke prijs', 'subtitle' => 'Vooraf duidelijk'],
                    ['icon' => 'map-pin', 'title' => 'In Apeldoorn', 'subtitle' => 'Eigen werkplaats'],
                ],
            ],
            'problems' => [
                'title' => 'Wat is er mis met je laptop?',
                'subtitle' => 'Kies je probleem en ontdek hoe wij helpen.',
                'items' => [
                    ['icon' => 'monitor-x', 'title' => 'Geen beeld'],
                    ['icon' => 'alert-triangle', 'title' => 'Blue screen'],
                    ['icon' => 'gauge', 'title' => 'Traag systeem'],
                    ['icon' => 'battery-low', 'title' => 'Batterij leeg'],
                    ['icon' => 'keyboard', 'title' => 'Toetsenbord stuk'],
                    ['icon' => 'tablet', 'title' => 'Scherm gebroken'],
                    ['icon' => 'plug', 'title' => 'Oplader probleem'],
                    ['icon' => 'fan', 'title' => 'Ventilator lawaai'],
                    ['icon' => 'droplet', 'title' => 'Waterschade'],
                    ['icon' => 'bug', 'title' => 'Software fout'],
                    ['icon' => 'wifi', 'title' => 'Geen Wi-Fi'],
                    ['icon' => 'memory-stick', 'title' => 'Geheugen issue'],
                ],
            ],
            'speciality' => [
                'badge' => 'Onze specialiteit',
                'title1' => 'Reparatie op',
                'title2' => 'componentniveau',
                'description' => 'Waar anderen het moederbord vervangen, repareren wij op componentniveau. Schematisch zoeken we de exacte storing en lossen die precies op — vaak stukken goedkoper.',
                'list' => [
                    ['icon' => 'microchip', 'title' => 'Moederbord diagnose'],
                    ['icon' => 'cpu', 'title' => 'Chip-level soldeerwerk'],
                    ['icon' => 'circuit-board', 'title' => 'Schema analyse'],
                    ['icon' => 'test-tube', 'title' => 'Component test'],
                    ['icon' => 'wrench', 'title' => 'Precisie reparatie'],
                ],
                'video' => '',
            ],
            'equipment' => [
                'items' => [
                    ['icon' => 'microscope', 'title' => 'Microscoop', 'subtitle' => 'Fijne inspectie van circuits'],
                    ['icon' => 'flame', 'title' => 'Heetluchtstation', 'subtitle' => 'BGA reflow & soldeerwerk'],
                    ['icon' => 'multimeter', 'title' => 'Multimeter', 'subtitle' => 'Doormeten van spanning'],
                    ['icon' => 'power', 'title' => 'Voedingstester', 'subtitle' => 'Stroom & stabiliteit'],
                ],
            ],
            'example' => [
                'title' => 'Een reparatie van dichtbij',
                'subtitle' => 'Zo lossen we een complexe storing op.',
                'before_image' => '',
                'before_label' => 'Vóór',
                'before_text' => 'Laptop gaf geen beeld en sloot direct na opstart af.',
                'diagnose_image' => '',
                'diagnose_label' => 'Diagnose',
                'diagnose_text' => 'Via schema-analyse een kortsluiting in het voedingscircuit gelokaliseerd.',
                'after_image' => '',
                'after_label' => 'Na reparatie',
                'after_text' => 'Nieuwe condensator geplaatst, volledig getest en werkend afgeleverd.',
                'tested_title' => 'Getest & werkend',
                'tested_text' => 'Alle poorten, scherm en batterij grondig getest voor levering.',
            ],
            'other' => [
                'title' => 'Andere laptop reparaties',
                'items' => [
                    ['image' => '', 'title' => 'Scherm vervanging', 'subtitle' => 'LCD & touchscreen'],
                    ['image' => '', 'title' => 'Toetsenbord', 'subtitle' => 'Mechanisch & flex'],
                    ['image' => '', 'title' => 'Batterij', 'subtitle' => 'Oplaadbaar'],
                    ['image' => '', 'title' => 'Ventilator', 'subtitle' => 'Koeling & stof'],
                    ['image' => '', 'title' => 'Oplader & poort', 'subtitle' => 'Laden en voeding'],
                    ['image' => '', 'title' => 'Waterschade', 'subtitle' => 'Reinigen & drogen'],
                ],
            ],
            'faq' => [
                'title' => 'Veelgestelde vragen',
                'items' => [
                    ['question' => 'Hoe lang duurt een laptop reparatie?', 'answer' => 'De meeste reparaties zijn binnen 1-3 werkdagen klaar, afhankelijk van de onderdelen.'],
                    ['question' => 'Krijg ik garantie op de reparatie?', 'answer' => 'Ja, op onze reparaties zit garantie. We bespreken de voorwaarden vooraf met je.'],
                    ['question' => 'Is mijn data veilig?', 'answer' => 'We gaan zorgvuldig om met je gegevens en maken indien gewenst een backup voor de reparatie.'],
                    ['question' => 'Wat kost een diagnose?', 'answer' => 'De diagnose is transparant geprijsd en wordt verrekend bij een doorgaande reparatie.'],
                    ['question' => 'Kan ik langskomen zonder afspraak?', 'answer' => 'Je bent welkom in onze werkplaats in Apeldoorn; een afspraak versnelt de service.'],
                ],
                'more_url' => '',
                'cta_title' => 'Klaar om je laptop te laten maken?',
                'cta_subtitle' => 'Plan vandaag nog je reparatie in Apeldoorn.',
                'cta_phone' => '085 080 1167',
                'cta_button' => 'Reparatie aanvragen',
                'cta_bg' => '',
            ],
            'bottom' => [
                'items' => [
                    ['icon' => 'zap', 'title' => 'Snelle service', 'subtitle' => 'Vaak dezelfde dag'],
                    ['icon' => 'shield-check', 'title' => 'Garantie', 'subtitle' => 'Op de reparatie'],
                    ['icon' => 'badge-euro', 'title' => 'Eerlijke prijs', 'subtitle' => 'Vooraf duidelijk'],
                    ['icon' => 'user-round', 'title' => 'Persoonlijk', 'subtitle' => 'Advies op maat'],
                    ['icon' => 'map-pin', 'title' => 'Lokaal', 'subtitle' => 'Apeldoorn'],
                ],
            ],
        ];

        foreach ($laptop as $section => $blocks) {
            $sort = 0;
            foreach ($blocks as $key => $value) {
                $isJson = is_array($value);
                ContentBlock::firstOrCreate(
                    ['page' => 'laptopreparatie', 'section' => $section, 'block_key' => $key],
                    [
                        'type' => $isJson ? 'json' : 'text',
                        'value' => $isJson ? null : $value,
                        'json_value' => $isJson ? $value : null,
                        'sort_order' => $sort++,
                    ]
                );
            }
        }

        $design = [
            'meta_title' => 'Slimme-PC — Computerreparatie & IT-service in Apeldoorn',
            'meta_description' => 'Van diagnose tot reparatie: Slimme-PC is jouw betrouwbare partner voor computerreparatie, laptopreparatie, data recovery en IT-service in Apeldoorn.',
        ];

        ContentMeta::updateOrCreate(
            ['meta_key' => 'design'],
            ['meta_value' => json_encode($design)]
        );

        ContentMeta::updateOrCreate(
            ['meta_key' => config('cms.cache_version_key')],
            ['meta_value' => (string) now()->timestamp]
        );

        Cms::bust();
    }
}

