<?php

namespace Spatie\LaravelAutoDiscoverer\ProfileReferences;

use Closure;
use ReflectionClass;

abstract class ProfileReference
{
    abstract public function satisfies(ReflectionClass $reflectionClass): bool;

    public static function implementing(string ...$interfaces): ProfileReference
    {
        return new OrCombinationProfileReference(...array_map(
            fn (string $interface) => new ImplementsProfileReference($interface),
            $interfaces
        ));
    }

    public static function extending(string ...$classes): ProfileReference
    {
        return new OrCombinationProfileReference(...array_map(
            fn (string $class) => new ExtendsProfileReference($class),
            $classes
        ));
    }

    public static function named(string ...$classes): ProfileReference
    {
        return new OrCombinationProfileReference(...array_map(
            fn (string $class) => new NameProfileReference($class),
            $classes
        ));
    }

    public static function custom(Closure ...$closures): ProfileReference
    {
        return new OrCombinationProfileReference(...array_map(
            fn (Closure $closure) => new CustomProfileReference($closure),
            $closures
        ));
    }

    public static function combination(ProfileReference ...$references): ProfileReference
    {
        return new AndCombinationProfileReference(...$references);
    }

    public static function any(ProfileReference ...$references): ProfileReference
    {
        return new OrCombinationProfileReference(...$references);
    }
}
