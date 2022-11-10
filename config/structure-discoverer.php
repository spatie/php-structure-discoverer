<?php

return [
    /*
     *  A list of files that should be ignored during the discovering process.
     */
    'ignored_files' => [],

    /*
     *  Configure how the discovered structures will be cached
     */
    'cache' => [
        'driver' => \Spatie\StructureDiscoverer\Cache\LaravelDiscoverCacheDriver::class,
        'connection' => null,
    ]
];
