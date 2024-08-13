<?php

namespace Spatie\StructureDiscoverer\Support;

use Closure;
use Spatie\StructureDiscoverer\Discover;
use Spatie\StructureDiscoverer\StructureScout;

class StructureScoutManager
{
    /** @var string[] */
    protected static array $extra = [];

    /**
     * @param string[] $directories
     *
     * @return array<string>
     */
    public static function cache(array $directories): array
    {
        return self::forEachScout($directories, function (StructureScout $discoverer) {
            $discoverer->cacheDriver()->forget($discoverer->identifier());

            $discoverer->cache();
        });
    }

    /**
     * @param string[] $directories
     *
     * @return array<string>
     */
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

    /**
     * @param array<string> $directories
     * @param Closure(StructureScout): void $closure
     *
     * @return array<string>
     */
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
