<?php

namespace Spatie\StructureDiscoverer;

use Spatie\StructureDiscoverer\Cache\DiscoverCacheDriver;
use Spatie\StructureDiscoverer\Data\DiscoveredStructure;
use Spatie\StructureDiscoverer\Exceptions\StructureScoutsCacheDriverMissing;
use Spatie\StructureDiscoverer\Support\DiscoverCacheDriverFactory;
use Spatie\StructureDiscoverer\Support\LaravelDetector;

abstract class StructureScout
{
    public static function create(): static
    {
        return new static();
    }

    public function identifier(): string
    {
        return static::class;
    }

    abstract protected function definition(): Discover;

    public function cacheDriver(): DiscoverCacheDriver
    {
        if (LaravelDetector::isRunningLaravel()) {
            return DiscoverCacheDriverFactory::create(config('structure-discoverer.cache'));
        }

        throw new StructureScoutsCacheDriverMissing();
    }

    /**
     * @return array<DiscoveredStructure>|array<string>
     */
    public function get(): array
    {
        return $this->definition()
            ->withCache($this->identifier(), $this->cacheDriver())
            ->get();
    }

    /**
     * @return array<DiscoveredStructure>|array<string>
     */
    public function cache(): array
    {
        return $this->definition()
            ->withCache($this->identifier(), $this->cacheDriver())
            ->cache();
    }

    public function clear(): static
    {
        $this->cacheDriver()->forget($this->identifier());

        return $this;
    }

    public function isCached(): bool
    {
        return $this->cacheDriver()->has($this->identifier());
    }
}
