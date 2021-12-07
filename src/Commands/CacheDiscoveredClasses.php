<?php

namespace Spatie\LaravelAutoDiscoverer\Commands;

use Illuminate\Console\Command;
use Spatie\LaravelAutoDiscoverer\Discoverer;

class CacheDiscoveredClasses extends Command
{
    public $signature = 'discover-classes:cache';

    public $description = 'Cache all auto discovered class';

    public function handle()
    {
        $identifiers = Discoverer::cache();

        if($identifiers->isEmpty()){
            $this->info('No auto discover profiles were found');

            return;
        }

        $this->info("Cached auto discovered classes for profiles: {$identifiers->join(', ', ' and ')}");
    }
}
