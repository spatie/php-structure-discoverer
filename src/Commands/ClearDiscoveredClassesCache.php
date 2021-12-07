<?php

namespace Spatie\LaravelAutoDiscoverer\Commands;

use Illuminate\Console\Command;
use Spatie\LaravelAutoDiscoverer\Discoverer;

class ClearDiscoveredClassesCache extends Command
{
    public $signature = 'discover-classes:clear';

    public $description = 'Clear auto discovered classes cache';

    public function handle()
    {
        $identifiers = Discoverer::clearCache();

        if ($identifiers->isEmpty()) {
            $this->info('No auto discover profiles were found');

            return;
        }

        $this->info("Removed auto discovered classes cache for profiles: {$identifiers->join(', ', ' and ')}");
    }
}
