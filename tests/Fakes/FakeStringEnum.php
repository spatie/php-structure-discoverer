<?php

namespace Spatie\StructureDiscoverer\Tests\Fakes;

enum FakeStringEnum: string implements FakeChildInterface
{
    case A = 'a';
    case B = 'b';
    case C = 'c';
}
