<?php

namespace Spatie\StructureDiscoverer\Cache;

interface DiscoverCacheDriver
{
    public function has(string $id): bool;

    /** @return array<mixed> */
    public function get(string $id): array;

    /** @param array<mixed> $discovered */
    public function put(string $id, array $discovered): void;

    public function forget(string $id): void;
}
