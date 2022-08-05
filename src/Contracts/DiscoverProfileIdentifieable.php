<?php

namespace Spatie\LaravelAutoDiscoverer\Contracts;

interface DiscoverProfileIdentifieable
{
    public function getIdentifier(): string;
}
