<?php

namespace Spatie\LaravelAutoDiscoverer\Data;

use Illuminate\Support\Collection;
use Spatie\LaravelAutoDiscoverer\Collections\UsageCollection;
use Spatie\LaravelAutoDiscoverer\Enums\DiscoveredEnumType;

/**
 * @property array<string> $implements
 */
class DiscoveredEnum extends DiscoveredData
{
    public function __construct(
        public string $name,
        public string $namespace,
        public string $file,
        public DiscoveredEnumType $type,
        public array $implements,
    ) {
        parent::__construct($name, $namespace, $file);
    }
}
