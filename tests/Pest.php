<?php

use Spatie\LaravelAutoDiscoverer\Discover;
use Spatie\LaravelAutoDiscoverer\DiscoverCache;
use Spatie\LaravelAutoDiscoverer\DiscoverProfilesCollection;
use Spatie\LaravelAutoDiscoverer\Tests\TestCase;
use Spatie\LaravelAutoDiscoverer\ValueObjects\DiscoverProfile;
use Spatie\LaravelAutoDiscoverer\ValueObjects\DiscoverProfileConfig;

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

        Discover::resetDiscovered();
    }
}
