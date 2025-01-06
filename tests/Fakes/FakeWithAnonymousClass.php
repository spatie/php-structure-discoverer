<?php

namespace Spatie\StructureDiscoverer\Tests\Fakes;

class FakeWithAnonymousClass
{
    public function foo(): object
    {
        return new class() {
            public function bar(): string {
                return 'baz';
            }
        };
    }
}
