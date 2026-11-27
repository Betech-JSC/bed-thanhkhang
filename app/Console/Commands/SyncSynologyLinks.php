<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

/**
 * Disabled command - Legacy FunkyStyle feature removed from Thanh Khang CMS.
 */
class SyncSynologyLinks extends Command
{
    protected $hidden = true;
    protected $signature = 'synology:sync-all-links';
    protected $description = 'Deprecated command (inactive)';

    public function handle(): int
    {
        $this->warn('This legacy command is disabled in Thanh Khang CMS.');
        return 0;
    }
}
