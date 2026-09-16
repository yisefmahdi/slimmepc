<?php

namespace Database\Seeders;

use App\Models\ChatFaq;
use Illuminate\Database\Seeder;

class ChatFaqSeeder extends Seeder
{
    public function run(): void
    {
        $faqs = [
            [
                'question' => 'Wat zijn jullie openingstijden?',
                'answer' => 'Wij zijn geopend van maandag tot en met vrijdag van 09:00 tot 17:00 en op zaterdag van 10:00 tot 14:00. Op zondag zijn wij gesloten.',
                'category' => 'Algemeen',
                'sort_order' => 1,
            ],
            [
                'question' => 'Wat kost een laptop reparatie?',
                'answer' => 'De kosten hangen af van het probleem en het onderdeel. Kijk op onze Tarieven-pagina voor indicaties of meld je reparatie aan voor een vrijblijvende diagnose.',
                'category' => 'Prijzen',
                'sort_order' => 2,
            ],
            [
                'question' => 'Hoe kan ik contact met jullie opnemen?',
                'answer' => 'Je kunt ons bellen op 055 203 21 45, mailen naar info@slimme-pc.nl, een WhatsApp sturen of langskomen in Apeldoorn.',
                'category' => 'Contact',
                'sort_order' => 3,
            ],
            [
                'question' => 'Hoe meld ik een reparatie aan?',
                'answer' => 'Via de pagina Reparatie aanmelden op onze website doorloop je 5 korte stappen. Je ontvangt daarna een aanmeldnummer per e-mail.',
                'category' => 'Reparatie',
                'sort_order' => 4,
            ],
            [
                'question' => 'Hoe lang duurt een reparatie?',
                'answer' => 'Veel reparaties voeren wij dezelfde dag uit. Als er een onderdeel besteld moet worden, duurt het iets langer en houden wij je per e-mail op de hoogte.',
                'category' => 'Reparatie',
                'sort_order' => 5,
            ],
            [
                'question' => 'Waarmee kunnen jullie helpen?',
                'answer' => 'Wij helpen met laptop- en computerreparatie, Mac en iPad, verkoop van laptops en onderdelen in onze webshop, software en Windows, netwerk en wifi, moederbord-reparatie en data recovery. Vraag gerust naar prijzen, producten of neem contact op.',
                'category' => 'Algemeen',
                'sort_order' => 0,
            ],
        ];

        foreach ($faqs as $faq) {
            ChatFaq::updateOrCreate(
                ['question' => $faq['question']],
                $faq + ['is_active' => true]
            );
        }
    }
}
