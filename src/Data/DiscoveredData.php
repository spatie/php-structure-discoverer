<?php

namespace Spatie\LaravelAutoDiscoverer\Data;


use Illuminate\Support\Collection;
use Spatie\LaravelAutoDiscoverer\Collections\UsageCollection;

abstract class DiscoveredData
{
    public function __construct(
        public string $name,
        public string $namespace,
        public string $file,
    ) {
    }
}
