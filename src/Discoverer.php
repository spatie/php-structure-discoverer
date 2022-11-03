<?php

namespace Spatie\LaravelAutoDiscoverer;

use Illuminate\Support\Collection;
use ParseError;
use Spatie\LaravelAutoDiscoverer\Collections\UsageCollection;
use Spatie\LaravelAutoDiscoverer\Data\Token;
use Spatie\LaravelAutoDiscoverer\Data\Usage;
use Spatie\LaravelAutoDiscoverer\Enums\DiscoveredStructureType;
use Spatie\LaravelAutoDiscoverer\Resolvers\FileResolver;
use Spatie\LaravelAutoDiscoverer\Resolvers\NamespaceResolver;
use Spatie\LaravelAutoDiscoverer\Resolvers\UseResolver;
use SplFileInfo;
use Symfony\Component\Finder\Finder;

class Discoverer
{
    private FileResolver $fileResolver;

    public function __construct(
        protected array $directories,
    ) {
        $this->fileResolver = resolve(FileResolver::class);
    }

    public function execute(): array
    {
        $files = (new Finder())->files()->in($this->directories);

        $ignoredFiles = array_merge(
            config('auto-discoverer.ignored_files') ?? [],
            Composer::getAutoloadedFiles(base_path('composer.json'))
        );

        $found = [];

        foreach ($files as $file) {
            if (in_array($file->getPathname(), $ignoredFiles)) {
                continue;
            }

            $contents = file_get_contents($file->getPathname()) ?: '';

            $found = [...$found, ...$this->fileResolver->execute($file->getPathname(), $contents)];
        }

        return $found;
    }
}
