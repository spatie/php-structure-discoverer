<?php

namespace Spatie\LaravelAutoDiscoverer\ProfileConditions;

use Closure;
use ReflectionClass;

abstract class ProfileCondition
{
    abstract public function satisfies(ReflectionClass $reflectionClass): bool;

    public static function implementing(string ...$interfaces): ProfileCondition
    {
        return new OrCombinationProfileCondition(...array_map(
            fn (string $interface) => new ImplementsProfileCondition($interface),
            $interfaces
        ));
    }

    public static function extending(string ...$classes): ProfileCondition
    {
        return new OrCombinationProfileCondition(...array_map(
            fn (string $class) => new ExtendsProfileCondition($class),
            $classes
        ));
    }

    public static function named(string ...$classes): ProfileCondition
    {
        return new OrCombinationProfileCondition(...array_map(
            fn (string $class) => new NameProfileCondition($class),
            $classes
        ));
    }

    public static function custom(Closure ...$closures): ProfileCondition
    {
        return new OrCombinationProfileCondition(...array_map(
            fn (Closure $closure) => new CustomProfileCondition($closure),
            $closures
        ));
    }

    public static function attribute(string $attribute, null|Closure|array $arguments = null): ProfileCondition
    {
        return new AttributeProfileCondition($attribute, $arguments);
    }

    public static function combination(ProfileCondition ...$conditions): ProfileCondition
    {
        return new AndCombinationProfileCondition(...$conditions);
    }

    public static function any(ProfileCondition ...$conditions): ProfileCondition
    {
        return new OrCombinationProfileCondition(...$conditions);
    }
}
