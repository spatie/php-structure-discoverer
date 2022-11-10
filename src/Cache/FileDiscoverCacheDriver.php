<?php

namespace Spatie\StructureDiscoverer\Cache;

class FileDiscoverCacheDriver implements DiscoverCacheDriver
{
    public function __construct(
        public string $directory
    ) {
        $this->directory = rtrim($this->directory, '/');

        if (! file_exists($this->directory)) {
            mkdir($this->directory);
        }
    }

    public function has(string $id): bool
    {
        return file_exists($this->resolvePath($id));
    }

    public function get(string $id): array
    {
        return unserialize(file_get_contents($this->resolvePath($id)));
    }

    public function put(string $id, array $discovered): void
    {
        $export = serialize($discovered);

        file_put_contents(
            $this->resolvePath($id),
            $export,
        );
    }

    public function forget(string $id): void
    {
        unlink($this->resolvePath($id));
    }

    private function resolvePath(string $id): string
    {
        return "{$this->directory}/discoverer-cache-{$id}";
    }
}
