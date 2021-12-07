<?php

namespace Spatie\LaravelAutoDiscoverer\ProfileReferences;

use ReflectionClass;

class AndCombinationProfileReference extends ProfileReference
{
    private array $references;

    public function __construct(ProfileReference ...$references)
    {
        $this->references = $references;
    }

    public function add(ProfileReference $reference)
    {
        return $this->references[] = $reference;
    }

    public function satisfies(ReflectionClass $reflectionClass): bool
    {
        foreach ($this->references as $reference) {
            if (! $reference->satisfies($reflectionClass)) {
                return false;
            }
        }

        return true;
    }
}
