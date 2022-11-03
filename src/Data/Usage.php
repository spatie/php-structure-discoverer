<?php

namespace Spatie\LaravelAutoDiscoverer\Data;

use Illuminate\Support\Str;

class Usage
{
    public string $name;

    public function __construct(
        public string $fcqn
    ) {
        $this->name = Str::afterLast($this->fcqn, '\\');
    }
}
