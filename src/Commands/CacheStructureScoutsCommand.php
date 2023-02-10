<?php

namespace Spatie\StructureDiscoverer\Commands;

use Illuminate\Console\Command;
use Spatie\StructureDiscoverer\Support\StructureScoutManager;

class CacheStructureScoutsCommand extends Command
{
    protected $signature = 'structure-scouts:cache';

    protected $description = 'Cache discoverers within your application';

    public function handle(): void
    {
        $this->components->info('Caching structure scouts...');

        $cached = StructureScoutManager::cache(config('structure-discoverer.structure_scout_directories'));

        collect($cached)
            ->each(fn (string $identifier) => $this->components->task($identifier))
            ->whenNotEmpty(fn () => $this->newLine());

        $this->components->info('All done!');
    }
}
