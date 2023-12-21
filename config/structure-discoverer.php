<?php

return [
    /*
     *  A list of files that should be ignored during the discovering process.
     */
    'ignored_files' => [

    ],

    /**
     * The directories where the package should search for structure scouts
     */
    'structure_scout_directories' => [
        app_path(),
    ],

    /*
     *  Configure the cache driver for discoverers
     */
    'cache' => [
        'driver' => \Spatie\StructureDiscoverer\Cache\LaravelDiscoverCacheDriver::class,
        'store' => null,
    ],
];
