<?php

namespace Spatie\StructureDiscoverer\Support;

use Spatie\StructureDiscoverer\Data\DiscoveredStructure;
use Spatie\StructureDiscoverer\Discover;
use Spatie\StructureDiscoverer\DiscoverWorkers\DiscoverWorker;
use Spatie\StructureDiscoverer\DiscoverWorkers\SynchronousDiscoverWorker;
use Spatie\StructureDiscoverer\Enums\Sort;
use SplFileInfo;
use Symfony\Component\Finder\Finder;

class StructuresResolver
{
    public function __construct(
        protected DiscoverWorker $discoverWorker = new SynchronousDiscoverWorker(),
        protected StructureChainResolver $structureChainResolver = new StructureChainResolver()
    ) {
    }

    /** @return array<DiscoveredStructure>|array<string> */
    public function execute(Discover $profile): array
    {
        $structures = $this->discover(
            $profile->config->directories,
            $profile->config->ignoredFiles,
            $profile->config->sort,
            $profile->config->reverseSorting,
        );

        if ($profile->config->withChains) {
            $this->structureChainResolver->execute($structures);
        }

        $structures = array_filter(
            $structures,
            fn (DiscoveredStructure $discovered) => $profile->config->conditions->satisfies($discovered)
        );

        if ($profile->config->full === false) {
            $structures = array_map(
                fn (DiscoveredStructure $discovered) => $discovered->getFcqn(),
                $structures
            );
        }

        return array_values($structures);
    }

    /** @return array<DiscoveredStructure> */
    public function discover(
        array $directories,
        array $ignoredFiles,
        ?Sort $sort,
        bool $reverseSorting
    ): array {
        if (empty($directories)) {
            return [];
        }

        $finder = (new Finder())->files();

        if ($sort) {
            $sort->apply($finder);
        }

        if ($reverseSorting) {
            $finder->reverseSorting();
        }

        $files = $finder->in($directories);

        $filenames = collect($files)
            ->reject(fn (SplFileInfo $file) => in_array($file->getPathname(), $ignoredFiles) || $file->getExtension() !== 'php')
            ->map(fn (SplFileInfo $file) => $file->getPathname());

        return $this->discoverWorker->run($filenames);
    }
}
