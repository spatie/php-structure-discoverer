<?php

namespace Spatie\StructureDiscoverer\Cache;

use Exception;

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
        $file = file_get_contents($this->resolvePath($id));

        if($file === false){
            throw new Exception("Could not load file {$this->resolvePath($id)}");
        }

        return unserialize($file);
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
