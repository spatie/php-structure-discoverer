<?php

namespace Spatie\StructureDiscoverer\Cache;

class StaticDiscoverCacheDriver implements DiscoverCacheDriver
{
    public static array $entries = [];

    public function has(string $id): bool
    {
        return array_key_exists($id, static::$entries);
    }

    public function get(string $id): array
    {
        return static::$entries[$id];
    }

    public function put(string $id, array $discovered): void
    {
        static::$entries[$id] = $discovered;
    }

    public function forget(string $id): void
    {
        unset(static::$entries[$id]);
    }

    public static function clear(): void
    {
        static::$entries = [];
    }
}
