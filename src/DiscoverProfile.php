<?php

namespace Spatie\LaravelAutoDiscoverer;

use Closure;
use Spatie\LaravelAutoDiscoverer\ProfileConditions\AndCombinationProfileCondition;
use Spatie\LaravelAutoDiscoverer\ProfileConditions\OrCombinationProfileCondition;
use Spatie\LaravelAutoDiscoverer\ProfileConditions\ProfileCondition;

/** @mixin ProfileCondition */
class DiscoverProfile
{
    public AndCombinationProfileCondition $conditions;

    public array $callBacks = [];

    public array $directories = [];

    public bool $returnReflection = false;

    public function __construct(public string $identifier)
    {
        $this->conditions = new AndCombinationProfileCondition();
    }

    public function __call(string $name, array $arguments): static
    {
        $condition = ProfileCondition::{$name}(...$arguments);

        $this->conditions->add($condition);

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
