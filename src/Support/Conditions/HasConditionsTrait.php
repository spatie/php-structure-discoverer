<?php

namespace Spatie\StructureDiscoverer\Support\Conditions;

use Closure;
use Spatie\StructureDiscoverer\DiscoverConditions\AnyDiscoverCondition;
use Spatie\StructureDiscoverer\DiscoverConditions\AttributeDiscoverCondition;
use Spatie\StructureDiscoverer\DiscoverConditions\CustomDiscoverCondition;
use Spatie\StructureDiscoverer\DiscoverConditions\DiscoverCondition;
use Spatie\StructureDiscoverer\DiscoverConditions\ExactDiscoverCondition;
use Spatie\StructureDiscoverer\DiscoverConditions\ExtendsDiscoverCondition;
use Spatie\StructureDiscoverer\DiscoverConditions\ExtendsWithoutChainDiscoverCondition;
use Spatie\StructureDiscoverer\DiscoverConditions\ImplementsDiscoverCondition;
use Spatie\StructureDiscoverer\DiscoverConditions\ImplementsWithoutChainDiscoverCondition;
use Spatie\StructureDiscoverer\DiscoverConditions\NameDiscoverCondition;
use Spatie\StructureDiscoverer\DiscoverConditions\TypeDiscoverCondition;
use Spatie\StructureDiscoverer\Enums\DiscoveredStructureType;

trait HasConditionsTrait
{
    public function named(string ...$names): self
    {
        $this->conditionsStore()->add(new NameDiscoverCondition(...$names));

        return $this;
    }

    public function types(DiscoveredStructureType ...$types): self
    {
        $this->conditionsStore()->add(new TypeDiscoverCondition(...$types));

        return $this;
    }

    public function classes(): self
    {
        $this->conditionsStore()->add(new TypeDiscoverCondition(DiscoveredStructureType::ClassDefinition));

        return $this;
    }

    public function enums(): self
    {
        $this->conditionsStore()->add(new TypeDiscoverCondition(DiscoveredStructureType::Enum));

        return $this;
    }

    public function traits(): self
    {
        $this->conditionsStore()->add(new TypeDiscoverCondition(DiscoveredStructureType::Trait));

        return $this;
    }

    public function interfaces(): self
    {
        $this->conditionsStore()->add(new TypeDiscoverCondition(DiscoveredStructureType::Interface));

        return $this;
    }

    public function extending(string ...$classOrInterfaces): self
    {
        $this->conditionsStore()->add(new ExtendsDiscoverCondition(...$classOrInterfaces));

        return $this;
    }

    public function extendingWithoutChain(string ...$classOrInterfaces): self
    {
        $this->conditionsStore()->add(new ExtendsWithoutChainDiscoverCondition(...$classOrInterfaces));

        return $this;
    }

    public function implementing(string ...$interfaces): self
    {
        $this->conditionsStore()->add(new ImplementsDiscoverCondition(...$interfaces));

        return $this;
    }

    public function implementingWithoutChain(string ...$interfaces): self
    {
        $this->conditionsStore()->add(new ImplementsWithoutChainDiscoverCondition(...$interfaces));

        return $this;
    }

    public function withAttribute(string ...$attributes): self
    {
        $this->conditionsStore()->add(new AttributeDiscoverCondition(...$attributes));

        return $this;
    }

    public function custom(DiscoverCondition|HasConditions|Closure ...$conditions): self
    {
        foreach ($conditions as $condition) {
            $this->conditionsStore()->add(
                $condition instanceof Closure
                    ? new CustomDiscoverCondition($condition)
                    : $condition
            );
        }

        return $this;
    }

    public function any(DiscoverCondition|HasConditions ...$conditions): self
    {
        $this->conditionsStore()->add(new AnyDiscoverCondition(...$conditions));

        return $this;
    }

    public function exact(DiscoverCondition|HasConditions ...$conditions): self
    {
        $this->conditionsStore()->add(new ExactDiscoverCondition(...$conditions));

        return $this;
    }
}
