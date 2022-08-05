<?php

namespace Spatie\LaravelAutoDiscoverer;

use Illuminate\Support\Collection;
use Spatie\LaravelAutoDiscoverer\Exceptions\DuplicateDiscoverProfile;
use Spatie\LaravelAutoDiscoverer\Exceptions\UnknownDiscoverProfile;
use Spatie\LaravelAutoDiscoverer\ValueObjects\DiscoverProfile;
use Spatie\LaravelAutoDiscoverer\ValueObjects\DiscoverProfileConfig;

class DiscoverProfilesCollection
{
    /** @var Collection<string, \Spatie\LaravelAutoDiscoverer\ValueObjects\DiscoverProfile> */
    protected Collection $objectives;

    public function __construct(?Collection $profiles = null)
    {
        $this->objectives = $profiles ?? collect();
    }

    public function add(DiscoverProfile $objective): self
    {
        if ($this->has($objective)) {
            throw DuplicateDiscoverProfile::forIdentifier($objective->getIdentifier());
        }

        $this->objectives[$objective->getIdentifier()] = $objective;

        return $this;
    }

    public function get(string $identifier): DiscoverProfile
    {
        if (! $this->has($identifier)) {
            throw UnknownDiscoverProfile::forIdentifier($identifier);
        }

        return $this->objectives->get($identifier);
    }

    public function has(string|DiscoverProfile $objective): bool
    {
        return $this->objectives->has(
            $objective instanceof DiscoverProfile ? $objective->getIdentifier() : $objective
        );
    }

    public function filter(?callable $callback): self
    {
        return new self($this->objectives->filter($callback));
    }

    public function map(callable $callback): self
    {
        $clone = clone $this;

        $clone->objectives = $clone->objectives->map($callback);

        return $this;
    }

    public function transform(callable $callback): self
    {
        $this->objectives->transform($callback);

        return $this;
    }

    public function each(callable $callback): self
    {
        $this->objectives->each($callback);

        return $this;
    }

    public function partition(callable $callback): array
    {
        [$a, $b] = $this->objectives->partition($callback);

        return [new self($a), new self($b)];
    }

    public function merge(DiscoverProfilesCollection $other): self
    {
        return new self($this->objectives->merge($other->objectives));
    }

    public function reset(): DiscoverProfilesCollection
    {
        return $this->transform(fn(DiscoverProfile $profile) => (clone $profile)->reset());
    }

    /** @return Collection<\Spatie\LaravelAutoDiscoverer\ValueObjects\DiscoverProfile> */
    public function toCollection(): Collection
    {
        return $this->objectives;
    }
}
