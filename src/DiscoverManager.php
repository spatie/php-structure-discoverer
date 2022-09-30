<?php

namespace Spatie\LaravelAutoDiscoverer;

use Closure;
use Illuminate\Support\Collection;
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

    public function has(string $identifier): bool
    {
        return $this->profiles->has($identifier);
    }

    public function get(string $identifier): Collection
    {
        $profile = $this->profiles->get($identifier);

        if (! $profile->isDiscovered()) {
            throw CallbackRequired::create($identifier);
        }

        return $profile->getDiscoveredClasses();
    }

    public function getInstantly(string $identifier): Collection
    {
        $profile = $this->profiles->get($identifier);

        if (! $profile->isDiscovered()) {
            $this->run([$profile->getIdentifier()]);
        }

        return $profile->getDiscoveredClasses();
    }

    public function update(string $identifier, Closure $closure): void
    {
        $closure($this->profiles->get($identifier)->config);
    }

    public function run(?array $selectedProfiles = null): void
    {
        [$activeProfiles, $ignoredProfiles] = $this->profiles->partition(fn(DiscoverProfile $profile) => match (true) {
            $profile->isDiscovered() => false,
//            count($profile->config->callBacks) === 0 => false, Let's say we have an instantly get without callable, then this should not evaluate
            $selectedProfiles === null => true,
            default => in_array($profile->getIdentifier(), $selectedProfiles),
        });

        [$cachedProfiles, $nonCachedProfiles] = $activeProfiles->partition(
            fn(DiscoverProfile $profile) => $this->cache->has($profile)
        );

        $this->profiles = $cachedProfiles
            ->transform(
                fn(DiscoverProfile $profile) => $profile
                    ->addDiscovered(...$this->cache->get($profile))
                    ->markDiscovered(fromCache: true)
            )
            ->merge(ClassDiscoverer::create()->discover($nonCachedProfiles))
            ->merge($ignoredProfiles);

        $this->profiles
            ->filter(fn(DiscoverProfile $profile) => $profile->isDiscovered())
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
