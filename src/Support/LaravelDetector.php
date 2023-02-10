<?php

namespace Spatie\StructureDiscoverer\Support;

class LaravelDetector
{
    public static function isRunningLaravel(): bool
    {
        return defined('LARAVEL_VERSION') && defined('LARAVEL_START');
    }
}
