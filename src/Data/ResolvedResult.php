<?php

namespace Spatie\LaravelAutoDiscoverer\Data;

class ResolvedResult
{
    public function __construct(
        public mixed $result,
        public int $index,
    ) {
    }
}
