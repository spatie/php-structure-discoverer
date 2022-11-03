<?php

namespace Spatie\LaravelAutoDiscoverer\Data;

use Illuminate\Support\Collection;
use Spatie\LaravelAutoDiscoverer\Collections\UsageCollection;

class DiscoveredInterface extends DiscoveredData
{
    public function __construct(
        string $name,
        string $file,
        string $namespace,
        public array $extends,
    ) {
        parent::__construct($name, $namespace, $file);
    }
}
