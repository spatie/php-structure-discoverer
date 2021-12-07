<?php

namespace Spatie\LaravelAutoDiscoverer\ProfileReferences;

use ReflectionClass;

class OrCombinationProfileReference extends ProfileReference
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
        if(empty($this->references)){
            return true;
        }

        foreach ($this->references as $reference) {
            if ($reference->satisfies($reflectionClass)) {
                return true;
            }
        }

        return false;
    }
}
