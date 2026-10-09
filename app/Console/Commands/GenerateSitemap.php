<?php

namespace App\Console\Commands;

use App\Support\SitemapBuilder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class GenerateSitemap extends Command
{
    protected $signature = 'sitemap:generate';

    protected $description = 'Genereer public/sitemap.xml voor Google (statische pagina\'s + diensten + webshop)';

    public function handle(): int
    {
        $urls = SitemapBuilder::urls();
        $xml = SitemapBuilder::xml($urls);

        $path = public_path('sitemap.xml');
        file_put_contents($path, $xml);

        // Houd de dynamische /sitemap.xml-route in sync.
        Cache::put('sitemap.xml', $xml, now()->addDay());

        $this->info('Sitemap gegenereerd: '.$path.' ('.count($urls).' URL\'s)');

        return self::SUCCESS;
    }
}
