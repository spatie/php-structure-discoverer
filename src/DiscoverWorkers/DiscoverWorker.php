<?php

namespace Spatie\StructureDiscoverer\DiscoverWorkers;

use Illuminate\Support\Collection;
use Spatie\StructureDiscoverer\Data\DiscoverProfileConfig;

interface DiscoverWorker
{
    public function run(Collection $filenames, DiscoverProfileConfig $config): array;
}
