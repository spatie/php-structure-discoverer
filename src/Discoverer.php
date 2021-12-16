<?php

namespace Spatie\LaravelAutoDiscoverer;

use Closure;
use Illuminate\Foundation\Events\DiscoverEvents;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use phpDocumentor\Reflection\Types\Self_;
use ReflectionClass;
use Spatie\LaravelAutoDiscoverer\Exceptions\UnknownDiscoverProfile;

class Discoverer
{
    /** @var \Spatie\LaravelAutoDiscoverer\DiscoverProfile[] */
    protected static array $profiles = [];

    public static function classes(string $identifier): DiscoverProfile
    {
        $profile = new DiscoverProfile($identifier);

        self::$profiles[] = $profile;

        return $profile;
    }

    public static function run()
    {
        /** @var \Illuminate\Support\Collection<\Spatie\LaravelAutoDiscoverer\DiscoverProfile> $cachedProfiles */
        /** @var \Illuminate\Support\Collection<\Spatie\LaravelAutoDiscoverer\DiscoverProfile> $profilesToDiscover */
        [$cachedProfiles, $profilesToDiscover] = collect(self::$profiles)->partition(
            fn (DiscoverProfile $profile) => Cache::has(self::resolveProfileCacheKey($profile))
        );

        $cachedDiscoveredClasses = $cachedProfiles->map(function (DiscoverProfile $profile) {
            $discovered = collect(Cache::get(self::resolveProfileCacheKey($profile)));

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

    public static function cache(): Collection
    {
        return self::discoverClassesForProfiles(...self::$profiles)
            ->eachSpread(function (DiscoverProfile $profile, Collection $discovered) {
                Cache::set(
                    self::resolveProfileCacheKey($profile),
                    $discovered->map(fn (ReflectionClass $class) => $class->name)->all()
                );
            })
            ->mapSpread(fn (DiscoverProfile $profile, Collection $discovered) => $profile->identifier);
    }

    public static function clearCache(): Collection
    {
        return collect(self::$profiles)
            ->each(fn (DiscoverProfile $profile) => Cache::forget(self::resolveProfileCacheKey($profile)))
            ->map(fn (DiscoverProfile $profile) => $profile->identifier);
    }

    public static function clearProfiles(): void
    {
        static::$profiles = [];
    }

    public static function get(string $identifier, Closure $closure): void
    {
        foreach (static::$profiles as $profile){
            if($profile->identifier === $identifier){
                $profile->get($closure);

                return;
            }
        }

        throw UnknownDiscoverProfile::forIdentifier($identifier);
    }

    private static function discoverClassesForProfiles(DiscoverProfile ...$profiles): Collection
    {
        $profiles = collect($profiles);

        $directories = $profiles
            ->flatMap(fn (DiscoverProfile $profile) => $profile->directories)
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
                ->filter(fn (ReflectionClass $reflectionClass, string $path) => self::isValidClassForProfile($reflectionClass, $path, $profile))
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

        $isSubDir = Arr::first($profile->directories, fn (string $directory) => str_starts_with(
            $path,
            $directory,
        ));

        if ($isSubDir === null) {
            return false;
        }

        return $profile->references->satisfies($reflectionClass);
    }

    private static function resolveProfileCacheKey(DiscoverProfile $profile): string
    {
        return "laravel-auto-discoverer.{$profile->identifier}";
    }
}
