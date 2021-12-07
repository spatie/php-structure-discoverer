<?php

namespace Spatie\LaravelAutoDiscoverer;

use Closure;
use Spatie\LaravelAutoDiscoverer\ProfileReferences\AndCombinationProfileReference;
use Spatie\LaravelAutoDiscoverer\ProfileReferences\CustomProfileReference;
use Spatie\LaravelAutoDiscoverer\ProfileReferences\ExtendsProfileReference;
use Spatie\LaravelAutoDiscoverer\ProfileReferences\ImplementsProfileReference;
use Spatie\LaravelAutoDiscoverer\ProfileReferences\NameProfileReference;
use Spatie\LaravelAutoDiscoverer\ProfileReferences\OrCombinationProfileReference;
use Spatie\LaravelAutoDiscoverer\ProfileReferences\ProfileReference;

/** @mixin ProfileReference */
class DiscoverProfile
{
    public OrCombinationProfileReference $references;

    public array $callBacks = [];

    public array $directories = [];

    public bool $returnReflection = false;

    public function __construct(
        public string $identifier
    ) {
        $this->references = new OrCombinationProfileReference();
    }

    public function __call(string $name, array $arguments): static
    {
        $reference = ProfileReference::{$name}(...$arguments);

        $this->references->add($reference);

        return $this;
    }

    public function within(string ...$directories): static
    {
        $this->directories = array_merge($this->directories, array_map(
            fn(string $directory) => realpath($directory),
            $directories
        ));

        return $this;
    }

    public function returnReflection(bool $returnReflection = true): static
    {
        $this->returnReflection = $returnReflection;

        return $this;
    }

    public function get(Closure $callBack): static
    {
        $this->callBacks[] = $callBack;

        return $this;
    }
}
