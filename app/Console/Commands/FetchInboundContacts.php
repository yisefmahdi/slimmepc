<?php

namespace App\Console\Commands;

use App\Services\InboundContactFetcher;
use Illuminate\Console\Command;

class FetchInboundContacts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'contact:fetch-inbound
                           {--limit=50 : Maximum number of unseen messages to process per run}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch customer e-mail replies and attach them to their inbox threads';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $result = app(InboundContactFetcher::class)->run((int) $this->option('limit'));

        foreach ($result['errors'] as $error) {
            $this->warn($error);
        }

        $this->line(sprintf(
            'Done. %d of %d messages matched.',
            $result['matched'],
            $result['processed'],
        ));

        return $result['errors'] ? self::FAILURE : self::SUCCESS;
    }
}