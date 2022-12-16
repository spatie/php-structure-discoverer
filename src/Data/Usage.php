<?php

namespace Spatie\StructureDiscoverer\Data;

class Usage
{
    public function __construct(
        public string $fcqn,
        public ?string $name = null,
    ) {
        $this->name ??= $this->resolveNonFcqnName($this->fcqn);
    }

    public function resolveNonFcqnName(string $fcqn): string
    {
        $position = strrpos($fcqn, '\\');

        if ($position === false) {
            return $fcqn;
        }

        return substr($fcqn, $position + strlen('\\'));
    }
}
