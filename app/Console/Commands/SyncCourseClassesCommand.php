<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SyncCourseClassesCommand extends Command
{
    protected $signature = 'courses:sync-classes';
    protected $description = 'Deprecated FunkyStyle course class sync command';

    public function handle(): int
    {
        $this->info('This feature has been deprecated in Saigonchem Base CMS.');
        return 0;
    }
}
