<?php

namespace Spatie\StructureDiscoverer\Tests\Fakes;

enum FakeIntEnum: int implements FakeChildInterface
{
    case A = 'a';
    case B = 'b';
    case C = 'c';
}
