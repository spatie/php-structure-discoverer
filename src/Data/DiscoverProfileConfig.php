<?php

namespace Spatie\StructureDiscoverer\Data;

use Spatie\StructureDiscoverer\Cache\DiscoverCacheDriver;
use Spatie\StructureDiscoverer\DiscoverWorkers\DiscoverWorker;

class DiscoverProfileConfig
{
    public function __construct(
        public ?string $cacheId,
        public array $directories,
        public array $ignoredFiles,
        public bool $asString,
        public DiscoverWorker $worker,
        public ?DiscoverCacheDriver $cacheDriver,
        public bool $withChains,
    ) {
    }

    public function shouldUseCache(): bool
    {
        return $this->cacheId !== null && $this->cacheDriver !== null;
    }
}
