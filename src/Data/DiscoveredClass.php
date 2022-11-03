<?php

namespace Spatie\LaravelAutoDiscoverer\Data;

use Illuminate\Support\Collection;
use Spatie\LaravelAutoDiscoverer\Collections\UsageCollection;
use Spatie\LaravelAutoDiscoverer\Resolvers\ReferenceListResolver;

/**
 * @property array<string> $extends
 * @property array<string> $implements
 * @property array<\Spatie\LaravelAutoDiscoverer\Data\DiscoveredAttribute> $attributes
 */
class DiscoveredClass extends DiscoveredData
{
    public function __construct(
        string $name,
        string $file,
        string $namespace,
        public bool $isFinal,
        public bool $isAbstract,
        public bool $isReadonly,
        public ?string $extends,
        public array $implements,
        public array $attributes,
    ) {
        parent::__construct($name, $namespace, $file);
    }
}
