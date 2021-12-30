<?php

use Spatie\LaravelAutoDiscoverer\DiscoverCache;
use Spatie\LaravelAutoDiscoverer\DiscoverProfile;
use Spatie\LaravelAutoDiscoverer\Tests\TestCase;

uses(TestCase::class)->in(__DIR__);

if (! function_exists('setProfileInCache')) {
    function setProfileInCache(DiscoverProfile $profile, array $classes)
    {
        app(DiscoverCache::class)->save(collect([
            [
                $profile,
                collect($classes)->map(fn(string $class) => new ReflectionClass($class)),
            ],
        ]));
    }

}
