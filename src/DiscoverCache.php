<?php

namespace Spatie\LaravelAutoDiscoverer;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use ReflectionClass;

class DiscoverCache
{
    public static ?array $cache = null;

    public function has(DiscoverProfile $profile): bool
    {
        return array_key_exists($profile->identifier, $this->all());
    }

    public function get(DiscoverProfile $profile): array
    {
        return $this->all()[$profile->identifier];
    }

    public function save(Collection $profilesAndDiscovered): void
    {
        $json = $profilesAndDiscovered->mapWithKeys(function (array $item) {
            /** @var \Spatie\LaravelAutoDiscoverer\DiscoverProfile $profile */
            /** @var \Illuminate\Support\Collection $discovered */
            [$profile, $discovered] = $item;

            return [
                $profile->identifier => $discovered->map(fn(ReflectionClass $class) => $class->name)->all(),
            ];
        })->toJson();

        File::ensureDirectoryExists(config('auto-discoverer.cache_directory'));
        File::put($this->resolveCacheFile(), $json);
    }

    public function clear(): void
    {
        File::delete($this->resolveCacheFile());
        self::$cache = null;
    }

    private function all(): array
    {
        if (self::$cache !== null) {
            return self::$cache;
        }

        $cacheFile = $this->resolveCacheFile();

        if (! File::exists($cacheFile)) {
            return [];
        }

        return json_decode(File::get($cacheFile), true);
    }

    private function resolveCacheFile(): string
    {
        return rtrim(config('auto-discoverer.cache_directory'), '/') . '/cached.json';
    }
}
