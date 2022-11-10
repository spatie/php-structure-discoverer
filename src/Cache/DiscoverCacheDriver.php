<?php

namespace Spatie\StructureDiscoverer\Cache;

interface DiscoverCacheDriver
{
    public function has(string $id): bool;

    public function get(string $id): array;

    public function put(string $id, array $discovered): void;

    public function forget(string $id): void;
}
