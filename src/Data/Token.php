<?php

namespace Spatie\StructureDiscoverer\Data;

class Token
{
    public function __construct(
        public int $type,
        public string $value,
    ) {
    }

    public static function fromToken(array $token): self
    {
        return new self($token[0], $token[1]);
    }

    public function isType(string ...$tokenTypes): bool
    {
        return in_array($this->type, $tokenTypes);
    }

    public function isStructureHeadType(): bool
    {
        return $this->isType(
            T_EXTENDS,
            T_IMPLEMENTS,
            T_STRING,
            T_NAME_FULLY_QUALIFIED,
            T_NAME_FULLY_QUALIFIED,
            T_NAME_RELATIVE,
            T_NAME_QUALIFIED
        );
    }
}
