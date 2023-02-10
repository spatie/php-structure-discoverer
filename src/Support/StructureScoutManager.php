<?php

namespace Spatie\StructureDiscoverer\Support;

use Closure;
use Spatie\StructureDiscoverer\Discover;
use Spatie\StructureDiscoverer\StructureScout;

class StructureScoutManager
{
    protected static array $extra = [];

    public static function cache(array $directories): array
    {
        return self::forEachScout($directories, function (StructureScout $discoverer) {
            $discoverer->cacheDriver()->forget($discoverer->identifier());

            $discoverer->cache();
        });
    }

    public static function clear(array $directories): array
    {
        return self::forEachScout($directories, function (StructureScout $discoverer) {
            $discoverer->cacheDriver()->forget($discoverer->identifier());
        });
    }

    public static function add(string $scout): void
    {
        if (in_array($scout, static::$extra)) {
            return;
        }

        static::$extra[] = $scout;
    }

    private static function forEachScout(
        array $directories,
        Closure $closure
    ): array {
        /** @var string[] $discoveredScouts */
        $discoveredScouts = Discover::in(...$directories)
            ->classes()
            ->extending(StructureScout::class)
            ->get();

        $scouts = array_unique([
            ...$discoveredScouts,
            ...static::$extra,
        ]);

        $touched = [];

        foreach ($scouts as $scout) {
            /** @var StructureScout $scout */
            $scout = LaravelDetector::isRunningLaravel()
                ? app($scout)
                : new $scout();

            $closure($scout);

            $touched[] = $scout->identifier();
        }

        return $touched;
    }
}
