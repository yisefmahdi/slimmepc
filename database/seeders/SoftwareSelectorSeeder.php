<?php

namespace Database\Seeders;

use App\Models\ContentBlock;
use App\Support\Cms;
use Illuminate\Database\Seeder;

class SoftwareSelectorSeeder extends Seeder
{
    /**
     * Seeds the software selector tabs with full detail data (button + panel
     * per tab: key, emoji, title, detail_title, image, image_text, problems).
     * Also removes the obsolete selected_* blocks (replaced by tabs data).
     */
    public function run(): void
    {
        $tabs = [
            ['key' => 'windows', 'emoji' => '▦', 'title' => "Windows &\nSoftware",
             'detail_title' => 'Windows & Software problemen?',
             'image' => 'assets/img/landing/windows-service.jpg',
             'image_text' => "Installatie • Updates • Drivers\nFouten • Trage PC • Software",
             'problems' => [['title' => 'Windows start niet of vastlopers'], ['title' => 'Drivers installeren of bijwerken'], ['title' => 'Blauw scherm of foutmeldingen'], ['title' => "Programma's installeren / verwijderen"], ['title' => 'Trage computer of lange opstarttijd'], ['title' => 'Software werkt niet goed'], ['title' => 'Windows updates problemen'], ['title' => 'Bestanden kwijt of beschadigd']]],
            ['key' => 'printer', 'emoji' => '🖨', 'title' => 'Printer',
             'detail_title' => 'Printerproblemen?',
             'image' => 'assets/img/landing/printer-service.png',
             'image_text' => "Installatie • WiFi • Scannen\nDrivers • Configuratie • Storingen",
             'problems' => [['title' => 'Printer wordt niet gevonden'], ['title' => 'Printer installeren en configureren'], ['title' => 'Printopdrachten blijven hangen'], ['title' => 'Printer verbinden met WiFi'], ['title' => 'Scanner werkt niet'], ['title' => 'Drivers installeren of herstellen'], ['title' => 'Printer offline melding'], ['title' => 'Verbindingsproblemen oplossen']]],
            ['key' => 'wifi', 'emoji' => '◉', 'title' => "Internet &\nWiFi",
             'detail_title' => 'Internet & WiFi problemen?',
             'image' => 'assets/img/landing/router-service.png',
             'image_text' => "WiFi • Router • Modem\nBereik • Snelheid • Verbinding",
             'problems' => [['title' => 'Geen internetverbinding'], ['title' => 'Trage internetverbinding'], ['title' => 'WiFi valt steeds weg'], ['title' => 'Slecht WiFi bereik'], ['title' => 'Router of modem instellen'], ['title' => 'Apparaten verbinden met WiFi'], ['title' => 'WiFi netwerk beveiligen'], ['title' => 'Internet storing onderzoeken']]],
            ['key' => 'network', 'emoji' => '⛓', 'title' => 'Netwerk',
             'detail_title' => 'Netwerkproblemen?',
             'image' => 'assets/img/landing/network-service.png',
             'image_text' => "Netwerk • Bekabeling • Apparaten\nRouter • Delen • Verbinden",
             'problems' => [['title' => 'Thuisnetwerk installeren'], ['title' => 'Computers met elkaar verbinden'], ['title' => 'Netwerkschijven instellen'], ['title' => 'Bekabeld netwerk installeren'], ['title' => 'Netwerkapparaten configureren'], ['title' => 'NAS of gedeelde opslag instellen'], ['title' => 'Netwerkproblemen onderzoeken'], ['title' => 'Draadloze verbinding optimaliseren']]],
            ['key' => 'email', 'emoji' => '✉', 'title' => 'E-mail',
             'detail_title' => 'Problemen met e-mail?',
             'image' => 'assets/img/landing/email-service.png',
             'image_text' => "Outlook • Gmail • Accounts\nSynchronisatie • Verzenden • Ontvangen",
             'problems' => [['title' => 'E-mailaccount instellen'], ['title' => 'E-mail werkt niet meer'], ['title' => 'Kan geen berichten verzenden'], ['title' => 'Kan geen berichten ontvangen'], ['title' => 'Outlook problemen'], ['title' => 'Wachtwoord of account herstellen'], ['title' => 'Synchronisatie problemen'], ['title' => 'E-mail overzetten naar nieuw apparaat']]],
            ['key' => 'cloud', 'emoji' => '☁', 'title' => "Accounts &\nCloud",
             'detail_title' => 'Accounts & Cloud problemen?',
             'image' => 'assets/img/landing/cloud-service.png',
             'image_text' => "Microsoft • Google • OneDrive\nAccounts • Cloud • Synchronisatie",
             'problems' => [['title' => 'Microsoft account problemen'], ['title' => 'Google account instellen'], ['title' => 'OneDrive werkt niet'], ['title' => 'Cloud synchronisatie herstellen'], ['title' => 'Bestanden synchroniseren'], ['title' => 'Account herstellen'], ['title' => 'Cloud opslag instellen'], ['title' => 'Bestanden overzetten']]],
            ['key' => 'security', 'emoji' => '♢', 'title' => 'Beveiliging',
             'detail_title' => 'Computerbeveiliging nodig?',
             'image' => 'assets/img/landing/security-service.png',
             'image_text' => "Malware • Virussen • Privacy\nBeveiliging • Controle • Opschonen",
             'problems' => [['title' => 'Virussen verwijderen'], ['title' => 'Malware verwijderen'], ['title' => "Ongewenste programma's verwijderen"], ['title' => 'Computer beveiligen'], ['title' => 'Browser beveiliging'], ['title' => 'Privacy instellingen controleren'], ['title' => 'Beveiligingssoftware installeren'], ['title' => 'Verdachte meldingen onderzoeken']]],
            ['key' => 'devices', 'emoji' => '⌨', 'title' => 'Randapparatuur',
             'detail_title' => 'Problemen met randapparatuur?',
             'image' => 'assets/img/landing/devices-service.png',
             'image_text' => "Toetsenbord • Muis • Webcam\nMonitor • USB • Bluetooth",
             'problems' => [['title' => 'Toetsenbord werkt niet'], ['title' => 'Muis werkt niet'], ['title' => 'Webcam instellen'], ['title' => 'Monitor aansluiten'], ['title' => 'USB apparaten werken niet'], ['title' => 'Bluetooth problemen'], ['title' => 'Externe schijf aansluiten'], ['title' => 'Randapparatuur installeren']]],
            ['key' => 'other', 'emoji' => '•••', 'title' => "Ander IT-\nprobleem?",
             'detail_title' => 'Staat jouw probleem er niet tussen?',
             'image' => 'assets/img/landing/other-it-service.png',
             'image_text' => "Vertel ons wat er speelt.\nWij zoeken samen naar een oplossing.",
             'problems' => [['title' => 'Onbekende foutmelding'], ['title' => 'Computer werkt niet goed'], ['title' => 'Probleem na een update'], ['title' => 'Apparaat werkt niet zoals verwacht'], ['title' => 'Software of hardware conflict'], ['title' => 'Hulp bij instellingen'], ['title' => 'Technisch advies nodig'], ['title' => 'Ander IT-probleem']]],
        ];

        ContentBlock::updateOrCreate(
            ['page' => 'software', 'section' => 'selector', 'block_key' => 'tabs'],
            ['type' => 'json', 'value' => null, 'json_value' => $tabs, 'sort_order' => 2]
        );

        ContentBlock::where('page', 'software')
            ->where('section', 'selector')
            ->whereIn('block_key', ['selected_title', 'selected_image', 'selected_image_text', 'selected_problems'])
            ->delete();

        Cms::bust();
    }
}
