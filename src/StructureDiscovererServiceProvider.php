<?php

namespace Spatie\StructureDiscoverer;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Spatie\StructureDiscoverer\Commands\CacheStructureScoutsCommand;
use Spatie\StructureDiscoverer\Commands\ClearStructureScoutsCommand;
use Spatie\StructureDiscoverer\Support\DiscoverCacheDriverFactory;

class StructureDiscovererServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('structure-discoverer')
            ->hasConfigFile()
            ->hasCommand(CacheStructureScoutsCommand::class)
            ->hasCommand(ClearStructureScoutsCommand::class);
    }

    public function packageRegistered(): void
    {
        $this->app->bind(Discover::class, fn ($app, $provided) => new Discover(
            directories: $provided['directories'] ?? [],
            ignoredFiles: config('structure-discoverer.ignored_files'),
            cacheDriver: DiscoverCacheDriverFactory::create(config('structure-discoverer.cache')),
        ));
    }
}
