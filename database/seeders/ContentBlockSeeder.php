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
                'badge' => 'Laptop reparatie Apeldoorn',
                'title1' => 'Laptop kapot?',
                'title2' => 'Wij repareren verder',
                'title3' => 'waar anderen stoppen.',
                'description' => 'Van schermproblemen tot reparaties op componentniveau.' . "\n" . 'Snelle service, scherpe prijzen & vakmanschap.',
                'image' => 'assets/img/landing/85cea032-2e38-4f3d-8071-f8677565c0a3.png',
                'usp' => [
                    ['icon' => 'power', 'title' => 'Gratis diagnose', 'subtitle' => 'Eerlijk en duidelijk'],
                    ['icon' => 'microchip', 'title' => 'Component level repair', 'subtitle' => 'Wij repareren, niet vervangen'],
                    ['icon' => 'flask-conical', 'title' => 'Snelle service', 'subtitle' => 'Vaak dezelfde dag klaar'],
                    ['icon' => 'shield-check', 'title' => 'Garantie', 'subtitle' => 'Op reparaties & onderdelen'],
                ],
            ],
            'problems' => [
                'title' => 'Wat is er',
                'title_highlight' => 'mis',
                'subtitle' => 'met je laptop?',
                'items' => [
                    ['icon' => 'power', 'title' => 'Start niet'],
                    ['icon' => 'monitor-x', 'title' => 'Geen beeld'],
                    ['icon' => 'thermometer', 'title' => 'Wordt heet'],
                    ['icon' => 'battery-warning', 'title' => 'Laadt niet'],
                    ['icon' => 'monitor-x', 'title' => 'Scherm kapot'],
                    ['icon' => 'droplets', 'title' => 'Vloeistofschade'],
                    ['icon' => 'gauge', 'title' => 'Traag'],
                    ['icon' => 'battery-medium', 'title' => 'Batterij probleem'],
                    ['icon' => 'keyboard', 'title' => 'Toetsenbord defect'],
                    ['icon' => 'volume-2', 'title' => 'Geluid / Speakers'],
                    ['icon' => 'hard-drive', 'title' => 'SSD / Opslag'],
                    ['icon' => 'circle-ellipsis', 'title' => 'Anders probleem'],
                ],
            ],
            'speciality' => [
                'badge' => 'Onze specialiteit',
                'title1' => 'Reparatie op',
                'title2' => 'componentniveau',
                'description' => 'Niet meteen het hele moederbord vervangen. Wij onderzoeken het defect en repareren waar mogelijk het beschadigde onderdeel.',
                'list' => [
                    ['icon' => 'circle-check-big', 'title' => 'IC / Chip reparatie'],
                    ['icon' => 'circle-check-big', 'title' => 'Laadcircuit reparatie'],
                    ['icon' => 'circle-check-big', 'title' => 'MOSFET & Power circuits'],
                    ['icon' => 'circle-check-big', 'title' => 'Connectoren & poorten'],
                    ['icon' => 'circle-check-big', 'title' => 'Kortsluiting opsporen & herstellen'],
                ],
                'video' => '',
                'video_poster' => 'assets/img/landing/e4703bd3-ffe8-4ca1-8543-7f5a97484698.png',
            ],
            'equipment' => [
                'items' => [
                    ['icon' => 'microscope', 'title' => 'Professionele apparatuur', 'subtitle' => 'Microscoop, soldeerstation en meetapparatuur'],
                    ['icon' => 'user-round-check', 'title' => 'Ervaren technici', 'subtitle' => 'Jarenlange ervaring in moederbord reparaties'],
                    ['icon' => 'badge-check', 'title' => 'Kwaliteit onderdelen', 'subtitle' => 'Alleen originele of hoogwaardige compatibele onderdelen'],
                    ['icon' => 'shield-check', 'title' => 'Getest & gegarandeerd', 'subtitle' => 'Elke reparatie wordt uitgebreid getest en gegarandeerd'],
                ],
            ],
            'example' => [
                'title' => 'Een reparatie van dichtbij',
                'subtitle' => 'Voorbeeld van een echte reparatie',
                'before_image' => 'assets/img/landing/kO1LIJHDa11tczsJsamOPZfBGTjhLrQnH18u2AZ4.webp',
                'before_label' => 'Voor',
                'before_text' => 'Klacht:' . "\n" . 'Laptop startte niet meer op.' . "\n" . 'Geen reactie, geen beeld.',
                'diagnose_image' => 'assets/img/landing/363f8f55-fba7-4f23-88db-8c8e728d522e.png',
                'diagnose_label' => 'Diagnose',
                'diagnose_text' => 'Diagnose:' . "\n" . 'Defecte laad-IC op het moederbord.' . "\n" . 'Kortsluiting in de laadlijn.',
                'after_image' => 'assets/img/landing/53f89edd-3207-4891-b580-7246605e1858.png',
                'after_label' => 'Na',
                'after_text' => 'Oplossing:' . "\n" . 'Component gerepareerd en vervangen.' . "\n" . 'Laptop werkt weer perfect!',
                'tested_title' => '100% getest',
                'tested_text' => 'Na elke reparatie testen we alle functies van je laptop uitgebreid.',
            ],
            'other' => [
                'title' => 'Ook voor al je',
                'title_highlight' => 'andere laptop reparaties',
                'items' => [
                    ['image' => 'e6CDTwkoydKhv1YqP7v960jbKDJiHFBxVh8og5LH.png', 'title' => 'Scherm reparatie'],
                    ['image' => 'e6cc3cb7-5aea-460d-a1a9-884318edc64a.png', 'title' => 'Batterij vervangen'],
                    ['image' => 'kO1LIJHDa11tczsJsamOPZfBGTjhLrQnH18u2AZ4.webp', 'title' => 'Toetsenbord reparatie'],
                    ['image' => 'LHMFw9TB8j5VOwa8M102upJRC5ZQXl04WjQ6HF9D.png', 'title' => 'Laadpoort reparatie'],
                    ['image' => 'cd15d7f8-c7f8-4d86-aa46-b51b04415092.png', 'title' => 'Oververhitting oplossen'],
                    ['image' => 'b4c74892-bfeb-4b3f-8968-32762aa3af6a.png', 'title' => 'Upgrade & Onderhoud', 'subtitle' => 'SSD, RAM, opschonen'],
                ],
            ],
            'faq' => [
                'title' => 'Veelgestelde vragen',
                'items' => [
                    ['question' => 'Hoe lang duurt een laptop reparatie?', 'answer' => 'Veel reparaties kunnen dezelfde dag worden uitgevoerd. Bij onderdelenbestelling kan dit iets langer duren.'],
                    ['question' => 'Wat zijn de kosten voor diagnose?', 'answer' => 'Wij bespreken vooraf duidelijk de kosten en mogelijkheden voordat een reparatie wordt uitgevoerd.'],
                    ['question' => 'Krijg ik garantie op de reparatie?', 'answer' => 'Ja. Wij geven garantie op uitgevoerde reparaties en de door ons geplaatste onderdelen.'],
                    ['question' => 'Repareren jullie alle laptop merken?', 'answer' => 'Wij repareren vrijwel alle bekende laptopmerken, waaronder HP, Lenovo, Dell, Asus, Acer, MSI en Apple.'],
                ],
                'more_url' => '#',
                'cta_title' => 'Laptop',
                'cta_title2' => 'laten repareren?',
                'cta_subtitle' => 'Meld je reparatie eenvoudig online' . "\n" . 'of kom langs in onze winkel in Apeldoorn.',
                'cta_phone' => '055 203 21 45',
                'cta_bg' => 'assets/img/landing/53f89edd-3207-4891-b580-7246605e1858.png',
            ],
            'bottom' => [
                'items' => [
                    ['icon' => 'shield-check', 'title' => 'Gratis diagnose', 'subtitle' => 'Eerlijk en transparant'],
                    ['icon' => 'calendar-x', 'title' => 'Geen afspraak nodig', 'subtitle' => 'Loop gewoon binnen'],
                    ['icon' => 'gauge', 'title' => 'Snelle service', 'subtitle' => 'Vaak dezelfde dag klaar'],
                    ['icon' => 'flask-conical', 'title' => 'Duidelijke prijzen', 'subtitle' => 'Geen verrassingen achteraf'],
                    ['icon' => 'shield-check', 'title' => 'Garantie op reparaties', 'subtitle' => 'Zekerheid voorop'],
                ],
            ],
        ];

        foreach ($laptop as $section => $blocks) {
            $sort = 0;
            foreach ($blocks as $key => $value) {
                $isJson = is_array($value);
                ContentBlock::updateOrCreate(
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

