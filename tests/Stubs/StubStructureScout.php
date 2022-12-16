<?php

namespace Spatie\StructureDiscoverer\Tests\Stubs;

use Spatie\StructureDiscoverer\Cache\DiscoverCacheDriver;
use Spatie\StructureDiscoverer\Cache\StaticDiscoverCacheDriver;
use Spatie\StructureDiscoverer\Discover;
use Spatie\StructureDiscoverer\StructureScout;

class StubStructureScout extends StructureScout
{
    public function identifier(): string
    {
        return 'stub';
    }

    public function cacheDriver(): DiscoverCacheDriver
    {
        return new StaticDiscoverCacheDriver();
    }

    protected function definition(): Discover
    {
        return Discover::in(__DIR__ . '/../Fakes')->enums();
    }
}
