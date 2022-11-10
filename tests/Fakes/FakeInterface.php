<?php

namespace Spatie\StructureDiscoverer\Tests\Fakes;

use Spatie\StructureDiscoverer\Tests\Fakes\Nested\FakeNestedInterface;

interface FakeInterface extends FakeOtherInterface, FakeNestedInterface
{
}
