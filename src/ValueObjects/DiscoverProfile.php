<?php

namespace Spatie\LaravelAutoDiscoverer\ValueObjects;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use ReflectionClass;
use Spatie\LaravelAutoDiscoverer\Contracts\DiscoverProfileIdentifieable;

class DiscoverProfile implements DiscoverProfileIdentifieable
{
    protected Collection $discovered;

    protected bool $isDiscovered = false;

    protected bool $isDiscoveredFromCache = false;

    protected int $callBacksRanCounter = 0;

    public function __construct(public DiscoverProfileConfig $config)
    {
        $this->discovered = new Collection();
    }

    public function getIdentifier(): string
    {
        return $this->config->identifier;
    }

    public function getRootNamespace(): string
    {
        return $this->config->rootNamespace;
    }

    public function getBasePath(): string
    {
        return realpath($this->config->basePath);
    }

    public function getDirectories(): array
    {
        $directories = ! empty($this->config->directories)
            ? $this->config->directories
            : [$this->config->basePath];

        return array_map(
            fn (string $directory) => realpath($directory),
            $directories
        );
    }

    public function isValidPathForProfile(string $path): bool
    {
        $path = realpath(dirname($path));

        $isSubDir = Arr::first($this->getDirectories(), fn (string $directory) => str_starts_with(
            $path,
            $directory,
        ));

        return $isSubDir !== null;
    }

    public function markDiscovered(bool $fromCache = false): self
    {
        $this->isDiscovered = true;
        $this->isDiscoveredFromCache = $fromCache;

        return $this;
    }

    public function isDiscovered(): bool
    {
        return $this->isDiscovered;
    }

    public function addDiscovered(
        string|ReflectionClass ...$classes,
    ): self {
        foreach ($classes as $class) {
            $class instanceof ReflectionClass
                ? $this->discovered->put($class->name, $class)
                : $this->discovered->put($class, null);
        }

        return $this;
    }

    public function getDiscoveredClasses(): Collection
    {
        $shouldReturnReflection = ($this->isDiscoveredFromCache && $this->config->returnReflectionWhenCached)
            || (! $this->isDiscoveredFromCache && $this->config->returnReflection);

        return $shouldReturnReflection
            ? $this->getDiscoveredReflectionClasses()
            : $this->getDiscoveredClassNames();
    }

    public function getDiscoveredClassNames(): Collection
    {
        return $this->discovered->keys();
    }

    public function getDiscoveredReflectionClasses(): Collection
    {
        return $this->discovered
            ->transform(
                fn (?ReflectionClass $reflection, string $class) => $reflection === null
                ? new ReflectionClass($class)
                : $reflection
            )
            ->values();
    }

    public function runCallbacks(): self
    {
        while ($this->callBacksRanCounter < count($this->config->callBacks)) {
            ($this->config->callBacks[$this->callBacksRanCounter])($this->getDiscoveredClasses());

            $this->callBacksRanCounter++;
        }

        return $this;
    }

    public function reset(): self
    {
        $this->discovered = new Collection();
        $this->isDiscovered = false;
        $this->isDiscoveredFromCache = false;
        $this->callBacksRanCounter = 0;

        return $this;
    }
}
