<?php

namespace Spatie\LaravelAutoDiscoverer;

/** @mixin \Spatie\LaravelAutoDiscoverer\DiscoverManager */
class Discover
{
    protected static DiscoverManager $manager;

    private function __construct()
    {
    }

    public static function __callStatic(string $name, array $arguments): mixed
    {
        if (! isset(static::$manager)) {
            static::$manager = new DiscoverManager();
        }

        return call_user_func([static::$manager, $name], ...$arguments);
    }
}
