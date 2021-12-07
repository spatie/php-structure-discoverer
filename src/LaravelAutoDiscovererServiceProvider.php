<?php

namespace Spatie\LaravelAutoDiscoverer;

use Illuminate\Foundation\Bootstrap\BootProviders;
use Illuminate\Support\Facades\Event;
use Spatie\LaravelAutoDiscoverer\Commands\CacheDiscoveredClasses;
use Spatie\LaravelAutoDiscoverer\Commands\ClearDiscoveredClassesCache;
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

        Event::listen('bootstrapped: ' . BootProviders::class, fn () => Discoverer::run());
    }
}
