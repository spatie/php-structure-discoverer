<?php
return [
    /*
     *  The base path where the package (recursively) will search for classes.
     *  By default, this will be the base path of your application.
     */
    'base_path' => base_path(),

    /*
     *  A list of files that should be ignored during the discovering process.
     */
    'ignored_files' => [],

    /*
     *  Directory where cached discover profiles are stored
     */
    'cache_directory' => storage_path('app/auto-discoverer/'),
];
