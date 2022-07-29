<?php

namespace Spatie\LaravelAutoDiscoverer;

use Error;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use ReflectionClass;
use SplFileInfo;
use Symfony\Component\Finder\Finder;
use Throwable;

class ClassDiscoverer
{
    public function discover(DiscoverProfile $profile): Collection
    {
        $files = (new Finder())->files()->in($profile->getDirectories());

        $ignoredFiles = array_merge(
            config('auto-discoverer.ignored_files'),
            Composer::getAutoloadedFiles(base_path('composer.json'))
        );

        return collect($files)
            ->reject(fn(SplFileInfo $file) => in_array($file->getPathname(), $ignoredFiles))
            ->map(fn(SplFileInfo $file) => $this->fullQualifiedClassNameFromFile($profile, $file))
            ->map(function (string $class) {
                try {
                    return new ReflectionClass($class);
                } catch (Throwable $e) {
                    return null;
                }
            })
            ->filter();
    }

    protected function fullQualifiedClassNameFromFile(
        DiscoverProfile $profile,
        SplFileInfo $file
    ): string {
        return Str::of($file->getRealPath())
            ->replaceFirst($profile->basePath, '')
            ->replaceLast('.php', '')
            ->trim(DIRECTORY_SEPARATOR)
            ->ucfirst()
            ->replace(
                [DIRECTORY_SEPARATOR, 'App\\'],
                ['\\', app()->getNamespace()],
            )
            ->prepend(Str::finish($profile->rootNamespace, '\\'));
    }
}
