<?php

namespace Spatie\StructureDiscoverer\DiscoverWorkers;

use function Amp\async;
use function Amp\Future\await;

use Illuminate\Support\Collection;
use Spatie\StructureDiscoverer\Data\DiscoveredStructure;
use Spatie\StructureDiscoverer\Data\DiscoverProfileConfig;

class ParallelDiscoverWorker implements DiscoverWorker
{
    public function __construct(
        public int $filesPerJob = 50,
    ) {
    }

    /**
     * @param Collection<int, string> $filenames
     * @param DiscoverProfileConfig $config
     *
     * @return array<DiscoveredStructure>
     */
    public function run(Collection $filenames, DiscoverProfileConfig $config): array
    {
        $sets = $filenames->chunk($this->filesPerJob)->toArray();

        $found = await(array_map(function ($set) use ($config) {
            return async(fn () => $config->structureParser->execute($set));
        }, $sets));

        return array_merge(...$found);
    }
}
