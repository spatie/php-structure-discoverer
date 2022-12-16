<?php

namespace Spatie\StructureDiscoverer\Tests\Fakes;

use Spatie\StructureDiscoverer\Tests\Fakes\Nested\FakeNestedInterface;

interface FakeChildInterface extends FakeRootInterface, FakeNestedInterface
{
}
