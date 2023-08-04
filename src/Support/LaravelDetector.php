<?php

namespace Spatie\StructureDiscoverer\Support;

use Illuminate\Foundation\Application;

class LaravelDetector
{
    protected static ?bool $isRunningLaravel = null;

    public static function isRunningLaravel(): bool
    {
        return static::$isRunningLaravel ??= class_exists(Application::class);
    }
}
