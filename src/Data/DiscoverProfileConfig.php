<?php

namespace Spatie\StructureDiscoverer\Data;

use Spatie\StructureDiscoverer\Cache\DiscoverCacheDriver;
use Spatie\StructureDiscoverer\DiscoverConditions\ExactDiscoverCondition;
use Spatie\StructureDiscoverer\DiscoverWorkers\DiscoverWorker;
use Spatie\StructureDiscoverer\Enums\Sort;
use Spatie\StructureDiscoverer\StructureParsers\StructureParser;

class DiscoverProfileConfig
{
    /**
     * @param array<string> $directories
     * @param array<string> $ignoredFiles
     */
    public function __construct(
        public array $directories,
        public array $ignoredFiles,
        public bool $full,
        public DiscoverWorker $worker,
        public ?DiscoverCacheDriver $cacheDriver,
        public ?string $cacheId,
        public bool $withChains,
        public ExactDiscoverCondition $conditions,
        public ?Sort $sort,
        public bool $reverseSorting,
        public StructureParser $structureParser,
        public ?string $reflectionBasePath,
        public ?string $reflectionRootNamespace,
    ) {
    }

    public function shouldUseCache(): bool
    {
        return $this->cacheId !== null && $this->cacheDriver !== null;
    }
}
