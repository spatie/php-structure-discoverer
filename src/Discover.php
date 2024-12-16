<?php

namespace Spatie\StructureDiscoverer;

use Spatie\StructureDiscoverer\Cache\DiscoverCacheDriver;
use Spatie\StructureDiscoverer\Data\DiscoveredStructure;
use Spatie\StructureDiscoverer\Data\DiscoverProfileConfig;
use Spatie\StructureDiscoverer\DiscoverConditions\ExactDiscoverCondition;
use Spatie\StructureDiscoverer\DiscoverWorkers\DiscoverWorker;
use Spatie\StructureDiscoverer\DiscoverWorkers\ParallelDiscoverWorker;
use Spatie\StructureDiscoverer\DiscoverWorkers\SynchronousDiscoverWorker;
use Spatie\StructureDiscoverer\Enums\Sort;
use Spatie\StructureDiscoverer\Exceptions\NoCacheConfigured;
use Spatie\StructureDiscoverer\StructureParsers\PhpTokenStructureParser;
use Spatie\StructureDiscoverer\StructureParsers\ReflectionStructureParser;
use Spatie\StructureDiscoverer\StructureParsers\StructureParser;
use Spatie\StructureDiscoverer\Support\Conditions\HasConditionsTrait;
use Spatie\StructureDiscoverer\Support\LaravelDetector;
use Spatie\StructureDiscoverer\Support\StructuresResolver;

class Discover
{
    use HasConditionsTrait;

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

    /**
     * @param array<string> $directories
     * @param array<string> $ignoredFiles
     */
    public function __construct(
        array $directories = [],
        array $ignoredFiles = [],
        ExactDiscoverCondition $conditions = new ExactDiscoverCondition(),
        bool $full = false,
        DiscoverWorker $worker = new SynchronousDiscoverWorker(),
        ?DiscoverCacheDriver $cacheDriver = null,
        ?string $cacheId = null,
        bool $withChains = true,
        ?Sort $sort = null,
        bool $reverseSorting = false,
        StructureParser $structureParser = new PhpTokenStructureParser(),
        ?string $reflectionBasePath = null,
        ?string $reflectionRootNamespace = null,
    ) {
        $this->config = new DiscoverProfileConfig(
            directories: $directories,
            ignoredFiles: $ignoredFiles,
            full: $full,
            worker: $worker,
            cacheDriver: $cacheDriver,
            cacheId: $cacheId,
            withChains: $withChains,
            conditions: $conditions,
            sort: $sort,
            reverseSorting: $reverseSorting,
            structureParser: $structureParser,
            reflectionBasePath: $reflectionBasePath,
            reflectionRootNamespace: $reflectionRootNamespace,
        );
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

    public function sortBy(Sort $sort, bool $reverse = false): self
    {
        $this->config->sort = $sort;
        $this->config->reverseSorting = $reverse;

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

    public function withCache(string $id, ?DiscoverCacheDriver $cache = null): self
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

    public function useReflection(?string $basePath = null, ?string $rootNamespace = null): self
    {
        $this->config->structureParser = new ReflectionStructureParser($this->config);
        $this->config->reflectionBasePath = $basePath;
        $this->config->reflectionRootNamespace = $rootNamespace;

        return $this;
    }

    /** @return array<DiscoveredStructure>|array<string> */
    public function get(): array
    {
        if (! $this->config->shouldUseCache()) {
            return $this->getWithoutCache();
        }

        return $this->config->cacheDriver->has($this->config->cacheId)
            ? $this->config->cacheDriver->get($this->config->cacheId)
            : $this->cache();
    }

    /** @return array<DiscoveredStructure>|array<string> */
    public function getWithoutCache(): array
    {
        $discoverer = new StructuresResolver($this->config->worker);

        return $discoverer->execute($this);
    }

    /** @return array<DiscoveredStructure>|array<string> */
    public function cache(): array
    {
        $structures = $this->getWithoutCache();

        $this->config->cacheDriver->put(
            $this->config->cacheId,
            $structures
        );

        return $structures;
    }

    public function conditionsStore(): ExactDiscoverCondition
    {
        return $this->config->conditions;
    }
}
