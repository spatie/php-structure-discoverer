<?php

namespace Spatie\StructureDiscoverer\Cache;

use Exception;

class NullDiscoverCacheDriver implements DiscoverCacheDriver
{
    public function has(string $id): bool
    {
        return false;
    }

    public function get(string $id): array
    {
        throw new Exception('Null driver cannot get a cached item');
    }

    public function put(string $id, array $discovered): void
    {
    }

    public function forget(string $id): void
    {
    }
}
