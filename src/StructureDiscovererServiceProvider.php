<?php

namespace Spatie\StructureDiscoverer;

use Illuminate\Support\Arr;
use Spatie\StructureDiscoverer\Cache\LaravelDiscoverCacheDriver;
use Spatie\StructureDiscoverer\Commands\CacheDiscoveredClasses;
use Spatie\StructureDiscoverer\Commands\ClearDiscoveredClassesCache;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class StructureDiscovererServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('structure-discoverer')
            ->hasConfigFile()
            ->hasCommand(CacheDiscoveredClasses::class)
            ->hasCommand(ClearDiscoveredClassesCache::class);
    }

    public function packageRegistered()
    {
        $this->app->bind(Discover::class, fn($app, $provided) => new Discover(
            directories: $provided['directories'] ?? [],
            ignoredFiles: config('structure-discoverer.ignored_files'),
            cache: app(
                config('structure-discoverer.cache.driver'),
                Arr::except(config('structure-discoverer.cache'), 'driver')
            )
        ));
    }
}
