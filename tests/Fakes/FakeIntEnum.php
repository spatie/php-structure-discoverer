<?php

namespace Spatie\StructureDiscoverer\Tests\Fakes;

enum FakeIntEnum: int implements FakeChildInterface
{
    case A = 0;
    case B = 1;
    case C = 2;
}
