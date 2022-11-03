<?php

namespace Spatie\LaravelAutoDiscoverer;

use Spatie\LaravelAutoDiscoverer\DiscoverConditions\AndCombinationDiscoverCondition;
use Spatie\LaravelAutoDiscoverer\DiscoverConditions\DiscoverCondition;

class Discover
{
    protected AndCombinationDiscoverCondition $conditions;

    protected array $directories = [];

    protected ?string $basePath = null;

    protected ?string $rootNamespace = null;

    public static function all(
        string $identifier
    ): Discover {
        return new self($identifier);
    }

    public static function classes(
        string $identifier
    ): Discover {
        return new self($identifier);
    }

    public function __construct(
        public string $identifier,
    ) {
        $this->conditions = new AndCombinationDiscoverCondition();
    }

    public function __call(string $name, array $arguments): static
    {
        $condition = DiscoverCondition::{$name}(...$arguments);

        $this->conditions->add($condition);

        return $this;
    }

    public function within(string ...$directories): static
    {
        $this->directories = array_merge($this->directories, $directories);

        return $this;
    }

    public function basePath(string $basePath): static
    {
        $this->basePath = $basePath;

        return $this;
    }

    public function rootNamespace(string $rootNamespace): static
    {
        $this->rootNamespace = $rootNamespace;

        return $this;
    }

    public function get(): array
    {
        return (new Discoverer($this->directories))->execute();
    }
}
