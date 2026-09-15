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
                'keywords' => 'openingstijden,open,gesloten,dicht,uren,tijd,wanneer,maandag,zaterdag,zondag,hours,open,closed,دوام,مفتوح,مغلق,مسكر,ساعات,وقت,متى,ايام,تفتح,تفتحوا,يفتح,فاتح',
                'category' => 'Algemeen',
                'sort_order' => 1,
            ],
            [
                'question' => 'Wat kost een laptop reparatie?',
                'answer' => 'De kosten hangen af van het probleem en het onderdeel. Kijk op onze Tarieven-pagina voor indicaties of meld je reparatie aan voor een vrijblijvende diagnose.',
                'keywords' => 'prijs,kosten,tarief,hoeveel,laptop,reparatie,price,cost,سعر,اسعار,تكلفة,بكم,لابتوب,تصليح',
                'category' => 'Prijzen',
                'sort_order' => 2,
            ],
            [
                'question' => 'Hoe kan ik contact met jullie opnemen?',
                'answer' => 'Je kunt ons bellen op 055 203 21 45, mailen naar info@slimme-pc.nl, een WhatsApp sturen of langskomen in Apeldoorn.',
                'keywords' => 'contact,bellen,telefoon,nummer,mail,email,whatsapp,adres,apeldoorn,bereiken,تواصل,اتوصل,بتوصل,يتواصل,اتصل,اتصال,رقم,هاتف,تلفون,ايميل,بريد,عنوان,وين,موقعكم,موقع,معكم',
                'category' => 'Contact',
                'sort_order' => 3,
            ],
            [
                'question' => 'Hoe meld ik een reparatie aan?',
                'answer' => 'Via de pagina Reparatie aanmelden op onze website doorloop je 5 korte stappen. Je ontvangt daarna een aanmeldnummer per e-mail.',
                'keywords' => 'aanmelden,reparatie,aanvraag,stappen,formulier,register,تسجيل,طلب,تصليح,كيف,خطوات',
                'category' => 'Reparatie',
                'sort_order' => 4,
            ],
            [
                'question' => 'Hoe lang duurt een reparatie?',
                'answer' => 'Veel reparaties voeren wij dezelfde dag uit. Als er een onderdeel besteld moet worden, duurt het iets langer en houden wij je per e-mail op de hoogte.',
                'keywords' => 'dur,lang,wachten,tijd,snel,dezelfde,dag,long,wait,كم,مدة,طول,وقت,سريع,يوم',
                'category' => 'Reparatie',
                'sort_order' => 5,
            ],
            [
                'question' => 'Waarmee kunnen jullie helpen?',
                'answer' => 'Wij helpen met laptop- en computerreparatie, Mac en iPad, verkoop van laptops en onderdelen in onze webshop, software en Windows, netwerk en wifi, moederbord-reparatie en data recovery. Vraag gerust naar prijzen, producten of neem contact op.',
                'keywords' => 'help,helpen,helpt,waarmee,doen,diensten,services,can you,do you,بتساعد,بتساعدوني,ساعد,تساعد,تساعدوني,خدمات,ممكن,بشو,تقدم,تقدموا',
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
