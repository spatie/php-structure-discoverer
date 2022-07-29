<?php

namespace Spatie\LaravelAutoDiscoverer;

use Closure;
use ReflectionClass;
use Spatie\LaravelAutoDiscoverer\ProfileConditions\AndCombinationProfileCondition;
use Spatie\LaravelAutoDiscoverer\ProfileConditions\ProfileCondition;

/**
 * @method DiscoverProfile implementing(string ...$interfaces)
 * @method DiscoverProfile extending(string ...$classes)
 * @method DiscoverProfile named(string ...$classes)
 * @method DiscoverProfile custom(Closure ...$closures)
 * @method DiscoverProfile attribute(string $attribute, null|Closure|array $arguments = null)
 * @method DiscoverProfile combination(ProfileCondition ...$conditions)
 * @method DiscoverProfile any(ProfileCondition ...$conditions)
 */
class DiscoverProfile
{
    public AndCombinationProfileCondition $conditions;

    public string $basePath;

    public string $rootNamespace;

    public array $callBacks = [];

    public array $directories = [];

    public bool $returnReflection = false;

    public bool $returnReflectionWhenCached = false;

    public array $discovered;

    public function __construct(
        public string $identifier,
        string $basePath,
        string $rootNamespace,
    ) {
        $this->conditions = new AndCombinationProfileCondition();

        $this->basePath($basePath);
        $this->rootNamespace($rootNamespace);
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

    public function basePath(string $basePath): static
    {
        $this->basePath = realpath($basePath);

        return $this;
    }

    public function rootNamespace(string $rootNamespace): static
    {
        $this->rootNamespace = $rootNamespace;

        return $this;
    }

    public function returnReflection(bool $returnReflection = true): static
    {
        $this->returnReflection = $returnReflection;

        return $this;
    }

    public function returnReflectionWhenCached(bool $returnReflectionWhenCached = true): static
    {
        $this->returnReflectionWhenCached = $returnReflectionWhenCached;

        return $this;
    }

    public function get(Closure $callBack): static
    {
        $this->callBacks[] = $callBack;

        return $this;
    }

    public function getDirectories(): array
    {
        $directories = ! empty($this->directories)
            ? $this->directories
            : [$this->basePath];

        return array_map(
            fn(string $directory) => realpath($directory),
            $directories
        );
    }
}
