<?php

namespace Spatie\StructureDiscoverer;

use Spatie\StructureDiscoverer\Cache\DiscoverCacheDriver;
use Spatie\StructureDiscoverer\Cache\LaravelDiscoverCacheDriver;
use Spatie\StructureDiscoverer\Cache\NullDiscoverCacheDriver;
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

    abstract protected function definition(): Discover|DiscoverConditionFactory;

    public function cacheDriver(): DiscoverCacheDriver
    {
        if (LaravelDetector::isRunningLaravel()) {
            return DiscoverCacheDriverFactory::create(config('structure-discoverer.cache'));
        }

        throw new StructureScoutsCacheDriverMissing();
    }

    /** @return array<DiscoveredStructure>|array<string> */
    public function get(): array
    {
        return $this->definition()
            ->cache($this->identifier(), $this->cacheDriver())
            ->get();
    }
}
