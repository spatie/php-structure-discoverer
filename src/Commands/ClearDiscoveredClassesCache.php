<?php

namespace Spatie\LaravelAutoDiscoverer\Commands;

use Illuminate\Console\Command;
use Spatie\LaravelAutoDiscoverer\Discover;

class ClearDiscoveredClassesCache extends Command
{
    public $signature = 'auto-discovered:clear';

    public $description = 'Clear auto discovered classes cache';

    public function handle(): void
    {
        Discover::clearCache();

        $this->info('Laravel auto discover cache cleared');
    }
}
