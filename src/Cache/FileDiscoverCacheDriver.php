<?php

namespace Spatie\StructureDiscoverer\Cache;

use Exception;

class FileDiscoverCacheDriver implements DiscoverCacheDriver
{
    public function __construct(
        public string $directory,
        public bool $serialize = true,
        public ?string $filename = null,
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

    /** @return array<mixed> */
    public function get(string $id): array
    {
        $path = $this->resolvePath($id);

        if ($this->serialize === false) {
            return require $path;
        }

        $file = file_get_contents($path);

        if ($file === false) {
            throw new Exception("Could not load file {$path}");
        }

        return unserialize($file);
    }

    /** @param array<mixed> $discovered */
    public function put(string $id, array $discovered): void
    {
        $export = $this->serialize
            ? serialize($discovered)
            : '<?php return ' . var_export($discovered, true) . ';';

        file_put_contents(
            $this->resolvePath($id),
            $export,
        );
    }

    public function forget(string $id): void
    {
        $path = $this->resolvePath($id);

        if (file_exists($path)) {
            unlink($path);
        }
    }

    private function resolvePath(string $id): string
    {
        return $this->filename
            ? "{$this->directory}/{$this->filename}"
            : "{$this->directory}/discoverer-cache-{$id}";
    }
}
