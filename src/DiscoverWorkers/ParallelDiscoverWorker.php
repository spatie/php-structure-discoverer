<?php

namespace Spatie\StructureDiscoverer\DiscoverWorkers;

use function Amp\ParallelFunctions\parallelMap;
use function Amp\Promise\wait;

use Illuminate\Support\Collection;
use Spatie\StructureDiscoverer\TokenParsers\MultiFileTokenParser;

class ParallelDiscoverWorker implements DiscoverWorker
{
    public function __construct(
        public int $filesPerJob = 50
    ) {
    }

    public function run(Collection $filenames): array
    {
        $sets = $filenames->chunk($this->filesPerJob)->toArray();

        $promise = parallelMap($sets, function (array $set): array {
            return (new MultiFileTokenParser())->execute($set);
        });

        $found = wait($promise);

        return array_merge(...$found);
    }
}
