<?php

namespace Spatie\StructureDiscoverer\Support;

class LaravelDetector
{
    public static function isRunningLaravel(): bool
    {
        return function_exists('app') && function_exists('resolve');
    }
}
