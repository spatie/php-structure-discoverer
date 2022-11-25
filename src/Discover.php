<?php

namespace Spatie\StructureDiscoverer;

use Spatie\StructureDiscoverer\Cache\DiscoverCacheDriver;
use Spatie\StructureDiscoverer\Data\DiscoverProfileConfig;
use Spatie\StructureDiscoverer\DiscoverConditions\ExactDiscoverCondition;
use Spatie\StructureDiscoverer\DiscoverWorkers\AsynchronousDiscoverWorker;
use Spatie\StructureDiscoverer\DiscoverWorkers\DiscoverWorker;
use Spatie\StructureDiscoverer\DiscoverWorkers\SynchronousDiscoverWorker;
use Spatie\StructureDiscoverer\Exceptions\NoCacheConfigured;
use Spatie\StructureDiscoverer\Resolvers\StructuresResolver;

/**
 * TODO
 * - test chains
 * - add extra conditions based upon chains
 * - readme
 */
class Discover extends DiscoverConditionFactory
{
    public readonly DiscoverProfileConfig $config;

    public static function in(string ...$directories): self
    {
        if (function_exists('app') && function_exists('resolve')) {
            return app(self::class, [
                'directories' => $directories,
            ]);
        }

        return new self(directories: $directories);
    }

    public function __construct(
        ?string $cacheId = null,
        array $directories = [],
        array $ignoredFiles = [],
        ExactDiscoverCondition $conditions = new ExactDiscoverCondition(),
        bool $asString = false,
        DiscoverWorker $worker = new SynchronousDiscoverWorker(),
        ?DiscoverCacheDriver $cache = null,
        bool $withChains = true,
    ) {
        $this->config = new DiscoverProfileConfig(
            $cacheId,
            $directories,
            $ignoredFiles,
            $asString,
            $worker,
            $cache,
            $withChains
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

    public function asString(): self
    {
        $this->config->asString = true;

        return $this;
    }

    public function usingWorker(DiscoverWorker $worker): self
    {
        $this->config->worker = $worker;

        return $this;
    }

    public function async(int $filesPerJob = 50): self
    {
        return $this->usingWorker(new AsynchronousDiscoverWorker($filesPerJob));
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

    public function get(): array
    {
        $discoverer = new StructuresResolver($this->config->worker);

        return $discoverer->run($this);
    }
}
