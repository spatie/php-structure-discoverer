<?php

namespace Spatie\StructureDiscoverer;

use Spatie\StructureDiscoverer\Cache\DiscoverCacheDriver;
use Spatie\StructureDiscoverer\Data\DiscoveredStructure;
use Spatie\StructureDiscoverer\Data\DiscoverProfileConfig;
use Spatie\StructureDiscoverer\DiscoverConditions\ExactDiscoverCondition;
use Spatie\StructureDiscoverer\DiscoverWorkers\DiscoverWorker;
use Spatie\StructureDiscoverer\DiscoverWorkers\ParallelDiscoverWorker;
use Spatie\StructureDiscoverer\DiscoverWorkers\SynchronousDiscoverWorker;
use Spatie\StructureDiscoverer\Exceptions\NoCacheConfigured;
use Spatie\StructureDiscoverer\Support\LaravelDetector;
use Spatie\StructureDiscoverer\Support\StructuresResolver;

/**
 * TODO
 * - readme
 */
class Discover extends DiscoverConditionFactory
{
    public readonly DiscoverProfileConfig $config;

    public static function in(string ...$directories): self
    {
        if (LaravelDetector::isRunningLaravel()) {
            return app(self::class, [
                'directories' => $directories,
            ]);
        }

        return new self(
            directories: $directories,
        );
    }

    public function __construct(
        array $directories = [],
        array $ignoredFiles = [],
        ExactDiscoverCondition $conditions = new ExactDiscoverCondition(),
        bool $full = false,
        DiscoverWorker $worker = new SynchronousDiscoverWorker(),
        ?DiscoverCacheDriver $cacheDriver = null,
        ?string $cacheId = null,
        bool $withChains = true,
    ) {
        $this->config = new DiscoverProfileConfig(
            directories: $directories,
            ignoredFiles: $ignoredFiles,
            full: $full,
            worker: $worker,
            cacheDriver: $cacheDriver,
            cacheId: $cacheId,
            withChains: $withChains
        );

        parent::__construct($conditions);
    }

    public function inDirectories(string ...$directories): self
    {
        array_push($this->config->directories, ...$directories);

        return $this;
    }

    public function ignoreFiles(string ...$ignoredFiles): self
    {
        array_push($this->config->ignoredFiles, ...$ignoredFiles);

        return $this;
    }

    public function full(): self
    {
        $this->config->full = true;

        return $this;
    }

    public function usingWorker(DiscoverWorker $worker): self
    {
        $this->config->worker = $worker;

        return $this;
    }

    public function parallel(int $filesPerJob = 50): self
    {
        return $this->usingWorker(new ParallelDiscoverWorker($filesPerJob));
    }

    public function cache(string $id, ?DiscoverCacheDriver $cache = null): self
    {
        $this->config->cacheId = $id;

        if ($this->config->cacheDriver === null && $cache === null) {
            throw new NoCacheConfigured();
        }

        $this->config->cacheDriver = $cache;

        return $this;
    }

    public function withoutChains(bool $withoutChains = true): self
    {
        $this->config->withChains = ! $withoutChains;

        return $this;
    }

    /** @return array<DiscoveredStructure>|array<string> */
    public function get(): array
    {
        $discoverer = new StructuresResolver($this->config->worker);

        return $discoverer->execute($this);
    }
}
