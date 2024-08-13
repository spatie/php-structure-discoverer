<?php

namespace Spatie\StructureDiscoverer\Support;

use Spatie\StructureDiscoverer\Cache\DiscoverCacheDriver;

class DiscoverCacheDriverFactory
{
    /**
     * @param array<string, mixed> $config
     */
    public static function create(array $config): DiscoverCacheDriver
    {
        /** @var class-string<DiscoverCacheDriver> $driverClass */
        $driverClass = $config['driver'];

        $params = $config;
        unset($params['driver']);

        return new $driverClass(...$params);
    }
}
