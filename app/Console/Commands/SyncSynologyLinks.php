<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SyncSynologyLinks extends Command
{
    protected $signature = 'synology:sync-all-links';
    protected $description = 'Deprecated FunkyStyle Synology command';

    public function handle(): int
    {
        $this->info('This feature has been deprecated in Saigonchem Base CMS.');
        return 0;
    }
}
