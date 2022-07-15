<?php

namespace Spatie\LaravelAutoDiscoverer;

use Illuminate\Support\Str;

class Composer
{
    public static function getAutoloadedFiles(string $composerJsonPath): array
    {
        $fileContents = file_get_contents($composerJsonPath);

        if (! $fileContents) {
            return [];
        }

        $basePath = Str::before($composerJsonPath, 'composer.json');

        $composerContents = json_decode($fileContents, true);

        $paths = array_merge(
            $composerContents['autoload']['files'] ?? [],
            $composerContents['autoload-dev']['files'] ?? []
        );

        return array_map(fn (string $path) => realpath($basePath.$path), $paths);
    }
}
