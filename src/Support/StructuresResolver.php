<?php

namespace Spatie\StructureDiscoverer\Support;

use Spatie\StructureDiscoverer\Data\DiscoveredStructure;
use Spatie\StructureDiscoverer\Discover;
use Spatie\StructureDiscoverer\DiscoverWorkers\DiscoverWorker;
use Spatie\StructureDiscoverer\DiscoverWorkers\SynchronousDiscoverWorker;
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
        if ($profile->config->shouldUseCache() && $profile->config->cacheDriver->has($profile->config->cacheId)) {
            return $profile->config->cacheDriver->get($profile->config->cacheId);
        }

        $structures = $this->discover(
            $profile->config->directories,
            $profile->config->ignoredFiles
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

        $structures = array_values($structures);

        if ($profile->config->shouldUseCache()) {
            $profile->config->cacheDriver->put(
                $profile->config->cacheId,
                $structures
            );
        }

        return $structures;
    }

    /** @return array<DiscoveredStructure> */
    public function discover(
        array $directories,
        array $ignoredFiles = []
    ): array {
        $files = (new Finder())->files()->in($directories);

        $filenames = collect($files)
            ->reject(fn (SplFileInfo $file) => in_array($file->getPathname(), $ignoredFiles) || $file->getExtension() !== 'php')
            ->map(fn (SplFileInfo $file) => $file->getPathname());

        return $this->discoverWorker->run($filenames);
    }
}
