<?php

namespace Spatie\StructureDiscoverer\DiscoverWorkers;

use Illuminate\Support\Collection;
use Spatie\StructureDiscoverer\Data\DiscoverProfileConfig;
use function Amp\ParallelFunctions\parallelMap;
use function Amp\Promise\wait;

class ParallelDiscoverWorker implements DiscoverWorker
{
    public function __construct(
        public int $filesPerJob = 50,
    ) {
    }

    public function run(Collection $filenames, DiscoverProfileConfig $config): array
    {
        $sets = $filenames->chunk($this->filesPerJob)->toArray();

        $promise = parallelMap(
            $sets,
            fn (array $set): array => $config->structureParser->execute($set)
        );

        $found = wait($promise);

        return array_merge(...$found);
    }
}
