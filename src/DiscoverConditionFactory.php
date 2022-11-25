<?php

namespace Spatie\StructureDiscoverer;

use Spatie\StructureDiscoverer\DiscoverConditions\AnyDiscoverCondition;
use Spatie\StructureDiscoverer\DiscoverConditions\AttributeDiscoverCondition;
use Spatie\StructureDiscoverer\DiscoverConditions\DiscoverCondition;
use Spatie\StructureDiscoverer\DiscoverConditions\ExactDiscoverCondition;
use Spatie\StructureDiscoverer\DiscoverConditions\ExtendsDiscoverCondition;
use Spatie\StructureDiscoverer\DiscoverConditions\ImplementsDiscoverCondition;
use Spatie\StructureDiscoverer\DiscoverConditions\NameDiscoverCondition;
use Spatie\StructureDiscoverer\DiscoverConditions\TypeDiscoverCondition;
use Spatie\StructureDiscoverer\Enums\DiscoveredStructureType;

class DiscoverConditionFactory
{
    public function __construct(
        public ExactDiscoverCondition $conditions = new ExactDiscoverCondition(),
    ) {
    }

    public function named(string ...$names): self
    {
        $this->conditions->add(new NameDiscoverCondition(...$names));

        return $this;
    }

    public function types(DiscoveredStructureType ...$types): self
    {
        $this->conditions->add(new TypeDiscoverCondition(...$types));

        return $this;
    }

    public function classes(): self
    {
        $this->conditions->add(new TypeDiscoverCondition(DiscoveredStructureType::ClassDefinition));

        return $this;
    }

    public function enums(): self
    {
        $this->conditions->add(new TypeDiscoverCondition(DiscoveredStructureType::Enum));

        return $this;
    }

    public function traits(): self
    {
        $this->conditions->add(new TypeDiscoverCondition(DiscoveredStructureType::Trait));

        return $this;
    }

    public function interfaces(): self
    {
        $this->conditions->add(new TypeDiscoverCondition(DiscoveredStructureType::Interface));

        return $this;
    }

    public function extending(string ...$classOrInterfaces): self
    {
        $this->conditions->add(new ExtendsDiscoverCondition(...$classOrInterfaces));

        return $this;
    }

    public function implementing(string ...$interfaces): self
    {
        $this->conditions->add(new ImplementsDiscoverCondition(...$interfaces));

        return $this;
    }

    public function withAttribute(string ...$attributes): self
    {
        $this->conditions->add(new AttributeDiscoverCondition(...$attributes));

        return $this;
    }

    public function custom(DiscoverCondition|DiscoverConditionFactory ...$conditions): self
    {
        foreach ($conditions as $condition) {
            $this->conditions->add($condition);
        }

        return $this;
    }

    public function any(DiscoverCondition|DiscoverConditionFactory ...$conditions): self
    {
        $this->conditions->add(new AnyDiscoverCondition(...$conditions));

        return $this;
    }

    public function exact(DiscoverCondition|DiscoverConditionFactory ...$conditions): self
    {
        $this->conditions->add(new ExactDiscoverCondition(...$conditions));

        return $this;
    }
}
