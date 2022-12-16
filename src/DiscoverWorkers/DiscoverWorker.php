<?php

namespace Spatie\StructureDiscoverer\DiscoverWorkers;

use Illuminate\Support\Collection;

interface DiscoverWorker
{
    public function run(Collection $filenames): array;
}
