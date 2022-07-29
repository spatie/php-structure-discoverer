<?php

namespace Spatie\LaravelAutoDiscoverer;

use Closure;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use ReflectionClass;
use Spatie\LaravelAutoDiscoverer\Exceptions\CallbackRequired;
use Spatie\LaravelAutoDiscoverer\Exceptions\UnknownDiscoverProfile;

class Discover
{
    /** @var \Spatie\LaravelAutoDiscoverer\DiscoverProfile[] */
    protected static array $profiles = [];

    public static function classes(string $identifier): DiscoverProfile
    {
        $profile = new DiscoverProfile(
            $identifier,
            config('auto-discoverer.base_path') ?? base_path(),
            '',
        );

        static::$profiles[] = $profile;

        return $profile;
    }

    public static function clearProfiles(): void
    {
        static::$profiles = [];
    }

    public static function addCallback(string $identifier, Closure $closure): void
    {
        static::findProfile($identifier)->get($closure);
    }

    public static function get(string $identifier): array
    {
        $profile = static::findProfile($identifier);

        if(! isset($profile->discovered)){
            throw CallbackRequired::create($identifier);
        }

        return $profile->discovered;
    }

    public static function update(string $identifier, Closure $closure): void
    {
        $closure(static::findProfile($identifier));
    }

    public static function run(): void
    {
        $cache = resolve(DiscoverCache::class);

        /** @var \Illuminate\Support\Collection<\Spatie\LaravelAutoDiscoverer\DiscoverProfile> $cachedProfiles */
        /** @var \Illuminate\Support\Collection<\Spatie\LaravelAutoDiscoverer\DiscoverProfile> $profilesToDiscover */
        [$cachedProfiles, $profilesToDiscover] = collect(static::$profiles)->partition(
            fn(DiscoverProfile $profile) => $cache->has($profile)
        );

        $cachedDiscoveredClasses = $cachedProfiles->map(function (DiscoverProfile $profile) use ($cache) {
            $discovered = collect($cache->get($profile));

            if ($profile->returnReflectionWhenCached) {
                $discovered = $discovered->map(fn(string $class) => new ReflectionClass($class));
            }

            return [$profile, $discovered];
        });

        $discoveredClasses = self::discoverClassesForProfiles(...$profilesToDiscover)->mapSpread(function (DiscoverProfile $profile, Collection $discovered) {
            if ($profile->returnReflection === false) {
                $discovered = $discovered->map(fn(ReflectionClass $reflectionClass) => $reflectionClass->name);
            }

            return [$profile, $discovered];
        });

        $cachedDiscoveredClasses->merge($discoveredClasses)->eachSpread(function (DiscoverProfile $profile, Collection $discovered) {
            $profile->discovered = $discovered->all();

            foreach ($profile->callBacks as $callBack) {
                $callBack($discovered->all());
            }
        });
    }

    public static function cache(): Collection
    {
        $cache = resolve(DiscoverCache::class);

        return self::discoverClassesForProfiles(...static::$profiles)
            ->tap(fn(Collection $profilesAndDiscovered) => $cache->save($profilesAndDiscovered))
            ->mapSpread(fn(DiscoverProfile $profile, Collection $discovered) => $profile->identifier);
    }

    public static function clearCache(): void
    {
        resolve(DiscoverCache::class)->clear();
    }

    protected static function findProfile(string $identifier): DiscoverProfile
    {
        foreach (static::$profiles as $profile) {
            if ($profile->identifier === $identifier) {
                return $profile;
            }
        }

        throw UnknownDiscoverProfile::forIdentifier($identifier);
    }

    private static function discoverClassesForProfiles(DiscoverProfile ...$profiles): Collection
    {
        $profiles = collect($profiles);

        $discoverer = new ClassDiscoverer();

        return $profiles
            ->map(function (DiscoverProfile $profile) use ($discoverer) {
                $classes = $discoverer->discover($profile)
                    ->filter(fn(ReflectionClass $reflectionClass, string $path) => self::isValidClassForProfile($reflectionClass, $path, $profile))
                    ->values();

                return [$profile, $classes];
            });
    }

    private static function isValidClassForProfile(
        ReflectionClass $reflectionClass,
        string $path,
        DiscoverProfile $profile
    ): bool {
        $path = realpath(dirname($path));

        $isSubDir = Arr::first($profile->getDirectories(), fn(string $directory) => str_starts_with(
            $path,
            $directory,
        ));

        if ($isSubDir === null) {
            return false;
        }

        return $profile->conditions->satisfies($reflectionClass);
    }
}
