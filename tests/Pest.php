<?php

use Spatie\StructureDiscoverer\Resolvers\StructuresResolver;
use Spatie\StructureDiscoverer\DiscoverCache;
use Spatie\StructureDiscoverer\DiscoverProfilesCollection;
use Spatie\StructureDiscoverer\Tests\TestCase;
use Spatie\StructureDiscoverer\ValueObjects\DiscoverProfile;
use Spatie\StructureDiscoverer\ValueObjects\DiscoverProfileConfig;

uses(TestCase::class)->in(__DIR__);

if (! function_exists('setProfileInCache')) {
    function setProfileInCache(DiscoverProfileConfig $config, array $classes)
    {
        $profile = new DiscoverProfile($config);

        foreach ($classes as $class) {
            $profile->addDiscovered($class);
        }

        $profile->markDiscovered();

        $collection = new DiscoverProfilesCollection(collect([
            $profile,
        ]));

        app(DiscoverCache::class)->save($collection);

        StructuresResolver::resetDiscovered();
    }
}
