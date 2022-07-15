<?php

namespace Spatie\LaravelAutoDiscoverer;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use ReflectionClass;
use SplFileInfo;
use Symfony\Component\Finder\Finder;
use Throwable;

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
            return new Collection();
        }

        $files = (new Finder())->files()->in($this->directories);

        $ignoredFiles = array_merge(
            $this->ignoredFiles,
            Composer::getAutoloadedFiles(base_path('composer.json'))
        );

        return collect($files)
            ->reject(fn (SplFileInfo $file) => in_array($file->getPathname(), $ignoredFiles))
            ->map(fn (SplFileInfo $file) => $this->fullQualifiedClassNameFromFile($file))
            ->map(function (string $class) {
                try {
                    return new ReflectionClass($class);
                } catch (Throwable) {
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
