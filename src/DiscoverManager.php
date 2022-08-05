<?php

namespace Spatie\LaravelAutoDiscoverer;

use Closure;
use Illuminate\Support\Collection;
use ReflectionClass;
use Spatie\LaravelAutoDiscoverer\Exceptions\CallbackRequired;
use Spatie\LaravelAutoDiscoverer\ValueObjects\DiscoverProfile;
use Spatie\LaravelAutoDiscoverer\ValueObjects\DiscoverProfileConfig;

class DiscoverManager
{
    protected DiscoverProfilesCollection $profiles;

    protected DiscoverCache $cache;

    public function __construct()
    {
        $this->profiles = new DiscoverProfilesCollection();
        $this->cache = new DiscoverCache();
    }

    public function classes(string $identifier): DiscoverProfileConfig
    {
        $data = new DiscoverProfileConfig(
            $identifier,
            config('auto-discoverer.base_path') ?? base_path(),
            '',
        );

        $this->profiles->add(new DiscoverProfile($data));

        return $data;
    }

    public function clearProfiles(): void
    {
        $this->profiles = new DiscoverProfilesCollection();
    }

    public function addCallback(string $identifier, Closure $closure): void
    {
        $this->profiles->get($identifier)->config->get($closure);
    }

    public function get(string $identifier): Collection
    {
        $profile = $this->profiles->get($identifier);

        if (! $profile->isDiscovered()) {
            throw CallbackRequired::create($identifier);
        }

        return $profile->getDiscoveredClasses();
    }

    public function update(string $identifier, Closure $closure): void
    {
        $closure($this->profiles->get($identifier)->config);
    }

    public function run(): void
    {
        [$discoveredProfiles, $nonDiscoveredProfiles] = $this->profiles->partition(
            fn(DiscoverProfile $profile) => $profile->isDiscovered()
        );

        [$cachedProfiles, $nonCachedProfiles] = $nonDiscoveredProfiles->partition(
            fn(DiscoverProfile $profile) => $this->cache->has($profile)
        );

        $this->profiles = $cachedProfiles
            ->transform(fn(DiscoverProfile $profile) => $profile
                ->addDiscovered(...$this->cache->get($profile))
                ->markDiscovered(fromCache: true)
            )
            ->merge(ClassDiscoverer::create()->discover($nonCachedProfiles))
            ->merge($discoveredProfiles)
            ->each(fn(DiscoverProfile $profile) => $profile->runCallbacks());
    }

    public function resetDiscovered(): void
    {
        $this->profiles->reset();
    }

    public function cache(): void
    {
        $this->cache->save(ClassDiscoverer::create()->discover($this->profiles->reset()));
    }

    public function clearCache(): void
    {
        $this->cache->clear();
    }
}
