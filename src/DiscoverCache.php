<?php

namespace Spatie\LaravelAutoDiscoverer;

use Illuminate\Support\Facades\File;
use Spatie\LaravelAutoDiscoverer\Contracts\DiscoverProfileIdentifieable;
use Spatie\LaravelAutoDiscoverer\ValueObjects\DiscoverProfile;

class DiscoverCache
{
    public static ?array $cache = null;

    public function has(DiscoverProfileIdentifieable $profile): bool
    {
        return array_key_exists($profile->getIdentifier(), $this->all());
    }

    public function get(DiscoverProfileIdentifieable $profile): array
    {
        return $this->all()[$profile->getIdentifier()];
    }

    public function save(DiscoverProfilesCollection $profiles): void
    {
        $json = $profiles
            ->filter(fn (DiscoverProfile $profile) => $profile->isDiscovered())
            ->toCollection()
            ->mapWithKeys(fn (DiscoverProfile $profile) => [
                $profile->getIdentifier() => $profile->getDiscoveredClassNames()->all(),
            ])
            ->toJson();

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
