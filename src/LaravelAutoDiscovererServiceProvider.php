<?php

namespace Spatie\LaravelAutoDiscoverer;

use Illuminate\Foundation\Bootstrap\BootProviders;
use Illuminate\Support\Facades\Event;
use Spatie\LaravelAutoDiscoverer\Commands\CacheDiscoveredClasses;
use Spatie\LaravelAutoDiscoverer\Commands\ClearDiscoveredClassesCache;
use Spatie\LaravelAutoDiscoverer\Facades\Discover;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class LaravelAutoDiscovererServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-auto-discoverer')
            ->hasConfigFile()
            ->hasViews()
            ->hasCommand(CacheDiscoveredClasses::class)
            ->hasCommand(ClearDiscoveredClassesCache::class);

        Event::listen('bootstrapped: ' . BootProviders::class, fn () => Discover::run());

        $this->app->bind('laravel-auto-discoverer', DiscoverManager::class);
//        $this->app->instance('laravel-auto-discoverer', DiscoverManager::class);
    }
}
