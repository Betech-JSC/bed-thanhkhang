<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

/**
 * Disabled command - Legacy FunkyStyle feature removed from Thanh Khang Tecsat CMS.
 */
class SyncCourseClassesCommand extends Command
{
    protected $hidden = true;
    protected $signature = 'courses:sync-classes';
    protected $description = 'Deprecated command (inactive)';

    public function handle(): int
    {
        $this->warn('This legacy command is disabled in Thanh Khang Tecsat CMS.');
        return 0;
    }
}
