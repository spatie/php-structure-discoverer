<?php

namespace Spatie\StructureDiscoverer\DiscoverWorkers;

use Illuminate\Support\Collection;
use Spatie\StructureDiscoverer\TokenParsers\MultiFileTokenParser;

class SynchronousDiscoverWorker implements DiscoverWorker
{
    public function __construct(
        protected MultiFileTokenParser $multiFileResolver = new MultiFileTokenParser(),
    ) {
    }

    public function run(Collection $filenames): array
    {
        return $this->multiFileResolver->execute($filenames->toArray());
    }
}
