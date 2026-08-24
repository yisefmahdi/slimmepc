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
                    ['label' => 'Moederbord reparatie', 'url' => '/diensten/moederbord-reparatie', 'icon' => 'microchip', 'subtitle' => 'Component-level service'],
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
                    ['icon' => 'monitor', 'title' => 'PC Reparatie', 'description' => 'Desktop traag, start niet of hardwareproblemen? Wij lossen het snel op.', 'link' => '/diensten/pc-reparatie', 'image' => 'aad70dda-b34c-4737-881f-eddab9c5b46c.png', 'hidden' => false],
                    ['icon' => 'apple', 'title' => 'MacBook Reparatie', 'description' => 'Professionele reparatie voor MacBook Air, MacBook Pro en andere Apple-apparaten.', 'link' => '/diensten/mac-reparatie', 'image' => '363f8f55-fba7-4f23-88db-8c8e728d522e.png', 'hidden' => false],
                    ['icon' => 'database', 'title' => 'Data Recovery', 'description' => 'Wij herstellen gegevens van beschadigde of niet meer werkende opslagapparaten.', 'link' => '/diensten/data-recovery', 'image' => 'e6cc3cb7-5aea-460d-a1a9-884318edc64a.png', 'hidden' => false],
                    ['icon' => 'arrow-up', 'title' => 'iPad Reparatie', 'description' => 'Scherm, batterij, laadpoort en andere iPad & tablet reparaties.', 'link' => '/diensten/ipad-reparatie', 'image' => 'ipad.png', 'hidden' => false],
                    ['icon' => 'cpu', 'title' => 'Moederbord Reparatie', 'description' => 'Component-level reparatie bij complexe storingen, geen beeld en stroomproblemen.', 'link' => '/diensten/moederbord-reparatie', 'image' => '85cea032-2e38-4f3d-8071-f8677565c0a3.png', 'hidden' => false],
                    ['icon' => 'panels-top-left', 'title' => 'Software & Windows', 'description' => 'Installatie, updates, drivers, optimalisatie en het verwijderen van virussen.', 'link' => '/diensten/software-windows', 'image' => '45e1353e-fb4a-4fec-9e45-1e00df7b86ec.png', 'hidden' => false],
                    ['icon' => 'wifi', 'title' => 'Netwerkoplossingen', 'description' => 'Installatie en onderhoud van stabiele, veilige en efficiënte netwerken.', 'link' => '/diensten/netwerkoplossingen', 'image' => 'cd15d7f8-c7f8-4d86-aa46-b51b04415092.png', 'hidden' => false],
                    ['icon' => 'gamepad-2', 'title' => 'Playstation / Xbox', 'description' => 'PlayStation, Xbox en Nintendo reparatie: HDMI, ventilator, laadpoort en stroomproblemen lossen wij vakkundig op.', 'link' => '/diensten/console-reparatie', 'image' => 'playstation-xbox.png', 'hidden' => false],
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

        $console = [
            'hero' => [
                'badge' => 'Console reparatie · Apeldoorn',
                'title1' => 'PlayStation 5 of Xbox',
                'title2' => 'kapot?',
                'description' => 'Wij brengen je console weer terug in de game.',
                'problem_list' => [
                    ['title' => 'Geen beeld'],
                    ['title' => 'Valt uit'],
                    ['title' => 'Controller problemen'],
                    ['title' => 'HDMI probleem'],
                    ['title' => 'Start niet'],
                    ['title' => 'Software probleem'],
                    ['title' => 'Oververhitting'],
                    ['title' => 'Ventilator lawaai'],
                    ['title' => 'En meer...'],
                ],
                'cta1_text' => 'Console reparatie aanmelden',
                'cta1_url' => '/reparatie-aanmelden',
                'cta2_text' => 'Bekijk problemen',
                'cta2_url' => '#problemen',
                'image' => 'assets/img/landing/playtios2.png',
            ],
            'consoles' => [
                'title' => 'Welke console heb je?',
                'subtitle' => 'Wij repareren alle populaire gaming consoles',
                'items' => [
                    ['name' => 'PlayStation 5', 'image' => 'assets/img/landing/playstation-xbox.png'],
                    ['name' => 'Xbox Series X', 'image' => 'assets/img/landing/Xbox-series-X-wit-digital-only.jpg'],
                    ['name' => 'Xbox Series S', 'image' => 'assets/img/landing/Microsoft-Xbox-Series-X_b562417b-7584-4740-983b-422cb2832fb9.7ef4114105e8cbe37cc4125a3cb125f0.avif'],
                ],
            ],
            'problems' => [
                'title' => 'Wat is er mis met je console?',
                'subtitle' => 'Selecteer het probleem en wij lossen het op',
                'items' => [
                    ['emoji' => '🖥️', 'title' => 'Geen beeld', 'subtitle' => 'Zwart scherm of geen signaal'],
                    ['emoji' => '🌡️', 'title' => 'Wordt heet', 'subtitle' => 'Oververhitting tijdens gamen'],
                    ['emoji' => '⏻', 'title' => 'Valt uit', 'subtitle' => 'Uitval tijdens het spelen'],
                    ['emoji' => '🔌', 'title' => 'Start niet', 'subtitle' => 'Geen reactie bij opstarten'],
                    ['emoji' => '🔊', 'title' => 'Veel geluid', 'subtitle' => 'Ventilator maakt lawaai'],
                    ['emoji' => '🔌', 'title' => 'USB werkt niet', 'subtitle' => 'Controller niet verbonden'],
                    ['emoji' => '🎮', 'title' => 'Controller probleem', 'subtitle' => 'Sticks of knoppen doen het niet'],
                    ['emoji' => '❓', 'title' => 'Anders', 'subtitle' => 'Een ander probleem? Wij kijken mee'],
                ],
            ],
            'werkwijze' => [
                'title' => 'Onze werkwijze',
                'steps' => [
                    ['number' => '1', 'title' => 'Aanmelden', 'description' => 'Meld je console reparatie online of in de winkel aan.'],
                    ['number' => '2', 'title' => 'Diagnose', 'description' => 'Wij stellen gratis de oorzaak vast.'],
                    ['number' => '3', 'title' => 'Reparatie', 'description' => 'Wij repareren je console vakkundig.'],
                    ['number' => '4', 'title' => 'Testen', 'description' => 'Alles wordt grondig getest voor oplevering.'],
                    ['number' => '5', 'title' => 'Klaar!', 'description' => 'Je console is weer speelklaar.'],
                ],
            ],
            'motorkap' => [
                'title' => 'Onder de motorkap',
                'description' => 'Component-level reparatie van je console: wij vervangen niet zomaar, wij repareren het defecte onderdeel. Met professionele apparatuur en ervaren technici.',
                'checklist' => [
                    ['title' => 'HDMI poort vervangen & repareren'],
                    ['title' => 'Koeling & ventilator revisie'],
                    ['title' => 'Stroomvoorziening & laadcircuit'],
                    ['title' => 'Moederbord diagnose op componentniveau'],
                ],
                'image' => 'assets/img/landing/playtios2.png',
                'spots' => [
                    ['title' => 'HDMI PORT', 'description' => 'Defecte poort vervangen of opnieuw solderen.'],
                    ['title' => 'KOELING', 'description' => 'Ventilator en koelpasta vernieuwen.'],
                    ['title' => 'VENTILATOR', 'description' => 'Lawaai of uitval van de koeling oplossen.'],
                    ['title' => 'VOEDING', 'description' => 'Stroomproblemen en kortsluiting herstellen.'],
                    ['title' => 'USB CONNECTOR', 'description' => 'Loszittende of defecte USB-poorten.'],
                ],
            ],
            'voorana' => [
                'title' => 'Voor & na: Professionele reiniging',
                'before_image' => 'assets/img/landing/playtios2.png',
                'before_label' => 'Voor',
                'after_image' => 'assets/img/landing/playtios2.png',
                'after_label' => 'Na',
                'checklist' => [
                    ['title' => 'Volledige demontage & reiniging'],
                    ['title' => 'Koelpasta vernieuwd'],
                    ['title' => 'Ventilator volledig ontstoff'],
                    ['title' => 'Weer stil & koel tijdens gamen'],
                ],
                'hdmi_title' => 'HDMI probleem opgelost',
                'hdmi_steps' => [
                    ['number' => '1', 'title' => 'Defecte HDMI poort verwijderd'],
                    ['number' => '2', 'title' => 'Nieuwe poort geplaatst & gesoldeerd'],
                    ['number' => '3', 'title' => 'Beeld getest op 4K resolutie'],
                    ['number' => '4', 'title' => 'Console weer speelklaar'],
                ],
            ],
            'services' => [
                'title' => 'Onze console services',
                'items' => [
                    ['emoji' => '❄️', 'title' => 'Cooling Service', 'description' => 'Reiniging, nieuwe koelpasta en ventilator revisie.', 'price' => 'Vanaf €55'],
                    ['emoji' => '🔌', 'title' => 'HDMI Repair', 'description' => 'HDMI poort vervangen of opnieuw solderen.', 'price' => 'Vanaf €69'],
                    ['emoji' => '⚡', 'title' => 'Power / No Boot', 'description' => 'Geen stroom of start niet? Stroomcircuit herstellen.', 'price' => 'Vanaf €59'],
                    ['emoji' => '🛠️', 'title' => 'General Check', 'description' => 'Volledige inspectie en diagnose van je console.', 'price' => 'Vanaf €59'],
                ],
            ],
            'garantie' => [
                'title' => 'Garantie op reparaties',
                'description' => 'Wij staan achter ons werk. Elke console reparatie wordt uitgebreid getest en komt met garantie, zodat jij zorgeloos verder kunt gamen.',
                'points' => [
                    ['title' => 'Garantie op uitgevoerde reparaties'],
                    ['title' => 'Alleen hoogwaardige onderdelen'],
                    ['title' => 'Gratis controle na de reparatie'],
                    ['title' => 'Eerlijke prijsopgave vooraf'],
                ],
            ],
        ];

        $mac = [
            'hero' => [
                'badge' => 'MacBook & iMac reparatie · Apeldoorn',
                'title1' => 'Je Mac verdient',
                'title2' => 'meer dan een',
                'title3' => 'snelle fix.',
                'description' => 'Professionele diagnose en reparatie van MacBook en iMac — van scherm en batterij tot complexe moederbordproblemen.',
                'trust' => [
                    ['title' => 'Snelle service'],
                    ['title' => 'Deskundige reparateurs'],
                    ['title' => 'Heldere communicatie'],
                    ['title' => 'Garantie op reparaties'],
                ],
                'image' => 'assets/img/landing/imac-macbook2.png',
            ],
            'devices' => [
                'title' => 'Welke Mac kunnen we voor je repareren?',
                'subtitle' => 'Voor elk model en elke generatie — wij helpen je verder.',
                'items' => [
                    ['name' => 'MacBook', 'image' => 'assets/img/landing/macbook.webp', 'sub1' => 'Air · Pro', 'sub2' => 'Intel & Apple Silicon'],
                    ['name' => 'iMac', 'image' => 'assets/img/landing/imac-2422-m1-3.webp', 'sub1' => '21.5″ · 24″ · 27″', 'sub2' => 'Intel & Apple Silicon'],
                    ['name' => 'Mac mini & overige Macs', 'image' => 'assets/img/landing/mac-mini.jpg', 'sub1' => 'Mac mini', 'sub2' => 'Overige Apple apparaten'],
                ],
            ],
            'problems' => [
                'title' => 'Wat is er mis met je Mac?',
                'subtitle' => 'Klik op een probleem en bekijk wat wij voor je kunnen betekenen.',
                'items' => [
                    ['emoji' => '⏻', 'title' => 'Mac start niet op', 'subtitle' => 'Power / logic board'],
                    ['emoji' => '🔌', 'title' => 'Mac laadt niet', 'subtitle' => 'USB-C / charging'],
                    ['emoji' => '▣', 'title' => 'Scherm defect', 'subtitle' => 'Display / backlight'],
                    ['emoji' => '🔋', 'title' => 'Batterij probleem', 'subtitle' => 'Battery / power'],
                    ['emoji' => '♨', 'title' => 'Wordt warm', 'subtitle' => 'Cooling / vervuiling'],
                    ['emoji' => '💧', 'title' => 'Waterschade', 'subtitle' => 'Liquid damage'],
                    ['emoji' => '●', 'title' => 'macOS problemen', 'subtitle' => 'Software / recovery'],
                    ['emoji' => '▤', 'title' => 'Data nodig?', 'subtitle' => 'Data recovery'],
                ],
                'component_title' => 'Niet alleen onderdelen vervangen.',
                'component_text' => 'Wij doen component-level reparatie: meten, onderzoeken en precies het juiste onderdeel herstellen.',
                'component_items' => [
                    ['title' => 'Logic board repair'],
                    ['title' => 'USB-C / HDMI reparatie'],
                    ['title' => 'Laadproblemen & circuits'],
                    ['title' => 'MacBook & iMac specialist'],
                ],
            ],
            'process' => [
                'title' => 'Zo repareren wij je Mac',
                'subtitle' => 'We zorgen dat je Mac weer perfect werkt.',
                'items' => [
                    ['emoji' => '🔍', 'title' => '1. Nauwkeurig onderzoek', 'description' => 'We onderzoeken zorgvuldig waar het probleem vandaan komt.'],
                    ['emoji' => '▣', 'title' => '2. Advies op maat', 'description' => 'Je ontvangt duidelijk advies over de beste oplossing.'],
                    ['emoji' => '⚙', 'title' => '3. Reparatie', 'description' => 'We repareren indien nodig tot op componentniveau.'],
                    ['emoji' => '✓', 'title' => '4. Testen & opleveren', 'description' => 'Je Mac wordt uitgebreid getest voordat je hem terugkrijgt.'],
                ],
            ],
            'water' => [
                'title' => 'Vloeistofschade?',
                'text' => 'Zet je Mac uit en probeer hem niet opnieuw op te starten. Wij voeren een grondige reiniging en specialistische reparatie uit.',
                'image' => 'assets/img/landing/macbook-moederbord-reparatie-utrecht-1920w.webp',
            ],
            'battery' => [
                'title' => 'Batterij problemen?',
                'items' => [
                    ['title' => 'Snelle ontlading'],
                    ['title' => 'Mac onverwacht uit'],
                    ['title' => 'Batterij opgezwollen'],
                ],
                'image' => 'assets/img/landing/TmDelUWE2PnmwawZ.medium-2.jpeg',
            ],
            'imac' => [
                'title' => 'Ook je iMac is bij ons welkom.',
                'text' => 'Van traag systeem tot geen beeld — wij lossen het probleem vakkundig op.',
                'image' => 'assets/img/landing/imac-2422-m1-3.webp',
                'items' => [
                    ['title' => 'Traag? – SSD of software probleem'],
                    ['title' => 'Start niet? – diagnose'],
                    ['title' => 'Geen beeld? – scherm / hardware'],
                    ['title' => 'Wordt warm? – onderhoud'],
                ],
            ],
            'why' => [
                'badge' => 'Slimme-PC Apeldoorn',
                'title' => 'MacBook & iMac: alles onder één dak',
                'text' => 'Geen onnodige onderdelen vervangen. Eerst onderzoeken, daarna gericht repareren.',
                'items' => [
                    ['title' => 'Specialist in Apple apparaten'],
                    ['title' => 'Reparatie op componentniveau'],
                    ['title' => 'Professionele diagnose'],
                    ['title' => 'Duidelijke prijs vooraf'],
                    ['title' => 'Garantie op reparaties'],
                    ['title' => 'Zorgvuldig omgaan met je data'],
                ],
            ],
            'recent' => [
                'title' => 'Recente Mac reparaties',
                'items' => [
                    ['image' => 'assets/img/landing/macbook.webp', 'title' => 'MacBook Pro – vloeistofschade', 'text' => 'Logic board professioneel hersteld.'],
                    ['image' => 'assets/img/landing/imac-2422-m1-3.webp', 'title' => 'MacBook Air – laadt niet', 'text' => 'Charging circuit gerepareerd', 'text2' => ''],
                    ['image' => 'assets/img/landing/Mac-macbook.png', 'title' => 'iMac – start niet', 'text' => 'Voedingsprobleem onderzocht en opgelost.'],
                ],
            ],
            'faq' => [
                'title' => 'Veelgestelde vragen',
                'subtitle' => 'Praktische informatie over onze Mac reparaties.',
                'items' => [
                    ['question' => 'Hoe lang duurt een Mac reparatie?', 'answer' => 'Dit hangt af van het defect en de benodigde onderdelen. Na de diagnose laten wij weten wat de verwachte reparatietijd is.'],
                    ['question' => 'Repareren jullie ook oudere Macs?', 'answer' => 'Ja. We bekijken eerst technisch en economisch of reparatie nog verstandig is.'],
                    ['question' => 'Kan mijn data behouden blijven?', 'answer' => 'In veel gevallen wel. Bij problemen met opslag of het logic board bekijken we eerst welke mogelijkheden er zijn om je data veilig te behouden.'],
                    ['question' => 'Geven jullie garantie op reparaties?', 'answer' => 'Op uitgevoerde reparaties en vervangen onderdelen geven wij garantie volgens de voorwaarden van de reparatie.'],
                    ['question' => 'Repareren jullie Apple Silicon Macs?', 'answer' => 'Ja. We werken met zowel Intel Macs als nieuwere Apple Silicon modellen.'],
                ],
            ],
            'cta' => [
                'title' => 'Probleem met je Mac?',
                'subtitle' => 'Vervangen is niet altijd nodig.',
                'text' => 'Laat hem eerst professioneel onderzoeken.',
            ],
        ];

        foreach ($mac as $section => $blocks) {
            $sort = 0;
            foreach ($blocks as $key => $value) {
                $isJson = is_array($value);
                ContentBlock::updateOrCreate(
                    ['page' => 'mac', 'section' => $section, 'block_key' => $key],
                    [
                        'type' => $isJson ? 'json' : 'text',
                        'value' => $isJson ? null : $value,
                        'json_value' => $isJson ? $value : null,
                        'sort_order' => $sort++,
                    ]
                );
            }
        }

        foreach ($console as $section => $blocks) {
            $sort = 0;
            foreach ($blocks as $key => $value) {
                $isJson = is_array($value);
                ContentBlock::updateOrCreate(
                    ['page' => 'console', 'section' => $section, 'block_key' => $key],
                    [
                        'type' => $isJson ? 'json' : 'text',
                        'value' => $isJson ? null : $value,
                        'json_value' => $isJson ? $value : null,
                        'sort_order' => $sort++,
                    ]
                );
            }
        }

        $ipad = [
            'hero' => [
                'badge' => 'Tablet & iPad Reparatie · Apeldoorn',
                'title1' => 'Tablet kapot?',
                'title2' => 'Wij maken ’m',
                'title3' => 'weer compleet.',
                'description' => 'Van een gebarsten scherm en batterijproblemen tot laadproblemen en andere defecten.',
                'trust' => [
                    ['title' => 'Professionele reparatie'],
                    ['title' => 'Snel en betrouwbaar'],
                    ['title' => 'Kwaliteitsonderdelen'],
                    ['title' => 'Garantie op reparaties'],
                ],
                'image' => 'assets/img/landing/19f2b394-7583-4c87-9dd9-82cb5a851fd9.png',
            ],
            'problems' => [
                'title' => 'Wat is er kapot?',
                'subtitle' => 'Klik op het probleem en bekijk wat wij voor je kunnen betekenen.',
                'left_items' => [
                    ['emoji' => '▭', 'title' => 'Gebarsten scherm', 'subtitle' => 'Scherm vervangen'],
                    ['emoji' => '▥', 'title' => 'Batterij snel leeg', 'subtitle' => 'Batterij vervangen'],
                    ['emoji' => '⚡', 'title' => 'Laadt niet', 'subtitle' => 'Laadpoort reparatie'],
                    ['emoji' => '◷', 'title' => 'Start niet op', 'subtitle' => 'Moederbord reparatie'],
                ],
                'center_image' => 'assets/img/landing/l00v181zmokewobpvy9j8uw8pw9wnl135377.avif',
                'right_items' => [
                    ['emoji' => '☝', 'title' => 'Touch werkt niet', 'subtitle' => 'Touchscreen problemen'],
                    ['emoji' => '◉', 'title' => 'Camera / geluid', 'subtitle' => 'Camera of luidspreker'],
                    ['emoji' => '▯', 'title' => 'Knoppen defect', 'subtitle' => 'Knoppen vervangen'],
                    ['emoji' => '⚙', 'title' => 'Softwareproblemen', 'subtitle' => 'Systeemfouten & updates'],
                ],
                'cta_title' => 'Tablet laadt niet?',
                'cta_text' => "We controleren de kabel,\nlaadpoort, batterij en het\nlaadcircuit om de oorzaak\nte vinden.",
            ],
            'screen' => [
                'title' => 'Schermreparatie',
                'subtitle' => 'Gebarsten, geen beeld of touchproblemen? Wij vervangen snel en vakkundig je scherm.',
                'before_image' => 'assets/img/landing/blauwe-achtergrond-met-gebroken-glaseffect_53876-147682.avif',
                'before_label' => 'VOOR',
                'after_image' => 'assets/img/landing/Samsung-Galaxy-Tab-S10-FE-Tablet-Grijs-128GB.webp',
                'after_label' => 'NA',
                'benefits' => [
                    ['title' => 'Originele kwaliteit schermen'],
                    ['title' => 'Perfecte touch & helder beeld'],
                    ['title' => 'Professionele montage'],
                    ['title' => 'Garantie op schermreparatie'],
                ],
            ],
            'brands' => [
                'title' => 'Wij repareren verschillende merken en modellen',
                'items' => [
                    ['emoji' => '●', 'title' => 'Apple iPad', 'subtitle' => 'Alle iPad modellen'],
                    ['emoji' => '▯', 'title' => 'Samsung', 'subtitle' => 'Galaxy Tab'],
                    ['emoji' => '▭', 'title' => 'Lenovo', 'subtitle' => 'Lenovo Tab'],
                    ['emoji' => '▦', 'title' => 'Microsoft Surface', 'subtitle' => 'Surface Pro / Go'],
                    ['emoji' => '◉', 'title' => 'Huawei', 'subtitle' => 'Huawei MatePad'],
                    ['emoji' => '•••', 'title' => 'Andere tablets', 'subtitle' => 'Vraag naar jouw model'],
                ],
            ],
            'steps' => [
                'title' => 'Onze reparatie stappen',
                'steps' => [
                    ['number' => '01', 'title' => 'Beschadigd', 'description' => "Je tablet werkt\nniet zoals het hoort."],
                    ['number' => '02', 'title' => 'Reparatie', 'description' => "We onderzoeken en\nrepareren vakkundig."],
                    ['number' => '03', 'title' => 'Klaar', 'description' => "Je tablet is weer\nals nieuw."],
                ],
                'benefits' => [
                    ['title' => 'Gratis diagnose'],
                    ['title' => 'Duidelijk advies vooraf'],
                    ['title' => 'Pas repareren na akkoord'],
                    ['title' => 'Garantie op reparatie'],
                ],
            ],
            'repair' => [
                'repair_title' => 'Repareren of toch vervangen?',
                'repair_subtitle' => 'Vaak is reparatie de beste en voordeligere keuze.',
                'repair_items' => [
                    ['title' => 'Scherm vervangen'],
                    ['title' => 'Batterij vervangen'],
                    ['title' => 'Laadpoort reparatie'],
                    ['title' => 'Kleine defecten oplossen'],
                    ['title' => 'Voordeliger & duurzamer'],
                ],
                'repair_image' => 'assets/img/landing/l00v181zmokewobpvy9j8uw8pw9wnl135377.avif',
                'replace_title' => 'Vervangen',
                'replace_items' => [
                    ['title' => 'Soms niet nodig'],
                    ['title' => 'Hogere kosten'],
                    ['title' => 'Gegevens overzetten'],
                    ['title' => 'Niet altijd de beste keuze'],
                ],
                'replace_image' => 'assets/img/landing/763983dd201b21a191e84072371b2c39884063.webp',
                'advice_title' => "Laat ons eerst bekijken\nwat er defect is.",
                'advice_text' => 'Wij geven eerlijk advies of reparatie loont.',
            ],
            'numbers' => [
                'title' => 'Slimme-PC in cijfers',
                'items' => [
                    ['emoji' => '⚒', 'value' => '10+', 'label' => 'Jaar ervaring'],
                    ['emoji' => '☆', 'value' => '2500+', 'label' => 'Tablets gerepareerd'],
                    ['emoji' => '♢', 'value' => '90 Dagen', 'label' => 'Garantie op reparaties'],
                    ['emoji' => '◷', 'value' => 'Snel', 'label' => 'Meeste reparaties klaar binnen 24–48 uur'],
                ],
            ],
            'faq' => [
                'title' => 'Veelgestelde vragen',
                'items' => [
                    ['question' => 'Hoe lang duurt een tablet reparatie?', 'answer' => 'De meeste reparaties zijn afhankelijk van onderdeel en schade binnen korte tijd uitvoerbaar.'],
                    ['question' => 'Krijg ik garantie op de reparatie?', 'answer' => 'Ja, op uitgevoerde reparaties en gebruikte onderdelen geldt garantie volgens onze voorwaarden.'],
                    ['question' => 'Gaat mijn data verloren?', 'answer' => 'We proberen je gegevens altijd te behouden en bespreken risico\'s vooraf.'],
                    ['question' => 'Welke tablets repareren jullie?', 'answer' => 'We repareren onder andere Apple, Samsung, Lenovo, Microsoft Surface, Huawei en andere merken.'],
                ],
            ],
            'cta' => [
                'title' => 'Geef je tablet een tweede kans.',
                'subtitle' => 'Snel, vakkundig en met garantie gerepareerd.',
                'address_title' => 'Slimme-PC Apeldoorn',
                'address_text' => "Laan van de Mensenrechten 400\n7331 VZ Apeldoorn",
                'image' => 'assets/img/landing/scherm-en-beeldkwaliteit_hero_1751220810.webp',
            ],
        ];

        foreach ($ipad as $section => $blocks) {
            $sort = 0;
            foreach ($blocks as $key => $value) {
                $isJson = is_array($value);
                ContentBlock::updateOrCreate(
                    ['page' => 'ipad', 'section' => $section, 'block_key' => $key],
                    [
                        'type' => $isJson ? 'json' : 'text',
                        'value' => $isJson ? null : $value,
                        'json_value' => $isJson ? $value : null,
                        'sort_order' => $sort++,
                    ]
                );
            }
        }

        $software = [
            'hero' => [
                'badge' => 'IT Hulp & Software · Apeldoorn',
                'title1' => 'Software-probleem?',
                'title2' => 'Wij',
                'title3' => 'lossen',
                'title4' => 'het op.',
                'description' => "Van Windows en software tot printers,\ninternet, e-mail en netwerk.\nEén adres voor al jouw IT-hulp.",
                'bullets' => [
                    ['title' => 'Voor particulier & zakelijk'],
                    ['title' => 'Ervaren & snel geholpen'],
                    ['title' => 'Eerlijk advies, vaste tarieven'],
                    ['title' => 'Remote of bij ons in de winkel'],
                ],
                'image' => 'assets/img/landing/software-hero.png',
            ],
            'selector' => [
                'title' => 'Waar kunnen we je mee helpen?',
                'subtitle' => 'Klik op een categorie of kies het probleem dat je ervaart.',
                'tabs' => [
                    ['emoji' => '▦', 'title' => "Windows &\nSoftware"],
                    ['emoji' => '🖨', 'title' => 'Printer'],
                    ['emoji' => '◉', 'title' => "Internet &\nWiFi"],
                    ['emoji' => '⛓', 'title' => 'Netwerk'],
                    ['emoji' => '✉', 'title' => 'E-mail'],
                    ['emoji' => '☁', 'title' => "Accounts &\nCloud"],
                    ['emoji' => '♢', 'title' => 'Beveiliging'],
                    ['emoji' => '⌨', 'title' => 'Randapparatuur'],
                    ['emoji' => '•••', 'title' => "Ander IT-\nprobleem?"],
                ],
                'selected_title' => 'Windows & Software problemen?',
                'selected_image' => 'assets/img/landing/windows-service.jpg',
                'selected_image_text' => "Installatie • Updates • Drivers\nFouten • Trage PC • Software",
                'selected_problems' => [
                    ['title' => 'Windows start niet of vastlopers'],
                    ['title' => 'Drivers installeren of bijwerken'],
                    ['title' => 'Blauw scherm of foutmeldingen'],
                    ['title' => 'Programma\'s installeren / verwijderen'],
                    ['title' => 'Trage computer of lange opstarttijd'],
                    ['title' => 'Software werkt niet goed'],
                    ['title' => 'Windows updates problemen'],
                    ['title' => 'Bestanden kwijt of beschadigd'],
                ],
            ],
            'services' => [
                'title' => 'Dit kunnen we voor je doen',
                'items' => [
                    ['image' => 'assets/img/landing/windows-service-card.png', 'title' => 'Windows & Software', 'points' => 'Installatie Windows 10 / 11,Updates & optimalisatie,Drivers & programma\'s,Trage PC oplossen'],
                    ['image' => 'assets/img/landing/printer-service.png', 'title' => 'Printerproblemen', 'points' => 'Installatie & configuratie,Printer niet gevonden,Printen / scannen werkt niet,Drivers & WiFi problemen'],
                    ['image' => 'assets/img/landing/router-service.png', 'title' => 'Internet & WiFi', 'points' => 'Geen internet verbinding,Trage of instabiele WiFi,WiFi bereik vergroten,Router / modem hulp'],
                    ['image' => 'assets/img/landing/network-service.png', 'title' => 'Netwerk', 'points' => 'Thuisnetwerk instellen,Apparaten verbinden,Netwerkproblemen oplossen,Bekabeld of draadloos'],
                    ['image' => 'assets/img/landing/email-service.png', 'title' => 'E-mail', 'points' => 'E-mail instellen & herstellen,Verzenden/ontvangen werkt niet,Outlook, Gmail, etc.,Synchronisatie problemen'],
                    ['image' => 'assets/img/landing/security-service.png', 'title' => 'Beveiliging', 'points' => 'Malware & virussen verwijderen,Beveiligingsinstellingen,Ongewenste software,Privacy & veiligheid'],
                ],
            ],
            'steps' => [
                'title' => 'Zo gaan we te werk',
                'steps' => [
                    ['emoji' => '☵', 'title' => '1. Probleem bespreken', 'description' => 'Je vertelt ons wat er speelt. We stellen de juiste vragen.'],
                    ['emoji' => '⌕', 'title' => '2. Diagnose', 'description' => 'We onderzoeken het probleem grondig.'],
                    ['emoji' => '▧', 'title' => '3. Oplossing voorstellen', 'description' => 'Je ontvangt een duidelijke uitleg en een eerlijk advies.'],
                    ['emoji' => '⚙', 'title' => '4. Reparatie & testen', 'description' => 'We lossen het probleem op en testen alles goed.'],
                    ['emoji' => '✓', 'title' => '5. Alles werkt weer', 'description' => 'Je apparaat werkt weer zoals het hoort!'],
                ],
            ],
            'trust' => [
                'items' => [
                    ['emoji' => '◷', 'title' => 'Snelle service', 'subtitle' => "Vaak klaar terwijl je wacht\nof binnen 24 uur"],
                    ['emoji' => '€', 'title' => 'Eerlijke prijzen', 'subtitle' => "Geen verrassingen\nHeldere tarieven vooraf"],
                    ['emoji' => '♢', 'title' => 'Gegarandeerd', 'subtitle' => "Op reparaties &\nsoftware oplossingen"],
                    ['emoji' => '♟', 'title' => 'Persoonlijke hulp', 'subtitle' => "We nemen de tijd\nvoor jouw probleem"],
                ],
            ],
            'final' => [
                'remote_title' => 'Probleem zonder bezoek oplossen?',
                'remote_text' => "Veel software-, e-mail- en Windowsproblemen\nkunnen we veilig op afstand oplossen.",
                'contact_title' => 'Nog vragen? Wij helpen je graag!',
                'contact_subtitle' => 'Bel, WhatsApp of kom langs in onze winkel in Apeldoorn.',
                'contact_phone' => '055 203 21 45',
                'contact_address' => "Laan van de Mensenrechten 400\n7331 VZ Apeldoorn",
                'contact_image' => 'assets/img/landing/slimme-pc-shop.jpg',
                'review_text' => "Mijn printer deed niets meer en internet viel steeds weg.\nBinnen 30 minuten alles opgelost!",
                'review_author' => '– Klant uit Apeldoorn',
            ],
        ];

        foreach ($software as $section => $blocks) {
            $sort = 0;
            foreach ($blocks as $key => $value) {
                $isJson = is_array($value);
                ContentBlock::updateOrCreate(
                    ['page' => 'software', 'section' => $section, 'block_key' => $key],
                    [
                        'type' => $isJson ? 'json' : 'text',
                        'value' => $isJson ? null : $value,
                        'json_value' => $isJson ? $value : null,
                        'sort_order' => $sort++,
                    ]
                );
            }
        }

        $netwerk = [
            'hero' => [
                'badge' => 'NETWERKOPLOSSINGEN · APELDOORN',
                'title1' => 'Sterk netwerk.',
                'title2' => 'Altijd verbonden.',
                'description' => "Betrouwbare netwerken voor thuis en bedrijven.\nVan stabiele WiFi en bekabeling tot complete netwerkinstallaties en beheer.",
                'bullets' => [
                    ['title' => 'Snel & stabiel internet'],
                    ['title' => 'WiFi dekking in elke ruimte'],
                    ['title' => 'Professionele installatie'],
                    ['title' => 'Bekabeld of draadloos'],
                    ['title' => 'Netwerk voor thuis & zakelijk'],
                    ['title' => 'Onderhoud & beheer'],
                ],
                'image' => 'assets/img/landing/e4be1a09-745c-4b20-bd54-d93ecab9442a.png',
            ],
            'trust' => [
                'items' => [
                    ['emoji' => '♢', 'title' => 'Gratis advies', 'subtitle' => 'Vrijblijvend & persoonlijk'],
                    ['emoji' => '♢', 'title' => 'Vakkundige installatie', 'subtitle' => 'Netjes & professioneel'],
                    ['emoji' => '⚭', 'title' => 'Top kwaliteit apparatuur', 'subtitle' => 'Betrouwbare merken'],
                    ['emoji' => '◷', 'title' => 'Nazorg & support', 'subtitle' => 'Wij blijven voor je klaar'],
                ],
            ],
            'solutions' => [
                'title' => 'Waarmee kunnen we je helpen?',
                'subtitle' => 'Kies de oplossing die bij jouw situatie past.',
                'items' => [
                    ['emoji' => '◉', 'title' => 'WiFi oplossingen', 'description' => "Sterke dekking en stabiel\ninternet in elke ruimte."],
                    ['emoji' => '⚯', 'title' => 'Netwerkbekabeling', 'description' => "Netwerkkabels trekken,\npunten aanleggen en\nstructuur verbeteren."],
                    ['emoji' => '▤', 'title' => 'Netwerkapparatuur', 'description' => "Routers, switches,\naccess points en\nfirewalls op maat."],
                    ['emoji' => '♧', 'title' => 'Zakelijk netwerk', 'description' => "Complete netwerkinstallaties\nvoor bedrijven en kantoren."],
                    ['emoji' => '♢', 'title' => 'Netwerk beveiliging', 'description' => "Beveiliging, gastnetwerken\nen toegangsbeheer."],
                    ['emoji' => '⚙', 'title' => 'Netwerkbeheer', 'description' => "Monitoring, onderhoud\nen support voor een\nzorgeloos netwerk."],
                ],
            ],
            'recognize' => [
                'title' => 'Herken je dit?',
                'items' => [
                    ['emoji' => '◉', 'title' => 'WiFi valt weg'],
                    ['emoji' => '◌', 'title' => 'Slecht bereik'],
                    ['emoji' => '↗', 'title' => 'Trage verbinding'],
                    ['emoji' => '◎', 'title' => 'Geen internet'],
                    ['emoji' => '♧', 'title' => 'Netwerk uitbreiden'],
                    ['emoji' => '♙', 'title' => "Nieuw kantoor\naansluiten"],
                ],
            ],
            'home_business' => [
                'home_title' => 'Thuisnetwerk',
                'home_items' => [
                    ['title' => 'WiFi in elke kamer'],
                    ['title' => 'Sneller internet'],
                    ['title' => 'Slimme apparaten verbinden'],
                    ['title' => 'Ouderlijk toezicht'],
                    ['title' => 'Gastnetwerk'],
                ],
                'home_image' => 'assets/img/landing/2b775919-c76612bc-step-2_-install-structured-cabling-(ethernet-backh.jpg',
                'business_title' => 'Zakelijk netwerk',
                'business_items' => [
                    ['title' => 'Stabiele verbindingen'],
                    ['title' => 'Veilig en betrouwbaar'],
                    ['title' => 'Schaalbaar en toekomstproof'],
                    ['title' => "Gast- & medewerkersnetwerk"],
                    ['title' => "WiFi voor gasten gescheiden\nvan bedrijfsnetwerk"],
                    ['title' => "Werkplekken, printers,\nNAS & apparatuur verbinden"],
                    ['title' => 'Centrale beheeropties'],
                ],
                'business_image' => 'assets/img/landing/LAN-Corning1.jpg',
            ],
            'steps' => [
                'title' => 'Onze werkwijze',
                'steps' => [
                    ['emoji' => '☵', 'title' => '1. Advies', 'description' => "We bespreken jouw wensen\nen bekijken de situatie."],
                    ['emoji' => '▧', 'title' => '2. Plan op maat', 'description' => "We maken een voorstel\ndat past bij jouw behoeften."],
                    ['emoji' => '⚒', 'title' => '3. Installatie & configuratie', 'description' => "Vakkundige installatie en\nconfiguratie van alle apparatuur."],
                    ['emoji' => '✓', 'title' => '4. Test & controle', 'description' => "We testen alles grondig\nvoor optimale prestaties."],
                    ['emoji' => '♫', 'title' => '5. Support & beheer', 'description' => "We blijven beschikbaar voor\nonderhoud en support."],
                ],
            ],
            'brands' => [
                'title' => 'Merken waarop we vertrouwen',
                'items' => [
                    ['image' => 'assets/img/landing/brand-ubiquiti.png', 'title' => 'Ubiquiti'],
                    ['image' => 'assets/img/landing/brand-tplink.png', 'title' => 'TP-Link'],
                    ['image' => 'assets/img/landing/brand-mikrotik.png', 'title' => 'MikroTik'],
                    ['image' => 'assets/img/landing/brand-synology.png', 'title' => 'Synology'],
                    ['image' => 'assets/img/landing/brand-netgear.png', 'title' => 'Netgear'],
                ],
            ],
            'final' => [
                'title' => 'Klaar voor een sterker netwerk?',
                'subtitle' => 'Vraag vrijblijvend advies aan en ontdek wat wij voor jou kunnen betekenen.',
                'benefits' => [
                    ['title' => 'Gratis & vrijblijvend advies', 'subtitle' => 'Bij jou thuis of op locatie'],
                    ['title' => 'Snelle service', 'subtitle' => 'Binnen 24–48 uur'],
                    ['title' => 'Altijd bereikbaar', 'subtitle' => 'Ook voor spoedklussen'],
                ],
                'image' => 'assets/img/landing/images.jpeg',
            ],
        ];

        foreach ($netwerk as $section => $blocks) {
            $sort = 0;
            foreach ($blocks as $key => $value) {
                $isJson = is_array($value);
                ContentBlock::updateOrCreate(
                    ['page' => 'netwerk', 'section' => $section, 'block_key' => $key],
                    [
                        'type' => $isJson ? 'json' : 'text',
                        'value' => $isJson ? null : $value,
                        'json_value' => $isJson ? $value : null,
                        'sort_order' => $sort++,
                    ]
                );
            }
        }

        $moederbord = [
            'hero' => [
                'badge' => 'Moederbord reparatie Apeldoorn',
                'title1' => 'Defect moederbord?',
                'title2' => 'Wij repareren verder',
                'title3' => 'waar anderen stoppen.',
                'description' => "Geen onnodige vervanging. Eerst meten.\nDan repareren. Op componentniveau.",
                'usps' => [
                    ['icon' => 'shield-check', 'title' => 'Gratis diagnose', 'subtitle' => 'Eerlijk & duidelijk'],
                    ['icon' => 'microchip', 'title' => 'Component level repair', 'subtitle' => 'Wij vervangen niet het hele bord'],
                    ['icon' => 'flask-conical', 'title' => 'Snelle doorlooptijd', 'subtitle' => 'Vaak dezelfde dag klaar'],
                    ['icon' => 'shield-check', 'title' => 'Garantie', 'subtitle' => 'Op reparaties & onderdelen'],
                ],
                'image' => 'assets/img/landing/fd440a8c-4dba-4e09-86a1-b71edb28ea87.png',
            ],
            'process' => [
                'title1' => 'Van klacht',
                'title2' => 'naar oplossing',
                'description' => "Wij doorlopen een gestructureerd diagnoseproces om het echte\nprobleem te vinden en gericht te repareren.",
                'center_image' => 'assets/img/landing/363f8f55-fba7-4f23-88db-8c8e728d522e.png',
                'items' => [
                    ['icon' => 'laptop', 'title' => 'KLACHT', 'subtitle' => 'Bijv. laptop start niet meer'],
                    ['icon' => 'activity', 'title' => 'METING', 'subtitle' => 'Voeding & signalen worden gemeten'],
                    ['icon' => 'microchip', 'title' => 'COMPONENT', 'subtitle' => 'Defect onderdeel wordt gelokaliseerd'],
                    ['icon' => 'syringe', 'title' => 'REPARATIE', 'subtitle' => 'Microsolderen & vervangen van defecte onderdelen'],
                    ['icon' => 'shield-check', 'title' => 'TEST', 'subtitle' => 'Grondig getest voor 100% zekerheid'],
                ],
            ],
            'workbench' => [
                'title' => 'Echte reparatie.',
                'highlight' => 'vakwerk.',
                'description' => "Onder de microscoop zoeken we naar de oorzaak\nen repareren we op componentniveau.",
                'features' => [
                    ['icon' => 'microscope', 'title' => 'Ervaren technici'],
                    ['icon' => 'settings', 'title' => 'Professionele apparatuur'],
                    ['icon' => 'shield-check', 'title' => 'Nauwkeurig & veilig'],
                ],
                'video' => null,
                'video_poster' => 'assets/img/landing/e4703bd3-ffe8-4ca1-8543-7f5a97484698.png',
                'lab_items' => [
                    ['title' => 'Microscoop inspectie'],
                    ['title' => 'Soldeerstation (JBC)'],
                    ['title' => 'DC Power Supply'],
                    ['title' => 'Digitale Multimeter'],
                    ['title' => 'Oscilloscoop'],
                    ['title' => 'ESD veilige werkplek'],
                ],
            ],
            'repairs' => [
                'title' => 'Wat repareren wij op een moederbord?',
                'subtitle' => 'Wij repareren alleen het defecte onderdeel, niet onnodig het hele moederbord.',
                'items' => [
                    ['icon' => 'zap', 'title' => 'Voeding (MOSFET / IC)', 'description' => 'Defecte voedingen worden opgespoord en vervangen.', 'image' => 'assets/img/landing/what-computer-chips-made-of.jpg'],
                    ['icon' => 'plug', 'title' => 'Laadcircuit (DC / USB-C)', 'description' => 'Reparatie van laadpoort, laad-IC en power circuits.', 'image' => 'assets/img/landing/what-computer-chips-made-of.jpg'],
                    ['icon' => 'cpu', 'title' => 'BIOS / Firmware', 'description' => 'BIOS problemen, corrupte chip of herprogrammeren.', 'image' => 'assets/img/landing/what-computer-chips-made-of.jpg'],
                    ['icon' => 'laptop-minimal', 'title' => 'Connectoren & Poorten', 'description' => 'HDMI, USB, audio, DC-jack en andere connectoren.', 'image' => 'assets/img/landing/what-computer-chips-made-of.jpg'],
                    ['icon' => 'activity', 'title' => 'Kortsluiting Opsporen', 'description' => 'Short circuit detectie en reparatie op componentniveau.', 'image' => 'assets/img/landing/what-computer-chips-made-of.jpg'],
                    ['icon' => 'brain-circuit', 'title' => 'Component Vervangen', 'description' => 'IC, capacitors, resistors, coil, transistor en meer.', 'image' => 'assets/img/landing/what-computer-chips-made-of.jpg'],
                ],
            ],
            'compare' => [
                'left_title' => 'Het hele moederbord vervangen?',
                'left_items' => [
                    ['title' => 'Zeer hoge kosten'],
                    ['title' => 'Gegevens kunnen verloren gaan'],
                    ['title' => 'Niet altijd direct beschikbaar'],
                    ['title' => 'Niet altijd de echte oplossing'],
                ],
                'right_title' => 'Slimme-PC repareert op componentniveau',
                'right_items' => [
                    ['title' => 'Alleen het defecte onderdeel vervangen'],
                    ['title' => 'Lagere kosten'],
                    ['title' => 'Gegevens blijven behouden'],
                    ['title' => 'Duurzame oplossing'],
                ],
            ],
            'cases' => [
                'title' => 'Echte moederbord reparaties bij Slimme-PC',
                'subtitle' => 'Voorbeelden van succesvolle component level reparaties.',
                'items' => [
                    ['badge' => 'CASE 01', 'title' => 'Laptop laadt niet', 'defect' => 'Defect: Charging IC', 'solution' => 'Oplossing: IC vervangen', 'image' => 'assets/img/landing/kO1LIJHDa11tczsJsamOPZfBGTjhLrQnH18u2AZ4.webp'],
                    ['badge' => 'CASE 02', 'title' => 'Geen beeld', 'defect' => 'Defect: BIOS probleem', 'solution' => 'Oplossing: BIOS geprogrammeerd', 'image' => 'assets/img/landing/e6CDTwkoydKhv1YqP7v960jbKDJiHFBxVh8og5LH.png'],
                    ['badge' => 'CASE 03', 'title' => 'Dood moederbord', 'defect' => 'Kortsluiting in power circuit', 'solution' => 'Oplossing: MOSFET vervangen', 'image' => 'assets/img/landing/589e8caa-b215-46bc-8687-99fbcae79b5a.png'],
                ],
            ],
            'faq' => [
                'title' => 'Veelgestelde vragen',
                'items' => [
                    ['question' => 'Kan elk moederbord gerepareerd worden?', 'answer' => 'Niet elk defect kan worden hersteld, maar veel problemen op componentniveau zijn wel degelijk te repareren.'],
                    ['question' => 'Hoe lang duurt een moederbord reparatie?', 'answer' => 'Dit hangt af van het defect en de beschikbaarheid van componenten.'],
                    ['question' => 'Wat kost een moederbord reparatie?', 'answer' => 'De kosten zijn afhankelijk van de diagnose en het beschadigde circuit of component.'],
                    ['question' => 'Is de reparatie betrouwbaar?', 'answer' => 'Na de reparatie testen wij het apparaat uitgebreid voordat het wordt teruggegeven.'],
                ],
            ],
            'cta' => [
                'title1' => 'Moederbord defect betekent',
                'title2' => 'niet automatisch einde laptop.',
                'description' => 'Laat ons eerst onderzoeken wat er werkelijk defect is.',
                'phone' => '055 203 21 45',
                'image' => 'assets/img/landing/kO1LIJHDa11tczsJsamOPZfBGTjhLrQnH18u2AZ4.webp',
            ],
            'benefits' => [
                'items' => [
                    ['icon' => 'shield-check', 'title' => 'Gratis diagnose', 'subtitle' => 'Eerlijk & transparant'],
                    ['icon' => 'microchip', 'title' => 'Component level repair', 'subtitle' => 'Niet vervangen, maar repareren'],
                    ['icon' => 'gauge', 'title' => 'Snelle service', 'subtitle' => 'Vaak dezelfde dag klaar'],
                    ['icon' => 'shield', 'title' => 'Garantie op reparaties', 'subtitle' => 'Op onderdelen & arbeid'],
                    ['icon' => 'lock-keyhole', 'title' => 'Veilig & betrouwbaar', 'subtitle' => 'ESD veilig & professioneel'],
                ],
            ],
        ];

        foreach ($moederbord as $section => $blocks) {
            $sort = 0;
            foreach ($blocks as $key => $value) {
                $isJson = is_array($value);
                ContentBlock::updateOrCreate(
                    ['page' => 'moederbord', 'section' => $section, 'block_key' => $key],
                    [
                        'type' => $isJson ? 'json' : 'text',
                        'value' => $isJson ? null : $value,
                        'json_value' => $isJson ? $value : null,
                        'sort_order' => $sort++,
                    ]
                );
            }
        }

        $datarecovery = [
            'hero' => [
                'badge' => 'Data recovery Apeldoorn',
                'title1' => 'Belangrijke bestanden',
                'title2' => 'kwijt?',
                'subtitle' => 'Geef je data nog niet op.',
                'description' => "Wij herstellen gegevens van beschadigde HDD's, SSD's,\nUSB-sticks, geheugenkaarten en meer.",
                'usps' => [
                    ['icon' => 'clipboard-check', 'title' => 'Gratis diagnose', 'subtitle' => 'Zonder verplichting'],
                    ['icon' => 'badge-check', 'title' => 'Hoge slagingskans', 'subtitle' => 'Geavanceerde technieken'],
                    ['icon' => 'shield-check', 'title' => 'Vertrouwelijk behandeld', 'subtitle' => 'Jouw data blijft privé'],
                    ['icon' => 'clock-3', 'title' => 'Snelle doorlooptijd', 'subtitle' => 'Vaak binnen 2–5 dagen'],
                ],
                'hero_image' => 'assets/img/landing/c2bf5922-aa0e-445e-a81a-b4f31a4822da.png',
                'media' => [
                    ['icon' => 'file-text', 'title' => 'Documenten', 'subtitle' => 'Word, Excel, PDF...'],
                    ['icon' => 'image', 'title' => "Foto's", 'subtitle' => 'JPG, PNG, RAW...'],
                    ['icon' => 'video', 'title' => "Video's", 'subtitle' => 'MP4, MOV, AVI...'],
                    ['icon' => 'cloud', 'title' => 'Back-ups', 'subtitle' => 'Belangrijke kopieën'],
                ],
            ],
            'devices' => [
                'title1' => 'Waar staan je',
                'title2' => 'bestanden',
                'title3' => 'op?',
                'subtitle' => 'Selecteer het type opslagapparaat',
                'items' => [
                    ['image' => 'assets/img/landing/hdd.jpeg', 'title' => 'HDD', 'subtitle' => 'Interne harde schijf'],
                    ['image' => 'assets/img/landing/SSD-hard.jpg', 'title' => 'SSD', 'subtitle' => 'Solid State Drive'],
                    ['image' => 'assets/img/landing/group_1477_group.jpeg', 'title' => 'USB-Stick', 'subtitle' => 'Geheugenstick'],
                    ['image' => 'assets/img/landing/micro-sd-kaart.jpg', 'title' => 'SD / MicroSD', 'subtitle' => 'Geheugenkaart'],
                    ['image' => 'assets/img/landing/external-hard-drive.webp', 'title' => 'Externe HDD', 'subtitle' => 'Externe harde schijf'],
                    ['image' => 'assets/img/landing/windows-apple-.jpg', 'title' => 'Laptop / PC', 'subtitle' => 'Systeem problemen'],
                ],
            ],
            'process' => [
                'title1' => 'Van beschadiging naar',
                'title2' => 'herstel',
                'subtitle' => 'Zo werken wij aan het terughalen van jouw data',
                'steps' => [
                    ['icon' => 'stethoscope', 'title' => 'Diagnose', 'description' => 'We onderzoeken gratis de schade en de haalbaarheid.'],
                    ['icon' => 'scan-search', 'title' => 'Analyse', 'description' => 'We scannen de schijf op leesbare gegevens.'],
                    ['icon' => 'folder-open', 'title' => 'Herstel', 'description' => 'Bestanden worden veilig gekopieerd naar nieuwe opslag.'],
                    ['icon' => 'shield-check', 'title' => 'Controle', 'description' => 'We controleren de bestanden samen met jou.'],
                    ['icon' => 'cloud-download', 'title' => 'Terug naar jou', 'description' => 'Je ontvangt jouw data veilig terug.'],
                ],
            ],
            'recover' => [
                'title1' => 'Wat kunnen wij',
                'title2' => 'herstellen?',
                'subtitle' => 'Wij werken met bijna alle opslagmedia en bestandssystemen.',
                'items' => [
                    ['icon' => 'hard-drive', 'title' => 'HDD (SATA / IDE)', 'subtitle' => 'Alle merken'],
                    ['icon' => 'cpu', 'title' => 'SSD (SATA / NVMe)', 'subtitle' => 'Alle types'],
                    ['icon' => 'usb', 'title' => 'USB-Sticks', 'subtitle' => 'Alle capaciteiten'],
                    ['icon' => 'sd-card', 'title' => 'SD / MicroSD', 'subtitle' => 'Camera, telefoon'],
                    ['icon' => 'server', 'title' => 'NAS / RAID', 'subtitle' => 'RAID 0/1/5/6/10'],
                    ['icon' => 'briefcase', 'title' => 'Externe schijven', 'subtitle' => 'Alle formaten'],
                    ['icon' => 'folder-cog', 'title' => 'Bestandssystemen', 'subtitle' => 'NTFS, exFAT, FAT32, HFS+, APFS'],
                ],
            ],
            'cases' => [
                'title1' => 'Echte data recovery',
                'title2' => 'cases',
                'subtitle' => 'Enkele voorbeelden van succesvolle herstelde gegevens.',
                'items' => [
                    ['badge' => 'CASE #1021', 'title' => 'HDD maakt geluid', 'description' => 'Defecte harde schijf met mechanische schade.', 'result' => 'Hersteld: 1.2 TB', 'image' => 'assets/img/landing/hdd-2.avif'],
                    ['badge' => 'CASE #0987', 'title' => 'SSD wordt niet herkend', 'description' => 'SSD met controller probleem, geen toegang tot data.', 'result' => 'Hersteld: 480 GB', 'image' => 'assets/img/landing/Externe-schijf-wordt-niet-herkend-door-Windows.webp'],
                    ['badge' => 'CASE #0954', 'title' => 'USB per ongeluk geformatteerd', 'description' => "Belangrijke documenten en foto's teruggehaald.", 'result' => 'Hersteld: 64 GB', 'image' => 'assets/img/landing/group_1477_group.jpeg'],
                ],
            ],
            'trust_cta_faq' => [
                'trust_title' => 'Jouw data is bij ons veilig',
                'trust_items' => [
                    ['title' => 'Geen data wordt zonder toestemming gedeeld'],
                    ['title' => 'We werken op een beveiligde werkplek'],
                    ['title' => 'Jouw data blijft uitsluitend van jou'],
                    ['title' => 'Indien niet herstelbaar: geen kosten'],
                ],
                'cta_title1' => 'Laat je data herstellen',
                'cta_title2' => 'door specialisten.',
                'cta_description' => "Wacht niet langer en vergroot de kans\nop succesvol herstel.",
                'cta_image' => 'assets/img/landing/e6cc3cb7-5aea-460d-a1a9-884318edc64a.png',
                'faq_title' => 'Veelgestelde vragen',
                'faq_items' => [
                    ['question' => 'Wat kost een data recovery?', 'answer' => 'De kosten hangen af van het type opslagmedium en de schade.'],
                    ['question' => 'Hoe lang duurt het herstelproces?', 'answer' => 'Veel onderzoeken en herstellingen duren enkele werkdagen.'],
                    ['question' => 'Welke schijven kunnen jullie herstellen?', 'answer' => 'HDD, SSD, USB, SD-kaarten, externe schijven en diverse RAID-systemen.'],
                    ['question' => 'Is mijn data veilig bij jullie?', 'answer' => 'Wij behandelen alle ontvangen gegevens vertrouwelijk.'],
                    ['question' => 'Wat als de data niet herstelbaar is?', 'answer' => 'Na onderzoek bespreken wij duidelijk de haalbaarheid en mogelijkheden.'],
                ],
            ],
            'benefits' => [
                'items' => [
                    ['icon' => 'shield-check', 'title' => 'Gratis diagnose', 'subtitle' => 'Zonder verplichting'],
                    ['icon' => 'atom', 'title' => 'Geavanceerde technieken', 'subtitle' => 'Professionele tools'],
                    ['icon' => 'badge-check', 'title' => 'Hoge slagingskans', 'subtitle' => 'Jarenlange ervaring'],
                    ['icon' => 'clock', 'title' => 'Snelle doorlooptijd', 'subtitle' => 'Vaak binnen 2–5 dagen'],
                    ['icon' => 'shield', 'title' => 'Geen herstel, geen kosten', 'subtitle' => 'Eerlijk & transparant'],
                ],
            ],
        ];

        foreach ($datarecovery as $section => $blocks) {
            $sort = 0;
            foreach ($blocks as $key => $value) {
                $isJson = is_array($value);
                ContentBlock::updateOrCreate(
                    ['page' => 'datarecovery', 'section' => $section, 'block_key' => $key],
                    [
                        'type' => $isJson ? 'json' : 'text',
                        'value' => $isJson ? null : $value,
                        'json_value' => $isJson ? $value : null,
                        'sort_order' => $sort++,
                    ]
                );
            }
        }

        $pc = [
            'hero' => [
                'badge' => 'PC Reparatie & PC op maat · Apeldoorn',
                'title1' => 'Problemen met je PC?',
                'title2' => 'Of tijd voor iets beters?',
                'description' => "Van reparatie en upgrades tot een complete PC op maat.\nKwaliteit, snelheid en eerlijk advies.",
                'bullets' => [
                    ['title' => 'Diagnose & reparatie'],
                    ['title' => 'Zakelijke computers'],
                    ['title' => 'Upgrades'],
                    ['title' => 'PC op maat'],
                    ['title' => "Gaming PC's"],
                    ['title' => 'Professionele montage'],
                ],
                'image' => 'assets/img/landing/0bdab181-585e-44c9-a56e-11cc49cff612.png',
            ],
            'benefits' => [
                'items' => [
                    ['emoji' => '⌁', 'title' => 'Gratis diagnose', 'subtitle' => 'Eerlijk & transparant'],
                    ['emoji' => '◷', 'title' => 'Snelle service', 'subtitle' => 'Vaak binnen 24 uur'],
                    ['emoji' => '♢', 'title' => 'Garantie', 'subtitle' => 'Op reparaties & onderdelen'],
                    ['emoji' => '☆', 'title' => 'Betrouwbaar', 'subtitle' => 'Jarenlange ervaring'],
                ],
            ],
            'help' => [
                'repair_title' => 'Mijn PC is kapot',
                'repair_items' => [
                    ['title' => 'Start niet'],
                    ['title' => 'Valt uit'],
                    ['title' => 'Geen beeld'],
                    ['title' => 'Maakt geluid'],
                    ['title' => 'Wordt te warm'],
                    ['title' => 'Overige problemen'],
                ],
                'repair_image' => 'assets/img/landing/0bdab181-585e-44c9-a56e-11cc49cff612.png',
                'custom_title' => 'Ik wil een PC',
                'custom_items' => [
                    ['title' => 'Gaming PC'],
                    ['title' => 'Custom build'],
                    ['title' => 'Werk / kantoor'],
                    ['title' => 'Upgrade bestaande PC'],
                    ['title' => 'Foto & video'],
                    ['title' => 'Advies op maat'],
                ],
                'custom_image' => 'assets/img/landing/0bdab181-585e-44c9-a56e-11cc49cff612.png',
            ],
            'choice' => [
                'title1' => 'Jouw PC,',
                'title2' => 'jouw keuze',
                'subtitle' => 'Klik op een onderdeel voor meer informatie',
                'center_image' => 'assets/img/landing/0bdab181-585e-44c9-a56e-11cc49cff612.png',
                'left_items' => [
                    ['emoji' => '▧', 'title' => 'Processor (CPU)', 'description' => "Het brein van je PC.\nMeer kernen, meer kracht."],
                    ['emoji' => '▥', 'title' => 'Werkgeheugen (RAM)', 'description' => "Meer RAM = soepeler multitasken\nen snelheid."],
                    ['emoji' => '▱', 'title' => 'Opslag (SSD / HDD)', 'description' => "Snelle SSD voor snelheid,\nHDD voor capaciteit."],
                ],
                'right_items' => [
                    ['emoji' => '▦', 'title' => 'Moederbord', 'description' => "Verbindt alles met elkaar.\nKies kwaliteit & stabiliteit."],
                    ['emoji' => '⌁', 'title' => 'Grafische kaart (GPU)', 'description' => "Voor gaming, 3D en zware\ntoepassingen."],
                    ['emoji' => '⬡', 'title' => 'Voeding (PSU)', 'description' => "Stabiele stroom voor een\nveilige en betrouwbare PC."],
                ],
                'cooling_title' => 'Koeling',
                'cooling_text' => "Houdt je PC koel en stil.\nBetere prestaties, langere levensduur.",
            ],
            'problems' => [
                'title1' => 'PC probleem?',
                'title2' => 'Wij vinden de oplossing.',
                'description' => "Van kleine storingen tot complexe problemen,\nwij sporen het op en lossen het vakkundig op.",
                'items' => [
                    ['emoji' => '⏻', 'title' => 'Power', 'points' => 'Geen stroom,Start en valt uit,Ventilatoren draaien niet'],
                    ['emoji' => '▣', 'title' => 'Display', 'points' => 'Geen beeld,Blue screen / errors,GPU problemen'],
                    ['emoji' => '◴', 'title' => 'Performance', 'points' => 'Traag / haperingen,Oververhitting,Crashes / vastlopers'],
                ],
                'image' => 'assets/img/landing/0bdab181-585e-44c9-a56e-11cc49cff612.png',
            ],
            'upgrades' => [
                'title1' => 'Niet altijd een',
                'title2' => 'nieuwe PC nodig.',
                'description' => "Met de juiste upgrade geef je jouw PC\neen tweede leven en haal je weer het maximale eruit.",
                'items' => [
                    ['title' => 'Opslag upgrade', 'before_label' => 'HDD 1TB', 'before_image' => 'assets/img/landing/pc/hdd.png', 'before_spec' => '100 MB/s', 'after_label' => 'NVMe SSD 1TB', 'after_image' => 'assets/img/landing/pc/nvme.png', 'after_spec' => '3500 MB/s'],
                    ['title' => 'Geheugen upgrade', 'before_label' => '8GB RAM', 'before_image' => 'assets/img/landing/0bdab181-585e-44c9-a56e-11cc49cff612.png', 'before_spec' => '', 'after_label' => '32GB RAM', 'after_image' => 'assets/img/landing/0bdab181-585e-44c9-a56e-11cc49cff612.png', 'after_spec' => ''],
                    ['title' => 'Koeling upgrade', 'before_label' => 'Standaard koeler', 'before_image' => 'assets/img/landing/0bdab181-585e-44c9-a56e-11cc49cff612.png', 'before_spec' => '', 'after_label' => 'Premium koeler', 'after_image' => 'assets/img/landing/0bdab181-585e-44c9-a56e-11cc49cff612.png', 'after_spec' => ''],
                    ['title' => 'Grafische kaart upgrade', 'before_label' => 'GTX 1650', 'before_image' => 'assets/img/landing/0bdab181-585e-44c9-a56e-11cc49cff612.png', 'before_spec' => '', 'after_label' => 'RTX 4060', 'after_image' => 'assets/img/landing/0bdab181-585e-44c9-a56e-11cc49cff612.png', 'after_spec' => ''],
                ],
            ],
            'builds' => [
                'title1' => 'PC builds door',
                'title2' => 'Slimme-PC',
                'subtitle' => 'Met zorg samengesteld. Op maat gebouwd. 100% getest.',
                'items' => [
                    ['badge' => 'Gaming Beast', 'title' => 'Voor de beste game-ervaring', 'description' => 'High FPS • Stil • Krachtig', 'image' => 'assets/img/landing/0bdab181-585e-44c9-a56e-11cc49cff612.png'],
                    ['badge' => 'Creator Pro', 'title' => 'Voor foto, video & 3D', 'description' => 'Stabiel • Snel • Betrouwbaar', 'image' => 'assets/img/landing/0bdab181-585e-44c9-a56e-11cc49cff612.png'],
                    ['badge' => 'Workstation', 'title' => 'Voor werk & productiviteit', 'description' => 'Efficiënt • Uitbreidbaar • Stil', 'image' => 'assets/img/landing/0bdab181-585e-44c9-a56e-11cc49cff612.png'],
                    ['badge' => 'Office PC', 'title' => 'Voor kantoor & dagelijks gebruik', 'description' => 'Snel • Betrouwbaar • Voordelig', 'image' => 'assets/img/landing/0bdab181-585e-44c9-a56e-11cc49cff612.png'],
                ],
            ],
            'why' => [
                'items' => [
                    ['emoji' => '◉', 'title' => 'Prestaties op maat', 'description' => 'Alleen wat je nodig hebt, geen onnodige kosten.'],
                    ['emoji' => '✥', 'title' => 'Betere kwaliteit', 'description' => 'Hoogwaardige onderdelen van topmerken.'],
                    ['emoji' => '⌘', 'title' => 'Uitbreidbaar', 'description' => 'Makkelijk te upgraden in de toekomst.'],
                    ['emoji' => '♢', 'title' => 'Professioneel gebouwd', 'description' => 'Netjes kabelmanagement en optimale koeling.'],
                    ['emoji' => '◌', 'title' => '100% getest', 'description' => 'We testen de PC uitgebreid voor levering.'],
                    ['emoji' => '♙', 'title' => 'Persoonlijk advies', 'description' => 'Wij denken met je mee voor het beste resultaat.'],
                ],
            ],
            'faq_cta' => [
                'faq_title' => 'Veelgestelde vragen',
                'faq_items' => [
                    ['question' => 'Hoe lang duurt een PC reparatie?', 'answer' => 'De meeste reparaties worden binnen één tot enkele werkdagen uitgevoerd.'],
                    ['question' => 'Wat kost een diagnose?', 'answer' => 'We bekijken eerst het probleem en bespreken daarna duidelijk de mogelijkheden.'],
                    ['question' => 'Bouwen jullie ook gaming PC\'s?', 'answer' => 'Ja. We bouwen gaming PC\'s volledig afgestemd op jouw games en budget.'],
                    ['question' => 'Kan ik mijn eigen onderdelen aanleveren?', 'answer' => 'Dat kan in overleg. We controleren vooraf de compatibiliteit.'],
                ],
                'cta_title' => 'Klaar voor een betere PC?',
                'cta_description' => "Laat je PC repareren of stel jouw ideale PC samen.\nWij helpen je graag verder!",
                'cta_image' => 'assets/img/landing/0bdab181-585e-44c9-a56e-11cc49cff612.png',
            ],
        ];

        foreach ($pc as $section => $blocks) {
            $sort = 0;
            foreach ($blocks as $key => $value) {
                $isJson = is_array($value);
                ContentBlock::updateOrCreate(
                    ['page' => 'pcreparatie', 'section' => $section, 'block_key' => $key],
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

