<?php

namespace Spatie\StructureDiscoverer\Cache;

use Illuminate\Contracts\Cache\Repository;

class LaravelDiscoverCacheDriver implements DiscoverCacheDriver
{
    public function __construct(
        public ?string $prefix = null,
        public ?string $store = null,
    ) {
    }

    public function has(string $id): bool
    {
        return $this->resolveCacheRepository()->has($this->resolveCacheKey($id));
    }

    /** @return array<mixed> */
    public function get(string $id): array
    {
        return $this->resolveCacheRepository()->get($this->resolveCacheKey($id));
    }

    /** @param  array<mixed> $discovered */
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
        return cache()->store($this->store);
    }

    private function resolveCacheKey(string $id): string
    {
        return $this->prefix
            ? "{$this->prefix}-discoverer-cache-{$id}"
            : "discoverer-cache-{$id}";
    }
}
