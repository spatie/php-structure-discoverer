<?php

namespace Spatie\StructureDiscoverer\Commands;

use Illuminate\Console\Command;
use Spatie\StructureDiscoverer\Support\StructureScoutManager;

class ClearStructureScoutsCommand extends Command
{
    protected $signature = 'structure-scouts:clear';

    protected $description = 'Clear cached discoverers within your application';

    public function handle(): void
    {
        $this->components->info('Clearing structure scouts...');

        $cached = StructureScoutManager::clear(config('structure-discoverer.structure_scout_directories'));

        collect($cached)
            ->each(fn (string $identifier) => $this->components->task($identifier))
            ->whenNotEmpty(fn () => $this->newLine());

        $this->components->info('All done!');
    }
}
