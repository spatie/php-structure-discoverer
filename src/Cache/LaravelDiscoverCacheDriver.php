<?php

namespace Spatie\StructureDiscoverer\Cache;

use Illuminate\Contracts\Cache\Repository;

class LaravelDiscoverCacheDriver implements DiscoverCacheDriver
{
    public function __construct(
        public ?string $driver = null,
    ) {
    }

    public function has(string $id): bool
    {
        return $this->resolveCacheRepository()->has($this->resolveCacheKey($id));
    }

    public function get(string $id): array
    {
        return $this->resolveCacheRepository()->get($this->resolveCacheKey($id));
    }

    public function put(string $id, array $discovered): void
    {
        $this->resolveCacheRepository()->put($this->resolveCacheKey($id), $discovered);
    }

    public function forget(string $id): void
    {
        $this->resolveCacheRepository()->forget($this->resolveCacheKey($id));
    }

    private function resolveCacheRepository(): Repository
    {
        return cache()->driver($this->driver);
    }

    private function resolveCacheKey(string $id): string
    {
        return "discoverer-cache-{$id}";
    }
}
