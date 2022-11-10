<?php

namespace Spatie\StructureDiscoverer\DiscoverWorkers;

use Illuminate\Support\Collection;
use Spatie\StructureDiscoverer\TokenParsers\MultiFileTokenParser;
use function Amp\ParallelFunctions\parallelMap;
use function Amp\Promise\wait;

class AsynchronousDiscoverWorker implements DiscoverWorker
{
    public function __construct(
        public int $filesPerJob = 50
    )
    {
    }

    public function run(Collection $filenames): array
    {
        $sets = $filenames->chunk($this->filesPerJob)->toArray();

        $found = wait(parallelMap($sets, function (array $set) {
            return (new MultiFileTokenParser())->execute($set);
        }));

        return array_merge(...$found);
    }
}
