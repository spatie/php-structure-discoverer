<?php

namespace Spatie\StructureDiscoverer\Support;

use Closure;
use Spatie\StructureDiscoverer\Discover;
use Spatie\StructureDiscoverer\StructureScout;

class StructureScoutManager
{
    public static function cache(array $directories): array
    {
        return self::forEachDiscoverer($directories, function (StructureScout $discoverer) {
            $discoverer->cacheDriver()->forget($discoverer->identifier());

            $discoverer->get();
        });
    }

    public static function clear(array $directories): array
    {
        return self::forEachDiscoverer($directories, function (StructureScout $discoverer) {
            $discoverer->cacheDriver()->forget($discoverer->identifier());
        });
    }

    private static function forEachDiscoverer(
        array $directories,
        Closure $closure
    ): array {
        /** @var string[] $discoverers */
        $discoverers = Discover::in(...$directories)
            ->classes()
            ->extending(StructureScout::class)
            ->get();

        $touched = [];

        foreach ($discoverers as $discoverer) {
            /** @var StructureScout $discoverer */
            $discoverer = LaravelDetector::isRunningLaravel()
                ? app($discoverer)
                : new $discoverer();

            $closure($discoverer);

            $touched[] = $discoverer->identifier();
        }

        return $touched;
    }
}
