<?php

namespace App\Services\Chat;

use App\Models\Category;
use App\Support\Cms;
use Illuminate\Support\Facades\Cache;

/**
 * Compacte CMS-context voor de chat (diensten + contact + tarieven + webshop).
 * Alleen publieke gegevens met echte links. Gecachet op CMS-versie.
 */
class ChatCmsContext
{
    /**
     * Compiler-versie: ophogen als compile() verandert (cache-bust).
     */
    protected const CACHE_VERSION = 2;

    public function build(): string
    {
        $version = Cms::version();

        return Cache::remember(
            'chat.cms.ctx.v'.self::CACHE_VERSION.'.'.$version,
            now()->addMonth(),
            fn () => $this->compile()
        );
    }

    protected function compile(): string
    {
        $parts = [];

        try {
            $home = Cms::page('home');
            $services = $home['services']['services'] ?? [];
            if (is_array($services) && $services) {
                $lines = [];
                foreach ($services as $s) {
                    if (! empty($s['hidden']) || empty($s['title'])) {
                        continue;
                    }
                    $lines[] = '- '.trim($s['title']).(isset($s['link']) && $s['link'] ? ' ('.url($s['link']).')' : '');
                }
                if ($lines) {
                    $parts[] = "Diensten van Slimme-PC:\n".implode("\n", $lines);
                }
            }
        } catch (\Throwable $e) {
            // stil: CMS-context is optioneel
        }

        try {
            $contact = Cms::page('contact');
            $methods = $contact['gegevens']['contact_methods'] ?? [];
            $bits = [];
            if (is_array($methods)) {
                foreach ($methods as $m) {
                    $label = trim(($m['label'] ?? '').' '.($m['value'] ?? ''));
                    if ($label !== '') {
                        $bits[] = $label.(isset($m['url']) && $m['url'] ? ' ('.$m['url'].')' : '');
                    }
                }
            }
            $bits[] = 'Reparatie aanmelden: '.url('/reparatie-aanmelden');
            $bits[] = 'Afspraak aan huis: '.url('/afspraak');
            $bits[] = 'Tarieven: '.url('/tarieven').' (indicaties; exacte prijs na diagnose)';

            $loc = $contact['locatie'] ?? [];
            if (is_array($loc)) {
                if (! empty($loc['route_url'])) {
                    $bits[] = 'Route/kaart: '.$loc['route_url'];
                }
                if (is_array($loc['location_items'] ?? null)) {
                    foreach ($loc['location_items'] as $li) {
                        $t = trim(($li['title'] ?? '').' '.($li['text'] ?? ''));
                        if ($t !== '') {
                            $bits[] = $t;
                        }
                    }
                }
            }

            $parts[] = 'Contact & pagina\'s: '.implode(' | ', $bits);
        } catch (\Throwable $e) {
            $parts[] = 'Contact: bel 055 203 21 45 of mail info@slimme-pc.nl. Tarieven: '.url('/tarieven').'. Reparatie aanmelden: '.url('/reparatie-aanmelden').'.';
        }

        try {
            $cats = Category::where('status', true)->orderBy('sort_order')->orderBy('name')->get(['name', 'slug']);
            if ($cats->isNotEmpty()) {
                $parts[] = 'Webshop-categorieën: '.$cats->map(fn ($c) => $c->name.' ('.url('/webshop/'.$c->slug).')')->implode(', ');
            }
        } catch (\Throwable $e) {
            // stil
        }

        return implode("\n\n", $parts);
    }
}
