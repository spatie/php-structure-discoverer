<?php

namespace Spatie\LaravelAutoDiscoverer\Tests\Fakes;

use Attribute;

#[Attribute]
class FakeAttribute
{
    public function __construct(public ?string $method = null)
    {
    }
}
