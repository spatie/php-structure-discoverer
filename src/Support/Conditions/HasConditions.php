<?php

namespace Spatie\StructureDiscoverer\Support\Conditions;

use Closure;
use Spatie\StructureDiscoverer\DiscoverConditions\DiscoverCondition;
use Spatie\StructureDiscoverer\DiscoverConditions\ExactDiscoverCondition;
use Spatie\StructureDiscoverer\Enums\DiscoveredStructureType;

interface HasConditions
{
    public function conditionsStore(): ExactDiscoverCondition;

    public function named(string ...$names): self;

    public function types(DiscoveredStructureType ...$types): self;

    public function classes(): self;

    public function enums(): self;

    public function traits(): self;

    public function interfaces(): self;

    public function extending(string ...$classOrInterfaces): self;

    public function extendingWithoutChain(string ...$classOrInterfaces): self;

    public function implementing(string ...$interfaces): self;

    public function implementingWithoutChain(string ...$interfaces): self;

    public function withAttribute(string ...$attributes): self;

    public function custom(DiscoverCondition|HasConditions|Closure ...$conditions): self;

    public function any(DiscoverCondition|HasConditions ...$conditions): self;

    public function exact(DiscoverCondition|HasConditions ...$conditions): self;
}
