<?php

namespace Spatie\LaravelAutoDiscoverer;

use Error;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use ReflectionClass;
use SplFileInfo;
use Symfony\Component\Finder\Finder;

class ClassDiscoverer
{
    public function __construct(
        public array $directories,
        public string $basePath,
        public string $rootNamespace,
        public array $ignoredFiles,
    ) {
    }

    public function discover(): Collection
    {
        if (empty($this->directories)) {
            return collect();
        }

        $files = (new Finder())->files()->in($this->directories);

        return collect($files)
            ->reject(fn(SplFileInfo $file) => in_array($file->getPathname(), $this->ignoredFiles))
            ->map(fn(SplFileInfo $file) => $this->fullQualifiedClassNameFromFile($file))
            ->map(function (string $class) {
                try {
                    return new  ReflectionClass($class);
                } catch (Exception|Error) {
                    return null;
                }
            })
            ->filter();
    }

    protected function fullQualifiedClassNameFromFile(
        SplFileInfo $file
    ): string {
        return Str::of($file->getRealPath())
            ->replaceFirst($this->basePath, '')
            ->replaceLast('.php', '')
            ->trim(DIRECTORY_SEPARATOR)
            ->ucfirst()
            ->replace(
                [DIRECTORY_SEPARATOR, 'App\\'],
                ['\\', app()->getNamespace()],
            )
            ->prepend($this->rootNamespace);
    }
}
