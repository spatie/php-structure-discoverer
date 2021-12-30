<?php

namespace Spatie\LaravelAutoDiscoverer;

use Closure;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use ReflectionClass;
use Spatie\LaravelAutoDiscoverer\Exceptions\UnknownDiscoverProfile;

class DiscoverManager
{
    /** @var \Spatie\LaravelAutoDiscoverer\DiscoverProfile[] */
    protected array $profiles = [];

    public function __construct(
        protected DiscoverCache $cache
    ) {
    }

    public function classes(string $identifier): DiscoverProfile
    {
        $profile = new DiscoverProfile($identifier);

        $this->profiles[] = $profile;

        return $profile;
    }

    public function run()
    {
        /** @var \Illuminate\Support\Collection<\Spatie\LaravelAutoDiscoverer\DiscoverProfile> $cachedProfiles */
        /** @var \Illuminate\Support\Collection<\Spatie\LaravelAutoDiscoverer\DiscoverProfile> $profilesToDiscover */
        [$cachedProfiles, $profilesToDiscover] = collect($this->profiles)->partition(
            fn (DiscoverProfile $profile) => $this->cache->has($profile)
        );

        $cachedDiscoveredClasses = $cachedProfiles->map(function (DiscoverProfile $profile) {
            $discovered = collect($this->cache->get($profile));

            if ($profile->returnReflection) {
                $discovered = $discovered->map(fn (string $class) => new ReflectionClass($class));
            }

            return [$profile, $discovered];
        });

        $discoveredClasses = self::discoverClassesForProfiles(...$profilesToDiscover)->mapSpread(function (DiscoverProfile $profile, Collection $discovered) {
            if ($profile->returnReflection === false) {
                $discovered = $discovered->map(fn (ReflectionClass $reflectionClass) => $reflectionClass->name);
            }

            return [$profile, $discovered];
        });

        $cachedDiscoveredClasses->merge($discoveredClasses)->eachSpread(function (DiscoverProfile $profile, Collection $discovered) {
            foreach ($profile->callBacks as $callBack) {
                $callBack($discovered->all());
            }
        });
    }

    public function cache(): Collection
    {
        return self::discoverClassesForProfiles(...$this->profiles)
            ->tap(fn (Collection $profilesAndDiscovered) => $this->cache->save($profilesAndDiscovered))
            ->mapSpread(fn (DiscoverProfile $profile, Collection $discovered) => $profile->identifier);
    }

    public function clearCache(): void
    {
        $this->cache->clear();
    }

    public function clearProfiles(): void
    {
        $this->profiles = [];
    }

    public function get(string $identifier, Closure $closure): void
    {
        foreach ($this->profiles as $profile) {
            if ($profile->identifier === $identifier) {
                $profile->get($closure);

                return;
            }
        }

        throw UnknownDiscoverProfile::forIdentifier($identifier);
    }

    private function discoverClassesForProfiles(DiscoverProfile ...$profiles): Collection
    {
        $profiles = collect($profiles);

        $directories = $profiles
            ->flatMap(fn (DiscoverProfile $profile) => $this->resolveDirectoriesForProfile($profile))
            ->unique()
            ->all();

        $classDiscoverer = new ClassDiscoverer(
            directories: $directories,
            basePath: config('auto-discoverer.base_path', base_path()),
            rootNamespace: config('auto-discoverer.root_namespace', ''),
            ignoredFiles: config('auto-discoverer.ignored_files', []),
        );

        $discovered = $classDiscoverer->discover();

        return $profiles->map(function (DiscoverProfile $profile) use ($discovered) {
            $classes = $discovered
                ->filter(fn (ReflectionClass $reflectionClass, string $path) => $this->isValidClassForProfile($reflectionClass, $path, $profile))
                ->values();

            return [$profile, $classes];
        });
    }

    private function isValidClassForProfile(
        ReflectionClass $reflectionClass,
        string $path,
        DiscoverProfile $profile
    ): bool {
        $path = realpath(dirname($path));

        $isSubDir = Arr::first($this->resolveDirectoriesForProfile($profile), fn (string $directory) => str_starts_with(
            $path,
            $directory,
        ));

        if ($isSubDir === null) {
            return false;
        }

        return $profile->conditions->satisfies($reflectionClass);
    }

    private function resolveDirectoriesForProfile(DiscoverProfile $profile): array
    {
        if (! empty($profile->directories)) {
            return $profile->directories;
        }

        return [config('auto-discoverer.base_path', base_path())];
    }
}
