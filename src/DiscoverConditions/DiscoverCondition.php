<?php

namespace Spatie\LaravelAutoDiscoverer\DiscoverConditions;

use Closure;
use ReflectionClass;

abstract class DiscoverCondition
{
    abstract public function satisfies(ReflectionClass $reflectionClass): bool;

    public static function implementing(string ...$interfaces): DiscoverCondition
    {
        return new OrCombinationDiscoverCondition(...array_map(
            fn (string $interface) => new ImplementsDiscoverCondition($interface),
            $interfaces
        ));
    }

    public static function extending(string ...$classes): DiscoverCondition
    {
        return new OrCombinationDiscoverCondition(...array_map(
            fn (string $class) => new ExtendsDiscoverCondition($class),
            $classes
        ));
    }

    public static function named(string ...$classes): DiscoverCondition
    {
        return new OrCombinationDiscoverCondition(...array_map(
            fn (string $class) => new NameDiscoverCondition($class),
            $classes
        ));
    }

    public static function custom(Closure ...$closures): DiscoverCondition
    {
        return new OrCombinationDiscoverCondition(...array_map(
            fn (Closure $closure) => new CustomDiscoverCondition($closure),
            $closures
        ));
    }

    public static function attribute(string $attribute, null|Closure|array $arguments = null): DiscoverCondition
    {
        return new AttributeDiscoverCondition($attribute, $arguments);
    }

    public static function combination(DiscoverCondition ...$conditions): DiscoverCondition
    {
        return new AndCombinationDiscoverCondition(...$conditions);
    }

    public static function any(DiscoverCondition ...$conditions): DiscoverCondition
    {
        return new OrCombinationDiscoverCondition(...$conditions);
    }
}
