<?php

namespace Spatie\StructureDiscoverer\Data;

use Spatie\StructureDiscoverer\Cache\DiscoverCacheDriver;
use Spatie\StructureDiscoverer\DiscoverConditions\ExactDiscoverCondition;
use Spatie\StructureDiscoverer\DiscoverWorkers\DiscoverWorker;
use Spatie\StructureDiscoverer\Enums\StructureResolverSort;

class DiscoverProfileConfig
{
    public function __construct(
        public array $directories,
        public array $ignoredFiles,
        public bool $full,
        public DiscoverWorker $worker,
        public ?DiscoverCacheDriver $cacheDriver,
        public ?string $cacheId,
        public bool $withChains,
        public ExactDiscoverCondition $conditions,
        public ?StructureResolverSort $sortBy,
        public bool $reverseSorting
    ) {
    }

    public function shouldUseCache(): bool
    {
        return $this->cacheId !== null && $this->cacheDriver !== null;
    }
}
