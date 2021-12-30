<?php

namespace Spatie\LaravelAutoDiscoverer\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Spatie\LaravelAutoDiscoverer\DiscoverManager
 * @mixin \Spatie\LaravelAutoDiscoverer\DiscoverManager
 */
class Discover extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'laravel-auto-discoverer';
    }
}
