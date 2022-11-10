<?php

namespace Spatie\StructureDiscoverer\Resolvers;

use Illuminate\Support\Collection;
use ParseError;
use Spatie\Async\Pool;
use Spatie\StructureDiscoverer\Data\DiscoveredData;
use Spatie\StructureDiscoverer\Discover;
use Spatie\StructureDiscoverer\Exceptions\InvalidDiscoverCacheId;
use Spatie\StructureDiscoverer\TokenParsers\MultiFileTokenParser;
use Spatie\StructureDiscoverer\Collections\UsageCollection;
use Spatie\StructureDiscoverer\Data\Token;
use Spatie\StructureDiscoverer\Data\Usage;
use Spatie\StructureDiscoverer\DiscoverWorkers\AsynchronousDiscoverWorker;
use Spatie\StructureDiscoverer\DiscoverWorkers\DiscoverWorker;
use Spatie\StructureDiscoverer\DiscoverWorkers\SynchronousDiscoverWorker;
use Spatie\StructureDiscoverer\Enums\DiscoveredStructureType;
use Spatie\StructureDiscoverer\TokenParsers\FileTokenParser;
use Spatie\StructureDiscoverer\TokenParsers\NamespaceTokenParser;
use Spatie\StructureDiscoverer\TokenParsers\UseTokenParser;
use SplFileInfo;
use Symfony\Component\Finder\Finder;

class StructuresResolver
{
    public function __construct(
        protected DiscoverWorker $discoverWorker = new SynchronousDiscoverWorker(),
        protected StructureChainResolver $structureChainResolver = new StructureChainResolver()
    ) {
    }

    public function run(Discover $profile): array
    {
        if ($profile->config->shouldUseCache() && $profile->config->cacheDriver->has($profile->config->cacheId)) {
            return $profile->config->cacheDriver->get($profile->config->cacheId);
        }

        $structures = $this->discover(
            $profile->config->directories,
            $profile->config->ignoredFiles
        );

        if($profile->config->withChains){
            $this->structureChainResolver->execute($structures);
        }

        $structures = array_filter(
            $structures,
            fn(DiscoveredData $discovered) => $profile->conditions->satisfies($discovered)
        );

        if ($profile->config->asString) {
            $structures = array_map(
                fn(DiscoveredData $discovered) => $discovered->getFcqn(),
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

    /** @return array<DiscoveredData> */
    public function discover(
        array $directories,
        array $ignoredFiles = []
    ): array {
        $files = (new Finder())->files()->in($directories);

        $filenames = collect($files)
            ->reject(fn(SplFileInfo $file) => in_array($file->getPathname(), $ignoredFiles) || $file->getExtension() !== 'php')
            ->map(fn(SplFileInfo $file) => $file->getPathname());

        return $this->discoverWorker->run($filenames);
    }

    private function ensureCacheIdIsValid(?string $id): void
    {
        if ($id === null) {
            return;
        }

        if (preg_match("/^[\s\w-]+$/", $id) !== 1) {
            throw InvalidDiscoverCacheId::create($id);
        }
    }
}
